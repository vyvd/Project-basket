<?php 
include('auth/startup.php');
include('data/data-functions.php');
//SITE SETTINGS
list($meta_title, $meta_description, $site_title, $site_email) = all_settings();
$cpc_on = cpc_on(); 
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

        <div id="page-content-wrapper">
            <div class="container-fluid">
				<?php include('assets/comp/referral-stat-boxes.php');?>
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-6">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['TOP_REFERRING_URLS'];?></span>
								<div class="date-filter pull-right">
									<form method="post" action="data/set-filter">
										From <input type="date" id="start_date_tru" name="start_date" value="<?php echo $start_date;?>"> to <input id="end_date_tru" type="date" name="end_date" value="<?php echo $end_date;?>">
										<input type="hidden" name="redirect" value="../<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);?>">
										<input type="submit" id="filter-tru" class="btn btn-xs btn-primary" value="Filter">
									</form>
								</div>
							</div>
							<div class="panel-content">
								
									<div id="status"></div>
									<table id="tru" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><?php echo $lang['AFFILIATE'];?></th>
											<th><?php echo $lang['LANDING_PAGE'];?></th>
										</tr>
									</thead>

									<!--<tbody>
										<?php //top_url_referral_table($start_date, $end_date); ?>
									</tbody>-->
								</table>
							</div>
						</div>
					</div>
					<!-- End Panel -->
					<div class="col-lg-6">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['TOP_REFERRING_AFFILIATES'];?></span>
								<div class="date-filter pull-right">
									<form method="post" action="data/set-filter">
										From <input id="start_date_tra" type="date" name="start_date" value="<?php echo $start_date;?>"> to <input id="end_date_tra" type="date" name="end_date_tra" value="<?php echo $end_date;?>">
										<input type="hidden" name="redirect" value="../<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);?>">
										<input type="submit" id="filter-tra" class="btn btn-xs btn-primary" value="Filter">
									</form>
								</div>
							</div>
							<div class="panel-content">
								<table id="tra" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><?php echo $lang['AFFILIATE'];?></th>
											<th><?php echo $lang['REFERRED_VISITS'];?></th>
											<?php $cpc_on = cpc_on(); if($cpc_on=='1'){ echo '<th>'.$lang['CPC_EARNINGS'].'</th>';}?>
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
					<!-- End Panel -->
	
					<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['ALL_REFERRAL_TRAFFIC'];?></span>
								<div class="date-filter pull-right">
									<form method="post" action="data/set-filter">
										From <input type="date" id="start_date" name="start_date" value="<?php echo $start_date;?>"> to <input type="date" id="end_date" name="end_date" value="<?php echo $end_date;?>">
										<input type="hidden" name="redirect" value="../<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);?>">
										<input type="submit" id="filter-art" class="btn btn-xs btn-primary" value="Filter">
									</form>
								</div>
							</div>
							<div class="panel-content">
								<div>
									<div id="status"></div>
									<table id="art" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><?php echo $lang['AFFILIATE'];?></th>
											<th><?php echo $lang['IP_ADDRESS'];?></th>
											<th><?php echo $lang['BROWSER'];?></th>
											<th><?php echo $lang['ISP_PROVIDER'];?></th>
											<th><?php echo $lang['LANDING_PAGE'];?></th>
											<th><?php echo 'Course Name'?></th>
											<?php 
												if($cpc_on=='1'){ 
													echo '<th>'.$lang['CPC_EARNINGS'].'</th>';
												}
											?>
											<th><?php echo $lang['DATETIME'];?></th>
											<th><?php echo $lang['ACTION'];?></th>
										</tr>
									</thead>

									<!--<tbody>
										<?php //referral_table($start_date, $end_date); ?>
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
	//$('#tra').DataTable();
	$(document).ready(function() {
		function fetch_data(is_date_search) {
			var start_date = end_date = '';
			if(is_date_search == 'yes') {
				var start_date = $('#start_date').val();
				var end_date = $('#end_date').val();
			}
		   	$('#art').DataTable({
   				"order": [[7, "DESC"]],
		        "processing": true,
	        	"serverSide": true,
	        	"columnDefs": [ {
		            "searchable": true,
		            "orderable": true,
		            "targets": 0
		        }],
	        	"ajax": {
	        		"url": "<?php echo 'http://'.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-referral-traffic.php",
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
		                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
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
		                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
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
		                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
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
		                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
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
		                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
		                },
		                title: 'IPs',
		            },
		        ],
			});
		}
		function fetch_data_tru(is_date_search) {
			var start_date = end_date = '';
			if(is_date_search == 'yes') {
				var start_date = $('#start_date_tru').val();
				var end_date = $('#end_date_tru').val();
			}
			$('#tru').DataTable({
	        	order:[[1, "DESC"]],
		        "processing": true,
	        	"serverSide": true,
	        	"columnDefs": [ {
		            "searchable": false,
		            "orderable": false,
		            "targets": 0
		        } ],
		        "columns": [
		        	{ "data": "affiliate_id" },
		        	{ "data": "landing_page" },
		        ],
	        	"ajax": {
	        		"url": "<?php echo 'http://'.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-referred-traffic-top-urls.php",
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
    			searching: false, 
    			paging: false
			});
		}
		function fetch_data_tra(is_date_search) {
			var start_date = end_date = '';
			if(is_date_search == 'yes') {
				var start_date = $('#start_date_tra').val();
				var end_date = $('#end_date_tra').val();
			}
			$('#tra').DataTable({
	        	order:[],
		        "processing": true,
	        	"serverSide": true,
	        	"columnDefs": [ {
		            "searchable": false,
		            "orderable": false,
		            "targets": 0
		        } ],
		        "columns": [
		        	{ "data": "affiliate_name" },
		        	{ "data": "count" },
                    { "data": "total_cpc" },
		        ],
	        	"ajax": {
	        		"url": "<?php echo 'http://'.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-referred-traffic-top-affiliates.php",
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
    			searching: false, 
    			paging: false
			});
		}
		function fetch_meta_boxes() {
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
			$.ajax({
				type: "POST",
				url: "<?php echo 'http://'.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-referral-traffic-mb.php",
				dataType: "json",
				data: {
					'owner': '<?php echo $owner ?>',
					'start_date': start_date,
					'end_date': end_date
				},
				beforeSend: function () {
					$('#total_referrals_period').html('Loading');
					$('#total_cpc_earnings').html('Loading');
					$('#active_affiliates_period').html('Loading');
				},
				success: function (response, textStatus, errorno) {
					$('#total_referrals_period').html(response.total_referrals_period);
					$('#total_cpc_earnings').html(response.total_cpc_earnings);
					$('#active_affiliates_period').html(response.active_affiliates_period);
				}
			});
		}
		fetch_data('yes');
		fetch_data_tru('yes');
		fetch_data_tra('yes');
		fetch_meta_boxes();
		$('#filter-art').click(function(e) {
			e.preventDefault();
			$('#art').DataTable().destroy();
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
			if(start_date.indexOf('1970') == '-1' && end_date.indexOf('1970') == '-1') {
				fetch_data('yes');
			} else {
				fetch_data('no');
			}
			fetch_meta_boxes();	
		});
		$('#filter-tra').click(function(e) {
			e.preventDefault();
			$('#tra').DataTable().destroy();
			var start_date = $('#start_date_tra').val();
			var end_date = $('#end_date_tra').val();
			if(start_date.indexOf('1970') == '-1' && end_date.indexOf('1970') == '-1') {
				fetch_data_tra('yes');
			} else {
				fetch_data_tra('no');
			}	
		});
		$('#filter-tru').click(function(e) {
			e.preventDefault();
			$('#tru').DataTable().destroy();
			var start_date = $('#start_date_tru').val();
			var end_date = $('#end_date_tru').val();
			var datas = {
				"table": 'tru', 
				"order": false, 
				"file": 'ajax-referred-traffic-top-urls.php', 
				"start_date": start_date, 
				"end_date": end_date
			};
			if(start_date.indexOf('1970') == '-1' && end_date.indexOf('1970') == '-1') {
				fetch_data_tru('yes');
			} else {
				fetch_data_tru('no');
			}	
		});
		$('#art').on('click', '.delete', function(e) {
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
				   	var start_date = $('#start_date').val();
					var end_date = $('#end_date').val();
					var affiliate_id = $(this).data('affiliate');
					var id = $(this).data('id');
					$.ajax({
						type: "POST",
						url: "<?php echo 'http://'.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-referral-traffic-delete.php",
						data : {
							affiliate_id: affiliate_id,
							id: id
						},
						success: function (response, textStatus, errorno) {
							$('#art').DataTable().destroy();
							var datas = {
								"table": "art", 
								"order": "7", 
								"file": "ajax-referral-traffic.php", 
								"start_date": start_date, 
								"end_date": end_date
							};
							if(start_date.indexOf('1970') == '-1' && end_date.indexOf('1970') == '-1') {
								fetch_data('yes');
							} else {
								fetch_data('no');
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
