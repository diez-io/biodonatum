<?php
/**
 * Checkout shipping information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-shipping-fields containers_line">
	<?php if ( true === WC()->cart->needs_shipping_address() ) : ?>

		<h3 id="ship-to-different-address" class="line">
			<label class="woocommerce-form__label woocommerce-form__label-for-checkbox">
				<input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" <?php checked( apply_filters( 'woocommerce_ship_to_different_address_checked', 'shipping' === get_option( 'woocommerce_ship_to_destination' ) ? 1 : 0 ), 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" />
				<div class="svg_container">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect width="20" height="20" rx="5" fill="#27AAE2"/>
						<path d="M13.8574 7.82946L13.1525 7.13953C13.0574 7.04651 12.9386 7 12.804 7C12.6693 7 12.5505 7.04651 12.4554 7.13953L9.06535 10.4574L7.55248 8.96899C7.45743 8.87597 7.33861 8.82946 7.20396 8.82946C7.06931 8.82946 6.9505 8.87597 6.85545 8.96899L6.1505 9.65891C6.04752 9.75194 6 9.86822 6 10C6 10.1318 6.04752 10.2481 6.14257 10.3411L8.01188 12.1705L8.71683 12.8605C8.81188 12.9535 8.93069 13 9.06535 13C9.2 13 9.31881 12.9535 9.41386 12.8605L10.1188 12.1705L13.8574 8.51163C13.9525 8.4186 14 8.30233 14 8.17054C14 8.03876 13.9525 7.92248 13.8574 7.82946Z" fill="white"/>
					</svg>
				</div>
				<span><?= get_static_content('ship_to_different_address') ?></span>
			</label>
		</h3>

		<div class="shipping_address">

			<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

			<div class="woocommerce-shipping-fields__field-wrapper">
				<?php
				$fields = $checkout->get_checkout_fields( 'shipping' );

				foreach ( $fields as $key => $field ) {
					$placeholder = substr($key, strpos($key, '_') + 1);
					$field['placeholder'] = get_static_content($placeholder);
					$field['label'] = '';
					woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
				}
				?>
			</div>

			<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>

		</div>

	<?php endif; ?>
</div>
<div class="woocommerce-additional-fields">
	<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

	<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) : ?>

		<?php if ( ! WC()->cart->needs_shipping() || wc_ship_to_billing_address_only() ) : ?>

			<h3><?php esc_html_e( 'Additional information', 'woocommerce' ); ?></h3>

		<?php endif; ?>

		<div class="woocommerce-additional-fields__field-wrapper">
			<?php foreach ( $checkout->get_checkout_fields( 'order' ) as $key => $field ) : ?>
				<?
					$field['placeholder'] = $field['label'];
					$field['label'] = '';
				?>
				<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
			<?php endforeach; ?>
		</div>

	<?php endif; ?>

	<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
</div>
