<?php

function reviewmvp_register_course_post_type() {

    $base = get_option('course_permalink_base', 'course');
    $archive_slug = 'courses';
    
    $labels = array(
        'name'                  => _x('Courses', 'Post Type General Name', 'reviewmvp'),
        'singular_name'         => _x('Course', 'Post Type Singular Name', 'reviewmvp'),
        'menu_name'             => __('Courses', 'reviewmvp'),
        'name_admin_bar'        => __('Course', 'reviewmvp'),
        'all_items'             => __('All Courses', 'reviewmvp'),
        'add_new_item'          => __('Add New Course', 'reviewmvp'),
        'add_new'               => __('Add New', 'reviewmvp'),
        'new_item'              => __('New Course', 'reviewmvp'),
        'edit_item'             => __('Edit Course', 'reviewmvp'),
        'update_item'           => __('Update Course', 'reviewmvp'),
        'view_item'             => __('View Course', 'reviewmvp'),
        'search_items'          => __('Search Course', 'reviewmvp'),
        'not_found'             => __('No courses found', 'reviewmvp'),
        'not_found_in_trash'    => __('No courses found in Trash', 'reviewmvp'),
    );

    $args = array(
        'label'                 => __('Course', 'reviewmvp'),
        'description'           => __('Custom Post Type for Courses', 'reviewmvp'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'excerpt', 'thumbnail', 'comments', 'revisions', 'author', 'custom-fields'),
        'hierarchical'          => false,
        'public'                => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-welcome-learn-more',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => $archive_slug,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'show_in_rest'          => true,
        'capability_type'       => 'post',
        'rewrite'               => array(
            'slug' => $base,
            'with_front' => false
        ),
    );

    register_post_type('course', $args);
}
add_action('init', 'reviewmvp_register_course_post_type');

