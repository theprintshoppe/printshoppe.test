<?php get_header(); ?>

				<header class="entry-header">
					<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>
					<div class="cf"></div>
					<?php if(get_field('ps_subhead')) : ?>

						<div class="subhead">
							<?php echo get_field('ps_subhead'); ?>
						</div>

					<?php endif; ?>

				</header>

			</header>

			<div id="content">

				<div id="inner-content">


						<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class( '' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

								<section class="entry-content" itemprop="articleBody">
									<?php
										// the content (pretty self explanatory huh)
										//the_content();
									?>
									<p class="large">Please return to your company's private store to continue ordering products.</p>
								</section> <?php // end article section ?>

							</article>

							<?php endwhile; endif; ?>

						</main>

						<!--<aside class="proposal-trust-factors">

							<img src="<?php bloginfo('stylesheet_directory'); ?>/library/images/print-shoppe-secure-server.svg" />
							
						</aside>-->

				</div>

			</div>

<?php get_footer(); ?>
