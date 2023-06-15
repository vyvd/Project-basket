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
$table = 'courseReviews';

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

            return '<input type="checkbox" name="ids[]" value="'.$d.'" />
            ';

        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 1,
        'formatter' => function( $d, $row ) {
            $review = ORM::for_table('courseReviews')->find_one($d);

            if($review->courseID == null) {
                return $review->course;
            } else {
                $course = ORM::for_table("courses")->find_one($review->courseID);
                return $course->title;
            }

        }
    ),
    array(
        'db' => 'id',
        'dt' => 2,
        'formatter' => function ($d, $row ){
            $review = ORM::for_table('courseReviews')->find_one($d);
            $media = ORM::for_table('media')
                ->where('modelType', 'courseReviewController')
                ->where('modelId', $d)
                ->find_one();

            $view = '';
            if(@$media->url){
                $imageUrl = str_replace($media->fileName, 'thumb/'.$media->fileName, $media->url);
                $view .= '<img src="' . $imageUrl.'" style="width:30px;height:30px;border-radius:50%;margin-right:6px;" />';
            }
            $view .= $review->firstname . " ". $review->lastname;
            return $view;
        }
    ),
    array(
        'db'        => 'email',
        'dt'        => 3,
    ),
    array(
        'db'        => 'rating',
        'dt'        => 4,
    ),
    array(
        'db'        => 'whenSubmitted',
        'dt'        => 5,
        'formatter' => function( $d, $row ) {
            return date('d/m/Y', strtotime($d));
        }
    ),
    array(
        'db'        => 'status',
        'dt'        => 6,
        'formatter' => function( $d, $row ) {

            if($d == "a") {
                return '<label class="label label-success">Approved</label>';
            } else {
                return '<label class="label label-system">Pending</label>';;
            }

        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 7,
        'formatter' => function( $d, $row ) {
            return '<a href="javascript:;" class="label label-info editItem" data-edit="'.$d.'">View</a>
            <label class="label label-danger" onclick="deleteItem('.$d.')"><i class="fa fa-times"></i></label>';
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
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, "status != 'r'" )
);