<?php
$this->setControllers(array("blog", "course"));
$item = $this->controller->getBlog();
$item->categories = $this->blog->getBlogCategories($item->id);
$metaTitle = "Edit Blog";
$blogCategories = $this->blog->getCategories();
$courses = $this->course->getAllCourses();

include BASE_PATH . 'blume.header.base.php';

?>
<link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/select2/css/core.css">
<style>
    .select2.select2-container{
        width: 100% !important;
    }
</style>
<form name="edit" class="allcp-form theme-primary">

    <input type="hidden" name="itemID" value="<?= $item->id ?>" />

    <!-- -------------- Content -------------- -->

    <section id="content" class="table-layout animated fadeIn">


        <div class="chute chute-center">



            <div id="return_status" style="position:relative;top:-26px;"></div>



            <div class="row" style="margin-top: -29px;margin-bottom: 17px;">

                <div class="col-xs-6">



                </div>

                <div class="col-xs-6">

                    <a href="javascript:;" data-toggle="modal" data-target="#uploadPDF" class="btn btn-warning pull-right" style="margin-left:10px;">
                        <i class="fa fa-upload"></i>
                        Upload PDF
                    </a>

                    <button class="btn btn-success pull-right" style="margin-left:5px;">Update</button>
                </div>

            </div>





            <h4>Contents - <?= $item->title ?></h4>

            <div class="panel mb25 mt5">

                <div class="panel-body pn" style="margin-top:0px;">


                    <div class="panel-body pn of-h">
                        <div class="row mb20">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" class="form-control" name="title" value="<?= $item->title ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="row mb20">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Replace Featured Image</label>
                                    <input type="file" class="form-control" name="uploaded_file" />
                                </div>
                            </div>
                        </div>
                        <div class="row mb20">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Publish Date</label>
                                    <input type="text" class="form-control" name="whenAdded" value="<?= $item->whenAdded ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="row mb20">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Slug / URL</label>
                                    <input type="text" class="form-control" name="slug" value="<?= $item->slug ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="row mb20">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select courses to show in [courseblock]</label>
                                    <select class="form-control select2" name="courses[]"
                                            multiple="multiple">
                                        <?php
                                        $courses = explode(",", $item->courses);
                                        foreach (ORM::for_table("courses")->find_many() as $course) {
                                            ?>
                                            <option value="<?= $course->id ?>"
                                                    <?php if (in_array($course->id,
                                                        $courses)) { ?>selected<?php } ?>><?= $course->title ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="row mb20">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Select Categories</label>

                                    <select name="blogCategories[]" multiple class="form-control select2">
                                        <option value="">Select Categories</option>
                                        <?php
                                        foreach($blogCategories as $category) {
                                            ?>
                                            <option <?php if(@$item->categories && in_array($category->id, array_keys($item->categories))) {?> selected="selected" <?php }?> value="<?= $category->id ?>">
                                                <?= $category->title ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Select Course</label>
                                    <select name="courseID" class="form-control select2">
                                        <option value="">Select Course</option>
                                        <?php
                                        foreach($courses as $course) {
                                            ?>
                                            <option <?php if($item->courseID == $course->id) {?> selected="selected" <?php }?> value="<?= $course->id ?>">
                                                <?= $course->title ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <script src='<?= SITE_URL ?>assets/js/tinymce/tinymce.min.js'></script>
                        <script>
                            tinymce.init({
                                selector: '.tinymce',
                                plugins: 'table link lists hr textcolor emoticons image imagetools media link preview visualchars visualblocks wordcount template code',
                                toolbar: 'undo redo paste | styleselect template | bold italic strikethrough underline | link image media | bullist numlist | aligncenter alignleft alignright alignjustify alignnone | blockquote | backcolor forecolor | removeformat visualblocks code',
                                height: '800',
                                templates: [
                                    {
                                        title: "Default Starter",
                                        description: "",
                                        url: "<?= SITE_URL ?>assets/cdn/editorTemplates/moduleDefault.html"
                                    },
                                    {
                                        title: "Blue Summary Box",
                                        description: "",
                                        url: "<?= SITE_URL ?>assets/cdn/editorTemplates/blueSummary.html"
                                    },
                                    {
                                        title: "Grey Background Content",
                                        description: "",
                                        url: "<?= SITE_URL ?>assets/cdn/editorTemplates/greyBackground.html"
                                    },
                                    {
                                        title: "Did You Know / Tip",
                                        description: "",
                                        url: "<?= SITE_URL ?>assets/cdn/editorTemplates/didYouKnow.html"
                                    },
                                    {
                                        title: "Paper / Notepad",
                                        description: "",
                                        url: "<?= SITE_URL ?>assets/cdn/editorTemplates/paper.html"
                                    }
                                ],
                                content_css : "https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css,<?= SITE_URL ?>assets/css/global.css,<?= SITE_URL ?>assets/css/editor.css",
                                relative_urls : false,
                                remove_script_host : false,
                                convert_urls : true,
                                // enable title field in the Image dialog
                                image_title: true,
                                // enable automatic uploads of images represented by blob or data URIs
                                automatic_uploads: true,
                                // URL of our upload handler (for more details check: https://www.tinymce.com/docs/configure/file-image-upload/#images_upload_url)
                                images_upload_url: '<?= SITE_URL ?>ajax?c=blumeNew&a=tiny-mce-uploader',
                                // here we add custom filepicker only to Image dialog
                                file_picker_types: 'image',
                                // and here's our custom image picker
                                image_advtab: true,
                                file_picker_callback: function(cb, value, meta) {
                                    var input = document.createElement('input');
                                    input.setAttribute('type', 'file');
                                    input.setAttribute('accept', 'image/*');

                                    // Note: In modern browsers input[type="file"] is functional without
                                    // even adding it to the DOM, but that might not be the case in some older
                                    // or quirky browsers like IE, so you might want to add it to the DOM
                                    // just in case, and visually hide it. And do not forget do remove it
                                    // once you do not need it anymore.

                                    input.onchange = function() {
                                        var file = this.files[0];

                                        var reader = new FileReader();
                                        reader.onload = function () {
                                            // Note: Now we need to register the blob in TinyMCEs image blob
                                            // registry. In the next release this part hopefully won't be
                                            // necessary, as we are looking to handle it internally.
                                            var id = 'imageID' + (new Date()).getTime();
                                            var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                                            var base64 = reader.result.split(',')[1];
                                            var blobInfo = blobCache.create(id, file, base64);
                                            blobCache.add(blobInfo);

                                            // call the callback and populate the Title field with the file name
                                            cb(blobInfo.blobUri(), { title: file.name });
                                        };
                                        reader.readAsDataURL(file);
                                    };

                                    input.click();
                                }
                            });
                        </script>

                        <textarea name="contents" class="tinymce" style="height:400px;"><?= $item->contents ?></textarea>

                    </div>

                </div>

            </div>


            <div class="mv40"></div>





        </div>

        <!-- -------------- /Column Center -------------- -->



    </section>

</form>


<script src='<?= SITE_URL ?>assets/blume/js/plugins/select2/select2.min.js'></script>


<script type="text/javascript">

    $('.select2').select2();

    jQuery("form[name='edit']").submit(function(e) {

        tinyMCE.triggerSave();

        e.preventDefault();


        // $('textarea[name="text"]').html($('.summernote').code());


        var formData = new FormData($(this)[0]);






        jQuery.ajax({

            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-blog",

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

        return false;

    });





</script>

<div class="modal fade" id="uploadPDF" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Upload PDF</h4>
            </div>
            <form name="addNewItem">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select file</label>
                        <input type="file" name="file" class="form-control" />
                    </div>
                    <div id="returnStatusAddNewPDF"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
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
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=upload-pdf",
            type: "POST",
            data: formData,
            async: true,
            success: function (msg) {
                jQuery('#returnStatusAddNewPDF').html(msg);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
</script>


<!-- -------------- /Content -------------- -->

<?php include BASE_PATH . 'blume.footer.base.php'; ?>
