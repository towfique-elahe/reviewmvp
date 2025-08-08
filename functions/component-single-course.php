<?php
// Register shortcode
function render_single_course_page() {
    ob_start(); ?>

<div class="single-course-wrapper">
    <!-- Header Section -->
    <div class="course-header">
        <div class="course-title">
            <span class="tag">The designership</span>
            <h1>Ultimate Figma Masterclass (UX/UI design)</h1>
            <p><em>By Michael Wong</em></p>
            <div class="rating">
                ★★★★☆ <span>(4.0 from 10 reviews)</span>
            </div>
            <div class="outcomes">
                <span>💪 Improved skill (40%)</span>
                <span>🛠 Built project (30%)</span>
                <span>🚫 No impact (10%)</span>
                <span>💼 Career boost</span>
                <span>💰 Earned income</span>
                <span>🎯 Gained confidence</span>
            </div>
            <button class="write-review-btn">✏️ Write your review</button>
        </div>

        <div class="course-info-card">
            <div class="course-rating-overall">
                <h2>4.0</h2>
                <p>10 reviews</p>
                <div class="stars-bar">
                    ★★★★★ — 6<br>
                    ★★★★☆ — 2<br>
                    ★★★☆☆ — 1<br>
                    ★★☆☆☆ — 1<br>
                    ★☆☆☆☆ — 0
                </div>
            </div>
            <div class="course-pricing">
                <a class="visit-button" href="#">Visit course website →</a>
                <h3>$100</h3>
                <ul>
                    <li>👤 Instructor: Michael Wong</li>
                    <li>⏳ Duration: 8 hour</li>
                    <li>📘 Level: Beginner</li>
                    <li>📄 Certificate: No</li>
                    <li>🔁 Refund Available: No</li>
                    <li>🌍 Language: English</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <button class="tab-button active" data-tab="overview">Course Overview</button>
        <button class="tab-button" data-tab="reviews">Reviews</button>
        <button class="tab-button" data-tab="instructor">About the Instructor</button>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content active" id="overview">
        <h3>Course description</h3>
        <p>The UX/UI design industry is highly competitive, and staying ahead requires mastery of tools like Figma.</p>
        <p>This course will teach you everything from basic functionality to advanced design systems, components,
            prototyping, project workflow, and handoff.</p>
        <a href="#">Show more ↓</a>
    </div>

    <div class="tab-content" id="reviews">
        <h3>See what reviewers are saying</h3>

        <div class="review">
            <div class="review-head">
                <span class="avatar">LM</span>
                <div>
                    <strong>Lisa M</strong><br>
                    <span>Software developer</span>
                    <span class="badge">Rising voice</span>
                </div>
                <span class="review-date">Mar 3, 2025</span>
            </div>
            <div class="review-body">
                <p><strong>I liked the course but could be better</strong></p>
                <p>👍 What was good? <br> I like the teaching style.</p>
                <p>👎 What was bad? <br> Some sections felt rushed.</p>
                <p><strong>My Results:</strong> 💰 Earned income, 🎯 Gained confidence</p>
                <p><strong>Content quality:</strong> ★★★★☆</p>
                <p><strong>Instructor & Support:</strong> I like the teaching style.</p>
                <p><strong>Worth the money?</strong> ❌ No, overpriced</p>
                <p><strong>Recommend?</strong> ✅ Yes</p>
                <p><strong>Refund Experience:</strong> Didn’t issue refund</p>
            </div>
        </div>

        <!-- Clone the .review block for more entries -->

        <button class="see-all-reviews">See all reviews</button>
    </div>

    <div class="tab-content" id="instructor">
        <h3>About the Instructor</h3>
        <p>Michael Wong is a UX/UI designer with 10+ years of experience. He has taught over 50,000 students and is
            known for clear, concise, and practical teaching approaches.</p>
    </div>
</div>

<?php
    return ob_get_clean();
}
add_shortcode('single_course_page', 'render_single_course_page');
?>