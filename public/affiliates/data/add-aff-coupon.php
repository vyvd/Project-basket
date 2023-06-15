<?php 
include_once '../auth/startup.php';
require '../data/data-functions.php';
$affiliate_id = intval(filter_input(INPUT_POST, 'af', FILTER_SANITIZE_STRING));
$redirect = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_STRING);
$voucher_code = filter_input(INPUT_POST, 'aff_coupon', FILTER_SANITIZE_STRING);
$aff_coupon_value = intval(filter_input(INPUT_POST, 'aff_coupon_value', FILTER_SANITIZE_NUMBER_INT));
$discount_type = filter_input(INPUT_POST, 'comission_type', FILTER_SANITIZE_STRING);
$date = filter_input(INPUT_POST, 'aff_expire_date', FILTER_SANITIZE_STRING);
$date = date('Y-m-d h:i:s', strtotime($date));
$coupon_in_wc = check_coupon_wc_dbs_exists($voucher_code, $affiliate_id);

if(!$coupon_in_wc) {
	
	$coupon_wc = add_coupon_to_dbs($voucher_code, $affiliate_id, $aff_coupon_value, $discount_type, $date);

	if($coupon_wc) {
		$query = "SELECT * FROM ap_affiliate_voucher WHERE voucher_code = '{$voucher_code}'";
		$result = $mysqli->query($query);
		if($result){
			if($result->num_rows < 1) {
				$stmt = $mysqli->prepare("INSERT INTO ap_affiliate_voucher (aff_id, coupon_id_wc, voucher_code, voucher_value, comission_type, expire_date) VALUES (?, ?, ?, ?, ?, ?)");
				$res = $stmt->bind_param("iisiss", $affiliate_id, $coupon_wc, $voucher_code, $aff_coupon_value, $discount_type, $date);	
				$res = $stmt->execute();
				if(!$res) {
					$redirect .= "&err=".$stmt->error;
				}
				$stmt->close();
			} else {
				$redirect .=  "&err=Voucher already exists in affiliate voucher DBS"; 
			}
		} else { 
			$redirect .=  "&err=Invalid Query"; 
		}
		$mysqli->close();
	} else {
		$redirect .=  "&err=Issue Inserting coupon in DBS"; 
	}
} else {
	$redirect .=  "&err=Coupon with ID ".$coupon_in_wc." is already in DBS"; 
}
//var_Dump($affiliate_id, $voucher_code, $aff_coupon_value, $date);
header('Location: '.$redirect.'');