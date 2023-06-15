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

                    <div class="quiz-timer">
                        <p>Time limit: <span id="countdown-hurry"></span></p>
                        <div class="quiz-100"><div class="quiz-percent" style="width: 27%"></div></div>
                        <h4>Question <?= $count ?> of <?= $total ?></h4>
                    </div>
                    <div class="quiz-question">
                        <h3><?= $question->question ?></h3>
                        <?php
                        foreach($options as $option) {

                            ?>
                            <div class="quiz-option">
                                <input type="radio" name="q_<?= $question->id ?>" value="<?= $option->id ?>"> <?= $option->title ?>
                            </div>
                            <?php

                        }
                        ?>
                        <div class="quiz-option wrong-option">
                            <input type="radio">Fondant, cake spatula, piping tools, pastry bags and tips
                        </div>
                        <div class="quiz-option correct-option">
                            <input type="radio">Pre-made icing in tubes
                        </div>
                        <div class="quiz-option">
                            <input type="radio">Nothing, you can buy everything you need when you get your ingredients
                        </div>
                        <a href="javascript:;" class="quiz-button">Next Question</a>
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