<?php

$this->setControllers(array("course"));

$metaTitle = "Courses";
include BASE_PATH . 'blume.header.base.php';
?>
<link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/select2/css/core.css">
<style>
    .select2.select2-container{
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
                <span class="panel-title">Courses</span>
                <button data-toggle="modal" data-target="#addNewImport" class=" btn btn-dark pull-right ml10"><i class="fa fa-download"></i> Import Course By OldID</button>
                <button data-type="multiple" data-url="<?= SITE_URL?>ajax?c=import&a=nsfa_courses" class="importJson btn btn-dark pull-right ml10"><i class="fa fa-download"></i> Import Courses</button>
                <a href="#" data-toggle="modal" data-target="#addNew" class="btn btn-success pull-right">
                    Add New
                </a>
                <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=export-course-titles" target="_blank" class="btn btn-warning pull-right" style="margin-right:5px;">
                    Export Course Titles (CSV)
                </a>
            </div>
            <div class="panel-body pn">
                <div id="returnStatusAddNew"></div>
                <div class="table-responsive">

                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th width="20">Title</th>
                            <th width="30">Categories</th>
                            <th width="10">Enrollments</th>
                            <th width="10">Has Videos?</th>
                            <th width="10">Has Audio?</th>
                            <th width="5">Rating</th>
                            <th width="5">Rating Count</th>
                            <th width="5">Created</th>
                            <th width="5">Actions</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th width="20">Title</th>
                            <th width="30">Categories</th>
                            <th width="10">Enrollments</th>
                            <th width="10">Has Videos?</th>
                            <th width="10">Has Audio?</th>
                            <th width="5">Rating</th>
                            <th width="5">Rating Count</th>
                            <th width="5">Created</th>
                            <th width="5">Actions</th>
                        </tr>
                        </tfoot>
                    </table>

                    <script type="text/javascript">
                        function handleClick(cb, type, id) {
                            jQuery('#returnStatusAddNew').html("");
                            var val = 0;
                            if(cb.checked){
                                val = 1;
                            }
                            jQuery.ajax({
                                url: "<?= SITE_URL ?>ajax?c=course&a=updateColumn",
                                type: "POST",
                                data: {
                                    id: id,
                                    column: type,
                                    value: val,
                                },
                                async: true,
                                success: function (msg) {
                                    jQuery('#returnStatusAddNew').html(msg);
                                },
                            });
                        }
                        $(document).ready(function() {
                            $('.datatable').DataTable( {
                                "processing": true,
                                "serverSide": true,
                                "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                                "ajax": {
                                    "type" : "GET",
                                    "url" : "<?= SITE_URL ?>blume/datatables/courses",
                                    "dataSrc": function ( json ) {

                                        return json.data;

                                    }
                                },
                                // 'columnDefs': [{
                                //     'targets': 3,
                                //     'searchable':false,
                                //     'className': 'dt-body-center',
                                //     'render': function (data, type, full, meta){
                                //         return '<input type="checkbox" name="id[]" value="'
                                //             + $('<div/>').text(data).html() + '">';
                                //     }
                                // }],
                                "drawCallback": function( settings ) {

                                }
                            } );

                        } );

                        function deleteItem(x) {
                            var result = confirm("Are you sure you want to delete this? This action can not be undone");

                            if (result==true) {
                                $(".deleteAccountReturn").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-course&id="+x);

                                $("#item" + x).fadeOut("slow");
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
<div id="deleteProduct"></div>
<script type="text/javascript">
    function deleteItem(id) {
        if (window.confirm("Are you sure you want to delete this course? \nThis action will permanently delete this course from the site and cannot be undone.")) {
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-course&id="+id);
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
                        <label>Course Name</label>
                        <input type="text" name="title" class="form-control" required/>
                    </div>
                    <div class="form-group">
                        <label>Price (GBP)</label>
                        <input type="text" name="price" value="100.00" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" value="Online" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Duration (hours)</label>
                        <input type="text" name="duration" placeholder="0" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select class="form-control select2" name="categories[]" multiple>
                            <?php
                            foreach($this->getCourseCategories() as $category) {
                            ?>
                            <option value="<?= $category->id  ?>"><?= $category->title?>

                                <?php
                                }
                                ?>
                        </select>
                    </div>
                    <p><em>Slug/URL is generated automatically.</em></p>
                    <p><em>You will be redirected to add content to this course.</em></p>
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
<div class="modal fade" id="addNewImport" tabindex="-1" role="dialog" aria-labelledby="addNewImport">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Import By ID</h4>
            </div>
            <form name="addNewImportItem">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Old ID</label>
                        <input type="text" id="course_old_id" name="course_old_id" class="form-control" required/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src='<?= SITE_URL ?>assets/blume/js/plugins/select2/select2.min.js'></script>
<script type="text/javascript">
    $('.select2').select2();

    jQuery("form[name='addNewItem']").submit(function(e) {
        e.preventDefault();
        // $('textarea[name="text"]').html($('.summernote').code());
        var formData = new FormData($(this)[0]);
        // alert(formData);
        // return false;

        jQuery.ajax({
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=create-course",
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
    jQuery("form[name='addNewImportItem']").submit(function(e) {
        e.preventDefault();
        // $('textarea[name="text"]').html($('.summernote').code());
        //var formData = new FormData($(this)[0]);
        // alert(formData);
        // return false;
        var course_old_id = $("#course_old_id").val();
        $("#importingModal").modal();
        jQuery.ajax({
            url: "<?= SITE_URL ?>ajax?c=import&a=courses&course_old_id="+course_old_id,
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
        return false;
    });
</script>

<?php include_once(__DIR__ . '/edit/includes/js/copy-course-js.php'); ?>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
