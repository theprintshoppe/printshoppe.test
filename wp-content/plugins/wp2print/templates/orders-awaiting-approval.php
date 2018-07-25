<?php
global $wp, $wpdb, $current_user;

$group_users = array();
$group_users_name = array();
$view_order_id = false;
$allow_modify_pdf = false;

if (isset($_GET['view'])) { $view_order_id = $_GET['view']; }

$is_superuser = get_user_meta($current_user->ID, '_superuser_group', true);

if ($is_superuser) {
	$user_group = get_user_meta($current_user->ID, '_user_group', true);
	$wp_group_users = $wpdb->get_results(sprintf("SELECT user_id FROM %susermeta WHERE meta_key = '_user_group' AND meta_value = '%s'", $wpdb->prefix, $user_group));
	if ($wp_group_users) {
		foreach($wp_group_users as $wp_group_user) {
			$group_users[] = $wp_group_user->user_id;
			$group_users_name[$wp_group_user->user_id] = $wpdb->get_var(sprintf("SELECT display_name FROM %susers WHERE ID = %s", $wpdb->prefix, $wp_group_user->user_id));
		}
	}
	$user_group_data = print_products_users_groups_data($user_group);
	if ($user_group_data) {
		$allow_modify_pdf = $user_group_data->allow_modify_pdf;
	}
} else {
	$group_users_name[$current_user->ID] = $current_user->display_name;
}
// check order owner
if ($view_order_id) {
	$order_customer = get_post_meta($view_order_id, '_customer_user', true);
	if ($is_superuser) {
		if (!in_array($order_customer, $group_users)) {
			$view_order_id = false;
		}
	} else {
		if ($order_customer != $current_user->ID) {
			$view_order_id = false;
		}
	}
}

