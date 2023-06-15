<?php
/* affiliate tracking and discounting */
include(TO_PATH.'/affiliates/controller/affiliate-tracking.php');

if($this->get["ref"] != "") {

    $_SESSION["refCodeInternal"] = $this->get["ref"];

    $affVoucher = ORM::For_table("ap_affiliate_voucher")->where("aff_id", $this->get["ref"])->find_one();

    if($affVoucher != "") {

        $_SESSION["affiliateDiscount"] = $affVoucher->voucher_value;
        $_SESSION["affiliateDiscountType"] = $affVoucher->comission_type;

        $affCoupon = ORM::for_table("coupons")->where("code", $affVoucher->voucher_code)->find_one();

        if($affCoupon->id != "") {

            if($affCoupon->valueMax != "") {
                $_SESSION["affiliateDiscountMax"] = $affCoupon->valueMax;
            } else {
                $_SESSION["affiliateDiscountMax"] = "";
            }

            if($affCoupon->valueMin != "") {
                $_SESSION["affiliateDiscountMin"] = $affCoupon->valueMin;
            } else {
                $_SESSION["affiliateDiscountMin"] = "";
            }

            $_SESSION["excludedCourses"] = $affCoupon->excludeCourses;

        }

    }

}

$url = $_SERVER["SCRIPT_NAME"];
$break = Explode('/', $url);
$file = $break[count($break) - 1];
$cachefile = TO_PATH_CDN.'cache/cached-'.str_replace("/", "", REQUEST).substr_replace($file ,"",-4).$_SESSION["refCodeInternal"].'.html';
$cachetime = 18000;


// Serve from the cache if it is younger than $cachetime
if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile) && CUR_ID_FRONT == "") {
    echo "<!-- Cached copy, generated ".date('H:i', filemtime($cachefile))." -->\n";
    readfile($cachefile);
    exit;
}
ob_start(); // Start the output buffer
?>