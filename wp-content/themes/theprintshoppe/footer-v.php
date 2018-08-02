
		</div> <?php // end container ?>
			
		<footer class="site-footer" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">

			<div id="map">
			</div>

			<div class="inner-footer">

				<div class="inner-footer-wrap">

					<div class="footer-column footer-column-logo-about">
						<div class="footer-logo">
							<a href="<?php echo home_url(); ?>" rel="nofollow"><img itemprop="logo" class="main-logo-footer" src="<?php bloginfo('stylesheet_directory'); ?>/library/images/print-shoppe-austin-tx.svg" /></a>
						</div>
						<div class="footer-blurb">
							<p>We fuel massive growth for your brand using the most proven marketing channel: print &amp; direct mail.</p>
							<p><a href="<?php bloginfo('url'); ?>/company" class="link-cta">More About Us</i></a></p>
						</div>
						<div class="footer-important-info">
							<a href="<?php bloginfo('url'); ?>/privacy/">Our Privacy Policy</a> <br />
							<a href="<?php bloginfo('url'); ?>/terms/">Terms + Conditions</a> 
						</div>
					</div>
					<div class="footer-column footer-column-services">
						<h3>What We Do</h3>
						<div class="footer-services-nav">
							<nav role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
								<?php wp_nav_menu(array(
		    					         'container' => false,                           // remove nav container
		    					         'container_class' => 'menu cf',                 // class of container (should you choose to use it)
		    					         'menu' => __( 'Footer Services Menu', 'pstheme' ),  // nav name
		    					         'menu_class' => 'nav footer-services-nav cf',               // adding custom nav class
		    					         'theme_location' => 'footer-services-nav',                 // where it's located in the theme
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
					<div class="footer-column footer-column-resources">
						<h3>Resources</h3>
						<div class="footer-resources-nav">
							<nav role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
								<?php wp_nav_menu(array(
		    					         'container' => false,                           // remove nav container
		    					         'container_class' => 'menu cf',                 // class of container (should you choose to use it)
		    					         'menu' => __( 'Footer Resources Menu', 'pstheme' ),  // nav name
		    					         'menu_class' => 'nav footer-resources-nav cf',               // adding custom nav class
		    					         'theme_location' => 'footer-resources-nav',                 // where it's located in the theme
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
					<div class="omega footer-column footer-column-contact">
						<h3>Contact Us</h3>
						<p class="footer-contact-cta"><a href="<?php bloginfo('url'); ?>/proposal" class="button button-small">Request a Proposal</a> <a href="<?php bloginfo('url'); ?>/upload" class="button button-alt button-ghost button-small">Upload a File</a></p>
						<p class="footer-contact-row email"><i class="fas fa-at"></i><a href="mailto:sales(at)printshoppe(dot)net">sales@printshoppe.net</a></p>
						<p class="footer-contact-row phone"><i class="fas fa-phone"></i>512-328-9206</p>
						<p class="footer-contact-row address"><i class="fas fa-map-marker-alt"></i>5321 Industrial Oaks Blvd,<br />Suite 128<br />Austin, TX 78735</p>
					</div>
				</div>
			</div>

		</footer>

		<?php // all js scripts are loaded in library/bones.php ?>
		<?php wp_footer(); ?>
		<?php 
		global $post;

		if(get_field('ps_product_story')) : ?>
			<div id="fl-product-story" class="lightbox-container">
				<h2 class="story-header"><?php the_title(); ?> / <span><?php echo get_field('ps_product_story_title'); ?></span></h2>
				<?php echo get_field('ps_product_story'); ?>
		</div>
		<?php endif; ?>
		<div id="printShoppeIcon"><img src="<?php bloginfo('stylesheet_directory'); ?>/library/images/print-shoppe-icon.svg" /><p>5321 Industrial Oaks Blvd<br />Suite 128<br />Austin, TX 78735<br /><a href="https://www.google.com/maps/place/The+Print+Shoppe/@30.2389098,-97.8409883,18z/data=!4m5!3m4!1s0x0:0xb6f7d70d253f9473!8m2!3d30.2388481!4d-97.8397221" target="_blank">Directions</a></p></div>
		<script>
			var mySVGsToInject = document.querySelectorAll('img.main-logo');
			SVGInjector(mySVGsToInject);
			var mySVGsToInject2 = document.querySelectorAll('img.main-logo-footer');
			SVGInjector(mySVGsToInject2);
			
			jQuery(document).ready(function($) {
				$('.sub-menu .menu-item > a').click(function() {
					window.location = $(this).attr('href');
				});

				$('.client-gallery').slick({
				  	infinite: true,
				  	slidesToShow: 4,
				  	slidesToScroll: 4,
				  	autoplay: true,
  					autoplaySpeed: 2000,
  					arrows: false,
  					dots: false,
				});

				$('.slider').slick({
				  	infinite: false,
				  	slidesToShow: 3,
				  	slidesToScroll: 1,
  					arrows: true,
  					dots: true,
  					centerMode: true,
  					variableWidth: true
				});

				<?php if(get_the_ID() === 4115) : ?>

					$('.timer').each(count);
					function count(options) {
				    	var $this = $(this);
				    	options = $.extend({}, options || {}, $this.data('countToOptions') || {});
				    	$this.countTo(options);
				    }

				    $('.direct-mail-use-cases ul li h5').on('click', function() {
				    	$(this).parent().toggleClass('open');
				    	$(this).siblings('p').slideToggle();
				    });

				<?php endif; ?>

				<?php if(get_the_ID() === 6) : ?>

				    $('.direct-mail-use-cases ul li h5').on('click', function() {
				    	$(this).parent().toggleClass('open');
				    	$(this).siblings('p').slideToggle();
				    });

				    $(".proof-approval").change(function() {
					    if(this.checked) {
					        $('.cart-collaterals').toggleClass('approved');
					    } else {
					    	$('.cart-collaterals').toggleClass('approved');
					    }
					});

				<?php endif; ?>
			});
		</script>

	</body>

</html> <!-- end of site. what a ride! -->
