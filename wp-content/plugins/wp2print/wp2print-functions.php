<?php
eval(base64_decode('ZnVuY3Rpb24gYTEyKCl7aWYoZ2V0X29wdGlvbigncHJpbnRfcHJvZHVjdHNfbGljZW5zZV9hY3RpdmF0aW9uJykgJiYgcHJpbnRfcHJvZHVjdHNfY2hlY2tfbWQoZXhwbG9kZSgnOicsZ2V0X29wdGlvbigncHJpbnRfcHJvZHVjdHNfbGljZW5zZV9hY3RpdmF0aW9uJykpKSl7cmV0dXJuIHRydWU7fXJldHVybiBmYWxzZTt9'));

function print_products_check_md($sarray) {
	$home_url = $_SERVER['SERVER_NAME'];
	if (md5($sarray[0].$home_url) == $sarray[1]) {
		return true;
	}
}

function print_products_get_product_types() {
	$product_types = array(
		'area'  => __('Area product', 'wp2print'),
		'fixed' => __('Fixed size product', 'wp2print'),
		'book'  => __('Book product', 'wp2print')
	);
	$license_type = print_products_license_type();
	if ($license_type == 'aec_only') {
		$product_types = array(
			'aec' => __('AEC % Coverage product', 'wp2print'),
			'aecbwc' => __('AEC B/W or Color product', 'wp2print')
		);
	} else if ($license_type == 'all') {
		$product_types = array(
			'area'  => __('Area product', 'wp2print'),
			'fixed' => __('Fixed size product', 'wp2print'),
			'book'  => __('Book product', 'wp2print'),
			'aec'   => __('AEC % Coverage product', 'wp2print'),
			'aecbwc' => __('AEC B/W or Color product', 'wp2print')
		);
	}
	return $product_types;
}

function print_products_is_wp2print_type($type) {
	$product_types = array('area', 'fixed', 'book', 'aec', 'aecbwc');
	if (in_array($type, $product_types)) {
		return true;
	}
	return false;
}

$print_products_license_activation = get_option('print_products_license_activation');
function print_products_license_type() {
	global $print_products_license_activation;
	$ladata = explode(':', $print_products_license_activation);
	if (substr($ladata[0], -7) == 'h9C2hWe') {
		return 'aec_only';
	} else if (substr($ladata[0], -7) == 'd7vh8Rw') {
		return 'all';
	}
	return 'except_aec';
}

function print_products_is_allow_aec() {
	$license_type = print_products_license_type();
	if ($license_type != 'except_aec') {
		return true;
	}
	return false;
}

function print_products_get_type($product_id) {
	global $print_products_settings;
	if ($terms = wp_get_object_terms($product_id, 'product_type')) {
		return sanitize_title(current($terms)->slug);
	}
}

function print_products_price_matrix_get_types() {
	return array(0 => __('Printing matrix', 'wp2print'), 1 => __('Finishing matrix', 'wp2print'));
}

function print_products_get_num_types() {
	$dimension_unit = print_products_get_dimension_unit();
	$square_unit = print_products_get_square_unit($dimension_unit);
	return array(
		0 => __('Quantity', 'wp2print'),
		1 => __('Total Pages', 'wp2print'),
		2 => __('Total Area', 'wp2print').' ('.$square_unit.')',
		3 => __('Total Perimeter', 'wp2print'),
		4 => __('Total Width', 'wp2print')
	);
}

function print_products_get_num_type_labels() {
	$dimension_unit = print_products_get_dimension_unit();
	$square_unit = print_products_get_square_unit($dimension_unit);
	return array(
		0 => __('Quantities', 'wp2print'),
		1 => __('Total Pages', 'wp2print'),
		2 => __('Total Areas', 'wp2print').' ('.$square_unit.')',
		3 => __('Total Perimeters', 'wp2print'),
		4 => __('Total Widths', 'wp2print')
	);
}

function print_products_get_weight_unit() {
	return get_option('woocommerce_weight_unit');
}


function print_products_get_dimension_unit() {
	return get_option('woocommerce_dimension_unit');
}

function print_products_get_aec_dimension_unit() {
	global $print_products_plugin_aec;
	$aec_dimensions_unit = print_products_get_dimension_unit();
	if (strlen($print_products_plugin_aec['aec_dimensions_unit'])) {
		$aec_dimensions_unit = $print_products_plugin_aec['aec_dimensions_unit'];
	}
	return $aec_dimensions_unit;
}

function print_products_get_square_unit($unit = '') {
	if (!$unit) { $unit = print_products_get_dimension_unit(); }
	if ($unit == 'in' || $unit == 'ft') {
		return 'ft&#178;';
	}
	return 'm&#178;';
}

function print_products_get_area_square_unit($dimension_unit) {
	switch ($dimension_unit) {
		case 'mm':
		case 'cm':
		case 'm':
			return 'm';
		break;
		case 'ft':
		case 'in':
		case 'yd':
			return 'in';
		break;
	}
}

$terms_names = array();
$attribute_names = array();
$attribute_slugs = array();
$attribute_types = array();
$attribute_imgs = array();
$attribute_help_texts = array();
function print_products_price_matrix_attr_names_init($attributes = '') {
	global $attribute_names, $attribute_slugs, $attribute_types, $attribute_imgs, $attribute_help_texts, $terms_names, $wpdb;
	if (!IS_WOOCOMMERCE) { return; }
	if (!$attributes) {
		$attributes = $wpdb->get_results(sprintf("SELECT * FROM %swoocommerce_attribute_taxonomies ORDER BY attribute_order, attribute_label", $wpdb->prefix));
	}
	if ($attributes) {
		$taxs = array();
		foreach($attributes as $attribute) {
			$taxs[] = 'pa_'.$attribute->attribute_name;
			$attribute_names[$attribute->attribute_id] = wc_attribute_label('pa_'.$attribute->attribute_name);
			$attribute_slugs[$attribute->attribute_id] = $attribute->attribute_name;
			$attribute_types[$attribute->attribute_id] = $attribute->attribute_type;
			$attribute_imgs[$attribute->attribute_id] = $attribute->attribute_img;
			$attribute_help_texts[$attribute->attribute_id] = $attribute->attribute_help_text;
		}
		$attr_terms = $wpdb->get_results(sprintf("SELECT t.*, tt.taxonomy FROM %sterms t LEFT JOIN %sterm_taxonomy tt ON tt.term_id = t.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.term_order, t.name", $wpdb->prefix, $wpdb->prefix, implode("','", $taxs)));
		if ($attr_terms) {
			foreach($attr_terms as $attr_term) {
				$terms_names[$attr_term->term_id] = $attr_term->name;
			}
		}
	}
}

