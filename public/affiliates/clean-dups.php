<?php 
include('auth/startup.php');
//include('data/data-functions.php');
$get_wc_orders = mysqli_query($mysqli, "SELECT product, id FROM ap_earnings");
$orders = [];
while($row = mysqli_fetch_row($get_wc_orders)) {
	//var_dump($row);
	$orders[$row[1]] = $row[0];
}
$unique = array_unique($orders);
$data = [];
foreach($unique as $val=>$id) {
	$data[] = "'$val'";
}
$thestr = implode(', ', $data);
$sql = "DELETE FROM ap_earnings WHERE id NOT IN ($thestr)";
$res = mysqli_query($mysqli, $sql);
echo 'Done';
?>

