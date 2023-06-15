<?php

$metaTitle = "Email Templates";
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
                <span class="panel-title">Email Templates</span>
<!--                <a href="#" data-toggle="modal" data-target="#addNew" class="btn btn-success pull-right">-->
<!--                    Add New-->
<!--                </a>-->
            </div>
            <div class="panel-body pn">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th width="20">Title</th>
                            <th width="30">Subject</th>
                            <th width="20">Variables</th>
                            <th width="10">Actions</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th width="20">Title</th>
                            <th width="30">Subject</th>
                            <th width="20">Variables</th>
                            <th width="10">Actions</th>
                        </tr>
                        </tfoot>
                    </table>
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
        if (window.confirm("Are you sure you want to delete this item?")) {
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-testimonial&id="+id);
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
                        <label>Name</label>
                        <input type="text" name="name" placeholder="e.g. Jane Doe" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Course</label>
                        <input type="text" name="course" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Testimonial</label>
                        <textarea class="form-control" name="testimonial" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Vimeo URL</label>
                        <input type="text" name="video" placeholder="Paste here..." class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Where should this review be shown?</label>
                        <select class="form-control" name="location">
                            <option value="f">Footer</option>
                            <option value="c">Single Course Page</option>
                            <option value="p">Testimonials Page</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Upload Image Thumbnail</label>
                        <input type="file" name="uploaded_file" class="form-control" />
                    </div>
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

<script type="text/javascript">
    $(document).ready(function() {
        $('.datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "type": "GET",
                "url": "<?= SITE_URL ?>blume/datatables/content/email-templates",
                "dataSrc": function (json) {

                    return json.data;

                }
            },
            "drawCallback": function (settings) {

            }
        });
    });


    jQuery("form[name='addNewItem']").submit(function(e) {
        e.preventDefault();
        // $('textarea[name="text"]').html($('.summernote').code());
        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=add-testimonial",
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
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
