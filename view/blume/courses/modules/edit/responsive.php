<?php
$this->setControllers(array("courseModule", "media"));

$module = $this->courseModule->getModuleByID($_GET['id']);

$audioMedia = $this->courseModule->getModuleAudio($module->id);
$videoTransMedia = $this->courseModule->getModuleVideoTrans($module->id);
$mp3CloudMedia = $this->courseModule->getModuleMp3Audio($module->id);

$subModules = $this->courseModule->getSubmodules($module->id);

$metaTitle = "Edit Module";
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
                <span class="panel-title">Edit Module - <?= $module->title ?></span>
                <?php if(@$module->parentID){?>
                    <a href="<?= SITE_URL.'blume/courses/modules/edit?id='.$module->parentID.'#lesson_section';?>" class="btn btn-system pull-right">
                        <i class="fa fa-arrow-left"></i>
                        Back to Modules List
                    </a>
                <?php } else{ ?>
                    <a href="<?= SITE_URL.'blume/courses/edit?tab=modules&id='.$module->courseID;?>" class="btn btn-system pull-right">
                        <i class="fa fa-arrow-left"></i>
                        Back to Modules List
                    </a>
                <?php }?>

                <a href="javascript:;" data-toggle="modal" data-target="#uploadPDF" class="btn btn-warning pull-right" style="margin-right:10px;">
                    <i class="fa fa-upload"></i>
                    Upload PDF
                </a>

            </div>
            <div class="panel-body">

                <?php
                if($this->get["restore"] == "true") {
                    $this->setAlertSuccess("The contents of this module are now restored back to your selected version.");
                }
                ?>

                <div class="module" id="module<?= $module->id ?>">
                    <div class="row">
                        <form name="editModule<?= $module->id ?>">
                            <div class="col-xs-8">
                                <div class="form-group">
                                    <label>Module Name</label>
                                    <input type="text" class="form-control" name="title" value="<?= $module->title ?>" />
                                </div>
                            </div>

                            <div class="col-xs-4">
                                <div class="form-group">
                                    <label>Estimated Time <small>(minutes)</small></label>
                                    <input type="number" class="form-control" name="estTime" value="<?= $module->estTime ?>" placeholder="Minutes..." />
                                </div>
                            </div>

