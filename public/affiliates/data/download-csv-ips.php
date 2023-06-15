<?php
include_once '../auth/startup.php';
require '../inc/vendor/autoload.php';
require '../data/data-functions.php';
require(__DIR__ . '/../../../wp-load.php');
if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    error_reporting(E_ALL);
}
use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
$section = 'dashboard.php';
$config = new ExporterConfig();
$exporter = new Exporter($config);

$exporter->export('php://output', array(
    array('1', 'alice', 'alice@example.com'),
    array('2', 'bob', 'bob@example.com'),
    array('3', 'carol', 'carol@example.com'),
));
$output_file_name = 'ips.csv';
header('Content-Type: application/csv');
header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
//header("Location: http://".$_SERVER['HTTP_HOST'].'/affiliates/'.$section);
//exit;

