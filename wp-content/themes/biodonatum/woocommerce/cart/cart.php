<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined( 'ABSPATH' ) || exit; ?>

<main class="main">
    <section class="section pt-50">
        <div class="container">
            <div class="breadcrumbs mb-40">
                <a href="<?= home_url(); ?>" class="breadcrumbs__link">to index</a>
                <span class="breadcrumbs__link">Your basket</span>
            </div>
            <div class="cart">
                <div class="head">
                    <h2 class="title">Your basket</h2>
                </div>
                <div class="island">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>SubTotal</th>
                                <th>Loyalty program</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                                $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                                $post_type = 'advanced_product';
                                $post_type_prefix = $post_type . '_';
                                $advanced_product_id = null;


                                $queryArgs = [
                                    'post_type'  => $post_type,
                                    'meta_query' => [
                                        [
                                            'key'     => $post_type_prefix . 'woo_id',
                                            'value'   => $product_id,
                                            'compare' => '='
                                        ],
                                    ],
                                    'tax_query' => [
                                        [
                                            'taxonomy' => 'taxonomy_language',
                                            'field'    => 'slug',
                                            'terms'    => $_SESSION['lang'],
                                        ],
                                    ],
                                ];

                                $query = new WP_Query($queryArgs);

                                if ($query->have_posts()) {
                                    $advanced_product_id = $query->posts[0]->ID;
                                }

                                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
                                    $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                                    ?>
                                    <tr data-cart_item_key="<?= $cart_item_key ?>">
                                        <td>
                                            <div class="cart__table--product">
                                                <? $image = get_field($post_type_prefix . 'images', $advanced_product_id)[0][$post_type_prefix . 'images_item']; ?>
                                                <picture>
                                                    <img src="<?= esc_url($image['url']); ?>" alt="<?= esc_attr($image['alt']); ?>">
                                                </picture>
                                                <div>
                                                    <?= get_field($post_type_prefix . 'name', $advanced_product_id) ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="quantity_panel">
                                                <div class="quantity_panel--minus">-</div>
                                                <input type="text" value="<?= $cart_item['quantity'] ?>">
                                                <div class="quantity_panel--plus">+</div>
                                            </div>
                                        </td>
                                        <td><?= WC()->cart->get_product_price( $_product ) ?></td>
                                        <td class="card-item__subtotal"><?= WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ) ?></td>
                                        <td class="card-item__discount">
                                            <?
                                                $line_subtotal = $cart_item['line_subtotal'];
                                                $line_total = $cart_item['line_total'];
                                                $discount = $line_total - $line_subtotal;
                                            ?>
                                            <?= wc_price($discount) ?>
                                        </td>
                                        <td>
                                            <div class="cart__table--del">
                                                <svg>
                                                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-trash-can"></use>
                                                </svg>
                                            </div>
                                        </td>
                                    </tr>
                                <? endif; ?>
                            <? endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="cart__promocode-and-total">
                    <div class="cart__promocode island">
                        <div class="cart__promocode--title">Details</div>
                        <form>
                            <input class="input" type="text" name="coupon_code" placeholder="Promocode">
                            <button class="button button--wide" type="submit">apply promocode</button>
                        </form>
                        <div class="cart__promocode--title">Order Notes (optional)</div>
                        <textarea class="input input--area" type="textarea" placeholder="Your message here"></textarea>
                    </div>
                    <div class="cart__total island">
                        <div class="cart__total__grid">
                            <div class="cart__total--title">Subtotal</div>
                            <div class="cart__total--subtotal"><?= WC()->cart->get_cart_subtotal() ?></div>
                            <div class="cart__total--title">Shipping</div>
                            <div class="cart__total--shipping">Shipping options will be updated upon ordering.</div>
                            <?
                                $appliedCoupons = WC()->cart->get_applied_coupons();
                                $show = !empty($appliedCoupons) ? '' : 'style="display:none;"';
                                $discountTotal = WC()->cart->get_cart_discount_total();
                            ?>
                            <div class="cart__total--title" <?= $show ?>>Applied coupons</div>
                            <div class="cart__total--coupons" <?= $show ?>>
                                <? foreach ($appliedCoupons as $coupon) : ?>
                                    <div class="cart__total--coupon">
                                        <div class="cart__total--coupon-name">
                                            <?= $coupon ?>
                                        </div>
                                        <div class="cart__total--coupon-del">
                                            <svg>
                                                <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-x"></use>
                                            </svg>
                                        </div>
                                    </div>
                                <? endforeach; ?>
                            </div>
                            <div class="cart__total--title" <?= $show ?>>Discount</div>
                            <div class="cart__total--discount" <?= $show ?>><?= wc_price(-$discountTotal) ?></div>
                            <div class="cart__total--title">Total</div>
                            <div class="cart__total--total"><?= WC()->cart->get_cart_total() ?></div>
                        </div>
                        <button class="button button--wide">validate the order</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section pt-80 ptm-60 pb-80 pbm-60">
        <div class="container">
            <div class="containers_line">
                <div class="feedback__form">
                    <h3 class="feedback__title">Write to us</h3>
                    <div class="wpcf7 js" id="wpcf7-f148-o1" lang="en-US" dir="ltr" data-wpcf7-id="148">
                        <div class="screen-reader-response"><p role="status" aria-live="polite" aria-atomic="true"></p> <ul></ul></div>
                        <form action="/" method="post" class="wpcf7-form">
                            <label class="line">
                                <input class="checkbox" type="radio" name="sex" value="муж">
                                <input class="checkbox" type="radio" name="sex" value="жен">
                            </label>
                            <label class="line">
                                <input class="input" placeholder="E-mail" type="email" name="email">  
                            </label>
                            <label class="line">
                                <input class="checkbox" type="checkbox" name="send" value="Получать рассылку">
                            </label>
                            <label class="line">
                                <input class="input" placeholder="E-mail" type="email" name="email">  
                            </label>
                            <label>
                                <input class="input" placeholder="Имя*" type="text" name="name">
                            </label>
                            <label>
                                <input class="input" placeholder="Фамилия*" type="text" name="surname">
                            </label>
                            <label class="line">
                                <input class="input" placeholder="E-mail" type="email" name="email">  
                            </label>
                            <label class="line">
                                <input class="input" placeholder="E-mail" type="email" name="email">  
                            </label>
                            <label>
                                <input class="input" placeholder="Имя*" type="text" name="name">
                            </label>
                            <label>
                                <input class="input" placeholder="Фамилия*" type="text" name="surname">
                            </label>
                            <label class="line">
                                <input class="input" placeholder="E-mail" type="email" name="email">  
                            </label>
                            <input class="wpcf7-submit" type="submit" value="отправить">
                        </form>
                    </div>
                </div>
                <div class="feedback__form">
                    <h3 class="feedback__title">Write to us</h3>
                    <div class="wpcf7 js" id="wpcf7-f148-o1" lang="en-US" dir="ltr" data-wpcf7-id="148">
                        <div class="screen-reader-response"><p role="status" aria-live="polite" aria-atomic="true"></p> <ul></ul></div>
                        <form action="/cart/#wpcf7-f148-o1" method="post" class="wpcf7-form init" aria-label="Contact form" novalidate="novalidate" data-status="init">
                        <div style="display: none;">
                        <input type="hidden" name="_wpcf7" value="148">
                        <input type="hidden" name="_wpcf7_version" value="6.0.1">
                        <input type="hidden" name="_wpcf7_locale" value="en_US">
                        <input type="hidden" name="_wpcf7_unit_tag" value="wpcf7-f148-o1">
                        <input type="hidden" name="_wpcf7_container_post" value="0">
                        <input type="hidden" name="_wpcf7_posted_data_hash" value="">
                        </div>
                        <span class="wpcf7-form-control-wrap" data-name="text-969"><input size="40" maxlength="400" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required input" autocomplete="name" aria-required="true" aria-invalid="false" placeholder="Имя*" value="" type="text" name="text-969"></span>
                        <span class="wpcf7-form-control-wrap" data-name="email-288"><input size="40" maxlength="400" class="wpcf7-form-control wpcf7-email wpcf7-validates-as-required wpcf7-text wpcf7-validates-as-email input" autocomplete="email" aria-required="true" aria-invalid="false" placeholder="E-mail*" value="" type="email" name="email-288"></span>
                        <span class="wpcf7-form-control-wrap" data-name="textarea-230"><textarea cols="40" rows="10" maxlength="2000" class="wpcf7-form-control wpcf7-textarea input input--area" aria-invalid="false" placeholder="Ваше сообщение" name="textarea-230"></textarea></span>
                        <input class="wpcf7-form-control wpcf7-submit has-spinner" type="submit" value="отправить"><span class="wpcf7-spinner"></span>
                        <div class="wpcf7-response-output" aria-hidden="true"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <? get_template_part('components/feedback'); ?>
