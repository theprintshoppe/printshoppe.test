<?php
function personalize_sp_add_saved_project($session_key, $image_url) {
	global $wpdb, $current_user;

	$product_id = (int)$_REQUEST['add-to-cart'];

	$adata = '';
	if (isset($_REQUEST['print_products_checkout_process_action'])) {
		$product_type = $_REQUEST['product_type'];
		switch ($product_type) {
			case 'fixed':
				$ppdata = print_products_checkout_fixed($session_key, false, true);
			break;
			case 'book':
				$ppdata = print_products_checkout_book($session_key, false, true);
			break;
			case 'area':
				$ppdata = print_products_checkout_area($session_key, false, true);
			break;
			case 'aec':
				$ppdata = print_products_checkout_aec($session_key, false, true);
			break;
			case 'aecbwc':
				$ppdata = print_products_checkout_aecbwc($session_key, false, true);
			break;
			case 'simple':
				$ppdata = print_products_checkout_simple($session_key, false, true);
			break;
			case 'variable':
				$ppdata = print_products_checkout_variable($session_key, false, true);
			break;
		}
		$price = $ppdata['price'];

		$adata = array();
		$adata['product_attributes'] = $ppdata['product_attributes'];
		$adata['additional'] = $ppdata['additional'];
		$ex_params = array('add-to-cart', 'quantity', 'add', 'q', 'print_products_checkout_process_action', 'sattribute', 'fattribute', 'save_only');
		foreach($_REQUEST as $pkey => $pval) {
			if (!in_array($pkey, $ex_params)) {
				$adata[$pkey] = $pval;
			}
		}
		$adata = serialize($adata);
	} else {
		$product = wc_get_product($product_id);
		$price = $product->get_price();
	}

	$id = $wpdb->get_var(sprintf("SELECT id FROM %s WHERE session_key = '%s'", SAVED_PROJECTS_TABLE, $session_key));
	if ($id) {
		personalize_sp_update_saved_project($id, $image_url);
	} else {
		$insert = array();
		$insert['user_id'] = (int)$current_user->ID;
		$insert['session_key'] = $session_key;
		$insert['product_id'] = $product_id;
		$insert['variation_id'] = (int)$_REQUEST['variation_id'];
		$insert['quantity'] = (int)$_REQUEST['quantity'];
		$insert['price'] = $price;
		$insert['image_url'] = $image_url;
		$insert['adata'] = $adata;
		$wpdb->insert(SAVED_PROJECTS_TABLE, $insert);
		$id = $wpdb->insert_id;
		personalize_sp_set_cookie_saved_project($id);
	}
}

add_action('wp_loaded', 'personalize_sp_actions_init');
function personalize_sp_actions_init() {
	if (isset($_POST['saved_projects_action']) && $_POST['saved_projects_action'] == 'delete') {
		$saved_project_id = (int)$_POST['saved_project_id'];
		$redirect_to = $_POST['redirect_to'];
		if ($saved_project_id) {
			personalize_sp_delete_saved_project($saved_project_id);
		}
		wp_redirect($redirect_to);
		exit;
	}
}

function personalize_sp_update_saved_project($id, $image_url) {
	global $wpdb;
	$wpdb->update(SAVED_PROJECTS_TABLE, array('image_url' => $image_url), array('id' => $id));
}

function personalize_sp_delete_saved_project($id) {
	global $wpdb;
	$wpdb->query(sprintf("DELETE FROM %s WHERE id = %s", SAVED_PROJECTS_TABLE, $id));
	$saved_projects = personalize_sp_get_cookie_saved_projects();
	if ($saved_projects) {
		$akey = array_search($id, $saved_projects);
		if ($akey) {
			unset($saved_projects[$akey]);
			personalize_sp_save_cookie_saved_projects($saved_projects);
		}
	}
}

function personalize_sp_set_cookie_saved_project($id) {
	$saved_projects = personalize_sp_get_cookie_saved_projects();
	if (!in_array($id, $saved_projects)) {
		$saved_projects[] = $id;
		personalize_sp_save_cookie_saved_projects($saved_projects);
	}
}

function personalize_sp_save_cookie_saved_projects($saved_projects) {
	setcookie('designer-saved-projects', serialize($saved_projects), time() + 2600000, '/'); // expire after month
}

function personalize_sp_get_cookie_saved_projects() {
	$saved_projects = array();
	if (isset($_COOKIE['designer-saved-projects'])) {
		$saved_projects = unserialize($_COOKIE['designer-saved-projects']);
	}
	return $saved_projects;
}

function personalize_sp_get_data($id) {
	global $wpdb;
	return $wpdb->get_row(sprintf("SELECT * FROM %s WHERE id = %s", SAVED_PROJECTS_TABLE, $id));
}

