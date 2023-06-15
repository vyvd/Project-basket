<?php

// auto assign courses
$items = ORM::for_table("courseReviews")->where_null("courseID")->find_many();

foreach($items as $item) {

    $course = ORM::for_table("courses")->where_like("title", "%".$item->course."%")->find_one();

    if($course->id != "") {

        $update = ORM::for_table("courseReviews")->find_one($item->id);

        $update->courseID = $course->id;

        $update->save();


    }

}

$metaTitle = "Course Reviews";
include BASE_PATH . 'blume.header.base.php';
?>
<!-- -------------- Content -------------- -->
<section id="content" class="table-layout animated fadeIn">


    <!-- -------------- /Column Left -------------- -->

    <!-- -------------- Column Center -------------- -->
    <div class="chute chute-center">

        <div id="returnStatus2"></div>

        <!-- -------------- Data Filter -------------- -->
        <div class="panel" id="spy2">
            <div class="panel-heading">
                <span class="panel-title">Course Reviews</span>
                <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=export-course-reviews-csv" class="btn btn-warning pull-right">
                    <i class="fa fa-download"></i>
                    Export CSV
                </a>

                <a href="javascript:;" data-toggle="modal" data-target="#addReview" class="btn btn-success pull-right">
                    <i class="fa fa-plus"></i>
                    Add Review
                </a>
            </div>

            <div class="panel-body pn">
                <div class="table-responsive">

                    <form name="ajaxAction">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Course</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Rating</th>
                                <th>Added</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Course</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Rating</th>
                                <th>Added</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </tfoot>
                        </table>

                        <input type="checkbox" name="select-all" id="select-all" /> Select all

                        <hr />

                        <p>With selected:</p>

                        <select class="form-control" name="status">
                            <option value="a">Approved</option>
                            <option value="r">Rejected</option>
                            <option value="p">Pending</option>
                        </select>
                        <br />

                        <button class="btn btn-success btnGo" type="submit">Go</button>
                    </form>

                </div>
            </div>
        </div>

        <script type="text/javascript">
            jQuery("form[name='ajaxAction']").submit(function(e) {
                e.preventDefault();

                var formData = new FormData($(this)[0]);

                $(".btnGo").html('<i class="fa fa-spin fa-spinner"></i>');

                jQuery.ajax({
                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=action-course-reviews",
                    type: "POST",
                    data: formData,
                    async: true,
                    success: function (msg) {
                        jQuery('#returnStatus2').html(msg);
                        $(".btnGo").html('Go');
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            });
        </script>


        <!-- -------------- DEMO Break -------------- -->
        <div class="mv40"></div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>
<div id="deleteProduct"></div>
<script type="text/javascript">
    function deleteItem(id) {
        if (window.confirm("Are you sure you want to delete this course review permanently?")) {
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-course-review&id="+id);
            window.location.reload();
            $("#item"+id).fadeOut();
        }
    }
</script>

<div class="modal fade" id="addReview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Review</h4>
            </div>
            <form name="addReview">
                <div class="modal-body">
                    <p>This review will be automatically approved.</p>
                    <div class="form-group">
                        <label>Assign Course</label>
                        <select class="form-control" name="courseID">
                            <option value="">Please select...</option>
                            <?php
                            foreach(ORM::for_table("courses")->order_by_asc("title")->find_many() as $course) {
                                ?>
                                <option value="<?= $course->id ?>"><?= $course->title ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Full Review</label>
                        <textarea name="comments" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Firstname</label>
                        <input type="text" name="firstname" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Lastname</label>
                        <input type="text" name="lastname" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="uploaded_file" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Rating</label>
                        <select class="form-control" name="rating">
                            <option>5</option>
                            <option>4</option>
                            <option>3</option>
                            <option>2</option>
                            <option>1</option>
                        </select>
                    </div>
                    <p><em>This review will only show on the website</em></p>
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
        $('.datatable').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": {
                "type" : "GET",
                "url" : "<?= SITE_URL ?>blume/datatables/reviews",
                "dataSrc": function ( json ) {

                    return json.data;

                }
            },
            "order": [[ 5, "desc" ]],
            "drawCallback": function( settings ) {
                $( ".editItem" ).click(function() {
                    var id = $(this).data("edit");
                    $("#edit").modal("toggle");
                    $("#ajaxEdit").load("<?= SITE_URL ?>ajax?c=blumeNew&a=edit-review-form&id="+id);
                });

                $( "#select-all" ).click(function() {
                    if(this.checked) {

                        $(':checkbox').each(function() {
                            this.checked = true;
                        });
                    } else {
                        $(':checkbox').each(function() {
                            this.checked = false;
                        });
                    }
                });
            }
        } );
    } );

    jQuery("form[name='addReview']").submit(function(e) {
        e.preventDefault();
        // $('textarea[name="text"]').html($('.summernote').code());
        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=add-course-review",
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
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Manage Review</h4>
            </div>
            <div id="ajaxEdit">

            </div>
        </div>
    </div>
</div>



<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
