<?php
/**
 * Plugin Name: Language Detector
 * Description: Detects and stores the client's preferred language using willdurand/negotiation.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
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

    return 'en';
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

add_action('init', function () {
    fetchSupportedLanguages();
    check_lang_version();
    rewrite_rule_for_lang_slugs();

    if (!session_id()) {
        session_start();
    }

    if (!isset($_SESSION['lang']) && $acceptLang = parseAcceptLanguage()) {
        $_SESSION['lang'] = $acceptLang;
    }
});

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

    if (is_singular('blog')) {
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
            $default_language = 'en'; // Adjust if needed
            wp_safe_redirect(home_url('/' . $default_language . '/' . $requested_path));
            exit;
        }
        // Set the detected language in the session
        $_SESSION['lang'] = $language_slug;
        define('CURRENT_LANGUAGE', $language_slug);
    }
});

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
    $translation_group_field = "{$post_type}_translate_group";

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