<?php

$metaTitle = "Why New Skills";
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
                <span class="panel-title">Why New Skills</span>
                <a href="#" data-toggle="modal" data-target="#addNew" class="btn btn-success pull-right">
                    Add New
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
                            <th>Title</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $items = ORM::for_table("whyNewSkills")->order_by_asc("title")->find_many();

                        foreach($items as $item) {
                            ?>
                            <tr id="item<?= $item->id ?>">
                                <td>
                                    <?= $item->title ?>
                                </td>
                                <td>
                                    <a href="#" data-toggle="modal" data-target="#edit<?= $item->id ?>" class="label label-warning">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="javascript:;" onclick="deleteItem(<?= $item->id ?>);" class="label label-danger">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </td>
                            </tr>

                            <div class="modal fade" id="edit<?= $item->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Edit</h4>
                                        </div>
                                        <form name="addNewItem<?= $item->id ?>">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Title</label>
                                                    <input type="text" name="title" class="form-control" value="<?= $item->title ?>" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Content</label>
                                                    <textarea class="form-control" name="content" rows="3"><?= $item->content ?></textarea>
                                                </div>
                                                <div id="returnStatusAddNew"></div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                            <input type="hidden" name="itemID" value="<?= $item->id ?>" />
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <script type="text/javascript">
                                jQuery("form[name='addNewItem<?= $item->id ?>']").submit(function(e) {
                                    e.preventDefault();
                                    // $('textarea[name="text"]').html($('.summernote').code());
                                    var formData = new FormData($(this)[0]);

                                    jQuery.ajax({
                                        url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-why",
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
<script type="text/javascript">
    function deleteItem(id) {
        if (window.confirm("Are you sure you want to delete this item?")) {
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-why&id="+id);
            $("#item"+id).fadeOut();
        }
    }
</script>
<div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add New</h4>
            </div>
            <form name="addNewItem">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Content</label>
                        <textarea class="form-control" name="content" rows="3"></textarea>
                    </div>
                    <div id="returnStatusAddNew"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Publish</button>
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
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=add-why",
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

<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
