<?php
add_action('admin_menu', 'print_products_create_order_admin_menu', 100);
function print_products_create_order_admin_menu() {
	add_submenu_page('woocommerce', __('Create Order', 'wp2print'), __('Create Order', 'wp2print'), 'manage_options', 'print-products-create-order', 'print_products_create_order_admin_page');
}

function print_products_create_order_admin_page() {
	include PRINT_PRODUCTS_TEMPLATES_DIR . 'admin-create-order.php';
}

add_action('wp_loaded', 'print_products_create_order_actions');
function print_products_create_order_actions() {
	if (isset($_POST['print_products_create_order_action']) && $_POST['print_products_create_order_action'] == 'process') {
		$order_data = print_products_create_order_get_order_data();
		switch ($_POST['process_step']) {
			case '1':
				$order_data['customer'] = $_POST['order_customer'];
				$order_data['product'] = $_POST['order_product'];
				$order_data['product_data'] = array();
				print_products_create_order_set_order_data($order_data);
			break;
			case '2':
				print_products_create_order_set_customer_address();
			break;
			case '3':
				print_products_create_order_set_product_data();
			break;
			case 'create':
				print_products_create_order_save_order();
			break;
		}
	}
}

function print_products_create_order_get_order_data() {
	$order_data = array();
	if (isset($_SESSION['create_order_data'])) { $order_data = $_SESSION['create_order_data']; }
	return $order_data;
}

function print_products_create_order_set_order_data($order_data) {
	$_SESSION['create_order_data'] = $order_data;
}

function print_products_create_order_set_product_data() {
	$product_type = $_POST['product_type'];
	$quantity = (int)$_POST['quantity'];
	$price = (float)$_POST['price'];
	$tax = (float)$_POST['tax'];
	$tax_rate_id = $_POST['tax_rate_id'];
	$shipping = (float)$_POST['shipping'];
	$shipping_tax = (float)$_POST['shipping_tax'];
	$total = (float)$_POST['total'];

	$product_data = array(
		'product_type' => $product_type,
		'quantity' => $quantity,
		'price' => $price,
		'tax' => $tax,
		'tax_rate_id' => $tax_rate_id,
		'shipping' => $shipping,
		'shipping_tax' => $shipping_tax,
		'total' => $total
	);

	$cart_item_key = '100010001';
	$checkout_data = false;
	switch ($product_type) {
		case 'fixed':
			$checkout_data = print_products_checkout_fixed($cart_item_key, false, true);
		break;
		case 'book':
			$checkout_data = print_products_checkout_book($cart_item_key, false, true);
		break;
		case 'area':
			$checkout_data = print_products_checkout_area($cart_item_key, false, true);
		break;
		case 'aec':
			$checkout_data = print_products_checkout_aec($cart_item_key, false, true);
		break;
		case 'aecbwc':
			$checkout_data = print_products_checkout_aecbwc($cart_item_key, false, true);
		break;
	}

	if ($checkout_data) {
		$product_data['smparams'] = $_POST['smparams'];
		$product_data['fmparams'] = $_POST['fmparams'];
		$product_data['product_attributes'] = $checkout_data['product_attributes'];
		$product_data['additional'] = $checkout_data['additional'];
	}

	switch ($product_type) {
		case 'area':
			$product_data['width'] = $_POST['width'];
			$product_data['height'] = $_POST['height'];
		break;
		case 'aec':
			$product_data['project_name'] = $_POST['aec_project_name'];
			$product_data['total_area'] = $_POST['aec_total_area'];
			$product_data['total_pages'] = $_POST['aec_total_pages'];
		break;
		case 'aecbwc':
			$product_data['project_name'] = $_POST['aec_project_name'];
			$product_data['total_area'] = $_POST['aec_total_area'];
			$product_data['total_pages'] = $_POST['aec_total_pages'];
			$product_data['area_bw'] = $_POST['aec_area_bw'];
			$product_data['pages_bw'] = $_POST['aec_pages_bw'];
			$product_data['area_cl'] = $_POST['aec_area_cl'];
			$product_data['pages_cl'] = $_POST['aec_pages_cl'];
		break;
		case 'variable':
			$product_data['attributes'] = $_POST['attributes'];
			$product_data['variation_id'] = (int)$_POST['variation_id'];
		break;
	}

	$order_data = print_products_create_order_get_order_data();
	$order_data['product_data'] = $product_data;
	print_products_create_order_set_order_data($order_data);
}

