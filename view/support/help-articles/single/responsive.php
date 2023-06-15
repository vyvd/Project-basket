<?php
$css = array("course-category.css");
$article = $this->controller->getHelpArticle();

$breadcrumb = array(
    "Learn Zone" => SITE_URL.'support',
    "Help Articles" => ""
);

$pageTitle = $article->title;
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
                    <div class="col-md-12 col-lg-4 order-2 order-md-1">
                        <?php
                        foreach($this->controller->relatedHelpArticles($article) as $item) {
                            ?>
                            <div class="queries">
                                <a href="<?= SITE_URL ?>support/help-articles/<?= $item->slug ?>">
                                    <i class="far fa-file-alt"></i>
                                    <span><?= $item->title ?></span>
                                </a>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="col-md-12 col-lg-8 order-1 order-md-2">
                        <div class="white-box help-faq" style="padding: 26px 30px;">
                            <h3><?= $article->title ?></h3>
                            <?= $article->contents ?>
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