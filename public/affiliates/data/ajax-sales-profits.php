<?php
include_once '../auth/startup.php';
require '../auth/ssp.class.php';
require '../data/data-functions.php';
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
    array( 
        'db' => 'affiliate_id', 
        'dt' => 0,
        'formatter' => function( $affiliate_id, $row ) {
            global $mysqli;
            $get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id= '$affiliate_id'"));
            $affiliate_user = $get_affiliate['fullname'];
            $string = '<a href="affiliate-stats?a='.$affiliate_id.'">'; 
            if($affiliate_user!=''){
                $string .= $affiliate_user;
            } else {
                $string .= 'No Affiliate';
            } 
            $string .= '</a>';
            return $string;
        }
    ),
    array(
        'db' => 'product',
        'dt' => 1,
        'formatter' => function( $d, $row ) {
            return get_customer_name($d);
        }
    ),
    array(
        'db' => 'product',
        'dt' => 2,
        'formatter' => function( $d, $row ) {
            return get_customer_email($d);
        }
    ),
    array( 
        'db' => 'product',     
        'dt' => 3,
        'formatter' => function( $d, $row ) {
            return $d;
        }
    ),
    array( 
        'db' => 'product',     
        'dt' => 4,
        'formatter' => function( $d, $row ) {
            return get_product_bought($d);
        }
    ),
    array( 
        'db' => 'product',     
        'dt' => 5,
        'formatter' => function( $d, $row ) {
            return get_location_by_product($d);
        }
    ),
    array( 
        'db' => "sale_amount",     
        'dt' => 6,
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
        'dt' => 7,
        'formatter' => function( $d, $row ) {
            return $d.'%';
        }
    ),
    array( 
        'db' => "net_earnings",     
        'dt' => 8,
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
//    array(
//        'db' => 'recurring',
//        'dt' => 8,
//        'formatter' => function( $d, $row ) {
//            global $mysqli;
//            $affiliate_id = $row['affiliate_id'];
//            $get_rc_on = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT rc_on FROM ap_other_commissions WHERE id=1"));
//            $rc_on = $get_rc_on['rc_on'];
//            $string = '';
//            if($rc_on=='1'){
//                $data = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT stop_recurring, recurring_fee FROM ap_earnings WHERE affiliate_id = '$affiliate_id'"));
//                if($data['stop_recurring']=='1'){
//                    $string .= '<span class="red">Recurring Stopped</span>';
//                } else {
//                    if($row['recurring']=='Non-recurring' || $row['recurring']==''){
//                        $string .= 'Non-Recurring';
//                    } else {
//                        $recurring_fee = $data['recurring_fee'] / 100;
//                        $string .= $row['recurring'].' @ ';
//                        $mv = $row['sale_amount'] * $recurring_fee;
//                        $string .= $money_format->formatCurrency($mv, $currency_symbol);
//                        $string .= ' ('.$data['recurring_fee'].'%)';
//                    }
//                }
//            }
//            return $string;
//        }
//    ),
    array(
        'db' => 'datetime',
        'dt' => 9
    ),
    array(
        'db' => 'id',     
        'dt' => 10,
        'formatter' => function( $d, $row ) {
            global $mysqli;
            $sql = "SELECT void FROM ap_earnings WHERE id = '$d'";
            $query = mysqli_query($mysqli, $sql);
            $data = mysqli_fetch_assoc($query);
           // var_dump($data);
            $var = '';
            if($data['void'] != '1'){ 
                $var = '<button data-affiliate="'.$row['affiliate_id'].'" data-id="'.$d.'" class="btn btn-sm btn-inverse refund">Refund</button>';
            }
            $var .= '<buton data-affiliate="'.$row['affiliate_id'].'" data-id="'.$d.'" class="btn btn-sm btn-danger delete">Delete</button';
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
$where = '';
if(!empty($start_date) && !empty($end_date)) {
    if(strpos($start_date, '1970') === false && strpos($end_date, '1970') === false) {
        $where .= "datetime BETWEEN '$start_date' AND '$end_date'";
    }
}
echo json_encode(
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, $where )
);
