<?php

/**
 * Handle registration safely before page render
 */
add_action('init', function() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_register_nonce'])) {
        if (wp_verify_nonce($_POST['custom_register_nonce'], 'custom_register_action')) {
            $name     = sanitize_text_field($_POST['name']);
            $email    = sanitize_email($_POST['email']);
            $password = sanitize_text_field($_POST['password']);
            $remember = !empty($_POST['remember']);

            if (empty($name) || empty($email) || empty($password)) {
                set_transient('custom_register_error', 'All fields are required.', 30);
                return;
            }
            if (email_exists($email)) {
                set_transient('custom_register_error', 'Email already registered. Please login.', 30);
                return;
            }

            $username = sanitize_user(current(explode('@', $email)), true);
            if (username_exists($username)) {
                $username .= '_' . wp_generate_password(4, false);
            }

            $user_id = wp_create_user($username, $password, $email);
            if (!is_wp_error($user_id)) {
                $user = new WP_User($user_id);
                $user->set_role('reviewer');

                wp_update_user([
                    'ID' => $user_id,
                    'display_name' => $name
                ]);

                // Auto-login
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id, $remember);

                // Redirect to thank you or review page
                wp_redirect(site_url('/write-a-review/')); 
                exit;
            } else {
                set_transient('custom_register_error', 'Registration failed. Try again.', 30);
                return;
            }
        }
    }
});


/**
 * Shortcode: [custom_register_form]
 */
function reviewmvp_custom_register_form() {
    if (is_user_logged_in()) {
        return '<p style="text-align:center;">You are already registered and logged in.</p>';
    }

    // Pull error message if any
    $message = get_transient('custom_register_error');
    if ($message) {
        delete_transient('custom_register_error');
        $message = '<p style="color:crimson;">' . esc_html($message) . '</p>';
    }

    ob_start(); ?>

<div id="auth" class="auth-container">
    <div class="custom-register-form">
        <p class="verified-note">
            <img src="<?= get_theme_media_url('icon-verified-badge.svg') ?>" alt="verified badge">
            Get verified badge instantly with LinkedIn login
        </p>

        <div class="social-btn-group">
            <button type="button" class="social-btn linkedin-btn">
                <ion-icon name="logo-linkedin"></ion-icon>
                Continue with LinkedIn
            </button>

            <button type="button" class="social-btn google-btn">
                <ion-icon name="logo-google"></ion-icon>
                Continue with Google
            </button>
        </div>

        <p class="divider-text">Or, sign up with email</p>

        <?php if (!empty($message)) echo $message; ?>

        <form method="post">
            <?php wp_nonce_field('custom_register_action', 'custom_register_nonce'); ?>

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="form-group remember-me">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn-submit">Sign up</button>
        </form>

        <div class="divider">
            <hr>
            <p>Already a member?</p>
            <hr>
        </div>

        <p class="suggest-text">
            <a href="<?php echo site_url('/login/'); ?>">Sign in</a> in using your Spill the course
            account.
        </p>

        <p class="legal-text">
            By proceeding, you agree to our <a href="#">Terms of Use</a> and confirm you have read our <a
                href="#">Privacy and Cookie Statement</a>.
        </p>
    </div>
</div>

<?php
    return ob_get_clean();
}
add_shortcode('custom_register_form', 'reviewmvp_custom_register_form');