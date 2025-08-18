<?php
/**
 * Template for displaying a single Course
 */

get_header(); 

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

global $post; 
$course_id = $post->ID;

// -------- DEMO DATA (replace with real course data later) ---------
    // Generate demo data for the course
    $seed = 42; // Initialize seed for randomization
    function rnd() {
        global $seed;
        $seed = ($seed * 1664525 + 1013904223) % 4294967296;
        return $seed / 4294967296;
    }
    $title = get_the_title();
    $rating = round(3 + rnd() * 2, 1); // Random rating between 3 and 5
    $reviews_count = 10 + floor(rnd() * 200); // Random reviews count between 10 and 200
    $price = get_post_meta( get_the_ID(), '_course_price', true );
    $duration = get_post_meta( get_the_ID(), '_course_duration', true );
    $certificate = get_post_meta( get_the_ID(), '_course_certificate', true );
    $refundable = get_post_meta( get_the_ID(), '_course_refundable', true );
    $link = get_post_meta( get_the_ID(), '_course_link', true );
    $level = get_post_meta(get_the_ID(), '_course_level', true);
    $level_label = [
        'beginner'     => 'Beginner',
        'intermediate' => 'Intermediate',
        'advance'      => 'Advance',
    ];
    $languages = (array) get_post_meta( get_the_ID(), '_course_language', true );
    $instructor  = get_post_meta( get_the_ID(), '_course_instructor', true );
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
    $course_description = apply_filters( 'the_content', get_the_content() );

// Reviews
$args = [
    'post_type'      => 'course_review',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'meta_query'     => [
        [
            'key'   => '_review_course',
            'value' => $course_id,
        ]
    ]
];

