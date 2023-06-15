<?php
$id = $_GET['id'];
// DB table to use
$table = 'jobClicks';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array( 'db' => 'id', 'dt' => 0),
    array( 'db' => 'name', 'dt' => 1),
    array( 'db' => 'email', 'dt' => 2),
    array( 'db' => 'jobID', 'dt' => 3),
    array( 'db' => 'whenClicked', 'dt' => 4),
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
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, "jobID = '$id'" )
);
