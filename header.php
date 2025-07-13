<?php
/**
 * Template Name: Header
 * 
 * The template for displaying header.
 * 
 * @package ReviewMVP
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <header id="site-header" class="site-header">
        <?php if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('header')) : ?>
        <div class="container">
            <div class="header-inner">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" aria-label="Home">
                    <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                    <?php else : ?>
                    <h1 class="site-title"><?php bloginfo('name'); ?></h1>
                    <?php endif; ?>
                </a>

                <nav id="main-navigation" class="main-navigation" aria-label="Primary Navigation">
                    <?php
                        wp_nav_menu([
                            'theme_location' => 'primary-menu',
                            'menu_class'     => 'nav-menu',
                            'fallback_cb'    => 'reviewmvp_fallback_menu',
                        ]);
                        ?>
                </nav>

                <!-- Mobile Menu Button -->
                <button class="mobile-menu-toggle" aria-label="Toggle Mobile Menu">
                    <span class="hamburger-icon"></span>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <nav id="mobile-navigation" class="mobile-navigation" aria-label="Mobile Navigation">
            <?php
                wp_nav_menu([
                    'theme_location' => 'mobile-menu',
                    'menu_class'     => 'mobile-nav-menu',
                    'fallback_cb'    => 'reviewmvp_fallback_menu',
                ]);
                ?>
        </nav>
        <?php endif; ?>
    </header>