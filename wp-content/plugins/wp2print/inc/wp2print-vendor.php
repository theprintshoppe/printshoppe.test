<?php
$print_products_vendor_options = get_option('print_products_vendor_options');

add_action('init', 'print_products_vendor_init');
function print_products_vendor_init() {
	$vendor = get_role('vendor');
	if (!$vendor) {
		$capabilities = array(
			'read' => true,
			'edit_shop_order' => true,
			'read_shop_order' => true,
			'edit_shop_orders' => true,
			'edit_others_shop_orders' => true,
			'read_private_shop_orders' => true
		);
		add_role('vendor', __('Vendor'), $capabilities);
	}
}

add_action('add_meta_boxes', 'print_products_vendor_add_meta_boxes', 11);
function print_products_vendor_add_meta_boxes() {
	if (!print_products_vendor_is_vendor()) {
		$vendors = print_products_vendor_get_vendors();
		if ($vendors) {
			add_meta_box('order-vendors-box', __('Vendor Information', 'wp2print'), 'print_products_vendor_meta_box', 'shop_order', 'side', 'high');
		}
	}
}

function print_products_vendor_get_option($okey) {
	global $print_products_vendor_options;
	return $print_products_vendor_options[$okey];
}

function print_products_vendor_meta_box() {
	global $post;

	$order_id = $post->ID;
	$order_vendor = (int)get_post_meta($order_id, '_order_vendor', true);
	$order_vendor_address = get_post_meta($order_id, '_order_vendor_address', true);
	if (!$order_vendor_address) { $order_vendor_address = 'customer'; }

	$customer_address = print_products_vendor_get_address($order_id, 'customer');
	$vendor_address = print_products_vendor_get_address($order_id, 'vendor');

	$vendors = print_products_vendor_get_vendors();
	if ($vendors) { ?>
		<select name="order_vendor" class="order-vendor">
			<option value="">-- <?php _e('Select Vendor', 'wp2print'); ?> --</option>
			<?php foreach($vendors as $vendor) { ?>
				<option value="<?php echo $vendor->ID; ?>"<?php if ($vendor->ID == $order_vendor) { echo ' SELECTED'; } ?>><?php echo $vendor->display_name; ?> (<?php echo $vendor->user_email; ?>)</option>
			<?php } ?>
		</select>
		<div class="order-vendor-address"<?php if (!$order_vendor) { echo ' style="display:none;"'; } ?>>
			<div class="customer-address">
				<input type="radio" name="order_vendor_address" value="customer" class="ovendor-address"<?php if ($order_vendor_address == 'customer') { echo ' CHECKED'; } ?>><?php _e('Dropship to customer', 'wp2print'); ?>
				<div class="address-line"<?php if ($order_vendor_address != 'customer') { echo ' style="display:none;"'; } ?>><?php echo $customer_address; ?></div>
			</div>
			<?php if (strlen($vendor_address)) { ?>
				<div class="vendor-address">
					<input type="radio" name="order_vendor_address" value="vendor" class="ovendor-address"<?php if ($order_vendor_address == 'vendor') { echo ' CHECKED'; } ?>><?php _e('Ship to printshop', 'wp2print'); ?>
					<div class="address-line"<?php if ($order_vendor_address != 'vendor') { echo ' style="display:none;"'; } ?>><?php echo $vendor_address; ?></div>
				</div>
			<?php } ?>
		</div>
		<?php
	}
}

$order_vendor_email = false;
add_action('save_post', 'print_products_vendor_save_post', 11, 2); 
function print_products_vendor_save_post($post_id, $post){
	global $order_vendor_email;
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return $post_id;
	}
	if ($post->post_type == 'shop_order' && isset($_POST['order_vendor'])) {
		$order_vendor = (int)$_POST['order_vendor'];
		$order_vendor_address = $_POST['order_vendor_address'];

		$old_order_vendor = (int)get_post_meta($post_id, '_order_vendor', true);

		if (!$order_vendor) { $order_vendor_address = 'customer'; }

		update_post_meta($post_id, '_order_vendor', $order_vendor);
		update_post_meta($post_id, '_order_vendor_address', $order_vendor_address);

		// send order email to vendor
		if ($order_vendor && $old_order_vendor != $order_vendor) {
			$order_vendor_email = true;

			$vendor_data = get_userdata($order_vendor);
			$order_data = wc_get_order($post_id);

			$email_subject = print_products_vendor_get_option('email_subject');

			$wcecpo = WC()->mailer()->emails['WC_Email_New_Order'];
			$wcecpo->object = $order_data;

			$wcecpo->find['order-date']   = '{order_date}';
			$wcecpo->find['order-number'] = '{order_number}';

			$wcecpo->replace['order-date']   = date_i18n( wc_date_format(), strtotime( $wcecpo->object->order_date ) );
			$wcecpo->replace['order-number'] = $wcecpo->object->get_order_number();

			if (!strlen($email_subject)) { $email_subject = $wcecpo->get_subject(); }

			$wcecpo->recipient = $vendor_data->user_email;
			//$wcecpo->recipient = 'gavrilenko.ruslan@gmail.com';
			$email_content = $wcecpo->get_content();
			$email_content = print_products_vendor_remove_top_line_text($email_content);
			$email_content = print_products_vendor_remove_prices($email_content);
			$wcecpo->send($wcecpo->get_recipient(), $email_subject, $email_content, $wcecpo->get_headers());
			$order_vendor_email = false;
		}
	}
}

