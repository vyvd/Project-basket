<div class="home-content-area popular_courses iframe_popular_courses">
    <div class="row">
        <div id="sortable"
             class="courses-listing iframe-courses-listing clearfix">
            <?php
            $course_ids = [];
            foreach ($checked_checkboxes as $chk_checkbox) {
                $data = explode('_', $chk_checkbox);
                $course_ids[] = $data[0];
            }
            $checked_checkboxes = array_unique($course_ids);
            //var_dump($checked_checkboxes);


            //		if (!empty($checked_checkboxes)) {
            //			if (in_array('0', $checked_checkboxes)) {
            //				if(count($checked_checkboxes) < 1) {
            //					echo $_POST['redirecting'];
            //					exit;
            //				} else {
            //					queryCoursesAjaxfuncAffiliates($checked_checkboxes);
            //				}
            //			} else {
            //				queryCoursesAjaxfuncAffiliates($checked_checkboxes);
            //			}
            //		} else {
            //			echo $_POST['redirecting'];
            //			exit;
            //		}

            //		$layout=ThemexCore::getOption('courses_layout', 'fullwidth');
            //		$view=ThemexCore::getOption('courses_view', 'grid');
            $layout = 'fullwidth';
            $view = 'grid';
            $i = 0;
            $counter = 0;
            $cIDs = implode(",", $checked_checkboxes);
            $sql = "SELECT id,title,slug,price,duration FROM courses WHERE id IN ($cIDs)";
            $result = mysqli_query($mysqli, $sql);

            $total = mysqli_num_rows($result);

            if (in_array($layout, array('left', 'right'))) {
                $columns = $columns - 1;
            }
            if ($columns == 4) {
                $width = 'three';
            } else {
                if ($columns == 3) {
                    $width = 'four';
                } else {
                    $width = 'six';
                }
            }
            $lastrow = ceil(floatval($total / $columns));
            $elems = $columns * ($lastrow
                    - 1); // elements in the first rows except last one
            $start = $elems + 1;

            while ($row = $result->fetch_assoc()) {
                $counter++;
                $i++;
                $the_id = $row['id'];
                $ids[] = $the_id;
                $class = '';
                if ($i >= $start) {
                    $class = 'lastones';
                }

                $courseDetail = $row;
                $courseDetail['url'] = 'https://'.$_SERVER['HTTP_HOST'].'/course/'.$courseDetail['slug'];
                $sql = "SELECT url from media WHERE modelType = 'courseController' and modelId = '".$courseDetail['id']."' and type='main_image'";
                $courseImage = mysqli_fetch_object(mysqli_query($mysqli, $sql));
                $courseDetail['imageUrl'] = $courseImage->url ?? null;

                ?>
                <div data-product="<?php echo $the_id ?>"
                     class="column <?php echo $width; ?>col <?php echo $counter
                     == $columns ? 'last ' : '';
                     echo $class; ?>">
                    <?php

                    /* Updated by Zubaer to Avoid unsupported characters in the title */
                    $courseDetail['title'] = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $courseDetail['title']);


                    $args_transfer = [
                        'affiliate_filter' => $affiliate_filter,
                        'coupon_data'      => $coupon_data,
                        'course' => $courseDetail
                    ];
                    $ret = load_and_process($_SERVER['DOCUMENT_ROOT']
                        ."/affiliates/data/course-grid-affiliates.php",
                        $args_transfer);
                    echo $ret;
                    ?>
                </div>
                <?php
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                    if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])
                        !== 'xmlhttprequest'
                    ) {
                        if ($counter == $columns) {
                            $counter = 0;
                            echo '<div class="clear"></div>';
                        }
                    }
                } else {
                    if ($counter == $columns) {
                        $counter = 0;
                        echo '<div class="clear"></div>';
                    }
                }
            }
            $exclude = '';
            if (!empty($ids)) {
                $exclude = implode(',', $ids);
            }
            ?>
        </div>
    </div>
</div>