function print_products_price_matrix_attr_names($pmtattributes) {
	global $attribute_names;
	$price_matrix_attr_names = array();
	if (count($pmtattributes)) {
		foreach($pmtattributes as $pmtattribute) {
			$price_matrix_attr_names[] = $attribute_names[$pmtattribute];
		}
	}
	return $price_matrix_attr_names;
}

function print_products_get_attribute_terms($aterms) {
	global $terms_names;
	$attribute_terms = array();
	foreach($terms_names as $tid => $tname) {
		if (in_array($tid, $aterms)) {
			$attribute_terms[$tid] = $tname;
		}
	}
	return $attribute_terms;
}

function print_products_sort_attribute_terms($attr_terms) {
	$sorted_aterms = array();
	if ($attr_terms) {
		foreach($attr_terms as $attr_id => $aterms) {
			$attr_terms = array();
			if (is_array($aterms)) {
				$aterms = print_products_get_attribute_terms($aterms);
				foreach($aterms as $term_id => $term_name) {
					$attr_terms[] = $term_id;
				}
			}
			$sorted_aterms[$attr_id] = $attr_terms;
		}
	}
	return $sorted_aterms;
}

function print_products_price_matrix_get_array($mtattributes, $aterms) {
	$matrix_array = array();
	$mattr_total = count($mtattributes);
	$attr_id = $mtattributes[0];
	$terms_ids = $aterms[$attr_id];
	foreach($terms_ids as $terms_id) {
		if ($mattr_total > 1) {
			$attr_id2 = $mtattributes[1];
			$terms_ids2 = $aterms[$attr_id2];
			foreach($terms_ids2 as $terms_id2) {
				if ($mattr_total > 2) {
					$attr_id3 = $mtattributes[2];
					$terms_ids3 = $aterms[$attr_id3];
					foreach($terms_ids3 as $terms_id3) {
						if ($mattr_total > 3) {
							$attr_id4 = $mtattributes[3];
							$terms_ids4 = $aterms[$attr_id4];
							foreach($terms_ids4 as $terms_id4) {
								if ($mattr_total > 4) {
									$attr_id5 = $mtattributes[4];
									$terms_ids5 = $aterms[$attr_id5];
									foreach($terms_ids5 as $terms_id5) {
										$matrix_array[] = array($terms_id, $terms_id2, $terms_id3, $terms_id4, $terms_id5);
									}
								} else {
									$matrix_array[] = array($terms_id, $terms_id2, $terms_id3, $terms_id4);
								}
							}
						} else {
							$matrix_array[] = array($terms_id, $terms_id2, $terms_id3);
						}
					}
				} else {
					$matrix_array[] = array($terms_id, $terms_id2);
				}
			}
		} else {
			$matrix_array[] = array($terms_id);
		}
	}
	return $matrix_array;
}

function print_products_price_finishing_matrix_get_array($mtattributes, $aterms) {
	global $print_products_settings;

	$matrix_array = array();
	if (count($mtattributes)) {
		$mattr_total = count($mtattributes);
		$attr_id = $mtattributes[0];
		$terms_ids = $aterms[$attr_id];
		if ($terms_ids && count($terms_ids)) {
			foreach($terms_ids as $terms_id) {
				for ($a=1; $a<count($mtattributes); $a++) {
					$sub_attr_id = $mtattributes[$a];
					if ($aterms[$sub_attr_id]) {
						$sub_terms_ids = $aterms[$sub_attr_id];
						foreach($sub_terms_ids as $sub_terms_id) {
							$matrix_array[] = array($terms_id, $sub_terms_id);
						}
					} else {
						$matrix_array[] = array($terms_id, $sub_attr_id);
					}
				}
			}
		}
	}
	return $matrix_array;
}

function print_products_get_matrix_numbers($num, $mtype_id) {
	global $wpdb;
	$lastnum = $num;
	$matrix_numbers = array(0, 0);
	$numbers = explode(',', $wpdb->get_var(sprintf("SELECT numbers FROM %sprint_products_matrix_types WHERE mtype_id = %s", $wpdb->prefix, $mtype_id)));
	if ($num > 0 && $numbers) {
		for ($i=0; $i<count($numbers); $i++) {
			$anumb = (int)$numbers[$i];
			if ($num < $anumb) {
				return array($lastnum, $anumb);
			} else if ($num == $anumb) {
				return array($anumb, $anumb);
			}
			$lastnum = $anumb;
		}
		if (count($numbers) == 1) {
			$matrix_numbers = array($numbers[0], $lastnum);
		} else {
			$matrix_numbers = array($numbers[count($numbers) - 2], $lastnum);
		}
	}
	return $matrix_numbers;
}

function print_products_get_matrix_price_aterms($aterms, $attribute_types) {
	$price_aterms = ''; $patsep = '';
	$aterms_array = explode('-', $aterms);
	if ($aterms_array) {
		foreach($aterms_array as $atline) {
			$atline_array = explode(':', $atline);
			if ($attribute_types[$atline_array[0]] != 'text') {
				$price_aterms .= $patsep . $atline;
				$patsep = '-';
			}
		}
	}
	return $price_aterms;
}

function print_products_get_matrix_price($mtype_id, $mval, $nmb, $nums) {
	global $wpdb;
	$matrix_price = 0;
	$min_nmb = $nums[0];
	$max_nmb = $nums[1];
	$pmatrix = array();
	$print_products_matrix_prices = $wpdb->get_results(sprintf("SELECT * FROM %sprint_products_matrix_prices WHERE mtype_id = %s", $wpdb->prefix, $mtype_id));
	if ($print_products_matrix_prices) {
		foreach($print_products_matrix_prices as $print_products_matrix_price) {
			$pmkey = $print_products_matrix_price->aterms.'-'.$print_products_matrix_price->number;
			$pmatrix[$pmkey] = $print_products_matrix_price->price;
		}
	}
	if ($nmb == $min_nmb && $nmb < $max_nmb) {
		$mval = $mval . '-' . $max_nmb;
		if ($pmatrix[$mval]) {
			$matrix_price = ($pmatrix[$mval] / $max_nmb) * $nmb;
		}
	} else if ($nmb == $min_nmb && $nmb == $max_nmb) {
		$mval = $mval . '-' . $nmb;
		if ($pmatrix[$mval]) {
			$matrix_price = $pmatrix[$mval];
		}
	} else if ($nmb > $min_nmb && $nmb < $max_nmb) {
		$min_mval = $mval . '-' . $min_nmb;
		$max_mval = $mval . '-' . $max_nmb;
		if ($pmatrix[$min_mval] && $pmatrix[$max_mval]) {
			$matrix_price = $pmatrix[$min_mval] + ($nmb - $min_nmb) * ($pmatrix[$max_mval] - $pmatrix[$min_mval]) / ($max_nmb - $min_nmb);
		}
	} else if ($nmb > $min_nmb && $nmb > $max_nmb) {
		$min_mval = $mval . '-' . $min_nmb;
		$max_mval = $mval . '-' . $max_nmb;
		if ($pmatrix[$min_mval] && $pmatrix[$max_mval]) {
			if ($min_nmb == $max_nmb) {
				$matrix_price = $pmatrix[$max_mval] * $nmb;
			} else {
				$matrix_price = $pmatrix[$max_mval] + ($nmb - $max_nmb) * ($pmatrix[$max_mval] - $pmatrix[$min_mval]) / ($max_nmb - $min_nmb);
			}
		}
	}
	return $matrix_price;
}