function print_products_create_order_set_customer_address() {
	$order_data = print_products_create_order_get_order_data();
	$order_data['billing_address'] = $_POST['billing_address'];
	$order_data['shipping_address'] = $_POST['shipping_address'];
	print_products_create_order_set_order_data($order_data);
	
}

function print_products_create_order_product_data_html($product_id, $product_data) {
	global $wpdb;
	$product_type = $product_data['product_type'];
	$dimension_unit = print_products_get_aec_dimension_unit();
	$attribute_labels = (array)get_post_meta($product_id, '_attribute_labels', true);
	$product_attributes = unserialize($product_data['product_attributes']); ?>
	<ul style="margin-bottom:0px;">
		<?php echo '<li>'.__('Quantity', 'wp2print').': <strong>'.$product_data['quantity'].'</strong></li>'; ?>
		<?php if ($product_type == 'area') {
			echo '<li>'.print_products_attribute_label('width', $attribute_labels, __('Width', 'wp2print')).': <strong>'.$product_data['width'].'</strong></li>';
			echo '<li>'.print_products_attribute_label('height', $attribute_labels, __('Height', 'wp2print')).': <strong>'.$product_data['height'].'</strong></li>';
		}
		if ($product_type == 'aec' || $product_type == 'aecbwc') {
			$project_name = $product_data['project_name'];
			if ($project_name) {
				echo '<li>'.__('Project Name', 'wp2print').': <strong>'.$project_name.'</strong></li>';
			}
		}
		if ($product_attributes) {
			$attr_terms = print_products_get_attributes_vals($product_attributes, $product_type, $attribute_labels);
			echo '<li>'.implode('</li><li>', $attr_terms).'</li>';
		}
		if ($product_type == 'aec') {
			$total_area = $product_data['total_area'];
			$total_pages = $product_data['total_pages'];
			if ($total_area) {
				echo '<li>'.__('Total Area', 'wp2print').': <strong>'.number_format($total_area, 2).' '.$dimension_unit.'<sup>2</sup></strong></li>';
			}
			if ($total_pages) {
				echo '<li>'.__('Total Pages', 'wp2print').': <strong>'.$total_pages.'</strong></li>';
			}
		} else if ($product_type == 'aecbwc') {
			$total_area = $product_data['total_area'];
			$total_pages = $product_data['total_pages'];
			$area_bw = $product_data['area_bw'];
			$pages_bw = $product_data['pages_bw'];
			$area_cl = $product_data['area_cl'];
			$pages_cl = $product_data['pages_cl'];
			if ($total_area) {
				echo '<li>'.__('Total Area', 'wp2print').': <strong>'.number_format($total_area, 2).' '.$dimension_unit.'<sup>2</sup></strong></li>';
			}
			if ($total_pages) {
				echo '<li>'.__('Total Pages', 'wp2print').': <strong>'.$total_pages.'</strong></li>';
			}
			if ($area_bw) {
				echo '<li>'.__('Area B/W', 'wp2print').': <strong>'.number_format($area_bw, 2).' '.$dimension_unit.'<sup>2</sup></strong></li>';
			}
			if ($pages_bw) {
				echo '<li>'.__('Pages B/W', 'wp2print').': <strong>'.$pages_bw.'</strong></li>';
			}
			if ($area_cl) {
				echo '<li>'.__('Area Color', 'wp2print').': <strong>'.number_format($area_cl, 2).' '.$dimension_unit.'<sup>2</sup></strong></li>';
			}
			if ($pages_cl) {
				echo '<li>'.__('Pages Color', 'wp2print').': <strong>'.$pages_cl.'</strong></li>';
			}
		} else if ($product_type == 'variable') {
			$attribute_names = array();
			$wc_attributes = $wpdb->get_results(sprintf("SELECT * FROM %swoocommerce_attribute_taxonomies ORDER BY attribute_order, attribute_label", $wpdb->prefix));
			if ($wc_attributes) {
				foreach($wc_attributes as $wc_attribute) {
					$attribute_names[$wc_attribute->attribute_name] = $wc_attribute->attribute_label;
				}
			}

			$variation_id = $product_data['variation_id'];
			$product = wc_get_product($variation_id);
			$attributes = $product->get_attributes();
			if ($attributes) {
				foreach($attributes as $akey => $aval) {
					$aname = str_replace('pa_', '', $akey);
					echo '<li>'.$attribute_names[$aname].': <strong>'.$product->get_attribute($akey).'</strong></li>';
				}
			}
		}
		?>
	</ul>
	<?php
}

