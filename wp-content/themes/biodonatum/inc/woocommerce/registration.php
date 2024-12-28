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
                    $response['errors'][$code] = $validation_error->get_error_message($code);
                }
                throw new Exception('Validation failed');
            }

            // Create new customer
            $new_customer = wc_create_new_customer( sanitize_email( $email ), wc_clean( $username ), $password );

            if ( is_wp_error( $new_customer ) ) {
                // Collect customer creation errors
                foreach ( $new_customer->get_error_codes() as $code ) {
                    $response['errors'][$code] = $new_customer->get_error_message($code);
                }

                throw new Exception('Customer creation failed');
            }

            // Set success message
            $response['success'] = true;
            $response['message'] = 'Your account was created successfully.';

            ob_start();
            get_template_part('components/signUpSuccess', null, ['email' => $email]);
            $response['successComponent'] = ob_get_clean();

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
                    $errors[$errorCode] = $validation_error->get_error_message($errorCode);
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
                    $errors[$errorCode] = $user->get_error_message($errorCode);
                }

                throw new Exception();
            } else {
                // Return strict JSON success response.
                wp_send_json( array(
                    'success' => true,
                    'message' => 'Login successful.',
                    'errors'  => $errors,
                    'redirect' => esc_url($_POST['_wp_http_referer']),
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

function handle_custom_lost_password() {
    if ( isset( $_POST['user_login'] ) ) {
        $nonce_value = wc_get_var( $_REQUEST['woocommerce-lost-password-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) );

        if ( ! wp_verify_nonce( $nonce_value, 'lost_password' ) ) {
            wp_send_json( [
                'success' => false,
                'errors' => [
                    'invalid_nonce' => __( 'Nonce verification failed.', 'woocommerce' ),
                ],
            ] );
        }

        $errors = [];
        $login = sanitize_user( wp_unslash( $_POST['user_login'] ) ); // WPCS: input var ok, CSRF ok.

        if ( empty( $login ) ) {
            $errors['empty_username'] = __( 'Enter a username or email address.', 'woocommerce' );
        } else {
            // Check on username first, as customers can use emails as usernames.
            $user_data = get_user_by( 'login', $login );
        }

        // If no user found, check if the login is email and lookup user based on email.
        if ( !$user_data && is_email( $login ) && apply_filters( 'woocommerce_get_username_from_email', true ) ) {
            error_log('If no user found, check if the login is email and lookup user based on email.');
            $user_data = get_user_by( 'email', $login );
        }

        if ( !$user_data ) {
            $errors['invalid_username'] = __( 'Invalid username or email.', 'woocommerce' );
        }

        if ( $user_data && is_multisite() && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
            $errors['invalid_username'] = __( 'Invalid username or email.', 'woocommerce' );
        }

        if ( $user_data ) {
            $allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

            if ( ! $allow ) {
                $errors['password_reset_not_allowed'] = __( 'Password reset is not allowed for this user.', 'woocommerce' );
            } elseif ( is_wp_error( $allow ) ) {
                $errors['password_reset_error'] = $allow->get_error_message();
            } else {
                // Get password reset key.
                $key = get_password_reset_key( $user_data );

                if ( is_wp_error( $key ) ) {
                    error_log('Get password reset key.');
                    error_log(print_r($user_data, true));

                    $errors['key_generation_failed'] = $key->get_error_message();
                } else {
                    // Send email notification.
                    WC()->mailer(); // Load email classes.
                    do_action( 'woocommerce_reset_password_notification', $user_data->user_login, $key );
                }
            }
        }

        if ( ! empty( $errors ) ) {
            wp_send_json( [
                'success' => false,
                'errors'  => $errors,
            ] );
        }

        ob_start();
        get_template_part('components/lostPasswordSuccess', null, ['email' => $user_data->user_email]);
        $successComponent = ob_get_clean();

        // Success response.
        wp_send_json( [
            'success' => true,
            'errors'  => [],
            'successComponent' => $successComponent,
        ] );
    }

    // Invalid request response.
    wp_send_json( [
        'success' => false,
        'errors'  => [
            'invalid_request' => __( 'Invalid request.', 'woocommerce' ),
        ],
    ] );
}
add_action( 'wp_ajax_nopriv_custom_lost_password', 'handle_custom_lost_password' );

/**
 * Handle reset password form.
 */
function handle_custom_reset_password() {
    $response = [
        'success' => false,
        'message' => '',
        'errors'  => []
    ];

    $nonce_value = wc_get_var($_REQUEST['woocommerce-reset-password-nonce'], wc_get_var($_REQUEST['_wpnonce'], ''));

    if (!wp_verify_nonce($nonce_value, 'reset_password')) {
        $response['errors']['invalid_nonce'] = __('Invalid nonce.', 'woocommerce');
        wp_send_json($response);
    }

    $posted_fields = ['password_1', 'password_2', 'reset_key', 'reset_login'];

    foreach ($posted_fields as $field) {
        if (!isset($_POST[$field])) {
            $response['errors']['missing_field'] = sprintf(__('The %s field is required.', 'woocommerce'), $field);
            wp_send_json($response);
        }

        $posted_fields[$field] = in_array($field, ['password_1', 'password_2'], true)
            ? $_POST[$field]
            : wp_unslash($_POST[$field]);
    }

    $user = WC_Shortcode_My_Account::check_password_reset_key($posted_fields['reset_key'], $posted_fields['reset_login']);

    if ($user instanceof WP_User) {
        if (empty($posted_fields['password_1'])) {
            $response['errors']['empty_password'] = __('Please enter your password.', 'woocommerce');
        }

        if ($posted_fields['password_1'] !== $posted_fields['password_2']) {
            $response['errors']['password_mismatch'] = __('Passwords do not match.', 'woocommerce');
        }

        $errors = new WP_Error();
        do_action('validate_password_reset', $errors, $user);

        if ($errors->get_error_codes()) {
            foreach ($errors->get_error_codes() as $code) {
                $response['errors'][$code] = $errors->get_error_message($code);
            }
        }

        if (empty($response['errors'])) {
            WC_Shortcode_My_Account::reset_password($user, $posted_fields['password_1']);
            do_action('woocommerce_customer_reset_password', $user);

            $response['success'] = true;
            $response['message'] = __('Password reset successfully.', 'woocommerce');

            ob_start();
            get_template_part('components/resetPasswordSuccess');
            $response['successComponent'] = ob_get_clean();

            wp_send_json($response);
        }
    } else {
        $response['errors']['invalid_user'] = __('Invalid reset key or login.', 'woocommerce');
    }

    $response['message'] = __('Password reset failed.', 'woocommerce');
    wp_send_json($response);
}

add_action('wp_ajax_nopriv_custom_reset_password', 'handle_custom_reset_password');
