<?php
/**
 * Handles all responses from the Web Payment Software API
 *
 * Parses XML received by Web Payment Software API
 *
 * @since 1.0
 * @extends SimpleXMLElement
*/


class WC_Paygate_API_Response {
	
	public function __construct() {
	}
	
	// parse response
	public function parse_api_response($response, $order, $transType, $debugon, $admin_access) {
		global $woocommerce, $woocommerce_errors;
		$content = $response['body'];
		$datas = new SimpleXmlElement($content);
		$final_response = json_decode(json_encode((array) $datas), 1);
		if($debugon) {
		//doPaygateLOG( "RESPONSE RAW: " . json_encode( $content ) . "\n\nRESPONSE:" . json_encode( $final_response ) );
		}
		// Retreive response
		if ( '00' == $final_response['response_code']) {
			// Successful payment
			if(!$admin_access) {
				$order->add_order_note( __( 'Web Payment Software payment completed', 'wc-paygate' ) . ' ( Auth Response Text: ' . $final_response['auth_response_text'] . ' & Response Text: '. $final_response['response_text']. ')' );
			}
			update_post_meta($order->id, '_paygate_order_id', $final_response['order_id']);
			if(!$admin_access) {
				update_post_meta($order->id, '_orginal_paygate_transaction_type', $transType);
				update_post_meta($order->id, '_approval_code_for_'.$transType, $final_response['approval_code']);
			}
			update_post_meta($order->id, '_paygate_transaction_type', $transType);
			if( $transType == 'mark' ) {
				$order->add_order_note('Current Transaction Status : '. $transType . '  that means your transaction is marked for settlement. ');
			} 
			if( $transType == 'void' ) {
				$order->add_order_note('Current Transaction Status : '. $transType . '  that means your transaction is removed from the WPS settlement queue. ');
			}
			if($transType == 'authonly' || $transType == 'authonly') {
				$order->add_order_note('Address Match Record Code : " ' .$final_response['avs_result_code']. ' " that means ' . $final_response['auth_response_text'] );
			}
			return true;
		} else {
			if($debugon) doPaygateLOG( "Web Payment Software ERROR:\n order id :" .$order->id."\nresponse_code:" . $final_response['response_code'] . "\nresponse_reasib_text:" .$final_response['auth_response_text'] .' Or '. $final_response['response_text']);
			$cancelNote = __( 'Web Payment Software Payment Failed', 'wc-paygate' )  . __( ' , Payment was rejected due to an error', 'wc-paygate' ) . ': "' . $final_response['auth_response_text'] . ' '. $final_response['response_text'].'"';
			$order->add_order_note( $cancelNote );
			if(!$admin_access) {
				$woocommerce->add_error( $cancelNote );
			} else {
				$woocommerce_errors[] = json_encode($final_response) ;
			}
			return false;
		}
	}
}