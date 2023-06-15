<?php
include_once '../auth/startup.php';
require '../data/data-functions.php';
extract($_POST);
if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    error_reporting(E_ALL);
}

//$start_date = date('Y-m-d h:i:s', strtotime($start_date));
$start_date = date('Y-m-d', strtotime($start_date));
//$end_date = date('Y-m-d h:i:s', strtotime($end_date));
$end_date = date('Y-m-d 23:59:59', strtotime($end_date));



$tsp = total_sales_period_i_my_sales($start_date, $end_date, $owner);
$aep = affiliate_earnings_period_i_my_sales($start_date, $end_date, $owner);
$data = [
    'total_sales_period' => $tsp,
    'my_total_cpc_earnings' => $aep.' ',
];
echo json_encode($data);
