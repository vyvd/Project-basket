<?php

$offer = $this->controller->getSingleOffer();

$metaTitle = "Edit Special Offer";
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
                <span class="panel-title">Edit Special Offer</span>
                <a href="<?= SITE_URL ?>special-offer/<?= $offer->slug ?>" target="_blank" class="btn btn-info pull-right" style="margin-left:5px;">
                    <i class="fa fa-search"></i>
                    View Offer
                </a>

            </div>

            <br />

            <div class="tab-block">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab1" data-toggle="tab">General</a>
                    </li>
                    <li>
                        <a href="#tab2" data-toggle="tab">Courses</a>
                    </li>
                </ul>
                <div class="tab-content p30">
                    <div id="tab1" class="tab-pane active">

                        <div class="row">
                            <div class="col-xs-12">
                                <form name="update" autocomplete="off">
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" class="form-control" name="title" value="<?= $offer->title ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Slug / URL <small>Needs to be unique</small></label>
                                                <input type="text" class="form-control" name="slug" value="<?= $offer->slug ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Price for 1 course</label>
                                                <input type="text" class="form-control" name="course1Price" value="<?= $offer->course1Price ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Price for more than 1 course</label>
                                                <input type="text" class="form-control" name="courseOtherPrice" value="<?= $offer->courseOtherPrice ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Max. Courses</label>
                                                <input type="text" class="form-control" name="maxCourses" value="<?= $offer->maxCourses ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Show/feature on user accounts?</label>
                                                <select class="form-control" name="showInAccounts">
                                                    <option value="0">No</option>
                                                    <option value="1" <?php if($offer->showInAccounts == "1") { ?>selected<?php } ?>>Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Primary Colour</label>
                                                <input type="color" value="<?= $offer->primaryCol ?>" name="primaryCol" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Secondary Colour</label>
                                                <input type="color" value="<?= $offer->secondCol ?>" name="secondCol" />
                                            </div>
                                        </div>
                                        <div class="col-xs-12">
                                            <h4>Description</h4>
                                            <textarea class="tinymce" name="contents"><?= $offer->contents ?></textarea>
                                        </div>
                                        <div class="col-xs-12">
                                            <h4>Account Description</h4>
                                            <textarea class="tinymce" name="contentsAccount"><?= $offer->accountDescription ?></textarea>
                                        </div>
                                        <div class="col-xs-4">
                                            <input type="submit" class="btn btn-success" value="Update" />
                                        </div>

                                    </div>
                                    <input type="hidden" name="itemID" value="<?= $offer->id ?>" />
                                </form>
                                <br />
                                <div id="returnGeneral"></div>
                                <script type="text/javascript">
                                    jQuery("form[name='update']").submit(function(e) {
                                        tinyMCE.triggerSave();
                                        e.preventDefault();
                                        var formData = new FormData($(this)[0]);

                                        jQuery.ajax({
                                            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-offer-page",
                                            type: "POST",
                                            data: formData,
                                            async: true,
                                            success: function (msg) {
                                                jQuery('#returnGeneral').html(msg);
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
                    <div id="tab2" class="tab-pane">

                        <h4>Which course(s) are part of this special offer?</h4>

                        <form name="editCategories">
                            <div class="row">
                                <?php
                                $existing = @$offer->courses ? json_decode($offer->courses) : null;
                                foreach($this->getAllCourses() as $course) {

                                    ?>
                                    <div class="col-xs-6">
                                        <label>
                                            <input type="checkbox" name="courses[]" <?php if(in_array($course->id, $existing)) { ?>checked<?php } ?> value="<?= $course->id ?>" />
                                            <?= $course->title ?>
                                            <?php if($course->usImport == "1") { ?><label class="label label-danger">US</label><?php } ?>
                                        </label>
                                    </div>
                                    <?php

                                }
                                ?>
                            </div>

                            <input type="hidden" name="itemID" value="<?= $offer->id ?>" />

                            <input type="submit" class="btn btn-success" value="Update" />

                            <div id="returnStatusCats"></div>

                        </form>
                        <script type="text/javascript">
                            jQuery("form[name='editCategories']").submit(function(e) {
                                e.preventDefault();
                                // $('textarea[name="text"]').html($('.summernote').code());
                                var formData = new FormData($(this)[0]);

                                jQuery.ajax({
                                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-offer-courses",
                                    type: "POST",
                                    data: formData,
                                    async: true,
                                    success: function (msg) {
                                        jQuery('#returnStatusCats').html(msg);
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


        <!-- -------------- DEMO Break -------------- -->
        <div class="mv40"></div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>

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


<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
