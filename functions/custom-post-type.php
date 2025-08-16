<?php
/**
 * Function File Name: Custom Post Type
 * 
 * The file for custom post type 'course' and additional meta fields.
 */

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
    wp_nonce_field('reviewmvp_save_course_meta_box', 'reviewmvp_course_meta_box_nonce');

    // Get meta
    $meta = [
        'course_provider'       => get_post_meta($post->ID, '_course_provider', true),
        'course_price'       => get_post_meta($post->ID, '_course_price', true),
        'course_duration'    => get_post_meta($post->ID, '_course_duration', true),
        'course_certificate' => get_post_meta($post->ID, '_course_certificate', true),
        'course_refundable'  => get_post_meta($post->ID, '_course_refundable', true),
        'course_link'        => get_post_meta($post->ID, '_course_link', true),
        'course_level'       => get_post_meta($post->ID, '_course_level', true),
        'course_language'    => (array) get_post_meta($post->ID, '_course_language', true),
        'course_instructor'  => get_post_meta($post->ID, '_course_instructor', true),
    ];

    if (!is_array($meta['course_instructor'])) {
        $meta['course_instructor'] = [
            'name' => '',
            'position' => '',
            'details' => '',
            'facebook' => '',
            'instagram' => '',
            'linkedin' => '',
            'twitter' => '',
            'youtube' => '',
        ];
    }

    ?>
<p>
    <label><strong>Provider</strong></label><br>
    <input type="text" name="course_provider" value="<?php echo esc_attr($meta['course_provider']); ?>"
        style="width:100%;">
</p>

<p>
    <label><strong>Course Price ($)</strong></label><br>
    <input type="number" step="0.01" name="course_price" value="<?php echo esc_attr($meta['course_price']); ?>"
        style="width:100%;">
</p>

<p>
    <label><strong>Course Duration (hours)</strong></label><br>
    <input type="number" name="course_duration" value="<?php echo esc_attr($meta['course_duration']); ?>"
        style="width:100%;">
</p>

<p>
    <label><strong>Certificate</strong></label><br>
    <select name="course_certificate" style="width:100%;">
        <option value="Yes" <?php selected($meta['course_certificate'], 'Yes'); ?>>Yes</option>
        <option value="No" <?php selected($meta['course_certificate'], 'No'); ?>>No</option>
    </select>
</p>

<p>
    <label><strong>Refundable</strong></label><br>
    <select name="course_refundable" style="width:100%;">
        <option value="Yes" <?php selected($meta['course_refundable'], 'Yes'); ?>>Yes</option>
        <option value="No" <?php selected($meta['course_refundable'], 'No'); ?>>No</option>
    </select>
</p>

<p>
    <label><strong>Course Link</strong></label><br>
    <input type="url" name="course_link" value="<?php echo esc_attr($meta['course_link']); ?>" style="width:100%;">
</p>

<p>
    <label><strong>Level</strong></label><br>
    <select name="course_level" style="width:100%;">
        <option value="beginner" <?php selected($meta['course_level'], 'beginner'); ?>>Beginner</option>
        <option value="intermediate" <?php selected($meta['course_level'], 'intermediate'); ?>>Intermediate</option>
        <option value="advance" <?php selected($meta['course_level'], 'advance'); ?>>Advance</option>
    </select>
</p>

<p>
    <label><strong>Language(s)</strong></label><br>
    <select name="course_language[]" multiple style="width:100%; height: 80px;">
        <?php foreach (['English','French','Spanish','German','Hindi'] as $lang): ?>
        <option value="<?php echo esc_attr($lang); ?>"
            <?php echo in_array($lang, $meta['course_language']) ? 'selected' : ''; ?>>
            <?php echo esc_html($lang); ?>
        </option>
        <?php endforeach; ?>
    </select>
    <small>Hold Ctrl (Windows) / Command (Mac) to select multiple.</small>
</p>

<hr>
<h3>Instructor Details</h3>

<p>
    <label><strong>Name</strong></label><br>
    <input type="text" name="course_instructor[name]"
        value="<?php echo esc_attr($meta['course_instructor']['name']); ?>" style="width:100%;">
