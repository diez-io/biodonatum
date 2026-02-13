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

add_action('woocommerce_thankyou', 'add_conversion_tracking');
function add_conversion_tracking($order_id) {
    if (!$order_id) return;
    echo "<script> gtag('event', 'conversion', {'send_to': 'AW-16544789369/zrHWCNzhnuAZEPnmldE9'}); </script>";
}


function theme_enqueue_assets() {
    // Enqueue CSS
    wp_enqueue_style('my-theme-style', get_template_directory_uri() . '/css/main.css');
    wp_enqueue_style('my-theme-header', get_template_directory_uri() . '/css/header.css');
    wp_enqueue_style('my-theme-account', get_template_directory_uri() . '/css/account.css');
    wp_enqueue_style('my-theme-common-styles', get_template_directory_uri() . '/css/common.css');

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

    if (is_account_page()) {
        wp_enqueue_script( 'wc-add-payment-method' );
    }

    if (is_checkout()) {
        wp_enqueue_style('checkout-style', get_template_directory_uri() . '/css/checkout.css');
    }


    wp_enqueue_script('st_select', get_template_directory_uri() . '/js/st_select.min.js', array(), null, false); // st_select lib
}

add_action('wp_enqueue_scripts', 'theme_enqueue_assets');
add_action('wp_head', 'google_fonts');

function handle_language_switch() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['lang'])) {
        return;
    }
    $language = sanitize_text_field($_POST['lang']);
    global $supported_languages;
    if (empty($supported_languages) && function_exists('fetchSupportedLanguages')) {
        fetchSupportedLanguages();
    }
    if (empty($supported_languages) || !array_key_exists($language, $supported_languages)) {
        return;
    }

    // If singular and a translation exists, redirect to the translated post URL (with language prefix)
    if (is_singular()) {
        $current_post_id = get_the_ID();
        $translated_post = function_exists('get_translated_post') ? get_translated_post($current_post_id, $language) : null;
        if ($translated_post) {
            wp_safe_redirect(get_permalink($translated_post->ID));
            exit;
        }
    }

    // Build path with new language prefix: replace or add /lang/ at the start
    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $segments = $path === '' ? [] : explode('/', $path);
    if (!empty($segments) && !empty($supported_languages) && array_key_exists($segments[0], $supported_languages)) {
        array_shift($segments);
    }
    $rest = implode('/', $segments);
    $new_path = $rest === '' ? $language : $language . '/' . $rest;
    $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    $redirect_url = home_url('/' . $new_path . '/');
    if ($query) {
        $redirect_url .= '?' . $query;
    }
    wp_safe_redirect($redirect_url);
    exit;
}

add_action('template_redirect', 'handle_language_switch');

/**
 * Returns URL with current language prefix in path (e.g. /en/cart/). Use for cart, checkout, myaccount, shop, home.
 */
function biodonatum_url_with_lang($url) {
    $lang = function_exists('get_current_language') ? get_current_language() : '';
    if ($lang === '') {
        return $url;
    }
    $path = parse_url($url, PHP_URL_PATH);
    if ($path === null || $path === '') {
        return home_url('/' . $lang . '/');
    }
    $path = trim($path, '/');
    return $path === '' ? home_url('/' . $lang . '/') : home_url('/' . $lang . '/' . $path . '/');
}

/**
 * SEO meta: early computation of post_object + language_slug, find seo_meta record, store in global, start output buffer.
 * Buffer callback (late) replaces <title> and <meta name="description"> in final HTML.
 */
