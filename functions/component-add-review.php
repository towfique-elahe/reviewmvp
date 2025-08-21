<?php
/**
 * Function File Name: Component Add Review
 * 
 * A front-end form to submit a new review (status: pending).
 */

// Shortcode: [add_review]
function reviewmvp_add_review_form() {
    ob_start(); ?>
<form method="post" class="reviewmvp-add-review" id="reviewmvpAddReviewForm">
    <?php wp_nonce_field('reviewmvp_add_review_action', 'reviewmvp_add_review_nonce'); ?>

    <div id="pageOne" class="page">
        <h3 class="form-section-heading">Course info</h3>

        <div class="form-group">
            <label for="review_course">Course title</label>
            <select name="review_course" id="review_course" class="reviewmvp-select2" required>
                <option value="">— Select Course —</option>
                <?php 
        $courses = get_posts([
            'post_type'   => 'course',
            'numberposts' => -1,
            'orderby'     => 'title',
            'order'       => 'ASC'
        ]);
        foreach ($courses as $course) {
            $platform = get_post_meta($course->ID, '_course_provider', true); // 👈 stored platform
            echo '<option value="'.$course->ID.'" data-platform="'.esc_attr($platform).'">'.esc_html($course->post_title).'</option>';
        }
        ?>
            </select>
        </div>

        <div class="form-group">
            <label for="review_course_platform">Platform</label>
            <input type="text" name="review_course_platform" id="review_course_platform"
                placeholder="e.g., Udemy, Coursera, etc." required>
        </div>

        <div class="form-group">
            <h4 class="form-section-heading">How was your experience?</h4>
            <p class="form-para">Share your overall thoughts and what you achieved</p>
        </div>

        <div class="form-group">
            <label for="">Overall rating</label>
            <div class="form-star-group">
                <?php for ($i=1;$i<=5;$i++): ?>
                <label><input type="radio" name="review_rating" value="<?php echo $i; ?>">
                    <?php echo $i; ?>★
                </label>
                <?php endfor; ?>
                <p class="form-tip">click to rate</p>
            </div>
            <textarea name="review_message" id="review_message" rows="4" required></textarea>
            <p class="form-para">Tell us what you liked and what could have been better</p>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="review_good">What was good?</label>
                <textarea name="review_good" id="review_good" rows="3"
                    placeholder="What you liked about the course? What worked well?"></textarea>
            </div>

            <div class="form-group">
                <label for="review_bad">What was bad?</label>
                <textarea name="review_bad" id="review_bad" rows="3"
                    placeholder="What could have been better? Outdated content, missing topics...."></textarea>
            </div>
        </div>

        <?php
        $outcomes = [
            "Earned Income"      => ["icon" => "icon-income.svg", "info" => "Used the skills from the course to generate income — through a job, freelance work, business, or side project."],
            "Career Boost"       => ["icon" => "icon-career.svg", "info" => "Career improved through a promotion, new project, business start, or better work opportunities using the course skills."],
            "Built Project"      => ["icon" => "icon-built-project.svg", "info" => "Made something during or after the course — for example, a project, example work, or a useful tool."],
            "Improved Skill"     => ["icon" => "icon-improved-skill.svg", "info" => "Learned a new skill or improved a skill you already had."],
            "Gained Confidence"  => ["icon" => "icon-confidence.svg", "info" => "Finished the course feeling more confidence in the skills and now ready to apply them in real-world situations."],
            "No Impact"          => ["icon" => "icon-no-impact.svg", "info" => "The course did not provide meaningful skills, results, or value."]
        ];
    ?>
        <div class="form-group">
            <label for="">What result did you get from this course?</label>
            <p class="form-tip">Select all that apply</p>
            <div class="form-options-group">
                <div class="col">
                    <?php foreach ($outcomes as $outcomeText => $data): ?>
                    <span class="option">
                        <img src="<?= get_theme_icon_url($data['icon']) ?>" alt="<?= esc_attr($outcomeText) ?> Icon"
                            class="option-icon">
                        <?php echo esc_html($outcomeText); ?>
                        <span class="tooltip-wrapper">
                            <ion-icon name="information-circle-outline"></ion-icon>
                            <span class="tooltip-text">
                                <?= esc_html($data['info']); ?>
                            </span>
                        </span>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php
        $recommends = [
            "Yes, I’d recommend it",
            "No, I would’t"
        ]
    ?>
        <div class="form-group">
            <label for="">Would you recommend this course to others?</label>
            <div class="form-options-group">
                <div class="col">
                    <?php foreach ($recommends as $data): ?>
                    <span class="option">
                        <?php echo esc_html($data); ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php
        $worths = [
            "Yes, good value",
            "No, overpriced"
        ]
    ?>
        <div class="form-group">
            <label for="">Was it worth the money?</label>
            <div class="form-options-group">
                <div class="col">
                    <?php foreach ($worths as $data): ?>
                    <span class="option">
                        <?php echo esc_html($data); ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="">Proof of enrollment</label>
            <p class="form-tip">Optional</p>
            <input type="file" name="" id="attachementBox">
            <label for="attachementBox" class="form-attachement-box">
                <img src="<?= get_theme_icon_url('icon-attachment.svg') ?>" alt="Attachement Icon">
                Upload Course certificate, screenshot of dashboard or reciept
            </label>
        </div>

        <div class="form-group notice">
            <label for="">Why verify?</label>
            <p class="form-para">
                Get a “Verified purchase” badge to make your review more trustworthy. We only need to see your name
                clearly.
                We never display your certificate—only confirm it privately
            </p>
        </div>

        <div class="button-group">
            <button>Next</button>
        </div>
    </div>

    <div id="pageTwo" class="page">
        <div class="form-group">
            <h3 class="form-section-heading">Course details</h3>
            <p class="form-para">Help others understand the course quality and difficulty</p>
        </div>

        <div class="form-group">
            <label for="">Was the course content up to date and practical enough to apply in real-world
                situations?</label>
            <textarea name="" id="" rows="3" placeholder=""></textarea>
        </div>

        <?php
        $levels = [
            "Beginner",
            "Intermediate",
            "Advance"
        ]
    ?>
        <div class="form-group">
            <label for="">What level did this course feel like to you?</label>
            <div class="form-options-group">
                <div class="col">
                    <?php foreach ($levels as $data): ?>
                    <span class="option">
                        <?php echo esc_html($data); ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="">Instructor & Support</label>
            <textarea name="" id="" rows="1" placeholder="Was the teaching clear, engaging, and helpful?"></textarea>
        </div>

        <div class="form-group">
            <label for="">Any Refund Issues?</label>
            <textarea name="" id="" rows="1"
                placeholder="Any problem getting a refund if you requested one?"></textarea>
        </div>

        <div class="form-group">
            <label for="">Video Review</label>
            <p class="form-tip">Optional</p>
            <input type="file" name="" id="videoBox">
            <label for="videoBox" class="form-video-box">
                <ion-icon name="videocam-outline"></ion-icon>
                <strong>Upload a video review</strong>
                <span class="form-tip">MP4 format, up to 2 minutes</span>
            </label>
        </div>

        <div class="form-group">
            <label for="">Linkedin profile</label>
            <a href="">
                <ion-icon name="logo-linkedin"></ion-icon>
                Connect your linkedin profile
            </a>
        </div>

        <div class="form-group notice">
            <p class="form-para">
                Get a “Varified user” badge to build more trust. (We will never display your Linkedin profile)
            </p>
        </div>

        <div class="form-group">
            <input type="checkbox" name="" id="concent">
            <label for="concent">
                I certify this review is based on my own experience and is my genuine opinion of this course, and that I
                have no personal or business relationship with this establishment. I understand that Spill the course
                has a
                zero-tolerance policy on fake reviews and reviews that do not comply with guidelines.
            </label>
        </div>

        <div class="button-group">
            <button>Previous</button>
            <button type="submit">Submit Review</button>
        </div>
    </div>

    <div id="reviewmvpReviewFormMessage" style="margin-top:10px;"></div>
</form>

<script>
jQuery(document).ready(function($) {
    // when a course is selected, auto-fill platform field
    $('#review_course').on('change', function() {
        var platform = $(this).find(':selected').data('platform') || '';
        $('#review_course_platform').val(platform);
    });
});
(function($) {
    $('#reviewmvpAddReviewForm').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let messageBox = $('#reviewmvpReviewFormMessage');
        messageBox.html('').css('color', '');

        // simple validation
        let course = $('#review_course').val();
        let rating = $('input[name="review_rating"]:checked').val();
        let message = $('#review_message').val().trim();

        if (course === '' || !rating || message === '') {
            messageBox.css('color', 'crimson').text('Please complete all required fields.');
            return;
        }

        // AJAX submit
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin- ajax.php'); ?>',
            data: form.serialize() + '&action=reviewmvp_submit_review',
            success: function(response) {
                if (response.success) {
                    window.location.href = "<?php echo site_url('/thank-you/'); ?>";
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
add_shortcode('add_review', 'reviewmvp_add_review_form');

/**
 * Handle AJAX review submission
 */
function reviewmvp_handle_review_submission() {
    if (!isset($_POST['reviewmvp_add_review_nonce']) || 
        !wp_verify_nonce($_POST['reviewmvp_add_review_nonce'], 'reviewmvp_add_review_action')) {
        wp_send_json_error('Security check failed.');
    }

    $course_id    = intval($_POST['review_course'] ?? 0);
    $rating       = intval($_POST['review_rating'] ?? 0);
    $message      = sanitize_textarea_field($_POST['review_message'] ?? '');
    $good         = sanitize_textarea_field($_POST['review_good'] ?? '');
    $bad          = sanitize_textarea_field($_POST['review_bad'] ?? '');

    if ($course_id <= 0 || $rating <= 0 || empty($message)) {
        wp_send_json_error('Required fields are missing.');
    }

    // Create review post
    $post_id = wp_insert_post(array(
        'post_title'   => 'Review for course '.$course_id,
        'post_type'    => 'course_review',
        'post_status'  => 'pending',
    ));

    if ($post_id) {
        update_post_meta($post_id, '_review_course', $course_id);
        update_post_meta($post_id, '_review_rating', $rating);
        update_post_meta($post_id, '_review_message', $message);
        update_post_meta($post_id, '_review_good', $good);
        update_post_meta($post_id, '_review_bad', $bad);

        // Optional: auto-save review date
        update_post_meta($post_id, '_review_date', current_time('Y-m-d'));

        wp_send_json_success('Review submitted successfully.');
    }

    wp_send_json_error('Something went wrong while saving review.');
}
add_action('wp_ajax_reviewmvp_submit_review', 'reviewmvp_handle_review_submission');
add_action('wp_ajax_nopriv_reviewmvp_submit_review', 'reviewmvp_handle_review_submission');