function print_products_get_numb_price($price, $nmb_val, $nmb) {
	if ($nmb_val != $nmb) {
		if ($nmb == 1 && $nmb_val < 10) {
			$price = $price * $nmb_val;
		} else {
			$price = ($price / $nmb) * $nmb_val;
		}
	}
	return $price;
}

function print_products_get_matrix_title($mtype_id) {
	global $wpdb;
	return $wpdb->get_var(sprintf("SELECT title FROM %sprint_products_matrix_types WHERE mtype_id = %s", $wpdb->prefix, $mtype_id));
}

function print_products_get_attributes_vals($product_attributes, $ptype, $attribute_labels) {
	global $attribute_names, $attribute_types, $terms_names, $print_products_settings;
	$size_attribute = $print_products_settings['size_attribute'];
	$printing_attributes = unserialize($print_products_settings['printing_attributes']);
	if (!$attribute_names) { print_products_price_matrix_attr_names_init(); }
	$attr_terms = array();
	$pqnmb = 0;
	$aprefix = '';
	if ($ptype == 'book') {
		foreach($product_attributes as $akey => $product_attribute) {
			$aarray = explode(':', $product_attribute);
			if ($aarray[0] == $size_attribute) {
				$attr_terms[] = print_products_attribute_label($aarray[0], $attribute_labels, $attribute_names[$aarray[0]]).': <strong>'.$terms_names[$aarray[1]].'</strong>';
				unset($product_attributes[$akey]);
			}
		}
	}
	foreach($product_attributes as $product_attribute) {
		$aarray = explode(':', $product_attribute);
		if (substr($aarray[0], 0, 3) == 'pq|') {
			$aprefix = str_replace('pq|', '', $aarray[0]);
			$attr_terms[] = $aprefix.' '.print_products_attribute_label('pquantity', $attribute_labels, __('Pages Quantity', 'wp2print')).': <strong>'.$aarray[1].'</strong>';
			$pqnmb++;
		} else {
			if ($terms_names[$aarray[1]] != __('None', 'wp2print') && $terms_names[$aarray[1]] != __('No', 'wp2print')) {
				$attr_line = print_products_attribute_label($aarray[0], $attribute_labels, $attribute_names[$aarray[0]]).': <strong>'.$terms_names[$aarray[1]].'</strong>';
				if (strlen($aprefix) && in_array($aarray[0], $printing_attributes)) {
					$attr_line = $aprefix.' '.print_products_attribute_label($aarray[0], $attribute_labels, $attribute_names[$aarray[0]]).': <strong>'.$terms_names[$aarray[1]].'</strong>';
				}
				if ($attribute_types[$aarray[0]] == 'text') {
					$attr_line = print_products_attribute_label($aarray[0], $attribute_labels, $attribute_names[$aarray[0]]).': <strong>'.$aarray[1].'</strong>';
				}
				$attr_terms[] = $attr_line;
			}
		}
	}
	return $attr_terms;
}

function print_products_product_attributes_list_html($item_data) {
	if ($item_data) {
		$dimension_unit = print_products_get_aec_dimension_unit();
		$attribute_labels = (array)get_post_meta($item_data->product_id, '_attribute_labels', true); ?>
		<div class="print-products-area">
			<ul class="product-attributes-list">
				<?php if ($item_data->product_type == 'area' && $item_data->additional) {
					$additional = unserialize($item_data->additional);
					echo '<li>'.print_products_attribute_label('width', $attribute_labels, __('Width', 'wp2print')).': <strong>'.$additional['width'].'</strong></li>';
					echo '<li>'.print_products_attribute_label('height', $attribute_labels, __('Height', 'wp2print')).': <strong>'.$additional['height'].'</strong></li>';
				}
				if (($item_data->product_type == 'aec' || $item_data->product_type == 'aecbwc') && $item_data->additional) {
					$additional = unserialize($item_data->additional);
					$project_name = $additional['project_name'];
					if ($project_name) {
						echo '<li>'.__('Project Name', 'wp2print').': <strong>'.$project_name.'</strong></li>';
					}
				}
				$product_attributes = unserialize($item_data->product_attributes);
				if ($product_attributes) {
					$attr_terms = print_products_get_attributes_vals($product_attributes, $item_data->product_type, $attribute_labels);
					echo '<li>'.implode('</li><li>', $attr_terms).'</li>';
				}
				if ($item_data->product_type == 'aec' && $item_data->additional) {
					$additional = unserialize($item_data->additional);
					$total_area = $additional['total_area'];
					$total_pages = $additional['total_pages'];
					if ($total_area) {
						echo '<li>'.__('Total Area', 'wp2print').': <strong>'.number_format($total_area, 2).' '.$dimension_unit.'<sup>2</sup></strong></li>';
					}
					if ($total_pages) {
						echo '<li>'.__('Total Pages', 'wp2print').': <strong>'.$total_pages.'</strong></li>';
					}
				} else if ($item_data->product_type == 'aecbwc' && $item_data->additional) {
					$additional = unserialize($item_data->additional);
					$total_area = $additional['total_area'];
					$total_pages = $additional['total_pages'];
					$area_bw = $additional['area_bw'];
					$pages_bw = $additional['pages_bw'];
					$area_cl = $additional['area_cl'];
					$pages_cl = $additional['pages_cl'];
					if ($total_area) {
						echo '<li>'.__('Total Area', 'wp2print').': <strong>'.number_format($total_area, 2).' '.$dimension_unit.'<sup>2</sup></strong></li>';
					}
					if ($total_pages) {
						echo '<li>'.__('Total Pages', 'wp2print').': <strong>'.$total_pages.'</strong></li>';
					}
					if ($area_bw) {
						echo '<li>'.__('Area B/W', 'wp2print').': <strong>'.number_format($area_bw, 2).' '.$dimension_unit.'<sup>2</sup></strong></li>';
					}
					if ($pages_bw) {
						echo '<li>'.__('Pages B/W', 'wp2print').': <strong>'.$pages_bw.'</strong></li>';
					}
					if ($area_cl) {
						echo '<li>'.__('Area Color', 'wp2print').': <strong>'.number_format($area_cl, 2).' '.$dimension_unit.'<sup>2</sup></strong></li>';
					}
					if ($pages_cl) {
						echo '<li>'.__('Pages Color', 'wp2print').': <strong>'.$pages_cl.'</strong></li>';
					}
				}
				?>
			</ul>
			<?php if (is_cart() && $product_attributes) { ?>
				<div class="modify-attr"><a href="<?php echo print_products_get_modify_url($item_data->product_id, $item_data->cart_item_key); ?>" class="button"><?php _e('Modify', 'wp2print'); ?></a></div>
			<?php } ?>
		</div>
	<?php }
}

