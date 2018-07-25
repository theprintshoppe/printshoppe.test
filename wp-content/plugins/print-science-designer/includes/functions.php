<?php
function personalize_get_option($option) {
	global $wpdb;
	$personalize_options = $wpdb->get_row('SELECT * FROM ' . API_INFO_TABLE . ' WHERE id = 1', ARRAY_A);
	if ($personalize_options) {
		return $personalize_options[$option];
	}
}

function personalize_update_option($option, $value) {
	global $wpdb;
	$personalize_options = $wpdb->get_row('SELECT * FROM ' . API_INFO_TABLE . ' WHERE id = 1', ARRAY_A);
	if ($personalize_options) {
		unset($personalize_options['id']);
		$personalize_options[$option] = $value;
		$wpdb->update(API_INFO_TABLE, $personalize_options, array('id' => 1));
	}
}

function personalize_file_source_options($source_options) {
	$source_options['design'] = __('Design online', 'personalize');
	$source_options['both'] = __('Customer uploads file OR Design online', 'personalize');
	return $source_options;
}

/** Change class of add to cart for personalization */
$personalize_window_type = '';
function personalize_get_window_type() {
	global $wpdb, $personalize_window_type;
	if (!strlen($personalize_window_type)) {
		$personalize_window_type = $wpdb->get_var(sprintf("SELECT window_type FROM %sapi_info WHERE id = 1", $wpdb->prefix));
	}
	return $personalize_window_type;
}

function print_designer_get_product_type($product_id) {
	if ($terms = wp_get_object_terms($product_id, 'product_type')) {
		return sanitize_title(current($terms)->slug);
	}
}

function print_designer_is_wp2print_type($product_type) {
	if (in_array($product_type, array('fixed', 'book', 'area', 'aec', 'aecbwc'))) {
		return true;
	}
	return false;
}

function personalize_set_session_key($cart_item_key, $sessionKey) {
	global $wpdb;
	$update = array();
	$update['sessionKey'] = $sessionKey;
	$wpdb->update(CART_DATA_TABLE, $update, array('cart_item_key' => $cart_item_key));
}

function personalize_get_session_key($cart_item_key) {
	global $wpdb;
	return $wpdb->get_var(sprintf("SELECT sessionKey FROM %s WHERE cart_item_key = '%s'", CART_DATA_TABLE, $cart_item_key));
}

function personalize_get_order_item_session_key($order_item_id) {
	global $wpdb;
	return $wpdb->get_var(sprintf("SELECT meta_value FROM %swoocommerce_order_itemmeta WHERE order_item_id = %s AND meta_key = '_edit_session_key'", $wpdb->prefix, $order_item_id));
}

function personalize_update_design_image($cart_item_key) {
	global $wpdb;
	$arr_return = personalize_get_response_from_api($_SESSION['sessionkey']);
	$update = array();
	$update['printImage'] = implode(',', $arr_return['img_urls']);
	$update['sessionKey'] = $_SESSION['sessionkey'];
	$wpdb->update(CART_DATA_TABLE, $update, array('cart_item_key' => $cart_item_key));
}

function personalize_update_order_item_data($item_id) {
	global $wpdb;
	$arr_return = personalize_get_response_from_api($_SESSION['oaasessionkey']);
	$img_urls = implode(',', $arr_return['img_urls']);
	$pdf_urls = implode(',', $arr_return['pdf_urls']);
	wc_update_order_item_meta($item_id, '_image_link', $img_urls);
	wc_update_order_item_meta($item_id, '_pdf_link', $pdf_urls);
}

function close_div() {
    global $wpdb;
    $window_type = personalize_get_window_type();
    if ($window_type != 'New Window') {
        echo "<script>jQuery(document).ready(function($) {closethepopup();});</script>";
    }
}

function serverURL() {
    return site_url();
}

function add_css_to_email() {
    echo '<style type="text/css">
		small { display:none !important;}
		</style>
		';
}

function pz_custom_variation_price_email($itemtable) {
    $itemtable = delete_all_between($itemtable);
    return $itemtable;
}

add_filter('wp_footer', 'personalize_wp_footer');
function personalize_wp_footer() {
    global $wpdb, $api_info;
    $successUrl = serverURL() . $_SERVER['REQUEST_URI'];
    if ($_REQUEST['r'] == 's') {
        $successUrl = remove_query_arg(array('r'), $successUrl);
    }
    if ($_REQUEST['r'] == 'e') {
        $successUrl = remove_query_arg(array('r'), $successUrl);
    }
    if ($_REQUEST['cancel'] == '1') {
        $successUrl = remove_query_arg(array('cancel'), $successUrl);
    }
    if ($_REQUEST['fail'] == '1') {
        $successUrl = remove_query_arg(array('fail'), $successUrl);
        $successUrl = add_query_arg(array('wc_error' => 'API is unable to connect'), $successUrl);
    }
    if (isset($_REQUEST['re_edit'])) {
        $successUrl = remove_query_arg(array('re_edit'), $successUrl);
        $successUrl = remove_query_arg(array('cart_item_key'), $successUrl);
    }
    $image_url = $api_info->image_url;
    $serverURL = serverURL();

    $background_color = "#000000";
	if ($api_info->background_color) {
	    $background_color = $api_info->background_color;
	}
    $opacity = "0.6";
	if ($api_info->opacity) {
	    $opacity = $api_info->opacity;
		if ($opacity > 10) {
			$opacity = $opacity / 100;
		}
	}
    $margin = 12;
	if ($api_info->margin) {
	    $margin = $api_info->margin;
	}

	echo '<style>
		.modalPopLite-mask {
			background-color:#' . $background_color . ' !important;
		}	
		#popup-wrapper
		{
			width:1150px;
			height:600px;
			left:0!important;
			top:30!important;
			background: url('.plugins_url().'/print-science-designer/images/iframe-loader.gif) no-repeat;
			background-position: 50% 50%;
			background-color: #' . $background_color . ' !important;
		}
		.modalPopLite-wrapper
		{
			border:none!important;	
		}
		.modalPopLite-mask {
			opacity:' . $opacity . ' !important;
		}
		#popup_frame{
			border:0px;
		}
		.designer-loading-mask{
			position: fixed;
			z-index: 100;
			left: 0;
			right: 0;
			top: 0;
			bottom: 0;
			background: rgba(0, 0, 0, .6);
			display:none;
		}
		.designer-loading-mask img{
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translateX(-50%) translateY(-50%);
		}

		</style><a id="close-btn" ></a><input type="hidden" name="host" id="host" value="' . serverURL() . '"/><input type="hidden" name="server_url" id="server_url" value="' . $successUrl . '"/><input type="hidden" name="margin" id="margin" value="' . $margin . '"/><div id="popup-wrapper"><iframe id="popup_frame" name="popup_frame" style="width: 1399px; height: 716px;" src=""></iframe></div>';
		echo '<div class="designer-loading-mask"><img src="'.plugins_url().'/print-science-designer/images/iframe-loader.gif"></div>';
}

