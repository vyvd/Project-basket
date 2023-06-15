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
$table = 'orderItems';

// Table's primary key
$primaryKey = 'id';


// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array(
        'db'        => 'id',
        'dt'        => 0,
        'formatter' => function( $d, $row ) {

            return '<input type="checkbox" name="ids[]" value="'.$d.'" />
            ';

        }
    ),
    array(
        'db'        => 'orderID',
        'dt'        => 1,
        'formatter' => function( $d, $row ) {
            $item = ORM::for_table("orders")->find_one($d);

            return $item->firstname.' '.$item->lastname.' ('.$item->email.')';

        }
    ),
    array(
        'db'        => 'whenCreated',
        'dt'        => 2,
        'formatter' => function( $d, $row ) {
            return date('d/m/Y H:i:s', strtotime($d));

        }
    ),
    array(
        'db'        => 'certID',
        'dt'        => 3,
        'formatter' => function( $d, $row ) {

            $cert = ORM::for_table("coursesAssigned")->find_one($d);
            $course = ORM::For_table("courses")->find_one($cert->courseID);

            return $course->title;

        }
    ),
    array(
        'db'        => 'certNumber',
        'dt'        => 4,
        'formatter' => function( $d, $row ) {

            return $d;


        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 5,
        'formatter' => function( $d, $row ) {

            $item = ORM::for_table("orderItems")->find_one($d);

            if($item->status == "p") {
                return '
<span class="statusLabel'.$d.'">
<label class="label label-system">Pending</label>
<i class="fa fa-check dispatchCert" data-id="'.$d.'" style="cursor: pointer;margin-left: 5px;color: #50d8b0;font-size: 16px;"></i>
</span>
';
            } else {
                return '<span class="statusLabel'.$d.'"><label class="label label-success">Dispatched</label></span>';
            }


        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 6,
        'formatter' => function( $d, $row ) {

            $item = ORM::for_table("orderItems")->find_one($d);

            return '<a href="'.SITE_URL.'ajax?c=certificate&a=cert-pdf&id='.$item->certID.'&adminKey=gre45h-56trh_434rdfng" target="_blank" class="label label-warning"><i class="fa fa-folder"></i></a>
            
           <a href="'.SITE_URL.'blume/orders/view?id='.$item->orderID.'" target="_blank" class="label label-success" target="_blank"><i class="fa fa-eye"></i></a>
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
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, "certID != '' AND certPaid = '1'" )
);