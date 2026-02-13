<?php
/**
 * Возвращает массив SEO-meta по указанным типам постов.
 * Каждый элемент: post_id, post_name, language, uri, title, description, keywords (sub_array), text (из main).
 *
 * Запуск: http://ваш-сайт/seo-meta-by-post-types.php  — отдаёт JSON.
 * При включении: $data = require 'seo-meta-by-post-types.php';  — массив (см. конец файла).
 */

set_time_limit( 0 );

// Список типов постов для отчёта (пустой = все типы из БД, кроме исключённых).
$post_types_filter = [
	'page',
	'story',
	'blog',
	'contact',
	'advanced_product',
	'scientist',
	'vacancy',
	'static_content',
];

$post_types_exclude = [ 'revision', 'nav_menu_item', 'attachment', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block', 'wp_template', 'wp_template_part', 'wp_global_styles', 'wp_navigation', 'seo-meta' ];

// Имена полей ACF у записей seo-meta (meta_key в wp_postmeta).
$seo_meta_post_object_key   = 'post_object';
$seo_meta_title_key         = 'title';
$seo_meta_description_key   = 'description';
$seo_meta_keywords_repeater = 'keywords';
$seo_meta_keyword_subfield  = 'keyword';

$seo_meta_language_taxonomy = 'taxonomy_language';

$report_base_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' ) . '://' . ( $_SERVER['HTTP_HOST'] ?? '' ) . '/';

// Файл для сохранения прогресса (при перезагрузке продолжаем с уже обработанных).
$seo_meta_output_file = __DIR__ . '/seo-meta-output.json';
$seo_meta_save_every  = 25;

$config_path = __DIR__ . '/wp-config.php';
if ( ! is_readable( $config_path ) ) {
	die( 'wp-config.php не найден.' );
}
$config = file_get_contents( $config_path );

preg_match( "/define\s*\(\s*['\"]DB_NAME['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)/", $config, $m );
$db_name = isset( $m[1] ) ? $m[1] : '';
preg_match( "/define\s*\(\s*['\"]DB_USER['\"]\s*,\s*['\"]([^'\"]*)['\"]\s*\)/", $config, $m );
$db_user = isset( $m[1] ) ? $m[1] : '';
preg_match( "/define\s*\(\s*['\"]DB_PASSWORD['\"]\s*,\s*['\"]([^'\"]*)['\"]\s*\)/", $config, $m );
$db_pass = isset( $m[1] ) ? $m[1] : '';
preg_match( "/define\s*\(\s*['\"]DB_HOST['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)/", $config, $m );
$db_host = isset( $m[1] ) ? $m[1] : 'localhost';
preg_match( "/\\\$table_prefix\s*=\s*['\"]([^'\"]+)['\"]/", $config, $m );
$prefix = isset( $m[1] ) ? $m[1] : 'wp_';

$tbl_posts              = $prefix . 'posts';
$tbl_postmeta           = $prefix . 'postmeta';
$tbl_term_relationships = $prefix . 'term_relationships';
$tbl_term_taxonomy      = $prefix . 'term_taxonomy';
$tbl_terms              = $prefix . 'terms';

try {
	$pdo = new PDO(
		'mysql:host=' . $db_host . ';dbname=' . $db_name . ';charset=utf8mb4',
		$db_user,
		$db_pass,
		[ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]
	);
} catch ( PDOException $e ) {
	die( 'Ошибка подключения к БД: ' . htmlspecialchars( $e->getMessage() ) );
}

/**
 * Получает HTML страницы по URL через cURL.
 *
 * @param string $url URL для запроса.
 * @return string HTML или пустая строка при ошибке.
 */
function fetch_html_by_uri( $url ) {
	$ch = curl_init( $url );
	if ( $ch === false ) {
		return '';
	}
	curl_setopt_array( $ch, [
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_TIMEOUT        => 30,
		CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; SEO-meta-fetcher/1.0)',
		CURLOPT_ENCODING       => '',
	] );
	$body = curl_exec( $ch );
	$err  = curl_errno( $ch );
	curl_close( $ch );
	if ( $err !== 0 || $body === false || $body === '' ) {
		return '';
	}
	return (string) $body;
}

