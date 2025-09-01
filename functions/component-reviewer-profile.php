<?php
/**
 * Shortcode: [reviewer_profile_form]
 * Reviewer profile edit form with dashboard sidebar
 */
function reviewmvp_reviewer_profile_form() {
    if (!is_user_logged_in()) {
        wp_redirect(site_url('/login/'));
        exit;
    }

    $user = wp_get_current_user();

    // Allow only reviewers
    if (!in_array('reviewer', (array) $user->roles)) {
        return '<p style="color:crimson; text-align:center;">You do not have access to this page.</p>';
    }

    $message = '';

    // Handle form submit
    if (isset($_POST['reviewer_profile_nonce']) &&
        wp_verify_nonce($_POST['reviewer_profile_nonce'], 'reviewer_profile_update')) {

        $errors = [];

        // Update display name
        $display_name = sanitize_text_field($_POST['display_name'] ?? '');
        if (!empty($display_name)) {
            wp_update_user([
                'ID' => $user->ID,
                'display_name' => $display_name,
            ]);
        }

        // Change password
        $pass1 = $_POST['password'] ?? '';
        $pass2 = $_POST['password_confirm'] ?? '';
        if (!empty($pass1) || !empty($pass2)) {
            if ($pass1 !== $pass2) {
                $errors[] = "Passwords do not match.";
            } elseif (strlen($pass1) < 6) {
                $errors[] = "Password must be at least 6 characters.";
            } else {
                wp_set_password($pass1, $user->ID);
                // Re-login after password change
                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID);
            }
        }

        if (empty($errors)) {
            $message = '<p style="color:green;">Profile updated successfully <ion-icon name="checkmark-circle-outline"></ion-icon></p>';
        } else {
            $message = '<p style="color:crimson;">'.implode('<br>', $errors).'</p>';
        }
    }

    ob_start(); ?>
<div id="reviewerPortal" class="portal-layout reviewer-dashboard">
    <aside class="sidebar">
        <ul>
            <li><a href="<?php echo site_url('/reviewer-dashboard/'); ?>">Dashboard</a></li>
            <li class="active"><a href="<?php echo site_url('/reviewer-profile/'); ?>">Profile</a></li>
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

        <h2 class="heading">
            <ion-icon name="person-outline"></ion-icon>
            My Profile
        </h2>
        <?php echo $message; ?>
        <form method="post" class="profile-form">
            <?php wp_nonce_field('reviewer_profile_update', 'reviewer_profile_nonce'); ?>
            <div class="form-group">
                <label>Username</label>
                <input type="text" value="<?php echo esc_attr($user->user_login); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?php echo esc_attr($user->user_email); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="display_name">Display Name</label>
                <input type="text" name="display_name" id="display_name"
                    value="<?php echo esc_attr($user->display_name); ?>">
            </div>
            <hr>
            <h3>Change Password</h3>
            <div class="form-inline">
                <input type="password" name="password" placeholder="New password">
                <input type="password" name="password_confirm" placeholder="Confirm password">
            </div>
            <button type="submit" class="btn">Update Profile</button>
        </form>
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
add_shortcode('reviewer_profile_form', 'reviewmvp_reviewer_profile_form');