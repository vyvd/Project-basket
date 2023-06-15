<?php
require_once(APP_ROOT_PATH . 'services/AWSService.php');

$this->setControllers(array("courseModule"));
$module = $this->controller->getSingleModule();

$moduleAudio = $this->courseModule->getModuleAudio($module->id)->url ? AWSService::getFromS3($this->courseModule->getModuleAudio($module->id)->fileName) : null;
$mp3Audio = $this->courseModule->getModuleMp3Audio($module->id)->url ?? null;

$videoTransMedia = $this->courseModule->getModuleVideoTrans($module->id);

$course = $this->controller->getSingleCourse($module->courseID);
$blogs = $this->controller->getAllCourseBlogs($course);

$moduleOriginal = $module;

$completedModules = $this->courseModule->getCompletedModules($course->id, true);

$currentAssigned = $this->controller->getCourseAssigned($course);

$courseModules = $this->controller->courseModules($course);
$prevModule = $this->courseModule->getPreviousModule($module);

$css = array("dashboard.css");
$pageTitle = $module->title.' - '.$course->title;
include BASE_PATH . 'account.header.php';
if(@$_SESSION['q_ids']){
    unset($_SESSION['q_ids']);
}

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.6.0/themes/prism-okaidia.min.css">

<section class="page-title with-nav module-title">
    <div class="container">
        <h1><?= $course->title ?></h1>
        
        <label class="focus-toggle">
            Focus Mode
            <input type="checkbox">
            <span class="toggle" onclick="toggleFocus()"></span>
            <span class="focus-tooltip">Turn on focus mode to see only course content</span>
        </label>

        <ul class="nav navbar-nav inner-nav nav-tabs">
            <i class="fal fa-times closeCourseNav"></i>
            <li class="nav-item link">
                <a class="nav-link active show" href="#module" data-toggle="tab">Current Module</a>
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

            <li class="nav-item link">
                <a class="nav-link" href="#resources" data-toggle="tab">Course Resources</a>
            </li>

            <?php if(count($blogs) >= 1) {?>
                <li class="nav-item link">
                    <a class="nav-link" href="#additionalReading" data-toggle="tab">Additional Resources</a>
                </li>
            <?php }?>
        </ul>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.6.0/components/prism-markup.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.6.0/prism.js"></script>
<script>
    (function(){

        document.addEventListener('DOMContentLoaded', function(event) {

            var list = document.querySelectorAll('pre');

            [].forEach.call(list, function(el) {
                var snippet = el.innerHTML.replace(/</g,'&lt;');
                snippet = snippet.replace(/ /g,'&nbsp;');
                var code = '<pre class="language-markup"><code>'+snippet+'</pre></code>';
                //   el.insertAdjacentHTML('afterend',code);
                $(el).html(code);
            });

            // if your page has prism.js you get syntax highlighting
            if(window.Prism){
                Prism.highlightAll(false);
            }

        });
    })();

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

<script>
    // mobile nav
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        $(".module-title .nav-link").closest("li").css("display", "none");
        $(".module-title .nav-link.active").closest("li").css("display", "block");

        $( ".module-title .nav-link.active" ).click(function() {

            $( ".module-title .nav-link" ).each(function( index ) {
                if($(this).hasClass("active")) {

                } else {
                    $(this).closest("li").slideDown();
                }
            });

            $( ".module-title .nav-link.active" ).click(function() {

                $( ".module-title .nav-link" ).each(function( index ) {
                    if($(this).hasClass("active")) {

                    } else {
                        $(this).closest("li").slideDown();
                    }
                });
            });

        });

        $( ".closeCourseNav" ).click(function() {

            $( ".module-title .nav-link" ).each(function( index ) {
                if($(this).hasClass("active")) {

                } else {
                    $(this).closest("li").slideUp();
                }
            });

            //rebind
            $( ".module-title .nav-link.active" ).click(function() {

                $( ".module-title .nav-link" ).each(function( index ) {
                    if($(this).hasClass("active")) {

                    } else {
                        $(this).closest("li").slideDown();
                    }
                });
            });

        });

    }
