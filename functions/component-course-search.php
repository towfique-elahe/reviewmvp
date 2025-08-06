<?php
/**
 * Function File Name: Course Search
 * 
 * The file for custom search box for course.
 */

// Shortcode: [course_search_box]

function course_search_box_shortcode() {

    // Demo static data (courses + categories). Replace later with AJAX.
    $demo_items = [
        ['type' => 'course', 'name' => 'Intro to Data Science'],
        ['type' => 'course', 'name' => 'Advanced React'],
        ['type' => 'course', 'name' => 'Foundations of Marketing'],
        ['type' => 'course', 'name' => 'PHP & WordPress Essentials'],
        ['type' => 'category', 'name' => 'Design'],
        ['type' => 'category', 'name' => 'Development'],
        ['type' => 'category', 'name' => 'Business'],
        ['type' => 'category', 'name' => 'Data'],
    ];

    wp_localize_script('reviewmvp-course-search', 'courseSearchData', [
        'items'         => $demo_items,
        'addMissingUrl' => site_url('/add-missing-course/'),
        'placeholder'   => __('Search course or category', 'textdomain'),
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