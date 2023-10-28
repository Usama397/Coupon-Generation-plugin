<?php
// Make sure the script is being run from WordPress.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Include WordPress core functions and classes.
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

// Global WordPress database object.
global $wpdb;

// Define the name of the table to be removed.
$table_name = $wpdb->prefix . 'coupons_users';

// Drop the database table if it exists.
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// You can add additional cleanup tasks here if needed.
