<?php
/**
 * WooCommerce Web Payment Software
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to admin@dualcube.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Web Payment Software to newer
 * versions in the future. 
 *
 * @package   WC-Paygate/Gateway
 * @author    DualCube
 * @copyright Copyright (c) 2014, DualCube
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Paygate extends WC_Payment_Gateway {

	/**
	 * Initialize the gateway
	 */
	public function __construct() {

		$this->id                 = 'paygate';
		$this->method_title       = __( 'Web Payment Software', 'wc-paygate' );
		$this->method_description = __( 'Accept credit card payments from customers through Web Payment Software.', 'wc-paygate' );

		$this->supports   = array( 'products' );

		$this->has_fields = true;

		$this->icon = apply_filters( 'wc_paygate_icon', PAYGATE_URL . 'assets/images/cards.png' );

		// Load the form fields
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();
		
		// Define user set variables
		foreach ( $this->settings as $setting_key => $setting ) {
			$this->$setting_key = $setting;
		}

		// pay page fallback
		add_action( 'woocommerce_receipt_' . $this->id, create_function( '$order', 'echo "<p>" . __( "Thank you for your order.", "wc-paygate" ) . "</p>";' ) );

		add_action( 'admin_notices', array( $this,'paygate_ssl_check' ) );

		// Save settings
		if ( is_admin() ) {
			add_action( 'woocommerce_update_options_payment_gateways',              array( $this, 'process_admin_options' ) ); // WC < 2.0
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) ); // WC >= 2.0
		}
	}

	
	/**
	 * Check if SSL is enabled and notify the user
	 */
	public function paygate_ssl_check() {

		if ( 'no' == get_option( 'woocommerce_force_ssl_checkout' ) && 'yes' == $this->enabled ) {

			echo '<div class="error"><p>'.sprintf( __( 'Web Payment Software is enabled But Gateway is not available in fronted as the <a href="%s">force SSL option</a> is disabled; your checkout is not secure! Please enable SSL and ensure your server has a valid SSL certificate.', 'wc-paygate' ), admin_url( 'admin.php?page=woocommerce' ) ) . '</p></div>';

		}
	}


	/**
	 * Initialize Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		$defaults = array('pending' => 'pending','failed' => 'failed','on-hold' => 'on-hold','processing' => 'processing','completed' => 'completed','refunded' => 'refunded' ,'cancelled' => 'cancelled');
		$this->form_fields = array(

			'enabled' => array(
				'title'   => __( 'Enable / Disable', 'wc-paygate' ),
				'label'   => __( 'Enable this gateway', 'wc-paygate' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),

			'title' => array(
				'title'    => __( 'Title', 'wc-paygate' ),
				'type'     => 'text',
				'desc_tip' => __( 'Payment method title that the customer will see during checkout.', 'wc-paygate' ),
				'default'  => __( 'Credit card', 'wc-paygate' ),
			),

			'description' => array(
				'title'    => __( 'Description', 'wc-paygate' ),
				'type'     => 'textarea',
				'desc_tip' => __( 'Payment method description that the customer will see during checkout.', 'wc-paygate' ),
				'default'  => __( 'Pay securely using your credit card.', 'wc-paygate' ),
			),

			'merchant_id' => array(
				'title'    => __( 'Merchant ID', 'wc-paygate' ),
				'type'     => 'password',
				'desc_tip' => __( 'This is the Merchant ID supplied by Web Payment Software', 'wc-paygate' ),
				'default'  => '',
			),

			'merchant_key' => array(
				'title'    => __( 'Merchant Key', 'wc-paygate' ),
				'type'     => 'password',
				'desc_tip' => __( 'This is the Transaction Key supplied by Web Payment Software', 'wc-paygate' ),
				'default'  => '',
			),

			'trans_type' => array(
				'title'    => __( 'Transaction Mode', 'wc-paygate' ),
				'type'     => 'select',
				'desc_tip' => __( 'Select which sale method to use. Authorize Only will authorize the customers card for the purchase amount only.  Authorize &amp; Capture will authorize the customer\'s card and collect funds.', 'wc-paygate' ),
				'options'  => array(
					'authonly'    => 'Authorize Only',
					'authcapture' => 'Authorize &amp; Mark',
				),
				'default'  => 'authcapture',
			),
			
			'order_status_mark' => array(
				'title'    => __( 'Set Order Status For Mark', 'wc-paygate' ),
				'type'     => 'select',
				'desc_tip' => __( 'Select which order status to use for Mark transaction type.', 'wc-paygate' ),
				'options'  => $defaults,
				'default'  => 'completed',
			),

			'order_status_void' => array(
				'title'    => __( 'Set Order Status For Void', 'wc-paygate' ),
				'type'     => 'multiselect',
				'desc_tip' => __( 'Select which order status to use for Void transaction type.', 'wc-paygate' ),
				'options'  => $defaults,
				'default'  => array('on-hold' => 'on-hold' , 'cancelled' => 'cancelled'),
			),
			
			'host' => array(
				'title'    => __( 'Host', 'wc-paygate' ),
				'type'     => 'text',
				'desc_tip' => __( 'URL for Web Payment Software gateway processor.', 'wc-paygate' ),
				'default'  => 'https://secure.web-payment-software.com',
			),

			'cardtypes' => array(
				'title'    => __( 'Accepted Cards', 'wc-paygate' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'css'      => 'width: 350px;',
				'desc_tip' => __( 'Select which card types to accept.', 'wc-paygate' ),
				'options' => array(
					'MasterCard'       => 'MasterCard',
					'Visa'             => 'Visa',
					'Discover'         => 'Discover',
					'American Express' => 'American Express',
				),
				'default' => array( 'MasterCard', 'Visa', 'Discover', 'American Express' ),
			),

			'cvv' => array(
				'title'   => __( 'CVV', 'wc-paygate' ),
				'label'   => __( 'Require customer to enter credit card CVV code', 'wc-paygate' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),

			'testmode' => array(
				'title'       => __( 'Web Payment Software Test Mode', 'wc-paygate' ),
				'label'       => __( 'Enable Test Mode', 'wc-paygate' ),
				'type'        => 'checkbox',
				'description' => __( 'Place the payment gateway in test mode.', 'wc-paygate' ),
				'default'     => 'no',
			),

			'debugon' => array(
				'title'       => __( 'Logging', 'wc-paygate' ),
				'label'       => __( 'Enable Logging', 'wc-paygate' ),
				'type'        => 'checkbox',
				'description' => __( 'Enable logging of request and responses in <strong>log/paygate.log</strong>.', 'wc-paygate' ),
				'default'     => 'no',
			),

		);
	}


	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 */
	public function admin_options() {
		?>
		<h3><?php _e( 'Web Payment Software','wc-paygate' ); ?></h3>
		<p><?php _e( 'Web Payment Software works by adding credit card fields on the checkout and then sending the details to Web Payment Software for verification.', 'wc-paygate' ); ?></p>
		<table class="form-table">
			<?php $this->generate_settings_html(); ?>
		</table><!--/.form-table-->
		<?php
	}


	/**
	 * Payment fields for Paygate.
	 */
	public function payment_fields() {
		?>
		<fieldset>

			<p class="form-row form-row-first">
				<label for="ccnum"><?php _e( 'Credit Card number', 'wc-paygate' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" id="ccnum" name="ccnum" />
			</p>

			<p class="form-row form-row-last">
				<label for="cardtype"><?php _e( 'Card type', 'wc-paygate' ); ?> <span class="required">*</span></label>
				<select name="cardtype" id="cardtype" class="woocommerce-select">
					<?php foreach ( $this->cardtypes as $type ) : ?>
						<option value="<?php echo $type ?>"><?php _e($type, 'wc-paygate'); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<div class="clear"></div>

			<p class="form-row form-row-first">
				<label for="cc-expire-month"><?php _e( 'Expiration date', 'wc-paygate' ); ?> <span class="required">*</span></label>
				<select name="expmonth" id="expmonth" class="woocommerce-select woocommerce-cc-month">
					<option value=""><?php _e( 'Month', 'wc-paygate' ); ?></option>
					<?php foreach ( range( 1, 12 ) as $month ) : ?>
						<option value="<?php echo $month; ?>"><?php printf( '%02d', $month ); ?></option>
					<?php endforeach; ?>
				</select>
				<select name="expyear" id="expyear" class="woocommerce-select woocommerce-cc-year">
					<option value=""><?php _e( 'Year', 'wc-paygate' ) ?></option>
					<?php
						$years = array();
						for ( $i = date( 'y' ); $i <= date( 'y' ) + 15; $i++ ) {
							printf( '<option value="20%u">20%u</option>', $i, $i );
						}
					?>
				</select>
			</p>
			<?php if ( 'yes' == $this->cvv ) : ?>

			<p class="form-row form-row-last">
				<label for="cvv"><?php _e( 'Card security code', 'wc-paygate' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" id="cvv" name="cvv" maxlength="4" style="width:45px" />
			</p>
			<?php endif; ?>

			<div class="clear"></div>
		</fieldset>
		<?php
	}


	/**
	 * Process the payment and return the result
	 */
	public function process_payment( $order_id ) {
		global $woocommerce;

		$order = new WC_Order( $order_id );
		
		$testmode = ( 'yes' == $this->testmode ) ? '1' : '0';
		$debugon = ( 'yes' == $this->debugon ) ? '1' : '0';
		$request = new WC_Paygate_API_Request($this->merchant_id, $this->merchant_key, $this->trans_type, $this->host, $testmode);
		
		$response = $request->make_api_call($order, $debugon);
		
		// Payment Processing complete completion
		if($response) {
			$order->payment_complete();
      // Return thank you redirect
      return array(
        'result'   => 'success',
        'redirect' => $this->get_return_url( $order ),
      );
    } 
	}


	/**
	 * Validate payment form fields
	 */
	public function validate_fields() {
		global $woocommerce;

		$cardType            = $this->get_post( 'card_type' );
		$cardNumber          = $this->get_post( 'ccnum' );
		$cardCSC             = $this->get_post( 'cvv' );
		$cardExpirationMonth = $this->get_post( 'expmonth' );
		$cardExpirationYear  = $this->get_post( 'expyear' );

		if ( 'yes' == $this->cvv ){
			//check security code
			if ( ! ctype_digit( $cardCSC ) ) {
				$woocommerce->add_error( __( 'Card security code is invalid (only digits are allowed)', 'wc-paygate' ) );
				return false;
			}

			if ( ( strlen( $cardCSC ) != 3 && in_array( $cardType, array('Visa', 'MasterCard', 'Discover', 'Diners', 'JCB' ) ) ) || ( strlen( $cardCSC ) != 4 && $cardType == 'American Express' ) ) {
				$woocommerce->add_error( __( 'Card security code is invalid (wrong length)', 'wc-paygate' ) );
				return false;
			}
		}

		//check expiration data
		$currentYear = date( 'Y' );

		if ( ! ctype_digit( $cardExpirationMonth ) || ! ctype_digit( $cardExpirationYear ) ||
			$cardExpirationMonth > 12 ||
			$cardExpirationMonth < 1 ||
			$cardExpirationYear < $currentYear ||
			$cardExpirationYear > $currentYear + 20 ) {

			$woocommerce->add_error( __( 'Card expiration date is invalid', 'wc-paygate' ) );
			return false;

		}

		//check card number
		$cardNumber = str_replace( array( ' ', '-' ), '', $cardNumber );

		if ( empty( $cardNumber ) || ! ctype_digit( $cardNumber ) ) {
			$woocommerce->add_error( __( 'Card number is invalid', 'wc-paygate' ) );
			return false;
		}

		return true;
	}


	/**
	 * Get post data if set
	 */
	private function get_post( $name ) {
		if ( isset( $_POST[ $name ] ) ) {
			return $_POST[ $name ];
		}

		return null;
	}
	
}

