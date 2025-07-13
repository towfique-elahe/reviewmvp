<?php
/**
 * Template Name: Index Page
 * 
 * The template for displaying index page.
 * 
 * @package ReviewMVP
 */

get_header(); // Include header.php

if ( have_posts() ) :
    while ( have_posts() ) : the_post();
?>
<main id="main-content" class="site-main">
    <div class="container">
        <?php
                // Check if the content is built with Elementor and display it
                if ( Elementor\Plugin::$instance->documents->get( get_the_ID() )->is_built_with_elementor() ) {
                    the_content(); // Render Elementor content
                } else {
                    // Fallback for standard content
                ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <h2 class="entry-title"><?php the_title(); ?></h2>
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        </article>
        <?php } ?>
    </div>
</main>
<?php
    endwhile;
endif;

get_footer(); // Include footer.php
?>