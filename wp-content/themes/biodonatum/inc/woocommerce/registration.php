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
            $validation_errors = $validation_error->get_error_codes();

            if ( $validation_errors ) {
                // Collect validation errors
                foreach ( $validation_errors as $code ) {
                    $response['errors'][$code] = 'Validation failed';
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
        $response['message'] = 'Invalid request or nonce.';
    }

    // Return strict JSON response with consistent structure
    wp_send_json( $response );
}
add_action('wp_ajax_nopriv_custom_register', 'handle_custom_registration');

function handle_custom_login() {
    error_log('handle_custom_login');
    static $valid_nonce = null;
    $errors = [];

    if ( null === $valid_nonce ) {
        // Retrieve and verify nonce.
        $nonce_value = wc_get_var( $_REQUEST['woocommerce-login-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) );
        $valid_nonce = wp_verify_nonce( $nonce_value, 'woocommerce-login' );
    }

    // Check if login details are set and nonce is valid.
    if ( isset( $_POST['username'], $_POST['password'] ) && $valid_nonce ) {

        try {
            $creds = array(
                'user_login'    => trim( wp_unslash( $_POST['username'] ) ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                'user_password' => $_POST['password'], // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
                'remember'      => isset( $_POST['rememberme'] ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            );

            $validation_error = new WP_Error();
            $validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $creds['user_login'], $creds['user_password'] );

            if ( $validation_error->get_error_code() ) {
                foreach ($validation_error->get_error_codes() as $errorCode) {
                    $errors[$errorCode] = 'Invalid username.';
                }

                throw new Exception();
            }

            if ( empty( $creds['user_login'] ) ) {
                throw new Exception( 'Username is required.');
            }

            // On multisite, ensure user exists on current site.
            if ( is_multisite() ) {
                $user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

                if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
                    add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
                }
            }

            // Perform the login.
            $user = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), is_ssl() );

            if ( is_wp_error( $user ) ) {
                foreach ($user->get_error_codes() as $errorCode) {
                    $errors[$errorCode] = 'Invalid username.';
                }

                throw new Exception();
            } else {
                // Return strict JSON success response.
                wp_send_json( array(
                    'success' => true,
                    'message' => 'Login successful.',
                    'errors'  => $errors,
                    'redirect' => htmlspecialchars($_SERVER['_wp_http_referer']),
                ) );
            }
        } catch ( Exception $e ) {
            // Return strict JSON error response.
            wp_send_json( array(
                'success' => false,
                'message' => 'Login failed.',
                'errors'  => $errors,
            ) );
        }
    }

    // Return JSON error if login data is not set or nonce is invalid.
    wp_send_json( array(
        'success' => false,
        'message' => 'Invalid request. Please try again.',
        'errors'  => [],
    ) );
}
add_action('wp_ajax_nopriv_custom_login', 'handle_custom_login');
