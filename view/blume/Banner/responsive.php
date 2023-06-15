<?php

$metaTitle = "Edit Banner";
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


                <a href="javascript:;" class="btn btn-success pull-right" data-toggle="modal" data-target="#addBanner">
                    <i class="fa fa-plus"></i>
                    Add Banner
                </a>


                <span class="panel-title">Add a Banner</span>
                <p>View and manage all Banner's on <?= SITE_NAME ?></p>

                <br />
                <br />
            </div>
<style>
    td{max-width: 0;overflow: hidden;text-overflow: ellipsis;}
    </style>
            <!-- displays the frame for the Banners table -->
            <div class="panel-body pn">
                <div  class="table-responsive">
                    <table  class="table datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Banner Text GBP</th>
                                <th>Banner Text USD</th>
                                <th>Banner Text EUR</th>
                                <th>Banner Text CAD</th>
                                <th>Banner Text AUD</th>
                                <th>Banner Text NZD</th>
                                <th>Banner Color</th>
                                <th>Banner Text Color</th>
                                <th>Banner Ref </th>
                                <th>Banner Timer</th>
                                <th>banner State</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Banner Text GBP</th>
                                <th>Banner Text USD</th>
                                <th>Banner Text EUR</th>
                                <th>Banner Text CAD</th>
                                <th>Banner Text AUD</th>
                                <th>Banner Text NZD</th>
                                <th>Banner Color</th>
                                <th>Banner Text Color</th>
                                <th>Banner Ref </th>
                                <th>Banner Timer</th>
                                <th>banner State</th>
                                <th>Actions</th>
                            </tr>
                        </tfoot>

                    </table>
                    <!-- pulls all the data from Banners and puts it in the table -->
                    <script type="text/javascript">
                        $(document).ready(function() {

                            $('.datatable').DataTable({
                                scrollX: true,
                                "processing": true,
                                "serverSide": true,
                                "order": [
                                    [0, "desc"]
                                ],
                                "ajax": {
                                    "type": "GET",
                                    "url": "<?= SITE_URL ?>blume/datatables/banner",
                                    "dataSrc": function(json) {

                                        return json.data;
                                    }
                                },
                                
                                "drawCallback": function(settings) {
                                    $(".editItem").click(function() {
                                        var id = $(this).data("edit");
                                        $("#edit").modal("toggle");
                                        $("#ajaxEdit").load("<?= SITE_URL ?>ajax?c=blumeNew&a=edit-Banner-form&id=" + id);
                                        
                                    });
                                }
                                
                            });
                            
                        });

                        function deleteItem(x) {
                            var result = confirm("Are you sure you want to delete this? This action can not be undone");

                            if (result == true) {
                                $(".deleteAccountReturn").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-Banner&id=" + x);

                                $("#item" + x).fadeOut("slow");
                            }
                        }

                        function changeBannerState(x) {
                            var result = confirm("Are you sure you want to change the banner State?");

                            if (result == true) {
                                $(".deleteAccountReturn").load("<?= SITE_URL ?>ajax?c=blumeNew&a=banner-State&id=" + x);
                            }
                        }
                    </script>
                </div>
            </div>
            
        </div>




 


    </div>


</section>

<div class="deleteAccountReturn"></div>


        <script src='<?= SITE_URL ?>assets/js/tinymce/tinymce.min.js'></script>
        <script>
    tinymce.init({
        selector: '.tinymce',
        plugins: 'table link lists hr textcolor emoticons image imagetools media link preview visualchars visualblocks wordcount template code',
        toolbar: 'undo redo paste | styleselect template | bold italic strikethrough underline | link image media | bullist numlist | aligncenter alignleft alignright alignjustify alignnone | blockquote | backcolor forecolor | removeformat visualblocks code',
        height: '150',
        templates: [{
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
        content_css: "https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css,<?= SITE_URL ?>assets/css/global.css,<?= SITE_URL ?>assets/css/editor.css",
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,
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
                reader.onload = function() {
                    // Note: Now we need to register the blob in TinyMCEs image blob
                    // registry. In the next release this part hopefully won't be
                    // necessary, as we are looking to handle it internally.
                    var id = 'imageID' + (new Date()).getTime();
                    var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);

                    // call the callback and populate the Title field with the file name
                    cb(blobInfo.blobUri(), {
                        title: file.name
                    });
                };
                reader.readAsDataURL(file);
            };

            input.click();
        }
    });
