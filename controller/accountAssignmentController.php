<?php
require_once(__DIR__ . '/mediaController.php');

class accountAssignmentController extends Controller {

    /**
     * @var mediaController
     */
    protected $media;

    /**
     * @var string
     */
    protected $table;

    public function __construct()
    {

        $this->post = $_POST;
        $this->get = $_GET;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;

        $this->table = "accountAssignments";

        // if user is not logged in then dont let them do anything
        if (CUR_ID_FRONT == "") {
            header('Location: '.SITE_URL.'blume/login');
            exit;
        }

        $this->media = new mediaController();
    }

    public function getAssignAssignments($studentID = null) {
        $assignments = [];
        $studentID = $studentID ?? CUR_ID_FRONT;
        $assignedCourses = ORM::for_table('coursesAssigned')
            ->where('accountID', $studentID)
            ->order_by_asc('whenAssigned')
            ->find_many();
        if (count($assignedCourses) >= 1) {
            $courseIDs = [];
            foreach ($assignedCourses as $assignedCourse) {
                $courseIDs[] = $assignedCourse->courseID;
            }
            $assignments = ORM::For_table("courseModules")
                ->where_in('courseID', $courseIDs)
                ->where("contentType", "assignment")
                ->order_by_asc('courseID')
                ->find_many();
        }
        return $assignments;

    }

    public function getAssignmentFileUploader()
    {
        $accountID = $this->get['accountID'] ?? CUR_ID_FRONT;
        $moduleID = $this->get['moduleID'];
        $assignmentID = $this->get['assignmentID'] ?? null;
        $uploadAssignment = ORM::for_table('accountAssignments')->find_one($assignmentID);
        $uploaderID = $accountID . '_' . $moduleID;
        $companionUrl = NSA_APP_API_URL.'assignments/'.$accountID.'/'.$moduleID;
        if($this->get['action'] && $this->get['action'] == 'teacher'){
            $companionUrl .= '/teacher/nsa';
        }else{
            $companionUrl .= '/user/nsa';
        }
    ?>
        <div id="drag-drop-area<?= $uploaderID ?>"></div>
        <script>

            const uppy<?= $moduleID ?> = new Uppy()
                .use(Dashboard, {
                    inline: true,
                    //proudlyDisplayPoweredByUppy: false,
                    //showLinkToFileUploadResult: false,
                    height: 300,
                    target: '#drag-drop-area<?= $uploaderID ?>'
                })
                .use(AwsS3Multipart, {
                    limit: 2,
                    companionUrl: '<?= $companionUrl?>',
                    //hideUploadButton: true
                })

            uppy<?= $moduleID ?>.on('complete', (result) => {
                console.log('Upload complete! We’ve uploaded these files:', result.successful);
                Swal.fire(
                    'Success!',
                    'Your files has been uploaded successfully!',
                    'success'
                ).then(function() {
                    <?php if(@$this->get['reloadDiv'] && ($this->get['action'] == 'teacher')){ ?>
                        $("#uploadTeacherFiles<?= $moduleID ?>").load("<?= SITE_URL ?>ajax?c=accountAssignment&a=uploadTeacherFiles&action=teacher&moduleID=<?= $moduleID ?>&assignmentID=<?= $assignmentID ?>&status=<?= $uploadAssignment->status ?>");
                    <?php }elseif(@$this->get['reloadDiv']){?>
                    $("#uploadUserFiles<?= $moduleID ?>").load("<?= SITE_URL ?>ajax?c=accountAssignment&a=uploadUserFiles&moduleID=<?= $moduleID ?>&assignmentID=<?= $assignmentID ?>");
                    <?php } ?>
                    //location.reload();
                });
            })
        </script>
    <?php
    }

    public function getAssignmentByModuleID($moduleID, $studentID = null)
    {
        $studentID = $studentID ?? CUR_ID_FRONT;
        return ORM::for_table('accountAssignments')
            ->where('moduleID', $moduleID)
            ->where('accountID', $studentID)
            ->find_one();
    }

    public function getUserFilesByAssignmentID($assignmentID)
    {
        if($assignmentID){
            return $this->media->getMedia('accountAssignmentController', $assignmentID, 'userAssignment', true);
        }
        return null;
    }

    public function getTeacherFilesByAssignmentID($assignmentID)
    {
        if($assignmentID){
            return $this->media->getMedia('accountAssignmentController', $assignmentID, 'teacherAssignment', true);
        }
        return null;
    }

