<?php

$metaTitle = "Discount Coupons";
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
                    <a href="#" data-toggle="modal" data-target="#add" class="btn btn-info pull-right">
                        <i class="fa fa-plus"></i>
                        Add
                    </a>

                    <a href="#" data-toggle="modal" data-target="#addBulk" class="btn btn-warning pull-right" style="margin-left:5px;margin-right:5px;">
                        <i class="fa fa-plus"></i>
                        Import CSV
                    </a>

                    <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=export-coupons-csv-all" target="_blank" class="btn btn-system pull-right">
                        <i class="fa fa-download"></i>
                        Export Most Used (CSV)
                    </a>

                    <span class="panel-title">Discount Coupons</span>
                    <p>View and manage all coupons on <?= SITE_NAME ?></p>

                    <br />
                    <br />
                </div>
                <div class="panel-body pn">
                    <div class="table-responsive">
                        <form name="ajaxAction" action="<?= SITE_URL ?>ajax?c=blumeNew&a=export-coupons-csv" method="post">
                            <table class="table datatable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Code</th>
                                    <th>Value</th>
                                    <th>Uses</th>
                                    <th>Expiry</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Code</th>
                                    <th>Value</th>
                                    <th>Uses</th>
                                    <th>Expiry</th>
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
                                            "url" : "<?= SITE_URL ?>blume/datatables/coupons",
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
                                        $(".deleteAccountReturn").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-coupon&id="+x);

                                        $("#item" + x).fadeOut("slow");
                                    }
                                }

                            </script>

                            <input type="submit" class="btn btn-default" value="Export Selected (CSV)" />

                        </form>

                        <hr />

                        <form action="<?= SITE_URL ?>ajax?c=blumeNew&a=export-coupons-csv" method="post">
                            <div class="row">
                                <div class="col-xs-4">
                                    <input type="text" class="form-control datepicker" name="dateFrom" placeholder="Date From" />
                                </div>
                                <div class="col-xs-4">
                                    <input type="text" class="form-control datepicker" name="dateTo" placeholder="Date To" />
                                </div>
                                <div class="col-xs-4">
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


    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Coupon</h4>
                </div>
                <form name="addNewItem">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Code</label>
                            <input type="text" name="code" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" name="type">
                                <option value="v">Money (Set Value) Off</option>
                                <option value="p">Percentage (%) Off</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Value</label>
                            <input type="text" name="value" placeholder="i.e. 10" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Use Limit</label>
                            <input type="number" name="totalLimit" placeholder="0" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Expiry</label>
                            <input type="text" name="expiry" class="form-control datepicker" />
                        </div>
                        <div class="form-group">
                            <label>Minimum Course Value (if applicable)</label>
                            <input type="number" name="valueMin" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Maximum Course Value (if applicable)</label>
                            <input type="number" name="valueMax" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Account ID (if applicable)</label>
                            <input type="number" name="forUser" placeholder="Paste here..." class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Apply To</label>
                            <select class="form-control" name="applyTo">
                                <option value="single_course">Single Course / Item</option>
                                <option value="basket">Entire Basket</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Which currencies should this coupon be available in?</label>
                            <div class="row">
                                <?php
                                $currencies = ORM::for_table("currencies")->find_many();

                                foreach($currencies as $currency) {
                                    ?>
                                    <div class="col-xs-6">
                                        <label>
                                            <input type="checkbox" name="currencies[]" value="<?= $currency->id ?>" <?php if($currency->code == "GBP") { ?>checked<?php } ?> />
                                            <?= $currency->short ?> / <?= $currency->code ?>
                                        </label>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div id="returnStatusAddNew"></div>
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
        jQuery("form[name='addNewItem']").submit(function(e) {
            e.preventDefault();

            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "<?= SITE_URL ?>ajax?c=blumeNew&a=create-coupon",
                type: "POST",
                data: formData,
                async: true,
                success: function (msg) {
                    jQuery('#returnStatusAddNew').html(msg);
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    </script>

    <div class="modal fade" id="addBulk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Import Coupons as CSV</h4>
                </div>
                <form name="addNewItemBulk">
                    <div class="modal-body">

                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" name="type">
                                <option value="v">Money (Set Value) Off</option>
                                <option value="p">Percentage (%) Off</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Value</label>
                            <input type="text" name="value" placeholder="i.e. 10" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Use Limit</label>
                            <input type="number" name="totalLimit" placeholder="0" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Expiry</label>
                            <input type="text" name="expiry" class="form-control datepicker" />
                        </div>
                        <div class="form-group">
                            <label>Minimum Course Value (if applicable)</label>
                            <input type="number" name="valueMin" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Maximum Course Value (if applicable)</label>
                            <input type="number" name="valueMax" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Apply To</label>
                            <select class="form-control" name="applyTo">
                                <option value="single_course">Single Course / Item</option>
                                <option value="basket">Entire Basket</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Which currencies should this coupon be available in?</label>
                            <div class="row">
                                <?php
                                $currencies = ORM::for_table("currencies")->find_many();

                                foreach($currencies as $currency) {
                                    ?>
                                    <div class="col-xs-6">
                                        <label>
                                            <input type="checkbox" name="currencies[]" value="<?= $currency->id ?>" <?php if($currency->code == "GBP") { ?>checked<?php } ?> />
                                            <?= $currency->short ?> / <?= $currency->code ?>
                                        </label>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Select CSV</label>
                            <input type="file" name="file" class="form-control" />
                        </div>

                        <p>Required fields names for importing are as follows:</p>

                        <p>
                            <strong>CODE</strong>: the coupon code<br />
                        </p>

                        <p>If you are importing codes that already exist, then they will be overwritten with the newly specified data.</p>


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
                url: "<?= SITE_URL ?>ajax?c=blumeNew&a=import-bulk-coupons",
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

    <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Edit Coupon</h4>
                </div>
                <form name="editItem">
                    <div class="modal-body">
                        <div id="ajaxEdit">
                            <p class="text-center">
                                <i class="fa fa-spin fa-spinner"></i>
                            </p>
                        </div>
                        <div id="returnStatusEdit"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery("form[name='editItem']").submit(function(e) {
            e.preventDefault();

            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-coupon",
                type: "POST",
                data: formData,
                async: true,
                success: function (msg) {
                    jQuery('#returnStatusEdit').html(msg);
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
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<?php include BASE_PATH . 'blume.footer.base.php'; ?>