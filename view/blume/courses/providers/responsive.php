<?php
$this->setControllers(array("courseProviders"));
$metaTitle = "Course Providers";
include BASE_PATH . 'blume.header.base.php';
?>
    <link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/select2/css/core.css">
    <style>
        .select2.select2-container {
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
                    <span class="panel-title">Course Providers</span>
                    <a href="#" data-toggle="modal" data-target="#addNew" class="btn btn-success pull-right">
                        Add New
                    </a>
                </div>
                <div class="panel-menu">
                    <input id="fooFilter" type="text" class="form-control" placeholder="Search...">
                </div>
                <div class="panel-body pn">
                    <div class="table-responsive">
                        <table class="table footable" data-filter="#fooFilter" data-page-navigation=".pagination"
                               data-page-size="50">
                            <thead>
                            <tr>
                                <th>Name</th>
<!--                                <th>Slug</th>-->
                                <th>Logo</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $items = ORM::for_table("courseProviders")->order_by_asc("name")->find_many();

                            foreach ($items as $item) {
                                $courses = count(ORM::for_table("courseCategoryIDs")->where("category_id",
                                    $item->id)->find_many());
                                ?>
                                <tr id="item<?= $item->id ?>">
                                    <td>
                                        <?= $item->name ?>
                                    </td>

                                    <td>
                                        <?php
                                        $imageUrl = $this->courseProviders->getProviderImage($item->id);
                                        if(@$imageUrl) {
                                            ?>
                                            <img class="mt-2" width="100" src="<?= $imageUrl;?>" />
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?= SITE_URL ?>blume/courses/providers/edit?id=<?= $item->id ?>"
                                           class="label label-warning">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <label class="label label-danger" onclick="deleteItem(<?= $item->id ?>);"
                                               style="margin-left:5px;cursor:pointer;">
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
                $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-course-providers&id=" + id);
                $("#item" + id).fadeOut();
            }
        }
    </script>
    <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Add New</h4>
                </div>
                <form name="addNewItem" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Upload Logo</label>
                            <input type="file" class="form-control" name="uploaded_file">
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

    <script src='<?= SITE_URL ?>assets/blume/js/plugins/select2/select2.min.js'></script>
    <script type="text/javascript">
        $('.select2').select2();

        jQuery("form[name='addNewItem']").submit(function (e) {
            e.preventDefault();
            // $('textarea[name="text"]').html($('.summernote').code());
            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "<?= SITE_URL ?>ajax?c=blumeNew&a=add-course-providers",
                type: "POST",
                data: formData,
                async: true,
                success: function (msg) {
                    jQuery('#returnStatusAddNew').html(msg);
                    //$(this).reset();
                    return false;
                },
                cache: false,
                contentType: false,
                processData: false,
            });
            return false;
        });
    </script>

    <!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>