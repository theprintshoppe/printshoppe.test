<?php get_header(); ?>

			<div id="content">

				<div id="inner-content">

						<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class( '' ); ?> role="article">

								<header class="article-header">

									<h1 class="h2 entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
									<p class="byline entry-meta vcard">
                                                                        <?php printf( __( 'Posted', 'pstheme' ).' %1$s %2$s',
                       								/* the time the post was published */
                       								'<time class="updated entry-time" datetime="' . get_the_time('Y-m-d') . '" itemprop="datePublished">' . get_the_time(get_option('date_format')) . '</time>',
                       								/* the author of the post */
                       								'<span class="by">'.__( 'by', 'pstheme').'</span> <span class="entry-author author" itemprop="author" itemscope itemptype="http://schema.org/Person">' . get_the_author_link( get_the_author_meta( 'ID' ) ) . '</span>'
                    							); ?>
									</p>

								</header>

								<section class="entry-content">
									<?php the_content(); ?>
								</section>

								<footer class="article-footer">
									<p class="footer-comment-count">
										<?php comments_number( __( '<span>No</span> Comments', 'pstheme' ), __( '<span>One</span> Comment', 'pstheme' ), __( '<span>%</span> Comments', 'pstheme' ) );?>
									</p>


                 	<?php printf( '<p class="footer-category">' . __('filed under', 'pstheme' ) . ': %1$s</p>' , get_the_category_list(', ') ); ?>

                  <?php the_tags( '<p class="footer-tags tags"><span class="tags-title">' . __( 'Tags:', 'pstheme' ) . '</span> ', ', ', '</p>' ); ?>


								</footer>

							</article>

							<?php endwhile; ?>

									<?php ps_page_navi(); ?>

							<?php else : ?>

									<article id="post-not-found" class="hentry">
											<header class="article-header">
												<h1><?php _e( 'Oops, Post Not Found!', 'pstheme' ); ?></h1>
										</header>
											<section class="entry-content">
												<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'pstheme' ); ?></p>
										</section>
										<footer class="article-footer">
												<p><?php _e( 'This is the error message in the index.php template.', 'pstheme' ); ?></p>
										</footer>
									</article>

							<?php endif; ?>


						</main>

					<?php get_sidebar(); ?>

				</div>

			</div>


<?php get_footer(); ?>
