<?php
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'orders';

// Table's primary key
$primaryKey = 'id';


// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array(
        'db'        => 'id',
        'dt'        => 0
    ),
    array(
        'db'        => 'id',
        'dt'        => 1,
        'formatter' => function( $d, $row ) {
            $item = ORM::for_table("orders")->find_one($d);

            $source = '';

            if($item->utm_source == "google") {
                $source = '<i class="fa fa-google" style="color: #E34135;margin-left: 2px;"></i>';
            } else if($item->utm_source == "facebook") {
                $source = '<i class="fa fa-facebook" style="color: #4867AA;margin-left: 2px;"></i>';
            } else if($item->utm_source == "bing") {
                $source = '<span style="color: #007F6F;margin-left: 2px;">B</span>';
            }

            if($item->utm_medium == "email") {
                $source = '<i class="fa fa-envelope" style="color: #d19425;margin-left: 2px;"></i>';
            }

            return $item->firstname.' '.$item->lastname.' <a href="'.SITE_URL.'blume/accounts/view?id='.$item->accountID.'" target="_blank" class="label label-info"><i class="fa fa-user"></i></a> '.$source;
        }
    ),
    array(
        'db'        => 'email',
        'dt'        => 2,
        'formatter' => function( $d, $row ) {
            return $d;

        }
    ),
    array(
        'db'        => 'whenUpdated',
        'dt'        => 3,
        'formatter' => function( $d, $row ) {
            return date('d/m/Y H:i:s', strtotime($d));

        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 4,
        'formatter' => function( $d, $row ) {

            $item = ORM::for_table("orderItems")->where("orderID", $d)->find_one();

            if($item->id == "") {
                return '0';
            } else {

                $text = '';

                if($item->course == "1") {
                    $course = ORM::for_table("courses")->find_one($item->courseID);
                    if($course->title == "") {
                        $course = ORM::for_table("courses")->where("oldID", $item->courseID)->find_one();
                    }
                    $text = $course->title;
                }
                else if($item->voucherID != "") {
                    // then its a gifted voucher
                    $voucher = ORM::for_table("vouchers")->find_one($item->voucherID);
                    $course = ORM::for_table("courses")->find_one($voucher->courses);
                    $text = 'Gift Voucher for '.$course->title;
                }
                else if($item->premiumSubPlanID != "") {
                    $text = 'Subscription';
                }
                else {
                    // then its a cert.
                    $cert = ORM::for_table("coursesAssigned")->find_one($item->certID);

                    $text = 'Cert: '.$cert->certNo;

                }

                if (strlen($text) > 30) {
                    $text = substr($text, 0, 30) . '...';
                }

                return '('.ORM::for_table("orderItems")->where("orderID", $d)->count().') '.$text;

            }


        }
    ),
    array(
        'db'        => 'method',
        'dt'        => 5,
        'formatter' => function( $d, $row ) {

            if($d == "stripe_cc" || $d == "stripe_pay" || $d == "stripe_pre_sub") {
                $d = 'stripe';
            }

            return $d;


        }
    ),
    array(
        'db'        => 'currencyID',
        'dt'        => 6,
        'formatter' => function( $d, $row ) {

            $currency = ORM::for_table("currencies")->find_one($d);
            return $currency->code;

        }
    ),
    array(
        'db'        => 'total',
        'dt'        => 7,
        'formatter' => function( $d, $row ) {

            return number_format($d, 2);


        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 8,
        'formatter' => function( $d, $row ) {

            return '
            <a href="'.SITE_URL.'blume/orders/view?id='.$d.'" target="_blank" class="label label-success" target="_blank"><i class="fa fa-eye"></i></a>
            ';


        }
    ),
);

// SQL server connection information
$sql_details = array(
    'user' => DB_USERNAME,
    'pass' => DB_PASS,
    'db'   => DB_NAME,
    'host' => DB_HOST
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require( APP_ROOT_PATH . 'classes/ssp.class.php' );

$where = "status = 'completed'";

if(@$_GET['startDate'] && @$_GET['endDate']){
    $where .= " and whenCreated >= '".$_GET['startDate']."' and whenCreated <= '".$_GET['endDate']."'";
}

echo json_encode(
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $where )
);