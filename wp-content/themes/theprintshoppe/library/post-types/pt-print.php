<?php
/* Bones Custom Post Type Example
This page walks you through creating 
a custom post type and taxonomies. You
can edit this one or copy the following code 
to create another one. 

I put this in a separate file so as to 
keep it organized. I find it easier to edit
and change things if they are concentrated
in their own file.

Developed by: Eddie Machado
URL: http://themble.com/bones/
*/

// Flush rewrite rules for custom post types
add_action( 'after_switch_theme', 'ps_flush_rewrite_rules' );

register_taxonomy( 'ps_print_cat', 
		array('ps_print'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
		array('hierarchical' => true,     /* if this is true, it acts like categories */
			'labels' => array(
				'name' => __( 'Print Category', 'pstheme' ), /* name of the custom taxonomy */
				'singular_name' => __( 'Print Category', 'pstheme' ), /* single taxonomy name */
				'search_items' =>  __( 'Search Print Categories', 'pstheme' ), /* search title for taxomony */
				'all_items' => __( 'All Print Categories', 'pstheme' ), /* all title for taxonomies */
				'parent_item' => __( 'Parent Print Category', 'pstheme' ), /* parent title for taxonomy */
				'parent_item_colon' => __( 'Parent Print Category:', 'pstheme' ), /* parent taxonomy title */
				'edit_item' => __( 'Edit Print Category', 'pstheme' ), /* edit custom taxonomy title */
				'update_item' => __( 'Update Print Category', 'pstheme' ), /* update title for taxonomy */
				'add_new_item' => __( 'Add New Print Category', 'pstheme' ), /* add new title for taxonomy */
				'new_item_name' => __( 'New Print Category Name', 'pstheme' ) /* name title for taxonomy */
			),
			'show_admin_column' => true, 
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'printssentials' ),
		)
	);

// let's create the function for the custom type
function ps_print_pt() { 
	// creating (registering) the custom type 
	register_post_type( 'ps_print', /* (http://codex.wordpress.org/Function_Reference/register_post_type) */
		// let's now add all the options for this post type
		array( 'labels' => array(
			'name' => __( 'Printssentials', 'pstheme' ), /* This is the Title of the Group */
			'singular_name' => __( 'Print Product', 'pstheme' ), /* This is the individual type */
			'all_items' => __( 'All Print Products', 'pstheme' ), /* the all items menu item */
			'add_new' => __( 'Add Print Product', 'pstheme' ), /* The add new menu item */
			'add_new_item' => __( 'Add New Print Product', 'pstheme' ), /* Add New Display Title */
			'edit' => __( 'Edit', 'pstheme' ), /* Edit Dialog */
			'edit_item' => __( 'Edit Print Products', 'pstheme' ), /* Edit Display Title */
			'new_item' => __( 'New Print Product', 'pstheme' ), /* New Display Title */
			'view_item' => __( 'View Print Products', 'pstheme' ), /* View Display Title */
			'search_items' => __( 'Search Print Products', 'pstheme' ), /* Search Custom Type Title */ 
			'not_found' =>  __( 'Nothing found in the Database.', 'pstheme' ), /* This displays if there are no entries yet */ 
			'not_found_in_trash' => __( 'Nothing found in Trash', 'pstheme' ), /* This displays if there is nothing in the trash */
			'parent_item_colon' => ''
			), /* end of arrays */
			'description' => __( 'This is the example team member', 'pstheme' ), /* Custom Type Description */
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 8, /* this is what order you want it to appear in on the left hand side menu */ 
			'menu_icon' => get_stylesheet_directory_uri() . '/library/images/custom-post-icon.png', /* the icon for the custom post type menu */
			'rewrite'	=> array( 'slug' => 'printssentials/%ps_print_cat%', 'with_front' => false ), /* you can specify its url slug */
			'has_archive' => 'printssentials', /* you can rename the slug here */
			'capability_type' => 'page',
			'hierarchical' => true,
			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions')
		) /* end of options */
	); /* end of register post type */
	
	/* this adds your post categories to your custom post type */
	register_taxonomy_for_object_type( 'category', 'ps_print_cat' );
	
}

	// adding the function to the Wordpress init
	add_action( 'init', 'ps_print_pt');
	
	/*
	for more information on taxonomies, go here:
	http://codex.wordpress.org/Function_Reference/register_taxonomy
	*/
	
	// now let's add custom categories (these act like categories)
	

function wpa_show_permalinks( $post_link, $post ){
    if ( is_object( $post ) && $post->post_type == 'ps_print' ){
        $terms = wp_get_object_terms( $post->ID, 'ps_print_cat' );
        if( $terms ){
            return str_replace( '%ps_print_cat%' , $terms[0]->slug , $post_link );
        }
    }
    return $post_link;
}
add_filter( 'post_type_link', 'wpa_show_permalinks', 1, 2 );