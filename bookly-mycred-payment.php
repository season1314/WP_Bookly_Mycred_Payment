<?php
/*
Plugin Name: Bookly-Mycred-Payment
Plugin URI:  https://github.com/season1314/WP_Bookly_Mycred_Payment.git
Description: A plugin that allows users to pay for Bookly appointments using myCRED points.
Version:     1.0.0
Author:      Q Hao
Author URI:  https://github.com/season1314/
License:     GPL2
Text Domain: bookly-mycred-payment
*/

require_once plugin_dir_path( __FILE__ ) . 'methods/bookly-cleanup-payment.php';
require_once plugin_dir_path( __FILE__ ) . 'methods/bookly-get-payments-by-cids.php';
require_once plugin_dir_path( __FILE__ ) . 'methods/bookly-cancel-appointment.php';
require_once plugin_dir_path( __FILE__ ) . 'methods/bookly-get-appointments-by-cids.php';
require_once plugin_dir_path( __FILE__ ) . 'frontend/mycred-payment-list.php';
require_once plugin_dir_path( __FILE__ ) . 'frontend/mycred-appointment-list.php';
require_once plugin_dir_path( __FILE__ ) . 'frontend/mycred-checkout-desk.php';



function bnp_enqueue_checkout_styles() {
    if ( ! is_admin() ) {
        wp_enqueue_style( 
            'bnp-mycred-checkout-style', 
            plugins_url( 'frontend/assets/css/mycred-checkout.css', __FILE__ ), 
            array(), 
            '1.1.0' 
        );

        wp_enqueue_style( 
            'bnp-mycred-table-style', 
            plugins_url( 'frontend/assets/css/mycred-table.css', __FILE__ ), 
            array(), 
            '1.1.0' 
        );
    }
}
add_action( 'wp_enqueue_scripts', 'bnp_enqueue_checkout_styles' );



if (!defined('ABSPATH')) exit;



