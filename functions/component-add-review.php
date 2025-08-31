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

    <div id="pageOne" class="form-page-container active">
        <div class="form-heading-group">
            <h3 class="form-section-heading">Course info</h3>
        </div>

        <div class="form-group">
            <label for="reviewCourse" class="form-label">Course title <span class="required">*</span></label>
            <div class="custom-select-wrapper">
                <select name="review_course" id="reviewCourse">
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
            </div>
            <div class="error-message"></div>
        </div>

        <div class="form-heading-group">
            <h4 class="form-section-heading">How was your experience?</h4>
            <p class="form-para bold" style="margin-top: 19px;">Share your overall thoughts and what you achieved</p>
        </div>

        <div class="form-group">
            <label class="form-label">Overall rating <span class="required">*</span></label>
            <div class="form-star-group">
                <?php for ($i=1; $i<=5; $i++): ?>
                <label class="form-star" data-value="<?php echo $i; ?>">
                    <input type="radio" name="review_rating" value="<?php echo $i; ?>" hidden>
                    <ion-icon name="star-outline"></ion-icon>
                </label>
                <?php endfor; ?>
                <p class="form-tip">click to rate</p>
            </div>
            <div class="error-message"></div>
        </div>

        <div class="form-group">
            <label class="form-label">Your overall feedback <span class="required">*</span></label>
            <textarea name="review_message" id="reviewMessage" rows="5"></textarea>
            <div class="error-message"></div>
        </div>

        <div class="form-group">
            <p class="form-para bold" style="margin-bottom: 24px">
                Tell us what you liked and what could have been better
            </p>
            <div class="form-row">
                <div class="form-group">
                    <label for="reviewGood" class="form-label">What was good? <span class="required">*</span></label>
                    <textarea name="review_good" id="reviewGood" rows="5"
                        placeholder="What you liked about the course? What worked well?"></textarea>
                    <div class="error-message"></div>
                </div>

                <div class="form-group">
                    <label for="reviewBad" class="form-label">What was bad? <span class="required">*</span></label>
                    <textarea name="review_bad" id="reviewBad" rows="5"
                        placeholder="What could have been better? Outdated content, missing topics...."></textarea>
                    <div class="error-message"></div>
                </div>
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
            <label class="form-label">What result did you get from this course? <span class="required">*</span></label>
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
            <div class="error-message"></div>
        </div>

        <?php
            $recommends = [
                "Yes, I’d recommend it" => "recommendYes",
                "No, I wouldn’t" => "recommendNo"
            ]
        ?>
        <div class="form-group">
            <label class="form-label">Would you recommend this course to others? <span class="required">*</span></label>
            <div class="form-options-group">
                <?php foreach ($recommends as $data => $id): ?>
                <label for="<?= esc_attr($id) ?>" class="form-option">
                    <input type="radio" name="review_recommend" id="<?= esc_attr($id) ?>"
                        value="<?= esc_attr($data) ?>">
                    <?= esc_html($data); ?>
                </label>
                <?php endforeach; ?>
            </div>
            <div class="error-message"></div>
        </div>

        <?php
            $worths = [
                "Yes, good value" => "worthYes",
                "No, overpriced" => "worthNo"
            ]
        ?>
        <div class="form-group">
            <label class="form-label">Was it worth the money? <span class="required">*</span></label>
            <div class="form-options-group">
                <?php foreach ($worths as $data => $id): ?>
                <label for="<?= esc_attr($id) ?>" class="form-option">
                    <input type="radio" name="review_worth" id="<?= esc_attr($id) ?>" value="<?= esc_attr($data) ?>">
                    <?php echo esc_html($data); ?>
                </label>
                <?php endforeach; ?>
            </div>
            <div class="error-message"></div>
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

    <div id="pageTwo" class="form-page-container">
        <div class="form-heading-group">
            <h3 class="form-section-heading">Course details</h3>
            <p class="form-para bold">Help others understand the course quality and difficulty</p>
        </div>

        <div class="form-group">
            <label for="reviewQuality" class="form-label">Was the course content up to date and practical enough to
                apply in
                real-world
                situations? <span class="required">*</span></label>
            <textarea name="review_quality" id="reviewQuality" rows="1"></textarea>
            <div class="error-message"></div>
        </div>

        <?php
            $levels = [
                "Beginner" => "levelBeginner",
                "Intermediate" => "levelIntermediate",
                "Advance" => "levelAdvance"
            ]
        ?>
        <div class="form-group">
            <label class="form-label">What level did this course feel like to you? <span
                    class="required">*</span></label>
            <div class="form-options-group">
                <?php foreach ($levels as $data => $id): ?>
                <label for="<?= esc_attr($id) ?>" class="form-option">
                    <input type="radio" name="review_level" id="<?= esc_attr($id) ?>" value="<?= esc_attr($data) ?>">
                    <?php echo esc_html($data); ?>
                </label>
                <?php endforeach; ?>
            </div>
            <div class="error-message"></div>
        </div>

        <div class="form-group">
            <label for="reviewSupport" class="form-label">Instructor & Support <span class="required">*</span></label>
            <textarea name="review_support" id="reviewSupport" rows="1"
                placeholder="Was the teaching clear, engaging, and helpful?"></textarea>
            <div class="error-message"></div>
        </div>

        <div class="form-group">
            <label for="reviewRefund" class="form-label">Any Refund Issues? <span class="required">*</span></label>
            <textarea name="review_refund" id="reviewRefund" rows="1"
                placeholder="Any problem getting a refund if you requested one?"></textarea>
            <div class="error-message"></div>
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
            <label for="reviewLinkedin" class="form-label">LinkedIn profile <span
                    class="form-tip optional">Optional</span></label>

            <!-- hidden input (filled by OAuth if successful) -->
            <input type="hidden" name="review_linkedin" id="reviewLinkedin">

            <!-- OAuth connect -->
            <a href="#" id="connectLinkedin" class="connect-profile">
                <ion-icon name="logo-linkedin"></ion-icon>
                Connect your LinkedIn profile
            </a>

            <!-- fallback manual field (initially hidden) -->
            <div id="linkedinManual" style="display:none; margin-top:10px;">
                <input type="url" name="review_linkedin_manual" id="reviewLinkedinManual"
                    placeholder="https://www.linkedin.com/in/your-profile" style="width:100%;">
                <small>Couldn’t connect automatically? Paste your LinkedIn profile URL here.</small>
            </div>

            <p id="linkedinConnected" style="display:none; color:green; margin-top:5px;">
                <ion-icon name="checkmark-circle"></ion-icon> LinkedIn profile connected
            </p>
        </div>

        <div class="form-group notice">
            <p class="form-para">
                Get a “Varified user” badge to build more trust. (We will never display your Linkedin profile)
            </p>
        </div>

        <div class="form-group">
            <?php if (is_user_logged_in()): ?>
            <label for="reviewAnonymously" class="form-label anonymous">
                <input type="checkbox" name="review_anonymously" id="reviewAnonymously">
                <span><strong>Review anonymously</strong> (your name will not be shown publicly)</span>
            </label>
            <?php endif; ?>
            <label for="reviewConsent" class="form-label consent">
                <input type="checkbox" name="review_consent" id="reviewConsent">
                I certify this review is based on my own experience and is my genuine opinion of this course, and that I
                have no personal or business relationship with this establishment. I understand that Spill the course
                has a
                zero-tolerance policy on fake reviews and reviews that do not comply with guidelines.
            </label>
            <div class="error-message"></div>
        </div>

        <div class="button-group">
            <button class="form-button">
                <ion-icon name="arrow-back"></ion-icon> Previous
            </button>
            <button type="submit" class="form-button submit">Submit review</button>
        </div>
    </div>

    <div id="reviewmvpReviewFormMessage" style="margin-top:10px;"></div>
