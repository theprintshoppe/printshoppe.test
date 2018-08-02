<?php get_header(); ?>
			
			<?php 
			$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); 
			?>
	
			<header class="entry-header">
				<h1 class="page-title"><?php echo $term->name; ?> Marketing Center</h1>
				<div class="subhead"><?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?></div>
			</header>

			</header>

			<div id="content">

				<div id="inner-content">

						<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
							
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class( ); ?> role="article">
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
									<?php

										if(has_post_thumbnail(get_the_ID())) :
											$thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'full');
										endif;

									?>
									<?php if(isset($thumbnail)) : ?>

										<div class="product-image">
											<img src="<?php echo $thumbnail; ?>" />
										</div>

									<?php endif; ?>

									<section class="entry-content">
										<h3 class="h2 entry-title"><?php the_title(); ?></h3>
									</section>
									<span class="product-link">Personalize <i class="fas fa-arrow-right"></i></span>
									<div class="icon">
										<i class="fas fa-chevron-right"></i>
									</div>
								</a>
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
