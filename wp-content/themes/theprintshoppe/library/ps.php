<?php
/* Welcome to Bones :)
This is the core Bones file where most of the
main functions & features reside. If you have
any custom functions, it's best to put them
in the functions.php file.

Developed by: Eddie Machado
URL: http://themble.com/bones/

  - head cleanup (remove rsd, uri links, junk css, ect)
  - enqueueing scripts & styles
  - theme support functions
  - custom menu output & fallbacks
  - related post function
  - page-navi function
  - removing <p> from around images
  - customizing the post excerpt

*/

/*********************
WP_HEAD GOODNESS
The default wordpress head is
a mess. Let's clean it up by
removing all the junk we don't
need.
*********************/

function ps_head_cleanup() {
	// category feeds
	// remove_action( 'wp_head', 'feed_links_extra', 3 );
	// post and comment feeds
	// remove_action( 'wp_head', 'feed_links', 2 );
	// EditURI link
	remove_action( 'wp_head', 'rsd_link' );
	// windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// WP version
	remove_action( 'wp_head', 'wp_generator' );
	// remove emojis
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	// remove WP version from css
	add_filter( 'style_loader_src', 'ps_remove_wp_ver_css_js', 9999 );
	// remove Wp version from scripts
	add_filter( 'script_loader_src', 'ps_remove_wp_ver_css_js', 9999 );

} /* end bones head cleanup */

// A better title
// http://www.deluxeblogtips.com/2012/03/better-title-meta-tag.html
function rw_title( $title, $sep, $seplocation ) {
  global $page, $paged;

  // Don't affect in feeds.
  if ( is_feed() ) return $title;

  // Add the blog's name
  if ( 'right' == $seplocation ) {
    $title .= get_bloginfo( 'name' );
  } else {
    $title = get_bloginfo( 'name' ) . $title;
  }

  // Add the blog description for the home/front page.
  $site_description = get_bloginfo( 'description', 'display' );

  if ( $site_description && ( is_home() || is_front_page() ) ) {
    $title .= " {$sep} {$site_description}";
  }

  // Add a page number if necessary:
  if ( $paged >= 2 || $page >= 2 ) {
    $title .= " {$sep} " . sprintf( __( 'Page %s', 'dbt' ), max( $paged, $page ) );
  }

  return $title;

} // end better title

// remove WP version from RSS
function ps_rss_version() { return ''; }

// remove WP version from scripts
function ps_remove_wp_ver_css_js( $src ) {
	if ( strpos( $src, 'ver=' ) )
		$src = remove_query_arg( 'ver', $src );
	return $src;
}

// remove injected CSS for recent comments widget
function ps_remove_wp_widget_recent_comments_style() {
	if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
		remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
	}
}

// remove injected CSS from recent comments widget
function ps_remove_recent_comments_style() {
	global $wp_widget_factory;
	if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
		remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
	}
}

// remove injected CSS from gallery
function ps_gallery_style($css) {
	return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );
}

// remove query strings from static resources
function _remove_script_version( $src ){ 
	$parts = explode( '?', $src ); 	
	return $parts[0]; 
} 
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 ); 
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );


/*********************
SCRIPTS & ENQUEUEING
*********************/