function reviewmvp_register_course_category_taxonomy() {
    $labels = array(
        'name'              => _x('Course Categories', 'taxonomy general name', 'reviewmvp'),
        'singular_name'     => _x('Course Category', 'taxonomy singular name', 'reviewmvp'),
        'search_items'      => __('Search Course Categories', 'reviewmvp'),
        'all_items'         => __('All Course Categories', 'reviewmvp'),
        'parent_item'       => __('Parent Category', 'reviewmvp'),
        'parent_item_colon' => __('Parent Category:', 'reviewmvp'),
        'edit_item'         => __('Edit Course Category', 'reviewmvp'),
        'update_item'       => __('Update Course Category', 'reviewmvp'),
        'add_new_item'      => __('Add New Course Category', 'reviewmvp'),
        'new_item_name'     => __('New Course Category Name', 'reviewmvp'),
        'menu_name'         => __('Course Categories', 'reviewmvp'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array('slug' => 'course-category'),
    );

    register_taxonomy('course_category', array('course'), $args);
}
add_action('init', 'reviewmvp_register_course_category_taxonomy');

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

function reviewmvp_render_course_meta_box($post) {
    wp_nonce_field('reviewmvp_save_course_meta_box', 'reviewmvp_course_meta_box_nonce');

    $meta = [
        'course_provider'    => get_post_meta($post->ID, '_course_provider', true),
        'course_short_desc'  => get_post_meta($post->ID, '_course_short_desc', true),
        'course_price'       => get_post_meta($post->ID, '_course_price', true),
        'course_duration'    => get_post_meta($post->ID, '_course_duration', true),
        'course_certificate' => get_post_meta($post->ID, '_course_certificate', true),
        'course_refundable'  => get_post_meta($post->ID, '_course_refundable', true),
        'course_link'        => get_post_meta($post->ID, '_course_link', true),
        'course_level'       => get_post_meta($post->ID, '_course_level', true),
        'course_language'    => (array) get_post_meta($post->ID, '_course_language', true),
        'course_instructor'  => get_post_meta($post->ID, '_course_instructor', true),
        'course_reviewer'    => get_post_meta($post->ID, '_course_reviewer', true),
    ];

    $meta['course_instructor'] = is_array($meta['course_instructor']) ? $meta['course_instructor'] : [];

    $defaults = [
        'name'     => '',
        'position' => '',
        'details'  => '',
        'facebook' => '',
        'instagram'=> '',
        'linkedin' => '',
        'twitter'  => '',
        'youtube'  => '',
    ];

    $meta['course_instructor'] = array_merge($defaults, $meta['course_instructor']);

    ?>
<p>
    <label><strong>Platform</strong></label><br>
    <input type="text" name="course_provider" value="<?php echo esc_attr($meta['course_provider']); ?>"
        placeholder="e.g., Coursera, Udemy, Skillshare" style="width:100%;">
</p>

<p>
    <label><strong>Short Description</strong></label><br>
    <textarea name="course_short_desc" rows="3" style="width:100%;"
        placeholder="Enter a brief summary of the course..."><?php 
        echo esc_textarea(get_post_meta($post->ID, '_course_short_desc', true)); 
    ?></textarea>
</p>

<p>
    <label><strong>Course Price ($)</strong></label><br>
    <input type="number" step="0.01" name="course_price" value="<?php echo esc_attr($meta['course_price']); ?>"
        placeholder="e.g., 49.99" style="width:100%;">
</p>

<p>
    <label><strong>Course Duration (hours)</strong></label><br>
    <input type="number" name="course_duration" value="<?php echo esc_attr($meta['course_duration']); ?>"
        placeholder="e.g., 20" style="width:100%;">
</p>

<p>
    <label><strong>Certificate</strong></label><br>
    <select name="course_certificate" style="width:100%;">
        <option value="">— Select —</option>
        <option value="Yes" <?php selected($meta['course_certificate'], 'Yes'); ?>>Yes</option>
        <option value="No" <?php selected($meta['course_certificate'], 'No'); ?>>No</option>
    </select>
</p>

<p>
    <label><strong>Refundable</strong></label><br>
    <select name="course_refundable" style="width:100%;">
        <option value="">— Select —</option>
        <option value="Yes" <?php selected($meta['course_refundable'], 'Yes'); ?>>Yes</option>
        <option value="No" <?php selected($meta['course_refundable'], 'No'); ?>>No</option>
    </select>
</p>

<p>
    <label><strong>Course Link</strong></label><br>
    <input type="url" name="course_link" value="<?php echo esc_attr($meta['course_link']); ?>"
        placeholder="https://example.com/course" style="width:100%;">
</p>

<p>
    <label><strong>Level</strong></label><br>
    <select name="course_level" style="width:100%;">
        <option value="">— Select Level —</option>
        <option value="beginner" <?php selected($meta['course_level'], 'beginner'); ?>>Beginner</option>
        <option value="intermediate" <?php selected($meta['course_level'], 'intermediate'); ?>>Intermediate</option>
        <option value="advance" <?php selected($meta['course_level'], 'advance'); ?>>Advance</option>
    </select>
</p>

<p>
    <label><strong>Language(s)</strong></label><br>
    <select name="course_language[]" multiple style="width:100%; height: 80px;"
        aria-placeholder="Select one or more languages">
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
        value="<?php echo esc_attr($meta['course_instructor']['name']); ?>" placeholder="Instructor full name"
        style="width:100%;">
</p>
<p>
    <label><strong>Position</strong></label><br>
    <input type="text" name="course_instructor[position]"
        value="<?php echo esc_attr($meta['course_instructor']['position']); ?>" placeholder="e.g., Senior UX Designer"
        style="width:100%;">
</p>
<p>
    <label><strong>Details</strong></label><br>
    <textarea name="course_instructor[details]" rows="4" style="width:100%;"
        placeholder="Short bio or instructor details..."><?php 
        echo esc_textarea($meta['course_instructor']['details']); ?></textarea>
</p>

<hr>
<h4>Social Links</h4>
<p><label><strong>Facebook</strong></label><br>
    <input type="url" name="course_instructor[facebook]"
        value="<?php echo esc_attr($meta['course_instructor']['facebook'] ?? ''); ?>"
        placeholder="https://facebook.com/instructor" style="width:100%;">
</p>
<p><label><strong>Instagram</strong></label><br>
    <input type="url" name="course_instructor[instagram]"
        value="<?php echo esc_attr($meta['course_instructor']['instagram'] ?? ''); ?>"
        placeholder="https://instagram.com/instructor" style="width:100%;">
</p>
<p><label><strong>LinkedIn</strong></label><br>
    <input type="url" name="course_instructor[linkedin]"
        value="<?php echo esc_attr($meta['course_instructor']['linkedin'] ?? ''); ?>"
        placeholder="https://linkedin.com/in/instructor" style="width:100%;">
</p>
<p><label><strong>Twitter / X</strong></label><br>
    <input type="url" name="course_instructor[twitter]"
        value="<?php echo esc_attr($meta['course_instructor']['twitter'] ?? ''); ?>"
        placeholder="https://twitter.com/instructor" style="width:100%;">
</p>
<p><label><strong>YouTube</strong></label><br>
    <input type="url" name="course_instructor[youtube]"
        value="<?php echo esc_attr($meta['course_instructor']['youtube'] ?? ''); ?>"
        placeholder="https://youtube.com/@instructor" style="width:100%;">
</p>


<hr>
<h3>Suggestion Details</h3>
<p>
    <label><strong>Suggested by</strong></label><br>
    <select name="course_reviewer" style="width:100%;">
        <option value="">— Select Reviewer —</option>
        <?php
            $selected_reviewer = $meta['course_reviewer'];
            $reviewers = get_users(['role' => 'reviewer']);
            foreach ($reviewers as $rev) {
            echo '<option value="'.$rev->ID.'" '.selected($selected_reviewer, $rev->ID, false).'>
                '.esc_html($rev->display_name).'</option>';
            }
        ?>
    </select>
</p>

<hr>
<p><em>Course Description uses the post content (main editor).</em></p>
<?php
}

function reviewmvp_save_course_meta_box($post_id) {
    if (!isset($_POST['reviewmvp_course_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['reviewmvp_course_meta_box_nonce'], 'reviewmvp_save_course_meta_box')) {
        return;
    }

    if (isset($_POST['course_reviewer']) && $_POST['course_reviewer'] !== '') {
        update_post_meta($post_id, '_course_reviewer', intval($_POST['course_reviewer']));
    } else {
        update_post_meta($post_id, '_course_reviewer', 'guest');
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['course_provider','course_short_desc','course_price','course_duration','course_certificate','course_refundable','course_link','course_level'];

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

    if (isset($_POST['course_language'])) {
        $langs = array_map('sanitize_text_field', (array)$_POST['course_language']);
        update_post_meta($post_id, '_course_language', $langs);
    } else {
        delete_post_meta($post_id, '_course_language');
    }

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
        'labels'          => $labels,
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true, 
        'menu_position'   => 6,
        'menu_icon'       => 'dashicons-star-filled',
        'supports'        => array('title', 'editor', 'author'),
        'capability_type' => 'post',
        'has_archive'     => false,
    );

    register_post_type('course_review', $args);
}
add_action('init', 'reviewmvp_register_review_post_type');

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
        'status'    => (array) get_post_meta($post->ID, '_review_status', true),
        'review_date' => get_post_meta($post->ID, '_review_date', true),
        'reviewer'    => get_post_meta($post->ID, '_reviewer', true),
        'course_id'   => get_post_meta($post->ID, '_review_course', true),
        'rating'      => get_post_meta($post->ID, '_review_rating', true),
        'message'     => get_post_meta($post->ID, '_review_message', true),
        'good'        => get_post_meta($post->ID, '_review_good', true),
        'bad'         => get_post_meta($post->ID, '_review_bad', true),
        'outcome'     => get_post_meta($post->ID, '_review_outcome', true),
        'quality'     => get_post_meta($post->ID, '_review_quality', true),
        'level'       => get_post_meta($post->ID, '_review_level', true),
        'support'     => get_post_meta($post->ID, '_review_support', true),
        'worth'       => get_post_meta($post->ID, '_review_worth', true),
        'recommend'   => get_post_meta($post->ID, '_review_recommend', true),
        'refund'      => get_post_meta($post->ID, '_review_refund', true),
        'proof'       => get_post_meta($post->ID, '_review_proof', true),
        'video'       => get_post_meta($post->ID, '_review_video', true),
        'linkedin'    => get_post_meta($post->ID, '_review_linkedin', true),
    ];

    $statuses = ['verified','verified_purchase','rising_voice','top_voice','anonymous'];
    echo '<p><strong>Status:</strong><br>';
    foreach ($statuses as $status) {
        echo '<label><input type="checkbox" name="review_status[]" value="'.$status.'" '.checked(in_array($status, $meta['status']), true, false).'> '.ucwords(str_replace('_',' ', $status)).'</label><br>';
    }
    echo '</p>';

    echo '<p><label><strong>Review Date</strong></label><br>';
    echo '<input type="date" name="review_date" value="'.esc_attr($meta['review_date']).'" style="width:200px;"></p>';

    $users = get_users(['role__not_in'=>['Administrator']]);
    echo '<p><label><strong>Reviewer</strong></label><br>';
    echo '<select name="reviewer" style="width:100%;">';
    echo '<option value="">— Select User —</option>';
    foreach ($users as $user) {
        echo '<option value="'.$user->ID.'" '.selected($meta['reviewer'], $user->ID, false).'>'.esc_html($user->display_name).'</option>';
    }
    echo '</select></p>';

    $courses = get_posts(['post_type'=>'course','numberposts'=>-1]);
    echo '<p><label><strong>Which Course</strong></label><br>';
    echo '<select name="review_course" style="width:100%;">';
    echo '<option value="">— Select Course —</option>';
    foreach ($courses as $course) {
        echo '<option value="'.$course->ID.'" '.selected($meta['course_id'], $course->ID, false).'>'.esc_html($course->post_title).'</option>';
    }
    echo '</select></p>';

    echo '<p><label><strong>Rating</strong></label><br>';
    for ($i=1;$i<=5;$i++) {
        echo '<label><input type="radio" name="review_rating" value="'.$i.'" '.checked($meta['rating'],$i,false).'> '.$i.'★</label> ';
    }
    echo '</p>';

    $fields = [
        'message'   => 'Review Message',
        'good'      => 'What was good?',
        'bad'       => 'What was bad?',
        'quality'   => 'Content Quality',
        'support'   => 'Instructor & Support',
        'refund'    => 'Refund Experience',
    ];
    foreach ($fields as $key=>$label) {
        echo '<p><label><strong>'.$label.'</strong></label><br>';
        echo '<textarea name="review_'.$key.'" rows="3" style="width:100%;" placeholder="Enter '.$label.'">'.esc_textarea($meta[$key]).'</textarea></p>';
    }

    echo '<p><label><strong>What level did this course feel like to you?</strong></label><br>';
    echo '<select name="review_level" style="width:100%;">';
    echo '<option value="">— Select Level —</option>';
    $options_level = ['Beginner','Intermediate','Advance'];
    foreach ($options_level as $opt) {
        echo '<option value="'.$opt.'" '.selected($meta['level'], $opt, false).'>'.$opt.'</option>';
    }
    echo '</select></p>';

    echo '<p><label><strong>Worth Money?</strong></label><br>';
    echo '<select name="review_worth" style="width:100%;">';
    echo '<option value="">— Select Option —</option>';
    $options_worth = ['Yes, good value','No, overpriced'];
    foreach ($options_worth as $opt) {
        echo '<option value="'.$opt.'" '.selected($meta['worth'], $opt, false).'>'.$opt.'</option>';
    }
    echo '</select></p>';

    echo '<p><label><strong>Recommend this course?</strong></label><br>';
    echo '<select name="review_recommend" style="width:100%;">';
    echo '<option value="">— Select Option —</option>';
    $options_recommend = ['Yes, I’d recommend it','No, I wouldn’t'];
    foreach ($options_recommend as $opt) {
        echo '<option value="'.$opt.'" '.selected($meta['recommend'], $opt, false).'>'.$opt.'</option>';
    }
    echo '</select></p>';

    $outcomes = [
        'Earned Income',
        'Career Boost',
        'Built Project',
        'Improved Skill',
        'Gained Confidence',
        'No Impact'
    ];

    $current_outcomes = (array) $meta['outcome'];

    echo '<p><label><strong>Course Outcomes</strong></label><br>';
    echo '<select name="review_outcome[]" multiple style="width:100%; height:120px;">';
    foreach ($outcomes as $out) {
        echo '<option value="'.$out.'" '.selected(in_array($out, $current_outcomes), true, false).'>'.$out.'</option>';
    }
    echo '</select><br><small>Hold Ctrl (Windows) or Cmd (Mac) to select multiple</small></p>';

    echo '<p><label><strong>Proof of Enrollment</strong></label><br>';
    echo '<input type="text" name="review_proof" value="'.esc_attr($meta['proof']).'" placeholder="Enter media URL of proof" style="width:80%;"> ';
    echo '<button class="button upload_review_proof">Upload</button></p>';

    echo '<p><label><strong>Video Review</strong></label><br>';
    echo '<input type="text" name="review_video" value="'.esc_attr($meta['video']).'" placeholder="Enter media URL of video"  style="width:80%;"> ';
    echo '<button class="button upload_review_video">Upload</button></p>';

    echo '<p><label><strong>LinkedIn Profile</strong></label><br>';
    echo '<input type="url" name="review_linkedin" value="'.esc_attr($meta['linkedin']).'" style="width:100%;" placeholder="https://linkedin.com/in/...">';
    echo '</p>';

    ?>
<script>
jQuery(document).ready(function($) {
    function bindUploader(buttonClass, inputSelector) {
        $(buttonClass).on('click', function(e) {
            e.preventDefault();
            var input = $(this).prev('input');
            var frame = wp.media({
                title: 'Select File',
                multiple: false
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                input.val(attachment.url);
            });
            frame.open();
        });
    }
    bindUploader('.upload_review_proof', 'input[name="review_proof"]');
    bindUploader('.upload_review_video', 'input[name="review_video"]');
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
        'review_good','review_bad','review_quality','review_support','review_level',
        'review_worth','review_recommend','review_refund','review_proof','review_video','review_linkedin'
    ];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $val = is_array($_POST[$field]) ? array_map('sanitize_text_field', $_POST[$field]) : sanitize_text_field($_POST[$field]);
            update_post_meta($post_id, '_'.$field, $val);
        }
    }

    if (isset($_POST['review_status'])) {
        $statuses = array_map('sanitize_text_field', (array)$_POST['review_status']);
        update_post_meta($post_id, '_review_status', $statuses);
    } else {
        delete_post_meta($post_id, '_review_status');
    }

    if (isset($_POST['review_outcome'])) {
        $outcomes = array_map('sanitize_text_field', (array) $_POST['review_outcome']);
        update_post_meta($post_id, '_review_outcome', $outcomes);
    } else {
        delete_post_meta($post_id, '_review_outcome');
    }
}
add_action('save_post_course_review', 'reviewmvp_save_review_meta');

