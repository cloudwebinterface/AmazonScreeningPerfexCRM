<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Apitest extends CI_Controller
{
	public function index() {
		$user 	= $this->input->get('user');
		$key 	= $this->input->get('key');

		if ( !$user ) { exit; }
		if ( !$key ) { exit; }
	}

	public function authenticate($user = '', $key = '') {

		$basic_token = base64_encode($user . ":" . $key );
		$host = AB_API_HOST . '/authenticate';
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => $host,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_HEADER => array('Content-Length: 0'),
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_POSTFIELDS => json_encode(array()),
			CURLOPT_HTTPHEADER => array(
				"Authorization: Basic " . $basic_token
			),
			CURLOPT_VERBOSE => true // debugging purpose
		));

		$response = curl_exec($curl);

		curl_close($curl);

		echo $response;

	}

	public function searches() {
		json_encode($_SERVER);
	}
}