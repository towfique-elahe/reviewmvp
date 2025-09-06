<?php

function reviewmvp_reviewer_leaderboard() {
    if (!is_user_logged_in()) {
        wp_redirect(site_url('/login/'));
        exit;
    }

    $current_user = wp_get_current_user();

    if (!in_array('reviewer', (array) $current_user->roles)) {
        return '<p style="color:crimson; text-align:center;">You do not have access to this page.</p>';
    }

    $reviewers = get_users(['role' => 'reviewer']);

    $leaderboard = [];

    foreach ($reviewers as $user) {
        $reviews = new WP_Query([
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
        $review_count = $reviews->found_posts;

        $courses = new WP_Query([
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
        $course_count = $courses->found_posts;

        $score = $review_count + $course_count;

        $leaderboard[] = [
            'id'       => $user->ID,
            'name'     => $user->display_name,
            'reviews'  => $review_count,
            'courses'  => $course_count,
            'score'    => $score,
        ];
    }

    usort($leaderboard, function($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    ob_start(); ?>
<div id="reviewerPortal" class="portal-layout reviewer-leaderboard">
    <aside class="sidebar">
        <ul>
            <li><a href="<?php echo site_url('/reviewer-dashboard/'); ?>">Dashboard</a></li>
            <li><a href="<?php echo site_url('/reviewer-profile/'); ?>">Profile</a></li>
            <li class="active"><a href="<?php echo site_url('/reviewer-leaderboard/'); ?>">Leaderboard</a></li>
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

        <h2 class="heading">
            <ion-icon name="medal-outline"></ion-icon>
            Reviewer Leaderboard
        </h2>
        <div class="table-responsive">
            <table class="leaderboard-table">

                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Reviewer</th>
                        <th>Reviews</th>
                        <th>Courses</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $rank = 1;
                        foreach ($leaderboard as $row): 
                            $is_current = ($row['id'] == $current_user->ID);
                    ?>
                    <tr class="<?php echo $is_current ? 'me' : ''; ?>">
                        <td><?php echo $rank; ?></td>
                        <td class="reviewer-cell">
                            <?php echo esc_html($row['name']); ?>
                            <?php echo $is_current ? '<ion-icon name="caret-back-outline"></ion-icon>' : ''; ?>
                        </td>
                        <td><?php echo intval($row['reviews']); ?></td>
                        <td><?php echo intval($row['courses']); ?></td>
                        <td><?php echo intval($row['score']); ?></td>
                    </tr>
                    <?php $rank++; endforeach; ?>
                </tbody>
            </table>
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
add_shortcode('reviewer_leaderboard', 'reviewmvp_reviewer_leaderboard');