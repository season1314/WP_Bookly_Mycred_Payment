<?php
function display_mycred_checkout_desk($atts) {
    wp_enqueue_script(
        'bnp-checkout-js', 
        plugins_url( 'assets/js/checkout.js', __FILE__ ), 
        array('jquery'),
        '1.0', 
        true 
    );

    wp_localize_script('bnp-checkout-js', 'bnp_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('bnp_checkout_nonce')
    ));

    $atts = shortcode_atts( 
        array(
            'redirect' => '/',
            'key' => 'mycred_default'
        ), 
        $atts, 'bnp_mycred_desk' 
    );

    $redirect_url = $atts['redirect'];
    $key = $atts['key'];

    //Verify user login status
    $current_user = wp_get_current_user();
    $template_path = __DIR__ . '/templates/mycred-checkout-error.php';
    if ( 0 == $current_user->ID ) {
        $error = 'Please log in first to access the mycred checkout page.';
        ob_start();
        include $template_path;
        $output = ob_get_clean();
        return $output;
    }

    //Get user balance
    $balance = 0;
    if ( function_exists( 'mycred_get_users_balance' ) ) {
        $balance = mycred_get_users_balance($current_user->ID,$key);
    }

    //Get Url bookNo /book number array or payment id
    $bookNo = isset($_GET['bookNo']) ? intval(strip_tags($_GET['bookNo'])) : 0;
    $pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
    if($bookNo == 0 &&  $pid == 0){
        $error = 'Payment Status Changes';
        ob_start();
        include $template_path;
        $output = ob_get_clean();
        return $output;
    }

    //Get payment id through bookNo
    global $wpdb;
    $table_name = $wpdb->prefix . 'bookly_customer_appointments';

    $payment_id = $pid;

    //Priority bookNo
    if($bookNo !== 0){
        $payment_id = $wpdb->get_var( $wpdb->prepare("SELECT payment_id FROM $table_name WHERE id = %d", $bookNo));
    }
    if(empty($payment_id)){
        $error = 'Error:payment_id is not existed';
        ob_start();
        include $template_path;
        $output = ob_get_clean();
        return $output;
    }

    //Get total payment amount
    $table_name = $wpdb->prefix . 'bookly_payments';
    $payment_info = $wpdb->get_row($wpdb->prepare("SELECT id, total, paid, status FROM $table_name WHERE id = %d", $payment_id));
    if (empty($payment_info)){
        $error = 'Payment Status Changes';
        ob_start();
        include $template_path;
        $output = ob_get_clean();
        return $output;
    }
    $total  = $payment_info->total;
    $paid   = $payment_info->paid;
    $status = $payment_info->status;

    if($status !== 'pending'){ 
        $error = 'Payment Status Changes';
        ob_start();
        include $template_path;
        $output = ob_get_clean();
        return $output;
    }
    
    $template_path = __DIR__ . '/templates/mycred-checkout-panel.php';

    if ( file_exists( $template_path ) ) {
        ob_start();
        include $template_path;
        $output = ob_get_clean();
        return $output;
    } else {
        return 'Error: Template not found at ' . $template_path;
    }
    include(locate_template($template_path));
    return ob_get_clean();
}
add_shortcode( 'bnp_mycred_desk', 'display_mycred_checkout_desk' );



/**
 * Handle payment using myCred credits.
 *
 * This AJAX handler deducts user credits from myCred
 * and marks the Bookly payment as completed.
 *
 * AJAX Action: mycred_process_payment
 *
 * Parameters (POST):
 * @param int payment_id Bookly payment ID that needs to be processed.
 * @param string key myCred point type key (e.g. "mycred_default").
 *
 * Requirements:
 * - User must be logged in.
 *
 * Process:
 * 1. Retrieve the Bookly payment record.
 * 2. Verify payment status has not already been completed.
 * 3. Check user's myCred balance.
 * 4. Deduct credits using mycred_add().
 * 5. Update Bookly payment status to 'completed'.
 *
 * Returns (JSON):
 * - success: Payment successful and credits deducted.
 * - error: Payment failed due to validation or balance issues.
 */


add_action( 'wp_ajax_mycred_process_payment', 'handle_mycred_payment_callback' );

function handle_mycred_payment_callback() {
    global $wpdb;

    $payment_id = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0;
    $key = isset($_POST['key'])? $_POST['key']:"mycred_default";
    $current_user_id = get_current_user_id();

     //Get payment
    $table_p = $wpdb->prefix . 'bookly_payments';
    $payment_info = $wpdb->get_row($wpdb->prepare("SELECT id, total, paid, status FROM $table_p WHERE id = %d", $payment_id));
    if (empty($payment_info)){wp_send_json_error('Error : Cannot find payment');}
    $total  = $payment_info->total;
    $paid   = $payment_info->paid;
    $status = $payment_info->status;
    if($status == 'completed' || $paid > 0){wp_send_json_error('Error:Payment status changed');}


    //Get user balance
    $balance = mycred_get_users_balance($current_user_id);

    if($balance < $total){wp_send_json_error('Insufficient credits. Please top up to continue.');}

    //Deduct points in mycred
    $ref = 'bookly_appointment_payment';
    $result = mycred_add($ref,$current_user_id,-$total,"Bookly payment: #" . $payment_id,$payment_id,'',$key);

    if(empty($result)){wp_send_json_error('Error:Payment status changed');}

    //Update the payment
    $data_to_update = array('status' => 'completed','paid'   => $total,);
    $where = array( 'id' => $payment_id );
    $update_result = $wpdb->update( $table_p, $data_to_update, $where );

    //Update all appointment
    if ( false === $update_result ) {wp_send_json_error( 'Error: Failed to update Bookly payment status.' );} 

    wp_send_json_success( 'Payment successful! Your credits have been deducted.');

}
