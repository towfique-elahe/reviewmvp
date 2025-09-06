<?php
/**
 * Template for displaying a single Course
 */

get_header(); 

global $post; 
$course_id = $post->ID;

$statusBadges = [
    "verified"          => ["text" => "", "icon" => "icon-verified-badge.svg"],
    "verified_purchase" => ["text" => "Verified Purchase", "icon" => "icon-verified-purchase.svg"],
    "rising_voice"      => ["text" => "Rising Voice", "icon" => "icon-rising-voice-badge.svg"],
    "top_voice"         => ["text" => "Top Voice", "icon" => "icon-top-voice-badge.svg"]
];

$outcomeBadges = [
    "Earned Income" => "icon-income.svg",
    "Career Boost" => "icon-career.svg",
    "Built Project" => "icon-built-project.svg",
    "Improved Skill" => "icon-improved-skill.svg",
    "Gained Confidence" => "icon-confidence.svg",
    "No Impact" => "icon-no-impact.svg"
];

$title = get_the_title();
$provider = get_post_meta( get_the_ID(), '_course_provider', true );
$price = get_post_meta( get_the_ID(), '_course_price', true );
$duration = get_post_meta( get_the_ID(), '_course_duration', true );
$certificate = get_post_meta( get_the_ID(), '_course_certificate', true );
$refundable = get_post_meta( get_the_ID(), '_course_refundable', true );
$link = get_post_meta( get_the_ID(), '_course_link', true );
$level = get_post_meta(get_the_ID(), '_course_level', true);
$level_label = [
    'beginner'     => 'Beginner',
    'intermediate' => 'Intermediate',
    'advance'      => 'Advance'
];
$languages = (array) get_post_meta( get_the_ID(), '_course_language', true );
$instructor  = get_post_meta( get_the_ID(), '_course_instructor', true );
$course_description = apply_filters( 'the_content', get_the_content() );

$stats = reviewmvp_get_course_rating_data($course_id);
$overallRating = $stats['average'];
$reviews_count = $stats['count'];
$rating_breakdown = $stats['breakdown'];
$overallOutcomes = reviewmvp_get_course_outcomes($course_id);

$reviewArgs = [
    'post_type'      => 'course_review',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
    'meta_query'     => [
        [
            'key'   => '_review_course',
            'value' => $course_id,
        ]
    ],
    'orderby'        => 'date',
    'order'          => 'DESC',
];
$reviews = get_posts($reviewArgs);

$allReviewArgs = [
    'post_type'      => 'course_review',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'meta_query'     => [
        [
            'key'   => '_review_course',
            'value' => $course_id,
        ]
    ],
    'orderby'        => 'date',
    'order'          => 'DESC',
];
$allReviews = get_posts($allReviewArgs);

?>

