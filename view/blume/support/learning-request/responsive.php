<?php

$metaTitle = "Learning Request";
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

                </div>
                <div class="panel-body pn">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Job Center</th>
                                <th>Course</th>
                                <th>Created At</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Job Center</th>
                                <th>Course</th>
                                <th>Created At</th>
                            </tr>
                            </tfoot>
                        </table>

                        <script type="text/javascript">
                            $(document).ready(function() {
                                $('.datatable').DataTable( {
                                    "processing": true,
                                    "serverSide": true,
                                    "order": [[ 6, "desc" ]],
                                    "ajax": {
                                        "type" : "GET",
                                        "url" : "<?= SITE_URL ?>blume/datatables/support/learning-request",
                                        "dataSrc": function ( json ) {

                                            return json.data;

                                        }
                                    },
                                    "drawCallback": function( settings ) {
                                        
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