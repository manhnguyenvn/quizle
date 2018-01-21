<?php

class FacebookApi
{	
	public function GetUserInformation($access_token) {
		$url = 'https://graph.facebook.com/v2.5/me?fields=id,name,gender,email&access_token='. $access_token;
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch); 
		if($http_code != '200')
			throw new Exception('UNAUTHORIZED_REQUEST', 1);
		
		$user_data = array();
		$user_data['user_thirdparty_id'] = $data['id'];
		if(array_key_exists('email', $data))
			$user_data['user_email'] = $data['email'];
		else
			$user_data['user_email'] = NULL;
		$user_data['user_full_name'] = $data['name'];
		$user_data['user_picture_url'] = 'http://graph.facebook.com/v2.5/' . $data['id'] . '/picture';
		$user_data['registration_source'] = 'FB';
		if(!isset($data['gender']))
			$user_data['user_gender'] = NULL;
		else
			$user_data['user_gender'] = ($data['gender'] == 'male' ? 'M' : 'F');

		return $user_data;
	}
}

?>