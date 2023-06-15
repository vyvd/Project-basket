<?php 
include_once '../auth/startup.php';
require '../data/data-functions.php';
//require __DIR__.'/../../../wp-load.php';;
//global $wpdb;
$affiliate_id = intval(filter_input(INPUT_POST, 'af', FILTER_SANITIZE_NUMBER_INT));
$redirect = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_STRING);
$report_id = intval(filter_input(INPUT_POST, 'report_id', FILTER_SANITIZE_NUMBER_INT));
$sql = "SELECT date_gen FROM ap_reports WHERE ID = {$report_id}";
$result = mysqli_query($mysqli, $sql);
//var_Dump($result);

$report = mysqli_fetch_assoc($result);

//var_dump($report);

$date_gen = $report['date_gen'];
$month = date('m', strtotime($date_gen));
$year = date('Y', strtotime($date_gen));
$last_day = date('t', strtotime($date_gen));
$sql = "SELECT * FROM ap_earnings WHERE affiliate_id={$affiliate_id} AND `datetime` BETWEEN '{$year}-{$month}-01 00:00:00' AND '{$year}-{$month}-31 23:59:59'";
//echo $sql;
$result = mysqli_query($mysqli, $sql);
$data = [['Order ID', 'Course', 'Spend', 'Comission', 'Sale Amount', 'Net Earnings', 'Refund', 'Date Purchase']];
$file_name = 'report_'.$date_gen.'.csv';

//$rows = $wpdb->get_results($sql, ARRAY_A);
$result = mysqli_query($mysqli, $sql);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
//var_dump($rows);

//die();
function findProductOrder($product) {
    preg_match('/WC Order #:(.*)/', $product, $orderIdMatch);
    if (!is_array($orderIdMatch) || !isset($orderIdMatch[1])) {
        return false;
    }
    $orderId = (int)trim($orderIdMatch[1]);
    $findOrder = ORM::for_table('orders')->where('oldID', $orderId)->findOne();
    if (!$findOrder) {
        $findOrder = ORM::for_table('orders')->whereIdIs($orderId)->findOne();
        if (!$findOrder) {
            return false;
        }
    }
    return $findOrder;
}
function findProductCourse($order) {
    $findOrderItems = ORM::for_table('orderItems')->where('orderID', $order->get('id'))->findMany();
    return array_map(function (ORM $orderItem) {
        $course = ORM::for_table('courses')->where('oldID', $orderItem->get('courseID'))->findOne();
        if (!$course) {
            return ORM::for_table('courses')->whereIdIs($orderItem->get('courseID'))->findOne();
        }
        return $course;
    }, $findOrderItems);
}
foreach($rows as $row) {
	extract($row);
	$refund = ($refund !== '0') ? 'Yes': 'No';
    $order = findProductOrder($product);

    if ($order instanceof ORM) {
        $courses = findProductCourse($order);
        if (count($courses)) {
            foreach ($courses as $index => $course) {
                $courseText = "{$course->id}: {$course->title}";
                $data[] = [
                    $product, $courseText, number_format($order->get('total'), 2),  $comission, $sale_amount, $net_earnings, $refund, $datetime
                ];
            }
        }
    } else {
        $data[] = [
            $product, '-', '-', $comission, $sale_amount, $net_earnings, $refund, $datetime
        ];
    }
}
//var_dump($data);
convert_to_csv_download($data, $file_name);
header('Location: '.$redirect.'');