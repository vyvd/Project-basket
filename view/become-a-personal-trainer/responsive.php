<?php
// create landing page generator in the future
$this->setControllers(array("course"));
$css = array("animate.min.css", "landing.css");

$pageTitle = "Become A Personal Trainer";
include BASE_PATH . 'header.php';

?>

<style>
    header {
        display:none !important;
    }
</style>

    <main role="main" class="regular ncfe-landing">

        <div id="hero">
            <div class="container wider-container">

                <a href="<?= SITE_URL ?>">
                    <img src="<?= SITE_URL ?>assets/images/logo-white.png" class="logo" />
                </a>

                <div class="row">
                    <div class="col-12 col-lg-7">

                        <h1 class="wow fadeInUp" data-wow-delay="0.2s">Start your career as a Personal Trainer for <u><strong>only</strong> £25</u></h1>
                        <p class="wow fadeInUp" data-wow-delay="0.4s">With one of the UK's most trusted training providers</p>

                        <a href="<?= SITE_URL ?>checkout?courseID=858" class="cta wow pulse" data-wow-delay="3s">
                            Start For Just £25
                        </a>

                    </div>
                    <div class="col-12 col-lg-5" style="position:relative;">
                        <img src="<?= SITE_URL ?>assets/images/fitness-landing/star-white.png" class="starBG" />
                        <img src="<?= SITE_URL ?>assets/images/fitness-landing/personal-trainer-course.png" alt="Become a Personal Trainer" class="wow fadeInDown" style="z-index:9;" />
                    </div>
                </div>

            </div>
        </div>

        <div class="courseUpsellOne">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-lg-7">

                        <h2 class="wow fadeInUp">
                            Start your Level 3 Diploma in Gym Instructing and Personal Training <span style="color:#6ADBAA">for
                                as little as £25</span></h2>

                        <p>New Skills Fitness Academy is a leading training provider within the fitness sector with years of online training expertise.</p>

                        <p>Our Level 3 Diploma in Gym Instructing and Personal Training is nationally
                            recognised and endorsed by CIMSPA, allowing you to be registered as a Level 3
                            Personal Trainer.</p>

                        <p>Our course is fully online, allowing you to study all theoretical knowledge at
                            home and in your own time.</p>

                        <p>All you have to pay to start your learning is £25 per month for every month you
                            are registered to the course. You are free to cancel at any time. It is completely
                            up to you how quickly you move through each module.  At the end of each
                            module, you will submit your workbook to your tutor/assessor to be marked.
                            When submitting your end of unit workbooks, you will be asked to pay £50 for
                            each unit you submit (there are 12 units in total within this course), totalling
                            £600 to complete the course. </p>

                        <a href="<?= SITE_URL ?>checkout?courseID=858" class="cta blue wow pulse" data-wow-delay="3s">
                            Start Now
                        </a>

                    </div>
                    <div class="col-12 col-lg-5" style="position:relative;text-align:center;">
                        <img src="<?= SITE_URL ?>assets/images/fitness-landing/star.png" class="starBG" style="bottom:auto;" />
                        <img src="<?= SITE_URL ?>assets/images/fitness-landing/gym-instructor-courses.png" alt="gym instructor courses" style="z-index:9;position:relative;" />
                    </div>
                </div>
            </div>
        </div>

        <div class="courseUpsellTwo">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-lg-12">

                        <h2 class="wow fadeInUp">Pay as you learn</h2>

                        <h3 class="wow fadeInUp">Level 2 Gym Instructor & Level 3 Personal Trainer Diploma</h3>

                        <p class="highlight wow fadeInUp">
                            You pay only £25 per month to access our learning portal and course materials.
                            You then only pay £50 per unit assessment if/when you are ready.
                        </p>

                        <h4 class="wow fadeInUp">Level 2 Gym Instructor Course</h4>



                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="unitItem">
                            <div class="title">
                                Unit 01 Anatomy and Physiology for Exercise (T/617/4001)
                            </div>

                            <p>This unit covers the knowledge a Gym Instructor needs of anatomy and physiology, to
                                enable effective exercise/activity programming for a range of clients.</p>

                            <p>Assessment Fee: £50</p>

                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="unitItem">
                            <div class="title">
                                Unit 02 Maximising the Customer Experience in A Gym Environment
                                (A/617/4002)
                            </div>

                            <p>This unit aims to provide learners with the knowledge, skills and understanding to build
                                and maintain relationships with customers in a gym environment.</p>

                            <p>Assessment Fee: £50</p>

                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="unitItem">
                            <div class="title">
                                Unit 03 Supporting Client Health and Well-being (D/617/4008)
                            </div>

                            <p>This unit covers the knowledge a Gym Instructor needs to promote a healthy lifestyle and
                                to facilitate behaviour change and adherence to exercise. Learners will also cover the
                                prevention and management of common health conditions.</p>

                            <p>Assessment Fee: £50</p>

                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="unitItem">
                            <div class="title">
                                Unit 04 Conducting Client Consultations and Gym Inductions (F/617/4003)
                            </div>

                            <p>This unit provides learners with the knowledge, skills and understanding to conduct
                                consultations, fitness assessments and gym inductions with customers and clients.</p>

                            <p>Assessment Fee: £50</p>

                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="unitItem">
                            <div class="title">
                                Unit 05 Planning and Reviewing Gym-based Exercise Programmes (R/617/4006)
                            </div>

                            <p>This unit covers the knowledge, understanding and skills a learner needs to plan, tailor
                                and review gym-based exercise programmes.</p>

                            <p>Assessment Fee: £50</p>

                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="unitItem">
                            <div class="title">
                                Unit 06 Instructing and Supervising Gym-based Exercise Programmes
                                (Y/617/4007)
                            </div>

                            <p>This unit covers the skills and knowledge a Gym Instructor needs to deliver and supervise
                                gym-based exercise sessions to clients.</p>

                            <p>Assessment Fee: £50</p>

                        </div>
                    </div>
                    <div class="col-12">
                        <a class="expand" href="javascript:;" onclick="$('#level3').slideToggle();">
                            See Level 3 Personal Training Modules
                        </a>
                    </div>
                </div>

                <div class="row" id="level3" style="display:none;">
                    <div class="col-12 col-lg-12">

                        <h4 class="wow fadeInUp">Level 3 Personal Training Certificate</h4>

                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="unitItem">
                            <div class="title">
                                Unit 07 Applied Anatomy and Physiology for Activity, Health and Fitness
                                (D/617/1707)
                            </div>

                            <p>This unit covers the knowledge a Personal Trainer needs around anatomy, physiology,
                                biomechanics and kinesiology, to enable effective exercise/activity programming for a
                                range of clients.</p>

                            <p>Assessment Fee: £50</p>

                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="unitItem">
                            <div class="title">
                                Unit 08 Client Motivation and Lifestyle Management (H/617/1708)
                            </div>

                            <p>This unit covers the knowledge a Personal Trainer needs regarding lifestyle management,
                                client motivation and health and well-being, to be able to develop and implement
                                strategies to encourage long-term adherence to positive lifestyle practices.</p>

                            <p>Assessment Fee: £50</p>

                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="unitItem">
                            <div class="title">
                                Unit 09 Programming Personal Training Sessions (K/617/1709)
                            </div>

                            <p>This unit covers the knowledge and skills a learner needs to design, manage and adapt a
                                personal training programme with adults of all ages.</p>

                            <p>Assessment Fee: £50</p>

                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="unitItem">
                            <div class="title">
                                Unit 10 Delivering Personal Training Sessions (D/617/1710)
                            </div>

                            <p>This unit covers the skills and knowledge a Personal Trainer needs to deliver exercise and
                                physical activity training sessions to adults of all ages.</p>

                            <p>Assessment Fee: £50</p>

                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="unitItem">
                            <div class="title">
                                Unit 11 Nutrition to Support a Physical Activity Programme (H/617/1711)
                            </div>

                            <p>This unit covers the knowledge and skills a Personal Trainer needs to be able to apply the
                                principles of nutrition and recommend current healthy eating guidelines to individuals.</p>

                            <p>Assessment Fee: £50</p>

                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="unitItem">
                            <div class="title">
                                Unit 12 Business Acumen for Personal Trainers (K/617/1712)
                            </div>

                            <p>This unit covers the knowledge and skills a Personal Trainer needs to grow a successful
                                personal training business. This includes the use of technology to support the personal
                                training business.</p>

                            <p>Assessment Fee: £50</p>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <section class="staff-learning">
            <div class="container learn-container">
                <div class="row text-center">
                    <div class="col-12 mt-5 career-enhancing">
                        <h2 class="section-title wow fadeIn ncfe-heading-title">6 Great Reasons To Train With Us</h2>
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
                                        <h5><b>Start for only £25</b></h5>
                                        <h6>No long-term commitment. Cancel anytime.</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 p-3 career-course wow fadeIn">
                                <div class="row">
                                    <div class="col-4 col-md-3 text-right">
                                        <div class="icon">
                                            <i class="far fa-clipboard-list-check"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 col-md-8 pt-md-3">
                                        <h5><b>Pay As You Learn</b></h5>
                                        <h6>Pay for your assessments only if you decide to take them. Assessment marking costs £40 per unit (there are 12 units).</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 p-3 career-course wow fadeIn">
                                <div class="row">
                                    <div class="col-4 col-md-3 text-right">
                                        <div class="icon">
                                            <i class="far fa-university"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 col-md-8 pt-md-3">
                                        <h5><b>Fully Accredited Course</b></h5>
                                        <h6>Certificate sent to you free of charge on completion.</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 p-3 career-course wow fadeIn">
                                <div class="row">
                                    <div class="col-4 col-md-3 text-right">
                                        <div class="icon">
                                            <i class="far fa-stopwatch"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 col-md-8 pt-md-3">
                                        <h5><b>Go At Your Own Pace</b></h5>
                                        <h6>Get qualified in as fast as 4-6 months or take as long as you need.</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 p-3 career-course wow fadeIn">
                                <div class="row">
                                    <div class="col-4 col-md-3 text-right">
                                        <div class="icon">
                                            <i class="far fa-books"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 col-md-8 pt-md-3">
                                        <h5><b>Access Our Full Library</b></h5>
                                        <h6>Get access to over 800 CPD courses.</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 p-3 career-course wow fadeIn">
                                <div class="row">
                                    <div class="col-4 col-md-3 text-right">
                                        <div class="icon">
                                            <i class="far fa-wifi"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 col-md-8 pt-md-3">
                                        <h5><b>Flexible Study</b></h5>
                                        <h6>Courses delivered completely online. Learn when best suits you.</h6>
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

        <section class="howItWorks">

            <div class="container wider-container">
                <h2 class="ncfe-heading-title">How it works</h2>

                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="item">
                            <div class="count">
                                1.
                            </div>
                            <div class="content">
                                <p><strong>Sign-up for only £25</strong></p>
                                <p class="desc">Get instant access to all the course material and start studying at your own pace.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="item">
                            <div class="count">
                                2.
                            </div>
                            <div class="content">
                                <p><strong>Complete your assessments</strong></p>
                                <p class="desc">Complete the assessments as you study. Our tutors are here to help you along the way.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="item">
                            <div class="count">
                                3.
                            </div>
                            <div class="content">
                                <p><strong>Submit your assessments</strong></p>
                                <p class="desc">Submit one assessment at a time or all at once. It’s your choice. Pay just £50 marking fee for each assessment.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="item">
                            <div class="count">
                                4.
                            </div>
                            <div class="content">
                                <p><strong>Receive your certification</strong></p>
                                <p class="desc">Once you have passed all 12 units you will receive your Cimpsa / NCFE Certificate. It’s time to start your career.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </section>

        <br />
        <br />

        <section class="cta sticky">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <h4>Start your career as a personal trainer today for just £25</h4>
                    </div>
                    <div class="col-12 col-lg-4">
                        <a href="<?= SITE_URL ?>checkout?courseID=858">
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
                        <h2 class="section-title wow fadeIn ncfe-heading-title">More about the course</h2>

                        <iframe src='https://player.vimeo.com/video/487266425' style="width: 100%"  height="670" frameborder='0' allow='autoplay; fullscreen' allowfullscreen></iframe>

                    </div>
                </div>
            </div>
        </section>

        <section class="question-learning" id="studentcount">
            <div class="container learn-container text-center">
                <div class="row">
                    <div class="col-12 mt-5">
                        <h2 class="section-title wow fadeIn ncfe-heading-title">Join over 800k students...</h2>
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
                                <h5>I am on the 4th module and so far I have to say I am really enjoying it. I have already learnt so much that I never knew before about the human body. I started this course because I am currently getting into health and fitness to make a better life and future for myself and so I know what is right and wrong when I exercise or with what I am putting into my body. The layout is really easy to understand and follow, the questions at the end of each module is really helpful too as refreshes what you have already read and learnt. Overall, I am pretty addicted to the course and getting through it quick. Definitely worth the money.</h5>
                                <div><img src="<?= SITE_URL ?>assets/images/speach-new.png" alt=""></div>
                            </div>
                            <div class="mt-5 students-cont">
                                <img src="<?= SITE_URL ?>assets/images/Michelle-Paddick-cc7a012ab0added3a6fa748559e7837b.jpeg" alt="">
                                <h5>Michelle Paddick</h5>
                            </div>
                        </div>
                    </div>
                    <br />
                    <br />
                    <br />
                </div>
            </div>
            <br />
            <p class="text-center">
                <a href="https://uk.trustpilot.com/review/newskillsacademy.co.uk" target="_blank">
                    <img src="<?= SITE_URL ?>assets/images/fitness-landing/tp-landing.png" alt="trustpilot 5 stars" style="margin-top: -70px;margin-bottom: 50px;" />
                </a>
            </p>
        </section>

        <section class="question-learning" id="Frequently-ques">
            <div class="container learn-container text-center">
                <div class="row">
                    <div class="col-12 mt-5">
                        <h2 class="section-title wow fadeIn ncfe-heading-title">Frequently Asked Questions</h2>
                    </div>
                </div>
            </div>
            <div class="container lerning-container">

                <div class="accordion" id="accordionFaqs">
                    <?php
                    $questions = ORM::for_table("faqs")->where("type", "Become A Fitness Instructor")->order_by_desc("id")->find_many();

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
                                    <?= $question->answer ?>
                                </div>
                            </div>
                        </div>
                        <?php

                    }
                    ?>
                </div>

            </div>
        </section>

        <section id="fitnessBrands">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <h2 class="ncfe-heading-title">Working with some of the UK's best fitness brands</h2>

                        <div class="logos">
                            <div class="item">
                                <img src="<?= SITE_URL ?>assets/images/fitness-landing/ncfe.png" />
                            </div>
                            <div class="item">
                                <img src="<?= SITE_URL ?>assets/images/fitness-landing/cpd.png" />
                            </div>
                            <div class="item">
                                <img src="<?= SITE_URL ?>assets/images/fitness-landing/uklrp.png" />
                            </div>
                            <div class="item">
                                <img src="<?= SITE_URL ?>assets/images/fitness-landing/other.png" />
                            </div>
                            <div class="item">
                                <img src="<?= SITE_URL ?>assets/images/fitness-landing/xcelerate.png" />
                            </div>
                            <div class="item">
                                <img src="<?= SITE_URL ?>assets/images/fitness-landing/fit-first.png" />
                            </div>
                            <div class="item">
                                <img src="<?= SITE_URL ?>assets/images/fitness-landing/ypt.png" />
                            </div>
                        </div>

                    </div>
                    <div class="col-12 col-lg-4" style="position:relative;">
                        <img src="<?= SITE_URL ?>assets/images/fitness-landing/star.png" class="starBG" style="bottom:auto;" />
                        <img src="<?= SITE_URL ?>assets/images/fitness-landing/pay-montly-personal-trainer-course.png" alt="pay monthly personal trainer course" width="100%" class="mainImg wow fadeInRight" style="position:relative;z-index:9;" />
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


    </script>

<?php include BASE_PATH . 'footer.php'; ?>