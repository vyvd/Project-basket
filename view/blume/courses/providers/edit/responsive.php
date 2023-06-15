<?php

$this->setControllers(array("courseProviders", "course"));

$item = $this->courseProviders->getCourseProvidersEdit();

$allCourses = ORM::for_table("courseProviders")->where_not_equal("id", $item->id)->order_by_asc("name")->find_many();
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
                <span class="panel-title">Edit Course - <?= $item->name ?></span>
                <div class="tab-block">
                    <ul class="nav nav-tabs"></ul>
                    <div class="tab-content p30">
                        <div id="tab1" class="tab-pane active">
                            <form name="addNewItem" autocomplete="off">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="name" value="<?= $item->name ?>"
                                                   class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload/Replace Logo</label>
                                            <input type="file" class="form-control mb-2" name="uploaded_file">
                                            <?php
                                                $imageUrl = $this->courseProviders->getProviderImage($item->id);
                                                if(@$imageUrl) {
                                            ?>
                                                    <img class="mt-2" width="100" src="<?= $imageUrl;?>" />
                                            <?php
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
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
                                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-course-providers",
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