function personalize_sp_reedit() {
    global $personalize_settings, $api_info;
	$id = (int)$_REQUEST['saved_project_id'];
	if ($id) {
		$sp_data = personalize_sp_get_data($id);
		if ($sp_data && $personalize_settings) {
			$product_id = $sp_data->product_id;
			$session_key = $sp_data->session_key;
			$return_url = personalize_sp_get_return_url($sp_data);
			$success_params = personalize_sp_get_success_params($sp_data);

			$apiUrl = $api_info->url;
			$username = $api_info->username;
			$api_key = $api_info->api_key;
			$client = new xmlrpc_client($apiUrl);
			$successUrl = add_query_arg($success_params, $return_url);
			$failUrl = add_query_arg(array('fail' => '1'), $return_url);
			$cancelUrl = add_query_arg(array('cancel' => '1'), $return_url);
			$user_id = get_current_user_id();
			$comment = '"User: "' . $user_id;
			$templateId = get_post_meta($product_id, 'a_product_id', true);
			$function = new xmlrpcmsg('resumePersonalization', array(
				php_xmlrpc_encode($username),
				php_xmlrpc_encode($api_key),
				php_xmlrpc_encode($session_key),
				php_xmlrpc_encode($templateId),
				php_xmlrpc_encode($successUrl),
				php_xmlrpc_encode($failUrl),
				php_xmlrpc_encode($cancelUrl),
				php_xmlrpc_encode($comment),
				$TemplatexML,
			));
			$response = $client->send($function);
			$sessionkey = $response->value()->arrayMem(0)->scalarval();
			$preview_url = $response->value()->arrayMem(1)->scalarval();
			$_SESSION['sessionkey'] = $sessionkey;
			wp_redirect($preview_url);
			exit;
		}
	}
}

function personalize_sp_get_return_url($sp_data) {
	if (is_user_logged_in()) {
		$mapage = personalize_get_myaccount_pagename();
		$return_url = site_url('/'.$mapage.'/designer-saved-projects/');
	} else {
		$return_url = home_url();
		$saved_projects_page = personalize_get_option('saved_projects_page');
		if ($saved_projects_page) {
			$return_url = get_permalink($saved_projects_page);
		}
	}
	return $return_url;
}

function personalize_sp_get_success_params($sp_data) {
	$success_params = array();
	$id = $sp_data->id;
	$product_id = $sp_data->product_id;
	$quantity = $sp_data->quantity;
	$success_params['saved_project_id'] = $id;
	$success_params['add-to-cart'] = $product_id;
	$success_params['product_id'] = $product_id;
	$success_params['add'] = $product_id;
	$success_params['personalize'] = $product_id;
	$success_params['quantity'] = $quantity;
	$success_params['q'] = $quantity;
		
	if (strlen($sp_data->adata)) {
		$adata = unserialize($sp_data->adata);
		$success_params['print_products_checkout_process_action'] = 'add-to-cart';
		foreach($adata as $dkey => $dval) { if ($dkey != 'product_attributes' && $dkey != 'additional') {
			$success_params[$dkey] = $dval;
		}}
	}
	return $success_params;
}

add_filter('woocommerce_account_menu_items', 'personalize_sp_woocommerce_account_menu_items');
function personalize_sp_woocommerce_account_menu_items($items) {
	$new_items = array();
	foreach($items as $ikey => $ival) {
		if ($ikey == 'customer-logout') {
			$new_items['designer-saved-projects'] = __('Saved projects', 'personalize');
		}
		$new_items[$ikey] = $ival;
	}
	return $new_items;
}

add_filter('request', 'personalize_sp_account_menu_request', 0, 1);
function personalize_sp_account_menu_request($query) {
	if ($query['attachment'] == 'designer-saved-projects') {
		unset($query['attachment']);
		$query['pagename'] = personalize_get_myaccount_pagename();
		$query['designer-saved-projects'] = __('Saved projects', 'personalize');
	}
	return $query;
}

add_action('init', 'personalize_sp_rewrite_endpoint');
function personalize_sp_rewrite_endpoint() {
	add_rewrite_endpoint('designer-saved-projects', EP_PAGES);
}

add_action('woocommerce_account_designer-saved-projects_endpoint', 'personalize_sp_designer_saved_projects');
function personalize_sp_designer_saved_projects() {
	personalize_sp_designer_saved_projects_html();
}

add_shortcode('print-science-designer-saved-projects', 'personalize_sp_designer_saved_projects_shortcode');
function personalize_sp_designer_saved_projects_shortcode($attr) {
	ob_start();
	personalize_sp_designer_saved_projects_html();
	return ob_get_clean();
}

