<?php
/**
 * Pay for order form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-pay.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.2.0
 */

defined( 'ABSPATH' ) || exit; ?>

<main class="main">
    <section class="section pt-50">
        <div class="container">
            <div class="breadcrumbs mb-40">
                <a href="<?= esc_url(biodonatum_url_with_lang(home_url('/'))); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                <span class="breadcrumbs__link"><?= get_static_content('cart') ?></span>
            </div>
            <div class="checkout checkout__pay">
				<?
					$totals = $order->get_order_item_totals(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				?>
				<form id="order_review" method="post">

					<div class="island">
						<h2><?= get_static_content('your_order') ?></h2>
						<table class="shop_table">
							<tbody>
								<?php if ( count( $order->get_items() ) > 0 ) : ?>
									<?php foreach ( $order->get_items() as $item_id => $item ) : ?>
										<?
											$post_type = 'advanced_product';
											$post_type_prefix = $post_type . '_';
											$advanced_product_id = null;
											$_product = $item->get_product();

											$queryArgs = [
												'post_type'  => $post_type,
												'meta_query' => [
													[
														'key'     => $post_type_prefix . 'woo_id',
														'value'   => $item->get_product_id(),
														'compare' => '='
													],
												],
												'tax_query' => [
													[
														'taxonomy' => 'taxonomy_language',
														'field'    => 'slug',
														'terms'    => function_exists('get_current_language') ? get_current_language() : 'en',
													],
												],
											];

											$query = new WP_Query($queryArgs);

											if ($query->have_posts()) {
												$advanced_product_id = $query->posts[0]->ID;
											}

											if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
												continue;
											}
										?>
										<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
											<td class="product-name">
												<? if ($advanced_product_id) : ?>
													<?= get_field($post_type_prefix . 'name', $advanced_product_id) ?>
													<? if ($_product->get_type() === 'variation') : ?>
														<div class="cart__table__variation">
															<?= get_static_content('months_' . $_product->get_attribute('duration')) ?>
														</div>
													<? endif; ?>
													<span class="product-quantity">x <?= $item->get_quantity() ?></span>
												<? else :
													echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );

													do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

													wc_display_item_meta( $item );

													do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
												endif; ?>
											</td>
											<td class="product-subtotal"><?php echo $order->get_formatted_line_subtotal( $item ); ?></td><?php // @codingStandardsIgnoreLine ?>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
							<tfoot>
								<?php if ( $totals ) : ?>
									<?php foreach ( $totals as $total ) : ?>
										<tr>
											<th scope="row"><?php echo $total['label']; ?></th><?php // @codingStandardsIgnoreLine ?>
											<td class="product-total"><?php echo $total['value']; ?></td><?php // @codingStandardsIgnoreLine ?>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tfoot>
						</table>
					</div>

					<?php
					/**
					 * Triggered from within the checkout/form-pay.php template, immediately before the payment section.
					 *
					 * @since 8.2.0
					 */
					do_action( 'woocommerce_pay_order_before_payment' );
					?>

					<div id="payment">
						<?php if ( $order->needs_payment() ) : ?>
							<ul class="wc_payment_methods payment_methods methods containers_line">
								<?php
								if ( ! empty( $available_gateways ) ) {
									foreach ( $available_gateways as $gateway ) {
										wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
									}
								} else {
									echo '<li>';
									wc_print_notice( apply_filters( 'woocommerce_no_available_payment_methods_message', esc_html__( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) ), 'notice' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
									echo '</li>';
								}
								?>
							</ul>
						<?php endif; ?>
						<div class="form-row">
							<input type="hidden" name="woocommerce_pay" value="1" />

							<?php wc_get_template( 'checkout/terms.php' ); ?>

							<?php do_action( 'woocommerce_pay_order_before_submit' ); ?>

							<?php echo apply_filters( 'woocommerce_pay_order_button_html', '<button type="submit" class="button alt' . esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ) . '" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

							<?php do_action( 'woocommerce_pay_order_after_submit' ); ?>

							<?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
    <? get_template_part('components/feedback'); ?>
</main>


