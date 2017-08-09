<?php

include 'httpful.phar';
$uri = $_SERVER['REQUEST_URI'];
$exploded = explode('/', trim($uri, ' '));
$uri2=$exploded[3];
$json_url = "http://itnthackathon.bweas.tm.com.my/api/getSiebelCustInfo2?filter=IC_No,ew,".$uri2;
$json = file_get_contents($json_url);
$data = json_decode(callAPI($json_url));
//$param= trim($exploded[3]);
//echo $uri;
echo convertJSON($data->{'getSiebelCustInfo2'}->{'records'}[0]);

function convertJSON($array){
	$json_string = array(
	'LOGIN_ID' => $array[1],
	'CELL_PHONE_NO' => $array[2],
	'SUBSCRIBER_NAME' => $array[3],
	'SUBSCRIBER_ADDRESS' => $array[4],
	'SUBSCRIBER_PACKAGE' => $array[5],
	'SERVICE_STATUS' => $array[6],
	'TOTAL_DUE' => $array[7],
	'IC_NO' => trim($array[11]),
	);
	return json_encode($json_string);
}

function callAPI($request_uri){
	$response = \Httpful\Request::get($request_uri)
	->expectsJson()
	//->withXTrivialHeader('Just a demo')
	->send();
	
	//echo get_class($response);
	$string_response = $response->__toString();
	
	return $string_response;
}

?>