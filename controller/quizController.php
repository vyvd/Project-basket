<?php
require_once(__DIR__ . '/../services/AWSService.php');

class quizController extends Controller {

    public function getQuizQuestions($quiz) {

        return ORM::for_table("quizQuestions")
            ->where("quizID", $quiz)
            ->order_by_asc("ord")
            ->find_many();

    }

    public function countQuizQuestions($quizID, int $limit = null) {


        if(@$limit){
            $aCount = ORM::for_table("quizQuestions")
                ->where("quizID", $quizID)
                ->order_by_asc("ord")
                ->limit($limit)
                ->find_many();
            $count = count($aCount);
        }else{
            $count = ORM::for_table("quizQuestions")
                ->where("quizID", $quizID)
                ->order_by_asc("ord")
                ->count();
        }

        return $count;

    }

    public function getAnswersArray($answers) {

        return json_decode($answers, true);

    }
    public function getAnswersByQuestionID($questionID, $onlyCorrect = false) {

        $answers = ORM::for_table('quizQuestionAnswers')
            ->where('questionID', $questionID);
        if($onlyCorrect){
            $answers = $answers->where('isCorrect', 1);
        }

        return $answers->find_many();
    }

    public function getQuestion() {

        $count = $this->get["count"];

        $_SESSION['quiz']["total"] = $count;

        $currentQuestionId = $_SESSION['quiz']["questionData"][$count - 1]->id ?? $_SESSION['quiz']["questionData"][$count - 1]['id'];
        $question = ORM::for_table('quizQuestions')->find_one($currentQuestionId);

        if($question->id == "") {
            echo "Question not found";
        }else{
            $imageMedia = ORM::for_table('media')
                ->where('modelType', 'quizQuestionController')
                ->where('modelId', $question->id )
                ->where('type', 'main_image')
                ->find_one();
            $audioMedia = ORM::for_table('media')
                ->where('modelType', 'quizQuestionController')
                ->where('modelId', $question->id )
                ->where('type', 'audio')
                ->find_one();
            $mp3AudioMedia = ORM::for_table('media')
                ->where('modelType', 'quizQuestionController')
                ->where('modelId', $question->id )
                ->where('type', 'mp3_audio')
                ->find_one();
            $vimeoURLMedia = ORM::for_table('media')
                ->where('modelType', 'quizQuestionController')
                ->where('modelId', $question->id )
                ->where('type', 'vimeoURL')
                ->find_one();

        }

        ?>
        <script>
            $(".questionCount").html(<?= $count ?>);
        </script>
        <?php $answers = $this->getAnswersByQuestionID($question->id);?>

        <form class="quizForm" name="answer<?= $question->id ?>" id="answer<?= $question->id ?>">
            <input type="hidden" name="timeUp" id="timeUp" value="0" />
            <input type="hidden" name="count" value="<?= $count ?>" />
            <div class="quiz-question">
                <h3><?= $question->question ?></h3>
                <?php
                    if(@$imageMedia){
                ?>
                        <div class="pb-4">
                            <img src="<?= $imageMedia->url?>" style="max-width: 100%">
                        </div>
                <?php
                    }
                    if(@$mp3AudioMedia->url){
                ?>
                        <div class="module_audio">
                            <div class="row mt-4 ml-2">
                                <div class="col-md-12">
                                   <audio controls>
                                        <source src="<?= $mp3AudioMedia->url?>" type="audio/mp3">
                                        Your browser does not support the audio tag.
                                    </audio>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                    if(@$audioMedia->url) {
                        $moduleAudio = AWSService::getFromS3($audioMedia->fileName);
                ?>
                        <div class="module_audio">
                            <div class="row mt-4 ml-2">
                                <div class="col-md-12">
                                    <h4>Listen to the Module</h4>
                                    <audio controls>
                                        <source src="<?php echo $moduleAudio;?>" type="audio/ogg">
                                        <source src="<?php echo $moduleAudio;?>" type="audio/mpeg">
                                        Your browser does not support the audio tag.
                                    </audio>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                    if(@$vimeoURLMedia->url){
                        $vimeoID = substr(parse_url($vimeoURLMedia->url, PHP_URL_PATH), 1);
                        $vimeoID = str_replace("video/", "", $vimeoID);
                ?>
                        <iframe src="https://player.vimeo.com/video/<?= $vimeoID ?>" width="100%" height="500" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                <?php
                    }
                ?>

                <?php
                    if($question->answerType == 'usertype'){?>
                        <label class="quiz-option">
                            <input class="form-control" placeholder="Please Enter your answer" type="text" name="answer" value="<?php if(@$_SESSION['quiz']["answerData"][$count - 1]){ echo $_SESSION['quiz']["answerData"][$count - 1];}?>">
                        </label>
                <?php
                    } else{
                        //$count = 0;
                        foreach($answers as $answer) {
                            ?>
                            <label class="quiz-option">
                                <?php if($question->answerType == 'single'){?>
                                    <input type="radio" <?php if(@$_SESSION['quiz']["answerData"][$count - 1] && ($_SESSION['quiz']["answerData"][$count - 1] == trim($answer->answer))){?> checked <?php }?> name="answer" value="<?= trim($answer->answer) ?>"><?= $answer->answer ?>
                                <?php } elseif($question->answerType == 'multiple'){?>
                                    <input type="checkbox" <?php if(@$_SESSION['quiz']["answerData"][$count - 1] && ( in_array(trim($answer->answer), $_SESSION['quiz']["answerData"][$count - 1]))){?> checked <?php }?> name="answer[]" value="<?= trim($answer->answer) ?>"> <?= $answer->answer ?>
                                <?php } ?>
                            </label>
                            <?php
                            ///$count ++;
                        }
                    }

                ?>
            </div>

            <input type="hidden" name="question" value="<?= $question->id ?>" />
            <input type="hidden" id="quizTimeSpent" name="time" />
        </form>
        <div class="row mt-4">
            <div class="col-6">
                <?php if($count >= 2){?>
                    <a href="javascript:;" onclick="" class="quiz-button prevButton">Previous</a>
                <?php }?>

            </div>
            <div class="col-6">
                <a href="javascript:;" onclick="$('#answer<?= $question->id ?>').submit();" class="quiz-button">Next</a>
            </div>
            <?php if($count >= 2){?>
                <div class="col-12 mt-3">
                    <a href="javascript:;" onclick="" class="quiz-button saveButton">Save Progress & Continue Later</a>
                </div>
            <?php }?>

        </div>
        <div id="quizInfoSave" class="modal fade">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="module-prev-next d-flex">
                                    <a href="<?= SITE_URL?>dashboard" class="btn prev-next" style="    width: 100%;
        margin-left: 0;">Return to my dashboard</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="module-prev-next d-flex">
                                    <a href="" class="btn prev-next continueTest" style="    width: 100%;
        margin-left: 0;">Continue Test</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function (){
                $(".quiz-button.prevButton").on('click', function (){
                    $("#questionAjax").load("<?= SITE_URL ?>ajax?c=quiz&a=get-question&count=<?= $count-1 ?>");
                });

                $(".continueTest").on('click', function (){
                    $( "#pauseBtnhms" ).trigger( "click" );
                    $('#quizInfoSave').modal('hide');
                });

                $(".quiz-button.saveButton").on('click', function (){
                    $( "#pauseBtnhms" ).trigger( "click" );
                    var time = $("#hms_timer").text();
                    $.ajax({
                        url: '<?= SITE_URL?>ajax?c=quiz&a=saveQuizProgress&time='+time,
                        type: 'get',
                        dataType: 'JSON',
                    }).done(function (response) {

                        // toastr.options.positionClass = "toast-bottom-left";
                        // toastr.options.closeDuration = 1000;
                        // toastr.options.timeOut = 6000;
                        // toastr.success(response.message, 'Success')
                        $('#quizInfoSave').modal('show');
                        //console.log(response);
                        return false;
                    });
                });
            });

        </script>
        <?php
        $this->renderFormAjax("quiz", "submit-answer", "answer".$question->id);


    }