add_filter('woocommerce_email_heading_new_order', 'print_products_vendor_woocommerce_email_heading_new_order');
function print_products_vendor_woocommerce_email_heading_new_order($heading) {
	global $order_vendor_email;
	if ($order_vendor_email) {
		$email_header = print_products_vendor_get_option('email_header');
		if (strlen($email_header)) {
			$heading = $email_header;
		}
	}
	return $heading;
}

function print_products_vendor_remove_top_line_text($content) {
	if ($ppos = strpos($content, '<p>')) {
		$pendpos = strpos($content, '</p>');
		$before_content = substr($content, 0, $ppos + 3);
		$after_content = substr($content, $pendpos);
		$email_top_text = print_products_vendor_get_option('email_top_text');
		$content = $before_content . $email_top_text . $after_content;
	}
	return $content;
}

function print_products_vendor_remove_prices($content) {
	if (strpos($content, '<tfoot>')) {
		$before_tfoot = substr($content, 0, strpos($content, '<tfoot>'));
		$after_tfoot = substr($content, strpos($content, '</tfoot>') + 8);
		$content = $before_tfoot . $after_tfoot;
	}
	$pspanpos = strpos($content, '<span class="woocommerce-Price-amount');
	while ($pspanpos) {
		$before_content = substr($content, 0, $pspanpos);
		$after_content = substr($content, $pspanpos);
		$endtd = strpos($after_content, '</td>');
		$after_content = substr($after_content, $endtd);
		$content = $before_content . $after_content;
		$pspanpos = strpos($content, '<span class="woocommerce-Price-amount');
	}
	return $content;
}

function print_products_vendor_get_address($order_id, $type) {
	if ($type == 'customer') {
		$company = get_post_meta($order_id, '_shipping_company', true);
		$address_1 = get_post_meta($order_id, '_shipping_address_1', true);
		$address_2 = get_post_meta($order_id, '_shipping_address_2', true);
		$city = get_post_meta($order_id, '_shipping_city', true);
		$state = get_post_meta($order_id, '_shipping_state', true);
		$postcode = get_post_meta($order_id, '_shipping_postcode', true);
		$country = get_post_meta($order_id, '_shipping_country', true);
	} else {
		$shipping_address = print_products_vendor_get_option('shipping_address');

		$company = $shipping_address['company'];
		$address_1 = $shipping_address['address_1'];
		$address_2 = $shipping_address['address_2'];
		$city = $shipping_address['city'];
		$state = $shipping_address['state'];
		$postcode = $shipping_address['postcode'];
		$country = $shipping_address['country'];
	}

	$address_line  = $company.'<br>';
	$address_line .= $address_1.'<br>';
	if (strlen($address_2)) { $address_line .= $address_2.'<br>'; }
	$address_line .= $city.', '.$state.' '.$postcode.', '.$country;

	return $address_line;
}

function print_products_vendor_get_vendors() {
	return get_users(array('role' => 'vendor'));
}

function print_products_vendor_is_vendor() {
	global $current_user;
	if (in_array('vendor', $current_user->roles)) {
		return true;
	}
	return false;
}

add_action('pre_get_posts', 'print_products_vendor_pre_get_posts');
function print_products_vendor_pre_get_posts($query) {
	global $current_user;
	if (is_admin() && $query->is_main_query() && print_products_vendor_is_vendor()) {
		$query->set('meta_key', '_order_vendor');
		$query->set('meta_value_num', $current_user->ID);
	}
}

add_filter('admin_body_class', 'print_products_vendor_admin_body_class');
function print_products_vendor_admin_body_class($classes) {
	if (print_products_vendor_is_vendor()) {
		if (strlen($classes)) { $classes .= ' '; }
		$classes .= 'vendor-role-user';
	}
	return $classes;
}

