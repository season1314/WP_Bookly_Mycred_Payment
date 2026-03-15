<?php
/**
 * Get bookly payments by customer_id
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function bookly_get_payments_by_cids($customer_ids, $items_per_page = 20,$current_page = 1) {
    global $wpdb;
    $table_p = $wpdb->prefix . 'bookly_payments';
    if (empty($customer_ids)) return [];
    $offset = ($current_page - 1) * $items_per_page;
    $placeholders = implode(', ', array_fill(0, count($customer_ids), '%d'));
    $total_items = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_p WHERE customer_id IN ($placeholders) AND status IN ('pending', 'completed')",
        $customer_ids
    ));
    $total_pages = max(1,ceil($total_items / $items_per_page));
    $query = $wpdb->prepare(
        "SELECT * FROM $table_p 
         WHERE customer_id IN ($placeholders) 
         AND status IN ('pending', 'completed') 
         ORDER BY created_at DESC 
         LIMIT %d OFFSET %d", 
        array_merge($customer_ids, [$items_per_page, $offset])
    );
    return [
        'data'         => $wpdb->get_results($query, ARRAY_A),
        'total_pages'  => $total_pages,
        'current_page' => $current_page
    ];
}