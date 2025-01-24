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

            return do_shortcode('[contact-form-7 id="' . $form->id() . '" title="' . $form->title() . '" html_class="form"]');
        }
    }

    return ''; // Return empty string if no form found
}

add_filter('woocommerce_enqueue_styles', '__return_empty_array');
add_action('wp_footer', function() {
    ?>
    <script>
        new st_select('.st_select');
        new st_mask({ //Маска для телефона
            selector: 'input[type=phone]',
            mask: "+7 ({\\d}{\\d}{\\d}) {\\d}{\\d}{\\d} - {\\d}{\\d} - {\\d}{\\d}",
            placeholder: true,
            filler: '_',
            full: function (input) {input.removeAttribute('invalid')},
            not_full: function (input) {input.setAttribute('invalid')}
        });
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

add_filter('wc_stripe_params', 'set_stripe_locale');
function set_stripe_locale($params) {
    $params['locale'] = $_SESSION['lang'];
    return $params;
}
