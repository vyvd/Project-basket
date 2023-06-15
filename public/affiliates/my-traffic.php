<?php 
include('auth/startup.php');
include('data/data-functions.php');
//SITE SETTINGS
list($meta_title, $meta_description, $site_title, $site_email) = all_settings();
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
				<?php include('assets/comp/referral-stat-boxes-i.php');?>
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['ALL_REFERRAL_TRAFFIC'];?></span>
								<div class="date-filter pull-right">
									<form method="post" action="data/set-filter">
										From <input type="date" id="start_date" name="start_date" value="<?php echo $start_date;?>"> to <input id="end_date" type="date" name="end_date" value="<?php echo $end_date;?>">
										<input type="hidden" name="redirect" value="../<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);?>">
										<input type="submit" id="filter-users" class="btn btn-xs btn-primary" value="Filter">
									</form>
								</div>
							</div>
							<div class="panel-content">
								<div>
									<div id="status"></div>
									<table id="users" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><?php echo $lang['IP_ADDRESS'];?></th>
											<th><?php echo $lang['BROWSER'];?></th>
											<th><?php echo $lang['ISP_PROVIDER'];?></th>
											<th><?php echo $lang['LANDING_PAGE'];?></th>
											<th> Course Name </th>
											<?php
                                            $cpc_on = cpc_on();
                                            $data_table_order_by_column_index = 5;
											if($cpc_on=='1'){
                                                $data_table_order_by_column_index = 6;
											    echo '<th>'.$lang['CPC_EARNINGS'].'</th>';
											}?>
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
    <!-- Base Theme JS -->
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
	
	<script>
	$(document).ready(function() {
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		function fetch_data(is_date_search, start_date = '', end_date = '') {
		   	var datatable = $('#users').DataTable({
			        "order": [[<?php echo $data_table_order_by_column_index; ?>, "DESC"]],
			        "processing": true,
		        	"serverSide": true,
		        	"columnDefs": [ {
			            "searchable": true,
			            "orderable": true,
			            "targets": 0
			        } ],
		        	"ajax": {
		        		"url": "<?php echo $protocol.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-all-referred-traffic.php",
		        		"type": "GET",
		        		"data": {
		        			"is_date_search" : is_date_search,
		        			"start_date": start_date,
		        			"end_date": end_date
		        		}
		        	},
		        	"language": {                
            			"infoFiltered": ""
        			},
        			lengthMenu: [[10, 100, 1000], [10, 100, 1000]],
	        		pageLength: 10,
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
			                    columns: [ 0, 1, 2, 3, 4, 5, 6 ]
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
			                    columns: [ 0, 1, 2, 3, 4, 5, 6 ]
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
			                    columns: [ 0, 1, 2, 3, 4, 5, 6 ]
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
			                    columns: [ 0, 1, 2, 3, 4, 5, 6 ]
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
			                    columns: [ 0, 1, 2, 3, 4, 5, 6 ]
			                },
			                title: 'IPs',
			            },
			        ],
			 });
		}
		function fetch_meta_boxes(start_date, end_date) {
			$.ajax({
				type: "POST",
				url: "<?php echo $protocol.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-all-referred-traffic-mb.php",
				dataType: "json",
				data: {
					'owner': '<?php echo $owner ?>',
					'start_date': start_date,
					'end_date': end_date
				},
				beforeSend: function () {
					$('#total_referrals_period').html('Loading');
					$('#my_total_cpc_earnings').html('Loading');
					$('#my_conversion_period').html('Loading');
				},
				success: function (response, textStatus, errorno) {
					console.log(response);
					$('#total_referrals_period').html(response.total_referrals_period);
					$('#my_total_cpc_earnings').html(response.my_total_cpc_earnings);
					$('#my_conversion_period').html(response.my_conversion_period);
				}
			});
		}
		fetch_data('yes', start_date, end_date);
		$('#filter-users').click(function(e) {
			e.preventDefault();
			$('#users').DataTable().destroy();
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
			if(start_date.indexOf('1970') == '-1' && end_date.indexOf('1970') == '-1') {
				fetch_data('yes', start_date, end_date);
			} else {
				fetch_data('no');
			}
			fetch_meta_boxes(start_date, end_date);	
		});
		fetch_meta_boxes(start_date, end_date);	
	});
	</script>
	
</body>
</html>