/**
 * Извлекает текст из первого тега <main> в HTML (скрипты, стили, картинки удаляются).
 *
 * @param string $html Исходный HTML.
 * @return string Нормализованный текст или пустая строка.
 */
function extract_text_from_main( $html ) {
	if ( $html === '' ) {
		return '';
	}
	$use_errors = libxml_use_internal_errors( true );
	$dom        = new DOMDocument();
	$dom->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	libxml_use_internal_errors( $use_errors );
	$main = $dom->getElementsByTagName( 'main' )->item( 0 );
	if ( ! $main ) {
		return '';
	}
	$tags_to_remove = [ 'script', 'style', 'img' ];
	foreach ( $tags_to_remove as $tag ) {
		$list = $main->getElementsByTagName( $tag );
		$nodes = [];
		for ( $i = 0; $i < $list->length; $i++ ) {
			$nodes[] = $list->item( $i );
		}
		foreach ( $nodes as $node ) {
			if ( $node->parentNode ) {
				$node->parentNode->removeChild( $node );
			}
		}
	}
	$text = $main->textContent;
	$text = trim( preg_replace( '/\s+/', ' ', $text ) );
	return $text;
}

/**
 * Находит наибольший общий префикс (longest common prefix) среди всех строк.
 *
 * @param array $strings Массив строк.
 * @return string Общий префикс.
 */
function find_common_prefix( $strings ) {
	if ( empty( $strings ) ) {
		return '';
	}
	$strings = array_filter( $strings, function( $s ) { return $s !== ''; } );
	if ( empty( $strings ) ) {
		return '';
	}
	$prefix = $strings[0];
	foreach ( $strings as $str ) {
		while ( strpos( $str, $prefix ) !== 0 ) {
			$prefix = substr( $prefix, 0, -1 );
			if ( $prefix === '' ) {
				return '';
			}
		}
	}
	return $prefix;
}

/**
 * Находит наибольший общий суффикс (longest common suffix) среди всех строк.
 *
 * @param array $strings Массив строк.
 * @return string Общий суффикс.
 */
function find_common_suffix( $strings ) {
	if ( empty( $strings ) ) {
		return '';
	}
	$strings = array_filter( $strings, function( $s ) { return $s !== ''; } );
	if ( empty( $strings ) ) {
		return '';
	}
	$reversed = array_map( 'strrev', $strings );
	$reversed_suffix = find_common_prefix( $reversed );
	return strrev( $reversed_suffix );
}

$stmt = $pdo->query( "SELECT DISTINCT post_type FROM `{$tbl_posts}` ORDER BY post_type" );
$all_types = $stmt->fetchAll( PDO::FETCH_COLUMN );

if ( ! empty( $post_types_filter ) ) {
	$post_types = array_values( array_intersect( $all_types, $post_types_filter ) );
} else {
	$post_types = array_values( array_diff( $all_types, $post_types_exclude ) );
}

$result = [];

$stmt_posts = $pdo->prepare( "SELECT ID, post_title, post_name, post_type, post_status FROM `{$tbl_posts}` WHERE post_type = ? ORDER BY ID" );
$stmt_seo   = $pdo->prepare( "SELECT p.ID FROM `{$tbl_posts}` p INNER JOIN `{$tbl_postmeta}` pm ON p.ID = pm.post_id AND pm.meta_key = ? AND pm.meta_value = ? WHERE p.post_type = 'seo-meta' AND p.post_status = 'publish' ORDER BY p.ID" );
$stmt_meta  = $pdo->prepare( "SELECT meta_key, meta_value FROM `{$tbl_postmeta}` WHERE post_id = ?" );
$stmt_lang  = $pdo->prepare( "SELECT t.name FROM `{$tbl_term_relationships}` tr INNER JOIN `{$tbl_term_taxonomy}` tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = ? INNER JOIN `{$tbl_terms}` t ON tt.term_id = t.term_id WHERE tr.object_id = ? ORDER BY t.name" );

