<?php
$css = array("staff-training.css");
$pageTitle = "Staff Training";

$breadcrumb = array(
    "Staff Training" => ""
);

include BASE_PATH . 'header.php';
?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--page title-->
        <section class="course-title">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="section-title text-left">Upskill Your Staff</h1>
                    </div>
                </div>
            </div>
        </section>

        <!--Page Content-->
        <section class="staff-training">
            <div class="container wider-container">
                <div class="row">

                    <div class="col-12 col-md-6 col-lg-6 staff-texts">
                        <h2 class="heading-underlined">Tailor-made Training Packages</h2>
                        <p><img class="alignright size-full wp-image-50216" src="https://newskillsacademy.co.uk/wp-content/uploads/2018/04/tailor.png" sizes="(max-width: 90px) 100vw, 90px" srcset="https://newskillsacademy.co.uk/wp-content/uploads/2018/04/tailor.png 90w, https://newskillsacademy.co.uk/wp-content/uploads/2018/04/tailor-75x75.png 75w" alt="Training Package" width="90" height="90" />Ensuring your staff remain fully trained is essential to the overall success and growth of your business.</p>
                        <p>Whether you have 2 or 20,000 staff members, New Skills Academy, in association with our sister company&nbsp;<strong>Staff Skills Training</strong>&nbsp;have training packages to suit your business.</p>
                        <p>With over 600 courses available, you can customise the training package you put together for your staff.</p>
                        <h2 class="heading-underlined">Tracking & Reporting</h2>
                        <p><img class="alignright size-full wp-image-50218" src="https://newskillsacademy.co.uk/wp-content/uploads/2018/04/dash.png" sizes="(max-width: 90px) 100vw, 90px" srcset="https://newskillsacademy.co.uk/wp-content/uploads/2018/04/dash.png 90w, https://newskillsacademy.co.uk/wp-content/uploads/2018/04/dash-75x75.png 75w" alt="Dashboard" width="90" height="90" />Our advanced dashboard allows you to track your staff&rsquo;s progress every step of the way.</p>
                        <p>You can see metrics such as overall progress and time logged in. Once completed, you can download your staff member&rsquo;s certificate.</p>
                        <p>All qualifications gained by your staff can be validated by you or your customers 24/7 via our website.</p>
                        <h2 class="heading-underlined">Fully Accredited Courses</h2>
                        <p>Our students&rsquo; reputation matters, which is why all of our online courses have been reviewed and certified in partnership with qualified industry experts. For added security and peace of mind, our courses have been approved by CPD and various other awarding bodies.</p>
                        <h2 class="heading-underlined">Reasons to choose Staff Skills Training</h2>
                        <ul class="learn-benefitu">
                            <li>One simple platform populated with over 600 CPD & RoSPA courses</li>
                            <li>Mental Health, Wellbeing, Hobbies & Lifestyle courses included</li>
                            <li>Unlimited use of our entire library</li>
                            <li>One price for 12 months access</li>
                            <li>Create your own bespoke courses</li>
                            <li>New courses added each month</li>
                            <li>Train, Retain & Reward your team</li>
                        </ul>
                        <H2 class="heading-underlined">Trusted By...</H2>

                        <img src="<?= SITE_URL ?>assets/images/staffskills-client-list.jpeg" alt="trusted by" style="max-width:100%" />

                    </div>

                    <div class="col-12 col-md-6 col-lg-6 staff-form">
                        <h4>Complete the quick enquiry form below and one of our student advisors will be in touch shortly</h4>
                        <script type="text/javascript" src="https://form.jotform.com/jsform/211332423199350"></script>
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