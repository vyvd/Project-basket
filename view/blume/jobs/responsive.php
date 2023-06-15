<?php

$metaTitle = "Jobs";
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
            <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=export-job-stats-csv" style="margin-left:5px;"class="btn btn-info pull-right">
                        Export All (CSV)
                    </a>


                <a href="javascript:;" class="btn btn-success pull-right" data-toggle="modal" data-target="#addJob">
                    <i class="fa fa-plus"></i>
                    Add Job
                </a>


                <span class="panel-title">Add a Job</span>
                <p>View and manage all Job Postings on <?= SITE_NAME ?></p>


                <br />
                <br />
            </div>
            <!-- displays the frame for the jobs table -->
            <div class="panel-body pn">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Job Title</th>
                                <th>Job Description</th>
                                <th>Company Name</th>
                                <th>Location</th>
                                <th>Salary</th>
                                <th>Application Link</th>
                                <th>Closing Date</th>
                                <th>Date Posted</th>
                                <th>Amount Of Clicks</th>
                                <th>Job State</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Job Title</th>
                                <th>Job Description</th>
                                <th>Company Name</th>
                                <th>Location</th>
                                <th>Salary</th>
                                <th>Application Link</th>
                                <th>Closing Date</th>
                                <th>Date Posted</th>
                                <th>Amount Of Clicks</th>
                                <th>Job State</th>
                                <th>Actions</th>
                            </tr>
                        </tfoot>
                    <style>
                    .datatable.dataTable td:nth-child(7){
                        word-break: break-all;
                        }
                    </style>
                    </table>
                    <!-- pulls all the datafrom jobs and puts it in the table -->
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $('.datatable').DataTable({
                                "processing": true,
                                "serverSide": true,
                                "order": [
                                    [0, "desc"]
                                ],
                                buttons: [ 'csv' ],
                                "ajax": {
                                    "type": "GET",
                                    "url": "<?= SITE_URL ?>blume/datatables/jobs",
                                    "dataSrc": function(json) {

                                        return json.data;

                                    }
                                },
                                "drawCallback": function(settings) {
                                    $(".editItem").click(function() {
                                        var id = $(this).data("edit");
                                        $("#edit").modal("toggle");
                                        $("#ajaxEdit").load("<?= SITE_URL ?>ajax?c=blumeNew&a=edit-job-form&id=" + id);
                                    });
                                }
                            });
                        });

                        function deleteItem(x) {
                            var result = confirm("Are you sure you want to delete this? This action can not be undone");

                            if (result == true) {
                                $(".deleteAccountReturn").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-job&id=" + x);

                                $("#item" + x).fadeOut("slow");
                            }
                        }

                        function changeJobState(x) {
                            var result = confirm("Are you sure you want to change the Job State?");

                            if (result == true) {
                                $(".deleteAccountReturn").load("<?= SITE_URL ?>ajax?c=blumeNew&a=job-State&id=" + x);
                            }
                        }
                    </script>
                </div>

            </div>

        </div>




        <div class="mv40"></div>


    </div>


</section>

<div class="deleteAccountReturn"></div>
<script src='<?= SITE_URL ?>assets/js/tinymce/tinymce.min.js'></script>
<!-- popup for add a job -->
<div class="modal fade" id="addJob" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Job</h4>
            </div>
            <form name="addNewItem" autocomplete="off">
                <div class="modal-body">
                    <div class="form-group">
                    </div>
                    <div class="form-group">
                        <label for="jobTitle">Job Title</label>
                        <input type="text" id="jobTitle" class="form-control" name="jobTitle">
                    </div>
                    <div class="form-group">
                        <label for="jobDescription">Description</label>
                        <textarea name="jobDescription" class="tinymce form-control" id="jobDescription" style="height:150px;"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="companyName">Company Name</label>
                        <input type="text" id="companyName" class="form-control" name="companyName">
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" class="form-control" name="location">
                    </div>
                    <div class="form-group">
                        <label for="salary">Salary</label>
                        <input type="number" id="salary" max="9999999999" step="any" class="form-control" name="salary">
                    </div>
                    <div class="form-group">
                        <label for="applicationLink">Link for applicaion</label>
                        <input type="url" id="applicationLink" class="form-control" name="applicationLink">
                    </div>
                    <div class="form-group">
                        <label for="closingDate">Closing date</label>
                        <input type="date" id="closingDate" class="form-control" name="closingDate">
                    </div>
                    <div class="form-group">
                        <label for="jobState">Job State </label>
                        <select id="jobState" value="off" default="off"class="form-control" name="jobState">
                            <option value="">Select a Job State</option>
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
            
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=add-jobs",
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
<!-- pop-up form for editing jobs after the admin hits the edit button  -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Job</h4>
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
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-job",
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