if ($view_order_id) {
	$the_order = wc_get_order($view_order_id);
	if ($the_order->user_id) {
		$user_info = get_userdata($the_order->user_id);
	}
	$proof_files = get_post_meta($view_order_id, '_proof_files', true);
	?>
	<div class="wrap orders-awaiting-approval-details">
		<?php if (strlen($proof_files)) { $pfiles = explode(';', $proof_files); ?>
			<div class="ma-section">
				<div class="ma-section-head opened" rel="download-proofs-list">
					<strong><?php _e('Download your proofs', 'wp2print'); ?></strong>
					<div class="a-box"></div>
				</div>
				<div class="ma-section-content download-proofs-list">
					<ul>
						<?php foreach($pfiles as $proof_file) { ?>
							<li><a href="<?php echo print_products_get_amazon_file_url($proof_file); ?>" class="button button-primary" target="_blank"><?php _e('Download', 'wp2print'); ?></a> <?php echo basename($proof_file); ?></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		<?php } ?>
		<div class="ma-section">
			<div class="ma-section-head" rel="order-details">
				<strong><?php _e('Order details', 'wp2print'); ?></strong>
				<div class="a-box"></div>
			</div>
			<div class="ma-section-content order-details hidden-section">
				<table width="100%" class="">
					<tr>
						<td colspan="3"><?php _e('Order #', 'wp2print'); ?>: <?php echo $view_order_id; ?></td>
					</tr>
					<tr>
						<td colspan="3"><?php _e('Order Date', 'wp2print'); ?>: <?php echo $the_order->order_date; ?></td>
					</tr>
					<tr>
						<td colspan="3"><?php _e('Payment', 'wp2print'); ?>: <?php echo $the_order->payment_method_title; ?></td>
					</tr>
					<tr>
						<td valign="top" style="width:33%;">
							<strong><?php _e('Customer Details', 'wp2print'); ?>:</strong><br />
							<?php _e('Name', 'wp2print'); ?>: <?php echo $user_info->display_name; ?><br />
							<?php _e('Email', 'wp2print'); ?>: <?php echo $user_info->billing_email; ?><br />
							<?php _e('Phone', 'wp2print'); ?>: <?php echo $user_info->billing_phone; ?><br />
							<?php _e('IP Address', 'wp2print'); ?>: <?php echo get_post_meta($view_order_id, '_customer_ip_address', true); ?>
						</td>
						<?php if ($address = $the_order->get_formatted_billing_address()) { ?>
						<td valign="top" style="width:33%;">
							<strong><?php _e('Billing Details', 'wp2print'); ?>:</strong><br />
							<?php echo $address; ?>
						</td>
						<?php } ?>
						<?php if ($address = $the_order->get_formatted_shipping_address()) { ?>
						<td valign="top" style="width:33%;">
							<strong><?php _e('Shipping Details', 'wp2print'); ?>:</strong><br />
							<?php echo $address; ?>
						</td>
						<?php } ?>
					</tr>
					<tr>
						<td colspan="3"><strong><?php _e('Items Details', 'wp2print'); ?>:</strong></td>
					</tr>
					<tr>
						<td colspan="3">
							<table cellspacing="0" cellpadding="0" width="60%" class="items-table">
								<tr style="background:#F4F4F4;">
									<td><strong><?php _e('Item', 'wp2print'); ?></strong></td>
									<td><strong><?php _e('Cost', 'wp2print'); ?></strong></td>
									<td><strong><?php _e('Qty', 'wp2print'); ?></strong></td>
									<td><strong><?php _e('Total', 'wp2print'); ?></strong></td>
								</tr>
								<?php foreach ( $the_order->get_items() as $item_id => $item ) {
									$order_item_data = $wpdb->get_row(sprintf("SELECT * FROM %sprint_products_order_items WHERE item_id = '%s'", $wpdb->prefix, $item_id)); ?>
									<tr>
										<td><?php echo $item['name']; ?>
										<?php if ($order_item_data) {
											$artwork_files = unserialize($order_item_data->artwork_files);
											print_products_product_attributes_list_html($order_item_data);
											if ($artwork_files) { ?>
												<div class="print-products-area">
													<ul class="product-attributes-list">
														<li><?php _e('Artwork Files', 'wp2print'); ?>:</li>
														<li><ul class="product-artwork-files-list">
															<?php foreach($artwork_files as $artwork_file) {
																echo '<li>'.print_products_artwork_file_html($artwork_file, 'download').'</li>';
															} ?>
														</ul></li>
													</ul>
												</div>
											<?php }
										}
										$designer_image = wc_get_order_item_meta($item_id, '_image_link', true);
										if (strlen($designer_image)) {
											$dimages = explode(',', $designer_image); ?>
											<div class="print-products-area">
												<ul class="product-attributes-list">
													<li><?php _e('Designer File', 'wp2print'); ?>:</li>
													<li>
														<ul class="product-artwork-files-list">
															<?php foreach($dimages as $dimage) { ?>
																<li><a href="<?php echo $dimage; ?>" title="<?php _e('Download', 'wp2print'); ?>"><img src="<?php echo $dimage; ?>" style="width:70px;border:1px solid #C1C1C1;"></a></li>
															<?php } ?>
														</ul>
													</li>
											</div>
											<?php
										}
										$pdf_link = wc_get_order_item_meta($item_id, '_pdf_link', true);
										if (strlen($pdf_link)) { $pdf_links = explode(',', $pdf_link); ?>
											<div class="print-products-area">
												<ul class="product-attributes-list">
													<li><?php _e('PDF File(s)', 'wp2print'); ?>:</li>
													<li>
														<ul class="product-artwork-files-list">
															<?php foreach($pdf_links as $pdf_link) { ?>
																<li><a href="<?php echo $pdf_link; ?>" title="<?php _e('Download', 'wp2print'); ?>" target="_blank"><img src="<?php echo PRINT_PRODUCTS_PLUGIN_URL; ?>images/icon_doc_pdf.png"></a></li>
															<?php } ?>
														</ul>
													</li>
												</ul>
												<?php if ($allow_modify_pdf && function_exists('personalize_reedit_order_item')) {
													$sess_key = wc_get_order_item_meta($item_id, '_edit_session_key', true);
													$prod_id = wc_get_order_item_meta($item_id, '_product_id', true);
													?>
													<a href="<?php echo site_url('/?oaa_reedit=true&oiid='.$item_id.'&skey='.$sess_key.'&pid='.$prod_id.'&rurl='.$_SERVER['REQUEST_URI']); ?>" data-rel="nofollow" class="button"><?php _e('Re-edit', 'wp2print'); ?></a>
												<?php } ?>
											</div>
										<?php } ?>
										</td>
										<td><?php echo wc_price($the_order->get_item_total($item, false, true), array('currency' => $the_order->get_order_currency())); ?></td>
										<td><?php echo $item['qty']; ?></td>
										<td><?php echo wc_price($item['line_total'], array('currency' => $the_order->get_order_currency())); ?></td>
									</tr>
								<?php } ?>
								<tr>
									<td colspan="3" style="border-top:2px solid #F4F4F4;text-align:right;"><strong><?php _e('Subtotal', 'wp2print'); ?>:</strong></td>
									<td style="border-top:2px solid #F4F4F4;"><?php echo wc_price($the_order->get_subtotal(), array('currency' => $the_order->get_order_currency())); ?></td>
								</tr>
								<tr>
									<td colspan="3" style="text-align:right;"><strong><?php _e('Shipping', 'wp2print'); ?>:</strong></td>
									<td><?php echo wc_price($the_order->get_total_shipping(), array('currency' => $the_order->get_order_currency())); ?></td>
								</tr>
								<?php if (wc_tax_enabled()) : ?>
									<?php foreach ($the_order->get_tax_totals() as $code => $tax) : ?>
										<tr>
											<td colspan="3" style="text-align:right;"><strong><?php echo $tax->label; ?>:</strong></td>
											<td><?php echo $tax->formatted_amount; ?></td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
								<tr>
									<td colspan="3" style="text-align:right;"><strong><?php _e('Order Total', 'wp2print'); ?>:</strong></td>
									<td><?php echo $the_order->get_formatted_order_total(); ?></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="ma-section">
		<div class="ma-section-head opened" rel="approving-order">
			<strong><?php _e('Approval of proofs', 'wp2print'); ?></strong>
			<div class="a-box"></div>
		</div>
		<div class="ma-section-content approving-order">
			<form method="POST" name="awaiting_approval_form">
			<table width="100%" class="">
				<tr>
					<td colspan="2"><strong><?php _e('Comments', 'wp2print'); ?>:</strong></td>
				</tr>
				<tr>
					<td colspan="2"><textarea name="order_comments" style="width:100%; height:150px;"></textarea></td>
				</tr>
				<tr>
					<td><input type="submit" value="<?php _e('Approve', 'wp2print'); ?>" class="act-button approve-button" onclick="document.awaiting_approval_form.awaiting_approval_action.value='approve';"></td>
					<td style="text-align:right;"><input type="submit" value="<?php _e('Reject', 'wp2print'); ?>" class="act-button reject-button" onclick="document.awaiting_approval_form.awaiting_approval_action.value='reject';"></td>
				</tr>
			</table>
			<input type="hidden" name="awaiting_approval_submit" value="true">
			<input type="hidden" name="order_id" value="<?php echo $view_order_id; ?>">
			<input type="hidden" name="awaiting_approval_action">
			<input type="hidden" name="redirectto" value="<?php echo get_permalink() . 'orders-awaiting-approval/'; ?>">
			</form>
			</div>
		</div>
	</div>
	<?php
} else {
	$aa_orders = print_products_orders_proof_get_awaiting_orders($is_superuser, $group_users);
	?>
	<div class="wrap orders-awaiting-approval-wrap">
		<?php if (strlen($_SESSION['awaiting_approval_message'])) { ?>
			<div class="notice-success"><p><?php echo $_SESSION['awaiting_approval_message']; ?></p></div>
		<?php unset($_SESSION['awaiting_approval_message']); } ?>
		<table class="woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
			<thead>
				<tr>
					<th scope="col" class="manage-column" style="width:60px;"><?php _e('Order', 'wp2print'); ?></th>
					<th scope="col" class="manage-column"><?php _e('Author', 'wp2print'); ?></th>
					<th scope="col" class="manage-column"><?php _e('Purchased', 'wp2print'); ?></th>
					<th scope="col" class="manage-column"><?php _e('Total', 'wp2print'); ?></th>
					<th scope="col" class="manage-column"><?php _e('Payment', 'wp2print'); ?></th>
					<th scope="col" class="manage-column"><?php _e('Date', 'wp2print'); ?></th>
					<th scope="col" class="manage-column" style="width:40px;"><?php _e('View', 'wp2print'); ?></th>
				</tr>
			</thead>
			<tbody id="the-list">
				<?php if ($aa_orders) {
					foreach($aa_orders as $aa_order) {
						$order_id = $aa_order->ID;
						$the_order = wc_get_order($order_id);
						$item_count = $the_order->get_item_count();
						?>
						<tr>
							<td><a href="?view=<?php echo $order_id; ?>">#<?php echo $order_id; ?></a></td>
							<td><?php echo $group_users_name[$aa_order->user_id]; ?></td>
							<td><?php echo $item_count; ?> <?php if ($item_count > 1) { _e('items', 'wp2print'); } else { _e('item', 'wp2print'); } ?></td>
							<td><?php echo $the_order->get_formatted_order_total(); ?></td>
							<td><?php echo $the_order->payment_method_title; ?></td>
							<td><?php echo date('M j, Y', strtotime($aa_order->post_date)); ?></td>
							<td><a href="?view=<?php echo $order_id; ?>" class="woocommerce-button button view"><?php _e('View', 'wp2print'); ?></a></td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="7"><?php _e('No orders.', 'wp2print'); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
<?php } ?>