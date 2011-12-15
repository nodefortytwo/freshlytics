<?php
function debug($var){
	print '<pre>' . print_r($var, true) . '</pre>';
	
}
function beginsWith($str, $sub) {
    return (strncmp($str, $sub, strlen($sub)) == 0);
}
function redirect($url, $code='301'){
	
	switch ($code){
		case 301:
			header( "HTTP/1.1 301 Moved Permanently" ); 
		break;
	}

	header('Location: ' . $url);

}

function module_get_path($module_name){
    $basepath = dirname($_SERVER['PHP_SELF']);
    $path = $basepath . '/libs/modules/' . $module_name;
    return $path;
}


function get_data($url)
{
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}

?>