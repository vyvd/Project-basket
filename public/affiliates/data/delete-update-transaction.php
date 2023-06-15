<?php
include_once '../auth/startup.php';
require '../data/data-functions.php';
$affiliate_id = intval(filter_input(INPUT_POST, 'a', FILTER_SANITIZE_STRING));
$redirect = filter_input(INPUT_POST, 'r', FILTER_SANITIZE_STRING);
$voucher_code = filter_input(INPUT_POST, 'voucher_code', FILTER_SANITIZE_STRING);
$coupon_id_wc = intval(filter_input(INPUT_POST, 'c', FILTER_SANITIZE_STRING));
$aff_coupon_value = filter_input(INPUT_POST, 'voucher_value', FILTER_SANITIZE_STRING);
$aff_coupon_value = intval(floatval(preg_replace('~[^0-9\.]~', '' ,$aff_coupon_value)));
$discount_type = filter_input(INPUT_POST, 'comission_type', FILTER_SANITIZE_STRING);
$row_id = intval(filter_input(INPUT_POST, 'm', FILTER_SANITIZE_STRING));
$delete = (isset($_POST['delete'])) ? $_POST['delete'] : '';
$action = (isset($_POST['update'])) ? $_POST['update'] : $delete;
if($action == 'Update') {
	//var_dump($coupon_in_wc);
	$update = update_coupon_wc($coupon_id_wc, $aff_coupon_value, $discount_type);
	if($update) {
		$stmt = $mysqli->prepare("UPDATE ap_affiliate_voucher SET voucher_value = ?, comission_type = ? WHERE ID = ?"); 
		$res = $stmt->bind_param("isi", $aff_coupon_value, $discount_type, $row_id);
		$res = $stmt->execute();
		if(!$res) {
			$redirect .= "&err=".$stmt->error;
		}

	} else {
		$redirect .= "&err=Updating Coupon {$voucher_code} wasn't necessary. Same Value.";
	}
} elseif ($action == 'Delete') {
	$delete = delete_coupon_wc($coupon_id_wc);
	if($delete) {
		$stmt = $mysqli->prepare("DELETE FROM ap_affiliate_voucher WHERE ID = ? LIMIT 1");
		//var_dump($affiliate_id, $voucher_code);
		$res = $stmt->bind_param("i", $row_id	);
		$res = $stmt->execute();
		if(!$res) {
			$redirect .= "&err=".$stmt->error;
		}
		$stmt->close();
	} else {
		$redirect .= "&err=Failed Removing the coupon from DBS";
	}
}
header('Location: '.$redirect.'');