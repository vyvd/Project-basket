<?php

$metaTitle = "Testimonials";
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
                <span class="panel-title">Testimonials</span>
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
                            <th>Image</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Added</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $items = ORM::for_table("testimonials")->order_by_asc("name")->find_many();

                        foreach($items as $item) {
                            ?>
                            <tr id="item<?= $item->id ?>">
                                <td>
                                    <img src="<?= $this->controller->getTestimonialImage($item->id) ?>" style="height:50px;" />
                                </td>
                                <td>
                                    <?= $item->name ?>
                                </td>
                                <td>
                                    <?= $item->course ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($item->whenAdded)) ?></td>
                                <td>
                                    <?php
                                    if($item->location == "f") {
                                        echo "Footer";
                                    } else if($item->location == "c") {
                                        echo "Default";
                                    } else {
                                        echo "Testimonials Page";
                                    }
                                    ?>
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
                                                    <label>Name</label>
                                                    <input type="text" name="name" placeholder="e.g. Jane Doe" class="form-control" value="<?= $item->name ?>" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Course</label>
                                                    <input type="text" name="course" class="form-control" value="<?= $item->course ?>" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Testimonial</label>
                                                    <textarea class="form-control" name="testimonial" rows="3"><?= $item->testimonial ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>Vimeo URL</label>
                                                    <input type="text" name="video" placeholder="Paste here..." class="form-control" value="<?= $item->video ?>" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Where should this review be shown?</label>
                                                    <select class="form-control" name="location">
                                                        <option value="f">Footer</option>
                                                        <option value="c" <?php if($item->location == "c") { ?>selected<?php } ?>>Default</option>
                                                        <option value="p" <?php if($item->location == "p") { ?>selected<?php } ?>>Testimonials Page</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Replace Image Thumbnail</label>
                                                    <input type="file" name="uploaded_file" class="form-control" />
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
                                        url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-testimonial",
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
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-testimonial&id="+id);
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
                        <label>Name</label>
                        <input type="text" name="name" placeholder="e.g. Jane Doe" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Course</label>
                        <input type="text" name="course" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Testimonial</label>
                        <textarea class="form-control" name="testimonial" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Vimeo URL</label>
                        <input type="text" name="video" placeholder="Paste here..." class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Where should this review be shown?</label>
                        <select class="form-control" name="location">
                            <option value="f">Footer</option>
                            <option value="c">Default</option>
                            <option value="p">Testimonials Page</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Upload Image Thumbnail</label>
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
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=add-testimonial",
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
