<?php
function display_bookly_payment_list($atts) {
    wp_enqueue_script(
        'bnp-payment-list-js', 
        plugins_url( 'assets/js/payment-list.js', __FILE__ ), 
        array('jquery'),
        '1.0', 
        true 
    );

    wp_localize_script('bnp-payment-list-js', 'bnp_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('bnp_payment_nonce')
    ));


    $atts = shortcode_atts( array('redirect' => '/'), $atts, 'bnp_bookly_payment_list' );

    $redirect_url = $atts['redirect'];

    //Verify user login status
    $current_user = wp_get_current_user();
    if ( 0 == $current_user->ID ) return '<p>Please log in first to access the mycred checkout page.</p>';
    $user_id = $current_user->ID;
    $user_email   = $current_user->user_email;

    //User ID to get bookly customer_id
    global $wpdb;
    $table_c = $wpdb->prefix . 'bookly_customers';
    $customer_ids = $wpdb->get_col( $wpdb->prepare("SELECT id FROM $table_c WHERE wp_user_id = %d", $user_id));

    //Bookly do not record wp_user_id use user_email to get customer_ids
    if(empty($customer_ids)) $customer_ids  = $wpdb->get_col( $wpdb->prepare("SELECT id FROM $table_c WHERE email = %s", $user_email));
    

    //Not data
    if(empty($customer_ids))return '<div>No payment records found for the current user (' . $user_email . ')</div>';

    $result = bookly_get_payments_by_cids($customer_ids, 10, 1); 
    $items = $result['data'];
    $tpages = $result['total_pages'];
    $cpage = $result['current_page'];

    $ajax_url = admin_url('admin-ajax.php');
    $template_path = __DIR__ . '/templates/bookly-payment-table.php';
    ob_start();
    include $template_path;
    $output = '<div class="bnp-payment-table-container">' . ob_get_clean() . '</div>';
    return $output;
}
add_shortcode( 'bnp_bookly_payment_list', 'display_bookly_payment_list' );



//Ajax Payment list 
add_action( 'wp_ajax_mycred_payment_list', 'handle_payment_list_callback' );
function handle_payment_list_callback() {
    global $wpdb;
    $current_user = wp_get_current_user();
    ob_start();
    if ( 0 == $current_user->ID ) {
        wp_send_json_error('Error : Please log in first to access the mycred checkout page');
    }
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
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
    if(empty($customer_ids)){
        wp_send_json_error('Error : Please log in first to access the mycred checkout page');
    }

    $result = bookly_get_payments_by_cids($customer_ids, 10, $page); 
    $items = $result['data'];
    $tpages = $result['total_pages'];
    $cpage = $result['current_page'];

    $template_path = plugin_dir_path( __FILE__ ) . 'templates/bookly-payment-table.php';
    include $template_path;
    $html_content = ob_get_clean();

    wp_send_json_success( array(
        'html'         => $html_content, 
    ) );
    
}