</form>

<script>
// Initialize Select2 for course selection
jQuery(document).ready(function($) {
    $('#reviewCourse').select2({
        placeholder: "— Select Course —",
        allowClear: true,
        width: '100%'
    });
});

// Show platform with course title in course selection
jQuery(document).ready(function($) {
    $('#reviewCourse').select2({
        placeholder: "— Select Course —",
        allowClear: true,
        width: '100%',
        templateResult: function(state) {
            if (!state.id) return state.text; // placeholder

            // get platform from data attribute
            var platform = $(state.element).data('platform');

            if (platform) {
                return $('<div class="selec2-course"><span class="select2-course-title">' + state
                    .text + '</span><span class="select2-course-platform">' + platform +
                    '</span></div>');
            }
            return state.text;
        },
        templateSelection: function(state) {
            if (!state.id) return state.text;
            var platform = $(state.element).data('platform');
            return platform ? state.text + ' — ' + platform : state.text;
        }
    });
});

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

// LinkedIn connect (OpenID)
document.getElementById("connectLinkedin").addEventListener("click", function(e) {
    e.preventDefault();

    let clientId = "8619zvo75jvko7"; // LinkedIn Client ID
    let redirectUri = "<?php echo site_url('/linkedin-callback/'); ?>";
    let state = Math.random().toString(36).substring(2);

    let oauthUrl = "https://www.linkedin.com/oauth/v2/authorization?response_type=code" +
        "&client_id=" + clientId +
        "&redirect_uri=" + encodeURIComponent(redirectUri) +
        "&scope=openid%20profile%20email" +
        "&state=" + state;

    try {
        window.location.href = oauthUrl;
    } catch (err) {
        document.getElementById("linkedinManual").style.display = "block";
    }
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

jQuery(document).ready(function($) {
    let currentPage = 1;

    function showPage(page) {
        $(".form-page-container.active").removeClass("active").fadeOut(300, function() {
            let target = (page === 1) ? "#pageOne" : "#pageTwo";
            $(target).fadeIn(300).addClass("active");

            // Instant scroll to top of form
            $(window).scrollTop($("#addReviewForm").offset().top - 40);
        });

        $(".form-page-number").removeClass("active");
        $(".form-page-number").eq(page - 1).addClass("active");
    }

    // Validation: Page One
    function validatePageOne() {
        let hasError = false;
        let firstError = null;
        $("#pageOne .error-message").text("");

        function showError(input, message) {
            const group = $(input).closest('.form-group');
            group.find('.error-message').html(
                "<ion-icon name='alert-circle-outline'></ion-icon> " + message
            );
            if (!firstError) firstError = group; // capture first invalid group
            hasError = true;
        }

        if (!$('#reviewCourse').val()) showError('#reviewCourse', 'Please select a course.');
        if (!$('input[name="review_rating"]:checked').val()) showError('input[name="review_rating"]',
            'Please give a rating.');
        if (!$('#reviewMessage').val().trim()) showError('#reviewMessage',
            'Please enter your overall feedback.');
        if (!$('#reviewGood').val().trim()) showError('#reviewGood', 'Please tell us what was good.');
        if (!$('#reviewBad').val().trim()) showError('#reviewBad', 'Please tell us what was bad.');
        if (!$('input[name="review_outcome[]"]:checked').length) showError('input[name="review_outcome[]"]',
            'Please select at least one outcome.');
        if (!$('input[name="review_recommend"]:checked').val()) showError('input[name="review_recommend"]',
            'Please select an option.');
        if (!$('input[name="review_worth"]:checked').val()) showError('input[name="review_worth"]',
            'Please select an option.');

        // Scroll to first error if found
        if (firstError) {
            $('html, body').scrollTop(firstError.offset().top - 50);
        }

        return !hasError;
    }

    // Validation: Page Two
    function validatePageTwo() {
        let hasError = false;
        let firstError = null;
        $("#pageTwo .error-message").text("");

        function showError(input, message) {
            const group = $(input).closest('.form-group');
            group.find('.error-message').html(
                "<ion-icon name='alert-circle-outline'></ion-icon> " + message
            );
            if (!firstError) firstError = group; // capture first invalid group
            hasError = true;
        }

        if (!$('#reviewQuality').val().trim()) showError('#reviewQuality',
            'Please describe the course content quality.');
        if (!$('input[name="review_level"]:checked').val()) showError('input[name="review_level"]',
            'Please select a course level.');
        if (!$('#reviewSupport').val().trim()) showError('#reviewSupport',
            'Please describe the instructor & support.');
        if (!$('#reviewRefund').val().trim()) showError('#reviewRefund', 'Please describe refund experience.');
        if (!$('#reviewConsent').is(':checked')) showError('#reviewConsent',
            'You must agree before submitting.');

        // Scroll to first error if found
        if (firstError) {
            $('html, body').scrollTop(firstError.offset().top - 50);
        }

        return !hasError;
    }

    // Next button → validate Page 1 before moving
    $("#pageOne .form-button").on("click", function(e) {
        e.preventDefault();
        if (validatePageOne()) {
            currentPage = 2;
            showPage(currentPage);
        }
    });

    // Previous button
    $("#pageTwo .form-button").first().on("click", function(e) {
        e.preventDefault();
        currentPage = 1;
        showPage(currentPage);
    });

    // Submit → validate Page 2
    $('#addReviewForm').on('submit', function(e) {
        e.preventDefault();
        if (!validatePageTwo()) {
            // Scroll to first error on Page 2
            let firstError = $("#pageTwo .error-message:contains('ion-icon')").first().closest(
                ".form-group");
            if (firstError.length) {
                $('html, body').scrollTop(firstError.offset().top - 50);
            }
            return;
        }

        // ✅ No errors → proceed AJAX
        let form = $(this)[0];
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
                    alert(response.data || 'Submission failed. Try again.');
                }
            },
            error: function() {
                alert('Server error. Please try again.');
            }
        });
    });

    // Init
    $(".form-page-container").hide();
    $("#pageOne").show().addClass("active");
});
</script>
<?php
    return ob_get_clean();
}
add_shortcode('add_review', 'reviewmvp_add_review_form');

