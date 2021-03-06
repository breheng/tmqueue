<?php
header('Access-Control-Allow-Origin: *');  
include 'httpful.phar';

$uri = $_SERVER['REQUEST_URI'];
$exploded = explode('/', trim($uri, ' '));
$json_url = "http://172.30.201.238:8080/api/getTMPointInfo";
$json = file_get_contents($json_url);
$data = json_decode(callAPI($json_url));
$param= trim($exploded[2]);
echo convertJSON($data->{'getTMPointInfo'}->{'records'}[$param]);



function convertJSON($array){
	$json_string = array(
	'tmpoint_id' => $array[0],
	'name' => $array[1],
	'no_of_counter' => $array[3],
	'current_queue_no' => $array[4],
	'total_queue_no' => $array[5]
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
