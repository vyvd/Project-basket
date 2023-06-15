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
$table = 'offers';

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
    array( 'db' => 'code', 'dt' => 1 ),
    array(
        'db'        => 'courses',
        'dt'        => 2,
        'formatter' => function( $d, $row ) {

            if($d == "") {
                return "<em>All/Any</em>";
            } else {
                return count(explode(",", $d));
            }

        }
    ),
    array(
        'db'        => 'price',
        'dt'        => 3,
        'formatter' => function( $d, $row ) {

            return $this->price($d);

        }
    ),
    array(
        'db'        => 'dateStart',
        'dt'        => 4,
        'formatter' => function( $d, $row ) {

            return date('d/m/Y', strtotime($d));


        }
    ),
    array(
        'db'        => 'dateEnd',
        'dt'        => 5,
        'formatter' => function( $d, $row ) {

            if($d == "") {
                return '-';
            } else {
                return date('d/m/Y', strtotime($d));
            }


        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 6,
        'formatter' => function( $d, $row ) {

            return '<label class="label label-danger" onclick="deleteItem('.$d.')"><i class="fa fa-trash"></i></label>';


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
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);