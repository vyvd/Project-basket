<?php 
include('auth/startup.php');
include('data/data-functions.php');
$affiliate_filter = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_STRING);
$affiliate = get_affiliate_type_comission($affiliate_filter, $outside_wordpress = true, $return_both = true);
$end_date = date('Y-m-d');
$time = strtotime($end_date);
$start_date = date("Y-m-d", strtotime("-1 month", $time));
//var_dump();
//SITE SETTINGS
list($meta_title, $meta_description, $site_title, $site_email) = all_settings();
//REDIRECT ADMIN
if($admin_user!='1'){header('Location: dashboard');}

$protocol = 'http://';

if( isset($_SERVER['HTTPS'] ) ) {
    $protocol = 'https://';
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
	<title>
		<?php echo $meta_title;?>
	</title>
	<!-- Bootstrap Core CSS -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="assets/css/base.css" rel="stylesheet">
	<!-- Elusive and Font Awesome Icons -->
	<link href="assets/fonts/css/elusive.css" rel="stylesheet">
	<link href="assets/fonts/css/fa.css" rel="stylesheet">
	<!-- Webfont -->
	<link href='https://fonts.googleapis.com/css?family=Archivo+Narrow:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
	<!-- SweetAlert Plugin -->
	<link href="assets/css/plugins/sweetalert.css" rel="stylesheet" media="all">
	<!-- Datatables Plugin -->
	<link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.css" rel="stylesheet" media="all">
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
	<!-- Start Top Navigation -->
	<?php include('assets/comp/top-nav.php');?>
	<!-- Start Main Wrapper -->
	<div id="wrapper">
		<!-- Side Wrapper -->
		<div id="side-wrapper">
			<ul class="side-nav">
				<?php include('assets/comp/side-nav.php');?>
			</ul>
		</div>
		<!-- End Main Navigation -->
		<div id="page-content-wrapper">
			<div class="container-fluid">
				<?php include('assets/comp/individual-stat-boxes.php');?>
				<div class="row">
					<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title">(<?php affiliate_name($affiliate_filter); echo ') Add / Update / Delete Coupons';?></span>
							</div>
							<div class="panel-content">
								<div>
									<div id="status"></div>
									<form method="post" action="data/add-aff-coupon">
										<input type="text" maxlength="15" value="" name="aff_coupon" placeholder="Coupon" required>
										<br>
										<br>
										<select name="comission_type" id="comission_type" required>
											<option value="">Comission Type</option>
											<option value="fixed">Fixed</option>
											<option value="percentage">Percentage</option>
											<option value="fixed_price">Fixed Price</option>
										</select>
										<br>
										<br>
										<span id="aff_coupon_value"><input type="text" maxlength="10" value="" name="aff_coupon_value" placeholder="Coupon Value" required><br><br></span>
										<input type="date" value="" name="aff_expire_date" placeholder="Coupon Expire Date" required>
										<br>
										<br>
                                        <?php
                                        $current_page_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                                        ?>

                                        <!--<input type="hidden" name="redirect" value="../--><?php //echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);?><!--?a=--><?php //echo $affiliate_filter ?><!--">-->
                                        <input type="hidden" name="redirect" value="<?php echo $current_page_link;?>">

                                        <input type="hidden" name="af" value="<?php echo $affiliate_filter;?>">
										<input type="submit" class="btn btn-xs btn-primary" value="Add User Coupon">
									</form>
									<?php
										$error = (isset($_GET['err'])) ? $_GET['err'] : '';
										if (!empty($error)) {
											echo $error;
										}
									?>
								</div>
								<br>
								<br>
								<div>
									<table id="voucher" class="row-border" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>Voucher Code</th>
												<th>Voucher Value</th>
												<th>Comission Type</th>
												<th>Expire Date</th>
												<th>
													<?php echo $lang['ACTION'];?>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php voucher_table($affiliate_filter); ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title">(<?php affiliate_name($affiliate_filter); echo ') Commission Settings';?></span>
							</div>
							<div class="panel-content">
								<div>
									<div id="status"></div>
									<form method="post" action="data/update-user-comission">
										<input type="text" value="<?php echo $affiliate->comission; ?>" name="commission_aff">
										<input type="submit" class="btn btn-xs btn-primary" value="Update Commission">
                                        <?php
                                        $current_page_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
                                        ?>

<!--                                        <input type="hidden" name="redirect" value="../--><?php //echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);?><!--">-->
                                        <input type="hidden" name="redirect" value="<?php echo $current_page_link;?>">


										<input type="hidden" name="af" value="<?php echo $affiliate_filter;?>">
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title">(<?php affiliate_name($affiliate_filter); echo ') '.$lang['SALES_AND_PROFITS'];?></span>
								<div class="date-filter pull-right">
									<form method="post" action="data/set-filter">
										From
										<input type="date" id="start_date" name="start_date" value="<?php echo $start_date;?>"> to
										<input type="date" id="end_date" name="end_date" value="<?php echo $end_date;?>">
										<input type="hidden" name="redirect" value="../<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);?>">
										<input type="hidden" name="af" value="<?php echo $affiliate_filter;?>">
										<input type="submit" id="filter" class="btn btn-xs btn-primary" value="Filter">
									</form>
								</div>
							</div>
							<div class="panel-content">
								<div>
									<div id="status"></div>
									<table id="users" class="row-border" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>Affiliate ID</th>
												<th>Order</th>
												<th>
													<?php echo $lang['PRODUCT'];?>
												</th>
												<th>
													<?php echo $lang['SALE_AMOUNT'];?>
												</th>
												<th>
													<?php echo $lang['COMISSION'];?>
												</th>
												<th>
													<?php echo $lang['NET_EARNINGS'];?>
												</th>
												<th>
													<?php echo $lang['DATETIME'];?>
												</th>
												<th>
													<?php echo $lang['ACTION'];?>
												</th>
											</tr>
										</thead>
										<!--<tbody>
											<?php //sales_table($start_date, $end_date, $affiliate_filter); ?>
										</tbody>-->
									</table>
								</div>
							</div>
						</div>
						<!-- End Panel -->
					</div>
				</div>
				<div class="row">
					<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title">(<?php affiliate_name($affiliate_filter); echo ') '.$lang['REFERRAL_TRAFFIC'];?></span>
								<div class="date-filter pull-right">
									<form method="post" action="data/set-filter">
										From
										<input type="date" name="start_date" id="start_date_tra" value="<?php echo $start_date;?>"> to
										<input type="date" id="end_date_tra" name="end_date" value="<?php echo $end_date;?>">
										<input type="hidden" name="redirect" value="../<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);?>">
										<input type="submit" id="filter-tra" class="btn btn-xs btn-primary" value="Filter">
									</form>
								</div>
							</div>
							<div class="panel-content">
								<div>
									<div id="status"></div>
									<table id="traffic" class="row-border" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>
													<?php echo $lang['AFFILIATE'];?>
												</th>
												<th>
													<?php echo $lang['IP_ADDRESS'];?>
												</th>
												<th>
													<?php echo $lang['BROWSER'];?>
												</th>
												<th>
													<?php echo $lang['ISP_PROVIDER'];?>
												</th>
												<th>
													<?php echo $lang['LANDING_PAGE'];?>
												</th>
												<th>
													<?php echo $lang['DATETIME'];?>
												</th>
												<th>
													<?php echo $lang['ACTION'];?>
												</th>
											</tr>
										</thead>
										<!--<tbody>
											<?php //referral_table($start_date, $end_date, $affiliate_filter); ?>
										</tbody>-->
									</table>
								</div>
							</div>
						</div>
						<!-- End Panel -->
					</div>
				</div>
			</div>
			<!-- End Page Content -->
		</div>
		<!-- End Main Wrapper  -->
		<!-- jQuery -->
		<script src="assets/js/jquery.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<!-- Base Theme JS -->
		<script src="assets/js/base.js"></script>
		<!-- SweetAlert -->
		<script src="https://unpkg.com/sweetalert2@7.17.0/dist/sweetalert2.all.js"></script>
		<!-- Datatables -->
		<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
		<script>
		$(document).ready(function() {
			//$(document).on('change','#comission_type',function(){
			$('#comission_type').on('change', function() {
				var comission_type = $('#comission_type').val();
				if (comission_type == 'fixed_price') {
					$('#aff_coupon_value').hide();
					$('#aff_coupon_value input').removeAttr('required');
				} else {
					$('#aff_coupon_value').show();
					$('#aff_coupon_value input').attr('required');
				}
			});
		});
		$(document).ready(function() {
			//$('#users, #traffic').DataTable();
			var affiliate_id = <?php echo (isset($_GET['a'])) ? $_GET['a']: ''; ?>;
			function fetch_data(is_date_search) {
				var start_date = end_date = '';
				if (is_date_search == 'yes') {
					var start_date = $('#start_date').val();
					var end_date = $('#end_date').val();
				}
				$('#users').DataTable({
					order: [[6, "DESC"]],
					"processing": true,
					"serverSide": true,
					"columnDefs": [
						{
							"targets": [0],
							"visible": false,
							"searchable": false
						}
					],
					"ajax": {
						"url": "<?php echo $protocol.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-affiliate-stats-sales-profits.php",
						"type": "GET",
						"data": {
							"is_date_search": is_date_search,
							"start_date": start_date,
							"end_date": end_date,
							"owner": affiliate_id
						}
					},
					"language": {
						"infoFiltered": ""
					}
				});
			}

			function fetch_data_tra(is_date_search) {
				var start_date = end_date = '';
				if (is_date_search == 'yes') {
					var start_date = $('#start_date_tra').val();
					var end_date = $('#end_date_tra').val();
				}
				$('#traffic').DataTable({
					"order": [
						[5, "DESC"]
					],
					"processing": true,
					"serverSide": true,
					"columnDefs": [
						{
							"targets": [0],
							"visible": false,
							"searchable": false
						}
					],
					"ajax": {
						"url": "<?php echo $protocol.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-affiliate-stats-referral-traffic.php",
						"type": "GET",
						"data": {
							"is_date_search": is_date_search,
							"start_date": start_date,
							"end_date": end_date,
							"owner": affiliate_id
						}
					},
					"language": {
						"infoFiltered": ""
					}
				});
			}

			function fetch_meta_boxes() {
				var start_date = $('#start_date').val();
				var end_date = $('#end_date').val();
				$.ajax({
					type: "POST",
					url: "<?php echo $protocol.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-affiliates-stats-mb.php",
					dataType: "json",
					data: {
						'affiliate_id': affiliate_id,
						'start_date': start_date,
						'end_date': end_date
					},
					beforeSend: function() {
						$('#total_sales_period').html('Loading');
						$('#affiliate_earnings_period').html('Loading');
						$('#total_referrals_period').html('Loading');
					},
					success: function(response, textStatus, errorno) {
						$('#total_sales_period').html(response.total_sales_period);
						$('#affiliate_earnings_period').html(response.affiliate_earnings_period);
						$('#total_referrals_period').html(response.total_referrals_period);
					}
				});
			}
			fetch_data('yes');
			fetch_data_tra('yes');
			fetch_meta_boxes();
			$('#filter').click(function(e) {
				e.preventDefault();
				$('#users').DataTable().destroy();
				var start_date = $('#start_date').val();
				var end_date = $('#end_date').val();
				if (start_date.indexOf('1970') == '-1' && end_date.indexOf('1970') == '-1') {
					fetch_data('yes');
				} else {
					fetch_data('no');
				}
				fetch_meta_boxes();
			});
			$('#filter-tra').click(function(e) {
				e.preventDefault();
				$('#traffic').DataTable().destroy();
				var start_date = $('#start_date_tra').val();
				console.log(sta)
				var end_date = $('#end_date_tra').val();
				if (start_date.indexOf('1970') == '-1' && end_date.indexOf('1970') == '-1') {
					fetch_data_tra('yes');
				} else {
					fetch_data_tra('no');
				}
				fetch_meta_boxes();
			});
			$('#users').on('click', '.delete-sales, .refund-sales', function(e) {
				var sale_or_refund = ($(this).attr('class').indexOf('delete-sales') > 0) ? 'delete' : 'refund';
				console.log($(this).attr('class').indexOf('delete-sales'));
				var file_sale = 'ajax-affiliate-stats-sales-profits-delete.php';
				var file_refund = 'ajax-affiliate-stats-sales-profits-refund.php';
				var file = (sale_or_refund == 'delete') ? file_sale : file_refund;
				swal({
				  title: "Are you sure?",
				  text: "You will "+sale_or_refund+" the sale!",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonClass: "btn-danger",
				  confirmButtonText: "Yes, "+sale_or_refund+" it!",
				  cancelButtonText: "No, cancel pls!",
				}).then((result) => {
				  	if (result.value) {
				  		//console.log(123);
					   	var start_date = $('#start_date').val();
						var end_date = $('#end_date').val();
						var affiliate_id = $(this).data('affiliate');
						var id = $(this).data('id');
						$.ajax({
							type: "POST",
							url: "<?php echo $protocol.$_SERVER['HTTP_HOST'] ?>/affiliates/data/"+file,
							data : {
								affiliate_id: affiliate_id,
								id: id
							},
							success: function (response, textStatus, errorno) {
								$('#users').DataTable().destroy();
								if(start_date.indexOf('1970') == '-1' && end_date.indexOf('1970') == '-1') {
									fetch_data('yes');
								} else {
									fetch_data('no');
								}
								fetch_meta_boxes();
							}
						}); 
				  	} else {
				    	swal("Cancelled", "Your referral was not "+sale_or_refund+"ed", "error");
				  	}
				});
			});
			$('#traffic').on('click', '.delete-referral', function(e) {
				swal({
				  title: "Are you sure?",
				  text: "You will delete the referral!",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonClass: "btn-danger",
				  confirmButtonText: "Yes, delete it!",
				  cancelButtonText: "No, cancel pls!",
				}).then((result) => {
				  	if (result.value) {
				  		//console.log(123);
					   	var start_date = $('#start_date_tra').val();
						var end_date = $('#end_date_tra').val();
						var affiliate_id = $(this).data('affiliate');
						var id = $(this).data('id');
						$.ajax({
							type: "POST",
							url: "<?php echo $protocol.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-affiliate-stats-referral-traffic-delete.php",
							data : {
								affiliate_id: affiliate_id,
								id: id
							},
							success: function (response, textStatus, errorno) {
								$('#traffic').DataTable().destroy();
								if(start_date.indexOf('1970') == '-1' && end_date.indexOf('1970') == '-1') {
									fetch_data_tra('yes');
								} else {
									fetch_data_tra('no');
								}
								fetch_meta_boxes();
							}
						}); 
				  	} else {
				    	swal("Cancelled", "Your referral was not deleted", "error");
				  	}
				});
			});
		});
		</script>
</body>

</html>