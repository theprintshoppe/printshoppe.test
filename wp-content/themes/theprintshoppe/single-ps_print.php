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
						<?php if (get_field('ps_product_blurb') || get_field('ps_product_blurb_support')) : ?>
							<div class="subhead">								
								<?php if(get_field('ps_product_story') && get_field('ps_product_blurb')) : ?>

									<?php echo ps_product_add_keep_reading_link('ps_product_blurb'); ?>
									<?php echo get_field('ps_product_blurb_support'); ?>

								<?php elseif(get_field('ps_product_story') && !get_field('ps_product_blurb')) : ?>

									<a data-featherlight="#fl-product-story" class="link-cta">Read the story</a>
									<?php echo get_field('ps_product_blurb_support'); ?>

								<?php else : ?>

									<?php echo get_field('ps_product_blurb'); ?>
									<?php echo get_field('ps_product_blurb_support'); ?>

								<?php endif; ?>
							</div>
						<?php endif; ?>
						<p class="entry-info-cta">
							<a href="<?php bloginfo('url'); ?>/proposal" class="button button-alt button-cta">Request A Proposal</a>
							<?php if( have_rows('ps_product_templates') ) : ?>
								<a class="template-link" href="#ps_product_templates">Download Templates</a>
							<?php endif; ?>
						</p>
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

					<div class="content-wrapper">

						<aside id="process" class="print-process">

								<p>We view every project as an opportunity to showcase the value print can bring to your brand. <strong>Clients like you trust us to guide them to finding that value.</strong> Every project is treated with care and is put through our meticulous process:</p>

								<?php ps_part_print_process(); ?>

							</aside>

						<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

							<?php	if(have_posts()) : while(have_posts()) : the_post(); the_content(); endwhile; endif; ?>

							<?php global $post;

								if( have_rows('ps_product_templates') ) :
									echo '<div class="ps-product-templates-section">';
										echo '<h2 id="ps_product_templates" class="ps-product-templates-header">Product Templates</h2>';
										echo '<ul class="ps-product-template-list">';
										while ( have_rows('ps_product_templates') ) : the_row(); 

											// $ps_template_file_url = get_sub_field('ps_product_templates_file');
											$ps_template_description = get_sub_field('ps_product_templates_description');
											$ps_template_icon = get_sub_field('ps_product_templates_icon');

											?>

											<li class="ps-product-template-item">
													<div class="ps-product-template-item-icon">
														<?php switch ($ps_template_icon) :

															case 'Acrobat PDF': ?>
																<img src="<?php bloginfo('stylesheet_directory'); ?>/library/images/file-icons/icn_pdf.svg" />
															<?php break;

															case 'InDesign': ?>
																<img src="<?php bloginfo('stylesheet_directory'); ?>/library/images/file-icons/icn_indd.svg" />
															<?php break;

															case 'Illustrator': ?>
																<img src="<?php bloginfo('stylesheet_directory'); ?>/library/images/file-icons/icn_ai.svg" />
															<?php break;

															case 'Photoshop': ?>
																<img src="<?php bloginfo('stylesheet_directory'); ?>/library/images/file-icons/icn_psd.svg" />
															<?php break;

														endswitch; ?>

													</div>
													<div class="ps-product-template-item-description">
														<p><?php echo $ps_template_description; ?></p>
														<?php if( have_rows('ps_product_template_group_files') ) : ?>

															<?php while ( have_rows('ps_product_template_group_files') ) : the_row();

																$file_url = get_sub_field('ps_product_template_file');
																$file_description = get_sub_field('ps_product_template_file_description');
																?>
																<a href="<?php echo $file_url['url']; ?>" target="_blank"><?php echo $file_description; ?></a>

															<?php endwhile; ?>

														<?php endif; ?>
													</div>
											</li>											
										
										<?php endwhile;
										echo '</ul>';
									echo '</div>';
								endif;	?>
						</main>

					</div>

				</div>

			</div>

<?php get_footer(); ?>
