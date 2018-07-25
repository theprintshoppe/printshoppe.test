<?php
// Send proof functionality
add_action('wp_loaded', 'print_products_orders_proof_actions');
function print_products_orders_proof_actions() {
	global $wpdb, $current_user, $current_user_group;
	if (isset($_POST['orders_proof_action'])) {
		switch ($_POST['orders_proof_action']) {
			case 'send':
				$order_id = $_POST['order_id'];
				$proof_files = $_POST['proof_files'];
				$email_subject = trim($_POST['email_subject']);
				$email_message = trim($_POST['email_message']);

				$myaccount_page_id = get_option('woocommerce_myaccount_page_id');
				if ($order_id) {
					$order = new WC_Order($order_id);
					$user_email = $order->billing_email;

					update_post_meta($order_id, '_approval_status', 'awaiting');
					update_post_meta($order_id, '_proof_files', $proof_files);

					// send email to order user
					$oaa_link = get_permalink($myaccount_page_id).'orders-awaiting-approval/?view='.$order_id;
					$email_message = str_replace('[ORDERS_AWAITING_APPROVAL_LINK]', $oaa_link, $email_message);
					$headers = 'From: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>' . "\r\n";
					wp_mail($user_email, $email_subject, $email_message, $headers);
				}
				wp_redirect('post.php?post='.$order_id.'&action=edit&proofsent=true');
				exit;
			break;
		}
	}
	if (isset($_POST['awaiting_approval_submit']) && $_POST['awaiting_approval_submit'] == 'true') {
		$order_id = $_POST['order_id'];
		$awaiting_approval_action = $_POST['awaiting_approval_action'];
		$order_comments = trim($_POST['order_comments']);
		$redirectto = $_POST['redirectto'];

		$the_order = wc_get_order($order_id);
		$user_info = get_userdata($the_order->user_id);

		if ($awaiting_approval_action == 'approve') {
			update_post_meta($order_id, '_approval_status', 'approved');
			update_post_meta($order_id, '_approval_approved', current_time('mysql'));
			$awaiting_approval_message = __('Order was successfully approved.', 'wp2print');
		} else {
			update_post_meta($order_id, '_approval_status', 'rejected');
			update_post_meta($order_id, '_approval_rejected', current_time('mysql'));
			$awaiting_approval_message = __('Order was successfully rejected.', 'wp2print');

			// send email to customer
			$is_superuser = get_user_meta($current_user->ID, '_superuser_group', true);
			if ($is_superuser) {
				$orders_email_contents = unserialize($current_user_group->orders_email_contents);
				if (!strlen($orders_email_contents['email_subject_order_rejection'])) {
					$orders_email_contents['email_subject_order_rejection'] = 'There is a problem with your order';
				}
				if (!strlen($orders_email_contents['email_message_order_rejection'])) {
					$orders_email_contents['email_message_order_rejection'] = 'We are not able to proceed with your order [ORDERID]. Your order was not approved for production for the following reason:'.chr(10).chr(10).'[COMMENTS]'.chr(10).chr(10).'Please return to the website and place a new order.';
				}

				$subject = $orders_email_contents['email_subject_order_rejection'];
				$message = $orders_email_contents['email_message_order_rejection'];
				$message = str_replace('[ORDERID]', $order_id, $message);
				$message = str_replace('[COMMENTS]', $order_comments, $message);
				$headers = 'From: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>' . "\r\n";

				wp_mail($user_info->user_email, $subject, $message, $headers);
			}
		}

		if (strlen($order_comments)) {
			$data = array(
				'comment_post_ID' => $order_id,
				'comment_author' => $current_user->display_name,
				'comment_author_email' => $current_user->user_email,
				'comment_content' => $order_comments,
				'comment_type' => 'order_note',
				'comment_parent' => 0,
				'user_id' => $current_user->ID,
				'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
				'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
				'comment_date' => current_time('mysql'),
				'comment_approved' => 1,
			);
			$comment_id = wp_insert_comment($data);
			add_comment_meta($comment_id, 'is_customer_note', 1);
		}

		$_SESSION['awaiting_approval_message'] = $awaiting_approval_message;
		if (!print_products_orders_proof_show_menu_item()) {
			unset($_SESSION['awaiting_approval_message']);
			$redirectto = str_replace('orders-awaiting-approval/', '', $redirectto);
		}
		wp_redirect($redirectto);
		exit;
	}
}

add_action('admin_notices', 'print_products_orders_proof_admin_notices');
function print_products_orders_proof_admin_notices() {
	if (isset($_GET['proofsent']) && $_GET['proofsent'] == 'true') { ?>
		<div id="message" class="updated notice notice-success">
			<p><?php _e('Approval order email was successfully sent.', 'wp2print'); ?></p>
		</div>
		<?php
	}
}

