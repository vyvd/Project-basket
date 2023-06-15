<?php 
ob_start();
ini_set('xdebug.max_nesting_level', 2000);
include_once '../auth/startup.php';
require '../data/data-functions.php';
require __DIR__.'/../../../wp-load.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
global $wpdb;
$affiliate_id = intval(filter_input(INPUT_POST, 'af', FILTER_SANITIZE_NUMBER_INT));
$redirect = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_STRING);
$report_id = intval(filter_input(INPUT_POST, 'report_id', FILTER_SANITIZE_NUMBER_INT));
$result = mysqli_query($mysqli, "SELECT * FROM ap_members WHERE id=$affiliate_id");
$get_affiliate = mysqli_fetch_assoc($result);
$sql = "SELECT date_gen FROM ap_reports WHERE ID = {$report_id}";
$result = mysqli_query($mysqli, $sql);
//var_Dump($result);
$report = mysqli_fetch_assoc($result);
$date_gen = $report['date_gen'];
$month = date('m', strtotime($date_gen));
$year = date('Y', strtotime($date_gen));
$last_day = date('t', strtotime($date_gen));
//$sql = "SELECT * FROM ap_earnings WHERE affiliate_id={$affiliate_id} AND `datetime` BETWEEN '{$year}-{$month}-01 00:00:00' AND '{$year}-{$month}-31 23:59:59' AND refund = 0 and void = 0 ORDER BY datetime DESC";
$sql_reports = "SELECT * FROM ap_earnings WHERE affiliate_id={$affiliate_id} AND datetime >= '{$year}-{$month}-01 00:00:00' AND datetime <= '{$year}-{$month}-{$last_day} 23:59:59' AND void = 0 and refund = 0";
$sql = "SELECT option_value FROM {$wpdb->options} WHERE option_name = 'wpo_wcpdf_template_settings'";
$wpo_wcpdf_template_settings = $wpdb->get_var($sql);
$wpo_wcpdf_template_settings = preg_replace_callback(
    '/(?<=^|\{|;)s:(\d+):\"(.*?)\";(?=[asbdiO]\:\d|N;|\}|$)/s',
    function($m){
        return 's:' . mb_strlen($m[2]) . ':"' . $m[2] . '";';
    },
    $wpo_wcpdf_template_settings
);
$shop_address = unserialize($wpo_wcpdf_template_settings)['shop_address'];
$template_dir = $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/academy';
//var_Dump($template_dir);
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Invoice</title>
	<style type="text/css">
		@page {
			margin: 1cm;
        }
		@font-face {
			font-family: 'Open Sans';
			font-style: normal;
			font-weight: normal;
			src: local('Open Sans'), local('OpenSans'), url(http://themes.googleusercontent.com/static/fonts/opensans/v7/yYRnAC2KygoXnEC8IdU0gQLUuEpTyoUstqEm5AMlJo4.ttf) format('truetype');
		}
		@font-face {
			font-family: 'Open Sans';
			font-style: normal;
			font-weight: bold;
			src: local('Open Sans Bold'), local('OpenSans-Bold'), url(http://themes.googleusercontent.com/static/fonts/opensans/v7/k3k702ZOKiLJc3WVjuplzMDdSZkkecOE1hvV7ZHvhyU.ttf) format('truetype');
		}
		@font-face {
			font-family: 'Open Sans';
			font-style: italic;
			font-weight: normal;
			src: local('Open Sans Italic'), local('OpenSans-Italic'), url(http://themes.googleusercontent.com/static/fonts/opensans/v7/O4NhV7_qs9r9seTo7fnsVCZ2oysoEQEeKwjgmXLRnTc.ttf) format('truetype');
		}
		@font-face {
			font-family: 'Open Sans';
			font-style: italic;
			font-weight: bold;
			src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url(http://themes.googleusercontent.com/static/fonts/opensans/v7/PRmiXeptR36kaC0GEAetxrQhS7CD3GIaelOwHPAPh9w.ttf) format('truetype');
		}
		body {
			background: #fff;
			color: #000;
			font-family: 'Open Sans', sans-serif;
			font-size: 14px;
			max-height: 100%;
			line-height: 100%;
		}
		.container {
			width: 100%;
			margin: 0 auto;
			text-align: center;
		}
		.order-details {
			text-align: center;
			width: 100%;
		}
		.shop-info {
			text-align: right;
		}
		.order-data-addresses {
			margin: 0 auto;
		    width: 100%;
		}
		.order-data-addresses td {
			text-align: right;
		}
		#footer {
			text-align: right;
			display: block;
		}
		.fullname {
			text-align: left;
		}
		a {
			color: #000;
			text-decoration: none;
		}
	</style>
</head>
<body>
	<div class="container">
		<table class="head">
			<tr>
				<td class="header">
					<img src="<?php echo $template_dir ?>/images/logo-email.png">
				</td>
				<td class="shop-info">
					<div class="shop-name"><h3><?php echo get_bloginfo( 'name' )  ?></h3></div>
					<div class="shop-address"><?php echo $shop_address ?></div>
				</td>
			</tr>
		</table>
		<br>
		<table class="order-data-addresses">
			<tr>
				<td>
					<div class="fullname"><h2><?php echo $get_affiliate['fullname'] ?></h2></div>
				</td>
				<td>
					<div class="invoice-date">
						<h2>Invoice Date: <?php echo $date_gen ?></h2>
						<h2>Payment Method: Paypal</h2>
					</div>
				</td>
			</tr>
		</table>
		<br>
		<table class="order-details">
			<thead>
				<tr>
					<th>Order Number</th>
					<th>Commission</th>
					<th>Sale</th>
					<th>Net Revenue</th>
					<th>Date Purchased</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$sum_sales = 0;
				$sum_revenue = 0;
				$currency_simbol = '&pound;';
				if(defined('COM_SITE') && !defined('COZA_SITE')) {
					$currency_simbol = '$';
				} elseif(defined('COZA_SITE')) {
					$currency_simbol = 'R';
				}
				$i = 0;
				$total = [];
				$total = mysqli_fetch_array($result);
				$total = count($total);
				$separate_row = 28;
				$net_revenue = 0;
				$vat_revenue = 0;
				$rows = $wpdb->get_results($sql_reports, ARRAY_A);
				foreach($rows as $row) { 
					extract($row);
					$i++;
					$order_nr = filter_var($product, FILTER_SANITIZE_NUMBER_INT);
					$sum_sales += $sale_amount;
					$sum_revenue += $net_earnings;
					$net_revenue = $sum_revenue;
					if(!defined('COM_SITE') && !defined('COZA_SITE')) {
						$net_revenue = ($sum_revenue * 5) / 6;
						$vat_revenue = $sum_revenue - $net_revenue;
					}
					?>
					<tr class="order-info">
						<td class="order-nr"><?php echo $order_nr ?></td>
						<td class="comission"><?php echo $comission ?>%</td>
						<td class="sale"><?php echo $sale_amount ?></td>
						<td class="net_revenue"><?php echo $net_earnings ?></td>
						<td class="date_time"><?php echo $datetime ?></td>
					</tr>
					<?php if($i % $separate_row == 0 && $separate_row == $total) { ?>
					<tr class="order-info">
						<td colspan="6"><?php echo str_repeat('<br>', 3); ?></td>
					</tr>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>
		<br><br>
		<div id="footer">
			<strong>Total sales: </strong>
			<span><?php echo $currency_simbol.number_format($sum_sales, 2) ?></span><br>
			<strong>Net revenue: </strong>
			<span><?php echo $currency_simbol.number_format($net_revenue, 2) ?></span><br>
			<?php if(!defined('COM_SITE') && !defined('COZA_SITE')) { ?>
			<strong>VAT: </strong>
			<span>-<?php echo $currency_simbol.number_format($vat_revenue, 2) ?></span><br>
			<?php } ?>
			<strong>Total revenue: </strong>
			<span><?php echo $currency_simbol.number_format($sum_revenue, 2) ?></span>
		</div>
	</div>
</body>
</html>

<?php
	$html = ob_get_contents();
	ob_end_clean();
	$html = stripslashes($html);
	$html = preg_replace('/>\s+</', '><', $html);
	require(ABSPATH."wp-content/plugins/courses/certificate/dompdf/vendor/autoload.php");
	
	use Dompdf\Dompdf;
	use Dompdf\Options;
	use Dompdf\FontMetrics;
	$arr = [
		'debugLayout' => false
	];
	$dompdf = new DOMPDF($arr);
	$paper = 'A4';
	$dompdf->loadHtml($html);
	$dompdf->setPaper($paper, 'portait');
	$dompdf->render();
	$fontMetrics = $dompdf->getFontMetrics();
	$fontMetrics->getFont('opensans');
	$options = new Options();
	$options->set('isPhpEnabled', true);
	$options->set('isHtml5ParserEnabled', true);
	if($_SERVER['REMOTE_ADDR'] == '127.0.0.1'){
		ob_flush();
	}
	$dompdf->stream('invoice_'.str_replace('-', '_', $date_gen), array("Attachment" => 1));
	exit;