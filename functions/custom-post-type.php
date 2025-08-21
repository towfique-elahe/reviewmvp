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
 * Register Custom Post Type: Review (as a submenu under Courses)
 */
function reviewmvp_register_review_post_type() {
    $labels = array(
        'name'               => _x('Reviews', 'Post Type General Name', 'reviewmvp'),
        'singular_name'      => _x('Review', 'Post Type Singular Name', 'reviewmvp'),
        'menu_name'          => __('Reviews', 'reviewmvp'),
        'name_admin_bar'     => __('Review', 'reviewmvp'),
        'add_new_item'       => __('Add New Review', 'reviewmvp'),
        'edit_item'          => __('Edit Review', 'reviewmvp'),
        'new_item'           => __('New Review', 'reviewmvp'),
        'view_item'          => __('View Review', 'reviewmvp'),
        'all_items'          => __('All Reviews', 'reviewmvp'),
        'search_items'       => __('Search Reviews', 'reviewmvp'),
        'not_found'          => __('No reviews found', 'reviewmvp'),
        'not_found_in_trash' => __('No reviews found in Trash', 'reviewmvp'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false, // Reviews don’t need to appear on frontend automatically
        'show_ui'            => true,
        'show_in_menu'       => 'edit.php?post_type=course', // 👈 attaches to Courses menu
        'supports'           => array('title', 'editor', 'author'),
        'capability_type'    => 'post',
        'has_archive'        => false,
    );

    register_post_type('course_review', $args);
}
add_action('init', 'reviewmvp_register_review_post_type');

/**
 * Add Review Meta Box
 */
function reviewmvp_add_review_meta_box() {
    add_meta_box(
        'review_details_meta_box',
        __('Review Details', 'reviewmvp'),
        'reviewmvp_render_review_meta_box',
        'course_review',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'reviewmvp_add_review_meta_box');

function reviewmvp_render_review_meta_box($post) {
    wp_nonce_field('reviewmvp_save_review_meta', 'reviewmvp_review_meta_nonce');

    $meta = [
        'status'             => (array) get_post_meta($post->ID, '_review_status', true),
        'review_date'        => get_post_meta($post->ID, '_review_date', true),
        'reviewer'           => get_post_meta($post->ID, '_reviewer', true),
        'course_id'          => get_post_meta($post->ID, '_review_course', true),
        'rating'             => get_post_meta($post->ID, '_review_rating', true),
        'message'            => get_post_meta($post->ID, '_review_message', true),
        'good'               => get_post_meta($post->ID, '_review_good', true),
        'bad'                => get_post_meta($post->ID, '_review_bad', true),
        'outcome'            => get_post_meta($post->ID, '_review_outcome', true),
        'quality'            => get_post_meta($post->ID, '_review_quality', true),
        'support'            => get_post_meta($post->ID, '_review_support', true),
        'worth'              => get_post_meta($post->ID, '_review_worth', true),
        'recommend'          => get_post_meta($post->ID, '_review_recommend', true),
        'refund'             => get_post_meta($post->ID, '_review_refund', true),
        'proof'              => get_post_meta($post->ID, '_review_proof', true),
    ];

    // Status checkboxes
    $statuses = ['active','verified','verified_purchase','rising_voice','top_voice'];
    echo '<p><strong>Status:</strong><br>';
    foreach ($statuses as $status) {
        echo '<label><input type="checkbox" name="review_status[]" value="'.$status.'" '.checked(in_array($status, $meta['status']), true, false).'> '.ucwords(str_replace('_',' ', $status)).'</label><br>';
    }
    echo '</p>';

    // Review Date
    echo '<p><label><strong>Review Date</strong></label><br>';
    echo '<input type="date" name="review_date" value="'.esc_attr($meta['review_date']).'" style="width:200px;"></p>';

    // Reviewer (select WP user)
    $users = get_users(['role__not_in'=>['Administrator']]);
    echo '<p><label><strong>Reviewer</strong></label><br>';
    echo '<select name="reviewer" style="width:100%;">';
    echo '<option value="">— Select User —</option>';
    foreach ($users as $user) {
        echo '<option value="'.$user->ID.'" '.selected($meta['reviewer'], $user->ID, false).'>'.esc_html($user->display_name).'</option>';
    }
    echo '</select></p>';

    // Course
    $courses = get_posts(['post_type'=>'course','numberposts'=>-1]);
    echo '<p><label><strong>Which Course</strong></label><br>';
    echo '<select name="review_course" style="width:100%;">';
    echo '<option value="">— Select Course —</option>';
    foreach ($courses as $course) {
        echo '<option value="'.$course->ID.'" '.selected($meta['course_id'], $course->ID, false).'>'.esc_html($course->post_title).'</option>';
    }
    echo '</select></p>';

    // Rating (stars)
    echo '<p><label><strong>Rating</strong></label><br>';
    for ($i=1;$i<=5;$i++) {
        echo '<label><input type="radio" name="review_rating" value="'.$i.'" '.checked($meta['rating'],$i,false).'> '.$i.'★</label> ';
    }
    echo '</p>';

    // Textareas
    $fields = [
        'message'   => 'Review Message',
        'good'      => 'What was good?',
        'bad'       => 'What was bad?',
        'quality'   => 'Content Quality',
        'support'   => 'Instructor & Support',
        'worth'     => 'Worth Money?',
        'recommend' => 'Recommend this course?',
        'refund'    => 'Refund Experience',
    ];
    foreach ($fields as $key=>$label) {
        echo '<p><label><strong>'.$label.'</strong></label><br>';
        echo '<textarea name="review_'.$key.'" rows="3" style="width:100%;">'.esc_textarea($meta[$key]).'</textarea></p>';
    }

    // Course Outcomes (multi-select)
    $outcomes = [
        'Earned Income',
        'Career Boost',
        'Built Project',
        'Improved Skill',
        'Gained Confidence',
        'No Impact'
    ];

    $current_outcomes = (array) $meta['outcome']; // ensure it's always array

    echo '<p><label><strong>Course Outcomes</strong></label><br>';
    echo '<select name="review_outcome[]" multiple style="width:100%; height:120px;">';
    foreach ($outcomes as $out) {
        echo '<option value="'.$out.'" '.selected(in_array($out, $current_outcomes), true, false).'>'.$out.'</option>';
    }
    echo '</select><br><small>Hold Ctrl (Windows) or Cmd (Mac) to select multiple</small></p>';

    // Proof of enrollment (image upload)
    echo '<p><label><strong>Proof of Enrollment</strong></label><br>';
    echo '<input type="text" name="review_proof" value="'.esc_attr($meta['proof']).'" style="width:80%;"> ';
    echo '<button class="button upload_review_proof">Upload</button></p>';

    // JS uploader
    ?>
<script>
jQuery(document).ready(function($) {
    $('.upload_review_proof').on('click', function(e) {
        e.preventDefault();
        var button = $(this);
        var input = button.prev('input');
        var frame = wp.media({
            title: 'Select Image',
            multiple: false,
            library: {
                type: 'image'
            }
        });
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            input.val(attachment.url);
        });
        frame.open();
    });
});
</script>
<?php
}

