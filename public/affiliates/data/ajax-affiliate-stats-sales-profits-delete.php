<?php
include_once '../auth/startup.php';
require '../data/data-functions.php';
$transaction_id = $_POST['id'];
$affiliate_id = $_POST['affiliate_id'];

$get_ta = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT net_earnings FROM ap_earnings WHERE id=$transaction_id"));
$ta = $get_ta['net_earnings'];

if($admin_user=='1'){
if ($stmt = $mysqli->prepare("DELETE FROM ap_earnings WHERE id = ? LIMIT 1")) { 
		$stmt->bind_param("i", $transaction_id);	
		$stmt->execute();
		$stmt->close();
	} else { 
		echo "ERROR: could not prepare SQL statement."; 
	}
}
//UPDATE BALANCE
$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT balance FROM ap_members WHERE id=$affiliate_id"));
$tb = $get_tb['balance'];

$updated_balance = $tb - $ta;
if($updated_balance < 0){$updated_balance ='0.00';}
	$update_one = $mysqli->prepare("UPDATE ap_members SET balance = ? WHERE id=$affiliate_id"); 
	$update_one->bind_param('s', $updated_balance);
	$update_one->execute();
	$update_one->close();

$mysqli->close();