<?php
/**
 * Shortcode: [reviewer_dashboard]
 * Reviewer dashboard with stats
 */
function reviewmvp_reviewer_dashboard() {
    if (!is_user_logged_in()) {
        wp_redirect(site_url('/login/'));
        exit;
    }

    $user = wp_get_current_user();

    // Allow only reviewers
    if (!in_array('reviewer', (array) $user->roles)) {
        return '<p style="color:crimson; text-align:center;">You do not have access to this dashboard.</p>';
    }

    // --- Stats ---
    // 1. Count reviews authored by this reviewer
    $review_count = new WP_Query([
        'post_type'      => 'course_review',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => [
            [
                'key'   => '_reviewer',
                'value' => $user->ID,
            ]
        ]
    ]);
    $total_reviews = $review_count->found_posts;

    // 2. Count courses suggested by this reviewer
    $course_count = new WP_Query([
        'post_type'      => 'course',
        'post_status'    => ['publish','pending','draft'],
        'posts_per_page' => -1,
        'meta_query'     => [
            [
                'key'   => '_course_reviewer',
                'value' => $user->ID,
            ]
        ]
    ]);
    $total_courses = $course_count->found_posts;

    ob_start(); ?>
<div id="reviewerPortal" class="portal-layout reviewer-dashboard">
    <aside class="sidebar">
        <ul>
            <li class="active"><a href="<?php echo site_url('/reviewer-dashboard/'); ?>">Dashboard</a></li>
            <li><a href="<?php echo site_url('/reviewer-profile/'); ?>">Profile</a></li>
            <li><a href="<?php echo site_url('/reviewer-leaderboard/'); ?>">Leaderboard</a></li>
        </ul>
        <ul>
            <li><a href="<?php echo wp_logout_url(site_url('/login/')); ?>" class="logout">Logout</a></li>
        </ul>
    </aside>
    <main class="content">
        <div class="menu-toggle" onclick="toggleSidebar()">
            <ion-icon name="menu-outline"></ion-icon> Menu
        </div>
        <div class="overlay" onclick="toggleSidebar()"></div>

        <h2 class="heading">Welcome, <?php echo esc_html($user->display_name); ?> 👋</h2>
        <div class="dashboard-cards">
            <div class="card">
                <ion-icon name="chatbubbles-outline"></ion-icon>
                <h3><?php echo intval($total_reviews); ?></h3>
                <p>Reviews Given</p>
            </div>
            <div class="card">
                <ion-icon name="school-outline"></ion-icon>
                <h3><?php echo intval($total_courses); ?></h3>
                <p>Courses Suggested</p>
            </div>
        </div>
    </main>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.querySelector('#reviewerPortal .sidebar');
    const overlay = document.querySelector('#reviewerPortal .overlay');
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
}
</script>

<?php
    return ob_get_clean();
}
add_shortcode('reviewer_dashboard', 'reviewmvp_reviewer_dashboard');