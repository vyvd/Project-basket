<?php 
include('auth/startup.php');
include('data/data-functions.php');
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

        <div id="page-content-wrapper">
            <div class="container-fluid">
				<?php include('assets/comp/affiliates-stat-boxes.php');?>
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['AFFILIATES'];?></span>
							</div>
							<div class="panel-content">
								<div>
									<div id="status"></div>
									<table id="users" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><?php echo $lang['AFFILIATE_ID'];?></th>
											<th><?php echo $lang['NAME'];?></th>
											<th><?php echo $lang['USERNAME'];?></th>
											<th><?php echo $lang['EMAIL'];?></th>
											<th><?php echo $lang['TOTAL_REFERRED'];?></th>
											<th><?php echo $lang['TOTAL_SALES'];?></th>
											<th><?php echo $lang['BALANCE'];?></th>
											<th><?php echo $lang['ACCEPTED_TERMS'];?></th>
											<th><?php echo $lang['ACTION'];?></th>
											<th><?php echo 'Switch To'; ?></th>
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
	function fetch_data() {
        $('#users').DataTable({
            "order": [
                [0, "DESC"]
            ],
            "processing": true,
            "serverSide": true,
            "columnDefs": [{
                "searchable": true,
                "orderable": true,
                "targets": 0
            }],
            "ajax": {
                "url": "<?php echo $protocol.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-affiliates.php",
                "type": "GET",
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
	                    columns: [ 2, 3, 4, 5, 6 ]
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
	                    columns: [ 2, 3, 4, 5, 6 ]
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
	                    columns: [ 2, 3, 4, 5, 6 ]
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
	                    columns: [ 2, 3, 4, 5, 6 ]
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
	                    columns: [ 2, 3, 4, 5, 6 ]
	                },
	                title: 'IPs',
	            },
	        ],
        });
    }

    function fetch_meta_boxes() {
        $.ajax({
            type: "POST",
            url: '<?php echo $protocol.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-affiliates-mb.php',
            dataType: "json",
            beforeSend: function() {
                $('#total-affiliates').html('Loading... ');
                $('#total-balance').html('Loading... ');
            },
            success: function(response, textStatus, errorno) {
                $('#total-affiliates').html(response.total_affiliates);
                $('#total-balance').html(response.total_balance);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
            	console.log(errorThrown);
     			swal("Cancelled", "Server Error for meta moxes", "error");
 			 }
        });
    }
	$(document).ready(function() {
		fetch_meta_boxes();
	    fetch_data();
	    $('#users').on('click', '.delete', function(e) {
	        swal({
	            title: "Are you sure?",
	            text: "You will delete the affiliates!",
	            type: "warning",
	            showCancelButton: true,
	            confirmButtonClass: "btn-danger",
	            confirmButtonText: "Yes, delete it!",
	            cancelButtonText: "No, cancel pls!",
	        }).then((result) => {
	            if (result.value) {
	                //console.log(123);
	                var affiliate_id = $(this).data('affiliate');
	                var id = $(this).data('id');
	                $.ajax({
	                    type: "POST",
	                    url: "<?php echo $protocol.$_SERVER['HTTP_HOST'] ?>/affiliates/data/ajax-affiliates-delete.php",
	                    data: {
	                        id: id
	                    },
	                    success: function(response, textStatus, errorno) {
	                        $('#users').DataTable().destroy();
	                        fetch_data();
	                        fetch_meta_boxes();
	                    }
	                });
	            } else {
	                swal("Cancelled", "Your affiliates was not deleted", "error");
	            }
	        });
	    });
	});
	</script>
	

	
</body>
</html>
