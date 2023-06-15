<?php
$css = array("dashboard.css");
$pageTitle = "Documents";
include BASE_PATH . 'account-ncfe.header.php';
$this->setControllers(array("accountAssignment", "course", "courseModule", "cart"));
$assignments = $this->accountAssignment->getAssignAssignments();
$courses = [];
$courseIds = [];
if(count($assignments) >= 1) {
    foreach ($assignments as $assignment) {
        if(!in_array($assignment->courseID, $courseIds)) {
            $course = $this->course->getCourseByID($assignment->courseID);
            $courseIds[] = $assignment->courseID;
            $courses[$assignment->courseID]['title'] = $course->title;
            $courses[$assignment->courseID]['course'] = $course;
        }
        $courses[$assignment->courseID]['assignments'][] = $assignment;
    }
}
$selectedTab = $_GET['tab'] ?? 'my';

?>

<link rel="stylesheet" href="https://releases.transloadit.com/uppy/v2.1.0/uppy.min.css">
<style>
    .uppy-size--md .uppy-Dashboard-inner{
        margin: 0 auto;
    }
</style>
<script src="<?= SITE_URL ?>dist/bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.5/dist/sweetalert2.all.min.js"></script>
<style>

</style>

    <section class="page-title with-nav">
        <div class="container">