<!--                            <div class="col-xs-4">-->
<!--                                <div class="form-group">-->
<!--                                    <label>Layouts</label>-->
<!--                                    <select class="form-control" name="new_style_with_video">-->
<!--                                        <option value="0">Default</option>-->
<!--                                        <option value="1" >New Style With Video</option>-->
<!--                                    </select>-->
<!--                                </div>-->
<!--                            </div>-->
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <label>Order</label>
                                    <input type="number" class="form-control" name="ord" value="<?= $module->ord ?>" />
                                </div>
                            </div>

                            <div class="col-xs-4">
                                <label>Does this Module has optional worksheet section?</label>
                                <div class="form-group">
                                    <input rel="<?= $module->id?>" class="form-check-input" type="radio" name="has_optional_section" id="inlineRadio1<?= $module->id?>" value="1" <?php if( $module->has_optional_section == 1 ) { ?> checked <?php } ?> />
                                    <label class="form-check-label" for="inlineRadio1<?= $module->id?>">Yes</label>

                                    <input rel="<?= $module->id?>" class="form-check-input" type="radio" name="has_optional_section" id="inlineRadio0<?= $module->id?>" value="0" <?php if( $module->has_optional_section == 0 ) { ?> checked <?php } ?>/>
                                    <label class="form-check-label" for="inlineRadio0<?= $module->id?>">No</label>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <label>Content Type</label>
                                    <select name="contentType" class="form-control content_type" data-content-id="<?= $module->id?>">
                                        <option value="">Select Content Type</option>
                                        <option value="video" <?php if($module->contentType == 'video'){?>selected <?php }?>>Video</option>
                                        <option value="quiz" <?php if($module->contentType == 'quiz'){?>selected <?php }?>>Test</option>
                                        <option value="upload" <?php if($module->contentType == 'upload'){?>selected <?php }?>>Upload</option>
                                        <option value="text" <?php if($module->contentType == 'text'){?>selected <?php }?>>Rich text</option>
                                        <option value="assessment" <?php if($module->contentType == 'assessment'){?>selected <?php }?>>Assessment</option>
                                        <option value="assignment" <?php if($module->contentType == 'assignment'){?>selected <?php }?>>Assignment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 p-0" id="competeyes<?= $module->id?>" style="display: <?= $module->has_optional_section == 1 ? 'block' : 'none' ?>">
                                <div class="row">
                                    <div class="col-xs-4" >
                                        <div class="form-group">
                                            <label>Worksheet Title</label>
                                            <input type="text" class="form-control" name="worksheet_title" value="<?= $module->worksheet_title ?>" />
                                        </div>
                                    </div>

                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <label>Worksheet Text</label>
                                            <input type="text" class="form-control" name="worksheet_text" value="<?= $module->worksheet_text ?>" />
                                        </div>
                                    </div>

                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <label>Worksheet Estimate Time</label>
                                            <input type="number" class="form-control" name="worksheet_estimate_time" value="<?= $module->worksheet_estimate_time ?>" />
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label>Replace Existing Worksheet</label>
                                            <input type="file" name="worksheet_replace" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label>Replace Existing / Add Featured Image</label>
                                            <input type="file" name="featured_image_replace" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" name="description" rows="3"><?= $module->description ?></textarea>
                                </div>
                            </div>

                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Disable the "next" button timer on this module?</label>
                                    <select class="form-control" name="disableModuleTimer">
                                        <option value="0">No</option>
                                        <option value="1" <?php if($module->disableModuleTimer == "1") { ?>selected<?php } ?>>Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Slug/URL <small>newskillsacademy.com/module/{slug}</small></label>
                                    <input type="text" class="form-control" name="moduleSlug" value="<?= $module->slug ?>" />
                                    </div>
                                </div>

                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Is this a video only module?</label>
                                    <select class="form-control" name="new_style_with_video">
                                        <option value="0">No</option>
                                        <option value="1" <?php if($module->new_style_with_video == "1") { ?>selected<?php } ?>>Yes</option>
                                    </select>
                                </div>
                            </div>

                            <?php
                            $vimeo = $this->courseModule->getEmbedVideoById($module->id);
                            ?>

                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Vimeo URL</label>
                                    <input type="text" class="form-control" name="vimeoURL" value="<?= $vimeo->url ?>" />
                                </div>
                            </div>

                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Upload Audio</label>
                                    <input type="file" class="form-control" name="uploaded_file" accept="audio/*" />
                                </div>
                                <?php if(@$audioMedia->url){?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <audio controls>
                                                    <source src="<?php echo AWSService::getFromS3($audioMedia->fileName);?>" type="audio/ogg">
                                                    <source src="<?php echo AWSService::getFromS3($audioMedia->fileName);?>" type="audio/mpeg">
                                                </audio>
                                            </div>
                                            <div class="col-sm-2 mt5">
                                            <button onclick="deleteModelItem('<?= $audioMedia->id ?>', 'media', true)" type="button" class="btn btn-danger btn-small ml10">
                                                <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Upload Video Transcription</label>
                                    <input type="file" class="form-control" name="video_trans" />
                                </div>
                                <?php if(@$videoTransMedia->url){?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <a href="<?= $videoTransMedia->url ?>" target="_blank">View Existing</a>
                                            </div>
                                            <div class="col-sm-2 mt5">
                                                <button data-id="<?= $videoTransMedia->id?>" data-table="media" type="button" class="btn btn-danger btn-small ml10 deleteItem">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Mp3 Audio URL</label>
                                    <input type="text" class="form-control" name="mp3_audio" />
                                </div>
                                <?php if(@$mp3CloudMedia->url){?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <audio controls>
                                                    <source src="<?php echo $mp3CloudMedia->url;?>" type="audio/mp3">
                                                </audio>
                                            </div>
                                            <div class="col-sm-2 mt5">
                                                <button data-id="<?= $mp3CloudMedia->id ?>" data-table="media" type="button" class="btn btn-danger btn-small ml10 deleteItem">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php }?>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>Edit Contents <a href="javascript:;" onclick="$('#editModuleContents<?= $module->id ?>').slideToggle();">Show / Hide</a></label>
                                    <div id="editModuleContents<?= $module->id ?>">
                                        <textarea name="contents" class="tinymce"><?= $module->contents ?></textarea>
                                    </div>
                                </div>

                            </div>

                            <div class="col-xs-12">
                                <button type="submit" class="btn btn-success btn-small">
                                    <i class="fa fa-check"></i>
                                    Update
                                </button>
                                <hr />
                            </div>
                            <input type="hidden" name="itemID" value="<?= $module->id ?>" />
                            <div id="returnStatusAddNew"></div>
                        </form>
                            <div class='col-xs-12 moduleContentType upload'>
                                <?php include ('includes/upload.php'); ?>
                            </div>
                            <div class='col-xs-12 moduleContentType assignment'>
                                <?php include ('includes/assignment.php'); ?>
                            </div>
                            <div class='col-xs-12 quiz'>
                                <?php include ('includes/quiz.php'); ?>
                            </div>


                        <script type="text/javascript">

                            // $('input[name="has_optional_section"]').bind('change',function(){
                            //     var showOrHide = ($(this).val() == 0 ) ? true : false;
                            //     $('#competeyes').toggle(showOrHide);
                            // });
                            $('input[type=radio][name=has_optional_section]').change(function() {
                                var r = $(this).attr('rel');
                                if (this.value == 1) {
                                    $("#competeyes"+r).css('display','block');
                                }else{
                                    $("#competeyes"+r).css('display','none');
                                }
                            });

                            jQuery("form[name='editModule<?= $module->id ?>']").submit(function(e) {

                                tinyMCE.triggerSave();
                                e.preventDefault();
                                var formData = new FormData($(this)[0]);

                                jQuery.ajax({
                                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-course-module",
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
                                return false;

                            });
                        </script>

                    </div>
                </div>

                <script type="text/javascript">
                    $( document ).ready(function() {

                        // Hide all contents
                        $(".moduleContentType").css('display', 'none');
                        $(".moduleContentType.<?= $module->contentType?>").css('display', 'block');


                        $("#editModuleContents<?= $module->id ?>").slideUp();
                        $("#editModuleQuizContents<?= $module->id ?>").slideUp();

                        $(".content_type").on('change', function (){
                            //alert($(this).val());
                        })

                        $(".btn-addfile").on('click', function (){
                            var type = $(this).data('type');
                            var total = parseInt($("#total"+type).val()) + 1;

                            $(".more"+type).append('<div class="row mt10 ">' +
                                '<div class="col-xs-4"><input class="form-control" type="text" name="'+type+'['+total+'][title]" value=""></div>' +
                                '<div class="col-xs-4"><input class="form-control" type="file" name="'+type+'['+total+'][file]"></div>' +
                                '<div class="col-xs-4"></div>' +
                                '</div>');
                            $("#total"+type).val(total);
                        });
                        $(".delrow").on('click', function (){
                            alert("ss");
                        });

                    });
                </script>

                <?php
                    if(empty($module->parentID)){
                ?>
                        <hr id="lesson_section" />
                        <h4>Lessons:</h4>
                        <table width="100%" class="table lessonDatatable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th >Title</th>
                                <th>Type</th>
                                <th>Order</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if(count($subModules) >=1) {
                                        foreach ($subModules as $lesson) {
                                ?>
                                            <tr>
                                                <td><?php echo $lesson->id;?></td>
                                                <td><?php echo $lesson->title;?></td>
                                                <td><?php echo $lesson->contentType;?></td>
                                                <td><?php echo $lesson->ord;?></td>
                                                <td>
                                                    <a href="<?= SITE_URL?>blume/courses/modules/edit?id=<?= $lesson->id?>" class="label label-warning"><i class="fa fa-edit"></i></a>
                                                    <label class="label label-danger deleteItem" data-id="<?= $lesson->id?>" data-table="courseModules" data-reload="true"><i class="fa fa-trash"></i></label>
                                                </td>
                                            </tr>

                                <?php
                                        }
                                    }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th >Title</th>
                                    <th>Type</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                            </tfoot>
                        </table>
                        <script type="text/javascript">
                            function deleteModuleItem(x) {
                                var result = confirm("Are you sure you want to delete this? This action can not be undone");

                                if (result==true) {

                                    jQuery.ajax({
                                        url: "<?= SITE_URL ?>ajax?c=blumeNew&a=deleteCourseModule&id="+x,
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
                            $(document).ready(function() {
                                $('.lessonDatatable').DataTable({
                                    "order": [[ 3, "asc" ]]
                                });

                            } );

                        </script>
                <?php
                    }

                ?>

                <hr />

                <h4>Revision history:</h4>

                <table width="100%" class="table historyDatatable">
                    <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="40%" >Title</th>
                        <th width="20%">Saved</th>
                        <th width="20%">Author</th>
                        <th width="10%">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $history = ORM::for_table("courseModuleHistory")->where("moduleID", $module->id)->order_by_desc("id")->find_many();

                    foreach($history as $item) {

                        $author = ORM::for_table("blumeUsers")->find_one($item->adminID);

                        ?>
                        <tr>
                            <td width="10%"><?= $item->id ?></td>
                            <td width="40%" ><?= $item->title ?></td>
                            <td width="20%"><?= date('d/m/Y @ H:i:s', strtotime($item->whenSaved)) ?></td>
                            <td width="20%"><?= $author->name.' '.$author->surname ?></td>
                            <td width="10%">
                                <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=restore-module&id=<?= $item->id ?>" class="label label-info" onclick="return confirm('Are you sure you want to restore this version of the module from <?= date('d/m/Y @ H:i:s', strtotime($item->whenSaved)) ?>?');">
                                    Restore
                                </a>
                            </td>
                        </tr>
                        <?php

                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="40%" >Title</th>
                        <th width="20%">Saved</th>
                        <th width="20%">Author</th>
                        <th width="10%">Actions</th>
                    </tr>
                    </tfoot>
                </table>

                <script type="text/javascript">
                    $(document).ready(function() {
                        $('.historyDatatable').DataTable({
                            "order": [[ 0, "desc" ]]
                        });

                    } );

                </script>


            </div>
        </div>
    </div>
</section>

    <script src='<?= SITE_URL ?>assets/js/tinymce/tinymce.min.js'></script>
    <script>
        var wWidth = $(window).width();
        var wHeight = $(window).height();
        tinymce.init({
            selector: '.tinymce',
            plugins: 'table link lists hr textcolor emoticons image imagetools media link preview visualchars visualblocks wordcount template code',
            toolbar: 'undo redo paste | styleselect template | bold italic strikethrough underline | link image media | bullist numlist | aligncenter alignleft alignright alignjustify alignnone | blockquote | backcolor forecolor | removeformat visualblocks code',
            height: '550',
            code_dialog_height: wHeight * 0.7,
            code_dialog_width: wWidth * 0.7,
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
                },
                {
                    title: "Sub Header with dotted underline",
                    description: "",
                    url: "<?= SITE_URL ?>assets/cdn/editorTemplates/subHeaderDotted.html"
                },
                {
                    title: "Dotted Highlight",
                    description: "",
                    url: "<?= SITE_URL ?>assets/cdn/editorTemplates/dottedHighlight.html"
                },
                {
                    title: "Warning",
                    description: "",
                    url: "<?= SITE_URL ?>assets/cdn/editorTemplates/warning.html"
                },
                {
                    title: "Fact",
                    description: "",
                    url: "<?= SITE_URL ?>assets/cdn/editorTemplates/fact.html"
                },
                {
                    title: "Assignment",
                    description: "",
                    url: "<?= SITE_URL ?>assets/cdn/editorTemplates/assignment.html"
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

        // Active Tabs
        $(document).ready(function(){
            var tab = 'tab1';
            <?php
            if(@$_GET['tab'] && $_GET['tab'] == 'modules'){
            ?>
            tab = 'tabModules'
            <?php
            }
            ?>


            activaTab(tab);
        });

        function activaTab(tab){
            $('.nav-tabs a[href="#' + tab + '"]').tab('show');
        }
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