<?php get_header(); ?>

			<div id="content">

				<div id="inner-content">

					<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

						<article id="post-not-found" class="hentry">

							<header class="article-header">

								<h1><?php _e( 'Epic 404 - Article Not Found', 'tpstheme' ); ?></h1>

							</header>

							<section class="entry-content">

								<p><?php _e( 'The article you were looking for was not found, but maybe try looking again!', 'tpstheme' ); ?></p>

							</section>

							<section class="search">

									<p><?php get_search_form(); ?></p>

							</section>

							<footer class="article-footer">

									<p><?php _e( 'This is the 404.php template.', 'tpstheme' ); ?></p>

							</footer>

						</article>

					</main>

				</div>

			</div>

<?php get_footer(); ?>
