<?php
/*
Plugin Name: Biodonatum Currency Switcher
Description: Custom WooCommerce currency switcher with exchange rates from api.exchangerate.host
Version: 1.0
Author: Your Name
*/

// Helper for theme: get current currency
if (!function_exists('get_biodonatum_current_currency')) {
    function get_biodonatum_current_currency() {
        global $biodonatum_currency_switcher;
        if ($biodonatum_currency_switcher instanceof Biodonatum_Currency_Switcher) {
            return $biodonatum_currency_switcher->get_user_currency();
        }
        return 'EUR';
    }
}

// Helper for theme: get all currencies
if (!function_exists('get_biodonatum_currencies')) {
    function get_biodonatum_currencies($with_names = false) {
        global $biodonatum_currency_switcher;
        if ($biodonatum_currency_switcher instanceof Biodonatum_Currency_Switcher) {
            $settings = $biodonatum_currency_switcher->get_settings();
            $currencies = $settings['currencies'] ?? ['EUR'];
            if ($with_names) {
                // Try to get names from plugin file
                $currency_names = [];
                $currencies_file = plugin_dir_path(__FILE__) . 'cl-currencies-select-option.html';
                if (file_exists($currencies_file)) {
                    $html = file_get_contents($currencies_file);
                    if (preg_match_all("/<option value='(.*?)' title='(.*?)'>.*?<\\/option>/", $html, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $m) {
                            $currency_names[$m[1]] = $m[2];
                        }
                    }
                }
                $result = [];
                foreach ($currencies as $code) {
                    $result[] = [
                        'code' => $code,
                        'name' => $currency_names[$code] ?? $code
                    ];
                }
                return $result;
            }
            return $currencies;
        }
        if ($with_names) {
            return [['code' => 'EUR', 'name' => 'Euro']];
        }
        return ['EUR'];
    }
}

if (!defined('ABSPATH')) exit;

if (!class_exists('Biodonatum_Currency_Switcher')) {
register_activation_hook(__FILE__, function() {
    $option_name = 'biodonatum_currency_settings';
    $settings = get_option($option_name, []);
    $currencies_file = plugin_dir_path(__FILE__) . 'cl-currencies-select-option.html';
    $all_currencies = [];
    if (file_exists($currencies_file)) {
        $html = file_get_contents($currencies_file);
        if (preg_match_all("/<option value='(.*?)' title='(.*?)'>.*?<\\/option>/", $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $all_currencies[$m[1]] = $m[2];
            }
        }
    }
    $wc_currency = get_option('woocommerce_currency', 'USD');
    if (!isset($all_currencies[$wc_currency])) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('Biodonatum Currency Switcher: The current WooCommerce currency (' . esc_html($wc_currency) . ') is not supported by exchangerate.host. Please choose a supported currency before activating this plugin.');
    }
    $settings['currencies'] = [$wc_currency];
    $settings['default_currency'] = $wc_currency;
    $settings['rate_offset'] = 0;
    update_option($option_name, $settings);

    if (!wp_next_scheduled('biodonatum_daily_rate_update')) {
        wp_schedule_event(strtotime('00:00 GMT'), 'daily', 'biodonatum_daily_rate_update');
    }
});

register_deactivation_hook(__FILE__, function() {
    wp_clear_scheduled_hook('biodonatum_daily_rate_update');
});

class Biodonatum_Currency_Switcher {
    private $option_name = 'biodonatum_currency_settings';
    private $rates_file;
    private $rate_offset;

