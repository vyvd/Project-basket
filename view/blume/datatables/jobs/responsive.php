<?php
// DB table to use
$table = 'jobs';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array( 'db' => 'id', 'dt' => 0),
    array( 'db' => 'jobTitle', 'dt' => 1),
    array( 'db' => 'jobDescription', 'dt' => 2),
    array( 'db' => 'companyName', 'dt' => 3),
    array( 'db' => 'location', 'dt' => 4),
    array( 'db' => 'salary','dt'    => 5),
    array( 'db' => 'applicationLink', 'dt' => 6),
    array( 'db' => 'closingDate', 'dt' => 7),
    array( 'db' => 'datePosted','dt'   => 8),
    array( 'db' => 'clickAmount','dt'   => 9),
    array( 'db' => 'jobState','dt'   => 10),

    array(
        'db'        => 'id',
        'dt'        => 11,
        'formatter' => function( $d, $row ) {
            return '
            <label class="label label-warning editItem" data-edit="'.$d.'"><i class="fa fa-edit"></i></label>
            <label class="label label-danger" onclick="deleteItem('.$d.')"><i class="fa fa-trash"></i></label>
            <label class="label label-danger" onclick="changeJobState('.$d.')"><i class="fa fa-toggle-on"></i></label>
            <a href="'.SITE_URL.'blume/jobs/viewClickInfo?id='.$d.'" target="_blank" class="label label-info" target="_blank"><i class="fa fa-eye"></i></label>

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
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns)
);
