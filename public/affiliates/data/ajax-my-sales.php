<?php
include_once '../auth/startup.php';
require '../auth/ssp.class.php';
require '../data/data-functions.php';
require '../../wp-load.php';

if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    error_reporting(E_ALL);
}
$table = 'ap_earnings';
 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
$columns = array(
    array( 'db' => 'product', 'dt' => 0 ),
    array( 
        'db' => 'product',     
        'dt' => 1,
        'formatter' => function( $d, $row ) {
            return get_user_email_for_bought_product($d, $require = false);
        }
    ),
    array( 
        'db' => "sale_amount",     
        'dt' => 2,
        'formatter' => function( $d, $row ) {
            global $money_format, $currency_symbol, $owner, $mysqli;
            $product = $row['product'];
            $query = "SELECT * FROM ap_earnings WHERE affiliate_id = '$owner' AND product = '$product'";
            $result = mysqli_fetch_assoc($mysqli->query($query));

            if($result['void']=='1'){ 
                return '<span class="red">-'.$money_format->formatCurrency($row['sale_amount'], $currency_symbol).' (Refunded)</span>'; 
            } else { 
                return $money_format->formatCurrency($row['sale_amount'], $currency_symbol); 
            }
        }
    ),
    array(
        'db' => 'comission',     
        'dt' => 3,
        'formatter' => function( $d, $row ) {
            return $d.'%';
        }
    ),
    array( 
        'db' => "net_earnings",     
        'dt' => 4,
        'formatter' => function( $d, $row ) {
            global $money_format, $currency_symbol, $owner, $mysqli;
            $product = $row['product'];
            $query = "SELECT void FROM ap_earnings WHERE affiliate_id = '$owner' AND product = '$product'";
            $result = mysqli_fetch_assoc($mysqli->query($query));
            if($result['void']=='1'){ 
                return '<span class="red">-'.$money_format->formatCurrency($row['sale_amount'], $currency_symbol).' (Refunded)</span>'; 
            } else { 
                return $money_format->formatCurrency($d, $currency_symbol); 
            }
        }
    ),
    array('db' => 'recurring', 'dt' => 5),
    array('db' => 'datetime', 'dt' => 6),
);
 
// SQL server connection information
$sql_details = array(
    'user' => USER,
    'pass' => PASSWORD,
    'db'   => DATABASE,
    'host' => HOST
);
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];


//$start_date = date('Y-m-d h:i:s', strtotime($start_date));
$start_date = date('Y-m-d', strtotime($start_date));
//$end_date = date('Y-m-d h:i:s', strtotime($end_date));
$end_date = date('Y-m-d 23:59:59', strtotime($end_date));



$where = "affiliate_id = '$owner'";
if(!empty($start_date) && !empty($end_date)) {
    if(strpos($start_date, '1970') === false && strpos($end_date, '1970') === false) {
        $where .= " AND datetime BETWEEN '$start_date' AND '$end_date'";
    }
}
echo json_encode(
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, $where )
);
