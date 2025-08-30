<?php
/**
 * Shortcode: [reviewer_dashboard]
 * Minimal dashboard for reviewer role
 */
function reviewmvp_reviewer_dashboard() {
    if (!is_user_logged_in()) {
        // Redirect non-logged users to login
        wp_redirect(site_url('/login/'));
        exit;
    }

    $user = wp_get_current_user();

    // Allow only reviewers
    if (!in_array('reviewer', (array) $user->roles)) {
        return '<p style="color:crimson; text-align:center;">You do not have access to this dashboard.</p>';
    }

    ob_start(); ?>

<div id="reviewerPortal" class="portal-layout reviewer-dashboard">
    <aside class="sidebar">
        <ul>
            <li><a href="<?php echo site_url('/reviewer-dashboard/'); ?>">Dashboard</a></li>
            <li><a href="<?php echo site_url('/reviewer-dashboard/profile/'); ?>">Profile</a></li>
            <li><a href="<?php echo site_url('/reviewer-dashboard/leaderboard/'); ?>">Leaderboard</a></li>
        </ul>
        <ul>
            <li><a href="<?php echo wp_logout_url(site_url('/login/')); ?>" class="logout">Logout</a></li>
        </ul>
    </aside>
    <main class="content">
        <h2>Welcome, <?php echo esc_html($user->display_name); ?> 👋</h2>
        <p>This is your dashboard.</p>
    </main>
</div>

<?php
    return ob_get_clean();
}
add_shortcode('reviewer_dashboard', 'reviewmvp_reviewer_dashboard');