// loading modernizr and jquery, and reply script
function ps_scripts_and_styles() {

  global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way

  if (!is_admin()) {

		// modernizr (without media query polyfill)
		wp_register_script( 'ps-modernizr', get_stylesheet_directory_uri() . '/library/js/libs/modernizr.custom.min.js', array(), '2.5.3', false );

		// register main stylesheet
		wp_register_style( 'ps-stylesheet', get_stylesheet_directory_uri() . '/library/css/style.css', array(), '', 'all' );

		// register main stylesheet
		// wp_register_style( 'ps-fontawesome', '//use.fontawesome.com/releases/v5.0.13/css/all.css', array(), '', 'all' );

		// ie-only style sheet
		wp_register_style( 'ps-ie-only', get_stylesheet_directory_uri() . '/library/css/ie.css', array(), '' );

    // comment reply script for threaded comments
    if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
		  wp_enqueue_script( 'comment-reply' );
    }

    	/**********
    	**
    	** IMPORTANT:
    	** ADD DEFER AND ASYNC ATTRIBUTES USING THE FILTER ON LINE 396 & 409
    	**
    	**********/

		//adding scripts file in the footer
		wp_register_script( 'ps-js', get_stylesheet_directory_uri() . '/library/js/scripts.min.js', array( 'jquery' ), '', true );

		//adding svginjector file in the footer
		wp_register_script( 'ps-svg-injector', get_stylesheet_directory_uri() . '/library/js/svg-injector.min.js', array( 'jquery' ), '', true );

		//adding svginjector file in the footer
		wp_register_script( 'ps-gmaps', get_stylesheet_directory_uri() . '/library/js/gmaps.min.js', array( 'jquery' ), '', true );

		//adding selectFx file in the footer
		wp_register_script( 'ps-classie-js', get_stylesheet_directory_uri() . '/library/js/classie.min.js', array( 'jquery' ), '', true );
		wp_register_script( 'ps-select-fx-js', get_stylesheet_directory_uri() . '/library/js/selectFx.min.js', array( 'jquery' ), '', true );

		//adding slick script & style
		wp_register_script( 'ps-slick-js', get_stylesheet_directory_uri() . '/library/slick/slick.min.js', array( 'jquery' ), '', true );
		wp_register_style( 'ps-slick-css', get_stylesheet_directory_uri() . '/library/slick/slick.css', array(), '', 'all' );
		wp_register_style( 'ps-slick-theme-css', get_stylesheet_directory_uri() . '/library/slick/slick-theme.css', array(), '', 'all' );

		//adding featherlight script & style
		wp_register_script( 'ps-featherlight-js', get_stylesheet_directory_uri() . '/library/featherlight/featherlight.min.js', array( 'jquery' ), '', true );
		wp_register_script( 'ps-featherlight-gallery-js', get_stylesheet_directory_uri() . '/library/featherlight/featherlight.gallery.min.js', array( 'jquery' ), '', true );
		wp_register_style( 'ps-featherlight-css', get_stylesheet_directory_uri() . '/library/featherlight/featherlight.min.css', array(), '', 'all' );
		wp_register_style( 'ps-featherlight-gallery-css', get_stylesheet_directory_uri() . '/library/featherlight/featherlight.gallery.min.css', array(), '', 'all' );
		
		// enqueue styles and scripts
		wp_enqueue_script( 'ps-modernizr' );
		wp_enqueue_style( 'ps-stylesheet' );		
		// wp_enqueue_style( 'ps-fontawesome' );
		wp_enqueue_style( 'ps-ie-only' );
		wp_enqueue_style( 'ps-slick-css' );
		wp_enqueue_style( 'ps-slick-theme-css' );
		wp_enqueue_style( 'ps-featherlight-css' );
		wp_enqueue_style( 'ps-featherlight-gallery-css' );

		$wp_styles->add_data( 'ps-ie-only', 'conditional', 'lt IE 9' ); // add conditional wrapper around ie stylesheet

		/*
		I recommend using a plugin to call jQuery
		using the google cdn. That way it stays cached
		and your site will load faster.
		*/
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'ps-js' );
		wp_enqueue_script( 'ps-svg-injector' );
		wp_enqueue_script( 'ps-gmaps' );
		wp_enqueue_script( 'ps-slick-js' );
		wp_enqueue_script( 'ps-featherlight-js' );
		wp_enqueue_script( 'ps-featherlight-gallery-js' );
		wp_enqueue_script( 'ps-classie-js' );
		wp_enqueue_script( 'ps-select-fx-js' );
	}
}

/*********************
THEME SUPPORT
*********************/

