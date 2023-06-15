<?php

$this->setControllers(array("courseCategory", "course"));

$item = $this->courseCategory->getCourseCategoryEdit();

$allCourses = ORM::for_table("courseCategories")->where_not_equal("id", $item->id)->order_by_asc("title")->find_many();
$metaTitle = "Edit Course";
include BASE_PATH . 'blume.header.base.php';
?>
<link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/select2/css/core.css">
<style>
    .select2.select2-container {
        width: 100% !important;
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
                <span class="panel-title">Edit Course - <?= $item->title ?></span>
                <div class="tab-block">
                    <ul class="nav nav-tabs"></ul>
                    <div class="tab-content p30">
                        <div id="tab1" class="tab-pane active">
                            <form name="addNewItem" autocomplete="off">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type="text" name="title" value="<?= $item->title ?>"
                                                   class="form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                        <label>Parents Category</label>
                                        <select class="form-control select2" name="parentID">
                                            <option value="">Select Category</option>
                                            <?php
                                            foreach ($this->getCourseCategories() as $category) {
                                                ?>
                                                <option value="<?= $category->id ?>"
                                                        <?php if ($category->id == $item->parentID) { ?>selected<?php } ?>>
                                                    <?= $category->title ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                        <label>Show on homepage?</label>
                                        <select class="form-control" name="showOnHome">
                                            <option value="0">No</option>
                                            <option value="1"
                                                    <?php if ($item->showOnHome == "1") { ?>selected<?php } ?>>
                                                Yes
                                            </option>
                                        </select>
                                    </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label>Upload/Replace Image</label>
                                            <input type="file" class="form-control" name="uploaded_file"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <img src="<?= $this->courseCategory->getCategoryImage($item->id, "thumb") ?>"
                                                 width="80" height="80"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control" id=""
                                                      cols="30"><?= $item->description ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <label>Meta Title</label>
                                            <input type="text" name="meta_title" class="form-control"
                                                   value="<?= $item->meta_title ?>"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <label>Meta Keywords</label>
                                            <input type="text" name="meta_keywords" class="form-control"
                                                   value="<?= $item->meta_keywords ?>"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <label>Meta Description</label>
                                            <textarea name="meta_description" class="form-control" id=""
                                                      cols="30"><?= $item->meta_description ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-success" value="Update"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt20" id="returnStatusAddNew"></div>

                                <input type="hidden" name="itemID" value="<?= $item->id ?>"/>
                            </form>

                            <script src='<?= SITE_URL ?>assets/blume/js/plugins/select2/select2.min.js'></script>
                            <script type="text/javascript">

                                $('.select2').select2();

                                jQuery("form[name='addNewItem']").submit(function (e) {
                                    //tinyMCE.triggerSave();
                                    e.preventDefault();

                                    // $('textarea[name="text"]').html($('.summernote').code());
                                    var formData = new FormData($(this)[0]);

                                    jQuery.ajax({
                                        url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-course-category",
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
            </div>
        </div>
    </div>
    <!-- -------------- DEMO Break -------------- -->
    <!-- -------------- /Column Center -------------- -->
</section>


<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