function print_products_product_thumbs_list_html($item_data) {
	global $print_products_plugin_options;
	if ($item_data) {
		$artwork_files = unserialize($item_data->artwork_files);
		if ($artwork_files) { ?>
			<div class="print-products-area">
				<ul class="product-attributes-list">
					<?php if ($item_data->product_type == 'aec' || $item_data->product_type == 'aecbwc') { ?>
						<li><?php _e('Files', 'wp2print'); ?>:</li>
						<?php foreach($artwork_files as $artwork_file) {
							echo '<li><strong><a href="'.print_products_get_amazon_file_url($artwork_file).'" title="'.__('Download', 'wp2print').'">'.basename($artwork_file).'</a></strong></li>';
						} ?>
					<?php } else { ?>
						<li><?php _e('Artwork Files', 'wp2print'); ?>:</li>
						<li><ul class="product-artwork-files-list ftp<?php echo $print_products_plugin_options['dfincart']; ?>">
							<?php foreach($artwork_files as $artwork_file) {
								echo '<li>'.print_products_artwork_file_html($artwork_file, $item_data->item_id).'</li>';
							} ?>
						</ul></li>
					<?php } ?>
				</ul>
			</div>
		<?php }
	}
}

function print_products_get_modify_url($product_id, $cart_item_key) {
	$modify_url = get_permalink($product_id);
	if (strpos($modify_url, '?')) {
		$modify_url .= '&';
	} else {
		$modify_url .= '?';
	}
	$modify_url .= 'modify='.$cart_item_key;
	return $modify_url;
}

function print_products_artwork_files_html($artwork_files, $prod_cart_data = false) {
	global $print_products_plugin_options;
	if ($prod_cart_data && ($prod_cart_data->product_type == 'aec' || $prod_cart_data->product_type == 'aecbwc')) {
		?>
		<div class="print-products-area">
			<ul class="artwork-files-list ftpfilenames">
				<li><?php if (count($artwork_files) == 1) { echo __('File', 'wp2print').': '; } else { echo __('Files', 'wp2print').':'; } ?><br />
				<?php foreach($artwork_files as $artwork_file) { ?>
					<strong><?php echo basename($artwork_file); ?></strong><br />
				<?php } ?>
				</li>
			</ul>
		</div>
		<?php
	} else {
		?>
		<div class="print-products-area">
			<ul class="artwork-files-list ftp<?php echo $print_products_plugin_options['dfincart']; ?>">
				<?php foreach($artwork_files as $artwork_file) { ?>
					<li><?php echo print_products_artwork_file_html($artwork_file, $cart_item_key); ?></li>
				<?php } ?>
			</ul>
		</div>
		<?php
	}
}

function print_products_artwork_file_html($artwork_file, $key) {
	global $print_products_plugin_options;
	$imgext = array('jpg', 'jpeg', 'png', 'tif', 'tiff', 'psd');
	$fileext = array('ai', 'doc', 'eps', 'jpg', 'jpeg', 'pdf', 'png', 'ppt', 'psd', 'tif', 'tiff', 'txt', 'xls', 'zip');
	$earray = explode('.', basename($artwork_file));
	$ext = end($earray);

	$icon_file = PRINT_PRODUCTS_PLUGIN_URL.'images/icons/file.png';
	if (in_array($ext, $fileext)) {
		$icon_file = PRINT_PRODUCTS_PLUGIN_URL.'images/icons/'.$ext.'.png';
	}

	$fvalue = '<img src="'.$icon_file.'" style="width:84px;">';
	if ($print_products_plugin_options['dfincart'] == 'filenames') {
		$fvalue = basename($artwork_file);
	}
	if ($key == 'download') {
		return '<a href="'.print_products_get_amazon_file_url($artwork_file).'" title="'.__('Download', 'wp2print').'">'.$fvalue.'</a>';
	} else if (in_array($ext, $imgext)) {
		return '<a href="'.print_products_get_amazon_file_url($artwork_file).'" rel="prettyPhoto" data-rel="prettyPhoto['.$key.']" title="'.__('View', 'wp2print').'">'.$fvalue.'</a>';
	} else {
		return '<a href="'.print_products_get_amazon_file_url($artwork_file).'" target="_blank" title="'.__('View', 'wp2print').'">'.$fvalue.'</a>';
	}
}

add_filter('body_class', 'print_products_body_class');
function print_products_body_class($classes) {
	if (is_single() || is_cart() || is_checkout() || is_page('my-account')) {
		$classes[] = 'print-products-area';
	}
	return $classes;
}

function print_products_clear_cart_data() {
	global $wpdb;
	$print_products_clear_date = get_option('print_products_clear_date');
	if ($print_products_clear_date != date('Y-m-d')) {
		$cdate = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')));
		$wpdb->query(sprintf("DELETE FROM %sprint_products_cart_data WHERE date_added < '%s'", $wpdb->prefix, $cdate));
		update_option('print_products_clear_date', date('Y-m-d'));
	}
}

function print_products_get_min_price($product_id) {
	global $wpdb;
	$price = 1;
	if (!IS_WOOCOMMERCE) { return; }
	$price_decimals = wc_get_price_decimals();
	$mtype_id = $wpdb->get_var(sprintf("SELECT mtype_id FROM %sprint_products_matrix_types WHERE product_id = %s AND mtype = 0 ORDER BY sorder LIMIT 0, 1", $wpdb->prefix, $product_id));
	if ($mtype_id) {
		$price = $wpdb->get_var(sprintf("SELECT MIN(price) FROM %sprint_products_matrix_prices WHERE price > 0 AND mtype_id = %s", $wpdb->prefix, $mtype_id));
	}
	return number_format($price, $price_decimals, '.', '');
}

function print_products_update_product_price($product_id) {
	global $wpdb;
	$product_price = print_products_get_min_price($product_id);
	update_post_meta($product_id, '_price', $product_price);
	update_post_meta($product_id, '_regular_price', $product_price);
}

