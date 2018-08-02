<?php get_header(); ?>

				<header class="entry-header">
					<h1 class="page-title">Newsroom</h1>
					<div class="subhead"><?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?></div>
				</header>

			</header>

			<div id="content">

				<div id="inner-content">

						<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
							
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class( ); ?> role="article">

								<header class="entry-header article-header">

									<h3 class="h2 entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
								
								</header>

								<section class="entry-content">

									<p><?php the_date(); ?></p>

								</section>

								<footer class="article-footer">
									<p><a href="<?php the_permalink(); ?>" class="link-cta">Read More</a></p>
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
												<p><?php _e( 'This is the error message in the archive.php template.', 'pstheme' ); ?></p>
										</footer>
									</article>

							<?php endif; ?>

						</main>

				</div>

			</div>

<?php get_footer(); ?>
