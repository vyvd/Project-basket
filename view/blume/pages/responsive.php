<?php

$metaTitle = "Pages";
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
                <span class="panel-title">Static Pages</span>
                <a href="javascript:;" data-toggle="modal" data-target="#add" class="btn btn-success pull-right">Add Page</a>
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
                        $items = ORM::for_table("pages")->find_many();

                        foreach($items as $item) {
                            ?>
                            <tr>
                                <td>
                                    <?= $item->title ?>
                                </td>
                                <td>
                                    <a href="<?= SITE_URL ?><?= $item->slug ?>" class="label label-info" target="_blank">View</a>
                                    <a href="<?= SITE_URL ?>blume/pages/edit?id=<?= $item->id ?>" class="label label-warning"><i class="fa fa-edit"></i></a>
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

        <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Add New Page</h4>
                    </div>
                    <form name="addNewItem">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>Template</label>
                                <select class="form-control" name="seoPage">
                                    <option value="0">Standard</option>
                                    <option value="1">SEO Page</option>
                                </select>
                            </div>
                            <p><em>The page will be created and you will be able to add content on the next step.</em></p>
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
                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=add-page",
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



        <!-- -------------- DEMO Break -------------- -->
        <div class="mv40"></div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>
<div id="deleteProduct"></div>
<script type="text/javascript">
    function deleteProduct(id) {
        $("#deleteProduct").load("<?= SITE_URL ?>ajax/blume?action=delete-product&id="+id);
        $("#product"+id).fadeOut();
    }
</script>

<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
