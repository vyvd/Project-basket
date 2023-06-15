<?php
// create landing page generator in the future
$this->setControllers(array("course"));
$css = array("animate.min.css", "landing.css");

$pageTitle = "Unlimited Course Subscription";
include BASE_PATH . 'header.php';

$courseCount = 700;

if($currency->code != "GBP") {
    $courseCount = 300;
}

?>

    <main role="main" class="regular">

        <div id="hero">
            <div class="container wider-container">

                <div class="row">
                    <div class="col-12 col-lg-7">

                        <h2 class="wow fadeInUp">Become a New Skills Academy Premium Member & get</h2>
                        <h1 class="wow fadeInUp" data-wow-delay="0.2s">Unlimited access <br />to <strong><?= $courseCount ?>+ courses</strong></h1>
                        <p class="wow fadeInUp" data-wow-delay="0.4s">Only <?= $this->price($currency->prem1) ?> per month!</p>

                        <a href="<?= SITE_URL ?>checkout?addSub=true&plan=1" class="cta wow pulse" data-wow-delay="3s">
                            Get Access Now
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
                                        <h5><b>Access to all <?= $courseCount ?>+ courses</b></h5>
                                        <h6>With courses ranging from dog grooming to retail banking, we're bound to have the perfect one for you.</h6>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if($currency->code == "GBP") {
                                ?>
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
                                <?php
                            }
                            ?>
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
                                            <i class="far fa-briefcase"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 col-md-8 pt-md-3">
                                        <h5><b>Job Board</b></h5>
                                        <h6>Browse for jobs in your local area with our expansive job search engine.</h6>
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
                                        <h5><b>CV Builder</b></h5>
                                        <h6>Quickly build and review your CV using our powerful CV builder tool.</h6>
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
                            <?= $courseCount ?>+
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
                    <h2 class="section-title">Access all of these courses, and more, for just <?= $this->price($currency->prem1) ?> p/m...</h2>

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
                        <h4>Start your premium membership today for only <?= $this->price($currency->prem1) ?> p/m</h4>
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

        <?php
        if($currency->code == "GBP") {
        ?>
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
        <?php } ?>

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
                                <div class=""><img src="<?= SITE_URL ?>assets/images/speach-new.png" alt=""></div>
                            </div>
                            <div class=" mt-5 students-cont">
                                <img src="<?= SITE_URL ?>assets/images/Amy-Hewson.png" alt="">
                                <h5>Amy Hewson</h5>
                            </div>
                        </div>
                        <div class="col-md-4 text-center student">
                            <div class="student-counting">
                                <h5>The courses are absolutely brilliant for someone like me that has a busy working life – the modules are chunked into bite sized pieces and are really simple and straightforward to use. It is easy to dip in and out of and pick up from where you left off. It really has proved excellent value for money!</h5>
                                <div><img src="<?= SITE_URL ?>assets/images/speach-new.png" alt=""></div>
                            </div>
                            <div class=" mt-5 students-cont">
                                <img src="<?= SITE_URL ?>assets/images/Ryan-Knight.png" alt="">
                                <h5>Ryan Knight</h5>
                            </div>
                        </div>
                        <div class="col-md-4 text-center student">
                            <div class="student-counting">
                                <h5>Absolutely loved this course!! I am very happy with my experience in completing this online course with New Skills Academy. It was an easy process and their website is very easy to get around. No stress over time limits. I worked through the course at my own pace, and from the comforts of my own home.</h5>
                                <div><img src="<?= SITE_URL ?>assets/images/speach-new.png" alt=""></div>
                            </div>
                            <div class="mt-5 students-cont">
                                <img src="<?= SITE_URL ?>assets/images/Natalie-Rogerson.png" alt="">
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
            <div class="container lerning-container">
                <div class="accordion" id="accordionFaqs">
                    <?php
                    $questions = ORM::for_table("faqs")->where("type", "Subscriptions")->order_by_desc("id")->find_many();

                    foreach($questions as $question) {

                        ?>
                        <div class="card">
                            <div class="card-header" id="headingOne<?= $question->id ?>">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left accordion-button collapsed" type="button" data-toggle="collapse" data-target="#collapseOne<?= $question->id ?>" aria-expanded="false" aria-controls="collapseOne<?= $question->id ?>">
                                        <?= $question->question ?>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseOne<?= $question->id ?>" class="collapse" aria-labelledby="headingOne<?= $question->id ?>" data-parent="#accordionFaqs">
                                <div class="card-body">
                                    <?= str_replace("700 courses", $courseCount.' courses', $question->answer) ?>
                                </div>
                            </div>
                        </div>
                        <?php

                    }
                    ?>
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