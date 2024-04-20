<?php
/**
 * File name    :   sw-http.php
 *
 * @author      :   Callistus
 * Description  :   The functions in this file performs a remote GET & POST request to the
 * URL assoiciated with a service, this is useful in situations where you want to take certain actions
 * based on the status of a user's service subscription.
 * In subsequent updates, there will be lot's of improvements and customizations to allow for custom webhooks.
 * @package SmartWooAPIs.
 */

defined( 'ABSPATH' ) || exit; // Prevent direct access.

/**
 * Notify Service URL about the service deactivation.
 * Please note: The API endpoint in this HTTP request is handled by our service manager must use plugin.
 * Find out more about our Service Manager Must use plugin https://callismart.com.ng/service-manager
 *
 * @param object $service SmartWoo_Service object
 */
function smartwoo_deactivate_this_service( SmartWoo_Service $service ) {
	$api_is_enabled = get_option( 'smartwoo_enable_api_feature', 0 );

	if ( ! $api_is_enabled ) {
		return;
	}

	if ( ! empty( $service ) && $service->getServiceType() === 'Web Service' ) {

		// Construct the URL to disable the website
		$service_url = $service->getServiceUrl();
		$service_id  = $service->getServiceId();
		$status      = smartwoo_service_status( $service_id );

		// Confirm that Service is truly Not active.
		if ( $status !== 'Active' || $status !== 'Due for Renewal' || $status !== 'Grace Period' ) {

			$remote_url = $service_url . '?serviceid=' . urlencode( $service_id ) . '&status=Expired';
			$response 	= wp_remote_get( $remote_url );

			// Log the HTTP status code.
			$http_status = wp_remote_retrieve_response_code( $response );
			error_log( 'HTTP Status Code when deactivating ' . $service_id . '= ' . $http_status );

			// Check for errors in the response
			if ( is_wp_error( $response ) ) {
				// Handle error by logging to the error log
				error_log( 'Failed to notify service URL: ' . $response->get_error_message() );
			}
		}
	}
}
// Hook into service cancellation action
add_action( 'smartwoo_service_deactivated', 'smartwoo_deactivate_this_service' );


/**
 * Check and Notify All Expired Service's URL about the service expiration.
 * Please note: The API endpoint in this HTTP request is handled by our service manager must use plugin.
 * Find out more about our Service Manager Must use plugin https://callismart.com.ng/service-manager
 */
function check_and_disable_all_expired_services() {
	$api_is_enabled = get_option( 'smartwoo_enable_api_feature', 0 );

	if ( ! $api_is_enabled ) {
		return;
	}
	
	$services = SmartWoo_Service_Database::get_all_services();

	foreach ( $services as $service ) {
		// Check if the service status is 'Expired' or 'Suspended'
		$service_status = smartwoo_service_status( $service->getServiceId() );

		if ( $service_status === 'Expired' || $service_status === 'Suspended' || $service_status === 'Cancelled' ) {
			// Construct the URL to disable the website
			$service_url = $service->getServiceUrl();
			$service_id  = $service->getServiceId();

			// Construct the remote URL
			$remote_url = $service_url . '?serviceid=' . urlencode( $service_id ) . '&status=Expired';

			// Use WordPress HTTP API to perform the GET request
			$response = wp_remote_get( $remote_url );

			// Check for errors in the response
			if ( is_wp_error( $response ) ) {
				// Handle error by logging to the error log
				error_log( 'Failed to notify service URL: ' . $response->get_error_message() );
			}
		}
	}
}
// Hook to run twice daily
add_action( 'smartwoo_twice_daily_task', 'check_and_disable_all_expired_services' );


/**
 * Notify Service URL about the service Activation.
 * Please note: The API endpoint in this HTTP request is handled by our service manager must use plugin.
 * Find out more about our Service Manager Must use plugin https://callismart.com.ng/service-manager
 *
 * @param object $service SmartWoo_Service object
 */
function check_and_activate_paid_service( SmartWoo_Service $service ) {
	$api_is_enabled = get_option( 'smartwoo_enable_api_feature', 0 );

	if ( ! $api_is_enabled ) {
		return;
	}

	if ( ! empty( $service ) && $service->getServiceType() === 'Web Service' ) {
		// Get service status
		$service_status = smartwoo_service_status( $service->getServiceId() );

		// Make sure service is active
		if ( $service_status === 'Active' ) {
			// Prepare remote get variable
			$user_id     = $service->getUserId();
			$user_email  = get_userdata( $user_id )->user_email;
			$service_id  = $service->getServiceId();
			$service_url = $service->getServiceUrl();

			$remote_url = $service_url . '/access.php?email=' . urlencode( $user_email ) . '&serviceid=' . urlencode( $service_id ) . '&status=' . urlencode( $service_status );

			// Use WordPress HTTP API to perform the GET request
			$response = wp_remote_get( $remote_url );

			// Log the HTTP status code
			$http_status = wp_remote_retrieve_response_code( $response );
			error_log( 'HTTP Status Code when Activating ' . $service_id . '= ' . $http_status );

			// Check for errors in the response
			if ( is_wp_error( $response ) ) {
				// Handle error by logging to the error log
				error_log( 'Failed to notify service URL: ' . $response->get_error_message() );
			}
		}
	}
}
// Hook to run when edit to active in admin page
add_action( 'smartwoo_service_active', 'check_and_activate_paid_service' );
// Hook to run when renewed Due service
add_action( 'smartwoo_service_renewed', 'check_and_activate_paid_service' );
// Hook to run when reactivated
add_action( 'smartwoo_expired_service_activated', 'check_and_activate_paid_service' );