function print_products_get_product_sku($mtype_id, $aterms) {
	global $wpdb, $attribute_types;

	if (strlen($aterms)) {
		$aterms = explode('-', $aterms);
		$attributes = $wpdb->get_results(sprintf("SELECT * FROM %swoocommerce_attribute_taxonomies ORDER BY attribute_order, attribute_label", $wpdb->prefix));
		print_products_price_matrix_attr_names_init($attributes);

		$sku_aterms = array();
		foreach($aterms as $aterm) {
			$aterm_array = explode(':', $aterm);
			$akey = $aterm_array[0];
			$aval = $aterm_array[1];
			if ($attribute_types[$akey] != 'text') {
				$sku_aterms[] = $aterm;
			}
		}
		if (count($sku_aterms)) {
			return $wpdb->get_var(sprintf("SELECT sku FROM %sprint_products_matrix_sku WHERE mtype_id = %s AND aterms = '%s'", $wpdb->prefix, $mtype_id, implode('-', $sku_aterms)));
		}
	}
}

function print_products_get_item_sku($order_item_data) {
	global $print_products_settings;
	if ($order_item_data) {
		$additional = unserialize($order_item_data->additional);
		if (strlen($additional['sku'])) {
			return $additional['sku'];
		}
		return get_post_meta($product_id, '_sku', true);
	}
}

function print_products_attribute_label($attribute, $attribute_labels, $def_label = '') {
	if (strlen($attribute_labels[$attribute])) {
		return $attribute_labels[$attribute];
	}
	return $def_label;
}

function print_products_attribute_help_icon($attribute_id) {
	global $print_products_plugin_options, $attribute_help_texts;
	if ($print_products_plugin_options['ahelpicon'] == 1) {
		$help_text = $attribute_help_texts[$attribute_id];
		if (strlen($help_text)) { ?>
			<div class="a-help">
				<img src="<?php echo PRINT_PRODUCTS_PLUGIN_URL; ?>images/info.png">
				<div class="a-help-text"><div class="ah-text-box"><?php echo wpautop($help_text); ?></div></div>
			</div>
			<?php
		}
	}
}

function print_products_designer_installed() {
	global $designer_installed;
	return $designer_installed;
}

function print_products_buttons_class() {
	global $current_user_group;
	$buttons_class = '';
	if ($current_user_group) {
		$theme = unserialize($current_user_group->theme);
		if (strlen($theme['butclass'])) {
			$buttons_class = $theme['butclass'];
		}
	}
	if (!strlen($buttons_class)) {
		$print_products_plugin_options = get_option('print_products_plugin_options');
		if (strlen($print_products_plugin_options['butclass'])) {
			$buttons_class = $print_products_plugin_options['butclass'];
		}
	}
	if (!strlen($buttons_class)) {
		$buttons_class = 'button';
	}
	echo $buttons_class;
}

function print_products_get_thumb($attach_id, $width, $height, $crop = false) {
	if (is_numeric($attach_id)) {
		$image_src = wp_get_attachment_image_src($attach_id, 'full');
		$file_path = get_attached_file($attach_id);
	} else {
		$imagesize = getimagesize($attach_id);
		$image_src[0] = $attach_id;
		$image_src[1] = $imagesize[0];
		$image_src[2] = $imagesize[1];
		$file_path = $_SERVER["DOCUMENT_ROOT"].str_replace(get_bloginfo('siteurl'), '', $attach_id);
		
	}
	
	$file_info = pathinfo($file_path);
	$extension = '.'. $file_info['extension'];

	// image path without extension
	$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];

	$resized_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;

	// if file size is larger than the target size
	if ($image_src[1] > $width || $image_src[2] > $height) {
		// if resized version already exists
		if (file_exists($resized_img_path)) {
			return str_replace(basename($image_src[0]), basename($resized_img_path), $image_src[0]);
		}

		if (!$crop) {
			// calculate size proportionaly
			$proportional_size = wp_constrain_dimensions($image_src[1], $image_src[2], $width, $height);
			$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;			

			// if file already exists
			if (file_exists($resized_img_path)) {
				return str_replace(basename($image_src[0]), basename($resized_img_path), $image_src[0]);
			}
		}

		// resize image if no such resized file
		$image = wp_get_image_editor($file_path);
		if (!is_wp_error($image)) {
			$image->resize($width, $height, $crop);
			$image->save($resized_img_path);
			return str_replace(basename($image_src[0]), basename($resized_img_path), $image_src[0]);
		}
	}

	// return without resizing
	return $image_src[0];
}

function print_products_amazon_s3_get_path($ptype) {
	global $current_user;
	$amazon_s3_path = '';
	$cdate = date('Y-m-d');
	$user_login = $current_user->user_login;
	if (!strlen($user_login)) { $user_login = 'unknown'; }
	if (strlen($ptype)) {
		switch ($ptype) {
			case 'date':
				$amazon_s3_path = $cdate;
			break;
			case 'username':
				$amazon_s3_path = $user_login;
			break;
			case 'date/username':
				$amazon_s3_path = $cdate.'/'.$user_login;
			break;
			case 'username/date':
				$amazon_s3_path = $user_login.'/'.$cdate;
			break;
		}
		$amazon_s3_path = $amazon_s3_path.'/';
	}
	return $amazon_s3_path;
}

