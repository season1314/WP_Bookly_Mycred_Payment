<?php
function display_bookly_appointment_list($atts = array()) {
        wp_enqueue_script(
        'bnp-appointment-list-js', 
        plugins_url( 'assets/js/appointment-list.js', __FILE__ ), 
        array('jquery'),
        '1.0', 
        true 
    );

    wp_localize_script('bnp-appointment-list-js', 'bnp_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('bnp_payment_nonce')
    ));

    
    $atts = shortcode_atts( array(
        'status' => null,
        'key' => 'mycred_default',
        'advance' => 3600
    ), $atts, 'display_bookly_appointment_list' );

    $atts_status = $atts['status'];
    $atts_key = $atts['key'];
    $atts_advance = $atts['advance'];
    
    //Verify user login status
    $current_user = wp_get_current_user();
    if ( 0 == $current_user->ID ) {return '<p>Please log in first to access the mycred checkout page.</p>';}
    $user_id = $current_user->ID;
    $user_email   = $current_user->user_email;

    //User ID to get bookly customer_id
    global $wpdb;
    $table_c = $wpdb->prefix . 'bookly_customers';
    $customer_ids = $wpdb->get_col( $wpdb->prepare("SELECT id FROM $table_c WHERE wp_user_id = %d", $user_id));

    //Bookly do not record wp_user_id use user_email to get customer_ids
    if(empty($customer_ids)){
        $customer_ids  = $wpdb->get_col( $wpdb->prepare("SELECT id FROM $table_c WHERE email = %s", $user_email));
    }

    //Not data
    if(empty($customer_ids)){return '<div>No payment records found for the current user (' . $user_email . ')</div>';}


    //Get customer appointments
    $status_condition = "AND ca.status != 'rejected'";
    if(!empty($atts_status)){
        $status_condition = 'AND ca.status = "' .$atts_status . '"'; 
    }
    
    $result =  bookly_get_appointments_by_cids($customer_ids,$status_condition,10,1);
    
    $items = $result['data'];
    $tpages = $result['total_pages'];
    $cpage = $result['current_page'];

    if(empty($items)){
        return '<div>No available appointments found for the current user (' . $user_email . ')</div>';
    }

    $ajax_url = admin_url('admin-ajax.php');
    $template_path = __DIR__ . '/templates/bookly-appointment-table.php';
    ob_start();
    include $template_path;
    $output = '<div class="bnp-payment-table-container">' . ob_get_clean() . '</div>';
    return $output;
}
add_shortcode( 'bnp_bookly_appointment_list', 'display_bookly_appointment_list' );



//Ajax Appointments list 
add_action( 'wp_ajax_mycred_appointments_list', 'handle_appointment_list_callback' );
function handle_appointment_list_callback() {
    global $wpdb;
    $current_user = wp_get_current_user();
    ob_start();
    if ( 0 == $current_user->ID ) {
        wp_send_json_error('Error : Please log in first to access the mycred checkout page');
    }
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $atts_status = $_GET['status'];

    $user_id = $current_user->ID;
    $user_email   = $current_user->user_email;

    //User ID to get bookly customer_id
    global $wpdb;
    $table_c = $wpdb->prefix . 'bookly_customers';
    $customer_ids = $wpdb->get_col( $wpdb->prepare("SELECT id FROM $table_c WHERE wp_user_id = %d", $user_id));
    if(empty($customer_ids)){
        wp_send_json_error('No payment records found for the current user');
    }

    //Bookly do not record wp_user_id use user_email to get customer_ids
    if(empty($customer_ids)){
        $customer_ids  = $wpdb->get_col( $wpdb->prepare("SELECT id FROM $table_c WHERE email = %s", $user_email));
    }
    if(empty($customer_ids)){
        wp_send_json_error('Error : Please log in first to access the mycred checkout page');
    }

    $status_condition = "AND ca.status != 'rejected'";
    if(!empty($atts_status)){
        $status_condition = 'AND ca.status = "' . $atts_status . '"'; 
    }

    $result =  bookly_get_appointments_by_cids($customer_ids,$status_condition,10,$page);

    $items = $result['data'];
    $tpages = $result['total_pages'];
    $cpage = $result['current_page'];

    $template_path = plugin_dir_path( __FILE__ ) . 'templates/bookly-appointment-table.php';
    include $template_path;
    $html_content = ob_get_clean();

    wp_send_json_success( array(
        'html'         => $html_content, 
    ) );
    
}

/**
 * Cancel a Bookly appointment by customer via AJAX.
 */


