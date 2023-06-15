<?php

$metaTitle = "Printed Course Orders";
include BASE_PATH . 'blume.header.base.php';
?>

    <!-- -------------- Content -------------- -->
    <section id="content" class="table-layout animated fadeIn">


        <!-- -------------- /Column Left -------------- -->

        <!-- -------------- Column Center -------------- -->
        <div class="chute chute-center">


            <!-- -------------- Data Filter -------------- -->
            <div class="panel" id="spy2">
                <div class="panel-heading">

                    <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=export-printed-orders" class="btn btn-info pull-right">
                        Export All Pending (CSV)
                    </a>

                    <span class="panel-title">Printed Course Orders</span>
                    <p>View and manage all printed course/material orders on <?= SITE_NAME ?></p>


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
                                    <th>Status</th>
                                    <th>View</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Date & Time</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>View</th>
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
                                            "url" : "<?= SITE_URL ?>blume/datatables/orders/printed",
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
                                        }
                                    } );
                                } );


                            </script>

                            <hr />

                            <p>With selected:</p>

                            <select class="form-control" name="type">
                                <option value="csv">Export CSV</option>
                                <option value="sent">Mark As Sent</option>
                                <option value="unsent">Unmark As Sent</option>
                                <option value="zip">Download ZIP</option>
                            </select>

                            <input type="submit" class="btn btn-success" value="Go" />

                        </form>
                        <script type="text/javascript">
                            jQuery("form[name='ajaxAction']").submit(function(e) {
                                e.preventDefault();

                                var formData = new FormData($(this)[0]);

                                jQuery.ajax({
                                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=action-printed-orders",
                                    type: "POST",
                                    data: formData,
                                    async: true,
                                    success: function (msg) {
                                        jQuery('#returnStatus').html(msg);
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