function print_products_amazon_s3_get_data($amazon_s3_settings, $file_upload_max_size) {
	$s3_access_key = $amazon_s3_settings['s3_access_key'];
	$s3_secret_key = $amazon_s3_settings['s3_secret_key'];
	$s3_bucketname = $amazon_s3_settings['s3_bucketname'];
	$s3_region = $amazon_s3_settings['s3_region'];
	$s3path = print_products_amazon_s3_get_path($amazon_s3_settings['s3_path']);

	if (strlen($s3_region)) {
		$amazon_url = 'https://'.$s3_bucketname.'.s3-'.$s3_region.'.amazonaws.com/';

		$short_date = gmdate('Ymd');
		$iso_date = gmdate("Ymd\THis\Z");
		$expiration_date = gmdate('Y-m-d\TG:i:s\Z', strtotime('+1 hours'));

		$policy = utf8_encode(
			json_encode(
				array(
					'expiration' => $expiration_date,  
					'conditions' => array(
						array('acl' => print_products_get_s3_acl()),
						array('bucket' => $s3_bucketname),
						array('starts-with', '$key', $s3path),
						array('starts-with', '$name', ''),
						array('starts-with', '$Content-Type', ''),
						array('content-length-range', '1', 5000000000),
						array('x-amz-credential' => $s3_access_key.'/'.$short_date.'/'.$s3_region.'/s3/aws4_request'),
						array('x-amz-algorithm' => 'AWS4-HMAC-SHA256'),
						array('X-amz-date' => $iso_date)
					)
				)
			)
		); 
		$kdate = hash_hmac('sha256', $short_date, 'AWS4' . $s3_secret_key, true);
		$kregion = hash_hmac('sha256', $s3_region, $kdate, true);
		$kservice = hash_hmac('sha256', "s3", $kregion, true);
		$ksigning = hash_hmac('sha256', "aws4_request", $kservice, true);
		$signature = hash_hmac('sha256', base64_encode($policy), $ksigning);
		$amazon_file_url = $amazon_url.$s3path;
		$multiparams = "multipart_params: {
			'key': '".$s3path."$"."{filename}',
			'acl': '".print_products_get_s3_acl()."',
			'X-Amz-Credential' : '".$s3_access_key."/".$short_date."/".$s3_region."/s3/aws4_request',
			'X-Amz-Algorithm' : 'AWS4-HMAC-SHA256',
			'X-Amz-Date' : '".$iso_date."',
			'policy' : '".base64_encode($policy)."',
			'X-Amz-Signature' : '".$signature."'
		},";
	} else {
		$amazon_url = 'https://'.$s3_bucketname.'.s3.amazonaws.com/';
		$policy = base64_encode(json_encode(array(
			'expiration' => date('Y-m-d\TH:i:s.000\Z', strtotime('+1 year')),  
			'conditions' => array(
				array('bucket' => $s3_bucketname),
				array('acl' => print_products_get_s3_acl()),
				array('starts-with', '$key', $s3path),
				array('starts-with', '$Content-Type', ''),
				array('starts-with', '$name', ''),
				array('starts-with', '$Filename', $s3path),
			)
		)));
		$signature = base64_encode(hash_hmac('sha1', $policy, $s3_secret_key, true));
		$amazon_file_url = $amazon_url.$s3path;
		$multiparams = "multipart_params: {
			'key': '".$s3path."$"."{filename}', // use filename as a key
			'Filename': '".$s3path."$"."{filename}', // adding this to keep consistency across the runtimes
			'acl': '".print_products_get_s3_acl()."',
			'AWSAccessKeyId' : '".$s3_access_key."',
			'policy': '".$policy."',
			'signature': '".$signature."'
		},";
	}
	$amazon_s3_data = array(
		'amazon_url' => $amazon_url,
		'amazon_file_url' => $amazon_file_url,
		'multiparams' => $multiparams
	);

	return $amazon_s3_data;
}

function print_products_aec_amazon_s3_get_data($amazon_s3_settings, $file_upload_max_size) {
	$s3_access_key = $amazon_s3_settings['s3_access_key'];
	$s3_secret_key = $amazon_s3_settings['s3_secret_key'];
	$s3_bucketname = $amazon_s3_settings['s3_bucketname'];
	$s3_region = $amazon_s3_settings['s3_region'];

	$amazon_url = 'https://'.$s3_bucketname.'.s3.amazonaws.com/';
	$amazon_file_url = $amazon_url;

	if (strlen($s3_region)) {
		$short_date = gmdate('Ymd');
		$kdate = hash_hmac('sha256', $short_date, 'AWS4' . $s3_secret_key, true);
		$kregion = hash_hmac('sha256', $s3_region, $kdate, true);
		$kservice = hash_hmac('sha256', "s3", $kregion, true);
		$ksigning = hash_hmac('sha256', "aws4_request", $kservice);

		$amazonS3_params = "amazonS3 : {
				accessKeyId: '".$s3_access_key."',
				acl: '".print_products_get_s3_acl()."',
				key: '<FILENAME>',
				signatureKey: '".$ksigning."',
				bucket: '".$s3_bucketname."',
				region: '".$s3_region."',
				v4: true
			}
		";
	} else {
		$policy = base64_encode(json_encode(array(
			'expiration' => date('Y-m-d\TH:i:s.000\Z', strtotime('+1 year')),  
			'conditions' => array(
				array('bucket' => $s3_bucketname),
				array('acl' => print_products_get_s3_acl()),
				array('starts-with', '$Filename', ''),
				array('starts-with', '$key', ''),
				array('starts-with', '$Content-Type', ''),
				array('eq', '$success_action_status', '201')
			)
		)));
		$signature = base64_encode(hash_hmac('sha1', $policy, $s3_secret_key, true));
		$amazonS3_params = "amazonS3 : {
				accessKeyId: '".$s3_access_key."',
				policy: '".$policy."',
				signature: '".$signature."',
				acl: '".print_products_get_s3_acl()."',
				key: '<FILENAME>'
			}
		";
	}
	$amazon_s3_data = array(
		'amazon_url' => $amazon_url,
		'amazon_file_url' => $amazon_file_url,
		'amazonS3_params' => $amazonS3_params
	);

	return $amazon_s3_data;
}

function print_products_get_s3_acl() {
	global $print_products_amazon_s3_settings;
	if ($print_products_amazon_s3_settings['s3_access'] == 'private') {
		return 'private';
	}
	return 'public-read';
}

function print_products_get_amazon_file_url($fileurl) {
	global $amazonS3Client, $print_products_amazon_s3_settings;
	if ($print_products_amazon_s3_settings['s3_access'] == 'private' && $amazonS3Client) {
		$fkey = substr($fileurl, strpos($fileurl, 'amazonaws.com') + 14);
		$fileurl = $amazonS3Client->getObjectUrl($print_products_amazon_s3_settings['s3_bucketname'], $fkey, '+48 hours');
	}
	return $fileurl;
}

function print_products_is_empty_amazon_region() {
	$file_upload_target = get_option("print_products_file_upload_target");
	if ($file_upload_target == 'amazon') {
		$amazon_s3_settings = get_option("print_products_amazon_s3_settings");
		if (!strlen($amazon_s3_settings['s3_region'])) {
			return true;
		}
	}
	return false;
}

function print_products_tab_classes() {
	$tab_classes = array();
	$product_types = print_products_get_product_types();
	foreach($product_types as $tpkey => $tpname) {
		$tab_classes[] = 'hide_if_'.$tpkey;
	}
	return implode(' ', $tab_classes);
}

function print_products_get_uploader_lang_js_file() {
	$uploader_lang_file = 'language_en.js';
	$wplangcode = get_locale();
	$langarray = explode('_', $wplangcode);
	$lang = $langarray[0];
	if (file_exists(PRINT_PRODUCTS_PLUGIN_DIR . '/js/universal/Localization/language_'.$lang.'.js')) {
		$uploader_lang_file = 'language_'.$lang.'.js';
	}
	return $uploader_lang_file;
}

define('ALLOW_UNFILTERED_UPLOADS', true);
add_filter('upload_mimes', 'print_products_myme_types');
function print_products_myme_types($mime_types) {
	$mime_types['csv'] = 'text/csv';
	return $mime_types;
}

function print_products_get_aec_sizes() {
	return array(
		100 => __('Full size', 'wp2print'),
		200 => __('200% - 4x Area', 'wp2print'),
		140 => __('140% - 2x Area', 'wp2print'),
		70  => __('70% - 1/2 Area', 'wp2print'),
		50  => __('50% - 1/4 Area', 'wp2print')
	);
}