    private function results() {

        $quiz = ORM::for_table("quizzes")->find_one($_SESSION['quiz']['quizID']);

        $questions = $_SESSION['quiz']['questionData'];

        $currentAnswers = $_SESSION['quiz']['answerData'];

        $count = 0;
        $correct = 0;

        foreach($questions as $question) {
            $questionID = $question->id ?? $question['id'];
            $resultQuestions[] = $questionID;
            $answers = $this->getAnswersByQuestionID($questionID, true);

            $correctAnswers = [];
            foreach($answers as $answer) {
                $correctAnswers[] = trim($answer->answer);
            }
            $questionData = ORM::for_table('quizQuestions')->find_one($questionID);


            if(@$currentAnswers[$count] && is_array($currentAnswers[$count])){ // if multiple choices question
                $ac = 1;
                foreach ($currentAnswers[$count] as $ca){
                    if(!in_array(trim($ca), $correctAnswers)){
                        $ac = 0;
                    }
                }
                if($ac == 1){
                    $correct++;
                }
            }else{
                if(in_array($currentAnswers[$count], $correctAnswers)){
                    $correct++;
                }
            }
            $count ++;

        }
        
        $percentage = number_format(($correct/count($questions))*100, 2);

        $item = ORM::for_table("quizResults")
            ->where('userID', CUR_ID_FRONT)
            ->where('quizID', $quiz->id)
            ->where('completed', 0)
            ->find_one();
        if(empty($item)){
            $item = ORM::for_table("quizResults")->create();
        }

        $item->userID = CUR_ID_FRONT;
        $item->quizID = $quiz->id;
        $item->correct = $correct;
        $item->incorrect = count($questions)-$correct;
        $item->total = count($questions);
        $item->percentage = $percentage;
        $item->questionData = json_encode($resultQuestions);
        $item->answerData = json_encode($currentAnswers);
        $item->whenAdded = date("Y-m-d H:i:s");

        if($percentage < $quiz->passingPercentage) {
            $item->passed = "0";
        } else {
            $item->passed = "1";
        }
        $item->completed = 1;

        $item->save();
        unset($_SESSION['quiz']);
        $_SESSION["resultID"] = $item->id();
        // show results to user
        ?>
        <script type="text/javascript">
            $(".quiz-timer h4").remove();
            $("#questionAjax").html('<p class="text-center"><i class="fa fa-spin fa-spinner" style="font-size:50px;display:block;"></i>Loading results...</p>');
            $("#questionAjax").load("<?= SITE_URL ?>ajax?c=quiz&a=show-results");
        </script>
        <?php


    }

