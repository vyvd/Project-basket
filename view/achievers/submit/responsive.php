<?php
$css = array("acheiver.css");
$pageTitle = "Submit to Achiever Board";

$breadcrumb = array(
    "Achiever Board" => SITE_URL.'achievers',
    "Submit" => SITE_URL.'achievers',
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
                        <h1>Let's Celebrate Your Success</h1>
                        <p>Send us a picture of you with your certificate. Each month we give one lucky student a free course and a &pound;20 Amazon Voucher. Complete the short form below to get started.</p>
                    </div>
                    <form name="submit">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <input type="text" id="firstName" name="firstname" placeholder="First Name" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" id="lastName" name="lastname" placeholder="Last name" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <input type="email" id="email" name="email" placeholder="Email Address" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" id="contact" name="phone" placeholder="Contact Number" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <input type="text" id="city" name="city" placeholder="City You Live In" class="form-control">
                            </div>
                            <div class="custom-file form-group col-md-6">
                                <input type="file" class="custom-file-input" id="customFile" name="uploaded_file">
                                <label class="custom-file-label" for="customFile">Upload Image</label>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="form-group col-md-12 col-lg-10 custom-control custom-checkbox terms">
                                <input type="checkbox" class="custom-control-input" id="terms" name="terms" value="1">
                                <label class="custom-control-label" for="terms">
                                    Yes
                                </label>
                                <span>I agree that by submitting my image it may be published on newskillsacademy.co.uk, sister sites and promotions</span>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center submit-btn">
                            <input type="submit" value="SUBMIT" class="btn">
                        </div>
                    </form>
                    <?php
                    $this->renderFormAjax("achieverBoard", "submit", "submit");
                    ?>
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