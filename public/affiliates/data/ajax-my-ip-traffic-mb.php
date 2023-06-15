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
$tce = total_sales_period_i_my_ip($start_date, $end_date, $owner, $lp);
//var_Dump($start_date, $end_date);
$data = [
    'my_total_cpc_earnings' => $tce.' '
];
echo json_encode($data);
