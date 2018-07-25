<?php
add_filter('woocommerce_account_menu_items', 'print_products_orders_missing_files_account_menu_items');
function print_products_orders_missing_files_account_menu_items($items) {
	$new_items = array();
	foreach($items as $ikey => $ival) {
		if ($ikey == 'downloads') {
			$new_items['orders-missing-files'] = __('Orders missing files', 'wp2print');
		}
		$new_items[$ikey] = $ival;
	}
	return $new_items;
}

add_filter('request', 'print_products_orders_missing_files_request_permalink', 0, 1);
function print_products_orders_missing_files_request_permalink($query) {
	if ($query['attachment'] == 'orders-missing-files') {
		unset($query['attachment']);
		$query['pagename'] = print_products_get_myaccount_pagename();
		$query['orders-missing-files'] = __('Orders missing files', 'wp2print');
	}
	return $query;
}

add_action('init', 'print_products_orders_missing_files_rewrite_endpoint');
function print_products_orders_missing_files_rewrite_endpoint() {
	add_rewrite_endpoint('orders-missing-files', EP_PAGES);
}

add_action('woocommerce_account_orders-missing-files_endpoint', 'print_products_orders_missing_files_account_page');
function print_products_orders_missing_files_account_page() {
	include PRINT_PRODUCTS_TEMPLATES_DIR . 'orders-missing-files.php';
}

add_action('wp_loaded', 'print_products_orders_missing_files_actions');
function print_products_orders_missing_files_actions() {
	global $wpdb, $current_user;
	if (isset($_POST['orders_missing_files_submit']) && $_POST['orders_missing_files_submit'] == 'true') {
		$order_id = $_POST['order_id'];
		$artworkfiles = $_POST['artworkfiles'];
		$redirectto = $_POST['redirectto'];
		if ($order_id && $artworkfiles) {
			foreach($artworkfiles as $item_id => $artwork_files) {
				if (strlen($artwork_files)) {
					$update = array();
					$update['artwork_files'] = serialize(explode(';', $artwork_files));
					$wpdb->update($wpdb->prefix.'print_products_order_items', $update, array('item_id' => $item_id));
				}
			}
		}
		$_SESSION['orders_missing_files_message'] = __('Files were successfully saved.', 'wp2print');
		wp_redirect($redirectto);
		exit;
	}
}
?>