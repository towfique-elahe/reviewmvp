<?php
/**
 * Function File Name: Theme Support Functions
 * 
 * The file for theme support functions.
 */

// Start session
function reviewmvp_start_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'reviewmvp_start_session', 1);

// Register theme support features
function reviewmvp_advanced_theme_support() {
    // Enable custom logo support with specific dimensions and flexibility
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Enable dynamic title tag support
    add_theme_support('title-tag');

    // Enable post thumbnails (featured images)
    add_theme_support('post-thumbnails');

    // Add custom image sizes
    add_image_size('custom-thumbnail', 600, 400, true);  // 600x400 crop mode
    add_image_size('hero-image', 1920, 800, true);       // 1920x800 crop mode

    // Enable WooCommerce support
    add_theme_support('woocommerce');

    // Enable HTML5 markup support for various elements
    add_theme_support('html5', array(
        'comment-list',
        'comment-form',
        'search-form',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Add support for selective refresh in the customizer
    add_theme_support('customize-selective-refresh-widgets');

    // Enable support for editor styles and load a custom editor stylesheet
    add_editor_style('editor-style.css');

    // Enable custom background support
    add_theme_support('custom-background', array(
        'default-color' => 'ffffff',
        'default-image' => '',
    ));

    // Add theme support for block styles (Gutenberg)
    add_theme_support('wp-block-styles');

    // Add wide and full alignment support for Gutenberg blocks
    add_theme_support('align-wide');

    // Add support for responsive embedded content
    add_theme_support('responsive-embeds');
	
	// Add support for widgets
	add_theme_support( 'widgets' );
}
add_action('after_setup_theme', 'reviewmvp_advanced_theme_support');

/**
 * Add Elementor Support
 */
function reviewmvp_add_elementor_support() {
    // Ensure Elementor can work with your theme
    add_theme_support('elementor');

    // Register locations for Elementor Theme Builder (e.g., header, footer)
    if (class_exists('Elementor\ThemeManager')) {
        add_action('elementor/theme/register_locations', function($elementor_theme_manager) {
            $elementor_theme_manager->register_all_core_location();
        });
    }

    // Enable custom breakpoints for Elementor if needed
    add_theme_support('elementor-custom-breakpoints');
}
add_action('after_setup_theme', 'reviewmvp_add_elementor_support');

/**
 * Menu Registration and Custom Menu Functions
 */

// Register theme menus
function reviewmvp_register_menus() {
    register_nav_menus([
        'primary-menu'   => __('Primary Menu', 'reviewmvp'),
        'footer-menu-1'  => __('Footer Menu 1', 'reviewmvp'),
        'footer-menu-2'  => __('Footer Menu 2', 'reviewmvp'),
        'footer-menu-3'  => __('Footer Menu 3', 'reviewmvp'),
        'mobile-menu'    => __('Mobile Menu', 'reviewmvp'),
    ]);
}
add_action('init', 'reviewmvp_register_menus');

/**
 * Display a fallback menu when no menu is assigned.
 */
function reviewmvp_fallback_menu() {
    echo '<ul class="fallback-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . __('Home', 'reviewmvp') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/about')) . '">' . __('About', 'reviewmvp') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contact')) . '">' . __('Contact', 'reviewmvp') . '</a></li>';
    echo '</ul>';
}

/**
 * Custom Walker for Nav Menus (for adding custom classes and structure).
 */
class reviewmvp_Custom_Nav_Walker extends Walker_Nav_Menu {
    // Start level (for submenus)
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }

    // Start element (for menu items)
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= '<li' . $class_names . '>';

        $attributes  = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        $item_output  = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= $item_output;
    }
}

/**
 * Display a menu with optional fallback and custom walker.
 *
 * @param string $theme_location The registered menu location.
 */
function reviewmvp_display_menu($theme_location) {
    if (has_nav_menu($theme_location)) {
        wp_nav_menu([
            'theme_location' => $theme_location,
            'container'      => 'nav',
            'container_class'=> 'reviewmvp-nav',
            'menu_class'     => 'reviewmvp-menu',
            'fallback_cb'    => 'reviewmvp_fallback_menu',
            'walker'         => new reviewmvp_Custom_Nav_Walker(),
        ]);
    } else {
        reviewmvp_fallback_menu();
    }
}

/**
 * Registered widget area
 */
function reviewmvp_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Main Sidebar', 'reviewmvp' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Widgets in this area will be shown on the sidebar.', 'reviewmvp' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'reviewmvp_widgets_init' );

/**
 * Allow JSON files
 */
function allow_json_uploads($mimes) {
    $mimes['json'] = 'application/json';
    return $mimes;
}
add_filter('upload_mimes', 'allow_json_uploads');