/**
 * Handle LinkedIn profile connect (OpenID only)
 */
add_action('init', function() {
    if (
        isset($_GET['code']) && isset($_GET['state']) &&
        strpos($_SERVER['REQUEST_URI'], 'linkedin-callback') !== false
    ) {
        $code = sanitize_text_field($_GET['code']);

        // Exchange code for access token
        $response = wp_remote_post("https://www.linkedin.com/oauth/v2/accessToken", [
            'body' => [
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => site_url('/linkedin-callback/'),
                'client_id'     => '8619zvo75jvko7',
                'client_secret' => 'WPL_AP1.fyIbGk7LUs77imwB.Ul/W8A=='
            ]
        ]);

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $access_token = $body['access_token'] ?? '';

        if ($access_token) {
            // Fetch profile using OpenID userinfo
            $profile = wp_remote_get("https://api.linkedin.com/v2/userinfo", [
                'headers' => ['Authorization' => 'Bearer ' . $access_token]
            ]);
            $profileData = json_decode(wp_remote_retrieve_body($profile), true);

            $id = $profileData['sub'] ?? '';
            $linkedinUrl = $id ? "https://www.linkedin.com/openid/id/" . $id : '';

            if ($linkedinUrl) {
                setcookie("linkedin_profile", $linkedinUrl, time()+3600, "/");
            }

            // Always redirect back to review form
            wp_redirect(site_url('/write-a-review/?linkedin=success'));
            exit;
        } else {
            // OAuth failed → fallback
            wp_redirect(site_url('/write-a-review/?linkedin=fallback'));
            exit;
        }
    }
});

