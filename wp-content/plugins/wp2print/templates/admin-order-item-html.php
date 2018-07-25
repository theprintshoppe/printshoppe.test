<?php
global $wpdb, $siteurl;
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
?>
<tr class="item <?php if (!empty($class)) echo $class; ?>" data-order_item_id="<?php echo $item_id; ?>">
    <td class="check-column"><input type="checkbox" /></td>
    <td class="thumb">
        <?php
        if ($_product) :
            $image = $_product->get_image(array(38, 38, 1), array('title' => '')); ?>
            <a href="<?php echo esc_url(admin_url('post.php?post=' . absint($_product->id) . '&action=edit')); ?>" class="tips" data-tip="<?php
            echo '<strong>' . __('Product ID:', 'woocommerce') . '</strong> ' . absint($item['product_id']);
            if ($item['variation_id'])
                echo '<br/><strong>' . __('Variation ID:', 'woocommerce') . '</strong> ' . absint($item['variation_id']);
            ?>"><?php echo $image; ?></a>
        <?php else : ?>
	        <?php echo wc_placeholder_img(array(38, 38, 1)); ?>
		<?php endif; ?>
    </td>
    <td class="name">

        <?php if ($_product) : ?>
            <a target="_blank" href="<?php echo esc_url(admin_url('post.php?post=' . absint($_product->id) . '&action=edit')); ?>"><?php echo esc_html($item['name']); ?></a>
        <?php else : ?>
                <?php echo esc_html($item['name']); ?>
        <?php endif; ?>

		<?php
		$order_item_data = $wpdb->get_row(sprintf("SELECT * FROM %sprint_products_order_items WHERE item_id = '%s'", $wpdb->prefix, $item_id));
		?>

		<?php if ($_product) {
			$product_sku = $_product->get_sku();
			if ($order_item_data) {
				$item_sku = print_products_get_item_sku($order_item_data);
				if ($item_sku) {
					$product_sku = $item_sku;
				}
			}
			if ($product_sku) { echo ' &ndash; (' . esc_html($product_sku) . ')'; } ?>

		<?php } ?>


		<div class="view">
			<?php
				global $wpdb;

				if ( $metadata = $order->has_meta( $item_id ) ) {
					echo '<table cellspacing="0" class="display_meta">';
					foreach ( $metadata as $meta ) {

						// Skip hidden core fields
						if ( in_array( $meta['meta_key'], apply_filters( 'woocommerce_hidden_order_itemmeta', array(
							'_qty',
							'_tax_class',
							'_product_id',
							'_variation_id',
							'_line_subtotal',
							'_line_subtotal_tax',
							'_line_total',
							'_line_tax'
						) ) ) ) {
							continue;
						}

						// Skip serialised meta
						if ( is_serialized($meta['meta_value']) || substr($meta['meta_key'], 0, 1) == '_' ) {
							continue;
						}

						// Get attribute data
						if ( taxonomy_exists( wc_sanitize_taxonomy_name( $meta['meta_key'] ) ) ) {
							$term               = get_term_by( 'slug', $meta['meta_value'], wc_sanitize_taxonomy_name( $meta['meta_key'] ) );
							$meta['meta_key']   = wc_attribute_label( wc_sanitize_taxonomy_name( $meta['meta_key'] ) );
							$meta['meta_value'] = isset( $term->name ) ? $term->name : $meta['meta_value'];
						} else {
							$meta['meta_key']   = apply_filters( 'woocommerce_attribute_label', wc_attribute_label( $meta['meta_key'], $_product ), $meta['meta_key'] );
						}

						echo '<tr><th>' . wp_kses_post( rawurldecode( $meta['meta_key'] ) ) . ':</th><td>' . wp_kses_post( wpautop( make_clickable( rawurldecode( $meta['meta_value'] ) ) ) ) . '</td></tr>';
					}
					echo '</table>';
				}
			?>
		</div>

		<?php
		if ($order_item_data) {
			$artwork_files = unserialize($order_item_data->artwork_files);
			print_products_product_attributes_list_html($order_item_data);
			if ($artwork_files) { ?>
				<div class="print-products-area">
					<ul class="product-attributes-list">
						<?php if ($order_item_data->product_type == 'aec') { ?>
							<li><?php _e('Files', 'wp2print'); ?>:</li>
							<?php foreach($artwork_files as $artwork_file) {
								echo '<li><strong><a href="'.$artwork_file.'" title="'.__('Download', 'wp2print').'">'.basename($artwork_file).'</a></strong></li>';
							} ?>
						<?php } else { ?>
							<li><?php _e('Artwork Files', 'wp2print'); ?>:</li>
							<li><ul class="product-artwork-files-list">
								<?php foreach($artwork_files as $artwork_file) {
									echo '<li>'.print_products_artwork_file_html($artwork_file, 'download').'</li>';
								} ?>
							</ul></li>
						<?php } ?>
					</ul>
				</div>
			<?php }
		}
		$designer_image = $wpdb->get_var(sprintf("SELECT meta_value FROM %swoocommerce_order_itemmeta WHERE order_item_id = %s AND meta_key = '_image_link'", $wpdb->prefix, $item_id));
		if (strlen($designer_image)) {
			$pdf_link = $wpdb->get_var(sprintf("SELECT meta_value FROM %swoocommerce_order_itemmeta WHERE order_item_id = %s AND meta_key = '_pdf_link'", $wpdb->prefix, $item_id));
			$dimages = explode(',', $designer_image); ?>
			<div class="print-products-area">
				<ul class="product-attributes-list">
					<li><?php _e('Designer File', 'wp2print'); ?>:</li>
					<li>
						<ul class="product-artwork-files-list">
							<?php foreach($dimages as $dimage) {
								$dimage_link = $dimage;
								if (strlen($pdf_link)) { $dimage_link = $pdf_link; } ?>
								<li><a href="<?php echo $dimage_link; ?>" title="<?php _e('Download', 'wp2print'); ?>"><img src="<?php echo $dimage; ?>" style="width:70px;border:1px solid #C1C1C1;"></a></li>
							<?php } ?>
						</ul>
					</li>
			</div>
			<?php
		}
		?>

        <input type="hidden" class="order_item_id" name="order_item_id[]" value="<?php echo esc_attr($item_id); ?>" />

	<?php
	if ($_product && isset($_product->variation_data))
		echo '<br/>' . woocommerce_get_formatted_variation($_product->variation_data, true);
		?>
        <table class="meta" cellspacing="0">
            <tfoot>
                <tr>
                    <td colspan="4"><button class="add_order_item_meta button" type="button"><?php _e('Add&nbsp;meta', 'woocommerce'); ?></button></td>
                </tr>
            </tfoot>
            <tbody class="meta_items">
				<?php
				if ($metadata = $order->has_meta($item_id)) {
					foreach ($metadata as $meta) {

						// Skip hidden core fields
						if (in_array($meta['meta_key'], apply_filters('woocommerce_hidden_order_itemmeta', array(
									'_qty',
									'_tax_class',
									'_product_id',
									'_variation_id',
									'_line_subtotal',
									'_line_subtotal_tax',
									'_line_total',
									'_line_tax',
									'_edit_session_key'
								))))
							continue;

						// Skip serialised meta
						if (is_serialized($meta['meta_value']) || substr($meta['meta_key'], 0, 1) == '_')
							continue;

						$meta['meta_key'] = esc_attr($meta['meta_key']);
						$meta['meta_value'] = esc_textarea(urldecode($meta['meta_value'])); // using a <textarea />
						$meta['meta_id'] = absint($meta['meta_id']);

						echo '<tr data-meta_id="' . $meta['meta_id'] . '">
										<td><input type="text" name="meta_key[' . $meta['meta_id'] . ']" value="' . $meta['meta_key'] . '" /></td>
										<td><input type="text" name="meta_value[' . $meta['meta_id'] . ']" value="' . $meta['meta_value'] . '" /></td>
										<td width="1%"><button class="remove_order_item_meta button" type="button">&times;</button></td>
									</tr>';
					}
				}
				?>
            </tbody>
        </table>
    </td>

    <?php do_action('woocommerce_admin_order_item_values', $_product, $item, absint($item_id)); ?>

    <?php if (get_option('woocommerce_calc_taxes') == 'yes') : ?>

        <td class="tax_class" width="1%">
            <select class="tax_class" name="order_item_tax_class[<?php echo absint($item_id); ?>]" title="<?php _e('Tax class', 'woocommerce'); ?>">
                <?php
                $item_value = isset($item['tax_class']) ? sanitize_title($item['tax_class']) : '';

                $tax_classes = array_filter(array_map('trim', explode("\n", get_option('woocommerce_tax_classes'))));

                $classes_options = array();
                $classes_options[''] = __('Standard', 'woocommerce');

                if ($tax_classes)
                    foreach ($tax_classes as $class)
                        $classes_options[sanitize_title($class)] = $class;

                foreach ($classes_options as $value => $name)
                    echo '<option value="' . esc_attr($value) . '" ' . selected($value, $item_value, false) . '>' . esc_html($name) . '</option>';
                ?>
            </select>
        </td>

    <?php endif; ?>

    <td class="quantity" width="1%">
        <input type="number" step="<?php echo apply_filters('woocommerce_quantity_input_step', '1', $_product); ?>" min="0" autocomplete="off" name="" placeholder="0" value="<?php echo esc_attr($item['qty']); ?>" size="4" class="quantity" />
    </td>

    <td class="line_cost" width="1%">
        <label><?php _e('Total', 'woocommerce'); ?>: <input type="number" step="any" min="0"  placeholder="0.00" value="<?php if (isset($item['line_total'])) echo sprintf("%01.2f", $item['line_total']); ?>" class="line_total1" onchange="javascript:change_hidden(<?php echo absint($item_id); ?>, this.value)"/></label>

        <span class="subtotal"><label><?php _e('Subtotal', 'woocommerce'); ?>: <input type="number" step="any" min="0"  placeholder="0.00" value="<?php if (isset($item['line_subtotal'])) echo sprintf("%01.2f", $item['line_subtotal']); ?>" class="line_subtotal" /></label></span>
    </td>

    <?php if (get_option('woocommerce_calc_taxes') == 'yes') : ?>

        <td class="line_tax" width="1%">
            <input type="number" step="any" min="0"  placeholder="0.00" value="<?php if (isset($item['line_tax'])) echo sprintf("%01.2f", $item['line_tax']); ?>" class="line_tax1" onchange="javascript:change_tax_hidden(<?php echo absint($item_id); ?>, this.value)" />

            <span class="subtotal"><input type="number" step="any" min="0"  placeholder="0.00" value="<?php if (isset($item['line_subtotal_tax'])) echo sprintf("%01.2f", $item['line_subtotal_tax']); ?>" class="line_subtotal_tax" /></span>
        </td>

    <?php endif; ?>

</tr>