function print_products_format_price($price) {
	if (!IS_WOOCOMMERCE) { return; }
	$price_decimals = wc_get_price_decimals();
	$decimal_sep = wc_get_price_decimal_separator();
	$thousand_sep = wc_get_price_thousand_separator();
	return number_format($price, $price_decimals, $decimal_sep, $thousand_sep);
}

function print_products_display_price($price) {
	$price = print_products_format_price($price);
	$currency_symbol = get_woocommerce_currency_symbol();
	$currency_pos = get_option('woocommerce_currency_pos');
	if ($currency_pos == 'left') {
		return $currency_symbol . $price;
	} else if ($currency_pos == 'right') {
		return $price . $currency_symbol;
	} else if ($currency_pos == 'left_space') {
		return $currency_symbol . ' ' . $price;
	} else if ($currency_pos == 'right_space') {
		return $price . ' ' . $currency_symbol;
	}
}

function print_products_get_aec_coverage_ranges() {
	global $print_products_plugin_aec;
	$coverage_ranges = array(5,25,50,75,100);
	if (strlen($print_products_plugin_aec['aec_coverage_ranges'])) {
		$coverage_ranges = explode(',', trim($print_products_plugin_aec['aec_coverage_ranges']));
	}
	return $coverage_ranges;
}

function print_products_get_myaccount_pagename() {
	$myaccount_page_id = (int)wc_get_page_id('myaccount');
	$myaccount_page = get_post($myaccount_page_id);
	if ($myaccount_page) {
		return $myaccount_page->post_name;
	}
}

function print_products_ajax_get_price_with_tax() {
	$product_id = $_POST['product_id'];
	$price = $_POST['price'];
	$_product = new WC_Product($product_id);
	if (function_exists('wc_get_price_including_tax')) {
		$price_incl_tax = wc_get_price_including_tax($_product, array('qty' => 1, 'price' => $price));
	} else {
		$price_incl_tax = $_product->get_price_including_tax(1, $price);
	}
	echo $price_incl_tax;
}

function print_products_help_icon($fkey) {
	$htexts = array(
		'size_attribute' => 'Help text for field Size attribute',
		'colour_attribute' => 'Help text for field Colour attribute',
		'material_attribute' => 'Help text for field Material attribute',
		'page_count_attribute' => 'Help text for field Page Count attribute',
		'printing_attributes' => 'Help text for field Printing attributes',
		'finishing_attributes' => 'Help text for field Finishing attributes',
		'attributes_order' => 'Help text for field Attributes sort order',
		'attributes' => 'Help text for field Attributes',
		'attribute_prefix' => 'Help text for field Attribute prefix',
		'quantity_display_style' => 'Help text for field Quantity display style',
		'proportional_quantity' => 'Help text for field Proportional quantity',
		'quantities' => 'Help text for field Quantities',
		'default_quantity_value' => 'Help text for field Default value for quantity',
		'display_sort_order' => 'Help text for field Display Sort Order',
		'license_key' => 'Help text for field License Key',
		'file_upload_target' => 'Help text for field File upload target',
		's3_access_key' => 'Help text for field S3 Access Key',
		's3_secret_key' => 'Help text for field S3 Secret Key',
		's3_bucketname' => 'Help text for field S3 Bucketname',
		's3_region' => 'Help text for field S3 Region',
		's3_path' => 'Help text for field S3 Path',
		's3_access' => 'Help text for field S3 Files Access',
		'file_upload_max_size' => 'Help text for field File upload max size',
		'infoform_form_title' => 'Help text for field Form title',
		'infoform_form_success_text' => 'Help text for field Form success text',
		'infoform_default_country' => 'Help text for field Default country',
		'infoform_enable_state_field' => 'Help text for field Enable State field',
		'infoform_state_field_label' => 'Help text for field State field label',
		'infoform_zip_field_label' => 'Help text for field Zip field label',
		'infoform_customer_email_subject' => 'Help text for field Customer email subject',
		'infoform_customer_email_heading' => 'Help text for field Customer email heading',
		'infoform_customer_email_content' => 'Help text for field Customer email content',
		'infoform_admin_email_subject' => 'Help text for field Admin email subject',
		'infoform_admin_email_heading' => 'Help text for field Admin email heading',
		'options_butclass' => 'Help text for field Buttons CSS class',
		'options_dfincart' => 'Help text for field Display files in cart as',
		'options_ahelpicon' => 'Help text for field Display attributes help icon',
		'options_allowmodifygroup' => 'Help text for field Allow users to modify group',
		'api_enable' => 'Help text for field Enable Single Sign-on',
		'api_key' => 'Help text for field API Key',
		'aec_coverage_ranges' => 'Help text for field Coverage % Ranges',
		'aec_dimensions_unit' => 'Help text for field Dimensions unit',
		'aec_enable_size' => 'Help text for field Enable size modification in Low-cost option pop-up',
		'aec_pay_now_text' => 'Help text for field Pay Now button text',
		'aec_order_email_subject' => 'Help text for field RapidQuote Email Subject',
		'aec_order_email_message' => 'Help text for field RapidQuote Email Message',
		'email_order_proof_subject' => 'Help text for field Approval Order Email Subject',
		'email_order_proof_message' => 'Help text for field Approval Order Email Message',
		'jobticket_exclude_prices' => 'Help text for field Job-ticket excludes prices',
		'emailquote_enable' => 'Help text for field Enable Widget',
		'emailquote_subject' => 'Help text for field Email Quote Email Subject',
		'emailquote_heading' => 'Help text for field Message Heading',
		'emailquote_toptext' => 'Help text for field Message Top Text',
		'emailquote_bottomtext' => 'Help text for field Message Bottom Text',
		'emailquote_disable_private' => 'Help text for field Disable widget in Private Stores',
		'vendor_shipping_address' => 'Help text for field Vendor Shipping Address',
		'vendor_billing_address' => 'Help text for field Vendor Billing Address',
		'vendor_use_billing' => 'Help text for field Use printshop billing address',
		'vendor_email_subject' => 'Help text for field Vendor Email Subject',
		'vendor_email_header' => 'Help text for field Vendor Email Header',
		'vendor_email_top_text' => 'Help text for field Vendor Email Top Text'
	); ?>
	<img src="<?php echo PRINT_PRODUCTS_PLUGIN_URL; ?>images/help.png" class="help-icon" title="<?php _e($htexts[$fkey], 'wp2print'); ?>" width="16" height="16">
	<?php
}

