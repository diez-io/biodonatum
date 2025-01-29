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
                <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                <span class="breadcrumbs__link"><?= get_static_content('cart') ?></span>
            </div>
            <div class="cart">
                <div class="head">
                    <h2 class="title"><?= get_static_content('cart') ?></h2>
                </div>
                <div class="island">
                    <table>
                        <thead>
                            <tr>
                                <th><?= get_static_content('product') ?></th>
                                <th><?= get_static_content('quantity') ?></th>
                                <th><?= get_static_content('price') ?></th>
                                <th><?= get_static_content('subtotal') ?></th>
                                <th><?= get_static_content('loyalty_program') ?></th>
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
                                        <td class="cart__table__cell__product">
                                            <div class="cart__table--product">
                                                <? $image = get_field($post_type_prefix . 'images', $advanced_product_id)[0][$post_type_prefix . 'images_item']; ?>
                                                <picture>
                                                    <img src="<?= esc_url($image['url']); ?>" alt="<?= esc_attr($image['alt']); ?>">
                                                </picture>
                                                <div>
                                                    <?= get_field($post_type_prefix . 'name', $advanced_product_id) ?>
                                                    <? if ($_product->get_type() === 'variation') : ?>
                                                        <div class="cart__table__variation">
                                                            <?= get_static_content('months_' . $_product->get_attribute('duration')) ?>
                                                        </div>
                                                    <? endif; ?>
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
                                        <td class="cart__table__cell__not-del cart__table__cell__not-del__top-border">
                                            <div class="cart__table__header--mob"><?= get_static_content('price') ?></div>
                                            <div class="cart__table__cell--mob">
                                                <?= WC()->cart->get_product_price( $_product ) ?>
                                            </div>
                                        </td>
                                        <td class="cart__table__cell__not-del">
                                            <div class="cart__table__header--mob"><?= get_static_content('subtotal') ?></div>
                                            <div class="cart__table__cell--mob card-item__subtotal">
                                                <?= WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ) ?>
                                            </div>
                                        </td>
                                        <td class="cart__table__cell__not-del cart__table__cell__not-del__bottom-margin">
                                            <div class="cart__table__header--mob"><?= get_static_content('loyalty_program') ?></div>
                                            <div class="cart__table__cell--mob card-item__discount">
                                                <?
                                                    $line_subtotal = $cart_item['line_subtotal'];
                                                    $line_total = $cart_item['line_total'];
                                                    $discount = $line_total - $line_subtotal;
                                                ?>
                                                <?= wc_price($discount) ?>
                                            </div>
                                        </td>
                                        <td class="cart__table__cell__del">
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
                        <form>
                            <input class="input" type="text" name="coupon_code" placeholder="<?= get_static_content('promocode') ?>">
                            <button class="button button--wide" type="submit"><?= get_static_content('apply_promocode') ?></button>
                        </form>
                    </div>
                    <div class="cart__total island">
                        <div class="cart__total__grid">
                            <div class="cart__total--title"><?= get_static_content('subtotal') ?></div>
                            <div class="cart__total--subtotal"><?= WC()->cart->get_cart_subtotal() ?></div>
                            <div class="cart__total--title"><?= get_static_content('shipping') ?></div>
                            <div class="cart__total--shipping"><?= get_static_content('shipping_options_will_be_updated') ?></div>
                            <?
                                $appliedCoupons = WC()->cart->get_applied_coupons();
                                $show = !empty($appliedCoupons) ? '' : 'style="display:none;"';
                                $discountTotal = WC()->cart->get_cart_discount_total();
                            ?>
                            <div class="cart__total--title" <?= $show ?>><?= get_static_content('applied_coupons') ?></div>
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
                            <div class="cart__total--title" <?= $show ?>><?= get_static_content('discount') ?></div>
                            <div class="cart__total--discount" <?= $show ?>><?= wc_price(-$discountTotal) ?></div>
                            <div class="cart__total--title"><?= get_static_content('total') ?></div>
                            <div class="cart__total--total"><?= WC()->cart->get_cart_total() ?></div>
                        </div>
                        <a href="<?= esc_url(wc_get_checkout_url()) ?>" class="button button--wide mt-20">
                            <?= get_static_content('validate_the_order') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <? get_template_part('components/feedback'); ?>
</main>
