<?php

$metaTitle = "Redirects";
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
                <span class="panel-title">Redirects</span>
                <a href="javascript:;" data-toggle="modal" data-target="#add" class="btn btn-success pull-right">Add New</a>
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
                            <th>From</th>
                            <th>To</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $items = ORM::for_table("redirects")->find_many();

                        foreach($items as $item) {


                            ?>
                            <tr>
                                <td>
                                    <a href="<?= SITE_URL ?><?= $item->rFrom ?>" target="_blank">
                                        <?= SITE_URL ?><?= $item->rFrom ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= $item->rTo ?>" target="_blank">
                                        <?= $item->rTo ?>
                                    </a>
                                </td>

                                <td>
                                    <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=delete-redirect&id=<?= $item->id ?>" class="label label-danger" onclick="return confirm('Are you sure you want to delete this?')"><i class="fa fa-times"></i></a>
                                </td>
                            </tr>
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
            </div>
        </div>


        <!-- -------------- DEMO Break -------------- -->
        <div class="mv40"></div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>
<div id="deleteProduct"></div>


<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Create Redirect</h4>
            </div>
            <form name="add">
                <div class="modal-body">
                    <div class="form-group">
                        <label>From</label><br />
                        <?= SITE_URL ?>
                        <input type="text" name="rFrom" placeholder="e.g. learn-zone/" class="form-control" style="display: inline-block;
    width: 287px;" />
                    </div>

                    <div class="form-group">
                        <label>To</label>
                        <input type="text" name="rTo" placeholder="Paste full URL to redirect to" class="form-control" />
                    </div>
                    <div id="returnStatusAddNew"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
            <script type="text/javascript">
                jQuery("form[name='add']").submit(function(e) {
                    e.preventDefault();
                    // $('textarea[name="text"]').html($('.summernote').code());
                    var formData = new FormData($(this)[0]);

                    jQuery.ajax({
                        url: "<?= SITE_URL ?>ajax?c=blumeNew&a=create-redirect",
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
        </div>
    </div>
</div>

<style>
    .ui-datepicker {
        z-index:9999 !important;
    }
</style>

<link rel="stylesheet" href="<?= SITE_URL ?>assets/blume/jquery.datetimepicker.min.css">
<script src="<?= SITE_URL ?>assets/blume/jquery.datetimepicker.min.js"></script>

<script type="text/javascript">
    $( document ).ready(function() {
        jQuery('.datetimepicker').datetimepicker();
    });
</script>

<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
