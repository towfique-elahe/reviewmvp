<?php
/**
 * Template Name: 404 Error Page
 * 
 * The template for displaying 404 pages (Not Found).
 * 
 * @package ReviewMVP
 */

get_header(); // Include header.php
?>

<main id="main-content" class="site-main">
    <div class="container">
        <section class="error-404 not-found">
            <h1 class="page-title"><?php _e('Oops! That page can’t be found.', 'reviewmvp'); ?></h1>
            <div class="page-content">
                <p><?php _e('It looks like nothing was found at this location. Maybe try a search or check out the links below.', 'reviewmvp'); ?>
                </p>

                <?php get_search_form(); ?>

                <div class="recent-posts">
                    <h2><?php _e('Recent Posts', 'reviewmvp'); ?></h2>
                    <ul>
                        <?php
                        $recent_posts = wp_get_recent_posts(['numberposts' => 5]);
                        foreach ($recent_posts as $post) :
                            ?>
                        <li>
                            <a href="<?php echo get_permalink($post['ID']); ?>">
                                <?php echo esc_html($post['post_title']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>