<?php
$print_products_cart_data = array();

add_action('wp_loaded', 'print_products_checkout_process_loaded');
function print_products_checkout_process_loaded() {
	print_products_get_cart_data();
	// artwork files upload
	if (isset($_GET['ajaxupload']) && $_GET['ajaxupload'] == 'artwork') {
		if ($_FILES['file']['name']) {
			require_once('./wp-admin/includes/file.php');

			$ufile = wp_handle_upload($_FILES['file'], array('test_form' => false), current_time('mysql'));
			if (!isset($ufile['error'])) {
				echo $ufile['url'];
			}
		}
		if ($_FILES['Filedata']['name']) {
			require_once('./wp-admin/includes/file.php');

			$ufile = wp_handle_upload($_FILES['Filedata'], array('test_form' => false), current_time('mysql'));
			if (!isset($ufile['error'])) {
				echo $ufile['url'];
			}
		}
		exit;
	}

	// update cart action
	if (isset($_REQUEST['print_products_checkout_process_action']) && $_REQUEST['print_products_checkout_process_action'] == 'update-cart') {
		$cart_item_key = $_REQUEST['cart_item_key'];
		switch ($_REQUEST['product_type']) {
			case "fixed":
				print_products_checkout_fixed($cart_item_key, true);
			break;
			case "book":
				print_products_checkout_book($cart_item_key, true);
			break;
			case "area":
				print_products_checkout_area($cart_item_key, true);
			break;
			case "aec":
				print_products_checkout_aec($cart_item_key, true);
			break;
			case "aecbwc":
				print_products_checkout_aecbwc($cart_item_key, true);
			break;
		}

		wp_redirect(wc_get_cart_url());
		exit;
	}

	if (isset($_POST['cart_upload_action']) && $_POST['cart_upload_action'] == 'save') {
		print_products_cart_upload_save();
	}
}

add_action('woocommerce_add_to_cart', 'print_products_add_to_cart', 10, 2);
function print_products_add_to_cart($cart_item_key, $product_id) {
	if (isset($_REQUEST['print_products_checkout_process_action'])) {
		switch ($_REQUEST['print_products_checkout_process_action']) {
			case "add-to-cart":
				$artwork_source = get_post_meta($product_id, '_artwork_source', true);
				switch ($_REQUEST['product_type']) {
					case "fixed":
						print_products_checkout_fixed($cart_item_key);
					break;
					case "book":
						print_products_checkout_book($cart_item_key);
					break;
					case "area":
						print_products_checkout_area($cart_item_key);
					break;
					case "aec":
						print_products_checkout_aec($cart_item_key);
					break;
					case "aecbwc":
						print_products_checkout_aecbwc($cart_item_key);
					break;
					case "simple":
						if (strlen($artwork_source)) {
							print_products_checkout_simple($cart_item_key);
						}
					break;
					case "variable":
						if (strlen($artwork_source)) {
							print_products_checkout_variable($cart_item_key);
						}
					break;
				}
			break;
			case "reorder":
				print_products_reorder_product($cart_item_key);
			break;
		}
	}
}

function print_products_add_cart_data($cartdata) {
	global $wpdb;

	$cart_item_key = $cartdata['cart_item_key'];

	$insert = array();
	$insert['cart_item_key'] = $cart_item_key;
	$insert['product_id'] = $cartdata['product_id'];
	$insert['product_type'] = $cartdata['product_type'];
	$insert['quantity'] = $cartdata['quantity'];
	$insert['price'] = $cartdata['price'];
	$insert['product_attributes'] = $cartdata['product_attributes'];
	$insert['additional'] = $cartdata['additional'];
	$insert['artwork_files'] = $cartdata['artwork_files'];
	$insert['atcaction'] = $cartdata['atcaction'];
	$insert['date_added'] = current_time('mysql');
	$wpdb->insert($wpdb->prefix."print_products_cart_data", $insert);
	print_products_get_cart_data();

	WC()->cart->set_quantity($cart_item_key, $cartdata['quantity']);
}

function print_products_update_cart_data($cartdata) {
	global $wpdb;

	$cart_item_key = $cartdata['cart_item_key'];

	$update = array();
	$update['quantity'] = $cartdata['quantity'];
	$update['price'] = $cartdata['price'];
	$update['product_attributes'] = $cartdata['product_attributes'];
	$update['additional'] = $cartdata['additional'];
	$wpdb->update($wpdb->prefix."print_products_cart_data", $update, array('cart_item_key' => $cart_item_key));
	print_products_get_cart_data();

	WC()->cart->set_quantity($cart_item_key, $cartdata['quantity']);
}

function print_products_get_cart_data() {
	global $wpdb, $print_products_cart_data;
	if (WC()->cart) {
		$cart = WC()->cart->get_cart();
		if ($cart) {
			$cart_item_keys = array();
			foreach ($cart as $cart_item_key => $values) {
				$cart_item_keys[] = $cart_item_key;
			}
			$prod_cart_datas = $wpdb->get_results(sprintf("SELECT * FROM %sprint_products_cart_data WHERE cart_item_key IN ('%s')", $wpdb->prefix, implode("','", $cart_item_keys)));
			if ($prod_cart_datas) {
				foreach($prod_cart_datas as $prod_cart_data) {
					$print_products_cart_data[$prod_cart_data->cart_item_key] = $prod_cart_data;
				}
			}
		}
	}
}

