<?php
/**
 * File name  sw_service_api.php
 *
 * @author Callistus
 * Description Service API autheticator functions
 */

defined( 'ABSPATH' ) || exit; // Prevent direct access.

/**
 * Handle API requests for Smart Woo Service.
 *
 * This function is triggered when the 'sw_get_service_api' query parameter is present
 * in the HTTP request. It retrieves and validates the 'email' and 'serviceid' parameters,
 * maps the email to a user ID, and checks authentication and service details.
 *
 * @since   1.0.0
 * @author  Callistus
 */
function smartwoo_service_API_responder() {
	// Check if there is an HTTP request to the endpoint.
	if ( isset( $_GET['sw_get_service_api'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$response = array();

		$email      = isset( $_GET['email'] ) ? sanitize_email( $_GET['email'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$service_id = isset( $_GET['serviceid'] ) ? sanitize_key( $_GET['serviceid'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// Validate email and service_id parameters.
		if ( empty( $email ) || empty( $service_id ) ) {
			$response['error'] = 'Missing or invalid "email" or "serviceid" parameter.';
			http_response_code( 400 );
		} else {
			$user = get_user_by( 'email', $email );

			// Check if user exists
			if ( empty( $user ) ) {
				$response['error'] = 'User not found with the provided email.';
				http_response_code( 404 ); // Not Found
			} else {
				$user_id = $user->ID;

				// Retrieve service details by service_id
				$service = SmartWoo_Service_Database::get_service_by_id( $service_id );

				// Check authentication and service details
				if ( $user_id !== 0 && $service !== false && $user_id === $service->getUserId() ) {
					$status = smartwoo_service_status( $service_id );

					// Prepare the response with status and service details
					$response = array(
						'status'            => $status,
						'service_name'      => $service->getServiceName(),
						'service_id'        => $service->getServiceId(),
						'billing_cycle'     => $service->getBillingCycle(),
						'start_date'        => $service->getStartDate(),
						'next_payment_date' => $service->getNextPaymentDate(),
						'end_date'          => $service->getEndDate(),
					);
				} else {
					// Wrong credentials
					$response['error'] = 'Authentication failed.';
					http_response_code( 401 ); // Unauthorized
				}
			}
		}

		// Output the response as JSON
		header( 'Content-Type: application/json' );

		// Set cache control headers
		header( 'Cache-Control: max-age=7200' ); // Cache for 2 hours

		// Set expiration header to 24 hours ago
		$expiration_time = strtotime( '+24 hours' );
		header( 'Expires: ' . gmdate( 'D, d M Y H:i:s \G\M\T', $expiration_time ) );

		// Set last modified header to 24 hours ago
		$last_modified_time = strtotime( '-24 hours' );
		header( 'Last-Modified: ' . date_i18n( 'D, d M Y H:i:s \G\M\T', $last_modified_time ) );

		echo wp_json_encode( $response );
		exit; // End processing to prevent further output.
	}
}
add_action( 'wp', 'smartwoo_service_API_responder' );
