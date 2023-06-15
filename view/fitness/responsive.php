<?php
// create landing page generator in the future
$this->setControllers(array("course"));
$css = array("animate.min.css", "landing.css");

$pageTitle = "Fitness Landing Page";
include BASE_PATH . 'header.php';
?>

    <main role="main" class="regular">

        <div id="hero">
            <div class="container wider-container">

                <div class="row">
                    <div class="col-12 col-lg-7">

                        <h2 class="wow fadeInUp">Start your career as a personal trainer</h2>
                        <p class="wow fadeInUp" data-wow-delay="0.4s">Start for only £25...</p>

                        <a href="<?= SITE_URL ?>checkout?addSub=true&plan=1" class="cta wow pulse" data-wow-delay="3s">
                            Get Started
                        </a>

                    </div>
                    <div class="col-12 col-lg-5">
                        <img src="<?= SITE_URL ?>assets/images/free-online-banner.png" alt="Unlimited Courses Subscription" />
                    </div>
                </div>

            </div>
        </div>

        <section class="staff-learning">
            <div class="container learn-container">
                <div class="row text-center">
                    <div class="col-12 mt-5 career-enhancing">
                        <h2 class="section-title wow fadeIn">Unlimited Learning with Access to Our Course Library - Plus More</h2>
                    </div>
                </div>
                <div class="container lerning-container text-center">
                    <div class="col-md-12 mt-5 mb-5">
                        <div class="row mt-5">
                            <div class="col-md-6 p-3 career-course wow fadeIn">
                                <div class="row ">
                                    <div class="col-4 col-md-3  text-right">
                                        <div class="icon">
                                            <i class="far fa-rocket"></i>
                                        </div>
                                    </div>
                                    <div class=" col-8 col-md-8 pt-md-3">
                                        <h5><b>Access to all 700 courses</b></h5>
                                        <h6>With courses ranging from dog grooming to retail banking, we're bound to have the perfect one for you.</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 p-3 career-course wow fadeIn">
                                <div class="row">
                                    <div class="col-4 col-md-3 text-right">
                                        <div class="icon">
                                            <i class="far fa-tag"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 col-md-8 pt-md-3">
                                        <h5><b>Free XO Student Discount Membership</b></h5>
                                        <h6>Save on 100’s of big brands with an XO Student Discounts digital membership.</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 p-3 career-course wow fadeIn">
                                <div class="row">
                                    <div class="col-4 col-md-3 text-right">
                                        <div class="icon">
                                            <i class="far fa-puzzle-piece"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 col-md-8 pt-md-3">
                                        <h5><b>Career Personality Matching</b></h5>
                                        <h6>Find your ideal career based on your personality, interests and education.</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 p-3 career-course wow fadeIn">
                                <div class="row">
                                    <div class="col-4 col-md-3 text-right">
                                        <div class="icon">
                                            <i class="far fa-tv-alt"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 col-md-8 pt-md-3">
                                        <h5><b>Tutor Support</b></h5>
                                        <h6>Get 24/7 support from our student support assistants and friendly tutors.</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br />
            <br />

        </section>

        <section class="landing-stats">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-lg-4">

                        <i class="far fa-rocket"></i>

                        <p class="stat wow fadeInUp">
                            700+
                        </p>

                        <p class="description">
                            CPD Approved Courses
                        </p>

                    </div>
                    <div class="col-12 col-lg-4">

                        <i class="far fa-chalkboard"></i>

                        <p class="stat wow fadeInUp" data-wow-delay="0.2s">
                            4550+
                        </p>

                        <p class="description">
                            Lessons
                        </p>

                    </div>
                    <div class="col-12 col-lg-4">

                        <i class="far fa-file-word"></i>

                        <p class="stat wow fadeInUp" data-wow-delay="0.4s">
                            4000+
                        </p>

                        <p class="description">
                            Worksheets
                        </p>

                    </div>
                </div>
            </div>
        </section>

        <br />
        <br />

        <!-- Popular Course Slider-->
        <section class="bottom-bordered-section">
            <div class="container wider-container">

                <div class="home-slider padded-sec">
                    <h2 class="section-title">Access all of these courses, and more, for just £12 p/m...</h2>

                    <div class="landingCourses">
                        <?php
                        $courses = ORM::for_table("courses")->where("hidden", "0")->where("featured", "1")->order_by_expr("RAND()")->limit(9)->find_many();
                        foreach ($courses as $course) {

                            ?>
                            <div class="single">
                                <div class="img" style="background-image:url('<?= $this->course->getCourseImage($course->id, "large") ?>');"></div>
                                <div class="title">
                                    <?= $course->title ?>
                                </div>
                            </div>
                            <?php

                        }
                        ?>
                    </div>
                </div>

            </div>
        </section>

        <section class="cta sticky">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <h4>Start your premium membership today for only £12 p/m</h4>
                        <p>& get access to our entire course catalogue and exclusive benefits</p>
                    </div>
                    <div class="col-12 col-lg-4">
                        <a href="<?= SITE_URL ?>checkout?addSub=true&plan=1">
                            Let's Do It!
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="question-learning">
            <div class="container learn-container text-center">
                <div class="row">
                    <div class="col-12 mt-5">
                        <h2 class="section-title wow fadeIn">We're also giving you FREE access to discounts at the biggest retailers...</h2>

                        <div class="text-center">
                            <img src="<?= SITE_URL ?>assets/images/small-xo-logo.png" />
                        </div>

                    </div>
                </div>
            </div>
            <div class="container lerning-container xoUpsell">
                <div class="col-md-12 mt-5 ">
                    <div class="row ">
                        <div class="col-md-12">
                            <img src="<?= SITE_URL ?>assets/images/brands.png" alt="XO Brands" width="100%" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="question-learning" id="studentcount">
            <div class="container learn-container text-center">
                <div class="row">
                    <div class="col-12 mt-5">
                        <h2 class="section-title wow fadeIn">Don't just take our word for it...</h2>
                    </div>
                </div>
            </div>
            <div class="container lerning-container">
                <div class="col-md-12 mt-5 ">
                    <div class="row ">
                        <div class="col-md-4 text-center student">
                            <div class="student-counting ">
                                <h5>Absolutely amazing courses.. I had so much fun learning with New Skills Academy. The courses waere so informative, set out really well and I learned so much. It was great that with two young children under 3, I still managed to learn and pass my courses. I'm so happy. New Skills Academy have given me the confidence to start my venture . Thank you so much. Amy xx</h5>
                                <div class=""><img src="https://rebuild.newskillsacademy.co.uk/assets/images/speach-new.png" alt=""></div>
                            </div>
                            <div class=" mt-5 students-cont">
                                <img src="https://rebuild.newskillsacademy.co.uk/assets/images/Amy-Hewson.png" alt="">
                                <h5>Amy Hewson</h5>
                            </div>
                        </div>
                        <div class="col-md-4 text-center student">
                            <div class="student-counting">
                                <h5>The courses are absolutely brilliant for someone like me that has a busy working life – the modules are chunked into bite sized pieces and are really simple and straightforward to use. It is easy to dip in and out of and pick up from where you left off. It really has proved excellent value for money!</h5>
                                <div><img src="https://rebuild.newskillsacademy.co.uk/assets/images/speach-new.png" alt=""></div>
                            </div>
                            <div class=" mt-5 students-cont">
                                <img src="https://rebuild.newskillsacademy.co.uk/assets/images/Ryan-Knight.png" alt="">
                                <h5>Ryan Knight</h5>
                            </div>
                        </div>
                        <div class="col-md-4 text-center student">
                            <div class="student-counting">
                                <h5>Absolutely loved this course!! I am very happy with my experience in completing this online course with New Skills Academy. It was an easy process and their website is very easy to get around. No stress over time limits. I worked through the course at my own pace, and from the comforts of my own home.</h5>
                                <div><img src="https://rebuild.newskillsacademy.co.uk/assets/images/speach-new.png" alt=""></div>
                            </div>
                            <div class="mt-5 students-cont">
                                <img src="https://rebuild.newskillsacademy.co.uk/assets/images/Natalie-Rogerson.png" alt="">
                                <h5>Natalie Rogerson</h5>
                            </div>
                        </div>
                    </div>
                    <br />
                    <br />
                    <br />
                </div>
            </div>
        </section>

        <section class="question-learning" id="Frequently-ques">
            <div class="container learn-container text-center">
                <div class="row">
                    <div class="col-12 mt-5">
                        <h2 class="section-title wow fadeIn">Frequently Asked Questions</h2>
                    </div>
                </div>
            </div>
            <div class="container lerning-container text-center">
                <div class="col-md-12 mt-5 mb-5">
                    <div class="row mt-5">
                        <div class="col-md-5 offset-md-1  question-asked">
                            <h5>Which courses can I study? </h5>
                            <p>As a premium member you will get access to every individual course in our library. That’s over 700 courses.</p>
                        </div>
                        <div class="col-1"></div>
                        <div class="col-md-5 question-asked">
                            <h5>Are there any restrictions?</h5>
                            <p>The only restriction is that you can have a maximum of 50 active courses at any one time. As soon as you complete a course you can then add another.</p>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-5 offset-md-1 question-asked">
                            <h5>Who is eligible for a free course? </h5>
                            <p>Anyone who is registered at one of the listed Job Centres can apply for a free course. Once validated we will create an account and add your specified course to it.</p>
                        </div>
                        <div class="col-1"></div>
                        <div class="col-md-5 question-asked">
                            <h5>How do I cancel my premium membership?</h5>
                            <p>You can cancel your membership at any time from your account. Alternatively contact our friendly student support advisors and they’ll be able to do it for you.
                            </p>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-5 offset-md-1 question-asked">
                            <h5>If I cancel my premium membership do I lose access to my courses and certifications?</h5>
                            <p>No, any completed courses and awarded certificates will remain in your account for you to access for 12 months after your last payment.</p>
                        </div>
                        <div class="col-1"></div>
                        <div class="col-md-5 question-asked">
                            <h5>What does the premium membership include?</h5>
                            <p>As a premium member you will receive access to our entire course library.

                                In addition you will receive an XO Student Discounts Digital membership (rrp £10) for as long as you are a member

                                You will also get access to our personality career matching service.

                                Access to our community of learners

                                All this in addition to the usual benefits New Skills Academy students receive
                            </p>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-5 offset-md-1 question-asked">
                            <h5>How do I become a premium member?</h5>
                            <p>It’s simple! Click the Let’s Go / Buy Now button. Complete your details on the checkout and your membership will be activated immediately.</p>
                        </div>
                        <div class="col-1"></div>
                        <div class="col-md-5 question-asked">
                            <h5>Do I get support?</h5>
                            <p>Yes. You will still receive 24 hour  support from our friendly student support assistants and Tutors.
                            </p>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-5 offset-md-1 question-asked">
                            <h5>When will my payment be taken?</h5>
                            <p>Your payment will be taken on the same day in 12 months time unless you have cancelled your membership beforehand.</p>
                        </div>
                        <div class="col-1"></div>
                        <div class="col-md-5 question-asked">
                            <h5>Who can become a premium member?</h5>
                            <p>Anyone over the age of 18 can become a premium member.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js" integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        new WOW().init();

        $(window).scroll(function() {
            var height = $(window).scrollTop();

            if(height  > 100) {
                $(".cta.sticky").fadeIn();
            } else {
                $(".cta.sticky").fadeOut();
            }
        });


        $( document ).ready(function() {

            var heroHeight = $("#hero").outerHeight();

            $("#hero img").css("height", heroHeight);

        });
    </script>

<?php include BASE_PATH . 'footer.php'; ?>