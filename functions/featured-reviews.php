<?php
/**
 * Function File Name: Featured Reviews
 * 
 * The file for custom featured reviews of courses.
 */

// Shortcode: [featured_reviews]

function featured_reviews_shortcode() {
    ob_start();
    ?>
<div id="featuredReviews" class="featured-reviews">
    <?php for ($i = 0; $i < 3; $i++): ?>
    <div class="review-card">
        <div class="review-header">
            <div class="left-col">
                <span class="rating">
                    <img src="<?php echo get_theme_icon_url('icon-star.svg'); ?>" alt="Star" /> 4.8
                </span>
                <span class="reviews-count">109 reviews</span>
            </div>
            <div class="right-col">
                <span class="platform">Udemy</span>
            </div>
        </div>
        <div class="course-info">
            <h3 class="course-title">Complete python bootcamp: from zero to hero</h3>
            <p class="instructor">By <strong>Jose portilla</strong></p>
        </div>
        <div class="review-stats">
            <span class="stat">
                <img src="<?php echo get_theme_icon_url('icon-improved-skill.svg'); ?>" alt="Improved Skill" /> Improved
                skill (7%)
            </span>
            <span class="stat">
                <img src="<?php echo get_theme_icon_url('icon-built-project.svg'); ?>" alt="Built Project" /> Built
                project (8%)
            </span>
            <span class="stat">
                <img src="<?php echo get_theme_icon_url('icon-no-impact.svg'); ?>" alt="No Impact" /> No impact (1%)
            </span>
        </div>
        <div class="reviewer">
            <p class="name">
                Reviewed by Lisa M.
            </p>
            <img src="<?php echo get_theme_icon_url('icon-verified-badge.svg'); ?>" alt="Verified Badge"
                class="verified-badge" />
            <span class="verified-purchase">
                <img src="<?php echo get_theme_icon_url('icon-verified-purchase.svg'); ?>" alt="Verified Purchase" />
                Verified purchase
            </span>
        </div>
        <ul class="pros-cons">
            <li class="pro">
                <img src="<?php echo get_theme_icon_url('icon-positive.svg'); ?>" alt="Pro" />
                <span>Well organized content, I
                    like his teaching style.</span>
            </li>
            <li class="con">
                <img src="<?php echo get_theme_icon_url('icon-negative.svg'); ?>" alt="Con" />
                <span>Overpriced! most of these
                    are free in youtube. If...</span>
            </li>
        </ul>
        <p class="worth">
            <img src="<?php echo get_theme_icon_url('icon-worth.svg'); ?>" alt="Money" />
            <strong>Worth the money?</strong> <span class="yes">94% say YES</span>
        </p>
        <button class="view-reviews">View all reviews</button>
    </div>
    <?php endfor; ?>
</div>
<?php
    return ob_get_clean();
}
add_shortcode('featured_reviews', 'featured_reviews_shortcode');