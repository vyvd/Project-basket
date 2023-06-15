<?php

$metaTitle = "Currencies";
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
                <span class="panel-title">Currencies</span>
                <a href="javascript:;" data-toggle="modal" data-target="#add" class="btn btn-success pull-right">
                    Add Currency
                </a>
                <a href="javascript:;" data-toggle="modal" data-target="#bulk" class="btn btn-system pull-right">
                    Bulk Pricing Editor
                </a>
            </div>
            <div class="panel-menu">
                <input id="fooFilter" type="text" class="form-control"
                       placeholder="Search...">
            </div>
            <div class="panel-body pn">
                <div class="table-responsive">
                    <table class="table footable" data-filter="#fooFilter" data-page-navigation=".pagination" data-page-size="50">
                        <thead>
                        <tr>
                            <th>Currency</th>
                            <th>Tax</th>
                            <th>Sub. Trial Status</th>
                            <th>GBP Rate</th>
                            <th>FB XML</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $items = ORM::for_table("currencies")->find_many();

                        foreach($items as $item) {
                            ?>
                            <tr>
                                <td>
                                    <?= $item->code ?> (<?= $item->short ?>)
                                </td>
                                <td>
                                    <?= $item->taxPercent ?>%
                                </td>
                                <td>
                                    <?= $item->trialStatus ?> <small>(<?= $item->trialDays ?> days)</small>
                                </td>
                                <td><?= $item->convRate ?></td>
                                <td>
                                    <a href="<?= SITE_URL ?>assets/cdn/fb-catalog-<?= $item->code ?>.xml" target="_blank" style="margin-right:10px;">fb-catalog-<?= $item->code ?>.xml</a>
                                    <a href="<?= SITE_URL ?>assets/cdn/fb-catalog-<?= $item->code ?>-dynamic-refs.xml" target="_blank">fb-catalog-<?= $item->code ?>-dynamic-refs.xml</a>
                                    <a href="<?= SITE_URL ?>blume/currencies/catalogue?id=<?= $item->id ?>" class="label label-info" style="margin-left:10px;"><i class="fa fa-edit"></i></a>
                                </td>
                                <td>
                                    <a href="javascript:;" class="label label-warning" data-toggle="modal" data-target="#add<?= $item->id ?>"><i class="fa fa-edit"></i></a>
                                </td>
                            </tr>



                            <div class="modal fade" id="add<?= $item->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Edit Currency</h4>
                                        </div>
                                        <form name="addNewItem<?= $item->id ?>">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Code / Title</label>
                                                    <input type="text" name="code" value="<?= $item->code ?>" placeholder="e.g. USD" class="form-control" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Short</label>
                                                    <input type="text" name="short" value="<?= $item->short ?>" placeholder="e.g. $" class="form-control" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Tax Rate (%)</label>
                                                    <input type="text" name="taxPercent" value="<?= $item->taxPercent ?>" placeholder="e.g. 20.00" class="form-control" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Post/Zip Code Label <small>(used on checkout)</small></label>
                                                    <input type="text" name="postZipWording" placeholder="" value="<?= $item->postZipWording ?>" class="form-control" />
                                                </div>
                                                <div class="form-group">
                                                    <label>GBP Conversion rate</label>
                                                    <input type="text" name="convRate" placeholder="" value="<?= $item->convRate ?>" class="form-control" />
                                                </div>

                                                <hr />

                                                <h4 style="    font-size: 16px;
    padding-left: 10px;
    margin-top: -10px;">Subscription Pricing</h4>

                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <label>
                                                            Monthly
                                                        </label>
                                                        <input type="text" class="form-control" name="prem1" value="<?= $item->prem1 ?>" />
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label>
                                                            Annually
                                                        </label>
                                                        <input type="text" class="form-control" name="prem12" value="<?= $item->prem12 ?>" />
                                                    </div>
                                                </div>

                                                <hr />

                                                <h4 style="    font-size: 16px;
    padding-left: 10px;
    margin-top: -10px;">Annual Subscription Trials</h4>

                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <label>
                                                            Trial Period (days)
                                                        </label>
                                                        <input type="text" class="form-control" name="trialDays" value="<?= $item->trialDays ?>" />
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label>
                                                            Trial Status
                                                        </label>
                                                        <select class="form-control" name="trialStatus">
                                                            <option><?= $item->trialStatus ?></option>
                                                            <option>None</option>
                                                            <option>Only via URL</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-12">
                                                        <p style="text-align:center;margin:0;padding:5px;border-radius:10px;background:#f4f4f4;margin-top:15px;"><small>
                                                                If a trial is only allowed via URL, then this means marketing must point users to links like newskillsacademy.com/subscription?<strong>trial=true</strong>. If the status is set to <em>None</em> above, then the trial will not work, even if the variable in the URL.
                                                            </small></p>
                                                    </div>
                                                </div>

                                                <hr />

                                                <h4 style="    font-size: 16px;
    padding-left: 10px;
    margin-top: -10px;">Trial Settings</h4>

                                                <div class="row">

                                                    <div class="col-xs-6">
                                                        <label>
                                                            Job Board Access?
                                                        </label>
                                                        <select class="form-control" name="trialJobBoard">
                                                            <option value="0">No</option>
                                                            <option value="1" <?php if($item->trialJobBoard == "1") { ?>selected<?php } ?>>Yes</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-xs-6">
                                                        <label>
                                                            Career Radar Access?
                                                        </label>
                                                        <select class="form-control" name="trialCareerRadar">
                                                            <option value="0">No</option>
                                                            <option value="1" <?php if($item->trialCareerRadar == "1") { ?>selected<?php } ?>>Yes</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-xs-6" style="padding-top:20px;">
                                                        <label>
                                                            Certificate Access?
                                                        </label>
                                                        <select class="form-control" name="trialCerts">
                                                            <option value="0">No</option>
                                                            <option value="1" <?php if($item->trialCerts == "1") { ?>selected<?php } ?>>Yes</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-xs-6" style="padding-top:20px;">
                                                        <label>
                                                            Active Course Limit
                                                        </label>
                                                        <input type="text" class="form-control" name="trialActiveCourses" value="<?= $item->trialActiveCourses ?>" />
                                                    </div>

                                                    <div class="col-xs-12">
                                                        <p style="text-align:center;margin:0;padding:5px;border-radius:10px;background:#f4f4f4;margin-top:15px;"><small>
                                                                Configure available premium features for those in trials for this selected currency.
                                                            </small></p>
                                                    </div>
                                                </div>

                                                <hr />

                                                <h4 style="    font-size: 16px;
    padding-left: 10px;
    margin-top: -10px;">Certificate Pricing</h4>

                                                <div class="row">
                                                    <?php
                                                    $limit = 10;
                                                    $count = 1;

                                                    while($count <= $limit) {

                                                        $name = "cert".$count;

                                                        ?>
                                                        <div class="col-xs-6" style="margin-bottom:20px;">
                                                            <label>
                                                                x<?= $count ?> Certificates
                                                            </label>
                                                            <input type="text" class="form-control" name="cert<?= $count ?>" value="<?= $item->$name ?>" />
                                                        </div>
                                                        <?php

                                                        $count ++;

                                                    }
                                                    ?>
                                                </div>

                                                <div id="returnStatusAddNew<?= $item->id ?>"></div>
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
                                jQuery("form[name='addNewItem<?= $item->id ?>']").submit(function(e) {

                                    e.preventDefault();

                                    var formData = new FormData($(this)[0]);

                                    jQuery.ajax({
                                        url: "<?= SITE_URL ?>ajax?c=blumePricing&a=edit-currency&id=<?= $item->id ?>",
                                        type: "POST",
                                        data: formData,
                                        async: true,
                                        success: function (msg) {
                                            jQuery('#returnStatusAddNew<?= $item->id ?>').html(msg);
                                        },
                                        cache: false,
                                        contentType: false,
                                        processData: false
                                    });
                                });
                            </script>
                            <?php
                        }
                        ?>
                        </tbody>
                        <tfoot class="footer-menu">
                        <tr>
                            <td colspan="7">
                                <nav class="text-right">
                                    <ul class="pagination hide-if-no-paging"></ul>
                                </nav>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                <br />

                <a href="<?= SITE_URL ?>ajax?c=cron&a=get-conversion-rate" target="_blank">Refresh rates manually</a>

            </div>
        </div>

        <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Add Currency</h4>
                    </div>
                    <form name="addNewItem">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Code / Title</label>
                                <input type="text" name="code" placeholder="e.g. USD" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>Short</label>
                                <input type="text" name="short" placeholder="e.g. $" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>Tax Rate (%)</label>
                                <input type="text" name="taxPercent" placeholder="e.g. 20.00" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>Post/Zip Code Label <small>(used on checkout)</small></label>
                                <input type="text" name="postZipWording" placeholder="" value="Postcode" class="form-control" />
                            </div>
                            <p><em>This currency will be available to customers.</em></p>
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
                // $('textarea[name="text"]').html($('.summernote').code());
                var formData = new FormData($(this)[0]);

                jQuery.ajax({
                    url: "<?= SITE_URL ?>ajax?c=blumePricing&a=add-currency",
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

        <div class="modal fade" id="bulk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Bulk Pricing Editor</h4>
                    </div>
                    <form name="bulk">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Old Price</label>
                                <input type="text" name="priceSource" placeholder="0.00" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>New Price</label>
                                <input type="text" name="priceNew" placeholder="0.00" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>Currency</label>
                                <select class="form-control" name="currency">
                                    <?php
                                    foreach($items as $item) {

                                        ?>
                                        <option value="<?= $item->id ?>"><?= $item->short ?> / <?= $item->code ?></option>
                                        <?php

                                    }
                                    ?>
                                </select>
                            </div>
                            <div id="returnStatusAddNewBulk"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Edit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            jQuery("form[name='bulk']").submit(function(e) {
                e.preventDefault();
                // $('textarea[name="text"]').html($('.summernote').code());
                var formData = new FormData($(this)[0]);

                jQuery.ajax({
                    url: "<?= SITE_URL ?>ajax?c=blumePricing&a=bulk-edit-pricing",
                    type: "POST",
                    data: formData,
                    async: true,
                    success: function (msg) {
                        jQuery('#returnStatusAddNewBulk').html(msg);
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            });
        </script>



        <!-- -------------- DEMO Break -------------- -->
        <div class="mv40"></div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>
<div id="deleteProduct"></div>
<script type="text/javascript">
    function deleteProduct(id) {
        $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumePricing&a=delete-currency&id="+id);
        $("#product"+id).fadeOut();
    }
</script>

<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
