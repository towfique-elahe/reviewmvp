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

/**
 * LinkedIn Login (OpenID Connect)
 */
add_action('init', function() {
    if (
        isset($_GET['code']) && isset($_GET['state']) &&
        strpos($_SERVER['REQUEST_URI'], 'linkedin-login-callback') !== false
    ) {
        $code = sanitize_text_field($_GET['code']);

        // Get credentials
        $linkedin_client_id     = trim((string) get_option('linkedin_client_id', ''));
        $linkedin_client_secret = trim((string) get_option('linkedin_client_secret', ''));

        if (empty($linkedin_client_id) || empty($linkedin_client_secret)) {
            wp_redirect(site_url('/login/?error=missing-creds'));
            exit;
        }

        // Exchange code for token
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
            // Fetch LinkedIn profile
            $profile = wp_remote_get("https://api.linkedin.com/v2/userinfo", [
                'headers' => ['Authorization' => 'Bearer ' . $access_token]
            ]);
            $profileData = json_decode(wp_remote_retrieve_body($profile), true);

            $id    = $profileData['sub'] ?? '';
            $email = $profileData['email'] ?? '';
            $name  = $profileData['name'] ?? ($profileData['given_name'] ?? '') . ' ' . ($profileData['family_name'] ?? '');
            $linkedinUrl = $id ? "https://www.linkedin.com/openid/id/" . $id : '';

            if ($email) {
                // Check if user exists
                $user = get_user_by('email', $email);

                if ($user) {
                    // Login existing
                    wp_set_current_user($user->ID);
                    wp_set_auth_cookie($user->ID, true);

                    // Add verified status
                    update_user_meta($user->ID, '_linkedin_profile', esc_url_raw($linkedinUrl));

                } else {
                    // Create new reviewer user
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

                        // Save LinkedIn info
                        update_user_meta($user_id, '_linkedin_profile', esc_url_raw($linkedinUrl));

                        // Login new user
                        wp_set_current_user($user_id);
                        wp_set_auth_cookie($user_id, true);
                    }
                }

                // Redirect after login
                wp_redirect(site_url('/reviewer-dashboard/'));
                exit;
            }
        }

        wp_redirect(site_url('/login/?error=linkedin-failed'));
        exit;
    }
});

/**
 * Google Login
 */
add_action('init', function() {
    if (isset($_GET['code']) && strpos($_SERVER['REQUEST_URI'], 'google-login-callback') !== false) {
        $code = sanitize_text_field($_GET['code']);
        $client_id     = get_option('google_client_id', '');
        $client_secret = get_option('google_client_secret', '');
        $redirect_uri  = site_url('/google-login-callback/');

        // Exchange code for token
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
                wp_redirect(site_url('/reviewer-dashboard/'));
                exit;
            }
        }
        wp_redirect(site_url('/login/?google=error'));
        exit;
    }
});