function print_products_checkout_fixed($cart_item_key, $update = false, $onlyreturn = false) {
	global $wpdb, $attribute_types;

	print_products_price_matrix_attr_names_init();

	$sku = '';
	$price = 0;
	$weight = 0;
	$product_id = $_REQUEST['add-to-cart'];
	$quantity = $_REQUEST['quantity'];
	$smparams = $_REQUEST['smparams'];
	$fmparams = $_REQUEST['fmparams'];
	$atcaction = $_REQUEST['atcaction'];
	$artworkfiles = $_REQUEST['artworkfiles'];
	if (!strlen($atcaction)) { $atcaction = 'artwork'; }
	if (!$product_id) { $product_id = $_REQUEST['product_id']; }

	$product_attributes = array();
	if ($smparams) {
		$smattrs = explode(';', $smparams);
		foreach($smattrs as $smattr) {
			$smarray = explode('|', $smattr);
			$mtype_id = $smarray[0];
			$aterms = $smarray[1];
			$number = $smarray[2];

			$nmb_val = $quantity;

			$paterms = print_products_get_matrix_price_aterms($aterms, $attribute_types);

			$nums = print_products_get_matrix_numbers($nmb_val, $mtype_id);
			$smprice = print_products_get_matrix_price($mtype_id, $paterms, $nmb_val, $nums);
			if ($smprice) {
				$price += $smprice;
			}

			$pattributes = array();
			$atarray = explode('-', $aterms);
			foreach($atarray as $attr_term) {
				if (!in_array($attr_term, $product_attributes)) {
					$product_attributes[] = $attr_term;
				}
				$pattributes[] = $attr_term;
			}

			$pweight = print_products_get_total_product_weight($product_id, 'fixed', $quantity, $nmb_val, $pattributes);
			$weight = $weight + $pweight;
			if (!strlen($sku)) {
				$sku = print_products_get_product_sku($mtype_id, $aterms);
			}
		}
	}
	if ($fmparams) {
		$fmattrs = explode(';', $fmparams);
		foreach($fmattrs as $fmattr) {
			$fmarray = explode('|', $fmattr);
			$mtype_id = $fmarray[0];
			$aterms = $fmarray[1];
			$number = $fmarray[2];

			$nmb_val = $quantity;

			$paterms = print_products_get_matrix_price_aterms($aterms, $attribute_types);

			$nums = print_products_get_matrix_numbers($nmb_val, $mtype_id);
			$fmprice = print_products_get_matrix_price($mtype_id, $paterms, $nmb_val, $nums);
			if ($fmprice) {
				$price += $fmprice;
			}

			$atarray = explode('-', $aterms);
			foreach($atarray as $attr_term) {
				if (!in_array($attr_term, $product_attributes)) {
					$product_attributes[] = $attr_term;
				}
			}
		}
	}

	$additional = array('weight' => $weight, 'sku' => $sku);

	$cartdata = array(
		'cart_item_key' => $cart_item_key,
		'product_id' => $product_id,
		'product_type' => 'fixed',
		'quantity' => $quantity,
		'price' => $price,
		'product_attributes' => serialize($product_attributes),
		'additional' => serialize($additional),
		'atcaction' => $atcaction
	);
	if (strlen($artworkfiles)) {
		$cartdata['artwork_files'] = serialize(explode(';', $artworkfiles));
	}
	if ($onlyreturn) {
		return $cartdata;
	}
	if ($update) {
		print_products_update_cart_data($cartdata);
	} else {
		print_products_add_cart_data($cartdata);
	}
}