function print_products_info_form_get_countries() {
	return array(242 => "Afghanistan", 2 => "Albania", 3 => "Algeria", 5 => "Andorra", 6 => "Angola", 7 => "Anguilla", 8 => "Antarctica", 9 => "Antigua and Barbuda", 10 => "Argentina", 11 => "Armenia", 12 => "Aruba", 13 => "Australia", 14 => "Austria", 15 => "Azerbaijan", 16 => "Bahamas", 17 => "Bahrain", 18 => "Bangladesh", 19 => "Barbados", 20 => "Belarus", 21 => "Belgium", 22 => "Belize", 23 => "Benin", 24 => "Bermuda", 25 => "Bhutan", 26 => "Bolivia", 27 => "Bosnia and Herzegowina", 28 => "Botswana", 29 => "Bouvet Island", 30 => "Brazil", 31 => "British Indian Ocean Territory", 32 => "Brunei Darussalam", 33 => "Bulgaria", 34 => "Burkina Faso", 35 => "Burundi", 36 => "Cambodia", 37 => "Cameroon", 38 => "Canada", 39 => "Cape Verde", 40 => "Cayman Islands", 41 => "Central African Republic", 42 => "Chad", 43 => "Chile", 44 => "China", 45 => "Christmas Island", 46 => "Cocos (Keeling) Islands", 47 => "Colombia", 48 => "Comoros", 49 => "Congo", 243 => "Congo (Kinshasa)", 50 => "Cook Islands", 51 => "Costa Rica", 52 => "Cote D'Ivoire", 53 => "Croatia", 54 => "Cuba", 55 => "Cyprus", 56 => "Czech Republic", 57 => "Denmark", 58 => "Djibouti", 59 => "Dominica", 60 => "Dominican Republic", 61 => "East Timor", 62 => "Ecuador", 63 => "Egypt", 64 => "El Salvador", 65 => "Equatorial Guinea", 66 => "Eritrea", 67 => "Estonia", 68 => "Ethiopia", 69 => "Falkland Islands (Malvinas)", 70 => "Faroe Islands", 71 => "Fiji", 72 => "Finland", 73 => "France", 74 => "France, Metropolitan", 75 => "French Guiana", 76 => "French Polynesia", 77 => "French Southern Territories", 78 => "Gabon", 79 => "Gambia", 80 => "Georgia", 81 => "Germany", 82 => "Ghana", 83 => "Gibraltar", 84 => "Greece", 85 => "Greenland", 86 => "Grenada", 87 => "Guadeloupe", 88 => "Guam", 89 => "Guatemala", 90 => "Guinea", 91 => "Guinea-bissau", 92 => "Guyana", 93 => "Haiti", 94 => "Heard and Mc Donald Islands", 95 => "Honduras", 96 => "Hong Kong", 97 => "Hungary", 98 => "Iceland", 99 => "India", 100 => "Indonesia", 101 => "Iran", 102 => "Iraq", 103 => "Ireland", 104 => "Israel", 105 => "Italy", 106 => "Jamaica", 107 => "Japan", 108 => "Jordan", 109 => "Kazakhstan", 110 => "Kenya", 111 => "Kiribati", 112 => "Korea, Democratic Peoples Republic of", 113 => "Korea, Republic of", 114 => "Kuwait", 115 => "Kyrgyzstan", 116 => "Lao Peoples Democratic Republic", 117 => "Latvia", 118 => "Lebanon", 119 => "Lesotho", 120 => "Liberia", 121 => "Libyan Arab Jamahiriya", 122 => "Liechtenstein", 123 => "Lithuania", 124 => "Luxembourg", 125 => "Macau", 126 => "Macedonia", 127 => "Madagascar", 128 => "Malawi", 129 => "Malaysia", 130 => "Maldives", 131 => "Mali", 132 => "Malta", 133 => "Marshall Islands", 134 => "Martinique", 135 => "Mauritania", 136 => "Mauritius", 137 => "Mayotte", 138 => "Mexico", 139 => "Micronesia", 140 => "Moldova", 141 => "Monaco", 142 => "Mongolia", 244 => "Montenegro", 143 => "Montserrat", 144 => "Morocco", 145 => "Mozambique", 146 => "Myanmar", 147 => "Namibia", 148 => "Nauru", 149 => "Nepal", 150 => "Netherlands", 151 => "Netherlands Antilles", 152 => "New Caledonia", 153 => "New Zealand", 154 => "Nicaragua", 155 => "Niger", 156 => "Nigeria", 157 => "Niue", 158 => "Norfolk Island", 159 => "Northern Mariana Islands", 160 => "Norway", 161 => "Oman", 162 => "Pakistan", 163 => "Palau", 164 => "Panama", 165 => "Papua New Guinea", 166 => "Paraguay", 167 => "Peru", 168 => "Philippines", 169 => "Pitcairn", 170 => "Poland", 171 => "Portugal", 172 => "Puerto Rico", 173 => "Qatar", 174 => "Reunion", 175 => "Romania", 176 => "Russian Federation", 177 => "Rwanda", 178 => "Saint Kitts and Nevis", 179 => "Saint Lucia", 180 => "Saint Vincent and the Grenadines", 181 => "Samoa", 182 => "San Marino", 183 => "Sao Tome and Principe", 184 => "Saudi Arabia", 185 => "Senegal", 245 => "Serbia", 186 => "Seychelles", 187 => "Sierra Leone", 188 => "Singapore", 189 => "Slovakia (Slovak Republic)", 190 => "Slovenia", 191 => "Solomon Islands", 192 => "Somalia", 193 => "South Africa", 194 => "South Georgia and the South Sandwich Islands", 246 => "South Sudan", 195 => "Spain", 196 => "Sri Lanka", 197 => "St. Helena", 198 => "St. Pierre and Miquelon", 199 => "Sudan", 200 => "Suriname", 201 => "Svalbard and Jan Mayen Islands", 202 => "Swaziland", 203 => "Sweden", 204 => "Switzerland", 205 => "Syrian Arab Republic", 206 => "Taiwan, Province of China", 207 => "Tajikistan", 208 => "Tanzania, United Republic of", 209 => "Thailand", 247 => "Timor-Leste", 210 => "Togo", 211 => "Tokelau", 212 => "Tonga", 213 => "Trinidad and Tobago", 214 => "Tunisia", 215 => "Turkey", 216 => "Turkmenistan", 217 => "Turks and Caicos Islands", 218 => "Tuvalu", 219 => "Uganda", 220 => "Ukraine", 221 => "United Arab Emirates", 222 => "United Kingdom", 223 => "United States", 224 => "United States Minor Outlying Islands", 225 => "Uruguay", 226 => "Uzbekistan", 227 => "Vanuatu", 228 => "Vatican City State (Holy See)", 229 => "Venezuela", 230 => "Viet Nam", 231 => "Virgin Islands (British)", 232 => "Virgin Islands (U.S.)", 233 => "Wallis and Futuna Islands", 234 => "Western Sahara", 235 => "Yemen", 236 => "Yugoslavia", 237 => "Zaire", 238 => "Zambia", 239 => "Zimbabwe");
}
?>