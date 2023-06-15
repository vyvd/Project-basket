<?php
require $_SERVER['DOCUMENT_ROOT']."/wp-load.php";
$range_day_month = range('1', date('t'));
foreach($range_day_month as $day) {
    if($day < 10) {
        $day = '0'.$day;
    }
    if(date('d h:i:s') >= $day.' 00:00:00' && date('d h:i:s') <= $day.' 01:00:00') {
        $day = date('d h:i:s');
        break;
    }
}
$every_day = (date('d h:i:s') == $day) ? true : false;
$every_week = (date('d') == date('d') % 7 && date('d') == $day) ? true : false;
$every_two_weeks = (date('d') == '01' || date('d') == '15') ? true : false;
$every_month = (date('d') == '01') ? true : false;
class remove_dups {
    public function __construct() {
        global $wpdb;
        $sql = "SELECT id, product from ap_earnings";
        $orders = $wpdb->get_results($sql);
        $datas = [];
        foreach($orders as $elem) {
            $datas[$elem->id] = $elem->product;
        }
        $nr_orders = count($orders);
        $unique = array_unique($datas);
        $nr_unique = count($unique);
        $implode_unique = implode(', ', array_keys($unique));
        $sql_1 = "DELETE FROM ap_earnings WHERE id NOT IN ($implode_unique)";
        $delete = $wpdb->query($sql_1);
        $vars = get_defined_vars();
    }
}
new remove_dups();

class remove_old_redeemed_coupons {
    public function __construct() {
        global $wpdb;
        $date_to_remove = date('Y-m-d h:i:s', strtotime(" -14 months"));
        $sql = "DELETE FROM {$wpdb->prefix}voucher_code WHERE used = 1 AND date_redeemed <= '{$date_to_remove}'";
        $test = $wpdb->query($sql);
    }
}
if($every_two_weeks) {
    new remove_old_redeemed_coupons();
}
