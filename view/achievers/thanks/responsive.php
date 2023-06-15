<?php
$css = array("acheiver.css");
$pageTitle = "Thank You";

$breadcrumb = array(
    "Achiever Board" => SITE_URL.'achievers',
    "Submit" => SITE_URL.'achievers/submit',
    "Thank You" => '',
);

include BASE_PATH . 'header.php';
?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--Page Content-->
        <section class="">
            <div class="container wider-container">

                <div class="row align-items-center acheiver-board-submit">
                    <div class="col-12 text-center">
                        <h1 style="margin-top:15px;">Thank you!</h1>
                        <p>We have received your submission and will review. If we approve your submission, then it will appear publicly on our achiever board.</p>
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