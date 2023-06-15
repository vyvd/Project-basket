
<?php
$newsletterCourse = $this->getNewsletterCourse();
?>
<section class="free-cource">
    <div class="container wider-container padded-sec">
        <div class="row text-center">
            <div class="col-12 col-md-4 d-none d-md-block">
                <img src="<?= SITE_URL?>assets/images/free-course.png" alt="Free Course">
                <span class="rrp">
                    RRP
                    <span><?php echo $currency->short ?>100</span>
                </span>
            </div>
            <div class="col-12 col-md-8">
                <h2 class="section-title colored">Get a <span>FREE</span> Course</h2>
                <p>Sign up to our newsletter and get access to the <?= $newsletterCourse->title ?> course for free! <br/>Simply enter your details below and we will email you access to your free course!</p>
                <form name="freeCourse" style="width:100%;">
                    <div class="col-12">
                        <input type="text" placeholder="First Name" class="text-field" name="firstname">
                        <input type="email" placeholder="Email Address" class="text-field" name="email">
                    </div>
                    <div class="col-12">
                        <input type="submit" class="btn btn-secondary btn-lg extra-radius" value="GET MY FREE COURSE">
                    </div>
                </form>
                <?php
                $this->renderFormAjax("account", "join-newsletter", "freeCourse")
                ?>
            </div>
        </div>
    </div>
</section>