<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Functions:
 * get_basic_token
 * generate_api_token
 * ab_fetch_data_no_cache
 * ab_convert_data_to_table
 * ab_table_names
 * ab_load_table
 * ab_update_searches_data
 * register_api_webhook
 */
function get_basic_token() {
	$username 	= AB_API_USER;
	$password 	= AB_API_PASS;
	$basic_token = base64_encode($username . ":" . $password );
	return $basic_token;
}

function generate_api_token() {

	// lets check if there is token stored in the database
	$old_token 		= get_option('ab_api_token');
	$old_token_time = get_option('ab_api_token_timestamp');
	$current_time 	= time();
	$remaining_time = $current_time - $old_token_time;
	$host 			= AB_API_HOST . '/authenticate';
	
	if ( $old_token_time != '' && $old_token != '' ) {

		if ( $remaining_time < 300 ) {
			return $old_token;
		}
		
	}
	
	// oh the old token is more than an hour old, let's generate new one
	$basic_token = get_basic_token();

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

	return $response;
	
	// Set the regex.
	$regex = '/(?<=\bx-abc-token:\s)(?:[\w-]+)/is';

	// Run the regex with preg_match_all.
	preg_match_all($regex, $response, $matches);

	$result = $matches[0];
	$token 	= $result[0];

	update_option('ab_api_token', $token, true);
	update_option('ab_api_token_timestamp', time(), true);
	
	return $token;

}

function ab_fetch_data_no_cache($token = '', $per_page = '', $next = '') {

	$basic_token 	= get_basic_token();
	$token 			= $token == '' ? generate_api_token() : $token;
	
	$endslash 		= AB_DATA == 'testapi' ? '/' : '';
	if ( $per_page == '' && $next == '' ) {
		$params 	= '';
	} else {
		$per_page 	= $per_page == '' ? null : $per_page;
		$next 		= $next == '' ? null : $next;
		$query_params = array(
			'page' 	=> $per_page,
			'next' 	=> $next
		);
		$params  	= '?' . http_build_query($query_params);
	}
	
	if ( AB_DATA == 'testapi' ) {
		$endpoint = AB_API_HOST . '/searches' . $endslash . $params;
	} else {
		$endpoint = AB_API_HOST . '/searches' . $params;
	}

	$curl = curl_init();
	curl_setopt_array( $curl, array(
		CURLOPT_URL => $endpoint,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_HEADER => false,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_POSTFIELDS => array(),
		CURLOPT_HTTPHEADER => array(
			"x-abc-token: ". $token,
			"Authorization: Basic " . $basic_token
		),
	));

	$response 	= curl_exec($curl);

	curl_close($curl);

	return $response;

}

function ab_convert_data_to_table($data) {
	$decoded_data = @json_decode( $data );
	if ( ! $decoded_data ) { return; }
	$searches = isset($decoded_data->searches) ? $decoded_data->searches : array();
	return $searches;
}

function ab_table_names($table_name = '') {
	$tables = array(
		'additiontypes' => 'AdditionTypes',
		'additionactiontypes' => 'AdditionActionTypes',
		'chargelevels' => 'ChargeLevels',
		'civilcasetype' => 'CivilCaseType',
		'civilcasedispositions' => 'CivilCaseDispositions',
		'counties' => 'Counties',
		'dispositions' => 'Dispositions',
		'probationtypes' => 'ProbationTypes'
	);

	return $table_name == '' ? $tables : $tables[$table_name];
}

function ab_load_table($table_name) {
	$basic_token 	= get_basic_token();
	$token 			= generate_api_token();
	return $token;

	$name 			= $table_name;
	$versions 		= get_option('charge_table_versions');

	$table_name 	= AB_DATA == 'testapi' ? '?name=' . $table_name : $table_name;
	$host 			= AB_API_HOST . '/table/' . $table_name;

	$curl 			= curl_init();

	curl_setopt_array( $curl, array(
		CURLOPT_URL => $host,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_HEADER => false,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_POSTFIELDS => array(),
		CURLOPT_HTTPHEADER => array(
			"x-abc-token: ". $token,
			"Authorization: Basic " . $basic_token
		),
	));

	$response 	= curl_exec($curl);

	curl_close($curl);

	update_option('table_' . $name, $response, 'no');

	return $response;
}

function ab_update_searches_data($update_data = '') {

	$basic_token 	= get_basic_token();
	$token 			= generate_api_token();
	$endslash 		= AB_DATA == 'testapi' ? '/' : '';
	$host 			= AB_API_HOST . '/searches' . $endslash;
	$request_mode 	= AB_DATA == 'testapi' ? 'POST' : 'PUT';

	/*return array(
		'basic_token' 	=> $basic_token,
		'token' 		=> $token,
		'host' 			=> $host,
		'request_mode' 	=> $request_mode,
		'data' 			=> $update_data
	);*/

	$curl 			= curl_init();
	curl_setopt_array( $curl, array(
		CURLOPT_URL 			=> $host,
		CURLOPT_RETURNTRANSFER 	=> true,
		CURLOPT_ENCODING 		=> "",
		CURLOPT_MAXREDIRS 		=> 10,
		CURLOPT_TIMEOUT 		=> 0,
		CURLOPT_FOLLOWLOCATION 	=> true,
		CURLOPT_HTTP_VERSION 	=> CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST 	=> $request_mode,
		CURLOPT_POSTFIELDS 		=> $update_data,
		CURLOPT_HTTPHEADER 		=> array(
			"x-abc-token: ". $token,
			"Authorization: Basic " . $basic_token,
			"Content-Type: text/plain"
		)
	));

	$response 	= curl_exec($curl);

	curl_close($curl);
	
	$update = json_decode( $response );
	/*if ( isset($update->completed_updates) && isset($update->completed) && $update->completed == true ) {
		delete_option( 'ab_cached_data' );
		delete_option( 'ab_cached_data_timestamp' );
	}*/
	return $update;
}

function register_api_webhook($webhooks = array()) {
	$webhooks 		= json_encode($webhooks);
	$basic_token 	= get_basic_token();
	$token 			= generate_api_token();
	$endslash 		= AB_DATA == 'testapi' ? '/' : '';
	$host 			= AB_API_HOST . '/webhooks' . $endslash;
	$request_mode 	= AB_DATA == 'testapi' ? 'POST' : 'PUT';
	$curl 			= curl_init();
	curl_setopt_array( $curl, array(
		CURLOPT_URL => $host,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => $request_mode,
		CURLOPT_POSTFIELDS => $webhooks,
		CURLOPT_HTTPHEADER => array(
			"x-abc-token: ". $token,
			"Authorization: Basic " . $basic_token,
			"Content-Type: application/json"
		)
	));

	$response 	= curl_exec($curl);

	curl_close($curl);
	
	return json_decode( $response );

}

function ab_reload_search_data($search_id) {
	$basic_token 	= get_basic_token();
	$token 			= generate_api_token();
	$endslash 		= AB_DATA == 'testapi' ? '/' : '';
	
	if ( AB_DATA == 'testapi' ) {
		$endpoint = AB_API_HOST . '/searches/?id=' . $search_id;
	} else {
		$endpoint = AB_API_HOST . '/searches/' . $search_id;
	}
	$curl = curl_init();
	curl_setopt_array( $curl, array(
		CURLOPT_URL => $endpoint,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_HEADER => false,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_POSTFIELDS => array(),
		CURLOPT_HTTPHEADER => array(
			"x-abc-token: ". $token,
			"Authorization: Basic " . $basic_token
		),
	));

	$response 	= curl_exec($curl);

	curl_close($curl);

	return json_decode( $response );
}