<!--            <a href="javascript:;" class="mainBtn" data-toggle="modal" data-target="#upload">-->
<!--                <i class="fas fa-plus"></i>-->
<!--                Upload-->
<!--            </a>-->
            <div class="row">
                <div class="col-12">
                    <h1><?= $pageTitle ?></h1>
                    <div id="userBasket" class="assessmentBasket">
                        <p class="text-center">
                            <i class="fa fa-spin fa-spinner"></i>
                        </p>
                    </div>
                    <script type="text/javascript">
                        $("#userBasket").load("<?= SITE_URL ?>ajax?c=cart&a=assignmentBasket");
                    </script>
                </div>
            </div>
            <ul class="nav navbar-nav inner-nav nav-tabs">
                <li class="nav-item link">
                    <a class="nav-link <?= $selectedTab == 'my' ? 'active show' : ''?>" href="#my-assignments" data-toggle="tab">My Assignments</a>
                </li>
                <li class="nav-item link ">
                    <a class="nav-link <?= $selectedTab == 'all' ? 'active show' : ''?>" href="#all-assignments" data-toggle="tab">All Assignments</a>
                </li>
                <li class="nav-item link ">
                    <a class="nav-link <?= $selectedTab == 'checkout' ? 'active show' : ''?>" href="#checkout-assignments" data-toggle="tab">Checkout</a>
                </li>
            </ul>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="loader_wrapper" style="display: none"><i class="fas fa-spin fa-spinner"></i></div>
            <div  class="row">
                <div class="col-12 regular-full">
                    <div class="tab-content container" style="padding:0;">

                        <div id="my-assignments" class="tab-pane <?= $selectedTab == 'my' ? 'active show' : ''?>">
                            <div class="row">
                                <div class="col-12 col-md-12 notification pt-0">
                                    <?php
                                        if(count($courses) >= 1) {
                                            $trainingCourseLevel2Completed = false;
                                            foreach($courses as $courseID => $course) {
//                                                echo "<pre>";
//                                                print_r($course);
//                                                die;
                                                $trainingCourseDisable = false;
                                                if(!empty($course['course']->personalTraningOrder)){
                                                    if($course['course']->personalTraningOrder == 1) {
                                                        $trainingCourseLevel2Completed = $this->course->checkCourseComplete($course);
                                                    }elseif ($trainingCourseLevel2Completed === false && ($course['course']->personalTraningOrder == 2)) {
                                                        $trainingCourseDisable = true;
                                                    }
                                                }
                                                if($trainingCourseDisable === false) {
                                        ?>
                                                    <div class="row assignAccordion">
                                                        <h3><?= $course['title'];?></h3>
                                                        <div id="accordion<?= $courseID?>" class="col-12">
                                            <?php
                                                            $ai = 0;
                                                            foreach ($course['assignments'] as $module){
                                                                $title = $module->title;
                                                                $status = '';
                                                                if($module->parentID){
                                                                    $parentModule = $this->courseModule->getModuleByID($module->parentID);
                                                                    $title = $parentModule->title;
                                                                }

                                                                $uploadAssignment = $this->accountAssignment->getAssignmentByModuleID($module->id);
                                                                if($uploadAssignment) {
                                                                    if($uploadAssignment->status == 1){
                                                                        $htmlStatus = "<label class='status awaiting_feedback'>Awaiting Feedback</label>";
                                                                    }else if($uploadAssignment->status == 2){
                                                                        $htmlStatus = "<label class='status refer'>Refer</label>";
                                                                    }else if($uploadAssignment->status == 3){
                                                                        $htmlStatus = "<label class='status completed'>Pass</label>";
                                                                    }else{
                                                                        $htmlStatus = "<label class='status payment_pending'>Payment Pending</label>";
                                                                    }

                                                                    $status = $uploadAssignment->status;
                                                                    $account = $this->controller->getAccountByID($uploadAssignment->accountID);
                                                                }else{
                                                                    $htmlStatus = '<label class="status">Not Sent</label>';
                                                                }

                                                                $uploadOriginalFiles = $this->courseModule->getModuleAssignments($module->id);
                                                                $uploadUserFiles = $this->accountAssignment->getUserFilesByAssignmentID($uploadAssignment->id);
                                            ?>
                                                                <div class="card grey-bg-box">
                                                                    <div class="card-header" id="module<?= $module->id?>">
                                                                        <h5 class="mb-0">
                                                                            <a class="faq-title<?php if($ai >= 1){?> collapsed <?php } ?>" data-toggle="collapse" data-target="#module<?= $module->id?>Data" aria-expanded="true" aria-controls="module<?= $module->id?>Data">
                                                                                <?= $title ?> <span>Assessment Fee <?= $this->price($this->cart->getAssessmentModulePrice()) ?></span>
                                                                            </a>
                                                                            <?= $htmlStatus ?>
                                                                        </h5>
                                                                    </div>

                                                                    <div id="module<?= $module->id?>Data" class="collapse <?php if($ai == 0){?> show <?php } ?>" aria-labelledby="module<?= $module->id?>" data-parent="#accordion<?= $courseID?>">
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
                                                                                        $("#uploadUserFiles<?= $module->id ?>").load("<?= SITE_URL ?>ajax?c=accountAssignment&a=uploadUserFiles&moduleID=<?= $module->id ?>&assignmentID=<?= $uploadAssignment->id ?>&status=<?= $uploadAssignment->status ?>");
                                                                                    </script>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <?php
                                                                                        if(@$uploadAssignment && $uploadAssignment->status >= 1) {
                                                                                            $teacherComments = $uploadAssignment->comments;
                                                                                    ?>      <div id="uploadTeacherFiles<?= $module->id ?>">
                                                                                                <p class="text-center">
                                                                                                    <i class="fa fa-spin fa-spinner"></i>
                                                                                                </p>
                                                                                            </div>
                                                                                            <script type="text/javascript">
                                                                                                $("#uploadTeacherFiles<?= $module->id ?>").load("<?= SITE_URL ?>ajax?c=accountAssignment&a=uploadTeacherFiles&action=user&moduleID=<?= $module->id ?>&assignmentID=<?= $uploadAssignment->id ?>&status=<?= $uploadAssignment->status ?>");
                                                                                            </script>
                                                                                            <div class="userAssignmentSection">
                                                                                                <h3>Teacher Comments</h3>
                                                                                                <?php if(empty($teacherComments)){ ?>
                                                                                                    <p>Awaiting Comments</p>
                                                                                                <?php } else { ?>
                                                                                                    <p><?= $teacherComments ?></p>
                                                                                                <?php }?>
                                                                                            </div>
                                                                                    <?php
                                                                                        }
                                                                                    ?>
                                                                                    <?php if($status == 3){?>
                                                                                        <div class="completeAssignmentSec">
                                                                                            Awesome work <?= $account->firstname?>! Keep it up.
                                                                                        </div>
                                                                                    <?php } else {?>
                                                                                        <div class="userAssignmentSection">
                                                                                            <h3>Upload Files</h3>
                                                                                            <div class="files_to_upload">
                                                                                                <div id="fileUploader<?= $module->id ?>">
                                                                                                    <p class="text-center">
                                                                                                        <i class="fa fa-spin fa-spinner"></i>
                                                                                                    </p>
                                                                                                </div>
                                                                                                <script type="text/javascript">
                                                                                                    $("#fileUploader<?= $module->id ?>").load("<?= SITE_URL ?>ajax?c=accountAssignment&a=getAssignmentFileUploader&moduleID=<?= $module->id ?>&reloadDiv=true&assignmentID=<?= $uploadAssignment->id ?>");
                                                                                                </script>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php }?>

                                                                                    <?php if($status != '' && $status == 0) { ?>
                                                                                        <div class="row">
                                                                                            <div class="col-12 text-center">
                                                                                                <button type="button" class="btn btn-danger btn-lg add_cart" rel="<?= $uploadAssignment->id ?>">Add To Order</button>
                                                                                            </div>
                                                                                        </div>
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
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div id="all-assignments" class="tab-pane <?= $selectedTab == 'all' ? 'active show' : ''?>">
                            <?php include ('includes/all_assignments.php')?>
                        </div>

                        <div id="checkout-assignments" class="tab-pane <?= $selectedTab == 'checkout' ? 'active show' : ''?>">
                            <?php include ('includes/checkout.php')?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="upload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="height:auto;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel" style="display:block;width:100%;">Upload Document</h4>
                </div>
                <form name="addNewItem">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Select file:</label>
                            <input type="file" name="file" style="padding:0;" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
                <?php
                $this->renderFormAjax("accountDocument", "upload-document", "addNewItem");
                ?>
            </div>
        </div>
    </div>


    <script>

        $(document).ready(function (){
            $('.with-nav ul li a').on('click', function (){
                var href = $(this).attr('href');
                if(href == '#checkout-assignments'){
                    var url = '<?= SITE_URL ?>dashboard/ncfe/assignments?tab=checkout';
                    window.location.href = url;
                }
            });
            $('.add_cart').on('click', function (){
                var assignmentID = $(this).attr('rel');
                const url = "<?= SITE_URL ?>ajax?c=cart&a=addAssessmentItem&id="+assignmentID;
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(response){

                        response = JSON.parse(response);
                        //
                        var status = response.status;
                        if (status == 200) {
                            $("#userBasket").load("<?= SITE_URL ?>ajax?c=cart&a=assignmentBasket");
                            Swal.fire({
                                title: 'Added to Cart',
                                //text: "You won't be able to revert this!",
                                //showDenyButton: true,
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonText: 'Continue to checkout',
                                confirmButtonColor: '#248cab',
                                cancelButtonText: "Add more modules",
                            }).then((result) => {
                                /* Read more about isConfirmed, isDenied below */
                                if (result.isConfirmed) {
                                    window.location.href = "<?= SITE_URL ?>dashboard/ncfe/assignments?tab=checkout";
                                }
                            });
                        }
                        console.log(response);
                    },
                    error: function(xhr, status, error){
                        console.error(xhr);
                    }
                });
            });
        });
        //var app = new Vue({
        //    el: '#assignmentOrder',
        //    data: {
        //        orderSummaryProcessing: 0,
        //    },
        //    methods: {
        //        getCartItems: function (e) {
        //            that = this;
        //            that.orderSummaryProcessing = 1;
        //            const url = "<?//= SITE_URL ?>//ajax?c=cart&a=currentCartAssessmentItems&action=json";
        //        },
        //    }
        //})
    </script>

<?php include BASE_PATH . 'account.footer.php';?>