foreach ( $post_types as $post_type ) {
	$stmt_posts->execute( [ $post_type ] );
	$posts = $stmt_posts->fetchAll( PDO::FETCH_ASSOC );

	foreach ( $posts as $post ) {
		$post_id   = (int) $post['ID'];
		$post_name = (string) ( $post['post_name'] ?? '' );
		$uri       = $report_base_url . '?p=' . $post_id;

		$stmt_seo->execute( [ $seo_meta_post_object_key, (string) $post_id ] );
		$seo_posts = $stmt_seo->fetchAll( PDO::FETCH_ASSOC );

		if ( empty( $seo_posts ) ) {
			$result[] = [
				'post_id'     => $post_id,
				'post_name'   => $post_name,
				'language'    => null,
				'uri'         => $uri,
				'title'       => null,
				'description' => null,
				'keywords'    => [],
			];
			continue;
		}

		foreach ( $seo_posts as $seo ) {
			$seo_id = (int) $seo['ID'];
			$stmt_meta->execute( [ $seo_id ] );
			$meta_rows = $stmt_meta->fetchAll( PDO::FETCH_ASSOC );
			$meta_map  = [];
			foreach ( $meta_rows as $r ) {
				$meta_map[ $r['meta_key'] ] = $r['meta_value'];
			}

			$title       = isset( $meta_map[ $seo_meta_title_key ] ) ? $meta_map[ $seo_meta_title_key ] : null;
			$description = isset( $meta_map[ $seo_meta_description_key ] ) ? $meta_map[ $seo_meta_description_key ] : null;
			$keywords    = [];
			foreach ( $meta_map as $k => $v ) {
				if ( preg_match( '/^' . preg_quote( $seo_meta_keywords_repeater, '/' ) . '_(\d+)_' . preg_quote( $seo_meta_keyword_subfield, '/' ) . '$/', $k, $mm ) ) {
					$keywords[ (int) $mm[1] ] = $v;
				}
			}
			ksort( $keywords );
			$keywords = array_values( $keywords );

			$stmt_lang->execute( [ $seo_meta_language_taxonomy, $seo_id ] );
			$language_terms = $stmt_lang->fetchAll( PDO::FETCH_COLUMN );
			$language       = empty( $language_terms ) ? null : implode( ', ', $language_terms );

			$result[] = [
				'post_id'     => $post_id,
				'post_name'   => $post_name,
				'language'    => $language,
				'uri'         => $uri,
				'title'       => $title !== null && $title !== '' ? $title : null,
				'description' => $description !== null && $description !== '' ? $description : null,
				'keywords'    => $keywords,
			];
		}
	}
}

// Группировка по slug (post_name) и выбор оригинала в каждой группе (приоритет: en).
$groups = [];
foreach ( $result as $i => $item ) {
	$slug = $item['post_name'];
	if ( ! isset( $groups[ $slug ] ) ) {
		$groups[ $slug ] = [];
	}
	$groups[ $slug ][] = $i;
}

$is_original = [];
foreach ( $groups as $slug => $indices ) {
	$original_idx = null;
	foreach ( $indices as $idx ) {
		$lang = strtolower( (string) ( $result[ $idx ]['language'] ?? '' ) );
		if ( strpos( $lang, 'en' ) !== false ) {
			$original_idx = $idx;
			break;
		}
	}
	if ( $original_idx === null ) {
		$original_idx = $indices[0];
	}
	$is_original[ $original_idx ] = true;
}

// Загружаем уже сохранённый прогресс (при перезагрузке не начинаем с начала).
$existing_text = [];
if ( is_readable( $seo_meta_output_file ) ) {
	$raw = file_get_contents( $seo_meta_output_file );
	$decoded = json_decode( $raw, true );
	if ( is_array( $decoded ) ) {
		foreach ( $decoded as $row ) {
			$key = ( (int) ( $row['post_id'] ?? 0 ) ) . '|' . ( (string) ( $row['language'] ?? '' ) );
			if ( array_key_exists( 'text', $row ) ) {
				$existing_text[ $key ] = $row['text'];
			}
		}
	}
}

