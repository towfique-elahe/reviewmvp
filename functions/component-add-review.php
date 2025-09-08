<?php

function reviewmvp_add_review_form() {
    ob_start();
    $uid = is_user_logged_in() ? get_current_user_id() : 0;
?>
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
                <?php
                    $guest_pending_course = 0;
                    if (!is_user_logged_in()) {
                        if (!session_id()) {
                            session_start();
                        }
                        $guest_pending_course = $_SESSION['guest_last_course_id'] ?? 0;
                    }
                ?>
                <select name="review_course" id="reviewCourse">
                    <option value="" selected disabled>— Select Course —</option>
                    <?php 
                        $selected_course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

                        $args = [
                            'post_type'   => 'course',
                            'numberposts' => -1,
                            'orderby'     => 'title',
                            'order'       => 'ASC',
                            'post_status' => 'publish',
                        ];

                        if (is_user_logged_in()) {
                            $user = wp_get_current_user();
                            if (in_array('reviewer', (array) $user->roles)) {
                                $args['post_status'] = ['publish','pending'];
                                $args['meta_query'] = [
                                    'relation' => 'OR',
                                    [
                                        'relation' => 'AND',
                                        [
                                            'key'     => '_course_reviewer',
                                            'value'   => $user->ID,
                                            'compare' => '='
                                        ],
                                        [
                                            'key'     => '_course_status_flag',
                                            'compare' => 'EXISTS'
                                        ]
                                    ],
                                    [
                                        'key'     => '_course_reviewer',
                                        'compare' => 'NOT EXISTS'
                                    ],
                                    [
                                        'key'     => '_course_reviewer',
                                        'compare' => 'EXISTS'
                                    ]
                                ];
                            }
                        }

                        if (!is_user_logged_in() && $guest_pending_course) {
                            $args['post_status'] = ['publish', 'pending'];
                        }

                        $courses = get_posts($args);

                        if (is_user_logged_in()) {
                            $user = wp_get_current_user();
                            if (in_array('reviewer', (array) $user->roles) && !$selected_course_id) {
                                $pending_courses = array_filter($courses, function($c) use ($user) {
                                    return $c->post_status === 'pending' && get_post_meta($c->ID, '_course_reviewer', true) == $user->ID;
                                });
                                if (count($pending_courses) === 1) {
                                    $selected_course_id = reset($pending_courses)->ID;
                                }
                            }
                        }

                        if ($guest_pending_course && !$selected_course_id) {
                            $selected_course_id = $guest_pending_course;
                        }

                        foreach ($courses as $course) {
                            if ($course->post_status === 'pending') {
                                $course_reviewer = get_post_meta($course->ID, '_course_reviewer', true);

                                if (is_user_logged_in()) {
                                    if ($course_reviewer != get_current_user_id()) {
                                        continue;
                                    }
                                } else {
                                    if ($course->ID != $guest_pending_course) {
                                        continue;
                                    }
                                }
                            }

                            $platform = get_post_meta($course->ID, '_course_provider', true); 
                            $selected = $selected_course_id === $course->ID ? 'selected' : '';

                            $label = $course->post_title;
                            if ($course->post_status === 'pending') {
                                $label .= ' (Pending)';
                            }

                            echo '<option value="'.$course->ID.'" data-platform="'.esc_attr($platform).'" '.$selected.'>'
                                .esc_html($label).
                                '</option>';
                        }
                    ?>
                    <option value="custom_add_course" data-custom="true">Can’t find your course? click here</option>
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
            <label for="reviewLinkedin" class="form-label">
                LinkedIn profile <span class="form-tip optional">Optional</span>
            </label>

            <?php
                $connected = false;
                $li_name   = '';
                if ( is_user_logged_in() ) {
                    $uid       = get_current_user_id();
                    $connected = ( get_user_meta( $uid, '_linkedin_connected', true ) === 'yes' );
                    $li_name   = trim( (string) get_user_meta( $uid, '_linkedin_name', true ) );
                }

                if ( $connected ) :
            ?>
            <a href="javascript:void(0)" id="connectLinkedin" class="connect-profile connected" aria-disabled="true"
                data-connected="yes">
                <ion-icon name="logo-linkedin"></ion-icon>
                <span class="text">
                    <?= $li_name ? 'Connected to ' . esc_html($li_name) : 'LinkedIn connected ✓'; ?>
                </span>
            </a>
            <?php else : ?>
            <a href="#" id="connectLinkedin" class="connect-profile" data-connected="no">
                <ion-icon name="logo-linkedin"></ion-icon>
                <span class="text">Connect / Sign in with LinkedIn</span>
            </a>
            <?php endif; ?>

            <input type="hidden" id="linkedinConnectNonce"
                value="<?php echo esc_attr( wp_create_nonce('linkedin_connect_nonce') ); ?>">
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
jQuery(document).ready(function($) {
    $('#reviewCourse').select2({
        placeholder: "— Select Course —",
        allowClear: true,
        width: '100%',
        templateResult: function(state) {
            if (!state.id) return state.text;

            if ($(state.element).data('custom')) {
                return $(
                    '<div class="select2-missing-course">' +
                    '<ion-icon name="add-outline"></ion-icon>' +
                    state.text +
                    '</div>'
                );
            }

            var platform = $(state.element).data('platform');
            if (platform) {
                return $('<div class="select2-course">' +
                    '<span class="select2-course-title">' + state.text + '</span>' +
                    '<span class="select2-course-platform">' + platform + '</span>' +
                    '</div>');
            }
            return state.text;
        },
        templateSelection: function(state) {
            if (!state.id) return state.text;

            if ($(state.element).data('custom')) {
                return $(
                    '<span><ion-icon name="add-outline"></ion-icon>' +
                    state.text +
                    '</span>'
                );
            }

            var platform = $(state.element).data('platform');
            return platform ? state.text + ' — ' + platform : state.text;
        },
        matcher: function(params, data) {
            if ($(data.element).data('custom')) {
                return data;
            }
            return $.fn.select2.defaults.defaults.matcher(params, data);
        }
    });

    $('#reviewCourse').on('select2:select', function(e) {
        var data = e.params.data;
        if ($(data.element).data('custom')) {
            try {
                if (window.__clearReviewDraft) window.__clearReviewDraft();
            } catch (e) {}
            window.location.href = "<?php echo site_url('/add-missing-course'); ?>";
        }
    });
});

document.querySelectorAll('.form-star-group').forEach(group => {
    const stars = group.querySelectorAll('.form-star');
    let selected = 0;

    stars.forEach((star, index) => {
        const input = star.querySelector('input');

        star.addEventListener('mouseenter', () => {
            stars.forEach((s, i) => {
                s.querySelector('ion-icon').setAttribute('name', i <= index ? 'star' :
                    'star-outline');
            });
        });

        star.addEventListener('mouseleave', () => {
            stars.forEach((s, i) => {
                s.querySelector('ion-icon').setAttribute('name', i < selected ? 'star' :
                    'star-outline');
            });
        });

        input.addEventListener('change', () => {
            selected = index + 1;
            stars.forEach((s, i) => {
                s.querySelector('ion-icon').setAttribute('name', i < selected ? 'star' :
                    'star-outline');
            });
        });
    });
});

function validateFile(input, allowedTypes, maxSizeMB, infoId) {
    const info = document.getElementById(infoId);
    info.innerHTML = "";
    info.classList.remove("error");

    if (!input.files.length) return;

    const file = input.files[0];
    const fileType = file.type;
    const fileSizeMB = file.size / (1024 * 1024);

    const validType = allowedTypes.some(type => fileType.includes(type));
    if (!validType) {
        info.innerHTML = "<ion-icon name='close-circle-outline'></ion-icon> Invalid file type. Allowed: " +
            allowedTypes.join(", ");
        info.classList.add("error");
        input.value = "";
        return;
    }

    if (fileSizeMB > maxSizeMB) {
        info.innerHTML =
            `<ion-icon name="close-circle-outline"></ion-icon> File is too large. Max allowed: ${maxSizeMB} MB`;
        info.classList.add("error");
        input.value = "";
        return;
    }

    info.innerHTML =
        `<ion-icon name="checkmark-circle-outline"></ion-icon> Selected: ${file.name} (${fileSizeMB.toFixed(2)} MB)`;
}

document.getElementById("attachementBox").addEventListener("change", function() {
    validateFile(this, ["jpeg", "jpg", "png", "pdf"], 5, "attachementInfo");
});

document.getElementById("videoBox").addEventListener("change", function() {
    validateFile(this, ["mp4"], 20, "videoInfo");
});

jQuery(document).ready(function($) {
    let currentPage = 1;

    function showPage(page) {
        $(".form-page-container.active").removeClass("active").fadeOut(300, function() {
            let target = (page === 1) ? "#pageOne" : "#pageTwo";
            $(target).fadeIn(300).addClass("active");

            $(window).scrollTop($("#addReviewForm").offset().top - 40);
        });

        $(".form-page-number").removeClass("active");
        $(".form-page-number").eq(page - 1).addClass("active");
    }

    function validatePageOne() {
        let hasError = false;
        let firstError = null;
        $("#pageOne .error-message").text("");

        function showError(input, message) {
            const group = $(input).closest('.form-group');
            group.find('.error-message').html(
                "<ion-icon name='alert-circle-outline'></ion-icon> " + message
            );
            if (!firstError) firstError = group;
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

        if (firstError) {
            $('html, body').scrollTop(firstError.offset().top - 50);
        }

        return !hasError;
    }

    function validatePageTwo() {
        let hasError = false;
        let firstError = null;
        $("#pageTwo .error-message").text("");

        function showError(input, message) {
            const group = $(input).closest('.form-group');
            group.find('.error-message').html(
                "<ion-icon name='alert-circle-outline'></ion-icon> " + message
            );
            if (!firstError) firstError = group;
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

        if (firstError) {
            $('html, body').scrollTop(firstError.offset().top - 50);
        }

        return !hasError;
    }

    $("#pageOne .form-button").on("click", function(e) {
        e.preventDefault();
        if (validatePageOne()) {
            currentPage = 2;
            showPage(currentPage);
        }
    });

    $("#pageTwo .form-button").first().on("click", function(e) {
        e.preventDefault();
        currentPage = 1;
        showPage(currentPage);
    });

    $('#addReviewForm').on('submit', function(e) {
        e.preventDefault();
        if (!validatePageTwo()) {
            let firstError = $("#pageTwo .error-message:contains('ion-icon')").first().closest(
                ".form-group");
            if (firstError.length) {
                $('html, body').scrollTop(firstError.offset().top - 50);
            }
            return;
        }

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
                    try {
                        if (window.__clearReviewDraft) window.__clearReviewDraft();
                        localStorage.removeItem('reviewFormDraft');
                    } catch (e) {}

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

    $(".form-page-container").hide();
    $("#pageOne").show().addClass("active");
});

(function() {
    var USER_ID = <?php echo (int) $uid; ?>; // 0 for guest, >0 for logged-in
    var USER_KEY = 'reviewFormDraft_' + USER_ID; // e.g., reviewFormDraft_123
    var GUEST_KEY = 'reviewFormDraft_0'; // guest bucket
    var form = document.getElementById('addReviewForm');
    if (!form) return;

    // --- MIGRATION: if logged in, and no user draft yet, but guest draft exists -> migrate it ---
    if (USER_ID > 0) {
        try {
            var userRaw = localStorage.getItem(USER_KEY);
            var guestRaw = localStorage.getItem(GUEST_KEY);
            if (!userRaw && guestRaw) {
                // Copy guest → user key, then remove guest key
                localStorage.setItem(USER_KEY, guestRaw);
                localStorage.removeItem(GUEST_KEY);
            }
        } catch (e) {}
    }

    function isSkippable(el) {
        return !el.name || el.type === 'file' || /nonce/i.test(el.name);
    }

    function serializeForm() {
        var data = {};
        var els = form.querySelectorAll('input, select, textarea');
        els.forEach(function(el) {
            if (isSkippable(el)) return;

            if (el.type === 'checkbox') {
                if (!data[el.name]) data[el.name] = [];
                if (el.checked) data[el.name].push(el.value || 'on');
            } else if (el.type === 'radio') {
                if (el.checked) data[el.name] = el.value;
            } else {
                data[el.name] = el.value;
            }
        });

        var onPageTwo = document.getElementById('pageTwo').classList.contains('active');
        data.__page = onPageTwo ? 2 : 1;
        data.__ts = Date.now(); // optional: timestamp to resolve conflicts

        try {
            localStorage.setItem(USER_KEY, JSON.stringify(data));
        } catch (e) {}
    }

    function restoreForm() {
        var raw = null;

        // Prefer user key; if guest, that *is* the user key
        try {
            raw = localStorage.getItem(USER_KEY);
        } catch (e) {}
        if (!raw) return;

        var data = {};
        try {
            data = JSON.parse(raw) || {};
        } catch (e) {
            return;
        }

        Object.keys(data).forEach(function(k) {
            if (/nonce/i.test(k)) delete data[k];
        });

        var els = form.querySelectorAll('input, select, textarea');
        els.forEach(function(el) {
            if (isSkippable(el)) return;

            if (el.type === 'checkbox') {
                var arr = data[el.name];
                el.checked = Array.isArray(arr) ? arr.indexOf(el.value || 'on') !== -1 : false;
            } else if (el.type === 'radio') {
                el.checked = (data[el.name] === el.value);
            } else if (data[el.name] != null) {
                el.value = data[el.name];
            }
        });

        var $course = jQuery('#reviewCourse');
        if ($course.length && data['review_course'] != null) {
            $course.val(data['review_course']).trigger('change.select2');
        }

        var page = parseInt(data.__page || 1, 10);
        if (page === 2) {
            jQuery(function($) {
                $(".form-page-container.active").removeClass("active").hide();
                $("#pageTwo").show().addClass("active");
                $(".form-page-number").removeClass("active").eq(1).addClass("active");
            });
        }

        var checked = form.querySelector('.form-star-group input[type=radio]:checked');
        if (checked) checked.dispatchEvent(new Event('change', {
            bubbles: true
        }));
    }

    var saveTimer = null;

    function scheduleSave() {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(serializeForm, 250);
    }

    form.addEventListener('input', scheduleSave, true);
    form.addEventListener('change', scheduleSave, true);

    restoreForm();

    window.__saveReviewDraft = serializeForm;
    window.__clearReviewDraft = function() {
        try {
            localStorage.removeItem(USER_KEY);
        } catch (e) {}
    };
})();
(function() {
    var CURRENT_KEY = 'reviewFormDraft_<?php echo is_user_logged_in() ? get_current_user_id() : 0; ?>';
    var GUEST_KEY = 'reviewFormDraft_0';

    function clearDraft() {
        try {
            localStorage.removeItem(CURRENT_KEY);
            // Also clear guest bucket in case of recent login migration
            localStorage.removeItem(GUEST_KEY);
        } catch (e) {}
    }

    // A) Clear on user-initiated reload (F5/Ctrl-R), *unless* we set a skip flag for LinkedIn
    try {
        var nav = (performance.getEntriesByType && performance.getEntriesByType('navigation') || [])[0];
        var isReload = nav ? (nav.type === 'reload') : (performance.navigation && performance.navigation.type ===
            1);
        var skip = false;
        try {
            skip = sessionStorage.getItem('skipDraftClearOnReload') === '1';
        } catch (e) {}

        if (isReload && !skip) {
            clearDraft();
        }
        // always clean the flag after load
        try {
            sessionStorage.removeItem('skipDraftClearOnReload');
        } catch (e) {}
    } catch (e) {}

    // B) Clear on same-tab navigations via link clicks (intentional leave)
    document.addEventListener('click', function(e) {
        var a = e.target && e.target.closest ? e.target.closest('a[href]') : null;
        if (!a) return;

        // ignore LinkedIn connect button; its flow handles draft preservation
        if (a.id === 'connectLinkedin') return;

        var href = a.getAttribute('href') || '';
        if (!href || href.charAt(0) === '#') return;
        if (/^javascript:/i.test(href)) return;
        if (a.target && a.target.toLowerCase() === '_blank') return; // new tab → keep draft

        clearDraft(); // user is leaving this page intentionally in the same tab
    }, true);
})();
(function() {
    var currentKey = 'reviewFormDraft_<?php echo is_user_logged_in() ? get_current_user_id() : 0; ?>';
    var guestKey = 'reviewFormDraft_0';

    // Do NOT delete the guest key here; let the migration logic handle it on load.
    Object.keys(localStorage).forEach(function(k) {
        if (!k.startsWith('reviewFormDraft_')) return;
        if (k === currentKey) return;
        if (k === guestKey) return; // keep guest draft so it can be migrated after login
        localStorage.removeItem(k);
    });
})();
(function() {
    function openPopup(url, title, w, h) {
        var dl = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
        var dt = window.screenTop !== undefined ? window.screenTop : window.screenY;
        var ww = window.innerWidth || document.documentElement.clientWidth || screen.width;
        var wh = window.innerHeight || document.documentElement.clientHeight || screen.height;
        var left = ((ww - w) / 2) + dl;
        var top = ((wh - h) / 2) + dt;
        var win = window.open(url, title, 'scrollbars=yes,width=' + w + ',height=' + h + ',top=' + top + ',left=' +
            left);
        if (win && win.focus) win.focus();
        return win;
    }

    var btn = document.getElementById('connectLinkedin');
    if (!btn) return;

    var AJAX_URL = "<?php echo esc_js( admin_url('admin-ajax.php', 'relative') ); ?>"; // same-origin safe

    btn.addEventListener('click', function(e) {
        e.preventDefault();

        if (btn.getAttribute('data-connected') === 'yes' || btn.classList.contains('connected')) {
            return;
        }

        if (window.__saveReviewDraft) {
            try {
                window.__saveReviewDraft();
            } catch (e) {}
        }

        try {
            sessionStorage.setItem('skipDraftClearOnReload', '1');
        } catch (e) {}

        var nonce = document.getElementById('linkedinConnectNonce').value;

        var popup = openPopup('about:blank', 'LinkedIn', 600, 720);
        if (!popup) {
            alert('Please allow popups to continue.');
            return;
        }

        jQuery.post(AJAX_URL, {
                action: 'linkedin_connect_start',
                _nonce: nonce
            })
            .done(function(resp) {
                if (typeof resp === 'string') {
                    try {
                        resp = JSON.parse(resp);
                    } catch (e) {}
                }
                if (resp && resp.success && resp.data && resp.data.url) {
                    popup.location.assign(resp.data.url);
                } else {
                    try {
                        sessionStorage.removeItem('skipDraftClearOnReload');
                    } catch (e) {}
                    popup.close();
                    alert((resp && resp.data && resp.data.message) ? resp.data.message :
                        'Could not start LinkedIn connect.');
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                try {
                    sessionStorage.removeItem('skipDraftClearOnReload');
                } catch (e) {}
                popup.close();
                alert('AJAX failed (' + jqXHR.status + '): ' + (errorThrown || textStatus));
            });
    });

    window.addEventListener('message', function(event) {
        var expected = "<?php echo rtrim( esc_js( site_url('/') ), '/' ); ?>";
        var origin = (event.origin || '').replace(/\/$/, '');
        if (origin !== expected) return;

        var data = event.data || {};
        if (data.type !== 'linkedin_connect') return;

        if (data.status === 'success') {
            // ensure the reload keeps the draft
            try {
                sessionStorage.setItem('skipDraftClearOnReload', '1');
            } catch (e) {}
            window.location.reload();
        } else {
            // cleanup flag on failure
            try {
                sessionStorage.removeItem('skipDraftClearOnReload');
            } catch (e) {}
            alert('LinkedIn connect failed: ' + (data.message || 'Unknown error'));
        }
    }, false);
})();
</script>
<?php
    return ob_get_clean();
}
add_shortcode('add_review', 'reviewmvp_add_review_form');

function reviewmvp_handle_review_submission() {
    if (
        !isset($_POST['reviewmvp_add_review_nonce']) || 
        !wp_verify_nonce($_POST['reviewmvp_add_review_nonce'], 'reviewmvp_add_review_action')
    ) {
        wp_send_json_error('Security check failed.');
    }

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

    $reviewer_id   = 0;
    $reviewer_name = '';
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $reviewer_id   = $current_user->ID;
        $reviewer_name = $current_user->display_name;
    }

    // prevent duplicate review (one user → one course)
    if ($reviewer_id > 0) {
        $dupe = get_posts([
            'post_type'   => 'course_review',
            'post_status' => ['pending','publish','draft','future','private'],
            'numberposts' => 1,
            'fields'      => 'ids',
            'meta_query'  => [
                'relation' => 'AND',
                [
                    'key'   => '_review_course',
                    'value' => $course_id,
                    'compare' => '=',
                ],
                [
                    'key'   => '_reviewer',
                    'value' => $reviewer_id,
                    'compare' => '=',
                ],
            ],
        ]);

        if (!empty($dupe)) {
            wp_send_json_error('You’ve already submitted a review for this course.');
        }
    }

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
        'post_author'  => $reviewer_id ?: 0,
    ]);

    if (!$post_id) {
        wp_send_json_error('Something went wrong while saving review.');
    }

    if ($reviewer_id) {
        update_post_meta($post_id, '_reviewer', $reviewer_id);
        update_post_meta($post_id, '_reviewer_name', sanitize_text_field($reviewer_name));
    }

    $statuses = [];

    if ($reviewer_id && !empty($_POST['review_anonymously'])) {
        $statuses[] = 'anonymous';
    }

    if ($reviewer_id) {
        $li_connected = get_user_meta($reviewer_id, '_linkedin_connected', true);
        if ($li_connected === 'yes') {
            $statuses[] = 'verified';
            $li_url = get_user_meta($reviewer_id, '_linkedin_profile', true);
            if ($li_url) {
                update_post_meta($post_id, '_review_linkedin', esc_url_raw($li_url));
            }
        }

        // Count published reviews for this reviewer (by meta only)
        $ids_by_meta = get_posts([
            'post_type'        => 'course_review',
            'post_status'      => 'publish',
            'fields'           => 'ids',
            'posts_per_page'   => -1,
            'no_found_rows'    => true,
            'suppress_filters' => true,
            'meta_query'       => [
                [
                    'key'     => '_reviewer',
                    'value'   => $reviewer_id,
                    'compare' => '=',
                ],
            ],
        ]);

        $published_reviews_count = is_array($ids_by_meta) ? count($ids_by_meta) : 0;

        if ($published_reviews_count >= 5) {
            $statuses[] = 'rising_voice';
            $statuses[] = 'top_voice';
        } elseif ($published_reviews_count >= 2) {
            $statuses[] = 'rising_voice';
        }
    }

    $statuses = array_values(array_unique(array_filter($statuses)));
    update_post_meta($post_id, '_review_status', $statuses);

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

add_action('wp_enqueue_scripts', function() {
    if (is_page('write-a-review')) { 
        wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], null, true);
    }
});

add_action('wp_ajax_linkedin_connect_start', 'reviewmvp_linkedin_connect_start');
add_action('wp_ajax_nopriv_linkedin_connect_start', 'reviewmvp_linkedin_connect_start');

function reviewmvp_linkedin_connect_start(){
    $nonce = isset($_POST['_nonce']) ? $_POST['_nonce'] : '';
    if (!wp_verify_nonce($nonce, 'linkedin_connect_nonce')) {
        wp_send_json_error(['message' => 'Invalid request (nonce).'], 400);
    }

    // NEW: read guest pending course from the session
    if (!session_id()) { session_start(); }
    $guest_course_id = isset($_SESSION['guest_last_course_id']) ? intval($_SESSION['guest_last_course_id']) : 0;

    $client_id = trim((string) get_option('linkedin_client_id', ''));
    if (!$client_id) {
        wp_send_json_error(['message' => 'LinkedIn client ID is missing.'], 500);
    }

    $state = wp_generate_password(20, false, false);

    // CHANGED: store a payload array instead of just the user id
    $payload = [
        'initiator'        => get_current_user_id(), // 0 for guest
        'guest_course_id'  => $guest_course_id,
        'ts'               => time(),
    ];
    set_transient('li_connect_state_' . $state, $payload, 10 * MINUTE_IN_SECONDS);

    $redirect_uri = site_url('/linkedin-callback/');
    $scope = 'openid profile email';

    $url = add_query_arg([
        'response_type' => 'code',
        'client_id'     => $client_id,
        'redirect_uri'  => $redirect_uri,
        'scope'         => $scope,
        'state'         => $state,
    ], 'https://www.linkedin.com/oauth/v2/authorization');

    wp_send_json_success(['url' => $url]);
}

// Helper to convert a guest's pending course to the logged-in reviewer
function reviewmvp_claim_guest_course_for_user( int $user_id, int $course_id ) : void {
    if ($user_id <= 0 || $course_id <= 0) return;

    $post = get_post($course_id);
    if (!$post || $post->post_type !== 'course') return;

    // Only touch pending courses that were marked as 'guest'
    $current_reviewer = get_post_meta($course_id, '_course_reviewer', true);
    if ($post->post_status !== 'pending' || $current_reviewer !== 'guest') return;

    // Assign to this reviewer
    update_post_meta($course_id, '_course_reviewer', $user_id);

    // (Optional) mark a flag so your reviewer query branch with '_course_status_flag' also matches, if you rely on it elsewhere
    if (!metadata_exists('post', $course_id, '_course_status_flag')) {
        update_post_meta($course_id, '_course_status_flag', '1');
    }

    // Clear the guest session pointer if it exists
    if (!session_id()) { @session_start(); }
    if (isset($_SESSION['guest_last_course_id']) && intval($_SESSION['guest_last_course_id']) === $course_id) {
        unset($_SESSION['guest_last_course_id']);
    }
}

add_action('init', function () {
    if (strpos($_SERVER['REQUEST_URI'], 'linkedin-callback') === false) return;

    $finish = function (string $status, string $message = '', array $extra = []) {
        $origin  = esc_js( site_url('/') );
        $payload = ['type' => 'linkedin_connect', 'status' => $status, 'message' => $message] + $extra;

        header('Content-Type: text/html; charset=utf-8'); ?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>LinkedIn</title>
</head>

<body>
    <script>
    (function() {
        var data = <?php echo wp_json_encode($payload); ?>;
        try {
            if (window.opener) window.opener.postMessage(data, "<?php echo $origin; ?>");
        } catch (e) {}
        window.close();
    })();
    </script>
</body>

</html>
<?php exit; };

    // --- Basic checks ---
    $code  = isset($_GET['code'])  ? sanitize_text_field($_GET['code'])  : '';
    $state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';
    if (!$code || !$state) { $finish('error', 'Missing code/state'); }

    // CHANGED: retrieve the payload array (initiator + guest course)
    $payload = get_transient('li_connect_state_' . $state);
    delete_transient('li_connect_state_' . $state);

    $initiator_id    = (is_array($payload) && isset($payload['initiator']))       ? (int) $payload['initiator']      : 0;
    $guest_course_id = (is_array($payload) && isset($payload['guest_course_id'])) ? (int) $payload['guest_course_id']: 0;

    $client_id     = trim((string) get_option('linkedin_client_id', ''));
    $client_secret = trim((string) get_option('linkedin_client_secret', ''));
    $redirect_uri  = site_url('/linkedin-callback/');
    if (!$client_id || !$client_secret) { $finish('error', 'Missing LinkedIn credentials'); }

    // --- Exchange code for token ---
    $response = wp_remote_post('https://www.linkedin.com/oauth/v2/accessToken', [
        'body'    => [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $redirect_uri,
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
        ],
        'timeout' => 20,
    ]);
    if (is_wp_error($response)) { $finish('error', 'Token request failed'); }
    $body         = json_decode(wp_remote_retrieve_body($response), true);
    $access_token = $body['access_token'] ?? '';
    if (!$access_token) { $finish('error', 'No access token'); }

    // --- Fetch OpenID userinfo ---
    $profile = wp_remote_get('https://api.linkedin.com/v2/userinfo', [
        'headers' => ['Authorization' => 'Bearer ' . $access_token],
        'timeout' => 20,
    ]);
    if (is_wp_error($profile)) { $finish('error', 'Profile request failed'); }
    $p      = json_decode(wp_remote_retrieve_body($profile), true);

    $email = isset($p['email']) ? sanitize_email($p['email']) : '';
    $name  = isset($p['name'])  ? sanitize_text_field($p['name'])  : '';
    $sub   = isset($p['sub'])   ? sanitize_text_field($p['sub'])   : '';
    if (!$email) { $finish('error', 'No email returned from LinkedIn'); }

    // Helper: find user already linked to this LinkedIn sub
    $find_user_by_linkedin_sub = function(string $sub) {
        if (!$sub) return null;
        $query = new WP_User_Query([
            'meta_key'   => '_linkedin_profile',
            'meta_value' => 'https://www.linkedin.com/openid/id/' . rawurlencode($sub),
            'number'     => 1,
            'count_total'=> false,
            'fields'     => 'all',
        ]);
        $results = $query->get_results();
        return $results ? $results[0] : null;
    };

    $existing_user_with_email = $email ? get_user_by('email', $email) : null;
    $existing_user_with_sub   = $sub ? $find_user_by_linkedin_sub($sub) : null;

    // =========================
    // LOGGED-IN INITIATOR FLOW
    // =========================
    if ($initiator_id > 0) {
        // Force session to the initiator (User A) to avoid account switching
        if (get_current_user_id() !== $initiator_id) {
            wp_set_current_user($initiator_id);
            wp_set_auth_cookie($initiator_id, true);
        }
        $user_a = get_user_by('id', $initiator_id);
        if (!$user_a) { $finish('error', 'Session expired. Please try again.'); }

        // Rule 1: LinkedIn email equals A's email -> verify A
        if (strcasecmp($email, $user_a->user_email) === 0) {
            update_user_meta($user_a->ID, '_linkedin_profile', esc_url_raw('https://www.linkedin.com/openid/id/' . rawurlencode($sub)));
            update_user_meta($user_a->ID, '_linkedin_connected', 'yes');
            if ($name) update_user_meta($user_a->ID, '_linkedin_name', $name);
            update_user_meta($user_a->ID, '_linkedin_email', $email);
            reviewmvp_claim_guest_course_for_user($user_a->ID, $guest_course_id);
            $finish('success', 'LinkedIn connected');
        }

        // Rule 2: This LinkedIn profile (sub) is already connected to another WP account -> error
        if ($existing_user_with_sub && (int) $existing_user_with_sub->ID !== (int) $user_a->ID) {
            $finish('error', 'This LinkedIn profile is already connected to another account. Please sign in as that account.');
        }

        // Rule 3: LinkedIn email belongs to some other WP account (B) -> error
        if ($existing_user_with_email && (int)$existing_user_with_email->ID !== (int)$user_a->ID) {
            $finish('error', 'This LinkedIn email is already connected to a different account. Please sign in as that account or use a different LinkedIn profile.');
        }

        // Rule 4: LinkedIn email not used in WP -> link to A anyway
        update_user_meta($user_a->ID, '_linkedin_profile', esc_url_raw('https://www.linkedin.com/openid/id/' . rawurlencode($sub)));
        update_user_meta($user_a->ID, '_linkedin_connected', 'yes');
        if ($name) update_user_meta($user_a->ID, '_linkedin_name', $name);
        update_user_meta($user_a->ID, '_linkedin_email', $email);
        reviewmvp_claim_guest_course_for_user($initiator_id, $guest_course_id);
        $finish('success', 'LinkedIn connected');
    }

    // ==============
    // GUEST FLOW
    // ==============
    // 1) If some WP account is already linked to this exact LinkedIn user (same `sub`), log the guest into THAT account.
    if ($existing_user_with_sub) {
        wp_set_current_user($existing_user_with_sub->ID);
        wp_set_auth_cookie($existing_user_with_sub->ID, true);

        // Make sure core fields are up-to-date
        update_user_meta($existing_user_with_sub->ID, '_linkedin_connected', 'yes');
        if ($name) update_user_meta($existing_user_with_sub->ID, '_linkedin_name', $name);
        if ($email) update_user_meta($existing_user_with_sub->ID, '_linkedin_email', $email);

        reviewmvp_claim_guest_course_for_user($existing_user_with_sub->ID, $guest_course_id);
        $finish('success', 'Signed in');
    }

    // 2) Else, if there’s an account with the same email, log into it and mark as connected.
    if ($existing_user_with_email) {
        wp_set_current_user($existing_user_with_email->ID);
        wp_set_auth_cookie($existing_user_with_email->ID, true);

        update_user_meta($existing_user_with_email->ID, '_linkedin_profile', esc_url_raw('https://www.linkedin.com/openid/id/' . rawurlencode($sub)));
        update_user_meta($existing_user_with_email->ID, '_linkedin_connected', 'yes');
        if ($name) update_user_meta($existing_user_with_email->ID, '_linkedin_name', $name);
        if ($email) update_user_meta($existing_user_with_email->ID, '_linkedin_email', $email);

        reviewmvp_claim_guest_course_for_user($existing_user_with_email->ID, $guest_course_id);
        $finish('success', 'Signed in');
    }

    // 3) Otherwise, create a fresh reviewer and log in.
    $username = sanitize_user( current( explode('@', $email) ), true );
    if (username_exists($username)) $username .= '_' . wp_generate_password(4, false);
    $password = wp_generate_password(12, false);
    $user_id  = wp_create_user($username, $password, $email);
    if (is_wp_error($user_id)) { $finish('error', 'User creation failed'); }

    $wpuser = new WP_User($user_id);
    $wpuser->set_role('reviewer');
    wp_update_user(['ID' => $user_id, 'display_name' => $name ?: $username]);

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);

    if ($sub) {
        update_user_meta($user_id, '_linkedin_profile', esc_url_raw('https://www.linkedin.com/openid/id/' . rawurlencode($sub)));
        update_user_meta($user_id, '_linkedin_connected', 'yes');
    }
    if ($name) update_user_meta($user_id, '_linkedin_name', $name);
    if ($email) update_user_meta($user_id, '_linkedin_email', $email);

    reviewmvp_claim_guest_course_for_user($user_id, $guest_course_id);
    $finish('success', 'Signed in');
});

add_action('wp_ajax_reviewmvp_refresh_nonce', 'reviewmvp_refresh_nonce');
add_action('wp_ajax_nopriv_reviewmvp_refresh_nonce', 'reviewmvp_refresh_nonce');
function reviewmvp_refresh_nonce() {
    wp_send_json_success([
        'nonce' => wp_create_nonce('reviewmvp_add_review_action'),
    ]);
}