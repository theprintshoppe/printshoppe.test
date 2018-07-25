<?php
add_action('wp_loaded', 'print_products_attributes_options_init');
function print_products_attributes_options_init() {
	global $wpdb;
	// form submit
	if (isset($_POST['print_products_attributes_options_action'])) {
		switch ($_POST['print_products_attributes_options_action']) {
			case "submit":
				$print_products_settings = get_option('print_products_settings');
				$sorder_notallowed = array(
					$print_products_settings['size_attribute'],
					$print_products_settings['colour_attribute'],
					$print_products_settings['page_count_attribute'],
					$print_products_settings['material_attribute']
				);

				$attributes_order = $_POST['attributes_order'];

				$print_products_settings['printing_attributes'] = serialize($_POST['printing_attributes']);
				$print_products_settings['finishing_attributes'] = serialize($_POST['finishing_attributes']);

				update_option("print_products_settings", $print_products_settings);

				if (is_array($attributes_order)) {
					foreach($attributes_order as $attribute_id => $sorder) {
						$attribute_order = (int)$sorder;
						if (!in_array($attribute_id, $sorder_notallowed) && $attribute_order < 4) {
							$attribute_order = 4;
						}
						$wpdb->update($wpdb->prefix.'woocommerce_attribute_taxonomies', array('attribute_order' => $attribute_order), array('attribute_id' => $attribute_id));
					}
				}

				$_SESSION['print_products_attributes_options_message'] = __('Settings were successfully saved.', 'wp2print');

				wp_redirect('edit.php?post_type=product&page=print-products-attributes-options');
				exit;
			break;
		}
	}
}

function print_products_attributes_options() {
	global $wpdb;
	$product_types = get_terms('product-type', 'hide_empty=0&orderby=id&order=asc');
	$wc_attributes = $wpdb->get_results(sprintf("SELECT * FROM %swoocommerce_attribute_taxonomies ORDER BY attribute_order, attribute_label", $wpdb->prefix));
	$print_products_settings = get_option('print_products_settings');
	$printing_attributes = unserialize($print_products_settings['printing_attributes']);
	$finishing_attributes = unserialize($print_products_settings['finishing_attributes']);

	$sorder_notallowed = array(
		$print_products_settings['size_attribute'],
		$print_products_settings['colour_attribute'],
		$print_products_settings['page_count_attribute'],
		$print_products_settings['material_attribute']
	);

	if (!is_array($printing_attributes)) { $printing_attributes = array(); }
	if (!is_array($finishing_attributes)) { $finishing_attributes = array(); }
	?>
	<div class="wrap wp2print-wrap">
		<?php screen_icon(); ?>
		<h2><?php _e('Attributes Options', 'wp2print'); ?></h2><br>
		<?php if(strlen($_SESSION['print_products_attributes_options_message'])) { ?><div id="message" class="updated fade"><p><?php echo $_SESSION['print_products_attributes_options_message']; ?></p></div><?php unset($_SESSION['print_products_attributes_options_message']); }
		if ($wc_attributes) { ?>
		<form action="edit.php?post_type=product&page=print-products-attributes-options" method="POST">
		<input type="hidden" name="print_products_attributes_options_action" value="submit">
		<table>
			<tr>
				<td><?php _e('Printing attributes', 'wp2print'); ?>:
				<?php print_products_help_icon('printing_attributes'); ?></td>
				<td>
					<?php if ($wc_attributes) {
						foreach($wc_attributes as $wc_attribute) { $s = ''; ?>
							<input type="checkbox" name="printing_attributes[]" value="<?php echo $wc_attribute->attribute_id; ?>"<?php if (in_array($wc_attribute->attribute_id, $printing_attributes)) { echo ' CHECKED'; } ?><?php if (in_array($wc_attribute->attribute_id, $sorder_notallowed)) { echo ' onclick="return false;"'; } ?>><?php echo $wc_attribute->attribute_label; ?><br>
							<?php
						}
					} ?>
				</td>
			</tr>
			<tr><td colspan="2" class="tddivider"><hr /></td></tr>
			<tr>
				<td><?php _e('Finishing attributes', 'wp2print'); ?>:
				<?php print_products_help_icon('finishing_attributes'); ?></td>
				<td>
					<?php if ($wc_attributes) {
						foreach($wc_attributes as $wc_attribute) { $s = '';
							if (!in_array($wc_attribute->attribute_id, $sorder_notallowed)) { ?>
								<input type="checkbox" name="finishing_attributes[]" value="<?php echo $wc_attribute->attribute_id; ?>"<?php if (in_array($wc_attribute->attribute_id, $finishing_attributes)) { echo ' CHECKED'; } ?>><?php echo $wc_attribute->attribute_label; ?><br>
								<?php
							}
						}
					} ?>
				</td>
			</tr>
			<tr><td colspan="2" class="tddivider"><hr /></td></tr>
			<tr>
				<td><?php _e('Attributes sort order', 'wp2print'); ?>:
				<?php print_products_help_icon('attributes_order'); ?></td>
				<td>
					<?php if ($wc_attributes) {
						foreach($wc_attributes as $wc_attribute) { $s = ''; ?>
							<input type="text" name="attributes_order[<?php echo $wc_attribute->attribute_id; ?>]" value="<?php echo $wc_attribute->attribute_order; ?>" style="width:25px; padding:1px; font-size:12px;"<?php if (in_array($wc_attribute->attribute_id, $sorder_notallowed)) { echo ' readonly'; } ?>> <?php echo $wc_attribute->attribute_label; ?><br>
							<?php
						}
					} ?>
				</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save', 'wp2print') ?>" /></p>
		</form>
		<?php } else { ?>
			<?php _e('Please add product attributes.', 'wp2print'); ?>
		<?php } ?>
	</div>
	<?php
}

add_action('presenters_edit_form_fields', 'presenters_taxonomy_custom_fields', 10, 2);

?>