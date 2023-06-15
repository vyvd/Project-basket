<?php
include_once '../auth/startup.php';
require '../data/data-functions.php';
extract($_POST);
if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    error_reporting(E_ALL);
}
$return = true;
$start_date = date('Y-m-d h:i:s', strtotime($start_date));
$end_date = date('Y-m-d h:i:s', strtotime($end_date));
$trp = total_referrals_period($start_date, $end_date);
$tce = total_cpc_earnings_referrals($start_date, $end_date);
$aap = active_affiliates_period($start_date, $end_date);
//var_Dump($tce);
$data = [
    'total_referrals_period' => $trp,
    'total_cpc_earnings' => $tce,
    'active_affiliates_period' => $aap
];
echo json_encode($data);
