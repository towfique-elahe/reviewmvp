<?php


/**
 * Register Custom Post Type: Course
 */
function reviewmvp_register_course_post_type() {
    $labels = array(
        'name'                  => _x('Courses', 'Post Type General Name', 'reviewmvp'),
        'singular_name'         => _x('Course', 'Post Type Singular Name', 'reviewmvp'),
        'menu_name'             => __('Courses', 'reviewmvp'),
        'name_admin_bar'        => __('Course', 'reviewmvp'),
        'archives'              => __('Course Archives', 'reviewmvp'),
        'attributes'            => __('Course Attributes', 'reviewmvp'),
        'parent_item_colon'     => __('Parent Course:', 'reviewmvp'),
        'all_items'             => __('All Courses', 'reviewmvp'),
        'add_new_item'          => __('Add Course', 'reviewmvp'),
        'add_new'               => __('Add New', 'reviewmvp'),
        'new_item'              => __('New Course', 'reviewmvp'),
        'edit_item'             => __('Edit Course', 'reviewmvp'),
        'update_item'           => __('Update Course', 'reviewmvp'),
        'view_item'             => __('View Course', 'reviewmvp'),
        'view_items'            => __('View Courses', 'reviewmvp'),
        'search_items'          => __('Search Course', 'reviewmvp'),
        'not_found'             => __('No courses found', 'reviewmvp'),
        'not_found_in_trash'    => __('No courses found in Trash', 'reviewmvp'),
        'featured_image'        => __('Featured Image', 'reviewmvp'),
        'set_featured_image'    => __('Set featured image', 'reviewmvp'),
        'remove_featured_image' => __('Remove featured image', 'reviewmvp'),
        'use_featured_image'    => __('Use as featured image', 'reviewmvp'),
        'insert_into_item'      => __('Insert into course', 'reviewmvp'),
        'uploaded_to_this_item' => __('Uploaded to this course', 'reviewmvp'),
        'items_list'            => __('Courses list', 'reviewmvp'),
        'items_list_navigation' => __('Courses list navigation', 'reviewmvp'),
        'filter_items_list'     => __('Filter courses list', 'reviewmvp'),
    );

    $args = array(
        'label'                 => __('Course', 'reviewmvp'),
        'description'           => __('Custom Post Type for Courses', 'reviewmvp'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'excerpt', 'thumbnail', 'comments', 'revisions', 'author', 'custom-fields'),
        'taxonomies'            => array('category', 'post_tag'),
        'hierarchical'          => false,
        'public'                => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-welcome-learn-more', // More relevant icon for courses
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'show_in_rest'          => true,
        'capability_type'       => 'post',
        'rewrite'               => array(
            'slug' => 'course',
            'with_front' => false
        ),
    );

    register_post_type('course', $args);
}
add_action('init', 'reviewmvp_register_course_post_type');

/**
 * Add custom meta box for 'course' post type.
 */
function reviewmvp_add_course_meta_box() {
    add_meta_box(
        'course_details_meta_box',
        __('Course Details', 'reviewmvp'),
        'reviewmvp_render_course_meta_box',
        'course',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'reviewmvp_add_course_meta_box');

/**
 * Render the meta box content.
 */
function reviewmvp_render_course_meta_box($post) {
    // Define fields and default values
    $fields = [
        'status'            => ['type' => 'select', 'options' => ['Draft', 'Active', 'Closed']],
        'instructor'        => ['type' => 'text'],
        'overview'          => ['type' => 'textarea'],
        'about_instructor'  => ['type' => 'textarea'],
        'price'             => ['type' => 'text'],
        'duration'          => ['type' => 'text'],
        'level'             => ['type' => 'select', 'options' => ['Beginner', 'Intermediate', 'Expert']],
        'certificate'       => ['type' => 'checkbox'],
        'refundable'        => ['type' => 'checkbox'],
        'language'          => ['type' => 'select', 'options' => ['English', 'Spanish', 'French']],
        'course_url'        => ['type' => 'text'],
    ];

    wp_nonce_field('reviewmvp_save_course_meta_box', 'reviewmvp_course_meta_box_nonce');

    foreach ($fields as $field => $config) {
        $id = 'course_' . $field;
        $meta = get_post_meta($post->ID, '_' . $id, true);

        echo '<p><label for="' . esc_attr($id) . '"><strong>' . ucwords(str_replace('_', ' ', $field)) . '</strong></label><br>';

        switch ($config['type']) {
            case 'textarea':
                echo '<textarea id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" rows="4" style="width:100%;">' . esc_textarea($meta) . '</textarea>';
                break;

            case 'select':
                echo '<select id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" style="width:100%;">';
                foreach ($config['options'] as $option) {
                    echo '<option value="' . esc_attr($option) . '" ' . selected($meta, $option, false) . '>' . esc_html($option) . '</option>';
                }
                echo '</select>';
                break;

            case 'checkbox':
                echo '<input type="checkbox" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" value="1" ' . checked($meta, '1', false) . ' />';
                break;

            default:
                echo '<input type="text" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" value="' . esc_attr($meta) . '" style="width:100%;" />';
                break;
        }

        echo '</p>';
    }
}

/**
 * Save course meta box fields.
 */
function reviewmvp_save_course_meta_box($post_id) {
    if (!isset($_POST['reviewmvp_course_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['reviewmvp_course_meta_box_nonce'], 'reviewmvp_save_course_meta_box')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = [
        'status',
        'instructor',
        'overview',
        'about_instructor',
        'price',
        'duration',
        'level',
        'certificate',
        'refundable',
        'language',
        'course_url',
    ];

    foreach ($fields as $field) {
        $key = '_course_' . $field;

        if (in_array($field, ['certificate', 'refundable'])) {
            update_post_meta($post_id, $key, isset($_POST['course_' . $field]) ? '1' : '0');
        } elseif (in_array($field, ['overview', 'about_instructor'])) {
            update_post_meta($post_id, $key, sanitize_textarea_field($_POST['course_' . $field]));
        } elseif ($field === 'course_url') {
            update_post_meta($post_id, $key, esc_url_raw($_POST['course_' . $field]));
        } else {
            update_post_meta($post_id, $key, sanitize_text_field($_POST['course_' . $field]));
        }
    }
}

/**
 * Add "Course base" field to Permalinks settings page.
 */
function reviewmvp_add_course_permalink_setting() {
    add_settings_field(
        'course_permalink_base',
        __('Course base', 'reviewmvp'),
        'reviewmvp_course_permalink_field_html',
        'permalink',
        'optional'
    );

    register_setting('permalink', 'course_permalink_base');
}
add_action('admin_init', 'reviewmvp_add_course_permalink_setting');

function reviewmvp_course_permalink_field_html() {
    $value = get_option('course_permalink_base', 'course');
    echo '<input type="text" name="course_permalink_base" value="' . esc_attr($value) . '" class="regular-text" />';
    echo '<p class="description">' . __('Custom base for Course URLs (e.g., course → yoursite.com/course/course-title).', 'reviewmvp') . '</p>';
}