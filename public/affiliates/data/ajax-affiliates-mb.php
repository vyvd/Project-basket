<?php
include_once '../auth/startup.php';
require '../data/data-functions.php';
if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    error_reporting(E_ALL);
}
$ta = total_affiliates();
$tb = total_balance();
$data = [
    'total_affiliates' => $ta,
    'total_balance' => $tb,
];
echo json_encode($data);
