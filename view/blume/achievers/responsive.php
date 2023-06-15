<?php

$metaTitle = "Achievers Board";
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
                <span class="panel-title">Achievers Board</span>
                <a href="javascript:;" data-toggle="modal" data-target="#add" class="btn btn-success pull-right">
                    <i class="fa fa-plus"></i>
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
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Added</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $items = ORM::for_table("achievers")->order_by_desc("whenSubmitted")->find_many();


                        foreach($items as $item) {


                            $status = '<label class="label label-system">Pending</label>';

                            if($item->status == "a") {
                                $status = '<label class="label label-success">Approved</label>';
                            }

                            ?>
                            <tr id="item<?= $item->id ?>">
                                <td>
                                    <a href="<?= $this->controller->getAchieverImage($item->id, "large") ?>" target="_blank">
                                        <img src="<?= $this->controller->getAchieverImage($item->id, "thumb") ?>" style="width:30px;height:30px;border-radius:50%;margin-right:6px;" />
                                    </a>
                                </td>
                                <td>
                                    <?= $item->firstname.' '.$item->lastname ?>
                                </td>
                                <td>
                                    <?= $item->email ?>
                                </td>
                                <td>
                                    <?= $item->phone ?>
                                </td>
                                <td>
                                    <?= $item->city ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($item->whenSubmitted)) ?></td>
                                <td>
                                    <?= $status ?>
                                </td>
                                <td>
                                    <?php
                                    if($item->status != "a") {
                                        ?>
                                        <a href="javascript:;" class="label label-info" onclick="approveItem(<?= $item->id ?>);">
                                            Approve
                                        </a>
                                        <?php
                                    }
                                    ?>
                                    <label class="label label-warning" data-toggle="modal" data-target="#add<?= $item->id ?>" style="margin-left:5px;cursor:pointer;">
                                        <i class="fa fa-edit"></i>
                                    </label>

                                    <label class="label label-danger" onclick="deleteItem(<?= $item->id ?>);" style="margin-left:5px;cursor:pointer;">
                                        <i class="fa fa-times"></i>
                                    </label>
                                </td>
                            </tr>

                            <div class="modal fade" id="add<?= $item->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Edit Achiever</h4>
                                        </div>
                                        <form name="addNewItem<?= $item->id ?>">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Firstname</label>
                                                    <input type="text" name="firstname" value="<?= $item->firstname ?>" class="form-control" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Lastname</label>
                                                    <input type="text" name="lastname" value="<?= $item->lastname ?>" class="form-control" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="text" name="email" value="<?= $item->email ?>" class="form-control" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Phone</label>
                                                    <input type="text" name="phone" value="<?= $item->phone ?>" class="form-control" />
                                                </div>
                                                <div class="form-group">
                                                    <label>City</label>
                                                    <input type="text" name="city" value="<?= $item->city ?>" class="form-control" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Replace Existing Image</label>
                                                    <input type="file" name="uploaded_file" class="form-control" />
                                                </div>
                                                <div id="returnStatusAddNew<?= $item->id ?>"></div>
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
                                        url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-achiever",
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
            </div>
        </div>


        <!-- -------------- DEMO Break -------------- -->
        <div class="mv40"></div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>
<div id="deleteProduct"></div>
<script type="text/javascript">
    function approveItem(id) {
        $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=approve-achiever&id="+id);
    }
    function deleteItem(id) {
        if (window.confirm("Are you sure you want to delete this item permanently?")) {
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-achiever&id="+id);
            $("#item"+id).fadeOut();
        }
    }
</script>

<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add New</h4>
            </div>
            <form name="addNewItem">
                <div class="modal-body">
                    <p>When adding a new item here, it's automatically marked as approved.</p>
                    <div class="form-group">
                        <label>Firstname</label>
                        <input type="text" name="firstname" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Lastname</label>
                        <input type="text" name="lastname" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="uploaded_file" class="form-control" />
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
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=add-achiever",
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
