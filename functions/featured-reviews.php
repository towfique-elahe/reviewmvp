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
            <span class="rating">★ 4.8</span>
            <span class="reviews-count">109 reviews</span>
            <span class="platform">Udemy</span>
        </div>
        <h3 class="course-title">Complete python bootcamp: from zero to hero</h3>
        <p class="instructor">By Jose portilla</p>
        <div class="review-stats">
            <span>🧠 Improved skill (7%)</span>
            <span>🛠 Built project (8%)</span>
            <span>⚪ No impact (1%)</span>
        </div>
        <div class="reviewer">
            <p><strong>Reviewed by Lisa M.</strong> <span class="verified">✔ Verified purchase</span></p>
        </div>
        <ul class="pros-cons">
            <li class="pro">👍 Well organized content, I like his teaching style.</li>
            <li class="con">👎 Overpriced! most of these are free in youtube. If...</li>
        </ul>
        <p class="worth">💰 <strong>Worth the money?</strong> <span class="yes">94% say YES</span></p>
        <button class="view-reviews">View all reviews</button>
    </div>
    <?php endfor; ?>
</div>
<?php
    return ob_get_clean();
}
add_shortcode('featured_reviews', 'featured_reviews_shortcode');