/**
 * Register a global "rejected" post status and wire it up for Course + Review CPTs.
 */
add_action('init', function () {
    register_post_status('rejected', array(
        'label'                     => _x('Rejected', 'post status', 'reviewmvp'),
        'public'                    => false,
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => true, // show count on All list
        'show_in_admin_status_list' => true, // show "Rejected (N)" filter when N>0
        'label_count'               => _n_noop(
            'Rejected <span class="count">(%s)</span>',
            'Rejected <span class="count">(%s)</span>',
            'reviewmvp'
        ),
    ));
});

/**
 * Show "Rejected" badge in the list table next to post titles.
 */
add_filter('display_post_states', function ($states, $post) {
    if ($post->post_status === 'rejected') {
        $states['rejected'] = __('Rejected', 'reviewmvp');
    }
    return $states;
}, 10, 2);

/**
 * Row actions: Reject / Restore for course + course_review.
 */
add_filter('post_row_actions', function ($actions, $post) {
    if (!in_array($post->post_type, array('course','course_review'), true)) {
        return $actions;
    }
    if (!current_user_can('edit_post', $post->ID)) {
        return $actions;
    }

    if ($post->post_status !== 'rejected') {
        $url = wp_nonce_url(
            add_query_arg(array(
                'action'    => 'mark_rejected',
                'post'      => $post->ID,
                'post_type' => $post->post_type,
            ), admin_url('edit.php')),
            'mark_rejected_' . $post->ID
        );
        $actions['mark_rejected'] = '<a href="' . esc_url($url) . '">' . esc_html__('Reject', 'reviewmvp') . '</a>';
    } else {
        $url = wp_nonce_url(
            add_query_arg(array(
                'action'    => 'unreject',
                'post'      => $post->ID,
                'post_type' => $post->post_type,
            ), admin_url('edit.php')),
            'unreject_' . $post->ID
        );
        $actions['unreject'] = '<a href="' . esc_url($url) . '">' . esc_html__('Restore', 'reviewmvp') . '</a>';
    }
    return $actions;
}, 10, 2);

