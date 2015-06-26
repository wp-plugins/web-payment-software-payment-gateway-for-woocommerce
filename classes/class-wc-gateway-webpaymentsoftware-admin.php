<?php
/**
*@author DualCube
*@package Web Payment Software
*/
class PayGate_Admin_Refund extends WC_Settings_API {
	
	private $orderID;
  private $order;
	private $api_merchant_id;
	private $api_merchant_key;
	private $testmode;
	private $host;
	private $debugon; 
  
	public function __construct() {
		add_action('woocommerce_order_status_changed', array($this, 'perform_operation_admin'), 10, 3);
	}

	
	function perform_operation_admin($order_id, $prev_status, $new_status) {
		global $woocommerce;
		$order = new WC_Order( $order_id );
		if( $prev_status == 'cancelled' ) {
			$_POST['order_status'] = 'cancelled';
			$order->add_order_note('Order Status cannot be changed as this Order was marked as cancelled before and removed from settlement queue.');
			return;
		}
		$this->id = 'paygate';
		$mark_status = $this->get_option('order_status_mark');
		$void_status = $this->get_option('order_status_void');
		$order = new WC_Order( $order_id );
		$this->orderID = $order_id;
  	$this->order = new WC_Order( $order_id );
  	$paygate_transaction_type = get_post_meta($order_id, '_orginal_paygate_transaction_type', true);
		if( $new_status == $mark_status && $paygate_transaction_type == 'authonly' ) {
			if( $prev_status == 'on-hold' || $prev_status == 'processing' ) {
				$trans_type = 'mark';
				$this->do_transaction($trans_type);
			}
		} else if(is_array($void_status) && in_array($new_status, $void_status)) {
			$trans_type = 'void';
			$this->do_transaction($trans_type);
		}
		if($new_status == $mark_status && $paygate_transaction_type == 'authcapture') {
			$order->add_order_note('This Order is already Marked');
		}
  }
  
	public function do_transaction($trans_type) {
		global $woocommerce_errors;
		$this->api_merchant_id = $this->get_option('merchant_id');
		$this->api_merchant_key = $this->get_option('merchant_key');
		$this->testmode = $this->get_option('testmode');
		$this->host = $this->get_option('host');
		$this->debugon = $this->get_option('debugon');
		$testmode = ( 'yes' == $this->testmode ) ? '1' : '0';
		$request = new WC_Paygate_API_Request($this->api_merchant_id, $this->api_merchant_key, $trans_type, $this->host, $this->testmode);
		$response = $request->make_api_call($this->order, $this->debugon, 1);
	}
}