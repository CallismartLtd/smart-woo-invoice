<?php
/**
 * Plugin Name: Smart Woo Service Invoicing
 * Description: Integrate powerful service subscriptions and invoicing directly into your online store.
 * Version: 1.0.51
 * Author: Callistus Nwachukwu
 * Author URI: https://callismart.com.ng/callistus
 * Plugin URI: https://callismart.com.ng/smart-woo
 * Requires at least: 6.0
 * Tested up to: 6.5.4
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * WC requires at least: 7.0
 * WC tested up to: 8.9.2
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html  
 * Text Domain: smart-woo-service-invoicing
 * 
 * @package SmartWoo
 */

defined( 'ABSPATH' ) || exit; // Prevent direct access.

if ( defined( 'SMARTWOO' ) ) {
	return;
}

define( 'SMARTWOO', 'Smart Woo Service Invoicing' );

if ( ! defined( 'SMARTWOO_PATH' ) ) {
	/** Smart Woo Path */
	define( 'SMARTWOO_PATH', __DIR__ . '/' );
}	

if ( ! defined( 'SMARTWOO_FILE' ) ) {
	/** Main file */
	define( 'SMARTWOO_FILE', __FILE__ );
}

// Define the Smart Woo Directory URL.
if ( ! defined( 'SMARTWOO_DIR_URL' ) ) {
	define( 'SMARTWOO_DIR_URL', plugin_dir_url( __FILE__ ) );
}	

// Define the Smart Woo versions.
if ( ! defined( 'SMARTWOO_VER' ) ) {
	define( 'SMARTWOO_VER', '1.0.51' );
}

if ( ! defined( 'SMARTWOO_DB_VER' ) ) {
	define( 'SMARTWOO_DB_VER', '1.0.51' );
}

// Load core and config files.
require_once SMARTWOO_PATH . 'includes/class-sw-install.php';
require_once SMARTWOO_PATH . 'includes/class-sw-config.php';
SmartWoo_Config::instance();
