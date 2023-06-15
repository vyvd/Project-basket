<?php
include_once '../auth/startup.php';
require '../data/data-functions.php';
if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    error_reporting(E_ALL);
}
extract($_POST);
//$affiliate_id = 10;

//var_dump($start_date);
//var_dump($end_date);

//$start_date = date('Y-m-d h:i:s', strtotime($start_date));
$start_date = date('Y-m-d', strtotime($start_date));
//$end_date = date('Y-m-d h:i:s', strtotime($end_date));
$end_date = date('Y-m-d 23:59:59', strtotime($end_date));

//var_dump($start_date);
//var_dump($end_date);

$return = true;
$tsp = total_sales_period($start_date, $end_date, $affiliate_id);
$aep = affiliate_earnings_period($start_date, $end_date, $affiliate_id);
$trp = total_referrals_period($start_date, $end_date, $affiliate_id);
//var_Dump($tce);
$data = [
    'total_sales_period' => $tsp,
    'affiliate_earnings_period' => $aep,
    'total_referrals_period' => $trp
];
echo json_encode($data);
