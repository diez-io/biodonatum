<?php
/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}
?>
<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ) ); ?>">

	<td class="woocommerce-table__product-name product-name">
		<?php
		$is_visible        = $product && $product->is_visible();
		$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

		$post_type = 'advanced_product';
		$post_type_prefix = $post_type . '_';
		$advanced_product_id = null;

		$isVariable = $product->get_type() === 'variation';

		if ($isVariable) {
			$product_id = $product->get_parent_id();
		}
		else {
			$product_id = $product->get_id();
		}

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
					'terms'    => function_exists('get_current_language') ? get_current_language() : 'en',
				],
			],
		];

		$query = new WP_Query($queryArgs);

		if ($query->have_posts()) {
			$advanced_product_id = $query->posts[0]->ID;
			$advanced_product_name = get_field($post_type_prefix . 'name', $advanced_product_id);
			$product_permalink = get_permalink($advanced_product_id);
		}

		echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $advanced_product_id ? $advanced_product_name : $item->get_name() ) : $item->get_name(), $item, $is_visible ) );

		$qty          = $item->get_quantity();
		$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

		if ( $refunded_qty ) {
			$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
		} else {
			$qty_display = esc_html( $qty );
		}


		if ($isVariable) : ?>
			<div class="cart__table__variation">
				<?= get_static_content('months_' . $product->get_attribute('duration')) ?>
			</div>
		<? endif;

		echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', $qty_display ) . '</strong>', $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</td>

	<td class="woocommerce-table__product-total product-total">
		<?php echo $order->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</td>

</tr>

<?php if ( $show_purchase_note && $purchase_note ) : ?>

<tr class="woocommerce-table__product-purchase-note product-purchase-note">

	<td colspan="2"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>

</tr>

<?php endif; ?>
