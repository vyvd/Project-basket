<?php
$this->get["search"] = htmlspecialchars($this->get["search"]);

$css = array("course-category.css");
$pageTitle = "Searching for ".$this->get["search"];
include BASE_PATH . 'header.php';
?>
    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--page title-->
        <section class="course-title course-filters">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-6">
                        <h1 class="section-title text-left">Search Results</h1>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6">
                        <form action="<?= SITE_URL ?>search" method="get">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control text-left" name="search" placeholder="Search" value="<?= $this->get["search"] ?>" aria-label="Search" aria-describedby="basic-addon2" style="border-top-right-radius:0px;border-bottom-right-radius:0px;">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <script>
            dataLayer.push({
                'event':'search',
                'term': '<?= $this->get["search"] ?>'
            });
        </script>

        <!--Page Content-->
        <section class="search-results">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-md-12">

                        <div class="row">
                            <?php
                            $courses = $this->controller->getCoursesSearch();

                            //echo ORM::get_last_query();

                            $existingIDs = array();

                            foreach($courses as $course) {

                                if($course->hidden == "0" && $course->is_lightningSkill == "0") {
                                    ?>
                                    <div class="col-md-6">
                                        <div class="results-box d-flex align-items-center" onclick="parent.location='<?= SITE_URL ?>course/<?= $course->slug ?>'">
                                            <img src="<?= $this->controller->getCourseImage($course->id, "medium") ?>" alt="nsa" />
                                            <p><?= $course->title ?></p>
                                        </div>
                                    </div>
                                    <?php
                                    array_push($existingIDs, $course->id);


                                }

                                //$this->debug($existingIDs);

                                // see if there are any bundles this belongs to
                                if($currency->code == "GBP") {
                                    $bundles = ORM::for_table("courses") ->where("hidden", "0")->where("is_lightningSkill", "0")->where("usImport", "0")->where_not_equal("childCourses", "")->find_many();
                                } else {
                                    $bundles = ORM::for_table("courses") ->where("hidden", "0")->where("is_lightningSkill", "0")->where("usImport", "1")->where_not_equal("childCourses", "")->find_many();
                                }


                                foreach($bundles as $bundle) {

                                    $children = str_replace("[", "", $bundle->childCourses);
                                    $children = str_replace("[", "", $children);
                                    $children = str_replace('"', "", $children);

                                    $children = explode(",", $children);

                                    if(in_array($course->id, $children) && !in_array($course->id, $existingIDs)) {
                                        ?>
                                        <!--<div class="col-md-6">
                                            <div class="results-box d-flex align-items-center" onclick="parent.location='<?= SITE_URL ?>course/<?= $bundle->slug ?>'">
                                                <img src="<?= $this->controller->getCourseImage($bundle->id, "medium") ?>" alt="nsa" />
                                                <p><?= $bundle->title ?></p>
                                            </div>
                                        </div>-->
                                        <?php
                                    }
                                }

                            }
                            ?>
                        </div>
                        <?php

                        if(count($courses) == 0) {
                            ?>
                            <script>
                                dataLayer.push({
                                    'event':'noResultSearch',
                                    'term': '<?= $this->get["search"] ?>'
                                });
                            </script>
                            <p class="text-center" style="padding:100px 0px;">We could not find any results. Please try broadening your search.</p>
                        <?php
                        } else {
                        ?>
                            <script>
                                dataLayer.push({
                                    'event':'ResultSearch',
                                    'term': '<?= $this->get["search"] ?>'
                                });
                            </script>
                            <?php
                        }
                        ?>

                    </div>
                </div>
            </div>
        </section>

        <?php include BASE_PATH . 'learn-confidence.php'; ?>

        <?php include BASE_PATH . 'newsletter.php'; ?>

        <?php include BASE_PATH . 'featured.php'; ?>

        <?php include BASE_PATH . 'success-stories.php'; ?>

    </main>
    <!-- Main Content End -->

<?php include BASE_PATH . 'footer.php';?>