</script>

<div class="tab-content container">


    <div id="module" class="tab-pane helpVideos active show">
        <div class="myprogress-status">
            <div class="progress <?php if($currentAssigned->percComplete == 0){ echo 'zeroProgress';}?>">
                <div class="progress-bar" role="progressbar" style="width: <?= $currentAssigned->percComplete ?>%" aria-valuenow="<?= $currentAssigned->percComplete ?>" aria-valuemin="0" aria-valuemax="100"><?= number_format($currentAssigned->percComplete) ?>%</div>
            </div>
        </div>

        <div class="module-cover" >
            <?php
            $image = $this->courseModule->getFeatureImageById($module->id);

            if($image->url != "") {
                ?>
                <div class="d-flex justify-content-center" style="float:left;width:160px;">
                    <img src="<?= $image->url ?>" alt="<?= $module->title ?>" style="margin:0;">
                </div>
                <?php
            }
            ?>
            <h1 class="text-left"><?= $module->title ?></h1>
            <?php if($module->estTime != "") { ?><p class="duration">Duration: Approx <?= $module->estTime ?> minutes</p><?php } ?>
            <?php
            if($module->new_style_with_video != "1") {
                ?>
                <div class="print-icons">
                    <a href="<?= SITE_URL ?>ajax?c=course&a=get-module-pdf&id=<?= $module->id ?>" title="Export (PDF)" target="_blank" style="margin-left: 5px;">
                        <i class="far fa-file-pdf"></i>
                    </a>
                    <a href="<?= SITE_URL ?>ajax?c=course&a=get-module-pdf&id=<?= $module->id ?>&print=true" title="Print (PDF)" target="_blank" style="margin-left: 5px;">
                        <i class="fas fa-print"></i>
                    </a>
                </div>
            <?php } ?>
            <?php
            if($videoTransMedia->url != "") {
                ?>
                <a href="<?= $videoTransMedia->url ?>" title="Video Transcription" target="_blank" style="margin-left: 5px;color:#000;">
                    <i class="fas fa-text"></i>
                </a>
                <?php
            }
            ?>
        </div>
        <?php if(@$moduleAudio){ ?>

            <!-- float at the top-->
            <style>
                .module_audio {
                    position: sticky;
                    top: 0;
                    background: #f8f8f8;
                    z-index: 999;
                }
                .module_audio audio {
                    width:98%;
                }
            </style>

            <div class="module_audio">
                <div class="row mt-4 ml-2">
                    <div class="col-md-12">
                        <h4>Listen to the Module</h4>
                        <audio controls>
                            <source src="<?php echo $moduleAudio;?>" type="audio/ogg">
                            <source src="<?php echo $moduleAudio;?>" type="audio/mpeg">
                            Your browser does not support the audio tag.
                        </audio>

                        <?php
                        $categories = $this->controller->getCourseCategories($course->id);
                        $cat = ORM::for_table("courseCategories")->find_one($categories[0]);
                        ?>
                        <script>
                            $("audio").on({
                                play:function(){ // send play to google
                                    dataLayer.push({
                                        'event':'playAudio',
                                        'ecommerce': {
                                            'detail': {
                                                'products': [{
                                                    'name': '<?= $course->title ?>',
                                                    'id': '<?= $course->id ?>',
                                                    'brand': 'NSA',
                                                    'category': '<?= $cat->title ?>',
                                                    'variant': 'Full Course'
                                                }]
                                            }
                                        }
                                    });
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>

        <?php }?>
        <?php if(@$mp3Audio){ ?>
        <div class="module_audio">
            <div class="row mt-4 ml-2">
                <div class="col-md-12">
                    <h4>Listen to the Module</h4>
                    <audio controls>
                        <source src="<?= $mp3Audio?>" type="audio/mp3">
                        Your browser does not support the audio tag.
                    </audio>
                </div>
            </div>
        </div>
    </div>

    <?php }?>
    <?php
    if($module->new_style_with_video == "1" ||  $module->contentType == 'video') {

        $media = $this->courseModule->getEmbedVideoById($module->id);

        $vimeoID = substr(parse_url($media->url, PHP_URL_PATH), 1);
        $vimeoID = str_replace("video/", "", $vimeoID);

        ?>
        <iframe src="https://player.vimeo.com/video/<?= $vimeoID ?>" id="vimeoIframe" width="100%" height="610" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>

        <?php
        if($course->isNCFE == "1") {
            // only allow the user to click the next button when the vimeo video is finished
            ?>
            <p style="text-align: Center;font-style: italic;">You must watch the entire video before moving on to the next section.</p>
            <script src="https://player.vimeo.com/api/player.js"></script>
            <script>
                $(document).ready(function(){

                    var originalLink = $(".module-prev-next a").attr("href");
                    $(".module-prev-next a").attr("href", "javascript:;");
                    $(".module-prev-next a").css("opacity", "0.5");

                    var iframe = $('#vimeoIframe');
                    var player = new Vimeo.Player(iframe);

                    player.on('ended', function() {
                        $(".module-prev-next a").attr("href", originalLink);
                        $(".module-prev-next a").css("opacity", "1");
                    });

                });
            </script>
            <?php
        }
        ?>

        <?php
        if($module->worksheet_title == "") {
            ?>
            <br />
            <br />
            <br />
            <br />
            <?php
        }
        ?>
        <?php

    } elseif ( $module->contentType == 'quiz') {
        $quiz = $this->controller->getModuleQuiz($module->id);
    ?>
        <div class="moduleContents">
            <div class="quiz-overlay">
                <div class="quiz-overlay__title">You are about to start this quiz</div>
                <div class="quiz-overlay__time">Quiz pass percentage: <?= $quiz->passingPercentage?>%</div>
                <div class="quiz-overlay__time">Quiz time limit: <?= $quiz->timeLimit?> minutes</div>
                <a href="<?= SITE_URL.'quiz/'.$module->slug;?>" class="quiz-overlay__start btn">Start</a>
            </div>
        </div>

    <?php
    }else {
        ?>
        <div class="moduleContents">
            <?php //$this->controller->moduleContents($module->contents) ?>
            <?php $this->courseModule->renderContents($module) ?>
            <?php
                if($module->contentType == 'upload') {
                    $uploadFiles = $this->courseModule->getModuleUploads($module->id);
                    if(count($uploadFiles) >= 1) {
            ?>
                        <div class="files_to_download">
                            <label>Files to download:</label>
                            <ol>
                                <?php
                                    foreach ($uploadFiles as $file) {
                                        echo '<li class="space-between"><a href="'.$file->url.'" target="_blank">'.$file->title.'</a></li>';
                                    }
                                ?>
                            </ol>
                        </div>
            <?php
                    }
                }elseif($module->contentType == 'assignment') {
                    $uploadFiles = $this->courseModule->getModuleAssignments($module->id);
                    if(count($uploadFiles) >= 1) {
                        ?>
                    <link rel="stylesheet" href="https://releases.transloadit.com/uppy/v2.1.0/uppy.min.css">
                    <style>
                        .uppy-size--md .uppy-Dashboard-inner{
                            margin: 0 auto;
                        }
                    </style>
                    <script src="<?= SITE_URL ?>dist/bundle.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.5/dist/sweetalert2.all.min.js"></script>
                        <div class="files_to_download">
                            <label>Files to download:</label>
                            <ol>
                                <?php
                                foreach ($uploadFiles as $file) {
                                    echo '<li class="space-between"><a href="'.$file->url.'" target="_blank">'.$file->title.'</a></li>';
                                }
                                ?>
                            </ol>
                        </div>
                        <div class="files_to_upload">
                            <div id="fileUploader">
                                <p class="text-center">
                                    <i class="fa fa-spin fa-spinner"></i>
                                </p>
                            </div>
                            <script type="text/javascript">
                                $("#fileUploader").load("<?= SITE_URL ?>ajax?c=accountAssignment&a=getAssignmentFileUploader&moduleID=<?= $module->id ?>");
                            </script>
                        </div>
                        <?php
                    }
                }
            ?>
        </div>
        <?php
    }
    ?>



    <?php
    $quiz = false; // no longer needed: $this->controller->getQuiz($module->id, "d");

    if($quiz != false) {

        ?>
        <h4>Before you proceed...</h4>
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

    <?php
    if($module->worksheet_title != "") {
        $worksheet = $this->courseModule->getWorksheetById($module->id);
        ?>
        <div class="module-infos d-flex align-items-center">
            <div class="texts col-12 col-md-4">
                <h5><?= $module->worksheet_title ?></h5>
                <p><?= $module->worksheet_text ?></p>
            </div>
            <div class="mins col-12 col-md-4 text-center d-flex align-items-center">
                <h1><?= $module->worksheet_estimate_time ?></h1>
                <span>Minutes</span>
            </div>
            <div class="pdf col-12 col-md-4 text-center d-flex align-items-center">
                <a href="<?= $worksheet->url ?>" target="_blank"><img src="<?= SITE_URL ?>assets/user/images/pdf-icon.png" alt="pdf" /></a>
            </div>
        </div>
        <?php
    }
    ?>

    <?php
    $socialText = "I've just completed ".$module->title." on New Skills Academy. Check out the course at ".SITE_URL.'course/'.$course->slug;
    ?>

    <style>
        .moduleShare .texts {
            background-color:#fff;
            text-align:center;
        }
        .moduleShare .texts h5 {
            text-decoration: none;
            margin-bottom: 20px;
            font-size: 23px;
        }
        .moduleShare .texts a:hover {
            text-decoration:none;
        }
    </style>

    <?php //include ('includes/share_your_progress.php');?>
    <?php
    $showNextTimer = false; // $this->controller->showLinkTimerOnModule($module, $currentAssigned);

    if($showNextTimer == false) {
        $nextModule = $this->controller->moduleNext($module, $course, true);
        // check if Quiz
        if ((strpos($nextModule, SITE_URL.'quiz/') !== false) && (empty($module->parentID))) {
            $quiz = $this->controller->getQuiz($module->id, "a");
            if(@$quiz->maxQuestionValue){
                $questionCount = $quiz->maxQuestionValue;
            }else{
                $questionCount = ORM::for_table('quizQuestions')
                    ->where('quizID', $quiz->id)
                    ->count();
            }
        ?>
            <div class="module-prev-next d-flex">
                <a data-toggle="modal" data-target="#quizInfo"  href="javascript::void(0);" class="btn prev-next" style="    width: 100%;
            margin-left: 0;">Continue >></a>
            </div>
            <div id="quizInfo" class="modal fade">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <h5 style="font-weight: normal">
                                Before you move on to the next module you will first need to pass a short test. The test is <?= $questionCount?> questions long.
                                <?php if(@$quiz->timeLimit){?>You will have <?= $quiz->timeLimit;?> minutes to complete the test.<?php }?>
                                You can take the test as many times as you need. If you need to take a break you can save your progress and return at a later time to complete it. Please press the next button to begin the test.
                            </h5>
                            <div class="module-prev-next d-flex">
                                <?php if($prevModule){?>
                                    <a href="<?= SITE_URL.'module/'.$prevModule->slug ?>" class="btn prev-next" style="    width: 100%;
        margin-left: 0; margin-right: 30px"><< Previous</a>
                                <?php } ?>
                                <a href="<?php echo $nextModule ?>" class="btn prev-next" style="    width: 100%;
        margin-left: 0;">Next >></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }else{
        ?>
            <div class="module-prev-next d-flex">
                <?php if($prevModule){?>
                    <a href="<?= SITE_URL.'module/'.$prevModule->slug ?>" class="btn prev-next" style="    width: 100%;
        margin-left: 0; margin-right: 30px"><< Previous</a>
                <?php } ?>
                <a href="<?php echo $nextModule ?>" class="btn prev-next" style="    width: 100%;
        margin-left: 0;">Next >></a>
            </div>
        <?php
        }
    } else {
        // disable next button for 10 minutes
        ?>

        <script>
            function startTimer(duration, display) {
                var timer = duration, minutes, seconds;
                setInterval(function () {
                    minutes = parseInt(timer / 60, 10);
                    seconds = parseInt(timer % 60, 10);

                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;

                    display.textContent = minutes + ":" + seconds;

                    if (--timer < 0) {
                        timer = duration;
                    }
                }, 1000);
            }

            window.onload = function () {
                var timerMinutes = 60 * 10,
                    display = document.querySelector('#time');
                startTimer(timerMinutes, display);
            };

            setTimeout(function () {
                $("#nextBtnTimed").html('<div class="module-prev-next d-flex"><a href="<?php $this->controller->moduleNext($module, $course) ?>" class="btn prev-next" style="    width: 100%;margin-left: 0;">Next >></a></div>');
            }, 600000);
        </script>

        <div id="nextBtnTimed">
            <div class="module-prev-next d-flex">
                <a href="javascript:;" class="btn prev-next" style="    width: 100%;
    margin-left: 0;">You can proceed in <span id="time"></span></a>
            </div>
        </div>
        <?php
    }
    ?>

</div>

<!--Modules-->
<div id="modules" class="tab-pane faq">
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

<!--FAQ-->
<div id="faq" class="tab-pane fade faq">
    <?php include_once('includes/faq.php'); ?>
</div>

<!--Course Resources-->
<div id="resources" class="tab-pane fade">
    <?php include_once('includes/course_resources.php'); ?>
</div>

<!--Additional Reading-->
<?php if(count($blogs) >= 1) { ?>
    <div id="additionalReading" class="tab-pane fade pt-5">
        <?php include_once('includes/additional_reading.php'); ?>
    </div>
<?php } ?>

</div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<div id="notesFloat" style="cursor:move;cursor:grab;right:172px;">
    <i class="fa fa-times" onclick="$('#notesFloat').fadeToggle();"></i>
    <?php
    $notes = $this->controller->getNotes($moduleOriginal->id);
    ?>
    <form name="notes">
        <textarea name="notes" class="tinymce"><?= $notes->notes ?></textarea>
        <input type="hidden" name="course" value="<?= $moduleOriginal->courseID ?>" />
        <input type="hidden" name="module" value="<?= $moduleOriginal->id ?>" />
        <button type="submit" class="mainBtn noFloat" style="border:0;margin-top:20px;">Save Notes</button>
    </form>

    <script type="text/javascript">
        $("form[name='notes']").submit(function(e) {
            tinyMCE.triggerSave();
            var formData = new FormData($(this)[0]);
            e.preventDefault();
            $( "#returnStatus" ).empty();

            $.ajax({
                url: "<?= SITE_URL ?>ajax?c=course&a=save-notes",
                type: "POST",
                data: formData,
                async: false,
                success: function (msg) {
                    $('#returnStatus').append(msg);

                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    </script>
</div>

<script>
    $( function() {
        $( "#notesFloat" ).draggable();
    } );
</script>

<div id="openNotes" onclick="$('#notesFloat').fadeToggle();">
    <i class="fa fa-edit"></i>
    My Notes
</div>

<script src='<?= SITE_URL ?>assets/js/tinymce/tinymce.min.js'></script>
<script>
    function initTiny() {

        tinymce.init({
            selector: '.tinymce',
            plugins: 'table link lists hr textcolor emoticons image imagetools media link preview visualchars visualblocks wordcount',
            toolbar: 'undo redo paste | styleselect | bold italic strikethrough underline | link image media | bullist numlist | aligncenter alignleft alignright alignjustify alignnone | blockquote | backcolor forecolor | removeformat visualblocks',
            height: '250',
            content_style:
                "body { font-size: 16px !important;}"
        });

    }
    initTiny();

    <?php
    if($this->get["open"] == "notes") {
    ?>
    $('#notesFloat').fadeToggle();
    <?php
    }
    ?>
</script>

<script>
    setInterval(function(){
        $("#returnStatus").load('<?= SITE_URL ?>ajax?c=courseTimeProgress&a=track&id=<?= $currentAssigned->id ?>');
    }, 60000);
</script>

<?php include BASE_PATH . 'account.footer.php';?>
