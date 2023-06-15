<?php
$css = array("staff-training.css");
$pageTitle = "Redeem A Voucher";

$breadcrumb = array(
    "Redeem A Voucher" => ""
);

include BASE_PATH . 'header.php';
?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--page title-->
        <section class="course-title">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="section-title text-center">Redeem A Voucher</h1>
                    </div>
                </div>
            </div>
        </section>

        <!--Page Content-->
        <section class="staff-training">
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
                                <input type="password" class="form-control" placeholder="Choose a Password (if you do not already have an account)" name="password">
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

        <?php include BASE_PATH . 'learn-confidence.php'; ?>

        <?php include BASE_PATH . 'newsletter.php'; ?>

        <?php include BASE_PATH . 'featured.php'; ?>

        <?php include BASE_PATH . 'success-stories.php'; ?>

    </main>
    <!-- Main Content End -->

<?php include BASE_PATH . 'footer.php';?> 