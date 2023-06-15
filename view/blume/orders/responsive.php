<?php

$metaTitle = "Orders";
include BASE_PATH . 'blume.header.base.php';
?>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        div.dt-buttons{
            float: right;
            margin-left: 20px;
        }
    </style>
    <!-- -------------- Content -------------- -->
    <section id="content" class="table-layout animated fadeIn">


        <!-- -------------- /Column Left -------------- -->

        <!-- -------------- Column Center -------------- -->
        <div class="chute chute-center">


            <!-- -------------- Data Filter -------------- -->
            <div class="panel" id="spy2">
                <div class="panel-heading">
                    <button type="button" data-toggle="modal" data-target="#exportOrders" class="btn btn-warning pull-right" style="margin-left:5px;"><i class="fa fa-download"></i> Export (CSV)</button>
                    <button data-type="multiple" data-url="<?= SITE_URL?>ajax?c=import&a=orders" class="importJson btn btn-dark pull-right ml10"><i class="fa fa-upload"></i> Import Orders</button>
                    <span class="panel-title">Orders</span>
                    <p>View and manage all completed orders on <?= SITE_NAME ?></p>

                </div>
                
                <div class="row mb-3 order-filters">
                    <div class="col-4 pull-right">
                        <input class="slinput daterange" style="width: 240px;" type="text" name="daterange" value="" autocomplete="off" placeholder=" Select Date" /> 
                        <button class="button-learn-more pull-right">Filter</button>    
                        <input type="hidden" id="select_date" value="0">  
                    </div>
                </div>

                <div class="panel-body pn">
                    <div class="row mb20">
                        <div class="col-12">
                            </div>
                    </div>
                    
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="">
                                <table style="max-width: 100%;" class="table datatable table-responsive">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Date & Time</th>
                                        <th>Items</th>
                                        <th>Method</th>
                                        <th>Currency</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Date & Time</th>
                                        <th>Items</th>
                                        <th>Method</th>
                                        <th>Currency</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                    </tfoot>
                                </table>

                                <script type="text/javascript">
                                    $(function () {

                                        $('input[name="daterange"]').daterangepicker({
                                            autoUpdateInput: false,
                                            locale: {
                                                cancelLabel: 'Clear'
                                            }
                                        });
                                        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
                                            $("#select_date").val('1');
                                            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                                            // alert('123');
                                        });
                                        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
                                            $("#select_date").val('0');
                                            $(this).val('');
                                            // alert('456');
                                        });
                                        $('form').submit(function () {
                                           // $(this).find('input[type="submit"]').attr('disabled', true);
                                           // $(this).find('button[type="submit"]').attr('disabled', true);
                                            return true;
                                        });

                                        function orderStats(startDate = null, endDate = null){

                                            $('.datatable').DataTable().destroy();

                                            $('.datatable').DataTable( {
                                                "processing": true,
                                                "pageLength": 100,
                                                "serverSide": true,
                                                "order": [[ 3, "desc" ]],
                                                "lengthMenu": [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500,]],
                                                ajax: {
                                                    "url" : "<?= SITE_URL ?>blume/datatables/orders",
                                                    type: 'get',
                                                    data: {
                                                        'startDate': startDate,
                                                        'endDate': endDate
                                                    },
                                                    "dataSrc": function ( json ) {

                                                        return json.data;

                                                        }
                                                },
                                                // "ajax": {
                                                //     "type" : "GET",
                                                //     "url" : "<?= SITE_URL ?>blume/datatables/orders",
                                                    

                                                //     "dataSrc": function ( json ) {

                                                //         return json.data;

                                                //     }
                                                    
                                                // },
                                                
                                                dom: 'lBfrtip',
                                                buttons: [
                                                    {
                                                        extend: 'excel',
                                                        exportOptions: {
                                                            columns: [0, 1, 2, 3, 4, 5]
                                                        }
                                                    },
                                                    {
                                                        extend: 'csv',
                                                        exportOptions: {
                                                            columns: [0, 1, 2, 3, 4, 5]
                                                        }
                                                    },
                                                    {
                                                        extend: 'pdf',
                                                        exportOptions: {
                                                            columns: [0, 1, 2, 3, 4, 5]
                                                        }
                                                    }
                                                ],
                                                "drawCallback": function( settings ) {
                                                    $( ".viewOrder" ).click(function() {
                                                        var id = $(this).data("id");
                                                        $("#view").modal("toggle");
                                                        $("#ajaxView").load("<?= SITE_URL ?>ajax?c=blumeNew&a=order-details&id="+id);
                                                    });
                                                }
                                            } );

                                
                                        }

                                        orderStats();

                                    
                                        $(".order-filters button").on('click', function(){
                                                if($("#select_date").val() == '1'){
                                                    var startDate = $('.daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                                    var endDate = $('.daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                                                }
                                                // var category_id = $('#category_id').val();
                                                // var brands_id = $('#brands_id').val();
                                                // // alert(user_id);
                                                orderStats(startDate, endDate);
                                        });
                                    });


                                </script>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <!-- -------------- DEMO Break -------------- -->
            <div class="mv40"></div>


        </div>
        <!-- -------------- /Column Center -------------- -->

    </section>
    <!-- -------------- /Content -------------- -->
    <div class="deleteAccountReturn"></div>

    <div class="modal fade" id="view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Order Details</h4>
                </div>

                <div class="modal-body">
                    <div id="ajaxView">
                        <p class="text-center">
                            <i class="fa fa-spin fa-spinner"></i>
                        </p>
                    </div>
                    <div id="returnStatusEdit"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="exportOrders" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Export Orders</h4>
                </div>

                <div class="modal-body">
                    <form action="<?= SITE_URL ?>ajax?c=blumeNew&a=export-orders-csv" method="post">
                        <div class="form-group">
                            <label>Date Range</label>
                            <input class="slinput daterange form-control" type="text" name="daterange" value="" autocomplete="off" placeholder="Click to select..." />
                        </div>
                        <div class="form-group">
                            <label>Currency</label>
                            <select class="form-control" name="currencyID">
                                <option value="">All</option>
                                <?php
                                foreach(ORM::for_table("currencies")->find_many() as $currency) {
                                    ?>
                                    <option value="<?= $currency->id ?>"><?php echo $currency->code ?> / <?php echo $currency->short ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select class="form-control" name="method">
                                <option value="">All</option>
                                <option value="stripe">Stripe</option>
                                <option value="paypal">PayPal</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Filter By Course</label>
                            <select class="form-control" name="courseID">
                                <option value="">All Courses</option>
                                <?php
                                foreach($this->getAllCourses() as $course) {
                                    ?>
                                    <option value="<?= $course->id ?>"><?= $course->title ?> <?php if($course->usImport == "1") { ?>(US)<?php } ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Filter By Course Category</label>
                            <select class="form-control" name="categoryID">
                                <option value="">All Categories</option>
                                <?php
                                foreach(ORM::for_table("courseCategories")->order_by_asc("title")->find_many() as $category) {
                                    ?>
                                    <option value="<?= $category->id ?>"><?= $category->title ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Data Protection</label>
                            <select class="form-control" name="hideDetails">
                                <option value="1">Don't export customer details</option>
                                <option value="0">Export customer details</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-download"></i>
                                Generate & Download CSV
                            </button>
                            <p>
                                <small>This might take a while if you have a wide date range.</small>
                            </p>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    

    <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

<?php include BASE_PATH . 'blume.footer.base.php'; ?>