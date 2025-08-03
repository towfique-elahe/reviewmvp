<?php
/**
 * Function File Name: Course Search
 * 
 * The file for custom search box for course.
 */

// Shortcode: [course_search_box]

function course_search_box_shortcode() {
    ob_start();
    ?>
<div class="course-search-wrapper">
    <input type="text" class="course-search-input" placeholder="Search course or category" />
    <ion-icon name="search-outline" class="search-icon"></ion-icon>
</div>
<?php
    return ob_get_clean();
}
add_shortcode('course_search_box', 'course_search_box_shortcode');