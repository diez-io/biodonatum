<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 *
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;
?>

<main class="main">
    <section class="section pt-50">
        <div class="container">
			<div class="checkout">
				<div class="woocommerce-order">

					<?php
					if ( $order ) :

						do_action( 'woocommerce_before_thankyou', $order->get_id() );
						?>

						<?php if ( $order->has_status( 'failed' ) ) : ?>

							<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

							<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
								<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?= get_static_content('pay') ?></a>
								<?php if ( is_user_logged_in() ) : ?>
									<a href="<?php echo esc_url( biodonatum_url_with_lang( wc_get_page_permalink( 'myaccount' ) ) ); ?>" class="button pay"><?= get_static_content('my_account') ?></a>
								<?php endif; ?>
							</p>

						<?php else : ?>

							<?php wc_get_template( 'checkout/order-received.php', array( 'order' => $order ) ); ?>

							<div class="island checkout__order-review">
							<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

								<li class="woocommerce-order-overview__order order">
									<?= get_static_content('order_number') ?>
									<strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
								</li>

								<li class="woocommerce-order-overview__date date">
									<?= get_static_content('date') . ':' ?>
									<strong><?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
								</li>

								<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
									<li class="woocommerce-order-overview__email email">
										<?= get_static_content('email') . ':' ?>
										<strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
									</li>
								<?php endif; ?>

								<li class="woocommerce-order-overview__total total">
									<?= get_static_content('total') . ':' ?>
									<strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
								</li>

								<?php if ( $order->get_payment_method_title() ) : ?>
									<li class="woocommerce-order-overview__payment-method method">
										<?= get_static_content('payment_method') . ':' ?>
										<strong><?php echo wp_kses_post( __($order->get_payment_method_title()) ); ?></strong>
									</li>
								<?php endif; ?>

							</ul>

						<?php endif; ?>

						<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
						<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

					<?php else : ?>

						<?php wc_get_template( 'checkout/order-received.php', array( 'order' => false ) ); ?>

					<?php endif; ?>

				</div>
			</div>
        </div>
    </section>

    <? get_template_part('components/feedback'); ?>
</main>