    private function moduleNext($module, $course, $quizID, $return = false) {

        // same function exists in quizController.php

        // find out the next module, bare in mind the next page for the user may actually be a quiz

        // all modules
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

        $key = array_search($module->id, $modArray);
        $newKey = $key+1;

        $next = ORM::for_table("courseModules")
            ->find_one($modArray[$newKey]);

        // do we have a quiz for this module that needs to appear after the module and before the next module
        $quizCurrent = ORM::for_table("quizzes")->where("moduleID", $module->id)->where("appear", "a")->where_not_equal("id", $quizID)->find_one();

        if($quizCurrent->id != "") {

            if($return == false) {
                echo SITE_URL.'quiz/'.$module->slug;
            } else {
                return SITE_URL.'quiz/'.$module->slug;
            }

        } else {

            if($modArray[$newKey] == "") {
                // no other module to complete, course is complete
                $token = openssl_random_pseudo_bytes(20);
                $token = bin2hex($token);

                $courseAssigned = ORM::for_table("coursesAssigned")
                    ->where("accountID", CUR_ID_FRONT)
                    ->where("courseID", $module->courseID)
                    //->where("completed", "0")
                    ->find_one();

                $courseAssigned->token = $token;

                if($return == false) {
                    echo SITE_URL.'ajax?c=course&a=complete-course&id='.$courseAssigned->id().'&token='.$token;
                } else {
                    return SITE_URL.'ajax?c=course&a=complete-course&id='.$courseAssigned->id().'&token='.$token;
                }

                $courseAssigned->save();

            } else {
                // no quiz, send to module
                if($return == false) {
                    echo SITE_URL.'module/'.$next->slug;
                } else {
                    return SITE_URL.'module/'.$next->slug;
                }
            }


        }


    }
    private function moduleNextNew($module, $course, $quizID, $return = false)
    {
        // same function exists in quizController.php

        // find out the next module, bare in mind the next page for the user may actually be a quiz

        // Check if submodule
        $isSubModule = @$module->parentID ? true : false;

        $currentAssigned = ORM::for_table("coursesAssigned")
            ->where("accountID", CUR_ID_FRONT)
            ->where("courseID", $module->courseID)
            ->find_one();

        // all modules
        $modules = $this->courseModules($course);
        $modArray = array();
        $count = 1;
        foreach ($modules as $mod) {
            $modArray[$count] = $mod->id;
            $count++;
        }
        $currentModuleId = $currentAssigned->currentModule;

        if($isSubModule) {
            $currentModuleId = $currentAssigned->currentSubModule;

            $subModules = ORM::for_table("courseModules")
                ->where("parentID", $module->parentID)
                ->order_by_asc("ord")
                ->find_many();

            $subModArray = array();
            $countSub = 1;
            foreach ($subModules as $mod) {
                $subModArray[$countSub] = $mod->id;
                $countSub++;
            }
            $key = array_search($module->parentID, $modArray);
            $newKey = $key + 1;
            $next = ORM::for_table("courseModules")->find_one($modArray[$newKey]);


            $keySub = array_search($module->id, $subModArray);
            $newSubKey = $keySub + 1;
            if(@$subModArray[$newSubKey]){
                $next = ORM::for_table("courseModules")->find_one($subModArray[$newSubKey]);
            }
        }else{
            $key = array_search($module->id, $modArray);
            $newKey = $key + 1;
            $next = ORM::for_table("courseModules")->find_one($modArray[$newKey]);
        }

        // Only change progress if this module is one AFTER the "current" one
        $currentAssignedModule = ORM::for_table("courseModules")->find_one($currentModuleId);

        $showQuiz = true;
        $count = $count - 1;
        if ($course->finalQuizOnly == "1") {
            if ($key != $count) { // show module
                $showQuiz = false;
            }
        }

        if (($module->ord >= $currentAssignedModule->ord) && ($isSubModule == true)) {
            $currentAssigned->currentModule = @$next->parentID ? $next->parentID : $next->id;
            if($next->parentID != $module->parentID){
                $subModule = ORM::for_table('courseModules')
                    ->where('parentID', $currentAssigned->currentModule)
                    ->order_by_asc('ord')
                    ->find_one();
                $currentAssigned->currentSubModule = $subModule->id ?? null;
            }else{
                $currentAssigned->currentSubModule = $next->id ?? null;
            }

            //$currentAssigned->currentModuleKey = array_search($currentAssigned->currentModule, $modArray);

            $currentAssigned->set_expr("lastAccessed", "NOW()");
            //$currentAssigned->percComplete = $percentage;
            $currentAssigned->save();

        }

        // do we have a quiz for this module that needs to appear after the module and before the next module
        $quizCurrent = ORM::for_table("quizzes")->where("moduleID", $module->id)
            ->where("appear", "a")->find_one();

        // see if they already completed the quiz
        $quizResult = ORM::for_table("quizResults")->where("userID", CUR_ID_FRONT)->where("quizID", $quizCurrent->id)->find_one();

        if($quizResult->completed == "1") {
            $showQuiz = false;
        }

        // check to see if questions exist
        if ($quizCurrent->id != "") {
            $question = ORM::for_table("quizQuestions")
                ->where("quizID", $quizCurrent->id)
                ->order_by_asc("ord")
                ->offset(0)
                ->find_one();
        } else {
            $question = array();
        }

        // if first question is about feng shui then do not bother
        if (strpos($question->question, 'According to Feng Shui') !== false) {
            $showQuiz = false;
        }

        if ($quizCurrent->id != "" && $question->id != ""
            && $currentAssigned->completed != "1"
            && $question->question
            != "How many hours do holiday reps work in a week?"
            && $showQuiz == true && $next->id == ""
        ) {

            if ($return == false) {
                echo SITE_URL.'quiz/'.$module->slug;
            } else {
                return SITE_URL.'quiz/'.$module->slug;
            }

        }else {

            if (($modArray[$newKey] == "" && $isSubModule == false) || (($isSubModule == true) && ($modArray[$newKey] == "") && ($subModArray[$newSubKey] == ""))) {
                // no other module to complete, course is complete
                $token = openssl_random_pseudo_bytes(20);
                $token = bin2hex($token);

                $currentAssigned->token = $token;

                if ($return == false) {
                    echo SITE_URL.'ajax?c=course&a=complete-course&id='
                        .$currentAssigned->id().'&token='.$token;
                } else {
                    return SITE_URL.'ajax?c=course&a=complete-course&id='
                        .$currentAssigned->id().'&token='.$token;
                }

                $currentAssigned->save();

            } else {
                // no quiz, send to module
                if ($return == false) {
                    echo SITE_URL.'module/'.$next->slug;
                } else {
                    return SITE_URL.'module/'.$next->slug;
                }
            }


        }


    }

