<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} ?>

<main class="main">
    <section class="section pt-50">
        <div class="container">
            <div class="breadcrumbs mb-40">
                <a href="<?= home_url(); ?>" class="breadcrumbs__link">to index</a>
                <span class="breadcrumbs__link">Checkout</span>
            </div>
            <div class="cart">
                <div class="head">
                    <h2 class="title">Checkout</h2>
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
                                                <?= $cart_item['quantity'] ?>
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
                                    </tr>
                                <? endif; ?>
                            <? endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="checkout">
                <?
                    // If checkout registration is disabled and not logged in, the user cannot checkout.
                    if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
                        echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
                        return;
                    }

                ?>

                <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

                    <div class="island checkout__address">
                        <?php if ( $checkout->get_checkout_fields() ) : ?>

                            <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

                            <div class="col2-set" id="customer_details">
                                <div class="col-1">
                                    <?php do_action( 'woocommerce_checkout_billing' ); ?>
                                </div>

                                <div class="col-2">
                                    <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                                </div>
                            </div>

                            <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

                        <?php endif; ?>
                    </div>

                    <div class="checkout__order">
                        <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

                        <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                        </div>

                        <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
                    </div>

                </form>

                <?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
            </div>
        </div>
    </section>

    <section class="section pt-80 ptm-60 pb-80 pbm-60">
        <div class="container">
            <form action="/" method="POST" class="containers_line">
                <div class="island">
                    <div class="cart__promocode--title">Contact:</div>
                    <div class="wpcf7_form">
                        <div class="line mb20">
                            <label>
                                <input type="radio" name="sex" value="m" checked>
                                <div class="checkbox"></div>
                                муж
                            </label>
                            <label>
                                <input type="radio" name="sex" value="w">
                                <div class="checkbox"></div>
                                жен
                            </label>
                        </div>
                        <label class="line">
                            <input class="input" placeholder="E-mail" type="email" name="email">
                        </label>
                        <div class="line mb20">
                            <label>
                                <input type="checkbox" name="send">
                                <div class="svg_container">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="20" height="20" rx="5" fill="#27AAE2"/>
                                        <path d="M13.8574 7.82946L13.1525 7.13953C13.0574 7.04651 12.9386 7 12.804 7C12.6693 7 12.5505 7.04651 12.4554 7.13953L9.06535 10.4574L7.55248 8.96899C7.45743 8.87597 7.33861 8.82946 7.20396 8.82946C7.06931 8.82946 6.9505 8.87597 6.85545 8.96899L6.1505 9.65891C6.04752 9.75194 6 9.86822 6 10C6 10.1318 6.04752 10.2481 6.14257 10.3411L8.01188 12.1705L8.71683 12.8605C8.81188 12.9535 8.93069 13 9.06535 13C9.2 13 9.31881 12.9535 9.41386 12.8605L10.1188 12.1705L13.8574 8.51163C13.9525 8.4186 14 8.30233 14 8.17054C14 8.03876 13.9525 7.92248 13.8574 7.82946Z" fill="white"/>
                                    </svg>
                                </div>
                                Получать рассылку
                            </label>
                        </div>
                        <label class="line">
                            <select class="st_select input" name="country" >
                                <option class="input" value="fr">France</option>
                                <option class="input" value="ru">Russian</option>
                                <option class="input" value="cn">China</option>
                            </select>
                        </label>
                        <label>
                            <input class="input" placeholder="Имя" type="text" name="name">
                        </label>
                        <label>
                            <input class="input" placeholder="Фамилия" type="text" name="surname">
                        </label>
                        <label class="line">
                            <input class="input" placeholder="Адрес улица" type="text" name="optional">
                        </label>
                        <label class="line">
                            <input class="input" placeholder="Апартаменты, люкс-студия и т.д. (по желанию)" type="text" name="unit">
                        </label>
                        <label>
                            <input class="input" placeholder="Город" type="text" name="city">
                        </label>
                        <label>
                            <input class="input" placeholder="Почтовый индекс" type="text" name="postal_code">
                        </label>
                        <label class="line">
                            <input class="input" placeholder="Телефон" type="phone" name="phone">
                        </label>
                    </div>
                </div>
                <div class="right_block_container">
                    <div class="cart__total island">
                        <h3 class="feedback__title">Your order</h3>
                        <div class="separator"></div>
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
                    </div>
                    <div class="island_wrapper">
                        <div class="line">
                            <label class="island">
                                <input type="radio" name="payment_method">
                                <div class="checkbox"></div>
                                Платёжка 1
                            </label>
                        </div>
                        <div class="line">
                            <label class="island">
                                <input type="radio" name="payment_method">
                                <div class="checkbox"></div>
                                Платёжка 2
                            </label>
                        </div>
                    </div>
                    <button class="button button--wide">order</button>
                </div>
            </form>
        </div>
    </section>

    <? get_template_part('components/feedback'); ?>
</main>
