<?php
/**
 * Cancel appointment
 */

if (!defined('ABSPATH')) exit;

function bookly_cancel_appointment_refund_by_cid($user_id,$cid,$refund,$point_type) {
    global $wpdb;
    $table_c_app = $wpdb->prefix . 'bookly_customer_appointments';
    $data_to_update = array('status' => 'cancelled');
    $where = array( 'id' => $cid );
    $update_result = $wpdb->update( $table_c_app, $data_to_update, $where );
    if ( false === $update_result ) {
        return false;
    }
    $ref = 'appointment_cancelled_refund';
    $result = mycred_add($ref,$user_id, $refund,"Bookly appointment cancel refund: #" . $cid,$cid,'',$point_type);
    return true;
}


function bookly_cancel_appointment_no_refund_by_cid($cid){
    global $wpdb;
    $table_c_app = $wpdb->prefix . 'bookly_customer_appointments';
    $data_to_update = array('status' => 'cancelled');
    $where = array( 'id' => $cid );
    $update_result = $wpdb->update( $table_c_app, $data_to_update, $where );
    if ( false === $update_result ) {
        return false;
    }
    return true;
}