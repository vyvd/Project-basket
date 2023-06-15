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
$table = 'coupons';

// Table's primary key
$primaryKey = 'id';

/*
 *  <th>Code</th>
                                <th>Value</th>
                                <th>Uses</th>
                                <th>Expiry</th>
                                <th>Actions</th>
 */

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array(
        'db'        => 'id',
        'dt'        => 0,
        'formatter' => function( $d, $row ) {

            return '<input type="checkbox" name="ids[]" value="'.$d.'" /> '.$d;

        }
    ),
    array( 'db' => 'code', 'dt' => 1 ),
    array(
        'db'        => 'id',
        'dt'        => 2,
        'formatter' => function( $d, $row ) {
            $item = ORM::for_table("coupons")->find_one($d);

            if($item->type == "p") {
                return $item->value.'%';
            } else {
                return '£'.number_format($item->value, 2);
            }

        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 3,
        'formatter' => function( $d, $row ) {
            $item = ORM::for_table("coupons")->find_one($d);

            return $item->totalUses.'/'.$item->totalLimit;

        }
    ),
    array(
        'db'        => 'expiry',
        'dt'        => 4,
        'formatter' => function( $d, $row ) {

            if($d == "") {
                return "<em>n/a</em>";
            } else {
                return date('d/m/Y H:i:s', strtotime($d));
            }

        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 5,
        'formatter' => function( $d, $row ) {

            return '
            <label class="label label-warning editItem" data-edit="'.$d.'"><i class="fa fa-edit"></i></label>
            <label class="label label-danger" onclick="deleteItem('.$d.')"><i class="fa fa-trash"></i></label>
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
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);