<?php
$metaTitle = "Job Info";
include BASE_PATH . 'blume.header.base.php';
?>
<!-- -------------- Content -------------- -->
<section id="content" class="table-layout animated fadeIn">

    <!-- -------------- Column Center -------------- -->
    <div class="chute chute-center">

        <!-- -------------- Data Filter -------------- -->
        <div class="panel" id="spy2">
            <div class="panel-heading">
            <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=export-job-click-stats-csv" class="btn btn-info pull-right">
                        Export All (CSV)
                    </a>

                <span class="panel-title">View Job Tracking</span>
                <p>View all Job tracking on <?= SITE_NAME ?></p>

                <br />
                <br />
            </div>
            <!-- displays the frame for the jobclicks table -->
            <div class="panel-body pn">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>ID</th>

                                <th>First Name</th>
                                <th>Email</th>
                                <th>Job Id</th>
                                <th>When Clicked</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>ID</th>

                                <th>First Name</th>
                                <th>Email</th>
                                <th>Job Id</th>
                                <th>When Clicked</th>
                            </tr>
                        </tfoot>

                    </table>
                    <!-- pulls all the data from jobclicks and puts it in the table -->
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $('.datatable').DataTable({
                                "processing": true,
                                "serverSide": true,
                                "order": [
                                    [0, "desc"]
                                ],
                                "ajax": {
                                    "type": "GET",
                                    "url": "<?= SITE_URL ?>blume/datatables/jobs/viewClickInfo?id=<?= $id = $_GET['id']; ?> ",
                                    "dataSrc": function(json) {

                                        return json.data;

                                    }
                                },
                            });
                        });
                    </script>
                </div>

            </div>



        </div>

        <div class="mv40"></div>

    </div>

</section>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

<?php include BASE_PATH . 'blume.footer.base.php'; ?>