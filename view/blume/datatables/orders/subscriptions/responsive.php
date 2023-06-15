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
$table = 'subscriptions';

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
        'db'        => 'orderID',
        'dt'        => 1,
        'formatter' => function( $d, $row ) {
            $order = ORM::for_table("orders")->find_one($d);

            return "<a href='".SITE_URL.'blume/accounts/view?id='.$order->accountID."'>".$order->firstname.' '.$order->lastname."</a>";

        }
    ),
    array(
        'db'        => 'whenAdded',
        'dt'        => 2,
        'formatter' => function( $d, $row ) {
            return date('d/m/Y H:i:s', strtotime($d));

        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 3,
        'formatter' => function( $d, $row ) {
            $schedule = ORM::for_table('subscription_schedules')
                ->where('subscriptionID', $d)
                ->order_by_asc('dueDate')
                ->find_one();

            $style = '';

            if($schedule->isPayed == 1) {
                $style = 'color:green;';
            }else if($schedule->dueDate < date("Y-m-d")) {
                $style = 'color:red;';
            }

            $view = '<span style="'.$style.'">'."£".number_format($schedule->amount,2) . '</span><br>';
            $view .= '<span style="'.$style.'">'. date('d/m/Y', strtotime($schedule->dueDate)) . '</span>';
            return $view;
        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 4,
        'formatter' => function( $d, $row ) {
            $schedule = ORM::for_table('subscription_schedules')
                ->where('subscriptionID', $d)
                ->order_by_asc('dueDate')
                ->offset(1)
                ->find_one();
            $style = '';

            if($schedule->isPayed == 1) {
                $style = 'color:green;';
            }else if($schedule->dueDate < date("Y-m-d")) {
                $style = 'color:red;';
            }

            $view = '<span style="'.$style.'">'."£".number_format($schedule->amount,2) . '</span><br>';
            $view .= '<span style="'.$style.'">'. date('d/m/Y', strtotime($schedule->dueDate)) . '</span>';
            return $view;
        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 5,
        'formatter' => function( $d, $row ) {
            $schedule = ORM::for_table('subscription_schedules')
                ->where('subscriptionID', $d)
                ->order_by_asc('dueDate')
                ->offset(2)
                ->find_one();
            $style = '';

            if($schedule->isPayed == 1) {
                $style = 'color:green;';
            }else if($schedule->dueDate < date("Y-m-d")) {
                $style = 'color:red;';
            }

            $view = '<span style="'.$style.'">'."£".number_format($schedule->amount,2) . '</span><br>';
            $view .= '<span style="'.$style.'">'. date('d/m/Y', strtotime($schedule->dueDate)) . '</span>';
            return $view;
        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 6,
        'formatter' => function( $d, $row ) {
            $schedule = ORM::for_table('subscription_schedules')
                ->where('subscriptionID', $d)
                ->order_by_asc('dueDate')
                ->offset(3)
                ->find_one();
            $style = '';

            if($schedule->isPayed == 1) {
                $style = 'color:green;';
            }else if($schedule->dueDate < date("Y-m-d")) {
                $style = 'color:red;';
            }

            $view = '<span style="'.$style.'">'."£".number_format($schedule->amount,2) . '</span><br>';
            $view .= '<span style="'.$style.'">'. date('d/m/Y', strtotime($schedule->dueDate)) . '</span>';
            return $view;
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
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, "status = '1'" )
);