function print_products_checkout_book($cart_item_key, $update = false, $onlyreturn = false) {
	global $wpdb, $print_products_settings, $attribute_types;

	print_products_price_matrix_attr_names_init();

	$sku = '';
	$price = 0;
	$weight = 0;
	$total_pages = 0;
	$product_id = $_REQUEST['add-to-cart'];
	$quantity = (int)$_REQUEST['quantity'];
	$smparams = $_REQUEST['smparams'];
	$fmparams = $_REQUEST['fmparams'];
	$atcaction = $_REQUEST['atcaction'];
	$artworkfiles = $_REQUEST['artworkfiles'];
	if (!strlen($atcaction)) { $atcaction = 'artwork'; }
	if (!$product_id) { $product_id = $_REQUEST['product_id']; }
	$size_attribute = $print_products_settings['size_attribute'];

	$bqflag = true;
	$product_attributes = array();
	if ($smparams) {
		$smnmb = 0;
		$smattrs = explode(';', $smparams);
		foreach($smattrs as $smattr) {
			$smarray = explode('|', $smattr);
			$mtype_id = $smarray[0];
			$aterms = $smarray[1];
			$number = $smarray[2];
			$number_type = $smarray[3];

			$atit = print_products_get_matrix_title($mtype_id);

			$pqty = $_REQUEST['page_quantity_'.$mtype_id];

			$nmb_val = $pqty;
			if ($number_type == 1) {
				$nmb_val = $pqty * $quantity;
				$bqflag = false;
			}

			$paterms = print_products_get_matrix_price_aterms($aterms, $attribute_types);

			$nums = print_products_get_matrix_numbers($nmb_val, $mtype_id);
			$smprice = print_products_get_matrix_price($mtype_id, $paterms, $nmb_val, $nums);
			if ($smprice) {
				$price += $smprice;
			}

			$total_pages = $total_pages + $nmb_val;

			$product_attributes[] = 'pq|'.$atit.':'.$pqty;

			$pattributes = array();
			$atarray = explode('-', $aterms);
			foreach($atarray as $attr_term) {
				$second_size = false;
				$atar = explode(':', $attr_term);
				if ($smnmb > 0 && $atar[0] == $size_attribute) {
					$second_size = true;
				}
				if (!$second_size) {
					$product_attributes[] = $attr_term;
				}
				$pattributes[] = $attr_term;
			}

			$pweight = print_products_get_total_product_weight($product_id, 'book', $quantity, $nmb_val, $pattributes, $smnmb);
			$weight = $weight + $pweight;
			if (!strlen($sku)) {
				$sku = print_products_get_product_sku($mtype_id, $aterms);
			}
			$smnmb++;
		}
	}
	if ($fmparams) {
		$fmattrs = explode(';', $fmparams);
		foreach($fmattrs as $fmattr) {
			$fmarray = explode('|', $fmattr);
			$mtype_id = $fmarray[0];
			$aterms = $fmarray[1];
			$number = $fmarray[2];
			$number_type = $fmarray[3];

			$nmb_val = $quantity;
			if ($number_type == 1) {
				$nmb_val = $total_pages;
			}

			$paterms = print_products_get_matrix_price_aterms($aterms, $attribute_types);

			$nums = print_products_get_matrix_numbers($nmb_val, $mtype_id);
			$fmprice = print_products_get_matrix_price($mtype_id, $paterms, $nmb_val, $nums);
			if ($fmprice) {
				$price += $fmprice;
			}

			$atarray = explode('-', $aterms);
			foreach($atarray as $attr_term) {
				if (!in_array($attr_term, $product_attributes)) {
					$product_attributes[] = $attr_term;
				}
			}
		}
	}
	if ($bqflag) {
		$price = $price * $quantity;
	}
	$additional = array('total_pages' => $total_pages, 'weight' => $weight, 'sku' => $sku);

	$cartdata = array(
		'cart_item_key' => $cart_item_key,
		'product_id' => $product_id,
		'product_type' => 'book',
		'quantity' => $quantity,
		'price' => $price,
		'product_attributes' => serialize($product_attributes),
		'additional' => serialize($additional),
		'atcaction' => $atcaction
	);
	if (strlen($artworkfiles)) {
		$cartdata['artwork_files'] = serialize(explode(';', $artworkfiles));
	}
	if ($onlyreturn) {
		return $cartdata;
	}
	if ($update) {
		print_products_update_cart_data($cartdata);
	} else {
		print_products_add_cart_data($cartdata);
	}
}

function print_products_checkout_area($cart_item_key, $update = false, $onlyreturn = false) {
	global $wpdb, $attribute_types;

	print_products_price_matrix_attr_names_init();

	$sku = '';
	$price = 0;
	$weight = 0;
	$dimension_unit = print_products_get_dimension_unit();
	$product_id = $_REQUEST['add-to-cart'];
	$quantity = (int)$_REQUEST['quantity'];
	$width = (int)$_REQUEST['width'];
	$height = (int)$_REQUEST['height'];
	$smparams = $_REQUEST['smparams'];
	$fmparams = $_REQUEST['fmparams'];
	$atcaction = $_REQUEST['atcaction'];
	$artworkfiles = $_REQUEST['artworkfiles'];
	if (!strlen($atcaction)) { $atcaction = 'artwork'; }
	if (!$product_id) { $product_id = $_REQUEST['product_id']; }

	if ($dimension_unit == 'in') {
		if ($width > 0) { $width = $width / 12; }
		if ($height > 0) { $height = $height / 12; }
	} else if ($dimension_unit == 'mm') {
		if ($width > 0) { $width = $width / 1000; }
		if ($height > 0) { $height = $height / 1000; }
	} else if ($dimension_unit == 'cm') {
		if ($width > 0) { $width = $width / 100; }
		if ($height > 0) { $height = $height / 100; }
	}

	$product_attributes = array();
	if ($smparams) {
		$smattrs = explode(';', $smparams);
		foreach($smattrs as $smattr) {
			$smarray = explode('|', $smattr);
			$mtype_id = $smarray[0];
			$aterms = $smarray[1];
			$number = $smarray[2];
			$number_type = $smarray[3];

			$nmb_val = $quantity;
			if ($number_type == 2) {
				$nmb_val = $quantity * $width * $height;
			} else if ($number_type == 3) {
				$nmb_val = $quantity * (($width * 2) + ($height * 2));
			} else if ($number_type == 4) {
				$nmb_val = $quantity * ($width * 2);
			}

			$paterms = print_products_get_matrix_price_aterms($aterms, $attribute_types);

			$nums = print_products_get_matrix_numbers($nmb_val, $mtype_id);
			$smprice = print_products_get_matrix_price($mtype_id, $paterms, $nmb_val, $nums);
			if ($smprice) {
				$price += $smprice;
			}

			$pattributes = array();
			$atarray = explode('-', $aterms);
			foreach($atarray as $attr_term) {
				$pattributes[] = $attr_term;
				if (!in_array($attr_term, $product_attributes)) {
					$product_attributes[] = $attr_term;
				}
			}
			$pweight = print_products_get_total_product_weight($product_id, 'area', $quantity, $nmb_val, $pattributes);
			$weight = $weight + $pweight;
			if (!strlen($sku)) {
				$sku = print_products_get_product_sku($mtype_id, $aterms);
			}
		}
	}
	if ($fmparams) {
		$fmattrs = explode(';', $fmparams);
		foreach($fmattrs as $fmattr) {
			$fmarray = explode('|', $fmattr);
			$mtype_id = $fmarray[0];
			$aterms = $fmarray[1];
			$number = $fmarray[2];
			$number_type = $fmarray[3];

			$nmb_val = $quantity;
			if ($number_type == 2) {
				$nmb_val = $quantity * $width * $height;
			} else if ($number_type == 3) {
				$nmb_val = $quantity * (($width * 2) + ($height * 2));
			} else if ($number_type == 4) {
				$nmb_val = $quantity * ($width * 2);
			}

			$paterms = print_products_get_matrix_price_aterms($aterms, $attribute_types);

			$nums = print_products_get_matrix_numbers($nmb_val, $mtype_id);
			$fmprice = print_products_get_matrix_price($mtype_id, $paterms, $nmb_val, $nums);
			if ($fmprice) {
				$price += $fmprice;
			}

			$atarray = explode('-', $aterms);
			foreach($atarray as $attr_term) {
				if (!in_array($attr_term, $product_attributes)) {
					$product_attributes[] = $attr_term;
				}
			}
		}
	}

	$additional = array('width' => $_REQUEST['width'], 'height' => $_REQUEST['height'], 'weight' => $weight, 'sku' => $sku);

	$cartdata = array(
		'cart_item_key' => $cart_item_key,
		'product_id' => $product_id,
		'product_type' => 'area',
		'quantity' => $quantity,
		'price' => $price,
		'product_attributes' => serialize($product_attributes),
		'additional' => serialize($additional),
		'atcaction' => $atcaction
	);
	if (strlen($artworkfiles)) {
		$cartdata['artwork_files'] = serialize(explode(';', $artworkfiles));
	}
	if ($onlyreturn) {
		return $cartdata;
	}
	if ($update) {
		print_products_update_cart_data($cartdata);
	} else {
		print_products_add_cart_data($cartdata);
	}
}

