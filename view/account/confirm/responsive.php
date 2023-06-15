<?php

if($_SESSION['userID'] == "") {
    header('Location: '.SITE_URL);
    exit;
}

$pageTitle = "Confirm Sign In";
include BASE_PATH . 'header.php';
?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--Page Content-->

        <section class="page404">
            <div class="container wider-container">

                <br />
                <br />
                <br />

                <form class="singlePageForm" name="confirm">

                    <h2>2FA Security</h2>

                    <div class="form-group">
                        <label>6 Digit Code <small>(this was sent to your email)</small></label>
                        <input type="text" class="form-control" placeholder="000000" name="code" />
                    </div>

                    <div class="totals">
                        <button type="submit" class="btn btn-primary extra-radius">Confirm Sign In</button>
                    </div>

                </form>
                <?php
                $this->renderFormAjax("account", "confirm-sign-in", "confirm");
                ?>

                <br />
                <br />
                <br />

            </div>
        </section>

        <?php include BASE_PATH . 'learn-confidence.php'; ?>

        <?php include BASE_PATH . 'newsletter.php'; ?>

        <?php include BASE_PATH . 'featured.php'; ?>

        <?php include BASE_PATH . 'success-stories.php'; ?>

    </main>
    <!-- Main Content End -->


<?php include BASE_PATH . 'footer.php';?>