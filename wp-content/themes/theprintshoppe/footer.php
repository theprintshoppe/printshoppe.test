
		</div> <?php // end container ?>
			
		<footer class="site-footer" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">

			<div id="map">
				<p>Map</p>
			</div>

			<div class="inner-footer">

				<nav role="navigation">
					<?php wp_nav_menu(array(
					'container' => 'div',                           // enter '' to remove nav container (just make sure .footer-links in _base.scss isn't wrapping)
					'container_class' => 'footer-links cf',         // class of container (should you choose to use it)
					'menu' => __( 'Footer Links', 'pstheme' ),   // nav name
					'menu_class' => 'nav footer-nav cf',            // adding custom nav class
					'theme_location' => 'footer-links',             // where it's located in the theme
					'before' => '',                                 // before the menu
					'after' => '',                                  // after the menu
					'link_before' => '',                            // before each link
					'link_after' => '',                             // after each link
					'depth' => 0,                                   // limit the depth of the nav
					'fallback_cb' => 'ps_footer_links_fallback'  // fallback function
					)); ?>
				</nav>

				<p class="source-org copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>.</p>

			</div>

		</footer>

		<?php // all js scripts are loaded in library/bones.php ?>
		<?php wp_footer(); ?>

		<script>
			// Elements to inject
var mySVGsToInject = document.querySelectorAll('img.main-logo');

// Do the injection
SVGInjector(mySVGsToInject);
		</script>

	</body>

</html> <!-- end of site. what a ride! -->
