<?php

/**
 * Customize the vednro slugs
 */
function ps_modify_vendor_taxonomy() {
    // get the arguments of the already-registered taxonomy
    $vendor_category_args = get_taxonomy( 'wcpv_product_vendors' ); // returns an object

    // make changes to the args
    // in this example there are three changes
    // again, note that it's an object
    $vendor_category_args->show_admin_column = true;
    $vendor_category_args->rewrite['with_front'] = false;
    $vendor_category_args->rewrite['slug'] = 'portal';

    // re-register the taxonomy
    register_taxonomy( 'wcpv_product_vendors', 'product', (array) $vendor_category_args );
}
add_action( 'init', 'ps_modify_vendor_taxonomy', 11 );

add_filter( 'wcpv_vendor_slug', 'ps_change_vendor_url_slug', 99 );
function ps_change_vendor_url_slug( $slug ) {
  $slug = 'portal';
  return $slug;
}

/**
 * Customize the product URL
 */
//add_filter('post_type_link', 'ps_edit_product_permalink_structure', 10, 4);
function ps_edit_product_permalink_structure($post_link, $post, $leavename, $sample) {
    if (false !== strpos($post_link, '%psvendor%')) {
        $ps_vendor_type_term = get_the_terms($post->ID, 'wcpv_product_vendors');
        if (!empty($ps_vendor_type_term))
            $post_link = str_replace('%psvendor%', array_pop($ps_vendor_type_term)->
            slug, $post_link);
        else
            $post_link = str_replace('%psvendor%', 'general', $post_link);
    }
    return $post_link;
}

/**
 *  Remove the tabs on the product page
 */
add_filter( 'woocommerce_product_tabs', 'ps_remove_product_tabs', 98 );
function ps_remove_product_tabs( $tabs ) {

    unset( $tabs['description'] ); // Remove the description tab
    unset( $tabs['reviews'] ); // Remove the reviews tab
    unset( $tabs['additional_information'] ); // Remove the additional information tab

    return $tabs;

}

/**
 *  Move stuff around on the product page
 */
function ps_woo_product_image_summary_open_wrap() { ?>
    <div class="single-product-image-summary-wrapper">
<?php }
function ps_woo_product_image_summary_close_wrap() { ?>
    </div>
<?php }
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
//remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

add_action('woocommerce_before_single_product_summary', 'ps_woo_product_image_summary_open_wrap', 5);
add_action('woocommerce_after_single_product_summary', 'ps_woo_product_image_summary_close_wrap', 30);
add_action('woocommerce_after_single_product_summary', 'woocommerce_template_single_add_to_cart', 40);


/**
 *  Move stuff around on the cart
 */
function ps_cart_proof_warning() { ?>
    <div class="ps-proof-warning">
        <h5>WARNING:</h5>
        <p>Please review your proofs carefully by clicking on the thumbnail images under "Preview". Our obligation is to print files as you submit them here; while we try to catch any egregious errors, we cannot be responsible for typos, misplaced pictures, or missing information.</p>
        <p class="ps-proof-warning-toggle">
            <input type="checkbox" name="proof_approval" class="proof-approval"/>
            <label for="proof_approval">I understand, and I approve the proof(s).</label>
        </p>
    </div>
<?php }
add_action('woocommerce_after_cart_table', 'ps_cart_proof_warning', 40);

/**
 *  Force new usernames to be the email address.
 */
add_filter( 'woocommerce_new_customer_data', function( $data ) {
    $data['user_login'] = $data['user_email'];
    return $data;
} );

/*
 * Change text of add to cart for personalization
 */
remove_filter('add_to_cart_text', 'woo_custom_cart_button_text');
remove_filter('woocommerce_product_add_to_cart_text', 'woo_custom_cart_button_text');
remove_filter('woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text');
remove_filter('single_add_to_cart_text', 'woo_custom_cart_button_text');
add_filter('add_to_cart_text', 'tps_custom_cart_button_text');
add_filter('woocommerce_product_add_to_cart_text', 'tps_custom_cart_button_text');
add_filter('woocommerce_product_single_add_to_cart_text', 'tps_custom_cart_button_text');
add_filter('single_add_to_cart_text', 'tps_custom_cart_button_text');
function tps_custom_cart_button_text($text) {
    global $post, $personalize_settings;
    $personalize = get_post_meta($post->ID, 'personalize', true);
    if($personalize == 'y' && $personalize_settings && !isset($_GET['add-to-cart'])) {
        return __('Personalize', 'personalize');
    }
    return $text;
}

/**
 * @snippet       Disable Shipping Fields for Local Pickup
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/?p=72660
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 3.0.7
 */
  
