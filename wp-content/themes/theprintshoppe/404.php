<?php get_header(); ?>

				
			</header>

			<div id="content">

				<div id="inner-content">


					<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
						
						<header class="entry-header">

							<h1><?php _e( 'Epic 404 - Article Not Found', 'pstheme' ); ?></h1>

						</header>

						<article id="post-not-found" class="hentry">


							<section class="one-half entry-content">

								<p><?php _e( 'The article you were looking for was not found, but maybe try looking again!', 'pstheme' ); ?></p>

								<p><?php get_search_form(); ?></p>

							</section>

							<section class="one-half 404-links">
								<h3>What's Next?</h3>
								<ul>
									<li>Go back to our <a href="<?php bloginfo('url'); ?>" title="Back to the Home Page">home page</a></li>
									<li>Read an article on <a href="<?php bloginfo('url'); ?>/insights" title="Back to the Home Page">our blog</a></li>
									<li>Request a quote for your <a href="#proposal" data-featherlight title="Request a Quote">print job</a></li>
									<li>Give us a call at <a href="tel:5123289206" title="Phone number">(512) 328-9206</a></li>
									<li>
										<?php
											$tips = array (
												'Eat a <a href="http://www.tiffstreats.com/Locations/Austin/Southwest-Austin.aspx" title="The Best Cookies in Austin" target="_blank">fresh baked cookie</a>',
												'Eat some <a href="http://www.itsallgoodbarbq.com/contact.html" title="It\'s All Good BBQ in Spicewood, Texas" target="_blank">delicious Texas BBQ</a>',
												'Grab a slice of <a href="http://www.conanspizza.com/" target="_blank" title="Conan\'s Pizza">deep dish pizza</a>',
												'Take a <a href="http://www.dancebycarly.com/" target="_blank" title="Dance by Carly">dance class</a>',
												'Drink a beer <a href="http://www.chuys.com/" target="_blank" title="Chuy\'s">with Elvis</a>',
												'Enjoy a <a href="http://www.hopdoddy.com/" target="_blank" title="Hopdoddy Burger Bar">burger + beer</a>',
												'Catch a view of <a href="http://www.hulahut.com/" target="_blank" title="Hula Hut">Lake Austin</a>',
											);
											$rand_key = array_rand($tips, 1);
											echo $tips[$rand_key];
										?>
									</li>
								</ul>
									

							</section>

						</article>

					</main>

				</div>

			</div>

<?php get_footer(); ?>