/**
 * Handle row actions.
 */
add_action('load-edit.php', function () {
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->post_type, array('course','course_review'), true)) {
        return;
    }

    if (empty($_GET['action']) || empty($_GET['post'])) {
        return;
    }
    $post_id = (int) $_GET['post'];
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if ($_GET['action'] === 'mark_rejected' && wp_verify_nonce($_GET['_wpnonce'] ?? '', 'mark_rejected_' . $post_id)) {
        wp_update_post(array('ID' => $post_id, 'post_status' => 'rejected'));
        wp_redirect(add_query_arg(array('updated' => 1, 'marked_rejected' => 1), remove_query_arg(array('action','post','_wpnonce'))));
        exit;
    }
    if ($_GET['action'] === 'unreject' && wp_verify_nonce($_GET['_wpnonce'] ?? '', 'unreject_' . $post_id)) {
        // Restore to Pending (change to 'draft' if you prefer)
        wp_update_post(array('ID' => $post_id, 'post_status' => 'pending'));
        wp_redirect(add_query_arg(array('updated' => 1, 'unrejected' => 1), remove_query_arg(array('action','post','_wpnonce'))));
        exit;
    }
});

/**
 * Bulk actions for both CPTs.
 */
function reviewmvp_register_bulk_reject($actions) {
    $actions['mark_rejected'] = __('Mark as Rejected', 'reviewmvp');
    $actions['unreject']      = __('Restore from Rejected', 'reviewmvp');
    return $actions;
}
add_filter('bulk_actions-edit-course', 'reviewmvp_register_bulk_reject');
add_filter('bulk_actions-edit-course_review', 'reviewmvp_register_bulk_reject');

