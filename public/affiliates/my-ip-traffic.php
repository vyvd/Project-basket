<?php 
include('auth/startup.php');
include('data/data-functions.php');
//SITE SETTINGS
list($meta_title, $meta_description, $site_title, $site_email) = all_settings();
//$start_date = '2013-07-23';

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
        </div><!-- End Main Navigation --> 

        <!-- YOUR CONTENT GOES HERE -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
				<?php //include('assets/comp/my-ip-stat-boxes-i.php');?>
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['SALES_AND_PROFITS'];?></span>
								<div class="date-filter pull-right">
									<form method="post" action="data/set-filter">
										From <input type="date" name="start_date" id="start_date" value="<?php echo $start_date;?>"> to <input type="date" id="end_date" name="end_date" value="<?php echo $end_date;?>">
										<input type="submit" id="filter-ip-traffic" class="btn btn-xs btn-primary" value="Filter">
									</form>
								</div>
							</div>
							<div class="panel-content">
								<div>
									<div id="status"></div>
									<table id="ip-traffic" class="display nowrap" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>IP</th>
												<th>Country</th>
												<th>Country Code</th>
												<th>Country Name</th>
												<th><?php echo $lang['DATETIME'];?></th>
											</tr>
										</thead>
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
	<script src="https://unpkg.com/sweetalert2@7.17.0/dist/sweetalert2.all.js"></script>
	<!-- Datatables -->
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
	<script src="assets/js/datatables/jszip.min.js"></script>
	<script src="assets/js/datatables/pdfmake.min.js"></script>
	<script src="assets/js/datatables/vfs_fonts.js"></script>
	<script src="assets/js/datatables/buttons.html5.min.js"></script>
	<script src="assets/js/datatables/buttons.print.min.js"></script>
	<?php 
		
	?>
	<script>
	$(document).ready(function() {
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		function fetch_data(is_date_search, start_date = '', end_date = '') {
		   	$('#ip-traffic').DataTable({
		        order: [[4, "DESC"]],
		        processing: true,
	        	serverSide: true,
	        	lengthMenu: [[10, 100, 1000], [10, 100, 1000]],
	        	pageLength: 10,
	        	columnDefs: [ {
		            searchable: true,
		            orderable: true,
		            targets: 0
		        } ],
	        	ajax: {
	        		"url": '<?php echo $protocol.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-my-ip-traffic.php',
	        		"type": "GET",
	        		"data": {
	        			"is_date_search" : is_date_search,
	        			"start_date": start_date,
	        			"end_date": end_date,
	        			"affiliate_id": '<?php echo $_GET['affiliate_id'] ?>',
	        			"landing_page": '<?php echo $_GET['landing_page'] ?>',
	        		}
	        	},
	        	language: {                
        			"infoFiltered": ""
    			},
		        dom: 'lBfrtip',
		        buttons: [
		            {
		            	extend: 'copy',
		                text: 'Copy',
		                exportOptions: {
			                modifier: {
			                    search: 'applied',
			                    order: 'applied'
			                },
		                    columns: [ 0, 2, 3, 4 ]
		                },
		                title: 'IPs',
		        	},
		            {
		            	extend: 'csv',
		                text: 'CSV',
		                exportOptions: {
			                modifier: {
			                    search: 'applied',
			                    order: 'applied'
			                },
		                    columns: [ 0, 2, 3, 4 ]
		                },
		                title: 'IPs',
		        	},
		        	{
		            	extend: 'excel',
		                text: 'Excel',
		                exportOptions: {
			                modifier: {
			                    search: 'applied',
			                    order: 'applied'
			                },
		                    columns: [ 0, 2, 3, 4 ]
		                },
		                title: 'IPs',
		        	},
		            {
		            	extend: 'pdf',
		                text: 'PDF',
		                exportOptions: {
			                modifier: {
			                    search: 'applied',
			                    order: 'applied'
			                },
		                    columns: [ 0, 2, 3, 4 ]
		                },
		                title: 'IPs',
		        	},
		            {
		                extend: 'print',
		                text: 'Print',
		                exportOptions: {
			                modifier: {
			                    search: 'applied',
			                    order: 'applied'
			                },
		                    columns: [ 0, 2, 3, 4 ]
		                },
		                title: 'IPs',
		            },
		        ],
			});
		}
		fetch_data('yes', start_date, end_date);
		$('#filter-ip-traffic').click(function(e) {
			e.preventDefault();
			$('#ip-traffic').DataTable().destroy();
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
			if(start_date.indexOf('1970') == '-1' && end_date.indexOf('1970') == '-1') {
				fetch_data('yes', start_date, end_date);
			} else {
				fetch_data('no');
			}
		});
	});	
	</script>
	

	
</body>
</html>