function print_products_create_order_get_address_html($address) {
	$address_lines = array();
	if (strlen($address['company'])) {
		$address_lines[] = $address['company'];
	}
	if (strlen($address['address_1'])) {
		$address_lines[] = $address['address_1'];
	}
	if (strlen($address['address_2'])) {
		$address_lines[] = $address['address_2'];
	}
	$address_lines[] = $address['city'].', '.$address['state'].' '.$address['postcode'].', '.$address['country'];
	if (strlen($address['email'])) {
		$address_lines[] = $address['email'];
	}
	if (strlen($address['phone'])) {
		$address_lines[] = $address['phone'];
	}
	return implode('<br>', $address_lines);
}

function print_products_create_order_get_customer_address($user_id, $atype) {
	$address = false;
	if ($atype == 'billing') {
		$billing_company = get_user_meta($user_id, 'billing_company', true);
		if ($billing_company) {
			$address = array(
				'country' => get_user_meta($user_id, 'billing_country', true),
				'address_1' => get_user_meta($user_id, 'billing_address_1', true),
				'address_2' => get_user_meta($user_id, 'billing_address_2', true),
				'city' => get_user_meta($user_id, 'billing_city', true),
				'state' => get_user_meta($user_id, 'billing_state', true),
				'postcode' => get_user_meta($user_id, 'billing_postcode', true),
				'company' => get_user_meta($user_id, 'billing_company', true),
				'email' => get_user_meta($user_id, 'billing_email', true),
				'phone' => get_user_meta($user_id, 'billing_phone', true)
			);
		}
	} else {
		$shipping_company = get_user_meta($user_id, 'shipping_company', true);
		if ($shipping_company) {
			$address = array(
				'country' => get_user_meta($user_id, 'shipping_country', true),
				'address_1' => get_user_meta($user_id, 'shipping_address_1', true),
				'address_2' => get_user_meta($user_id, 'shipping_address_2', true),
				'city' => get_user_meta($user_id, 'shipping_city', true),
				'state' => get_user_meta($user_id, 'shipping_state', true),
				'postcode' => get_user_meta($user_id, 'shipping_postcode', true),
				'company' => get_user_meta($user_id, 'shipping_company', true)
			);
		}
	}
	return $address;
}