add_action('admin_head', 'print_products_vendor_admin_head');
function print_products_vendor_admin_head() {
	if (print_products_vendor_is_vendor()) {
		?>
		<style>
		body.vendor-role-user #adminmenu #menu-posts-shop_order .wp-submenu li:last-child,
		body.vendor-role-user.post-type-shop_order .page-title-action,
		body.vendor-role-user .order-proof-container,
		body.vendor-role-user .woocommerce-Price-amount,
		body.vendor-role-user .wc-order-totals-items,
		body.vendor-role-user .wc-order-bulk-actions,
		body.vendor-role-user #wpo_wcpdf-data-input-box,
		body.vendor-role-user #wpo_wcpdf-box,
		body.vendor-role-user #order_data a.edit_address { display:none; }
		</style>
		<?php
	}
}

add_action('admin_footer', 'print_products_vendor_admin_footer');
function print_products_vendor_admin_footer() {
	if (print_products_vendor_is_vendor()) {
		?>
		<script>
		jQuery('body.vendor-role-user.post-type-shop_order .page-title-action').remove();
		jQuery('body.vendor-role-user .order-proof-container').remove();
		jQuery('body.vendor-role-user .woocommerce-Price-amount').remove();
		jQuery('body.vendor-role-user .wc-order-totals-items').remove();
		jQuery('body.vendor-role-user .wc-order-bulk-actions').remove();
		jQuery('body.vendor-role-user #wpo_wcpdf-data-input-box').remove();
		jQuery('body.vendor-role-user #wpo_wcpdf-box').remove();
		</script>
		<?php
	}
}

add_filter('woocommerce_order_formatted_billing_address', 'print_products_vendor_order_formatted_billing_address', 11, 2);
function print_products_vendor_order_formatted_billing_address($address, $order) {
	global $current_user, $order_vendor_email;
	$is_vendor_address = false;
	$order_id = $order->get_id();
	$_order_vendor = (int)get_post_meta($order_id, '_order_vendor', true);
	$_order_vendor_address = get_post_meta($order_id, '_order_vendor_address', true);

	$use_billing = (int)print_products_vendor_get_option('use_billing');

	if ($order_vendor_email) {
		if ($_order_vendor && $_order_vendor_address == 'vendor') {
			$is_vendor_address = true;
		}
	} else if (print_products_vendor_is_vendor()) {
		if ($_order_vendor && $_order_vendor_address == 'vendor') {
			$is_vendor_address = true;
		}
	}
	if ($is_vendor_address) {
		unset($address['first_name']);
		unset($address['last_name']);

		if ($use_billing) {
			$billing_address = print_products_vendor_get_option('billing_address');
			$address['company'] = $billing_address['company'];
			$address['address_1'] = $billing_address['address_1'];
			$address['address_2'] = $billing_address['address_2'];
			$address['city'] = $billing_address['city'];
			$address['state'] = $billing_address['state'];
			$address['postcode'] = $billing_address['postcode'];
			$address['country'] = $billing_address['country'];
		}
	}
	return $address;
}

add_filter('woocommerce_order_formatted_shipping_address', 'print_products_vendor_order_formatted_shipping_address', 11, 2);
function print_products_vendor_order_formatted_shipping_address($address, $order) {
	global $current_user, $order_vendor_email;
	$is_vendor_address = false;
	$order_id = $order->get_id();
	$_order_vendor = (int)get_post_meta($order_id, '_order_vendor', true);
	$_order_vendor_address = get_post_meta($order_id, '_order_vendor_address', true);

	if ($order_vendor_email) {
		if ($_order_vendor && $_order_vendor_address == 'vendor') {
			$is_vendor_address = true;
		}
	} else if (print_products_vendor_is_vendor()) {
		if ($_order_vendor && $_order_vendor_address == 'vendor') {
			$is_vendor_address = true;
		}
	}
	if ($is_vendor_address) {
		unset($address['first_name']);
		unset($address['last_name']);

		$shipping_address = print_products_vendor_get_option('shipping_address');
		$address['company'] = $shipping_address['company'];
		$address['address_1'] = $shipping_address['address_1'];
		$address['address_2'] = $shipping_address['address_2'];
		$address['city'] = $shipping_address['city'];
		$address['state'] = $shipping_address['state'];
		$address['postcode'] = $shipping_address['postcode'];
		$address['country'] = $shipping_address['country'];
	}
	return $address;
}
?>