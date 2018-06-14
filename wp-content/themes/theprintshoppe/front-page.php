<?php get_header(); ?>
				
				<header class="entry-header masthead-wrapper">
					<div class="masthead-title">
						<h1 class="masthead-headline">A really catching and witty, high converting headline goes here.</h1>
						<p class="masthead-subhead">oioijaeopfjeofj oijf poaeijf oejf</p>
					</div>
					<div class="masthead-image">
						<img src="<?php bloginfo('stylesheet_directory'); ?>/library/images/home-icon-pattern.svg" />
					</div>
				</header>

			</header>

			<div id="content">

				<div id="inner-content">

						<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

							<div class="main-cta-wrapper">
								<div class="main-cta printssentials-cta">
									<h2>Printssentials</h2>
									<p>eijeiofj</p>
									<p>oijef</p>
									<p class="link"><a href="#" class="button button-small button-cta">Learn More</a></p>
								</div>
								<div class="main-cta acquisition-cta">
									<h2>Acquisition</h2>
									<p>eijeiofj</p>
									<p>oijef</p>
									<p class="link"><a href="#" class="button button-small button-cta">Learn More</a></p>
								</div>
								<div class="main-cta engagement-cta">
									<h2>Engagement</h2>
									<p>eijeiofj</p>
									<p>oijef</p>
									<p class="link"><a href="#" class="button button-small button-cta">Learn More</a></p>
								</div>
								<div class="main-cta branding-cta">
									<h2>Branding</h2>
									<p>eijeiofj</p>
									<p>oijef</p>
									<p class="link"><a href="#" class="button button-small button-cta">Learn More</a></p>
								</div>
							</div>

							<div class="home-section home-journey">
								<h2>The Value Journey</h2>
							</div>	

							<div class="home-section home-clients">
								<div class="home-clients-wrapper">
									<h2 class="home-clients-heading">Clients Who Trust Us:</h2>
									<div class="client-gallery">
										<?php 
										$clients = get_field('ps_client_gallery');

										if($clients): ?>

											<?php foreach($clients as $client) :?>

												<div>
													<img src="<?php echo $client['sizes']['ps-client']; ?>" alt="<?php echo $client['alt']; ?>" />
												</div>

											<?php endforeach; ?>

										<?php endif; ?>
									</div>
								</div>
							</div>

							<div class="home-section home-about-subscribe">
								<div class="home-about-subscribe-wrap">
									<div class="home-about">
										<h2>About the Print Shoppe</h2>
										<p>Print Shope poeifeo fopai paeijfpoia foje foijapoe fpoiaj epof afe jpoaij efopa jefopij aojeoifj oiajeoifj opaij efopijaoejf poaije opifj aoeij fpoaije fopija eopifj aoie jfoiaj eofij apoeij fpoaij efopia jeof ijapoiej fpoij apoei jfapoiej fpoaejf  japo efpfo iajf</p>
										<p><a href="<?php bloginfo('url'); ?>/company" class="button button-small button-cta">Learn More About Us</a></p>
									</div>
									<div class="home-subscribe">
										<h2>Join Thousands of Smart Marketers</h2>
										<p>Sign up for our bi-weekly BrandHack email to gain powerful marketing knowledge to grow your small business brand.</p>
										<div class="optin-form">
											<form>
												<input type="text" placeholder="First Name" class="fname"/><input type="text" placeholder="Last Name" class="lname"/>
												<input type="email" placeholder="Your Email Address" />
												<div class="submit-button">
													<input type="submit" value="Join BrandHack" class="button button-cta"/>
												</div>
												<div class="disclaimer">
													<p>Don't worry &mdash; we hate spam too, unless it's fried. Learn about how we guard your privacy <a href="<?php bloginfo('url'); ?>/privacy">here</a>.</p>
												</div>
										</div>
									</div>
								</div>
							</div>

							<div class="home-section home-insights-news-events">
								<div class="home-insights-news-events-wrap">
									<div class="home-insights">
										<h2>Latest Insights</h2>
										<?php $query = new WP_Query( 'post_type=post&posts_per_page=3' ); ?>
										 <?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>

											 <div class="post">
											 	<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
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
												
												<i class="fa fa-chevron-right arrow"></i>
												</a>
											 </div> <!-- closes the first div box -->

										 <?php endwhile; 
										 
										 wp_reset_postdata();

										 else : ?>
											
											<p><?php esc_html_e( 'Sorry, no posts matched your criteria.' ); ?></p>
										 
										 <?php endif; ?>

									</div>
									<div class="home-news-events-wrap">
										<div class="home-news">
											<h2>Recent News</h2>
											<?php $query = new WP_Query( 'post_type=ps_news&posts_per_page=5' ); ?>
											 <?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>

											 <div class="news">
											
												 <!-- Display the Title as a link to the Post's permalink. -->
												 <h4><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
												  <?php 
												  	$cats = get_the_terms('', 'ps_news_types');
												  ?>
												  <p class="postmetadata"><?php echo $cats[0]->name; ?></p>
											 </div> <!-- closes the first div box -->

											 <?php endwhile; 
											 
											 wp_reset_postdata();
											 else : ?>
												<p><?php esc_html_e( 'Sorry, no posts matched your criteria.' ); ?></p>
											 <?php endif; ?>
										</div>
										<div class="home-events">
											<h2>Upcoming Events</h2>
											<?php $query = new WP_Query( 'post_type=events&posts_per_page=5' ); ?>
											 <?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>

											 <div class="post">
											 
											 <!-- Display the Title as a link to the Post's permalink. -->
											 <h4><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>

											 <!-- Display the date (November 16th, 2009 format) and a link to other posts by this posts author. -->
											 <small><?php the_time( 'F jS, Y' ); ?> by <?php the_author_posts_link(); ?></small>
											 
											  <div class="entry">
											  	<?php the_excerpt(); ?>
											  </div>

											  <p class="postmetadata"><?php esc_html_e( 'Posted in' ); ?> <?php the_category( ', ' ); ?></p>
											 </div> <!-- closes the first div box -->

											 <?php endwhile; 
											 
											 wp_reset_postdata();
											 else : ?>
												<p><?php esc_html_e( 'Sorry, there are no events coming up right now.' ); ?></p>
											 <?php endif; ?>
										</div>
									</div>
								</div>
							</div>
						</main>

				</div>

			</div>

<?php get_footer(); ?>
