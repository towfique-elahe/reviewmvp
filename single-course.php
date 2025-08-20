<?php
/**
 * Template for displaying a single Course
 */

get_header(); 

global $post; 
$course_id = $post->ID;

// Helper function to display media files from theme
function getMedia($fileName) {
    $themeDirectory = get_template_directory_uri() . "/assets/media/";
    return $themeDirectory . $fileName;
}

// Helper function to display rating stars
function get_rating_stars($rating) {
    $stars = "";
    for ($i = 1; $i <= 5; $i++) {
        if ($rating >= $i) {
            $stars .= '<span class="r-star active"><ion-icon name="star" aria-hidden="true"></ion-icon></span>';
        } elseif ($rating >= $i - 0.5) {
            $stars .= '<span class="r-star half active"><ion-icon name="star-half" aria-hidden="true"></ion-icon></span>';
        } else {
            $stars .= '<span class="r-star"><ion-icon name="star" aria-hidden="true"></ion-icon></span>';
        }
    }
    return $stars;
}

// Helper function to get rating data
function reviewmvp_get_course_rating_data($course_id) {
    global $wpdb;

    $query = $wpdb->prepare("
        SELECT pm.meta_value
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key = %s
          AND p.post_type = %s
          AND p.post_status = 'publish'
          AND EXISTS (
              SELECT 1 FROM {$wpdb->postmeta} pm2
              WHERE pm2.post_id = p.ID
              AND pm2.meta_key = %s
              AND pm2.meta_value = %d
          )
    ", '_review_rating', 'course_review', '_review_course', $course_id);

    $ratings = $wpdb->get_col($query);

    if (empty($ratings)) {
        return [
            'average'   => 0,
            'count'     => 0,
            'breakdown' => [5=>0,4=>0,3=>0,2=>0,1=>0],
        ];
    }

    $ratings = array_map('intval', $ratings);
    $count   = count($ratings);
    $average = round(array_sum($ratings) / $count, 1);

    // Count each star
    $counts = array_count_values($ratings);

    // Initialize breakdown (percentages)
    $breakdown = [];
    for ($i = 5; $i >= 1; $i--) {
        $starCount = $counts[$i] ?? 0;
        $breakdown[$i] = $count > 0 ? round(($starCount / $count) * 100) : 0;
    }

    return [
        'average'   => $average,
        'count'     => $count,
        'breakdown' => $breakdown,
    ];
}

// Helper function to get overall outcomes data
function reviewmvp_get_course_outcomes($course_id) {
    global $wpdb;

    // fetch all outcomes for reviews of this course
    $query = $wpdb->prepare("
        SELECT pm.meta_value
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key = %s
          AND p.post_type = %s
          AND p.post_status = 'publish'
          AND EXISTS (
              SELECT 1 FROM {$wpdb->postmeta} pm2
              WHERE pm2.post_id = p.ID
              AND pm2.meta_key = %s
              AND pm2.meta_value = %d
          )
    ", '_review_outcome', 'course_review', '_review_course', $course_id);

    $rawOutcomes = $wpdb->get_col($query);

    if (empty($rawOutcomes)) {
        return [];
    }

    // Flatten arrays (stored as serialized arrays in DB)
    $outcomes = [];
    foreach ($rawOutcomes as $val) {
        $arr = maybe_unserialize($val);
        if (is_array($arr)) {
            foreach ($arr as $item) {
                $outcomes[] = $item;
            }
        } else {
            $outcomes[] = $arr;
        }
    }

    if (empty($outcomes)) {
        return [];
    }

    $counts = array_count_values($outcomes);
    $total  = array_sum($counts);

    // map to icons
    $icons = [
        "Improved skill"    => "icon-improved-skill.svg",
        "Built Project"     => "icon-built-project.svg",
        "No Impact"         => "icon-no-impact.svg",
        "Career Boost"      => "icon-career.svg",
        "Earned Income"     => "icon-income.svg",
        "Gained Confidence" => "icon-confidence.svg",
    ];

    $overall = [];
    foreach ($counts as $label => $count) {
        $percent = $total > 0 ? round(($count / $total) * 100) : 0;
        $key = sprintf("%s (%d%%)", $label, $percent);
        $overall[$key] = $icons[$label] ?? 'icon-default.svg';
    }

    return $overall;
}

// Status badges
$statusBadges = [
    "verified"          => ["text" => "", "icon" => "icon-verified-badge.svg"],
    "verified_purchase" => ["text" => "Verified Purchase", "icon" => "icon-verified-purchase.svg"],
    "rising_voice"      => ["text" => "Rising Voice", "icon" => "icon-rising-voice-badge.svg"],
    "top_voice"         => ["text" => "Top Voice", "icon" => "icon-top-voice-badge.svg"]
];

// Outcome badges
$outcomeBadges = [
    "Earned Income" => "icon-income.svg",
    "Career Boost" => "icon-career.svg",
    "Built Project" => "icon-built-project.svg",
    "Improved Skill" => "icon-improved-skill.svg",
    "Gained Confidence" => "icon-confidence.svg",
    "No Impact" => "icon-no-impact.svg"
];

// Course data
$title = get_the_title();
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

// Course overall stats
$stats = reviewmvp_get_course_rating_data($course_id);
$overallRating = $stats['average'];
$reviews_count = $stats['count'];
$rating_breakdown = $stats['breakdown'];
$overallOutcomes = reviewmvp_get_course_outcomes($course_id);

// Reviews data (recent 3)
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

// All reviews data (from recent)
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
        <!-- background -->
        <img src="<?= getMedia('background-1.svg') ?>" alt="background" class="bg">

        <!-- course info/details -->
        <div class="course-info-container">
            <!-- main -->
            <div class="main">
                <div class="course-head">
                    <span class="tag">The designership</span>
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
                            <?php echo $overallRating; ?>
                            <span class="r-text-muted">
                                (
                                <?php echo $reviews_count; ?> reviews)
                            </span>
                        </span>
                    </div>
                    <div class="outcomes">
                        <div class="col">
                            <p class="outcomes-label">
                                Real Outcomes:
                            </p>
                        </div>
                        <div class="col">
                            <?php foreach ($overallOutcomes as $outcomeText => $iconName): ?>
                            <span class="outcome">
                                <img src="<?= getMedia($iconName) ?>" alt="<?= esc_attr($outcomeText) ?> Icon"
                                    class="outcome-icon">
                                <?php echo esc_html($outcomeText); ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button class="write-review-btn">
                        <img src="<?= getMedia('icon-pencil.svg') ?>" alt="Pencil Icon">
                        Write your review
                    </button>
                </div>

                <!-- Tabs -->
                <div class="tabs">
                    <a href="#overview" class="tab-button active">Course Overview</a>
                    <a href="#allReviews" class="tab-button">Reviews</a>
                    <a href="#instructor" class="tab-button">About the Instructor</a>
                </div>

                <!-- Overview -->
                <div class="overview-container" id="overview">
                    <h3 class="section-heading">Course description</h3>

                    <div class="course-description">
                        <div class="course-description-content" style="--clamp:6" data-clamp="6" id="course-desc">
                            <?php echo $course_description; ?>
                        </div>

                        <button class="toggle-desc-btn" type="button" aria-controls="course-desc" aria-expanded="false">
                            <span class="toggle-desc-text">Show more</span>
                            <ion-icon name="chevron-down" aria-hidden="true"></ion-icon>
                        </button>
                    </div>
                </div>

            </div>

            <!-- sidebar -->
            <div class="sidebar">
                <div class="real-rating">
                    <img src="<?= getMedia('icon-real-rating.svg') ?>" alt="Real Rating Icon">
                    <p>Courses can't pay to hide or boost reviews. Every opinion here is real.</p>
                </div>
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
                    <a class="cd-link" href="<?php echo esc_url($link); ?>" target="_blank">
                        Visit course website <ion-icon name="arrow-forward-outline"></ion-icon>
                    </a>
                    <p class="cd-price">
                        $
                        <?php echo number_format($price, 2); ?>
                    </p>
                    <div class="cd-list">
                        <div class="cd-list-item">
                            <img src="<?= getMedia('icon-instructor.svg') ?>" alt="Instructor Icon">
                            <p>
                                <span class="cd-list-label">Instructor:</span>
                                <?php echo esc_html($instructor['name']); ?>
                            </p>
                        </div>
                        <div class="cd-list-item">
                            <img src="<?= getMedia('icon-duration.svg') ?>" alt="Duration Icon">
                            <p>
                                <span class="cd-list-label">Duration:</span>
                                <?php echo $duration; ?> hour
                            </p>
                        </div>
                        <div class="cd-list-item">
                            <img src="<?= getMedia('icon-level.svg') ?>" alt="Level Icon">
                            <p>
                                <span class="cd-list-label">Level:</span>
                                <?php echo esc_html($level_label[$level]); ?>
                            </p>
                        </div>
                        <div class="cd-list-item">
                            <img src="<?= getMedia('icon-certificate.svg') ?>" alt="Certificate Icon">
                            <p>
                                <span class="cd-list-label">Certificate:</span>
                                <?php echo esc_html($certificate); ?>
                            </p>
                        </div>
                        <div class="cd-list-item">
                            <img src="<?= getMedia('icon-refundable.svg') ?>" alt="Refund Icon">
                            <p>
                                <span class="cd-list-label">Refund Available:</span>
                                <?php echo esc_html($refundable); ?>
                            </p>
                        </div>
                        <div class="cd-list-item">
                            <img src="<?= getMedia('icon-language.svg') ?>" alt="Language Icon">
                            <p>
                                <span class="cd-list-label">Language:</span>
                                <?php echo implode(", ", $languages); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- course reviews -->
        <div class="course-reviews-container" id="reviews">
            <h3 class="section-heading">See what reviewers are saying</h3>

            <div class="reviews">
                <?php 
                if ($reviews) :
                    foreach ($reviews as $review_post): 
                        $review_id   = $review_post->ID;

                        // Reviewer info
                        $reviewer_id = get_post_meta($review_id, '_reviewer', true);
                        $user        = $reviewer_id ? get_userdata($reviewer_id) : null;
                        $reviewer    = $user ? $user->display_name : 'Anonymous';
                        $colors = ['#FFB3BA','#FFDFBA','#FFFFBA','#BAFFC9','#BAE1FF','#E0BBE4','#FFCCE5','#D5E8D4','#FEE1E8','#F6EAC2','#C2F0F7','#D4E6F1','#F9E79F','#ABEBC6','#F5CBA7','#E8DAEF','#FADBD8','#D6EAF8','#FCF3CF','#D1F2EB'];
                        if ($user) {
                            $hash = crc32($user->ID);
                            $bg = $colors[$hash % count($colors)];
                        } else {
                            $bg = '#ccc';
                        }

                        // Meta fields
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
                        $proof       = get_post_meta($review_id, '_review_proof', true);
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
                                    <?php echo esc_html($reviewer); ?>
                                </span>
                                <?php
                                if (!empty($review['badges'])):
                                    foreach ($review['badges'] as $badge_key => $badge):
                                        ?>
                                <span class="reviewer-badge <?php echo esc_attr($badge_key); ?>">
                                    <img src="<?= getMedia($badge['icon']) ?>"
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
                                    <img src="<?= getMedia('icon-positive.svg') ?>" alt="Positive Icon">
                                    <span class="review-label">What was good?</span>
                                </p>
                                <p class="pc-review"><?php echo esc_html($good); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if ($bad): ?>
                            <div class="pc-col con">
                                <p class="pc-label">
                                    <img src="<?= getMedia('icon-negative.svg') ?>" alt="Negative Icon">
                                    <span class="review-label">What was bad?</span>
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
                                        <img src="<?= getMedia($iconName) ?>" alt="<?= esc_attr($outcomeText) ?> Icon"
                                            class="outcome-icon">
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
                                <span class="review-item-value"><?php echo esc_html($worth); ?></span>
                            </p>
                            <?php endif; ?>

                            <?php if ($recommend): ?>
                            <p class="review-item">
                                <span class="review-label">Would I recommend this course to others?</span>
                                <span class="review-item-value"><?php echo esc_html($recommend); ?></span>
                            </p>
                            <?php endif; ?>

                            <?php if ($refund): ?>
                            <p class="review-item">
                                <span class="review-label">Refund Experience:</span>
                                <span class="review-item-value"><?php echo esc_html($refund); ?></span>
                            </p>
                            <?php endif; ?>

                            <?php if ($proof): ?>
                            <img src="<?php echo esc_url($proof); ?>" alt="Proof of enrollment" class="proof">
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

            <div class="btn-container">
                <a href="#allReviews" class="all-reviews-btn">See all reviews</a>
            </div>
        </div>


        <!-- course instructor -->
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

            <div class="instructor-profiles">
                <?php 
        // Map instructor fields to Ionicons
        $platform_icons = [
            'linkedin'  => 'logo-linkedin',
            'facebook'  => 'logo-facebook',
            'instagram' => 'logo-instagram',
            'twitter'   => 'logo-twitter',
            'youtube'   => 'logo-youtube'
        ];

        foreach ($platform_icons as $platform => $icon) :
            if (!empty($instructor[$platform])) : ?>
                <a href="<?php echo esc_url($instructor[$platform]); ?>" class="instructor-profile" target="_blank"
                    rel="noopener">
                    <ion-icon name="<?php echo esc_attr($icon); ?>"></ion-icon>
                </a>
                <?php endif;
        endforeach;
        ?>
            </div>
        </div>


        <!-- course all reviews -->
        <div class="all-reviews-container" id="allReviews">
            <h3 class="section-heading">All reviews</h3>
            <div class="all-reviews-layout">
                <!-- overall rating sidebar -->
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

                <!-- all reviews -->
                <div class="reviews" id="all-reviews-list">
                    <?php 
                    if ($allReviews) :
                    $reviewIndex = 0;
                    foreach ($allReviews as $review_post): 
                        $review_id   = $review_post->ID;
                        $hiddenClass = $reviewIndex >= 3 ? 'hidden-review' : '';

                        // Reviewer info
                        $reviewer_id = get_post_meta($review_id, '_reviewer', true);
                        $user        = $reviewer_id ? get_userdata($reviewer_id) : null;
                        $reviewer    = $user ? $user->display_name : 'Anonymous';
                        $colors = ['#FFB3BA','#FFDFBA','#FFFFBA','#BAFFC9','#BAE1FF','#E0BBE4','#FFCCE5','#D5E8D4','#FEE1E8','#F6EAC2','#C2F0F7','#D4E6F1','#F9E79F','#ABEBC6','#F5CBA7','#E8DAEF','#FADBD8','#D6EAF8','#FCF3CF','#D1F2EB'];
                        if ($user) {
                            $hash = crc32($user->ID);
                            $bg = $colors[$hash % count($colors)];
                        } else {
                            $bg = '#ccc';
                        }

                        // Meta fields
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
                        $proof       = get_post_meta($review_id, '_review_proof', true);
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
                                        <?php echo esc_html($reviewer); ?>
                                    </span>
                                    <?php
                                if (!empty($review['badges'])):
                                    foreach ($review['badges'] as $badge_key => $badge):
                                        ?>
                                    <span class="reviewer-badge <?php echo esc_attr($badge_key); ?>">
                                        <img src="<?= getMedia($badge['icon']) ?>"
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
                                        <img src="<?= getMedia('icon-positive.svg') ?>" alt="Positive Icon">
                                        <span class="review-label">What was good?</span>
                                    </p>
                                    <p class="pc-review"><?php echo esc_html($good); ?></p>
                                </div>
                                <?php endif; ?>

                                <?php if ($bad): ?>
                                <div class="pc-col con">
                                    <p class="pc-label">
                                        <img src="<?= getMedia('icon-negative.svg') ?>" alt="Negative Icon">
                                        <span class="review-label">What was bad?</span>
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
                                            <img src="<?= getMedia($iconName) ?>"
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
                                    <span class="review-item-value"><?php echo esc_html($worth); ?></span>
                                </p>
                                <?php endif; ?>

                                <?php if ($recommend): ?>
                                <p class="review-item">
                                    <span class="review-label">Would I recommend this course to others?</span>
                                    <span class="review-item-value"><?php echo esc_html($recommend); ?></span>
                                </p>
                                <?php endif; ?>

                                <?php if ($refund): ?>
                                <p class="review-item">
                                    <span class="review-label">Refund Experience:</span>
                                    <span class="review-item-value"><?php echo esc_html($refund); ?></span>
                                </p>
                                <?php endif; ?>

                                <?php if ($proof): ?>
                                <img src="<?php echo esc_url($proof); ?>" alt="Proof of enrollment" class="proof">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php 
                        $reviewIndex++;
            endforeach;
        else:
            echo "<p>No reviews yet for this course.</p>";
        endif;
                ?>
                    <div class="btn-container">
                        <a href="#" class="load-more-btn">Load more</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</article>

<?php
endwhile;
?>

<?php get_footer(); ?>