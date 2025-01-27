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
                <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                <span class="breadcrumbs__link"><?= get_static_content('checkout') ?></span>
            </div>
            <div class="cart">
                <div class="head">
                    <h2 class="title"><?= get_static_content('checkout') ?></h2>
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
                    <div class="checkout__wrapper">
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
                    </div>
                </form>

                <?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
            </div>
        </div>
    </section>

    <? get_template_part('components/feedback'); ?>
</main>