    // --- Biodonatum: Add current currency code to all WooCommerce currency symbols globally ---
    private $biodonatum_currency_symbols = [
        'AED' => 'د.إ', 'AFN' => '؋', 'ALL' => 'L', 'AMD' => '֏', 'ANG' => 'ƒ', 'AOA' => 'Kz',
        'ARS' => '$', 'AUD' => 'A$', 'AWG' => 'ƒ', 'AZN' => '₼', 'BAM' => 'KM', 'BBD' => 'Bds$',
        'BDT' => '৳', 'BGN' => 'лв', 'BHD' => '.د.ب', 'BIF' => 'FBu', 'BMD' => 'BD$', 'BND' => 'B$',
        'BOB' => 'Bs.', 'BRL' => 'R$', 'BSD' => 'B$', 'BTC' => '₿', 'BTN' => 'Nu.', 'BWP' => 'P',
        'BYN' => 'Br', 'BYR' => 'Br', 'BZD' => 'BZ$', 'CAD' => 'C$', 'CDF' => 'FC', 'CHF' => 'Fr.',
        'CLF' => 'UF', 'CLP' => '$', 'CNY' => '¥', 'COP' => '$', 'CRC' => '₡', 'CUC' => '$',
        'CUP' => '$', 'CVE' => '$', 'CZK' => 'Kč', 'DJF' => 'Fdj', 'DKK' => 'kr', 'DOP' => 'RD$',
        'DZD' => 'دج', 'EEK' => 'kr', 'EGP' => '£', 'ERN' => 'Nfk', 'ETB' => 'Br', 'EUR' => '€',
        'FJD' => 'FJ$', 'FKP' => '£', 'GBP' => '£', 'GEL' => '₾', 'GGP' => '£', 'GHS' => '₵',
        'GIP' => '£', 'GMD' => 'D', 'GNF' => 'FG', 'GTQ' => 'Q', 'GYD' => 'G$', 'HKD' => 'HK$',
        'HNL' => 'L', 'HRK' => 'kn', 'HTG' => 'G', 'HUF' => 'Ft', 'IDR' => 'Rp', 'ILS' => '₪',
        'IMP' => '£', 'INR' => '₹', 'IQD' => 'ع.د', 'IRR' => '﷼', 'ISK' => 'kr', 'JEP' => '£',
        'JMD' => 'J$', 'JOD' => 'د.ا', 'JPY' => '¥', 'KES' => 'KSh', 'KGS' => 'лв', 'KHR' => '៛',
        'KMF' => 'CF', 'KPW' => '₩', 'KRW' => '₩', 'KWD' => 'د.ك', 'KYD' => 'CI$', 'KZT' => '₸',
        'LAK' => '₭', 'LBP' => 'ل.ل', 'LKR' => '₨', 'LRD' => 'L$', 'LSL' => 'L', 'LTL' => 'Lt',
        'LVL' => 'Ls', 'LYD' => 'ل.د', 'MAD' => 'د.م.', 'MDL' => 'L', 'MGA' => 'Ar', 'MKD' => 'ден',
        'MMK' => 'K', 'MNT' => '₮', 'MOP' => 'P', 'MRO' => 'UM', 'MUR' => '₨', 'MVR' => 'Rf',
        'MWK' => 'MK', 'MXN' => '$', 'MYR' => 'RM', 'MZN' => 'MT', 'NAD' => 'N$', 'NGN' => '₦',
        'NIO' => 'C$', 'NOK' => 'kr', 'NPR' => '₨', 'NZD' => 'NZ$', 'OMR' => 'ر.ع.', 'PAB' => 'B/.',
        'PEN' => 'S/', 'PGK' => 'K', 'PHP' => '₱', 'PKR' => '₨', 'PLN' => 'zł', 'PYG' => '₲',
        'QAR' => 'ر.ق', 'RON' => 'lei', 'RSD' => 'дин.', 'RUB' => '₽', 'RWF' => 'FRw', 'SAR' => 'ر.س',
        'SBD' => 'SI$', 'SCR' => '₨', 'SDG' => '£', 'SEK' => 'kr', 'SGD' => 'S$', 'SHP' => '£',
        'SLL' => 'Le', 'SOS' => 'S', 'SRD' => '$', 'STD' => 'Db', 'SVC' => '$', 'SYP' => '£',
        'SZL' => 'E', 'THB' => '฿', 'TJS' => 'ЅM', 'TMT' => 'T', 'TND' => 'د.ت', 'TOP' => 'T$',
        'TRY' => '₺', 'TTD' => 'TT$', 'TWD' => 'NT$', 'TZS' => 'TSh', 'UAH' => '₴', 'UGX' => 'USh',
        'USD' => '$', 'UYU' => '$U', 'UZS' => 'лв', 'VEF' => 'Bs', 'VND' => '₫', 'VUV' => 'VT',
        'WST' => 'WS$', 'XAF' => 'FCFA', 'XAG' => 'XAG', 'XAU' => 'XAU', 'XCD' => 'EC$', 'XDR' => 'SDR',
        'XOF' => 'CFA', 'XPF' => '₣', 'YER' => '﷼', 'ZAR' => 'R', 'ZMK' => 'ZK', 'ZMW' => 'ZK', 'ZWL' => 'Z$'
    ];

