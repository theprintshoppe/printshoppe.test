<?php
global $product, $wpdb, $print_products_settings, $attribute_names, $attribute_types, $attribute_imgs, $wp2print_attribute_images;

$woocommerce_calc_taxes = get_option('woocommerce_calc_taxes');
$woocommerce_prices_include_tax = get_option('woocommerce_prices_include_tax');
$price_display_incl_suffix = get_option('woocommerce_price_display_suffix');
$price_display_excl_suffix = get_option('woocommerce_price_display_excl_suffix');

unset($_SESSION['artworkfiles']);

$product_id = $product->id;

$artwork_source = get_post_meta($product_id, '_artwork_source', true);
$artwork_allow_later = get_post_meta($product_id, '_artwork_allow_later', true);
$artwork_file_count = (int)get_post_meta($product_id, '_artwork_file_count', true);
$artwork_afile_types = get_post_meta($product_id, '_artwork_afile_types', true);
$product_shipping_weights = unserialize(get_post_meta($product_id, '_product_shipping_weights', true));
$product_shipping_base_quantity = (int)get_post_meta($product_id, '_product_shipping_base_quantity', true);
$product_display_weight = get_post_meta($product_id, '_product_display_weight', true);
$product_display_price = get_post_meta($product_id, '_product_display_price', true);
$attribute_labels = (array)get_post_meta($product_id, '_attribute_labels', true);
$attribute_display = (array)get_post_meta($product_id, '_attribute_display', true);

if (!$product_display_price || $woocommerce_calc_taxes != 'yes' || $woocommerce_prices_include_tax == 'yes') { $product_display_price = 'excl'; }

$size_attribute = $print_products_settings['size_attribute'];
$material_attribute = $print_products_settings['material_attribute'];
$page_count_attribute = $print_products_settings['page_count_attribute'];

$attributes = $wpdb->get_results(sprintf("SELECT * FROM %swoocommerce_attribute_taxonomies ORDER BY attribute_order, attribute_label", $wpdb->prefix));
print_products_price_matrix_attr_names_init($attributes);

$anmb = 0;
$is_modify = false;
if (isset($_GET['modify']) && strlen($_GET['modify'])) {
	$cart_item_key = $_GET['modify'];
	$prod_cart_data = $wpdb->get_row(sprintf("SELECT * FROM %sprint_products_cart_data WHERE cart_item_key = '%s'", $wpdb->prefix, $cart_item_key));
	if ($prod_cart_data) {
		$is_modify = true;
		$quantity_val = $prod_cart_data->quantity;
		$product_attributes = unserialize($prod_cart_data->product_attributes);
	}
}