// Adding WP 3+ Functions & Theme Support
function ps_theme_support() {

	// wp thumbnails (sizes handled in functions.php)
	add_theme_support( 'post-thumbnails' );

	// default thumb size
	set_post_thumbnail_size(450, 450, true);

	// wp custom background (thx to @bransonwerner for update)
	add_theme_support( 'custom-background',
	    array(
	    'default-image' => '',    // background image default
	    'default-color' => '',    // background color default (dont add the #)
	    'wp-head-callback' => '_custom_background_cb',
	    'admin-head-callback' => '',
	    'admin-preview-callback' => ''
	    )
	);

	// rss thingy
	add_theme_support('automatic-feed-links');

	// to add header image support go here: http://themble.com/support/adding-header-background-image-support/

	// adding post format support
	add_theme_support( 'post-formats',
		array(
			'aside',             // title less blurb
			'gallery',           // gallery of images
			'link',              // quick link to other site
			'image',             // an image
			'quote',             // a quick quote
			'status',            // a Facebook like status update
			'video',             // video
			'audio',             // audio
			'chat'               // chat transcript
		)
	);

	// wp menus
	add_theme_support( 'menus' );

	// registering wp3+ menus
	register_nav_menus(
		array(
			'main-nav' => __( 'The Main Menu', 'pstheme' ),   // main nav in header
			'footer-services-nav' => __( 'Footer Services', 'pstheme' ), // secondary nav in footer
			'footer-resources-nav' => __( 'Footer Resources', 'pstheme' ) // secondary nav in footer
		)
	);

	// Enable support for HTML5 markup.
	add_theme_support( 'html5', array(
		'comment-list',
		'search-form',
		'comment-form'
	) );

} /* end bones theme support */


/*********************
RELATED POSTS FUNCTION
*********************/

// Related Posts Function (call using ps_related_posts(); )
function ps_related_posts() {
	echo '<ul id="ps-related-posts">';
	global $post;
	$tags = wp_get_post_tags( $post->ID );
	if($tags) {
		foreach( $tags as $tag ) {
			$tag_arr .= $tag->slug . ',';
		}
		$args = array(
			'tag' => $tag_arr,
			'numberposts' => 5, /* you can change this to show more */
			'post__not_in' => array($post->ID)
		);
		$related_posts = get_posts( $args );
		if($related_posts) {
			foreach ( $related_posts as $post ) : setup_postdata( $post ); ?>
				<li class="related_post"><a class="entry-unrelated" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
			<?php endforeach; }
		else { ?>
			<?php echo '<li class="no_related_post">' . __( 'No Related Posts Yet!', 'pstheme' ) . '</li>'; ?>
		<?php }
	}
	wp_reset_postdata();
	echo '</ul>';
} /* end bones related posts function */

/*********************
PAGE NAVI
*********************/

// Numeric Page Navi (built into the theme by default)
function ps_page_navi() {
  global $wp_query;
  if($wp_query->max_num_pages > 1) :
  	echo '<div class="load-more-button"><a class="button button-ghost">Load More</a></div>';
  endif;
} /* end page navi */

// Ajax Load More
function ps_load_more_scripts() {
	global $wp_query;

	wp_register_script('ps_load_more', get_stylesheet_directory_uri() . '/library/js/load-more.min.js', array('jquery'));

	wp_localize_script('ps_load_more', 'ps_load_more_params' , array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php',
		'posts' => json_encode($wp_query->query_vars),
		'current_page' => get_query_var('paged') ? get_query_var('paged') : 1,
		'max_page' => $wp_query->max_num_pages
	));

	wp_enqueue_script('ps_load_more');
};
add_action( 'wp_enqueue_scripts', 'ps_load_more_scripts' );

