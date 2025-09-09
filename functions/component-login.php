<?php

add_action('template_redirect', function() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_login_nonce'])) {
        if (wp_verify_nonce($_POST['custom_login_nonce'], 'custom_login_action')) {
            $creds = [];
            $creds['user_login']    = sanitize_email($_POST['email']);
            $creds['user_password'] = sanitize_text_field($_POST['password']);
            $creds['remember']      = !empty($_POST['remember']);

            $user = wp_signon($creds, false);

            if (!is_wp_error($user)) {
                if (in_array('reviewer', (array) $user->roles, true)) {
                    wp_safe_redirect(site_url('/reviewer-dashboard/'));
                    exit;
                } else {
                    wp_logout();
                    set_transient('custom_login_error', 'You are not allowed to login here.', 30);
                    wp_safe_redirect(site_url('/login/'));
                    exit;
                }
            } else {
                // Build a custom error for wrong password, and fix the reset link
                $lost_url = site_url('/forget-password/');
                $message  = '';

                if (in_array('incorrect_password', (array) $user->get_error_codes(), true)) {
                    $email   = sanitize_email($_POST['email']);
                    $message = sprintf(
                        'Error: The password you entered for the email address %s is incorrect. <a href="%s">Lost your password?</a>',
                        esc_html($email),
                        esc_url($lost_url)
                    );
                } else {
                    // Fall back to WP's message but replace the default lost-password link if present
                    $message = $user->get_error_message();
                    $message = preg_replace(
                        '#href=[\'"][^\'"]*wp-login\.php\?action=lostpassword[\'"]#i',
                        'href="' . esc_url($lost_url) . '"',
                        $message
                    );
                }

                set_transient('custom_login_error', $message, 30);
                wp_safe_redirect(site_url('/login/'));
                exit;
            }
        }
    }
});

function reviewmvp_custom_login_form() {
    if (is_user_logged_in()) {
        return '<p style="text-align:center;">You are already logged in.</p>';
    }

    ob_start(); ?>
<div id="auth" class="auth-container">
    <div class="custom-login-form">

        <div class="social-btn-group">
            <button type="button" class="social-btn linkedin-btn" onclick="linkedinLogin()">
                <ion-icon name="logo-linkedin"></ion-icon>
                Continue with LinkedIn
            </button>

            <button type="button" class="social-btn google-btn" onclick="googleLogin()">
                <ion-icon name="logo-google"></ion-icon>
                Continue with Google
            </button>
        </div>

        <p class="divider-text">Or, sign in with email</p>

        <?php
            $message = get_transient('custom_login_error');
            if ($message) {
                delete_transient('custom_login_error');
                echo '<div class="login-error" style="color:crimson; text-align:center; margin-bottom:10px;">' . wp_kses_post($message) . '</div>';
            }
        ?>

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
                <label class="bc-check" for="remember">
                    <input type="checkbox" name="remember" id="remember" class="cb-input">
                    <span class="cb-box">
                        <!-- checkmark icon -->
                        <svg class="cb-icon" viewBox="0 0 16 16" fill="none">
                            <path d="M4 8.5L7 11.5L12 5.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="cb-label">Remember me</span>
                </label>
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

function linkedinLogin() {
    let clientId = "<?php echo esc_js(get_option('linkedin_client_id')); ?>";
    let redirectUri = "<?php echo site_url('/linkedin-login-callback/'); ?>";
    let state = Math.random().toString(36).substring(2);

    let oauthUrl = "https://www.linkedin.com/oauth/v2/authorization?response_type=code" +
        "&client_id=" + clientId +
        "&redirect_uri=" + encodeURIComponent(redirectUri) +
        "&scope=openid%20profile%20email" +
        "&state=" + state;

    window.location.href = oauthUrl;
}

function googleLogin() {
    let clientId = "<?php echo esc_js(get_option('google_client_id')); ?>";
    let redirectUri = "<?php echo site_url('/google-login-callback/'); ?>";
    let state = Math.random().toString(36).substring(2);

    let oauthUrl = "https://accounts.google.com/o/oauth2/v2/auth" +
        "?response_type=code" +
        "&client_id=" + clientId +
        "&redirect_uri=" + encodeURIComponent(redirectUri) +
        "&scope=openid%20profile%20email" +
        "&state=" + state;

    window.location.href = oauthUrl;
}
</script>

<?php
    return ob_get_clean();
}
add_shortcode('custom_login_form', 'reviewmvp_custom_login_form');