    public function __construct() {
        $this->rates_file = plugin_dir_path(__FILE__) . 'exchange_rates.json';

        $settings = get_option($this->option_name, []);
        $this->rate_offset = intval(isset($settings['rate_offset']) ? esc_attr($settings['rate_offset']) : 0);

        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_ajax_biodonatum_update_rates', [$this, 'ajax_update_rates']);
        add_action('init', [$this, 'handle_currency_switch']);
        add_action('biodonatum_daily_rate_update', [$this, 'update_rates']);

        add_filter('woocommerce_get_price_html', [$this, 'convert_price_html'], 99, 2);
        add_filter('woocommerce_cart_product_price', [$this, 'get_cart_product_price'], 99, 2);
        add_filter('woocommerce_cart_product_subtotal', [$this, 'get_product_subtotal'], 99, 4);
        add_filter('woocommerce_cart_subtotal', [$this, 'convert_cart_totals'], 99, 3);
        add_filter('woocommerce_cart_contents_total', [$this, 'get_cart_total'], 99, 1);
        add_filter('woocommerce_cart_shipping_method_full_label', [$this, 'wc_cart_totals_shipping_method_label'], 99, 2);
        add_filter('woocommerce_cart_total', [$this, 'get_order_total'], 99, 1);

        add_filter('woocommerce_get_formatted_order_total', [$this, 'get_formatted_order_total'], 99, 4);
        add_filter('woocommerce_order_formatted_line_subtotal', [$this, 'get_formatted_line_subtotal'], 99, 3);
        add_filter('woocommerce_order_subtotal_to_display', [$this, 'get_subtotal_to_display'], 99, 3);
        add_filter('woocommerce_order_shipping_to_display', [$this, 'get_subtotal_to_display'], 99, 3);

        add_filter('woocommerce_currency_symbol', [$this, 'change_currency_symbol'], 99, 2);

        add_filter('woocommerce_currency', [$this, 'biodonatum_force_aed_currency_for_telr'], 99, 1);
        add_filter('woocommerce_order_get_total', [$this, 'biodonatum_convert_eur_to_aed_for_telr'], 99, 2);

        // Set a session flag before Telr payment request
        add_action('woocommerce_before_checkout_process', function() {
            if (isset($_POST['payment_method']) && $_POST['payment_method'] === 'wctelr') {
                if (WC()->session) {
                    WC()->session->set('biodonatum_telr_payment', true);
                }
            }
        });
        // Clear the flag after payment is processed
        add_action('woocommerce_thankyou', function($order_id) {
            if (WC()->session) {
                WC()->session->__unset('biodonatum_telr_payment');
            }
        });
    }

    public function biodonatum_convert_eur_to_aed_for_telr($total, $order) {
        if (WC()->session && WC()->session->get('biodonatum_telr_payment')) {
            $rate = $this->get_rate('AED');

            if (is_array($rate) && isset($rate['rate'])) {
                $rate = floatval($rate['rate']);
            } elseif (is_numeric($rate)) {
                $rate = floatval($rate);
            } else {
                $rate = 1;
            }

            if ($rate !== 1) {
                $rate *= 1 + $this->rate_offset / 100;
            }

            return round($total * $rate, 2);
        }

        return $total;
    }

    public function biodonatum_force_aed_currency_for_telr($currency) {
		if ( WC()->session ) {
            $chosen_gateway = WC()->session->get('chosen_payment_method');

            if ($chosen_gateway === 'wctelr') {
                return 'AED';
            }
        }

        return $currency;
    }

