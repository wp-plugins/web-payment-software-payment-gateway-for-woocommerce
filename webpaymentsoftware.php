<?php
/**
 * Plugin Name:  Web Payment Software Payment Gateway for WooCommerce
 * Description: Adds the WPS Paygate Gateway to your WooCommerce site
 * Plugin URI: http://dualcube.com
 * Description: A simple, affordable solution for your business to accept payments online through Woocommerce.
 * Author: Dualcube
 * Version: 1.0.1
 * Author URI: http://dualcube.com
 *
 * Copyright: (c) 2014 DualCube (admin@dualcube.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package   WC-Paygate
 * @author    DualCube
 * @Category  Payment-Gateways
 * @copyright Copyright (c) 2014 DualCube
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );


add_action('plugins_loaded', 'woocommerce_paygate_init', 0);

function woocommerce_paygate_init() {

	if (!class_exists('WC_Payment_Gateway')) return;

	if(!defined('PAYGATE_DIR')) {
		define('PAYGATE_DIR', untrailingslashit(plugin_dir_path(__FILE__)) . '/');
	}
	if(!defined('PAYGATE_URL')) {
		define('PAYGATE_URL', plugins_url( '' , __FILE__ ) . '/' );
	}

	include_once( 'classes/class-wc-gateway-webpaymentsoftware.php' );
	include_once( 'classes/class-wc-gateway-webpaymentsoftware-request.php' );
	include_once( 'classes/class-wc-gateway-webpaymentsoftware-response.php' );
	include_once( 'classes/class-wc-gateway-webpaymentsoftware-admin.php' );
	new PayGate_Admin_Refund();
	/**
	 * Add the gateway to woocommerce
	 **/
	function add_paygate_gateway( $methods ) {
		$methods[] = 'WC_Paygate';
		return $methods;
	}

	add_filter('woocommerce_payment_gateways', 'add_paygate_gateway' );
}


function load_paygate_translation() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'paygate' );
  load_textdomain( 'wc-paygate',  trailingslashit(dirname( __FILE__ ) ) . "languages/webpaymentsoftware-$locale.mo" );
}

// Load translation files
add_action( 'init', 'load_paygate_translation' );


/**
 * Checks if required PHP extensions are loaded. Adds an admin notice if either check fails
 *
 * @since  1.0
 */
function check_dependencies() {

  $missing_extensions = get_missing_dependencies();

  if ( count( $missing_extensions ) > 0 ) {

    $message = sprintf(
      _n( 'WooCommerce Paygate Gateway requires the %s PHP extension to function.  Contact your host or server administrator to configure and install the missing extension.',
      'WooCommerce Web Payment Software Gateway requires the following PHP extensions to function: %s.  Contact your host or server administrator to configure and install the missing extensions.',
      count( $missing_extensions ), 'wc-paygate' ),
      '<strong>' . implode( ', ', $missing_extensions ) . '</strong>'
    );

    echo '<div class="error"><p>' . $message . '</p></div>';
  }

}


/**
 * Gets the string name of any required PHP extensions that are not loaded
 *
 * @since 1.0
 * @return array
 */
function get_missing_dependencies() {
  $dependencies = array( 'SimpleXML' );
  $missing_extensions = array();

  foreach ( $dependencies as $ext ) {

    if ( ! extension_loaded( $ext ) )
      $missing_extensions[] = $ext;
  }

  return $missing_extensions;
}

if( is_admin() && ! defined( 'DOING_AJAX' ) ) {
  add_action( 'admin_notices', 'check_dependencies' );
  add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'paygate_action_links' );
}

if(is_admin()) {
	add_action('woocommerce_order_status_changed', 'perform_operation_refunded_paygate', 10, 3);
}

function perform_operation_refunded_paygate($order_id, $prev_status, $new_status) {
	
}

add_filter('woocommerce_available_payment_gateways', 'get_all_available_payment_gateway');
function get_all_available_payment_gateway($available_gateways) {
	//print_r($available_gateways);
	foreach($available_gateways as $key_gateway => $gatewayvalue ) {
		if( $key_gateway == 'paygate' &&  'no' == get_option( 'woocommerce_force_ssl_checkout' ) ) {
			unset($available_gateways[$key_gateway]);
		}
	}
	return $available_gateways;
}

/**
 * paygate_action_links function.
 *
 * @access public
 * @param mixed $links
 * @return void
 */
function paygate_action_links( $links ) {

  $plugin_links = array(
    '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_paygate' ) . '">' . __( 'Configure', 'wc-paygate' ) . '</a>'
  );

  return array_merge( $plugin_links, $links );
}


/**
 * Write to log file
 */
function doPaygateLOG($str) {
  $file = PAYGATE_DIR . 'log/webpaymentsoftware.log';
  
  if(file_exists($file)) {
    // Open the file to get existing content
    $current = file_get_contents($file);
    // Append a new content to the file
    $current .= "$str" . "\r\n";
    $current .= "-------------------------------------\r\n";
  } else {
    $current = "$str" . "\r\n";
    $current .= "-------------------------------------\r\n";
  }
  // Write the contents back to the file
  file_put_contents($file, $current);
}
