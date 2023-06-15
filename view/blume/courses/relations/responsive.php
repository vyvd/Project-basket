<?php

$metaTitle = "Course Relations";
include BASE_PATH . 'blume.header.base.php';
?>
<link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/select2/css/core.css"
      xmlns="http://www.w3.org/1999/html">
<style>
    .select2.select2-container{
        width: 100% !important;
    }
    .subcategory{
        margin-left: 15px;
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
                <span class="panel-title">Course Relations</span>
                <a href="#" data-toggle="modal" data-target="#addNew" class="btn btn-success pull-right">
                    Add New
                </a>
                <p>Use this tool to identify identical (or very similar) courses. These will then be automatically matched up on the frontend to generate the appropriate hreflang tags.</p>
            </div>
            <div class="panel-menu">
                <input id="fooFilter" type="text" class="form-control"
                       placeholder="Search...">
            </div>
            <div class="panel-body pn">
                <div class="table-responsive">
                    <table class="table footable" data-filter="#fooFilter" data-page-navigation=".pagination" data-page-size="50">
                        <thead>
                        <tr>
                            <th>Course 1</th>
                            <th>Course 2</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $items = ORM::for_table("courseRelations")->order_by_asc("id")->find_many();

                        foreach($items as $item) {
                            $course = ORM::for_table("courses")->find_one($item->courseID);
                            $course2 = ORM::for_table("courses")->find_one($item->courseID2);
                            ?>
                            <tr id="item<?= $item->id ?>">
                                <td>
                                    <?= $course->title ?>
                                </td>
                                <td>
                                    <?= $course2->title ?>
                                </td>
                                <td>
                                    <label class="label label-danger" onclick="deleteItem(<?= $item->id ?>);" style="margin-left:5px;cursor:pointer;">
                                        <i class="fa fa-times"></i>
                                    </label>
                                </td>
                            </tr>

                            <?php
                        }
                        ?>
                        </tbody>
                        <tfoot class="footer-menu">
                        <tr>
                            <td colspan="7">
                                <nav class="text-right">
                                    <ul class="pagination hide-if-no-paging"></ul>
                                </nav>
                            </td>
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
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-course-relation&id="+id);
            $("#item"+id).fadeOut();
        }
    }
</script>
<div class="modal fade" id="addNew" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add New</h4>
            </div>
            <form name="addNewItem">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Course 1</label>
                        <select class="form-control select2" name="courseID">
                            <?php
                            foreach($this->getAllCourses() as $course) {
                                ?>
                                <option value="<?= $course->id ?>"><?= $course->title ?> <?php if($course->usImport == "1") { ?>(US)<?php } ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Course 2</label>
                        <select class="form-control select2" name="courseID2">
                            <?php
                            foreach($this->getAllCourses() as $course) {
                                ?>
                                <option value="<?= $course->id ?>"><?= $course->title ?> <?php if($course->usImport == "1") { ?>(US)<?php } ?></option>
                                <?php
                            }
                            ?>
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

<script type="text/javascript">
    jQuery("form[name='addNewItem']").submit(function(e) {
        e.preventDefault();
        // $('textarea[name="text"]').html($('.summernote').code());
        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=add-course-relation",
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

<script src='<?= SITE_URL ?>assets/blume/js/plugins/select2/select2.min.js'></script>
<script type="text/javascript">
    $('.select2').select2();
</script>

<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
