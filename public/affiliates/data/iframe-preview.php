<?php
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/startup.php';
require $_SERVER['DOCUMENT_ROOT'].'/affiliates/data/data-functions.php';
//require(__DIR__ . '/../../../wp-load.php');
error_reporting(E_ALL);
//global $wpdb;
if(!isset($_POST['aff_id']) && !isset($_POST['product_ids']) && !isset($_POST['columns'])) {
	$output = $iframe = 'Issue Loading Iframe because one of the following elements are missing: product_ids, columns, aff_id';
	$resp = [
		'output' => $output,
		'iframe' => $iframe,
	];
	echo json_encode($resp);	
	die();
}
$protocol = 'https';
if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
	$protocol = 'http';
}
$affiliate_filter = filter_input(INPUT_POST, 'aff_id', FILTER_SANITIZE_STRING);
$columns = (isset($_POST['nr_columns'])) ? $_POST['nr_columns'] : '';
$checked_checkboxes = (isset($_POST['courses'])) ? $_POST['courses'] : '';
$show_categories = (isset($_POST['show_categories'])) ? intval($_POST['show_categories']) : 0;
//var_Dump($show_categories);
$selected_coupon_str = '';
$coupon_data = '';
$ih_item_square = 350;
$selected_voucher_id = '';
if(isset($_POST['selected_coupon']) && $_POST['selected_coupon'] !== '-1') {
	$selected_coupon_data = $_POST['selected_coupon'];
	$selected_coupon_str = '&selected_coupon='.$selected_coupon_data;
	$coupon_data = $_POST['selected_coupon'];
	$ih_item_square = 450;
	$selected_coupon_data_ex = explode('_', $selected_coupon_data);
	$selected_voucher_id = $_POST['selected_coupon'];
}
$courses_enc = json_encode($checked_checkboxes);
//var_Dump($courses_enc, $checked_checkboxes);
$sql = "SELECT aff_id FROM ap_iframe_generator WHERE aff_id = {$affiliate_filter}";

$dbData = ORM::for_table('table')->raw_query($sql, array())->find_many();

