<?php

class PaypalApi 
{
	private $url;

	public function __construct($is_sandbox) {
		if($is_sandbox == 1)
			$this->url = 'https://api-3t.sandbox.paypal.com/nvp';
		else
			$this->url = 'https://api-3t.paypal.com/nvp';
	}

	public function SetExpressCheckout($paypal_api_username, $paypal_api_password, $paypal_api_signature, $price_per_unit, $quantity, $currency, $payment_name, $logo_image_url, $success_url, $cancel_url, $language_code, $site_name) {
		$url_parameters = array();
		$url_parameters['METHOD'] = 'SetExpressCheckout';
		$url_parameters['USER'] = $paypal_api_username;
		$url_parameters['PWD'] = $paypal_api_password;
		$url_parameters['SIGNATURE'] = $paypal_api_signature;
		$url_parameters['VERSION'] = 115.0;
		$url_parameters['LOCALECODE'] = $language_code;
		$url_parameters['RETURNURL'] = $success_url;
		$url_parameters['CANCELURL'] = $cancel_url;
		$url_parameters['NOSHIPPING'] = 1;
		$url_parameters['ALLOWNOTE'] = 0;
		$url_parameters['LOGOIMG'] = $logo_image_url;
		$url_parameters['BRANDNAME'] = $site_name;
		$url_parameters['MAXAMT'] = $price_per_unit*$quantity;
		$url_parameters['PAYMENTREQUEST_0_AMT'] = $price_per_unit*$quantity;
		$url_parameters['PAYMENTREQUEST_0_CURRENCYCODE'] = $currency;
		$url_parameters['PAYMENTREQUEST_0_PAYMENTACTION'] = 'Sale';
		$url_parameters['L_PAYMENTREQUEST_0_NAME0'] = $payment_name;
		$url_parameters['L_PAYMENTREQUEST_0_AMT0'] = $price_per_unit;
		$url_parameters['L_PAYMENTREQUEST_0_QTY0'] = $quantity;

		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $this->url);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($url_parameters));	
		parse_str(curl_exec($ch), $data);	
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);		
		if($http_code != 200) 
			throw new Exception('Error : Paypal SetExpressCheckout Api Request Failed(1)', 2);
		else {
			if($data['ACK'] == 'Success')
				return $data['TOKEN'];
			else
				throw new Exception('Error : Paypal SetExpressCheckout Api Request Failed(2)', 2);
		}
	}

	public function GetExpressCheckoutDetails($paypal_api_username, $paypal_api_password, $paypal_api_signature, $token) {
		$url_parameters = array();
		$url_parameters['METHOD'] = 'GetExpressCheckoutDetails';
		$url_parameters['USER'] = $paypal_api_username;
		$url_parameters['PWD'] = $paypal_api_password;
		$url_parameters['SIGNATURE'] = $paypal_api_signature;
		$url_parameters['VERSION'] = 115.0;
		$url_parameters['TOKEN'] = $token;

		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $this->url);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($url_parameters));	
		parse_str(curl_exec($ch), $data);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);		
		if($http_code != 200) 
			throw new Exception('Error : Paypal GetExpressCheckoutDetails Api Request Failed(1)', 2);
		else {
			if($data['ACK'] == 'Success')
				return [ 'checkout_status' => $data['CHECKOUTSTATUS'], 'price_per_unit' => $data['L_PAYMENTREQUEST_0_AMT0'], 'quantity' => $data['L_PAYMENTREQUEST_0_QTY0'], 'currency' => $data['CURRENCYCODE'], 'payment_name' => $data['L_PAYMENTREQUEST_0_NAME0'] ];
			else
				throw new Exception('Error : Paypal GetExpressCheckoutDetails Api Request Failed(2)', 2);
		}
	}

	public function DoExpressCheckout($paypal_api_username, $paypal_api_password, $paypal_api_signature, $payer_id, $token, $price_per_unit, $quantity, $currency, $payment_name) {
		$url_parameters = array();
		$url_parameters['METHOD'] = 'DoExpressCheckoutPayment';
		$url_parameters['USER'] = $paypal_api_username;
		$url_parameters['PWD'] = $paypal_api_password;
		$url_parameters['SIGNATURE'] = $paypal_api_signature;
		$url_parameters['VERSION'] = 115.0;
		$url_parameters['TOKEN'] = $token;
		$url_parameters['PAYERID'] = $payer_id;
		$url_parameters['PAYMENTREQUEST_0_AMT'] = $price_per_unit*$quantity;
		$url_parameters['PAYMENTREQUEST_0_CURRENCYCODE'] = $currency;
		$url_parameters['PAYMENTREQUEST_0_PAYMENTACTION'] = 'Sale';
		$url_parameters['L_PAYMENTREQUEST_0_NAME0'] = $payment_name;
		$url_parameters['L_PAYMENTREQUEST_0_AMT0'] = $price_per_unit;
		$url_parameters['L_PAYMENTREQUEST_0_QTY0'] = $quantity;

		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $this->url);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($url_parameters));	
		parse_str(curl_exec($ch), $data);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);		
		if($http_code != 200) 
			throw new Exception('Error : Paypal DoExpressCheckout Api Request Failed(1)', 2);
		else {
			if($data['ACK'] == 'Success') {
				$response = [ 'transaction_id' => $data['PAYMENTINFO_0_TRANSACTIONID'], 'payment_status' => $data['PAYMENTINFO_0_PAYMENTSTATUS'], 'amount' => $data['PAYMENTINFO_0_AMT'], 'currency' => $data['PAYMENTINFO_0_CURRENCYCODE'] ];
				if($response['payment_status'] == 'Pending')
					$response['transaction_status_message'] = $data['PAYMENTINFO_0_PENDINGREASON'];
				else if($response['payment_status'] == 'Reversed')
					$response['transaction_status_message'] = $data['PAYMENTINFO_0_REASONCODE'];
				else if($response['payment_status'] == 'Completed-Funds-Held')
					$response['transaction_status_message'] = $data['PAYMENTINFO_0_HOLDDECISION'];
				else
					$response['transaction_status_message'] = '';

				return $response;
			}
			else
				throw new Exception('Error : Paypal DoExpressCheckout Api Request Failed(2)', 2);
		}
	}

	public function GetTransactionDetails($paypal_api_username, $paypal_api_password, $paypal_api_signature, $payment_transaction_id) {
		$url_parameters = array();
		$url_parameters['METHOD'] = 'GetTransactionDetails';
		$url_parameters['USER'] = $paypal_api_username;
		$url_parameters['PWD'] = $paypal_api_password;
		$url_parameters['SIGNATURE'] = $paypal_api_signature;
		$url_parameters['VERSION'] = 115.0;
		$url_parameters['TRANSACTIONID'] = $payment_transaction_id;

		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $this->url);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($url_parameters));	
		parse_str(curl_exec($ch), $data);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);		
		if($http_code != 200) 
			throw new Exception('Error : Paypal GetTransactionDetails Api Request Failed(1)', 2);
		else {
			if($data['ACK'] == 'Success') {
				$response = [ 'payment_status' => $data['PAYMENTSTATUS'] ];
				if($response['payment_status'] == 'Pending')
					$response['transaction_status_message'] = $data['PENDINGREASON'];
				else if($response['payment_status'] == 'Reversed')
					$response['transaction_status_message'] = $data['REASONCODE'];
				else
					$response['transaction_status_message'] = '';

				return $response;
			}
			else
				throw new Exception('Error : Paypal GetTransactionDetails Api Request Failed(2)', 2);
		}
	}
}

?>