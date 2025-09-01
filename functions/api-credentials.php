<?php

// Create menu in admin
add_action('admin_menu', function() {
    add_options_page(
        'API Credentials',
        'API Credentials',
        'manage_options',
        'api-credentials',
        'reviewmvp_api_credentials_page'
    );
});

// Register settings
add_action('admin_init', function() {
    register_setting('reviewmvp_api_settings', 'linkedin_client_id');
    register_setting('reviewmvp_api_settings', 'linkedin_client_secret');
    register_setting('reviewmvp_api_settings', 'google_client_id');
    register_setting('reviewmvp_api_settings', 'google_client_secret');
});

// Render page
function reviewmvp_api_credentials_page() { ?>
<div class="wrap">
    <h1>API Credentials</h1>
    <form method="post" action="options.php">
        <?php settings_fields('reviewmvp_api_settings'); ?>
        <?php do_settings_sections('reviewmvp_api_settings'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">LinkedIn Client ID</th>
                <td><input type="text" name="linkedin_client_id"
                        value="<?php echo esc_attr(get_option('linkedin_client_id')); ?>" style="width:350px;"></td>
            </tr>
            <tr valign="top">
                <th scope="row">LinkedIn Client Secret</th>
                <td><input type="password" name="linkedin_client_secret"
                        value="<?php echo esc_attr(get_option('linkedin_client_secret')); ?>" style="width:350px;"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Google Client ID</th>
                <td><input type="text" name="google_client_id"
                        value="<?php echo esc_attr(get_option('google_client_id')); ?>" style="width:350px;"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Google Client Secret</th>
                <td><input type="password" name="google_client_secret"
                        value="<?php echo esc_attr(get_option('google_client_secret')); ?>" style="width:350px;"></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
<?php }