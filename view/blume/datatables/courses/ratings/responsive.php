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
$table = 'courseRatings';

// Table's primary key
$primaryKey = 'id';


// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array(
        'db'        => 'courseID',
        'dt'        => 0,
        'formatter' => function( $d, $row ) {

            $course = ORM::For_table("courses")
                ->select("title")
                ->find_one($d);

            return $course->title;

        }
    ),
    array(
        'db'        => 'userID',
        'dt'        => 1,
        'formatter' => function( $d, $row ) {

            $user = ORM::for_table("accounts")
                ->select("firstname")
                ->select("lastname")
                ->find_one($d);

            return $user->firstname.' '.$user->lastname;

        }
    ),
    array(
        'db'        => 'rating',
        'dt'        => 2,
        'formatter' => function( $d, $row ) {
            return $d.'/5';
        }
    ),
    array(
        'db'        => 'whenRated',
        'dt'        => 3,
        'formatter' => function( $d, $row ) {
            return date('d/m/Y', strtotime($d));
        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 4,
        'formatter' => function( $d, $row ) {

            return '<label class="label label-danger" onclick="deleteItem('.$d.');" style="margin-left:5px;cursor:pointer;">
                                        <i class="fa fa-times"></i>
                                    </label>';

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
