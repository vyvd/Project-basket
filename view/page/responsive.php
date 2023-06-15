<?php
$this->setControllers(array("courseCategory"));
$page = $this->controller->getSinglePage();

$css = array("staff-training.css", "single-page.css");

$breadcrumb = array(
    $page->title => ""
);

$pageTitle = $page->title;
if($page->hideNewsletterModal == "1") {
    $hideNewsletterModal = true;
}
include BASE_PATH . 'header.php';
?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <?php
        if($page->seoPage == "1") {
            ?>

            <!--page title-->
            <section class="course-title staticPage">
                <div class="container wider-container">
                    <div class="row">
                        <div class="col-12">
                            <h1 class="section-title text-left"><?= $page->title ?></h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="staff-training staticPage">
                <div class="container wider-container">
                    <div class="row">

                        <div class="col-12 staff-texts extra-radius">
                            <?= $page->topContent ?>
                        </div>

                    </div>
                </div>
            </section>

            <!-- Course Categories Slider-->
            <div class="container wider-container">

                <div class="home-slider padded-sec">
                    <h1 class="section-title">Course Categories</h1>
                    <div id="courseCategorySlider" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">

                            <div class="carousel-item active">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-md-12">
                                        <div class="category-box-sub">
                                            <?php
                                            $categories = explode(",", $page->categories);
                                            foreach($categories as $catID) {
                                                $category = ORM::For_table("courseCategories")->find_one($catID);
                                                ?>
                                                <div class="category-box">
                                                    <a href="<?= SITE_URL ?>courses/<?= $category->slug ?>">
                                                        <img src="<?= $this->courseCategory->getCategoryImage($category->id, "medium") ?>" alt="fashion" />
                                                        <div class="category-title"><?= $category->title ?></div>
                                                        <span class="hover">
                                                            <?= ORM::for_table("courseCategoryIDs")->where("category_id", $category->id)->count() ?> courses
                                                        </span>
                                                    </a>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="all-course-btn"><button type="button" class="btn btn-outline-primary btn-lg extra-radius" onclick="parent.location='<?= SITE_URL ?>courses'">View All Courses</button></div>

                </div>

            </div>
            <!-- Course Categories Slider End-->

            <section class="staff-training staticPage">
                <div class="container wider-container">
                    <div class="row">

                        <div class="col-12 staff-texts extra-radius">
                            <script type='text/javascript' src='//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js?ver=5.4'></script>
                            <!-- TrustBox widget - Carousel -->
                            <div class="trustpilot-widget" data-locale="en-GB" data-template-id="53aa8912dec7e10d38f59f36" data-businessunit-id="5b450fa2ad92290001bfac20" data-style-height="160px" data-style-width="100%" data-theme="light" data-stars="5" data-schema-type="Organization">
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <section class="staff-training staticPage">
                <div class="container wider-container">
                    <div class="row">

                        <div class="col-12 staff-texts extra-radius">
                            <?= $page->contents ?>
                        </div>

                    </div>
                </div>
            </section>

            <?php

        } else {

            ?>
            <?php
            if($page->hideTitle == "0") {
                ?>
                <!--page title-->
                <section class="course-title staticPage" <?php if($page->width == "full") { ?>style="max-width:1200px;"<?php } ?>>
                    <div class="container wider-container">
                        <div class="row">
                            <div class="col-12">
                                <h1 class="section-title text-left"><?= $page->title ?></h1>
                            </div>
                        </div>
                    </div>
                </section>
                <?php
            } else {
                ?>
                <br />
                <br />
                <br />
                <?php
            }
            ?>
            <!--Page Content-->
            <section class="staff-training staticPage" <?php if($page->width == "full") { ?>style="max-width:1200px;"<?php } ?>>
                <div class="container wider-container">
                    <div class="row">

                        <div class="col-12 staff-texts extra-radius">
                            <?= $page->contents ?>
                        </div>

                    </div>
                </div>
            </section>

            <?php
            if($page->showRedeem == "1") {
                ?>
                <!--Page Content-->
                <section class="staff-training staticPage" <?php if($page->width == "full") { ?>style="max-width:1200px;"<?php } ?>>
                    <div class="container wider-container">
                        <div class="row">

                            <div class="col-12 col-md-12 col-lg-12 staff-form">
                                <form name="redeem">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Voucher Code" name="code">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="First Name" name="firstname">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Last Name" name="lastname">
                                    </div>
                                    <div class="form-group">
                                        <input type="email" class="form-control" placeholder="Email Address" name="email">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control" placeholder="Choose a Password" name="password">
                                    </div>
                                    <div class="form-group text-center">
                                        <input type="submit" value="Redeem Voucher" class="btn btn-white btn-lg extra-radius" />
                                    </div>
                                </form>
                                <?php
                                $this->renderFormAjax("redeem", "redeem-voucher", "redeem");
                                ?>
                            </div>
                        </div>
                    </div>
                </section>
                <?php
            }
            ?>

            <?php

        }
        ?>

        <script type="text/javascript" src="https://newskillsacademy.co.uk/affiliates/assets/js/iframeResizer.min.js"></script>
        <script type="text/javascript">iFrameResize({log : false, enablePublicMethods: true, heightCalculationMethod: "lowestElement" });</script>

        <?php include BASE_PATH . 'learn-confidence.php'; ?>

        <?php include BASE_PATH . 'newsletter.php'; ?>

        <?php include BASE_PATH . 'featured.php'; ?>

        <?php include BASE_PATH . 'success-stories.php'; ?>

    </main>
    <!-- Main Content End -->

<?php include BASE_PATH . 'footer.php';?>