function biodonatum_seo_meta_early_run() {
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return;
    }
    if (!function_exists('get_current_language')) {
        return;
    }

    $post_object = null;
    if (is_front_page()) {
        $front_id = (int) get_option('page_on_front');
        if ($front_id > 0) {
            $post_object = get_post($front_id);
        }
    } elseif (is_singular()) {
        $post_object = get_queried_object();
        if (!($post_object instanceof WP_Post)) {
            $post_object = get_post(get_queried_object_id());
        }
    }
    if (!$post_object || !($post_object instanceof WP_Post)) {
        return;
    }

    $language_slug = get_current_language();

    $q = new WP_Query([
        'post_type' => 'seo-meta',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'no_found_rows' => true,
        'tax_query' => [
            [
                'taxonomy' => 'taxonomy_language',
                'field' => 'slug',
                'terms' => $language_slug,
            ],
        ],
        'meta_query' => [
            [
                'key' => 'post_object',
                'value' => $post_object->ID,
                'compare' => '=',
            ],
        ],
    ]);

    if (!$q->have_posts()) {
        return;
    }
    $seo_post = $q->posts[0];
    wp_reset_postdata();

    if (function_exists('get_field')) {
        $GLOBALS['biodonatum_seo_meta'] = [
            'title' => trim(get_field('title', $seo_post->ID)),
            'description' => trim(get_field('description', $seo_post->ID)),
            'keywords' => implode(', ', array_map(fn($row) => $row['keyword'] ?? '', get_field('keywords', $seo_post->ID) ?: []))
        ];

        ob_start('biodonatum_seo_meta_buffer_callback');
    }
}
add_action('template_redirect', 'biodonatum_seo_meta_early_run', 20);

/**
 * Buffer callback: replace <title> and <meta name="description"> in final HTML with values from seo_meta.
 */
function biodonatum_seo_meta_buffer_callback($html) {
    if (!is_string($html) || empty($GLOBALS['biodonatum_seo_meta'])) {
        return $html;
    }
    $meta = $GLOBALS['biodonatum_seo_meta'];
    $title = isset($meta['title']) ? $meta['title'] : '';
    $description = isset($meta['description']) ? $meta['description'] : '';
    $keywords = isset($meta['keywords']) ? $meta['keywords'] : '';

    if ($title !== '') {
        $escaped_title = esc_html($title);
        $html = preg_replace('/<title>\s*.*?\s*<\/title>/s', '<title>' . $escaped_title . '</title>', $html, 1);
    }

    if ($description !== '') {
        $escaped_description = esc_attr(str_replace(["\r", "\n"], ' ', $description));
        if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\']([^"\']*)["\']/i', $html)) {
            $html = preg_replace('/(<meta\s+name=["\']description["\']\s+content=)(["\'])([^\2]*?)\2/i', '$1$2' . $escaped_description . '$2', $html, 1);
        } else {
            $tag = '<meta name="description" content="' . $escaped_description . '">';
            $html = preg_replace('/<\/head>/i', $tag . "\n</head>", $html, 1);
        }
    }

    if ($keywords !== '') {
        $escaped_keywords = esc_attr(str_replace(["\r", "\n"], ' ', $keywords));
        if (preg_match('/<meta\s+name=["\']keywords["\']\s+content=["\']([^"\']*)["\']/i', $html)) {
            $html = preg_replace('/(<meta\s+name=["\']keywords["\']\s+content=)(["\'])([^\2]*?)\2/i', '$1$2' . $escaped_keywords . '$2', $html, 1);
        } else {
            $tag = '<meta name="keywords" content="' . $escaped_keywords . '">';
            $html = preg_replace('/<\/head>/i', $tag . "\n</head>", $html, 1);
        }
    }

    return $html;
}

add_action('wp_enqueue_scripts', function() {
    wp_deregister_style('contact-form-7');
});

add_filter('wpcf7_form_elements', 'remove_cf7_br_tags');

function remove_cf7_br_tags($form) {
    // Use regex to remove all <br> tags
    $form = preg_replace(['/<br\s*\/?>/i', '/<\/?p[^>]*>/', '/ lang=".*?"/', '/ dir=".*?"/'], '', $form);
    return $form;
}

