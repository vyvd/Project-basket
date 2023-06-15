<?php
$this->setControllers(array("courseCategory"));
$brand = ORM::for_table("pages")->find_one($this->get["id"]);

$categories = explode(",", $brand->categories);

$metaTitle = "Edit Page";

include BASE_PATH . 'blume.header.base.php';

?>
<form name="editBrand" class="allcp-form theme-primary">

    <input type="hidden" name="itemID" value="<?= $brand->id ?>" />

    <!-- -------------- Content -------------- -->

    <section id="content" class="table-layout animated fadeIn">

<style type="text/css">
    .services-list {
        margin-bottom: 50px;
    }
    .services-list li {
        margin-bottom:15px;
    }
    .wrap.smaller {
        display:block;
        margin:0px auto;
        width:90%;
        max-width:850px;
    }
    .services-list li:nth-child(odd) {
        background: #eee9;
        padding: 10px;
        margin-bottom: 4px;
    }
    .services-list li:nth-child(even) {
        padding-left:10px;
        padding-right:10px;
    }
</style>






        <!-- -------------- /Column Left -------------- -->



        <!-- -------------- Column Center -------------- -->

        <div class="chute chute-center">



            <div id="return_status" style="position:relative;top:-26px;"></div>



            <div class="row" style="margin-top: -29px;margin-bottom: 17px;">

                <div class="col-xs-6">



                </div>

                <div class="col-xs-6">

                    <button class="btn btn-success pull-right" style="margin-left:5px;">Update</button>
                    <a href="<?= SITE_URL ?><?= $brand->slug ?>" target="_blank" class="btn-system btn pull-right">View</a>
                </div>

            </div>





            <h4>Contents - <?= $brand->title ?></h4>

            <div class="panel mb25 mt5">

                <div class="panel-body pn" style="margin-top:0px;">


                    <div class="panel-body pn of-h">

                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" value="<?= $brand->title ?>" />
                        </div>

                        <div class="form-group">
                            <label>Slug</label>
                            <input type="text" class="form-control" name="slug" value="<?= $brand->slug ?>" />
                        </div>

                        <div class="form-group">
                            <label>Hide newsletter popup/modal?</label>
                            <select class="form-control" name="hideNewsletterModal">
                                <option value="0">No</option>
                                <option value="1" <?php if($brand->hideNewsletterModal == "1") { ?>selected<?php } ?>>Yes</option>
                            </select>
                        </div>

                        <?php
                        if($brand->seoPage == "0") {
                            ?>
                            <div class="form-group">
                                <label>Page Width</label>
                                <select class="form-control" name="width">
                                    <option value="small">Small & Centered</option>
                                    <option value="full" <?php if($brand->width == "full") { ?>selected<?php } ?>>Full Width</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Show "Redeem A Voucher" form?</label>
                                <select class="form-control" name="showRedeem">
                                    <option value="0">No</option>
                                    <option value="1" <?php if($brand->showRedeem == "1") { ?>selected<?php } ?>>Yes</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Show H1 title?</label>
                                <select class="form-control" name="hideTitle">
                                    <option value="0">Yes</option>
                                    <option value="1" <?php if($brand->hideTitle == "1") { ?>selected<?php } ?>>No</option>
                                </select>
                            </div>

                            <script src='<?= SITE_URL ?>assets/js/tinymce/tinymce.min.js'></script>
                            <script>
                                tinymce.init({
                                    selector: '#mytextarea',
                                    plugins: 'table code link lists hr textcolor emoticons image imagetools media',
                                    content_css : '/assets/css/tinymce.css,<?= SITE_URL ?>assets/p2p/style.css',
                                    height: '800',
                                    relative_urls : false,
                                    extended_valid_elements: 'script[language|type|src]'
                                });
                            </script>

                            <textarea name="contents" id="mytextarea" style="height:400px;"><?= $brand->contents ?></textarea>
                            <?php
                        } else {
                            ?>
                            <div class="form-group">
                                <label>Course Categories To Feature/Show</label>
                                <div class="row" id="cats" style="max-height:300px;overflow:auto;padding-top:12px;">
                                    <?php
                                    foreach($this->courseCategory->getParentCategories() as $category) {
                                        $subCategories = $this->courseCategory->getSubCategories($category->id);
                                        ?>
                                        <div class="col-xs-6">
                                            <label>
                                                <input type="checkbox" name="categories[]" <?php if(in_array($category->id, $categories)) { ?>checked<?php } ?> value="<?= $category->id ?>" />
                                                <?= $category->title ?>
                                            </label>
                                            <?php
                                            if(count($subCategories) >= 1){
                                                ?>
                                                    <?php
                                                    foreach($subCategories as $subCategory ){
                                                        ?>
                                                        <div style="padding-left:15px;">
                                                            <label class="subcategory">
                                                                <input type="checkbox" name="categories[]"  <?php if(in_array($subCategory->id, $categories)) { ?>checked<?php } ?> value="<?= $subCategory->id ?>" />
                                                                <?= $subCategory->title ?>
                                                            </label>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>

                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <?php

                                    }
                                    ?>
                                </div>
                            </div>

                            <script src='<?= SITE_URL ?>assets/js/tinymce/tinymce.min.js'></script>
                            <script>
                                tinymce.init({
                                    selector: '.mytextarea',
                                    plugins: 'table code link lists hr textcolor emoticons image imagetools media',
                                    content_css : '/assets/css/tinymce.css,<?= SITE_URL ?>assets/p2p/style.css',
                                    height: '400',
                                    relative_urls : false
                                });
                            </script>

                            <div class="form-group">
                                <label>Top Content</label>
                                <textarea name="topContent" class="mytextarea" style="height:400px;"><?= $brand->topContent ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Bottom Content</label>
                                <textarea name="contents" class="mytextarea" style="height:400px;"><?= $brand->contents ?></textarea>
                            </div>
                            <?php
                        }
                        ?>


                    </div>

                </div>

            </div>


            <div class="mv40"></div>





        </div>

        <!-- -------------- /Column Center -------------- -->



    </section>

</form>





<script type="text/javascript">

    jQuery("form[name='editBrand']").submit(function(e) {

        tinyMCE.triggerSave();

        e.preventDefault();


        // $('textarea[name="text"]').html($('.summernote').code());


        var formData = new FormData($(this)[0]);






        jQuery.ajax({

            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-page",

            type: "POST",

            data: formData,

            async: true,

            success: function (msg) {

                jQuery('#return_status').html(msg);

            },

            cache: false,

            contentType: false,

            processData: false

        });



    });

    function summernoteShortDesc() {

        $('textarea[name="text"]').html($('.summernote').code());

    }



</script>



<!-- -------------- /Content -------------- -->

<?php include BASE_PATH . 'blume.footer.base.php'; ?>

