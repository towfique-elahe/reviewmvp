<?php
/**
 * Function File Name: Component Add Review
 * 
 * A front-end form to submit a new review (status: pending).
 */

// Shortcode: [add_review]
function reviewmvp_add_review_form() {
    ob_start(); ?>
<form method="post" class="add-review" id="addReviewForm">
    <?php wp_nonce_field('reviewmvp_add_review_action', 'reviewmvp_add_review_nonce'); ?>

    <div class="form-pagination">
        <div class="form-page">
            <span class="form-page-number active">1</span>
            <span class="form-page-name">Course info</span>
        </div>
        <div class="form-page">
            <span class="form-page-number">2</span>
            <span class="form-page-name">Course details</span>
        </div>
    </div>

    <div id="pageOne" class="page">
        <div class="form-heading-group">
            <h3 class="form-section-heading">Course info</h3>
        </div>

        <div class="form-group">
            <label for="reviewCourse" class="form-label">Course title</label>
            <div class="custom-select-wrapper">
                <select name="review_course" id="reviewCourse" required>
                    <option value="" selected disabled>— Select Course —</option>
                    <?php 
                        $selected_course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

                        $courses = get_posts([
                            'post_type'   => 'course',
                            'numberposts' => -1,
                            'orderby'     => 'title',
                            'order'       => 'ASC'
                        ]);

                        foreach ($courses as $course) {
                            $platform = get_post_meta($course->ID, '_course_provider', true); 
                            $selected = $selected_course_id === $course->ID ? 'selected' : '';
                            echo '<option value="'.$course->ID.'" data-platform="'.esc_attr($platform).'" '.$selected.'>'
                                .esc_html($course->post_title).
                                '</option>';
                        }
                    ?>
                </select>
                <ion-icon name="chevron-down-outline" class="custom-select-icon"></ion-icon>
            </div>
        </div>

        <div class="form-group">
            <label for="reviewPlatform" class="form-label">Platform</label>
            <input type="text" name="review_course_platform" id="reviewPlatform"
                placeholder="e.g., Udemy, Coursera, etc." required>
        </div>

        <div class="form-heading-group">
            <h4 class="form-section-heading">How was your experience?</h4>
            <p class="form-para">Share your overall thoughts and what you achieved</p>
        </div>

        <div class="form-group">
            <label class="form-label">Overall rating</label>
            <div class="form-star-group">
                <?php for ($i=1; $i<=5; $i++): ?>
                <label class="form-star" data-value="<?php echo $i; ?>">
                    <input type="radio" name="review_rating" value="<?php echo $i; ?>" hidden>
                    <ion-icon name="star-outline"></ion-icon>
                </label>
                <?php endfor; ?>
                <p class="form-tip">click to rate</p>
            </div>
            <textarea name="review_message" id="reviewMessage" rows="5" required></textarea>
            <p class="form-para" style="margin-top: 10px">Tell us what you liked and what could have been better</p>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="reviewGood" class="form-label">What was good?</label>
                <textarea name="review_good" id="reviewGood" rows="5"
                    placeholder="What you liked about the course? What worked well?"></textarea>
            </div>

            <div class="form-group">
                <label for="reviewBad" class="form-label">What was bad?</label>
                <textarea name="review_bad" id="reviewBad" rows="5"
                    placeholder="What could have been better? Outdated content, missing topics...."></textarea>
            </div>
        </div>

        <?php
            $outcomes = [
                "Earned Income"      => ["id" => "earnedIncome", "icon" => "icon-income.svg", "info" => "Used the skills from the course to generate income — through a job, freelance work, business, or side project."],
                "Career Boost"       => ["id" => "careerBoost", "icon" => "icon-career.svg", "info" => "Career improved through a promotion, new project, business start, or better work opportunities using the course skills."],
                "Built Project"      => ["id" => "builtProject", "icon" => "icon-built-project.svg", "info" => "Made something during or after the course — for example, a project, example work, or a useful tool."],
                "Improved Skill"     => ["id" => "improvedSkill", "icon" => "icon-improved-skill.svg", "info" => "Learned a new skill or improved a skill you already had."],
                "Gained Confidence"  => ["id" => "gainedConfidence", "icon" => "icon-confidence.svg", "info" => "Finished the course feeling more confidence in the skills and now ready to apply them in real-world situations."],
                "No Impact"          => ["id" => "noImpact", "icon" => "icon-no-impact.svg", "info" => "The course did not provide meaningful skills, results, or value."]
            ];
        ?>
        <div class="form-group">
            <label class="form-label">What result did you get from this course?</label>
            <p class="form-tip">Select all that apply</p>
            <div class="form-options-group">
                <?php foreach ($outcomes as $outcomeText => $data): ?>
                <label for="<?= esc_attr($data['id']) ?>" class="form-option">
                    <input type="checkbox" name="review_outcome[]" value="<?= esc_attr($outcomeText) ?>"
                        id="<?= esc_attr($data['id']) ?>">
                    <img src="<?= get_theme_icon_url($data['icon']) ?>" alt="<?= esc_attr($outcomeText) ?> Icon"
                        class="option-icon">
                    <?php echo esc_html($outcomeText); ?>
                    <span class="tooltip-wrapper">
                        <ion-icon name="information-circle-outline"></ion-icon>
                        <span class="tooltip-text">
                            <?= esc_html($data['info']); ?>
                        </span>
                    </span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <?php
            $recommends = [
                "Yes, I’d recommend it" => "recommendYes",
                "No, I would’t" => "recommendNo"
            ]
        ?>
        <div class="form-group">
            <label class="form-label">Would you recommend this course to others?</label>
            <div class="form-options-group">
                <?php foreach ($recommends as $data => $id): ?>
                <label for="<?= esc_attr($id) ?>" class="form-option">
                    <input type="radio" name="review_recommend" id="<?= esc_attr($id) ?>"
                        value="<?= esc_attr($data) ?>">
                    <?= esc_html($data); ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <?php
            $worths = [
                "Yes, good value" => "worthYes",
                "No, overpriced" => "worthNo"
            ]
        ?>
        <div class="form-group">
            <label class="form-label">Was it worth the money?</label>
            <div class="form-options-group">
                <?php foreach ($worths as $data => $id): ?>
                <label for="<?= esc_attr($id) ?>" class="form-option">
                    <input type="radio" name="review_worth" id="<?= esc_attr($id) ?>" value="<?= esc_attr($data) ?>">
                    <?php echo esc_html($data); ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label bold">
                Proof of enrollment <span class="form-tip optional">Optional</span>
            </label>
            <input type="file" name="review_proof" id="attachementBox" accept=".jpeg,.jpg,.png,.pdf" hidden>
            <label for="attachementBox" class="form-file-box">
                <ion-icon name="document-attach-outline"></ion-icon>
                Upload Course certificate, screenshot of dashboard or receipt
            </label>
            <p class="file-info" id="attachementInfo"></p>
        </div>

        <div class="form-group notice">
            <label for="" class="form-label bold">Why verify?</label>
            <p class="form-para">
                Get a “Verified purchase” badge to make your review more trustworthy. We only need to see your name
                clearly.
                We never display your certificate—only confirm it privately
            </p>
        </div>

        <div class="button-group">
            <button class="form-button">Next <ion-icon name="arrow-forward"></ion-icon></button>
        </div>
    </div>

    <div id="pageTwo" class="page">
        <div class="form-heading-group">
            <h3 class="form-section-heading">Course details</h3>
            <p class="form-para">Help others understand the course quality and difficulty</p>
        </div>

        <div class="form-group">
            <label for="reviewQuality" class="form-label">Was the course content up to date and practical enough to
                apply in
                real-world
                situations?</label>
            <textarea name="review_quality" id="reviewQuality" rows="1"></textarea>
        </div>

        <?php
            $levels = [
                "Beginner" => "levelBeginner",
                "Intermediate" => "levelIntermediate",
                "Advance" => "levelAdvance"
            ]
        ?>
        <div class="form-group">
            <label class="form-label">What level did this course feel like to you?</label>
            <div class="form-options-group">
                <?php foreach ($levels as $data => $id): ?>
                <label for="<?= esc_attr($id) ?>" class="form-option">
                    <input type="radio" name="reviewLevel" id="<?= esc_attr($id) ?>" value="<?= esc_attr($data) ?>">
                    <?php echo esc_html($data); ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="reviewSupport" class="form-label">Instructor & Support</label>
            <textarea name="review_support" id="reviewSupport" rows="1"
                placeholder="Was the teaching clear, engaging, and helpful?"></textarea>
        </div>

        <div class="form-group">
            <label for="reviewRefund" class="form-label">Any Refund Issues?</label>
            <textarea name="review_refund" id="reviewRefund" rows="1"
                placeholder="Any problem getting a refund if you requested one?"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label bold">
                Video Review <span class="form-tip optional">Optional</span>
            </label>
            <input type="file" name="review_video" id="videoBox" accept=".mp4" hidden>
            <label for="videoBox" class="form-file-box">
                <ion-icon name="videocam-outline"></ion-icon>
                <strong>Upload a video review</strong>
                <span class="form-tip">MP4 format, up to 2 minutes</span>
            </label>
            <p class="file-info" id="videoInfo"></p>
        </div>

        <div class="form-group">
            <label for="" class="form-label">Linkedin profile <span class="form-tip optional">Optional</span></label>
            <a href="" class="connect-profile">
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
            <label for="reviewConsent" class="form-label consent">
                <input type="checkbox" name="review_consent" id="reviewConsent" required>
                I certify this review is based on my own experience and is my genuine opinion of this course, and that I
                have no personal or business relationship with this establishment. I understand that Spill the course
                has a
                zero-tolerance policy on fake reviews and reviews that do not comply with guidelines.
            </label>
        </div>

        <div class="button-group">
            <button class="form-button">
                <ion-icon name="arrow-back"></ion-icon> Previous
            </button>
            <button type="submit" class="form-button submit">Submit review <ion-icon name="send"></ion-icon></button>
        </div>
    </div>

    <div id="reviewmvpReviewFormMessage" style="margin-top:10px;"></div>
</form>

<script>
// star behaviour
document.querySelectorAll('.form-star-group').forEach(group => {
    const stars = group.querySelectorAll('.form-star');
    let selected = 0;

    stars.forEach((star, index) => {
        const input = star.querySelector('input');

        // Hover effect
        star.addEventListener('mouseenter', () => {
            stars.forEach((s, i) => {
                s.querySelector('ion-icon').setAttribute('name', i <= index ? 'star' :
                    'star-outline');
            });
        });

        // Restore selection on mouse leave
        star.addEventListener('mouseleave', () => {
            stars.forEach((s, i) => {
                s.querySelector('ion-icon').setAttribute('name', i < selected ? 'star' :
                    'star-outline');
            });
        });

        // Lock in selection
        input.addEventListener('change', () => {
            selected = index + 1;
            stars.forEach((s, i) => {
                s.querySelector('ion-icon').setAttribute('name', i < selected ? 'star' :
                    'star-outline');
            });
        });
    });
});

// file upload validation
function validateFile(input, allowedTypes, maxSizeMB, infoId) {
    const info = document.getElementById(infoId);
    info.innerHTML = ""; // reset
    info.classList.remove("error");

    if (!input.files.length) return;

    const file = input.files[0];
    const fileType = file.type;
    const fileSizeMB = file.size / (1024 * 1024);

    // Check type
    const validType = allowedTypes.some(type => fileType.includes(type));
    if (!validType) {
        info.innerHTML = "<ion-icon name='close-circle-outline'></ion-icon> Invalid file type. Allowed: " +
            allowedTypes.join(", ");
        info.classList.add("error");
        input.value = ""; // reset file
        return;
    }

    // Check size
    if (fileSizeMB > maxSizeMB) {
        info.innerHTML =
            `<ion-icon name="close-circle-outline"></ion-icon> File is too large. Max allowed: ${maxSizeMB} MB`;
        info.classList.add("error");
        input.value = ""; // reset file
        return;
    }

    // Success
    info.innerHTML =
        `<ion-icon name="checkmark-circle-outline"></ion-icon> Selected: ${file.name} (${fileSizeMB.toFixed(2)} MB)`;
}

// Enrollment proof (jpg, png, pdf, max 5 MB)
document.getElementById("attachementBox").addEventListener("change", function() {
    validateFile(this, ["jpeg", "jpg", "png", "pdf"], 5, "attachementInfo");
});

// Video review (mp4, max 20 MB)
document.getElementById("videoBox").addEventListener("change", function() {
    validateFile(this, ["mp4"], 20, "videoInfo");
});

// when a course is selected, auto-fill platform field
jQuery(document).ready(function($) {
    $('#reviewCourse').on('change', function() {
        var platform = $(this).find(':selected').data('platform') || '';
        $('#reviewPlatform').val(platform);
    });
    $('#reviewCourse').trigger('change');
});
(function($) {
    $('#addReviewForm').on('submit', function(e) {
        e.preventDefault();

        let form = $(this)[0]; // DOM element
        let messageBox = $('#reviewmvpReviewFormMessage');
        messageBox.html('').css('color', '');

        // collect required fields
        let course = $('#reviewCourse').val();
        let platform = $('#reviewPlatform').val().trim();
        let rating = $('input[name="review_rating"]:checked').val();
        let message = $('#reviewMessage').val().trim();
        let recommend = $('input[name="review_recommend"]:checked').val();
        let worth = $('input[name="review_worth"]:checked').val();
        let quality = $('#reviewQuality').val().trim();
        let support = $('#reviewSupport').val().trim();
        let refund = $('#reviewRefund').val().trim();
        let consent = $('#reviewConsent').is(':checked');

        if (!course || !platform || !rating || !message || !recommend || !worth || !quality || !support || !
            refund || !consent) {
            messageBox.css('color', 'crimson').text(
                'Please complete all required fields before submitting.');
            return;
        }

        let formData = new FormData(form);
        formData.append('action', 'reviewmvp_submit_review');

        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: formData,
            processData: false,
            contentType: false,
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

    // Collect inputs
    $course_id = intval($_POST['review_course'] ?? 0);
    $platform  = sanitize_text_field($_POST['review_course_platform'] ?? '');
    $rating    = intval($_POST['review_rating'] ?? 0);
    $message   = sanitize_textarea_field($_POST['review_message'] ?? '');
    $good      = sanitize_textarea_field($_POST['review_good'] ?? '');
    $bad       = sanitize_textarea_field($_POST['review_bad'] ?? '');
    $quality   = sanitize_textarea_field($_POST['review_quality'] ?? '');
    $support   = sanitize_textarea_field($_POST['review_support'] ?? '');
    $refund    = sanitize_textarea_field($_POST['review_refund'] ?? '');
    $recommend = sanitize_text_field($_POST['review_recommend'] ?? '');
    $worth     = sanitize_text_field($_POST['review_worth'] ?? '');
    $outcomes  = array_map('sanitize_text_field', (array)($_POST['review_outcome'] ?? []));
    $consent   = !empty($_POST['review_consent']);

    // Validate required fields
    if ($course_id <= 0 || empty($platform) || $rating <= 0 || empty($message) || empty($quality) || empty($support) || empty($refund) || empty($recommend) || empty($worth) || !$consent) {
        wp_send_json_error('Please complete all required fields.');
    }

    // Create review post
    $post_id = wp_insert_post([
        'post_title'   => 'Review for course '.$course_id,
        'post_type'    => 'course_review',
        'post_status'  => 'pending',
    ]);

    if (!$post_id) {
        wp_send_json_error('Something went wrong while saving review.');
    }

    // Save meta
    update_post_meta($post_id, '_review_course', $course_id);
    update_post_meta($post_id, '_review_course_platform', $platform);
    update_post_meta($post_id, '_review_rating', $rating);
    update_post_meta($post_id, '_review_message', $message);
    update_post_meta($post_id, '_review_good', $good);
    update_post_meta($post_id, '_review_bad', $bad);
    update_post_meta($post_id, '_review_quality', $quality);
    update_post_meta($post_id, '_review_support', $support);
    update_post_meta($post_id, '_review_refund', $refund);
    update_post_meta($post_id, '_review_recommend', $recommend);
    update_post_meta($post_id, '_review_worth', $worth);
    update_post_meta($post_id, '_review_outcome', $outcomes);
    update_post_meta($post_id, '_review_date', current_time('Y-m-d'));

    /**
     * Handle file uploads (Proof + Video) - optional
     */
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }

    if (!empty($_FILES['review_proof']['name'])) {
        $uploaded = wp_handle_upload($_FILES['review_proof'], ['test_form' => false]);
        if (!isset($uploaded['error']) && isset($uploaded['url'])) {
            update_post_meta($post_id, '_review_proof', esc_url_raw($uploaded['url']));
        }
    }

    if (!empty($_FILES['review_video']['name'])) {
        $uploaded = wp_handle_upload($_FILES['review_video'], ['test_form' => false]);
        if (!isset($uploaded['error']) && isset($uploaded['url'])) {
            update_post_meta($post_id, '_review_video', esc_url_raw($uploaded['url']));
        }
    }

    wp_send_json_success('Review submitted successfully.');
}
add_action('wp_ajax_reviewmvp_submit_review', 'reviewmvp_handle_review_submission');
add_action('wp_ajax_nopriv_reviewmvp_submit_review', 'reviewmvp_handle_review_submission');