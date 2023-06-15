<?php
include_once '../auth/startup.php';
require '../auth/ssp.class.php';
require '../data/data-functions.php';
if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    error_reporting(E_ALL);
}
$table = 'ap_members';
 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
$columns = array(
    array( 
        'db' => 'id',     
        'dt' => 0,
        'formatter' => function( $d, $row ) {
            $member = $row['id'];
            return $member;
        }
    ),
    array( 
        'db' => 'fullname',     
        'dt' => 1,
        'formatter' => function( $d, $row ) {
            return '<a href="affiliate-stats?a='.$row['id'].'">'.$row['fullname'].'</a>';
        }
    ),
    array('db' => 'username', 'dt' => 2),
    array('db' => 'email', 'dt' => 3),
    array( 
        'db' => "id",     
        'dt' => 4,
        'formatter' => function( $d, $row ) {
            global $mysqli;
            $member = $row['id'];
            $query = mysqli_query($mysqli, "SELECT COUNT(id) as affiliate_referrals FROM ap_referral_traffic WHERE affiliate_id='$member'");
            $get_affiliate = mysqli_fetch_assoc($query);
            $affiliate_referrals = $get_affiliate['affiliate_referrals'];
            if($affiliate_referrals==''){
                $affiliate_referrals = '0.00';
            }
            return $affiliate_referrals;
        }
    ),
    array(
        'db' => 'id',     
        'dt' => 5,
        'formatter' => function( $d, $row ) {
            global $mysqli, $money_format, $currency_symbol;
            $member = $row['id'];
            $get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(sale_amount) as affiliate_sales FROM ap_earnings WHERE affiliate_id='$member'"));
            $affiliate_sales = $get_affiliate['affiliate_sales'];
            if($affiliate_sales==''){
                $affiliate_sales = '0.00';
            }
            return $money_format->formatCurrency($affiliate_sales, $currency_symbol);
        }
    ),
    array(
        'db' => 'balance',     
        'dt' => 6,
        'formatter' => function( $d, $row ) {
            global $mysqli, $money_format, $currency_symbol;
            return $money_format->formatCurrency($row['balance'], $currency_symbol);
        }
    ),
    array(
        'db' => 'terms',     
        'dt' => 7,
        'formatter' => function( $d, $row ) {
            global $mysqli;
            if($row['terms']=='1'){
                return 'Yes';
            }
            return 'No';
        }
    ),
    array(
        'db' => 'id',     
        'dt' => 8,
        'formatter' => function( $d, $row ) {
            $id = $row['id'];
            $string = '<a target="blank" href="http://'.$_SERVER['HTTP_HOST'].'/affiliates/affiliate-stats?a='.$id.'" class="btn btn-sm btn-primary">View Stats</a>';
            $string .= '<button data-id="'.$id.'" class="delete btn btn-sm btn-danger">Delete</button>';
            return $string;
        }
    ),
    array(
        'db' => 'email',
        'dt' => 9,
        'formatter' => function( $d, $row ) {
            $member = $row['id'];
            $token = 'dmswitchme2017';
            return '<a href="access/process_login.php?a='.$member.'&email='.$row['email'].'&token='.$token.'">Switch To</a>';
        }
    ),
);
 
// SQL server connection information
$sql_details = array(
    'user' => USER,
    'pass' => PASSWORD,
    'db'   => DATABASE,
    'host' => HOST
);
$where = "admin_user!=1";
echo json_encode(
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, $where )
);