function print_products_checkout_aec($cart_item_key, $update = false, $onlyreturn = false) {
	global $wpdb, $attribute_types;

	print_products_price_matrix_attr_names_init();

	$sku = '';
	$weight = 0;
	$product_id = $_REQUEST['add-to-cart'];
	$quantity = $_REQUEST['quantity'];
	$smparams = $_REQUEST['smparams'];
	$fmparams = $_REQUEST['fmparams'];
	$atcaction = $_REQUEST['atcaction'];
	$artworkfiles = $_REQUEST['artworkfiles'];
	$price = $_REQUEST['aec_total_price'];
	$project_name = $_REQUEST['aec_project_name'];
	$total_area = $_REQUEST['aec_total_area'];
	$total_pages = $_REQUEST['aec_total_pages'];
	$table_values = $_REQUEST['aec_table_values'];
	if (!strlen($atcaction)) { $atcaction = 'artwork'; }
	if (!$product_id) { $product_id = $_REQUEST['product_id']; }

	$product_attributes = array();
	if ($smparams) {
		$smattrs = explode(';', $smparams);
		foreach($smattrs as $smattr) {
			$smarray = explode('|', $smattr);
			$mtype_id = $smarray[0];
			$aterms = $smarray[1];
			$number = $smarray[2];

			$nmb_val = $quantity;

			$pattributes = array();
			$atarray = explode('-', $aterms);
			foreach($atarray as $attr_term) {
				if (!in_array($attr_term, $product_attributes)) {
					$product_attributes[] = $attr_term;
				}
				$pattributes[] = $attr_term;
			}

			$pweight = print_products_get_total_product_weight($product_id, 'aec', $quantity, $nmb_val, $pattributes);
			$weight = $weight + $pweight;
			if (!strlen($sku)) {
				$sku = print_products_get_product_sku($mtype_id, $aterms);
			}
		}
	}
	if ($fmparams) {
		$fmattrs = explode(';', $fmparams);
		foreach($fmattrs as $fmattr) {
			$fmarray = explode('|', $fmattr);
			$mtype_id = $fmarray[0];
			$aterms = $fmarray[1];
			$number = $fmarray[2];

			$nmb_val = $quantity;

			$atarray = explode('-', $aterms);
			foreach($atarray as $attr_term) {
				if (!in_array($attr_term, $product_attributes)) {
					$product_attributes[] = $attr_term;
				}
			}
		}
	}

	$additional = array('weight' => $weight, 'project_name' => $project_name, 'total_area' => round($total_area, 2), 'total_pages' => $total_pages, 'sku' => $sku, 'table_values' => $table_values);

	$cartdata = array(
		'cart_item_key' => $cart_item_key,
		'product_id' => $product_id,
		'product_type' => 'aec',
		'quantity' => $quantity,
		'price' => $price,
		'product_attributes' => serialize($product_attributes),
		'additional' => serialize($additional),
		'atcaction' => $atcaction
	);
	if (strlen($artworkfiles)) {
		$cartdata['artwork_files'] = serialize(explode(';', $artworkfiles));
	}
	if ($onlyreturn) {
		return $cartdata;
	}
	if ($update) {
		print_products_update_cart_data($cartdata);
	} else {
		print_products_add_cart_data($cartdata);
	}
}