function reviewmvp_save_review_meta($post_id) {
    if (!isset($_POST['reviewmvp_review_meta_nonce']) ||
        !wp_verify_nonce($_POST['reviewmvp_review_meta_nonce'], 'reviewmvp_save_review_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = [
        'review_date','reviewer','review_course','review_rating','review_message',
        'review_good','review_bad','review_quality','review_support',
        'review_worth','review_recommend','review_refund','review_proof'
    ];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $val = is_array($_POST[$field]) ? array_map('sanitize_text_field', $_POST[$field]) : sanitize_text_field($_POST[$field]);
            update_post_meta($post_id, '_'.$field, $val);
        }
    }

    // Status (multiple checkboxes)
    if (isset($_POST['review_status'])) {
        $statuses = array_map('sanitize_text_field', (array)$_POST['review_status']);
        update_post_meta($post_id, '_review_status', $statuses);
    } else {
        delete_post_meta($post_id, '_review_status');
    }

    // Course Outcomes (multiple select)
    if (isset($_POST['review_outcome'])) {
        $outcomes = array_map('sanitize_text_field', (array) $_POST['review_outcome']);
        update_post_meta($post_id, '_review_outcome', $outcomes);
    } else {
        delete_post_meta($post_id, '_review_outcome');
    }
}
add_action('save_post_course_review', 'reviewmvp_save_review_meta');