function print_products_create_order_save_order() {
	global $wpdb;
	$order_data = print_products_create_order_get_order_data();
	$customer_id = (int)$order_data['customer'];
	$product_id = (int)$order_data['product'];
	$product_data = $order_data['product_data'];

	$product_type = $product_data['product_type'];
	$quantity = (int)$product_data['quantity'];
	$price = (float)$product_data['price'];
	$tax = (float)$product_data['tax'];
	$tax_rate_id = $product_data['tax_rate_id'];
	$shipping = (float)$product_data['shipping'];
	$shipping_tax = (float)$product_data['shipping_tax'];
	$total = (float)$product_data['total'];

	$customer_data = get_userdata($customer_id);

	if ($product_type == 'variable') { $product_id = (int)$product_data['variation_id']; }

	$billing_address = array(
       'first_name' => $customer_data->first_name,
       'last_name'  => $customer_data->last_name,
       'company'    => $order_data['billing_address']['company'],
       'email'      => $order_data['billing_address']['email'],
       'phone'      => $order_data['billing_address']['phone'],
       'address_1'  => $order_data['billing_address']['address_1'],
       'address_2'  => $order_data['billing_address']['address_2'],
       'city'       => $order_data['billing_address']['city'],
       'state'      => $order_data['billing_address']['state'],
       'postcode'   => $order_data['billing_address']['postcode'],
       'country'    => $order_data['billing_address']['country']
	);
	$shipping_address = array(
       'first_name' => $customer_data->first_name,
       'last_name' => $customer_data->last_name,
       'company'    => $order_data['shipping_address']['company'],
       'address_1'  => $order_data['shipping_address']['address_1'],
       'address_2'  => $order_data['shipping_address']['address_2'],
       'city'       => $order_data['shipping_address']['city'],
       'state'      => $order_data['shipping_address']['state'],
       'postcode'   => $order_data['shipping_address']['postcode'],
       'country'    => $order_data['shipping_address']['country']
	);

	$order = wc_create_order(array('customer_id' => $customer_id));
	$order->set_address($billing_address, 'billing');
	$order->set_address($shipping_address, 'shipping');
	$order_item_id = $order->add_product(get_product($product_id), $quantity, array('totals' => array('tax' => $tax)));
	$order->calculate_totals();
	$order->set_total($total);

	$order_id = $order->get_id();

	wp_update_post(array('ID' => $order_id, 'post_status' => 'wc-on-hold'));

	update_post_meta($order_id, '_order_total', $total);
	update_post_meta($order_id, '_order_tax', $tax);
	update_post_meta($order_id, '_order_shipping', $shipping);

	print_products_create_order_set_item_meta($order_id, 'line_item', $price);
	print_products_create_order_set_item_meta($order_id, 'tax', $tax, $shipping_tax);
	print_products_create_order_set_item_meta($order_id, 'shipping', $shipping);

	print_products_create_order_set_tax_item_meta('tax', $order_id, $tax_rate_id, $tax);
	print_products_create_order_set_tax_item_meta('shipping_tax', $order_id, $tax_rate_id, $shipping_tax);

	// add record to print_products_order_items
	if ($product_data['product_attributes']) {
		$insert = array();
		$insert['item_id'] = $order_item_id;
		$insert['product_id'] = $product_id;
		$insert['product_type'] = $product_type;
		$insert['quantity'] = $quantity;
		$insert['price'] = $price;
		$insert['product_attributes'] = $product_data['product_attributes'];
		$insert['additional'] = $product_data['additional'];
		$wpdb->insert($wpdb->prefix."print_products_order_items", $insert);
	}

	unset($_SESSION['create_order_data']);

	wp_redirect('admin.php?page=print-products-create-order&step=completed&order='.$order_id);
	exit;
}

function print_products_create_order_get_order_item_attributes($item_id) {
	global $wpdb;
	$order_item_data = $wpdb->get_row(sprintf("SELECT * FROM %sprint_products_order_items WHERE item_id = '%s'", $wpdb->prefix, $item_id));
	if ($order_item_data) {
		print_products_product_attributes_list_html($order_item_data);
	}
}

function print_products_create_order_set_item_meta($order_id, $item_type, $value, $stax) {
	global $wpdb;

	if ($item_type == 'shipping') {
		$order_item_id = wc_add_order_item($order_id, array('order_item_name' => __('Shipping', 'wp2print'), 'order_item_type' => 'shipping'));
	} else {
		$order_item_id = $wpdb->get_var(sprintf("SELECT order_item_id FROM %swoocommerce_order_items WHERE order_id = %s AND order_item_type = '%s'", $wpdb->prefix, $order_id, $item_type));
	}
	if ($order_item_id) {
		if ($item_type == 'line_item') {
			wc_update_order_item_meta($order_item_id, '_line_subtotal', $value);
			wc_update_order_item_meta($order_item_id, '_line_total', $value);
		} else if ($item_type == 'shipping') {
			wc_add_order_item_meta($order_item_id, 'method_id', 'flat_rate:1');
			wc_add_order_item_meta($order_item_id, 'cost', $value);
		} else {
			wc_update_order_item_meta($order_item_id, 'tax_amount', $value);
			wc_update_order_item_meta($order_item_id, 'shipping_tax_amount', $stax);
		}
	}
}

