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
            $duringQuiz = $this->controller->getModuleQuiz($module->id, 'd');
            if(empty($duringQuiz)) {
                ?>
                <label class="label label-danger label-small">Not Set</label>
                <?php
            }
            ?>

            <a href="javascript:;" onclick="$('#editModuleQuizContents<?= $module->id ?>').slideToggle();">Show / Hide</a>
        </label>
        <div id="editModuleQuizContents<?= $module->id ?>" style="padding:10px 20px;border-radius:15px;background:#eee;">

            <?php
            if(empty($duringQuiz)) {
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
                <form name="updateQuiz<?= $duringQuiz->id ?>">
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>What % does a user have to score in order to pass?</label>
                                <input type="text" class="form-control" name="passingPercentage" value="<?= $duringQuiz->passingPercentage ?>" placeholder="0" />
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Maximum Number of Questions?</label>
                                <input type="text" class="form-control" name="maxQuestionValue" value="<?= @$duringQuiz->maxQuestionValue ? $duringQuiz->maxQuestionValue : ''  ?>" placeholder="" />
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Time Limit (in minutes)</label>
                                <input type="text" class="form-control" name="timeLimit" value="<?= @$duringQuiz->timeLimit ? $duringQuiz->timeLimit : ''  ?>" placeholder="" />
                            </div>
                        </div>
                        <div class="col-xs-4 pt30">
                            <div class="form-group">
                                <input type="submit" class="btn btn-warning btn-small" value="Update Quiz" />
                                <a href="javascript:;" onclick="deleteQuiz<?= $moduleQuiz->id ?>();"class="btn btn-danger btn-small ml10 "><i class="fa fa-trash"></i>Delete Quiz</a>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="quizID" value="<?= $duringQuiz->id ?>" />
                </form>

            <hr />

                <h4>Questions
<!--                    <a href="javascript:;" class="btn btn-system pull-right btn-small" style="margin-left:5px;">-->
<!--                        <i class="fa fa-sort"></i>-->
<!--                        Re-order-->
<!--                    </a>-->
                    <a href="javascript:;" data-toggle="modal" data-target="#addQuestion<?= $duringQuiz->id ?>" class="btn btn-success pull-right btn-small">
                        <i class="fa fa-plus"></i>
                        Add
                    </a>
                </h4>

                <div id="quizQuestionsAjax<?= $duringQuiz->id ?>"></div>

                <script type="text/javascript">
                    $( document ).ready(function() {
                        $("#quizQuestionsAjax<?= $duringQuiz->id ?>").load("<?= SITE_URL ?>ajax?c=blumeNew&a=get-module-quiz-questions&id=<?= $duringQuiz->id ?>");
                    });
                </script>

                <div class="modal fade" id="addQuestion<?= $duringQuiz->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Add Question</h4>
                            </div>
                            <form name="addQuizQuestion<?= $duringQuiz->id ?>">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Question</label>
                                        <input type="text" name="question" class="form-control" placeholder="How do you define a constant in PHP?" />
                                    </div>
                                    <input type="hidden" name="quizID" value="<?= $duringQuiz->id ?>" />
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
                $this->renderFormAjax("blumeNew", "update-module-quiz", "updateQuiz".$duringQuiz->id, '#returnStatus', false);
                $this->renderFormAjax("blumeNew", "create-quiz-question", "addQuizQuestion".$duringQuiz->id, '#returnStatus', false);
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

<div class="col-xs-12">

    <div class="form-group">
        <label>
            After Module Quiz

            <?php
            $afterQuiz = $this->controller->getModuleQuiz($module->id, 'a');
            if(empty($afterQuiz)) {
                ?>
                <label class="label label-danger label-small">Not Set</label>
                <?php
            }
            ?>

            <a href="javascript:;" onclick="$('#editModuleQuizContents2<?= $module->id ?>').slideToggle();">Show / Hide</a>
        </label>
        <div id="editModuleQuizContents2<?= $module->id ?>" style="display:none;padding:10px 20px;border-radius:15px;background:#eee;">

            <?php
            if(empty($afterQuiz)) {
                ?>
                <h4>Add Second Quiz</h4>
                <p><small>You're about to add a quiz to this module. You can select whether this quiz appears as part of the module, or after the module. If this quiz appears after, a user will have to complete the quiz before proceeding to the next module (or to pass the course if this is the last module).</small></p>
                <form name="addQuiz2<?= $module->id ?>">
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
            $this->renderFormAjax("blumeNew", "create-module-quiz", "addQuiz2".$module->id, '#returnStatus', false);
            } else {
            ?>
                <h4>Manage Quiz</h4>
                <form name="updateQuiz2<?= $afterQuiz->id ?>">
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>What % does a user have to score in order to pass?</label>
                                <input type="text" class="form-control" name="passingPercentage" value="<?= $afterQuiz->passingPercentage ?>" placeholder="0" />
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Maximum Number of Questions?</label>
                                <input type="text" class="form-control" name="maxQuestionValue" value="<?= @$afterQuiz->maxQuestionValue ? $afterQuiz->maxQuestionValue  : ''?>" placeholder="" />
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>Time Limit (in minutes)</label>
                                <input type="text" class="form-control" name="timeLimit" value="<?= @$afterQuiz->timeLimit ? $afterQuiz->timeLimit  : ''?>" placeholder="" />
                            </div>
                        </div>
                        <div class="col-xs-4 pt30">
                            <input type="hidden" name="appear" value="a">
                            <input type="submit" class="btn btn-warning btn-small" value="Update Quiz" />
                            <a href="javascript:;" onclick="deleteQuiz<?= $moduleQuiz->id ?>();"class="btn btn-danger btn-small ml10 "><i class="fa fa-trash"></i>Delete Quiz</a>
                        </div>
                    </div>
                    <input type="hidden" name="quizID" value="<?= $afterQuiz->id ?>" />
                </form>

            <hr />

                <h4>Questions
<!--                    <a href="javascript:;" class="btn btn-system pull-right btn-small" style="margin-left:5px;">-->
<!--                        <i class="fa fa-sort"></i>-->
<!--                        Re-order-->
<!--                    </a>-->
                    <a href="javascript:;" data-toggle="modal" data-target="#addQuestion2<?= $afterQuiz->id ?>" class="btn btn-success pull-right btn-small">
                        <i class="fa fa-plus"></i>
                        Add
                    </a>
                </h4>

                <div id="quizQuestionsAjax<?= $afterQuiz->id ?>"></div>

                <script type="text/javascript">
                    $( document ).ready(function() {
                        $("#quizQuestionsAjax<?= $afterQuiz->id ?>").load("<?= SITE_URL ?>ajax?c=blumeNew&a=get-module-quiz-questions&id=<?= $afterQuiz->id ?>");
                    });
                </script>

                <div class="modal fade" id="addQuestion2<?= $afterQuiz->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Add Question</h4>
                            </div>
                            <form name="addQuizQuestion2<?= $afterQuiz->id ?>">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Question</label>
                                        <input type="text" name="question" class="form-control" placeholder="How do you define a constant in PHP?" />
                                    </div>
                                    <input type="hidden" name="quizID" value="<?= $afterQuiz->id ?>" />
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
                $this->renderFormAjax("blumeNew", "update-module-quiz", "updateQuiz2".$afterQuiz->id, '#returnStatus', false);
                $this->renderFormAjax("blumeNew", "create-quiz-question", "addQuizQuestion2".$afterQuiz->id, '#returnStatus', false);
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