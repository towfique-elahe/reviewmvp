<?php

/**
 * Shortcode: [custom_forgot_password]
 * Displays a "forgot password" form and triggers WP's password reset email
 */
function reviewmvp_custom_forgot_password() {
    if (is_user_logged_in()) {
        return '<p style="text-align:center;">You are already logged in.</p>';
    }

    $message = '';

    // Handle submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_forgot_nonce'])) {
        if (wp_verify_nonce($_POST['custom_forgot_nonce'], 'custom_forgot_action')) {
            $email = sanitize_email($_POST['user_email']);

            if (empty($email)) {
                $message = '<p style="color:crimson; text-align:center;">Please enter your email address.</p>';
            } elseif (!email_exists($email)) {
                $message = '<p style="color:crimson; text-align:center;">No account found with that email address.</p>';
            } else {
                $user = get_user_by('email', $email);

                // Generate reset key
                $reset_key = get_password_reset_key($user);

                if (is_wp_error($reset_key)) {
                    $message = '<p style="color:crimson; text-align:center;">Could not generate reset link. Try again.</p>';
                } else {
                    // Custom reset password page (where [custom_reset_password] shortcode is used)
                    $reset_page = site_url('/reset-password/'); 

                    $reset_url = add_query_arg([
                        'key'   => $reset_key,
                        'login' => rawurlencode($user->user_login)
                    ], $reset_page);

                    // Send email
                    $subject = "Password Reset Request";
                    $body    = "Hi " . $user->display_name . ",\n\n";
                    $body   .= "Click the link below to reset your password:\n\n";
                    $body   .= $reset_url . "\n\n";
                    $body   .= "If you did not request this, please ignore this email.";
                    $headers = ['Content-Type: text/plain; charset=UTF-8'];

                    wp_mail($email, $subject, $body, $headers);

                    $message = '<p style="color:green;">Check your email for a password reset link.</p>';
                }
            }
        }
    }

    ob_start(); ?>

<div id="auth" class="auth-container">
    <div class="custom-forgot-password">
        <?php if (!empty($message)) echo $message; ?>

        <form method="post">
            <?php wp_nonce_field('custom_forgot_action', 'custom_forgot_nonce'); ?>

            <div class="form-group">
                <label for="user_email">Email address</label>
                <input type="email" name="user_email" id="user_email" placeholder="Enter email" required>
            </div>

            <button type="submit" class="btn-submit">Send link</button>
        </form>

        <div class="suggest-text">
            <a href="<?php echo site_url('/login/'); ?>">Back to login</a>
        </div>
    </div>
</div>

<?php
    return ob_get_clean();
}
add_shortcode('custom_forgot_password', 'reviewmvp_custom_forgot_password');