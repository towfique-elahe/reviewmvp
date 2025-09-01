<?php
/**
 * Function File Name: Component Add Course
 * 
 * A front-end form to submit a new course (status: pending).
 */

// Shortcode: [add_course]
function reviewmvp_add_course_form() {
    ob_start(); ?>
<form method="post" class="reviewmvp-add-course" id="reviewmvpAddCourseForm">
    <?php wp_nonce_field('reviewmvp_add_course_action', 'reviewmvp_add_course_nonce'); ?>

    <div class="form-group">
        <label for="course_name">Course Name (required)</label>
        <input type="text" name="course_name" id="course_name" placeholder="E.g., “Python Bootcamp by Jane Doe”"
            required>
    </div>

    <div class="form-group">
        <label for="course_url">Course Link (required)</label>
        <input type="url" name="course_url" id="course_url" placeholder="Paste the official course page link" required>
    </div>

    <div class="form-group">
        <label for="course_instructor">Instructor Name (required)</label>
        <input type="text" name="course_instructor" id="course_instructor" placeholder="E.g., “Jane Doe”" required>
    </div>

    <div class="button-group">
        <button type="submit">Submit Course</button>
    </div>

    <div id="reviewmvpFormMessage" style="margin-top:10px;"></div>
</form>

<script>
(function($) {
    $('#reviewmvpAddCourseForm').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let messageBox = $('#reviewmvpFormMessage');
        messageBox.html('').css('color', '');

        // simple validation
        let name = $('#course_name').val().trim();
        let url = $('#course_url').val().trim();
        let instructor = $('#course_instructor').val().trim();

        if (name === '' || url === '' || instructor === '') {
            messageBox.css('color', 'crimson').text('All fields are required.');
            return;
        }

        // AJAX submit
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: form.serialize() + '&action=reviewmvp_submit_course',
            success: function(response) {
                if (response.success) {
                    window.location.href = "<?php echo site_url('/course-added/'); ?>";
                } else {
                    messageBox.css('color', 'crimson').text(response.data ||
                        'Submission failed. Try again.');
                }
            },
            error: function() {
                messageBox.css('color', 'crimson').text('Server error. Please try again.');
            }
        });
    });
})(jQuery);
</script>
<?php
    return ob_get_clean();
}
add_shortcode('add_course', 'reviewmvp_add_course_form');


/**
 * Handle AJAX submission
 */
function reviewmvp_handle_course_submission() {
    if (!isset($_POST['reviewmvp_add_course_nonce']) || 
        !wp_verify_nonce($_POST['reviewmvp_add_course_nonce'], 'reviewmvp_add_course_action')) {
        wp_send_json_error('Security check failed.');
    }

    $course_name       = sanitize_text_field($_POST['course_name'] ?? '');
    $course_url        = esc_url_raw($_POST['course_url'] ?? '');
    $course_instructor = sanitize_text_field($_POST['course_instructor'] ?? '');
    $reviewer_value    = 'guest';

    if (empty($course_name) || empty($course_url) || empty($course_instructor)) {
        wp_send_json_error('All fields are required.');
    }

    $post_id = wp_insert_post(array(
        'post_title'   => $course_name,
        'post_type'    => 'course',
        'post_status'  => 'pending',
    ));

    // If user logged in and has reviewer role, assign them
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        if (in_array('reviewer', (array) $user->roles)) {
            $reviewer_value = $user->ID;
        }
    }

    if ($post_id) {
        update_post_meta($post_id, '_course_reviewer', $reviewer_value);
        update_post_meta($post_id, '_course_link', $course_url);
        update_post_meta($post_id, '_course_instructor', array(
            'name' => $course_instructor,
        ));

        // Store last added course ID for guest
        if ($reviewer_value === 'guest') {
            if (!session_id()) {
                session_start();
            }
            $_SESSION['guest_last_course_id'] = $post_id;
        }

        wp_send_json_success('Course submitted successfully.');
    }

    wp_send_json_error('Something went wrong while saving.');
}
add_action('wp_ajax_reviewmvp_submit_course', 'reviewmvp_handle_course_submission');
add_action('wp_ajax_nopriv_reviewmvp_submit_course', 'reviewmvp_handle_course_submission');