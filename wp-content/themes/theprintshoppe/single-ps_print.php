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

						<?php	if(have_posts()) : while(have_posts()) : the_post(); the_content(); endwhile; endif; ?>


					</main>

				</div>

			</div>

<?php get_footer(); ?>
