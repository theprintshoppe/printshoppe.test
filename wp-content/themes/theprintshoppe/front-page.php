<?php get_header(); ?>
				
				<header class="entry-header masthead-wrapper">
					<div class="masthead-title">
						<h1 class="masthead-headline">Fuel massive growth for your brand using the most proven marketing channel.</h1>
						<p class="masthead-subhead">Thousands of smart marketers leverage our design, printing, and mailing services to generate more revenue and get the most value out of their printing budget.</p>
						<p class="masthead-cta"><a href="#content" class="button button-cta">Show Me How</a></p>
					</div>
					<div class="masthead-image">
						<img src="<?php bloginfo('stylesheet_directory'); ?>/library/images/home-icon-pattern-alt.svg" />
					</div>
				</header>

			</header>

			<div id="content">

				<div id="inner-content">

						<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

							<div class="home-section home-hero-tabs">
								<div class="links">
									<div class="link link-heading">
										<h5>We provide 3 avenues to grow your business by accelerating customers through your funnel and coverting them to raving fans:</h5>
									</div>
									<div class="link link-printssentials " data-target="printssentials">
										<div class="menu-item-has-icon-subtitle">
											<div class="menu-item-icon">
												<i class="fa fa-book-open"></i>
											</div>
											<div class="menu-item-content">
												<span class="menu-item-title">Printssentials</span>
												<span class="menu-item-subtitle">Every print product you need to run and promote your business efficiently.</span>
											</div>
										</div>
										<i class="fas fa-chevron-right"></i>
									</div>
									<div class="link link-epic-mail active" data-target="epic-mail">
										<div class="menu-item-has-icon-subtitle">
											<div class="menu-item-icon">
												<i class="fa fa-chart-line"></i>
											</div>
											<div class="menu-item-content">
												<span class="menu-item-title">Epic Mail</span>
												<span class="menu-item-subtitle">High value marketing strategies through database and direct mail marketing</span>
											</div>
										</div>
										<i class="fas fa-chevron-right"></i>
									</div>
									<div class="link link-logistics-plus" data-target="logistics-plus">
										<div class="menu-item-has-icon-subtitle">
											<div class="menu-item-icon">
												<i class="fa fa-truck"></i>
											</div>
											<div class="menu-item-content">
												<span class="menu-item-title">Logistics+</span>
												<span class="menu-item-subtitle">Ordering, kitting, and fulfillment made simple using technology &amp; logistics.</span>
											</div>
										</div>
										<i class="fas fa-chevron-right"></i>
									</div>
								</div>
								<div class="slides">
									<div class="slide slide-printssentials " data-content="printssentials">
										<div class="slide-content">
											<h2 class="slide-title">Printssentials</h2>
											<p><strong>Printing products &amp; processes that are focused on delivering value for your brand.</strong></p>
											<ul class="check-circle-empty">
												<li>Corporate Stationary</li>
												<li>Marketing Collateral</li>
												<li>Books + Manuals</li>
												<li>Outdoor + Indoor Signage</li>
												<li>Labels + Packaging</li>
												<li>Forms</li>
												<li>Branded Products + Swag</li>
												<li>We <i class="fa fa-heart"></i> Custom Projects!</li>
											</ul> 
											<p class="cta-link"><a href="<?php bloginfo('url'); ?>/printssentials" class="link-cta">Get More Value from Your Print</a></p>
										</div>
									</div>
									<div class="slide slide-epic-mail active" data-content="epic-mail">
										<div class="slide-content">
											<h2 class="slide-title">Epic Mail</h2>
											<p><strong>Direct mail has led the marketing mix in effectiveness for years &mdash; and continues to do so.</strong></p>
											<p>Deep expertise in database and direct mail marketing allow us to create and execute hundreds of successful campaigns.</p>
											<ul class="check-circle-empty">
												<li>Direct Mail Marketing Strategies from Concept to Production</li>
												<li>Monthly Mailing Campaigns</li>
												<li>Nonprofit Fundraising Campaigns</li>
												<li>Complex, Multi-Piece Mailers</li>
												<li>Online Marketing Integration</li>
											</ul> 
											<p class="cta-link"><a href="<?php bloginfo('url'); ?>/epic-mail" class="link-cta">Download Your Direct Mail Cheat Sheet</a></p>
										</div>
									</div>
									<div class="slide slide-logistics-plus" data-content="logistics-plus">
										<div class="slide-content">
											<h2 class="slide-title">Logistics+</h2>
											<p><strong>Worries about shipping &amp; fulfillment are a thing of the past.</strong></p>
											<p>Leverage our experienced kitting, fulfillment, shipping team to make sure your teams and clients all over the world have materials on time and reliably.</p> 
											<ul class="check-circle-empty">
												<li>Lightning Fast Turnarounds</li>
												<li>Custom Kitted Packages</li>
												<li>On-Demand Production</li>
												<li>Discounted Shipping Rates</li>
											</ul>
											<p class="cta-link"><a href="<?php bloginfo('url'); ?>/logistics-plus" class="link-cta">Simplify Your Operations with Logistics+</a></p>
										</div>
									</div>
								</div>
							</div>

							<!--<div class="home-section home-journey">
								<h2>The Value Journey</h2>
							</div>	-->

							<!--<div class="home-section home-clients">
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
							</div>-->

							<div class="home-section home-about-subscribe">
								<div class="home-about-subscribe-wrap">
									<div class="home-about">
										<h2>Who Is the Print Shoppe</h2>
										<p><strong>We fuel massive growth for your brand using the most proven marketing channel: print.</strong></p>
										<p>Since 1987, we've helped businesses and nonprofits from Austina and beyond reach their propects &amp; customers.</p>
										<p>We are a place where tradition meets technology, art meets science, and where challenging projects define us.</p>
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

										<p class="home-all-archive-link"><a href="<?php home_url(); ?>" class="link-cta">All Insights</a></p>
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

											 <p class="home-all-archive-link"><a href="<?php bloginfo('url'); ?>/company/newsroom" class="link-cta">Visit Our Newsroom</a></p>
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
