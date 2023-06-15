<style>
    .module .form-group{
        /*padding: 20px;*/
    }
    .module {
        background: #f5f5f5;
        padding: 20px;
        margin-bottom: 15px;
        position: relative;
        border-radius: 15px;
    }
</style>
<div class="col-xs-12">

    <div class="form-group">
        <label>
            During Module Quiz

            <?php
            $moduleQuiz = $this->controller->getModuleQuiz($module->id, "d");
            if(empty($moduleQuiz)) {
                ?>
                <label class="label label-danger label-small">Not Set</label>
                <?php
            }
            ?>

            <a href="javascript:;" onclick="$('#editModuleQuizContents<?= $module->id ?>').slideToggle();">Show / Hide</a>
        </label>
        <div id="editModuleQuizContents<?= $module->id ?>" style="padding:10px 20px;border-radius:15px;background:#eee;">

            <?php
            if(empty($moduleQuiz)) {
                ?>
                <h4>Add Quiz</h4>
                <p><small>You're about to add a quiz to this module. You can select whether this quiz appears as part of the module, or after the module. If this quiz appears after, a user will have to complete the quiz before proceeding to the next module (or to pass the course if this is the last module).</small></p>
                <form name="addQuiz<?= $module->id ?>">
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>What % does a user have to score in order to pass?</label>
                                <input type="text" class="form-control" name="passingPercentage" placeholder="0" />
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Maximum Number of Questions?</label>
                                <input type="text" class="form-control" name="maxQuestionValue" value="" placeholder="" />
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Time Limit (in minutes)</label>
                                <input type="text" class="form-control" name="timeLimit" value="" placeholder="" />
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <input type="hidden" value="d" name="appear">
                            <input type="submit" class="btn btn-success" value="Create" />
                        </div>
                    </div>
                    <input type="hidden" name="moduleID" value="<?= $module->id ?>" />
                </form>
            <?php
            $this->renderFormAjax("blumeNew", "create-module-quiz", "addQuiz".$module->id, '#returnStatus', false);
            } else {
            ?>
                <h4>Manage Quiz</h4>
                <form name="updateQuiz<?= $moduleQuiz->id ?>">
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>What % does a user have to score in order to pass?</label>
                                <input type="text" class="form-control" name="passingPercentage" value="<?= $moduleQuiz->passingPercentage ?>" placeholder="0" />
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Maximum Number of Questions?</label>
                                <input type="text" class="form-control" name="maxQuestionValue" value="<?= @$moduleQuiz->maxQuestionValue ? $moduleQuiz->maxQuestionValue : ''  ?>" placeholder="" />
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Time Limit (in minutes)</label>
                                <input type="text" class="form-control" name="timeLimit" value="<?= @$moduleQuiz->timeLimit ? $moduleQuiz->timeLimit : ''  ?>" placeholder="" />
                            </div>
                        </div>
                        <div class="col-xs-4 pt30">
                            <div class="form-group">
                                <label>Show this quiz:</label>
                                <select class="form-control" name="appear">
                                    <option value="m">Not Active</option>
                                    <option value="d" <?php if($moduleQuiz->appear == "d") { ?>selected<?php } ?>>During Module</option>
                                    <option value="a" <?php if($moduleQuiz->appear == "a") { ?>selected<?php } ?>>After / End Of Module</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-4 pt30" style="padding-top: 63px !important;">
                            <div class="form-group">
                                <input type="submit" class="btn btn-warning btn-small" value="Update Quiz" />
                                <a href="javascript:;" onclick="deleteQuiz<?= $moduleQuiz->id ?>();"class="btn btn-danger btn-small ml10 "><i class="fa fa-trash"></i>Delete Quiz</a>
                                <a target="_blank" href="<?= SITE_URL ?>ajax?c=blumeNew&a=exportQuizCsv&quizId=<?= $moduleQuiz->id ?>&quizappear=<?= $moduleQuiz->appear ?>" class="btn btn-dark btn-small ml10 "><i class="fa fa-download"></i> Download Quiz CSV</a>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="quizID" value="<?= $moduleQuiz->id ?>" />
                </form>

            <hr />

                <h4>Questions
                    <!--                    <a href="javascript:;" class="btn btn-system pull-right btn-small" style="margin-left:5px;">-->
                    <!--                        <i class="fa fa-sort"></i>-->
                    <!--                        Re-order-->
                    <!--                    </a>-->
                    <a href="javascript:;" data-toggle="modal" data-target="#addQuestion<?= $moduleQuiz->id ?>" class="btn btn-success pull-right btn-small">
                        <i class="fa fa-plus"></i>
                        Add
                    </a>
                </h4>

                <div id="quizQuestionsAjax<?= $moduleQuiz->id ?>"></div>

                <script type="text/javascript">
                    $( document ).ready(function() {
                        $("#quizQuestionsAjax<?= $moduleQuiz->id ?>").load("<?= SITE_URL ?>ajax?c=blumeNew&a=get-module-quiz-questions&id=<?= $moduleQuiz->id ?>");
                    });
                </script>

                <div class="modal fade" id="addQuestion<?= $moduleQuiz->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Add Question</h4>
                            </div>
                            <form name="addQuizQuestion<?= $moduleQuiz->id ?>">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Question</label>
                                        <input type="text" name="question" class="form-control" placeholder="How do you define a constant in PHP?" />
                                    </div>
                                    <input type="hidden" name="quizID" value="<?= $moduleQuiz->id ?>" />
                                    <p>You'll be able to add answers and other information afterwards.</p>
                                    <div id="returnStatusAddNew"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Add Question</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php
                $this->renderFormAjax("blumeNew", "update-module-quiz", "updateQuiz".$moduleQuiz->id, '#returnStatus', false);
                $this->renderFormAjax("blumeNew", "create-quiz-question", "addQuizQuestion".$moduleQuiz->id, '#returnStatus', false);
            }
            ?>
            <script>
                function deleteQuiz<?= $moduleQuiz->id ?>() {
                    if (window.confirm("Are you sure you want to delete this Quiz?")) {
                        $("#returnStatus").load("<?= SITE_URL ?>ajax?c=blumeNew&a=deleteQuiz&id=<?= $moduleQuiz->id ?>");
                        window.location.reload();
                    }
                }    
            </script>

        </div>
    </div>

