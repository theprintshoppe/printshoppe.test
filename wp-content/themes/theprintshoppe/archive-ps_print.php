<?php get_header(); ?>

				<header class="entry-header">
					<h1 class="page-title">Printssentials</h1>
					<div class="subhead">
						<p><strong>Have confidence in your print marketing partner.</strong><br />We provide high quality, reliable design &amp; printing services for ambitious brands centered around smart growth.</p>
						<p><strong>See our <a href="#process">process</a>, our <a href="#products">procucts</a>, or <a href="<?php bloginfo('url'); ?>/proposal">request a proposal</a>.</strong></p>
					</div>
				</header>

			</header>

			<div id="content">

				<div id="inner-content">
						<aside id="process" class="print-process">

							<p>We view every project as an opportunity to showcase the value print can bring to your brand. <strong>Clients like you trust us to guide them to finding that value.</strong> Every project is treated with care and is put through our meticulous process:</p>

							<?php ps_part_print_process(); ?>

						</aside>

						<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
							<div id="products">

								<?php $custom_terms = get_terms('ps_print_cat');

								foreach($custom_terms as $custom_term) {
								    wp_reset_query();
								    $args = array('post_type' => 'ps_print',
								        'tax_query' => array(
								            array(
								                'taxonomy' => 'ps_print_cat',
								                'field' => 'slug',
								                'terms' => $custom_term->slug,
								            ),
								        ),
								     );

								     $loop = new WP_Query($args);
								     if($loop->have_posts()) {
								     	echo '<div class="print-category">';
									        echo '<h2>'.$custom_term->name.'</h2>';

									        while($loop->have_posts()) : $loop->the_post();
									            echo '<a href="'.get_permalink().'" class="product-link"><span>'.get_the_title().'</span><i class="fas fa-chevron-right"></i></a>';
									        endwhile;
									    echo '</div>';
								     }	
								} ?>
							</div>

						</main>

				</div>

			</div>

<?php get_footer(); ?>
