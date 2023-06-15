<?php
$css = array("staff-training.css");
$pageTitle = "Get Our Newsletter";

$breadcrumb = array(
    "Get Our Newsletter" => '',
);

include BASE_PATH . 'header.php';
?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--page title-->
        <section class="course-title staticPage">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="section-title text-left">Get Our Newsletter</h1>
                    </div>
                </div>
            </div>
        </section>

        <!--Page Content-->
        <section class="staff-training staticPage">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 staff-texts extra-radius text-center">
                        <div class="newsletter-box staff-form">
                            <form name="subscribeNewsletter">
                                <h2>
                                    Join our mailing list to keep up to date with our upcoming course launches and sales
                                </h2>
                                <div class="form-group input-box" >
                                    <input  class="form-control" type="email" name="email" placeholder="Enter email address" required>
                                    <div class="totals mt-3">
                                        <button type="submit" class="btn btn-secondary btn-lg extra-radius">Subscribe</button>
                                    </div>
                                    <div id="returnStatus"></div>
                                </div>
                            </form>
                            <?php
                                $this->renderFormAjax("account", "subscribeNewsletter", "subscribeNewsletter");
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