<?php
// NO NEED TO EDIT THIS FILE, USE DB-CONNECT.PHP
include_once 'config.inc.php';   // As functions.php is not included
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
if (!function_exists('get_country_by_ip')) {
	function get_country_by_ip($ip, $db) {
		if(strpos($_SERVER['HTTP_HOST'], '.co.uk') !== false) {
			$api_keys = [
				'0d894e84124339e6ee4953a85700089f679ca4ea',
				'98879b5df07da3734a86694bd763b6f53ca1749c'
			];
			$ipl = ip2long($ip);
			//var_dump($ipl);
			$query = "SELECT * FROM ip_Location_gb WHERE $ipl BETWEEN ip_from AND ip_to";
			//var_Dump($query);
			$result = $db->query("SELECT * FROM ip_Location_gb WHERE $ipl BETWEEN ip_from AND ip_to");
			$i = 0;

			if(!empty($result) && is_object($result)) {

                $num = $result->num_rows;
                if ($num == 0) {
                    $api_key = $api_keys[$i];
                    $url = 'https://api.db-ip.com/addrinfo?addr='.$ip.'&api_key='.$api_key;
                    $data = json_decode(file_get_contents($url));
                    while(isset($data->error) && $i < count($api_keys)) {
                        $i = $i+1;
                        $api_key = '';
                        if(isset($api_keys[$i])) {
                            $api_key = $api_keys[$i];
                        }
                        $url = 'https://api.db-ip.com/addrinfo?addr='.$ip.'&api_key='.$api_key;
                        $data = json_decode(file_get_contents($url));
                        if(!isset($data->error) || $i > count($api_keys)) {
                            //var_Dump($data);
                            break;
                        }
                    }
                    if (isset($data->country)) {
                        if ($data->country == 'GB') {
                            $num = 1;
                        }
                    }
                }
                return $num;

            }


		}
		return 1;
	}
}
//$_SERVER['REMOTE_ADDR'] = '151.226.131.100';

$user_country = 'GB';
if($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
	$user_country = get_country_by_ip($_SERVER['REMOTE_ADDR'], $mysqli);
} 
/* determine our thread id */
$thread_id = $mysqli->thread_id;
defined('DEBUG') or define('DEBUG', false);
if(DEBUG == true)
{
    ini_set('display_errors', 'On');
    error_reporting(E_ALL ^ E_NOTICE);
}
else
{
    ini_set('display_errors', 'Off');
    error_reporting(0);
}
if(isset($_SESSION['locale'])){
    $locale = $_SESSION['locale'];  
}else{

	//by Zubaer
	$locale = 'en-GB';

	/*
	$locale = 'en-Us';
	if ($user_country == 1) {
		$locale = 'en-GB';
	}
	*/
}
