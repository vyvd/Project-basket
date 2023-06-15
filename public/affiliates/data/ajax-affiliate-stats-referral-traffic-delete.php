<?php
include_once '../auth/startup.php';
require '../data/data-functions.php';
$referral_id = $_POST['id'];
$affiliate_id = $_POST['affiliate_id'];

if($admin_user=='1'){
if ($stmt = $mysqli->prepare("DELETE FROM ap_referral_traffic WHERE id = ? LIMIT 1")) { 
	$stmt->bind_param("i", $referral_id);	
	$stmt->execute();
	$stmt->close();
	} else { 
		echo "ERROR: could not prepare SQL statement."; 
	}
}

$mysqli->close();