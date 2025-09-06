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

                // Redirect
                wp_redirect(site_url('/reviewer-dashboard/')); 
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
            <button type="button" class="social-btn linkedin-btn" onclick="linkedinRegister()">
                <ion-icon name="logo-linkedin"></ion-icon>
                Continue with LinkedIn
            </button>

            <button type="button" class="social-btn google-btn" onclick="googleRegister()">
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

function linkedinRegister() {
    let clientId = "<?php echo esc_js(get_option('linkedin_client_id')); ?>";
    let redirectUri = "<?php echo site_url('/linkedin-register-callback/'); ?>";
    let state = Math.random().toString(36).substring(2);

    let oauthUrl = "https://www.linkedin.com/oauth/v2/authorization?response_type=code" +
        "&client_id=" + clientId +
        "&redirect_uri=" + encodeURIComponent(redirectUri) +
        "&scope=openid%20profile%20email" +
        "&state=" + state;

    window.location.href = oauthUrl;
}

function googleRegister() {
    let clientId = "<?php echo esc_js(get_option('google_client_id')); ?>";
    let redirectUri = "<?php echo site_url('/google-register-callback/'); ?>";
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
add_shortcode('custom_register_form', 'reviewmvp_custom_register_form');

/**
 * LinkedIn Register (OpenID Connect)
 */
add_action('init', function() {
    if (
        isset($_GET['code'], $_GET['state']) &&
        strpos($_SERVER['REQUEST_URI'], 'linkedin-register-callback') !== false
    ) {
        $code = sanitize_text_field($_GET['code']);

        $client_id     = get_option('linkedin_client_id', '');
        $client_secret = get_option('linkedin_client_secret', '');
        $redirect_uri  = site_url('/linkedin-register-callback/');

        // Exchange code for token
        $response = wp_remote_post("https://www.linkedin.com/oauth/v2/accessToken", [
            'body' => [
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => $redirect_uri,
                'client_id'     => $client_id,
                'client_secret' => $client_secret
            ]
        ]);
        $body = json_decode(wp_remote_retrieve_body($response), true);
        $access_token = $body['access_token'] ?? '';

        if ($access_token) {
            // Fetch profile
            $profile = wp_remote_get("https://api.linkedin.com/v2/userinfo", [
                'headers' => ['Authorization' => 'Bearer ' . $access_token]
            ]);
            $profileData = json_decode(wp_remote_retrieve_body($profile), true);

            // pull fields safely
            $sub   = isset($profileData['sub'])   ? sanitize_text_field($profileData['sub']) : '';
            $email = isset($profileData['email']) ? sanitize_email($profileData['email']) : '';
            $name  = isset($profileData['name'])  ? sanitize_text_field($profileData['name'])
                    : trim(
                        (isset($profileData['given_name'])  ? sanitize_text_field($profileData['given_name'])  : '') . ' ' .
                        (isset($profileData['family_name']) ? sanitize_text_field($profileData['family_name']) : '')
                    );
            $linkedinUrl = $sub ? 'https://www.linkedin.com/openid/id/' . rawurlencode($sub) : '';

            if ($email) {
                // If user already exists, log them in
                $user = get_user_by('email', $email);
                if ($user) {
                    // Save LinkedIn connect flags/meta
                    if ($linkedinUrl) update_user_meta($user->ID, '_linkedin_profile', esc_url_raw($linkedinUrl));
                    update_user_meta($user->ID, '_linkedin_connected', 'yes');
                    if ($name) update_user_meta($user->ID, '_linkedin_name', $name);

                    wp_set_current_user($user->ID);
                    wp_set_auth_cookie($user->ID, true);
                    wp_redirect(site_url('/reviewer-dashboard/'));
                    exit;
                }

                // Otherwise, register a new one
                $username = sanitize_user(current(explode('@', $email)), true);
                if (username_exists($username)) {
                    $username .= '_' . wp_generate_password(4, false);
                }

                $password = wp_generate_password(12, false);
                $user_id = wp_create_user($username, $password, $email);

                if (!is_wp_error($user_id)) {
                    $user_obj = new WP_User($user_id);
                    $user_obj->set_role('reviewer');

                    wp_update_user([
                        'ID' => $user_id,
                        'display_name' => $name ?: $username
                    ]);

                    // Save LinkedIn connect flags/meta
                    if ($linkedinUrl) update_user_meta($user_id, '_linkedin_profile', esc_url_raw($linkedinUrl));
                    update_user_meta($user_id, '_linkedin_connected', 'yes');
                    if ($name) update_user_meta($user_id, '_linkedin_name', $name);

                    // Auto-login new user
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id, true);

                    wp_redirect(site_url('/reviewer-dashboard/'));
                    exit;
                }
            }
        }

        // Fallback: registration failed
        wp_redirect(site_url('/sign-up/?linkedin=error'));
        exit;
    }
});

/**
 * Google Register
 */
add_action('init', function() {
    if (isset($_GET['code']) && strpos($_SERVER['REQUEST_URI'], 'google-register-callback') !== false) {
        $code = sanitize_text_field($_GET['code']);
        $client_id     = get_option('google_client_id', '');
        $client_secret = get_option('google_client_secret', '');
        $redirect_uri  = site_url('/google-register-callback/');

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
                    wp_redirect(site_url('/write-a-review/'));
                    exit;
                }

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
                    wp_redirect(site_url('/write-a-review/'));
                    exit;
                }
            }
        }
        wp_redirect(site_url('/sign-up/?google=error'));
        exit;
    }
});