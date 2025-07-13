<?php
/**
 * Function File Name: Role Management
 * 
 * The file for user's role mamagement and capabilites.
 */

 // Remove Existing Custom Roles
function reviewmvp_remove_custom_roles() {
    remove_role('parent');
    remove_role('teacher');
    remove_role('student');
    remove_role('hr');
    remove_role('employer');
    remove_role('subscriber');
    remove_role('contributor');
    remove_role('author');
    remove_role('editor');
}
add_action('init', 'reviewmvp_remove_custom_roles', 9);

// Custom User Roles with Base Capabilities
function reviewmvp_add_custom_roles() {
    // Define base capabilities for all roles
    $base_caps = [
        'read'            => true,
        'edit_posts'      => false,
        'delete_posts'    => false,
        'upload_files'    => false,
    ];

    // Define admin capabilities
    $admin_caps = array_merge($base_caps, [
        'manage_options'  => true,
        'edit_users'      => true,
        'delete_users'    => true,
        'create_users'    => true,
        'list_users'      => true,
        'promote_users'   => true,
    ]);

    // Add Reviewer Role
    add_role(
        'reviewer',
        __('Reviewer', 'reviewmvp'),
        $base_caps
    );
}
add_action('init', 'reviewmvp_add_custom_roles');

// Hide Admin Toolbar for Specific Roles
function reviewmvp_hide_admin_toolbar($show_toolbar) {
    $roles_to_hide_toolbar = ['reviewer'];

    foreach ($roles_to_hide_toolbar as $role) {
        if (current_user_can($role)) {
            return false;
        }
    }

    return $show_toolbar;
}
add_filter('show_admin_bar', 'reviewmvp_hide_admin_toolbar');

// Restrict Non-Admin Users from Accessing the WordPress Admin Area
function reviewmvp_restrict_admin_dashboard() {
    if (!current_user_can('manage_options') && is_admin() && !defined('DOING_AJAX')) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('admin_init', 'reviewmvp_restrict_admin_dashboard');