/**
 * Handle AJAX review submission
 */
function reviewmvp_handle_review_submission() {
    if (
        !isset($_POST['reviewmvp_add_review_nonce']) || 
        !wp_verify_nonce($_POST['reviewmvp_add_review_nonce'], 'reviewmvp_add_review_action')
    ) {
        wp_send_json_error('Security check failed.');
    }

    // Collect inputs
    $course_id = intval($_POST['review_course'] ?? 0);
    $rating    = intval($_POST['review_rating'] ?? 0);
    $message   = sanitize_textarea_field($_POST['review_message'] ?? '');
    $good      = sanitize_textarea_field($_POST['review_good'] ?? '');
    $bad       = sanitize_textarea_field($_POST['review_bad'] ?? '');
    $quality   = sanitize_textarea_field($_POST['review_quality'] ?? '');
    $support   = sanitize_textarea_field($_POST['review_support'] ?? '');
    $refund    = sanitize_textarea_field($_POST['review_refund'] ?? '');
    $recommend = sanitize_text_field($_POST['review_recommend'] ?? '');
    $worth     = sanitize_text_field($_POST['review_worth'] ?? '');
    $level     = sanitize_text_field($_POST['review_level'] ?? '');
    $outcomes  = array_map('sanitize_text_field', (array)($_POST['review_outcome'] ?? []));
    $consent   = !empty($_POST['review_consent']);

    if (
        $course_id <= 0 || $rating <= 0 || empty($message) ||
        empty($quality) || empty($support) || empty($refund) || empty($recommend) ||
        empty($worth) || empty($level) || !$consent
    ) {
        wp_send_json_error('Please complete all required fields.');
    }

    // Reviewer info (only for logged in users)
    $reviewer_id   = 0;
    $reviewer_name = '';
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $reviewer_id   = $current_user->ID;
        $reviewer_name = $current_user->display_name;
    }

    // Create review post
    $course_title = get_the_title($course_id);

    if ($reviewer_id) {
        $title_extra = " | Reviewer [ID:$reviewer_id | Name: $reviewer_name]";
    } else {
        $title_extra = " | Reviewer [Guest]";
    }

    $post_id = wp_insert_post([
        'post_title'   => 'Course [ID:'.$course_id.' | Title: '.$course_title.']'.$title_extra,
        'post_type'    => 'course_review',
        'post_status'  => 'pending',
        'post_author'  => $reviewer_id ?: 0, // assign logged-in user as author
    ]);

    if (!$post_id) {
        wp_send_json_error('Something went wrong while saving review.');
    }

    // Save reviewer meta (only if logged in)
    if ($reviewer_id) {
        update_post_meta($post_id, '_reviewer', $reviewer_id);
        update_post_meta($post_id, '_reviewer_name', sanitize_text_field($reviewer_name));
    }

    // Handle anonymous (only if logged in reviewer)
    if ($reviewer_id && !empty($_POST['review_anonymously'])) {
        update_post_meta($post_id, '_review_status', ['anonymous']);
    }

    // LinkedIn profile (OAuth or manual)
    $linkedin = sanitize_text_field($_POST['review_linkedin'] ?? ($_COOKIE['linkedin_profile'] ?? ''));
    if (empty($linkedin) && !empty($_POST['review_linkedin_manual'])) {
        $linkedin = sanitize_text_field($_POST['review_linkedin_manual']);
    }
    if (!empty($linkedin)) {
        update_post_meta($post_id, '_review_linkedin', esc_url_raw($linkedin));
    }

    // Save meta
    update_post_meta($post_id, '_review_course', $course_id);
    update_post_meta($post_id, '_review_rating', $rating);
    update_post_meta($post_id, '_review_message', $message);
    update_post_meta($post_id, '_review_good', $good);
    update_post_meta($post_id, '_review_bad', $bad);
    update_post_meta($post_id, '_review_quality', $quality);
    update_post_meta($post_id, '_review_support', $support);
    update_post_meta($post_id, '_review_refund', $refund);
    update_post_meta($post_id, '_review_recommend', $recommend);
    update_post_meta($post_id, '_review_worth', $worth);
    update_post_meta($post_id, '_review_level', $level);
    update_post_meta($post_id, '_review_outcome', $outcomes);
    update_post_meta($post_id, '_review_date', current_time('Y-m-d'));

    // Handle file uploads
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

// enqueue select2
add_action('wp_enqueue_scripts', function() {
    // Only enqueue on pages with the form
    if (is_page('write-a-review')) { 
        wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], null, true);
    }
});