	public function get_subtotal_to_display( $price, $compound, $order ) {
        if (!$this->biodonatum_should_convert_prices()) {
            return $price;
        }

        $rate = $this->get_current_rate();

        if (preg_match('/[\d,.]+/', $price, $matches)) {
            // Replace comma with dot for float conversion
            $normalized = str_replace(',', '.', $matches[0]);
            $price = (float) $normalized;
            $price = wc_price($price * $rate);
        }

		return $price;
	}

	public function get_formatted_line_subtotal( $subtotal, $item, $order ) {
        if (!$this->biodonatum_should_convert_prices()) {
            return $subtotal;
        }

        $rate = $this->get_current_rate();

		$subtotal = wc_price( $order->get_line_subtotal( $item, true ) * $rate, array( 'currency' => $order->get_currency() ) );

		return $subtotal;
	}

	public function get_formatted_order_total( $price, $order, $tax_display, $display_refunded ) {
        if (!$this->biodonatum_should_convert_prices()) {
            return $price;
        }

        $rate = $this->get_current_rate();

        if (preg_match('/[\d,.]+/', $price, $matches)) {
            // Replace comma with dot for float conversion
            $normalized = str_replace(',', '.', $matches[0]);
            $price = (float) $normalized;
            $price = wc_price($price * $rate);
        }

		return $price;
	}

	public function get_order_total( $price ) {
        if (!$this->biodonatum_should_convert_prices()) {
            return $price;
        }

        $rate = $this->get_current_rate();

        $price = (wc_prices_include_tax() ? WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax() : WC()->cart->get_cart_contents_total() );
        $price += WC()->cart->get_shipping_total();

        return wc_price($price * $rate);
	}

    public function wc_cart_totals_shipping_method_label( $label, $method ) {
        if (!$this->biodonatum_should_convert_prices()) {
            return $label;
        }

        $rate = $this->get_current_rate();

        $label     = $method->get_label();
        $has_cost  = 0 < $method->cost;
        $hide_cost = ! $has_cost && in_array( $method->get_method_id(), array( 'free_shipping', 'local_pickup' ), true );

        if ( $has_cost && ! $hide_cost ) {
            if ( WC()->cart->display_prices_including_tax() ) {
                $label .= ': ' . wc_price( ($method->cost + $method->get_shipping_tax()) * $rate );
                if ( $method->get_shipping_tax() > 0 && ! wc_prices_include_tax() ) {
                    $label .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                }
            } else {
                $label .= ': ' . wc_price( $method->cost * $rate );
                if ( $method->get_shipping_tax() > 0 && wc_prices_include_tax() ) {
                    $label .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                }
            }
        }

        return $label;
    }

	public function get_cart_total($price) {
        if (!$this->biodonatum_should_convert_prices()) {
            return $price;
        }

		return wc_price( (wc_prices_include_tax() ? WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax() : WC()->cart->get_cart_contents_total()) * $this->get_current_rate() );
	}

	public function get_product_subtotal( $price, $product, $quantity, $cart ) {
        if (!$this->biodonatum_should_convert_prices()) {
            return $price;
        }

        $rate = $this->get_current_rate();

        if (preg_match('/[\d,.]+/', $price, $matches)) {
            // Replace comma with dot for float conversion
            $normalized = str_replace(',', '.', $matches[0]);
            $price = (float) $normalized;
            $price = wc_price($price * $rate);
        }

		return $price;
	}

	public function get_cart_product_price( $price, $product ) {
        if (!$this->biodonatum_should_convert_prices()) {
            return $price;
        }

		if ( WC()->cart->display_prices_including_tax() ) {
			$product_price = wc_get_price_including_tax( $product );
		} else {
			$product_price = wc_get_price_excluding_tax( $product );
		}

		return wc_price($product_price * $this->get_current_rate());
	}

