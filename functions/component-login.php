<?php

/**
 * Shortcode: [custom_login_form]
 * Displays a login form that only allows "reviewer" role users to login
 */
function reviewmvp_custom_login_form() {
    // If already logged in, show message
    if (is_user_logged_in()) {
        return '<p>You are already logged in.</p>';
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_login_nonce'])) {
        if (wp_verify_nonce($_POST['custom_login_nonce'], 'custom_login_action')) {
            $creds = [];
            $creds['user_login']    = sanitize_email($_POST['email']);
            $creds['user_password'] = sanitize_text_field($_POST['password']);
            $creds['remember']      = !empty($_POST['remember']);

            $user = wp_signon($creds, false);

            if (is_wp_error($user)) {
                $error_message = $user->get_error_message();
            } else {
                // Check user role
                if (in_array('reviewer', (array) $user->roles)) {
                    wp_redirect(home_url('/')); // Redirect after successful login
                    exit;
                } else {
                    wp_logout();
                    $error_message = 'You are not allowed to login here.';
                }
            }
        }
    }

    ob_start(); ?>

<div class="custom-login-form">
    <!-- Social login (static buttons for now) -->
    <button type="button" class="social-btn linkedin-btn">
        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/linkedin.svg" alt="LinkedIn" width="20">
        Continue with LinkedIn
    </button>

    <button type="button" class="social-btn google-btn">
        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/google.svg" alt="Google" width="20">
        Continue with Google
    </button>

    <p class="divider">Or, sign in with email</p>

    <?php if (!empty($error_message)) : ?>
    <div class="login-error" style="color:red; margin-bottom:10px;">
        <?php echo $error_message; ?>
    </div>
    <?php endif; ?>

    <!-- Email login form -->
    <form method="post" action="">
        <?php wp_nonce_field('custom_login_action', 'custom_login_nonce'); ?>

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

        <button type="submit" class="btn-submit">Login</button>
    </form>

    <div class="form-links">
        <a href="<?php echo site_url('/sign-up/'); ?>">Don’t have an account?</a>
        <a href="<?php echo site_url('/forget-password/'); ?>" style="float:right;">Forgot password?</a>
    </div>
</div>

<style>
.custom-login-form {
    max-width: 400px;
    margin: 0 auto;
    text-align: center;
    font-family: Arial, sans-serif;
}

.social-btn {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 6px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 16px;
    cursor: pointer;
}

.divider {
    margin: 20px 0;
    font-size: 14px;
    color: #666;
}

.form-group {
    margin-bottom: 15px;
    text-align: left;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
}

.form-group input[type="email"],
.form-group input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

.remember-me {
    display: flex;
    align-items: center;
}

.remember-me label {
    margin-left: 8px;
    font-size: 14px;
}

.btn-submit {
    width: 100%;
    padding: 12px;
    background: #00e28c;
    border: none;
    border-radius: 6px;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
}

.form-links {
    margin-top: 20px;
    font-size: 14px;
}

.form-links a {
    color: #111;
    text-decoration: none;
}

.form-links a:hover {
    text-decoration: underline;
}
</style>

<?php
    return ob_get_clean();
}
add_shortcode('custom_login_form', 'reviewmvp_custom_login_form');