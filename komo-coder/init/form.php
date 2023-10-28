<?php

// Form HTML
function komo_coder_form() {
    ob_start(); // Start output buffering

    ?>

    <form method="POST" action="<?php echo admin_url('admin-post.php'); ?>">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <input type="email" class="form-control" id="user_email" name="user_email" required placeholder="Enter your email" style="width: 100%;background: white;margin: 1% 0%;border-radius: 3px;">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <input type="text" class="form-control" id="data_field" name="data_field" required placeholder="Enter your Full Name" style="width: 100%;background: white;margin: 1% 0%;border-radius: 3px;">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="display: flex;
    flex-direction: row;
    justify-content: center;">
                <input type="hidden" name="action" value="process_form">
                <button type="submit" class="btn btn-primary" style='font-family: "Libre Caslon Display", Sans-serif;
    font-size: 35px;
    font-weight: 400;
    line-height: 1em;
    border-style: solid;
    border-radius: 47px 47px 47px 47px;
    padding: 12px 35px 12px 35px;
    background:#f2e6d8;
    margin-top: 10px;
    color:#091a26;'>SEND</button>
            </div>
        </div>
    </form>
    <?php

    return ob_get_clean(); // Return the buffered content
}

function display_existing_user_error() {
    echo '<div class="alert alert-danger mt-3">User already exists on the list. Please try again.</div>';
}
function schedule_email_sending($user_email, $couponCode) {
    // Calculate the timestamp for 12 hours from now
    $timestamp = current_time('timestamp') + 12 * 60 * 60; // 12 hours in seconds

    // Schedule the email sending event
    wp_schedule_single_event($timestamp, 'send_email_event', array($user_email, $couponCode));
}

function send_email($user_email, $couponCode) {
    // Send the email
    $to = $user_email;
    $subject = 'Deliciousness on us!';
    $message = 'Congratulations, you’re about to enjoy a delicious Chef Macku Special on us! Please show this confirmation message to your server upon arriving for your Komo dining experience. Your unique code: '.$couponCode;
    $headers = 'From: Komo Chicago';

    wp_mail($to, $subject, $message, $headers);
}
// Handle form submissions
function process_form() {
    if (isset($_POST['user_email']) && isset($_POST['data_field'])) {
        $user_email = sanitize_email($_POST['user_email']);
        $data = sanitize_text_field($_POST['data_field']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'coupons_users';

        // Check if the user already exists
        $existing_user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE user_email = %s", $user_email));

        if (!$existing_user) {
            // User doesn't exist, insert the new record
            $create_date = date('Y-m-d');
            function generateCouponCode($length = 7) {
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $couponCode = '';
            
                for ($i = 0; $i < $length; $i++) {
                    $couponCode .= $characters[rand(0, strlen($characters) - 1)];
                }
            
                return $couponCode;
            }
            
            $couponCode = generateCouponCode();

            $wpdb->insert($table_name, array(
                'user_email'        => $user_email, 
                'user_fullname'     => $data, 
                'create_date'       => $create_date, 
                'coupon_code'       => $couponCode,
                'coupon_status'     => '0',
                'coupon_created'    => $create_date,
                'coupon_used_date'  => $create_date,
                'coupon_used_on'    => "Not Used"
            ));
             // Send an email
            schedule_email_sending($user_email, $couponCode);
            wp_redirect('/thank-you/');
            exit();
        }
        else {
            // User already exists, show an error message
            wp_redirect('/customer-exists/');
            exit();
            add_action('wp_footer', 'display_existing_user_error');
        }

        // Optionally, redirect the user after form submission
        
    }
}

// Add action hooks for form processing
add_action('admin_post_process_form', 'process_form');
add_action('admin_post_nopriv_process_form', 'process_form');
add_action('send_email_event', 'send_email', 10, 2);
// Display the form using a shortcode
function display_form_shortcode() {
    return komo_coder_form();
}
add_shortcode('komo-coder-form', 'display_form_shortcode');