$affiliate_exists = count($dbData);
if(empty($checked_checkboxes)) {
	$output = $iframe = 'Please check at least one course';
	$resp = [
		'output' => $output,
		'iframe' => $iframe,
	];
	$resp = json_encode($resp);
	die($resp);
}
/*
alter table `ap_iframe_generator` add column selected_voucher_id int NOT NULL;
ALTER TABLE `ap_iframe_generator` CHANGE `selected_voucher_id` `selected_voucher_id` VARCHAR(11) NULL DEFAULT NULL;
*/
if(isset($_POST['update_sql']) && $_POST['update_sql'] == 1) {
	$sql = "INSERT INTO `ap_iframe_generator` (`aff_id`, `cols`, `courses`, `selected_voucher_id`, `show_categories`) VALUES ('{$affiliate_filter}', '{$columns}', '{$courses_enc}', '{$selected_voucher_id}', '{$show_categories}')";
	if($affiliate_exists > 0) {
		$sql = "UPDATE ap_iframe_generator SET cols = '{$columns}', courses = '{$courses_enc}', selected_voucher_id ='{$selected_voucher_id}', show_categories = '{$show_categories}' WHERE aff_id = '{$affiliate_filter}'";
	}
    $dbData = ORM::for_table('table')->raw_query($sql, array())->find_many();
}
switch($columns) {
	case 2:
		$width = '60';
		break;
	case 3: 
		$width = '70';
		break;
	case 4:
		$width = '90';
		break;
}
//$checked_checkboxes = $product_ids;
?>
<style>
	.home-content-area {
		background: #f4f7f9 none repeat scroll 0 0;
		margin: 30px 0 0;
		padding: 0 0 30px;
	}
	.home-content-area .row {
		width: <?php //echo $width ?>100% !important;
		margin: 0 auto;
	}
	.courses-listing {
		padding: 10px 40px;
	}
	.courses-listing {
		display: block;
		font-size: 0;
		margin: 0px auto 20px;
		background-color: #fff;
		padding: 30px 60px;
		border-left: 1px solid #f4f4f4;
		border-right: 1px solid #f4f4f4;
		border-bottom: 1px solid #f4f4f4;
	}
	.column {
		display: inline-block;
		float: initial !important;
		vertical-align: top;
		font-size: 13px;
		margin-bottom: 30px;
	}
	.row .onecol {
		width: 4.85%;
	}
	.row .twocol {
		width: 13.45%;
	}
	.row .two-halfcol {
		width: 20%;
		float: left;
		list-style: none;
	}
	.row .threecol {
		width: 22.05%;
	}
	.row .fourcol {
		width: 30.75%;
	}
	.row .fivecol {
		width: 39.45%;
	}
	.row .sixcol {
		width: 48%;
	}
	.row .sevencol {
		width: 56.75%;
	}
	.row .eightcol {
		width: 65.4%;
	}
	.row .ninecol {
		width: 74.05%;
	}
	.row .tencol {
		width: 82.7%;
	}
	.row .elevencol {
		width: 91.35%;
	}
	.row .twelvecol {
		width: 100%;
	}
	.ie .onecol {
		width: 4.7%;
	}
	.ie .twocol {
		width: 13.2%;
	}
	.ie .threecol {
		width: 22.05%;
	}
	.ie .fourcol {
		width: 30.6%;
	}
	.ie .fivecol {
		width: 39%;
	}
	.ie .sixcol {
		width: 48%;
	}
	.ie .sevencol {
		width: 56.75%;
	}
	.ie .eightcol {
		width: 61.6%;
	}
	.ie .ninecol {
		width: 74.05%;
	}
	.ie .tencol {
		width: 82%;
	}
	.ie .elevencol {
		width: 91.35%;
	}
	.column {
		position: relative;
		float: left;
		margin-right: 2%;
		min-height: 1px;
	}
	.post_item.post_item_courses {
		cursor: pointer;
		margin-bottom: 30px;
	}
	.ih-item.square {
		min-height: <?php echo $ih_item_square ?>px;
		overflow: hidden;
		position: relative;
		width: 100%;
		background-color: #f4f7f9;
		margin-bottom: 20px;
	}
	.course-preview .course-image {
		position: relative;
		z-index: 10;
		background: #fff;
	}
	.ih-item a, .ih-item a:hover {
		color: #fff;
	}
	.home .course-preview .course-image img {
		height: auto !important;
		min-width: auto;
	}
	.course-preview .course-image img {
		display: block;
		width: 100%;
		background: #fff;
	}
	.course-preview .course-meta {
		overflow: hidden;
	}
	.course-meta {
		border-radius: 0 !important;
		background-color: #fff;
	}
	.course-preview .course-header {
		position: relative;
		z-index: 9;
		padding: 0.5em 15px 0.4em;
		background: #f4f7f9;
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
	}
	.course-header h5 {
		text-align: center;
	}
	.course-header h5 {
		font-size: 20px;
		font-weight: 100;
		line-height: 21px;
		color: #262626;
		padding-top: 10px;
	}
	.nomargin {
		margin: 0!important;
	}
	.course-price {
		background: #e4e4e4;
	}
	.course-price .price-text {
		background-color: #f4f7f9 !important;
		color: #18abcd;
		display: block;
		font-size: 1.5em;
		min-width: 38px;
		padding: 0.2em 10px;
		text-align: center;
		font-weight: 400;
	}
	s, strike, del {
		text-decoration: line-through;
	}
	u, ins {
		text-decoration: none;
	}
	.find-more-now {
		margin-bottom: 10px;
	}
	.find-more-now .find-out-more {
		background: #262626 none repeat scroll 0 0;
		display: block;
		font-size: 17px;
		font-weight: 100;
		margin: 20px auto;
		padding: 9px 10px;
		text-align: center;
		width: 200px;
	}
	.home-content-area {
		margin: 0px;
		padding: 0px;
		background: #fff;
	}
	.courses-listing {
		border: initial;
	}
	.ih-item.square {
		background-color: #f4f7f9;
	}
	
	.number_of_child_courses {
		background-image: url("<?php echo get_template_directory() ?>/images/img-var-in-one.png");
		display: block;
		height: 278px;
		margin-left: 10px;
		margin-top: 10px;
		position: absolute;
		width: 272px;
		z-index: 9999;
	}
	.number-of-courses {
		color: #fff;
		display: block;
		margin-left: 0;
		margin-top: 10px;
		text-align: center;
	}
	.number-of-courses-count {
		font-size: 100px;
	}
	.number-of-courses-text {
		display: block;
		font-size: 35px;
		margin-left: 60px;
		margin-top: -30px;
		position: absolute;
	}
	.number_of_child_courses-list {
		background-image: url("<?php echo get_template_directory() ?>/images/img-var-in-one-list.png");
		display: block;
		height: 92px;
		margin-left: 4px;
		margin-top: 2px;
		position: absolute;
		width: 90px;
		z-index: 9999;
	}
	.number_of_child_courses-list .number-of-courses {
		color: #fff;
		display: block;
		margin-left: 0;
		margin-top: 10px;
		text-align: center;
	}
	.number_of_child_courses-list .number-of-courses-count {
		font-size: 32px;
	}
	.number_of_child_courses-list .number-of-courses-text {
		display: block;
		font-size: 12px;
		margin-left: 21px;
		margin-top: -13px;
		position: absolute;
		color: #FFF;
	}
	.number_of_child_courses-list .number-of-courses-text p {
		color: #FFF;
	}
	
	@media only screen and (max-width: 1199px) and (min-width: 1000px) {
		.row {
			width: 940px;
		}
	}
	/* ---------------------------- 768px-999px --------------------------- */

	@media only screen and (max-width: 999px) and (min-width: 768px) {
		.row {
			width: auto;
		}
	}
	@media only screen and (max-width: 1199px) and (min-width: 1000px) {
		.row {
			width: 940px;
		}
	}
	/* ------------------------------ 0-767px ---------------------------- */

	@media handheld,
	only screen and (max-width: 767px) {
		.row .column {
			margin: 0 0 3em 0;
			width: 100%;
		}
		.row .column > .column {
			margin-bottom: 1em;
		}
		.column.last,
		.courses-listing .column,
		.lessons-listing,
		.formatted-form .column {
			margin-bottom: 0;
		}
	}
	@media only screen and (max-width: 480px) {
	    .price-course {
			margin-left: 0% !important;
			font-size: 18px !important;
		}
		.buy-now-course {
			padding: 5px 1px !important;
		}
		.title-single {
			margin-top: 60px !important;
		}
		.product-name > span {
			vertical-align: middle;
			margin: 0px !important;
		}
		.ih-item.square {
			min-height: 390px;
			overflow: hidden;
			position: relative;
			width: 100%;
			background-color: #e4e4e4;
			margin-bottom: 20px;
		}
		.test-see-more a {
			background-color: #01aace;
			color: #fff;
			font-size: 20px;
			padding: 20px;
			position: relative;
		}
		.test-see-more {
			text-align: center;
			color: #fff;
		}
		.price-course {
			margin-right: 12px;
			line-height: 40px;
		}
		.number_of_child_courses {
			background-image: url("<?php echo get_template_directory() ?>/images/img-var-in-one.png");
			display: block;
			margin-left: 77px;
			margin-top: 6px;
			position: absolute;
			z-index: 9999;
			background-size: 70px 70px;
			background-repeat: no-repeat;
		}
		.number-of-courses-count {
			font-size: 25px;
			margin-bottom: 1px;
			display: block;
			margin-top: -10px;
			margin-left: -200px;
		}
		.number-of-courses-text {
			display: block;
			font-size: 9px;
			margin-left: 16px;
			margin-top: -28px;
			position: absolute;
		}
		.number_of_child_courses-list {
			background-image: url("<?php echo get_template_directory() ?>/images/img-var-in-one-list.png");
			display: block;
			height: 60px;
			margin-left: 21px;
			margin-top: 2px;
			position: absolute;
			width: 60px;
			z-index: 9999;
			background-size: 60px 60px;
			background-repeat: no-repeat;
		}
		.number_of_child_courses-list .number-of-courses {
			color: #fff;
			display: block;
			margin-left: 0;
			margin-top: 10px;
			text-align: center;
		}
		.number_of_child_courses-list .number-of-courses-count {
			font-size: 25px;
			margin-left: 0px !important;
		}
		.number_of_child_courses-list .number-of-courses-text {
			display: block;
			font-size: 8px;
			margin-left: 14px;
			margin-top: -26px;
			position: absolute;
			color: #FFF;
		}
		.number_of_child_courses-list .number-of-courses-text p {
			color: #FFF;
		}
		.find-more-now {
			bottom: 0px;
			position: absolute;
			width: 100%;
		}
		.courses-listing {
			padding: 0px;
		}
	}
	@media only screen and (min-width: 480px) and (max-width: 767px) {
		.find-more-now {
			bottom: 0px;
			position: absolute;
			width: 100%;
		}
	}
	@media only screen and (max-width: 990px) and (min-width: 767px) {
		.ih-item.square {
			min-height: <?php echo $ih_item_square - 20; ?>px;
			overflow: hidden;
			position: relative;
			width: 100%;
			background-color: #e4e4e4;
			margin-bottom: 20px;
		}
		.course-header h5 {
			font-size: 20px;
			font-weight: 100;
			color: #262626;
		}
	}
	.find-more-now .find-out-more {
		position: absolute;
		bottom: 0px;
		width: 100%;
	}
	@media screen and (max-width: 825px) {
		.find-more-now .find-out-more {
			position: static;
		}
	}
	.discount-text-iframe {
		text-align: center;
		margin-top: 10px;
		display: block;
		bottom: 70px;
		position: absolute;
		width: 100%;
	}
	@media only screen and (max-width: 999px) {
		.row .threecol, .row .fourcol, .row .sixcol {
			width: 100%;
		}
	}
</style>
<?php 
	if($show_categories == 1) {
		require $_SERVER['DOCUMENT_ROOT'].'/affiliates/data/iframe-body-cats.php';
	} else {
		require $_SERVER['DOCUMENT_ROOT'].'/affiliates/data/iframe-body.php';
	}
?>
<?php
$output = ob_get_contents();
ob_end_clean();

//var_dump($productstr);
//$output = 123;
$iframe = '<iframe allowtransparency="true" width="100%" style="max-width: 1150px; border:none" scrolling="no" src="'.site_url('', $protocol).'/affiliates/iframe.php?aff_id='.$affiliate_filter.$selected_coupon_str.'"></iframe>';
$iframe .= '
<script type="text/javascript" src="'.site_url('', $protocol).'/affiliates/assets/js/iframeResizer.min.js"></script> 
<script type="text/javascript">iFrameResize({log : false, enablePublicMethods: true, heightCalculationMethod: "lowestElement" });</script>';
$resp = [
	'output' => $output,
	'iframe' => $iframe,
];
echo json_encode($resp);