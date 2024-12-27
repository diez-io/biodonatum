<?php

if (file_exists(get_template_directory() . '/inc/woocommerce/cart.php')) {
    require_once get_template_directory() . '/inc/woocommerce/cart.php';
}

if (file_exists(get_template_directory() . '/inc/woocommerce/registration.php')) {
    require_once get_template_directory() . '/inc/woocommerce/registration.php';
}

function google_fonts() {?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap" rel="stylesheet">
<?}

function theme_enqueue_assets() {
    // Enqueue CSS
    wp_enqueue_style('my-theme-style', get_template_directory_uri() . '/css/main.css');
    wp_enqueue_style('my-theme-header', get_template_directory_uri() . '/css/header.css');

    // Enqueue JS
    wp_enqueue_script('my-theme-script', get_template_directory_uri() . '/js/bundle.js', array(), null, true); // true loads it in the footer
    wp_enqueue_script('my-theme-common-script', get_template_directory_uri() . '/js/common.js', array(), null, true);

    wp_localize_script('my-theme-common-script', 'common', [
        //'siteUrl' => get_site_url(),
        //'ajaxUrl' => admin_url('admin-ajax.php'),
        //'nonce'   => wp_create_nonce('my_nonce'),
        'staticContent' => [
            'fill_out_this_field' => get_static_content('please_fill_out_this_field'),
        ],
    ]);
    wp_enqueue_script('st_select', get_template_directory_uri() . '/js/st_select.min.js', array(), null, false); // st_select lib
    wp_enqueue_script('st_mask', get_template_directory_uri() . '/js/st_mask.min.js', array(), null, false); // st_mask lib
}

add_action('wp_enqueue_scripts', 'theme_enqueue_assets');
add_action('wp_head', 'google_fonts');

function handle_language_switch() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lang'])) {
        $language = sanitize_text_field($_POST['lang']);
        session_start(); // Start the session if not already started
        $_SESSION['lang'] = $language;

        // Get the current URL
        $current_url = $_SERVER['REQUEST_URI'];

        // Get the current post ID (if applicable)
        if (is_singular()) {
            $current_post_id = get_the_ID();

            // Try to find a translated post for the chosen language
            $translated_post = get_translated_post($current_post_id, $_SESSION['lang']);

            if ($translated_post) {
                // Redirect to the translated post
                wp_safe_redirect(get_permalink($translated_post->ID));
                exit;
            }
        }

        // // If not a singular post or no translation found, rewrite the URL with the chosen language
        // $home_url = home_url('/');
        // $current_url_relative = str_replace($home_url, '', $current_url);

        // // Add the chosen language to the URL
        // $redirect_url = '/' . ltrim($current_url_relative, '/');

        // wp_safe_redirect($redirect_url);
        // exit;


        wp_safe_redirect(home_url($current_url));
    }
}

add_action('template_redirect', 'handle_language_switch');

function get_static_content($slug) {
    $transient_key = "static_content_{$slug}_{$_SESSION['lang']}";

    // Check if the content is cached
    $cached_content = get_transient($transient_key);

    if ($cached_content !== false) {
        return $cached_content; // Return cached content
    }

    // Query for static content matching the slug and language
    $args = [
        'post_type'  => 'static_content',
        'tax_query'  => [
            [
                'taxonomy' => 'taxonomy_language',
                'field'    => 'slug',
                'terms'    => $_SESSION['lang'],
            ],
        ],
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $post_id = $query->posts[0]->ID; // Get the ID of the first matching post
        $content = get_field("static_{$slug}", $post_id); // Fetch the ACF field
        wp_reset_postdata();

        set_transient($transient_key, $content, HOUR_IN_SECONDS);

        return $content;
    }

    wp_reset_postdata();

    return '';
}

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

add_action('wp_enqueue_scripts', function() {
    wp_deregister_style('contact-form-7');
});

add_filter('wpcf7_form_elements', 'remove_cf7_br_tags');

function remove_cf7_br_tags($form) {
    // Use regex to remove all <br> tags
    $form = preg_replace(['/<br\s*\/?>/i', '/<\/?p[^>]*>/'], '', $form);
    return $form;
}

function get_cf7_form_by_title($title) {
    if (class_exists('WPCF7_ContactForm')) {
        $forms = WPCF7_ContactForm::find(array('title' => $title . '_' . $_SESSION['lang']));

        if (!empty($forms) && is_array($forms)) {
            $form = reset($forms); // Get the first matching form

            return do_shortcode('[contact-form-7 id="' . $form->id() . '" title="' . $form->title() . '"]');
        }
    }

    return ''; // Return empty string if no form found
}

add_filter('woocommerce_enqueue_styles', '__return_empty_array');
add_action('wp_footer', function() {
    ?>
    <script>
        new st_select();
        new st_mask({ //Маска для телефона
            selector: 'input[type=phone]',
            mask: "+7 ({\\d}{\\d}{\\d}) {\\d}{\\d}{\\d} - {\\d}{\\d} - {\\d}{\\d}",
            placeholder: true,
            filler: '_',
            full: function (input) {input.removeAttribute('invalid')},
            not_full: function (input) {input.setAttribute('invalid')}
        });
        new st_mask({ //Маска для телефона
            selector: 'input[type=email]',
            mask: "+7 ({\\d}{\\d}{\\d}) {\\d}{\\d}{\\d} - {\\d}{\\d} - {\\d}{\\d}",
            placeholder: true,
            filler: '_',
            full: function (input) {input.removeAttribute('invalid')},
            not_full: function (input) {input.setAttribute('invalid')}
        });
    </script>
    <?
});
