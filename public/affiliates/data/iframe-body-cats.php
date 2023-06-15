<div class="home-content-area popular_courses iframe_popular_courses">
	<div class="row">
		<div id="sortable-cats" class="courses-listing iframe-course-listing clearfix">
		<?php
			$sql = "SELECT * from ap_iframe_generator WHERE aff_id = '{$affiliate_filter}'";
            $courseDetails = [];
            $termDetails = [];
			//var_dump($sql);
			//$checked_checkboxes = $wpdb->get_var($sql);
			$apIframe = mysqli_fetch_object(mysqli_query($mysqli, $sql));
            $checked_checkboxes = $apIframe->courses;


			if(!empty($checked_checkboxes)) {
				$checked_checkboxes = json_decode($checked_checkboxes);
			} else {
				$checked_checkboxes = $_POST['courses'];
			}

			$course_ids = $category_ids = $the_check_box = [];
			foreach($checked_checkboxes as $chk_checkbox) {
				$data = explode('_', $chk_checkbox);
				if(!in_array($data[0], array_keys($courseDetails)) && @$data[0]){
                    $sql = "SELECT id,title,slug,price from courses WHERE id = '".$data[0]."'";
                    $courseDetail = mysqli_fetch_object(mysqli_query($mysqli, $sql));
                    $courseDetail->url = 'https://'.$_SERVER['HTTP_HOST'].'/course/'.$courseDetail->slug;

                    $sql = "SELECT url from media WHERE modelType = 'courseController' and modelId = '".$courseDetail->id."' and type='main_image'";
                    $courseImage = mysqli_fetch_object(mysqli_query($mysqli, $sql));

                    $courseDetail->imageUrl = $courseImage->url ?? null;
                    //$courseDetail->priceFormat = $this->price($courseDetail->price);
                    $courseDetails[$data[0]] = $courseDetail;
                }
				$course_ids[] = $data[0];
				$category_ids[] = $data[1];
				$the_check_box[$data[1]][] = $data[0];
			}
			$category_ids = array_unique($category_ids);
			//var_dump($course_ids, $category_ids);
			//$layout=ThemexCore::getOption('courses_layout', 'fullwidth');
			//$view=ThemexCore::getOption('courses_view', 'grid');
			$layout = 'fullwidth';
			$view ='grid';
			$i = 0;
			$counter=0;
			$total = count($course_ids);
			if(in_array($layout, array('left', 'right'))) {
				$columns=$columns-1;
			}
			if($columns==4) {
				$width='three';
			} else if($columns==3) {
				$width='four';
			} else {
				$width='six';
			}
			$lastrow = ceil(floatval($total / $columns));
			$elems = $columns * ($lastrow - 1); // elements in the first rows except last one
			$start = $elems + 1;
			$course_ids = array_unique($course_ids);
			$the_course_ids = implode(', ', $course_ids);

			//$prev_ids = range(0, count($category_ids));
			//$prev_ids = array_values($prev_ids);
			//var_dump($prev_ids);
			$prev_ids = [];
			echo '<div class="filter_by_category category-item">Filter By Category<i class="fa fa-angle-down" aria-hidden="true"></i></div><br>';
			echo '<div class="categories-filter">';
			echo '<div id="all-courses" class="category-item">All Courses</div><br>';
			$the_course_ids_cat = [];


			foreach($category_ids as $term_id) {
			    $categoryIdColumn = 'id';
			    if ($apIframe->oldCourseID == 1){
                    $categoryIdColumn = 'oldID';
                    $sql = "SELECT id FROM courses WHERE oldID IN ($the_course_ids)";
                    $result = mysqli_query($mysqli, $sql);
                    $oldCourseIdsArray = [];
                    while($row = $result->fetch_assoc())
                    {
                        $oldCourseIdsArray[] = $row['id'];
                    }
                    $the_course_ids = implode(', ', $oldCourseIdsArray);
                    //$oldCourses =
                }
                $sql = "SELECT * from courseCategories WHERE $categoryIdColumn = '".$term_id."'";
                //$checked_checkboxes = $wpdb->get_var($sql);
                $term = mysqli_fetch_object(mysqli_query($mysqli, $sql));

                if(!in_array($term_id, array_keys($termDetails))){
                    $termDetails[$term_id] = $term;
                }


                $sql = "SELECT course_id FROM courseCategoryIDs WHERE category_id = $term->id AND course_id IN ($the_course_ids)";
                $result = mysqli_query($mysqli, $sql);
                $course_ids_cat = [];
                while($row = $result->fetch_assoc())
                {
                    $course_ids_cat[] = $row['course_id'];
                }
				$s = 0;
				?>
				<div id="<?php echo $term->slug ?>" class="category-item"><?php echo $term->title ?></div><br>
				<?php

				foreach($course_ids_cat as $course_id_cat) {
					if(in_array($course_id_cat.'_'.$term_id, $checked_checkboxes)) {
						$the_course_ids_cat[$term_id][] = $course_id_cat;
					}
				}
			}

			//var_dump($prev_ids);
			echo '</div>';
			echo '<div style="height: 20px;" class="clear"></div>';
			//var_dump($prev_ids);

			foreach($the_check_box as $term_id => $course_ids) {
				//var_dump($the_course_ids);
				$term = $termDetails[$term_id];

//				$sql = "SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = '{$term_id}' AND object_id IN ({$the_course_ids})";
//				$course_ids_cat = $wpdb->get_col($sql);
				if(!empty($course_ids)) {
			?>
				<div class="category-item-heading <?php echo $term->slug ?>"><?php echo $term->title ?><br></div>
				<div class="category-item-content <?php echo $term->slug ?>">
				<?php
					foreach($course_ids as $the_id) {
						if($i == 0) {
							$new_cat = 1;
						}
						$i++;
						$ids[] = $the_id;
						$class = '';
						if($i >= $start ) {
							$class = 'lastones';
						}
						?>
						<div data-product="<?php echo $the_id.'_'.$term_id?>" class="column newskill-course-single-cat <?php echo $width; ?>col <?php echo $class; ?>">
						<?php

                        /* Updated by Zubaer to Avoid unsupported characters in the title */
                        $courseDetails[$the_id]->title = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $courseDetails[$the_id]->title);

                        //var_dump($courseDetails[$the_id]);

                        //die();

							$args_transfer = [
								'affiliate_filter' => $affiliate_filter,
								'coupon_data' => $coupon_data,
								'the_id' => $the_id,
                                'course' => $courseDetails[$the_id]
							];

							//var_Dump($coupon_data);
							$ret = load_and_process($_SERVER['DOCUMENT_ROOT']."/affiliates/data/course-grid-affiliates-cats.php", $args_transfer); 
							echo $ret;

							//die();

						?>
						</div>
					<?php
					}
					?>
					<br>
					</div>
					<?php
				}
			}
			//var_Dump($prev_ids);
			$exclude = '';
			if (!empty($ids)) {
				$exclude = implode(',', $ids);
			}
			?>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(e) {
		$('#sortable-cats').on('click', '.filter_by_category', function(e) {
			$('.category-item-heading').show();
			$('.category-item-content').show();
			$('.categories-filter').slideToggle(600);
		});
		$('#sortable-cats').on('click', '.categories-filter .category-item', function(e) {
			$('.categories-filter').slideToggle(600);
			var id = $(this).attr('id');
			if($('.category-item-content, .category-item-heading').is(":visible")) {
				$('.category-item-content').not('.category-item-content.'+id).hide();
				$('.category-item-heading').not('.category-item-heading.'+id).hide();
			} else {
				$('.category-item-content').not('.category-item-content.'+id).show();
				$('.category-item-heading').not('.category-item-heading.'+id).show();
			}
			if(id == 'all-courses') {
				$('.categories-filter').hide(600);
				$('.category-item-heading').show();
				$('.category-item-content').show();
			}
			$('.category-item-heading.'+id).show();
			$('.category-item-content.'+id).show();

		});
	});
</script>