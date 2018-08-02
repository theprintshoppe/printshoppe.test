<?php get_header(); ?>

				<header class="entry-header">
					<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>
					<div class="cf"></div>
					<?php if(get_field('ps_subhead')) : ?>

						<div class="subhead">
							<?php echo get_field('ps_subhead'); ?>
						</div>
						<p class="masthead-cta"><a href="#inner-content" class="button button-alt button-cta">Learn About Direct Mail</a><a class="other-link" href="<?php bloginfo('url'); ?>/proposal/">Other Mailing Services</a></p>
					<?php endif; ?>

				</header>

			</header>

			<div id="content">

				<div id="inner-content">

					<div class="row fixed">
						<aside class="direct-mail-callout">
							<div class="direct-mail-stat-row">
								<div class="direct-mail-stat direct-mail-stat-responses">
									<h2 class="counter"><span class="timer count-title count-number" data-from="1" data-to="37" data-speed="1000"></span>X</h2>
									<h3 class="sub-counter">more responses than email</h3>
								</div>
								<div class="direct-mail-stat direct-mail-stat-purchase-percent">
									<h2 class="counter"><span class="timer count-title count-number" data-from="1" data-to="40" data-speed="1000"></span>%</h2>
									<h3 class="sub-counter">of consumers have made a purchase in the last 3 months because of direct mail</h3>
								</div>
							</div>
							<div class="direct-mail-roi">
								<p>In the US, advertisers earn <span class="positive-half">$2,095</span> for every <span class="negative">$167</span> spent on direct mail, yielding a <span class="positive">1,300%</span> return on investment.</p>
							</div>
						</aside>

						<aside class="direct-mail-headline">

							<div class="direct-mail-hero-image">
								<img src="<?php bloginfo('stylesheet_directory'); ?>/library/images/print-shoppe-direct-mail-versatile.jpeg" />
							</div>

							<div class="direct-mail-use-cases">
								<h2>Direct Mail is Ideal for</h2>
								<ul class="check">
									<li>
										<h5>Brand Awareness <i class="fas fa-arrow-down"></i></h5>
										<p>Make potential customers aware of your brand through the most intimate media channel. Leave a lasting first impression using the tactile power of print.</p>
									</li>
									<li>
										<h5>Customer Acquisition <i class="fas fa-arrow-down"></i></h5>
										<p>Direct mail has one of the lowest cost per acquisition among media channels. Leverage that to grow your profits fast.</p>
									</li>
									<li>
										<h5>Communication &amp; Engagement <i class="fas fa-arrow-down"></i></h5>
										<p>Direct mail to house lists has been proven to have tremendous response rates. Market more efficiently by utilizing your most valuable marketing asset.</p>
									</li>
								</ul>
							</div>
						</aside>
					</div>

					<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

						<section class="row full epic-mail-intro-hero">
							
							<div class="epic-mail-intro-wrapper">



							</div>
						
						</section>

						<section id="other-mailing-services" class="row fixed other-mailing-services">



						</section>

					</main>

				</div>

			</div>

<?php get_footer(); ?>
