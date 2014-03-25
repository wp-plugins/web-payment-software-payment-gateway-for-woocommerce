<?php
/**
 * Web Payment Software API Request Class
 *
 * Handles all requests to the Web Payment Software API
 *
 * @since 1.0
 */
 
class WC_Paygate_API_Request {


	/** @var string API Merchant ID */
	private $api_merchant_id;

	/** @var string API Merchant key */
	private $api_merchant_key;
	
	/** @var boolean test_mode */
	private $test_mode;
	
	/** @var string trans_type */
	private $trans_type;
	
	/** @var string trans_type */
	private $host;
	


	/**
	 * merchant information
	 *
	 * @since 1.0
	 * @param string $api_merchant_id required
	 * @param string $api_merchant_key required
	 * @return \WC_Paygate_API_Request
	 */
	public function __construct( $api_merchant_id, $api_merchant_key, $trans_type, $host, $test_mode = "FALSE" ) {
		$this->api_merchant_id  = $api_merchant_id;
		$this->api_merchant_key = $api_merchant_key;
		$this->test_mode = $test_mode;
		$this->trans_type = $trans_type;
		$this->host = $host;
	}
	
	/**
	 * Make the api request with order details
	 *
	 * @since 1.0
	 * @param object $order required
	 * @return \WC_Paygate_API_Request
	 */
	public function make_api_call($order, $debugon, $admin_access = 0) {
	  global $woocommerce;
	  try {
	  	
	  	$authnet_request = array(
				"merchant_id"          => $this->api_merchant_id,
				"merchant_key"         => $this->api_merchant_key,
				"trans_type"           => $this->trans_type,
			);
			if ($this->trans_type == 'unmark' || $this->trans_type == 'void' || $this->trans_type == 'mark' || $this->trans_type == 'postauth') {
					$authnet_request['order_id'] = get_post_meta($order->id, '_paygate_order_id', true);
					if(!get_post_meta($order->id, '_paygate_order_id', true)) {
						$order->add_order_note('This order does not have any "Order ID" saved After authonly or authcapture request... ');
					}
	  	} else {
					$authnet_request['amount'] 							 = $order->order_total;
					$authnet_request['tax']									 = woocommerce_format_decimal( $order->order_tax, 2 );
					$authnet_request['cc_number']						 = $_POST['ccnum'];
					$authnet_request['cc_exp_month']         = $_POST['expmonth'];
					$authnet_request['cc_exp_year']					 = $_POST['expyear'];
					$authnet_request['cc_cvv']          		 = ( isset( $_POST['cvv'] ) ) ? $_POST['cvv'] : '';
					$authnet_request['cc_company']         	 = $order->billing_company;
					$authnet_request['cc_name']          		 = $order->billing_first_name .' '. $order->billing_last_name;
					$authnet_request['cc_address']           = $order->billing_address_1;
					$authnet_request['cc_city']              = $order->billing_city;
					$authnet_request['cc_state']             = $order->billing_state;
					$authnet_request['cc_zip']               = $order->billing_postcode;
					$authnet_request['cc_country']           = $order->billing_country;
					$authnet_request['cc_phone']             = $order->billing_phone;
					$authnet_request['cc_email']             = $order->billing_email;
			}
			$authnet_request['ip_address']        	   = $_SERVER['REMOTE_ADDR'];
			$authnet_request['test_mode']       		   = $this->test_mode;
				
			// Send request
			//$woocommerce->add_error( __( 'Payment error', 'wc-paygate' ) . ': ' . json_encode($authnet_request) );
			$response = wp_remote_post( $this->host, array(
				'method'    => 'POST /gateway/card HTTP/1.0',
				'body'      => $authnet_request,
				'timeout'   => 70,
				'sslverify' => false,
			) );
			if ( is_wp_error( $response ) ) throw new Exception( __( 'There was a problem connecting to the payment gateway.', 'wc-paygate' ) );

			if ( empty( $response['body'] ) ) throw new Exception( __( 'Empty Authorize.net response.', 'wc-paygate' ) );
			
			$parse_response = new WC_Paygate_API_Response();
			$parse_response = $parse_response->parse_api_response($response, $order, $this->trans_type, $debugon, $admin_access);
			if($parse_response) return true;
		} catch( Exception $e ) {
			$woocommerce->add_error( __( 'Connection error:', 'wc-paygate' ) . ': "' . $e->getMessage() . '"' );
			return false;
		}
    return false;
	}
	
	
}