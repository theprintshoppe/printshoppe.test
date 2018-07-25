jQuery(document).ready(function() {
	jQuery('.pmo-attributes .atermitem').click(function(){
		var ao = jQuery(this).attr('rel');
		var achecked = false;
		jQuery('.pmo-attributes .atermitem').each(function(){
			if (jQuery(this).is(':checked')) {
				achecked = true;
			}
		});
		if (achecked) {
			jQuery('.'+ao).attr('checked', 'checked');
		} else {
			jQuery('.'+ao).removeAttr('checked');
		}
	});
	jQuery('#product-type').change(function(){
		print_products_check_tax_select();
	});
	print_products_check_tax_select();
	print_products_num_type();

	// disable select checkbox for rejected orders
	if (jQuery('mark.rejected-prod').size()) {
		jQuery('mark.rejected-prod').parent().prev().find('input').attr('disabled', 'disabled');
	}
	if (jQuery('mark.await-approval').size()) {
		jQuery('mark.await-approval').parent().prev().find('input').attr('disabled', 'disabled');
	}
	if (jQuery('.order_data_column_container #order_status').size()) {
		var ostatus = jQuery('.order_data_column_container #order_status').val();
		if (ostatus == 'wc-rejected-prod' || ostatus == 'wc-await-approval') {
			jQuery('.order_data_column_container #order_status').attr('disabled', 'disabled');
			jQuery('#woocommerce-order-actions').hide();
		}
	}

	// show General tab for wp2print products
	if (jQuery('ul.product_data_tabs').size()) {
		jQuery('p._tax_status_field').parent().addClass('show_if_fixed show_if_book show_if_area show_if_aec');
		jQuery('select#product-type').change();
	}
	jQuery('.apply-round-up').click(function(){
		if (jQuery(this).is(':checked')) {
			jQuery('table.round-up-discount').show();
		} else {
			jQuery('table.round-up-discount').hide();
		}
	});
	if (jQuery('select.bq-style').size()) {
		print_products_bq_style();
		print_products_pq_style();
	}
	jQuery('.order-send-proof').click(function(){
		var oid = jQuery(this).attr('rel');
		jQuery('.order-proof-form .proof-order-id').val(oid);
		jQuery.colorbox({inline:true, href:"#upload-artwork"});
		return false;
	});
	if (jQuery('#order_data')) {
		jQuery('#order_data').prepend('<input type="button" class="button button-primary order-print-btn" value="'+order_print_label+'" onclick="window.print();">');
	}
	jQuery('.order-vendor').change(function(){
		var oval = jQuery(this).val();
		if (oval) {
			jQuery('.order-vendor-address').slideDown();
		} else {
			jQuery('.order-vendor-address').slideUp();
		}
	});
	jQuery('.order-vendor-address .ovendor-address').click(function(){
		var ova = jQuery('.order-vendor-address .ovendor-address:checked').val();
		if (ova == 'vendor') {
			jQuery('.order-vendor-address .customer-address .address-line').slideUp();
			jQuery('.order-vendor-address .vendor-address .address-line').slideDown();
		} else {
			jQuery('.order-vendor-address .vendor-address .address-line').slideUp();
			jQuery('.order-vendor-address .customer-address .address-line').slideDown();
		}
	});
});
jQuery(function() {
	jQuery('.wp2print-wrap').tooltip();
});
function print_products_select_taxonomy(element) {
	jQuery('#print_products_sort_order_form').submit();
}
function print_products_check_tax_select() {
	var product_type = jQuery('#product-type').val();
	if (product_type == 'fixed' || product_type == 'area' || product_type == 'book' || product_type == 'aec') {
		jQuery('._tax_status_field').parent().show();
		jQuery('li.inventory_tab').addClass('show_if_fixed show_if_area show_if_book show_if_aec').show();
		setTimeout(function(){
			jQuery('li.inventory_tab').addClass('show_if_fixed show_if_area show_if_book show_if_aec').show();
		}, 2000);
	}
}

