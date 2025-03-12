<?php
/**
 * Plugin Name: Language Detector
 * Description: Detects and stores the client's preferred language using willdurand/negotiation.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!session_id()) {
    session_start();
}

function fetchSupportedLanguages() {
    global $supported_languages;

    $transientKey = 'supported_languages';

    $supported_languages = get_transient($transientKey);

    if (!$supported_languages) {
        $supported_languages = [];

        $supportedLanguages = get_terms([
            'taxonomy'   => 'taxonomy_language',
            'hide_empty' => false,
        ]);

        if (!is_wp_error($supportedLanguages)) {
            foreach ($supportedLanguages as $language) {
                $supported_languages[$language->slug] = $language->name;
            }
        }

        // Cache for 1 hour (3600 seconds)
        set_transient($transientKey, $supported_languages, 3600);
    }
}

function parseAcceptLanguage() {
    global $supported_languages;

    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) || $_SERVER['HTTP_ACCEPT_LANGUAGE'] === '') {
        return null;
    }

    $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    $languages = [];

    foreach (explode(',', $acceptLanguage) as $part) {
        $components = explode(';q=', $part);
        $lang = trim($components[0]);
        $quality = isset($components[1]) ? (float) $components[1] : 1.0;
        $languages[$lang] = $quality;
    }

    arsort($languages, SORT_NUMERIC);

    foreach ($languages as $lang => $quality) {
        $lang = explode('-', $lang)[0];

        if (array_key_exists($lang, $supported_languages)) {
            return $lang;
        }
    }

    return 'fr';
}

function check_lang_version() {
    if (get_option('lang_version') === false) {
        update_option('lang_version', time());
    }

    $current_version = get_option('lang_version', 0);

    if (!isset($_SESSION['lang_version']) || $_SESSION['lang_version'] != $current_version) {
        unset($_SESSION['lang']);
        $_SESSION['lang_version'] = $current_version;
    }
}

// function rewrite_rule_for_lang_slugs() {
//     // Supported languages
//     global $supported_languages;
//     $languages_pattern = implode('|', array_keys($supported_languages));
//     $post_types = get_post_types([], 'names');

//     foreach ($supported_languages as $key => $lang) {
//         // Add a rewrite rule for each language slug
//         add_rewrite_rule(
//             '^' . $key . '/(.*)?',
//             'index.php?pagename=$matches[1]', // Adjust based on your site's structure
//             'top'
//         );
//     }

//     // Ensure the rewrite rules are flushed
//     flush_rewrite_rules(false);
// }

function rewrite_rule_for_lang_slugs() {
    $post_types = get_post_types([], 'names');

    // Get supported languages (associative array)
    global $supported_languages;
    $languages_pattern = implode('|', array_keys($supported_languages)); // Use array_keys to get just the language slugs (keys)

    // Loop through each post type and add rewrite rule
    foreach ($post_types as $post_type) {
        // Skip default post types like 'post', 'page'
        if (in_array($post_type, ['post', 'page'])) {
            continue;
        }

        // Add rewrite rule for each CPT with dynamic language slugs
        add_rewrite_rule(
            '^(' . $languages_pattern . ')/' . $post_type . '/(.*)?',  // Matches language slugs (keys) and CPT slugs
            'index.php?post_type=' . $post_type . '&name=$matches[2]', // Pass matched CPT slug
            'top'
        );
    }

    // Add rewrite rule for pages
    add_rewrite_rule(
        '^(' . $languages_pattern . ')/(.*)?',  // Matches language slugs (keys) and page slugs
        'index.php?pagename=$matches[2]', // Pass matched page slug
        'top'
    );

    // Uncomment during testing to flush rewrite rules
    //flush_rewrite_rules(false); // Only flush once when testing or during plugin activation
}

function initSessionLang() {
    fetchSupportedLanguages();
    check_lang_version();
    rewrite_rule_for_lang_slugs();

    if (!session_id()) {
        session_start();
    }

    if (!isset($_SESSION['lang']) && $acceptLang = parseAcceptLanguage()) {
        $_SESSION['lang'] = $acceptLang;
    }
}

add_action('init', 'initSessionLang');

// Update language version on save
function update_lang_version($term_id, $tt_id, $taxonomy) {
    if ($taxonomy === 'taxonomy_language') {
        update_option('lang_version', time()); // Use current timestamp as version
    }
}

add_action('edited_taxonomy_language', 'update_lang_version', 10, 3);
add_action('created_taxonomy_language', 'update_lang_version', 10, 3);

// function add_hreflang_tags() {
//     // Define supported languages dynamically or use your existing global list.
//     global $supported_languages;

//     // Get the current URL.
//     $current_url = home_url($_SERVER['REQUEST_URI']);

//     // Generate hreflang links for each language.
//     foreach ($supported_languages as $key => $lang) {
//         $alternate_url = str_replace(home_url(), home_url('/' . $key), $current_url);
//         echo '<link rel="alternate" hreflang="' . $key . '" href="' . esc_url($alternate_url) . '" />';
//     }
// }

// function add_hreflang_tags() {
//     global $supported_languages;

//     $current_uri = $_SERVER['REQUEST_URI'];

//     // Remove the current language prefix from the URL if it exists.
//     $current_uri = preg_replace('/^\/[a-z]{2}\//', '/', $current_uri);

//     // Generate hreflang links for each language.
//     foreach ($supported_languages as $key => $lang) {
//         $alternate_url = home_url('/' . $key . $current_uri);

//         echo '<link rel="alternate" hreflang="' . esc_attr($key) . '" href="' . esc_url($alternate_url) . '" />' . PHP_EOL;
//     }
// }

function add_hreflang_tags() {
    if ( !is_singular() && !is_page() && !is_home() ) {
        return;
    }

    global $supported_languages;

    if (is_singular(['blog', 'advanced_product'])) {
        $current_post_id = get_the_ID();

        foreach ($supported_languages as $lang_slug => $lang_name) {
            // Get the translated post object.
            $translated_post = get_translated_post($current_post_id, $lang_slug);

            if ($translated_post) {
                $current_url = get_permalink($translated_post->ID);

                // Parse the URL to ensure consistent structure.
                $parsed_url = parse_url($current_url);

                // Extract the path and ensure it's valid.
                $path = isset($parsed_url['path']) ? $parsed_url['path'] : '/';

                // Check if the path contains a supported language slug at the start.
                $language_pattern = '/^\/(' . implode('|', array_keys($supported_languages)) . ')(\/|$)/';
                $clean_path = preg_replace($language_pattern, '/', $path);

                // Construct the base URL (protocol + domain).
                $base_url = $parsed_url['scheme'] . '://' . $parsed_url['host'];

                if (isset($parsed_url['port'])) {
                    $base_url .= ':' . $parsed_url['port'];
                }

                $alternate_url = $base_url . '/' . $lang_slug . rtrim($clean_path, '/');
                echo '<link rel="alternate" hreflang="' . $lang_slug . '" href="' . esc_url($alternate_url) . '" />';
            }
        }
    }
    else {
        // Get the current URL.
        $current_url = home_url($_SERVER['REQUEST_URI']);

        // Parse the URL to ensure consistent structure.
        $parsed_url = parse_url($current_url);

        // Extract the path and ensure it's valid.
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '/';

        // Check if the path contains a supported language slug at the start.
        $language_pattern = '/^\/(' . implode('|', array_keys($supported_languages)) . ')(\/|$)/';
        $clean_path = preg_replace($language_pattern, '/', $path);

        // Construct the base URL (protocol + domain).
        $base_url = $parsed_url['scheme'] . '://' . $parsed_url['host'];

        if (isset($parsed_url['port'])) {
            $base_url .= ':' . $parsed_url['port'];
        }

        // Generate hreflang links for each language.
        foreach ($supported_languages as $key => $lang) {
            // Add the language slug to the cleaned path.
            $alternate_url = $base_url . '/' . $key . rtrim($clean_path, '/');
            echo '<link rel="alternate" hreflang="' . $key . '" href="' . esc_url($alternate_url) . '" />';
        }
    }
}

add_action('wp_head', 'add_hreflang_tags');

add_action('template_redirect', function () {
    global $supported_languages;

    // Parse the current URL
    $requested_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $segments = explode('/', $requested_path);

    // Detect if a language slug is present
    $language_slug = array_key_exists($segments[0], $supported_languages) ? $segments[0] : null;

    // Logic to redirect
    if (isset($_SESSION['lang'])) {
        // If language slug is present and doesn't match the preferred language, redirect to the non-slug version
        if ($language_slug) {
            array_shift($segments); // Remove the language slug
            $new_path = '/' . implode('/', $segments);
            wp_safe_redirect(home_url($new_path));
            exit;
        }
    }
    else {
        // If no preferred language is set, ensure the language slug is present
        if (!$language_slug) {
            // Default to English if no language slug is present
            $default_language = 'fr'; // Adjust if needed
            wp_safe_redirect(home_url('/' . $default_language . '/' . $requested_path));
            exit;
        }
        // Set the detected language in the session
        $_SESSION['lang'] = $language_slug;
        define('CURRENT_LANGUAGE', $language_slug);
    }
});

function load_homepage_for_language_slugs($query) {
    global $supported_languages;

    if (!is_admin() && $query->is_main_query()) {
        // Extract the first path segment from the URL
        $request_uri = trim($_SERVER['REQUEST_URI'], '/');

        // If the first segment is a language slug, load the homepage
        if (array_key_exists($request_uri, $supported_languages)) {
            $query->set('page_id', get_option('page_on_front')); // Load homepage
            $query->is_404 = false; // Prevent 404 errors
            $query->is_home = true; // Treat it as homepage
        }
    }
}
add_action('pre_get_posts', 'load_homepage_for_language_slugs');

add_filter('post_type_link', function ($post_link, $post) {
    if (defined('CURRENT_LANGUAGE') && CURRENT_LANGUAGE) {
        //$post_link = home_url('/' . CURRENT_LANGUAGE . '/' . $post->post_name . '/');

        $cpt_slug = get_post_type_object($post->post_type)->rewrite['slug'];

        // Modify the post link to include language slug and CPT slug
        $post_link = home_url("/" . CURRENT_LANGUAGE . "/$cpt_slug/" . $post->post_name . "/");
    }
    return $post_link;
}, 10, 2);

function get_translated_post($post_id, $lang) {
    if (!$post_id) {
        return null;
    }

    // Get the post type of the current post
    $post_type = get_post_type($post_id);
    if (!$post_type) {
        return null;
    }

    // Construct the dynamic field name
    if ($post_type === 'advanced_product') {
        $translation_group_field = $post_type . '_woo_id';
    }
    else {
        $translation_group_field = "{$post_type}_translate_group";
    }

    // Retrieve the translation group field value for the current post
    $translation_group = get_field($translation_group_field, $post_id);

    if ($translation_group) {
        // Query for a post with the same translation group and desired language
        $args = [
            'post_type'  => $post_type,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key'     => $translation_group_field,
                    'value'   => $translation_group,
                    'compare' => '='
                ],
            ],
            'tax_query' => [
                [
                    'taxonomy' => 'taxonomy_language',
                    'field'    => 'slug',
                    'terms'    => $lang,
                ],
            ],
        ];

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            return $query->posts[0]; // Return the first matching post
        }
    }

    return null; // No translation found
}






//define('LOST_TRANSLATIONS_FILE', __DIR__ . '/lost_translations.php');
define('DEFAULT_TRANSLATIONS_FILE', __DIR__ . '/default_translations.php');

// Initialize or load the translation array.
// if (file_exists(LOST_TRANSLATIONS_FILE)) {
//     $translation_array = include LOST_TRANSLATIONS_FILE;
// } else {
//     $translation_array = [];
// }

if (file_exists(DEFAULT_TRANSLATIONS_FILE)) {
    $default_translations = include DEFAULT_TRANSLATIONS_FILE;
} else {
    $default_translations = [];
}

// Function to save the translation array to a file.
// function save_translation_array() {
//     global $translation_array;

//     // Generate PHP code to store the array.
//     $exported_array = var_export($translation_array, true);
//     $php_code = "<?php\n\nreturn " . $exported_array . ";\n";

//     // Save to the file.
//     file_put_contents(LOST_TRANSLATIONS_FILE, $php_code);
// }

// // Register a shutdown function to save the array at the end of the request.
// register_shutdown_function('save_translation_array');

// function get_static_content($slug) {
//     if (!session_id()) {
//         initSessionLang();
//     }

//     if (class_exists('ACF')) {
//         error_log('class_exists(ACF)');
//         // Force ACF initialization
//         do_action('acf/init');
//     }

//     error_log('get_object_taxonomies: ' . print_r(get_object_taxonomies('static_content', 'names'), true));
//     error_log('get_terms' . print_r(get_terms('taxonomy_language'), true));


//     $transient_key = "static_content_{$slug}_{$_SESSION['lang']}";

//     // Check if the content is cached
//     $cached_content = get_transient($transient_key);

//     if ($cached_content !== false) {
//         //return $cached_content; // Return cached content
//     }

//     error_log('$_SESSION[lang] = ' . $_SESSION['lang'] . '; slug = ' . $slug);
//     error_log('session id = ' . session_id());

//     // Query for static content matching the slug and language
//     $args = [
//         'post_type'  => 'static_content',
//         'tax_query'  => [
//             [
//                 'taxonomy' => 'taxonomy_language',
//                 'field'    => 'slug',
//                 'terms'    => $_SESSION['lang'],
//             ],
//         ],
//     ];

//     $query = new WP_Query($args);

//     error_log(print_r($query, true));

//     if ($query->have_posts()) {
//         error_log('have_posts: ' . $slug);

//         $post_id = $query->posts[0]->ID; // Get the ID of the first matching post
//         $content = get_field("static_{$slug}", $post_id); // Fetch the ACF field
//         wp_reset_postdata();

//         error_log('content = ' . $content);

//         if ($content) {
//             set_transient($transient_key, $content, HOUR_IN_SECONDS);
//         }
//         else {
//             global $translation_array;
//             $translation_array[$slug] = null;
//             $content = $slug;
//         }

//         return $content;
//     }

//     error_log('does not have posts: ' . $slug . ' request url: ' . $_SERVER['REQUEST_URI'] . ' query: ' . $_SERVER['QUERY_STRING']);

//     wp_reset_postdata();

//     return '';
// }


function get_static_content($slug) {
    global $wpdb;
    $transient_key = "static_content_{$slug}_{$_SESSION['lang']}";

    // Check if the content is cached
    $cached_content = get_transient($transient_key);
    if ($cached_content !== false) {
        return $cached_content; // Return cached content
    }

    // Define the taxonomy and meta keys
    $taxonomy = 'taxonomy_language';
    $meta_key = "static_{$slug}";
    $language = sanitize_text_field($_SESSION['lang']); // Sanitize the language

    // SQL query to get the post ID that matches the slug and language
    $query = $wpdb->prepare("
        SELECT p.ID
        FROM {$wpdb->posts} AS p
        INNER JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id
        INNER JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id
        WHERE p.post_type = 'static_content'
          AND p.post_status = 'publish'
          AND tt.taxonomy = %s
          AND t.slug = %s
        LIMIT 1
    ", $taxonomy, $language);

    $post_id = $wpdb->get_var($query); // Get the first matching post ID

    // if ($post_id) {
    //     // Retrieve the ACF field value
    //     //$content = get_field($meta_key, $post_id); // ACF function to get the field
    //     $content = $wpdb->get_var(
    //         $wpdb->prepare(
    //             "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s",
    //             $post_id,
    //             $meta_key
    //         )
    //     );

    //     if ($content) {
    //         set_transient($transient_key, $content, HOUR_IN_SECONDS); // Cache the content
    //         return $content;
    //     }
    //     // else {
    //     //     global $translation_array;
    //     //     $translation_array[$slug] = null;
    //     //     return $slug;
    //     // }
    // }

    if ($post_id) {
        // Fetch both the raw field value and the field key in one query
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT meta_key, meta_value
                FROM {$wpdb->postmeta}
                WHERE post_id = %d AND meta_key IN (%s, %s)
                ",
                $post_id,
                $meta_key,
                "_{$meta_key}" // Field key meta
            ),
            OBJECT_K
        );

        if (!empty($results) && isset($results[$meta_key]) && isset($results["_{$meta_key}"])) {
            $content = $results[$meta_key]->meta_value;
            $field_key = $results["_{$meta_key}"]->meta_value;

            // Retrieve field settings using the field key
            $field_settings_serialized = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT post_content FROM {$wpdb->posts} WHERE post_name = %s AND post_type = 'acf-field'",
                    $field_key
                )
            );

            if ($field_settings_serialized) {
                $field_settings = maybe_unserialize($field_settings_serialized);

                if ($slug === 'loyalty_program_text') {
                    error_log(print_r($field_settings, true));
                }

                if (isset($field_settings['new_lines'])) {
                    switch ($field_settings['new_lines']) {
                        case 'wpautop': // Automatically add <p> tags
                            $content = wpautop($content);
                            break;
                        case 'br': // Automatically add <br> tags
                            $content = nl2br($content);
                            break;
                        case 'none': // No formatting
                        default:
                            break;
                    }
                }
            }

            if ($content) {
                set_transient($transient_key, $content, HOUR_IN_SECONDS); // Cache the content
                return $content;
            }
        }
    }

    return ''; // Return empty if no matching post is found
}





function custom_translation_handler( $translated, $original, $domain ) {
    global $default_translations;

    if (array_key_exists($original, $default_translations)) {
        $translated = get_static_content($default_translations[$original]);
    }

    return $translated;
}

add_filter('gettext', 'custom_translation_handler', 20, 3);

add_filter('gettext_with_context', 'custom_translation_handler_with_context', 10, 4);

function custom_translation_handler_with_context($translated, $original, $context, $domain) {
    global $default_translations;

    if (array_key_exists($original, $default_translations)) {
        $translated = get_static_content($default_translations[$original]);
    }

    return $translated;
}

add_filter('ngettext', 'custom_translation_handler_for_single_plural', 10, 5);

function custom_translation_handler_for_single_plural($translation, $single, $plural, $number, $domain) {
    global $default_translations;

    if ($number === 1 && array_key_exists($single, $default_translations)) {
        $translation = get_static_content($default_translations[$single]);
    }
    elseif (array_key_exists($plural, $default_translations)) {
        $translation = get_static_content($default_translations[$plural]);
    }

    return $translation;
}



//remove_filter('gettext', 'custom_translation_handler');

// if (!function_exists('__')) {
//     function __($text, $domain = 'default') {
//         error_log('__("' . $text . '"');

//         return apply_filters('gettext', $text, $text, $domain);
//     }
// }

function clear_static_content_cache_for_language($language_slug) {
    global $wpdb;

    // Prefix for transients
    $prefix = '_transient_static_content_';

    $sql = "DELETE FROM {$wpdb->options} WHERE option_name LIKE '" . $wpdb->esc_like($prefix) . '%' . $wpdb->esc_like('_') . $language_slug . "'";

    // Delete transients matching the specific language
    $wpdb->query($sql);

    // Delete timeout entries for the transients
    $timeout_prefix = '_transient_timeout_static_content_';

    $sql = "DELETE FROM {$wpdb->options} WHERE option_name LIKE '" . $wpdb->esc_like($timeout_prefix) . '%' . $wpdb->esc_like('_') . $language_slug . "'";

    $wpdb->query($sql);
}

function on_static_content_change($post_id) {
    if (wp_is_post_revision($post_id)) return;

    // Check post type
    $post_type = get_post_type($post_id);

    if ($post_type !== 'static_content') return;

    // Get the taxonomy_language term assigned to the post
    $terms = wp_get_post_terms($post_id, 'taxonomy_language', ['fields' => 'slugs']);

    if (!is_wp_error($terms) && !empty($terms)) {
        $language_slug = $terms[0]; // Assuming a single language term per post
        clear_static_content_cache_for_language($language_slug);
    }
}

add_action('save_post_static_content', 'on_static_content_change');
add_action('delete_post', 'on_static_content_change');
