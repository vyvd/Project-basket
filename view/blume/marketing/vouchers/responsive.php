<?php

$metaTitle = "Vouchers";
include BASE_PATH . 'blume.header.base.php';
?>
    <style>
        .ui-datepicker {
            z-index:9999 !important;
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

                    <a href="#" data-toggle="modal" data-target="#addBulk" class="btn btn-warning pull-right">
                        <i class="fa fa-plus"></i>
                        Add
                    </a>

                    <a href="#" data-toggle="modal" data-target="#addImport" class="btn btn-info pull-right" style="margin-left:5px;margin-right:5px;">
                        <i class="fa fa-upload"></i>
                        Bulk Add
                    </a>

                    <a href="#" data-toggle="modal" data-target="#exportBy" class="btn btn-system pull-right">
                        <i class="fa fa-download"></i>
                        Export By...
                    </a>

                    <span class="panel-title">Vouchers</span>
                    <p>View and manage all vouchers on <?= SITE_NAME ?></p>

                    <br />
                    <br />
                </div>
                <div class="panel-body pn">
                    <div class="table-responsive">

                        <form name="ajaxAction" action="<?= SITE_URL ?>ajax?c=blumeNew&a=export-vouchers-csv" method="post">
                            <table class="table datatable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Code</th>
                                    <th>Expiry</th>
                                    <th>Claimed</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Code</th>
                                    <th>Expiry</th>
                                    <th>Claimed</th>
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
                                            "url" : "<?= SITE_URL ?>blume/datatables/vouchers",
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
                                        $(".deleteAccountReturn").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-voucher&id="+x);

                                        $("#item" + x).fadeOut("slow");
                                    }
                                }

                            </script>

                            <input type="submit" class="btn btn-default" value="Export Selected (CSV)" />

                        </form>

                        <hr />

                        <form action="<?= SITE_URL ?>ajax?c=blumeNew&a=export-vouchers-csv" method="post">
                            <div class="row">
                                <div class="col-xs-3">
                                    <input type="text" class="form-control datepicker" name="dateFrom" placeholder="Date From" />
                                </div>
                                <div class="col-xs-3">
                                    <input type="text" class="form-control datepicker" name="dateTo" placeholder="Date To" />
                                </div>
                                <div class="col-xs-3">
                                    <select class="form-control" name="type">
                                        <option value="all">All</option>
                                        <option value="redeemed">Redeemed</option>
                                        <option value="unredeemed">Un-redeemed</option>
                                    </select>
                                </div>
                                <div class="col-xs-3">
                                    <input type="submit" class="btn btn-default" value="Export (CSV)" />
                                </div>
                            </div>
                        </form>

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


    <div class="modal fade" id="addBulk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Bulk Vouchers</h4>
                </div>
                <form name="addNewItemBulk">
                    <div class="modal-body">

                        <div class="form-group">
                            <label>Allow own course selection? <small>No need to select below if this is set to yes</small></label>
                            <select class="form-control" name="allowCourseSelection">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Attach Courses</label>
                            <select class="form-control" name="attachCourses" id="attachCourses">
                                <!--<option value="all">Applicable for all courses</option>-->
                                <option value="specific">Applicable for specific courses</option>
                                <!--<option value="price">Applicable for courses up to a certain price</option>-->
                            </select>
                        </div>

                        <script>
                            jQuery( "#attachCourses" ).change(function() {

                                var current = jQuery(this).val();

                                if(current == "specific") {
                                    jQuery("#selectPrice").css("display", "none");
                                    jQuery("#selectSpecific").css("display", "block");
                                } else if(current == "price") {
                                    jQuery("#selectSpecific").css("display", "none");
                                    jQuery("#selectPrice").css("display", "block");
                                } else {
                                    jQuery("#selectSpecific").css("display", "none");
                                    jQuery("#selectPrice").css("display", "none");
                                }

                            });
                        </script>

                        <div id="selectSpecific" class="form-group" style="display:block;max-height:300px;overflow:auto;">
                            <label>Select the course(s) this applies to:</label>
                            <div class="row">
                                <?php
                                foreach($this->getAllCourses() as $course) {
                                    ?>
                                    <div class="col-xs-6">
                                        <label>
                                            <input type="checkbox" name="courses[]" value="<?= $course->id ?>" />
                                            <?= $course->title ?>
                                        </label>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <div id="selectPrice" class="form-group" style="display:none;">
                            <label>This voucher is applicable on courses up to:</label>
                            <input type="text" name="valueUpto" placeholder="0.00" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="text" name="qty" placeholder="0" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Prefix</label>
                            <input type="text" name="prefix" value="NSA" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Code Length</label>
                            <input type="number" name="codeLength" min="6" max="14" value="6" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="text" name="expiry" class="form-control datepicker" />
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
        jQuery("form[name='addNewItemBulk']").submit(function(e) {
            e.preventDefault();

            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "<?= SITE_URL ?>ajax?c=blumeNew&a=create-bulk-vouchers",
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

    <div class="modal fade" id="addImport" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Bulk Vouchers</h4>
                </div>
                <form name="addNewItemImport">
                    <div class="modal-body">

                        <div class="form-group">
                            <label>Attach Imported Vouchers To Courses</label>
                            <select class="form-control" name="attachCourses" id="attachCourses">
                                <!--<option value="all">Applicable for all courses</option>-->
                                <option value="specific">Applicable for specific courses</option>
                                <!--<option value="price">Applicable for courses up to a certain price</option>-->
                            </select>
                        </div>

                        <script>
                            jQuery( "#attachCourses" ).change(function() {

                                var current = jQuery(this).val();

                                if(current == "specific") {
                                    jQuery("#selectPrice").css("display", "none");
                                    jQuery("#selectSpecific").css("display", "block");
                                } else if(current == "price") {
                                    jQuery("#selectSpecific").css("display", "none");
                                    jQuery("#selectPrice").css("display", "block");
                                } else {
                                    jQuery("#selectSpecific").css("display", "none");
                                    jQuery("#selectPrice").css("display", "none");
                                }

                            });
                        </script>

                        <div id="selectSpecific" class="form-group" style="display:block;max-height:300px;overflow:auto;">
                            <label>Select the course(s) this applies to: *</label>
                            <div class="row">
                                <?php
                                foreach($this->getAllCourses() as $course) {
                                    ?>
                                    <div class="col-xs-6">
                                        <label>
                                            <input type="checkbox" name="courses[]" value="<?= $course->id ?>" />
                                            <?= $course->title ?>
                                        </label>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Paste Codes * <small>(one on each line)</small></label>
                            <textarea name="codes" class="form-control" rows="10"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="text" name="expiry" class="form-control datepicker" />
                        </div>


                        <p>If you are importing codes that already exist, then they will be overwritten with the newly specified data.</p>

                        <div id="return_statusBulkImport"></div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery("form[name='addNewItemImport']").submit(function(e) {
            e.preventDefault();

            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "<?= SITE_URL ?>ajax?c=blumeNew&a=import-bulk-vouchers",
                type: "POST",
                data: formData,
                async: true,
                success: function (msg) {
                    jQuery('#return_statusBulkImport').html(msg);
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    </script>

    <script>
        $( ".datepicker" ).datepicker();

    </script>

    <div class="modal fade" id="exportBy" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Export By..</h4>
                </div>

                <div class="modal-body">

                    <p><strong>Export by import bulk import group.</strong></p>

                    <?php
                    $items = ORM::for_table("vouchers")
                        ->where_not_null("groupID")
                        ->group_by("groupID")
                        ->order_by_desc("groupID")
                        ->find_many();

                    foreach($items as $item) {

                        ?>
                        <p>
                            <strong>
                                <?= date('d/m/Y', strtotime($item->whenAdded)) ?>
                            </strong>
                            |
                            <?php
                            echo "<a href='".SITE_URL."ajax?c=blumeNew&a=download-bulk-vouchers&group=".$item->groupID."' style='color:#336a70;font-weight:bold;'>Download as CSV.</a>";
                            ?>
                        </p>
                        <?php

                    }
                    ?>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<?php include BASE_PATH . 'blume.footer.base.php'; ?>