$text_by_uri     = [];
$newly_processed = 0;
$collected_texts = [];
$save_counter    = 0;
foreach ( $result as $i => $item ) {
	if ( ! isset( $is_original[ $i ] ) ) {
		continue;
	}
	$key = $item['post_id'] . '|' . ( (string) ( $item['language'] ?? '' ) );
	if ( array_key_exists( $key, $existing_text ) ) {
		$result[ $i ]['text'] = $existing_text[ $key ];
		$collected_texts[] = $existing_text[ $key ];
		continue;
	}
	$uri = $item['uri'];
	if ( ! isset( $text_by_uri[ $uri ] ) ) {
		$html = fetch_html_by_uri( $uri );
		$text_by_uri[ $uri ] = extract_text_from_main( $html );
	}
	$result[ $i ]['text'] = $text_by_uri[ $uri ];
	$existing_text[ $key ] = $text_by_uri[ $uri ];
	$collected_texts[] = $text_by_uri[ $uri ];
	$newly_processed++;
	$save_counter++;
	if ( $save_counter >= $seo_meta_save_every ) {
		$temp_output = [];
		foreach ( $groups as $slug => $indices ) {
			$orig_idx = null;
			foreach ( $indices as $idx ) {
				if ( isset( $is_original[ $idx ] ) ) {
					$orig_idx = $idx;
					break;
				}
			}
			if ( $orig_idx !== null && isset( $result[ $orig_idx ]['text'] ) ) {
				$temp_item = $result[ $orig_idx ];
				$similar = [];
				foreach ( $indices as $idx ) {
					if ( $idx !== $orig_idx ) {
						$similar[] = [
							'id'   => $result[ $idx ]['post_id'],
							'lang' => $result[ $idx ]['language'],
						];
					}
				}
				$temp_item['похожие_посты'] = $similar;
				$temp_output[] = $temp_item;
			}
		}
		if ( ! empty( $temp_output ) ) {
			file_put_contents( $seo_meta_output_file, json_encode( $temp_output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ), LOCK_EX );
		}
		$save_counter = 0;
	}
}

// Удаляем общий префикс и суффикс (хедер / футер) из всех текстов оригиналов.
$common_prefix = find_common_prefix( $collected_texts );
$common_suffix = find_common_suffix( $collected_texts );
$prefix_len = strlen( $common_prefix );
$suffix_len = strlen( $common_suffix );

if ( $prefix_len > 0 || $suffix_len > 0 ) {
	foreach ( $result as $i => $item ) {
		if ( ! isset( $is_original[ $i ] ) || ! isset( $result[ $i ]['text'] ) ) {
			continue;
		}
		$text = $result[ $i ]['text'];
		if ( $prefix_len > 0 && strpos( $text, $common_prefix ) === 0 ) {
			$text = substr( $text, $prefix_len );
		}
		if ( $suffix_len > 0 && substr( $text, -$suffix_len ) === $common_suffix ) {
			$text = substr( $text, 0, -$suffix_len );
		}
		$result[ $i ]['text'] = trim( $text );
		$key = $item['post_id'] . '|' . ( (string) ( $item['language'] ?? '' ) );
		$existing_text[ $key ] = $result[ $i ]['text'];
	}
}

// Построение выходного массива с похожие_посты (один элемент на группу slug).
$output = [];
foreach ( $groups as $slug => $indices ) {
	$original_idx = null;
	foreach ( $indices as $idx ) {
		if ( isset( $is_original[ $idx ] ) ) {
			$original_idx = $idx;
			break;
		}
	}
	if ( $original_idx === null ) {
		continue;
	}
	$original_item = $result[ $original_idx ];
	$similar_posts = [];
	foreach ( $indices as $idx ) {
		if ( $idx === $original_idx ) {
			continue;
		}
		$similar_posts[] = [
			'id'   => $result[ $idx ]['post_id'],
			'lang' => $result[ $idx ]['language'],
		];
	}
	$original_item['похожие_посты'] = $similar_posts;
	$output[] = $original_item;
}

// Сохранение в файл и вывод (только $output — оригиналы с похожие_посты).
// Всегда сохраняем, чтобы при первом запуске после обновления старый формат переписался в новый.
if ( ! empty( $output ) ) {
	file_put_contents( $seo_meta_output_file, json_encode( $output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ), LOCK_EX );
}

// Вывод: при прямом запуске — JSON; при включении с define('SEO_META_RETURN_ARRAY', true) — return массива.
$return_array = defined( 'SEO_META_RETURN_ARRAY' ) && constant( 'SEO_META_RETURN_ARRAY' );
if ( ! $return_array ) {
	header( 'Content-Type: application/json; charset=utf-8' );
	echo json_encode( $output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
	exit;
}

print_r($output);

return $output;