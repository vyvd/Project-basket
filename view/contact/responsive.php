<?php
$css = array("staff-training.css");

$breadcrumb = array(
    "Learn Zone" => SITE_URL.'support',
    "Contact" => ""
);

$pageTitle = "Contact Us";
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
                        <h1 class="section-title text-left">Get in Touch</h1>
                    </div>
                </div>
            </div>
        </section>

        <!--Page Content-->
        <section class="staff-training">
            <div class="container wider-container">
                <div class="row">

                    <div class="col-12 col-md-6 col-lg-6 staff-form contact">
                        <h4>Complete the quick enquiry form below and one of our student advisors will be in touch shortly</h4>
                        <form name="contact">
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
                                <input type="tel" class="form-control" placeholder="Contact Number" name="phone">
                            </div>
                            <div class="form-group" style="background: #f4f4f4;border-radius: 28px;">
                                <input type="text" class="form-control" placeholder="Which country are you based in?" name="site">
                            </div>
                            <div class="form-group" style="background: #f4f4f4;border-radius: 28px;">
                                <select class="form-control" name="subject" style="    height: auto;
    padding: 13px 21px;width:97%;">
                                    <option value="">Nature of Enquiry</option>
                                    <option>Question about a Course</option>
                                    <option>Tutor support</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" placeholder="Please leave details of your enquiry" rows="5" name="message"></textarea>
                            </div>
                            <script src="https://www.google.com/recaptcha/api.js" async defer></script>

                            <div class="form-group">
                                <!-- Google reCAPTCHA block -->
                                <div class="g-recaptcha" data-sitekey="6LcDY_olAAAAAPDRFvb8hYLLFDDKs1IpwvCJmfZi"></div>
                            </div>
                            
                            <div class="form-group text-center">
                                <input type="submit" value="Submit Enquiry" class="btn btn-primary btn-lg extra-radius" />
                            </div>
                        </form>
                        <?php
                        $this->renderFormAjax("support", "contact", "contact");
                        ?>
                    </div>

                    <div class="col-12 col-md-6 col-lg-6 contact-details">
                        <div class="contact-boxes">
                            <div class="contact-grey-panel text-center">
                                <i class="fas fa-envelope"></i>
                                <?php
                                if(SITE_TYPE == "uk") {
                                    echo 'support@newskillsacademy.co.uk';
                                } else {
                                    echo 'support@newskillsacademy.com';
                                }
                                ?>
                            </div>
                            <div class="contact-grey-panel text-center contact-social">

                                <a href="<?= $this->getSetting("instagram"); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                                <a href="<?= $this->getSetting("facebook"); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                <a href="<?= $this->getSetting("twitter"); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                                <a href="<?= $this->getSetting("youtube"); ?>" target="_blank"><i class="fab fa-youtube"></i></a>

                            </div>
                        </div>
                        <div class="contact-boxes">
                            <h3>Helpful Articles</h3>
                            <?php
                            foreach($this->controller->latestHelpArticles(5) as $article) {
                                ?>
                                <div class="contact-grey-panel">
                                    <a href="<?= SITE_URL ?>support/help-articles/<?= $article->slug ?>">
                                        <i class="fas fa-chevron-right"></i> <?= $article->title ?>
                                    </a>
                                </div>
                                <?php
                            }
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