<?php

define('PRINT_PRODUCTS_PLUGIN_DIR', dirname(__FILE__));
define('PRINT_PRODUCTS_PLUGIN_URL', get_bloginfo('url') .'/wp-content/plugins/wp2print/');

add_filter('wc_get_template', 'ps_print_products_woo_get_template');
function ps_print_products_woo_get_template($located, $template_name) {
	global $product, $woocommerce_disable_product_list_price;
	$print_type = false;
	$product_type = print_products_get_type($product->id);
	if (print_products_is_wp2print_type($product_type)) { $print_type = true; }

	$ptemplate = '';
	switch ($template_name) {
		case 'loop/price.php':
			if ($woocommerce_disable_product_list_price == 'yes') {
				$ptemplate = 'empty.php';
			}
		break;
		case 'single-product/product-image.php':
			if ($product_type == 'aec' || $product_type == 'aecbwc') {
				$ptemplate = 'product-type-aec-upload.php';
			}
		break;
		case 'single-product/price.php':
			if ($print_type) {
				$ptemplate = 'empty.php';
			}
		break;
		case 'single-product/add-to-cart/simple.php':
			$ptemplate = 'simple.php';
			if ($print_type) {
				$ptemplate = 'product-type-'.$product_type.'.php';
			}
		break;
		case 'single-product/add-to-cart/variable.php':
			$ptemplate = 'variable.php';
			if ($print_type) {
				$ptemplate = 'product-type-'.$product_type.'.php';
			}
		break;
		case 'cart/cart.php':
			if (!print_products_designer_installed()) {
				$ptemplate = 'cart.php';
			}
		break;
		case 'checkout/review-order.php':
			$ptemplate = 'review-order.php';
		break;
		case 'order/order-details-item.php':
			$ptemplate = 'order-details-item.php';
		break;
		case 'emails/email-order-items.php':
			$ptemplate = 'email-order-items.php';
		break;
	}
	if (strlen($ptemplate)) {
		$located = PRINT_PRODUCTS_TEMPLATES_DIR.$ptemplate;
	}
	return $located;
}