function reviewmvp_handle_bulk_reject($redirect_to, $doaction, $post_ids) {
    if ($doaction === 'mark_rejected') {
        $count = 0;
        foreach ($post_ids as $id) {
            if (current_user_can('edit_post', $id)) {
                wp_update_post(array('ID' => $id, 'post_status' => 'rejected'));
                $count++;
            }
        }
        return add_query_arg('bulk_marked_rejected', $count, $redirect_to);
    }
    if ($doaction === 'unreject') {
        $count = 0;
        foreach ($post_ids as $id) {
            if (current_user_can('edit_post', $id)) {
                wp_update_post(array('ID' => $id, 'post_status' => 'pending')); // or 'draft'
                $count++;
            }
        }
        return add_query_arg('bulk_unrejected', $count, $redirect_to);
    }
    return $redirect_to;
}
add_filter('handle_bulk_actions-edit-course', 'reviewmvp_handle_bulk_reject', 10, 3);
add_filter('handle_bulk_actions-edit-course_review', 'reviewmvp_handle_bulk_reject', 10, 3);

/**
 * Admin notices for actions.
 */
add_action('admin_notices', function () {
    if (!empty($_REQUEST['marked_rejected'])) {
        echo '<div class="updated notice is-dismissible"><p>' . esc_html__('Item rejected.', 'reviewmvp') . '</p></div>';
    }
    if (!empty($_REQUEST['unrejected'])) {
        echo '<div class="updated notice is-dismissible"><p>' . esc_html__('Item restored from Rejected.', 'reviewmvp') . '</p></div>';
    }
    if (!empty($_REQUEST['bulk_marked_rejected'])) {
        $n = (int) $_REQUEST['bulk_marked_rejected'];
        printf('<div class="updated notice is-dismissible"><p>' . esc_html(_n('%s item rejected.', '%s items rejected.', $n, 'reviewmvp')) . '</p></div>', number_format_i18n($n));
    }
    if (!empty($_REQUEST['bulk_unrejected'])) {
        $n = (int) $_REQUEST['bulk_unrejected'];
        printf('<div class="updated notice is-dismissible"><p>' . esc_html(_n('%s item restored from Rejected.', '%s items restored from Rejected.', $n, 'reviewmvp')) . '</p></div>', number_format_i18n($n));
    }
});

