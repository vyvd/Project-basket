<?php 
include('auth/startup.php');
include('data/data-functions.php');
//SITE SETTINGS
list($meta_title, $meta_description, $site_title, $site_email) = all_settings();
//REDIRECT ADMIN
if($admin_user!='1'){header('Location: dashboard');}
$transaction_id =  filter_input(INPUT_GET, 'tid', FILTER_SANITIZE_STRING);
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
				<?php include('assets/comp/mt-stat-boxes.php');?>
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['VIEWING']. ' '. $lang['TRANSACTION_ID'] .': MT-'.$transaction_id;?></span>
							</div>
							<div class="panel-content">
								<div id="status"></div>
									<table id="sales" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><?php echo $lang['AFFILIATE'];?></th>
											<th><?php echo $lang['LEVEL_TIER'];?></th>
											<th><?php echo $lang['COMISSION'];?></th>
											<th><?php echo $lang['NET_EARNINGS'];?></th>
											<th><?php echo $lang['DATETIME'];?></th>
											<th><?php echo $lang['ACTION'];?></th>
										</tr>
									</thead>

									<tbody>
										<?php mt_payments_table($transaction_id); ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<!-- End Panel -->

				</div>
            </div>
        </div>
        <!-- End Page Content -->

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
	
	<script>
	$(document).ready( function() {
    $('#sales').dataTable({
        /* Disable initial sort */
        "aaSorting": []
    });
	})	
		
	
	<?php 
	if(isset($_SESSION['action_deleted'])){ echo 'swal("Deleted", "This has been deleted as requested!", "success")';}
	unset($_SESSION['action_deleted']);
	?>	
	</script>
	

	
</body>
</html>
