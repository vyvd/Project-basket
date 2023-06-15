<?php

$metaTitle = "Special Offer Pages";
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
                <span class="panel-title">Special Offer Pages</span>

                <a href="javascript:;" data-toggle="modal" data-target="#add" class="btn btn-success pull-right">
                    <i class="fa fa-plus"></i>
                    Add
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
                            <th>Name</th>
                            <th>Courses</th>
                            <th>Show On Accounts?</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $items = ORM::for_table("offerPages")->order_by_desc("id")->find_many();


                        foreach($items as $item) {

                            ?>
                            <tr id="item<?= $item->id ?>">
                                <td>
                                    <?= $item->title ?>
                                </td>
                                <td>
                                    <?= count(explode(",", $item->courses)) ?>
                                </td>
                                <td>
                                    <?php
                                    if($item->showInAccounts == "1") {
                                        ?>
                                        <label class="label label-success">
                                            Yes
                                        </label>
                                        <?php
                                    } else {
                                        ?>
                                        <label class="label label-danger">
                                            No
                                        </label>
                                        <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span style="font-size:0"><?= strtotime($item->whenAdded) ?></span>
                                    <?= date('d/m/Y', strtotime($item->whenAdded)) ?>
                                </td>
                                <td>
                                    <a href="<?= SITE_URL ?>blume/offers/edit?id=<?= $item->id ?>" class="label label-warning" style="margin-left:5px;cursor:pointer;">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <label class="label label-danger" onclick="deleteItem(<?= $item->id ?>);" style="margin-left:5px;cursor:pointer;">
                                        <i class="fa fa-times"></i>
                                    </label>

                                    <a class="label label-info" href="<?= SITE_URL ?>special-offer/<?= $item->slug ?>" target="_blank" style="margin-left:5px;cursor:pointer;">
                                        View
                                    </a>
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
<script type="text/javascript">
    function deleteItem(id) {
        if (window.confirm("Are you sure you want to delete this item permanently?")) {
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-offer-page&id="+id);
            $("#item"+id).fadeOut();
        }
    }
</script>


<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add New Offer Page</h4>
            </div>
            <form name="addNew">
                <div class="modal-body">


                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" placeholder="e.g. January Offer" class="form-control" />
                    </div>
                    <div id="return_statusNew"></div>

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
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=new-offer-page",
            type: "POST",
            data: formData,
            async: true,
            success: function (msg) {
                jQuery('#return_statusNew').html(msg);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
</script>

<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