function personalize_sp_designer_saved_projects_html() {
	global $wpdb, $current_user;
	$cookie_saved_projects = personalize_sp_get_cookie_saved_projects();
	if (is_user_logged_in()) {
		$user_id = $current_user->ID;
		if ($cookie_saved_projects) {
			$wpdb->query(sprintf("UPDATE %s SET user_id = %s WHERE id IN (%s)", SAVED_PROJECTS_TABLE, $user_id, implode(',', $cookie_saved_projects)));
		}
		$saved_projects = $wpdb->get_results(sprintf("SELECT * FROM %s WHERE user_id = %s ORDER BY id DESC", SAVED_PROJECTS_TABLE, $user_id));
	} else {
		$saved_projects = $wpdb->get_results(sprintf("SELECT * FROM %s WHERE id IN (%s) ORDER BY id DESC", SAVED_PROJECTS_TABLE, implode(',', $cookie_saved_projects)));
	}
	?>
	<div class="designer-saved-projects woocommerce">
		<table class="shop_table shop_table_responsive">
			<thead>
				<tr>
					<th style="width:30px;">&nbsp;</th>
					<th>&nbsp;</th>
					<th><?php _e('Product', 'personalize'); ?></th>
					<th><?php _e('Qty', 'personalize'); ?></th>
					<th><?php _e('Price', 'personalize'); ?></th>
					<th><?php _e('Actions', 'personalize'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ($saved_projects) { ?>
					<?php foreach($saved_projects as $saved_project) { ?>
					<tr>
						<td data-title="<?php _e('Remove', 'personalize'); ?>"><a href="#delete" class="remove sp-delete" rel="<?php echo $saved_project->id; ?>" data-message="<?php _e('Are you sure?', 'personalize'); ?>">&times;</a></td>
						<td data-title="<?php _e('Design', 'personalize'); ?>">
							<?php if (strlen($saved_project->image_url)) {
								$image_urls = unserialize($saved_project->image_url);
								foreach($image_urls as $image_url) { ?>
									<a href="<?php echo $image_url; ?>" rel="prettyPhoto" data-rel="prettyPhoto[<?php echo $saved_project->id; ?>]"><img src="<?php echo $image_url; ?>" style="width:70px !important;"></a>
								<?php } ?>
							<?php } ?>
						</td>
						<td data-title="<?php _e('Product', 'personalize'); ?>">
							<a href="<?php echo get_permalink($saved_project->product_id); ?>"><strong><?php echo get_the_title($saved_project->product_id); ?></strong></a>
							<?php personalize_sp_variation_attributes_html($saved_project->variation_id); ?>
							<?php personalize_sp_wp2print_attributes_html($saved_project); ?>
						</td>
						<td data-title="<?php _e('Qty', 'personalize'); ?>"><?php echo $saved_project->quantity; ?></td>
						<td data-title="<?php _e('Price', 'personalize'); ?>"><?php echo wc_price($saved_project->price); ?></td>
						<td data-title="<?php _e('Actions', 'personalize'); ?>">
							<a href="<?php echo site_url('/?spreedit=true&saved_project_id='.$saved_project->id); ?>" class="button alt sp-reedit"><?php _e('Re-edit', 'personalize'); ?></a>&nbsp;&nbsp;<a href="#add-to-cart" class="button alt move-to-cart" rel="<?php echo $saved_project->id; ?>"><?php _e('Move to Cart', 'personalize'); ?></a>
							<form method="POST" class="mtc-form-<?php echo $saved_project->id; ?>" style="display:none;">
							<input type="hidden" name="add-to-cart" value="<?php echo $saved_project->product_id; ?>">
							<input type="hidden" name="product_id" value="<?php echo $saved_project->product_id; ?>">
							<input type="hidden" name="quantity" value="<?php echo $saved_project->quantity; ?>">
							<input type="hidden" name="saved_project_id" value="<?php echo $saved_project->id; ?>">
							<input type="hidden" name="sp_add_to_cart" value="true">
							<?php if (strlen($saved_project->adata)) { $adata = unserialize($saved_project->adata); ?>
								<input type="hidden" name="print_products_checkout_process_action" value="add-to-cart">
								<?php foreach($adata as $dkey => $dval) { if ($dkey != 'product_attributes' && $dkey != 'additional') { ?>
									<input type="hidden" name="<?php echo $dkey; ?>" value="<?php echo $dval; ?>">
								<?php }} ?>
							<?php } ?>
							</form>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="6"><?php _e('No saved projects.', 'personalize'); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<form method="POST" class="sp-delete-form" style="display:none;">
		<input type="hidden" name="saved_projects_action" value="delete">
		<input type="hidden" name="saved_project_id" class="saved-project-id">
		<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
		</form>
	</div>
	<?php
}

function personalize_sp_variation_attributes_html($variation_id) {
	if ($variation_id) {
		$variation_product = wc_get_product($variation_id);
		echo '<div class="v-name">'.wc_get_formatted_variation($variation_product).'</div>';
	}
}

function personalize_sp_wp2print_attributes_html($saved_project) {
	$adata = unserialize($saved_project->adata);
	if (count($adata) && function_exists('print_products_product_attributes_list_html')) {
		$item_data = new stdClass;
		$item_data->product_id = $saved_project->product_id;
		$item_data->product_type = $adata['product_type'];
		$item_data->product_attributes = $adata['product_attributes'];
		$item_data->additional = $adata['additional'];
		print_products_product_attributes_list_html($item_data);
	}
}
?>