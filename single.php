<?php
/**
 * Template Name: Single Post
 * 
 * The template for displaying all single posts.
 * 
 * @package ReviewMVP
 */

get_header(); // Include header.php
?>

<main id="main-content" class="site-main">
    <div class="container">
        <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
                <div class="entry-meta">
                    <span class="posted-on"><?php echo get_the_date(); ?></span>
                    <span class="author"><?php the_author_posts_link(); ?></span>
                </div>
            </header>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>

            <footer class="entry-footer">
                <div class="post-categories">
                    <?php the_category(', '); ?>
                </div>
                <div class="post-tags">
                    <?php the_tags('<span class="tag-links">', ', ', '</span>'); ?>
                </div>
            </footer>

            <?php comments_template(); ?>
        </article>

        <?php endwhile; ?>
        <?php else : ?>
        <p><?php _e('Sorry, no posts found.', 'reviewmvp'); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>