<?php
/**
 * Payment methods
 *
 * Shows customer payment methods on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/payment-methods.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.9.0
 */

defined( 'ABSPATH' ) || exit;

$saved_methods = wc_get_customer_saved_methods_list( get_current_user_id() );
$has_methods   = (bool) $saved_methods;
$types         = wc_get_account_payment_methods_types();

do_action( 'woocommerce_before_account_payment_methods', $has_methods ); ?>

<?php if ( $has_methods ) : ?>

	<table class="woocommerce-MyAccount-paymentMethods shop_table shop_table_responsive account-payment-methods-table">
		<?php foreach ( $saved_methods as $type => $methods ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
			<?php foreach ( $methods as $method ) : ?>
				<tr class="payment-method<?php echo ! empty( $method['is_default'] ) ? ' default-payment-method' : ''; ?>">
					<?php foreach ( wc_get_account_payment_methods_columns() as $column_id => $column_name ) : ?>
						<? if ( 'actions' === $column_id ) : ?>
							<td class="woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr( $column_id ); ?> payment-method-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
								<div class="account__cell-wrap">
								<? if (array_key_exists('default', $method['actions'])) {
									$key = 'default';
									$action = $method['actions'][$key];
									echo '<a href="' . esc_url( $action['url'] ) . '" class="account__make-default-btn ' . sanitize_html_class( $key ) . '">' . get_static_content('make_default') . '</a>';
								}
								else {
									echo '<div class="account__default-method">' . get_static_content('default') . '</div>';
								} ?>
								</div>
							</td>
							<td class="woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr( $column_id ); ?> payment-method-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
								<div class="account__cell-wrap">
								<?
									$key = 'delete';
									$action = $method['actions'][$key];
									echo '<a href="' . esc_url( $action['url'] ) . '" class="">';

									?>
									<svg>
										<use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-trash-can"></use>
									</svg>
									<?
									echo '</a>';
								?>
								</div>
							</td>
						<? else : ?>
						<td class="woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr( $column_id ); ?> payment-method-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
							<div class="account__cell-wrap">
							<?php
							if ( has_action( 'woocommerce_account_payment_methods_column_' . $column_id ) ) {
								do_action( 'woocommerce_account_payment_methods_column_' . $column_id, $method );
							} elseif ( 'method' === $column_id ) {
								if ( ! empty( $method['method']['last4'] ) ) {
									/* translators: 1: credit card type 2: last 4 digits */
									echo sprintf( get_static_content('1s_ending_in_2s'), esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) ), esc_html( $method['method']['last4'] ) );
								} else {
									echo esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) );
								}
							} elseif ( 'expires' === $column_id ) {
								echo esc_html( $method['expires'] );
							}
							?>
							</div>
						</td>
						<? endif; ?>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</table>

<?php else : ?>

	<?php wc_print_notice( get_static_content('no_saved_methods_found'), 'notice' ); ?>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_payment_methods', $has_methods ); ?>

<?php if ( WC()->payment_gateways->get_available_payment_gateways() ) : ?>
	<button class="input account__add-btn">
		+ <?= get_static_content('add_payment_method') ?>
	</button>

	<?
		do_action( 'before_woocommerce_add_payment_method' );
		do_action('woocommerce_account_add-payment-method_endpoint');
		//wc_get_template( 'myaccount/form-add-payment-method.php' );
		do_action( 'after_woocommerce_add_payment_method' );
	?>
<?php endif; ?>
