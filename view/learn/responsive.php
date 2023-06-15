<?php

$pageTitle = "Learn";
include BASE_PATH . 'header-jcp.php';

$courses = $this->controller->getCoursesSelect();
$jobCentres = $this->controller->getJobCentresSelect();
?>
    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--page title-->
        <section class="course-title-learn" id="free-online">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-md-6 mt-5 learn">
                        <h3>Improve your career prospects</h3>
                        <h1 class="mt-md-4 prospects">TAKE A <span class="online-course">FREE ONLINE COURSE</span></h1>


                        <h4 class="mt-md-4 mt-4 mb-4" > 800+ CPD approved courses to choose from</h4>
                        <a class="btn btn-primary access-button pt-3 pb-3 " href="#course-learn">GET ACCESS NOW</a>
                    </div>
                     <div class="col-6 ">
                            <img src="<?= SITE_URL ?>assets/images/free-online-banner.png" alt="">
                    </div>
                </div>
            </div>
        </section>

        <!--Page Content-->
        <section class="staff-learning">
            <div class="container learn-container">
                <div class="row text-center">
                    <div class="col-12 mt-5 career-enhancing">
                        <h1>Upgrade Your CV with one of our Career Enhancing courses</h1>
                    </div>
                </div>
                <div class="container lerning-container text-center">
                    <div class="col-md-12 mt-5 mb-5">
                        <div class="row mt-5">
                            <div class="col-md-6 p-3 career-course">
                                <div class="row ">
                                    <div class="col-4 col-md-3  text-right">
                                        <img src="<?= SITE_URL ?>assets/images/free-course-new.png" alt="">
                                    </div>
                                    <div class=" col-8 col-md-8 pt-md-3">
                                        <h5><b>Get a FREE Course</b></h5>
                                        <h6>Through our Partnership with JCP you can now study online certified courses FREE of charge.</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 p-3 career-course">
                                <div class="row">
                                    <div class="col-4 col-md-3 text-right">
                                        <img src="<?= SITE_URL ?>assets/images/800-courses-new.png" alt="">
                                    </div>
                                    <div class="col-8 col-md-8 pt-md-3">
                                        <h5><b>Over 800 courses to choose from</b></h5>
                                        <h6>Courses available in areas such as Admin, Animal Care, IT, Healthcare, Hospitality and Fitness.</h6>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-6 p-3 career-course">
                                <div class="row">
                                    <div class="col-4 col-md-3 text-right">
                                        <img src="<?= SITE_URL ?>assets/images/CPD-new.png" alt="">
                                    </div>
                                    <div class="col-8 col-md-8 pt-md-3">
                                        <h5><b>CDP Approved Learning</b></h5>
                                        <h6>All our courses are CPD certified and you will receive a certificate on completion.</h6>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-6 p-3 career-course">
                                <div class="row">
                                    <div class="col-4 col-md-3 text-right">
                                        <img src="<?= SITE_URL ?>assets/images/tutor-support-new.png" alt="">
                                    </div>
                                    <div class="col-8 col-md-8 pt-md-3">
                                        <h5><b>Tutor Support</b></h5>
                                        <h6>Courses available in areas such as Admin, Animal Care, IT, Healthcare, Hospitality and Fitness.</h6>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
           
        </section>
        <section class="course-title-learn" id="course-learn">
            <div class="container free-course">
                <div class="row">
                    <div class="col-12 mt-5  ">
                        <h1>Get Access to Your Free Course Now</h1>
                        <h5>Complete the short form below to get started</h5>
                        <div class="col-12 col-md-10 offset-md-1 learn-form">
                            <form name="learnAccess" class="row mb-4 ">
                                <div class="col-md-6 mt-4">
                                    <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name*" required>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name*" required>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email Address*">
                                </div>
                                <div class="col-md-6 mt-4">
                                    
                                    <div class="sldropdown select">
                                        <select class="form-control select2" name="jobCenterID" required>
                                            <option value="">Select Job centre</option>
                                            <?php
                                                foreach ($jobCentres as $jobCentre) {
                                            ?>
                                                    <option value="<?= $jobCentre['id']; ?>"><?= $jobCentre['name']; ?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                 </div>
                                <div class="col-md-6 mt-4">
                                    <div class="sldropdown select">
                                        <select class="form-control select2" name="courseID" required>
                                            <option value="">Select Areas of Interest</option>
                                            <option>Animal Care</option>
                                            <option>Beauty</option>
                                            <option>Horticulture</option>
                                            <option>Hospitality</option>
                                            <option>Fashion/Interior Design</option>
                                            <option>Self Employed Careers</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-4">
                                    <textarea class="form-control text-area" aria-label="With textarea" name="comment" placeholder="Comments (if you know which specific courses you would like access to please let us know here)"></textarea>
                                </div>
                                <div class="col-md-12 mt-4">
                                    <input type="submit" name="submit" class="btn btn-secondary btn-lg extra-radius" value="Send Request">
                                </div>
                            </form>
                            <?php
                                $this->renderFormAjax("course", "learnAccess", "learnAccess", "#returnStatussdf", false, true);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="question-learning" id="Frequently-ques">
            <div class="container learn-container text-center">
                <div class="row">
                    <div class="col-12 mt-5">
                        <h1>Frequently Asked Questions</h1>
                    </div>
                </div>
            </div>
            <div class="container lerning-container text-center">
                <div class="col-md-12 mt-5 mb-5">
                    <div class="row mt-5">
                        <div class="col-md-5 offset-md-1  question-asked">
                            <h5>Which courses can I study? </h5>
                            <P>You can choose from any of the individual courses on our website. Just let us know which course you want to study by filling in the form.</P>
                        </div>
                        <div class="col-1"></div>
                        <div class="col-md-5 question-asked">
                            <h5>Is there any charge? </h5>
                            <P>No, as long as you are eligible you will receive your course for free.</P>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-5 offset-md-1 question-asked">
                            <h5>Who is eligible for a free course? </h5>
                            <P>Anyone who is registered at one of the listed Job Centres can apply for a free course. Once validated we will create an account and add your specified course to it.</P>
                        </div>
                        <div class="col-1"></div>
                        <div class="col-md-5 question-asked">
                            <h5>Do I get a certificate? </h5>
                            <P>Yes. All courses come with an end of course certification.</P>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-5 offset-md-1 question-asked">
                            <h5>Can an employer validate my qualification? </h5>
                            <P>Yes, your certification can be validated anytime via our website.</P>
                        </div>
                        <div class="col-1"></div>
                        <div class="col-md-5 question-asked">
                            <h5>How long do I get access to my course for?</h5>
                            <P>You will get lifetime access to your free course.</P>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="question-learning" id="studentcount">
            <div class="container learn-container text-center">
                <div class="row">
                    <div class="col-12 mt-5">
                        <h1>Join Over 800k Students & Counting...</h1>
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
                            <div class=" mt-5 students-cont" >
                                <img src="<?= SITE_URL ?>assets/images/Amy-Hewson.png" alt="">
                                <h5>Amy Hewson</h5>
                            </div> 
                        </div>
                        <div class="col-md-4 text-center student">
                            <div class="student-counting">
                                <h5>The courses are absolutely brilliant for someone like me that has a busy working life – the modules are chunked into bite sized pieces and are really simple and straightforward to use. It is easy to dip in and out of and pick up from where you left off. It really has proved excellent value for money!</h5>
                                <div><img src="<?= SITE_URL ?>assets/images/speach-new.png" alt=""></div>
                            </div>
                            <div class=" mt-5 students-cont" >
                                <img src="<?= SITE_URL ?>assets/images/Ryan-Knight.png" alt="">
                                <h5>Ryan Hewson</h5>
                            </div>
                        </div> 
                        <div class="col-md-4 text-center student">
                            <div class="student-counting">
                                <h5>Absolutely loved this course!! I am very happy with my experience in completing this online course with New Skills Academy. It was an easy process and their website is very easy to get around. No stress over time limits. I worked through the course at my own pace, and from the comforts of my own home. Very informative and loved the worksheet after each module which gave me a chance to really think about the work needed for the final test and gave me a chance to really understand each module. I will definitely be putting what I have learned from the course into practice within our office.</h5>
                                <div><img src="<?= SITE_URL ?>assets/images/speach-new.png" alt=""></div>
                            </div>
                            <div class="mt-5 students-cont" >
                                <img src="<?= SITE_URL ?>assets/images/Natalie-Rogerson.png" alt="">
                                <h5>Natalie Rogerson</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        

       

    </main>
    <!-- Main Content End -->
    <div class="modal fade" id="learnAccess" tabindex="-1" role="dialog" aria-labelledby="learnAccess" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border: none;">
            <div class="modal-body">
                <a class="btn-close" data-dismiss="modal">X</a>
                <div class="modal-learn p-md-5">
                    <h1 class="mb-3"><b>Thanks <span id="username"></span></b></h1>
                    <p> Your request has been passed to one of our course advisors. They will verify your details with your job Center Plus and contact
                        you with your free course, normally within 48 hours.</p>
                    <p>If you need any further assistance please do not hesitate to contact us.</p>
                    
                    <p class="mt-md-5 mb-md-4">
                        <img src="<?= SITE_URL ?>assets/images/logo-blue.png" alt="">
                        <!-- <img src="assets/images/JCP-logo-new.png" alt=""> -->
                    </p>
                    <p>Free courses provided by New Skill Academy working in association with Job Centre plus</p>
                    <p>Your welcome email will come from <a href="mailTo:support@staffskillsacademy.co.uk">support@staffskillsacademy.co.uk</a>. Please whitelist this email address</p>
            </div>
            </div>
        </div>
    </div>
</div>
<?php include BASE_PATH . 'footer.php';?>