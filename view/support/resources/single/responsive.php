<?php

$article = ORM::for_table("resources")->where("slug", $_GET["request"])->find_one();

if($article->title == "") {
    $this->force404(); // force 404 error as the article does not exist
}

$css = array("course-category.css");

$breadcrumb = array(
    "Support" => SITE_URL.'support',
    "Resources" => SITE_URL.'support/resources'
);

$pageTitle = $article->title;
include BASE_PATH . 'header.php';
?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--course filters-->
        <section class="course-filters">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <h1 class="section-title text-center">Resources</h1>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6">


                    </div>
                </div>
            </div>
        </section>

        <style>
            .blog-texts img {
                max-width: 100%;
                width: auto;
                margin: auto;
            }
        </style>

        <!--courses listing-->
        <section class="courses-listing">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-8 blog-single">
                        <div class="blog-banner">
                            <img src="<?= $this->controller->getResourceImage($article->id) ?>" alt="<?= $article->title ?>" style="width:100%;" />
                            <div class="blog-banner-text overlay">
                                <h1><?= $article->title ?></h1>
                            </div>
                        </div>
                        <div class="blog-texts">
                            <?= $article->contents ?>

                        </div>
                    </div>
                    <div class="col-md-12 col-lg-4">
                        <?php
                        foreach(ORM::for_table("resources")->find_many() as $item) {
                            ?>
                            <div class="queries">
                                <a href="<?= SITE_URL ?>support/resources/<?= $item->slug ?>">
                                    <i class="far fa-file-alt"></i>
                                    <span><?= $item->title ?></span>
                                </a>
                            </div>
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