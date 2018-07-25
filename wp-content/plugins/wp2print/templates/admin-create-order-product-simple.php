<div class="co-box">
	<p class="form-field">
		<label><?php _e('Quantity', 'wp2print'); ?>: <span class="req">*</span></label>
		<input type="text" name="quantity" class="quantity" value="<?php if ($product_data['quantity']) { echo $product_data['quantity']; } else { echo '1'; } ?>" onblur="matrix_calculate_price()">
	</p>
</div>
<?php print_products_create_order_totals_box(); ?>
<input type="hidden" name="product_type" value="simple">
<script>
<?php if (!$product_data['price']) { ?>
jQuery(document).ready(function() {
	matrix_calculate_price();
});
<?php } ?>
function matrix_calculate_price() {
	var quantity = parseInt(jQuery('.create-order-form .quantity').val());
	var price = parseFloat(jQuery('.create-order-form .p-price').attr('data-price'));
	var subtotal = price * quantity;
	jQuery('.create-order-form .p-price').val(subtotal.toFixed(2));
	matrix_set_tax();
	matrix_set_prices();
}
</script>