function print_products_serialize(mixed_value) {
	var _utf8Size = function (str) {
		var size = 0,
			i = 0,
			l = str.length,
			code = '';
		for (i = 0; i < l; i++) {
			code = str.charCodeAt(i);
			if (code < 0x0080) {
				size += 1;
			} else if (code < 0x0800) {
				size += 2;
			} else {
				size += 3;
			}
		}
		return size;
	};
	var _getType = function (inp) {
		var type = typeof inp,
			match;
		var key;

		if (type === 'object' && !inp) {
			return 'null';
		}
		if (type === "object") {
			if (!inp.constructor) {
				return 'object';
			}
			var cons = inp.constructor.toString();
			match = cons.match(/(\w+)\(/);
			if (match) {
				cons = match[1].toLowerCase();
			}
			var types = ["boolean", "number", "string", "array"];
			for (key in types) {
				if (cons == types[key]) {
					type = types[key];
					break;
				}
			}
		}
		return type;
	};
	var type = _getType(mixed_value);
	var val, ktype = '';

	switch (type) {
	case "function":
		val = "";
		break;
	case "boolean":
		val = "b:" + (mixed_value ? "1" : "0");
		break;
	case "number":
		val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
		break;
	case "string":
		val = "s:" + _utf8Size(mixed_value) + ":\"" + mixed_value + "\"";
		break;
	case "array":
	case "object":
		val = "a";
		var count = 0;
		var vals = "";
		var okey;
		var key;
		for (key in mixed_value) {
			if (mixed_value.hasOwnProperty(key)) {
				ktype = _getType(mixed_value[key]);
				if (ktype === "function") {
					continue;
				}

				okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
				vals += this.print_products_serialize(okey) + this.print_products_serialize(mixed_value[key]);
				count++;
			}
		}
		val += ":" + count + ":{" + vals + "}";
		break;
	case "undefined":
		// undefined
	default:
		val = "N";
		break;
	}
	if (type !== "object" && type !== "array") {
		val += ";";
	}
	return val;
}

function print_products_num_type() {
	var ntype = jQuery('.num-type').val();
	jQuery('.numbers-label .nlabel').hide();
	jQuery('.numbers-label .nlabel-'+ntype).show();
}

function print_products_bq_style() {
	var bq_style = jQuery('select.bq-style').val();
	if (bq_style == 1) {
		jQuery('.bq-numbers-tr').show();
		jQuery('.bq-min-tr').hide();
	} else {
		jQuery('.bq-numbers-tr').hide();
		jQuery('.bq-min-tr').show();
	}
}

function print_products_pq_style() {
	var pq_style = jQuery('select.pq-style').val();
	if (pq_style == 1) {
		jQuery('.pq-numbers-tr').show();
		jQuery('.pq-defval-tr').hide();
	} else {
		jQuery('.pq-numbers-tr').hide();
		jQuery('.pq-defval-tr').show();
	}
}

function print_products_group_address_add(atype) {
	jQuery('.group-address-form')[0].reset();
	jQuery('.group-address-form .ga-action').val('add');
	jQuery('.group-address-form .ga-type').val(atype);
	jQuery('.group-address-form .ga-add-title').show();
	jQuery('.group-address-form .ga-edit-title').hide();
	jQuery('.group-address-form .ga-error').hide();
	if (atype == 'shipping') {
		jQuery('.group-address-form .ga-phone-email').hide();
	} else {
		jQuery('.group-address-form .ga-phone-email').show();
	}
	jQuery('.group-address-form .ga-country option').removeAttr('selected');
	print_products_group_address_country_change();
}

function print_products_group_address_edit(akey, atype) {
	var relobj = '.'+atype+'-'+akey;
	jQuery('.group-address-form')[0].reset();
	jQuery('.group-address-form .ga-action').val('edit');
	jQuery('.group-address-form .ga-type').val(atype);
	jQuery('.group-address-form .ga-rel').val(akey);
	jQuery('.group-address-form .ga-add-title').hide();
	jQuery('.group-address-form .ga-edit-title').show();
	jQuery('.group-address-form .ga-error').hide();

	var country = jQuery(relobj+' .a-country').val();
	var state = jQuery(relobj+' .a-state').val();

	jQuery('.group-address-form .ga-label').val(jQuery(relobj+' .a-label').val());
	jQuery('.group-address-form .ga-fname').val(jQuery(relobj+' .a-fname').val());
	jQuery('.group-address-form .ga-lname').val(jQuery(relobj+' .a-lname').val());
	jQuery('.group-address-form .ga-company').val(jQuery(relobj+' .a-company').val());
	jQuery('.group-address-form .ga-country option[value="'+country+'"]').attr('selected', 'selected');
	jQuery('.group-address-form .ga-address').val(jQuery(relobj+' .a-address').val());
	jQuery('.group-address-form .ga-address2').val(jQuery(relobj+' .a-address2').val());
	jQuery('.group-address-form .ga-city').val(jQuery(relobj+' .a-city').val());
	jQuery('.group-address-form .ga-zip').val(jQuery(relobj+' .a-zip').val());
	if (atype == 'billing') {
		jQuery('.group-address-form .ga-phone').val(jQuery(relobj+' .a-phone').val());
		jQuery('.group-address-form .ga-email').val(jQuery(relobj+' .a-email').val());
		jQuery('.group-address-form .ga-phone-email').show();
	} else {
		jQuery('.group-address-form .ga-phone-email').hide();
	}

	print_products_group_address_country_change();
	if (jQuery('.group-address-form .ga-state-'+country).size()) {
		jQuery('.group-address-form .ga-state-'+country+' option[value="'+state+'"]').attr('selected', 'selected');
	} else {
		jQuery('.group-address-form .ga-state-text').val(state);
	}
}

function print_products_group_address_delete(akey) {
	var delmessage = jQuery('.group-addresses-content').attr('data-dmessage');
	var d = confirm(delmessage);
	if (d) {
		jQuery('.group-addresses-content .'+akey).remove();
	}
}

function print_products_group_address_country_change() {
	var country = jQuery('.group-address-form .ga-country').val();
	if (country != '') {
		if (jQuery('.group-address-form .ga-state-'+country).size()) {
			jQuery('.group-address-form .ga-state option').removeAttr('selected');

			jQuery('.group-address-form .ga-state-'+country).show();
			jQuery('.group-address-form .ga-state-text').hide();
		} else {
			jQuery('.group-address-form .ga-state').hide();
			jQuery('.group-address-form .ga-state-text').show();
		}
	} else {
		jQuery('.group-address-form .ga-state').hide();
		jQuery('.group-address-form .ga-state-text').show();
	}
}

function print_products_group_address_save() {
	var error = false;
	var gaaction = jQuery('.group-address-form .ga-action').val();
	var gatype = wp2print_trim(jQuery('.group-address-form .ga-type').val());
	var garel = wp2print_trim(jQuery('.group-address-form .ga-rel').val());
	var label = wp2print_trim(jQuery('.group-address-form .ga-label').val());
	var fname = wp2print_trim(jQuery('.group-address-form .ga-fname').val());
	var lname = wp2print_trim(jQuery('.group-address-form .ga-lname').val());
	var company = wp2print_trim(jQuery('.group-address-form .ga-company').val());
	var country = wp2print_trim(jQuery('.group-address-form .ga-country').val());
	var address = wp2print_trim(jQuery('.group-address-form .ga-address').val());
	var address2 = wp2print_trim(jQuery('.group-address-form .ga-address2').val());
	var city = wp2print_trim(jQuery('.group-address-form .ga-city').val());
	var zip = wp2print_trim(jQuery('.group-address-form .ga-zip').val());
	var phone = wp2print_trim(jQuery('.group-address-form .ga-phone').val());
	var email = wp2print_trim(jQuery('.group-address-form .ga-email').val());

	var state = wp2print_trim(jQuery('.group-address-form .ga-state-text').val());
	if (jQuery('.group-address-form .ga-state-'+country).size()) {
		state = jQuery('.group-address-form .ga-state-'+country).val();
	}

	jQuery('.group-address-form .ga-error').hide();

	if (label == '') { error = true; }
	if (fname == '') { error = true; }
	if (lname == '') { error = true; }
	if (company == '') { error = true; }
	if (country == '') { error = true; }
	if (address == '') { error = true; }
	if (city == '') { error = true; }
	if (state == '') { error = true; }
	if (zip == '') { error = true; }
	if (gatype == 'billing') {
		if (phone == '') { error = true; }
		if (email == '') { error = true; }
	}

	if (error) {
		jQuery('.group-address-form .ga-error').slideDown();
	} else {
		if (gaaction == 'edit') {
			var arel = '.'+gatype+'-'+garel;
			jQuery(arel+' .a-line').html(label);
			jQuery(arel+' .a-label').val(label);
			jQuery(arel+' .a-fname').val(fname);
			jQuery(arel+' .a-lname').val(lname);
			jQuery(arel+' .a-company').val(company);
			jQuery(arel+' .a-country').val(country);
			jQuery(arel+' .a-address').val(address);
			jQuery(arel+' .a-address2').val(address2);
			jQuery(arel+' .a-city').val(city);
			jQuery(arel+' .a-state').val(state);
			jQuery(arel+' .a-zip').val(zip);
			if (gatype == 'billing') {
				jQuery(arel+' .a-phone').val(phone);
				jQuery(arel+' .a-email').val(email);
			}
		} else {
			var akey = new Date().getTime();
			var elabel = jQuery('.group-address-form').attr('data-edit');
			var dlabel = jQuery('.group-address-form').attr('data-delete');
			var ahtml = '<tr class="billing-'+akey+'">';
			ahtml += '<td><input type="checkbox" name="'+gatype+'_addresses['+akey+'][active]" value="1"></td>';
			ahtml += '<td class="a-line">'+label+'</td>';
			ahtml += '<td align="right"><a href="#TB_inline?width=400&height=535&inlineId=group-address-form" class="thickbox" onclick="print_products_group_address_edit(\''+akey+'\', \'billing\');">'+elabel+'</a>&nbsp;|&nbsp;<a href="#delete" class="delete-addr" onclick="print_products_group_address_delete(\'billing-'+akey+'\'); return false;">'+dlabel+'</a>';
			ahtml += '<div class="a-info" style="display:none;">';
			ahtml += '<input type="hidden" name="'+gatype+'_addresses['+akey+'][label]" value="'+label+'" class="a-fname">';
			ahtml += '<input type="hidden" name="'+gatype+'_addresses['+akey+'][fname]" value="'+fname+'" class="a-fname">';
			ahtml += '<input type="hidden" name="'+gatype+'_addresses['+akey+'][lname]" value="'+lname+'" class="a-lname">';
			ahtml += '<input type="hidden" name="'+gatype+'_addresses['+akey+'][company]" value="'+company+'" class="a-company">';
			ahtml += '<input type="hidden" name="'+gatype+'_addresses['+akey+'][country]" value="'+country+'" class="a-country">';
			ahtml += '<input type="hidden" name="'+gatype+'_addresses['+akey+'][address]" value="'+address+'" class="a-address">';
			ahtml += '<input type="hidden" name="'+gatype+'_addresses['+akey+'][address2]" value="'+address2+'" class="a-address2">';
			ahtml += '<input type="hidden" name="'+gatype+'_addresses['+akey+'][city]" value="'+city+'" class="a-city">';
			ahtml += '<input type="hidden" name="'+gatype+'_addresses['+akey+'][state]" value="'+state+'" class="a-state">';
			ahtml += '<input type="hidden" name="'+gatype+'_addresses['+akey+'][zip]" value="'+zip+'" class="a-zip">';
			ahtml += '<input type="hidden" name="'+gatype+'_addresses['+akey+'][phone]" value="'+phone+'" class="a-phone">';
			ahtml += '<input type="hidden" name="'+gatype+'_addresses['+akey+'][email]" value="'+email+'" class="a-email">';
			ahtml += '</div></td>';
			ahtml += '</tr>';

			if (jQuery('.ga-'+gatype+'-addresses table .a-noaddress').size()) {
				jQuery('.ga-'+gatype+'-addresses table .a-noaddress').remove();
			}
			jQuery('.ga-'+gatype+'-addresses table').append(ahtml);
		}
		self.parent.tb_remove();
	}
	return false;
}

function wp2print_trim(str) {
	if (str != 'undefined') {
		return str.replace(/^\s+|\s+$/g,"");
	} else {
		return '';
	}
}

function create_order_process(step) {
	var error = false;
	if (step == 1) {
		var customer = jQuery('.create-order-form .order-customer').val();
		var product = jQuery('.create-order-form .order-product').val();
		if (customer == '' || product == '') {
			error = true;
		}
	} else if (step == 2) {
		var afilled = true;
		jQuery('.create-order-form .co-billing-address .form-field').each(function(){
			if (jQuery(this).find('select').size()) {
				var fval = jQuery(this).find('select').val();
			} else {
				var fval = jQuery(this).find('input').val();
				if (jQuery(this).find('input').attr('id') == 'billing_address_2') {
					fval = 'FILLED';
				}
			}
			if (fval == '') {
				afilled = false;
			}
		});
		jQuery('.create-order-form .co-shipping-address .form-field').each(function(){
			if (jQuery(this).find('select').size()) {
				var fval = jQuery(this).find('select').val();
			} else {
				var fval = jQuery(this).find('input').val();
				if (jQuery(this).find('input').attr('id') == 'shipping_address_2') {
					fval = 'FILLED';
				}
			}
			if (fval == '') {
				afilled = false;
			}
		});
		if (!afilled) {
			error = true;
		}
	} else if (step == 3) {
		var quantity = jQuery('.create-order-form .quantity').val();
		if (quantity == '') {
			error = true;
		}
	} else if (step == 4) {
		var subtotal = parseFloat(jQuery('.create-order-form .p-price').val());
		var total = parseFloat(jQuery('.create-order-form .total-price').val());
		if ((subtotal == '' || subtotal == 0) && (total == '' || total == 0)) {
			error = true;
		}
	}
	if (error) {
		var error_message = jQuery('.create-order-form').attr('data-error-required');
		alert(error_message);
		return false;
	}
}

function create_order_copy_billing() {
	jQuery('.co-shipping-address #shipping_company').val(jQuery('.co-billing-address #billing_company').val());
	jQuery('.co-shipping-address #shipping_address_1').val(jQuery('.co-billing-address #billing_address_1').val());
	jQuery('.co-shipping-address #shipping_address_2').val(jQuery('.co-billing-address #billing_address_2').val());
	jQuery('.co-shipping-address #shipping_city').val(jQuery('.co-billing-address #billing_city').val());
	jQuery('.co-shipping-address #shipping_postcode').val(jQuery('.co-billing-address #billing_postcode').val());

	var country = jQuery('.co-billing-address #billing_country').val();
	var country_name = jQuery('.co-billing-address #billing_country option:selected').text();
	jQuery('.co-shipping-address #shipping_country option').removeAttr('selected');
	if (country) {
		jQuery('.co-shipping-address #shipping_country option[value="'+country+'"]').attr('selected', 'selected');
		jQuery('.co-shipping-address #select2-shipping_country-container').html(country_name);
		jQuery('.co-shipping-address #shipping_country').trigger('change');
	}
	var state = jQuery('.co-billing-address #billing_state').val();
	var state_name = state;
	if (jQuery('.co-billing-address #billing_state').size()) {
		state_name = jQuery('.co-billing-address #billing_state option:selected').text();
	}
	if (jQuery('.co-shipping-address select#shipping_state').size()) {
		jQuery('.co-shipping-address select#shipping_state option[value="'+state+'"]').attr('selected', 'selected');
		jQuery('.co-shipping-address #select2-shipping_state-container').html(state_name);
	} else {
		jQuery('.co-shipping-address #shipping_state').val(state);
	}
	return false;
}

function matrix_get_numbers(num, numbers) {
	var lastnum = num;
	var matrix_numbers = new Array();
	matrix_numbers[0] = 0;
	matrix_numbers[1] = 0;
	if (num > 0) {
		for (var i=0; i<numbers.length; i++) {
			anumb = parseInt(numbers[i]);
			if (num < anumb) {
				matrix_numbers[0] = lastnum;
				matrix_numbers[1] = anumb;
				return matrix_numbers;
			} else if (num == anumb) {
				matrix_numbers[0] = anumb;
				matrix_numbers[1] = anumb;
				return matrix_numbers;
			}
			lastnum = anumb;
		}
		if (numbers.length == 1) {
			matrix_numbers[0] = numbers[0];
		} else {
			matrix_numbers[0] = numbers[numbers.length - 2];
		}
		matrix_numbers[1] = lastnum;
	}
	return matrix_numbers;
}

function matrix_get_price(pmatrix, mval, nmb, nums) {
	var p = 0;
	var min_nmb = nums[0];
	var max_nmb = nums[1];
	if (nmb == min_nmb && nmb < max_nmb) {
		mval = mval + '-' + max_nmb;
		if (pmatrix[mval]) {
			p = (pmatrix[mval] / max_nmb) * nmb;
		}
	} else if (nmb == min_nmb && nmb == max_nmb) {
		mval = mval + '-' + nmb;
		if (pmatrix[mval]) {
			p = pmatrix[mval];
		}
	} else if (nmb > min_nmb && nmb < max_nmb) {
		var min_mval = mval + '-' + min_nmb;
		var max_mval = mval + '-' + max_nmb;
		if (pmatrix[min_mval] && pmatrix[max_mval]) {
			p = pmatrix[min_mval] + (nmb - min_nmb) * (pmatrix[max_mval] - pmatrix[min_mval]) / (max_nmb - min_nmb);
		}
	} else if (nmb > min_nmb && nmb > max_nmb) {
		var min_mval = mval + '-' + min_nmb;
		var max_mval = mval + '-' + max_nmb;
		if (pmatrix[min_mval] && pmatrix[max_mval]) {
			if (min_nmb == max_nmb) {
				p = pmatrix[max_mval] * nmb;
			} else {
				p = pmatrix[max_mval] + (nmb - max_nmb) * (pmatrix[max_mval] - pmatrix[min_mval]) / (max_nmb - min_nmb);
			}
		}
	}
	return p;
}

function matrix_set_tax() {
	var tax_rate = parseFloat(jQuery('.create-order-form .tax-price').attr('data-rate'));
	if (tax_rate) {
		var subtotal = parseFloat(jQuery('.create-order-form .p-price').val());
		if (subtotal > 0) {
			var tax_price = (subtotal / 100) * tax_rate;
			jQuery('.create-order-form .tax-price').val(tax_price.toFixed(2));
		}
	}
}

function matrix_set_shipping_tax() {
	var tax_rate = parseFloat(jQuery('.create-order-form .tax-price').attr('data-rate'));
	if (tax_rate) {
		var shipping = parseFloat(jQuery('.create-order-form .shipping-price').val());
		if (shipping > 0) {
			var shipping_tax_price = (shipping / 100) * tax_rate;
			jQuery('.create-order-form .shipping-tax-price').val(shipping_tax_price.toFixed(2));
		}
	}
}

function matrix_set_prices() {
	var subtotal = parseFloat(jQuery('.create-order-form .p-price').val());
	var tax = parseFloat(jQuery('.create-order-form .tax-price').val());
	var shipping = parseFloat(jQuery('.create-order-form .shipping-price').val());
	var shipping_tax = parseFloat(jQuery('.create-order-form .shipping-tax-price').val());
	var total = subtotal + tax + shipping + shipping_tax;
	jQuery('.create-order-form .total-price').val(total.toFixed(2));
}
