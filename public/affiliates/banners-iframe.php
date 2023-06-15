<?php 
include('auth/startup.php');
include('data/data-functions.php');
require(__DIR__ . '/../../wp-load.php');
//SITE SETTINGS
list($meta_title, $meta_description, $site_title, $site_email) = all_settings();
$url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_STRING);
$affiliate_filter = $_SESSION['user_id'];
$latest = 10;
$sql = "SELECT cols, courses, show_categories FROM ap_iframe_generator WHERE aff_id = '$affiliate_filter'";
$affiliates = ORM::for_table('table')->raw_query($sql, array())->find_many();
$the_courses = 'no courses yet';
$columns = 0;
$show_categories = 0;
if(!empty($affiliates)) {
	$columns = $affiliates[0]->cols;
	$show_categories = $affiliates[0]->show_categories;
	$the_courses =  json_decode($affiliates[0]->courses);
	$selected_coupon = $affiliates[0]->selected_voucher_id;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo $meta_description;?>">
    <meta name="author" content="">

    <title><?php echo $meta_title;?></title>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- Bootstrap Core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
   	<link href="assets/css/base.css" rel="stylesheet">
	
	<!-- Elusive and Font Awesome Icons -->
    <link href="assets/fonts/css/elusive.css" rel="stylesheet">
	<link href="assets/fonts/css/fa.css" rel="stylesheet">
	<!-- Webfont -->
	<link href='https://fonts.googleapis.com/css?family=Archivo+Narrow:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
	<!-- Animation Effects -->
	
	<!-- SweetAlert Plugin -->
	<link href="assets/css/plugins/sweetalert.css" rel="stylesheet" media="all">
	<!-- Datatables Plugin -->
	<link href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.css" rel="stylesheet" media="all">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	<style>
	.choose-courses h3, .choose-courses button {
		display: inline-block;
	}
	.choose-courses button {
		margin-left: 20px;
	}
	.generate {
		float: right;
		margin-left: 0px;
		margin-top: 20px;
		background: #18aace;
		border: 0px;
		color: #fff;
		padding: 6px 15px;
		border-radius: 8px;
		font-size: 17px;
	}
	.container-inner h2,  .container-inner button{
		display: inline-block;
	}
	.container-inner button {
		margin-left: 25px;
		vertical-align: super;
	}
	</style>
</head>

<body>
	<!-- Start Top Navigation -->
	<?php include('assets/comp/top-nav.php');?>
    <!-- Start Main Wrapper --> 
	<div class="form-loader"></div>
   	<div id="wrapper">
		<!-- Side Wrapper -->
        <div id="side-wrapper">
            <ul class="side-nav">
                <?php include('assets/comp/side-nav.php');?>
			</ul>
        </div><!-- End Main Navigation --> 

        <!-- YOUR CONTENT GOES HERE -->
        <div id="page-content-wrapper" class="iframe-generator">
            <div class="container-fluid">
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-warning">
								<span class="title">Generate Iframe Products</span>
							</div>
							<div class="panel-content">
								<form action="data/iframe-preview.php" method="POST" class="generate-form-submit">
									<br><br>
									<?php 
										$select = select_vouchers_aff($affiliate_filter);
									?>
									<div class="box">
										<?php if($select) { ?>
										<div class="dropdown">
											<select class="selected_coupon" name="selected_coupon">
												<option value="-1">Select Voucher</option>
												<?php echo $select ?>
											</select>
										</div>
										<?php } else { ?>
											Please click <a href="<?php echo site_url() ?>/contact/">HERE</a> and Create New Vouchers
										<?php } ?>
										<br>
										This field is not required but if you wish to display within the iframe product box a discount voucher you may select one of the available voucher above.
									</div>
									<br>
									<div class="box">
										<input id="show_categories" class="columns-select" type="checkbox" name="show_categories" value="1" <?php checked_status_banner_iframe(1, intval($show_categories)) ?>>
										<label style="margin: 0px;" for="show_categories">Show Categories</label> 
										<br>
										This field is not required but if you wish to display within the iframe categories of the courses you may check the checkbox
									</div>
									<br><br>
									<div class="box">
										<span class="select-columns columns-select">Select number of columns</span>
										<?php if(isset($columns)) { ?>
										<input id="col2" class="columns-select" type="radio" name="nr_columns" value="2" <?php checked_status_banner_iframe(2, $columns) ?>>
										<label for="col2">2</label> 
										<input id="col3" class="columns-select" type="radio" name="nr_columns" value="3" <?php checked_status_banner_iframe(3, $columns) ?>>
										<label for="col3">3</label> 
										<input id="col4" class="columns-select" type="radio" name="nr_columns" value="4" <?php checked_status_banner_iframe(4, $columns) ?>>
										<label for="col4">4</label>
										<?php } else { ?>
										<input id="col2" class="columns-select" type="radio" name="nr_columns" value="2">
										<label for="col2">2</label> 
										<input id="col3" class="columns-select" type="radio" name="nr_columns" value="3" checked="checked">
										<label for="col3">3</label> 
										<input id="col4" class="columns-select" type="radio" name="nr_columns" value="4">
										<label for="col4">4</label>
										<?php } ?>
									</div>
									<br><br>
									<div class="box">
										<span class="generated_code_span">Generated Code <br>(copy and paste)</span>
										<textarea rows="9" name="generated_code" class="generated_code"></textarea>
									</div>
									<div class="choose-courses">
										<h3>Choose Courses</h3> 
										<button class="select-unselect">Select/Unselect All</button>
										<button type="submit" class="generate">Generate</button>
									</div>
									<?php
										//$categories = get_categories_courses();
                                        $categories = ORM::for_table("courseCategories")->where_null('parentID')->where_not_in('slug', array('all', 'mega-course', 'mini-courses', 'video', 'short-courses'))->order_by_asc("title")->find_many();
										$stored_courses = [];
										foreach($categories as $category) {
											//var_Dump($stored_courses);
										?>
										<div class="category_container">
											<div class="container-inner">
												<h2><?php echo $category->title; ?></h2>
												<button data-category="<?php echo $category->slug; ?>" class="select-unselect-cat">Select/Unselect All</button>
											</div>
										<?php
											//$courses = get_courses_by_category($category->term_id);
                                        $courseCategories = ORM::for_table("courseCategoryIDs")->where("category_id", $category->id)->find_many();
                                        $courseIDs = array();

                                        foreach($courseCategories as $courseCat) {

                                            array_push($courseIDs, $courseCat->course_id);

                                        }

                                        $courses = ORM::for_table("courses")->where_in("id", $courseIDs)->order_by_asc("title")->find_many();


											//var_dump($courses);
										//	array_sort_by_column($courses, 'post_title', SORT_ASC, 'OBJ');
											$checked = '';
											if($the_courses == 'no courses yet'){
												//var_Dump($stored_courses);
												$i = 0;
												foreach($courses as $course) {
													if(!in_array($course->title, $stored_courses)) {
														if($i < $latest) {
															$checked = 'checked="checked"';
														} else {
															$checked = '';
														}
													?>
													<div class="course-countainer">							
														<input id="course_<?php echo $course->id ?>" type="checkbox" name="courses[]" class="css-checkbox css-checkbox_<?php echo $category->slug; ?>" value="<?php echo $course->id.'_'.$category->id ?>" <?php echo $checked ?>>
														<label for="course_<?php echo $course->id ?>" class="css-label"><?php echo $course->title ?></label>
													</div>
													<?php
														$i++;
													}
													$stored_courses[] = $course->title;
												}
											} else {
												foreach($courses as $course) {
													if(!in_array($course->title, $stored_courses)) {

													    $is_course_checked = false;


													    if(!empty($category->id)) {

                                                            $course_cat_id = $course->id.'_'.$category->id;
                                                            if(in_array($course->id, $the_courses) || in_array($course_cat_id, $the_courses)) {
                                                                $is_course_checked = true;
                                                            }

                                                        } else {

                                                            if( in_array($course->id, $the_courses) ) {
                                                                $is_course_checked = true;
                                                            }

                                                        }

														if( $is_course_checked ) {
															$checked = 'checked="checked"';
														} else {
															$checked = '';
														}
													?>
													<div class="course-countainer">
														<input id="course_<?php echo $course->id ?>" type="checkbox" name="courses[]" class="css-checkbox css-checkbox_<?php echo $category->slug; ?>" value="<?php echo $course->id.'_'.$category->id ?>" <?php echo $checked ?>>
														<label for="course_<?php echo $course->id ?>" class="css-label"><?php echo $course->title ?></label>
													</div>
												<?php 
													}
													$stored_courses[] = $course->title;
												}
											}
											?>
										</div>
										<?php
										//break;
										}
									?>
									<br><br>
									<input type="hidden" id="aff_id" name="aff_id" value="<?php echo $affiliate_filter ?>">
									<button type="submit" class="generate">Generate</button>
									<br><br>
								</form>
								<h3>Preview</h3>
								<span class="description">All Products/Courses are movable. You may drag and drop the containers in order to changed their order. You don't need to click Generate as this will re-order them as default. Simply drag and drop.</span>
								<div class="container-courses-preview">
									<span class="no-courses">No Courses Selected Yet</span>
								</div>
							</div>
						</div>
					</div>
					<!-- End Panel -->
				</div>
			</div>
        <!-- End Page Content -->
		</div>
	</div><!-- End Main Wrapper  -->
   
    <!-- jQuery -->
    <script src="assets/js/jquery.js"></script>

    <!-- Bootstrap -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- Base JS -->
   	<script src="assets/js/base.js"></script>
	<!-- SweetAlert -->
	<script src="assets/js/plugins/sweetalert.min.js"></script>
	<!-- Datatables -->
	<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
	$(document).ready(function() {
		$('#users').DataTable();
	} );
	$('.select-unselect').click(function (e) {
	   e.preventDefault();
	   var checkBoxes = $(".css-checkbox");
       checkBoxes.prop("checked", !checkBoxes.prop("checked"));
	});
	$('.select-unselect-cat').click(function (e) {
	   e.preventDefault();
	   var category = $(this).data('category');
	   var checkBoxes = $(".css-checkbox_"+category);
       checkBoxes.prop("checked", !checkBoxes.prop("checked"));
	});
	function submitFormAjax(regenrate = false, update = 0) {
		//var newCustomerForm = $('.generate-form-submit').serialize();
		var newCustomerForm = $('.generate-form-submit').find('input[name!=generated_code], select').serialize();


		newCustomerForm = newCustomerForm + '&update_sql=' + update;
		var url = $('.generate-form-submit').attr('action');
		$.ajax({
			type:"POST",
			url: url,
			dataType: "json",
			data: newCustomerForm,
			beforeSend: function (data) {
				if(regenrate) {
					$('.generated_code').text('Generating Default Code...');
					$('.loading-ns').show();
				}
				$('.container-courses-preview').html('Generating Default Products...');
			},
			success:function(data){
				if(regenrate) {
					$('.generated_code').text(data.iframe);
					$('.loading-ns').hide();
				}
				$('.container-courses-preview').html(data.output);
			},
			complete:function(data) {
				$('#sortable, #sortable-cats .category-item-content').sortable({
					helper: 'clone',
					revert: 'invalid',
					start: function(e, ui) {
						ui.placeholder.height(ui.item.height());
						ui.placeholder.css('visibility', 'visible');
					},
					update: function(data) {
						var dataList = $(".column").map(function() {
							return $(this).data("product");
						}).get();
						$.post('<?php echo site_url('/affiliates/data/update-data-iframe.php') ?>', {'courses':dataList, 'aff_id': $('#aff_id').val(), 'send_from_ordering': 1});
					}
				}).disableSelection();
			}
		});
	}
	//$( ".generate-form-submit" ).submit(function( event ) {
	/*$( ".css-checkbox, .columns-select" ).click(function( event ) {
		submitFormAjax(regenrate = false, update = 1);
	});*/
	$( ".generate" ).click(function( event ) {
		event.preventDefault();
		submitFormAjax(regenrate = true, update = 1);
	});
	$( ".selected_coupon" ).change(function( event ) {
		var $selectedVal = event.target.value;
		$( ".selected_coupon option[value='"+$selectedVal+"']" ).attr('selected', true);
		submitFormAjax(regenrate = true, update = 1);
	});
	$( document ).ready(function( event ) {
		submitFormAjax(regenrate = true);
	});
	$(".generated_code").focus(function() {
		var $this = $(this);
		$this.select();
		// Work around Chrome's little problem
		$this.mouseup(function() {
			// Prevent further mouseup intervention
			$this.unbind("mouseup");
			return false;
		});
	});
	</script>
</body>
</html>