    public function showResults() {

        $result = ORM::for_table("quizResults")->where("userID", CUR_ID_FRONT)->find_one($_SESSION["resultID"]);

        $quiz = ORM::for_table("quizzes")->find_one($result->quizID);
        $module = ORM::for_table("courseModules")->find_one($quiz->moduleID);
        $course = ORM::for_table("courses")->find_one($module->courseID);

        $user = $this->currentUser();

        ?>
        <div class="quiz-question">

            <div class="quizPassed">

                <?php
                if($result->passed == "1") {
                    ?>
                    <h2 class="quizResultTitle">Well done <?= $user->firstname ?>! You passed.</h2>

                    <img src="<?= SITE_URL ?>assets/images/quizSuccess.png" class="quizResultImg" />

                    <p>You answered</p>

                    <p class="quizSuccessText"><?= $result->correct ?> of <?= $result->total ?> questions correctly</p>

                    <div class="progress quiz success">
                        <div class="progress-bar" role="progressbar" style="width: <?= $result->percentage ?>%" aria-valuenow="<?= $result->percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <p class="quizPerc">
                        Your score: <?= $result->percentage ?>%
                    </p>

                    <?php
                    // if quiz appears during a course (viral, etc) then they do not need to answer correctly to proceed
                    if($quiz->appear == "d") {
                        ?>
                        <div class="row quizBtnRow">
                            <div class="col-12">
                                <a href="javascript:;" class="quiz-button dark" onclick="$('.answerSummary').slideToggle();">Show Answer Summary</a>
                            </div>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="row quizBtnRow">
                            <div class="col-6">
                                <a href="javascript:;" class="quiz-button dark" onclick="$('.answerSummary').slideToggle();">Show Answer Summary</a>
                            </div>
                            <div class="col-6">
                                <a href="<?= $this->moduleNextNew($module, $course, $quiz->id) ?>" class="quiz-button">Next</a>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                } else {

                    // if a user fails 3 times in a row, then customer service must reset their enrollment
                    if($_SESSION["quizFailCount".$quiz->id] != "") {

                        $_SESSION["quizFailCount".$quiz->id] = 0; // set session

                    }

                    $_SESSION["quizFailCount".$quiz->id] = $_SESSION["quizFailCount".$quiz->id]+1; // add 1

                    if($_SESSION["quizFailCount".$quiz->id] == 3) {

                        // delay for 10 mins


                    }

                    if($_SESSION["quizFailCount".$quiz->id] == 6) {

                        // now we require CS team to manually reset this enrollment

                    }

                    ?>
                    <h2 class="quizResultTitle">Unlucky <?= $user->firstname ?>! You did not pass.</h2>

                    <img src="<?= SITE_URL ?>assets/images/quizFail.png" class="quizResultImg" />

                    <p>You answered</p>

                    <p class="quizFailText"><?= $result->correct ?> of <?= $result->total ?> questions correctly</p>

                    <div class="progress quiz">
                        <div class="progress-bar" role="progressbar" style="width: <?= $result->percentage ?>%" aria-valuenow="<?= $result->percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <p class="quizPerc">
                        Your score: <?= $result->percentage ?>%
                    </p>

                    <?php
                    if($quiz->appear == "d") {
                        ?>
                        <div class="row quizBtnRow">
                            <div class="col-12">
                                <a href="javascript:;" class="quiz-button dark" onclick="$('.answerSummary').slideToggle();">Show Answer Summary</a>
                            </div>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="row quizBtnRow">
                            <div class="col-6">
                                <a href="javascript:;" class="quiz-button dark" onclick="$('.answerSummary').slideToggle();">Show Answer Summary</a>
                            </div>
                            <div class="col-6">
                                <?php
                                    if($module->contentType == 'quiz'){
                                ?>
                                        <a href="<?= SITE_URL ?>quiz/<?= $module->slug ?>" class="quiz-button">Continue</a>
                                <?php
                                    }else{
                                ?>
                                        <a href="<?= SITE_URL ?>module/<?= $module->slug ?>" class="quiz-button">Continue</a>
                                <?php
                                    }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                }
                ?>

            </div>

            <div class="answerSummary">
                <?php
                $userAnswers = json_decode($result->answerData, true);
                if(@$result->questionData){
                    $questionIDs = json_decode($result->questionData);
                    $sql = "SELECT * FROM quizQuestions WHERE id IN (".implode(',', $questionIDs).") ORDER BY FIELD(id, ".implode(',', $questionIDs).")";
                    $questions = ORM::for_table("quizQuestions")
                        ->raw_query($sql)
                        ->find_many();
                }else{
                    $questions = ORM::for_table("quizQuestions")
                        ->where("quizID", $result->quizID)
                        ->order_by_asc("ord")
                        ->find_many();
                }

                $count = 0;

                $correctAnswers = [];
                foreach($questions as $question) {
                    $correct = 0;

                    $questionAnswers = ORM::for_table('quizQuestionAnswers')
                        ->where('questionID', $question->id)
                        ->where('isCorrect', 1)->find_many();

                    if(count($questionAnswers) >= 2){
                        foreach($questionAnswers as $answer) {
                            $correctAnswers[$count][] = trim($answer->answer);
                        }
                    }else{
                        $correctAnswers[$count] = trim($questionAnswers[0]->answer);
                    }

                    if(@$userAnswers[$count] && is_array($userAnswers[$count])){ // if multiple choices question
                        $correct = 1;
                        foreach ($userAnswers[$count] as $ca){
                            if(!in_array($ca, $correctAnswers[$count])){
                                $correct = 0;
                            }
                        }
                    }else{
                        if($userAnswers[$count] == $correctAnswers[$count] || (is_array($correctAnswers[$count]) && (in_array($userAnswers[$count], $correctAnswers[$count] )))){
                            $correct = 1;
                        }
                    }
                    ?>
                    <div class="quiz-question">
                        <h3><?= $question->question ?></h3>
                        <label class="quiz-option">
                            <?php
                            if($correct == 1) {
                                ?>
<!--                                <i class="fa fa-check singleACorrect"></i>-->
                                <?php
                            } else {
                                ?>
<!--                                <i class="fa fa-times singleAWrong"></i>-->
                                <?php
                            }

                            ?>
                            <span class="answerSummarySingle">
                                <?php
                                if(is_array($userAnswers[$count])) {
                                    foreach ($userAnswers[$count] as $uAnswer) {
                                        echo in_array($uAnswer, $correctAnswers[$count]) ? '<i class="fa fa-check singleACorrect"></i> <span>' . $uAnswer . "</span><br>" : '<i class="fa fa-times singleAWrong"></i> <span>' . $uAnswer . "</span><br>";
                                    }
                                    //echo implode("<br>", $userAnswers[$count]);
                                }else{
                                    echo $correct == 1 ? '<i class="fa fa-check singleACorrect"></i> <span>'. $userAnswers[$count] ."</span>" : '<i class="fa fa-times singleAWrong"></i> <span>' . $userAnswers[$count] . "</span>";
                                }
                                ?>
                            </span>
                        </label>
                    </div>
                    <?php


                    $count ++;

                }

                ?>
            </div>

        </div>
        <?php


    }

    public function submitAnswer() {

        if($this->post["timeUp"] == "1") {
            $this->results();
        }else{
            if($this->post["answer"] == "") {
                // nothing selected
                $this->setToastDanger("You must select an answer before proceeding.");
                exit;
            }
            $count = $this->post["count"];
            $_SESSION['quiz']["answerData"][$count - 1] = is_array($this->post['answer']) ? $this->post['answer'] : trim($this->post['answer']);

            if(count($_SESSION['quiz']['questionData']) == $count) {
                // save and show results
                $this->results();

            } else {
                // load next question
                ?>
                <script type="text/javascript">
                    $("#questionAjax").load("<?= SITE_URL ?>ajax?c=quiz&a=get-question&count=<?= $count+1 ?>");
                </script>
                <?php
            }
        }


    }

    public function widget() {


        if(@$this->get["id"] || $this->get["quiz"]) {
            $quiz = ORM::for_table("quizzes")
                ->where("moduleID", $this->get["module"])
                ->find_one($this->get["id"] ?? ($this->get["quiz"] ?? 0));
        }

        if(!isset($quiz->id)){
            echo "";
            exit();
        }


        $timeLimitMins = @$quiz->timeLimit ? $quiz->timeLimit : null;
        $timeLimitSecs = 0;
        $startTime = 0;
        $count = 1;
        $userID = CUR_ID_FRONT;
        $quizID = $quiz->id;
        $_SESSION['quiz'] = [];
        $_SESSION['quiz']['userID'] = CUR_ID_FRONT;
        $_SESSION['quiz']['quizID'] = $quiz->id;

        // Check if save progress
        $quizResult = ORM::for_table('quizResults')
            ->where('userID', $userID)
            ->where('quizID', $quizID)
            ->where('completed', 0)
            ->find_one();
        if(empty($quizResult)){
            $totalQuestions = ORM::for_table('quizQuestions')->where('quizID', $quiz->id)->count();

            if(@$quiz->maxQuestionValue && $quiz->maxQuestionValue <= $totalQuestions){
                $limit = ' limit 0,'.$quiz->maxQuestionValue;
            }else{
                $limit = "";
            }
            $quizQuestions = ORM::for_table('quizQuestions')
                ->raw_query("Select id from quizQuestions where quizID='".$quiz->id."' ORDER BY RAND()$limit")
                ->find_array();

            $_SESSION['quiz']['questionData'] = $quizQuestions;
        }else{
            $_SESSION['quiz']['questionData'] = json_decode($quizResult->questionData);
            $_SESSION['quiz']['answerData'] = json_decode($quizResult->answerData);
            if(@$timeLimitMins){
                $time = $quizResult->time; // in seconds

                if($time >= 1){
                    if($time >= 60){
                        $timeLimitMins = floor(($time / 60) % 60);
                        $timeLimitSecs = $time % 60;
                    }else{
                        $timeLimitMins = 00;
                        $timeLimitSecs = $time;

                    }
                }
            }
            $startTime = $quizResult->time ?? 0;
            $count = $quizResult->total;
        }
        $type = $this->get["type"];


        ?>

        <div class="quiz-timer">
            <!--<p>Time limit: <span id="countdown-hurry">72d 1h 52m 25s </span></p>
            <div class="quiz-100"><div class="quiz-percent" style="width: 27%"></div></div>-->
            <h4 style="text-decoration:none;">
                Question <span class="questionCount"><?= $count ?></span> of <?= count($_SESSION['quiz']['questionData']) ?>
                <?php
                if(@$timeLimitMins || @$timeLimitSecs){
                ?>
                    <small class="float-right">Time Left: <span id="hms_timer"></span></small>
                    <button style="display: none" id="pauseBtnhms" value="pause">Pause</button>
                <?php
                }?>
            </h4>

            <div id="questionAjax">
                <p class="text-center">
                    <i class="fa fa-spin fa-spinner"></i>
                </p>
            </div>

            <script type="text/javascript">
                $("#questionAjax").load("<?= SITE_URL ?>ajax?c=quiz&a=get-question&count=<?= $count ?>");
            </script>


        </div>
        <?php
        if(@$timeLimitMins || @$timeLimitSecs){

            ?>
            <script src='<?= SITE_URL ?>assets/js/jquery.countdownTimer.min.js'></script>
            <script>
                $(function(){
                    $("#hms_timer").countdowntimer({
                        minutes : <?= $timeLimitMins ?>,
                        seconds : <?= $timeLimitSecs ?>,
                        size : "lg",
                        pauseButton : "pauseBtnhms",
                        timeUp : timeisUp
                        //stopButton : "stopBtnhms"
                    });
                    function timeisUp() {
                        $("#timeUp").val('1');
                        $(".quizForm").submit();
                        //Code to be executed when timer expires.
                    }
                });
            </script>
        <?php }?>
        <?php


    }

    public function saveQuiz(array $input) {

        if(isset($input['id'])){  //For Update
            $item = ORM::for_table("quizzes")->find_one($input['id']);
        }else{
            $item = ORM::for_table("quizzes")->create();
        }

        $data = $input;
        if(@$data['questions']){
            $questions = $data['questions'];
            unset($data['questions']);
        }
        $item->set($data);

        $item->save();

        // Check if Questions
        if(@$questions){

            foreach ($questions as $question)
            {
                $question['quizID'] = $item->id;
                $this->saveQuizQuestion($question);
            }
        }

        return $item;

    }

    public function saveQuizQuestion(array $input) {

        if(isset($input['id'])){  //For Update
            $item = ORM::for_table("quizQuestions")->find_one($input['id']);
        }else if(isset($input['oldID'])){  //For Update
            $item = ORM::for_table("quizQuestions")->where('quizID', $input['quizID'])->where('oldID', $input['oldID'])->find_one();
        }

        if(empty($item)){
            $item = ORM::for_table("quizQuestions")->create();
        }

        $data = $input;

        if(@$data['answerData']){
            $answers = $data['answerData'];
            unset($data['answerData']);

        }

        $item->set($data);

        $item->save();

        // Check if Questions
        if(@$answers){
            foreach ($answers as $answer) {
                $answerData['answer'] = $answer->title;
                $answerData['isCorrect'] = $answer->correct ?? '0';
                $answerData['questionID'] = $item->id;
                $this->saveQuizQuestionAnswer($answerData);
            }
        }

        return $item;

    }

    public function saveQuizQuestionAnswer($input)
    {


        if(isset($input['answer'])) {  //For Update
            $item = ORM::for_table('quizQuestionAnswers')
                ->where('answer', $input['answer'])
                ->where('questionID', $input['questionID'])
                ->find_one();
        }

        if(!@$item->id){
            $item = ORM::for_table("quizQuestionAnswers")->create();
        }

        $data = $input;

        $item->set($data);

        $item->save();

        return $item;
    }

    public function updateQuestionAnswers()
    {
        $questions = ORM::for_table('quizQuestions')
            ->where('updateAnswers', 0)
            ->find_many();
        foreach ($questions as $question){
            $answers = json_decode($question->answerData, true);
            $correctAnswers = 0;
            if(is_array($answers)){
                foreach ($answers as $answer){
                    $newAnswer = ORM::for_table('quizQuestionAnswers')->create();
                    $data = [
                            'questionID' => $question->id,
                            'answer' => $answer["*_answer"],
                            'isCorrect' => @$answer["*_correct"] ? 1 : 0,
                    ];
                    $newAnswer->set($data);
                    $newAnswer->save();
                    if(@$newAnswer->isCorrect){
                        $correctAnswers++;
                    }
                }
                $question->answerType = $correctAnswers >= 2 ? 'multiple' : 'single';
                $question->correctAnswers = $correctAnswers;
                $question->updateAnswers = 1;
                $question->save();
            }
        }
        echo "Updated";
        exit;
    }

    public function saveQuizProgress()
    {
        if($this->get['time']){
            $t = explode(':', $this->get['time']);
            $_SESSION['quiz']['time'] = ($t[0] * 60) + $t[1] ;
        }
        $userID = $_SESSION['quiz']['userID'];
        $quizID = $_SESSION['quiz']['quizID'];
        $total = $_SESSION['quiz']['total'];
        $answerData = json_encode($_SESSION['quiz']['answerData']);
        $questionData = json_encode($_SESSION['quiz']['questionData']);
        $quizResult = ORM::for_table('quizResults')
            ->where('userID', $userID)
            ->where('quizID', $quizID)
            ->where('completed', 0)
            ->find_one();
        if(empty($quizResult)){
            $quizResult = ORM::for_table('quizResults')->create();
        }
        $quizResult->userID = $userID;
        $quizResult->quizID = $quizID;
        $quizResult->answerData = $answerData;
        $quizResult->questionData = $questionData;
        $quizResult->whenAdded = date("Y-m-d H:i:s");
        $quizResult->time = $_SESSION['quiz']['time'] ?? null;
        $quizResult->total = $total;
        $quizResult->save();

        echo json_encode([
            'success' => true,
            'message' => 'Test progress has been saved!',
        ]);
        exit();
    }

    public function courseModules($course, $parentModules = true)
    {
        $modules = ORM::for_table("courseModules")
            ->where("courseID", $course->id);
        if($parentModules){
            $modules = $modules->where_null("parentID");
        }

        return $modules->order_by_asc("ord")->find_many();

    }

}