function print_products_orders_proof_get_approval_statuses() {
	$statuses = array(
		'awaiting' => __('Awaiting approval', 'wp2print'),
		'approved' => __('Approved for production', 'wp2print'),
		'rejected' => __('Rejected for production', 'wp2print')
	);
	return $statuses;
}

function print_products_orders_proof_show_menu_item() {
	global $wpdb, $current_user;
	$is_superuser = get_user_meta($current_user->ID, '_superuser_group', true);
	if ($is_superuser) {
		return true;
	} else {
		$aa_orders = print_products_orders_proof_get_awaiting_orders(false);
		if ($aa_orders) {
			return true;
		}
	}
	return false;
}

add_filter('woocommerce_account_menu_items', 'print_products_orders_proof_account_menu_items');
function print_products_orders_proof_account_menu_items($items) {
	if (print_products_orders_proof_show_menu_item()) {
		$new_items = array();
		foreach($items as $ikey => $ival) {
			if ($ikey == 'downloads') {
				$new_items['orders-awaiting-approval'] = __('Orders awaiting approval', 'wp2print');
			}
			$new_items[$ikey] = $ival;
		}
		return $new_items;
	}
	return $items;
}

add_filter('request', 'print_products_orders_proof_request_permalink', 0, 1);
function print_products_orders_proof_request_permalink($query) {
	if (print_products_orders_proof_show_menu_item()) {
		if ($query['attachment'] == 'orders-awaiting-approval') {
			unset($query['attachment']);
			$query['pagename'] = print_products_get_myaccount_pagename();
			$query['orders-awaiting-approval'] = __('Orders awaiting approval', 'wp2print');
		}
	}
	return $query;
}

add_action('init', 'print_products_orders_proof_rewrite_endpoint');
function print_products_orders_proof_rewrite_endpoint() {
	add_rewrite_endpoint('orders-awaiting-approval', EP_PAGES);
}

add_action('woocommerce_account_orders-awaiting-approval_endpoint', 'print_products_account_orders_awaiting_approval');
function print_products_account_orders_awaiting_approval() {
	include PRINT_PRODUCTS_TEMPLATES_DIR . 'orders-awaiting-approval.php';
}

function print_products_orders_proof_get_awaiting_orders($is_superuser, $group_users = false) {
	global $wpdb, $current_user;
	$awaiting_orders = false;
	if ($is_superuser) {
		if ($group_users) {
			$awaiting_orders = $wpdb->get_results(sprintf("SELECT p.*, pm.meta_value as user_id FROM %sposts p LEFT JOIN %spostmeta pm ON pm.post_id = p.ID LEFT JOIN %spostmeta pm2 ON pm2.post_id = p.ID WHERE p.post_type = 'shop_order' AND p.post_status != 'trash' AND pm.meta_key = '_customer_user' AND pm.meta_value IN ('%s') AND pm2.meta_key = '_approval_status' AND pm2.meta_value = 'awaiting' ORDER BY p.ID DESC", $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, implode("','", $group_users)));
		}
	} else {
		$awaiting_orders = $wpdb->get_results(sprintf("SELECT p.*, pm.meta_value as user_id FROM %sposts p LEFT JOIN %spostmeta pm ON pm.post_id = p.ID LEFT JOIN %spostmeta pm2 ON pm2.post_id = p.ID WHERE p.post_type = 'shop_order' AND p.post_status != 'trash' AND pm.meta_key = '_customer_user' AND pm.meta_value = '%s' AND pm2.meta_key = '_approval_status' AND pm2.meta_value = 'awaiting' ORDER BY p.ID DESC", $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $current_user->ID));
	}
	return $awaiting_orders;
}

add_action('woocommerce_admin_order_data_after_shipping_address', 'print_products_orders_proof_admin_order_data', 25);
function print_products_orders_proof_admin_order_data($order) {
	$order_id = $order->id;
	$approval_status = get_post_meta($order_id, '_approval_status', true);
	$approval_statuses = print_products_orders_proof_get_approval_statuses();
	?>
	</div></div>
	<div class="clear"></div>
	<div class="order-proof-container">
		<div class="order-proof">
			<ul>
				<li><?php _e('Approval', 'wp2print'); ?>:</li>
				<?php if (strlen($approval_status)) { ?><li><span class="<?php echo $approval_status; ?>" title="<?php echo $approval_statuses[$approval_status]; ?>"></span></li><?php } ?>
				<?php if (!strlen($approval_status) || $approval_status == 'rejected') { ?><li><a href="#send-proof" class="button button-primary order-send-proof" rel="<?php echo $order->id; ?>"><?php _e('Send proof', 'wp2print'); ?></a></li><?php } ?>
			</ul>
	<?php
}


?>