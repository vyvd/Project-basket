<?php

$metaTitle = "Messages";
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
                <span class="panel-title">Messages</span>
                <a href="javascript:;" onclick="$('.newMessage').slideToggle();" class="btn btn-success pull-right">New Message</a>
                <p>Easily send messages/notifications to all users.</p>
            </div>

            <div class="newMessage" style="display:none;">
                <form name="add">

                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" class="form-control" name="subject" />
                        </div>

                        <div class="form-group">
                            <label>Your Message</label>
                            <textarea class="tinymce" rows="6" name="message"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Should the users also be sent this message via email?</label>
                            <select class="form-control" name="sendEmail">
                                <option value="1">Yes, email them also</option>
                                <option value="0" selected>No, do not email them</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Optionally, set an expiry date:</label>
                            <input type="text" class="form-control datetimepicker" name="expiry" />
                        </div>

                        <p><em>Please note that messages sent from here are added to a queue to be sent in batches in order to reduce server load - so it may take a few minutes for all users to receive the message.</em></p>

                        <div id="returnStatusAddNew"></div>

                        <button type="submit" class="btn btn-primary sendBtn">Send</button>

                </form>
                <script type="text/javascript">
                    jQuery("form[name='add']").submit(function(e) {

                        tinyMCE.triggerSave();

                        e.preventDefault();
                        // $('textarea[name="text"]').html($('.summernote').code());
                        var formData = new FormData($(this)[0]);

                        $(".sendBtn").html('<i class="fa fa-spin fa-spinner"></i> Sending...');

                        jQuery.ajax({
                            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=message-all-users",
                            type: "POST",
                            data: formData,
                            async: true,
                            success: function (msg) {
                                jQuery('#returnStatusAddNew').html(msg);
                                $(".sendBtn").html('Send');
                            },
                            cache: false,
                            contentType: false,
                            processData: false
                        });
                    });
                </script>
            </div>

            <div class="panel-body pn">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Users</th>
                            <th>Sent At</th>
                            <th>Completed At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Subject</th>
                            <th>Users</th>
                            <th>Sent At</th>
                            <th>Completed At</th>
                            <th>Actions</th>
                        </tr>
                        </tfoot>
                    </table>

                    <script type="text/javascript">
                        $(document).ready(function() {

                            $('.datatable').DataTable( {
                                "processing": true,
                                "serverSide": true,
                                "ajax": {
                                    "type" : "GET",
                                    "url" : "<?= SITE_URL ?>blume/datatables/messages",
                                    "dataSrc": function ( json ) {

                                        return json.data;

                                    }
                                },
                                'columnDefs': [{
                                    'targets': 4,
                                    'orderable':false,
                                }],
                                "order": [[ 2, "desc" ]],
                                "drawCallback": function( settings ) {

                                }
                            } );

                        } );
                        function deleteItem(x) {
                            var result = confirm("Are you sure you want to delete this? This action can not be undone");

                            if (result==true) {

                                jQuery.ajax({
                                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=deleteMessageQueue&id="+x,
                                    type: "GET",
                                    data: '',
                                    async: true,
                                    success: function (msg) {
                                        location.reload();
                                    },
                                    cache: false,
                                    contentType: false,
                                    processData: false
                                });
                            }
                        }

                    </script>
                </div>
            </div>
        </div>


        <!-- -------------- DEMO Break -------------- -->
        <div class="mv40"></div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>
<div id="deleteMessage"></div>




<script src='<?= SITE_URL ?>assets/js/tinymce/tinymce.min.js'></script>
<script>
    tinymce.init({
        selector: '.tinymce',
        plugins: 'table link lists hr textcolor emoticons image imagetools media link preview visualchars visualblocks wordcount template code',
        toolbar: 'undo redo paste | styleselect template | bold italic strikethrough underline | link image media | bullist numlist | aligncenter alignleft alignright alignjustify alignnone | blockquote | backcolor forecolor | removeformat visualblocks code',
        height: '400',
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


<style>
    .ui-datepicker {
        z-index:9999 !important;
    }
</style>

<link rel="stylesheet" href="<?= SITE_URL ?>assets/blume/jquery.datetimepicker.min.css">
<script src="<?= SITE_URL ?>assets/blume/jquery.datetimepicker.min.js"></script>

<script type="text/javascript">
    $( document ).ready(function() {
        jQuery('.datetimepicker').datetimepicker();
    });
</script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