</script>

<!-- popup for add a Banner -->
<div class="modal fade" id="addBanner" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Banner</h4>
            </div>
            <form name="addNewItem" autocomplete="off">
                <div class="modal-body">
                    <div class="form-group">
                    </div>
                    <div class="form-group">
                        <label for="bannerTextGBP">Banner Text GBP</label>
                        <textarea name="bannerTextGBP" maxlength="120" class="tinymce form-control" id="bannerTextGBP" style="height:150px;"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="bannerTextUSD">Banner Text USD</label>
                        <textarea name="bannerTextUSD" maxlength="120" class="tinymce form-control" id="bannerTextUSD" style="height:150px;"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="bannerTextEUR">Banner Text EUR</label>
                        <textarea name="bannerTextEUR" maxlength="120" class="tinymce form-control" id="bannerTextEUR" style="height:150px;"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="bannerTextCAD">Banner Text CAD</label>
                        <textarea name="bannerTextCAD" maxlength="120" class="tinymce form-control" id="bannerTextCAD" style="height:150px;"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="bannerTextAUD">Banner Text AUD</label>
                        <textarea name="bannerTextAUD" maxlength="120" class="tinymce form-control" id="bannerTextAUD" style="height:150px;"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="bannerTextNZD">Banner Text NZD</label>
                        <textarea name="bannerTextNZD" maxlength="120" class="tinymce form-control" id="bannerTextNZD" style="height:150px;"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="bannerColor">Banner backgound Color </label>
                        <input type="color" id="bannerColor colorpicker" class="form-control" value="" name="bannerColor">
                    </div>
                    <div class="form-group">
                        <label for="bannerTextColor">Banner Text Color </label>
                        <input type="color" id="bannerTextColor colorpicker" class="form-control" value="" name="bannerTextColor">
                    </div>
                    <div class="form-group">
                        <label for="bannerRef">Banner Reference</label>
                        <input type="text" id="bannerRef" class="form-control"  name="bannerRef">
                    </div>
                    <div class="form-group">
                        <label for="bannerTimer">Banner End Date</label>
                        <input type="datetime-local" id="bannerTimer" class="form-control" name="bannerTimer">
                    </div>
                    <div class="form-group">
                        <label for="bannerState">Banner State </label>
                        <select id="bannerState" value="off" default="off"class="form-control" name="bannerState">
                            <option value="">Select a banner State</option>
                            <option value="on">On</option>
                            <option value="off">Off</option>
                        </select>
                    </div>
                    <div id="returnStatusAddNew"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src='<?= SITE_URL ?>assets/blume/js/plugins/select2/select2.min.js'></script>
<script type="text/javascript">
    $('.select2').select2();
    jQuery("form[name='addNewItem']").submit(function(e) {
        tinyMCE.triggerSave();
        e.preventDefault();

        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=add-Banner",
            type: "POST",
            data: formData,
            async: true,
            success: function(msg) {
                jQuery('#returnStatusAddNew').html(msg);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
</script>

<!-- pop-up form for editing Banners after the admin hits the edit button  -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Banner</h4>
            </div>
            <form name="editItem">
                <div class="modal-body">
                    <div id="ajaxEdit">
                        <p class="text-center">
                            <i class="fa fa-spin fa-spinner"></i>
                        </p>
                    </div>
                    <div id="returnStatusEdit"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery("form[name='editItem']").submit(function(e) {
        tinyMCE.triggerSave();
        e.preventDefault();

        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-Banner",
            type: "POST",
            data: formData,
            async: true,
            success: function(msg) {
                jQuery('#returnStatusEdit').html(msg);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
</script>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

<?php include BASE_PATH . 'blume.footer.base.php'; ?>