function print_products_checkout_aecbwc($cart_item_key, $update = false, $onlyreturn = false) {
	global $wpdb, $attribute_types;

	print_products_price_matrix_attr_names_init();

	$sku = '';
	$weight = 0;
	$product_id = $_REQUEST['add-to-cart'];
	$quantity = $_REQUEST['quantity'];
	$smparams = $_REQUEST['smparams'];
	$fmparams = $_REQUEST['fmparams'];
	$atcaction = $_REQUEST['atcaction'];
	$artworkfiles = $_REQUEST['artworkfiles'];
	$price = $_REQUEST['aec_total_price'];
	$project_name = $_REQUEST['aec_project_name'];
	$total_area = $_REQUEST['aec_total_area'];
	$total_pages = $_REQUEST['aec_total_pages'];
	$aec_area_bw = $_REQUEST['aec_area_bw'];
	$aec_pages_bw = $_REQUEST['aec_pages_bw'];
	$aec_area_cl = $_REQUEST['aec_area_cl'];
	$aec_pages_cl = $_REQUEST['aec_pages_cl'];
	$table_values = $_REQUEST['aec_table_values'];
	if (!strlen($atcaction)) { $atcaction = 'artwork'; }
	if (!$product_id) { $product_id = $_REQUEST['product_id']; }

	$product_attributes = array();
	if ($smparams) {
		$smattrs = explode(';', $smparams);
		foreach($smattrs as $smattr) {
			$smarray = explode('|', $smattr);
			$mtype_id = $smarray[0];
			$aterms = $smarray[1];
			$number = $smarray[2];

			$nmb_val = $quantity;

			$pattributes = array();
			$atarray = explode('-', $aterms);
			foreach($atarray as $attr_term) {
				if (!in_array($attr_term, $product_attributes)) {
					$product_attributes[] = $attr_term;
				}
				$pattributes[] = $attr_term;
			}

			$pweight = print_products_get_total_product_weight($product_id, 'aecbwc', $quantity, $nmb_val, $pattributes);
			$weight = $weight + $pweight;
			if (!strlen($sku)) {
				$sku = print_products_get_product_sku($mtype_id, $aterms);
			}
		}
	}
	if ($fmparams) {
		$fmattrs = explode(';', $fmparams);
		foreach($fmattrs as $fmattr) {
			$fmarray = explode('|', $fmattr);
			$mtype_id = $fmarray[0];
			$aterms = $fmarray[1];
			$number = $fmarray[2];

			$nmb_val = $quantity;

			$atarray = explode('-', $aterms);
			foreach($atarray as $attr_term) {
				if (!in_array($attr_term, $product_attributes)) {
					$product_attributes[] = $attr_term;
				}
			}
		}
	}

	$additional = array('weight' => $weight, 'project_name' => $project_name, 'total_area' => round($total_area, 2), 'total_pages' => $total_pages, 'sku' => $sku, 'area_bw' => $aec_area_bw, 'pages_bw' => $aec_pages_bw, 'area_cl' => $aec_area_cl, 'pages_cl' => $aec_pages_cl, 'table_values' => $table_values);

	$cartdata = array(
		'cart_item_key' => $cart_item_key,
		'product_id' => $product_id,
		'product_type' => 'aecbwc',
		'quantity' => $quantity,
		'price' => $price,
		'product_attributes' => serialize($product_attributes),
		'additional' => serialize($additional),
		'atcaction' => $atcaction
	);
	if (strlen($artworkfiles)) {
		$cartdata['artwork_files'] = serialize(explode(';', $artworkfiles));
	}
	if ($onlyreturn) {
		return $cartdata;
	}
	if ($update) {
		print_products_update_cart_data($cartdata);
	} else {
		print_products_add_cart_data($cartdata);
	}
}

function print_products_checkout_simple($cart_item_key, $update = false, $onlyreturn = false) {
	$product_id = $_REQUEST['add-to-cart'];
	$quantity = $_REQUEST['quantity'];
	$atcaction = $_REQUEST['atcaction'];
	$artworkfiles = $_REQUEST['artworkfiles'];

	if (!$product_id) { $product_id = $_REQUEST['product_id']; }

	$cart_data = WC()->cart->get_cart();
	$cart_item = $cart_data[$cart_item_key];
	$_product = wc_get_product($product_id);
	$price = $_product->get_price();

	$cartdata = array(
		'cart_item_key' => $cart_item_key,
		'product_id' => $product_id,
		'product_type' => 'simple',
		'quantity' => $quantity,
		'price' => $price,
		'atcaction' => $atcaction
	);
	if (strlen($artworkfiles)) {
		$cartdata['artwork_files'] = serialize(explode(';', $artworkfiles));
	}
	if ($onlyreturn) {
		return $cartdata;
	}
	if ($update) {
		print_products_update_cart_data($cartdata);
	} else {
		print_products_add_cart_data($cartdata);
	}
}

function print_products_checkout_variable($cart_item_key, $update = false, $onlyreturn = false) {
	$product_id = $_REQUEST['add-to-cart'];
	$quantity = $_REQUEST['quantity'];
	$atcaction = $_REQUEST['atcaction'];
	$artworkfiles = $_REQUEST['artworkfiles'];
	$variation_id = $_REQUEST['variation_id'];
	$price = get_post_meta($variation_id, '_price', true);
	if (!$product_id) { $product_id = $_REQUEST['product_id']; }

	$cartdata = array(
		'cart_item_key' => $cart_item_key,
		'product_id' => $product_id,
		'product_type' => 'variable',
		'quantity' => $quantity,
		'price' => $price,
		'atcaction' => $atcaction
	);
	if (strlen($artworkfiles)) {
		$cartdata['artwork_files'] = serialize(explode(';', $artworkfiles));
	}
	if ($onlyreturn) {
		return $cartdata;
	}
	if ($update) {
		print_products_update_cart_data($cartdata);
	} else {
		print_products_add_cart_data($cartdata);
	}
}

