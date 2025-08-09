<?php
// Register shortcode
function render_single_course_page() {
    // Generate demo data for the course
    $seed = 42; // Initialize seed for randomization
    function rnd() {
        global $seed;
        $seed = ($seed * 1664525 + 1013904223) % 4294967296;
        return $seed / 4294967296;
    }

    // Demo Data
    $course_title = "Ultimate Figma Masterclass (UX/UI design)";
    $course_author = "Michael Wong";
    $course_rating = round(3 + rnd() * 2, 1); // Random rating between 3 and 5
    $reviews_count = 10 + floor(rnd() * 200); // Random reviews count between 10 and 200
    $course_price = round((rnd() * 100) + 50, 2); // Price between $50 and $150
    $course_duration = 8; // 8 hours
    $outcomes = ["Improved skill (40%)", "Built project (30%)", "No impact (10%)", "Career boost", "Earned income", "Gained confidence"];
    $course_level = ["Beginner"];
    
    // Reviews (3 example reviews)
    $reviews = [
        [
            "avatar" => "LM",
            "name" => "Lisa M",
            "role" => "Software developer",
            "date" => "Mar 3, 2025",
            "review" => "I liked the course but could be better. Some sections felt rushed.",
            "rating" => 4,
            "recommend" => "Yes",
            "refund" => "Didn’t issue refund"
        ],
        [
            "avatar" => "JH",
            "name" => "John H",
            "role" => "Web Designer",
            "date" => "Feb 28, 2025",
            "review" => "Great course! The content was really useful and the instructor was great.",
            "rating" => 5,
            "recommend" => "Yes",
            "refund" => "Refund issued"
        ],
        [
            "avatar" => "RK",
            "name" => "Ravi K",
            "role" => "UX/UI Designer",
            "date" => "Feb 25, 2025",
            "review" => "Good course but expected more practical examples.",
            "rating" => 3,
            "recommend" => "No",
            "refund" => "No refund"
        ]
    ];

    ob_start(); ?>

<div class="single-course-wrapper">
    <!-- Header Section -->
    <div class="course-header">
        <div class="course-title-group">
            <span class="tag">The designership</span>
            <h1 class="course-title"><?php echo esc_html($course_title); ?></h1>
            <p class="course-author"><em>By <?php echo esc_html($course_author); ?></em></p>
            <div class="rating">
                <span class="r-stars">
                    <?php echo get_rating_stars($course_rating); ?>
                </span>
                <span class="r-text"><?php echo $course_rating; ?>
                    <span class="r-text-muted">
                        (<?php echo $reviews_count; ?> reviews)
                    </span>
                </span>
            </div>
            <div class="outcomes">
                <?php foreach ($outcomes as $outcome): ?>
                <span><?php echo esc_html($outcome); ?></span>
                <?php endforeach; ?>
            </div>
            <button class="write-review-btn">✏️ Write your review</button>
        </div>

        <div class="course-info-card">
            <div class="course-rating-overall">
                <h2><?php echo $course_rating; ?></h2>
                <p><?php echo $reviews_count; ?> reviews</p>
                <div class="stars-bar">
                    <?php echo get_rating_stars($course_rating); ?>
                </div>
            </div>
            <div class="course-pricing">
                <a class="visit-button" href="#">Visit course website →</a>
                <h3>$<?php echo number_format($course_price, 2); ?></h3>
                <ul>
                    <li>👤 Instructor: <?php echo esc_html($course_author); ?></li>
                    <li>⏳ Duration: <?php echo $course_duration; ?> hour</li>
                    <li>📘 Level: <?php echo implode(", ", $course_level); ?></li>
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

        <?php foreach ($reviews as $review): ?>
        <div class="review">
            <div class="review-head">
                <span class="avatar"><?php echo esc_html($review['avatar']); ?></span>
                <div>
                    <strong><?php echo esc_html($review['name']); ?></strong><br>
                    <span><?php echo esc_html($review['role']); ?></span>
                    <span class="badge">Rising voice</span>
                </div>
                <span class="review-date"><?php echo esc_html($review['date']); ?></span>
            </div>
            <div class="review-body">
                <p><strong><?php echo esc_html($review['review']); ?></strong></p>
                <p><strong>My Results:</strong> <?php echo implode(", ", array_slice($outcomes, 0, 3)); ?></p>
                <p><strong>Content quality:</strong> <?php echo get_rating_stars($review['rating']); ?></p>
                <p><strong>Instructor & Support:</strong> I like the teaching style.</p>
                <p><strong>Worth the money?</strong> No, overpriced</p>
                <p><strong>Recommend?</strong> Yes</p>
                <p><strong>Refund Experience:</strong> <?php echo esc_html($review['refund']); ?></p>
            </div>
        </div>
        <?php endforeach; ?>

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

// Helper function to display rating stars
function get_rating_stars($rating) {
    $stars = "";
    for ($i = 1; $i <= 5; $i++) {
        if ($rating >= $i) {
            $stars .= '<span class="r-star active"><ion-icon name="star" aria-hidden="true"></ion-icon></span>';
        } elseif ($rating >= $i - 0.5) {
            $stars .= '<span class="r-star active"><ion-icon name="star-half" aria-hidden="true"></ion-icon></span>';
        } else {
            $stars .= '<span class="r-star"><ion-icon name="star" aria-hidden="true"></ion-icon></span>';
        }
    }
    return $stars;
}

add_shortcode('single_course_page', 'render_single_course_page');
?>