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
$table = 'courseModules';

// Table's primary key
$primaryKey = 'id';


// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array(
        'db'        => 'ord',
        'dt'        => 0
    ),
    array(
        'db'        => 'id',
        'dt'        => 1,
        'formatter' => function( $d, $row ) {
            $item = ORM::for_table("courseModules")->find_one($d);
            return $item->title;
        }
    ),
    array(
        'db'        => 'estTime',
        'dt'        => 2,
    ),
    array(
        'db'        => 'id',
        'dt'        => 3,
        'formatter' => function( $d, $row ) {
            return '<a href="'.SITE_URL.'blume/courses/modules/edit?id='.$d.'" class="label label-warning"><i class="fa fa-edit"></i></a> 
<label class="label label-danger" onclick="deleteModuleItem('.$d.')"><i class="fa fa-trash"></i></label>
<a href="#" data-module-id="' . $d . '" data-course-id="' . $_GET['courseID'] . '" class="label label-warning copyCourseModule"><i class="fa fa-copy"></i></a>';
        }
    )
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
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, "courseID = '".$_GET["courseID"]."' and parentID IS NULL" )
);