function get_cf7_form_by_title($title, $htmlClass = 'form-custom') {
    if (class_exists('WPCF7_ContactForm')) {
        $lang = function_exists('get_current_language') ? get_current_language() : 'en';
        $forms = WPCF7_ContactForm::find(array('title' => $title . '_' . $lang));

        if (!empty($forms) && is_array($forms)) {
            $form = reset($forms); // Get the first matching form
            $html = do_shortcode('[contact-form-7 id="' . $form->id() . '" title="' . $form->title() . '" html_class="'. $htmlClass .'"]');
            $html = preg_replace(['/ lang=".*?"/', '/ dir=".*?"/'], '', $html);

            return $html;
        }
    }

    return ''; // Return empty string if no form found
}

add_filter('woocommerce_enqueue_styles', '__return_empty_array');
add_action('wp_footer', function() {
    ?>
    <script>
        new st_select('.st_select');
    </script>
    <?
});

add_action('woocommerce_save_account_details', 'custom_redirect_after_account_save', 20);

function custom_redirect_after_account_save() {
    // Check if there are no error notices.
    if (wc_notice_count('error') === 0) {
        // Add a success notice.
        wc_add_notice(get_static_content('account_details_changed_successfully'));

        // Redirect to the My Account page instead of the edit-account endpoint.
        wp_safe_redirect(get_permalink( get_option('woocommerce_myaccount_page_id')));
        exit;
    }
}

//add_filter('woocommerce_available_payment_gateways', 'force_add_payment_method_gateways');

// function force_add_payment_method_gateways($available_gateways) {
//     if (is_account_page() && !is_wc_endpoint_url('payment-methods')) {
//         // Get all gateways supporting 'add_payment_method' or 'tokenization'
//         foreach (WC()->payment_gateways->payment_gateways() as $gateway_id => $gateway) {
//             if ($gateway->supports('add_payment_method') || $gateway->supports('tokenization')) {
//                 $available_gateways[$gateway_id] = $gateway;
//             }
//         }
//     }

//     return $available_gateways;
// }

add_filter('woocommerce_available_payment_gateways', 'custom_available_payment_gateways');

function custom_available_payment_gateways($gateways) {
    if (is_account_page() && !is_wc_endpoint_url('payment-methods') && !is_wc_endpoint_url('add-payment-method')) {
        foreach ($gateways as $gateway_id => $gateway) {
            if (!$gateway->supports('add_payment_method') && !$gateway->supports('tokenization')) {
                unset($gateways[$gateway_id]);
            }

            $gateway->tokenization_script();
        }
    }

    return $gateways;
}

function wc_update_locale_in_stripe_element_options( $options ) {
    $locale = function_exists('get_current_language') ? get_current_language() : 'en';
    return array_merge(
        $options,
        array(
            'locale' => $locale,
        )
    );
};
add_filter( 'wcpay_payment_fields_js_config', 'wc_update_locale_in_stripe_element_options' );

add_action('init', 'handle_profile_image_upload');
function handle_profile_image_upload() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['upload_profile_picture']) && !empty($_FILES['image']['name'])) {
            if (!isset($_POST['profile_picture_upload_nonce']) || !check_admin_referer('profile_picture_upload_action', 'profile_picture_upload_nonce')) {
                wp_die('Security check failed.');
            }

            $user_id = get_current_user_id();

            if (!$user_id) {
                return;
            }

            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Handle the upload
            $uploaded_image = media_handle_upload('image', 0);

            if (is_wp_error($uploaded_image)) {
                echo "Error uploading image.";
            }
            else {
                // Get the image URL and save it as user meta
                $image_url = wp_get_attachment_url($uploaded_image);
                update_user_meta($user_id, 'profile_image_url', $image_url);
            }
        }

        if (isset($_POST['remove_profile_picture'])) {
            if (!isset($_POST['profile_picture_remove_nonce']) || !check_admin_referer('profile_picture_remove_action', 'profile_picture_remove_nonce')) {
                wp_die('Security check failed.');
            }

            $user_id = get_current_user_id();

            // Remove the profile picture (delete user meta)
            if (!delete_user_meta($user_id, 'profile_image_url')) {
                wp_die('Failed to remove profile picture.');
            }
        }
    }
}