add_filter('upload_mimes', 'personalize_upload_mimes');
function personalize_upload_mimes($mime_types) {
	$mime_types['json']  = 'application/octet-stream';
	return $mime_types;
}

add_action('admin_notices', 'woocommerce_admin_notice_handler');
function custom_add_to_cart_message() {
    global $woocommerce;
    $return_to = get_permalink(woocommerce_get_page_id('shop'));
    $message = sprintf('<a href="%s" class="button wc-forwards">%s</a> %s', $return_to, __('Continue Shopping', 'woocommerce'), __('Product successfully added to your cart.', 'woocommerce'));
    return $message;
}

add_action('show_user_profile', 'personalize_profile_field');
add_action('edit_user_profile', 'personalize_profile_field');
function personalize_profile_field($user) {
	global $wpdb;
	$user_agent_id = get_user_meta($user->ID, '_user_agent_id', true);
	?>
	<h3><?php _e('User Agent Options', 'wp2print'); ?></h3>
	<table class="form-table">
		<tr>
			<th><label for="twitter"><?php _e('Agent ID', 'wp2print'); ?></label></th>
			<td><input type="text" name="user_agent_id" value="<?php echo $user_agent_id; ?>"></td>
		</tr>
	</table>
	<?php
}

add_action('personal_options_update', 'personalize_save_profile_field');
add_action('edit_user_profile_update', 'personalize_save_profile_field');
function personalize_save_profile_field($user_id) {
	if (!current_user_can('edit_user', $user_id)) { return false; }
	update_usermeta($user_id, '_user_agent_id', $_POST['user_agent_id']);
}

// add PDF Link to xml export for 'WooCommerce Customer/Order XML Export Suite' plugin.
add_filter('wc_customer_order_xml_export_suite_order_line_item', 'personalize_order_xml_export_suite_order_line_item', 11, 3);
function personalize_order_xml_export_suite_order_line_item($item_data, $order, $item) {
	global $wpdb;
	$PDFLink = $wpdb->get_var(sprintf("SELECT oim.meta_value FROM %swoocommerce_order_itemmeta oim LEFT JOIN %swoocommerce_order_items oi ON oi.order_item_id = oim.order_item_id WHERE oi.order_id = %s AND oi.order_item_type = 'line_item' AND oi.order_item_name = '%s' AND oim.meta_key = '_pdf_link'", $wpdb->prefix, $wpdb->prefix, $order->id, $item_data['ItemName']));
	if (strlen($PDFLink)) {
		$item_data['PDFLink'] = $PDFLink;
	}
	$PreviewLink = $wpdb->get_var(sprintf("SELECT oim.meta_value FROM %swoocommerce_order_itemmeta oim LEFT JOIN %swoocommerce_order_items oi ON oi.order_item_id = oim.order_item_id WHERE oi.order_id = %s AND oi.order_item_type = 'line_item' AND oi.order_item_name = '%s' AND oim.meta_key = '_image_link'", $wpdb->prefix, $wpdb->prefix, $order->id, $item_data['ItemName']));
	if (strlen($PreviewLink)) {
		$item_data['PreviewLink'] = $PreviewLink;
	}
	return $item_data;
}

function personalize_get_myaccount_pagename() {
	$myaccount_page_id = (int)wc_get_page_id('myaccount');
	$myaccount_page = get_post($myaccount_page_id);
	if ($myaccount_page) {
		return $myaccount_page->post_name;
	}
}

function personalize_help_icon($fkey) {
	$htexts = array(
		'api_username' => 'Help text for field API Username',
		'api_key' => 'Help text for field API Key',
		'api_url' => 'Help text for field API URL',
		'api_image_url' => 'Help text for field API Image URL',
		'window_type' => 'Help text for field Window type for launch of Designer',
		'background_color' => 'Help text for field Background color of margin surrounding the modal window',
		'modal_opacity' => 'Help text for field Opacity of modal window',
		'modal_width' => 'Help text for field Width of margin surrounding the modal window',
		'show_pdf' => 'Help text for field Show PDF link in order email',
		'saved_projects_page' => 'Help text for field Saved projects page'
	); ?>
	<img src="<?php echo PERSONALIZE_URL; ?>images/help.png" class="help-icon" title="<?php _e($htexts[$fkey], 'personalize'); ?>" width="16" height="16">
	<?php
}
?>