function ps_load_more_ajax_handler(){
 
	// prepare our arguments for the query
	$args = json_decode( stripslashes( $_POST['query'] ), true );
	$args['paged'] = $_POST['page'] + 1; // we need next page to be loaded
	$args['post_status'] = 'publish';
 
	// it is always better to use WP_Query but not here
	query_posts( $args );
 
	if( have_posts() ) :
 
		// run the loop
		while( have_posts() ): the_post(); ?>
 
			<article id="post-<?php the_ID(); ?>" <?php post_class( ); ?> role="article">

				<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
					<?php

					if(has_post_thumbnail(get_the_ID())) :
						$thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'full');
					endif;

					?>
					<div class="post-thumbnail" <?php if(isset($thumbnail)) : ?>has-thumbnail<?php endif; ?>" role="banner" itemscope itemtype="http://schema.org/WPHeader" <?php if(isset($thumbnail)) : ?> style="background: no-repeat url('<?php echo $thumbnail; ?>'); background-size: cover;" <?php endif; ?>></div>
					<div class="article-content">
						<!-- Display the Title as a link to the Post's permalink. -->
						<h4><?php the_title(); ?></h4>

						<!-- Display the date (November 16th, 2009 format) and a link to other posts by this posts author. -->
						<small><?php the_time( 'F jS, Y' ); ?> by <?php the_author(); ?></small>
						 
						<div class="entry">
						  	<?php if(get_field('ps_subhead')) :
						  		echo get_field('ps_subhead');
						  	else :
						  		// the_excerpt(); 
						  	endif; ?>
						</div>

						<?php 
						  	$cats = get_the_category();
						  	$i = 0;
						  	$cat_len = count($cats);

						?>

						<p class="postmetadata"><?php esc_html_e( 'Posted in' ); ?> <?php foreach($cats as $category) : if($i == $cat_len - 1) : echo $category->cat_name; else : echo $category->cat_name . ', '; endif; $i++; endforeach; 	?></p>

						<?php unset($thumbnail); ?>
					</div>
					<i class="fa fa-chevron-right arrow"></i>
				</a>

			</article>
 
 
		<?php endwhile;
 
	endif;
	die; // here we exit the script and even no wp_reset_query() required!
}
 
 
 
add_action('wp_ajax_loadmore', 'ps_load_more_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_loadmore', 'ps_load_more_ajax_handler'); // wp_ajax_nopriv_{action}

/*********************
RANDOM CLEANUP ITEMS
*********************/

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function ps_filter_ptags_on_images($content){
	return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// This removes the annoying […] to a Read More link
function ps_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '...  <a class="excerpt-read-more" href="'. get_permalink( $post->ID ) . '" title="'. __( 'Read ', 'pstheme' ) . esc_attr( get_the_title( $post->ID ) ).'">'. __( 'Read more &raquo;', 'pstheme' ) .'</a>';
}



add_filter('post_gallery', 'ps_post_gallery', 10, 2);
function ps_post_gallery($output, $attr) {
    global $post;

    if (isset($attr['orderby'])) {
        $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
        if (!$attr['orderby'])
            unset($attr['orderby']);
    }

    extract(shortcode_atts(array(
        'order' => 'ASC',
        'orderby' => 'menu_order ID',
        'id' => $post->ID,
        'itemtag' => 'dl',
        'icontag' => 'dt',
        'captiontag' => 'dd',
        'columns' => 3,
        'size' => 'thumbnail',
        'include' => '',
        'exclude' => ''
    ), $attr));

    $id = intval($id);
    if ('RAND' == $order) $orderby = 'none';

    if (!empty($include)) {
        $include = preg_replace('/[^0-9,]+/', '', $include);
        $_attachments = get_posts(array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));

        $attachments = array();
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    }

    if (empty($attachments)) return '';

    // Here's your actual output, you may customize it to your need
    $output = "<div class=\"slider slideshow-wrapper\">\n";
    // Now you loop through each attachment
    foreach ($attachments as $id => $attachment) {
        // Fetch the thumbnail (or full image, it's up to you)
//      $img = wp_get_attachment_image_src($id, 'medium');
//      $img = wp_get_attachment_image_src($id, 'my-custom-image-size');
        $img = wp_get_attachment_image_src($id, 'large');

        $output .= "<img src=\"{$img[0]}\" width=\"{$img[1]}\" height=\"{$img[2]}\" alt=\"\" />\n";
    }
    $output .= "</div>\n";

    return $output;
}