function print_products_reorder_product($cart_item_key) {
	global $wpdb;
	$item_id = $_REQUEST['reorder_item_id'];
	$order_item_data = $wpdb->get_row(sprintf("SELECT * FROM %sprint_products_order_items WHERE item_id = '%s'", $wpdb->prefix, $item_id));
	if ($order_item_data) {
		$cartdata = array(
			'cart_item_key' => $cart_item_key,
			'product_id' => $order_item_data->product_id,
			'product_type' => $order_item_data->product_type,
			'quantity' => $order_item_data->quantity,
			'price' => $order_item_data->price,
			'product_attributes' => $order_item_data->product_attributes,
			'additional' => $order_item_data->additional,
			'artwork_files' => $order_item_data->artwork_files,
			'atcaction' => $order_item_data->atcaction
		);
		print_products_add_cart_data($cartdata);
	}
}

add_action('woocommerce_add_order_item_meta', 'print_products_add_order_item_meta', 11, 3);
function print_products_add_order_item_meta($item_id, $values, $cart_item_key) {
	global $wpdb;
	$prod_cart_data = $wpdb->get_row(sprintf("SELECT * FROM %sprint_products_cart_data WHERE cart_item_key = '%s'", $wpdb->prefix, $cart_item_key));
	if ($prod_cart_data) {
		$insert = array();
		$insert['item_id'] = $item_id;
		$insert['product_id'] = $prod_cart_data->product_id;
		$insert['product_type'] = $prod_cart_data->product_type;
		$insert['quantity'] = $prod_cart_data->quantity;
		$insert['price'] = $prod_cart_data->price;
		$insert['product_attributes'] = $prod_cart_data->product_attributes;
		$insert['additional'] = $prod_cart_data->additional;
		$insert['artwork_files'] = $prod_cart_data->artwork_files;
		$insert['atcaction'] = $prod_cart_data->atcaction;
		$wpdb->insert($wpdb->prefix."print_products_order_items", $insert);
	}
}

add_action('woocommerce_checkout_update_order_meta', 'print_products_checkout_update_order_meta', 10, 2);
function print_products_checkout_update_order_meta($order_id, $posted) {
	if (strlen($posted['order_comments'])) {
		update_post_meta($order_id, '_order_notes', $posted['order_comments']);
	}
}

add_action('woocommerce_order_status_changed', 'print_products_order_status_changed', 10, 3);
function print_products_order_status_changed($order_id, $old_status, $new_status) {
	global $wpdb;
	if (!is_admin() && $new_status != 'failed') {
		if (WC()->cart->get_cart() && $new_status != 'cancelled') {
			foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
				$wpdb->delete($wpdb->prefix."print_products_cart_data", array('cart_item_key' => $cart_item_key));
			}
		}
	}
}

add_action('woocommerce_new_order', 'print_products_woo_new_order');
function print_products_woo_new_order($order_id) {
	global $wpdb;
	$cart_contents = WC()->cart->cart_contents;
	if ($cart_contents) {
		$upd = false;
		foreach ($cart_contents as $cart_item_key => $values) {
			$prod_cart_data = $wpdb->get_row(sprintf("SELECT * FROM %sprint_products_cart_data WHERE cart_item_key = '%s'", $wpdb->prefix, $cart_item_key));
			if ($prod_cart_data) {
				$price = $prod_cart_data->price / $prod_cart_data->quantity;
				$cart_contents[$cart_item_key]['line_total'] = $price;
				$cart_contents[$cart_item_key]['line_subtotal'] = $price;
				$upd = true;
			}
		}
		if ($upd) {
			WC()->cart->cart_contents = $cart_contents;
		}
	}
}

add_action('woocommerce_after_cart_item_quantity_update', 'print_products_woo_cart_item_quantity_update', 10, 2);
function print_products_woo_cart_item_quantity_update($cart_item_key, $quantity) {
	global $wpdb;
	$prod_cart_data = $wpdb->get_row(sprintf("SELECT * FROM %sprint_products_cart_data WHERE cart_item_key = '%s'", $wpdb->prefix, $cart_item_key));
	if ($prod_cart_data) {
		$wpdb->update($wpdb->prefix.'print_products_cart_data', array('quantity' => $quantity), array('cart_item_key' => $cart_item_key));
	}
}

