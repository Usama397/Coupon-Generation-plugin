<?php


// Create an admin menu item and page
function komo_coder_admin_menu() {
    add_menu_page('Customer List', 'Customer List', 'manage_options', 'customer-list', 'komo_coder_customer_list_page');
}
add_action('admin_menu', 'komo_coder_admin_menu');

function komo_coder_customer_list_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'coupons_users';

    $users = $wpdb->get_results("SELECT * FROM $table_name");

    echo '<div class="wrap">';
    echo '<h2>User List</h2>';

    if (!empty($users)) {
        echo '<table class="widefat" id="user-list-table">';
        echo '<thead><tr><th>ID</th><th>Email</th><th>Full Name</th><th>Creation Date</th><th>Coupon Code</th><th>Coupon Status</th><th>Coupon Created</th><th>Coupon Used Date</th><th>Coupon Used On</th></tr></thead>';
        echo '<tbody>';
        foreach ($users as $user) {
            echo '<tr>';
            echo '<td>' . $user->id . '</td>';
            echo '<td>' . $user->user_email . '</td>';
            echo '<td>' . $user->user_fullname . '</td>';
            echo '<td>' . $user->create_date . '</td>';
            echo '<td>' . $user->coupon_code . '</td>';
            echo '<td>' . $user->coupon_status . '</td>';
            echo '<td>' . $user->coupon_created . '</td>';
            echo '<td>' . $user->coupon_used_date . '</td>';
            echo '<td>' . $user->coupon_used_on . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo 'No users found.';
    }

    echo '</div>';
    echo '<script>
    jQuery(document).ready(function($) {
        $("#user-list-table").DataTable();
    });
</script>';
}




function add_datatables_assets() {
    // Enqueue DataTables CSS in the header
    wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.11.6/css/jquery.dataTables.min.css', array(), '1.11.6');
    
    // Enqueue DataTables JS in the header
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js', array('jquery'), '1.11.6', false);
}
add_action('admin_enqueue_scripts', 'add_datatables_assets');



function coupon_status_admin_page() {
    echo '<div class="wrap">';
    echo '<h2>Coupon Status Update</h2>';
    echo '<form method="post" action="">';

    // Coupon Code Input
    echo '<label for="coupon_code">Coupon Code:</label>';
    echo '<input type="text" name="coupon_code" id="coupon_code" required>';

    // Select Box for Options
    echo '<label for="status">Select Option:</label>';
    echo '<select name="status" id="status">
            <option value="event">Event</option>
            <option value="store">Store</option>
          </select>';

    // Submit Button
    echo '<input type="submit" name="update_coupon_status" class="button button-primary" value="Update Status">';
    echo '</form>';

    echo '</div>';
}

function register_coupon_status_admin_page() {
    add_menu_page('Coupon Status', 'Coupon Status', 'manage_options', 'coupon-status', 'coupon_status_admin_page');
}
add_action('admin_menu', 'register_coupon_status_admin_page');


function handle_coupon_status_update() {
    if (isset($_POST['update_coupon_status'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'coupons_users';

        $coupon_code = sanitize_text_field($_POST['coupon_code']);
        $status = sanitize_text_field($_POST['status']);

        // Check if the coupon exists and its status is 0
        $existing_coupon = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE coupon_code = %s AND coupon_status = 0", $coupon_code));

        if ($existing_coupon) {
            // Update the coupon status to 1
            $wpdb->update($table_name, array('coupon_status' => 1), array('coupon_code' => $coupon_code));
            $wpdb->update($table_name, array('coupon_used_on' => $status), array('coupon_code' => $coupon_code));
            $wpdb->update($table_name, array('coupon_used_date' => date('y-m-d h:i:s')), array('coupon_code' => $coupon_code));
            // Display a success message
            echo '<div class="updated notice"><p>Coupon status updated successfully.</p></div>';
        } else {
            // Display an error message if the coupon doesn't exist or its status is not 0
            echo '<div class="error notice"><p>The coupon does not exist or its status is USED !.</p></div>';
        }
    }
}
add_action('admin_init', 'handle_coupon_status_update');