</div>


<?php
$rand = rand(1,99999);
?>
<div class="col-xs-12">

    <div class="form-group">
        <label>
            After Module Quiz

            <?php
            $moduleQuiz = $this->controller->getModuleQuiz($module->id, "a");
            if(empty($moduleQuiz)) {
                ?>
                <label class="label label-danger label-small">Not Set</label>
                <?php
            }
            ?>

            <a href="javascript:;" onclick="$('#editModuleQuizContents<?= $module->id ?><?= $rand ?>').slideToggle();">Show / Hide</a>
        </label>
        <div id="editModuleQuizContents<?= $module->id ?><?= $rand ?>" style="padding:10px 20px;border-radius:15px;background:#eee;">

            <?php
            if(empty($moduleQuiz)) {
                ?>
                <h4>Add Quiz</h4>
                <p><small>You're about to add a quiz to this module. You can select whether this quiz appears as part of the module, or after the module. If this quiz appears after, a user will have to complete the quiz before proceeding to the next module (or to pass the course if this is the last module).</small></p>
                <form name="addQuiz<?= $module->id ?><?= $rand ?>">
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>What % does a user have to score in order to pass?</label>
                                <input type="text" class="form-control" name="passingPercentage" placeholder="0" />
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Maximum Number of Questions?</label>
                                <input type="text" class="form-control" name="maxQuestionValue" value="" placeholder="" />
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Time Limit (in minutes)</label>
                                <input type="text" class="form-control" name="timeLimit" value="" placeholder="" />
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <input type="hidden" value="a" name="appear">
                            <input type="submit" class="btn btn-success" value="Create" />
                        </div>
                    </div>
                    <input type="hidden" name="moduleID" value="<?= $module->id ?>" />
                </form>
            <?php
            $this->renderFormAjax("blumeNew", "create-module-quiz", "addQuiz".$module->id.$rand, '#returnStatus', false);
            } else {
            ?>
                <h4>Manage Quiz</h4>
                <form name="updateQuiz<?= $moduleQuiz->id ?><?= $rand ?>">
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>What % does a user have to score in order to pass?</label>
                                <input type="text" class="form-control" name="passingPercentage" value="<?= $moduleQuiz->passingPercentage ?>" placeholder="0" />
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Maximum Number of Questions?</label>
                                <input type="text" class="form-control" name="maxQuestionValue" value="<?= @$moduleQuiz->maxQuestionValue ? $moduleQuiz->maxQuestionValue : ''  ?>" placeholder="" />
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Time Limit (in minutes)</label>
                                <input type="text" class="form-control" name="timeLimit" value="<?= @$moduleQuiz->timeLimit ? $moduleQuiz->timeLimit : ''  ?>" placeholder="" />
                            </div>
                        </div>
                        <div class="col-xs-4 pt30">
                            <div class="form-group">
                                <label>Show this quiz:</label>
                                <select class="form-control" name="appear">
                                    <option value="m">Not Active</option>
                                    <option value="d" <?php if($moduleQuiz->appear == "d") { ?>selected<?php } ?>>During Module</option>
                                    <option value="a" <?php if($moduleQuiz->appear == "a") { ?>selected<?php } ?>>After / End Of Module</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-5 pt30" style="padding-top: 63px !important;">
                            <div class="form-group">
                                <input type="submit" class="btn btn-warning btn-small" value="Update Quiz" />
                            <a href="javascript:;" onclick="deleteQuiz<?= $moduleQuiz->id ?>();"class="btn btn-danger btn-small ml10 "><i class="fa fa-trash"></i>Delete Quiz</a>
                                <a target="_blank" href="<?= SITE_URL ?>ajax?c=blumeNew&a=exportQuizCsv&quizId=<?= $moduleQuiz->id ?>&quizappear=<?= $moduleQuiz->appear ?>" class="btn btn-dark btn-small ml10 "><i class="fa fa-download"></i> Download Quiz CSV</a>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="quizID" value="<?= $moduleQuiz->id ?>" />
                </form>

            <hr />

                <h4>Questions
                    <!--                    <a href="javascript:;" class="btn btn-system pull-right btn-small" style="margin-left:5px;">-->
                    <!--                        <i class="fa fa-sort"></i>-->
                    <!--                        Re-order-->
                    <!--                    </a>-->
                    <a href="javascript:;" data-toggle="modal" data-target="#addQuestion<?= $moduleQuiz->id ?><?= $rand ?>" class="btn btn-success pull-right btn-small">
                        <i class="fa fa-plus"></i>
                        Add
                    </a>
                </h4>

                <div id="quizQuestionsAjax<?= $moduleQuiz->id ?>"></div>

                <script type="text/javascript">
                    $( document ).ready(function() {
                        $("#quizQuestionsAjax<?= $moduleQuiz->id ?>").load("<?= SITE_URL ?>ajax?c=blumeNew&a=get-module-quiz-questions&id=<?= $moduleQuiz->id ?>");
                    });
                </script>

                <div class="modal fade" id="addQuestion<?= $moduleQuiz->id ?><?= $rand ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Add Question</h4>
                            </div>
                            <form name="addQuizQuestion<?= $moduleQuiz->id.$rand ?>">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Question</label>
                                        <input type="text" name="question" class="form-control" placeholder="How do you define a constant in PHP?" />
                                    </div>
                                    <input type="hidden" name="quizID" value="<?= $moduleQuiz->id ?>" />
                                    <p>You'll be able to add answers and other information afterwards.</p>
                                    <div id="returnStatusAddNew"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Add Question</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php
                $this->renderFormAjax("blumeNew", "update-module-quiz", "updateQuiz".$moduleQuiz->id.$rand, '#returnStatus', false);
                $this->renderFormAjax("blumeNew", "create-quiz-question", "addQuizQuestion".$moduleQuiz->id.$rand, '#returnStatus', false);
            }
            ?>
            <script>
                function deleteQuiz<?= $moduleQuiz->id ?>() {
                    if (window.confirm("Are you sure you want to delete this Quiz?")) {
                        $("#returnStatus").load("<?= SITE_URL ?>ajax?c=blumeNew&a=deleteQuiz&id=<?= $moduleQuiz->id ?>");
                        window.location.reload();
                    }
                }    
            </script>

        </div>
    </div>

</div>