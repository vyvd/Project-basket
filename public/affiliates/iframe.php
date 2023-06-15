<?php
ob_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/db-connect.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/affiliates/auth/access-functions.php';
require $_SERVER['DOCUMENT_ROOT'].'/affiliates/data/data-functions.php';
require_once ('auth/register.inc.php');
if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
	ini_set('display_errors', true);
	ini_set('display_startup_errors', true);
	error_reporting(E_ALL);
	//$_GET['aff_id'] = 35;
}
if(!isset($_GET['aff_id'])) {
	die('Issue Loading Iframe because one of the following elements are missing: aff_id');
}
/*
CREATE TABLE ap_iframe_generator (
    ID int AUTO_INCREMENT NOT NULL,
    aff_id INT,
    cols int,
    courses varchar(20000),
	PRIMARY KEY (ID)
);
*/
$affiliate_filter = $_GET['aff_id'];
$sql = "SELECT * FROM `ap_iframe_generator` WHERE aff_id = {$affiliate_filter}";
//$stmt = $mysqli->prepare($sql);
//$stmt->execute();
//$stmt->store_result();
//$affiliate_data = $wpdb->get_results($sql);
$affiliate_data = mysqli_fetch_object(mysqli_query($mysqli, $sql));
//echo "<pre>";
//print_r($affiliate_data);
//die;
$columns = 3;
$coupon_data = '';
$ih_item_square = 350;
$position = 'absolute';
if(!empty($affiliate_data)) {
	$columns = $affiliate_data->cols;
	$checked_checkboxes = (!empty($affiliate_data->courses)) ? json_decode($affiliate_data->courses) : [];
	$coupon_data = (isset($affiliate_data->selected_voucher_id)) ? ORM::for_table('coupons')->where('code', $affiliate_data->selected_voucher_id)->find_one() : '';
	$show_categories = (intval($affiliate_data->show_categories) !== 0) ? 1 : 0;
} else {
	$sql = "SELECT ID FROM `$wpdb->posts` WHERE `post_type` = 'product' ORDER BY ID DESC LIMIT 0, 10";
	$checked_checkboxes = $wpdb->get_col($sql);
}
if ($columns == 2) {
	$position = 'initial';
}
if(!empty($coupon_data)) {
	//$ih_item_square = 450;
	$ih_item_square = 520;
	if($columns == 2) {
		$ih_item_square = $ih_item_square + 70;
	}
	$position = 'absolute';
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Iframe Generator</title>
	<meta name="robots" content="noindex,nofollow" />
    <link href="<?php echo $affiliate_base_url; ?>assets/css/wp_style.css" rel="stylesheet">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/fonts/css/fa.css" rel="stylesheet">
	<link href='https://fonts.googleapis.com/css?family=Archivo+Narrow:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<style>
		.courses-listing {
			padding: 0px;
			background-color: inherit !important;
			border: none !important;
			margin: 0px auto 0px !important;
		}
		.home-content-area {
		    margin: 0px;
			padding: 0px;
			background: inherit;
		}
		.courses-listing {
			border: initial;
		}
		.ih-item.square {
			background-color: #f4f7f9;
			min-height: <?php echo $ih_item_square ?>px;
		}
		.home-content-area .row {
			width: 100% !important;
			margin: 0 auto;
			padding: 0px;
		}
		.find-more-now .find-out-more {
			/*position: initial;*/
			position: <?php echo $position ?>;
			bottom: 0px;
			width: 100%;
		}
		.discount-text-iframe {
			text-align: center;
			/*margin-top: 10px;*/
			display: block;
			/*bottom: 70px;*/
			position: absolute;
			width: 100%;
		}
		.home-content-area:before {
			display: none;
		}
		.lastones {
			margin-bottom: 0px !important;
		}
		.course-preview .course-image img {
		    max-height: 200px;
		}
		#sortable-cats .category-item {
			display: block;
			width: 100%;
			font-size: 22px;
			background: #f4f4f4;
			margin-bottom: 3px;
			padding: 10px 20px;
			cursor: pointer;
		}
		#sortable-cats .category-item-content {
			display: block;
			width: 100%;
		}
		#sortable-cats .filter_by_category {
			display: block;
			font-size: 25px;
			cursor: pointer;
		}
		.category-item-heading {
			font-size: 20px;
		}
		.filter_by_category i.fa-angle-down {
			display: block;
			width: 66px;
			height: 55px;
			color: #fff;
			background: #333333;
			float: right;
			vertical-align: top;
			line-height: 56px;
			text-align: center;
			font-size: 48px;
			margin-top: -10px;
			margin-right: -20px;
		}
		.categories-filter {
			display: none;
		}
		.column {
			display: inline-block;
			float: initial !important;
			vertical-align: top;
			font-size: 13px;
			margin-bottom: 30px;
		}
		.category-item-content div:nth-child(<?php echo $columns ?>n) {
			margin-right: 0px !important;
		}
		.category-item-heading {
			font-size: 30px;
			text-decoration: underline;
			margin-bottom: 20px;
		}


        #sortable-cats .category-item-content .course-meta {
            background: #f4f7f9;
        }

		@media handheld, only screen and (min-width: 480px) and (max-width: 767px) {

			.newskill-course-single-cat {
			    width: 48% !important;
			    margin: 1% !important;
			}

		}
		
		


	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
</head>
<body style="background: transparent;">
	<?php 	
	if($show_categories == 1) {

		require $_SERVER['DOCUMENT_ROOT'].'/affiliates/data/iframe-body-cats.php';
	} else {
		require $_SERVER['DOCUMENT_ROOT'].'/affiliates/data/iframe-body.php';
	} 
	?>
	<script type="text/javascript" src="<?php echo $affiliate_base_url; ?>assets/js/iframeResizer.contentWindow.min.js"></script>
	
	<div class="test" data-iframe-height></div>

</body>
</html>
<?php
$output = ob_get_contents();
ob_end_clean();
echo $output;