function print_products_create_order_set_tax_item_meta($type, $order_id, $tax_rate_id, $tax) {
	global $wpdb;
	if ($type == 'shipping_tax') {
		$shipping_order_item_id = $wpdb->get_var(sprintf("SELECT order_item_id FROM %swoocommerce_order_items WHERE order_id = %s AND order_item_type = 'shipping'", $wpdb->prefix, $order_id));
		if ($shipping_order_item_id) {
			$taxes = array('total' => array($tax_rate_id => (string)$tax));
			wc_add_order_item_meta($shipping_order_item_id, 'total_tax', $tax);
			wc_add_order_item_meta($shipping_order_item_id, 'taxes', $taxes);
		}
	} else {
		$line_item_order_item_id = $wpdb->get_var(sprintf("SELECT order_item_id FROM %swoocommerce_order_items WHERE order_id = %s AND order_item_type = 'line_item'", $wpdb->prefix, $order_id));
		if ($line_item_order_item_id) {
			$line_tax_data = wc_get_order_item_meta($line_item_order_item_id, '_line_tax_data');
			if ($line_tax_data) {
				foreach($line_tax_data as $akey => $aarray) {
					foreach($aarray as $ak => $aval) {
						$aarray[$tax_rate_id] = (string)$tax;
					}
					$line_tax_data[$akey] = $aarray;
				}
			}
			wc_update_order_item_meta($line_item_order_item_id, '_line_tax_data', $line_tax_data);
			wc_update_order_item_meta($line_item_order_item_id, '_line_subtotal_tax', $tax);
			wc_update_order_item_meta($line_item_order_item_id, '_line_tax', $tax);
		}
	}
}

function print_products_create_order_get_order_shipping_tax($order_id) {
	global $wpdb;
	$shipping_order_item_id = $wpdb->get_var(sprintf("SELECT order_item_id FROM %swoocommerce_order_items WHERE order_id = %s AND order_item_type = 'shipping'", $wpdb->prefix, $order_id));
	if ($shipping_order_item_id) {
		return wc_get_order_item_meta($shipping_order_item_id, 'total_tax');
	}
}

function print_products_create_order_totals_box() {
	$tax_rate_id = 1;
	$tax_rate = 0;
	$order_data = print_products_create_order_get_order_data();
	$product_id = (int)$order_data['product'];
	$product_data = $order_data['product_data'];
	$product = wc_get_product($product_id);

	$customer_shipping_address = $order_data['shipping_address'];
	if (strlen($customer_shipping_address['country']) && strlen($customer_shipping_address['state'])) {
		$args = array(
			'country' => $customer_shipping_address['country'],
			'state' => $customer_shipping_address['state'],
			'city' => $customer_shipping_address['city'],
			'postcode' => $customer_shipping_address['postcode']
		);
		$tax_rates = WC_Tax::find_rates($args);
		if ($tax_rates) {
			$tax_rates_keys = array_keys($tax_rates);
			$tax_rate_id = $tax_rates_keys[0];
			$tax_rate = (float)$tax_rates[$tax_rate_id]['rate'];
		}
	}
	?>
	<div class="co-box" style="margin-top:15px;">
		<p class="form-field">
			<label><?php _e('Subtotal', 'wp2print'); ?>: <span class="req">*</span></label>
			<input type="text" name="price" class="p-price" value="<?php if ($product_data['price']) { echo $product_data['price']; } else { echo $product->get_price(); } ?>" data-price="<?php echo $product->get_price(); ?>" onblur="matrix_set_tax(); matrix_set_prices();">
		</p>
		<p class="form-field">
			<label><?php _e('Tax', 'wp2print'); ?>:</label>
			<input type="text" name="tax" class="tax-price" value="<?php if ($product_data['tax']) { echo $product_data['tax']; } else { echo '0.00'; } ?>" data-rate="<?php echo $tax_rate; ?>" onblur="matrix_set_prices()">
			<input type="hidden" name="tax_rate_id" value="<?php echo $tax_rate_id; ?>">
		</p>
		<p class="form-field">
			<label><?php _e('Shipping', 'wp2print'); ?>:</label>
			<input type="text" name="shipping" class="shipping-price" value="<?php if ($product_data['shipping']) { echo $product_data['shipping']; } else { echo '0.00'; } ?>" onblur="matrix_set_shipping_tax(); matrix_set_prices();">
		</p>
		<p class="form-field">
			<label><?php _e('Shipping Tax', 'wp2print'); ?>:</label>
			<input type="text" name="shipping_tax" class="shipping-tax-price" value="<?php if ($product_data['shipping_tax']) { echo $product_data['shipping_tax']; } else { echo '0.00'; } ?>" onblur="matrix_set_prices()">
		</p>
		<p class="form-field">
			<label><?php _e('Total', 'wp2print'); ?>: <span class="req">*</span></label>
			<input type="text" name="total" class="total-price" value="<?php if ($product_data['total']) { echo $product_data['total']; } ?>">
		</p>
	</div>
	<?php
}
?>