<?php

// Handle login submission before headers are sent
add_action('template_redirect', function() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_login_nonce'])) {
        if (wp_verify_nonce($_POST['custom_login_nonce'], 'custom_login_action')) {
            $creds = [];
            $creds['user_login']    = sanitize_email($_POST['email']);
            $creds['user_password'] = sanitize_text_field($_POST['password']);
            $creds['remember']      = !empty($_POST['remember']);

            $user = wp_signon($creds, false);

            if (!is_wp_error($user)) {
                if (in_array('reviewer', (array) $user->roles)) {
                    wp_redirect(site_url('/reviewer-dashboard/'));
                    exit;
                } else {
                    wp_logout();
                    set_transient('custom_login_error', 'You are not allowed to login here.', 30);
                    wp_redirect(site_url('/login/'));
                    exit;
                }
            } else {
                set_transient('custom_login_error', $user->get_error_message(), 30);
                wp_redirect(site_url('/login/'));
                exit;
            }
        }
    }
});

/**
 * Shortcode: [custom_login_form]
 * Displays a login form that only allows "reviewer" role users to login
 */
function reviewmvp_custom_login_form() {
    // If already logged in, show message
    if (is_user_logged_in()) {
        return '<p style="text-align:center;">You are already logged in.</p>';
    }

    ob_start(); ?>
<div id="auth" class="auth-container">
    <div class="custom-login-form">

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

        <p class="divider-text">Or, sign in with email</p>

        <?php
        $message = get_transient('custom_login_error');
        if ($message) {
            delete_transient('custom_login_error');
            echo '<div class="login-error" style="color:crimson; text-align:center; margin-bottom:10px;">' . esc_html($message) . '</div>';
        }
    ?>

        <!-- Email login form -->
        <form method="post" action="">
            <?php wp_nonce_field('custom_login_action', 'custom_login_nonce'); ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group password-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        <ion-icon id="passwordToggleIcon" name="eye-outline"></ion-icon>
                    </span>
                </div>
            </div>

            <div class="form-group remember-me">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn-submit">Login</button>
        </form>

        <div class="divider">
            <hr>
        </div>

        <div class="form-links">
            <a href="<?php echo site_url('/sign-up/'); ?>">Don’t have an account?</a>
            <a href="<?php echo site_url('/forget-password/'); ?>" style="float:right;">Forgot password?</a>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById("password");
    const icon = document.getElementById("passwordToggleIcon");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.setAttribute("name", "eye-off-outline");
    } else {
        passwordInput.type = "password";
        icon.setAttribute("name", "eye-outline");
    }
}
</script>

<?php
    return ob_get_clean();
}
add_shortcode('custom_login_form', 'reviewmvp_custom_login_form');