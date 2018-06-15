
              <?php
                /*
                 * This is the default post format.
                 *
                 * So basically this is a regular post. if you don't want to use post formats,
                 * you can just copy ths stuff in here and replace the post format thing in
                 * single.php.
                 *
                 * The other formats are SUPER basic so you can style them as you like.
                 *
                 * Again, If you want to remove post formats, just delete the post-formats
                 * folder and replace the function below with the contents of the "format.php" file.
                */
              ?>

              <article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article" itemscope itemprop="blogPost" itemtype="http://schema.org/BlogPosting">

                <section class="entry-content cf" itemprop="articleBody">
                  <p class="byline vcard">
                    <strong><?php echo ps_estimated_reading_time(); ?></strong> to read | Posted on <?php echo get_the_time(get_option('date_format')); ?>, and filed under <?php echo get_the_category_list(', '); ?>.
                  </p>
                  <?php
                    // the content (pretty self explanatory huh)
                    the_content();
                  ?>
                </section> <?php // end article section ?>

                

                <?php //comments_template(); ?>

              </article> <?php // end article ?>
