<?php
$css = array("staff-training.css");

$breadcrumb = array(
        "Learn Zone" => ""
);

$pageTitle = "Support";
include BASE_PATH . 'header.php';
include BASE_PATH . 'support-status-message.php';
?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--page title-->
        <section class="course-title">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="section-title text-left">Learn Zone.</h1>
                    </div>
                </div>
            </div>
        </section>

        <!--Page Content-->
        <section class="staff-training">
            <div class="container wider-container">
                <div class="row align-items-center support-row">

                    <div class="col-12 col-md-6 col-lg-3 text-center">
                        <div class="white-box" onclick="parent.location='<?= SITE_URL ?>contact'">
                            <a href="<?= SITE_URL ?>contact">
                                <img src="<?= SITE_URL ?>assets/images/email-icon.png" alt="contact">
                                <h3>Contact Support</h3>
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3 text-center">
                        <div class="white-box" onclick="parent.location='<?= SITE_URL ?>support/help-articles'">
                            <a href="<?= SITE_URL ?>support/help-articles">
                                <img src="<?= SITE_URL ?>assets/images/question.png" alt="contact">
                                <h3>Help Articles</h3>
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3 text-center">
                        <div class="white-box" onclick="parent.location='<?= SITE_URL ?>blog'">
                            <a href="<?= SITE_URL ?>blog">
                                <img src="<?= SITE_URL ?>assets/images/blog-icon.png" alt="contact">
                                <h3>Blog</h3>
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3 text-center">
                        <div class="white-box" onclick="parent.location='<?= SITE_URL ?>support/resources'">
                            <a href="<?= SITE_URL ?>support/resources">
                                <img src="<?= SITE_URL ?>assets/images/file-icon.png" alt="contact">
                                <h3>Resources</h3>
                            </a>
                        </div>
                    </div>

                </div>
                <div class="row align-items-center support-row">

                    <div class="col-12 col-md-12 col-lg-6">
                        <div class="white-box with-side-img">
                            <img src="<?= SITE_URL ?>assets/images/change-life.jpg" class="supportImgMobile" alt="NSA">
                            <div class="support-cnt">
                                <h3>Ready to change your life?</h3>
                                <p>Find your perfect course. Over 700 to choose from.</p>
                                <a href="<?= SITE_URL ?>courses"><img src="<?= SITE_URL ?>assets/images/right-arrow.png" alt="" /></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-12 col-lg-6">
                        <div class="white-box with-side-img">
                            <img src="<?= SITE_URL ?>assets/images/review-course.jpg" alt="NSA">
                            <div class="support-cnt">
                                <h3>Review Your Course?</h3>
                                <p>Let us know what you thought of your course.</p>
                                <a href="<?= SITE_URL ?>courses/review"><img src="<?= SITE_URL ?>assets/images/right-arrow.png" alt="" /></a>
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