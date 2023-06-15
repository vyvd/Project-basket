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

            return $item->firstname.' '.$item->lastname;

        }
    ),
    array(
        'db'        => 'whenUpdated',
        'dt'        => 2,
        'formatter' => function( $d, $row ) {
            return date('d/m/Y H:i:s', strtotime($d));

        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 3,
        'formatter' => function( $d, $row ) {

            return ORM::for_table("orderItems")->where("orderID", $d)->count();

        }
    ),
    array(
        'db'        => 'status',
        'dt'        => 4,
        'formatter' => function( $d, $row ) {

            if($d == "p") {
                return '<label class="label label-success">Paid</label>';
            } else {
                return '<label class="label label-danger">Abandoned</label>';
            }


        }
    ),
    array(
        'db'        => 'total',
        'dt'        => 5,
        'formatter' => function( $d, $row ) {

            return '£'.number_format($d, 2);


        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 6,
        'formatter' => function( $d, $row ) {

            return '
            <label class="label label-info viewOrder" data-id="'.$d.'"><i class="fa fa-eye"></i></label>
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

echo json_encode(
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, "status = 'cancelled'" )
);