
		</div> <?php // end container ?>
			
		<footer class="site-footer" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">

			<div id="map">
			</div>

			<div class="inner-footer">

				<div c

			</div>

		</footer>

		<?php // all js scripts are loaded in library/bones.php ?>
		<?php wp_footer(); ?>

		<div id="proposal" class="lightbox-container">This div will be opened in a lightbox</div>
		<div id="printShoppeIcon"><img src="<?php bloginfo('stylesheet_directory'); ?>/library/images/print-shoppe-icon.svg" /><p>5321 Industrial Oaks Blvd<br />Suite 128<br />Austin, TX 78735<br /><a href="https://www.google.com/maps/place/The+Print+Shoppe/@30.2389098,-97.8409883,18z/data=!4m5!3m4!1s0x0:0xb6f7d70d253f9473!8m2!3d30.2388481!4d-97.8397221" target="_blank">Directions</a></p></div>
		<script>
			var mySVGsToInject = document.querySelectorAll('img.main-logo');
			SVGInjector(mySVGsToInject);

			jQuery(document).ready(function($) {
				$('.request-proposal a').featherlight({
					targetAttr: '#proposal'
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
			});
		</script>

	</body>

</html> <!-- end of site. what a ride! -->