function ps_add_defer_attribute($tag, $handle) {
   // add script handles to the array below
   $scripts_to_defer = array('ps-gmaps');
   
   foreach($scripts_to_defer as $defer_script) {
      if ($defer_script === $handle) {
         return str_replace(' src', ' defer="defer" src', $tag);
      }
   }
   return $tag;
}
add_filter('script_loader_tag', 'ps_add_defer_attribute', 10, 2);

function ps_add_async_attribute($tag, $handle) {
   // add script handles to the array below
   $scripts_to_async = array();
   
   foreach($scripts_to_async as $async_script) {
      if ($async_script === $handle) {
         return str_replace(' src', ' async="async" src', $tag);
      }
   }
   return $tag;
}
add_filter('script_loader_tag', 'ps_add_async_attribute', 10, 2);


function ps_estimated_reading_time() {

    $post = get_post();

    $words = str_word_count( strip_tags( $post->post_content ) );
    $minutes = floor( $words / 120 );
    $seconds = floor( $words % 120 / ( 120 / 60 ) );

    if ( 1 <= $minutes ) {
        $estimated_time = $minutes . ' minute' . ($minutes == 1 ? '' : 's') . ', ' . $seconds . ' second' . ($seconds == 1 ? '' : 's');
    } else {
        $estimated_time = $seconds . ' second' . ($seconds == 1 ? '' : 's');
    }

    return $estimated_time;

}

add_filter('wp_nav_menu_objects', 'ps_wp_nav_menu_objects', 10, 2);
function ps_wp_nav_menu_objects( $items, $args ) {
	foreach( $items as &$item ) {
		$icon = get_field('ps_menu_item_icon', $item);
		$subtitle = get_field('ps_menu_item_subtitle', $item);
		if( $icon && $subtitle ) {	
			$item->title = '<div class="menu-item-has-icon-subtitle"><div class="menu-item-icon">' . $icon . '</div><div class="menu-item-content"><span class="menu-item-title">' . $item->title . '</span><span class="menu-item-subtitle">' . $subtitle . '</div></div>';
		} elseif ( $icon ) {
			$item->title = '<div class="menu-item-has-icon"><div class="menu-item-icon">' . $icon . '</div><span class="menu-item-title">' . $item->title . '</span></div>';
		} elseif ($subtitle) {
			$item->title = '<div class="menu-item-has-subtitle"><span class="menu-item-title">' . $item->title . '</span><span class="menu-item-subtitle">' . $subtitle . '</span></div>';
		}
	}
	return $items;	
}

add_filter( 'nav_menu_link_attributes', 'ps_add_featherlight_to_nav', 10, 3 );
function ps_add_featherlight_to_nav( $atts, $item, $args ) {
  global $post;
  $menu_item_ID = $item;
  $destination = $atts['href'];

  if (get_field('ps_menu_item_featherlight' , $item->ID)) {
    $atts['data-featherlight'] = $destination;
  }
  return $atts;
}



function ps_product_add_keep_reading_link( $field_name ) {
	
	$content_raw = wp_strip_all_tags(get_field( $field_name ));

	$content = wpautop($content_raw . ' <a data-featherlight="#fl-product-story" class="link-cta">Keep Reading</a>');
	
	return $content;
	
}

function ps_add_slug_body_class( $classes ) {
	global $post;
	if ( isset( $post ) ) {
		$classes[] = $post->post_type . '-' . $post->post_name;
	}
	return $classes;
}
add_filter( 'body_class', 'ps_add_slug_body_class' );