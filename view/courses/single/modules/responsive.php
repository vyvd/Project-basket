<?php

$course = $this->controller->getSingleCourse();

$pageTitle = $course->title;
include BASE_PATH . 'header.php';

?>


    <div class="container-fluid" id="site-header">
        <div class="container">
            <div class="row">
                <div class="col dash-header" id="site-header-container">
                    <h1><?= $course->title ?></h1>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="breadcrumb">
        <div class="container">
            <div class="row">
                <div class="col-lg-9" id="breadcrumb-links">
                    <a href="<?= SITE_URL ?>">Home</a>
                    > <a href="<?= SITE_URL ?>start/<?= $course->slug ?>"><?= $course->title ?></a>
                    > Modules
                </div>
                <div class="col-lg-3" id="breadcrumb-progress">

                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="checkout">
        <div class="container">
            <div class="row">
                <div class="col-lg-9" id="start-course">
                    <form id="mobile-course-menu">
                        <select class="select-css">
                            <option>Dashboard</option>
                            <option>Continue Course</option>
                            <option>Course Progress</option>
                            <option>Course Notes</option>
                            <option>Course Modules</option>
                            <option>Assistance</option>
                            <option>Download Certificate</option>
                            <option>Edit Profile</option>
                            <option>Revisit Modules</option>
                            <option>Logout</option>
                        </select>
                    </form>

                    <div id="single-course-body">
                        <h2>Course Modules/Lessons</h2>
                        <div class="accordion" id="accordionModules">
                            <?php
                            $count = 1;
                            foreach($this->controller->courseModules($course) as $module) {
                                ?>
                                <div class="card">
                                    <div class="card-header <?php if($count == 1) { ?>active<?php } ?>" id="heading<?= $module->id ?>">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse<?= $module->id ?>" aria-expanded="true" aria-controls="collapse<?= $module->id ?>">
                                                <span><?= $count ?></span> <?= $module->title ?>
                                            </button>
                                        </h2>
                                    </div>

                                    <div id="collapse<?= $module->id ?>" class="collapse <?php if($count == 1) { ?>show<?php } ?>" aria-labelledby="heading<?= $module->id ?>" data-parent="#accordionModules">
                                        <div class="card-body">
                                            <?= $module->description ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $count ++;
                            }
                            ?>
                        </div>
                    </div>

                </div>
                <div class="col-lg-3" id="account-menu">
                    <div id="menu-block">
                        <ul>
                            <li><a href="#"><i class="far fa-home-lg"></i> Dashboard</a></li>
                            <li><a href="#"><i class="far fa-tv-alt"></i> Continue Course</a></li>
                            <li><a href="#"><i class="fad fa-spinner-third"></i> Course Progress</a></li>
                            <li><a href="#"><i class="far fa-sticky-note"></i> Course Notes</a></li>
                            <li><a href="#"><i class="far fa-trophy-alt"></i> Course Modules</a></li>
                            <li><a href="#"><i class="far fa-user-headset"></i> Assistance</a></li>
                            <li><a href="#"><i class="far fa-file-certificate"></i> Download Certificate</a></li>
                            <li><a href="#"><i class="fas fa-pencil"></i> Edit Profile</a></li>
                            <li><a href="#"><i class="far fa-window"></i> Revisit Modules</a></li>
                            <li><a href="#"><i class="far fa-sign-out"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include BASE_PATH . 'validate-prospects.php';?>
<?php include BASE_PATH . 'footer.php';?>