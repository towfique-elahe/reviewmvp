<?php

function reviewmvp_register_styles() {
    $version = wp_get_theme()->get('Version');

    wp_enqueue_style(
        'reviewmvp-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Noto+Sans+Georgian:wght@100..900&display=swap',
        [],
        null
    );

    wp_enqueue_style('ionicons', 'https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.5.2/ionicons.min.css', [], null);

    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', [], null);

    wp_enqueue_style('reviewmvp-style', get_stylesheet_uri(), [], $version);

    $styles = [
        'reviewmvp-root-style'                  => 'assets/css/root.css',
        'reviewmvp-course-search-style'         => 'assets/css/course-search.css',
        'reviewmvp-featured-reviews-style'      => 'assets/css/featured-reviews.css',
        'reviewmvp-404-style'                   => 'assets/css/404.css',
        'reviewmvp-courses-style'               => 'assets/css/courses.css',
        'reviewmvp-single-course-style'         => 'assets/css/single-course.css',
        'reviewmvp-add-course-style'            => 'assets/css/add-course.css',
        'reviewmvp-add-review-style'            => 'assets/css/add-review.css',
        'reviewmvp-auth-style'                  => 'assets/css/auth.css',
        'reviewmvp-reviewer-portal-style'       => 'assets/css/reviewer-portal.css'
    ];

    foreach ($styles as $handle => $path) {
        wp_enqueue_style($handle, get_template_directory_uri() . '/' . $path, [], $version);
    }

    if (did_action('elementor/loaded')) {
        wp_enqueue_style('elementor-frontend');
        if (class_exists('ElementorPro\Plugin')) {
            wp_enqueue_style('elementor-pro-frontend');
        }
    }
}
add_action('wp_enqueue_scripts', 'reviewmvp_register_styles');

function reviewmvp_register_scripts() {
    $version = wp_get_theme()->get('Version');

    wp_enqueue_script('jquery');

    wp_enqueue_script('ionicons-esm', 'https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js', [], null, true);

    wp_enqueue_script('ionicons-nomodule', 'https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js', [], null, true);

    $scripts = [
        'reviewmvp-course-search'       => 'assets/js/course-search.js',
        'reviewmvp-courses'             => 'assets/js/courses.js',
        'reviewmvp-single-course'       => 'assets/js/single-course.js',
    ];

    foreach ($scripts as $handle => $path) {
        wp_enqueue_script($handle, get_template_directory_uri() . '/' . $path, [], $version, true);
    }
}
add_action('wp_enqueue_scripts', 'reviewmvp_register_scripts');