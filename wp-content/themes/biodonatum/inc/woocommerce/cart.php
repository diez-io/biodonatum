<?php
// Prevent direct access to the file.
if (!defined('ABSPATH')) {
    exit;
}

function get_cart_count() {
    return WC()->cart->get_cart_contents_count();
}

function enqueue_cart_count_script() {
    if (is_cart() || is_checkout() || is_singular('advanced_product')) {
        wp_enqueue_style('cart-style', get_template_directory_uri() . '/css/cart.css');
        wp_enqueue_style('add-to-cart-subscription-style', get_template_directory_uri() . '/css/add_to_cart_subscription.css');

        wp_enqueue_script(
            'cart-count-update',
            get_template_directory_uri() . '/js/cart.js',
            ['jquery'],
            null,
            true
        );

        wp_localize_script('cart-count-update', 'wc_cart', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_cart_count_script');

function get_cart_count_ajax() {
    wp_send_json(['cart_count' => get_cart_count()]);
}

add_action('wp_ajax_get_cart_count', 'get_cart_count_ajax');
add_action('wp_ajax_nopriv_get_cart_count', 'get_cart_count_ajax');

function get_cart_totals_ajax() {
    wp_send_json([
        'cart_subtotal' => WC()->cart->get_cart_subtotal(),
        'cart_total' => WC()->cart->get_cart_total(),
        'cart_discount' => wc_price(-WC()->cart->get_cart_discount_total()),
    ]);
}

add_action('wp_ajax_get_cart_totals', 'get_cart_totals_ajax');
add_action('wp_ajax_nopriv_get_cart_totals', 'get_cart_totals_ajax');

function custom_ajax_update_cart_item() {
    // Get cart item key and new quantity from the request
    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

    if (!$cart_item_key || $quantity < 0) {
        wp_send_json_error(['error' => get_static_content('invalid_input')]);
    }

    // Update the cart item's quantity
    WC()->cart->set_quantity($cart_item_key, $quantity);

    // Recalculate cart totals
    WC()->cart->calculate_totals();

    // Get cart item details
    $cart = WC()->cart->get_cart();
    $item = isset($cart[$cart_item_key]) ? $cart[$cart_item_key] : null;

    if (!$item) {
        wp_send_json_error(['error' => get_static_content('cart_item_not_found')]);
    }

    wp_send_json_success([
        'new_quantity' => $quantity,
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'item_subtotal' => wc_price($item['line_subtotal']),
        'item_discount' => wc_price($item['line_total'] - $item['line_subtotal']),
        'cart_subtotal' => WC()->cart->get_cart_subtotal(),
        'cart_total' => WC()->cart->get_cart_total(),
        'cart_discount' => wc_price(-WC()->cart->get_cart_discount_total()),
    ]);
}
add_action('wp_ajax_update_cart_item', 'custom_ajax_update_cart_item');
add_action('wp_ajax_nopriv_update_cart_item', 'custom_ajax_update_cart_item');

add_action('wp_ajax_apply_custom_coupon', 'apply_custom_coupon_handler');
add_action('wp_ajax_nopriv_apply_custom_coupon', 'apply_custom_coupon_handler');

function apply_custom_coupon_handler() {
    // Check if WooCommerce is active
    if (!class_exists('WC_Cart')) {
        wp_send_json_error(['message' => get_static_content('woocommerce_not_loaded')]);
        return;
    }

    // Get and sanitize the coupon code
    $coupon_code = isset($_POST['coupon_code']) ? sanitize_text_field($_POST['coupon_code']) : '';

    // Validate the coupon code
    if (empty($coupon_code)) {
        wp_send_json_error(['message' => get_static_content('coupon_code_is_required')]);
        return;
    }

    // Apply the coupon
    $result = WC()->cart->apply_coupon($coupon_code);

    // Check if the coupon was successfully applied
    if (!$result) {
        wp_send_json_error(['message' => get_static_content('invalid_coupon')]);
        return;
    }

    // Recalculate cart totals
    WC()->cart->calculate_totals();

    // Prepare cart items with discounts
    $cart_items = [];
    foreach (WC()->cart->get_cart() as $cart_item_key => $item) {
        $cart_items[] = [
            'cart_item_key' => $cart_item_key,
            'discount' => wc_price($item['line_total'] - $item['line_subtotal']),
        ];
    }

    // Send updated cart details
    wp_send_json_success([
        'cart_subtotal' => WC()->cart->get_cart_subtotal(),
        'cart_discount' => wc_price(-WC()->cart->get_cart_discount_total()),
        'cart_total' => WC()->cart->get_cart_total(),
        'coupon_applied' => WC()->cart->get_applied_coupons(),
        'cart_items' => $cart_items,
    ]);
}

function remove_custom_coupon_handler() {
    // Check if WooCommerce is active
    if (!class_exists('WC_Cart')) {
        wp_send_json_error(['message' => get_static_content('woocommerce_not_loaded')]);
        return;
    }

    // Get and sanitize the coupon code
    $coupon_code = isset($_POST['coupon_code']) ? sanitize_text_field($_POST['coupon_code']) : '';

    // Validate the coupon code
    if (empty($coupon_code)) {
        wp_send_json_error(['message' => get_static_content('coupon_code_is_required')]);
        return;
    }

    // Remove the coupon
    $result = WC()->cart->remove_coupon($coupon_code);

    // Check if the coupon was successfully removed
    if (!$result) {
        wp_send_json_error(['message' => get_static_content('failed_to_remove_coupon')]);
        return;
    }

    // Recalculate cart totals
    WC()->cart->calculate_totals();

    $cart_items = [];
    foreach (WC()->cart->get_cart() as $cart_item_key => $item) {
        $cart_items[] = [
            'cart_item_key' => $cart_item_key,
            'discount' => wc_price($item['line_total'] - $item['line_subtotal']),
        ];
    }

    // Send updated cart details
    wp_send_json_success([
        'cart_subtotal' => WC()->cart->get_cart_subtotal(),
        'cart_discount' => wc_price(-WC()->cart->get_cart_discount_total()),
        'cart_total' => WC()->cart->get_cart_total(),
        'coupon_applied' => WC()->cart->get_applied_coupons(),
        'cart_items' => $cart_items,
    ]);
}
add_action('wp_ajax_remove_custom_coupon', 'remove_custom_coupon_handler');
add_action('wp_ajax_nopriv_remove_custom_coupon', 'remove_custom_coupon_handler');
