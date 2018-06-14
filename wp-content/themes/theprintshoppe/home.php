<?php get_header(); ?>

				<header class="entry-header">
					<h1 class="page-title" itemprop="headline">Insights</h1>
					<div class="cf"></div>
					<div class="subhead">
						<p>Intro to our blog</p>
					</div>
				</header>

			</header>

			<div id="content">

				<div id="inner-content">

						<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">


							
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class( ); ?> role="article">

								<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
									<?php

									if(has_post_thumbnail(get_the_ID())) :
										$thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'full');
									endif;

									?>
									<div class="post-thumbnail" <?php if(isset($thumbnail)) : ?>has-thumbnail<?php endif; ?>" role="banner" itemscope itemtype="http://schema.org/WPHeader" <?php if(isset($thumbnail)) : ?> style="background: no-repeat url('<?php echo $thumbnail; ?>'); background-size: cover;" <?php endif; ?>></div>
									<div class="article-content">
										<!-- Display the Title as a link to the Post's permalink. -->
										<h4><?php the_title(); ?></h4>

										<!-- Display the date (November 16th, 2009 format) and a link to other posts by this posts author. -->
										<small><?php the_time( 'F jS, Y' ); ?> by <?php the_author(); ?></small>
										 
										<div class="entry">
										  	<?php if(get_field('ps_subhead')) :
										  		echo get_field('ps_subhead');
										  	else :
										  		// the_excerpt(); 
										  	endif; ?>
										</div>

										<?php 
										  	$cats = get_the_category();
										  	$i = 0;
										  	$cat_len = count($cats);

										?>

										<p class="postmetadata"><?php esc_html_e( 'Posted in' ); ?> <?php foreach($cats as $category) : if($i == $cat_len - 1) : echo $category->cat_name; else : echo $category->cat_name . ', '; endif; $i++; endforeach; 	?></p>

										<?php unset($thumbnail); ?>
									</div>
									<i class="fa fa-chevron-right arrow"></i>
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

					<?php get_sidebar(); ?>

				</div>

			</div>

<?php get_footer(); ?>