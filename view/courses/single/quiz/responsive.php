<?php

$module = $this->controller->getSingleModule();
$course = $this->controller->getSingleCourse($module->courseID);

$currentAssigned = ORM::for_table("coursesAssigned")
    ->where("accountID", CUR_ID_FRONT)
    ->where("courseID", $module->courseID)
    ->find_one();


$pageTitle = $module->title.' - '.$course->title;
include BASE_PATH . 'account.header.php';
if($module->contentType == 'quiz'){
    $quiz = $this->controller->getQuiz($module->id);
}else{
    $quiz = $this->controller->getQuiz($module->id, "a");
}


if(@$_SESSION['q_ids']){
    //print_r($_SESSION['q_ids']);
    unset($_SESSION['q_ids']);
}
// check if user has completed course already, if so they dont show quiz
if($currentAssigned->completed == "1") {
//    $this->redirectJS($this->controller->moduleNext($module, $course, true));
//    exit;
}

?>

    <section class="page-title with-nav">
        <div class="container">
            <h1><?= $course->title ?></h1>

            <label class="focus-toggle" onclick="toggleFocus()">
                Focus Mode
                <input type="checkbox">
                <span class="toggle" onclick="toggleFocus()"></span>
                <span class="focus-tooltip">Turn on focus mode to see only course content</span>
            </label>

            <ul class="nav navbar-nav inner-nav nav-tabs">
                <li class="nav-item link">
                    <a class="nav-link active show" href="#module" data-toggle="tab">Quiz</a>
                </li>
                <li class="nav-item link">
                    <a class="nav-link" href="#modules" data-toggle="tab">Modules</a>
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
            </ul>
        </div>
    </section>

    <script>
        // retain focus mode if it's been selected
        $(function() {
            if(document.cookie.indexOf('focusMode=true') != -1) {
                $('body').addClass('focus-mode');
                $('.focus-toggle input').prop('checked', true);
            }
        });

        function toggleFocus() {
            if(!$('.focus-toggle input:checked').val()) {
                $('body').addClass('focus-mode'); 
                document.cookie = "focusMode=true; path=/;";
            } else {
                $('body').removeClass('focus-mode');
                document.cookie = "focusMode=false; path=/;";
            
            }
        }
    </script>


    <div class="tab-content container">


        <div id="module" class="tab-pane helpVideos active show">

            <?php

            if($quiz != false) {

                ?>
                <div id="questionWidget">
                    <p class="text-center">
                        <i class="fa fa-spin fa-spinner"></i>
                    </p>
                </div>
                <script type="text/javascript">
                    $("#questionWidget").load("<?= SITE_URL ?>ajax?c=quiz&a=widget&module=<?= $module->id ?>&quiz=<?= $quiz->id ?>");
                </script>
                <?php

            }
            ?>
        </div>

        <!--Modules-->
        <div id="modules" class="tab-pane faq">
            <div id="modulesAccordion">
                <?php
                $count = 1;
                foreach($this->controller->courseModules($course) as $module) {
                    $count ++;
                    ?>

                    <div class="card">
                        <div class="card-header" id="heading<?= $module->id ?>">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#question<?= $module->id ?>" aria-expanded="<?php if($count == $key) { ?>true<?php } else { ?>false<?php } ?>" aria-controls="question<?= $module->id ?>">
                                    <?= $module->title ?>
                                </button>
                            </h5>
                        </div>

                        <div id="question<?= $module->id ?>" class="collapse <?php if($count == $key) { ?>show<?php } ?>" aria-labelledby="heading<?= $module->id ?>" data-parent="#modulesAccordion">
                            <div class="card-body">
                                <h4 class="underlined">Learning Topics</h4>
                                <br />
                                <?= $module->description ?>

                                <?php
                                if($module->estTime != "") {
                                    ?>
                                    <div class="modules-btn" style="margin-top:10px;">
                                        <button type="button" class="btn btn-secondary btn-lg" disabled>Approx Time: <?= $module->estTime ?> minutes</button>
                                    </div>
                                <?php } ?>
                                <?php if($count <= $key) { ?>
                                    <div class="modules-btn" onclick="parent.location='<?= SITE_URL ?>module/<?= $module->slug ?>'">
                                        <button type="button" class="btn btn-primary btn-lg">Revisit</button>
                                    </div>
                                <?php } else { ?>
                                    Not available until the previous module is completed.
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php

                }
                ?>
            </div>
        </div>

        <?php
        $modules = ORM::for_table("courseModules")
            ->where("courseID", $course->id)
            ->order_by_asc("ord")
            ->find_many();

        $modArray = array();

        $count = 1;
        foreach($modules as $mod) {

            $modArray[$count] = $mod->id;

            $count ++;
        }

        $key = array_search($currentAssigned->currentModule, $modArray);
        ?>
        <!--My Progress-->
        <div id="myProgress" class="tab-pane helpVideos fade">
            <div class="myprogress-status">
                <label>Module <?= $key ?> of <?= $this->controller->totalCourseModules($course) ?></label>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?= $item->percComplete ?>%" aria-valuenow="<?= $item->percComplete ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <?php
            $count = 1;
            foreach($this->controller->courseModules($course) as $module) {
                ?>
                <div class="myprogress-modules" <?php if($count <= $key) { ?>style="cursor:pointer;" onclick="parent.location='<?= SITE_URL ?>module/<?= $module->slug ?>'"<?php } ?>>
                    <div class="sno"><?= $count ?></div>
                    <div class="name"><?= $module->title ?></div>
                    <?php if($module->estTime != "") { ?>
                        <div class="time"><?= $module->estTime ?> minutes</div>
                    <?php } ?>
                    <div class="status <?php if($count <= $key) { ?>complete<?php } else { ?>uncomplete<?php } ?>"><i class="fas fa-check"></i></div>
                </div>
                <?php
                $count ++;
            }
            ?>
        </div>

        <!--Course Notes-->
        <div id="courseNotes" class="tab-pane fade">
            <div class="form-row">
                <div class="col-12 col-md-12">

                    <div class="row align-items-center">
                        <?php
                        $notes = $this->controller->getAllCourseNotes($course);

                        if(count($notes) == 0) {
                            ?>
                            <div class="col-12">
                                <br />
                                <br />
                                <br />
                                <br />
                                <p class="text-center">You've not yet made any notes for this course.</p>
                            </div>
                            <?php
                        }

                        foreach($notes as $note) {
                            $module = $this->controller->getModuleByID($note->moduleID);
                            ?>
                            <div class="col-12 col-md-4">
                                <div class="module">
                                    <h4><?= $module->title ?></h4>
                                    <div id="printable<?= $note->id ?>">
                                        <?= $note->notes ?>
                                    </div>
                                    <div class="module-links">
                                        <a href="<?= SITE_URL ?>module/<?= $module->slug ?>" class="visit">Visit Module</a>
                                        <a href="javascript:;" onclick="printNotes<?= $note->id ?>();"><i class="fas fa-print" aria-hidden="true"></i></a>
                                        <a href="<?= SITE_URL ?>module/<?= $module->slug ?>?open=notes"><i class="fas fa-pen" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </div>

                            <script>
                                function printNotes<?= $note->id ?>() {
                                    var printContents = document.getElementById("printable<?= $note->id ?>").innerHTML;
                                    var originalContents = document.body.innerHTML;

                                    document.body.innerHTML = printContents;

                                    window.print();

                                    document.body.innerHTML = originalContents;
                                }
                            </script>
                            <?php

                        }
                        ?>
                    </div>

                </div>
            </div>
        </div>


        <!--FAQ-->
        <div id="faq" class="tab-pane fade faq">
            <div class="row">
                <h3>Frequently Asked Questions</h3>
                <div id="accordion" class="col-12">
                    <?php
                    foreach(ORM::for_table("courseModuleFaqs")->order_by_asc("id")->find_many() as $faq) {
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

    <script>
        setInterval(function(){
            $("#returnStatus").load('<?= SITE_URL ?>ajax?c=courseTimeProgress&a=track&id=<?= $currentAssigned->id ?>');
        }, 60000);
    </script>

<?php include BASE_PATH . 'account.footer.php';?>