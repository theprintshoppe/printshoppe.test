<?php

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
// hook it up to 11 so that it overrides the original register_taxonomy function
add_action( 'init', 'ps_modify_vendor_taxonomy', 11 );

add_filter( 'wcpv_vendor_slug', 'ps_change_vendor_url_slug', 99 );
function ps_change_vendor_url_slug( $slug ) {
  $slug = 'portal';
  return $slug;
}

/**
 * Customize tpoisfef
 */
add_filter('post_type_link', 'ps_edit_product_permalink_structure', 10, 4);
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


add_filter( 'woocommerce_product_tabs', 'ps_remove_product_tabs', 98 );

function ps_remove_product_tabs( $tabs ) {

    unset( $tabs['description'] ); // Remove the description tab
    unset( $tabs['reviews'] ); // Remove the reviews tab
    unset( $tabs['additional_information'] ); // Remove the additional information tab

    return $tabs;

}

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action('woocommerce_before_main_content', 'woocommerce_template_single_add_to_cart', 30);
