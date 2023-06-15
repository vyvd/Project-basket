<?php
// DB table to use
$table = 'Banner';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array( 'db' => 'id', 'dt' => 0),
    array( 'db' => 'bannerTextGBP', 'dt' => 1),
    array( 'db' => 'bannerTextUSD', 'dt' => 2),
    array( 'db' => 'bannerTextEUR', 'dt' => 3),
    array( 'db' => 'bannerTextCAD', 'dt' => 4),
    array( 'db' => 'bannerTextAUD', 'dt' => 5),
    array( 'db' => 'bannerTextNZD', 'dt' => 6),
    array( 'db' => 'bannerColor', 'dt' => 7),
    array( 'db' => 'bannerTextColor', 'dt' => 8),
    array( 'db' => 'bannerRef', 'dt' => 9),
    array( 'db' => 'bannerTimer','dt'    => 10),
    array( 'db' => 'bannerState', 'dt' => 11),
    array(
        'db'        => 'id',
        'dt'        => 12,
        'formatter' => function( $d, $row ) {
            return '
            <label class="label label-warning editItem" data-edit="'.$d.'"><i class="fa fa-edit"></i></label>
            <label class="label label-danger" onclick="deleteItem('.$d.')"><i class="fa fa-trash"></i></label>
            <label class="label label-danger" onclick="changeBannerState('.$d.')"><i class="fa fa-toggle-on"></i></label>
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

require( APP_ROOT_PATH . 'classes/ssp.class.php' );

echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);
