<?php

$metaTitle = "Special Offers";
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

                    <a href="#" data-toggle="modal" data-target="#add" class="btn btn-warning pull-right">
                        <i class="fa fa-plus"></i>
                        Add
                    </a>

                    <span class="panel-title">Special Offers</span>
                    <p>View and manage all special offers on <?= SITE_NAME ?></p>

                    <br />
                    <br />
                </div>
                <div class="panel-body pn">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Course(s)</th>
                                <th>Price</th>
                                <th>Date From</th>
                                <th>Date To</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Course(s)</th>
                                <th>Price</th>
                                <th>Date From</th>
                                <th>Date To</th>
                                <th>Actions</th>
                            </tr>
                            </tfoot>
                        </table>

                        <script type="text/javascript">
                            $(document).ready(function() {
                                $('.datatable').DataTable( {
                                    "processing": true,
                                    "serverSide": true,
                                    "ajax": {
                                        "type" : "GET",
                                        "url" : "<?= SITE_URL ?>blume/datatables/offers",
                                        "dataSrc": function ( json ) {

                                            return json.data;

                                        }
                                    },
                                    "drawCallback": function( settings ) {
                                        $( ".editItem" ).click(function() {
                                            var id = $(this).data("edit");
                                            $("#edit").modal("toggle");
                                            $("#ajaxEdit").load("<?= SITE_URL ?>ajax?c=blumeNew&a=edit-coupon-form&id="+id);
                                        });
                                    }
                                } );
                            } );

                            function deleteItem(x) {
                                var result = confirm("Are you sure you want to delete this? This action can not be undone");

                                if (result==true) {
                                    $(".deleteAccountReturn").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-offer&id="+x);

                                    $("#item" + x).fadeOut("slow");
                                }
                            }

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


    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Special Offer</h4>
                </div>
                <form name="addNew">
                    <div class="modal-body">

                        <div class="form-group">
                            <label>Select the course(s) this applies to (leave blank if it applies to all): </label>
                            <div class="row" style="max-height:200px;overflow:auto;">
                                <?php
                                foreach($this->getAllCourses() as $course) {
                                    ?>
                                    <div class="col-xs-6">
                                        <input type="checkbox" name="courses[]" value="<?= $course->id ?>" />
                                        <?= $course->title ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Offer price:</label>
                            <input type="text" name="price" placeholder="0.00" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label>Code</label>
                            <input type="text" name="code" placeholder="e.g. ZOOM20" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="text" name="dateStart" placeholder="YYYY-MM-DD" value="<?= date('Y-m-d') ?>" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="text" name="dateEnd" placeholder="YYYY-MM-DD" class="form-control" />
                        </div>
                        <div id="return_statusBulkCreate"></div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery("form[name='addNew']").submit(function(e) {
            e.preventDefault();

            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "<?= SITE_URL ?>ajax?c=blumeNew&a=create-offer",
                type: "POST",
                data: formData,
                async: true,
                success: function (msg) {
                    jQuery('#return_statusBulkCreate').html(msg);
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    </script>

<?php include BASE_PATH . 'blume.footer.base.php'; ?>