    public function uploadUserFiles()
    {
        $files = $this->getUserFilesByAssignmentID($_GET['assignmentID']);
        $accountID = $_GET['accountID'] ?? CUR_ID_FRONT;
        ?>
        <div class="userAssignmentSection">
            <h3><?= $_GET['action'] == 'teacher' ? 'User Submitted Files' : 'My Submitted Files'?></h3>
            <?php
                if(count($files) >= 1) {
            ?>
                    <ul>
                        <?php
                        foreach ($files as $file) {
                            ?>
                            <li class="filerow<?= $file->id; ?>">
                                <a target="_blank" href="<?php echo AWSService::getFromS3('assignments/'.$accountID.'/'.$_GET['moduleID'].'/'.$file->fileName); ?>"><?= $file->fileName ?? $file->title ?></a>
                                <?php if($_GET['action'] == 'teacher' || $_GET['status'] == '3'){

                                } else{ ?>
                                    <a href="javascript:void(0);" class="delFile<?= $_GET['assignmentID'] ?> ml-2" rel="<?= $file->id; ?>"><i class="fa fa-trash text-danger"></i></a>
                                <?php }?>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>

                    <script>
                        $(".delFile<?= $_GET['assignmentID'] ?>").on('click', function (){

                            Swal.fire({
                                title: 'Are you sure want to delete?',
                                //showDenyButton: true,
                                showCancelButton: true,
                                confirmButtonText: 'Delete',
                                confirmButtonColor: '#f5393d',
                                //denyButtonText: `Don't save`,
                            }).then((result) => {
                                /* Read more about isConfirmed, isDenied below */
                                if (result.isConfirmed) {
                                    var rel = $(this).attr('rel');
                                    var route = '<?= SITE_URL ?>ajax?c=accountAssignment&a=deleteUserFile&assignmentID=<?= $_GET['assignmentID'] ?>&mediaID='+rel;
                                    $.ajax({
                                        type: "POST",
                                        url: route,
                                        data: {},
                                        success: function (msg) {
                                            //jQuery('#returnStatusAddNew').html(msg);
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Delete Successfully!',
                                                //showDenyButton: true,
                                                //showCancelButton: true,
                                                confirmButtonText: 'OK',
                                                //denyButtonText: `Don't save`,
                                            }).then((result) => {
                                                /* Read more about isConfirmed, isDenied below */
                                                if (result.isConfirmed) {
                                                    $(".filerow"+rel).remove();
                                                }
                                            });
                                        },
                                    });
                                }
                            });
                        });
                    </script>
            <?php
                } elseif ($_GET['action'] == 'teacher') {

                }else{
                    echo "<p>No file uploaded!</p>";
                }
            ?>
        </div>
        <?php
    }
    public function uploadTeacherFiles()
    {
        $files = $this->getTeacherFilesByAssignmentID($_GET['assignmentID']);
        $accountID = $_GET['accountID'] ?? CUR_ID_FRONT;
        ?>
        <div class="userAssignmentSection">
            <h3><?= $_GET['action'] == 'teacher' ? 'My Submitted Files' : 'Teacher Uploaded Files'?></h3>
            <?php
                if(count($files) >= 1) {
            ?>

                <ul>
                    <?php
                    foreach ($files as $file) {
                        ?>
                        <li class="filerow<?= $file->id; ?>">
                            <a target="_blank" href="<?php echo AWSService::getFromS3('assignments/'.$accountID.'/'.$_GET['moduleID'].'/'.$file->fileName); ?>"><?= $file->fileName ?? $file->title ?></a>
                            <?php if($_GET['action'] == 'user' || $_GET['status'] == '3'){

                            } else{ ?>
                                <a href="javascript:void(0);" class="delFile<?= $_GET['assignmentID'] ?> ml-2" rel="<?= $file->id; ?>"><i class="fa fa-trash text-danger"></i></a>
                            <?php }?>
                        </li>
                        <?php
                    }
                    ?>
                </ul>

                <script>
                    $(".delFile<?= $_GET['assignmentID'] ?>").on('click', function (){

                        Swal.fire({
                            title: 'Are you sure want to delete?',
                            //showDenyButton: true,
                            showCancelButton: true,
                            confirmButtonText: 'Delete',
                            confirmButtonColor: '#f5393d',
                            //denyButtonText: `Don't save`,
                        }).then((result) => {
                            /* Read more about isConfirmed, isDenied below */
                            if (result.isConfirmed) {
                                var rel = $(this).attr('rel');
                                var route = '<?= SITE_URL ?>ajax?c=accountAssignment&a=deleteUserFile&assignmentID=<?= $_GET['assignmentID'] ?>&mediaID='+rel;
                                $.ajax({
                                    type: "POST",
                                    url: route,
                                    data: {},
                                    success: function (msg) {
                                        //jQuery('#returnStatusAddNew').html(msg);
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Delete Successfully!',
                                            //showDenyButton: true,
                                            //showCancelButton: true,
                                            confirmButtonText: 'OK',
                                            //denyButtonText: `Don't save`,
                                        }).then((result) => {
                                            /* Read more about isConfirmed, isDenied below */
                                            if (result.isConfirmed) {
                                                $(".filerow"+rel).remove();
                                            }
                                        });
                                    },
                                });
                            }
                        });
                    });
                </script>
            <?php
                } elseif($_GET['action'] == 'user'){
            ?>
                    <p>Awaiting Files</p>
            <?php
                }
            ?>
        </div>
        <?php
    }

    public function deleteUserFile()
    {
        $assignmentID = $_GET['assignmentID'];
        $mediaID = $_GET['mediaID'];
        $assignment = ORM::for_table('accountAssignments')
            ->where('id', $assignmentID)
            ->where('accountID', CUR_ID_FRONT)
            ->find_one();

        if($assignment->id){
            $this->media->deleteMediaByModel('accountAssignmentController', $assignment->id, 'userAssignment', true, $mediaID);
        }

        $data = [
            'error' => false,
            'success' => true,
        ];
        echo json_encode($data);
        exit();
    }

    public function submitFeedback()
    {
        $assignment = ORM::for_table('accountAssignments')->find_one($this->post['assignmentID']);
        $assignment->comments = $this->post['comments'];
        $assignment->status = $this->post['completed'] == 'on' ? 3 : 2;
        $assignment->save();
    }
}
