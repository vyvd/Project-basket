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
$table = 'courses';

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
        'db'        => 'title',
        'dt'        => 0
    ),
    array(
        'db'        => 'id',
        'dt'        => 1,
        'formatter' => function( $d, $row ) {
            $categories = $this->controller->getCourseCategories($d);

            $course = ORM::for_table("courses")->find_one($d);

            $icons = '';

            if($course->usImport == "1") {
                $icons .= '<span class="ncfeUser" style="background:#de0000;">US</span>';
            }

            if($course->isNCFE == "1") {
                $icons .= '<span class="ncfeUser">NCFE</span>';
            }

            $echo = '';
            foreach($categories as $category) {
                $echo .= $category.', ';
            }

            if(CUR_ID == 3 || CUR_ID == 1) {
                return '<a href="'.SITE_URL.'ajax?c=blumeNew&a=delete-course&id='.$d.'" onclick="return confirm(\'Are you sure you want to delete this course?\');"><i class="fa fa-trash"></i></a> '.$icons.' '.$echo;
            } else {
                return $icons.' '.$echo;
            }


        }
    ),
    array(
        'db'        => 'enrollmentCount',
        'dt'        => 2,
        'formatter' => function( $d, $row ) {

            return $d;

        }
    ),
    array(
        'db'        => 'is_video',
        'dt'        => 3,
        'formatter' => function( $d, $row ) {
            $checked = $d == 1 ? 'checked="checked"' : '';
            return '
            <input type="checkbox" onclick="handleClick(this,'."'is_video',".$row['id'].');" '.$checked.' class="updateCourseCol" rel="is_video-'.$row['id'].'">
            ';
        }
    ),
    array(
        'db'        => 'is_audio',
        'dt'        => 4,
        'formatter' => function( $d, $row ) {
            $checked = $d == 1 ? 'checked="checked"' : '';
            return '
            <input type="checkbox" onclick="handleClick(this,'."'is_audio',".$row['id'].');" '.$checked.' class="updateCourseCol" rel="is_audio-'.$row['id'].'">
            ';
        }
    ),
    array(
        'db'        => 'averageRating',
        'dt'        => 5,
        'formatter' => function( $d, $row ) {

            return $d;

        }
    ),
    array(
        'db'        => 'totalRatings',
        'dt'        => 6,
        'formatter' => function( $d, $row ) {

            return $d;

        }
    ),
    array(
        'db'        => 'whenAdded',
        'dt'        => 7,
        'formatter' => function( $d, $row ) {

            return date("d/m/Y", strtotime(substr($d,0,10)));

        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 8,
        'formatter' => function( $d, $row ) {

            return '
            <a href="'.SITE_URL.'blume/courses/edit?id='.$d.'" class="label label-system" title="Export (JSON)">
                                        <i class="fa fa-download"></i>
                                    </a>
                                    <a href="'.SITE_URL.'blume/courses/edit?id='.$d.'" class="label label-warning" style="margin-left:5px;cursor:pointer;">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="'.SITE_URL.'ajax?c=blumeNew&a=get-course-pdf&id='.$d.'" class="label label-system" title="Export (PDF)" target="_blank" style="margin-left: 5px;">
                                        <i class="fa fa-download"></i>
                                    </a>
                                    <a  href="#" data-course-id="' . $d . '" class="label label-system copyCourse" title="Copy Course" style="margin-right: 5px;">
                                        <i class="fa fa-copy"></i>
                                    </a>
                                    <label class="label label-danger" onclick="deleteItem('.$d.')"><i class="fa fa-trash"></i></label >
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
