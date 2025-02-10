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

add_action('wp_enqueue_scripts', function() {
    wp_deregister_style('contact-form-7');
});

add_filter('wpcf7_form_elements', 'remove_cf7_br_tags');

function remove_cf7_br_tags($form) {
    // Use regex to remove all <br> tags
    $form = preg_replace(['/<br\s*\/?>/i', '/<\/?p[^>]*>/'], '', $form);
    return $form;
}

function get_cf7_form_by_title($title, $htmlClass = 'form-custom') {
    if (class_exists('WPCF7_ContactForm')) {
        $forms = WPCF7_ContactForm::find(array('title' => $title . '_' . $_SESSION['lang']));

        if (!empty($forms) && is_array($forms)) {
            $form = reset($forms); // Get the first matching form

            return do_shortcode('[contact-form-7 id="' . $form->id() . '" title="' . $form->title() . '" html_class="'. $htmlClass .'"]');
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
    return array_merge(
        $options,
        array(
            'locale' => $_SESSION['lang'] ?? 'en',
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
    return $_SESSION['lang'] ?? 'en';
});
