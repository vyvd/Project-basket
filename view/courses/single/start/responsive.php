<?php
$this->setControllers(array("courseLanguage",'courseModule'));

$course = $this->controller->getSingleCourse();

if($course->cudooCourseIDs != "") {
    // this is a language course, redirect to cudoo
    $account = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);

    $this->courseLanguage->access($account, $course);
    exit;

}


$item = $this->controller->getCourseAssigned($course);

$blogs = $this->controller->getAllCourseBlogs($course);
if($item->id == "") {
    header('Location: '.SITE_URL.'dashboard');
    exit;
}

if($item->currentModule == "" || $item->currentModule == "0") {
    $item->currentModule = "1";
}

if($course->allowSecondaryName == 1 && $item->secondaryName == "") { ?>
    <script>
        setSecondaryName = setInterval(function () {
            if(window.jQuery) {
                let name = prompt("Please enter the name you'd like on the certificate:", "");
                    jQuery.ajax({
                    type: "POST",
                    url: "<?= SITE_URL ?>ajax?c=course&a=assign-secondary-name",
                    dataType: "json",
                    data: {
                        'assignedCourseID': <?= $item->id ?>,
                        'name': name,
                    },
                });

                clearInterval(setSecondaryName);

            }}, 1000);
    </script>
<?php }

if($this->get["start"] == "true") {
    // actually start the course
    $this->controller->startCourse($course);
}
$courseModules = $this->controller->courseModules($course);

$pageTitle = $course->title;
$completedModules = $this->courseModule->getCompletedModules($course->id, true);
$currentAssigned = $this->controller->getCourseAssigned($course);
include BASE_PATH . 'account.header.php';


