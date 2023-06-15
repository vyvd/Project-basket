<?php include_once '../auth/startup.php';
$f = filter_input(INPUT_POST, 'commission_aff', FILTER_SANITIZE_NUMBER_INT);
$redirect = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_STRING);
$affiliate_filter = filter_input(INPUT_POST, 'af', FILTER_SANITIZE_STRING);
//update the values
$update_one = $mysqli->prepare("UPDATE ap_members SET comission = ? WHERE id=$affiliate_filter"); 
$update_one->bind_param('i', $f);
$update_one->execute();
$update_one->close();
	
$mysqli->close();
header('Location: '.$redirect);
?>