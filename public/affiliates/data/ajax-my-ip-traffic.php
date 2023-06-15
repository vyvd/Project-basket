<?php
include_once '../auth/startup.php';
require '../auth/ssp.class.php';
require '../data/data-functions.php';
if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    error_reporting(E_ALL);
}
$table = 'ap_referral_traffic';
 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
$owner = $_GET['affiliate_id'];
$course_name = $_GET['landing_page'];
$lp = 'http://'.$_SERVER['HTTP_HOST'].'/course/'.$course_name;
$sql = "SELECT landing_page FROM ap_referral_traffic WHERE affiliate_id = '$owner' AND landing_page LIKE '$lp%'";
$result = mysqli_query($mysqli, $sql);
$landing_page = mysqli_fetch_assoc($result);
//var_dump($owner);
$columns = array(
	array('db' => 'ip', 'dt' => 0),
    array( 
        'db' => 'ip',     
        'dt' => 1,
        'formatter' => function( $d, $row ) {
        	global $mysqli;
        	$country_flag = get_country_flag_by_ip($mysqli, $d);
        	$GLOBALS['country_flag'] = $country_flag;
            return $country_flag;
        }
    ),
    array( 
        'db' => 'ip',     
        'dt' => 2,
        'formatter' => function( $d, $row ) {
        	global $mysqli;
        	$data = $GLOBALS['country_flag'];
        	$data_flag = explode('/', $data);
	       	$country_code = str_replace(['.png', '">'], '', end($data_flag));
	       	$GLOBALS['country_code'] = $country_code;
            return strtoupper($country_code);
        }
    ),
    array( 
        'db' => 'ip',     
        'dt' => 3,
        'formatter' => function( $d, $row ) {
        	global $mysqli;
        	$country_name = get_country_name($GLOBALS['country_code']);
            return $country_name;
        }
    ),
    array('db' => 'datetime', 'dt' => 4),
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
$start_date = date('Y-m-d h:i:s', strtotime($start_date));
$end_date = date('Y-m-d h:i:s', strtotime($end_date));
$where = "affiliate_id = '$owner' AND landing_page LIKE '{$lp}%'";
if(!empty($start_date) && !empty($end_date)) {
    if(strpos($start_date, '1970') === false && strpos($end_date, '1970') === false) {
        $where .= " AND datetime BETWEEN '$start_date' AND '$end_date'";
    }
}
echo json_encode(
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, $where )
);
