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

            return $item->firstname.' '.$item->lastname.' <a href="'.SITE_URL.'blume/accounts/view?id='.$item->accountID.'" target="_blank" class="label label-info"><i class="fa fa-user"></i></a>';
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
            return date('d/m/Y H:i:s', strtotime($d)+3600);

        }
    ),
    array(
        'db'        => 'total',
        'dt'        => 4,
        'formatter' => function( $d, $row ) {

            return '£'.number_format($d, 2);


        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 5,
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

$where = "status = 'completed' and method = 'stripe_pre_sub'";

if(@$_GET['startDate'] && @$_GET['endDate']){
    $where .= " and whenCreated >= '".$_GET['startDate']."' and whenCreated <= '".$_GET['endDate']."'";
}

echo json_encode(
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $where )
);