<?php
/**
 * Template Name: Footer
 * 
 * The template for displaying footer.
 * 
 * @package ReviewMVP
 */
?>

<footer id="site-footer" class="site-footer">
    <?php if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('footer')) : ?>
    <div class="container">
        <div class="footer-inner">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All Rights Reserved.</p>

            <nav id="footer-navigation" class="footer-navigation" aria-label="Footer Navigation">
                <?php
                    wp_nav_menu([
                        'theme_location' => 'footer-menu-1',
                        'menu_class'     => 'footer-menu',
                        'fallback_cb'    => 'reviewmvp_fallback_menu',
                    ]);
                    ?>
            </nav>
        </div>
    </div>
    <?php endif; ?>
</footer>

<?php wp_footer(); ?>
</body>

</html>