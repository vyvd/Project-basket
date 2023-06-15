<?php
$css = array("course-category.css");

$breadcrumb = array(
    "Learn Zone" => SITE_URL.'support',
    "Help Articles" => ""
);

$pageTitle = "Help Articles";
include BASE_PATH . 'header.php';
?>
    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--page title-->
        <section class="course-title course-filters">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-6">
                        <h1 class="section-title text-left">Help Articles</h1>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control text-left" placeholder="Search" aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--Page Content-->
        <section class="help-modules">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-md-12 col-lg-8">

                        <?php
                        foreach($this->controller->latestHelpArticles() as $article) {
                            ?>
                            <div class="queries">
                                <a href="<?= SITE_URL ?>support/help-articles/<?= $article->slug ?>">
                                    <i class="far fa-file-alt"></i>
                                    <span><?= $article->title ?></span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="col-md-12 col-lg-4">
                        <div class="white-box help-faq">
                            <h3>FAQ's</h3>
                            <div id="accordion">
                                <?php
                                foreach(ORM::for_table("supportFaqs")->find_many() as $item) {
                                    ?>
                                    <div class="card grey-bg-box">
                                        <div class="card-header" id="module<?= $item->id ?>">
                                            <h5 class="mb-0">
                                                <a class="faq-title collapsed" data-toggle="collapse" data-target="#module<?= $item->id ?>Data" aria-expanded="false" aria-controls="module<?= $item->id ?>Data">
                                                    <?= $item->question ?>
                                                </a>
                                            </h5>
                                        </div>

                                        <div id="module<?= $item->id ?>Data" class="collapse" aria-labelledby="module<?= $item->id ?>" data-parent="#accordion">
                                            <div class="card-body">
                                                <?= $item->answer ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
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