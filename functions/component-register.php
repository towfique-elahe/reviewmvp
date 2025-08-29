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
        return '<p>You are already registered and logged in.</p>';
    }

    // Pull error message if any
    $message = get_transient('custom_register_error');
    if ($message) {
        delete_transient('custom_register_error');
        $message = '<p style="color:red;">' . esc_html($message) . '</p>';
    }

    ob_start(); ?>

<div class="custom-register-form">
    <h2>Unlock Trusted Reviews & Share Your Voice</h2>
    <p>Write your first review or just explore thousands of honest insights.</p>

    <p class="verified-note">
        <ion-icon name="checkmark-circle-outline"></ion-icon>
        Get verified badge instantly with LinkedIn login
    </p>

    <!-- Social signup buttons -->
    <button type="button" class="social-btn linkedin-btn">
        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/linkedin.svg" width="20">
        Continue with LinkedIn
    </button>

    <button type="button" class="social-btn google-btn">
        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/google.svg" width="20">
        Continue with Google
    </button>

    <p class="divider">Or, sign up with email</p>

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

    <div class="form-footer">
        <hr>
        <p>Already a member? <a href="<?php echo site_url('/login/'); ?>"><strong>Sign in</strong></a></p>
    </div>

    <p class="legal-text">
        By proceeding, you agree to our <a href="/terms-of-use">Terms of Use</a> and confirm you have read our <a
            href="/privacy-policy">Privacy and Cookie Statement</a>.
    </p>
</div>

<style>
.custom-register-form {
    max-width: 400px;
    margin: 0 auto;
    text-align: center;
    font-family: Arial, sans-serif;
}

.custom-register-form h2 {
    font-size: 22px;
    margin-bottom: 10px;
}

.custom-register-form p {
    margin: 8px 0;
    color: #444;
}

.verified-note {
    font-size: 14px;
    color: green;
    margin: 10px 0;
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

.form-group input {
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

.form-footer {
    margin-top: 20px;
    font-size: 14px;
}

.legal-text {
    margin-top: 20px;
    font-size: 12px;
    color: #666;
}

.legal-text a {
    color: #111;
}
</style>

<?php
    return ob_get_clean();
}
add_shortcode('custom_register_form', 'reviewmvp_custom_register_form');