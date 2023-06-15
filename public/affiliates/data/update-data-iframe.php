<?php
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/startup.php';
require $_SERVER['DOCUMENT_ROOT'].'/affiliates/data/data-functions.php';
require(__DIR__ . '/../../../wp-load.php');
error_reporting(E_ALL);
global $wpdb;
$affiliate_filter = filter_input(INPUT_POST, 'aff_id', FILTER_SANITIZE_STRING);
$checked_checkboxes = json_encode($_POST['courses']);
$sql = "UPDATE ap_iframe_generator SET courses = '$checked_checkboxes' WHERE aff_id = '{$affiliate_filter}'";
echo $sql;
$test = $wpdb->query($sql);
//var_dump($test);