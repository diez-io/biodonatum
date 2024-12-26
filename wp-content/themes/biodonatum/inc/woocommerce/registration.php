<?
if (!defined('ABSPATH')) {
    exit;
}

function registration_enqueue_assets() {
    // Enqueue CSS
    wp_enqueue_style('my-theme-registration-style', get_template_directory_uri() . '/css/registration.css');

    // Enqueue JS
    wp_enqueue_script('my-theme-registration-script', get_template_directory_uri() . '/js/registration.js', array(), null, true); // true loads it in the footer

    wp_localize_script('my-theme-registration-script', 'registration_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}

add_action('wp_enqueue_scripts', 'registration_enqueue_assets');

// function get_account_url_ajax() {
//     if (is_user_logged_in()) {
//         wp_send_json([
//             'url' => get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ),
//         ]);
//     }
//     else {
//         ob_start();
//         get_template_part('components/login');
//         $output = ob_get_clean();  // Get the output and clean the buffer
//         echo $output;  // Echo the output so it can be sent to the AJAX request
//         wp_die();
//     }
// }

// add_action('wp_ajax_get_account_url', 'get_account_url_ajax');
// add_action('wp_ajax_nopriv_get_account_url', 'get_account_url_ajax');

function handle_custom_registration() {
    // Start output buffering to capture any output
    ob_start();

    $nonce_value = isset( $_POST['_wpnonce'] ) ? wp_unslash( $_POST['_wpnonce'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $nonce_value = isset( $_POST['woocommerce-register-nonce'] ) ? wp_unslash( $_POST['woocommerce-register-nonce'] ) : $nonce_value; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

    // Prepare default response structure
    $response = [
        'success' => false,
        'message' => '',
        'errors'  => [],
    ];

    // Check if email is set and nonce is valid
    if ( isset( $_POST['email'] ) && wp_verify_nonce( $nonce_value, 'woocommerce-register' ) ) {
        $username = 'no' === get_option( 'woocommerce_registration_generate_username' ) && isset( $_POST['username'] ) ? wp_unslash( $_POST['username'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $password = 'no' === get_option( 'woocommerce_registration_generate_password' ) && isset( $_POST['password'] ) ? $_POST['password'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $email    = wp_unslash( $_POST['email'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

        try {
            // Create an array to hold error messages
            $validation_error  = new WP_Error();
            $validation_error  = apply_filters( 'woocommerce_process_registration_errors', $validation_error, $username, $password, $email );
            $validation_errors = $validation_error->get_error_messages();

            if ( $validation_errors ) {
                // Collect validation errors
                foreach ( $validation_errors as $message ) {
                    $response['errors'][] = $message;
                }
                throw new Exception('Validation failed');
            }

            // Create new customer
            $new_customer = wc_create_new_customer( sanitize_email( $email ), wc_clean( $username ), $password );

            if ( is_wp_error( $new_customer ) ) {
                // Collect customer creation errors
                $response['errors'][] = $new_customer->get_error_message();
                throw new Exception('Customer creation failed');
            }

            // Set success message
            $response['success'] = true;
            $response['message'] = 'Your account was created successfully.';
            if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) ) {
                $response['message'] .= ' A password has been sent to your email address.';
            } else {
                $response['message'] .= ' Your login details have been sent to your email address.';
            }

            // Set customer authentication cookie
            wc_set_customer_auth_cookie( $new_customer );

        } catch ( Exception $e ) {
            // On error, return failure and include error messages
            if ( ! empty( $response['errors'] ) ) {
                $response['message'] = 'There were errors during registration.';
            }
        }
    } else {
        // Invalid request or nonce error
        $response['errors'][] = 'Invalid request or nonce.';
    }

    // Return strict JSON response with consistent structure
    wp_send_json( $response );
}
add_action('wp_ajax_nopriv_custom_register', 'handle_custom_registration');
