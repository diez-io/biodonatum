<?php
/**
 * Отчёт Yoast SEO по типам записей (post type).
 * Для каждого типа из БД — все записи; для каждой записи — подтаблица с SEO-полями или пустая.
 *
 * Запуск: http://ваш-сайт/yoast-seo-by-post-types.php
 */

// Список типов для отчёта: пустой = все типы из БД; иначе только перечисленные.
$post_types_filter = [
	'page',
	'blog',
	'advanced_product',
];

// Служебные типы, которые не показывать (если не попали в фильтр).
$post_types_exclude = [ 'revision', 'nav_menu_item', 'attachment', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block', 'wp_template', 'wp_template_part', 'wp_global_styles', 'wp_navigation' ];

// Читаем конфиг (без загрузки WordPress)
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

// Базовый URL сайта для ссылок на записи (без WordPress: ?p=ID).
$report_base_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' ) . '://' . ( $_SERVER['HTTP_HOST'] ?? '' ) . '/';

// Все известные SEO-поля Yoast (free) и пояснения. Для каждого поста выводятся все; пустое = null.
$yoast_meta_keys_descriptions = [
	'_yoast_wpseo_title'                        => 'SEO-заголовок (title). Можно использовать переменные: %%title%%, %%page%%, %%sep%%, %%sitename%%.',
	'_yoast_wpseo_metadesc'                     => 'Мета-описание для сниппета в поиске (рекомендуется до 156 символов).',
	'_yoast_wpseo_focuskw'                      => 'Фокусное ключевое слово (focus keyphrase).',
	'_yoast_wpseo_content_score'                => 'Оценка контента Yoast (0–100).',
	'_yoast_wpseo_linkdex'                       => 'Индекс ссылок (внутренняя метрика Yoast).',
	'_yoast_wpseo_inclusive_language_score'     => 'Оценка инклюзивности языка (0–100).',
	'_yoast_wpseo_is_cornerstone'                => 'Ключевой контент (cornerstone): true/false.',
	'_yoast_wpseo_estimated-reading-time-minutes'=> 'Примерное время чтения (минуты).',
	'_yoast_wpseo_meta-robots-noindex'          => 'Индексация: 0 = по умолчанию для типа, 1 = noindex, 2 = index.',
	'_yoast_wpseo_meta-robots-nofollow'         => 'Следование по ссылкам: 0 = follow, 1 = nofollow.',
	'_yoast_wpseo_meta-robots-adv'              => 'Доп. директивы: noimageindex, noarchive, nosnippet (через запятую).',
	'_yoast_wpseo_bctitle'                      => 'Заголовок для хлебных крошек (breadcrumbs).',
	'_yoast_wpseo_canonical'                    => 'Канонический URL страницы.',
	'_yoast_wpseo_redirect'                     => 'URL редиректа (301).',
	'_yoast_wpseo_opengraph-title'              => 'Заголовок для Facebook / Open Graph.',
	'_yoast_wpseo_opengraph-description'        => 'Описание для Facebook / Open Graph.',
	'_yoast_wpseo_opengraph-image'              => 'URL изображения для Open Graph.',
	'_yoast_wpseo_opengraph-image-id'           => 'ID вложения (медиа) для Open Graph.',
	'_yoast_wpseo_twitter-title'                => 'Заголовок для Twitter Card.',
	'_yoast_wpseo_twitter-description'          => 'Описание для Twitter Card.',
	'_yoast_wpseo_twitter-image'                => 'URL изображения для Twitter.',
	'_yoast_wpseo_twitter-image-id'             => 'ID вложения для Twitter.',
	'_yoast_wpseo_schema_page_type'             => 'Тип страницы для Schema (Page).',
	'_yoast_wpseo_schema_article_type'           => 'Тип статьи для Schema (Article).',
];

if ( php_sapi_name() !== 'cli' ) {
	header( 'Content-Type: text/html; charset=utf-8' );
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Yoast SEO — по типам записей</title>
	<style>
		body { font-family: sans-serif; margin: 1rem; }
		table { border-collapse: collapse; margin: 0.5rem 0 1.5rem 1rem; }
		th, td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; vertical-align: top; }
		th { background: #eee; }
		pre { white-space: pre-wrap; word-break: break-all; max-width: 600px; margin: 0; }
		h1 { margin-bottom: 0.5rem; }
		h2 { margin-top: 2rem; margin-bottom: 0.5rem; }
		h3 { margin-top: 1.25rem; margin-bottom: 0.25rem; font-size: 1rem; }
		.empty-seo { color: #666; font-style: italic; }
		td:nth-child(3) { max-width: 320px; font-size: 0.9em; }
	</style>
</head>
<body>
<?php

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

// 1. Получить типы записей из БД
$stmt = $pdo->query( "SELECT DISTINCT post_type FROM `{$tbl_posts}` ORDER BY post_type" );
$all_types = $stmt->fetchAll( PDO::FETCH_COLUMN );

if ( ! empty( $post_types_filter ) ) {
	$post_types = array_intersect( $all_types, $post_types_filter );
	$post_types = array_values( $post_types );
} else {
	$post_types = array_diff( $all_types, $post_types_exclude );
	$post_types = array_values( $post_types );
}

echo '<h1>Yoast SEO — по типам записей</h1>';
echo '<p>Типы: ' . ( count( $post_types ) ? implode( ', ', array_map( 'htmlspecialchars', $post_types ) ) : '—' ) . '</p>';

$stmt_posts  = $pdo->prepare( "SELECT ID, post_title, post_type, post_status FROM `{$tbl_posts}` WHERE post_type = ? ORDER BY ID" );
$stmt_meta   = $pdo->prepare( "SELECT meta_key, meta_value FROM `{$tbl_postmeta}` WHERE post_id = ? AND meta_key LIKE '\_yoast_wpseo%'" );
$stmt_lang   = $pdo->prepare( "SELECT t.name FROM `{$tbl_term_relationships}` tr INNER JOIN `{$tbl_term_taxonomy}` tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'taxonomy_language' INNER JOIN `{$tbl_terms}` t ON tt.term_id = t.term_id WHERE tr.object_id = ? ORDER BY t.name" );

foreach ( $post_types as $post_type ) {
	echo '<h2>' . htmlspecialchars( $post_type ) . '</h2>';

	$stmt_posts->execute( [ $post_type ] );
	$posts = $stmt_posts->fetchAll( PDO::FETCH_ASSOC );

	if ( empty( $posts ) ) {
		echo '<p>Записей нет.</p>';
		continue;
	}

	foreach ( $posts as $post ) {
		$post_id = (int) $post['ID'];
		$stmt_lang->execute( [ $post_id ] );
		$language_terms = $stmt_lang->fetchAll( PDO::FETCH_COLUMN );
		$language_label = empty( $language_terms ) ? '' : ' <small>| taxonomy_language: ' . htmlspecialchars( implode( ', ', $language_terms ) ) . '</small>';
		$post_permalink = $report_base_url . '?p=' . $post_id;
		echo '<h3>ID: ' . $post_id . ' — <a href="' . htmlspecialchars( $post_permalink ) . '" target="_blank" rel="noopener">' . htmlspecialchars( $post['post_title'] ) . '</a> <small>(' . htmlspecialchars( $post['post_status'] ) . ')</small>' . $language_label . ' <small><a href="' . htmlspecialchars( $post_permalink ) . '" target="_blank" rel="noopener">страница</a></small></h3>';

		$stmt_meta->execute( [ $post_id ] );
		$meta_rows = $stmt_meta->fetchAll( PDO::FETCH_ASSOC );
		$meta_map = [];
		foreach ( $meta_rows as $m ) {
			$meta_map[ $m['meta_key'] ] = $m['meta_value'];
		}

		echo '<table><thead><tr><th>meta_key</th><th>meta_value</th><th>Пояснение</th></tr></thead><tbody>';

		foreach ( $yoast_meta_keys_descriptions as $meta_key => $description ) {
			$value = array_key_exists( $meta_key, $meta_map ) ? $meta_map[ $meta_key ] : null;
			$display = $value === null ? '<span class="empty-seo">null</span>' : ( strlen( $value ) > 150
				? '<pre>' . htmlspecialchars( substr( $value, 0, 300 ) ) . ( strlen( $value ) > 300 ? '…' : '' ) . '</pre>'
				: htmlspecialchars( $value ) );
			echo '<tr><td>' . htmlspecialchars( $meta_key ) . '</td><td>' . $display . '</td><td>' . htmlspecialchars( $description ) . '</td></tr>';
		}

		// Доп. поля Yoast, которых нет в основном списке (например primary_category, старые ключи)
		$known_keys = array_keys( $yoast_meta_keys_descriptions );
		foreach ( $meta_map as $meta_key => $value ) {
			if ( in_array( $meta_key, $known_keys, true ) ) {
				continue;
			}
			$display = strlen( $value ) > 150
				? '<pre>' . htmlspecialchars( substr( $value, 0, 300 ) ) . ( strlen( $value ) > 300 ? '…' : '' ) . '</pre>'
				: htmlspecialchars( $value );
			echo '<tr><td>' . htmlspecialchars( $meta_key ) . '</td><td>' . $display . '</td><td class="empty-seo">Доп. поле Yoast (напр. primary category)</td></tr>';
		}

		echo '</tbody></table>';
	}
}

?>
</body>
</html>
