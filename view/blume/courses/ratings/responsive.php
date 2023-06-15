<?php

$metaTitle = "Course Ratings";
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
                <span class="panel-title">Course Ratings</span>

                <a href="javascript:;" data-toggle="modal" data-target="#summary" class="btn btn-system pull-right">
                    View Summary
                </a>

            </div>
            <div class="panel-menu">
                <input id="fooFilter" type="text" class="form-control"
                       placeholder="Search...">
            </div>
            <div class="panel-body pn">
                <div class="table-responsive">

                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th>Course</th>
                            <th>Name</th>
                            <th>Rating</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Course</th>
                            <th>Name</th>
                            <th>Rating</th>
                            <th>Added</th>
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
                                    "url" : "<?= SITE_URL ?>blume/datatables/courses/ratings",
                                    "dataSrc": function ( json ) {

                                        return json.data;

                                    }
                                },
                                "drawCallback": function( settings ) {

                                }
                            } );

                        } );


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
        if (window.confirm("Are you sure you want to delete this course rating permanently?")) {
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-course-rating&id="+id);
            $("#item"+id).fadeOut();
        }
    }
</script>

<div class="modal fade" id="summary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Rating Summary</h4>
            </div>

            <div class="modal-body">

                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Course</th>
                        <th scope="col">Avg. Rating</th>
                        <th scope="col">Total Ratings</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach(ORM::for_table("courses")->order_by_desc("averageRating")->find_many() as $course) {
                        ?>
                        <tr>
                            <th scope="row"><?= $course->title ?></th>
                            <td><?= $course->averageRating ?></td>
                            <td><?= ORM::for_table("courseRatings")->where("courseID", $course->id)->count() ?></td>
                        </tr>
                        <?php
                    }
                    ?>

                    </tbody>
                </table>

            </div>

        </div>
    </div>
</div>


<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
