<?php
/**
 * Function File Name: Featured Reviews
 * 
 * The file for custom featured reviews of courses.
 */

// Shortcode: [featured_reviews]

function featured_reviews_shortcode() {

    // Status badges
    $statusBadges = [
        "verified"          => ["text" => "", "icon" => "icon-verified-badge.svg"],
        "verified_purchase" => ["text" => "Verified Purchase", "icon" => "icon-verified-purchase.svg"],
        "rising_voice"      => ["text" => "Rising Voice", "icon" => "icon-rising-voice-badge.svg"],
        "top_voice"         => ["text" => "Top Voice", "icon" => "icon-top-voice-badge.svg"]
    ];

    // Reviews data (recent 3)
    $reviewArgs = [
        'post_type'      => 'course_review',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    $reviews = get_posts($reviewArgs);

    $outcomeBadges = $outcomeBadges ?? [];
    $statusBadges  = $statusBadges ?? [];
    
    ob_start();
?>

<div id="featuredReviews" class="featured-reviews">
    <?php 
        if ($reviews) :
            foreach ($reviews as $review_post):
                $review_id   = $review_post->ID;

                // Reviewer info
                $reviewer_id = get_post_meta($review_id, '_reviewer', true);
                $user        = $reviewer_id ? get_userdata($reviewer_id) : null;
                $reviewer    = $user ? $user->display_name : 'Anonymous';

                // Meta fields
                $good        = get_post_meta($review_id, '_review_good', true);
                $bad         = get_post_meta($review_id, '_review_bad', true);
                $statuses = (array) get_post_meta($review_id, '_review_status', true);
                $review['badges'] = is_array($statusBadges) 
                    ? array_intersect_key($statusBadges, array_flip($statuses)) 
                    : [];

                // Course data
                $course_id   = get_post_meta($review_id, '_review_course', true);
                $course_post = $course_id ? get_post($course_id) : null;
                if ($course_post) {
                    $course_title   = get_the_title($course_post);
                    $course_link    = get_permalink($course_post);
                    $course_provider   = get_post_meta($course_id, '_course_provider', true);
                    $course_instructor = get_post_meta($course_id, '_course_instructor', true);
                    if (!is_array($course_instructor)) {
                        $course_instructor = [
                            'name'     => '',
                        ];
                    }
                    $ratingStats = reviewmvp_get_course_rating_data($course_id);
                    $overallRating = $ratingStats['average'];
                    $reviews_count = $ratingStats['count'];
                    $overallOutcomes = reviewmvp_get_course_outcomes($course_id);
                    $reviewStats = reviewmvp_get_course_review_stats($course_id);
                    $worthStat = $reviewStats['worth'];
                }
    ?>
    <div class="review-card">
        <div class="review-header">
            <div class="left-col">
                <span class="rating">
                    <img src="<?php echo get_theme_icon_url('icon-star.svg'); ?>" alt="Star" />
                    <?php echo esc_html($overallRating); ?>
                </span>
                <span class="reviews-count"><?php echo esc_html($reviews_count); ?> reviews</span>
            </div>
            <div class="right-col">
                <span class="platform"><?php echo esc_html($course_provider); ?></span>
            </div>
        </div>
        <div class="course-info">
            <a href="<?= esc_attr($course_link) ?>">
                <h3 class="course-title"><?php echo esc_html($course_title); ?></h3>
            </a>
            <p class="instructor">By <?php echo esc_html($course_instructor['name']); ?></p>
        </div>
        <div class="review-stats">
            <?php foreach ($overallOutcomes as $outcomeText => $iconName): ?>
            <span class="stat">
                <img src="<?= get_theme_media_url($iconName) ?>" alt="<?= esc_attr($outcomeText) ?> Icon">
                <?php echo esc_html($outcomeText); ?>
            </span>
            <?php endforeach; ?>
        </div>
        <div class="reviewer">
            <p class="name">
                Reviewed by <?php echo esc_html($reviewer); ?>
            </p>
            <?php
                if (!empty($review['badges'])):
                    foreach ($review['badges'] as $badge_key => $badge):
            ?>
            <span class="reviewer-badge <?php echo esc_attr($badge_key); ?>">
                <img src="<?= get_theme_media_url($badge['icon']) ?>" alt="<?= esc_attr($badge['text']) ?> Icon"
                    class="badge-icon">
                <?php echo esc_html($badge['text']); ?>
            </span>
            <?php
                    endforeach;
                endif;
            ?>
        </div>
        <ul class="pros-cons">
            <li class="pro">
                <img src="<?php echo get_theme_icon_url('icon-positive.svg'); ?>" alt="Pro" />
                <span>
                    <?php echo esc_html($good); ?>
                </span>
            </li>
            <li class="con">
                <img src="<?php echo get_theme_icon_url('icon-negative.svg'); ?>" alt="Con" />
                <span>
                    <?php echo esc_html($bad); ?>
                </span>
            </li>
        </ul>
        <?php 
            $worthColor = '#DC2625';
            if ($worthStat >= 70) {
                $worthColor = '#11B981';
            } elseif ($worthStat >= 30) {
                $worthColor = '#F6C701';
            }
        ?>
        <p class="worth">
            <img src="<?php echo get_theme_icon_url('icon-worth.svg'); ?>" alt="Money" />
            <strong>Worth the money?</strong>
            <span class="yes" style="color: <?php echo esc_attr($worthColor); ?>">
                <?php echo esc_html($worthStat); ?>% say YES
            </span>
        </p>
        <a href="<?= esc_attr($course_link).'/#allReviews' ?>" class="view-reviews">View all reviews</a>
    </div>
    <?php
            endforeach;
        else:
            echo "<p>No reviews yet for any course.</p>";
        endif;
    ?>
</div>

<?php
    return ob_get_clean();
}
add_shortcode('featured_reviews', 'featured_reviews_shortcode');