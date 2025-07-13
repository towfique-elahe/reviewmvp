<?php
/**
 * Template Name: Single Page
 * 
 * The template for displaying all pages.
 * 
 * @package ReviewMVP
 */

get_header(); // Include header.php
?>

<main id="main-content" class="site-main">
    <div class="container">
        <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>

        <?php
                // Check if the page is built with Elementor
                if (class_exists('Elementor\Plugin') && Elementor\Plugin::$instance->documents->get(get_the_ID())->is_built_with_elementor()) :
                    the_content(); // Render Elementor content
                else :
                ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <!-- Page Title -->
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>

            <!-- Page Content -->
            <div class="entry-content">
                <?php the_content(); ?>
            </div>

            <!-- Page Pagination (for paginated content) -->
            <div class="page-pagination">
                <?php
                            wp_link_pages([
                                'before' => '<div class="page-links">' . __('Pages:', 'reviewmvp'),
                                'after'  => '</div>',
                            ]);
                            ?>
            </div>

        </article>
        <?php endif; ?>

        <?php endwhile; ?>

        <?php else : ?>
        <!-- Fallback for No Content -->
        <div class="no-content">
            <h2><?php _e('Sorry, no content found.', 'reviewmvp'); ?></h2>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); // Include footer.php ?>