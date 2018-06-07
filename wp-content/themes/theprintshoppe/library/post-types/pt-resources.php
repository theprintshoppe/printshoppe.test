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

// Flush your rewrite rules
function ps_flush_rewrite_rules() {
	flush_rewrite_rules();
}

// let's create the function for the custom type
function ps_resources_pt() { 
	// creating (registering) the custom type 
	register_post_type( 'ps_resources', /* (http://codex.wordpress.org/Function_Reference/register_post_type) */
		// let's now add all the options for this post type
		array( 'labels' => array(
			'name' => __( 'Resources', 'pstheme' ), /* This is the Title of the Group */
			'singular_name' => __( 'Resource', 'pstheme' ), /* This is the individual type */
			'all_items' => __( 'All Resources', 'pstheme' ), /* the all items menu item */
			'add_new' => __( 'Add New', 'pstheme' ), /* The add new menu item */
			'add_new_item' => __( 'Add New Resource', 'pstheme' ), /* Add New Display Title */
			'edit' => __( 'Edit', 'pstheme' ), /* Edit Dialog */
			'edit_item' => __( 'Edit Resources', 'pstheme' ), /* Edit Display Title */
			'new_item' => __( 'New Resource', 'pstheme' ), /* New Display Title */
			'view_item' => __( 'View Resource', 'pstheme' ), /* View Display Title */
			'search_items' => __( 'Search Resources', 'pstheme' ), /* Search Custom Type Title */ 
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
			'rewrite'	=> array( 'slug' => 'resources', 'with_front' => false ), /* you can specify its url slug */
			'has_archive' => 'false', /* you can rename the slug here */
			'capability_type' => 'page',
			'hierarchical' => false,
			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions')
		) /* end of options */
	); /* end of register post type */
	
	/* this adds your post categories to your custom post type */
	register_taxonomy_for_object_type( 'category', 'ps_resource_types' );
	
}

	// adding the function to the Wordpress init
	add_action( 'init', 'ps_resources_pt');
	
	/*
	for more information on taxonomies, go here:
	http://codex.wordpress.org/Function_Reference/register_taxonomy
	*/
	
	// now let's add custom categories (these act like categories)
	register_taxonomy( 'ps_resource_types', 
		array('ps_resources'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
		array('hierarchical' => true,     /* if this is true, it acts like categories */
			'labels' => array(
				'name' => __( 'Type', 'pstheme' ), /* name of the custom taxonomy */
				'singular_name' => __( 'Type', 'pstheme' ), /* single taxonomy name */
				'search_items' =>  __( 'Search Types', 'pstheme' ), /* search title for taxomony */
				'all_items' => __( 'All Types', 'pstheme' ), /* all title for taxonomies */
				'parent_item' => __( 'Parent Type', 'pstheme' ), /* parent title for taxonomy */
				'parent_item_colon' => __( 'Parent Type:', 'pstheme' ), /* parent taxonomy title */
				'edit_item' => __( 'Edit Type', 'pstheme' ), /* edit custom taxonomy title */
				'update_item' => __( 'Update Type', 'pstheme' ), /* update title for taxonomy */
				'add_new_item' => __( 'Add New Type', 'pstheme' ), /* add new title for taxonomy */
				'new_item_name' => __( 'New Type Name', 'pstheme' ) /* name title for taxonomy */
			),
			'show_admin_column' => true, 
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'resources/type' ),
		)
	);
?>
