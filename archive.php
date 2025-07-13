<?php
/**
 * Template Name: Archive Page
 * 
 * The template for displaying archive pages.
 * 
 * @package ReviewMVP
 */

get_header(); // Include header.php
?>

<main id="main-content" class="site-main">
    <div class="container">
        <header class="archive-header">
            <h1 class="archive-title"><?php the_archive_title(); ?></h1>
            <div class="archive-description"><?php the_archive_description(); ?></div>
        </header>

        <?php if (have_posts()) : ?>
        <div class="archive-posts">
            <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2 class="entry-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <div class="entry-meta">
                    <span class="posted-on"><?php echo get_the_date(); ?></span>
                </div>
                <div class="entry-excerpt">
                    <?php the_excerpt(); ?>
                </div>
            </article>
            <?php endwhile; ?>

            <div class="pagination">
                <?php the_posts_pagination(); ?>
            </div>
        </div>
        <?php else : ?>
        <p><?php _e('Sorry, no posts found.', 'reviewmvp'); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>