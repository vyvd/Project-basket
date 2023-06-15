<?php
$css = array("course-category.css");
$pageTitle = "Resources";
include BASE_PATH . 'header.php';
?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--course filters-->
        <section class="course-filters">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-6">
                        <h1 class="section-title text-left">Resources</h1>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6">
                        <!--<div class="input-group mb-3">
                            <input type="text" class="form-control text-left" placeholder="Search" aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search" aria-hidden="true"></i></button>
                            </div>
                        </div>-->
                    </div>
                </div>
            </div>
        </section>

        <!--courses listing-->
        <section class="courses-listing">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12 popularCourseboxes">
                        <div class="row">
                            <?php
                            foreach(ORM::for_table("resources")->order_by_desc("id")->find_many() as $resource) {
                                ?>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="category-box resourse-box">
                                        <a href="<?= SITE_URL ?>support/resources/<?= $resource->slug ?>">
                                            <img src="<?= $this->controller->getResourceImage($resource->id, "medium") ?>" alt="<?= $resource->title ?>" />
                                        </a>
                                        <div class="resources-link"><a href="<?= SITE_URL ?>support/resources/<?= $resource->slug ?>"><?= $resource->title ?></a></div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>


                        </div>
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