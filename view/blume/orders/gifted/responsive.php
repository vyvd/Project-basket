<?php

$metaTitle = "Gifted Course Orders";
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

                    <span class="panel-title">Gift Course Orders</span>
                    <p>View and manage all gifted course orders on <?= SITE_NAME ?></p>


                </div>
                <div class="panel-body pn">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Date & Time</th>
                                <th>Course</th>
                                <th>Claimed</th>
                                <th>Total</th>
                                <th>Recipient</th>
                                <th>View</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Date & Time</th>
                                <th>Course</th>
                                <th>Claimed</th>
                                <th>Total</th>
                                <th>Recipient</th>
                                <th>View</th>
                            </tr>
                            </tfoot>
                        </table>

                        <script type="text/javascript">
                            $(document).ready(function() {
                                $('.datatable').DataTable( {
                                    "processing": true,
                                    "serverSide": true,
                                    "order": [[ 0, "desc" ]],
                                    "ajax": {
                                        "type" : "GET",
                                        "url" : "<?= SITE_URL ?>blume/datatables/orders/gifted",
                                        "dataSrc": function ( json ) {

                                            return json.data;

                                        }
                                    },
                                    "drawCallback": function( settings ) {
                                        $( ".viewOrder" ).click(function() {
                                            var id = $(this).data("id");
                                            $("#view").modal("toggle");
                                            $("#ajaxView").load("<?= SITE_URL ?>ajax?c=blumeNew&a=order-details&id="+id);
                                        });
                                    }
                                } );
                            } );


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