<?php
$css = array("home-page.css");
$pageTitle = "Teens Unite";
include BASE_PATH . 'header.php';
?>
<style>
    body {
        background: #f9f9f9;
    }
</style>
    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--What is Teens Unite-->
        <section class="teens-unite">
            <div class="container wider-container">
                <div class="row">

                    <div class="col-12 regular-white-sec">
                        <h1 class="section-title text-center">What is Teens Unite</h1>
                        <div class="row align-items-center">
                            <div class="col-12 col-md-8 padded-right">
                                <p>Teens Unite is a charity organisation that brings support to young people who have been affected by a cancer diagnosis.</p>
                                <p>The effects of such a diagnosis can be difficult to deal with and last far beyond treatments or even remission. Teens Unite focuses on 13-24 year-olds in the UK and offers them and their family members plenty of outlets and opportunities for support.</p>
                                <p>Their goal is to establish a retreat dedicated to this mission.</p>
                                <p>Teens Unite knows about the challenging social, emotional and physical difficulties of a youth cancer diagnosis, and they are working hard to help these adolescents feel strong and supported.</p>
                            </div>
                            <div class="col-12 col-md-4">
                                <img src="<?= SITE_URL ?>assets/images/teens1.jpg" alt="Teens Unite" >
                            </div>
                        </div>
                    </div>

                    <div class="col-12 regular-white-sec">
                        <h1 class="section-title text-center">New Skills & Teens Unite</h1>
                        <div class="row align-items-center">
                            <div class="col-12 col-md-8 padded-right">
                                <p>New Skills Academy and Teens Unite have recently established an amazing partnership between our two organisations.</p>
                                <p>Since New Skills Academy is a place to pursue personal and professional development, Teens Unite will now be able to use the valuable content and courses we have in place for the young people that they support.</p>
                                <p>We will also be working closely with Teens Unite to help support and link our content with the workshops that they hold. Plus, New Skills Academy will be further joining the mission of Teens Unite by becoming the Title Sponsors of the organisation’s Annual Golf Day on 12th July.</p>
                                <p>We are very excited about the development of our relationship with Teens Unite and to use our content to help move their mission ahead.</p>
                            </div>
                            <div class="col-12 col-md-4">
                                <img src="<?= SITE_URL ?>assets/images/teens2.jpg" alt="Teens Unite" >
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-center teens-btn">
                        <a class="btn btn-primary extra-radius" href="https://www.teensunite.org/" target="_blank">SEE HOW YOU CAN DONATE TO TEENS UNITE</a>
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