add_action('init', function() {
    if (
        isset($_GET['code']) && isset($_GET['state']) &&
        strpos($_SERVER['REQUEST_URI'], 'linkedin-login-callback') !== false
    ) {
        $code = sanitize_text_field($_GET['code']);

        $linkedin_client_id     = trim((string) get_option('linkedin_client_id', ''));
        $linkedin_client_secret = trim((string) get_option('linkedin_client_secret', ''));

        if (empty($linkedin_client_id) || empty($linkedin_client_secret)) {
            wp_safe_redirect(site_url('/login/?error=missing-creds'));
            exit;
        }

        $response = wp_remote_post("https://www.linkedin.com/oauth/v2/accessToken", [
            'body' => [
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => site_url('/linkedin-login-callback/'),
                'client_id'     => $linkedin_client_id,
                'client_secret' => $linkedin_client_secret
            ]
        ]);

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $access_token = $body['access_token'] ?? '';

        if ($access_token) {
            $profile = wp_remote_get("https://api.linkedin.com/v2/userinfo", [
                'headers' => ['Authorization' => 'Bearer ' . $access_token]
            ]);
            $profileData = json_decode(wp_remote_retrieve_body($profile), true);

            $id    = isset($profileData['sub']) ? sanitize_text_field($profileData['sub']) : '';
            $email = isset($profileData['email']) ? sanitize_email($profileData['email']) : '';
            $name  = isset($profileData['name'])  ? sanitize_text_field($profileData['name']) :
                    trim( (isset($profileData['given_name']) ? sanitize_text_field($profileData['given_name']) : '') . ' ' .
                        (isset($profileData['family_name']) ? sanitize_text_field($profileData['family_name']) : '') );
            $linkedinUrl = $id ? "https://www.linkedin.com/openid/id/" . rawurlencode($id) : '';

            if ($email) {
                $user = get_user_by('email', $email);

                if ($user) {
                    wp_set_current_user($user->ID);
                    wp_set_auth_cookie($user->ID, true);

                    if ($linkedinUrl) update_user_meta($user->ID, '_linkedin_profile', esc_url_raw($linkedinUrl));
                    update_user_meta($user->ID, '_linkedin_connected', 'yes');
                    if ($name) update_user_meta($user->ID, '_linkedin_name', $name);

                } else {
                    $username = sanitize_user(current(explode('@', $email)), true);
                    if (username_exists($username)) {
                        $username .= '_' . wp_generate_password(4, false);
                    }

                    $password = wp_generate_password(12, true);
                    $user_id = wp_create_user($username, $password, $email);

                    if (!is_wp_error($user_id)) {
                        $new_user = new WP_User($user_id);
                        $new_user->set_role('reviewer');

                        wp_update_user([
                            'ID' => $user_id,
                            'display_name' => $name ?: $username
                        ]);

                        if ($linkedinUrl) update_user_meta($user_id, '_linkedin_profile', esc_url_raw($linkedinUrl));
                        update_user_meta($user_id, '_linkedin_connected', 'yes');
                        if ($name) update_user_meta($user_id, '_linkedin_name', $name);

                        wp_set_current_user($user_id);
                        wp_set_auth_cookie($user_id, true);
                    }
                }

                wp_safe_redirect(site_url('/reviewer-dashboard/'));
                exit;
            }
        }

        wp_safe_redirect(site_url('/login/?error=linkedin-failed'));
        exit;
    }
});

add_action('init', function() {
    if (isset($_GET['code']) && strpos($_SERVER['REQUEST_URI'], 'google-login-callback') !== false) {
        $code = sanitize_text_field($_GET['code']);
        $client_id     = get_option('google_client_id', '');
        $client_secret = get_option('google_client_secret', '');
        $redirect_uri  = site_url('/google-login-callback/');

        $response = wp_remote_post("https://oauth2.googleapis.com/token", [
            'body' => [
                'code'          => $code,
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri'  => $redirect_uri,
                'grant_type'    => 'authorization_code'
            ]
        ]);
        $body = json_decode(wp_remote_retrieve_body($response), true);
        $access_token = $body['access_token'] ?? '';

        if ($access_token) {
            $profile = wp_remote_get("https://www.googleapis.com/oauth2/v3/userinfo", [
                'headers' => ['Authorization' => 'Bearer ' . $access_token]
            ]);
            $profileData = json_decode(wp_remote_retrieve_body($profile), true);

            $email = sanitize_email($profileData['email'] ?? '');
            $name  = sanitize_text_field($profileData['name'] ?? '');

            if ($email) {
                $user = get_user_by('email', $email);
                if ($user) {
                    wp_set_current_user($user->ID);
                    wp_set_auth_cookie($user->ID, true);
                } else {
                    $username = sanitize_user(current(explode('@', $email)), true);
                    if (username_exists($username)) {
                        $username .= '_' . wp_generate_password(4, false);
                    }
                    $password = wp_generate_password(12, false);
                    $user_id = wp_create_user($username, $password, $email);

                    if (!is_wp_error($user_id)) {
                        $user = new WP_User($user_id);
                        $user->set_role('reviewer');
                        wp_update_user([
                            'ID' => $user_id,
                            'display_name' => $name ?: $username
                        ]);
                        wp_set_current_user($user_id);
                        wp_set_auth_cookie($user_id, true);
                    }
                }
                wp_safe_redirect(site_url('/reviewer-dashboard/'));
                exit;
            }
        }
        wp_safe_redirect(site_url('/login/?google=error'));
        exit;
    }
});