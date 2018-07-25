<?php get_header(); ?>
				


				<header class="entry-header">
					<div class="entry-image" data-featherlight-gallery data-featherlight-filter="a">
						<?php if(get_field('ps_product_featured_image')) :
							$entry_image = get_field('ps_product_featured_image'); ?>

							<div class="product-featured-image">
								<a href="<?php echo $entry_image['url']; ?>"><img src="<?php echo $entry_image['url']; ?>" /></a>
							</div>
							<div class="product-supporting-images">
								<?php 

								$images = get_field('ps_product_supporting_images');

								if( $images ): ?>
							        <?php foreach( $images as $image ): ?>
						                <a href="<?php echo $image['url']; ?>">
						                     <img src="<?php echo $image['sizes']['thumbnail']; ?>" alt="<?php echo $image['alt']; ?>" />
						                </a>
							        <?php endforeach; ?>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="entry-info">
						<?php if (get_field('ps_product_custom_title')) : ?>
							<h4 class="product-title" itemprop="headline"><?php the_title(); ?></h4>
							<h1 class="page-title has-product-title" itemprop="headline"><?php echo get_field('ps_product_custom_title'); ?></h1>
						<?php else : ?>
							<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>
						<?php endif; ?>
						<div class="cf"></div>
						<?php if (get_field('ps_product_blurb')) : ?>
							<div class="subhead">								

								<?php if(get_field('ps_product_story')) : ?>

									<?php echo ps_product_add_keep_reading_link('ps_product_blurb'); ?>
									<?php echo get_field('ps_product_blurb_support'); ?>
								<?php else : ?>

									<?php echo get_field('ps_product_blurb'); ?>
									<?php echo get_field('ps_product_blurb_support'); ?>

								<?php endif; ?>
							</div>
						<?php endif; ?>
						<p class="entry-info-cta"><a href="<?php bloginfo('url'); ?>/proposal" class="button button-alt button-cta">Request A Proposal</a></p>
					</div>
				</header>

			</header>

			<div id="content">

				<div id="inner-content">
					<?php if(have_rows('ps_product_keys')) : ?>

						<div class="product-keys">

						<div id="keys" class="product-keys-wrapper">

							<?php while( have_rows('ps_product_keys')) : the_row(); ?>

								<div class="product-key-row">
									<div class="product-key-icon">
										<?php if(get_sub_field('ps_product_keys_icon')) : echo get_sub_field('ps_product_keys_icon'); endif; ?>
									</div>
									<div class="product-key-text">
										<?php if(get_sub_field('ps_product_keys_text')) : echo get_sub_field('ps_product_keys_text'); endif; ?>
									</div>
								</div>

							<?php endwhile; ?>

							</div>

						</div>

					<?php endif;?>

					<aside id="process" class="print-process">

							<p>We view every project as an opportunity to showcase the value print can bring to your brand. <strong>Clients like you trust us to guide them to finding that value.</strong> Every project is treated with care and is put through our meticulous process:</p>

							<?php ps_part_print_process(); ?>

						</aside>

					<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

						<?php	if(have_posts()) : while(have_posts()) : the_post(); the_content(); endwhile; endif; ?>

					</main>

				</div>

			</div>

			<div class=""

<?php get_footer(); ?>
