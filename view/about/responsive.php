<?php
$css = array("staff-training.css");
$pageTitle = "About Us";

$breadcrumb = array(
    "About Us" => '',
);

include BASE_PATH . 'header.php';

$usaUsersCount = 251475;

?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--page title-->
        <section class="course-title">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="section-title text-left">About Us</h1>
                    </div>
                </div>
            </div>
        </section>

        <!--Page Content-->
        <section class="staff-training">
            <div class="container wider-container">
                <div class="row">

                    <div class="col-12 staff-texts extra-radius about-boxes">
                        <h3 class="section-title text-center">Welcome to New Skills Academy – Part of the Be-a Education Family</h3>
                        <div class="row align-items-center">
                            <div class="col-12 col-md-8">
                                <p>In November of 2013, a single question was asked: ‘What do you want to learn?’ From there, a vision was born of comprehensive, high quality, and affordable online course availability. The name behind that vision was New Skills Academy.</p>
                                <p>When it comes to online schools, there are plenty, but at New Skills Academy we distinguish ourselves amongst the competition with our dedication to your long-term goals.</p>
                                <p>If you are entering a job market for the first time, or seeking a new career, we will provide you with the fast and efficient online support you need to make your dreams a reality.</p>
                                <p>Our expertly designed courses, detailed lesson plans and student testimonials all attest to the fact that we take great pride in our course offerings and provide only the best for our students.</p>
                                <p>Whether you are studying retail banking, dog grooming or even our cupcake academy, New Skills Academy has the tools you need to advance as a professional in your chosen profession.</p>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="bordered-elements"><p>Total Students</p><h2 class="counter" data-count="<?= ORM::for_table("accounts")->count()+$usaUsersCount ?>">0</h2></div>
                                <div class="bordered-elements"><p>Total Courses</p><h2 class="counter" data-count="<?= ORM::for_table("courses")->count() ?>">0</h2></div>
                                <div class="bordered-elements"><p>Total Lessons</p><h2 class="counter" data-count="<?= ORM::for_table("courseModules")->count() ?>">0</h2></div>
                            </div>
                        </div>
                    </div>

                    <script>

                        $('.counter').each(function() {
                            var $this = $(this),
                                countTo = $this.attr('data-count');

                            $({ countNum: $this.text()}).animate({
                                    countNum: countTo
                                },

                                {

                                    duration: 2000,
                                    easing:'linear',
                                    step: function() {
                                        $this.text(Math.floor(this.countNum));
                                    },
                                    complete: function() {
                                        $this.text(this.countNum);
                                        //alert('finished');
                                    }

                                });



                        });


                    </script>

                    <div class="col-12 staff-texts extra-radius about-boxes">
                        <h3 class="section-title text-center">Freedom to Study</h3>
                        <div class="row align-items-center">
                            <div class="col-12 col-md-8">
                                <p>Offering a broad range of course options is a good start, but we envisioned so much more for our students. We take great pride in offering our students the ability to study anytime, anywhere, and it is our belief that an award winning business would settle for nothing less.</p>
                                <p>Often, people dream of pursuing a degree or certification but never find the time to follow through. We know life gets busy, which is why our customers can take our courses online from anywhere while still working. Students can access our courses from a PC, mobile phone or tablet; this means you can learn anywhere you go.</p>
                                <p>Once students access our courses, they are offered up to date subject information for a lifetime!</p>
                            </div>
                            <div class="col-12 col-md-4 text-center">
                                <img src="<?= SITE_URL ?>assets/images/fish.png" alt="NSA">
                            </div>
                        </div>
                    </div>

                    <div class="col-12 staff-texts extra-radius about-boxes learn-with-confidence">
                        <h3 class="section-title text-center">Learn with Confidence</h3>
                        <div class="row align-items-center">
                            <div class="col-12">
                                <p>Our students’ reputation matters, which is why all of our online courses have been reviewed and certified in partnership with qualified industry experts. For added security and peace of mind, all of our courses have been approved by CPD. We welcome these reviews because we believe in our students, their interests, and ability to pursue and learn any new skill they may desire along the way.</p>
                            </div>
                            <div class="col-12 text-center">
                                <a href="#"><img src="<?= SITE_URL ?>assets/images/cpg.jpg" alt="cpd" /></a>
                                <a href="#"><img src="<?= SITE_URL ?>assets/images/cma.jpg" alt="cpd" /></a>
                                <a href="#"><img src="<?= SITE_URL ?>assets/images/ukrlp.jpg" alt="cpd" /></a>
                                <a href="#"><img src="<?= SITE_URL ?>assets/images/rospa.jpg" alt="cpd" /></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 staff-texts extra-radius about-boxes learn-with-confidence">
                        <h3 class="section-title text-center">Multi-Award Winning</h3>
                        <div class="row align-items-center">
                            <div class="col-12">
                                <p>New Skills Academy are proud to say that we are a multiple award winning company.</p>
                                <p>We have won both local and global awards in such categories as Best E-business, Outstanding Business, Family Business, Small Business and Entrepreneur of the Year.</p>
                                <p>As well as awards for the company as a whole, our co-founder, Chris Morgan, has been honoured for his hard work and dedication to bringing e-learning to individuals across the globe. He recently won both the Business Person of the Year award at the Hertfordshire Business Awards and Family Business Entrepreneur of the Year at the NatWest Great British Entrepreneur Awards.</p>
                                <p>We really do feel proud that our business’ aims and ambitions have been recognised in the positive light we had always hoped for.</p>
                                <p>The whole team at New Skills Academy are over the moon with this massive morale booster and are resolute on striving forward with an even stronger and more determined approach in the future.</p>
                            </div>
                            <div class="col-12 text-center awards">
                                <a href="#"><img src="<?= SITE_URL ?>assets/images/awards1.png" alt="Awards" /></a>
                                <a href="#"><img src="<?= SITE_URL ?>assets/images/awards2.png" alt="Awards" /></a>
                                <a href="#"><img src="<?= SITE_URL ?>assets/images/awards3.png" alt="Awards" /></a>
                                <a href="#"><img src="<?= SITE_URL ?>assets/images/awards4.png" alt="Awards" /></a>
                            </div>
                            <div class="col-12 text-center">
                                <a href="#"><img src="<?= SITE_URL ?>assets/images/awards5.png" alt="Awards" /></a>
                                <a href="#"><img src="<?= SITE_URL ?>assets/images/awards6.png" alt="Awards" /></a>
                                <a href="#"><img src="<?= SITE_URL ?>assets/images/awards7.png" alt="Awards" /></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 staff-texts extra-radius about-boxes">
                        <h3 class="section-title text-center">Supporting Teens Unite</h3>
                        <div class="row align-items-center">
                            <div class="col-12 col-md-8">
                                <p>New Skills Academy and Teens Unite have recently established an amazing partnership between our two organisations. Since New Skills Academy is a place to pursue personal and professional development, Teens Unite will now be able to use the valuable content and courses we have in place for the young people that they support.</p>
                                <p>We will also be working closely with Teens Unite to help support and link our content with the workshops that they hold.</p>
                                <p>You can find out more about our <a href="<?= SITE_URL ?>teens-unite">partnership here</a>.</p>
                            </div>
                            <div class="col-12 col-md-4 text-center">
                                <img src="<?= SITE_URL ?>assets/images/teens-united.png" alt="teens">
                            </div>
                        </div>
                    </div>

                    <div class="col-12 staff-texts extra-radius about-boxes">
                        <h3 class="section-title text-center">Key Personnel</h3>
                        <div class="row personal">
                            <?php
                            foreach($this->controller->getTeamMembers("key") as $team) {

                                ?>
                                <div class="col-6 col-md-4 col-lg-2 text-center">
                                    <img src="<?= $this->controller->getTeamImage($team->id) ?>" alt="<?= $team->name ?>">
                                    <span><?= $team->name ?> <br/><?= $team->title ?></span>
                                </div>
                                <?php

                            }
                            ?>
                        </div>

                        <h3 class="section-title text-center">Student Support</h3>

                        <div class="row personal">
                            <?php
                            foreach($this->controller->getTeamMembers("student") as $team) {

                                ?>
                                <div class="col-6 col-md-4 col-lg-2 text-center">
                                    <img src="<?= $this->controller->getTeamImage($team->id) ?>" alt="<?= $team->name ?>">
                                    <span><?= $team->name ?> <br/><?= $team->title ?></span>
                                </div>
                                <?php

                            }
                            ?>

                        </div>

                        <h3 class="section-title text-center">Tutors</h3>

                        <div class="row personal">
                            <?php
                            foreach($this->controller->getTeamMembers("tutors") as $team) {

                                ?>
                                <div class="col-6 col-md-4 col-lg-2 text-center">
                                    <img src="<?= $this->controller->getTeamImage($team->id) ?>" alt="<?= $team->name ?>">
                                    <span><?= $team->name ?> <br/><?= $team->title ?></span>
                                </div>
                                <?php

                            }
                            ?>

                        </div>

                        <h3 class="section-title text-center">Employee of the Month</h3>

                        <div class="row buster align-items-center">
                            <div class="col-12 col-md-2">
                                <img src="<?= SITE_URL ?>assets/images/offic-dog.png" alt="Dog">
                            </div>
                            <div class="col-12 col-md-10">
                                <h4>Buster</h4>
                                <p>-Office Dog-</p>
                                <p>What Buster lacks in formal qualifications he makes up for with enthusiasm. He is always ready to assist, especially with our pet related courses. Buster is a valued member of the New Skills Academy team.</p>
                            </div>
                        </div>

                    </div>

                    <div class="col-12 staff-texts extra-radius about-boxes about-contact">
                        <h3 class="section-title text-center">Contact</h3>
                        <div class="row buster align-items-center">
                            <div class="col-12 col-md-4 text-center">
                                <img src="<?= SITE_URL ?>assets/images/office-building.png" alt="Office">
                                <p>6 Corunna Court, Warwick,<br />CV34 5HQ</p>
                            </div>
                            <div class="col-12 col-md-4 text-center">
                                <img src="<?= SITE_URL ?>assets/images/contact-mail.png" alt="contact">
                                <p>0845 259 0244 <br/> support@newskillsacademy.co.uk</p>
                            </div>
                            <div class="col-12 col-md-4 text-center">
                                <img src="<?= SITE_URL ?>assets/images/like.png" alt="Office">
                                <div class="social-icons">
                                    <a href="<?= $this->getSetting("instagram"); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                                    <a href="<?= $this->getSetting("facebook"); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                    <a href="<?= $this->getSetting("twitter"); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                                    <a href="<?= $this->getSetting("youtube"); ?>" target="_blank"><i class="fab fa-youtube"></i></a>

                                </div>
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