<?php
while ( have_posts() ) : the_post();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div id="scDetails" class="course-info">
        <img src="<?= get_theme_media_url('background-1.svg') ?>" alt="background" class="bg">

        <div class="course-info-container">
            <div class="main">
                <div class="course-head">
                    <?php if (!empty($provider)) : ?>
                    <span class="tag">
                        <?php echo esc_html($provider); ?>
                    </span>
                    <?php endif; ?>
                    <h1 class="course-title">
                        <?php echo esc_html($title); ?>
                    </h1>
                    <p class="course-instructor"><em>By
                            <?php echo esc_html($instructor['name']); ?>
                        </em></p>
                    <div class="rating">
                        <span class="r-stars">
                            <?php echo get_rating_stars($overallRating); ?>
                        </span>
                        <span class="r-text">
                            <strong><?php echo $overallRating; ?></strong>
                            <span class="r-text-muted">
                                (<?php echo $reviews_count; ?> reviews)
                            </span>
                        </span>
                    </div>
                    <?php if (!empty($overallOutcomes)) : ?>
                    <div class="outcomes">
                        <div class="col">
                            <p class="outcomes-label">
                                Real Outcomes:
                            </p>
                        </div>
                        <div class="col">
                            <?php foreach ($overallOutcomes as $outcomeText => $iconName): ?>
                            <span class="outcome">
                                <img src="<?= get_theme_media_url($iconName) ?>"
                                    alt="<?= esc_attr($outcomeText) ?> Icon" class="outcome-icon">
                                <?php echo esc_html($outcomeText); ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <a href="<?= site_url('/write-a-review/?course_id=' . $course_id); ?>" class="write-review-btn">
                        <img src="<?= get_theme_media_url('icon-pencil.svg') ?>" alt="Pencil Icon">
                        Write your review
                    </a>
                </div>

                <div class="tabs" id="tabs">
                    <a href="#overview" class="tab-button active">Course Overview</a>
                    <a href="#allReviews" class="tab-button">Reviews</a>
                    <a href="#instructor" class="tab-button">About the Instructor</a>
                </div>

                <div class="overview-container" id="overview">
                    <div class="course-description">
                        <div class="course-description-content" style="--clamp:6" data-clamp="6" id="course-desc">
                            <?php if (!empty(trim($course_description))) : ?>
                            <?php echo $course_description; ?>
                            <?php else : ?>
                            <p class="no-data">No description yet for this course.</p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty(trim($course_description))) : ?>
                        <button class="toggle-desc-btn" type="button" aria-controls="course-desc" aria-expanded="false">
                            <span class="toggle-desc-text">Show more</span>
                            <ion-icon name="chevron-down" aria-hidden="true"></ion-icon>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="sidebar">
                <img src="<?= get_theme_media_url('real-review-badge.svg') ?>" alt="Real review badge"
                    class="real-rating">
                <div class="course-rating-overall">
                    <div class="col">
                        <h2 class="cro-rating">
                            <?php echo $overallRating; ?>
                        </h2>
                        <div class="cro-stars">
                            <?php echo get_rating_stars($overallRating); ?>
                        </div>
                        <p class="cro-review-count">
                            <?php echo $reviews_count; ?> reviews
                        </p>
                    </div>
                    <div class="col">
                        <?php foreach ($rating_breakdown as $stars => $percentage): ?>
                        <div class="rb-row">
                            <p class="rb-text">
                                <?php echo $stars; ?>-star
                            </p>
                            <div class="rb-container">
                                <div class="rb-bar" style="width: <?php echo $percentage; ?>%;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="course-details">
                    <?php if (!empty($link)) : ?>
                    <a class="cd-link" href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener">
                        Visit course website <ion-icon name="arrow-forward-outline"></ion-icon>
                    </a>
                    <?php endif; ?>
                    <p class="cd-price">
                        <?php if (!empty($price)) : ?>
                        $<?php echo number_format((float)$price, 2); ?>
                        <?php else : ?>
                        <span class="cd-price-free">Free</span>
                        <?php endif; ?>
                    </p>
                    <div class="cd-list">
                        <?php if (!empty($instructor['name'])): ?>
                        <div class="cd-list-item">
                            <img src="<?= get_theme_media_url('icon-instructor.svg') ?>" alt="Instructor Icon">
                            <p>
                                <span class="cd-list-label">Instructor:</span>
                                <?php echo esc_html($instructor['name']); ?>
                            </p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($duration)): ?>
                        <div class="cd-list-item">
                            <img src="<?= get_theme_media_url('icon-duration.svg') ?>" alt="Duration Icon">
                            <p>
                                <span class="cd-list-label">Duration:</span>
                                <?php echo intval($duration); ?> hour<?php echo (intval($duration) > 1 ? 's' : ''); ?>
                            </p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($level) && isset($level_label[$level])): ?>
                        <div class="cd-list-item">
                            <img src="<?= get_theme_media_url('icon-level.svg') ?>" alt="Level Icon">
                            <p>
                                <span class="cd-list-label">Level:</span>
                                <?php echo esc_html($level_label[$level]); ?>
                            </p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($certificate)): ?>
                        <div class="cd-list-item">
                            <img src="<?= get_theme_media_url('icon-certificate.svg') ?>" alt="Certificate Icon">
                            <p>
                                <span class="cd-list-label">Certificate:</span>
                                <?php echo esc_html($certificate); ?>
                            </p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($refundable)): ?>
                        <div class="cd-list-item">
                            <img src="<?= get_theme_media_url('icon-refundable.svg') ?>" alt="Refund Icon">
                            <p>
                                <span class="cd-list-label">Refund Available:</span>
                                <?php echo esc_html($refundable); ?>
                            </p>
                        </div>
                        <?php endif; ?>

                        <?php 
                            $clean_languages = array_filter((array) $languages);
                            if (!empty($clean_languages)):
                        ?>
                        <div class="cd-list-item">
                            <img src="<?= get_theme_media_url('icon-language.svg') ?>" alt="Language Icon">
                            <p>
                                <span class="cd-list-label">Language:</span>
                                <?php echo esc_html(implode(", ", $clean_languages)); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="course-reviews-container" id="reviews">
            <h3 class="section-heading">See what reviewers are saying</h3>

            <div class="reviews">
                <?php 
                if ($reviews) :
                    foreach ($reviews as $review_post): 
                        $review_id   = $review_post->ID;

                        $reviewer_id = get_post_meta($review_id, '_reviewer', true);
                        $user        = $reviewer_id ? get_userdata($reviewer_id) : null;
                        $statuses = (array) get_post_meta($review_id, '_review_status', true);
                        if (in_array('anonymous', $statuses)) {
                            $reviewer = 'Anonymous (User chose to stay private)';
                        } else {
                            $reviewer = $user ? $user->display_name : 'Guest <span style="color: #11b981">(Verified by Admin)</span>';
                        }
                        $colors = ['#FFB3BA','#FFDFBA','#FFFFBA','#BAFFC9','#BAE1FF','#E0BBE4','#FFCCE5','#D5E8D4','#FEE1E8','#F6EAC2','#C2F0F7','#D4E6F1','#F9E79F','#ABEBC6','#F5CBA7','#E8DAEF','#FADBD8','#D6EAF8','#FCF3CF','#D1F2EB'];
                        if ($user) {
                            $hash = crc32($user->ID);
                            $bg = $colors[$hash % count($colors)];
                        } else {
                            $bg = '#ccc';
                        }

                        $date_raw = get_post_meta($review_id, '_review_date', true);
                        if ($date_raw) {
                            $timestamp = strtotime($date_raw);
                            $date = date_i18n(get_option('date_format'), $timestamp);
                        } else {
                            $date = '';
                        }
                        $rating      = get_post_meta($review_id, '_review_rating', true);
                        $message     = get_post_meta($review_id, '_review_message', true);
                        $good        = get_post_meta($review_id, '_review_good', true);
                        $bad         = get_post_meta($review_id, '_review_bad', true);
                        $outcomes    = (array) get_post_meta($review_id, '_review_outcome', true);
                        $review['outcomes'] = array_intersect_key($outcomeBadges, array_flip($outcomes));
                        $quality     = get_post_meta($review_id, '_review_quality', true);
                        $support     = get_post_meta($review_id, '_review_support', true);
                        $level     = get_post_meta($review_id, '_review_level', true);
                        $worth       = get_post_meta($review_id, '_review_worth', true);
                        $recommend   = get_post_meta($review_id, '_review_recommend', true);
                        $refund      = get_post_meta($review_id, '_review_refund', true);
                        $video       = get_post_meta($review_id, '_review_video', true);
                        $statuses = (array) get_post_meta($review_id, '_review_status', true);
                        $review['badges'] = array_intersect_key($statusBadges, array_flip($statuses));
                ?>
                <div class="review">
                    <div class="review-head">
                        <div class="col">
                            <div class="avatar" style="background-color: <?php echo esc_attr($bg); ?>;">
                                <?php echo $user ? esc_html(substr($user->display_name,0,2)) : "U"; ?>
                            </div>
                        </div>
                        <div class="col">
                            <div class="reviewer">
                                <span class="reviewer-name">
                                    <?php echo $reviewer; ?>
                                </span>
                                <?php
                                if (!empty($review['badges'])):
                                    foreach ($review['badges'] as $badge_key => $badge):
                                        ?>
                                <span class="reviewer-badge <?php echo esc_attr($badge_key); ?>">
                                    <img src="<?= get_theme_media_url($badge['icon']) ?>"
                                        alt="<?= esc_attr($badge['text']) ?> Icon" class="badge-icon">
                                    <?php echo esc_html($badge['text']); ?>
                                </span>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                            <p class="review-date">
                                <?php echo esc_html($date); ?>
                            </p>
                        </div>
                    </div>
                    <div class="review-rating">
                        <?php echo get_rating_stars((int)$rating); ?>
                    </div>
                    <div class="review-content">
                        <?php if ($message): ?>
                        <p class="review-message"><?php echo esc_html($message); ?></p>
                        <?php endif; ?>

                        <div class="pro-con">
                            <?php if ($good): ?>
                            <div class="pc-col pro">
                                <p class="pc-label">
                                    <img src="<?= get_theme_media_url('icon-positive.svg') ?>" alt="Positive Icon">
                                    What was good?
                                </p>
                                <p class="pc-review"><?php echo esc_html($good); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if ($bad): ?>
                            <div class="pc-col con">
                                <p class="pc-label">
                                    <img src="<?= get_theme_media_url('icon-negative.svg') ?>" alt="Negative Icon">
                                    What was bad?
                                </p>
                                <p class="pc-review"><?php echo esc_html($bad); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="review-item-list">
                            <?php if (!empty($review['outcomes'])): ?>
                            <p class="review-item">
                                <span class="review-label">My Results from this Course</span>
                                <span class="review-item-value">
                                    <?php foreach ($review['outcomes'] as $outcomeText => $iconName): ?>
                                    <span class="outcome">
                                        <img src="<?= get_theme_media_url($iconName) ?>"
                                            alt="<?= esc_attr($outcomeText) ?> Icon" class="outcome-icon">
                                        <?php echo esc_html($outcomeText); ?>
                                    </span>
                                    <?php endforeach; ?>
                                </span>
                            </p>
                            <?php endif; ?>

                            <?php if ($quality): ?>
                            <p class="review-item">
                                <span class="review-label">Content quality:</span>
                                <span class="review-item-value"><?php echo esc_html($quality); ?></span>
                            </p>
                            <?php endif; ?>

                            <?php if ($support): ?>
                            <p class="review-item">
                                <span class="review-label">Instructor & Support Experience:</span>
                                <span class="review-item-value"><?php echo esc_html($support); ?></span>
                            </p>
                            <?php endif; ?>

                            <?php if ($level): ?>
                            <p class="review-item">
                                <span class="review-label">What level did this course feels like?</span>
                                <span class="review-item-value chip"><?php echo esc_html($level); ?></span>
                            </p>
                            <?php endif; ?>

                            <?php if ($worth): ?>
                            <p class="review-item">
                                <span class="review-label">Was it worth the money?</span>
                                <span class="review-item-value chip"><?php echo esc_html($worth); ?></span>
                            </p>
                            <?php endif; ?>

                            <?php if ($recommend): ?>
                            <p class="review-item">
                                <span class="review-label">Would I recommend this course to others?</span>
                                <span class="review-item-value chip"><?php echo esc_html($recommend); ?></span>
                            </p>
                            <?php endif; ?>

                            <?php if ($refund): ?>
                            <p class="review-item">
                                <span class="review-label">Refund Experience:</span>
                                <span class="review-item-value"><?php echo esc_html($refund); ?></span>
                            </p>
                            <?php endif; ?>

                            <?php if ($video): ?>
                            <video class="video-review" controls>
                                <source src="<?php echo esc_url($video); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php 
            endforeach;
        else:
            echo "<p>No reviews yet for this course.</p>";
        endif;
        ?>
            </div>

            <?php if ($reviews) : ?>
            <div class="btn-container">
                <a href="#allReviews" class="all-reviews-btn">See all reviews</a>
            </div>
            <?php endif; ?>
        </div>


        <div class="course-instructor-container" id="instructor">
            <h3 class="section-heading">Instructor details</h3>

            <?php if (!empty($instructor['name'])): ?>
            <p class="instructor-name">
                <?php echo esc_html($instructor['name']); ?>
            </p>
            <?php endif; ?>

            <?php if (!empty($instructor['position'])): ?>
            <p class="instructor-position">
                <?php echo esc_html($instructor['position']); ?>
            </p>
            <?php endif; ?>

            <?php if (!empty($instructor['details'])): ?>
            <p class="instructor-details">
                <?php echo esc_html($instructor['details']); ?>
            </p>
            <?php endif; ?>

            <?php 
                $platform_icons = [
                    'linkedin'  => 'logo-linkedin',
                    'facebook'  => 'logo-facebook',
                    'instagram' => 'logo-instagram',
                    'twitter'   => 'logo-twitter',
                    'youtube'   => 'logo-youtube'
                ];

                $has_platform = false;
                foreach ($platform_icons as $platform => $icon) {
                    if (!empty($instructor[$platform])) {
                        $has_platform = true;
                        break;
                    }
                }
            ?>
            <?php if ($has_platform): ?>
            <div class="instructor-profiles">
                <?php foreach ($platform_icons as $platform => $icon) : ?>
                <?php if (!empty($instructor[$platform])) : ?>
                <a href="<?php echo esc_url($instructor[$platform]); ?>" class="instructor-profile" target="_blank"
                    rel="noopener">
                    <ion-icon name="<?php echo esc_attr($icon); ?>"></ion-icon>
                </a>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>


        <?php if ($reviews) : ?>
        <div class="all-reviews-container" id="allReviews">
            <h3 class="section-heading">All reviews</h3>
            <div class="all-reviews-layout">
                <div class="course-rating-overall">
                    <div class="col">
                        <h2 class="cro-rating">
                            <?php echo $overallRating; ?>
                        </h2>
                        <div class="cro-stars">
                            <?php echo get_rating_stars($overallRating); ?>
                        </div>
                        <p class="cro-review-count">
                            <?php echo $reviews_count; ?> reviews
                        </p>
                    </div>
                    <div class="col">
                        <?php foreach ($rating_breakdown as $stars => $percentage): ?>
                        <div class="rb-row">
                            <p class="rb-text">
                                <?php echo $stars; ?>-star
                            </p>
                            <div class="rb-container">
                                <div class="rb-bar" style="width: <?php echo $percentage; ?>%;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="reviews" id="all-reviews-list">
                    <?php 
                        if ($allReviews) :
                        $reviewIndex = 0;
                        foreach ($allReviews as $review_post): 
                            $review_id   = $review_post->ID;
                            $hiddenClass = $reviewIndex >= 3 ? 'hidden-review' : '';

                            $reviewer_id = get_post_meta($review_id, '_reviewer', true);
                            $user        = $reviewer_id ? get_userdata($reviewer_id) : null;
                            $statuses = (array) get_post_meta($review_id, '_review_status', true);
                            if (in_array('anonymous', $statuses)) {
                                $reviewer = 'Anonymous (User chose to stay private)';
                            } else {
                                $reviewer = $user ? $user->display_name : 'Guest <span style="color: #11b981">(Verified by Admin)</span>';
                            }
                            $colors = ['#FFB3BA','#FFDFBA','#FFFFBA','#BAFFC9','#BAE1FF','#E0BBE4','#FFCCE5','#D5E8D4','#FEE1E8','#F6EAC2','#C2F0F7','#D4E6F1','#F9E79F','#ABEBC6','#F5CBA7','#E8DAEF','#FADBD8','#D6EAF8','#FCF3CF','#D1F2EB'];
                            if ($user) {
                                $hash = crc32($user->ID);
                                $bg = $colors[$hash % count($colors)];
                            } else {
                                $bg = '#ccc';
                            }

                            $date_raw = get_post_meta($review_id, '_review_date', true);
                            if ($date_raw) {
                                $timestamp = strtotime($date_raw);
                                $date = date_i18n(get_option('date_format'), $timestamp);
                            } else {
                                $date = '';
                            }
                            $rating      = get_post_meta($review_id, '_review_rating', true);
                            $message     = get_post_meta($review_id, '_review_message', true);
                            $good        = get_post_meta($review_id, '_review_good', true);
                            $bad         = get_post_meta($review_id, '_review_bad', true);
                            $outcomes    = (array) get_post_meta($review_id, '_review_outcome', true);
                            $review['outcomes'] = array_intersect_key($outcomeBadges, array_flip($outcomes));
                            $quality     = get_post_meta($review_id, '_review_quality', true);
                            $support     = get_post_meta($review_id, '_review_support', true);
                            $worth       = get_post_meta($review_id, '_review_worth', true);
                            $recommend   = get_post_meta($review_id, '_review_recommend', true);
                            $refund      = get_post_meta($review_id, '_review_refund', true);
                            $video       = get_post_meta($review_id, '_review_video', true);
                            $statuses = (array) get_post_meta($review_id, '_review_status', true);
                            $review['badges'] = array_intersect_key($statusBadges, array_flip($statuses));
                    ?>
                    <div class="review <?= $hiddenClass ?>">
                        <div class="review-head">
                            <div class="col">
                                <div class="avatar" style="background-color: <?php echo esc_attr($bg); ?>;">
                                    <?php echo $user ? esc_html(substr($user->display_name,0,2)) : "U"; ?>
                                </div>
                            </div>
                            <div class="col">
                                <div class="reviewer">
                                    <span class="reviewer-name">
                                        <?php echo $reviewer; ?>
                                    </span>
                                    <?php
                                if (!empty($review['badges'])):
                                    foreach ($review['badges'] as $badge_key => $badge):
                                        ?>
                                    <span class="reviewer-badge <?php echo esc_attr($badge_key); ?>">
                                        <img src="<?= get_theme_media_url($badge['icon']) ?>"
                                            alt="<?= esc_attr($badge['text']) ?> Icon" class="badge-icon">
                                        <?php echo esc_html($badge['text']); ?>
                                    </span>
                                    <?php
                                    endforeach;
                                endif;
                                ?>
                                </div>
                                <p class="review-date">
                                    <?php echo esc_html($date); ?>
                                </p>
                            </div>
                        </div>
                        <div class="review-rating">
                            <?php echo get_rating_stars((int)$rating); ?>
                        </div>
                        <div class="review-content">
                            <?php if ($message): ?>
                            <p class="review-message"><?php echo esc_html($message); ?></p>
                            <?php endif; ?>

                            <div class="pro-con">
                                <?php if ($good): ?>
                                <div class="pc-col pro">
                                    <p class="pc-label">
                                        <img src="<?= get_theme_media_url('icon-positive.svg') ?>" alt="Positive Icon">
                                        What was good?
                                    </p>
                                    <p class="pc-review"><?php echo esc_html($good); ?></p>
                                </div>
                                <?php endif; ?>

                                <?php if ($bad): ?>
                                <div class="pc-col con">
                                    <p class="pc-label">
                                        <img src="<?= get_theme_media_url('icon-negative.svg') ?>" alt="Negative Icon">
                                        What was bad?
                                    </p>
                                    <p class="pc-review"><?php echo esc_html($bad); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="review-item-list">
                                <?php if (!empty($review['outcomes'])): ?>
                                <p class="review-item">
                                    <span class="review-label">My Results from this Course</span>
                                    <span class="review-item-value">
                                        <?php foreach ($review['outcomes'] as $outcomeText => $iconName): ?>
                                        <span class="outcome">
                                            <img src="<?= get_theme_media_url($iconName) ?>"
                                                alt="<?= esc_attr($outcomeText) ?> Icon" class="outcome-icon">
                                            <?php echo esc_html($outcomeText); ?>
                                        </span>
                                        <?php endforeach; ?>
                                    </span>
                                </p>
                                <?php endif; ?>

                                <?php if ($quality): ?>
                                <p class="review-item">
                                    <span class="review-label">Content quality:</span>
                                    <span class="review-item-value"><?php echo esc_html($quality); ?></span>
                                </p>
                                <?php endif; ?>

                                <?php if ($support): ?>
                                <p class="review-item">
                                    <span class="review-label">Instructor & Support Experience:</span>
                                    <span class="review-item-value"><?php echo esc_html($support); ?></span>
                                </p>
                                <?php endif; ?>

                                <?php if ($worth): ?>
                                <p class="review-item">
                                    <span class="review-label">Was it worth the money?</span>
                                    <span class="review-item-value chip"><?php echo esc_html($worth); ?></span>
                                </p>
                                <?php endif; ?>

                                <?php if ($recommend): ?>
                                <p class="review-item">
                                    <span class="review-label">Would I recommend this course to others?</span>
                                    <span class="review-item-value chip"><?php echo esc_html($recommend); ?></span>
                                </p>
                                <?php endif; ?>

                                <?php if ($refund): ?>
                                <p class="review-item">
                                    <span class="review-label">Refund Experience:</span>
                                    <span class="review-item-value"><?php echo esc_html($refund); ?></span>
                                </p>
                                <?php endif; ?>

                                <?php if ($video): ?>
                                <video class="video-review" controls>
                                    <source src="<?php echo esc_url($video); ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php 
                                $reviewIndex++;
                            endforeach;
                        else:
                            echo "<p style='text-align: center;'>No reviews yet for this course.</p>";
                        endif;
                    ?>
                    <?php if ($reviews) : ?>
                    <div class="btn-container">
                        <a href="#" class="load-more-btn">Load more</a>
                    </div>
                    <?php endif; ?>
                    <button type="button" class="back-to-top-btn" aria-label="Back to top">
                        <ion-icon name="arrow-up-outline"></ion-icon>
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</article>

<?php
endwhile;
?>

<?php get_footer(); ?>