add_action('admin_menu', 'create_custom_content_menu');
add_action('init', 'move_dynamic_acf_post_types_under_menu', 20);

function create_custom_content_menu() {
    $edit_link = admin_url("edit-tags.php?taxonomy=taxonomy_language");

    add_menu_page(
        'Languages',
        'Languages',
        'manage_options',
        $edit_link,
        '',
        'dashicons-translation',
        25,
    );

    add_menu_page(
        'Content',
        'Content',
        'manage_options',
        'custom-content-menu',
        '',
        'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGhlaWdodD0iMjRweCIgdmlld0JveD0iMCAtOTYwIDk2MCA5NjAiIHdpZHRoPSIyNHB4IiBmaWxsPSIjNzVGQjRDIj48cGF0aCBkPSJtNDc2LTgwIDE4Mi00ODBoODRMOTI0LTgwaC04NGwtNDMtMTIySDYwM0w1NjAtODBoLTg0Wk0xNjAtMjAwbC01Ni01NiAyMDItMjAycS0zNS0zNS02My41LTgwVDE5MC02NDBoODRxMjAgMzkgNDAgNjh0NDggNThxMzMtMzMgNjguNS05Mi41VDQ4NC03MjBINDB2LTgwaDI4MHYtODBoODB2ODBoMjgwdjgwSDU2NHEtMjEgNzItNjMgMTQ4dC04MyAxMTZsOTYgOTgtMzAgODItMTIyLTEyNS0yMDIgMjAxWm00NjgtNzJoMTQ0bC03Mi0yMDQtNzIgMjA0WiIvPjwvc3ZnPg==',
        25
    );
}

function move_dynamic_acf_post_types_under_menu() {
    $parent_menu_slug = 'custom-content-menu';

    $post_types = [];

    // Retrieve all registered post types
    $all_post_types = get_post_types([], 'objects');

    foreach ($all_post_types as $post_type) {
        $taxonomies = get_object_taxonomies($post_type->name);

        if (in_array('taxonomy_language', $taxonomies)) {
            $post_types[] = $post_type->name;
        }
    }

    foreach ($post_types as $post_type) {
        add_submenu_page(
            $parent_menu_slug,
            get_post_type_object($post_type)->labels->name,
            get_post_type_object($post_type)->labels->menu_name,
            'manage_options',
            "edit.php?post_type=$post_type"
        );
    }
}


add_filter('locale', function() {
    return function_exists('get_current_language') ? get_current_language() : 'en';
});

// Remove sanctioned countries from WooCommerce country dropdowns
add_filter( 'woocommerce_countries_allowed_countries', function( $countries ) {
    $blocked = [
//         'IR', // Иран
//         'KP', // Северная Корея
//         'SD', // Судан
//         'SS', // Южный Судан
//         'UA', // Украина
//         'SY', // Сирия
//         'RU', // Российская Федерация
//         'MM', // Мьянма
//         'YE', // Йемен
    ];
    foreach ( $blocked as $code ) {
        unset( $countries[ $code ] );
    }
    return $countries;
});

add_action('template_redirect', function() {
    if (is_singular('review')) { 
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        exit;
    }
});

add_filter( 'woocommerce_get_price_html', 'my_add_discount_to_variation_price_html', 10, 2 );
/**
 * Добавляет блок "Скидка" к HTML цены для вариаций.
 *
 * @param string $price_html HTML текущей цены.
 * @param WC_Product $product Product object (может быть variation).
 * @return string Изменённый HTML
 */
function my_add_discount_to_variation_price_html( $price_html, $product ) {
    if ( ! $product instanceof WC_Product )
        return $price_html;

    if ( $product->is_type( 'variation' ) ) {
        $regular = (float) $product->get_regular_price();
        $price   = (float) $product->get_price();

        if ( $regular > 0 && $price < $regular ) {
            $diff = $regular - $price;
            $price_html .= '<span class="product-discount_container"><span class="product-discount">'.get_static_content('benefit').': '.wc_price( $diff ).'</span></span>';
        }
    }

    return $price_html;
}