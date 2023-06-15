<?php
$this->setControllers(array("account", "accountAssignment", "courseModule", "course"));
$pageTitle = "Student Progress";
$this->account->restrictUserAccessTo("tutor", "IQA");
include BASE_PATH . 'account.header.php';

$studentID = $_GET["request"];
if ($this->controller->isValidStudent($studentID) === false) {
    header('Location: '.SITE_URL);
    exit;
}
$assignments = $this->accountAssignment->getAssignAssignments($studentID);
$courses = [];
$courseIds = [];
if(count($assignments) >= 1) {
    foreach ($assignments as $assignment) {
        if(!in_array($assignment->courseID, $courseIds)) {
            $course = $this->course->getCourseByID($assignment->courseID);
            $courseIds[] = $assignment->courseID;
            $courses[$assignment->courseID]['title'] = $course->title;
        }
        $courses[$assignment->courseID]['assignments'][] = $assignment;
    }
}

$activeTab = $_GET['id'] ?? null;

$student = ORM::for_table("accounts")->find_one($studentID);

?>
    <link rel="stylesheet" href="https://releases.transloadit.com/uppy/v2.1.0/uppy.min.css">
    <style>
        .uppy-size--md .uppy-Dashboard-inner{
            margin: 0 auto;
        }
    </style>
    <script src="<?= SITE_URL ?>dist/bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.5/dist/sweetalert2.all.min.js"></script>
    <section class="page-title">
        <div class="container">
            <h1><?= $student->firstname.' '.$student->lastname ?>'s Progress</h1>
        </div>
    </section>
    <section class="page-content">
        <div class="container">
            <div  class="row">
                <div class="col-12 regular-full">
                    <div class="row">
                        <div class="col-12 col-md-12 notification pt-0">
                            <?php
                            if(count($courses) >= 1) {
                                foreach($courses as $courseID => $course) {
                                    ?>
                                    <div class="row assignAccordion">
                                        <h3><?= $course['title'];?></h3>
                                        <div id="accordion<?= $courseID?>" class="col-12">
                                            <?php
                                            $ai = 0;
                                            foreach ($course['assignments'] as $module){
                                                $title = '';
                                                $status = '';
                                                if($module->parentID){
                                                    $parentModule = $this->courseModule->getModuleByID($module->parentID);
                                                    $title = $parentModule->title.' - ';
                                                }

                                                $uploadAssignment = $this->accountAssignment->getAssignmentByModuleID($module->id, $studentID);
                                                if($uploadAssignment) {
                                                    if($uploadAssignment->status == 1){
                                                        $htmlStatus = "<label class='status payment_pending'>Pending Feedback</label>";
                                                    }else if($uploadAssignment->status == 2){
                                                        $htmlStatus = "<label class='status refer'>Refer</label>";
                                                    }else if($uploadAssignment->status == 3){
                                                        $htmlStatus = "<label class='status completed'>Pass</label>";
                                                    }else{
                                                        $htmlStatus = "<label class='status payment_pending'>Payment Pending</label>";
                                                    }

                                                    $status = $uploadAssignment->status;
                                                }else{
                                                    $htmlStatus = '<label class="status">Not Received </label>';
                                                }

                                                $uploadOriginalFiles = $this->courseModule->getModuleAssignments($module->id);
                                                $uploadUserFiles = $this->accountAssignment->getUserFilesByAssignmentID($uploadAssignment->id);
                                                $teacherComments = "";
                                                ?>
                                                <div class="card grey-bg-box">
                                                    <div class="card-header" id="module<?= $module->id?>">
                                                        <h5 class="mb-0">
                                                            <a class="faq-title <?php if(@$activeTab && $activeTab != $uploadAssignment->id){?> collapsed <?php } ?>" data-toggle="collapse" data-target="#module<?= $module->id?>Data" aria-expanded="true" aria-controls="module<?= $module->id?>Data">
                                                                <?= $title.$module->title ?>
                                                            </a>
                                                            <?= $htmlStatus ?>
                                                        </h5>
                                                    </div>

                                                    <div id="module<?= $module->id?>Data" class="collapse <?php if($activeTab && $activeTab == $uploadAssignment->id){?> show <?php } ?>" aria-labelledby="module<?= $module->id?>" data-parent="#accordion<?= $courseID?>">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-4">

                                                                    <?php
                                                                    if(count($uploadOriginalFiles) >= 1) {
                                                                        ?>
                                                                        <div class="userAssignmentSection">
                                                                            <h3>Original Workbook</h3>
                                                                            <ul>
                                                                                <?php
                                                                                foreach ($uploadOriginalFiles as $file) {
                                                                                    ?>
                                                                                    <li><a target="_blank" href="<?= $file->url ?>"><?= $file->fileName ?? $file->title ?></a></li>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </ul>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                    <div id="uploadUserFiles<?= $module->id ?>">
                                                                        <p class="text-center">
                                                                            <i class="fa fa-spin fa-spinner"></i>
                                                                        </p>
                                                                    </div>
                                                                    <script type="text/javascript">
                                                                        $("#uploadUserFiles<?= $module->id ?>").load("<?= SITE_URL ?>ajax?c=accountAssignment&a=uploadUserFiles&action=teacher&moduleID=<?= $module->id ?>&assignmentID=<?= $uploadAssignment->id ?>&accountID=<?= $studentID ?>");
                                                                    </script>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <?php if($status >= 1 && $status != 3){?>
                                                                        <div class="userAssignmentSection">
                                                                            <h3>Upload Files</h3>
                                                                            <div class="files_to_upload">
                                                                                <div id="fileUploader<?= $module->id ?>">
                                                                                    <p class="text-center">
                                                                                        <i class="fa fa-spin fa-spinner"></i>
                                                                                    </p>
                                                                                </div>
                                                                                <script type="text/javascript">
                                                                                    $("#fileUploader<?= $module->id ?>").load("<?= SITE_URL ?>ajax?c=accountAssignment&a=getAssignmentFileUploader&action=teacher&moduleID=<?= $module->id ?>&reloadDiv=true&assignmentID=<?= $uploadAssignment->id ?>&accountID=<?= $studentID ?>");
                                                                                </script>
                                                                            </div>
                                                                        </div>
                                                                    <?php }?>

                                                                    <?php
                                                                        if(@$uploadAssignment && $uploadAssignment->status >= 1) {
                                                                            $teacherComments = $uploadAssignment->comments;
                                                                    ?>
                                                                            <div id="uploadTeacherFiles<?= $module->id ?>">
                                                                                <p class="text-center">
                                                                                    <i class="fa fa-spin fa-spinner"></i>
                                                                                </p>
                                                                            </div>
                                                                            <script type="text/javascript">
                                                                                $("#uploadTeacherFiles<?= $module->id ?>").load("<?= SITE_URL ?>ajax?c=accountAssignment&a=uploadTeacherFiles&action=teacher&moduleID=<?= $module->id ?>&accountID=<?= $studentID ?>&assignmentID=<?= $uploadAssignment->id ?>&status=<?= $uploadAssignment->status ?>");
                                                                            </script>

                                                                        <?php
                                                                        }
                                                                    ?>


                                                                    <?php if($status >= 1) { ?>
                                                                        <form name="submitFeedback<?= $uploadAssignment->id ?>">
                                                                            <div class="userAssignmentSection">
                                                                                <h3>Your Comments</h3>
                                                                                <textarea class="form-control" name="comments" id="" rows="5" ><?= $teacherComments ?></textarea>
                                                                                <div class="row mt-4">
                                                                                    <div class="form-group col-12">
                                                                                        <label class="labelText" for="rewardNotification<?= $uploadAssignment->id ?>">Completed</label>&nbsp;&nbsp;
                                                                                        <label class="nsa-switch">
                                                                                            <input id="rewardNotification<?= $uploadAssignment->id ?>" name="completed" type="checkbox" <?php if($status == 3){?>checked<?php }?>>
                                                                                            <span  class="slider round"></span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <?php if($status != 3) { ?>
                                                                                <div class="row">
                                                                                    <div class="col-12 text-center">
                                                                                        <button type="submit" class="btn btn-danger btn-lg">Submit Feedback</button>
                                                                                        <input type="hidden" name="assignmentID" value="<?= $uploadAssignment->id ?>">
                                                                                    </div>
                                                                                </div>
                                                                            <?php } ?>
                                                                        </form>
                                                                        <?php $this->renderFormAjax("accountAssignment", "submitFeedback", "submitFeedback".$uploadAssignment->id, '', false, false, 'Feedback submitted successfully!'); ?>
                                                                    <?php } ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                $ai++;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include BASE_PATH . 'account.footer.php'; ?>