</main>



<? do_action( 'woocommerce_before_cart' ); ?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<thead>
			<tr>
				<th class="product-remove"><span class="screen-reader-text"><?php esc_html_e( 'Remove item', 'woocommerce' ); ?></span></th>
				<th class="product-thumbnail"><span class="screen-reader-text"><?php esc_html_e( 'Thumbnail image', 'woocommerce' ); ?></span></th>
				<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
				<th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
				<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
				/**
				 * Filter the product name.
				 *
				 * @since 2.1.0
				 * @param string $product_name Name of the product in the cart.
				 * @param array $cart_item The product in the cart.
				 * @param string $cart_item_key Key for the product in the cart.
				 */
				$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

						<td class="product-remove">
							<?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										/* translators: %s is the product name */
										esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() )
									),
									$cart_item_key
								);
							?>
						</td>

						<td class="product-thumbnail">
						<?php
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

						if ( ! $product_permalink ) {
							echo $thumbnail; // PHPCS: XSS ok.
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
						}
						?>
						</td>

						<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
						<?php
						if ( ! $product_permalink ) {
							echo wp_kses_post( $product_name . '&nbsp;' );
						} else {
							/**
							 * This filter is documented above.
							 *
							 * @since 2.1.0
							 */
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
						}

						do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

						// Meta data.
						echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
						}
						?>
						</td>

						<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
						</td>

						<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
						<?php
						if ( $_product->is_sold_individually() ) {
							$min_quantity = 1;
							$max_quantity = 1;
						} else {
							$min_quantity = 0;
							$max_quantity = $_product->get_max_purchase_quantity();
						}

						$product_quantity = woocommerce_quantity_input(
							array(
								'input_name'   => "cart[{$cart_item_key}][qty]",
								'input_value'  => $cart_item['quantity'],
								'max_value'    => $max_quantity,
								'min_value'    => $min_quantity,
								'product_name' => $product_name,
							),
							$_product,
							false
						);

						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
						?>
						</td>

						<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
						</td>
					</tr>
					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>

			<tr>
				<td colspan="6" class="actions">

					<?php if ( wc_coupons_enabled() ) { ?>
						<div class="coupon">
							<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php } ?>

					<button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

					<?php do_action( 'woocommerce_cart_actions' ); ?>

					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</td>
			</tr>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

<div class="cart-collaterals">
	<?php
		/**
		 * Cart collaterals hook.
		 *
		 * @hooked woocommerce_cross_sell_display
		 * @hooked woocommerce_cart_totals - 10
		 */
		do_action( 'woocommerce_cart_collaterals' );
	?>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
