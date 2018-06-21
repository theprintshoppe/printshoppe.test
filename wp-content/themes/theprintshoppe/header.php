<!doctype html>
<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<?php // force Internet Explorer to use the latest rendering engine available ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php wp_title(''); ?></title>
		<?php // mobile meta (hooray!) ?>
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<?php // icons & favicons (for more: http://www.jonathantneal.com/blog/understand-the-favicon/) ?>
		<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-touch-icon.png">
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
		<!--[if IE]>
			<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
		<![endif]-->
		<?php // or, set /favicon.ico for IE10 win ?>
		<meta name="msapplication-TileColor" content="#484a9a">
		<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">
        <meta name="theme-color" content="#484a9a">
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
		<?php // wordpress head functions ?>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
		<?php wp_head(); ?>
		<?php // end of wordpress head ?>
	</head>
	<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
		<div id="container">
			<?php

			if(has_post_thumbnail(get_the_ID())) :
				$thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'full');
			endif;

			?>
			<header class="site-header <?php if(isset($thumbnail)) : ?>has-thumbnail<?php endif; ?>" role="banner" itemscope itemtype="http://schema.org/WPHeader" <?php if(isset($thumbnail)) : ?> style="background: no-repeat url('<?php echo $thumbnail; ?>'); background-size: cover;" <?php endif; ?>>
				<div class="inner-header">
					<div class="row top-nav">
						<p class="masthead-announcement"><strong>Now Available:</strong> Our detailed guide to direct response marketing, <em>Brand Hacking</em> <a href="#" class="link-cta">Download Now</a></p>
						<p class="masthead-links">
							<a href="#test">Sample Request</a>
							<a href="#test">Upload a File</a>
							<a href="#test">Make a Payment</a>
						</p>
					</div>
					<div class="row main-nav-wrapper">
						<div class="main-nav-inner-wrapper">
							<p id="logo" class="h1" itemscope itemtype="http://schema.org/Organization"><a href="<?php echo home_url(); ?>" rel="nofollow"><img itemprop="logo" class="main-logo" src="<?php bloginfo('stylesheet_directory'); ?>/library/images/print-shoppe-austin-tx.svg" /></a></p>
							<nav role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
								<?php wp_nav_menu(array(
		    					         'container' => false,                           // remove nav container
		    					         'container_class' => 'menu cf',                 // class of container (should you choose to use it)
		    					         'menu' => __( 'The Main Menu', 'pstheme' ),  // nav name
		    					         'menu_class' => 'nav main-nav cf',               // adding custom nav class
		    					         'theme_location' => 'main-nav',                 // where it's located in the theme
		    					         'before' => '',                                 // before the menu
		        			               'after' => '',                                  // after the menu
		        			               'link_before' => '',                            // before each link
		        			               'link_after' => '',                             // after each link
		        			               'depth' => 0,                                   // limit the depth of the nav
		    					         'fallback_cb' => ''                             // fallback function (if there is one)
								)); ?>

							</nav>
						</div>
					</div>
				</div>
			
