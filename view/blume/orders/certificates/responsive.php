<?php

$metaTitle = "Certificate Orders";
include BASE_PATH . 'blume.header.base.php';
?>
    <link rel="stylesheet" href="<?= SITE_URL ?>assets/css/datepicker.css">
    <script src="<?= SITE_URL ?>assets/js/datepicker.min.js"></script>

    <!-- -------------- Content -------------- -->
    <section id="content" class="table-layout animated fadeIn">


        <!-- -------------- /Column Left -------------- -->

        <!-- -------------- Column Center -------------- -->
        <div class="chute chute-center">


            <!-- -------------- Data Filter -------------- -->
            <div class="panel" id="spy2">
                <div class="panel-heading">

                    <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=export-certificate-orders-csv" class="btn btn-info pull-right">
                        Export All (CSV)
                    </a>

                    <span class="panel-title">Certificate Orders</span>
                    <p>View and manage all certificate orders on <?= SITE_NAME ?></p>


                </div>
                <div class="panel-body pn">
                    <div class="table-responsive">
                        <form name="ajaxAction" id="ajaxAction">

                            <table class="table datatable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Date & Time</th>
                                    <th>Course</th>
                                    <th>Cert. Number</th>
                                    <th>Status</th>
                                    <th>Print</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Date & Time</th>
                                    <th>Course</th>
                                    <th>Cert. Number</th>
                                    <th>Status</th>
                                    <th>Print</th>
                                </tr>
                                </tfoot>
                            </table>

                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $('.datatable').DataTable( {
                                        "processing": true,
                                        "serverSide": true,
                                        "order": [[ 2, "desc" ]],
                                        "ajax": {
                                            "type" : "GET",
                                            "url" : "<?= SITE_URL ?>blume/datatables/orders/certificates",
                                            "dataSrc": function ( json ) {

                                                return json.data;

                                            }
                                        },
                                        "drawCallback": function( settings ) {
                                            $( ".dispatchCert" ).click(function() {

                                                var id = $(this).data("id");

                                                // update label without reloading page
                                                $(".statusLabel"+id).html('<label class="label label-success">Dispatched</label>');

                                                // send request to server
                                                $("#returnStatus").load("<?= SITE_URL ?>ajax?c=blumeNew&a=mark-order-dispatched&id="+id);

                                            });

                                            $( ".viewOrder" ).click(function() {
                                                var id = $(this).data("id");
                                                $("#view").modal("toggle");
                                                $("#ajaxView").load("<?= SITE_URL ?>ajax?c=blumeNew&a=order-details&id="+id);
                                            });

                                            $( "#select-all" ).click(function() {
                                                if(this.checked) {

                                                    $(':checkbox').each(function() {
                                                        this.checked = true;
                                                    });
                                                } else {
                                                    $(':checkbox').each(function() {
                                                        this.checked = false;
                                                    });
                                                }
                                            });

                                        }
                                    } );
                                } );


                            </script>

                            <input type="checkbox" name="select-all" id="select-all" /> Select all

                            <hr />

                            <p>Date Range <em>(selecting a date range will ignore the above selection)</em></p>

                            <div class="row">
                                <div class="col-xs-4">
                                    <label>From</label>
                                    <input type="text" class="form-control" name="dateFrom" data-toggle="datepicker" />
                                </div>
                                <div class="col-xs-4">
                                    <label>To</label>
                                    <input type="text" class="form-control" name="dateTo" data-toggle="datepicker" />
                                </div>
                            </div>

                            <script>
                                $('[data-toggle="datepicker"]').datepicker({
                                    format: 'dd/mm/yyyy'
                                });
                            </script>

                            <hr />

                            <select class="form-control" name="filter1">
                                <option value="pending">Download only pending orders (not dispatched)</option>
                                <option value="all">Download all</option>
                            </select>

                            <hr />

                            <p>With selected:</p>

                            <select class="form-control" name="type">
                                <option value="csv">Export CSV</option>
                                <option value="sent">Mark As Sent</option>
                                <option value="unsent">Unmark As Sent</option>
                                <option value="zip">Download ZIP</option>
                                <option value="merged">Merged PDF</option>
                            </select>
                            <br />

                            <button class="btn btn-success btnGo" type="submit">Go</button>

                        </form>
                        <script type="text/javascript">
                            jQuery("form[name='ajaxAction']").submit(function(e) {
                                e.preventDefault();

                                var formData = new FormData($(this)[0]);

                                $(".btnGo").html('<i class="fa fa-spin fa-spinner"></i>');

                                jQuery.ajax({
                                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=action-certificate-orders",
                                    type: "POST",
                                    data: formData,
                                    async: true,
                                    success: function (msg) {
                                        jQuery('#returnStatus').html(msg);
                                        $(".btnGo").html('Go');
                                    },
                                    cache: false,
                                    contentType: false,
                                    processData: false
                                });
                            });
                        </script>
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

    <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<?php include BASE_PATH . 'blume.footer.base.php'; ?>