/**
 * Classic editor: add "Rejected" to the status dropdown in submit box for our CPTs.
 * (Gutenberg doesn’t expose a simple PHP hook for this; if you need block editor UI, we can add a small JS plugin.)
 */
function reviewmvp_status_dropdown_js() {
    global $post, $typenow;
    $pt = $typenow ?: ($post->post_type ?? '');
    if (!in_array($pt, array('course', 'course_review'), true)) return;
    ?>
<script>
jQuery(function($) {
    var $st = $('#post_status');
    if ($st.length && !$st.find('option[value="rejected"]').length) {
        $st.append('<option value="rejected"><?php echo esc_js(__('Rejected', 'reviewmvp')); ?></option>');
    }
    <?php if (!empty($post) && $post->post_status === 'rejected') : ?>
    $('#post-status-display').text('<?php echo esc_js(__('Rejected', 'reviewmvp')); ?>');
    <?php endif; ?>
});
</script>
<?php
}
add_action('admin_footer-post.php', 'reviewmvp_status_dropdown_js');
add_action('admin_footer-post-new.php', 'reviewmvp_status_dropdown_js');

/**
 * Add pending-count bubbles to Courses and Reviews admin menus.
 */
add_action('admin_menu', function () {
    if (!is_admin()) return;

    global $menu, $submenu;

    // Helper to get pending count safely
    $get_pending = function (string $post_type): int {
        $counts = wp_count_posts($post_type);
        return $counts && isset($counts->pending) ? (int) $counts->pending : 0;
    };

    // Helper to render WP-style bubble
    $bubble = function (int $count): string {
        if ($count <= 0) return '';
        $num = number_format_i18n($count);
        // 'awaiting-mod' uses the red comment-style badge; works nicely for "pending"
        return ' <span class="awaiting-mod count-' . esc_attr($num) . '"><span class="pending-count">' . esc_html($num) . '</span></span>';
    };

    // Target menus for your CPTs
    $targets = [
        'course'        => 'edit.php?post_type=course',
        'course_review' => 'edit.php?post_type=course_review',
    ];

    foreach ($targets as $pt => $slug) {
        // Only show if the current user can see/edit this post type
        $pto = get_post_type_object($pt);
        if (!$pto || empty($pto->cap->edit_posts) || !current_user_can($pto->cap->edit_posts)) {
            continue;
        }

        $pending = $get_pending($pt);
        if ($pending <= 0) continue;

        $badge = $bubble($pending);

        // Add bubble on the top-level menu item
        foreach ($menu as $i => $m) {
            if (isset($m[2]) && $m[2] === $slug) {
                // $m[0] is the menu title label
                $menu[$i][0] .= $badge;
                break;
            }
        }

        // Also add to the "All {post_type}s" submenu item if present
        if (!empty($submenu[$slug])) {
            foreach ($submenu[$slug] as $j => $sub) {
                // Submenu slug for the list table equals the top-level slug
                if (isset($sub[2]) && $sub[2] === $slug) {
                    $submenu[$slug][$j][0] .= $badge;
                    break;
                }
            }
        }
    }
}, 999); // run late so the menu already exists

// Color the "Reject" row action link red on the list tables
add_action('admin_head-edit.php', function () { ?>
<style>
/* Row action link */
.row-actions a[href*="action=mark_rejected"] {
    color: #d63638 !important;
    /* WP danger red */
    font-weight: 600;
}

.row-actions a[href*="action=mark_rejected"]:hover {
    color: #b32d2e !important;
}

/* (Optional) Make the Rejected filter tab red too */
.subsubsub a[href*="post_status=rejected"],
.subsubsub .current[href*="post_status=rejected"] {
    color: #d63638 !important;
}
</style>
<?php });