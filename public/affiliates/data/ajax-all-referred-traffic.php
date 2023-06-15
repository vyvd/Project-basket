<?php
include_once '../auth/startup.php';
require '../auth/ssp.class.php';
require '../data/data-functions.php';
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);
$table = 'ap_referral_traffic';
 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
$columns = array(
    array( 'db' => 'ip', 'dt' => 0 ),
    array( 
        'db' => 'agent',     
        'dt' => 1,
        'formatter' => function( $d, $row ) {
            return substr_replace($d, '...', 100) ;
        }
    ),
    array( 'db' => 'host_name',   'dt' => 2 ),
    array( 
    	'db' => 'landing_page',     
    	'dt' => 3,
    	'formatter' => function( $d, $row ) {
            return $d;
        }
    ),
    array(
    	'db' => 'landing_page',     
    	'dt' => 4,
    	'formatter' => function( $d, $row ) {
            return '<a href="'.the_course_bought($row['landing_page'])['course_link'].'" target="_blank">'.the_course_bought($row['landing_page'])['course_name'].'</a>';
        }
    ),
    array(
        'db'        => 'cpc_earnings',
        'dt'        => 5,
        'formatter' => function( $d, $row ) {
        	global $money_format, $currency_symbol;
            if($d=='0'){
            	return $money_format->formatCurrency('0.00', $currency_symbol);
           	} else { 
           		return $money_format->formatCurrency($d, $currency_symbol); 
           	}
        }
    ),
    array( 'db' => 'datetime',   'dt' => 6 ),
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
$where = "affiliate_id = '$owner'";
if(!empty($start_date) && !empty($end_date)) {
    if(strpos($start_date, '1970') === false && strpos($end_date, '1970') === false) {
        $where .= " AND datetime BETWEEN '$start_date' AND '$end_date'";
    }
}
//echo $where;
echo json_encode(
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, $where )
);