$product_type_matrix_types = $wpdb->get_results(sprintf("SELECT * FROM %sprint_products_matrix_types WHERE product_id = %s ORDER BY mtype, sorder", $wpdb->prefix, $product_id));
if ($product_type_matrix_types) { ?>
	<div class="print-products-area product-attributes" style="margin:0 0 15px 0;">
		<form method="POST" class="add-cart-form" onsubmit="return products_add_cart_action();">
		<?php $sattrex = 0; $mtypecount = array();
		foreach($product_type_matrix_types as $product_type_matrix_type) {
			$mtype_id = $product_type_matrix_type->mtype_id;
			$mtype = $product_type_matrix_type->mtype;
			$mattributes = unserialize($product_type_matrix_type->attributes);
			$materms = unserialize($product_type_matrix_type->aterms);
			$numbers = explode(',', $product_type_matrix_type->numbers);
			$num_style = $product_type_matrix_type->num_style;
			$num_type = $product_type_matrix_type->num_type;

			$mtypecount[$mtype]++;

			if ($mattributes) { ?>
				<?php if ($mtype == 0) { // simple matrix ?>
					<div class="matrix-type-simple" data-mtid="<?php echo $mtype_id; ?>" data-ntp="<?php echo $num_type; ?>">
						<?php if ($numbers) { ?>
							<ul class="product-attributes-list numbers-list">
								<li>
									<label><?php echo print_products_attribute_label('quantity', $attribute_labels, __('Quantity', 'wp2print')); ?>:</label><br />
									<?php if ($num_style == 1) { ?>
										<select name="quantity" class="quantity" onchange="matrix_calculate_price()">
											<?php foreach($numbers as $number) { ?>
												<option value="<?php echo $number; ?>"<?php if ($quantity_val && $quantity_val == $number) { echo ' SELECTED'; } ?>><?php echo $number; ?></option>
											<?php } ?>
										</select>
									<?php } else { ?>
										<input type="text" name="quantity" class="quantity" value="<?php if ($quantity_val) { echo $quantity_val; } else { echo $numbers[0]; } ?>" onblur="matrix_calculate_price()">
									<?php } ?>
									<div class="area-wh-error width-error"></div>
								</li>
							</ul>
						<?php } ?>
						<ul class="product-attributes-list print-attributes">
							<?php foreach($mattributes as $mattribute) {
								$matype = $attribute_types[$mattribute];
								$aterms = $materms[$mattribute];
								$aval = '';
								if ($is_modify) {
									$avals = explode(':', $product_attributes[$anmb]);
									$akey = $avals[0];
									$aval = $avals[1];
									$anmb++;
								}
								if ($matype == 'text') { ?>
									<li class="matrix-attribute-<?php echo $mtype_id; ?>-<?php echo $mattribute; ?>">
										<label><?php echo print_products_attribute_label($mattribute, $attribute_labels, $attribute_names[$mattribute]); ?>:</label><br />
										<div class="attr-box">
											<input type="text" name="sattribute[<?php echo $mattribute; ?>]" class="smatrix-attr smatrix-attr-text" value="<?php echo $aval; ?>" data-aid="<?php echo $mattribute; ?>" onchange="matrix_calculate_price();" onblur="matrix_calculate_price();"><?php print_products_attribute_help_icon($mattribute); ?>
										</div>
									</li>
									<?php
								} else {
									if ($aterms) {
										$aimg = $attribute_imgs[$mattribute];
										$aterms = print_products_get_attribute_terms($aterms);
										$attr_class = '';
										if ($mattribute == $size_attribute) { $attr_class = ' smatrix-size'; }
										if ($mattribute == $material_attribute) { $attr_class = ' smatrix-material'; }
										if ($mattribute == $page_count_attribute) { $attr_class = ' smatrix-pagecount'; }

										$do_not_display = (int)$attribute_display[$mattribute];
										if ($do_not_display) {
											if (!$is_modify) { $aterms_keys = array_keys($aterms); $aval = $aterms_keys[0]; } ?>
											<input type="hidden" name="sattribute[<?php echo $mattribute; ?>]" class="smatrix-attr<?php echo $attr_class; ?>" value="<?php echo $aval; ?>" data-aid="<?php echo $mattribute; ?>">
										<?php } else { ?>
											<li class="matrix-attribute-<?php echo $mtype_id; ?>-<?php echo $mattribute; ?>">
												<label><?php echo print_products_attribute_label($mattribute, $attribute_labels, $attribute_names[$mattribute]); ?>:</label><br />
												<div class="attr-box">
													<select name="sattribute[<?php echo $mattribute; ?>]" class="smatrix-attr<?php echo $attr_class; ?>" data-aid="<?php echo $mattribute; ?>" onchange="matrix_calculate_price();<?php if ($aimg) { ?> matrix_attribute_image(this, <?php echo $mattribute; ?>, <?php echo $mtype_id; ?>);<?php } ?>">
														<?php foreach($aterms as $aterm_id => $aterm_name) { ?>
															<option value="<?php echo $aterm_id; ?>"<?php if ($aval == $aterm_id) { echo ' SELECTED'; } ?>><?php echo $aterm_name; ?></option>
														<?php } ?>
													</select><?php print_products_attribute_help_icon($mattribute); ?>
												</div>
												<?php if ($aimg) { $showai = false; $ainmb = 1;
													foreach($aterms as $aterm_id => $aterm_name) {
														if ($wp2print_attribute_images[$aterm_id]) { $showai = true; }
													}
													if ($showai) { ?>
														<div class="attribute-images attribute-images-<?php echo $mattribute; ?>">
															<ul>
																<?php foreach($aterms as $aterm_id => $aterm_name) {
																	if ($wp2print_attribute_images[$aterm_id]) { ?>
																		<li><img src="<?php echo print_products_get_thumb($wp2print_attribute_images[$aterm_id], 100, 80, true) ?>" class="attribute-image-<?php echo $aterm_id; ?><?php if ($ainmb == 1) { echo ' active'; } ?>" rel="matrix-attribute-<?php echo $mtype_id; ?>-<?php echo $mattribute; ?>"></li>
																	<?php } ?>
																<?php $ainmb++; } ?>
															</ul>
														</div>
													<?php } ?>
												<?php } ?>
											</li>
										<?php } ?>
									<?php } ?>
								<?php } ?>
							<?php } ?>
						</ul>
					</div>
				<?php } else { // finishing matrix ?>
					<div class="matrix-type-finishing" data-mtid="<?php echo $mtype_id; ?>" data-ntp="<?php echo $num_type; ?>">
						<ul class="product-attributes-list finishing-attributes">
						<?php foreach($mattributes as $mattribute) {
							$matype = $attribute_types[$mattribute];
							$aterms = $materms[$mattribute];
							$aval = '';
							if ($is_modify) {
								$avals = explode(':', $product_attributes[$anmb]);
								$akey = $avals[0];
								$aval = $avals[1];
								$anmb++;
							}
							if ($matype == 'text') { ?>
								<li class="matrix-attribute-<?php echo $mtype_id; ?>-<?php echo $mattribute; ?>">
									<label><?php echo print_products_attribute_label($mattribute, $attribute_labels, $attribute_names[$mattribute]); ?>:</label><br />
									<div class="attr-box">
										<input type="text" name="fattribute[<?php echo $mattribute; ?>]" class="fmatrix-attr" value="<?php echo $aval; ?>" data-aid="<?php echo $mattribute; ?>" onchange="matrix_calculate_price();" onblur="matrix_calculate_price();"><?php print_products_attribute_help_icon($mattribute); ?>
									</div>
								</li>
								<?php
							} else {
								if ($aterms) {
									$aimg = $attribute_imgs[$mattribute];
									$aterms = print_products_get_attribute_terms($aterms);
									$do_not_display = (int)$attribute_display[$mattribute];
									if ($do_not_display) {
										if (!$is_modify) { $aterms_keys = array_keys($aterms); $aval = $aterms_keys[0]; } ?>
										<input type="hidden" name="fattribute[<?php echo $mattribute; ?>]" class="fmatrix-attr" value="<?php echo $aval; ?>" data-aid="<?php echo $mattribute; ?>">
									<?php } else { ?>
										<li class="matrix-attribute-<?php echo $mtype_id; ?>-<?php echo $mattribute; ?>">
											<label><?php echo print_products_attribute_label($mattribute, $attribute_labels, $attribute_names[$mattribute]); ?>:</label><br />
											<div class="attr-box">
												<select name="fattribute[<?php echo $mattribute; ?>]" class="fmatrix-attr" data-aid="<?php echo $mattribute; ?>" onchange="matrix_calculate_price();<?php if ($aimg) { ?> matrix_attribute_image(this, <?php echo $mattribute; ?>, <?php echo $mtype_id; ?>);<?php } ?>">
													<?php foreach($aterms as $aterm_id => $aterm_name) { ?>
														<option value="<?php echo $aterm_id; ?>"<?php if ($aval == $aterm_id) { echo ' SELECTED'; } ?>><?php echo $aterm_name; ?></option>
													<?php } ?>
												</select><?php print_products_attribute_help_icon($mattribute); ?>
											</div>
											<?php if ($aimg) { $showai = false; $ainmb = 1;
												foreach($aterms as $aterm_id => $aterm_name) {
													if ($wp2print_attribute_images[$aterm_id]) { $showai = true; }
												}
												if ($showai) { ?>
													<div class="attribute-images attribute-images-<?php echo $mattribute; ?>">
														<ul>
															<?php foreach($aterms as $aterm_id => $aterm_name) {
																if ($wp2print_attribute_images[$aterm_id]) { ?>
																	<li><img src="<?php echo print_products_get_thumb($wp2print_attribute_images[$aterm_id], 100, 80, true) ?>" class="attribute-image-<?php echo $aterm_id; ?><?php if ($ainmb == 1) { echo ' active'; } ?>" rel="matrix-attribute-<?php echo $mtype_id; ?>-<?php echo $mattribute; ?>"></li>
																<?php } ?>
															<?php $ainmb++; } ?>
														</ul>
													</div>
												<?php } ?>
											<?php } ?>
										</li>
									<?php } ?>
								<?php } ?>
							<?php } ?>
						<?php } ?>
						</ul>
					</div>
				<?php } ?>
				<?php
				$lmtype = $mtype;
			}
		} ?>
	 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
		<?php if ($product_display_price == 'both') { ?>
			<div class="product-price product-price-incl-tax price-incl-tax"><?php _e('Price', 'wp2print'); ?>: <span class="pprice">0.00</span> <?php echo $price_display_incl_suffix; ?></div>
			<div class="product-price product-price-excl-tax price-excl-tax"><span class="pprice">0.00</span> <?php echo $price_display_excl_suffix; ?></div>
		<?php } else { ?>
			<?php if ($product_display_price == 'incl') { ?>
				<div class="product-price price-incl-tax"><?php _e('Price', 'wp2print'); ?>: <span class="pprice">0.00</span> <?php echo $price_display_incl_suffix; ?></div>
			<?php } else { ?>
				<div class="product-price price-excl-tax"><?php _e('Price', 'wp2print'); ?>: <span class="pprice">0.00</span></div>
			<?php } ?>
		<?php } ?>

		<?php if ($product_display_weight) { ?><div class="product-weight"><?php _e('Weight', 'wp2print'); ?>: <span class="pweight">0</span> <?php echo print_products_get_weight_unit(); ?></div><?php } ?>
		<div class="product-add-button">
			<?php if ($is_modify) { ?>
				<input type="hidden" name="print_products_checkout_process_action" value="update-cart">
				<input type="hidden" name="product_type" value="fixed" class="product-type">
				<input type="hidden" name="product_id" value="<?php echo $product->id; ?>" class="product-id">
				<input type="hidden" name="cart_item_key" value="<?php echo $cart_item_key; ?>">
				<input type="submit" value="<?php _e('Update cart', 'wp2print'); ?>" class="single_add_to_cart_button <?php print_products_buttons_class(); ?> update-cart-btn" onclick="return products_add_cart_action();">
			<?php } else { ?>
				<input type="hidden" name="print_products_checkout_process_action" value="add-to-cart">
				<input type="hidden" name="product_type" value="fixed" class="product-type">
				<input type="hidden" name="product_id" value="<?php echo $product->id; ?>" class="product-id">
				<input type="hidden" name="add-to-cart" value="<?php echo $product->id; ?>">
				<?php if (strlen($artwork_source)) { ?>
					<?php if ($artwork_source == 'artwork' || $artwork_source == 'both') { ?>
						<input type="button" value="<?php _e('Upload your own design', 'wp2print'); ?>" class="single_add_to_cart_button <?php print_products_buttons_class(); ?> alt artwork-btn upload-artwork-btn ch-price" onclick="return products_add_cart_action();">
						<?php if ($artwork_allow_later) { ?>
							<button class="single_add_to_cart_button <?php print_products_buttons_class(); ?> alt artwork-btn simple-add-btn ch-price" onclick="return products_add_cart_action();"><?php _e('Upload later', 'wp2print'); ?></button>
						<?php } ?>
					<?php } ?>
					<?php if (($artwork_source == 'design' || $artwork_source == 'both') && print_products_designer_installed()) {
						$personalizeclass = 'personalize';
						$window_type = personalize_get_window_type();
						if ($window_type == 'Modal Pop-up window') {
							$personalizeclass .= ' personalizep';
						}
						?>
						<button class="single_add_to_cart_button <?php print_products_buttons_class(); ?> alt design-online-btn <?php echo $personalizeclass; ?>" onclick="return products_add_cart_action();"><?php _e('DESIGN ONLINE', 'wp2print'); ?></button>
					<?php } ?>
				<?php } else { ?>
					<input type="submit" value="<?php _e('ADD TO CART', 'wp2print'); ?>" class="single_add_to_cart_button button alt simple-add-btn" onclick="return products_add_cart_action();">
				<?php } ?>
			<?php } ?>
		</div>
		<div class="product-na-text" style="display:none;"><?php _e('This product is not available with the set of options. Please choose another set of options.', 'wp2print'); ?></div>
		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
		<input type="hidden" name="smparams" class="sm-params">
		<input type="hidden" name="fmparams" class="fm-params">
		<input type="hidden" name="atcaction" class="atc-action" value="design">
		<input type="hidden" name="artworkfiles" class="artwork-files" value="">
		<input type="hidden" name="pprice" class="p-price">
		</form>
		<?php print_products_email_quote_form(); ?>
		<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />
	</div>
	<?php
	$smatrix = array();
	$fmatrix = array();
	foreach($product_type_matrix_types as $product_type_matrix_type) {
		$mtype_id = $product_type_matrix_type->mtype_id;
		$mtype = $product_type_matrix_type->mtype;
		$numbers = $product_type_matrix_type->numbers;

		$mnumbers[$mtype_id] = $numbers;

		$matrix_prices = $wpdb->get_results(sprintf("SELECT * FROM %sprint_products_matrix_prices WHERE mtype_id = %s", $wpdb->prefix, $mtype_id));
		if ($matrix_prices) {
			foreach($matrix_prices as $matrix_price) {
				$aterms = $matrix_price->aterms;
				$number = $matrix_price->number;
				$price = $matrix_price->price;

				if ($mtype == 1) {
					$fmatrix[$aterms.'-'.$number] = $price;
				} else {
					$smatrix[$aterms.'-'.$number] = $price;
				}
			}
		}
	}
	?>
	<script>
	var price = 0;

	var numbers_array = new Array();
	<?php foreach($mnumbers as $ntp => $narr) { ?>
	numbers_array[<?php echo $ntp; ?>] = '<?php echo $narr; ?>';
	<?php } ?>

	var smatrix = new Object();
	<?php foreach($smatrix as $mkey => $mval) { ?>
	smatrix['<?php echo $mkey; ?>'] = <?php echo $mval; ?>;
	<?php } ?>

	var fmatrix = new Object();
	<?php foreach($fmatrix as $mkey => $mval) { ?>
	fmatrix['<?php echo $mkey; ?>'] = <?php echo $mval; ?>;
	<?php } ?>

	var shipping_base_quantity = <?php echo $product_shipping_base_quantity; ?>;
	var shipping_weights = new Object();
	<?php if (count($product_shipping_weights)) {
		foreach($product_shipping_weights as $mterm => $sterms) {
			foreach($sterms as $sterm => $weight) {
				if (is_array($weight)) {
					$pcterms = $weight;
					foreach($pcterms as $pcterm => $weight) { ?>
						shipping_weights['<?php echo $mterm.'-'.$sterm.'-'.$pcterm; ?>'] = <?php echo (float)$weight; ?>;
					<?php } ?>
				<?php } else { ?>
					shipping_weights['<?php echo $mterm.'-'.$sterm; ?>'] = <?php echo (float)$weight; ?>;
				<?php } ?>
			<?php }
		}
	} ?>

	jQuery(document).ready(function() {
		matrix_calculate_price();
	});

	jQuery(document).keypress(function(e) {
		if (e.which == 13) { return false; }
	});

	function matrix_calculate_price() {
		var smparams = '';
		var fmparams = '';
		var weight_number = 0;
		var pdprice = '<?php echo $product_display_price; ?>';
		var na_price = false;

		price = 0;

		var quantity = parseInt(jQuery('.product-attributes .quantity').val());

		jQuery('.product-attributes .quantity').val(quantity);

		if (quantity <= 0 || !jQuery.isNumeric(quantity)) { quantity = 1; jQuery('.product-attributes .quantity').val('1'); }

		// simple matrix
		jQuery('.matrix-type-simple').each(function(){
			var mtid = jQuery(this).attr('data-mtid');
			var ntp = jQuery(this).attr('data-ntp');
			var smval = ''; var psmval = ''; var smsep = '';
			var size_val = parseInt(jQuery(this).find('.print-attributes .smatrix-size').eq(0).val());
			var material_val = parseInt(jQuery(this).find('.print-attributes .smatrix-material').eq(0).val());
			var pagecount_val = parseInt(jQuery(this).find('.print-attributes .smatrix-pagecount').eq(0).val());

			jQuery(this).find('.print-attributes .smatrix-attr').each(function(){
				var aid = jQuery(this).attr('data-aid');
				var fval = jQuery(this).val();
				smval += smsep + aid+':'+fval;
				if (!jQuery(this).hasClass('smatrix-attr-text')) {
					psmval += smsep + aid+':'+fval;
				}
				smsep = '-';
			});

			var nmb_val = quantity;
			var numbers = numbers_array[mtid].split(',');
			var min_number = parseInt(numbers[0]);

			jQuery('.area-wh-error').hide();
			if (nmb_val < min_number) {
				var emessage = '<?php _e('Min quantity is ', 'wp2print'); ?>'+min_number;
				jQuery('.area-wh-error').html(emessage).animate({height: 'show'}, 200);
				setTimeout(function(){ jQuery('.area-wh-error').animate({height: 'hide'}); }, 6000);
				jQuery('.product-attributes .quantity').val(min_number);
				quantity = min_number;
				nmb_val = quantity;
			}

			var nums = matrix_get_numbers(nmb_val, numbers);
			var smprice = matrix_get_price(smatrix, psmval, nmb_val, nums);
			if (smprice) { price = price + smprice; }

			if (smparams != '') { smparams += ';'; }
			smparams += mtid+'|'+smval+'|'+nmb_val;

			var mtweight = matrix_shipping_get_weight(nmb_val, material_val, size_val, pagecount_val);
			weight_number = weight_number + mtweight;

			if (smprice == -1) { na_price = true; }
		});
		jQuery('.sm-params').val(smparams);

		// finishing matrix
		jQuery('.matrix-type-finishing').each(function(){
			var mtid = jQuery(this).attr('data-mtid');
			var ntp = jQuery(this).attr('data-ntp');
			var fmsize_aid = 0;
			var fmsize_val = 0;
			if (jQuery('.matrix-type-simple').find('.smatrix-size').size()) {
				fmsize_aid = jQuery('.matrix-type-simple').find('.smatrix-size').attr('data-aid');
				fmsize_val = jQuery('.matrix-type-simple').find('.smatrix-size').val();
			}

			jQuery(this).find('.finishing-attributes .fmatrix-attr').each(function(){
				var fprice = 0;
				var aid = jQuery(this).attr('data-aid');
				var fval = jQuery(this).val();
				var fmval = aid+':'+fval;
				if (fmsize_aid) {
					fmval = fmsize_aid+':'+fmsize_val+'-'+aid+':'+fval;
				}

				var nmb_val = quantity;
				var numbers = numbers_array[mtid].split(',');

				var nums = matrix_get_numbers(nmb_val, numbers);
				var fmprice = matrix_get_price(fmatrix, fmval, nmb_val, nums);
				if (fmprice) { price = price + fmprice; }

				if (fmparams != '') { fmparams += ';'; }
				fmparams += mtid+'|'+fmval+'|'+nmb_val;

				if (fmprice == -1) { na_price = true; }
			});
		});
		jQuery('.fm-params').val(fmparams);

		if (!na_price) {
			if (pdprice == 'both' || pdprice == 'incl') {
				jQuery.post('<?php echo site_url('index.php'); ?>',
					{
						AjaxAction: 'product-get-price-with-tax',
						product_id: <?php echo $product_id; ?>,
						price: price
					},
					function(data) {
						var pricewithtax = parseFloat(data);
						jQuery('.product-attributes .price-incl-tax .pprice').html(matrix_html_price(pricewithtax));
					}
				);
			}
			if (price < 0) { price = 0; }
			jQuery('.product-attributes .price-excl-tax .pprice').html(matrix_html_price(price));
		}

		jQuery('.add-cart-form .p-price').val(price);
		matrix_shipping_weight(weight_number);
		matrix_na_price(na_price);
	}

	function matrix_na_price(na) {
		if (na) {
			jQuery('.add-cart-form .product-add-button').hide();
			jQuery('.add-cart-form .product-na-text').slideDown();
			jQuery('.product-attributes .product-price .pprice').html('N/A');
		} else {
			jQuery('.add-cart-form .product-na-text').hide();
			jQuery('.add-cart-form .product-add-button').slideDown();
		}
	}

	function matrix_html_price(price) {
		var price_decimals = <?php echo wc_get_price_decimals(); ?>;
		var currency_symbol = '<?php echo get_woocommerce_currency_symbol(); ?>';
		var currency_pos = '<?php echo get_option('woocommerce_currency_pos'); ?>';
		var fprice = matrix_format_price(price.toFixed(price_decimals));
		if (currency_pos == 'left') {
			return currency_symbol + fprice;
		} else if (currency_pos == 'right') {
			return fprice + currency_symbol;
		} else if (currency_pos == 'left_space') {
			return currency_symbol + ' ' + fprice;
		} else if (currency_pos == 'right_space') {
			return fprice + ' ' + currency_symbol;
		}
	}
	function matrix_format_price(p) {
		var decimal_sep = '<?php echo wc_get_price_decimal_separator(); ?>';
		var thousand_sep = '<?php echo wc_get_price_thousand_separator(); ?>';
		var pparts = p.toString().split('.');
		pparts[0] = pparts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousand_sep);
		return pparts.join(decimal_sep);
	}

	function matrix_shipping_get_weight(number, material_val, size_val, pagecount_val) {
		var product_weight = 0;
		var swkey = material_val+'-'+size_val;
		if (pagecount_val) {
			swkey = swkey+'-'+pagecount_val;
		}
		if (shipping_weights[swkey]) {
			var pweight = shipping_weights[swkey];
			product_weight = pweight * number;
			if (shipping_base_quantity) {
				product_weight = (pweight / shipping_base_quantity) * number;
			}
		}
		return product_weight;
	}

	function matrix_shipping_weight(product_weight) {
		if (product_weight) {
			jQuery('.product-weight .pweight').html(product_weight.toFixed(1));
			jQuery('.product-weight').animate({height:'show'}, 100);
		} else {
			jQuery('.product-weight').animate({height:'hide'}, 100);
		}
	}

	function matrix_attribute_image(o, aid, mtp) {
		var aval = jQuery(o).val();
		jQuery('.matrix-attribute-'+mtp+'-'+aid+' .attribute-images-'+aid+' img').removeClass('active');
		jQuery('.matrix-attribute-'+mtp+'-'+aid+' .attribute-images-'+aid+' .attribute-image-'+aval).addClass('active');
	}

	function products_add_cart_action() {
		if (price < 0) {
			alert("<?php _e('This combination of size and material not offered. Please select another.', 'wp2print'); ?>");
			return false;
		}
	}
	</script>
	<?php include('product-upload-artwork.php'); ?>
<?php } ?>