<?php
/**
 * Auto-cleanup task:
 * 1. Runs every 5 minutes to scan for unpaid bookings.
 * 2. If payment status remains 'pending' for >15 minutes:
 * - Set Payment status to 'cancelled'.
 * - Set Customer Appointment status from 'approved' to 'cancelled'.
 * 3. This releases the blocked time slots for other customers.
 */

if (!defined('ABSPATH')) exit;

 //Defined task by cron_schedules
add_filter('cron_schedules', function($schedules) {
    $schedules['every_five_minutes'] = array('interval' => 300,'display'  => __('Every 5 Minutes'));
    return $schedules;
});

//Dev version: Automatic Scheduling upon Loading
if (!wp_next_scheduled('qsh_booking_cleanup_event')) {
    wp_schedule_event(time(), 'every_five_minutes', 'qsh_booking_cleanup_event');
}

 //Main task execution
add_action('qsh_booking_cleanup_event', 'qsh_run_overdue_cleanup');
function qsh_run_overdue_cleanup() { 
    // if (get_option('qsh_auto_cancel_enabled') != '1') {return;} 
    global $wpdb;
    $table_ca = $wpdb->prefix . 'bookly_customer_appointments';
    $table_p  = $wpdb->prefix . 'bookly_payments';
    $current_local_time = current_time('mysql'); 
    $expiry_time = date('Y-m-d H:i:s', strtotime($current_local_time . ' -15 minutes'));

    $result = $wpdb->query($wpdb->prepare(
        "UPDATE $table_ca ca JOIN $table_p p ON ca.payment_id = p.id
        SET ca.status = 'rejected', p.status = 'rejected'
        WHERE p.status = 'pending' 
        AND p.created_at < %s",
        $expiry_time
    ));
    if ($result === false) {
        error_log("QSH Cleanup Task ERROR: Database query failed. Error: " . $wpdb->last_error);
    } elseif ($result > 0) {
        error_log("QSH Cleanup Task SUCCESS: Automatically cancelled $result records older than $expiry_time.");
    } else {
        error_log("QSH Cleanup Task NOTICE: No overdue pending payments found at this time.$expiry_time");
    }
}