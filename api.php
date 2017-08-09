<?php
	// Httpful phar (PHP archive) file
	include 'httpful.phar';

	$uri = $_SERVER['REQUEST_URI'];

	//echo 'The URL: ' . $uri . '<br/>';
	//echo 'Exploded: ';
	
	$GoogleMapsApiKey = 'AIzaSyBS4oL2YPC_WrpueoHAV5DkydLaCgbph3M';
	$GoogleMapsEmbedKey = 'AIzaSyBoNh0-THEvHKXhpGKGqftqYZDvhoeDG_I';
	
	$exploded = explode('/', trim($uri, ' '));
	
	if(count($exploded) <= 3){
		echo '{ "error" : "Please use the correct API" }';
		die();
	}
	
	//echo count($exploded) . '<br/>';
	//print_r($exploded);
	
	// event_name is always the first parameter
	$event_name = $exploded[3];
	
	// Switch to determine which API to call
	switch(strtolower($event_name)){
		case "test":
			testAPI($exploded);
			break;
		case "longpoll":
			longPoll($exploded);
			break;
		case "predicttraveltime":
			predictTravelTime($exploded);
			break;
		case "directions":
			getMapsDirections($exploded);
			break;
		case "openweather":
			getOpenWeather($exploded);
			break;
		case "yahooweather":
			getYahooWeather();
			break;
		case "getparkinginfo":
			getParkingCount($exploded);
			break;
		default:
			echo '{ "error" : "Please use the correct API" }';
			die();
	}
	
	// This is a test API
	function testAPI($exploded){
		$request_uri = 'https://jsonplaceholder.typicode.com/posts/' . $exploded[4];
		echo callAPI($request_uri);
	}
	
	// Get parking count
	function getParkingCount($exploded){
		$request_uri = 'http://itnthackathon.bweas.tm.com.my/api/getParkingInfo';
		$obj = json_decode(callAPI($request_uri));
		
		if(!is_null($exploded[4]) && !empty($exploded[4]))
			$parkinglevel = $exploded[4];
		else
			$parkinglevel = 'all';
		
		switch(strtolower($parkinglevel)){
			case "1":
			case "lg2":
				//echo '0 and lg2';
				echo convertJSONParkingCount($obj->{'getParkingInfo'}->{'records'}[0]);
				break;
			case "2":
			case "lg4":
				//echo '1 and lg4';
				echo convertJSONParkingCount($obj->{'getParkingInfo'}->{'records'}[1]);
				break;
			case "3":
			case "nx":
				//echo '2 and nx';
				echo convertJSONParkingCount($obj->{'getParkingInfo'}->{'records'}[2]);
				break;
			case "motorcycle":
				convertJSONParkingCountAllMotorcycle($obj->{'getParkingInfo'}->{'records'});
				break;
			default:
				//echo 'all';
				//echo callAPI($request_uri);
				//echo convertJSONParkingCount($obj->{'getParkingInfo'});
				convertJSONParkingCountAll($obj->{'getParkingInfo'}->{'records'});
		}
		
		//print_r($obj->{'getParkingInfo'}->{'records'}[2]);
	}
	
	// Encode array passed to JSON for all
	function convertJSONParkingCountAll($array){
		// NX, LG2, LG4
		$json_string = '{' . $array[2][2] . ',' . $array[0][2] . ',' . $array[1][2] . '}';
		//return json_encode($json_string);
		//print_r($array[0]);
		echo $json_string;
	}
	
	// Encode array passed to JSON for all
	function convertJSONParkingCountAllMotorcycle($array){
		// NX, LG2, LG4
		$json_string = '{' . $array[2][4] . ',' . $array[0][4] . ',' . $array[1][4] . '}';
		//return json_encode($json_string);
		//print_r($array[0]);
		echo $json_string;
	}
	
	// Encode array passed to JSON
	function convertJSONParkingCount($array){
		$json_string = array(
			'parking_id' => $array[0],
			'name' => $array[1],
			'current_car_load' => $array[2],
			'max_car_load' => $array[3],
			'last_updated' => $array[6]
		);
		return json_encode($json_string);
	}
	
	// Yahoo Weather API
	function getYahooWeather(){
		$request_uri = 'https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20weather.forecast%20where%20woeid%20in%20(select%20woeid%20from%20geo.places(1)%20where%20text%3D%22Kuala%20Lumpur%22)&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys';
		echo callAPI($request_uri);
	}
	
	// Open Weather API
	function getOpenWeather($exploded){
		$OpenWeatherAppKey = "708f6caaea85d52248aaa59d29ea0edb";
		
		$latitude = $exploded[4];
		$longitude = $exploded[5];
		
		$myvars = 'lat=' . $latitude . '&lon=' . $longitude . '&appid=' . $OpenWeatherAppKey . '&units=metric';
		
		$request_uri = 'http://api.openweathermap.org/data/2.5/weather?' . $myvars;
		
		//echo $myvars; die();
		
		echo callAPIURL($request_uri);
	}
	
	// Google Maps predict travel time
	function predictTravelTime($exploded){
		$latitude = $exploded[4];
		$longitude = $exploded[5];
		
		$myvars = 'origins=' . $latitude . ',' . $longitude . '&destinations=3.115996,101.665292&key=' . $GLOBALS['GoogleMapsApiKey'];
		
		if(count($exploded) < 7 || $exploded[6] == null || empty($exploded[6])){
			//echo 'Arrival time is empty or null';
		}else{
			$departuretime = $exploded[6];
			//1501496700
			//$myvars .= '&departure_time=1501496700';
			$myvars .= '&departure_time=' . $departuretime;
		}
		
		$request_uri = 'https://maps.googleapis.com/maps/api/distancematrix/json?' . $myvars;
		
		//echo $request_uri;
		echo callAPI($request_uri);
	}
	
	// Google Maps directions
	function getMapsDirections($exploded){
		$latitude = $exploded[4];
		$longitude = $exploded[5];
		$departuretime = $exploded[6];
		
		$request_uri = 'https://maps.googleapis.com/maps/api/directions/json?origin=' . $latitude . ',' . $longitude . '&destination=3.115996,101.665292&departure_time=' .$departuretime . '&traffic_model=best_guess&key=' . $GLOBALS['GoogleMapsApiKey'];
		
		echo callAPI($request_uri);
	}
	
	// Long polling
	function longPoll($exploded){
		echo 'Starting long poll';
		sleep(10);
		echo 'End long poll';
	}
	
	// Call the intended API
	function callAPI($request_uri){
		$response = \Httpful\Request::get($request_uri)
			->expectsJson()
			//->withXTrivialHeader('Just a demo')
			->send();
		
		//echo get_class($response);
		$string_response = $response->__toString();
		
		return $string_response;
	}
	
	// Call API via GET URL
	function callAPIURL($request_uri){
		$ch = curl_init( $request_uri );
		curl_setopt( $ch, CURLOPT_POST, 1);
		//curl_setopt( $ch, CURLOPT_POSTFIELDS, '');
		//curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		//curl_setopt( $ch, CURLOPT_HEADER, 0);
		//curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		//print_r(curl_getinfo($ch));
		
		$response = curl_exec( $ch );
		
		return $response;
	}
?>