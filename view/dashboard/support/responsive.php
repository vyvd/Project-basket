<?php
$css = array("dashboard.css");
$pageTitle = "Support";
include BASE_PATH . 'account.header.php';
?>
<?php
$userCourses = ORM::for_table("coursesAssigned")->where_null("bundleID")->where("accountID", CUR_ID_FRONT)->find_many();
?>

    <section class="page-title with-nav">
        <div class="container">
            <h1>Support</h1>
            <ul class="nav navbar-nav inner-nav nav-tabs">
                <li class="nav-item link">
                    <a class="nav-link active show" href="#submitATicket" data-toggle="tab">Submit a Ticket</a>
                </li>
                <li class="nav-item link">
                    <a class="nav-link" href="#courseFeedback" data-toggle="tab">Course feedback</a>
                </li>
                <li class="nav-item link ">
                    <a class="nav-link" href="#helpVideos" data-toggle="tab">Help Videos</a>
                </li>
                <li class="nav-item link">
                    <a class="nav-link" href="#faq" data-toggle="tab">FAQ's</a>
                </li>
            </ul>
        </div>
    </section>

<?php
include BASE_PATH . 'support-status-message.php';
?>

    <div class="tab-content container">
        <div id="submitATicket" class="tab-pane white-rounded profile-tabs active show">
            <h3>Create a Ticket</h3>
            <form name="newTicket">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="firstName">First name</label>
                        <input type="text" id="firstName" class="form-control" name="firstname" value="<?= $this->user->firstname ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="lastName">Last name</label>
                        <input type="text" id="lastName" class="form-control" name="lastname" value="<?= $this->user->lastname ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" name="email" value="<?= $this->user->email ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="contact">Contact Number</label>
                        <input type="text" id="contact" name="phone" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-12">
                        <label for="enquiry">Enquiry Details</label>
                        <textarea class="form-control" id="enquiry" name="message" rows="8"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Create Ticket</button>
            </form>
            <?php
            $this->renderFormAjax("support", "new-ticket", "newTicket");
            ?>
        </div>

        <div id="courseFeedback" class="tab-pane white-rounded profile-tabs">
            <h3>Course Feedback</h3>
            <form name="courseFeedback">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="firstName">First name</label>
                        <input type="text" id="firstName" class="form-control" name="firstname" value="<?= $this->user->firstname ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="lastName">Last name</label>
                        <input type="text" id="lastName" class="form-control" name="lastname" value="<?= $this->user->lastname ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" name="email" value="<?= $this->user->email ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="contact">Course you are studying</label>

                        <select class="form-control" name="course" required>
                            <option value="">Select Course</option>
                            <?php
                                if(count($userCourses) >= 1){
                                    foreach ($userCourses as $userCourse){
                                        $course = ORM::for_table('courses')->find_one($userCourse->courseID);
                            ?>
                                        <option value="<?= $course->title;?>"><?= $course->title;?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-12">
                        <label for="enquiry">Please let us know what you think of the course:</label>
                        <textarea class="form-control" id="enquiry" name="message" rows="8"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit Feedback</button>
            </form>
            <?php
            $this->renderFormAjax("support", "courseFeedback", "courseFeedback");
            ?>
        </div>

        <!--HELP VIDEOS-->
        <div id="helpVideos" class="tab-pane helpVideos fade">
            <div class="form-row">
                <div class="col-12 col-md-4 white-rounded">
                    <h3>Choose a Support Video</h3>
                    <?php
                    foreach(ORM::for_table("supportHelpVideos")->order_by_desc("whenCreated")->find_many() as $video) {
                        ?>
                        <div class="download-o">
                            <a href="javascript:;" onclick="showVideo(<?= $video->id ?>);">
                                <p><?= $video->title ?></p>
                            </a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="col-12 col-md-8">
                    <div class="video-outer" style="display:block;">
                        <style>
                            .singleVideo {
                                display:none;
                            }
                        </style>
                        <?php
                        $count = 0;
                        foreach(ORM::for_table("supportHelpVideos")->order_by_desc("whenCreated")->find_many() as $video) {

                            $vimeoID = substr(parse_url($video->vimeo, PHP_URL_PATH), 1);

                            ?>
                            <div class="singleVideo video<?= $video->id ?>" <?php if($count == 0) { ?>style="display:block"<?php } ?>>
                                <iframe src="https://player.vimeo.com/video/<?= $vimeoID ?>" width="100%" height="410" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                            </div>
                            <?php
                            $count ++;
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function showVideo(id) {

                $(".singleVideo").css("display", "none");
                $(".singleVideo.video"+id).css("display", "block");

            }
        </script>

        <!--FAQ-->
        <div id="faq" class="tab-pane fade faq">
            <div class="row">
                <h3>Frequently Asked Questions</h3>
                <div id="accordion" class="col-12">
                    <?php
                    foreach(ORM::for_table("supportFaqs")->find_many() as $faq) {
                        ?>
                        <div class="card">
                            <div class="card-header" id="heading<?= $faq->id ?>">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#question<?= $faq->id ?>" aria-expanded="false" aria-controls="question<?= $faq->id ?>">
                                        <?= $faq->question ?>
                                    </button>
                                </h5>
                            </div>
                            <div id="question<?= $faq->id ?>" class="collapse" aria-labelledby="heading<?= $faq->id ?>" data-parent="#accordion">
                                <div class="card-body">
                                    <?= $faq->answer ?>
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


<?php include BASE_PATH . 'account.footer.php';?>