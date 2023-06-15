<?php
/*
 * Used for general functionality around user subscriptions
 */
require_once(__DIR__ . '/accountController.php');
require_once(__DIR__ . '/courseController.php');

class subController extends Controller
{

    /**
     * @var accountController
     */
    protected $account;

    /**
     * @var courseController
     */
    protected $course;

    public function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;
        $this->account = new accountController();
        $this->course = new courseController();

    }

    private function makeUserSubscriptionActive($userID) {

        $user = ORM::for_table("accounts")->find_one($userID);

        $user->subActive = "1";

        $user->save();

    }

    public function newUserSubscription($userID) {

        $user = ORM::for_table("accounts")->find_one($userID);

        // send email
        $message = '<p>Hi '.$user->firstname.'</p>
        
        <p>Thank you for subscribing.</p>
        
        ';

        $this->sendEmail($user->email, $message, "Your new subscription with ".SITE_NAME);

        // mark subscription as active
        $this->makeUserSubscriptionActive($userID);

        // return to new dashboard

    }

    private function checkAccess() {

        // checks a user has an active subscription before doing things like adding a course
//        $account = ORM::for_table("accounts")->select("subActive")->find_one(CUR_ID_FRONT);
//
//        if($account->subActive != "1") {
//
//            $this->redirectJS(SITE_URL.'account/subscribe');
//            exit;
//
//        }
        if($this->isActiveSubscription(CUR_ID_FRONT) === false) {
            $this->redirectJS(SITE_URL.'account/subscribe');
            exit;
        }


    }

    public function addCourse() {

        $this->checkAccess();

        // configs
        $inTrial = $this->ifUserInTrial();
        $courseLimit = 50;

        if($inTrial == true) {

            $currency = $this->currentCurrency();

            $courseLimit = $currency->trialActiveCourses;

        }

        // make sure they're not at the limit
        if(($courseLimit-$this->countActiveCourses()) <= 0) {

            $this->setToastDanger("You cannot add more than ".$courseLimit." active courses to your account. Please remove courses from your account, or complete some, to add more.");

        } else {

            $courseID = $this->get["courseID"];

            // check if course is a bundle
            $courseData = ORM::for_table("courses")->find_one($courseID);

            if($courseData->childCourses != "") {
                // assign main bundle
                $assignBundle = $this->account->assignCourse($courseData->id, CUR_ID_FRONT, array("sub" => "1"));
                $bundleID = $assignBundle->id; // get assigned bundle ID

                foreach(json_decode($courseData->childCourses) as $child) {

                    // assign inner bundle
                    $assign = $this->account->assignCourse($child, CUR_ID_FRONT, array("bundleID" => $bundleID, "sub" => "1"));

                }

            }
            else {

                $assign = $this->account->assignCourse($courseData->id, CUR_ID_FRONT, array("sub" => "1"));

            }

            echo 'complete'; // provide feedback to frontend

        }



    }

    public function removeCourse() {

        $this->checkAccess();



            $courseID = $this->get["courseID"];

            $item = ORM::for_table("coursesAssigned")
                ->where("accountID", CUR_ID_FRONT)
                ->where("courseID", $courseID)
                ->where("completed", "0")
                ->find_one();

            if($item->id != "") {

                // delete bundle ones, if any
                $delete = ORM::for_table("coursesAssigned")->where("bundleID", $item->id)->delete_many();

            }

            $item->delete();


            echo 'complete'; // provide feedback to frontend





    }

    public function checkUserHasCourse($courseID) {

        $item = ORM::for_table("coursesAssigned")
            ->where("accountID", CUR_ID_FRONT)
            ->where("courseID", $courseID)
            ->find_one();

        if($item->id == "") {
            return false;
        } else {
            return true;
        }

    }

    public function renderCourseSmall($course, $columnClass = "col-lg-4") {

        $enrolled = $course->enrollmentCount;
        $enrolled *= (1 + 35 / 100);
        $enrolled = number_format($enrolled + 48);

        $assigned = ORM::for_table("coursesAssigned")
            ->where("accountID", CUR_ID_FRONT)
            ->where("courseID", $course->id)
            ->find_one();

        ?>
        <div class="col-12 <?= $columnClass ?>">
            <div class="category-box subCourse subCourse<?= $course->id ?> <?php if($this->checkUserHasCourse($course->id) == true) { ?>hasAccess<?php } ?>">
                <div class="img"
                     style="background-image:url('<?= $this->course->getCourseImage($course->id,
                         "large") ?>');"></div>

                <div class="Popular-title-top">
                    <i class="far fa-user"></i> <?= $enrolled ?>
                    students
                    <a class="btn btn-outline-primary btn-lg extra-radius offer_course_link courseInfoIcon" href="<?= SITE_URL ?>course/<?= $course->slug ?>" target="_blank"><i class="fa fa-info"></i></a>
                </div>
                <div class="Popular-title-bottom">
                    <?= $course->title ?>
                    <a href="javascript:;" role="button" class="btn btn-outline-primary btn-lg extra-radius btn-sub-add-course subAddCourse" data-course-id="<?= $course->id ?>">Add Course</a>
                </div>
                <div class="success-overlay">
                    <i class="fa fa-check"></i>
                    <p>You have access to this course.</p>
                    <?php
                    if($assigned->completed == "0" || $assigned->id == "") {
                    ?>
                    <a href="javascript:;" role="button" class="btn btn-outline-primary btn-lg extra-radius btn-sub-add-course subRemoveCourse" data-course-id="<?= $course->id ?>">Remove Course</a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php

    }

    public function browseCourses() {

        $this->checkAccess();

        $courseIDs = [];
        $excludeCourseIDs = [];

        $limit = 999;

        // get courses within selected category
        if($this->get["category"] != "") {
            $cIDs = $this->course->getCourseIDsByCategoryIDs(array($this->get["category"]));

            $courseIDs[0] = 0;
            if (count($cIDs) >= 1) {
                $i = 1;
                foreach ($cIDs as $cID) {
                    $courseIDs[$i] = $cID['course_id'];
                    $i++;
                }
            }

            $limit = 999;

        }else{ // exclude Qualifications courses
            $qualificationCategory = ORM::for_table('courseCategories')
                ->where('title', 'Qualifications')
                ->find_one();
            if($qualificationCategory->id){
                $cIDs = $this->course->getCourseIDsByCategoryIDs(array($qualificationCategory->id));

                $excludeCourseIDs[0] = 0;
                if (count($cIDs) >= 1) {
                    $i = 1;
                    foreach ($cIDs as $cID) {
                        $excludeCourseIDs[$i] = $cID['course_id'];
                        $i++;
                    }
                }
            }
        }

        // build sql query
        $courses = ORM::for_table('courses')->where_id_in($courseIDs);
        if($excludeCourseIDs){
            $courses = $courses->where_not_in('id', $excludeCourseIDs);
        }

        $whereRaw = [];

        // hide hidden courses
        $whereRaw[] = 'hidden = "0"';

        // hide ncfe courses
        $whereRaw[] = 'isNCFE = "0"';

        // hide language courses
        $whereRaw[] = 'is_cudoo = "0"';

        $currency = $this->currentCurrency();

        if($currency->code == "GBP") {
            $whereRaw[] = 'usImport = "0"';
        } else {
            $whereRaw[] = 'usImport = "1"';
        }

        $query = urldecode($this->get["q"]);

        if ($query != "") { //Search Text
            $searchText = $query;
            $whereRaw[] = '(title LIKE "%'.$searchText.'%" OR description LIKE "%'.$searchText.'%" OR additionalContent LIKE "%'.$searchText.'%")';
        }

        if (@$whereRaw) {
            $courses = $courses->where_raw(implode(" AND ", $whereRaw));
        }

        $courses = $courses->order_by_desc("enrollmentCount")->limit($limit)->find_many();

        foreach($courses as $course) {

            $this->renderCourseSmall($course);

        }

        ?>
        <!-- add subscription course -->
        <script>
            $( ".subAddCourse" ).click(function() {

                var courseID = $(this).data("course-id");

                $.get( "<?= SITE_URL ?>ajax?c=sub&a=add-course&courseID="+courseID, function( data ) {
                    if(data == "complete") {

                        $(".subCourse"+courseID).addClass("hasAccess");

                    } else {
                        $("#returnStatus").html(data);
                    }
                });

            });

            $( ".subRemoveCourse" ).click(function(e) {

                var courseID = $(this).data("course-id");

                e.preventDefault();
                if (window.confirm("Are you sure you want to remove this course from your account? You will lose any progress and notes.")) {
                    $.get( "<?= SITE_URL ?>ajax?c=sub&a=remove-course&courseID="+courseID, function( data ) {
                        if(data == "complete") {

                            $(".subCourse"+courseID).removeClass("hasAccess");

                        } else {
                            $("#returnStatus").html(data);
                        }
                    });
                }


            });

            var typingTimer;
            var doneTypingInterval = 500;
            var $input = $('#filterSearch');

            $input.on('keyup', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(doneTyping, doneTypingInterval);
            });

            $input.on('keydown', function () {
                clearTimeout(typingTimer);
            });

            function doneTyping () {
                var query = $("#filterSearch").val();
                var category = $("#currentCategoryID").val();

                console.log("done typing");

                $("#coursesAjax").load("<?= SITE_URL ?>ajax?c=sub&a=browse-courses&category="+category+"&q="+encodeURIComponent(query));
            }
        </script>
        <?php

    }

    public function countActiveCourses() {

        return ORM::for_table("coursesAssigned")
            ->where("accountID", CUR_ID_FRONT)
            ->where("sub", "1")
            ->where("completed", "0")
            ->where_null("bundleID")
            ->count();

    }

}

