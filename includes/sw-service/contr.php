<?php

/**
 * File name    :   contr.php
 * @author      :   Callistus
 * Description  :   Controller file for Service
 */



 function sw_handle_new_service_page() {
    echo '<h2>Add New Service</h2>';
    echo '<p> Publish new service subscription and setup billing interval</p>';

    // Check if the form is submitted
    if (isset($_POST['add_new_service_submit'])) {
        // Verify nonce for added security
        if (isset($_POST['sw_add_new_service_nonce']) && wp_verify_nonce($_POST['sw_add_new_service_nonce'], 'sw_add_new_service_nonce')) {
            // Form data validation and processing
            $user_id = isset($_POST['user_id']) ? absint($_POST['user_id']) : 0;
            $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
            $service_name = isset($_POST['service_name']) ? sanitize_text_field($_POST['service_name']) : '';
            $service_type = isset($_POST['service_type']) ? sanitize_text_field($_POST['service_type']) : '';
            $service_url = isset($_POST['service_url']) ? esc_url_raw($_POST['service_url']) : '';
            $invoice_id = isset($_POST['invoice_id']) ? sanitize_text_field($_POST['invoice_id']) : '';
            $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : '';
            $billing_cycle = isset($_POST['billing_cycle']) ? sanitize_text_field($_POST['billing_cycle']) : '';
            $next_payment_date = isset($_POST['next_payment_date']) ? sanitize_text_field($_POST['next_payment_date']) : '';
            $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : '';
            $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
            

            // Validation
            $validation_errors = array();

            if ( !preg_match( '/^[A-Za-z0-9 ]+$/', $service_name ) ) {
                $validation_errors[] = 'Service name should only contain letters, and numbers.';
            }

            if ( !empty( $service_type ) && !preg_match('/^[A-Za-z0-9 ]+$/', $service_type)) {
                $validation_errors[] = 'Service type should only contain letters, numbers, and spaces.';
            }

            if ( !empty( $service_url ) && filter_var($service_url, FILTER_VALIDATE_URL) === false) {
                $validation_errors[] = 'Invalid service URL format.';
            }
            if ( empty( $product_id ) ) {
                $validation_errors[] = 'A product is required to set up a service.';
            }


            if ( empty ( $start_date ) || empty ( $end_date ) || empty( $next_payment_date ) || empty( $billing_cycle )  ) {
                $validation_errors[] = 'All Dates must correspond to the billing circle';
            }



            if (!empty($validation_errors)) {
                // Display validation errors
                sw_error_notice( $validation_errors );
            } else {
                // Create a new Sw_Service object
                $newservice = sw_generate_service(
                    $user_id,
                    $product_id,
                    $service_name,
                    $service_url,
                    $service_type,
                    $invoice_id,
                    $start_date,
                    $end_date,
                    $next_payment_date,
                    $billing_cycle,
                    $status
                );

                if ($newservice) {
                    $service_id_value = $newservice->getServiceId();
                    $details_url = admin_url('admin.php?page=sw-admin&action=service_details&service_id=' . $service_id_value);
                    echo '<p class="success">Service successfully added. <a href="' . esc_url($details_url) . '">View Details</a></p>';
                } else {
                    echo '<p class="error">Failed to add the new service.</p>';
                }
            }
        }
    }

    // Render the form
    sw_render_add_new_service_form();
}



function sw_handle_edit_service_page() {
    echo '<h2>Edit Service</h2>';

    // Check if the form is submitted
    if (isset($_POST['edit_service_submit'])) {
        // Verify nonce for added security
        if (isset($_POST['sw_edit_service_nonce']) && wp_verify_nonce($_POST['sw_edit_service_nonce'], 'sw_edit_service_nonce')) {
            // Check if service_id is present in the URL
            if (isset($_GET['service_id'])) {
                $url_service_id = sanitize_text_field($_GET['service_id']);
                $service = Sw_Service_Database::get_service_by_id($url_service_id);

                // Check if the service exists
                if ($service) {
                    // Initialize an array to store validation errors
                    $errors = array();

                    // Form data validation and processing
                    $user_id = isset($_POST['user_id']) ? absint($_POST['user_id']) : 0;
                    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;

                    // Validate Service Name
                    $service_name = isset($_POST['service_name']) ? sanitize_text_field($_POST['service_name']) : '';
                    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $service_name)) {
                        $errors['service_name'] = 'Service Name should only contain letters, numbers, and spaces.';
                    }

                    // Validate Service Type
                    $service_type = isset($_POST['service_type']) ? sanitize_text_field($_POST['service_type']) : '';
                    if (!empty($service_type) && !preg_match('/^[a-zA-Z0-9\s]+$/', $service_type)) {
                        $errors['service_type'] = 'Service Type should only contain letters, numbers, and spaces.';
                    }
                    // Validate Service URL
                    $service_url = isset($_POST['service_url']) ? esc_url_raw($_POST['service_url']) : '';
                    if (!empty($service_url) && (!filter_var($service_url, FILTER_VALIDATE_URL) || strpos($service_url, ' ') !== false)) {
                        $errors['service_url'] = 'Service URL should be a valid URL without spaces.';
                    }

                    $invoice_id = isset($_POST['invoice_id']) ? sanitize_text_field($_POST['invoice_id']) : '';
                    $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : '';
                    $billing_cycle = isset($_POST['billing_cycle']) ? sanitize_text_field($_POST['billing_cycle']) : '';
                    $next_payment_date = isset($_POST['next_payment_date']) ? sanitize_text_field($_POST['next_payment_date']) : '';
                    $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : '';
                    $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

                    // Check for validation errors before updating
                    if (empty($errors)) {

                        // Update the service using the update_service method
                        $service->setUserId($user_id);
                        $service->setProductId($product_id);
                        $service->setServiceName($service_name);
                        $service->setServiceType($service_type);
                        $service->setServiceUrl($service_url);
                        $service->setInvoiceId($invoice_id);
                        $service->setStartDate($start_date);
                        $service->setBillingCycle($billing_cycle);
                        $service->setNextPaymentDate($next_payment_date);
                        $service->setEndDate($end_date);
                        $service->setStatus($status);

                        // Perform the update
                        $updated = Sw_Service_Database::update_service($service);

                        if ($updated) {
                            echo '<p class="success notice success is-dismissible">Service successfully updated.</p>';
                        } else {
                            echo sw_error_notice( 'Failed to update the service.' );
                        }
                    } else {
                        // Display validation errors
                            sw_error_notice( $errors );
                        
                    }
                } else {
                    // Service with the provided service_id does not exist
                    echo '<div class="error"><p>Service not found.</p></div>';
                }
            }
        } else {
            // Nonce verification failed
            echo '<p class="error">Security check failed. Please try again.</p>';
        }
    }

    // Render the form
    sw_render_edit_service_form();
}