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
    array( 
        'db' => 'affiliate_id', 
        'dt' => 0,
        'formatter' => function( $affiliate_id, $row ) {
            global $mysqli;
            $get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id= '$affiliate_id'"));
            $affiliate_user = $get_affiliate['fullname'];
            $string = '<a target="_blank" href="affiliate-stats?a='.$affiliate_id.'">'; 
            if($affiliate_user!=''){
                $string .= $affiliate_user;
            } else {
                $string .= 'No Affiliate';
            } 
            $string .= '</a>';
            return $string;
        }
    ),
    array( 'db' => 'ip', 'dt' => 1 ),
    array( 
        'db' => 'agent',     
        'dt' => 2,
        'formatter' => function( $d, $row ) {
            return substr_replace($d, '...', 100) ;
        }
    ),
    array( 'db' => 'host_name',   'dt' => 3 ),
    array( 
    	'db' => 'landing_page',     
    	'dt' => 4,
    	'formatter' => function( $d, $row ) {
            return $d;
        }
    ),
    array(
    	'db' => 'landing_page',     
    	'dt' => 5,
    	'formatter' => function( $d, $row ) {
            return '<a href="'.the_course_bought($row['landing_page'])['course_link'].'" target="_blank">'.the_course_bought($row['landing_page'])['course_name'].'</a>';
        }
    ),
    array(
        'db'        => 'cpc_earnings',
        'dt'        => 6,
        'formatter' => function( $d, $row ) {
        	global $money_format, $currency_symbol;
            if($d=='0'){
            	return $money_format->formatCurrency('0.00', $currency_symbol);
           	} else { 
           		return $money_format->formatCurrency($d, $currency_symbol); 
           	}
        }
    ),
    array( 'db' => 'datetime',   'dt' => 7 ),
    array(
        'db' => 'id',     
        'dt' => 8,
        'formatter' => function( $d, $row ) {
            global $mysqli;
            $sql = "SELECT void FROM ap_earnings WHERE id = '$d'";
            $query = mysqli_query($mysqli, $sql);
            $data = mysqli_fetch_assoc($query);
            $var = '<buton data-affiliate="'.$row['affiliate_id'].'" data-id="'.$d.'" class="btn btn-sm btn-danger delete">Delete</button';
            return $var;
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
 
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];
$start_date = date('Y-m-d h:i:s', strtotime($start_date));
$end_date = date('Y-m-d h:i:s', strtotime($end_date));
$where = "";
if(!empty($start_date) && !empty($end_date)) {
    if(strpos($start_date, '1970') === false && strpos($end_date, '1970') === false) {
        $where = "datetime BETWEEN '$start_date' AND '$end_date'";
    }
}
//var_dump($_GET);
echo json_encode(
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, $where )
);