add_filter('woocommerce_cart_subtotal', 'print_products_cart_subtotal', 10, 3);
function print_products_cart_subtotal($cart_subtotal, $compound, $cart) {
	global $wpdb;
	$exist_matrix_prods = false;
	$new_cart_subtotal = 0;
	if (count($cart->cart_contents)) {
		foreach($cart->cart_contents as $cart_item_key => $values) {
			$prod_cart_data = $wpdb->get_row(sprintf("SELECT * FROM %sprint_products_cart_data WHERE cart_item_key = '%s'", $wpdb->prefix, $cart_item_key));
			if ($prod_cart_data) {
				if (print_products_is_wp2print_type($prod_cart_data->product_type)) {
					$new_cart_subtotal = $new_cart_subtotal + $prod_cart_data->price;
				} else {
					$new_cart_subtotal = $new_cart_subtotal + ($prod_cart_data->price * $prod_cart_data->quantity);
				}
				$exist_matrix_prods = true;
			} else {
				$new_cart_subtotal = $new_cart_subtotal + $values['line_subtotal'];
			}
		}
	}
	if ($exist_matrix_prods) {
		$cart_subtotal = wc_price($new_cart_subtotal);
	}
	return $cart_subtotal;
}

add_action('woocommerce_before_calculate_totals', 'print_products_woo_before_calculate_totals');
function print_products_woo_before_calculate_totals($cart) {
	global $print_products_cart_data;
	$cart_contents = $cart->get_cart();
	foreach ($cart_contents as $cart_item_key => $values) {
		$product = $values['data'];
		$product_type = $product->get_type();
		if (print_products_is_wp2print_type($product_type)) {
			$product_price = $print_products_cart_data[$cart_item_key]->price / $values['quantity'];
			$product_weight = print_products_cart_get_product_weight($cart_item_key);
			$product->set_weight($product_weight);
			$product->set_price($product_price);
			$cart_contents[$cart_item_key]['data'] = $product;
			$cart_contents[$cart_item_key]['data']->weight = $product_weight;
			$cart_contents[$cart_item_key]['data']->price = $product_price;
			if ($_REQUEST['print_products_checkout_process_action'] == 'update-cart' && $print_products_cart_data[$cart_item_key]) {
				$cart_contents[$cart_item_key]['quantity'] = $print_products_cart_data[$cart_item_key]->quantity;
			}
		}
	}
	WC()->cart->cart_contents = $cart_contents;
	if (WC()->version > '2.0.6') {
		WC()->version = '2.0.6';
	}
}

add_action('woocommerce_calculate_totals', 'print_products_woo_calculate_totals', 11);
function print_products_woo_calculate_totals($cart) {
	global $wpdb;
	$cart_contents = WC()->cart->cart_contents;
	if ($cart_contents) {
		$cart_contents_total = 0;
		foreach ($cart_contents as $cart_item_key => $values) {
			$prod_cart_data = $wpdb->get_row(sprintf("SELECT * FROM %sprint_products_cart_data WHERE cart_item_key = '%s'", $wpdb->prefix, $cart_item_key));
			if ($prod_cart_data) {
				if (print_products_is_wp2print_type($prod_cart_data->product_type)) {
					$price = $prod_cart_data->price;
				} else {
					$price = $prod_cart_data->price * $prod_cart_data->quantity;
				}
				$price = $cart->get_discounted_price($values, $price, false);
				$cart_contents[$cart_item_key]['line_total'] = $price;
				$cart_contents[$cart_item_key]['line_subtotal'] = $price;
			} else {
				$price = $values['line_total'];
			}
			$cart_contents_total = $cart_contents_total + $price;
		}
		WC()->cart->cart_contents = $cart_contents;
		WC()->cart->cart_contents_total = $cart_contents_total;
	}
}

add_action('woocommerce_after_calculate_totals', 'print_products_woo_after_calculate_totals');
function print_products_woo_after_calculate_totals($cart) {
	WC()->version = WC_VERSION;
}


add_filter('woocommerce_cart_contents_weight', 'print_products_cart_contents_weight');
function print_products_cart_contents_weight($weight) {
	global $wpdb;
	$cart_contents_weight = 0;
	$cart = WC()->cart->get_cart();
	foreach ($cart as $cart_item_key => $values) {
		$product = $values['data'];
		$product_type = $product->get_type();
		if (print_products_is_wp2print_type($product_type)) {
			$product_weight = print_products_cart_get_product_weight($cart_item_key);
		} else {
			$product_weight = $product->get_weight() * $values['quantity'];
		}
		$cart_contents_weight += $product_weight;
	}
	return $cart_contents_weight;
}

add_filter('woocommerce_cart_shipping_packages', 'print_products_cart_shipping_packages');
function print_products_cart_shipping_packages($packages) {
	global $wpdb;
	if ($packages) {
		foreach($packages as $pkey => $package) {
			foreach($package['contents'] as $cart_item_key => $item) {
				$prod_cart_data = $wpdb->get_row(sprintf("SELECT * FROM %sprint_products_cart_data WHERE cart_item_key = '%s'", $wpdb->prefix, $cart_item_key));
				if ($prod_cart_data) {
					$product_type = $prod_cart_data->product_type;
					if (print_products_is_wp2print_type($product_type)) {
						$packages[$pkey]['contents'][$cart_item_key]['quantity'] = 1;
					}
				}
			}
		}
	}
	return $packages;
}

function print_products_cart_get_product_weight($cart_item_key) {
	global $print_products_cart_data;
	$product_weight = 0;
	if ($print_products_cart_data[$cart_item_key]) {
		$prod_cart_data = $print_products_cart_data[$cart_item_key];
		$additional = unserialize($prod_cart_data->additional);
		$weight = (float)$additional['weight'];
		if ($weight) {
			$product_weight = $weight;
		}
	}
	return $product_weight;
}

