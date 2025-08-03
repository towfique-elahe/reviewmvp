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
        <section id="pageNotFound" class="error-404 not-found">
            <div class="container">
                <img src="<?php echo esc_url(get_theme_media_url('404.svg')); ?>"
                    alt="<?php esc_attr_e('404 Error - Page Not Found', 'reviewmvp'); ?>" class="featured-image">

                <p class="message">
                    <?php _e("It seems the page you're looking for doesn’t exist. Let’s get you back on track—click below to return to the home page.", 'reviewmvp'); ?>
                </p>

                <a href="<?php echo esc_url(home_url('/')); ?>" class="button">
                    <?php _e('Return to Home', 'reviewmvp'); ?>
                </a>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>