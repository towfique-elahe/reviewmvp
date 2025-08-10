<?php

function getIcon($iconName) {
    $themeDirectory = "/wp-content/themes/reviewmvp";
    $iconPath = $themeDirectory . "/assets/media/";
    $iconFile = $iconPath . $iconName;

    return $iconFile;
}

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
    $course_certificate = "No";
    $course_refundable = "No";
    $course_link = "https://example.com";
    $course_level = ["Beginner"];
    $course_language = ["English"];
    $outcomes = [
        "Improved skill (40%)" => "icon-improved-skill.svg",
        "Built project (30%)" => "icon-built-project.svg",
        "No impact (10%)" => "icon-no-impact.svg",
        "Career boost" => "icon-career.svg",
        "Earned income" => "icon-income.svg",
        "Gained confidence" => "icon-confidence.svg"
    ];

    // Rating Breakdown Data
    $rating_breakdown = [
        5 => 80, // 80% of reviews are 5 stars
        4 => 45, // 45% of reviews are 4 stars
        3 => 60, // 60% of reviews are 3 stars
        2 => 20, // 20% of reviews are 2 stars
        1 => 15  // 15% of reviews are 1 star
    ];

    // Course description
    $course_description = "
        <p>The UX/UI design industry is highly competitive, and staying ahead requires mastery of tools like <a href='https://www.figma.com' target='_blank'>Figma</a>.</p>

        <h3>What You'll Learn</h3>
        <p>This course will teach you everything from basic functionality to advanced topics, including:</p>
        <ul>
            <li>Design Systems</li>
            <li>Components and Prototyping</li>
            <li>Project Workflow</li>
            <li>Design Handoff</li>
        </ul>

        <h3>Course Content</h3>
        <p>This course is divided into several modules that cover essential topics for mastering Figma. Each module will help you progressively improve your UX/UI design skills.</p>

        <h4>Module 1: Introduction to Figma</h4>
        <p>Learn the basics of Figma, including how to navigate the interface and create simple design elements.</p>

        <h4>Module 2: Design Systems & Components</h4>
        <p>Explore the power of design systems in Figma and how components can streamline your design process.</p>

        <h4>Module 3: Prototyping in Figma</h4>
        <p>Master the prototyping tools in Figma to create interactive designs that bring your ideas to life.</p>

        <h4>Module 4: Advanced Design Workflows</h4>
        <p>Learn how to work more efficiently with Figma, including tips for collaboration and sharing designs with teams.</p>

        <h4>Module 5: Handoff & Developer Collaboration</h4>
        <p>Understand the best practices for design handoff to developers and how to use Figma's features to facilitate collaboration.</p>

        <h3>Why Take This Course?</h3>
        <p>This course is perfect for aspiring UX/UI designers looking to enhance their skills and take their design process to the next level. Whether you're just starting or have some experience, you'll gain valuable insights and hands-on experience with Figma.</p>

        <p>Enroll now and start your journey towards becoming a proficient UX/UI designer with Figma!</p>
    ";
    
    // Reviews (3 example reviews)
    $reviews = [
        [
            "avatar" => "LM",
            "name" => "Lisa M",
            "role" => "Software developer",
            "date" => "Mar 3, 2025",
            "review" => "I liked the course but could be better. Some sections felt rushed.",
            "pro" => "The instructor's explanations were clear, and I learned some new skills.",
            "con" => "Some sections felt rushed and could have used more practical examples.",
            "content_quality" => "Good, but not enough real-world application examples.",
            "instructor_support_exp" => "The instructor was very responsive to questions and provided helpful feedback.",
            "worth_it" => "Yes, it's a good course for beginners, but I expected more in-depth content for intermediate learners.",
            "rating" => 4,
            "recommend" => "Yes",
            "refund" => "Didn’t issue refund",
            "outcomes" => [
                "Earned income" => "icon-income.svg",
                "Gained confidence" => "icon-confidence.svg"
            ],
            "badges" => [
                "verified" => ["text" => "", "icon" => "icon-verified-badge.svg"],
                "rising-voice" => ["text" => "Rising voice", "icon" => "icon-rising-voice-badge.svg"]
            ]
        ],
        [
            "avatar" => "JH",
            "name" => "John H",
            "role" => "Web Designer",
            "date" => "Feb 28, 2025",
            "review" => "Great course! The content was really useful and the instructor was great.",
            "pro" => "Well-structured lessons with practical insights into design systems.",
            "con" => "A bit too basic for someone with prior experience in Figma.",
            "content_quality" => "The content was excellent, with high-quality visuals and well-organized materials.",
            "instructor_support_exp" => "The instructor was knowledgeable and provided good feedback throughout the course.",
            "worth_it" => "Absolutely! The course is worth the money if you're a beginner or intermediate designer.",
            "rating" => 5,
            "recommend" => "Yes",
            "refund" => "Refund issued",
            "outcomes" => [
                "Earned income" => "icon-income.svg",
                "Gained confidence" => "icon-confidence.svg"
            ],
            "badges" => [
                "verified" => ["text" => "", "icon" => "icon-verified-badge.svg"],
                "verified-purchase" => ["text" => "Verified purchase", "icon" => "icon-verified-purchase.svg"],
                "top-voice" => ["text" => "Top voice", "icon" => "icon-top-voice-badge.svg"]
            ]
        ],
        [
            "avatar" => "RK",
            "name" => "Ravi K",
            "role" => "UX/UI Designer",
            "date" => "Feb 25, 2025",
            "review" => "Good course but expected more practical examples.",
            "pro" => "The course gave a solid overview of Figma's features and tools.",
            "con" => "Lacked more hands-on projects and real-world examples. The pace was a bit slow.",
            "content_quality" => "Decent, but the course could benefit from more real-world application.",
            "instructor_support_exp" => "The instructor was approachable but could improve on offering personalized feedback.",
            "worth_it" => "No, I expected more practical, hands-on examples and case studies to make it worth the money.",
            "rating" => 3,
            "recommend" => "No",
            "refund" => "No refund",
            "outcomes" => [
                "Earned income" => "icon-income.svg",
                "Gained confidence" => "icon-confidence.svg"
            ],
            "badges" => []
        ]
    ];

    // Instructor details
    $instructor = [
        "name"          =>  "Michael Wong",
        "position"      =>  "Founder of Designership & z0 Studio",
        "details"       =>  "Mizko, also known as Michael Wong, brings a 14-year track record as a Founder, Educator, Investor, and Designer. His career evolved from lead designer to freelancer, and ultimately to the owner of a successful agency, generating over $10M in revenue from Product (UX/UI) Design, Web Design, and No-code Development. His leadership at the agency contributed to the strategy and design for over 50 high-growth startups, aiding them in raising a combined total of over $400M+ in venture capital."
    ];

    $instructor_profiles = [
        "linkedin"      =>  "https://example.com",
        "facebook"      =>  "https://example.com",
        "instagram"     =>  "https://example.com",
        "youtube"       =>  "https://example.com"
    ];

    ob_start(); ?>