add_action( 'woocommerce_after_checkout_form', 'bbloomer_disable_shipping_local_pickup' );
 
function bbloomer_disable_shipping_local_pickup( $available_gateways ) {
global $woocommerce;
 
// Part 1: Hide shipping based on the static choice @ Cart
// Note: "#customer_details .col-2" strictly depends on your theme
 
$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
$chosen_shipping_no_ajax = $chosen_methods[0];
if ( 0 === strpos( $chosen_shipping_no_ajax, 'local_pickup' ) ) {
 
?>
<script type="text/javascript">
 
    jQuery('#customer_details .col-2').fadeOut();
 
</script>
<?php
     
} 
 
// Part 2: Hide shipping based on the dynamic choice @ Checkout
// Note: "#customer_details .col-2" strictly depends on your theme
 
?>
<script type="text/javascript">
                jQuery('form.checkout').on('change','input[name^="shipping_method"]',function() {
    var val = jQuery( this ).val();
    if (val.match("^local_pickup")) {
                jQuery('#customer_details .col-2 .woocommerce-shipping-fields').fadeOut();
        } else {
        jQuery('#customer_details .col-2 .woocommerce-shipping-fields').fadeIn();
    }
});
 
</script>
<?php
 
}


add_action('woocommerce_email_before_order_table', 'ps_email_order_id_as_a_global', 1, 4);
function ps_email_order_id_as_a_global($order, $sent_to_admin, $plain_text, $email){
    $GLOBALS['email_data'] = array(
        'sent_to_admin' => $sent_to_admin, // <== HERE we set "$sent_to_admin" value
        'email_id' => $email->id, // The email ID (to target specific email notification)
    );
}

add_action('woocommerce_order_item_meta_end', 'ps_print_designer_woo_order_item_meta_start', 5, 3);
function ps_print_designer_woo_order_item_meta_start($item_id, $item, $order) {
    global $api_info;

    $refNameGlobalsVar = $GLOBALS;
    $email_data = $refNameGlobalsVar['email_data'];

    if( ! ( is_array( $email_data ) && $email_data['sent_to_admin'] ) ) return;

    $pdf_link = wc_get_order_item_meta($item_id, '_pdf_link', true);
    if (strlen($pdf_link)) {
        $pdf_links = explode(',', $pdf_link);
        foreach($pdf_links as $pdf_link) {
            echo '<br/><a href="'.$pdf_link.'">'.__('Download PDF', 'personalize').'</a>';
        }
    }
}

//add_action('woocommerce_email_after_order_table', 'ps_print_designer_woo_after_order_table', 5, 4);
function ps_print_designer_woo_after_order_table($order, $sent_to_admin, $plain_text, $email) {
    global $api_info;

    if( $email->id == 'order_email_to_vendor') :
        $vendors = new WC_Product_Vendors_Order_Email_To_Vendor();

        //add_filter( 'woocommerce_get_order_item_totals', array( $vendors, 'filter_order_totals' ), 15, 2 );
        remove_filter( 'woocommerce_email_customer_details_fields', array( $vendors, 'filter_customer_fields' ), 15, 3 );
    endif;
}

//add_filter( 'woocommerce_get_order_item_totals', 'ps_filter_order_totals', 5, 2 );
function ps_filter_order_totals( $total_rows, $order ) {
    // don't show payment method to vendors
    set( $total_rows['payment_method'] );
    set( $total_rows['order_total'] );

    return $total_rows;
}

function ps_conditional_email_recipient( $recipient, $order ) {
    // Bail on WC settings pages since the order object isn't yet set yet
    // Not sure why this is even a thing, but shikata ga nai
    $page = $_GET['page'] = isset( $_GET['page'] ) ? $_GET['page'] : '';
    if ( 'wc-settings' === $page ) {
        return $recipient; 
    }
    
    // just in case
    if ( ! $order instanceof WC_Order ) {
        return $recipient; 
    }
    $items = $order->get_items();

    $vendors = WC_Product_Vendors_Utils::get_vendors_from_order( $order );

    foreach ( $vendors as $vendor_id => $data ) { 
            //$this->vendor = $vendor_id;
            $recipient .= ', ' . $data['email'];

            return $recipient;
    }
    
    return $recipient;
}
add_filter( 'woocommerce_email_recipient_new_order', 'ps_conditional_email_recipient', 10, 2 );

function set_default_display_name( $user_id ) {
  $user = get_userdata( $user_id );
  $name = sprintf( '%s %s', $user->first_name, $user->last_name );
  $args = array(
    'ID'           => $user_id,
    'display_name' => $name,
    'nickname'     => $name
  );
  wp_update_user( $args );
}
add_action( 'user_register', 'set_default_display_name' );