<?php 
/*
* Plugin Name: Komo Coder
* Description: Create Coupons For Users 
* Version: 1.0
* Author: Harry Kenndy
*/

// Security: Define and restrict direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

// Define the activation callback function
function komo_coder_activation() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'coupons_users';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_email varchar(100) NOT NULL,
        user_fullname varchar(100) NOT NULL,
        create_date datetime NOT NULL,
        coupon_code varchar(20) NOT NULL,
        coupon_status varchar(20) NOT NULL,
        coupon_created datetime NOT NULL,
        coupon_used_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        coupon_used_on varchar(100) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Register the activation hook
register_activation_hook(__FILE__, 'komo_coder_activation');


// Define the uninstall callback function
function komo_coder_uninstall() {
    if (!defined('WP_UNINSTALL_PLUGIN')) {
        exit;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'coupons_users';

    // Delete the table if it exists
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

// Register the uninstallation hook
register_uninstall_hook(__FILE__, 'komo_coder_uninstall');


include(plugin_dir_path(__FILE__).'init/form.php');
include(plugin_dir_path(__FILE__).'init/admin.php');
