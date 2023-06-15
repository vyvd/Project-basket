<?php
$pageTitle = "Page Not Found";
include BASE_PATH . 'header.php';
?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--Page Content-->

        <section class="page404">
            <div class="container wider-container">
                <div class="row align-items-center">
                    <div class="col-12 col-md-12 col-lg-6 text404">
                        <h1>Oops!</h1>
                        <p>We can't seem to find the page that you are looking for.</p>
                        <p>Please use the links at the top of page to navigate back to safety.</p>
                        <a href="<?= SITE_URL ?>courses">or see all of our courses here</a>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6">
                        <a href="<?= SITE_URL ?>courses">
                            <img src="<?= SITE_URL ?>assets/images/404.png" alt="404">
                        </a>
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