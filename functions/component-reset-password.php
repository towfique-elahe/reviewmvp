<?php

/**
 * Shortcode: [custom_reset_password]
 * Custom reset password form
 */
function reviewmvp_custom_reset_password() {
    if (is_user_logged_in()) {
        return '<p>You are already logged in.</p>';
    }

    $message = '';
    $error   = '';

    // Get key + login from reset email link
    $key   = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
    $login = isset($_GET['login']) ? sanitize_text_field($_GET['login']) : '';

    if (!$key || !$login) {
        return '<p style="color:red;">Invalid reset link.</p>';
    }

    $user = check_password_reset_key($key, $login);
    if (is_wp_error($user)) {
        return '<p style="color:red;">' . $user->get_error_message() . '</p>';
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_reset_nonce'])) {
        if (wp_verify_nonce($_POST['custom_reset_nonce'], 'custom_reset_action')) {
            $pass1 = sanitize_text_field($_POST['pass1']);
            $pass2 = sanitize_text_field($_POST['pass2']);

            if (empty($pass1) || empty($pass2)) {
                $error = 'Please fill in both password fields.';
            } elseif ($pass1 !== $pass2) {
                $error = 'Passwords do not match.';
            } else {
                reset_password($user, $pass1);
                $message = '<p style="color:green;">Password successfully reset. <a href="' . site_url('/login/') . '">Login here</a>.</p>';
            }
        }
    }

    ob_start(); ?>

<div class="custom-reset-password">
    <h2>Reset password</h2>
    <p>Please kindly set your new password</p>

    <?php if (!empty($error)) : ?>
    <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (!empty($message)) : ?>
    <?php echo $message; ?>
    <?php else: ?>
    <form method="post">
        <?php wp_nonce_field('custom_reset_action', 'custom_reset_nonce'); ?>

        <div class="form-group">
            <label for="pass1">New password</label>
            <input type="password" name="pass1" id="pass1" required>
        </div>

        <div class="form-group">
            <label for="pass2">Re-enter password</label>
            <input type="password" name="pass2" id="pass2" required>
        </div>

        <button type="submit" class="btn-submit">Save</button>
    </form>
    <?php endif; ?>
</div>

<style>
.custom-reset-password {
    max-width: 400px;
    margin: 0 auto;
    text-align: center;
    font-family: Arial, sans-serif;
}

.custom-reset-password h2 {
    font-size: 22px;
    margin-bottom: 10px;
}

.custom-reset-password p {
    margin: 8px 0;
    color: #444;
}

.form-group {
    margin: 15px 0;
    text-align: left;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
}

.form-group input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
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
</style>

<?php
    return ob_get_clean();
}
add_shortcode('custom_reset_password', 'reviewmvp_custom_reset_password');