?>
    <section class="page-title with-nav">
        <div class="container">

            <?php if($course->childCourses == "") { ?>
                        <?php
                        if($this->controller->checkCourseStarted($course) == false) {
                            $continueModuleUrl = SITE_URL.'start/'.$course->slug.'?start=true';
                            ?>
                            <a href="<?= $continueModuleUrl ?>" class="mainBtn">Start Now</a>
                        <?php } else {
                            $continueModuleUrl = $this->courseModule->getContinueModuleUrl($item);
                            ?>
                            <a href="<?= $continueModuleUrl ?>" class="mainBtn">Continue Course</a>
                        <?php } ?>
            <?php } else {
                ?>
                <a href="<?= SITE_URL ?>dashboard/courses" class="mainBtn">All Courses</a>
                <?php
            } ?>

            <h1 <?php if($course->childCourses != "") { ?>class="bundleCourseTitle"<?php } ?>><?= $course->title ?></h1>

            <?php
            $studyGroupUrl = $this->getSetting("study_group_url");
            if($course->childCourses == "") {
            ?>
                <ul class="nav navbar-nav inner-nav nav-tabs">
                    <li class="nav-item link">
                        <a class="nav-link active show" href="#modules" data-toggle="tab">Modules</a>
                    </li>
                    <li class="nav-item link ">
                        <a class="nav-link" href="#myProgress" data-toggle="tab">My Progress</a>
                    </li>
                    <li class="nav-item link">
                        <a class="nav-link" href="#courseNotes" data-toggle="tab">Course Notes</a>
                    </li>

                    <li class="nav-item link">
                        <a class="nav-link" href="#faq" data-toggle="tab">Course FAQ's</a>
                    </li>
                    <li class="nav-item link">
                        <a class="nav-link" href="#resources" data-toggle="tab">Course Resources</a>
                    </li>
                    <?php if(count($blogs) >= 1) {?>
                        <li class="nav-item link">
                            <a class="nav-link" href="#additionalReading" data-toggle="tab">Additional Resources</a>
                        </li>
                    <?php }?>
                    <?php if($studyGroupUrl != "") {?>
                        <li class="nav-item link">
                            <a class="nav-link" href="#studyGroup" data-toggle="tab" style="background: #259cc0;color: #fff;">My Study Group</a>
                        </li>
                    <?php }?>
                </ul>
            <?php } ?>

        </div>
    </section>

    <div class="tab-content container">

    <?php if($course->childCourses != "") { ?>
        <div class="popularCourseboxes" style="margin-top:55px;">
            <div class="row">
                <?php

                    // show child courses if this course has them

                    foreach(ORM::for_table("coursesAssigned")->where("bundleID", $item->id)->find_many() as $child) {

                        $courseChild = ORM::for_table("courses")->find_one($child->courseID);


                        $btnLabel = "Access";
                        if($child->percComplete == "100") {
                            $btnLabel = "Revisit";
                        }
                        ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="category-box">
                                <div class="img" style="background-image:url('<?= $this->controller->getCourseImage($courseChild->id, "large") ?>');"></div>
                                <div class="Popular-title-top">
                                    <div class="progress">
                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: <?= number_format($child->percComplete) ?>%;<?php if($child->percComplete == "0") { ?>color:#259cc0;<?php } ?>" aria-valuenow="<?= number_format($child->percComplete) ?>" aria-valuemin="0" aria-valuemax="100"><?= number_format($child->percComplete) ?>%</div>
                                    </div>
                                </div>
                                <div class="Popular-title-bottom">
                                    <h5><?= $courseChild->title ?></h5>
                                    <?php
                                    if($child->completed == "1") {
                                        ?>
                                        <div class="row courseBtnRow">
                                            <div class="col-6">
                                                <a class="btn btn-outline-light" href="<?= SITE_URL ?>start/<?= $courseChild->slug ?>"><?= $btnLabel ?></a>
                                            </div>
                                            <div class="col-6">
                                                <a class="btn btn-outline-light" href="<?= SITE_URL ?>ajax?c=certificate&a=cert-pdf&id=<?= $child->id ?>" target="_blank"><i class="fa fa-file"></i> Certificate</a>
                                            </div>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <a class="btn btn-outline-light" href="<?= SITE_URL ?>start/<?= $courseChild->slug ?>"><?= $btnLabel ?></a>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php

                    }

                ?>
            </div>
        </div>
    <?php } ?>
        <?php
            $modArray = array();
            $count = 1;
            foreach($courseModules as $mod) {
                $modArray[$count] = $mod->id;
                $count ++;
            }
            $key = array_search($item->currentModule, $modArray);
        ?>
        <!--Modules-->
        <div id="modules" class="tab-pane faq active show">
            <?php include_once('includes/modules.php'); ?>
        </div>

        <!--My Progress-->
        <div id="myProgress" class="tab-pane helpVideos fade">
            <?php include_once('includes/my_progress.php'); ?>
        </div>

        <!--Course Notes-->
        <div id="courseNotes" class="tab-pane fade">
            <?php include_once('includes/course_notes.php'); ?>
        </div>

        <!--Additional Reading-->
        <?php if(count($blogs) >= 1) {?>
            <div id="additionalReading" class="tab-pane fade pt-5">
                <?php include_once('includes/additional_reading.php'); ?>
            </div>
        <?php }?>

        <!--FAQ-->
        <div id="faq" class="tab-pane fade faq">
            <?php include_once('includes/faq.php'); ?>
        </div>

        <!--Course Resources-->
        <div id="resources" class="tab-pane fade">
            <?php include_once('includes/course_resources.php')?>
        </div>

        <?php
        if($studyGroupUrl != "") {
            ?>
            <!--study group-->
            <div id="studyGroup" class="tab-pane fade">
                <div class="form-row">
                    <div class="col-12 col-md-12">

                        <a href="<?= $studyGroupUrl ?>" target="_blank">
                            <img src="<?= SITE_URL ?>assets/images/study-group-block.png" width="100%" />
                        </a>

                        <div class="white-rounded studyGroupPoints">

                            <div class="point">
                                <span class="icon">
                                    <i class="far fa-comment-lines"></i>
                                </span>
                                <span class="content">
                                    Chat with students on your course
                                </span>
                            </div>

                            <div class="point">
                                <span class="icon">
                                    <i class="far fa-headset"></i>
                                </span>
                                <span class="content">
                                    Get support and advice
                                </span>
                            </div>

                            <div class="point">
                                <span class="icon">
                                    <i class="far fa-star"></i>
                                </span>
                                <span class="content">
                                    Exclusive access for New Skills Academy students
                                </span>
                            </div>

                            <div class="point">
                                <span class="icon">
                                    <i class="far fa-gift"></i>
                                </span>
                                <span class="content">
                                    Win study group prizes and be the first to hear about new offers
                                </span>
                            </div>

                        </div>

                        <a href="<?= $studyGroupUrl ?>" target="_blank" class="mainBtn">
                            Join Now
                        </a>

                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>

<script>
    setInterval(function(){
        $("#returnStatus").load('<?= SITE_URL ?>ajax?c=courseTimeProgress&a=track&id=<?= $currentAssigned->id ?>');
    }, 60000);
</script>

<?php include BASE_PATH . 'account.footer.php';?>