    // Only convert prices on the frontend, not in admin, REST, AJAX, or cron (so gateways get original values)
    private function biodonatum_should_convert_prices() {
        $result = false;

        // Never convert in REST or cron
        if (defined('DOING_CRON') && DOING_CRON) {
            $result = false;
        }
        elseif (defined('REST_REQUEST') && REST_REQUEST) {
            $result = false;
        }
        elseif (!is_admin()) {
            $result = true;
        }
        elseif (defined('DOING_AJAX') && DOING_AJAX) {
            // If user is not logged in or is not an admin, treat as frontend AJAX
            if (!current_user_can('manage_options')) {
                $result = true;
            }
            else {
                // Or, check for known frontend AJAX actions
                $frontend_ajax_actions = [
                    'get_cart_totals', 'update_cart_item', 'apply_custom_coupon', 'remove_custom_coupon', 'get_cart_count'
                ];

                if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], $frontend_ajax_actions, true)) {
                    $result = true;
                }
            }
        }

        if ($result) {
            $user_currency = $this->get_user_currency();
            $woocommerce_currency = get_option('woocommerce_currency', 'USD');

            $result = $user_currency !== $woocommerce_currency;
        }

        return $result;
    }

    public function change_currency_symbol($currency_symbol, $currency) {
        // Try to get the currently chosen currency (user's selection or default)
        if (function_exists('get_biodonatum_current_currency') && $this->biodonatum_should_convert_prices()) {
            $current = $this->get_user_currency();
            $currency_symbol = isset($this->biodonatum_currency_symbols[$current]) ? $this->biodonatum_currency_symbols[$current] : $currency_symbol;
        }

        return $currency_symbol;
    }

    public function add_admin_menu() {
        add_menu_page(
            'Biodonatum Currency',
            'Currency Switcher',
            'manage_options',
            'biodonatum-currency',
            [$this, 'settings_page'],
            'dashicons-money-alt'
        );
    }

    public function register_settings() {
        register_setting('biodonatum_currency_group', $this->option_name);
    }

    public function settings_page() {
        $settings = get_option($this->option_name, []);
        $access_key = isset($settings['access_key']) ? esc_attr($settings['access_key']) : '';
        $currencies = isset($settings['currencies']) ? (array)$settings['currencies'] : ['USD'];
        $default_currency = isset($settings['default_currency']) ? esc_attr($settings['default_currency']) : 'USD';
        $woocommerce_currency = get_option('woocommerce_currency', 'USD');
        // Handle WooCommerce currency change
        if (isset($_POST['set_wc_currency']) && !empty($_POST['wc_currency'])) {
            $new_wc = sanitize_text_field($_POST['wc_currency']);
            update_option('woocommerce_currency', $new_wc);
            $woocommerce_currency = $new_wc;
            echo '<div class="updated"><p>WooCommerce base currency updated to ' . esc_html($new_wc) . '.</p></div>';
        }

        // Get WooCommerce supported currencies
        if (function_exists('get_woocommerce_currencies')) {
            $wc_currencies = get_woocommerce_currencies();
        } else {
            $wc_currencies = [
                'USD' => 'United States Dollar',
                'EUR' => 'Euro',
                'GBP' => 'Pound Sterling',
            ]; // fallback
        }
        $rates = $this->get_rates();

        // Load all available currencies from the HTML file
        $currencies_file = plugin_dir_path(__FILE__) . 'cl-currencies-select-option.html';
        $all_currencies = [];
        if (file_exists($currencies_file)) {
            $html = file_get_contents($currencies_file);
            if (preg_match_all("/<option value='(.*?)' title='(.*?)'>.*?<\\/option>/", $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $m) {
                    $all_currencies[$m[1]] = $m[2];
                }
            }
        }

        $rates = $this->get_rates();
        $source = $woocommerce_currency;

        // Handle add/remove/default actions and rate update logic
        $need_update = false;
        if (isset($_POST['add_currency']) && !empty($_POST['new_currency'])) {
            $new = sanitize_text_field($_POST['new_currency']);
            if (!in_array($new, $currencies)) {
                $currencies[] = $new;
                // Check if rate is missing or outdated
                $pair = $source . $new;
                $pair_data = $rates['quotes'][$pair] ?? null;
                if (!$pair_data || (time() - ($pair_data['timestamp'] ?? 0)) > 86400) {
                    $need_update = true;
                }
            }
        }
        if (isset($_POST['remove_currency'])) {
            $remove = sanitize_text_field($_POST['remove_currency']);
            $currencies = array_values(array_diff($currencies, [$remove]));
            if ($default_currency === $remove && count($currencies)) {
                $default_currency = $currencies[0];
            }
        }
        if (isset($_POST['set_default'])) {
            $set = sanitize_text_field($_POST['set_default']);
            if (in_array($set, $currencies)) {
                $default_currency = $set;
            }
        }
        if (isset($_POST['set_api_access_key'])) {
            $posted = $_POST[$this->option_name];
            $key = sanitize_text_field($posted['access_key'] ?? '');

            $settings = get_option($this->option_name, []);
            $settings['access_key'] = $key;
            $access_key = $key;
            update_option($this->option_name, $settings);

            echo '<div class="updated"><p>API Access Key updated.</p></div>';
        }
        if (isset($_POST['set_exchange_rates_offset'])) {
            $posted = $_POST[$this->option_name];
            $offset = intval(sanitize_text_field($posted['rate_offset'] ?? 0));

            $settings = get_option($this->option_name, []);
            $settings['rate_offset'] = $offset;
            $this->rate_offset = $offset;
            update_option($this->option_name, $settings);

            echo '<div class="updated"><p>Exchange rates offset updated.</p></div>';
        }
        // If WooCommerce currency changed, check for outdated pairs
        if (isset($_POST['set_wc_currency']) && !empty($_POST['wc_currency'])) {
            $new_wc = sanitize_text_field($_POST['wc_currency']);
            update_option('woocommerce_currency', $new_wc);
            $woocommerce_currency = $new_wc;
            $source = $woocommerce_currency;
            foreach ($currencies as $cur) {
                $pair = $source . $cur;
                $pair_data = $rates['quotes'][$pair] ?? null;
                if (!$pair_data || (time() - ($pair_data['timestamp'] ?? 0)) > 86400) {
                    $need_update = true;
                    break;
                }
            }
            echo '<div class="updated"><p>WooCommerce base currency updated to ' . esc_html($new_wc) . '.</p></div>';
        }
        // Save settings if any action
        if (isset($_POST['add_currency']) || isset($_POST['remove_currency']) || isset($_POST['set_default']) || isset($_POST['set_wc_currency'])) {
            $settings['currencies'] = $currencies;
            $settings['default_currency'] = $default_currency;
            update_option($this->option_name, $settings);
        }
        // Update rates if needed
        if ($need_update) {
            $this->update_rates($currencies, $source);
            $rates = $this->get_rates();
            echo '<div class="updated"><p>Exchange rates updated.</p></div>';
        }

        ?>
        <div class="wrap">
            <h1>Biodonatum Currency Switcher</h1>
            <form method="post" style="margin-bottom: 20px;">
                <h2>API Access Key</h2>
                <input type="text" name="<?= esc_attr($this->option_name) ?>[access_key]" value="<?= esc_attr($access_key) ?>" size="40">
                <button name="set_api_access_key" value="1" class="button">Change</button>
            </form>
            <form method="post">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">WooCommerce Base Currency</th>
                        <td>
                            <form method="post" style="display:inline;">
                                <select name="wc_currency">
                                    <?php foreach ($wc_currencies as $code => $name): ?>
                                        <option value="<?= esc_attr($code) ?>" <?= $woocommerce_currency === $code ? 'selected' : '' ?>><?= esc_html($code . ' - ' . $name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button name="set_wc_currency" value="1" class="button">Change</button>
                            </form>
                        </td>
                    </tr>
                </table>
                <h2>Selected Currencies</h2>
                <table class="widefat" style="max-width:800px;">
                    <thead><tr><th>Code</th><th>Name</th><th>Default</th><th>Current Rate</th><th>Last Updated</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($currencies as $cur):
                        $pair = $source . $cur;
                        $pair_data = $rates['quotes'][$pair] ?? ['rate' => '', 'timestamp' => ''];
                        ?>
                        <tr>
                            <td><?= esc_html($cur) ?></td>
                            <td><?= esc_html($all_currencies[$cur] ?? $cur) ?></td>
                            <td><?= $cur === $default_currency ? '<strong>Default</strong>' : '' ?></td>
                            <td><?= $pair_data['rate'] !== '' ? esc_html($pair_data['rate']) : '<em>n/a</em>' ?></td>
                            <td>
                                <?= $pair_data['timestamp'] ? date('d.m.Y H:i', $pair_data['timestamp']) : '<em>n/a</em>' ?>
                            </td>
                            <td>
                                <?php if ($cur !== $default_currency): ?>
                                    <button name="set_default" value="<?= esc_attr($cur) ?>" class="button">Set Default</button>
                                <?php endif; ?>
                                <?php if (count($currencies) > 1): ?>
                                    <button name="remove_currency" value="<?= esc_attr($cur) ?>" class="button">Remove</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <h3>Add Currency</h3>
                <select name="new_currency">
                    <?php foreach ($all_currencies as $code => $name): if (in_array($code, $currencies)) continue; ?>
                        <option value="<?= esc_attr($code) ?>"><?= esc_html($code . ' - ' . $name) ?></option>
                    <?php endforeach; ?>
                </select>
                <button name="add_currency" value="1" class="button">+</button>
            </form>
            <form method="post" style="margin-bottom: 20px;">
                <h2>Exchange rates offset (%)</h2>
                <input type="number" name="<?= esc_attr($this->option_name) ?>[rate_offset]" value="<?= esc_attr($this->rate_offset) ?>" size="40">
                <button name="set_exchange_rates_offset" value="1" class="button">Change</button>
            </form>
            <form method="post" style="margin-top:20px;">
                <input type="hidden" name="biodonatum_update_rates" value="1">
                <input type="submit" class="button" value="Update Exchange Rates">
            </form>
        </div>
        <?php
        // Handle update rates button
        if (isset($_POST['biodonatum_update_rates'])) {
            $this->update_rates();
            echo '<div class="updated"><p>Exchange rates updated.</p></div>';
        }
    }

    public function get_settings() {
        $settings = get_option($this->option_name, []);
        if (isset($settings['currencies']) && is_string($settings['currencies'])) {
            $settings['currencies'] = array_map('trim', explode(',', $settings['currencies']));
        } elseif (!isset($settings['currencies']) || empty($settings['currencies'])) {
            $settings['currencies'] = ['USD'];
        }
        return $settings;
    }

    public function get_rates() {
        if (file_exists($this->rates_file)) {
            $json = file_get_contents($this->rates_file);
            $data = json_decode($json, true);
            if (is_array($data)) return $data;
        }
        return ['quotes' => []];
    }

    public function save_rates($rates) {
        file_put_contents($this->rates_file, json_encode($rates));
    }

    // Update rates for all pairs in $currencies (from $source)
    public function update_rates($currencies = null, $source = null) {
        $settings = $this->get_settings();
        $access_key = $settings['access_key'] ?? '';
        $currencies = $currencies ?: ($settings['currencies'] ?? ['USD']);
        $source = $source ?: get_option('woocommerce_currency', 'USD');
        $url = "https://api.exchangerate.host/live?access_key={$access_key}&source={$source}&currencies=" . implode(',', $currencies) . "&format=1";
        $response = wp_remote_get($url);
        if (is_wp_error($response)) return false;
        $body = wp_remote_retrieve_body($response);
        $json = json_decode($body, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($json['quotes'])) {
            $rates = $this->get_rates();
            $now = time();
            foreach ($json['quotes'] as $pair => $rate) {
                $rates['quotes'][$pair] = [
                    'rate' => $rate,
                    'timestamp' => $now
                ];
            }
            $this->save_rates($rates);
            return true;
        }
        return false;
    }

    public function ajax_update_rates() {
        $this->update_rates();
        wp_send_json_success(['message' => 'Rates updated']);
    }

    public function handle_currency_switch() {
        if (isset($_POST['currency'])) {
            $currency = sanitize_text_field($_POST['currency']);
            if (!headers_sent()) {
                setcookie('biodonatum_currency', $currency, time() + 3600 * 24 * 30, COOKIEPATH, COOKIE_DOMAIN);
            }
            $_COOKIE['biodonatum_currency'] = $currency;
            if (!headers_sent()) {
                wp_redirect($_SERVER['REQUEST_URI']);
                exit;
            }
        }
    }

    public function get_user_currency() {
        $settings = $this->get_settings();
        $default = $settings['default_currency'] ?? 'USD';
        if (isset($_COOKIE['biodonatum_currency']) && in_array($_COOKIE['biodonatum_currency'], $settings['currencies'])) {
            return $_COOKIE['biodonatum_currency'];
        }
        return $default;
    }

    public function get_rate($to) {
        $source = get_option('woocommerce_currency', 'USD');
        $rates = $this->get_rates();
        if (isset($rates['quotes'][ $source . $to ])) {
            return $rates['quotes'][ $source . $to ];
        }
        return 1;
    }

    public function get_current_rate() {
        $user_currency = $this->get_user_currency();
        $rate = $this->get_rate($user_currency);

        // Defensive: if $rate is array, get numeric value
        if (is_array($rate) && isset($rate['rate'])) {
            $rate = floatval($rate['rate']);
        } elseif (is_numeric($rate)) {
            $rate = floatval($rate);
        } else {
            $rate = 1;
        }

        if ($rate !== 1) {
            $rate *= 1 + $this->rate_offset / 100;
        }

        return $rate;
    }

    public function convert_price_html($price, $product) {
        if (!$this->biodonatum_should_convert_prices()) {
            return $price;
        }

        $rate = $this->get_current_rate();
        $isVariable = $product->get_type() === 'variable';

        if ($rate) {
            if ($isVariable) {
                $prices = $product->get_variation_prices( true );

                if ( empty( $prices['price'] ) ) {
                    $price = apply_filters( 'woocommerce_variable_empty_price_html', '', $this );
                } else {
                    $min_price     = current( $prices['price'] ) * $rate;
                    $max_price     = end( $prices['price'] ) * $rate;
                    $min_reg_price = current( $prices['regular_price'] ) * $rate;
                    $max_reg_price = end( $prices['regular_price'] ) * $rate;

                    if ( $min_price !== $max_price && !is_shop() ) {
                        $price = wc_format_price_range( $min_price, $max_price );
                    } elseif ( $product->is_on_sale() && $min_reg_price === $max_reg_price ) {
                        $price = wc_format_sale_price( wc_price( $max_reg_price ), wc_price( $min_price ) );
                    } else {
                        $price = wc_price( $min_price );
                    }
                }
            }
            else {
                if ( '' === $product->get_price() ) {
                    $price = apply_filters( 'woocommerce_empty_price_html', '', $product );
                } elseif ( $product->is_on_sale() ) {
                    $price = wc_format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ) * $rate, wc_get_price_to_display( $product ) * $rate ) . $product->get_price_suffix();
                } else {
                    $price = wc_price( wc_get_price_to_display( $product ) * $rate ) . $product->get_price_suffix();
                }
            }
        }

        return $price;
    }

    public function convert_cart_totals($cart_subtotal, $compound, $cart) {
        if (!$this->biodonatum_should_convert_prices()) {
            return $cart_subtotal;
        }

        $rate = $this->get_current_rate();

		if ( $compound ) {
			$cart_subtotal = wc_price( ($cart->get_cart_contents_total() + $cart->get_shipping_total() + $cart->get_taxes_total( false, false )) * $rate );

		} elseif ( $cart->display_prices_including_tax() ) {
			$cart_subtotal = wc_price( ($cart->get_subtotal() + $cart->get_subtotal_tax()) * $rate );

			if ( $cart->get_subtotal_tax() > 0 && ! wc_prices_include_tax() ) {
				$cart_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
			}
		} else {
			$cart_subtotal = wc_price( $cart->get_subtotal() * $rate );

			if ( $cart->get_subtotal_tax() > 0 && wc_prices_include_tax() ) {
				$cart_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
			}
		}

		return $cart_subtotal;
    }
} // end class
} // end if class_exists

// Instantiate the plugin only once and reuse the instance globally
global $biodonatum_currency_switcher;
if (!isset($biodonatum_currency_switcher) || !$biodonatum_currency_switcher instanceof Biodonatum_Currency_Switcher) {
    $biodonatum_currency_switcher = new Biodonatum_Currency_Switcher();
}
