<?php
$step = '1';
if (isset($_GET['step'])) { $step = $_GET['step']; }
$order_data = print_products_create_order_get_order_data();
if ($step != '1' && $step != 'completed' && empty($order_data)) {
	wp_redirect('admin.php?page=print-products-create-order');
	exit;
}
?>
<div class="wrap wp2print-create-order">
	<h2><?php _e('Create Order', 'wp2print'); ?></h2>
	<form method="POST" action="admin.php?page=print-products-create-order&step=<?php echo $step + 1; ?>" class="create-order-form" onsubmit="return create_order_process(<?php echo $step; ?>);" data-error-required="<?php _e('Please fill required field(s).', 'wp2print'); ?>">
		<input type="hidden" name="print_products_create_order_action" value="process">
		<div class="create-order-wrap">
			<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
			<?php //////////////////////////////////////////////// STEP 1 /////////////////////////////////////////////////////////// ?>
			<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
			<?php if ($step == '1') { ?>
				<input type="hidden" name="process_step" value="1">
				<div class="co-step-title"><?php _e('Step', 'wp2print'); ?> 1: <?php _e('Select order customer and product', 'wp2print'); ?></div>
				<?php $wpusers = get_users();
				if ($wpusers) { ?>
					<p class="form-field">
						<label><?php _e('Customer', 'wp2print'); ?>: <span class="req">*</span></label>
						<select name="order_customer" class="order-customer">
							<option value="">-- <?php _e('Select', 'wp2print'); ?> --</option>
							<?php foreach($wpusers as $wpuser) { ?>
								<option value="<?php echo $wpuser->ID; ?>"<?php if ($wpuser->ID == $order_data['customer']) { echo ' SELECTED'; } ?>><?php echo $wpuser->display_name; ?> (<?php echo $wpuser->user_email; ?>)</option>
							<?php } ?>
						</select>
					</p>
				<?php } ?>
				<?php $wooproducts = get_posts(array('post_type' => 'product', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'asc'));
				if ($wooproducts) { ?>
					<p class="form-field">
						<label><?php _e('Product', 'wp2print'); ?>: <span class="req">*</span></label>
						<select name="order_product" class="order-product">
							<option value="">-- <?php _e('Select', 'wp2print'); ?> --</option>
							<?php foreach($wooproducts as $wooproduct) { ?>
								<option value="<?php echo $wooproduct->ID; ?>"<?php if ($wooproduct->ID == $order_data['product']) { echo ' SELECTED'; } ?>><?php echo $wooproduct->post_title; ?></option>
							<?php } ?>
						</select>
					</p>
				<?php } ?>
				<p class="submit"><input type="submit" value="<?php _e('Continue', 'wp2print'); ?>" class="button button-primary"></p>
			<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
			<?php //////////////////////////////////////////////// STEP 2 /////////////////////////////////////////////////////////// ?>
			<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
			<?php } else if ($step == '2') {
				$customer_id = $order_data['customer'];
				if ($order_data['billing_address']) {
					$customer_billing_address = $order_data['billing_address'];
					$customer_shipping_address = $order_data['shipping_address'];
				} else {
					$customer_billing_address = print_products_create_order_get_customer_address($customer_id, 'billing');
					$customer_shipping_address = print_products_create_order_get_customer_address($customer_id, 'shipping');
				}
				$address_fields = array(
					'company' => array(
						'label' => __('Company', 'woocommerce').': <span class="req">*</span>',
						'type'  => 'text'
					),
					'address_1' => array(
						'label' => __('Address 1', 'woocommerce').': <span class="req">*</span>',
						'type'  => 'text'
					),
					'address_2' => array(
						'label' => __('Address 2', 'woocommerce').':',
						'type'  => 'text'
					),
					'city' => array(
						'label' => __('City', 'woocommerce').': <span class="req">*</span>',
						'type'  => 'text'
					),
					'postcode' => array(
						'label' => __('Postcode', 'woocommerce').': <span class="req">*</span>',
						'type'  => 'text'
					),
					'country' => array(
						'label'   => __('Country', 'woocommerce').': <span class="req">*</span>',
						'type'    => 'select',
						'style'   => 'width:95%;',
						'class'   => 'js_field-country select short',
						'options' => array('' => __('Select a country&hellip;', 'woocommerce') ) + WC()->countries->get_shipping_countries()
					),
					'state' => array(
						'label' => __('State', 'woocommerce').': <span class="req">*</span>',
						'class' => 'js_field-state select short',
						'type'  => 'text',
						'style' => 'width:95%;'
					)
				);
				?>
				<input type="hidden" name="process_step" value="2">
				<div class="co-step-title"><?php _e('Step', 'wp2print'); ?> 2: <?php _e('Customer billing and shipping address', 'wp2print'); ?></div>
				<table cellspacing="0" cellpadding="0" width="100%" class="co-addresses">
					<tr>
						<td valign="top" class="co-address">
							<div class="edit_address co-billing-address">
								<label><?php _e('Billing Address', 'wp2print'); ?></label>
								<?php foreach ($address_fields as $key => $field) {
									$field['id'] = 'billing_' . $key;
									$field['name'] = 'billing_address[' . $key . ']';
									$field['value'] = $customer_billing_address[$key];
									if ($field['type'] == 'select') {
										woocommerce_wp_select($field);
									} else {
										woocommerce_wp_text_input($field);
									}
									?>
								<?php } ?>
								<p class="form-field">
									<label><?php _e('Email', 'wp2print'); ?>: <span class="req">*</span></label>
									<input type="text" name="billing_address[email]" value="<?php echo $customer_billing_address['email']; ?>">
								</p>
								<p class="form-field">
									<label><?php _e('Phone', 'wp2print'); ?>: <span class="req">*</span></label>
									<input type="text" name="billing_address[phone]" value="<?php echo $customer_billing_address['phone']; ?>">
								</p>
							</div>
							<a href="#copy" class="copy-billing" onclick="return create_order_copy_billing();"><?php _e('Copy billing address to shipping', 'wp2print'); ?> >></a>
						</td>
						<td width="20">&nbsp;</td>
						<td valign="top" class="co-address">
							<div class="edit_address co-shipping-address">
								<label><?php _e('Shipping Address', 'wp2print'); ?></label>
								<?php foreach ($address_fields as $key => $field) {
									$field['id'] = 'shipping_' . $key;
									$field['name'] = 'shipping_address[' . $key . ']';
									$field['value'] = $customer_shipping_address[$key];
									if ($field['type'] == 'select') {
										woocommerce_wp_select($field);
									} else {
										woocommerce_wp_text_input($field);
									}
									?>
								<?php } ?>
							</div>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="button" value="<?php _e('Back', 'wp2print'); ?>" class="button" onclick="window.location.href='admin.php?page=print-products-create-order&step=<?php echo $step - 1; ?>';">
					<input type="submit" value="<?php _e('Continue', 'wp2print'); ?>" class="button button-primary">
				</p>
			<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
			<?php //////////////////////////////////////////////// STEP 3 /////////////////////////////////////////////////////////// ?>
			<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
			<?php } else if ($step == '3') {
				$product_id = (int)$order_data['product'];
				$product = wc_get_product($product_id);
				$product_type = print_products_get_type($product_id);
				$product_data = $order_data['product_data']; ?>
				<input type="hidden" name="process_step" value="3">
				<div class="co-step-title"><?php _e('Step', 'wp2print'); ?> 3: <?php _e('Select product attributes', 'wp2print'); ?></div>
				<p class="form-field">
					<label><?php _e('Product', 'wp2print'); ?>: <span><?php echo $product->get_name(); ?></span></label>
				</p>
				<?php if (file_exists(PRINT_PRODUCTS_TEMPLATES_DIR . 'admin-create-order-product-'.$product_type.'.php')) {
					include PRINT_PRODUCTS_TEMPLATES_DIR . 'admin-create-order-product-'.$product_type.'.php';
				} ?>
				<p class="submit">
					<input type="button" value="<?php _e('Back', 'wp2print'); ?>" class="button" onclick="window.location.href='admin.php?page=print-products-create-order&step=<?php echo $step - 1; ?>';">
					<input type="submit" value="<?php _e('Continue', 'wp2print'); ?>" class="button button-primary">
				</p>
			<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
			<?php //////////////////////////////////////////////// STEP 4 /////////////////////////////////////////////////////////// ?>
			<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
			<?php } else if ($step == '4') { $customer_data = get_userdata($order_data['customer']); ?>
				<input type="hidden" name="process_step" value="create">
				<div class="co-step-title"><?php _e('Step', 'wp2print'); ?> 4: <?php _e('Order Confirmation', 'wp2print'); ?></div>
				<div class="co-confirmation">
					<table cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td class="co-head"><?php _e('Customer', 'wp2print'); ?>:</td>
							<td class="co-value"><span class="co-edit"><a href="admin.php?page=print-products-create-order&step=1"><?php _e('edit', 'wp2print'); ?></a></span>
							<strong><?php echo $customer_data->display_name; ?> (<?php echo $customer_data->user_email; ?>)</strong></td>
						</tr>
						<tr>
							<td class="co-head"><?php _e('Billing Address', 'wp2print'); ?>:</td>
							<td class="co-value"><span class="co-edit"><a href="admin.php?page=print-products-create-order&step=2"><?php _e('edit', 'wp2print'); ?></a></span>
							<strong><?php echo print_products_create_order_get_address_html($order_data['billing_address']); ?></strong></td>
						</tr>
						<tr>
							<td class="co-head"><?php _e('Shipping Address', 'wp2print'); ?>:</td>
							<td class="co-value"><span class="co-edit"><a href="admin.php?page=print-products-create-order&step=2"><?php _e('edit', 'wp2print'); ?></a></span>
							<strong><?php echo print_products_create_order_get_address_html($order_data['shipping_address']); ?></strong></td>
						</tr>
						<tr>
							<td class="co-head"><?php _e('Product', 'wp2print'); ?>:</td>
							<td class="co-value"><span class="co-edit"><a href="admin.php?page=print-products-create-order&step=1"><?php _e('edit', 'wp2print'); ?></a></span>
							<strong><?php echo get_the_title($order_data['product']); ?></strong><br>
							<span class="co-edit" style="padding-top:15px;"><a href="admin.php?page=print-products-create-order&step=3"><?php _e('edit', 'wp2print'); ?></a></span>
							<?php print_products_create_order_product_data_html($order_data['product_id'], $order_data['product_data']); ?></td>
						</tr>
						<tr>
							<td class="co-head" style="line-height:25px;"><?php _e('Subtotal', 'wp2print'); ?>:<br>
							<?php _e('Tax', 'wp2print'); ?>:<br>
							<?php _e('Shipping', 'wp2print'); ?>:<br>
							<?php _e('Shipping Tax', 'wp2print'); ?>:<br>
							<?php _e('Total', 'wp2print'); ?>:</td>
							<td class="co-value" style="line-height:25px;"><span class="co-edit"><a href="admin.php?page=print-products-create-order&step=3"><?php _e('edit', 'wp2print'); ?></a></span>
							<strong><?php echo wc_price($order_data['product_data']['price']); ?></strong><br>
							<strong><?php echo wc_price($order_data['product_data']['tax']); ?></strong><br>
							<strong><?php echo wc_price($order_data['product_data']['shipping']); ?></strong><br>
							<strong><?php echo wc_price($order_data['product_data']['shipping_tax']); ?></strong><br>
							<strong><?php echo wc_price($order_data['product_data']['total']); ?></strong></td>
						</tr>
					</table>
				</div>
				<p class="submit" style="text-align:center;">
					<input type="submit" value="<?php _e('Create Order', 'wp2print'); ?>" class="button button-primary button-create">
				</p>
			<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
			<?php //////////////////////////////////////////////// STEP COMPLETED /////////////////////////////////////////////////// ?>
			<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
			<?php } else if ($step == 'completed') {
				$order_id = $_GET['order'];
				$order = wc_get_order($order_id);
				$customer_id = $order->get_customer_id();
				$customer_data = get_userdata($customer_id);
				$order_items = $order->get_items('line_item');
				foreach($order_items as $item_id => $product) {
					$product_name = $product->get_name();
					$quantity = $product->get_quantity();
				}
				$shipping_tax = print_products_create_order_get_order_shipping_tax($order_id);
				?>
				<h3><?php _e('Order was successfully created.', 'wp2print'); ?></h3>
				<div class="co-order">
					<ul>
						<li><?php _e('Order ID', 'wp2print'); ?>: <span><a href="post.php?post=<?php echo $order_id; ?>&action=edit"><?php echo $order_id; ?></a></span></li>
						<li><?php _e('Customer', 'wp2print'); ?>: <span><?php echo $customer_data->display_name; ?> (<?php echo $customer_data->user_email; ?>)</span></li>
						<li><?php _e('Billing Address', 'wp2print'); ?>:<br /><span><?php echo print_products_create_order_get_address_html($order->get_address()); ?></span></li>
						<li><?php _e('Shipping Address', 'wp2print'); ?>:<br /><span><?php echo print_products_create_order_get_address_html($order->get_address('shipping')); ?></span></li>
						<li style="line-height:22px;"><?php _e('Product', 'wp2print'); ?>: <span><?php echo $product_name; ?></span><br>
							<?php _e('Quantity', 'wp2print'); ?>: <span><?php echo $quantity; ?></span>
							<?php print_products_create_order_get_order_item_attributes($item_id); ?>
						</span></li>
						<li style="line-height:25px;"><?php _e('Subtotal', 'wp2print'); ?>: <span><?php echo wc_price($order->get_subtotal()); ?></span><br>
						<?php _e('Tax', 'wp2print'); ?>: <span><?php echo wc_price($order->get_total_tax()); ?></span><br>
						<?php _e('Shipping', 'wp2print'); ?>: <span><?php echo wc_price($order->get_shipping_total()); ?></span><br>
						<?php _e('Shipping Tax', 'wp2print'); ?>: <span><?php echo wc_price($shipping_tax); ?></span><br>
						<?php _e('Total', 'wp2print'); ?>: <span><?php echo wc_price($order->get_total()); ?></span></li>
					</ul>
				</div>
			<?php } ?>
			<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
		</div>
	</form>
</div>