<?php 
include('auth/startup.php');
include('data/data-functions.php');
//SITE SETTINGS
list($meta_title, $meta_description, $site_title, $site_email) = all_settings();
$affiliates_ids = get_all_affiliates();
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
        </div><!-- End Main Navigation --> 


        <div id="page-content-wrapper">
			<div class="container-fluid">					
				<div class="row">
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title">Affiliate Reports</span>
							</div>
							<div class="panel-content">
								<div>
									<div id="status"></div>
									<table id="reports" class="row-border" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>Affiliate</th>
												<th>Report Name </th>
												<th>Invoice / Report</th>
											</tr>
										</thead>
										<tbody>
											<?php 
												foreach($affiliates_ids as $affiliate_email=>$affiliate_id) {
													echo reports_table($affiliate_id, $affiliate_email); 
												}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
   
    <!-- jQuery -->
    <script src="assets/js/jquery.js"></script>

    <!-- Bootstrap -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- Base Theme JS -->
   	<script src="assets/js/base.js"></script>
	<!-- SweetAlert -->
	<script src="assets/js/plugins/sweetalert.min.js"></script>
	<!-- Datatables -->
	<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
	
	<script>
	$(document).ready( function() {
		$('#reports').dataTable({
			/* Disable initial sort */
			"aaSorting": []
		});
	})	
	</script>
	<script>
	$(document).ready( function() {
		$('#reports').on('change', '.select_report', function(e) {
			var report_id = $(this).val();
			var owner = $(this).attr('id');
			$('.report_id_'+owner).val(report_id);
		});
		$('#reports').on('click', '.pdf, .excel', function(e) {
			var affiliate_id = $(this).parent().find('.affiliate_id').data('id');
			console.log(affiliate_id);
			var report_id = $('.report_id_'+affiliate_id).val();
			console.log(report_id);
			if(report_id == '-1') {
				e.preventDefault();
				$(this).find('.excel, .pdf').addClass('disabled');
				$(this).find('.excel, .pdf').attr('disabled');
				alert('Please Select Month/Year.');
				return false;
			}
		});
	});
	</script>

	
</body>
</html>