$reviews = get_posts($args);

    // Reviews (extended sample data)
    $allReviews = [
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
            "proof" => "sample-certificate.png",
            "outcomes" => [
                "Earned income" => "icon-income.svg",
                "Gained confidence" => "icon-confidence.svg"
            ],
            "badges" => [
                "verified" => ["text" => "", "icon" => "icon-verified-badge.svg"],
                "rising-voice" => ["text" => "Rising Voice", "icon" => "icon-rising-voice-badge.svg"]
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
            "proof" => "sample-certificate.png",
            "outcomes" => [
                "Earned income" => "icon-income.svg",
                "Gained confidence" => "icon-confidence.svg"
            ],
            "badges" => [
                "verified" => ["text" => "", "icon" => "icon-verified-badge.svg"],
                "verified-purchase" => ["text" => "Verified Purchase", "icon" => "icon-verified-purchase.svg"],
                "top-voice" => ["text" => "Top Voice", "icon" => "icon-top-voice-badge.svg"]
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
            "proof" => "",
            "outcomes" => [
                "Earned income" => "icon-income.svg",
                "Gained confidence" => "icon-confidence.svg"
            ],
            "badges" => []
        ],
        [
            "avatar" => "AC",
            "name" => "Anna C",
            "role" => "Product Manager",
            "date" => "Mar 5, 2025",
            "review" => "Loved the course content, especially the prototyping module!",
            "pro" => "Very practical exercises and clear explanations.",
            "con" => "Some videos had low audio quality.",
            "content_quality" => "Strong and easy to follow, but minor technical issues.",
            "instructor_support_exp" => "Quick responses on the forum.",
            "worth_it" => "Yes, totally worth the investment.",
            "rating" => 5,
            "recommend" => "Yes",
            "refund" => "No refund",
            "proof" => "sample-certificate.png",
            "outcomes" => [
                "Improved skill (40%)" => "icon-improved-skill.svg",
                "Gained confidence" => "icon-confidence.svg"
            ],
            "badges" => [
                "verified" => ["text" => "", "icon" => "icon-verified-badge.svg"]
            ]
        ],
        [
            "avatar" => "TB",
            "name" => "Tom B",
            "role" => "Freelance Designer",
            "date" => "Mar 6, 2025",
            "review" => "Good course, but not much new for experienced designers.",
            "pro" => "Great for beginners and intermediate learners.",
            "con" => "Could use more advanced tips and tricks.",
            "content_quality" => "Well-made, clear, but not deep enough.",
            "instructor_support_exp" => "Helpful but limited availability.",
            "worth_it" => "Maybe, depending on your skill level.",
            "rating" => 4,
            "recommend" => "Yes",
            "refund" => "No refund",
            "proof" => "",
            "outcomes" => [
                "Built project (30%)" => "icon-built-project.svg"
            ],
            "badges" => []
        ],
        [
            "avatar" => "SK",
            "name" => "Sara K",
            "role" => "Junior UX Designer",
            "date" => "Mar 8, 2025",
            "review" => "This course gave me the confidence to apply for my first UX/UI role!",
            "pro" => "Motivating and easy to understand.",
            "con" => "Could use more real-world case studies.",
            "content_quality" => "Engaging and practical.",
            "instructor_support_exp" => "Very encouraging feedback.",
            "worth_it" => "Yes, helped me land interviews.",
            "rating" => 5,
            "recommend" => "Yes",
            "refund" => "No refund",
            "proof" => "sample-certificate.png",
            "outcomes" => [
                "Career boost" => "icon-career.svg",
                "Earned income" => "icon-income.svg"
            ],
            "badges" => [
                "verified" => ["text" => "", "icon" => "icon-verified-badge.svg"],
                "top-voice" => ["text" => "Top Voice", "icon" => "icon-top-voice-badge.svg"]
            ]
        ],
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
            "proof" => "sample-certificate.png",
            "outcomes" => [
                "Earned income" => "icon-income.svg",
                "Gained confidence" => "icon-confidence.svg"
            ],
            "badges" => [
                "verified" => ["text" => "", "icon" => "icon-verified-badge.svg"],
                "rising-voice" => ["text" => "Rising Voice", "icon" => "icon-rising-voice-badge.svg"]
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
            "proof" => "sample-certificate.png",
            "outcomes" => [
                "Earned income" => "icon-income.svg",
                "Gained confidence" => "icon-confidence.svg"
            ],
            "badges" => [
                "verified" => ["text" => "", "icon" => "icon-verified-badge.svg"],
                "verified-purchase" => ["text" => "Verified Purchase", "icon" => "icon-verified-purchase.svg"],
                "top-voice" => ["text" => "Top Voice", "icon" => "icon-top-voice-badge.svg"]
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
            "proof" => "",
            "outcomes" => [
                "Earned income" => "icon-income.svg",
                "Gained confidence" => "icon-confidence.svg"
            ],
            "badges" => []
        ],
        [
            "avatar" => "AC",
            "name" => "Anna C",
            "role" => "Product Manager",
            "date" => "Mar 5, 2025",
            "review" => "Loved the course content, especially the prototyping module!",
            "pro" => "Very practical exercises and clear explanations.",
            "con" => "Some videos had low audio quality.",
            "content_quality" => "Strong and easy to follow, but minor technical issues.",
            "instructor_support_exp" => "Quick responses on the forum.",
            "worth_it" => "Yes, totally worth the investment.",
            "rating" => 5,
            "recommend" => "Yes",
            "refund" => "No refund",
            "proof" => "sample-certificate.png",
            "outcomes" => [
                "Improved skill (40%)" => "icon-improved-skill.svg",
                "Gained confidence" => "icon-confidence.svg"
            ],
            "badges" => [
                "verified" => ["text" => "", "icon" => "icon-verified-badge.svg"]
            ]
        ],
        [
            "avatar" => "TB",
            "name" => "Tom B",
            "role" => "Freelance Designer",
            "date" => "Mar 6, 2025",
            "review" => "Good course, but not much new for experienced designers.",
            "pro" => "Great for beginners and intermediate learners.",
            "con" => "Could use more advanced tips and tricks.",
            "content_quality" => "Well-made, clear, but not deep enough.",
            "instructor_support_exp" => "Helpful but limited availability.",
            "worth_it" => "Maybe, depending on your skill level.",
            "rating" => 4,
            "recommend" => "Yes",
            "refund" => "No refund",
            "proof" => "",
            "outcomes" => [
                "Built project (30%)" => "icon-built-project.svg"
            ],
            "badges" => []
        ],
        [
            "avatar" => "SK",
            "name" => "Sara K",
            "role" => "Junior UX Designer",
            "date" => "Mar 8, 2025",
            "review" => "This course gave me the confidence to apply for my first UX/UI role!",
            "pro" => "Motivating and easy to understand.",
            "con" => "Could use more real-world case studies.",
            "content_quality" => "Engaging and practical.",
            "instructor_support_exp" => "Very encouraging feedback.",
            "worth_it" => "Yes, helped me land interviews.",
            "rating" => 5,
            "recommend" => "Yes",
            "refund" => "No refund",
            "proof" => "sample-certificate.png",
            "outcomes" => [
                "Career boost" => "icon-career.svg",
                "Earned income" => "icon-income.svg"
            ],
            "badges" => [
                "verified" => ["text" => "", "icon" => "icon-verified-badge.svg"],
                "top-voice" => ["text" => "Top Voice", "icon" => "icon-top-voice-badge.svg"]
            ]
        ]
    ];
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
                            <?php echo get_rating_stars($rating); ?>
                        </span>
                        <span class="r-text">
                            <?php echo $rating; ?>
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
                            <?php echo $rating; ?>
                        </h2>
                        <div class="cro-stars">
                            <?php echo get_rating_stars($rating); ?>
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

                // Meta fields
                $date        = get_post_meta($review_id, '_review_date', true);
                $rating      = get_post_meta($review_id, '_review_rating', true);
                $message     = get_post_meta($review_id, '_review_message', true);
                $good        = get_post_meta($review_id, '_review_good', true);
                $bad         = get_post_meta($review_id, '_review_bad', true);
                $outcome     = get_post_meta($review_id, '_review_outcome', true);
                $quality     = get_post_meta($review_id, '_review_quality', true);
                $support     = get_post_meta($review_id, '_review_support', true);
                $worth       = get_post_meta($review_id, '_review_worth', true);
                $recommend   = get_post_meta($review_id, '_review_recommend', true);
                $refund      = get_post_meta($review_id, '_review_refund', true);
                $proof       = get_post_meta($review_id, '_review_proof', true);
                $statuses    = (array) get_post_meta($review_id, '_review_status', true);
        ?>
                <div class="review">
                    <div class="review-head">
                        <div class="col">
                            <div class="avatar">
                                <?php echo $user ? esc_html(substr($user->display_name,0,2)) : "U"; ?>
                            </div>
                        </div>
                        <div class="col">
                            <div class="reviewer">
                                <span class="reviewer-name">
                                    <?php echo esc_html($reviewer); ?>
                                </span>
                                <?php if ($statuses): ?>
                                <?php foreach ($statuses as $status): ?>
                                <span class="reviewer-badge <?php echo esc_attr($status); ?>">
                                    <?php echo esc_html(ucwords(str_replace('_',' ', $status))); ?>
                                </span>
                                <?php endforeach; ?>
                                <?php endif; ?>
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
                            <?php if ($outcome): ?>
                            <p class="review-item">
                                <span class="review-label">My Results from this Course</span>
                                <span class="review-item-value"><?php echo esc_html($outcome); ?></span>
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
                            <?php echo $rating; ?>
                        </h2>
                        <div class="cro-stars">
                            <?php echo get_rating_stars($rating); ?>
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
                    $reviewIndex = 0;
                    foreach ($allReviews as $review): 
                        $hiddenClass = $reviewIndex >= 3 ? 'hidden-review' : '';
                    ?>
                    <div class="review <?= $hiddenClass ?>">
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
                                        <img src="<?= getMedia('icon-positive.svg') ?>" alt="Positive Icon">
                                        <span class="review-label">What was good?</span>
                                    </p>
                                    <p class="pc-review">
                                        <?php echo esc_html($review['pro']); ?>
                                    </p>
                                </div>
                                <div class="pc-col con">
                                    <p class="pc-label">
                                        <img src="<?= getMedia('icon-negative.svg') ?>" alt="Negative Icon">
                                        <span class="review-label">What was bad?</span>
                                    </p>
                                    <p class="pc-review">
                                        <?php echo esc_html($review['con']); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="review-item-list">
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
                                <p class="review-item">
                                    <span class="review-label">Content quality:</span>
                                    <span class="review-item-value">
                                        <?php echo esc_html($review['content_quality']); ?>
                                    </span>
                                </p>
                                <p class="review-item">
                                    <span class="review-label">Instructor & Support Experience:</span>
                                    <span class="review-item-value">
                                        <?php echo esc_html($review['instructor_support_exp']); ?>
                                    </span>
                                </p>
                                <p class="review-item">
                                    <span class="review-label">Was it worth the money?</span>
                                    <span class="review-item-value">
                                        <?php echo esc_html($review['worth_it']); ?>
                                    </span>
                                </p>
                                <p class="review-item">
                                    <span class="review-label">Would I recommend this course to others?</span>
                                    <span class="review-item-value">
                                        <?php echo esc_html($review['recommend']); ?>
                                    </span>
                                </p>
                                <p class="review-item">
                                    <span class="review-label">Refund Experience:</span>
                                    <span class="review-item-value">
                                        <?php echo esc_html($review['refund']); ?>
                                    </span>
                                </p>
                                <?php
                            if (!empty($review['proof'])) {
                        ?>
                                <img src="<?= getMedia($review['proof']) ?>" alt="Proof of enrollment" class="proof">
                                <?php
                        }
                        ?>
                            </div>
                        </div>
                    </div>
                    <?php 
                        $reviewIndex++;
                    endforeach; 
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