<?php

/**
 * File name    :   contr.php
 * @author      :   Callistus
 * Description  :   Controller file for Invoice frontend
 */

// Define the shortcode function
function sw_invoice_shortcode() {
    // Check if the user is logged in
    if (!is_user_logged_in()) {
        return 'You must be logged in to view this page.';
    }

    // Start output buffering
    ob_start();

    $current_user_id = get_current_user_id();
    $current_user = wp_get_current_user();
    $currentuseremail = $current_user->user_email;
    // Get and sanitize the 'invoice_page' parameter
    $url_param = isset($_GET['invoice_page']) ? sanitize_key($_GET['invoice_page']) : '';

    // Switch based on the validated 'invoice_page' parameter
    switch ($url_param) {
        case 'view_invoice':
            // Check if an invoice ID is provided in the URL
            $invoice_id = isset($_GET['invoice_id']) ? sanitize_text_field($_GET['invoice_id']) : '';
            if (!empty($invoice_id)) {
                echo view_invoice_details($invoice_id);
            } else {
                echo 'Invalid invoice ID.';
            }
            break;
        case 'invoices_by_status':
            // Check if a payment status is provided in the URL
            $payment_status = isset($_GET['payment_status']) ? sanitize_text_field($_GET['payment_status']) : '';
            if (!empty($payment_status)) {
                echo view_invoices_by_status($payment_status);
            } else {
                echo 'Invalid payment status.';
            }
            break;
        default:
            echo view_all_invoices();
            break;
    }

    // Get the buffered output
    $output = ob_get_clean();

    // Return the output
    return $output;
}
