<?php
if ($this->get["freeCredit"] == "true") {
    $_SESSION["freeCredit"] = "true";
}
// create landing page generator in the future
$this->setControllers(array("course"));
$css = array("animate.min.css", "landing.css");

$pageTitle = "Unlimited Course Subscription";
include BASE_PATH . 'header.php';

$courseCount = 700;
$cvWord = "CV";

if(SITE_TYPE == "us") {
    $courseCount = 300;
    $cvWord = "Resume";
}

?>
    <meta name="robots" content="noindex">

    <link rel="stylesheet" href="<?= SITE_URL.'assets/css/subscription.css' ?>">
    <style>
        #freeCourseLeave{
            display: none;
        }
        .mobileBasketToggle{
            display: none;
        }
    </style>
    <main role="main" class="regular subscription-2">

        <?php
        if ($this->getSetting("subBalanceOfferActive") == "Yes") {
            ?>
            <div class="promoMessage">
                <i class="fas fa-gift"></i>
                <?= $this->getSetting("subBalanceOfferMessage") ?>
            </div>
            <?php
        }

        if ($_SESSION["freeCredit"] == "true") {
            ?>
            <div class="promoMessage">
                <i class="fas fa-gift"></i>
                Purchase this subscription and we will automatically add £50.00 to your account balance
            </div>
            <?php
        }
        ?>
        <section class="subscription_courses">
            <div class="container learn-container">
                <div class="show-desktop courses_devices_bg">
                    <div class="row">
                        <div class="col-md-3 text-right">
                            <div class="courseDevicesStats float-right">
                                <?= $courseCount ?>+
                                <span>COURSES</span>
                            </div>
                        </div>
                        <div class="col-md-6 text-center">
                            <video class="" width="448" height="260" autoplay muted playsinline loop>
                                <source src="<?= SITE_URL ?>assets/images/ezgif.com-gif-maker.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="col-md-3 text-left">
                            <div class="courseDevicesStats">
                                4500+
                                <span>LESSONS</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="show-mobile ">
                    <div class="row text-right">
                        <div class="col-md-6 ">
                            <video class="mobile_video courses_bg" autoplay muted playsinline loop>
                                <source src="<?= SITE_URL ?>assets/images/ezgif.com-gif-maker.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6 text-center">
                                    <div class="courseDevicesStats">
                                        <?= $courseCount ?>+
                                        <span>COURSES</span>
                                    </div>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="courseDevicesStats">
                                        4500+
                                        <span>LESSONS</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
        <section class="staff-learning">
            <div class="container learn-container">
                <div class="col-12 pt-5 career-enhancing text-center">
                    <h2 class="section-title wow fadeIn">Unlimited Learners can study all of these courses and
                        many more...</h2>
                </div>
                <div class="lerning-container text-center">
                    <div class="col-md-12 mt-md-5 mb-5 show-desktop">
                        <div class="landingCourses">
                            <div class="row">
                                <?php
                                $courses = ORM::for_table("courses")->where("hidden", "0")->where("featured",
                                    "1")->order_by_expr("RAND()")->limit(24)->find_many();
                                foreach ($courses as $course) {

                                    ?>
                                    <div class="col-md-3 col-lg-2">
                                        <div class="single">
                                            <div class="single_inner"
                                                 style="background-image:url('<?= $this->course->getCourseImage($course->id,
                                                     "full", 'icon_image') ?>');">
                                                <div class="title"><?= $course->title ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php

                                }
                                ?>
                            </div>
                        </div>

                    </div>
                    <div class="landingCourses show-mobile">
                        <div id="landingCoursesSlider" class="landingCoursesSlider owl-carousel">
                            <?php
                            $courses = ORM::for_table("courses")->where("hidden", "0")->where("featured",
                                "1")->order_by_expr("RAND()")->limit(18)->find_many();
                            foreach ($courses as $course) {

                                ?>
                                <div class="single">
                                    <div class="single_inner"
                                         style="background-image:url('<?= $this->course->getCourseImage($course->id,
                                             "full", 'icon_image') ?>');">
                                        <div class="title"><?= $course->title ?></div>
                                    </div>
                                </div>

                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <br/>
            <br/>

        </section>
        <section class="cta sticky">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-md-9 ctaSticky_membership">
                                Start your Unlimited Learning membership today for only <?= $this->price($currency->prem12) ?>
                            </div>
                            <div class="col-md-3">
                                <a href="<?= SITE_URL ?>ajax?c=cart&a=add-renewal-discounted-subscription">
                                    START NOW
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
        <section class="question-learning_free_access">
            <div class="container pb-5 learn-container text-center">
                <h2 class="section-title wow fadeIn">Unlimited Learners also get access to....</h2>
                <?php
                if($currency->code == "GBP") {
                    ?>
                    <div class="p-3 " style="border-radius: 10px;">
                        <div class="row ">
                            <div class="col-md-5 text-left">
                                <div class="sub_sec_title_inner ">
                                    <div class="sub_sec_title_outer show-desktop">
                                        <label>
                                            XO Student Discount
                                            <!--                                        <img src="--><?//= SITE_URL ?><!--assets/images/xo_sub.png">-->
                                        </label>
                                    </div>
                                    <div class="sub_sec_title_outer show-mobile">
                                        <label>
                                            XO Student Discount
                                            <!--                                        <img src="--><?//= SITE_URL ?><!--assets/images/xo-b.png">-->
                                        </label>
                                    </div>
                                </div>
                                <h3 class="column-title mt-5">FREE XO Student Discounts membership</h3>
                                <h5 class="">Save money by grabbing exclusive discounts at 100’s of the UK’s
                                    biggest retailers.</h5>
                                <!--                            <label class="sec-col-lb mb-4">save £10</label>-->
                            </div>
                            <div class="col-md-7">
                                <div class="col_outer">
                                    <div class="col_inner">
                                        <img src="<?= SITE_URL ?>assets/images/xo-brands.png">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </section>
        <section class="question-learning_cv_builder">
            <div class="container pb-5 learn-container text-center">
                <div class="p-3 " style="border-radius: 10px;">
                    <div class="row">
                        <div class="col-md-6 order-md-first">
                            <div class="col_outer">
                                <div class="col_inner">
                                    <img src="<?= SITE_URL ?>assets/images/cv.png">
                                    <p>Free access to our award-winning AI-powered <?= $cvWord ?> builder and
                                        templates which are trusted by thousands of job seekers</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 order-first  ">
                            <div class="sub_sec_title_outer mt-md-4">
                                <label>Land Your Dream Job</label>
                            </div>
                            <h3 class="column-title-sub ml-md-3 mt-5">
                                <u>Our <?= $cvWord ?> Builder</u> <br>
                                Create the perfect <?= $cvWord ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="question-learning_free_access dream_career">
            <div class="container pb-5 learn-container text-center">
                <div class="p-3 " style="border-radius: 10px;">
                    <div class="row">
                        <div class="col-md-6 ">
                            <div class="sub_sec_title_outer mt-md-4 left">
                                <label>Find Your Dream Career</label>
                            </div>
                            <h3 class="column-title-sub mt-5">
                                <u>Our Career Personality Matching Service</u> <br>
                                Discover your ideal career path
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <div class="col_outer mt-3">
                                <div class="col_inner">
                                    <img src="<?= SITE_URL ?>assets/images/career_sub.png">
                                    <p>Find your ideal career based on your personality, interests and education.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="question-learning_cv_builder community">
            <div class="container pb-5 learn-container text-center">
                <div class="p-3 " style="border-radius: 10px;">
                    <div class="row">
                        <div class="col-md-6 order-md-first">
                            <div class="col_outer">
                                <div class="col_inner">
                                    <img src="<?= SITE_URL ?>assets/images/community.png">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 order-first">
                            <div class="sub_sec_title_outer">
                                <label>Meet Fellow Students</label>
                            </div>
                            <h3 class="column-title-sub w-100 mt-5 pl-md-3">
                                <u>Our learning community</u> <br>
                                Chat with 1000's of fellow students
                            </h3>
                            <h5 class="pl-md-3 text-left">Get advice, share your opinions and meet like-minded students
                                in our
                                fun friendly community.</h5>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="question-learning" id="studentcount">
            <div class="container lerning-container">
                <div class="col-md-12 p-4 p-md-5">
                    <div class="sub_sec_title_outer show-desktop text-center ml-auto">
                        <label>Testimonials</label>
                    </div>
                    <h2 class="section-title wow fadeIn mb-md-5">Don't just take our word for it...</h2>

                    <div class="show-desktop row ">
                        <div class="col-md-4 text-center student">
                            <div class="student-counting">
                                <h5>The courses are absolutely brilliant for someone like me that has a busy working
                                    life – the modules are chunked into bite sized pieces and are really simple and
                                    straightforward to use. It is easy to dip in and out of and pick up from where
                                    you
                                    left off. It really has proved excellent value for money!</h5>
                                <div class="blue_down"><img src="<?= SITE_URL ?>assets/images/arrow_d_b.png" alt="">
                                </div>
                                <div class="white_down"><img src="<?= SITE_URL ?>assets/images/arrow_d_w.png"
                                                             alt="">
                                </div>
                            </div>
                            <div class=" mt-5 students-cont">
                                <img src="<?= SITE_URL ?>assets/images/Ryan-Knight.png" alt="">
                                <h5>Ryan Knight</h5>
                            </div>
                        </div>
                        <div class="col-md-4 text-center student">
                            <div class="student-counting ">
                                <h5>Absolutely amazing courses.. I had so much fun learning with New Skills Academy.
                                    The
                                    courses waere so informative, set out really well and I learned so much. It was
                                    great that with two young children under 3, I still managed to learn and pass my
                                    courses. I'm so happy. New Skills Academy have given me the confidence to start
                                    my
                                    venture . Thank you so much. Amy xx</h5>
                                <div class="blue_down"><img src="<?= SITE_URL ?>assets/images/arrow_d_b.png" alt="">
                                </div>
                                <div class="white_down"><img src="<?= SITE_URL ?>assets/images/arrow_d_w.png"
                                                             alt="">
                                </div>
                            </div>


                            <div class=" mt-5 students-cont">
                                <img src="<?= SITE_URL ?>assets/images/Amy-Hewson.png" alt="">
                                <h5>Amy Hewson</h5>
                            </div>
                        </div>


                        <div class="col-md-4 text-center student">
                            <div class="student-counting">
                                <h5>Absolutely loved this course!! I am very happy with my experience in completing
                                    this
                                    online course with New Skills Academy. It was an easy process and their website
                                    is
                                    very easy to get around. No stress over time limits. I worked through the course
                                    at
                                    my own pace, and from the comforts of my own home.</h5>
                                <!--                                <div><img src="-->
                                <? //= SITE_URL ?><!--assets/images/speach-new.png" alt=""></div>-->
                                <div class="blue_down"><img src="<?= SITE_URL ?>assets/images/arrow_d_b.png" alt="">
                                </div>
                                <div class="white_down"><img src="<?= SITE_URL ?>assets/images/arrow_d_w.png"
                                                             alt="">
                                </div>
                            </div>
                            <div class="mt-5 students-cont">
                                <img src="<?= SITE_URL ?>assets/images/Natalie-Rogerson.png" alt="">
                                <h5>Natalie Rogerson</h5>
                            </div>
                        </div>
                    </div>
                    <div class="show-mobile ">
                        <div id="studentCountingSlider" class="studentCountingSlider owl-carousel">
                            <div class="col-md-4 text-center student">
                                <div class="student-counting">
                                    <h5>The courses are absolutely brilliant for someone like me that has a busy working
                                        life – the modules are chunked into bite sized pieces and are really simple and
                                        straightforward to use. It is easy to dip in and out of and pick up from where
                                        you
                                        left off. It really has proved excellent value for money!</h5>
                                    <div class="blue_down"><img src="<?= SITE_URL ?>assets/images/arrow_d_b.png" alt="">
                                    </div>
                                    <div class="white_down"><img src="<?= SITE_URL ?>assets/images/arrow_d_w.png"
                                                                 alt="">
                                    </div>
                                </div>
                                <div class=" mt-5 students-cont">
                                    <img src="<?= SITE_URL ?>assets/images/Ryan-Knight.png" alt="">
                                    <h5>Ryan Knight</h5>
                                </div>
                            </div>
                            <div class="col-md-4 text-center student">
                                <div class="student-counting ">
                                    <h5>Absolutely amazing courses.. I had so much fun learning with New Skills Academy.
                                        The
                                        courses waere so informative, set out really well and I learned so much. It was
                                        great that with two young children under 3, I still managed to learn and pass my
                                        courses. I'm so happy. New Skills Academy have given me the confidence to start
                                        my
                                        venture . Thank you so much. Amy xx</h5>
                                    <div class="blue_down"><img src="<?= SITE_URL ?>assets/images/arrow_d_b.png" alt="">
                                    </div>
                                    <div class="white_down"><img src="<?= SITE_URL ?>assets/images/arrow_d_w.png"
                                                                 alt="">
                                    </div>
                                </div>


                                <div class=" mt-5 students-cont">
                                    <img src="<?= SITE_URL ?>assets/images/Amy-Hewson.png" alt="">
                                    <h5>Amy Hewson</h5>
                                </div>
                            </div>


                            <div class="col-md-4 text-center student">
                                <div class="student-counting">
                                    <h5>Absolutely loved this course!! I am very happy with my experience in completing
                                        this
                                        online course with New Skills Academy. It was an easy process and their website
                                        is
                                        very easy to get around. No stress over time limits. I worked through the course
                                        at
                                        my own pace, and from the comforts of my own home.</h5>
                                    <!--                                <div><img src="-->
                                    <? //= SITE_URL ?><!--assets/images/speach-new.png" alt=""></div>-->
                                    <div class="blue_down"><img src="<?= SITE_URL ?>assets/images/arrow_d_b.png" alt="">
                                    </div>
                                    <div class="white_down"><img src="<?= SITE_URL ?>assets/images/arrow_d_w.png"
                                                                 alt="">
                                    </div>
                                </div>
                                <div class="mt-5 students-cont">
                                    <img src="<?= SITE_URL ?>assets/images/Natalie-Rogerson.png" alt="">
                                    <h5>Natalie Rogerson</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <br/>
                    <br/>
                </div>
            </div>
        </section>

        <section class="question-learning" id="Frequently-ques">
            <div class="container learn-container text-center">
                <div class="row">
                    <div class="col-md-4 mt-5">
                        <h2 class="section-title wow fadeIn text-left">Frequently Asked Questions</h2>
                    </div>
                    <div class="col-md-8 mt-5">
                        <div class="accordion" id="accordionFaqs">
                            <?php
                            $questions = ORM::for_table("faqs")->where("type",
                                "Subscriptions")->order_by_desc("id")->find_many();

                            foreach ($questions as $question) {

                                ?>
                                <div class="card">
                                    <div class="card-header" id="headingOne<?= $question->id ?>">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left accordion-button collapsed"
                                                    type="button" data-toggle="collapse"
                                                    data-target="#collapseOne<?= $question->id ?>" aria-expanded="false"
                                                    aria-controls="collapseOne<?= $question->id ?>">
                                                <?= $question->question ?>
                                            </button>
                                        </h2>
                                    </div>

                                    <div id="collapseOne<?= $question->id ?>" class="collapse"
                                         aria-labelledby="headingOne<?= $question->id ?>" data-parent="#accordionFaqs">
                                        <div class="card-body">
                                            <?= str_replace("700", $courseCount, $question->answer) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php

                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"
            integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        new WOW().init();

        $(window).scroll(function () {
            var height = $(window).scrollTop();

            if (height > 100) {
                $(".cta.sticky").fadeIn();
            } else {
                $(".cta.sticky").fadeOut();
            }
        });


        $(document).ready(function () {

            var heroHeight = $("#hero").outerHeight();

            $("#hero img").css("height", heroHeight);

        });
        $('.landingCoursesSlider.owl-carousel').owlCarousel({
            loop: false,
            autoplay: true,
            margin: 20,
            nav: false,
            responsive: {
                0: {
                    items: 2
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 6
                }
            }
        });
        $('.studentCountingSlider.owl-carousel').owlCarousel({
            loop: false,
            autoplay: true,
            margin: 20,
            nav: true,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 6
                }
            }
        });
    </script>

<?php include BASE_PATH . 'footer.php'; ?>