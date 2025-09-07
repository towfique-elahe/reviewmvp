<?php

function course_search_box_shortcode() {
    wp_localize_script('reviewmvp-course-search', 'courseSearchData', [
        'ajaxUrl'       => admin_url('admin-ajax.php'),
        'nonce'         => wp_create_nonce('reviewmvp_course_search'),
        'archiveUrl'  => get_post_type_archive_link('course'),
        'addMissingUrl' => site_url('/add-missing-course/'),
        'labels'        => [
            'course'   => __('Course', 'textdomain'),
            'category' => __('Category', 'textdomain'),
            'noMatch'  => __('Add missing course & review it now or later', 'textdomain'),
        ],
    ]);
    
    ob_start();
?>
<div id="searchCourse" class="course-search-wrapper" role="combobox" aria-haspopup="listbox"
    aria-owns="courseSearchList" aria-expanded="false">
    <label for="courseSearchInput"
        class="sr-only"><?php esc_html_e('Search course or category', 'textdomain'); ?></label>
    <input id="courseSearchInput" type="text" class="course-search-input"
        placeholder="<?php esc_attr_e('Search course or category', 'textdomain'); ?>" aria-autocomplete="list"
        aria-controls="courseSearchList" aria-activedescendant="" autocomplete="off" />
    <ion-icon name="search-outline" class="search-icon" aria-hidden="true"></ion-icon>

    <div class="results-popover" hidden>
        <ul id="courseSearchList" role="listbox" class="results-list"></ul>
        <button class="add-missing" type="button">
            <span class="plus">
                <ion-icon name="add-outline"></ion-icon>
            </span>
            <span
                class="add-missing-text"><?php esc_html_e('Add missing course & review it now or later', 'textdomain'); ?></span>
        </button>
    </div>
</div>
<?php
    return ob_get_clean();
}
add_shortcode('course_search_box', 'course_search_box_shortcode');

function reviewmvp_course_search_ajax() {
    check_ajax_referer('reviewmvp_course_search', 'nonce');

    $term = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
    $results = [];

    if (empty($term)) {
        $courses = get_posts([
            'post_type'      => 'course',
            'posts_per_page' => 10,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        foreach ($courses as $c) {
            $results[] = [
                'type' => 'course',
                'id'   => $c->ID,
                'name' => $c->post_title,
                'url'  => get_permalink($c->ID),
            ];
        }

        wp_send_json($results);
    }

    $courses = get_posts([
        'post_type'      => 'course',
        's'              => $term,
        'posts_per_page' => 10,
    ]);

    foreach ($courses as $c) {
        $results[] = [
            'type' => 'course',
            'id'   => $c->ID,
            'name' => $c->post_title,
            'url'  => get_permalink($c->ID),
        ];
    }

    $cats = get_terms([
        'taxonomy'   => 'course_category',
        'name__like' => $term,
        'number'     => 10,
    ]);
    if (!is_wp_error($cats)) {
        foreach ($cats as $cat) {
            $results[] = [
                'type' => 'category',
                'id'   => $cat->term_id,
                'name' => $cat->name,
                'url'  => get_term_link($cat),
            ];
        }
    }

    wp_send_json($results);
}
add_action('wp_ajax_reviewmvp_course_search', 'reviewmvp_course_search_ajax');
add_action('wp_ajax_nopriv_reviewmvp_course_search', 'reviewmvp_course_search_ajax');

function reviewmvp_save_no_match_term() {
    check_ajax_referer('reviewmvp_course_search', 'nonce');

    $term = isset($_POST['term']) ? sanitize_text_field($_POST['term']) : '';
    if (!session_id()) {
        session_start();
    }
    $_SESSION['no_course_term'] = $term;

    wp_send_json_success();
}
add_action('wp_ajax_reviewmvp_save_no_match_term', 'reviewmvp_save_no_match_term');
add_action('wp_ajax_nopriv_reviewmvp_save_no_match_term', 'reviewmvp_save_no_match_term');

function reviewmvp_no_course_term_shortcode() {
    $term = $_SESSION['no_course_term'] ?? '';
    if ($term) {
        unset($_SESSION['no_course_term']);
        return '<h2>Sorry, no results were found for your search "' . esc_html($term) . '"<h2>';
    }
    return '';
}
add_shortcode('no_course_term', 'reviewmvp_no_course_term_shortcode');