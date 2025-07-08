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
        if (class_exists('Biodonatum_Currency_Switcher')) {
            $plugin = new Biodonatum_Currency_Switcher();
            return $plugin->get_user_currency();
        }
        return 'EUR';
    }
}

// Helper for theme: get all currencies
if (!function_exists('get_biodonatum_currencies')) {
    function get_biodonatum_currencies() {
        if (class_exists('Biodonatum_Currency_Switcher')) {
            $plugin = new Biodonatum_Currency_Switcher();
            $settings = $plugin->get_settings();
            return $settings['currencies'] ?? ['EUR'];
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
    update_option($option_name, $settings);
});
class Biodonatum_Currency_Switcher {
    private $option_name = 'biodonatum_currency_settings';
    private $rates_file;

    public function __construct() {
        $this->rates_file = plugin_dir_path(__FILE__) . 'exchange_rates.json';
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_ajax_biodonatum_update_rates', [$this, 'ajax_update_rates']);
        add_action('init', [$this, 'handle_currency_switch']);
        add_filter('woocommerce_get_price_html', [$this, 'convert_price_html'], 99, 2);
        add_filter('woocommerce_cart_item_price', [$this, 'convert_price_html'], 99, 3);
        add_filter('woocommerce_cart_item_subtotal', [$this, 'convert_price_html'], 99, 3);
        add_filter('woocommerce_cart_subtotal', [$this, 'convert_cart_totals'], 99, 3);
        add_filter('woocommerce_cart_total', [$this, 'convert_cart_totals'], 99, 1);
        // add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
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
            if (preg_match_all("/<option value='(.*?)' title='(.*?)'>.*?<\/option>/", $html, $matches, PREG_SET_ORDER)) {
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
            <form method="post">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">API Access Key</th>
                        <td><input type="text" name="<?= $this->option_name ?>[access_key]" value="<?= $access_key ?>" size="40"></td>
                    </tr>
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
        $settings = $this->get_settings();
        $source = get_option('woocommerce_currency', 'USD');
        $rates = $this->get_rates();
        if (isset($rates['quotes'][ $source . $to ])) {
            return $rates['quotes'][ $source . $to ];
        }
        return 1;
    }

    public function convert_price_html($price, $product) {
        if (is_admin()) return $price;
        $user_currency = $this->get_user_currency();
        $woocommerce_currency = get_option('woocommerce_currency', 'USD');
        if ($user_currency === $woocommerce_currency) return $price;
        $rate = $this->get_rate($user_currency);
        $price_num = floatval(strip_tags($price));
        if ($rate && $price_num) {
            $converted = $price_num * $rate;
            return wc_price($converted, ['currency' => $user_currency]);
        }
        return $price;
    }

    public function convert_cart_totals($value) {
        $user_currency = $this->get_user_currency();
        $woocommerce_currency = get_option('woocommerce_currency', 'USD');
        if ($user_currency === $woocommerce_currency) return $value;
        $rate = $this->get_rate($user_currency);
        $value_num = floatval(strip_tags($value));
        if ($rate && $value_num) {
            $converted = $value_num * $rate;
            return wc_price($converted, ['currency' => $user_currency]);
        }
        return $value;
    }
} // end class
} // end if class_exists

new Biodonatum_Currency_Switcher();