</p>
<p>
    <label><strong>Position</strong></label><br>
    <input type="text" name="course_instructor[position]"
        value="<?php echo esc_attr($meta['course_instructor']['position']); ?>" style="width:100%;">
</p>
<p>
    <label><strong>Details</strong></label><br>
    <textarea name="course_instructor[details]" rows="4"
        style="width:100%;"><?php echo esc_textarea($meta['course_instructor']['details']); ?></textarea>
</p>

<hr>
<h4>Social Links</h4>
<p><label><strong>Facebook</strong></label><br>
    <input type="url" name="course_instructor[facebook]"
        value="<?php echo esc_attr($meta['course_instructor']['facebook'] ?? ''); ?>" style="width:100%;">
</p>
<p><label><strong>Instagram</strong></label><br>
    <input type="url" name="course_instructor[instagram]"
        value="<?php echo esc_attr($meta['course_instructor']['instagram'] ?? ''); ?>" style="width:100%;">
</p>
<p><label><strong>LinkedIn</strong></label><br>
    <input type="url" name="course_instructor[linkedin]"
        value="<?php echo esc_attr($meta['course_instructor']['linkedin'] ?? ''); ?>" style="width:100%;">
</p>
<p><label><strong>Twitter / X</strong></label><br>
    <input type="url" name="course_instructor[twitter]"
        value="<?php echo esc_attr($meta['course_instructor']['twitter'] ?? ''); ?>" style="width:100%;">
</p>
<p><label><strong>YouTube</strong></label><br>
    <input type="url" name="course_instructor[youtube]"
        value="<?php echo esc_attr($meta['course_instructor']['youtube'] ?? ''); ?>" style="width:100%;">
</p>

<hr>
<p><em>Course Description uses the post content (main editor).</em></p>
<?php
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

    // Basic text fields
    $fields = ['course_provider','course_price','course_duration','course_certificate','course_refundable','course_link','course_level'];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = $_POST[$field];
            if ($field === 'course_price' || $field === 'course_duration') {
                $value = intval($value);
            } elseif ($field === 'course_link') {
                $value = esc_url_raw($value);
            } else {
                $value = sanitize_text_field($value);
            }
            update_post_meta($post_id, '_' . $field, $value);
        }
    }

    // Languages (multi)
    if (isset($_POST['course_language'])) {
        $langs = array_map('sanitize_text_field', (array)$_POST['course_language']);
        update_post_meta($post_id, '_course_language', $langs);
    } else {
        delete_post_meta($post_id, '_course_language');
    }

    // Instructor (array)
    if (isset($_POST['course_instructor']) && is_array($_POST['course_instructor'])) {
        $instructor = [
            'name'     => sanitize_text_field($_POST['course_instructor']['name'] ?? ''),
            'position' => sanitize_text_field($_POST['course_instructor']['position'] ?? ''),
            'details'  => sanitize_textarea_field($_POST['course_instructor']['details'] ?? ''),
            'facebook' => esc_url_raw($_POST['course_instructor']['facebook'] ?? ''),
            'instagram'=> esc_url_raw($_POST['course_instructor']['instagram'] ?? ''),
            'linkedin' => esc_url_raw($_POST['course_instructor']['linkedin'] ?? ''),
            'twitter'  => esc_url_raw($_POST['course_instructor']['twitter'] ?? ''),
            'youtube'  => esc_url_raw($_POST['course_instructor']['youtube'] ?? ''),
        ];
        update_post_meta($post_id, '_course_instructor', $instructor);
    }
}
add_action('save_post_course', 'reviewmvp_save_course_meta_box');

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

/**
 * Add custom meta fields to REST API for 'course'
 */
function reviewmvp_register_course_rest_fields() {
    $fields = [
        'course_provider'  => '_course_provider',
        'course_duration'  => '_course_duration',
        'course_level'     => '_course_level',
        'course_instructor'=> '_course_instructor',
    ];

    foreach ($fields as $key => $meta_key) {
        register_rest_field('course', $key, [
            'get_callback' => function($object) use ($meta_key) {
                return get_post_meta($object['id'], $meta_key, true);
            },
            'schema' => null,
        ]);
    }
}
add_action('rest_api_init', 'reviewmvp_register_course_rest_fields');