function print_products_order_get_item_weight($order_item_id) {
	global $wpdb;
	$weight = 0;
	$order_item_data = $wpdb->get_row(sprintf("SELECT * FROM %sprint_products_order_items WHERE item_id = %s", $wpdb->prefix, (int)$order_item_id));
	if ($order_item_data) {
		$additional = unserialize($order_item_data->additional);
		$weight = (float)$additional['weight'];
	}
	return $weight;
}

function print_products_get_total_product_weight($product_id, $product_type, $quantity, $number, $product_attributes, $pmkey = 0) {
	global $wpdb, $print_products_settings;
	$product_weight = 0;
	$size_attribute = $print_products_settings['size_attribute'];
	$material_attribute = $print_products_settings['material_attribute'];
	$page_count_attribute = $print_products_settings['page_count_attribute'];

	$product_shipping_weights = get_post_meta($product_id, '_product_shipping_weights', true);
	$product_shipping_quantity = get_post_meta($product_id, '_product_shipping_base_quantity', true);
	if ($product_shipping_weights) {
		$product_shipping_weights = unserialize($product_shipping_weights);

		$psize = '';
		$pmaterial = '';
		$ppagecount = '';
		if ($product_attributes) {
			foreach ($product_attributes as $product_attribute) {
				$paarray = explode(':', $product_attribute);
				if ($paarray[0] == $material_attribute && !$pmaterial) {
					$pmaterial = $paarray[1];
				}
				if ($paarray[0] == $page_count_attribute && !$ppagecount) {
					$ppagecount = $paarray[1];
				}
				if ($paarray[0] == $size_attribute && !$psize) {
					$psize = $paarray[1];
				}
			}
		}
		if ($pmaterial) {
			if ($product_type == 'area') {
				$pweight = $product_shipping_weights[$pmaterial];
				if ($pweight) {
					$product_weight = print_products_get_product_weight($product_type, $number, $pweight, $product_shipping_quantity);
				}
			} else if ($product_type == 'book') {
				$product_shipping_quantity = unserialize($product_shipping_quantity);
				if ($psize) {
					$pweight = $product_shipping_weights[$pmkey][$pmaterial][$psize];
					if ($pweight) {
						$pp_product_weight = print_products_get_product_weight($product_type, $number, $pweight, $product_shipping_quantity[$pmkey]);
						$product_weight = $product_weight + $pp_product_weight;
					}
				}
			} else {
				if ($psize) {
					if ($ppagecount) {
						$pweight = $product_shipping_weights[$pmaterial][$psize][$ppagecount];
					} else {
						$pweight = $product_shipping_weights[$pmaterial][$psize];
					}
					if ($pweight) {
						$product_weight = print_products_get_product_weight($product_type, $number, $pweight, $product_shipping_quantity);
					}
				}
			}
		}
	}
	return $product_weight;
}

function print_products_get_product_weight($product_type, $number, $pweight, $pbqty) {
	if ($product_type == 'area') {
		$product_weight = $pweight * $number;
	} else {
		if ($pbqty) {
			$product_weight = ($pweight / $pbqty) * $number;
		} else {
			$product_weight = $pweight * $number;
		}
	}
	return $product_weight;
}

add_action('print_products_cart_product_thumbnail', 'print_products_cart_product_thumbnail_output', 10, 5);
function print_products_cart_product_thumbnail_output($prod_cart_data, $_product, $cart_item, $cart_item_key, $designer_thumb) {
	$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
	$artwork_files = false;
	if ($prod_cart_data) {
		$artwork_files = unserialize($prod_cart_data->artwork_files);
	}
	if ($artwork_files && count($artwork_files)) {
		print_products_artwork_files_html($artwork_files, $prod_cart_data);
	}
	if (!$designer_thumb && !$artwork_files) {
		$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
		if (!$_product->is_visible()) {
			echo $thumbnail;
		} else {
			printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $thumbnail );
		}
	}
	$cart_upload_button = (int)get_post_meta($product_id, '_cart_upload_button', true);
	if ($cart_upload_button) {
		$cart_upload_button_text = get_post_meta($product_id, '_cart_upload_button_text', true);
		if (!strlen($cart_upload_button_text)) { $cart_upload_button_text = __('Upload your database', 'wp2print'); }
		echo '<div class="cart-upload-button-box"><input type="button" value="'.$cart_upload_button_text.'" class="button" onclick="wp2print_cart_upload_button(\''.$cart_item_key.'\');"></div>';
	}
}

add_action('woocommerce_after_cart', 'print_products_woocommerce_after_cart');
function print_products_woocommerce_after_cart() {
	include(PRINT_PRODUCTS_TEMPLATES_DIR . 'cart-upload.php');
}

function print_products_cart_upload_save() {
	global $wpdb;
	$cart_item_key = $_POST['cart_item_key'];
	$artwork_files = $_POST['artwork_files'];
	$redirect_to = $_POST['redirect_to'];
	if (strlen($cart_item_key) && strlen($artwork_files)) {
		$prod_cart_data = $wpdb->get_row(sprintf("SELECT * FROM %sprint_products_cart_data WHERE cart_item_key = '%s'", $wpdb->prefix, $cart_item_key));
		if ($prod_cart_data) {
			$update = array();
			$update['artwork_files'] = serialize(explode(';', $artwork_files));
			$wpdb->update($wpdb->prefix."print_products_cart_data", $update, array('cart_item_key' => $cart_item_key));
			print_products_get_cart_data();
		}
	}
	wp_redirect($redirect_to);
	exit;
}
?>