<div class="single-course-wrapper">
    <!-- Main Section -->
    <div class="main">
        <div class="course-head">
            <span class="tag">The designership</span>
            <h1 class="course-title">
                <?php echo esc_html($course_title); ?>
            </h1>
            <p class="course-author"><em>By
                    <?php echo esc_html($course_author); ?>
                </em></p>
            <div class="rating">
                <span class="r-stars">
                    <?php echo get_rating_stars($course_rating); ?>
                </span>
                <span class="r-text">
                    <?php echo $course_rating; ?>
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
                    <?php foreach ($outcomes as $outcomeText => $iconName): ?>
                    <span class="outcome">
                        <img src="<?= getIcon($iconName) ?>" alt="<?= esc_attr($outcomeText) ?> Icon"
                            class="outcome-icon">
                        <?php echo esc_html($outcomeText); ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="write-review-btn">
                <img src="<?= getIcon('icon-pencil.svg') ?>" alt="Pencil Icon">
                Write your review
            </button>
        </div>

        <div class="tab-container">
            <!-- Tabs -->
            <div class="tabs">
                <button class="tab-button active" data-tab="overview">Course Overview</button>
                <button class="tab-button" data-tab="reviews">Reviews</button>
                <button class="tab-button" data-tab="instructor">About the Instructor</button>
            </div>

            <!-- Tab Contents - Overview -->
            <div class="tab-content overview active" id="overview">
                <h3 class="tab-heading">Course description</h3>
                <?php echo $course_description; ?>
            </div>

            <!-- Tab Contents - Reviews -->
            <div class="tab-content" id="reviews">
                <h3 class="tab-heading">See what reviewers are saying</h3>

                <div class="reviews">
                    <?php foreach ($reviews as $review): ?>
                    <div class="review">
                        <div class="review-head">
                            <div class="col">
                                <div class="avatar">
                                    <?php echo esc_html($review['avatar']); ?>
                                </div>
                            </div>
                            <div class="col">
                                <div class="reviewer">
                                    <span class="reviewer-name">
                                        <?php echo esc_html($review['name']); ?>
                                    </span>
                                    <?php 
                                        // Check if there are badges
                                        if (!empty($review['badges'])):
                                            foreach ($review['badges'] as $badge):
                                    ?>
                                    <span class="reviewer-badge">
                                        <img src="<?= getIcon($badge['icon']) ?>"
                                            alt="<?= esc_attr($badge['text']) ?> Icon" class="badge-icon">
                                        <?php echo esc_html($badge['text']); ?>
                                    </span>
                                    <?php 
                                            endforeach;
                                        endif;
                                    ?>
                                </div>
                                <p class="review-date">
                                    <?php echo esc_html($review['date']); ?>
                                </p>
                            </div>
                        </div>
                        <div class="review-rating">
                            <?php echo get_rating_stars($review['rating']); ?>
                        </div>
                        <div class="review-content">
                            <p class="review-message">
                                <?php echo esc_html($review['review']); ?>
                            </p>
                            <div class="pro-con">
                                <div class="pc-col pro">
                                    <p class="pc-label">
                                        <img src="<?= getIcon('icon-positive.svg') ?>" alt="Positive Icon">
                                        What was good?
                                    </p>
                                    <p class="pc-review">
                                        <?php echo esc_html($review['pro']); ?>
                                    </p>
                                </div>
                                <div class="pc-col con">
                                    <p class="pc-label">
                                        <img src="<?= getIcon('icon-negative.svg') ?>" alt="Negative Icon">
                                        What was bad?
                                    </p>
                                    <p class="pc-review">
                                        <?php echo esc_html($review['con']); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="review-item-list">
                                <p class="review-item">
                                    My Results from this Course
                                    <span class="review-item-value">
                                        <?php echo implode(", ", array_slice($outcomes, 0, 3)); ?>
                                    </span>
                                </p>
                                <p class="review-item">
                                    Content quality:
                                    <span class="review-item-value">
                                        <?php echo esc_html($review['content_quality']); ?>
                                    </span>
                                </p>
                                <p class="review-item">
                                    Instructor & Support Experience:
                                    <span class="review-item-value">
                                        <?php echo esc_html($review['instructor_support_exp']); ?>
                                    </span>
                                </p>
                                <p class="review-item">
                                    Was it worth the money?
                                    <span class="review-item-value">
                                        <?php echo esc_html($review['worth_it']); ?>
                                    </span>
                                </p>
                                <p class="review-item">
                                    Would I recommend this course to others?
                                    <span class="review-item-value">
                                        <?php echo esc_html($review['recommend']); ?>
                                    </span>
                                </p>
                                <p class="review-item">
                                    Refund Experience:
                                    <span class="review-item-value">
                                        <?php echo esc_html($review['refund']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <button class="see-all-reviews">See all reviews</button>
            </div>

            <!-- Tab Contents - Instructor -->
            <div class="tab-content instructor" id="instructor">
                <div class="container">
                    <h3 class="tab-heading">Instructor details</h3>
                    <p class="instructor-name">
                        <?php echo esc_html($instructor['name']); ?>
                    </p>
                    <p class="instructor-position">
                        <?php echo esc_html($instructor['position']); ?>
                    </p>
                    <p class="instructor-details">
                        <?php echo esc_html($instructor['details']); ?>
                    </p>
                    <div class="instructor-profiles">
                        <?php 
                        // Predefined icons for each platform
                        $platform_icons = [
                            'linkedin'  => 'logo-linkedin',
                            'facebook'  => 'logo-facebook',
                            'instagram' => 'logo-instagram',
                            'youtube'   => 'logo-youtube'
                        ];

                        // Loop through each profile and display the corresponding icon
                        foreach ($instructor_profiles as $platform => $url): 
                    ?>
                        <a href="<?php echo esc_url($url); ?>" class="instructor-profile" target="_blank">
                            <ion-icon name="<?php echo esc_attr($platform_icons[$platform]); ?>"></ion-icon>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Section -->
    <div class="sidebar">
        <div class="real-rating">
            <img src="<?= getIcon('icon-real-rating.svg') ?>" alt="Real Rating Icon">
            <p>Courses can't pay to hide or boost reviews. Every opinion here is real.</p>
        </div>
        <div class="course-rating-overall">
            <div class="col">
                <h2 class="cro-rating">
                    <?php echo $course_rating; ?>
                </h2>
                <div class="cro-stars">
                    <?php echo get_rating_stars($course_rating); ?>
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
            <a class="cd-link" href="<?php echo esc_url($course_link); ?>" target="_blank">
                Visit course website <ion-icon name="arrow-forward-outline"></ion-icon>
            </a>
            <p class="cd-price">
                $
                <?php echo number_format($course_price, 2); ?>
            </p>
            <div class="cd-list">
                <div class="cd-list-item">
                    <img src="<?= getIcon('icon-instructor.svg') ?>" alt="Instructor Icon">
                    <p>
                        <span class="cd-list-label">Instructor:</span>
                        <?php echo esc_html($course_author); ?>
                    </p>
                </div>
                <div class="cd-list-item">
                    <img src="<?= getIcon('icon-duration.svg') ?>" alt="Duration Icon">
                    <p>
                        <span class="cd-list-label">Duration:</span>
                        <?php echo $course_duration; ?> hour
                    </p>
                </div>
                <div class="cd-list-item">
                    <img src="<?= getIcon('icon-level.svg') ?>" alt="Level Icon">
                    <p>
                        <span class="cd-list-label">Level:</span>
                        <?php echo implode(", ", $course_level); ?>
                    </p>
                </div>
                <div class="cd-list-item">
                    <img src="<?= getIcon('icon-certificate.svg') ?>" alt="Certificate Icon">
                    <p>
                        <span class="cd-list-label">Certificate:</span>
                        <?php echo esc_html($course_certificate); ?>
                    </p>
                </div>
                <div class="cd-list-item">
                    <img src="<?= getIcon('icon-refundable.svg') ?>" alt="Refund Icon">
                    <p>
                        <span class="cd-list-label">Refund Available:</span>
                        <?php echo esc_html($course_refundable); ?>
                    </p>
                </div>
                <div class="cd-list-item">
                    <img src="<?= getIcon('icon-language.svg') ?>" alt="Language Icon">
                    <p>
                        <span class="cd-list-label">Language:</span>
                        <?php echo implode(", ", $course_language); ?>
                    </p>
                </div>
            </div>
        </div>
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