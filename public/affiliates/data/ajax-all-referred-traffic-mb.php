<?php
include_once '../auth/startup.php';
require '../data/data-functions.php';
extract($_POST);
if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    error_reporting(E_ALL);
}
$start_date = date('Y-m-d h:i:s', strtotime($start_date));
$end_date = date('Y-m-d h:i:s', strtotime($end_date));
$trp = total_referrals_period_i($start_date, $end_date, $owner);
$tce = my_total_cpc_earnings($owner, $start_date, $end_date);
$mcp = my_conversion_period_i_my_traffic($start_date, $end_date, $owner);
//var_Dump($tce);
$data = [
    'total_referrals_period' => $trp,
    'my_total_cpc_earnings' => $tce,
    'my_conversion_period' => $mcp
];
echo json_encode($data);
