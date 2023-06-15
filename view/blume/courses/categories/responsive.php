<?php

$this->setControllers(array("courseCategory"));
$metaTitle = "Course Categories";
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
                    <span class="panel-title">Course Categories</span>
                    <a href="#" data-toggle="modal" data-target="#addNew" class="btn btn-success pull-right">
                        Add New
                    </a>
                    <a href="#" data-toggle="modal" data-target="#categoryImport" class="btn btn-success pull-right"
                       style="margin-right: 10px;display: none">
                        Category Import
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
                                <th>Image</th>
                                <th>Title</th>
                                <th>Home?</th>
                                <th>Courses</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $items = ORM::for_table("courseCategories")->order_by_asc("title")->find_many();

                            foreach ($items as $item) {
                                $courses = count(
                                    ORM::for_table("courseCategoryIDs")->where("category_id", $item->id)->find_many()
                                );
                                ?>
                                <tr id="item<?= $item->id ?>">
                                    <td>
                                        <img src="<?= $this->courseCategory->getCategoryImage($item->id, "thumb") ?>"
                                             width="100" height="60"/>
                                    </td>
                                    <td>
                                        <?= $item->title ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($item->showOnHome == "1") {
                                            ?>
                                            <label class="label label-success">Yes</label>
                                            <?php
                                        } else {
                                            ?>
                                            <label class="label label-danger">No</label>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?= $courses ?>
                                    </td>
                                    <td>
                                        <a href="<?= SITE_URL ?>blume/courses/categories/edit?id=<?= $item->id ?>"
                                           class="label label-warning">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <?php
                                        if ($courses == 0) {
                                            ?>
                                            <label class="label label-danger" onclick="deleteItem(<?= $item->id ?>);"
                                                   style="margin-left:5px;cursor:pointer;">
                                                <i class="fa fa-times"></i>
                                            </label>
                                            <?php
                                        }
                                        ?>
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
                $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-course-category&id=" + id);
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
                <form name="addNewItem">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control"/>
                        </div>

                        <div class="form-group">
                            <label>Parents Category</label>
                            <select class="form-control select2" name="parentID">
                                <option value="">Select Category</option>
                                <?php
                                foreach ($this->getCourseCategories() as $category) {
                                ?>
                                <option value="<?= $category->id ?>"><?= $category->title ?>
                                    <?php
                                    }
                                    ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Icon (FontAwesome)</label>
                            <input type="text" name="icon" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" name="meta_title" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea name="meta_description" class="form-control" id="" cols="30"></textarea>
                        </div>
                        <p><em>Slug/URL is generated automatically.</em></p>
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
                url: "<?= SITE_URL ?>ajax?c=blumeNew&a=add-course-category",
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

    <div class="modal fade" id="categoryImport" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Import Categories</h4>
                </div>
                <form name="categoryImportForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Import Source</label>
                            <select class="form-control select2" name="import_source">
                                <option value="">Select Import Source</option>
                                <option value="google_sheet">Google Sheet</option>
                                <option value="file_upload">File Upload</option>
                            </select>
                        </div>

                        <div class="form-group categoryImportForm--google-sheet" style="display: none">
                            <label>Google Sheet Url</label>
                            <input type="text" name="google_sheet_url" class="form-control"/>
                        </div>
                        <div class="form-group categoryImportForm--file-upload" style="display: none">
                            <label>Select csv</label>
                            <input type="file" name="csv_file" class="form-control"/>
                        </div>
                        <div class="categoryImportResults"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function buildCategoryImportUl(sub_ul, data) {
            var sub_li = $('<li/>').html(data);
            sub_ul.append(sub_li);
            return sub_ul;
        }

        function buildCategoryImportResults(data) {
            var sub_ul = $('<ul/>').css('list-style', 'none');
            $.each(data, function (index, value) {
                if (value.hasOwnProperty('category_name') && value.hasOwnProperty('sub_categories')) {
                    sub_ul = buildCategoryImportUl(
                        sub_ul,
                        value.category_name
                    )
                    if (value.hasOwnProperty('sub_categories') && Array.isArray(value.sub_categories)) {
                        sub_ul = buildCategoryImportUl(
                            sub_ul,
                            buildCategoryImportResults(value.sub_categories)
                        )
                    }
                    if (value.hasOwnProperty('errors') && Array.isArray(value.errors)) {
                        sub_ul = buildCategoryImportUl(
                            sub_ul,
                            buildCategoryImportResults(value.errors)
                        )
                    }
                } else if (value.hasOwnProperty('category_name')) {
                    sub_ul = buildCategoryImportUl(
                        sub_ul,
                        value.category_name
                    )
                    if (value.hasOwnProperty('errors') && Array.isArray(value.errors)) {
                        sub_ul = buildCategoryImportUl(
                            sub_ul,
                            buildCategoryImportResults(value.errors)
                        )
                    }
                } else if (value.hasOwnProperty('message')) {
                    sub_ul = buildCategoryImportUl(
                        sub_ul,
                        `${value.message}: ${value.hasOwnProperty('course_name') ? value.course_name : ''}`
                    )
                }
            });
            return sub_ul;
        }

        function importCourseCategoriesCsvAjax() {
            const resultsWrapper = jQuery(this).find('.categoryImportResults');
            var formData = new FormData($(this)[0]);
            jQuery.ajax({
                url: "<?= SITE_URL ?>ajax?c=courseCategory&a=import-course-categories",
                type: "POST",
                data: formData,
                async: true,
                success: function (msg) {
                    let parseResponse = JSON.parse(msg);
                    var errorTitle = $('<h5/>').html('Import Errors');
                    if (typeof parseResponse !== 'object') {
                        jQuery(resultsWrapper).html(buildCategoryImportResults(['There was an error'])).fadeIn();
                        return;
                    }
                    if (!parseResponse.hasOwnProperty('success')) {
                        jQuery(resultsWrapper).html(buildCategoryImportResults(['There was a error'])).fadeIn();
                        return;
                    }
                    if (!parseResponse.success && parseResponse.hasOwnProperty('message')) {
                        jQuery(resultsWrapper).append(errorTitle);
                        jQuery(resultsWrapper).append(buildCategoryImportResults([{message: parseResponse.message}])).fadeIn();
                    }
                    if (parseResponse.success) {
                        if (parseResponse.hasOwnProperty('errors') && Array.isArray(parseResponse.errors)) {
                            jQuery(resultsWrapper).append(errorTitle);
                            jQuery(resultsWrapper).append(buildCategoryImportResults(parseResponse.errors)).fadeIn();
                        } else {
                            jQuery(resultsWrapper).html(buildCategoryImportResults(['Success'])).fadeIn();
                        }
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        }

        jQuery(document).on('change', 'form[name="categoryImportForm"] select[name="import_source"]', function (e) {
            e.preventDefault();
            const googleSheetWrapperClass = '.categoryImportForm--google-sheet';
            const fileUploadWrapperClass = '.categoryImportForm--file-upload';
            const form = jQuery(this).parents('form[name="categoryImportForm"]');
            const googleSheetWrapper = jQuery(form).find(googleSheetWrapperClass);
            const fileUploadWrapper = jQuery(form).find(fileUploadWrapperClass);
            let selectVal = jQuery(this).val();
            switch (selectVal) {
                case 'google_sheet':
                    jQuery(googleSheetWrapper).fadeIn();
                    jQuery(fileUploadWrapper).hide();
                    break;
                case 'file_upload':
                    jQuery(fileUploadWrapper).fadeIn();
                    jQuery(googleSheetWrapper).hide();
                    break;
            }
        })
        jQuery("form[name='categoryImportForm']").submit(function (e) {
            e.preventDefault();
            const resultsWrapper = jQuery(e).find('.categoryImportResults');
            let selectVal = jQuery(this).find('select[name="import_source"]').val();
            switch (selectVal) {
                case 'google_sheet':
                    googleApiAuth(
                        'google-sheets-authenticator',
                        importCourseCategoriesCsvAjax.bind(this),
                        function (errorMessage) {
                            jQuery(resultsWrapper).html(buildCategoryImportResults([errorMessage])).fadeIn();
                        }
                    )
                    break;
                case 'file_upload':
                    importCourseCategoriesCsvAjax.bind(this)()
                    break;
            }
        });
    </script>

    <!-- -------------- /Content -------------- -->
<?php
include BASE_PATH . 'blume.footer.base.php'; ?>