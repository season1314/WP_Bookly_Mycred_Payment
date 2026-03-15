<?php
/**
 * Get bookly appointments by customer_id
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function bookly_get_appointments_by_cids($customer_ids, $conditions,$items_per_page = 20,$current_page = 1) {

    global $wpdb;
    $table_p = $wpdb->prefix . 'bookly_payments';
    $customer_ids_str = implode( ',', array_map( 'intval', $customer_ids ) );

    if (empty($customer_ids_str)) return [];


    $table_ca = $wpdb->prefix . 'bookly_customer_appointments';
    $table_app      = $wpdb->prefix . 'bookly_appointments';
    $table_payments = $wpdb->prefix . 'bookly_payments';
    $table_staff    = $wpdb->prefix . 'bookly_staff';
    $table_services = $wpdb->prefix . 'bookly_services';

    $offset = ($current_page - 1) * $items_per_page;

    $base_sql = "
    FROM $table_ca ca
    INNER JOIN $table_app a ON ca.appointment_id = a.id
    LEFT JOIN $table_staff st ON a.staff_id = st.id
    LEFT JOIN $table_services se ON a.service_id = se.id
    LEFT JOIN $table_payments p ON ca.payment_id = p.id
    WHERE ca.customer_id IN ($customer_ids_str)
    AND p.status = 'completed'
    $conditions
    ";

    $data_query = $wpdb->prepare(
        "SELECT 
            ca.id AS book_no,
            ca.status AS app_status, 
            a.start_date,
            st.full_name AS staff_name, 
            se.title AS service_title,
            p.total AS amount,
            p.status AS pay_status
        $base_sql
        ORDER BY a.id DESC
        LIMIT %d OFFSET %d",
        $items_per_page,
        $offset
    );


    $query = $wpdb->prepare(
        "SELECT 
            ca.id AS book_no,
            ca.status AS app_status, 
            a.start_date,
            st.full_name AS staff_name, 
            se.title AS service_title,
            p.total AS amount,
            p.status AS pay_status
        FROM $table_ca ca
        INNER JOIN $table_app a ON ca.appointment_id = a.id
        LEFT JOIN $table_staff st ON a.staff_id = st.id
        LEFT JOIN $table_services se ON a.service_id = se.id
        LEFT JOIN $table_payments p ON ca.payment_id = p.id
        WHERE ca.customer_id IN ($customer_ids_str) 
        AND p.status = 'completed'
        $conditions
        ORDER BY a.id DESC 
        LIMIT %d OFFSET %d", 
        $items_per_page,
        $offset
    );

    $data = $wpdb->get_results($data_query, ARRAY_A);
    $count_query = "SELECT COUNT(*) $base_sql";
    $total_items = $wpdb->get_var($count_query);
    
    return [
        'data'         => $data,
        'current_page' => $current_page,
        'total_pages'  => ceil($total_items / $items_per_page)
    ];
}