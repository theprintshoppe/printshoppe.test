<?php
/**
 * Order Item Details
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.0
 */
global $wpdb;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}
?>
<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
	<td class="product-name">
		<?php
			$is_visible = $product && $product->is_visible();
			$order_item_data = $wpdb->get_row(sprintf("SELECT * FROM %sprint_products_order_items WHERE item_id = '%s'", $wpdb->prefix, $item_id));

			echo apply_filters( 'woocommerce_order_item_name', $is_visible ? sprintf( '<a href="%s">%s</a>', get_permalink( $item['product_id'] ), $item['name'] ) : $item['name'], $item, $is_visible );

			if ($order_item_data) {
				$sku = print_products_get_item_sku($order_item_data);
				if (strlen($sku)) {
					echo ' &ndash; (' . esc_html($sku) . ')';
				}
			}

			echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $item['qty'] ) . '</strong>', $item );

			do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

			$order->display_item_meta( $item );
			$order->display_item_downloads( $item );

			do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );

			$designer_image = $wpdb->get_var(sprintf("SELECT meta_value FROM %swoocommerce_order_itemmeta WHERE order_item_id = %s AND meta_key = '_image_link'", $wpdb->prefix, $item_id));
			if (strlen($designer_image)) {
				$dimages = explode(',', $designer_image); ?>
				<div class="print-products-area">
					<ul class="product-attributes-list">
						<li><?php _e('Designer File', 'wp2print'); ?>:</li>
						<li>
							<ul class="product-artwork-files-list">
								<?php foreach($dimages as $dimage) { ?>
									<li><a href="<?php echo $dimage; ?>" rel="prettyPhoto" data-rel="prettyPhoto[<?php echo $item_id; ?>]"><img src="<?php echo $dimage; ?>" width="100" style="width:70px;border:1px solid #C1C1C1;"></a></li>
								<?php } ?>
							</ul>
						</li>
					</ul>
				</div>
			<?php }

			if ($order_item_data) {
				print_products_product_attributes_list_html($order_item_data);
				print_products_product_thumbs_list_html($order_item_data);
			}

			echo '<div class="clear"></div>';

			if (is_page('my-account')) {
				if ($order_item_data) {
					if ($order_item_data->atcaction == 'design') { ?>
						<div class="reorder-buttons">
							<input type="button" value="Reorder with no changes" class="black-btn" onclick="reorder_product_action(<?php echo $item_id; ?>, 'designnochange');">
							<input type="button" value="Reorder with design change" class="black-btn" onclick="reorder_product_action(<?php echo $item_id; ?>, 'design');">
						</div>
					<?php } else if ($order_item_data->atcaction == 'artwork') { ?>
						<div class="reorder-buttons">
							<input type="button" value="Reorder" class="black-btn" onclick="reorder_product_action(<?php echo $item_id; ?>, 'artwork');">
						</div>
					<?php } ?>
					<form method="POST" class="history-reorder-form-<?php echo $item_id; ?>">
						<input type="hidden" name="print_products_checkout_process_action" value="reorder">
						<input type="hidden" name="add-to-cart" value="<?php echo $order_item_data->product_id; ?>">
						<input type="hidden" name="reorder_item_id" value="<?php echo $item_id; ?>">
						<input type="hidden" name="quantity" value="<?php echo $order_item_data->quantity; ?>">
						<input type="hidden" name="atcaction" class="atc-action">
						<input type="hidden" name="redesign" class="redesign-fld">
					</form>
				<?php }
			}
		?>
	</td>
	<td class="product-total" style="vertical-align:top;">
		<?php echo $order->get_formatted_line_subtotal( $item ); ?>
	</td>
</tr>
<?php if ( $order->has_status( array( 'completed', 'processing' ) ) && ( $purchase_note = get_post_meta( $product->id, '_purchase_note', true ) ) ) : ?>
<tr class="product-purchase-note">
	<td colspan="3"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></td>
</tr>
<?php endif; ?>