add_action( 'wp_ajax_cancel_appointment_by_customer', 'handle_cancel_appointment_by_customer_callback' );
function handle_cancel_appointment_by_customer_callback() {
    global $wpdb;
    
    $cid = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $advance = isset($_POST['advance']) ? intval($_POST['advance']) : 3600;
    $key = isset($_POST['key'])? $_POST['key']:"mycred_default";


    $current_user_id = get_current_user_id();
    $user = get_userdata($current_user_id);
    $email = $user->user_email;

    //Get customer appointment info by cid and appointment start time

    $table_c_app = $wpdb->prefix . 'bookly_customer_appointments';
    $table_app = $wpdb->prefix . 'bookly_appointments';
    $table_p = $wpdb->prefix . 'bookly_payments';
    $table_c = $wpdb->prefix . 'bookly_customers';

    $customer_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_c WHERE wp_user_id = %d",$current_user_id));

    if(empty($customer_id)){
    //Fallback setting using email to find $customer_id
    //$customer_id =  $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_c WHERE email = %s",$email));
    wp_send_json_error('Error:the customer is not existed');
    }

    $query = $wpdb->prepare("
    SELECT c.id, c.payment_id, c.customer_id AS c_customer_id,c.status AS c_status, a.start_date,p.status AS p_status,p.total AS p_total,p.details as p_details
    FROM $table_c_app c
    INNER JOIN $table_app a ON c.appointment_id = a.id
    INNER JOIN $table_p p ON c.payment_id = p.id
    WHERE c.id = %d", $cid);

    $c_app_info = $wpdb->get_row($query);

    if(empty($c_app_info)){
        wp_send_json_error('Cannot find the appointment');
    }

    if($c_app_info -> c_customer_id !== $customer_id){
        wp_send_json_error('No permission to do that');
    }

    if($c_app_info -> c_status !=="approved"){
        wp_send_json_error('Error : The appointment status is change');
    }

    if($c_app_info -> p_status !=="completed"){
        wp_send_json_error('Error : The payment of this appointment payment is error');
    }
    $start_date = $c_app_info->start_date; 
    $target_timestamp  = strtotime($start_date);
    $current_timestamp = current_time('timestamp'); 
    $diff_seconds = $target_timestamp - $current_timestamp;
    if($diff_seconds <= 0){
        wp_send_json_error('Error : The appointment status is change');
        exit;
    }
    if ( $diff_seconds <= $advance && $diff_seconds > 0 ) {
        $result = $wpdb->query($wpdb->prepare("UPDATE $table_c_app SET status = %s, updated_at = %s WHERE id = %d",'cancelled',current_time('mysql'),$cid));
        wp_send_json_success('Success cancel the appointment,No refund credits ');
        exit;
    }
    $total   = $c_app_info->p_total;
    $details = json_decode($c_app_info->p_details);
    $items = $details ->items;
    $refund = $total / count($items);

    $cancel_result = bookly_cancel_appointment_refund_by_cid($current_user_id,$cid,$refund,$key);

    if(!$cancel_result){
        wp_send_json_error('Error : The operation is interruption');
    }
    
    wp_send_json_success('Success cancel the appointment,the credits will refund to your account');
}



/*
*Ajax complete appointment by customer
*/
add_action( 'wp_ajax_complete_appointment_by_customer', 'handle_complete_appointment_by_customer_callback' );
function handle_complete_appointment_by_customer_callback() {
    global $wpdb;
    $cid = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $current_user_id = get_current_user_id();
    $user = get_userdata($current_user_id);
    $email = $user->user_email;
    //Get customer appointment info by cid and appointment start time
    $table_c_app = $wpdb->prefix . 'bookly_customer_appointments';
    $table_app = $wpdb->prefix . 'bookly_appointments';
    $table_p = $wpdb->prefix . 'bookly_payments';
    $table_c = $wpdb->prefix . 'bookly_customers';

    $customer_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_c WHERE wp_user_id = %d",$current_user_id));

    if(empty($customer_id)){
    //Fallback setting using email to find $customer_id
    //$customer_id =  $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_c WHERE email = %s",$email));
    wp_send_json_error('Error:the customer is not existed');
    }

    $query = $wpdb->prepare("
    SELECT c.id, c.payment_id, c.customer_id AS c_customer_id,c.status AS c_status, a.start_date,p.status AS p_status,p.total AS p_total,p.details as p_details
    FROM $table_c_app c
    INNER JOIN $table_app a ON c.appointment_id = a.id
    INNER JOIN $table_p p ON c.payment_id = p.id
    WHERE c.id = %d", $cid);

    $c_app_info = $wpdb->get_row($query);

    if(empty($c_app_info)){
        wp_send_json_error('Cannot find the appointment');
    }

    if($c_app_info -> c_customer_id !== $customer_id){
        wp_send_json_error('No permission to do that');
    }

    if($c_app_info -> c_status !=="approved"){
        wp_send_json_error('Error : The appointment status is change');
    }

    if($c_app_info -> p_status !=="completed"){
        wp_send_json_error('Error : The payment of this appointment payment is error');
    }
    $start_date = $c_app_info->start_date; 
    $target_timestamp  = strtotime($start_date);
    $current_timestamp = current_time('timestamp'); 
    $diff_seconds = $target_timestamp - $current_timestamp;
    if($diff_seconds > 0){
        wp_send_json_error('Error : The appointment status is change');
        exit;
    }
    
    $result = $wpdb->query($wpdb->prepare("UPDATE $table_c_app SET status = %s, updated_at = %s WHERE id = %d",'done',current_time('mysql'),$cid));

    if(!$result){wp_send_json_error('Error : The operation is interruption');}
    
    wp_send_json_success('Success complete the appointment');
}
