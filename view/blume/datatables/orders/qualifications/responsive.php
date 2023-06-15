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
$table = 'coursesAssigned';

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
        'db'        => 'accountID',
        'dt'        => 1,
        'formatter' => function( $d, $row ) {
            $item = ORM::for_table("accounts")->find_one($d);

            return $item->firstname.' '.$item->lastname;

        }
    ),
    array(
        'db'        => 'courseID',
        'dt'        => 2,
        'formatter' => function( $d, $row ) {
            $item = ORM::for_table("courses")->find_one($d);
            return $item->title;
        }
    ),
    array(
        'db'        => 'whenAssigned',
        'dt'        => 3
    ),
    array(
        'db'        => 'activated',
        'dt'        => 4,
        'formatter' => function( $d, $row ) {
            if($d == 1){
                return 'Activated';
            }else{
                return '
            <button class="btn btn-primary" type="button" onclick="activateItem('.$row['id'].')">Activate</button>
            ';
            }



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
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, "activated IN ('0','1')" )
);