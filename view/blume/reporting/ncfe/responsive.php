<?php
// used to provide general reporting for ncfe
$this->setControllers(array("subscription"));

$metaTitle = "NCFE Reporting";

$premium = ORM::for_table("accounts")
    ->select("id")
    ->where("isNCFE", "1")
    ->find_many();

$monthly = 0;
$annually = 0;
$subLengthDays = 0;
$subLengthItems = 0;
$newCustomer = 0;
$existingCustomer = 0;

$now = time();

foreach($premium as $user) {

    $subscription = $this->subscription->getCurrentUserSubscription($user->id);

    // length of subscription
    if($subscription->whenAdded != "") {
        $your_date = strtotime($subscription->whenAdded);
        $datediff = $now - $your_date;

        $subLengthDays = $subLengthDays+round($datediff / (60 * 60 * 24));
        $subLengthItems ++;
    }

    // to avoid querying DB every time
    $monthly ++;

}

// average order values
$orderItems = ORM::for_table("orderItems")->select("price")->select("orderID")->where("isNCFE", "1")->find_many();

$totalValue = 0;
$totalItems = 0;

foreach($orderItems as $item) {

    $order = ORM::for_table("orders")->where("status", "completed")->where("id", $item->orderID)->count();

    if($item->price == 99 || $item->price == 99.99) {
        $item->price = $item->price/12;
    }

    if($order != 0) {

        $totalValue = $totalValue+$item->price;
        $totalItems ++;

    }

}

$averageOrder = number_format($totalValue/$totalItems, 2);

// sales today
$from = date('Y-m-d').' 00:00:00';
$to = date('Y-m-d').' 23:59:59';
$orderItems = ORM::for_table("orderItems")
    ->select("price")
    ->select("orderID")
    ->where_gt("whenCreated", $from)
    ->where_lt("whenCreated", $to)
    ->where("isNCFE", "1")
    ->find_many();

$totalValueToday = 0;
$totalItemsToday = 0;

foreach($orderItems as $item) {

    $order = ORM::for_table("orders")->where("status", "completed")->where("id", $item->orderID)->count();

    if($order != 0) {

        $totalValueToday = $totalValueToday+$item->price;
        $totalItemsToday ++;

    }

}

// sales month
$from = date('Y-m-01').' 00:00:00';
$to = date('Y-m-t').' 23:59:59';
$orderItems = ORM::for_table("orderItems")
    ->select("price")
    ->select("orderID")
    ->where_gt("whenCreated", $from)
    ->where_lt("whenCreated", $to)
    ->where("isNCFE", "1")
    ->find_many();

$totalValueMonth = 0;
$totalItemsMonth = 0;

foreach($orderItems as $item) {

    $order = ORM::for_table("orders")->where("status", "completed")->where("id", $item->orderID)->count();

    if($order != 0) {

        $totalValueMonth = $totalValueMonth+$item->price;
        $totalItemsMonth ++;

    }

}

// sales year
$from = date('Y-01-01').' 00:00:00';
$to = date('Y-12-31').' 23:59:59';
$orderItems = ORM::for_table("orderItems")
    ->select("price")
    ->select("orderID")
    ->where_gt("whenCreated", $from)
    ->where_lt("whenCreated", $to)
    ->where("isNCFE", "1")
    ->find_many();

$totalValueYear = 0;
$totalItemsYear = 0;

foreach($orderItems as $item) {

    $order = ORM::for_table("orders")->where("status", "completed")->where("id", $item->orderID)->count();

    if($order != 0) {

        $totalValueYear = $totalValueYear+$item->price;
        $totalItemsYear ++;

    }

}

// assigned courses
$assignedCourses = 0;
$courses = ORM::for_table("courses")->where("isNCFE", "1")->find_many();

foreach($courses as $course) {

    $assignedCourses = $assignedCourses+ORM::for_table("coursesAssigned")->where("courseID", $course->id)->count();

}

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
                    <span class="panel-title">NCFE Reporting</span>
                    <p>An overview of reporting data for NCFE students.</p>

                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="panel panel-tile">
                            <div class="panel-body">
                                <div class="row pv10">
                                    <div class="col-xs-12 pl5 text-center">
                                        <h6 class="text-muted">NCFE Students</h6>

                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;"><?= ORM::for_table("accounts")->where("isNCFE", "1")->count(); ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="panel panel-tile">
                            <div class="panel-body">
                                <div class="row pv10">
                                    <div class="col-xs-12 pl5 text-center">
                                        <h6 class="text-muted">Assigned Courses</h6>

                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;"><?= $assignedCourses ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="panel panel-tile">
                            <div class="panel-body">
                                <div class="row pv10">
                                    <div class="col-xs-12 pl5 text-center">
                                        <h6 class="text-muted">Annual</h6>

                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;"><?= $annually ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="panel panel-tile">
                            <div class="panel-body">
                                <div class="row pv10">
                                    <div class="col-xs-12 pl5 text-center">
                                        <h6 class="text-muted">Average Monthly Payment</h6>

                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;">£<?= $averageOrder ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="panel panel-tile">
                            <div class="panel-body">
                                <div class="row pv10">
                                    <div class="col-xs-12 pl5 text-center">
                                        <h6 class="text-muted">Average Subscription Length</h6>

                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;"><?= number_format($subLengthDays/$subLengthItems, 1) ?> days</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="panel panel-tile">
                            <div class="panel-body">
                                <div class="row pv10">
                                    <div class="col-xs-12 pl5 text-center">
                                        <h6 class="text-muted">Sales Today</h6>

                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;">£<?= number_format($totalValueToday, 2) ?> (<?= $totalItemsToday ?>)</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="panel panel-tile">
                            <div class="panel-body">
                                <div class="row pv10">
                                    <div class="col-xs-12 pl5 text-center">
                                        <h6 class="text-muted">Sales Past Month</h6>

                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;">£<?= number_format($totalValueMonth, 2) ?> (<?= $totalItemsMonth ?>)</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="panel panel-tile">
                            <div class="panel-body">
                                <div class="row pv10">
                                    <div class="col-xs-12 pl5 text-center">
                                        <h6 class="text-muted">Sales Past Year</h6>

                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;">£<?= number_format($totalValueYear, 2) ?> (<?= $totalItemsYear ?>)</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                        <th>Email</th>
                                        <th>Firstname</th>
                                        <th>Lastname</th>
                                        <th>Registered</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Email</th>
                                        <th>Firstname</th>
                                        <th>Lastname</th>
                                        <th>Registered</th>
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
                                            $(this).find('input[type="submit"]').attr('disabled', true);
                                            $(this).find('button[type="submit"]').attr('disabled', true);
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
                                                    "url" : "<?= SITE_URL ?>blume/datatables/accounts/ncfe",
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