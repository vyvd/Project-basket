<?php

$metaTitle = "Customer Service Portal";
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
                    <span class="panel-title">Customer Service Portal</span>

                </div>

                <br />

                <div class="tab-block">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#tab1" data-toggle="tab">General</a>
                        </li>
                        <li>
                            <a href="#tab6" data-toggle="tab">Categories</a>
                        </li>
                        <li>
                            <a href="#tab2" data-toggle="tab">Modules</a>
                        </li>
                        <li>
                            <a href="#tab3" data-toggle="tab">Enrollments</a>
                        </li>
                        <li>
                            <a href="#tab4" data-toggle="tab">Stats</a>
                        </li>
                        <li>
                            <a href="#tab5" data-toggle="tab">Printed Materials</a>
                        </li>
                    </ul>
                    <div class="tab-content p30">
                        <div id="tab1" class="tab-pane active">



                        </div>
                        <div id="tab2" class="tab-pane">

                        </div>
                        <div id="tab3" class="tab-pane">



                        </div>
                        <div id="tab4" class="tab-pane">



                        </div>
                        <div id="tab5" class="tab-pane">



                        </div>
                        <div id="tab6" class="tab-pane">

                            <h4>Select which category, or categories, this course is part of:</h4>

                            <form name="editCategories">
                                <div class="row">
                                    <?php
                                    foreach($this->getCourseCategories() as $category) {

                                        ?>
                                        <div class="col-xs-6">
                                            <label>
                                                <input type="checkbox" name="categories[]" <?php if($this->course->checkCourseCategory($course->id, $category->id) == true) { ?>checked<?php } ?> value="<?= $category->id ?>" />
                                                <?= $category->title ?>
                                            </label>
                                        </div>
                                        <?php

                                    }
                                    ?>
                                </div>

                                <input type="hidden" name="courseID" value="<?= $course->id ?>" />

                                <input type="submit" class="btn btn-success" value="Update" />

                                <div id="returnStatusCats"></div>

                            </form>
                            <script type="text/javascript">
                                jQuery("form[name='editCategories']").submit(function(e) {
                                    e.preventDefault();
                                    // $('textarea[name="text"]').html($('.summernote').code());
                                    var formData = new FormData($(this)[0]);

                                    jQuery.ajax({
                                        url: "<?= SITE_URL ?>ajax?c=blumeNew&a=save-course-categories",
                                        type: "POST",
                                        data: formData,
                                        async: true,
                                        success: function (msg) {
                                            jQuery('#returnStatusCats').html(msg);
                                        },
                                        cache: false,
                                        contentType: false,
                                        processData: false
                                    });
                                });
                            </script>


                        </div>
                    </div>
                </div>
            </div>


            <!-- -------------- DEMO Break -------------- -->
            <div class="mv40"></div>


        </div>
        <!-- -------------- /Column Center -------------- -->

    </section>
    <!-- -------------- /Content -------------- -->
    <div class="deleteAccountReturn"></div>



<?php include BASE_PATH . 'blume.footer.base.php'; ?>