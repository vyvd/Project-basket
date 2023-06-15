<?php

require_once(__DIR__ . '/courseModuleController.php');
require_once(__DIR__ . '/mediaController.php');
require_once(__DIR__ . '/quizController.php');
require_once(__DIR__ . '/rewardsAssignedController.php');
require_once(__DIR__ . '/moosendController.php');
require_once(__DIR__ . '/emailTemplateController.php');
require_once (__DIR__ . '/../classes/Pagination/paginator.class.php');

use Dompdf\Dompdf;
use Dompdf\Options;
use Mpdf\Mpdf;
class courseController extends Controller
{

    /**
     * @var mediaController
     */
    protected $medias;

    /**
     * @var quizController
     */
    protected $quizzes;

    /**
     * @var courseModuleController
     */
    protected $courseModules;

    /**
     * @var rewardsAssignedController
     */
    protected $rewardsAssigned;

    /**
     * @var moosendController
     */
    protected $moosend;

    /**
     * @var emailTemplateController
     */
    protected $emailTemplates;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->medias = new mediaController();
        $this->quizzes = new quizController();
        $this->courseModules = new courseModuleController();
        $this->rewardsAssigned = new rewardsAssignedController();
        $this->moosend = new moosendController();
        $this->emailTemplates = new emailTemplateController();
        $this->pages = new Classes\Pagination\Paginator();

    }

    public function getAllCourses($order = null)
    {

        if ($this->get["order"] == "nameAsc" || ($order == 'nameAsc')) {
            return ORM::for_table("courses")->order_by_asc("title")
                ->find_many();
        } else {
            if ($this->get["order"] == "nameDesc") {
                return ORM::for_table("courses")->order_by_desc("title")
                    ->find_many();
            } else {
                if ($this->get["order"] == "priceAsc") {
                    return ORM::for_table("courses")->order_by_asc("price")
                        ->find_many();
                } else {
                    if ($this->get["order"] == "priceDesc") {
                        return ORM::for_table("courses")->order_by_desc("price")
                            ->find_many();
                    } else {
                        if ($this->get["order"] == "durationAsc") {
                            return ORM::for_table("courses")
                                ->order_by_asc("duration")->find_many();
                        } else {
                            if ($this->get["order"] == "durationDesc") {
                                return ORM::for_table("courses")
                                    ->order_by_desc("duration")->find_many();
                            } else {
                                return ORM::for_table("courses")->limit(30)
                                    ->find_many();
                            }
                        }
                    }
                }
            }
        }


    }

    public function coursesScroll()
    {
        $pageno = $this->post['pageno'];

        $no_of_records_per_page = 9;
        $offset = ($pageno - 1) * $no_of_records_per_page;

        if (isset($this->post["cats"])) { // the user has selected categories, so get category data first

            $courseIDs = array();

            $categories = ORM::for_table("courseCategoryIDs")
                ->where("category_id", $this->post["cats"])
                ->limit($no_of_records_per_page)
                ->offset($offset)
                ->find_many();
            if (count($categories) >= 1) {
                foreach ($categories as $cat) {
                    array_push($courseIDs, $cat->course_id);
                }

                $courses = ORM::for_table("courses")
                    ->where("hidden", "0")
                    ->where_not_equal("is_lightningSkill", "1")
                    ->where_in("id", $courseIDs)
                    ->order_by_desc("id")
                    ->find_many();
            }
            //die('no');

        } else {
            if ($this->post["order"] == "nameAsc") {

                $courses = ORM::for_table("courses")
                    ->where("hidden", "0")
                    ->where_not_equal("is_lightningSkill", "1")
                    ->limit($no_of_records_per_page)
                    ->offset($offset)
                    ->order_by_asc("title")
                    ->find_many();

            } else {
                if ($this->post["order"] == "nameDesc") {

                    $courses = ORM::for_table("courses")
                        ->where("hidden", "0")
                        ->where_not_equal("is_lightningSkill", "1")
                        ->limit($no_of_records_per_page)
                        ->offset($offset)
                        ->order_by_desc("title")
                        ->find_many();

                } else {
                    if ($this->post["order"] == "priceAsc") {

                        $courses = ORM::for_table("courses")
                            ->where("hidden", "0")
                            ->where_not_equal("is_lightningSkill", "1")
                            ->limit($no_of_records_per_page)
                            ->offset($offset)
                            ->order_by_asc("price")
                            ->find_many();

                    } else {
                        if ($this->post["order"] == "priceDesc") {

                            $courses = ORM::for_table("courses")
                                ->where("hidden", "0")
                                ->where_not_equal("is_lightningSkill", "1")
                                ->limit($no_of_records_per_page)
                                ->offset($offset)
                                ->order_by_desc("price")
                                ->find_many();

                        } else {
                            if ($this->post["order"] == "durationDesc") {

                                $courses = ORM::for_table("courses")
                                    ->where("hidden", "0")
                                    ->where_not_equal("is_lightningSkill", "1")
                                    ->limit($no_of_records_per_page)
                                    ->offset($offset)
                                    ->order_by_desc("duration")
                                    ->find_many();

                            } else {
                                if ($this->post["order"] == "durationAsc") {

                                    $courses = ORM::for_table("courses")
                                        ->where("hidden", "0")
                                        ->where_not_equal("is_lightningSkill",
                                            "1")
                                        ->limit($no_of_records_per_page)
                                        ->offset($offset)
                                        ->order_by_asc("duration")
                                        ->find_many();

                                } else {

                                    $courses = ORM::for_table("courses")
                                        ->select("id")
                                        ->select("title")
                                        ->select("price")
                                        ->select("slug")
                                        ->where_not_equal("is_lightningSkill",
                                            "1")
                                        ->where("hidden", "0")
                                        ->limit($no_of_records_per_page)
                                        ->offset($offset)
                                        ->order_by_desc("id")
                                        ->find_many();

                                }
                            }
                        }
                    }
                }
            }

        }

        if (count($courses) >= 1) {
            foreach ($courses as $course) {

                $enrolled = ORM::for_table("coursesAssigned")
                    ->where("courseID", $course->id)->count();
                $enrolled *= (1 + 35 / 100);
                $enrolled = number_format($enrolled + 48);
                ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="category-box">
                        <div class="img"
                             style="background-image:url('<?= $this->getCourseImage($course->id,
                                 "large") ?>');"></div>

                        <div class="Popular-title-top"><i
                                    class="far fa-user"></i> <?= $enrolled ?>
                            students enrolled
                        </div>
                        <div class="Popular-title-bottom"><?= $course->title ?>
                            <h3><?= $this->price($course->price) ?></h3></div>
                        <div class="popular-box-overlay">
                            <p><strong><?= $course->title ?></strong></p>
                            <div class="popular-overlay-btn">

                                <?php
                                if ($course->childCourses == "") {
                                    ?>
                                    <button type="button"
                                            class="btn btn-outline-primary btn-lg extra-radius"><?= ORM::for_table("courseModules")
                                            ->where("courseID", $course->id)
                                            ->count() ?>
                                        Modules
                                    </button>
                                    <?php
                                } else {
                                    ?>
                                    <button type="button"
                                            class="btn btn-outline-primary btn-lg extra-radius"><?= count(json_decode($course->childCourses)) ?>
                                        Courses
                                    </button>
                                    <?php
                                }
                                ?>

                            </div>
                            <div class="popular-overlay-btn">
                                <button type="button"
                                        class="btn btn-outline-primary btn-lg extra-radius">
                                    0% Finance
                                </button>
                            </div>
                            <h3><?= $this->price($course->price) ?></h3>
                            <div class="popular-overlay-btn-btm">
                                <a class="btn btn-outline-primary btn-lg extra-radius nsa_course_more_info"
                                   href="<?= SITE_URL ?>course/<?= $course->slug ?>"
                                   role="button">More Info</a>
                                <a class="btn btn-outline-primary btn-lg extra-radius start-course-button nsa_add_to_cart_btn"
                                   data-course-id="<?= $course->id ?>"
                                   href="javascript:;" role="button">Add to
                                    Cart</a>

                                <a class="saveHeart saveCourse<?= $course->id ?> <?php if ($this->checkCourseSaved($course->id)
                                    == true
                                ) { ?>active<?php } ?>"
                                   href="javascript:;" role="button"
                                   onclick="saveCourse(<?= $course->id ?>);">
                                    <i class="far fa-heart"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php

            }
        } else {
            ?>
            <div class="col-12 text-center">No Course found!</div>
            <?php
        }

    }

    public function getSingleCourse($id = "")
    {

        if ($id != "") {
            return ORM::for_table("courses")->find_one($id);
        } else {
            $slug = rtrim($_GET["request"], '/'); // ensures courses work with trailing slash, more so for US domain
            return ORM::for_table("courses")->where("slug", $slug)
                ->find_one();
        }

    }

    public function getCourseAssigned($course)
    {

        return ORM::for_table("coursesAssigned")
            ->where("accountID", CUR_ID_FRONT)
            ->where("courseID", $course->id)
            ->find_one();

    }

    public function totalCourseModules($course)
    {

        return ORM::for_table("courseModules")
            ->where("courseID", $course->id)
            ->count();

    }

    public function getSingleModule($moduleID = null, $accountID = null)
    {
        if (@$_GET["request"]) {
            // get the current module
            $module = ORM::for_table("courseModules")
                ->where("slug", $_GET["request"])->find_one();
            $accountID = CUR_ID_FRONT;
        } elseif (@$moduleID) { //Import from live site
            $module = ORM::for_table("courseModules")->find_one($moduleID);
        }

        // check user is allowed to access
        if ($module->id == "" || $this->checkUserCourseAccess($module->courseID, $accountID) == false) {
            header('Location: '.SITE_URL.'?signIn=true'); // redirect to sign in page
            exit;
        }

        // check if there are child modules
        $childModule = ORM::for_table("courseModules")
            ->where("parentID", $module->id)
            ->order_by_asc("ord")
            ->find_one();

        if($childModule->id != "") {
            header('Location: '.SITE_URL.'module/'.$childModule->slug);
            exit;
        }

        // mark this as current module and update percentage progress
        $isSubModule = @$module->parentID ? true : false;

        // calculate percentage
        $course = $this->getSingleCourse($module->courseID);
        $modules = $this->courseModules($course);



        // update percentage progress
        $currentAssigned = ORM::for_table("coursesAssigned")
            ->where("accountID", $accountID)
            ->where("courseID", $module->courseID)
            ->find_one();

        // Only change progress if this module is one AFTER the "current" one
        $currentAssignedModule = ORM::for_table("courseModules")->find_one($currentAssigned->currentModule);

        if($isSubModule){
            $currentAssignedModule = ORM::for_table("courseModules")->find_one($currentAssigned->currentSubModule);
            $modules = $this->courseModules->getSubmodules($module->parentID);
        }


        $count = 1;
        $modArray = array();

        foreach ($modules as $mod) {

            $modArray[$count] = $mod->id;

            $count++;

            // Save Module progress to complete for previous module
            if ($module->ord >= $currentAssignedModule->ord) {
                if (($module->ord != 1) && (($module->ord - 1) == $mod->ord)) {

                    $this->courseModules->saveCourseModuleProgress([
                        'accountID'     => $accountID,
                        'courseID'      => $mod->courseID,
                        'moduleID'      => $mod->id,
                        'completed'     => 1,
                        'whenCompleted' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        // check previous submodules
        $parentModuleID =  $isSubModule ? $module->parentID : $module->id;
        $parentModule = $this->courseModules->getModuleByID($parentModuleID);

        if($parentModule->ord >= 2){
            $parentPreviousModule = ORM::for_table('courseModules')
                ->where('courseID', $parentModule->courseID)
                ->where('ord', $parentModule->ord - 1)
                ->where_null('parentID')
                ->find_one();
            $parentPreviousSubmodules = $this->courseModules->getSubmodules($parentPreviousModule->id);
            foreach ($parentPreviousSubmodules as $mod) {
                // Save Module progress to complete for previous module
                $this->courseModules->saveCourseModuleProgress([
                    'accountID'     => $accountID,
                    'courseID'      => $mod->courseID,
                    'moduleID'      => $mod->id,
                    'completed'     => 1,
                    'whenCompleted' => date('Y-m-d H:i:s')
                ]);
            }
        }

        if (($module->ord >= $currentAssignedModule->ord) || ($isSubModule && ($module->ord == 1))) {


            // then we update
            $totalModules = $this->countModules($module->courseID);

            $percentage = number_format(($currentAssigned->currentModuleKey
                    / $totalModules) * 100, 2);

            if($isSubModule){
                $totalCompletedModules = ORM::for_table('courseModuleProgress')
                    ->where('accountID', CUR_ID_FRONT)
                    ->where('courseID', $module->courseID)
                    ->where('completed', 1)
                    ->count();

                if($totalCompletedModules >= 1){
                    $percentage = number_format(($totalCompletedModules / $totalModules) * 100, 2);
                }else{
                    $percentage = 0;
                }
            }
            $currentAssigned->percComplete = $percentage;
            $currentAssigned->save();

            //$key = array_search($module->id, $modArray);

            // Save Course Module Progress
            if(!$this->courseModules->isModuleAlreadyCompleted($module->id)) {
                $this->courseModules->saveCourseModuleProgress([
                    'accountID'   => $accountID,
                    'courseID'    => $module->courseID,
                    'moduleID'    => $module->id,
                    'completed'   => 0,
                    'whenStarted' => date('Y-m-d H:i:s')
                ]);
            }

        }

        // check for rewards
        $this->checkModuleRewards();

        if (@$_GET["request"]) {
            // save progress to moosend
            $course = ORM::for_table("courses")->find_one($module->courseID);
            $user = ORM::for_table("accounts")->find_one($accountID);

            $categories = array();

            $courseCategories = ORM::for_table("courseCategoryIDs")
                ->where("course_id", $course->id)->find_many();

            foreach ($courseCategories as $cat) {

                $catData = ORM::for_table("courseCategories")
                    ->find_one($cat->category_id);

                array_push($categories, $catData->title);

            }

            $imploded_cat_names = implode(',', $categories);

            $score = 0;
            
            $quiz = ORM::for_table("quizzes")->where('moduleID', $module->id)->order_by_asc('id')->find_one();
            if(!empty($quiz)) {
                $result = ORM::for_table("quizResults")->where('quizID', $quiz->id)->find_one();
                if(!empty($result)) $score = $result->percentage;
            }

            // $custom_fields = array(
            //     'course_id'             => $module->courseID,
            //     'course_name'           => $course->title,
            //     'course_category'       => $imploded_cat_names,
            //     'course_progress'       => number_format($percentage),
            //     'user_first_name'       => $user->firstname,
            //     'user_last_name'        => $user->lastname,
            //     'user_id'               => $user->id,
            //     'quiz_score_percentage' => $score,
            //     'last_study_date'       => date('Y-m-d H:i:s')
            // );

            // $this->moosend->addSubscriberCourseProgress($user->firstname,
            //     $user->email, $custom_fields);

            // klaviyo field names differ
            $custom_fields = array(
                'Course ID'             => $module->courseID,
                'Course Name'           => $course->title,
                'Course Category'       => $imploded_cat_names,
                'Course Progress'       => number_format($percentage),
                'First Name'            => $user->firstname,
                'Last Name'             => $user->lastname,
                'User ID'               => $user->id,
                'Quiz Score Percentage' => $score,
                'Last Study Date'       => date('Y-m-d H:i:s')
            );

            $this->moosend->addSubscriberCourseProgress($user->firstname,
                $user->email, $custom_fields);
        }

        // return module data
        return $module;

    }

    private function checkModuleRewards()
    {


    }

    public function getModuleByID($moduleID)
    {

        return ORM::for_table("courseModules")->find_one($moduleID);

    }

    public function getCourseByID($courseID)
    {

        return ORM::for_table("courses")->find_one($courseID);

    }

    public function getCourseByName($name)
    {
        return ORM::for_table("courses")
            ->where('title', $name)
            ->find_one();
    }

    public function getCourseCategoryByName($name)
    {
        return ORM::for_table("courseCategories")
            ->where_like('title', $name)
            ->find_one();
    }



    public function getCourseByOldID($oldID, $isUsCourse = '0')
    {
        $course = ORM::for_table("courses")
            ->where("usImport", $isUsCourse)
            ->where("oldID", $oldID);

        return $course->find_one();
    }

    public function checkCourseStarted($course)
    {

        $currentAssigned = ORM::for_table("coursesAssigned")
            ->where("accountID", CUR_ID_FRONT)
            ->where("courseID", $course->id)
            ->find_one();

        if ($currentAssigned->currentModule == "") {
            return false;
        } else {
            return $currentAssigned->currentModule;
        }

    }

    public function checkUserCourseAccess($course, $user = CUR_ID_FRONT)
    {

        $item = ORM::for_table("coursesAssigned")
            ->where("accountID", $user)
            ->where("courseID", $course)
            ->find_one();

        if ($item->id == "") {
            return false;
        } else {
            return true;
        }

    }

    public function userCourses($limit, $user = CUR_ID_FRONT)
    {

        return ORM::for_table("coursesAssigned")
            ->where("accountID", $user)
            ->where_null("bundleID")
            ->order_by_desc("id")
            ->limit($limit)
            ->find_many();

    }

    public function userCoursesFilter($limit, $user, $filter)
    {

        if ($filter == "completed") {
            return ORM::for_table("coursesAssigned")
                ->where("accountID", $user)
                ->where("completed", "1")
                ->where_null("bundleID")
                ->order_by_desc("id")
                ->limit($limit)
                ->find_many();
        } else {
            if ($filter == "active") {
                return ORM::for_table("coursesAssigned")
                    ->where("accountID", $user)
                    ->where("completed", "0")
                    ->where_null("bundleID")
                    ->order_by_desc("id")
                    ->limit($limit)
                    ->find_many();
            } else {
                return ORM::for_table("coursesAssigned")
                    ->where("accountID", $user)
                    ->where_null("bundleID")
                    ->order_by_desc("id")
                    ->limit($limit)
                    ->find_many();
            }
        }

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

    public function countModules($courseID)
    {
        $totalModules = 0;
        $modules = ORM::for_table("courseModules")
            ->where("courseID", $courseID)
            ->where_null('parentID')
            ->find_many();
        foreach ($modules as $module) {
            // check submodules
            $subModules = ORM::for_table("courseModules")
                ->where("courseID", $courseID)
                ->where('parentID', $module->id)
                ->count();
            if($subModules >= 1){
                $totalModules += $subModules;
            }else{
                $totalModules++;
            }
        }
        return $totalModules;

    }

    public function moduleQuestions($module)
    {

    }

    public function moduleContents($contents)
    {

        // NOW REDUNDANT: Moved to courseModule->renderContents()

        // removes shortcodes, such as the [tweet] one
        $contents = preg_replace('#\[[^\]]+\]#', '', $contents);

        // adds line breaks
        $contents = nl2br($contents);

        // remove linebreaks at the end
        //$contents = preg_replace('/(<br \/>)+$/', '', $contents);

        // show the contents
        echo $contents;

    }

    public function startCourse($course)
    {

        // get first module then redirect to there
        $module = ORM::for_table("courseModules")
            ->where("courseID", $course->id)->order_by_asc("ord")->find_one();

        header('Location: '.SITE_URL.'module/'.$module->slug);

    }

    public function checkCourseComplete($course)
    {

        $item = ORM::for_table("coursesAssigned")
            ->where("accountID", CUR_ID_FRONT)
            ->where("courseID", $course->id)
            ->group_by("courseID")
            ->find_one();

        if ($item->completed == "1") {
            return true;
        } else {
            return false;
        }

    }

    public function getUserNotes()
    {

        return ORM::for_table("courseNotes")
            ->where("userID", CUR_ID_FRONT)
            ->order_by_desc("whenUpdated")
            ->find_many();

    }

    public function rateCourse()
    {

        if ($this->post["rating"] == "") {
            $this->setToastDanger("Please enter your rating");
            exit;
        }

        $item = ORM::for_table("courseRatings")
            ->where("courseID", $this->post["course"])
            ->where("userID", CUR_ID_FRONT)
            ->find_one();

        if ($item->id == "") {

            $item = ORM::for_table("courseRatings")->create();

            $item->set(
                array(
                    'courseID' => $this->post["course"],
                    'userID'   => CUR_ID_FRONT
                )
            );

        }

        $item->rating = $this->post["rating"];
        $item->set_expr("whenRated", "NOW()");

        $item->save();

        $this->setToastSuccess("Thank you, your rating was successfully submitted");


        $this->moosend->addUserRatingField(CUR_ID_FRONT, $this->post["rating"]);

        if ($this->post["rating"] == "5") {

            $course = ORM::for_table("courses")
                ->find_one($this->post["course"]);

            $this->redirectJSDelay(SITE_URL.'courses/review?course='
                .$course->title, '2000');
        }

    }

    public function saveNotes()
    {

        $item = ORM::for_table("courseNotes")
            ->where("courseID", $this->post["course"])
            ->where("moduleID", $this->post["module"])
            ->where("userID", CUR_ID_FRONT)
            ->find_one();

        if ($item->id == "") {

            $item = ORM::for_table("courseNotes")->create();

            $item->set(
                array(
                    'courseID' => $this->post["course"],
                    'moduleID' => $this->post["module"],
                    'userID'   => CUR_ID_FRONT
                )
            );

            $item->set_expr("whenAdded", "NOW()");

        }

        $item->notes = $this->post["notes"];
        $item->set_expr("whenUpdated", "NOW()");

        $item->save();

        $this->setToastSuccess("Your notes were successfully saved. You can refer back to these at any time.");

    }

    public function getNotes($moduleID)
    {

        return ORM::for_table("courseNotes")
            ->where("moduleID", $moduleID)
            ->where("userID", CUR_ID_FRONT)
            ->find_one();

    }

    public function moduleNext($module, $course, $return = false)
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
            $subModules = $this->courseModules->getSubmodules($module->parentID);
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
        
        if (($module->ord >= $currentAssignedModule->ord) || ($isSubModule && ($module->ord == 1))) {
            if(!$this->courseModules->isModuleAlreadyCompleted($module->id)){
                $currentAssigned->currentModule = @$module->parentID ? $module->parentID : $module->id;
                $currentAssigned->currentSubModule = $isSubModule ? $module->id : null;
                $currentAssigned->currentModuleKey = $key;
                $currentAssigned->set_expr("lastAccessed", "NOW()");
                //$currentAssigned->percComplete = $percentage;
                $currentAssigned->save();
            }
        }

        // do we have a quiz for this module that needs to appear after the module and before the next module
        $quizCurrent = ORM::for_table("quizzes")->where("moduleID", $module->id)
            ->where("appear", "a")->find_one();

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
            && $showQuiz == true
        ) {

            if ($return == false) {
                echo SITE_URL.'quiz/'.$module->slug;
            } else {
                return SITE_URL.'quiz/'.$module->slug;
            }

        }
//        elseif ($isSubModule && ($next->contentType == 'quiz')) { // Check if next submodule is quiz
//            if ($return == false) {
//                echo SITE_URL.'quiz/'.$next->slug;
//            } else {
//                return SITE_URL.'quiz/'.$next->slug;
//            }
//        }
        else {

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

    public function getQuiz($module, $appear = null)
    {
        $quiz = ORM::for_table("quizzes")->where("moduleID", $module);
        if($appear) {
            $quiz->where("appear", $appear);
        }
        $quiz = $quiz->find_one();

        if ($quiz->id == "") {
            return false;
        } else {
            return $quiz;
        }


    }

    public function getQuizQuestions($quiz)
    {

        return ORM::for_table("quizQuestions")
            ->where("quizID", $quiz)
            ->order_by_asc("ord")
            ->find_many();

    }

    public function getAnswersArray($answers)
    {

        return json_decode($answers, true);

    }

    public function generateCertNo(): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function completeCourse($courseID = null, $accountID = null)
    {
        if (@$courseID && @$accountID) {
            $courseAssigned = ORM::for_table("coursesAssigned")
                ->where("courseID", $courseID)
                ->where("accountID", $accountID)
                ->find_one();
        } else {
            $courseAssigned = ORM::for_table("coursesAssigned")
                ->where("accountID", CUR_ID_FRONT)
                //->where("token", $this->get["token"])
                ->find_one($this->get["id"]);
            $accountID = CUR_ID_FRONT;
        }

        if ($courseAssigned->id == "") {
            $this->force404();
            exit;
        }

        // certificate number
        $randomString = $this->generateCertNo();

        // @todo: check existing

        // Complete Current Module
        $this->courseModules->saveCourseModuleProgress([
            'accountID'     => $accountID,
            'courseID'      => $courseAssigned->courseID,
            'moduleID'      => $courseAssigned->currentModule,
            'completed'     => 1,
            'whenCompleted' => date('Y-m-d H:i:s')
        ]);


        // get course details
        $course = ORM::for_table("courses")
            ->find_one($courseAssigned->courseID);

        // save details
        // if not already completed
        if($courseAssigned->certNo == "") {
            $courseAssigned->certNo = $randomString;
            $courseAssigned->set_expr("whenCompleted", "NOW()");
        }

        $courseAssigned->completed = "1";
        $courseAssigned->percComplete = "100";

        $courseAssigned->save();


        if(@$courseAssigned->bundleID && $courseAssigned->completed == '1'){ // Complete bundle if all courses completed
            $bundleID = $courseAssigned->bundleID;
            $totalBundleCourses = ORM::for_table('coursesAssigned')
                ->where('bundleID', $bundleID)
                ->where('accountID', $courseAssigned->accountID)
                ->count();

            $totalCompletedBundleCourses = ORM::for_table('coursesAssigned')
                ->where('bundleID', $bundleID)
                ->where('accountID', $courseAssigned->accountID)
                ->where('completed', '1')
                ->count();

            $assignBundleCourse = ORM::for_table('coursesAssigned')->find_one($bundleID);

            if($totalBundleCourses == $totalCompletedBundleCourses){
                $assignBundleCourse->certNo = $this->generateCertNo();
                $assignBundleCourse->completed = "1";
                $assignBundleCourse->set_expr("whenCompleted", "NOW()");
                $assignBundleCourse->percComplete = "100";
            }else{
                $percentage = number_format(($totalCompletedBundleCourses / $totalBundleCourses) * 100, 2);
                $assignBundleCourse->percComplete = number_format($percentage, 2);
            }
            $assignBundleCourse->save();
        }


        // check complete course reward
        $this->checkCourseRewards($accountID);

        if (@$courseID && @$accountID) {
            return $courseAssigned;
        }

        // Email to user
        $account = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);

        if($course->isNCFE == '1'){
            $emailTemplate = $this->emailTemplates->getTemplateByTitle('course_completed_ncfe');
        }else{
            $emailTemplate = $this->emailTemplates->getTemplateByTitle('course_completed');
        }

        if (@$emailTemplate->id) {
            $variables = [
                '[FIRST_NAME]' => $account->firstname,
                '[LAST_NAME]'  => $account->lastname,
                '[COURSE_NAME]'  => $course->title,
                '[CERT_NO]'  => $randomString,
                '[COMPLETION_DATE]'  => date('F d, Y'),
                '[CERTIFICATE_BUTTON]'  => $this->renderHtmlEmailButton("Open Certificate", SITE_URL.'dashboard'),
            ];

            $message = $emailTemplate->description;
            $subject = $emailTemplate->subject;

            foreach ($variables as $k => $v) {
                $message = str_replace($k, $v, $message);
                $subject = str_replace($k, $v, $subject);
            }

            $this->sendEmail($account->email, $message, $subject);
        }


        // save progress to moosend
        $user = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);

        $categories = array();

        $courseCategories = ORM::for_table("courseCategoryIDs")
            ->where("course_id", $course->id)->find_many();

        foreach ($courseCategories as $cat) {

            $catData = ORM::for_table("courseCategories")
                ->find_one($cat->category_id);

            array_push($categories, $catData->title);

        }

        $imploded_cat_names = implode(',', $categories);


        // below is used to determine the average quiz score for this course
        $score = 0;
        $modules = ORM::for_table("courseModules")->where('courseID', $courseAssigned->courseID)->order_by_asc('ord')->find_many();
        $noOfModules = !empty($modules) ? count($modules) : 0;
        foreach($modules as $module) {    
            $quiz = ORM::for_table("quizzes")->where('moduleID', $module->id)->order_by_asc('id')->find_many();
            if(!empty($quiz)) {
                $result = ORM::for_table("quizResults")->where('quizID', $quiz->id)->find_one();
                if(!empty($result)) $score += $result->percentage;
            }
        }

        // $custom_fields = array(
        //     'course_id'             => $course->id,
        //     'course_name'           => $course->title,
        //     'course_category'       => $imploded_cat_names,
        //     'course_progress'       => '100',
        //     'user_first_name'       => $user->firstname,
        //     'user_last_name'        => $user->lastname,
        //     'user_id'               => $user->id,
        //     'quiz_score_percentage' => number_format($score/$noOfModules, 2),
        //     'last_study_date'       => date('Y-m-d H:i:s')
        // );

        // $this->moosend->addSubscriberCourseProgress($user->firstname,
        //     $user->email, $custom_fields);

        // klaviyo field names differ
        $custom_fields = array(
            'Course ID'             => $course->id,
            'Course Name'           => $course->title,
            'Course Category'       => $imploded_cat_names,
            'Course Progress'       => '100',
            'First Name'            => $user->firstname,
            'Last Name'             => $user->lastname,
            'User ID'               => $user->id,
            'Quiz Score Percentage' => number_format($score/$noOfModules, 2),
            'Last Study Date'       => date('Y-m-d H:i:s')
        );

        $this->moosend->addSubscriberCourseProgress($user->firstname,
            $user->email, $custom_fields);

        header('Location: '.SITE_URL.'course/complete?token='
            .$randomString.'&id='.$courseAssigned->id());

    }

    public function getCourseComplete()
    {

        $courseAssigned = ORM::for_table("coursesAssigned")
            ->where("accountID", CUR_ID_FRONT)
            ->where("certNo", $this->get["token"])
            ->find_one($this->get["id"]);

        $course = ORM::for_table("courses")
            ->find_one($courseAssigned->courseID);

        return $course;

    }

    public function getCourseCompleteAssigned()
    {

        return ORM::for_table("coursesAssigned")
            ->where("accountID", CUR_ID_FRONT)
            ->where("certNo", $this->get["token"])
            ->find_one($this->get["id"]);

    }

    public function validateQualification()
    {

        $record = ORM::for_table("coursesAssigned")
            ->where("certNo", $this->post["cert"])
            ->count();

        $error = 0;

        if ($record > 0) {

            // check user matches name
            $account = ORM::for_table("accounts")
                ->where("firstname", $this->post["firstname"])
                ->where("lastname", $this->post["lastname"])
                ->where("id", $record->userID)
                ->count();


            if ($account > 0) {
                $error = 1;
            } else {
                $error = 0;
            }


        } else {
            $error = 0;
        }

        if ($error == 0) {

            $this->setToastDanger("We could not validate that qualification.");

        } else {
            $this->setToastSuccess("That qualification has been successfully validated");
        }

    }

    public function currentCategory()
    {

        $category = ORM::for_table("courseCategories")
            ->where("slug", $_GET["request"])->find_one();

        if ($category->id == "") {
            return array();
        } else {
            return $category;
        }

    }

    public function getCoursesCategory($id)
    {

        return ORM::for_table("courseCategoryIDs")->where("category_id", $id)
            ->find_many();

        /*if ($this->get["order"] == "nameAsc") {
            return ORM::for_table("courses")->where("category", $id)
                ->order_by_asc("title")->find_many();
        } else {
            if ($this->get["order"] == "nameDesc") {
                return ORM::for_table("courses")->where("category", $id)
                    ->order_by_desc("title")->find_many();
            } else {
                if ($this->get["order"] == "priceAsc") {
                    return ORM::for_table("courses")->where("category", $id)
                        ->order_by_asc("price")->find_many();
                } else {
                    if ($this->get["order"] == "priceDesc") {
                        return ORM::for_table("courses")->where("category", $id)
                            ->order_by_desc("price")->find_many();
                    } else {
                        if ($this->get["order"] == "durationAsc") {
                            return ORM::for_table("courses")
                                ->where("category", $id)
                                ->order_by_asc("duration")->find_many();
                        } else {
                            if ($this->get["order"] == "durationDesc") {
                                return ORM::for_table("courses")
                                    ->where("category", $id)
                                    ->order_by_desc("duration")->find_many();
                            } else {
                                return ORM::for_table("courses")
                                    ->where("category", $id)
                                    ->order_by_desc("whenAdded")->find_many();
                            }
                        }
                    }
                }
            }
        }*/


    }

    public function getCoursesSearch()
    {

        // remove common words
        $this->get["search"] = str_replace("course", "", $this->get["search"]);
        $this->get["search"] = str_replace("courses", "", $this->get["search"]);

        $currency = $this->currentCurrency();

        if($currency->code == "GBP") {
            $whereRaw[] = 'usImport = "0"';
        } else {
            $whereRaw[] = 'usImport = "1"';
        }

        $courses = $this->getCoursesSql(['searchText' => $this->get["search"]]);

        return $courses->find_many();


        if ($this->get["order"] == "nameAsc") {
            return ORM::for_table("courses")
                ->where("hidden", "0")
                ->where("is_lightningSkill", "0")
                ->where_like("title", "%".$this->get["search"]."%")
                ->order_by_asc("title")->find_many();
        } else {
            if ($this->get["order"] == "nameDesc") {
                return ORM::for_table("courses")
                    ->where("hidden", "0")
                    ->where("is_lightningSkill", "0")
                    ->where_like("title", "%".$this->get["search"]."%")
                    ->order_by_desc("title")->find_many();
            } else {
                if ($this->get["order"] == "priceAsc") {
                    return ORM::for_table("courses")
                        ->where("hidden", "0")
                        ->where("is_lightningSkill", "0")
                        ->where_like("title", "%".$this->get["search"]."%")
                        ->order_by_asc("price")->find_many();
                } else {
                    if ($this->get["order"] == "priceDesc") {
                        return ORM::for_table("courses")->where_like("title",
                            "%".$this->get["search"]."%")
                            ->where("hidden", "0")
                            ->where("is_lightningSkill", "0")
                            ->order_by_desc("price")->find_many();
                    } else {
                        if ($this->get["order"] == "durationAsc") {
                            return ORM::for_table("courses")
                                ->where("hidden", "0")
                                ->where("is_lightningSkill", "0")
                                ->where_like("title",
                                    "%".$this->get["search"]."%")
                                ->order_by_asc("duration")->find_many();
                        } else {
                            if ($this->get["order"] == "durationDesc") {
                                return ORM::for_table("courses")
                                    ->where("hidden", "0")
                                    ->where("is_lightningSkill", "0")
                                    ->where_like("title",
                                        "%".$this->get["search"]."%")
                                    ->order_by_desc("duration")->find_many();
                            } else {
                                if($currency->code == "GBP") {
                                    return ORM::for_table("courses")
                                        ->where_like("title",
                                            "%".$this->get["search"]."%")
                                        ->where("usImport", "0")
                                        ->order_by_desc("whenAdded")
                                        ->find_many();
                                } else {
                                    return ORM::for_table("courses")
                                        ->where_like("title",
                                            "%".$this->get["search"]."%")
                                        ->where("usImport", "1")
                                        ->order_by_desc("whenAdded")
                                        ->find_many();
                                }
                            }
                        }
                    }
                }
            }
        }


    }

    public function deleteCourseNote()
    {

        if($this->get["id"] != "") {
            $item = ORM::for_table("courseNotes")->where("userID", CUR_ID_FRONT)
                ->find_one($this->get["id"]);

            if ($item->id != "") {

                $item->delete();

                header('Location: '.SITE_URL.'dashboard/courses/notes');

            }
        }

    }

    public function getAllCourseNotes($course)
    {

        return ORM::for_table("courseNotes")
            ->where("userID", CUR_ID_FRONT)
            ->where("courseID", $course->id)
            ->order_by_asc("id")
            ->find_many();

    }

    public function getAllCourseBlogs($course)
    {

        return ORM::for_table("blog")
            ->where("courseID", $course->id)
            ->order_by_asc("id")
            ->find_many();

    }

    public function validateCert()
    {

        $this->post["cert"] = str_replace(" ", "", $this->post["cert"]);

        ?>
        <script>
            $("#validateFooterForm").css("display", "none");
        </script>
        <?php

        $item = ORM::For_table("coursesAssigned")
            ->where("certNo", $this->post["cert"])->find_one();

        if ($item->id == "" || $item->certNo == "") {
            ?>
            <div class="validateStudent-boxes">
                <div class="status invalid">
                    <span><i class="fas fa-question"></i></span> There has been
                    a problem
                </div>
                <p>We are unable to automatically verify this certificate.
                    Please check that you have entered the correct certificate
                    ID. If you are still having problems please contact our
                    customer support team.</p>
            </div>
            <?php
        } else {
            $course = ORM::for_table("courses")->find_one($item->courseID);
            $account = ORM::for_table("accounts")->find_one($item->accountID);
            ?>
            <div class="validateStudent-boxes">
                <div class="status valid">
                    <span><i class="fas fa-check" aria-hidden="true"></i></span>
                    This certificate is valid
                </div>
                <p>Certificate ID: <?= $item->certNo ?></p>
                <p>Name: <?= $account->firstname.' '
                    .$account->lastname ?></p>
                <p>Course: <?= $course->title ?></p>
                <p>Completion Date: <?= date('d/m/Y',
                        strtotime($item->whenCompleted)) ?></p>
            </div>
            <?php

        }

    }

    public function headerSearch()
    {

        // remove common words
        $this->get["q"] = str_replace("course", "", $this->get["q"]);
        $this->get["q"] = str_replace("courses", "", $this->get["q"]);

        $courses = ORM::for_table("courses")
            ->where_like("title", "%".$this->get["q"]."%")
            ->order_by_desc("whenAdded")
            ->find_many();

        $currency = $this->currentCurrency();

        if($currency->code == "GBP") {
            $courses = ORM::for_table("courses")
                ->where_like("title", "%".$this->get["q"]."%")
                ->where("usImport", "0")
                ->order_by_desc("whenAdded")
                ->find_many();
        } else {
            $courses = ORM::for_table("courses")
                ->where_like("title", "%".$this->get["q"]."%")
                ->where("usImport", "1")
                ->order_by_desc("whenAdded")
                ->find_many();
        }

        foreach ($courses as $course) {

            if ($course->hidden == "0" && $course->is_lightningSkill == "0") {
                ?>
                <a href="<?= SITE_URL ?>course/<?= $course->slug ?>"
                   class="item">
                    <?= $course->title ?>
                </a>
                <?php
            }

            // see if there are any bundles this belongs to
            $bundles = ORM::for_table("courses")->where("hidden", "0")
                ->where("is_lightningSkill", "0")
                ->where_not_equal("childCourses", "")->find_many();


            foreach ($bundles as $bundle) {

                $children = str_replace("[", "", $bundle->childCourses);
                $children = str_replace("[", "", $children);
                $children = str_replace('"', "", $children);

                $children = explode(",", $children);

                if (in_array($course->id, $children)) {
                    ?>
                    <!--<a href="<?= SITE_URL ?>course/<?= $bundle->slug ?>"
                       class="item">
                        <?= $bundle->title ?>
                    </a>-->
                    <?php
                }
            }


        }

    }

    public function refine()
    {

        $courses = ORM::for_table("courses")
            ->where_in("category", $this->post["categories"])->find_many();

        if ($this->post["categories"] == "") {
            $courses = $this->getAllCourses();
        }

        foreach ($courses as $course) {

            ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="category-box">
                    <img src="<?= SITE_URL ?>assets/cdn/courses/<?= $course->image ?>"
                         alt="Popular"/>
                    <div class="Popular-title-top"><i
                                class="far fa-user"></i> <?= ORM::for_table("coursesAssigned")
                            ->where("courseID", $course->id)->count() ?>students
                        enrolled
                    </div>
                    <div class="Popular-title-bottom"><?= $course->title ?>
                        <h3><?= $this->price($course->price) ?></h3></div>
                    <div class="popular-box-overlay">
                        <p><strong><?= $course->title ?></strong></p>
                        <div class="popular-overlay-btn">
                            <button type="button"
                                    class="btn btn-outline-primary btn-lg extra-radius"><?= ORM::for_table("courseModules")
                                    ->where("courseID", $course->id)->count() ?>
                                Modules
                            </button>
                        </div>
                        <div class="popular-overlay-btn">
                            <button type="button"
                                    class="btn btn-outline-primary btn-lg extra-radius">
                                0% Finance
                            </button>
                        </div>
                        <h3><?= $this->price($course->price) ?></h3>
                        <div class="popular-overlay-btn-btm">
                            <a class="btn btn-outline-primary btn-lg extra-radius nsa_course_more_info"
                               href="<?= SITE_URL ?>course/<?= $course->slug ?>"
                               role="button">More Info</a>
                            <a class="btn btn-outline-primary btn-lg extra-radius start-course-button nsa_add_to_cart_btn"
                               data-course-id="<?= $course->id ?>"
                               href="javascript:;" role="button">Add to Cart</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php

        }


    }

    public function reviewCourse()
    {

        if ($this->post["terms"] != "1") {
            $this->setToastDanger("You must accept the terms & conditions before continuing.");
            exit;
        }
        $validateImage = $this->validateUploadImage();

        if (@$validateImage) {
            $this->setToastDanger(implode(',', $validateImage));
            exit;
        }

        $fields = array(
            "firstname",
            "lastname",
            "email",
            "city",
            "course",
            "comments",
            "rating"
        );

        $this->validateValues($fields);

        $item = ORM::for_table("courseReviews")->create();

        $item->set(
            array(
                'firstname' => $this->post["firstname"],
                'lastname'  => $this->post["lastname"],
                'email'     => $this->post["email"],
                'city'      => $this->post["city"],
                'course'    => $this->post["course"],
                'comments'  => $this->post["comments"],
                'rating'    => $this->post["rating"]
            )
        );

        $item->set_expr("whenSubmitted", "NOW()");
        $item->userID = CUR_ID_FRONT;
        if (CUR_ID_FRONT == "") {
            $item->userID = "0";
        }
        //$item->image = $this->uploadImage("reviewImages");



        $item->save();
        $this->medias->uploadFile(
            ['thumb'],
            ['type' => 'courseReviewController', 'id' => $item->id],
            'uploaded_file');

        // Email to user
        $emailTemplate
            = $this->emailTemplates->getTemplateByTitle('course_reviewed');
        if (@$emailTemplate->id) {
            $variables = [
                '[FIRST_NAME]' => $this->post["firstname"],
                '[LAST_NAME]'  => $this->post["lastname"],
            ];

            $message = $emailTemplate->description;
            $subject = $emailTemplate->subject;

            foreach ($variables as $k => $v) {
                $message = str_replace($k, $v, $message);
                $subject = str_replace($k, $v, $subject);
            }

            $this->sendEmail($this->post["email"], $message, $subject);
        }


        ?>
        <script>
            $("#reviewCourse").html('<br /><br /><br /><p class="text-center" style="font-weight: bold;font-size: 24px;color: #248cab;"><i class="fad fa-check" style="display: block;font-size: 85px;color: #15a627;margin-bottom: 20px;"></i>Thank you. We will be in touch if we publish your review.</p>');
        </script>
        <?php


    }

    public function getReviews($courseID)
    {

        $reviews = ORM::for_table("courseReviews")->where("courseID", $courseID)
            ->where("status", "a")->order_by_expr("RAND()")->find_many();

        if (count($reviews) != 0) {
            return ORM::for_table("courseReviews")->where("courseID", $courseID)
                ->where("status", "a")->order_by_expr("RAND()")->find_many();
        } else {
            return ORM::for_table("testimonials")->where("location", "c")
                ->order_by_expr("RAND()")->find_many();
        }


    }


    public function saveCourse(array $input)
    {

        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table("courses")->find_one($input['id']);
        } else { //For Create
            $whenAdded = isset($data["whenAdded"]) ? $data["whenAdded"]
                : date("Y-m-d H:i:s");
            $item = ORM::for_table("courses")->create();
        }

        $data = $input;
//        $data = [
//          'title' => $input['title']
//        ];
        if (@$data['wpImage']) {
            unset($data['wpImage']);
        }
        if (@$data['modules']) {
            unset($data['modules']);
        }
        unset($data['categories']);

        if (@$data['mega_courses']) {
            unset($data['mega_courses']);
        }

        //$data['slug'] = isset($data["slug"]) ? $data["slug"]
        //    : $this->createSlug($data["title"]);
        if (isset($whenAdded)) {
            $data['whenAdded'] = $whenAdded;
        }
        $data['whenUpdated'] = isset($data["whenUpdated"])
            ? $data["whenUpdated"] : date("Y-m-d H:i:s");

        // Check if already have slug
        $slugCourse = ORM::for_table('courses')
            ->where('slug', $data['slug']);
        if(isset($input['id'])) {
            $slugCourse = $slugCourse->where_not_equal('id', $input['id']);
        }
        $slugCourse = $slugCourse->count();
        if($slugCourse >= 1){
            $data['slug'] = $data['slug'] . '-' . ($slugCourse + 1);
        }

        $item->set($data);
        $item->save();

        if (@$input['wpImage']
            && ($this->medias->hasMedia(courseController::class, $item->id)
                === false)
        ) {
            $this->medias->saveWPImage($input['wpImage'], array('type' => courseController::class, 'id' => $item->id), 'main_image', false, true);
        }

        // Update Categories
        if ($input['categories']) {
            $this->updateCourseCategories($item->id, $input['categories']);
        }

        // Update mega_courses
        if (@$input['mega_courses']) {
            $this->updateMegaCourses($item->id, $input['mega_courses']);
        }

        // redirect to edit course so modules, etc can be added
        return $item;
    }

    protected function updateMegaCourses(int $courseID, array $megaCourses)
    {
        if (@$megaCourses) {
            foreach ($megaCourses as $mc) {
                $mCourse = $this->getCourseByID($mc);

                $newChildCourses = [];

                if (@$mCourse->childCourses) {
                    $newChildCourses = json_decode($mCourse->childCourses);
                }
                array_push($newChildCourses, $courseID);
                $mCourse->set(array('childCourses' => json_encode(array_unique($newChildCourses))));
                $mCourse->save();
            }
        }
    }

    public function updateCourseCategories(int $course_id, array $categories)
    {
        if (count($categories) >= 1) {
            foreach ($categories as $category) {
                $item = ORM::for_Table("courseCategoryIDs")
                    ->where("course_id", $course_id)
                    ->where("category_id", $category)
                    ->find_one();
                if (empty($item)) {
                    $item = ORM::for_table("courseCategoryIDs")->create();
                    $item->set(array(
                        'course_id'   => $course_id,
                        'category_id' => $category,
                    ));
                }
                $item->save();
            }
        }
    }

    public function getCategoryByOldId(int $oldId)
    {
        $item = ORM::for_table("courseCategories")
            ->where('oldID', $oldId)
            ->find_one();
        return $item;
    }

    public function getCategoryById(int $id)
    {
        $item = ORM::for_table("courseCategories")
            ->find_one($id);
        return $item;
    }

    public function createSlug($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public function getCourseImage(int $courseId, $size = 'full', $type = 'main_image')
    {
        $media = $this->medias->getMedia(courseController::class, $courseId,
            $type);

        if (@$media['url']) {
            $url = $media['url'];
            if ($size != 'full') {
                $url = str_replace($media['fileName'],
                    $size.'/'.$media['fileName'], $url);
            }
        } else {
            $url = '';
        }

        return $url;
    }

    public function getCourseCategories($courseId)
    {

        $items = ORM::for_table("courseCategoryIDs")
            ->where("course_id", $courseId)
            ->find_many();

        $categories = array();

        foreach ($items as $item) {

            $category = ORM::for_table("courseCategories")
                ->find_one($item->category_id);

            $categories[$category->id] = $category->title;

        }

        return $categories;

    }

    public function getCategoriesByIds($ids)
    {
        $categories = ORM::for_table("courseCategories")
            ->where_in('id', $ids)
            ->where_not_equal('title', 'Courses')
            ->order_by_asc('parentID')
            ->find_many();

        return $categories;

    }

    // check if course has a certain category assigned, mainly used when editing a course to prefill checkboxes
    public function checkCourseCategory($courseId, $categoryId)
    {

        $items = ORM::for_table("courseCategoryIDs")
            ->where("course_id", $courseId)
            ->where("category_id", $categoryId)
            ->count();

        if ($items == 0) {
            return false;
        } else {
            return true;
        }

    }


    public function saveCourseAssigned(array $input)
    {
        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table("coursesAssigned")->find_one($input['id']);
        } else { //For Create
            $item = ORM::for_table("coursesAssigned")->create();
        }

        $data = [
            'accountID'     => $input['accountID'],
            'courseID'      => $input['courseID'],
            'whenAssigned'  => $input['whenAssigned'],
            'currentModule' => $input['currentModule'],
            'completed'     => $input['completed'],
            'certNo'        => $input['certNo'],
            'whenCompleted' => $input['whenCompleted'],
            'percComplete'  => $input['percComplete'],
        ];
        if(@$input['currentModuleKey']){
            $data['currentModuleKey'] = $input['currentModuleKey'];
        }

        $item->set($data);
        $item->save();
        return $item;
    }

    public function relatedCourses($categories, $course, $offset)
    {

        return ORM::for_table("courseCategoryIDs")
            ->where_not_equal("course_id", $course)
            ->where_in("category_id", $categories)
            ->where_not_equal("category_id", "8")
            ->limit(20)
            ->offset($offset)
            ->order_by_desc("id")
            ->find_many();


    }

    public function getModuleQuiz($module)
    {

        return ORM::for_table("quizzes")->where("moduleID", $module)
            ->find_one();

    }

    public function getModuleQuizQuestions()
    {

        $items = ORM::for_table("quizQuestions")
            ->where("quizID", $this->get["id"])
            ->order_by_asc("ord")
            ->find_many();

        foreach ($items as $item) {
            $answers = json_decode($item->answerData, true);
            ?>
            <div class="module" id="singleQuestion<?= $item->id ?>"
                 style="background:#fff;">
                <div class="form-group">
                    <form name="updateQuestion<?= $item->id ?>">
                        <input type="text" style="font-weight:bold;"
                               class="form-control" name="question"
                               value="<?= $item->question ?>"/>
                        <br/>
                        <label>Answer Type:</label>
                        <select class="form-control" name="answerType">
                            <option value="single">Single</option>
                        </select>

                        <br/>
                        <p><strong>Answers:</strong></p>

                        <div id="answers<?= $item->id ?>">
                            <?php
                            $count = 0;
                            foreach ($answers as $answer) {
                                $count++;
                                ?>
                                <div class="module"><input type="text"
                                                           class="form-control"
                                                           name="answer_<?= $count ?>"
                                                           value="<?= $answer["*_answer"] ?>"
                                                           style="margin-bottom:5px;"/><input
                                            type="checkbox"
                                            name="correct_<?= $count ?>"
                                            value="1"
                                            <?php if ($answer["*_correct"]
                                            == "1") { ?>checked<?php } ?> />
                                    Correct<a href="javascript:;"
                                              class="btn btn-danger btn-small pull-right"
                                              style="padding: 0px 8px;"><i
                                                class="fa fa-trash"></i></a>
                                </div>
                                <?php

                            }
                            ?>
                        </div>

                        <input type="hidden" name="answerCount"
                               id="answerCount<?= $item->id ?>"
                               value="<?= $count ?>"/>

                        <p>
                            <button type="submit"
                                    class="btn btn-success btn-small">
                                <i class="fa fa-check"></i>
                                Update
                            </button>
                            <a href="javascript:;"
                               onclick="addAnswer<?= $item->id ?>();"
                               class="btn btn-info btn-small">
                                <i class="fa fa-plus"></i>
                                Add Answer
                            </a>
                        </p>
                        <input type="hidden" name="itemID"
                               value="<?= $item->id ?>"/>
                    </form>
                    <?php
                    $this->renderFormAjax("blumeNew", "edit-question",
                        "updateQuestion".$item->id);
                    ?>

                    <script type="text/javascript">
                        function addAnswer<?= $item->id ?>() {

                            var count = $("#answerCount<?= $item->id ?>").val();
                            var newCount = parseInt(count) + 1;

                            $("#answers<?= $item->id ?>").append('<div class="module"><input type="text" class="form-control" name="answer_' + newCount + '" value="" style="margin-bottom:5px;" /><input type="checkbox" name="correct_' + newCount + '" value="1" /> Correct<a href="javascript:;" class="btn btn-danger btn-small pull-right" style="padding: 0px 8px;"><i class="fa fa-trash"></i></a></div>');

                            $("#answerCount<?= $item->id ?>").val(newCount);

                        }
                    </script>

                    <hr/>

                    <p>
                        <a href="javascript:;"
                           onclick="deleteQuestion<?= $item->id ?>();"
                           class="btn btn-danger btn-small">
                            <i class="fa fa-trash"></i>
                            Delete Question
                        </a>
                    </p>

                    <script>
                        function deleteQuestion<?= $item->id ?>() {

                            if (window.confirm("Are you sure you want to delete this question?")) {
                                $("#singleQuestion<?= $item->id ?>").slideUp();
                                $("#returnStatus").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-question&id=<?= $item->id ?>");
                            }

                        }
                    </script>

                </div>
            </div>
            <?php
        }

        if (count($items) == 0) {
            echo "<p><em>There are currently no questions in this quiz.</em></p>";
        }


    }

    public function coursePdfContents()
    {


        $id = $this->get["id"];

        if (!$id) {
            $this->force404();
            exit;
        }

        $course = ORM::for_table("courses")->find_one($id);

        if ($course->id == "") {
            $this->force404();
            exit;
        }

        ?>
        <div style="background: #fff; width: 100%">
            <div style="padding:10px 0;width:750px;">
                <div style="text-align: center; margin-bottom: 50px">
                    <img src="<?= SITE_URL ?>assets/images/logo-blue.png"
                         style="width:200px;"/>
                </div>

                <div style="text-align: center; page-break-after:always">
                    <h1><?= $course->title ?></h1>
                    <?php if ($this->getCourseImage($course->id, "medium")) { ?>
                        <img src="<?= $this->getCourseImage($course->id,
                            "medium") ?>"
                             style="width:100%; margin-top: 10px"/>
                    <?php } ?>
                </div>

                <div>
                    <h1>Table of Contents:</h1>
                    <?php
                    $modules = ORM::for_table("courseModules")
                        ->where("courseID", $course->id)
                        ->order_by_asc("ord")
                        ->find_many();

                    foreach ($modules as $key => $module) {
                        ?>
                        <div><?= $module->title ?></div>
                        <?php
                    }
                    ?>
                </div>

                <?php
                $modules = ORM::for_table("courseModules")
                    ->where("courseID", $course->id)
                    ->order_by_asc("ord")
                    ->find_many();

                foreach ($modules as $key => $module) {
                    $description = preg_replace("/<vidoe[^>]+\>/i", " ",
                        $module->description);
                    $description = preg_replace("/\[Tweet(.*?)\]/", "",
                        $description);
                    $description
                        = preg_replace("/<img class='alignright [^>]+\>/i", " ",
                        $description);
                    $description
                        = preg_replace("/<img class='alignleft [^>]+\>/i", " ",
                        $description);

                    $contents = preg_replace("/<vidoe[^>]+\>/i", " ",
                        $module->contents);
                    $contents = preg_replace("/\[Tweet(.*?)\]/", "", $contents);
                    $contents
                        = preg_replace('/<img class="alignright [^>]+\>/i', " ",
                        $contents);
                    $contents = preg_replace('/<img class="alignleft[^>]+\>/i',
                        " ", $contents);
                    $contents = preg_replace("/<svg[^>]+\>/i", " ",
                        $module->contents);
                    ?>
                    <div class="module" id="module<?= $module->id ?>"
                         style="margin-top: 20px; page-break-before:always">
                        <h1><?= $module->title ?></h1>
                        <div class="row">
                            <pre style="white-space: break-spaces; font-family: serif;"><?= html_entity_decode($description) ?></pre>
                        </div>
                        <div class="row">
                            <pre style="white-space: break-spaces; font-family: serif;"><?= html_entity_decode($contents) ?></pre>
                        </div>
                    </div>
                    <?php
                }
                ?>

                <div style="margin-top: 80px">copyright</div>
            </div>
        </div>

        <div style="height:1px;width:100%;background:#eeeeee;margin-top:10px;"></div>

        <style>
            body {
                padding: 0;
                margin: 0;
            }

            /*.diff {*/
            /*    margin-top: 50px;*/
            /*    margin-bottom: 50px;*/
            /*}*/

            h1, h2, h3, h4, h5, h6 {
                margin-top: 15px;
                margin-bottom: 15px;
            }

            p {
                margin-top: 15px;
                margin-bottom: 15px;
            }

            ul {
                line-height: 20px;
            }

            .alignright {
                float: right;
                margin: 0 0 1em 20px;
            }

            img {
                vertical-align: middle;
                border-style: none;
            }
        </style>

        <?php
    }

    public function getModulePdf()
    {


        $id = $this->get["id"];
        $print = $this->get["print"];

        if (!$id) {
            $this->force404();
            exit;
        }

        $module = ORM::for_table("courseModules")->find_one($id);

        if ($module->id == "") {
            $this->force404();
            exit;
        }

        $pdfContent = file_get_contents(SITE_URL
            .'ajax?c=course&a=module-pdf-contents&id='.$id);

        //echo $pdfContent;
        //exit;

        $mpdf = new Mpdf([
            'tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf'
        ]);
        $mpdf->WriteHTML($pdfContent);
        $mpdf->Output($module->title.'.pdf', $print ? 'I' : 'D');

        die('done');
    }

    public function modulePdfContents()
    {
        $id = $this->get["id"];

        if (!$id) {
            $this->force404();
            exit;
        }

        $module = ORM::for_table("courseModules")->find_one($id);

        if ($module->id == "") {
            $this->force404();
            exit;
        }

        ?>
        <div style="background: #fff; width: 100%">
            <div style="padding:10px 0;width:750px;">
                <div style="text-align: center; margin-bottom: 50px">
                    <img src="<?= SITE_URL ?>assets/images/logo-blue.png"
                         style="width:200px;"/>
                </div>

                <?php
                $description = preg_replace("/<img[^>]+\>/i", " ",
                    $module->description);
                $description = preg_replace("/<video[^>]+\>/i", " ",
                    $description);

                $contents = $module->contents;
                //$contents = preg_replace("/<img[^>]+\>/i", " ",
                //    $module->contents);
                $contents = preg_replace("/<video[^>]+\>/i", " ", $contents);
                $contents = preg_replace("/<svg[^>]+\>/i", " ", $contents);
                ?>
                <div class="module" id="module<?= $module->id ?>"
                     style="margin-top: 20px;">
                    <h1><?= $module->title ?></h1>
                    <div class="row">
                        <pre style="white-space: break-spaces; font-family: serif;"><?= html_entity_decode($description) ?></pre>
                    </div>
                    <div class="row">
                        <pre style="white-space: break-spaces; font-family: serif;"><?= html_entity_decode($contents) ?></pre>
                    </div>
                </div>
                <?php
                ?>

                <!--                <div style="margin-top: 80px">copyright</div>-->
            </div>
        </div>

        <div style="height:1px;width:100%;background:#eeeeee;margin-top:10px;"></div>

        <style>
            body {
                padding: 0;
                margin: 0;
            }

            /*.diff {*/
            /*    margin-top: 50px;*/
            /*    margin-bottom: 50px;*/
            /*}*/

            h1, h2, h3, h4, h5, h6 {
                margin-top: 15px;
                margin-bottom: 15px;
            }

            p {
                margin-top: 15px;
                margin-bottom: 15px;
            }

            ul {
                line-height: 20px;
            }
        </style>

        <?php
    }

    protected function checkCourseRewards($accountId)
    {

        $rewards = ORM::forTable('rewards')
            ->where('category', 'courses')
            ->orderByAsc('rorder')
            ->find_many();

        $accountCompletedCourses = ORM::for_table('coursesAssigned')
            ->where('accountID', $accountId)
            ->where('completed', '1')
            ->count();


        if ($rewards) {
            foreach ($rewards as $reward) {
                $totalCompletedCourses = str_replace("courses_", "",
                    $reward->short);
                if ($accountCompletedCourses >= $totalCompletedCourses) {
                    $this->rewardsAssigned->assignReward($accountId,
                        $reward->short, true, true, $reward->points);
                } else {
                    break;
                }
            }
        }

        $completeCourseLimit = $this->getSetting('reward_course_limit');
        if (($accountCompletedCourses >= 1)
            && ($accountCompletedCourses > $completeCourseLimit)
        ) {
            $signInRewardPoints
                = $this->getSetting('reward_course_points_after_limit');
            $this->rewardsAssigned->assignReward($accountId,
                "Complete ".$accountCompletedCourses." courses", false, true,
                $signInRewardPoints);

        }
    }

    public function userSaveCourse()
    {

        if (CUR_ID_FRONT == "") {
            ?>
            <script>
                $("#newAccountSavedCourse").val('<?= $this->post["id"] ?>');
                $("#newAccount").modal("toggle");
            </script>
            <?php
            exit;
        }

        // check for existing
        $existing = ORM::for_table("coursesSaved")
            ->where("userID", CUR_ID_FRONT)
            ->where("courseID", $this->post["id"])->count();

        if ($existing == 1) {
            // delete
            $delete = ORM::for_table("coursesSaved")
                ->where("userID", CUR_ID_FRONT)
                ->where("courseID", $this->post["id"])->delete_many();

            ?>
            <script>
                $(".saveCourse<?= $this->post["id"] ?>").removeClass("active");
            </script>
            <?php

        } else {
            // add
            $item = ORM::for_table("coursesSaved")->create();

            $item->userID = CUR_ID_FRONT;
            $item->courseID = $this->post["id"];
            $item->set_expr("whenAdded", "NOW()");

            $item->save();

            ?>
            <script>
                $(".saveCourse<?= $this->post["id"] ?>").addClass("active");
            </script>
            <?php

        }


    }


    public function getCourseIDsByCategoryIDs($categoryIDs)
    {
        return ORM::for_table("courseCategoryIDs")
            ->where_in('category_id', $categoryIDs)
            ->find_array();
    }

    public function getCoursesCount($filters = null)
    {
        $courses = $this->getCoursesSql($filters);
        return $courses->count();
        $courseIDs = [];

        if (@$filters['categoryIDs'][0]) {
            $courseIDs[0] = 0;
            $cIDs = $this->getCourseIDsByCategoryIDs($filters['categoryIDs']);
            if (count($cIDs) >= 1) {
                $i = 1;
                foreach ($cIDs as $cID) {
                    $courseIDs[$i] = $cID['course_id'];
                    $i++;
                }
            }
        }

        $courses = ORM::for_table('courses')->where_id_in($courseIDs);

        $whereRaw = [];

        $whereRaw[] = 'hidden = "0"';
        $whereRaw[] = 'is_lightningSkill = "0"';

        // if we are in GBP, then ignore US courses
        $currency = $this->currentCurrency();

        if($currency->code == "GBP") {
            $whereRaw[] = 'usImport = "0"';
        } else {
            $whereRaw[] = 'usImport = "1"';
        }

        if (@$filters['searchText']) { //Search Text
            $searchText = $filters['searchText'];
            $whereRaw[] = '(title LIKE "%'.$searchText
                .'%" OR description LIKE "%'.$searchText
                .'%" OR additionalContent LIKE "%'.$searchText
                .'%" OR seoKeywords LIKE "% '.$searchText
                .'" OR seoKeywords LIKE "% '.$searchText.',%")';
        }


        if (@$filters['duration']) { //Course Duration
            $duration = explode('-', $filters['duration']);
            $whereRaw[] = 'duration BETWEEN '.$duration[0].' AND '.$duration[1];
        }

        if (@$filters['types']) { //Course Duration
            foreach ($filters['types'] as $type) {
                if ($type == 'video') {
                    $types[] = "is_video = '1'";
                }
                if ($type == 'audio') {
                    $types[] = "is_audio = '1'";
                }
                if ($type == 'text') {
                    $types = [];
                }
            }
            if (@$types) {
                $whereRaw[] = '('.implode(' OR ', $types).')';
            }
        }

        if (@$whereRaw) {
            $courses = $courses->where_raw(implode(" AND ", $whereRaw));
        }

        $courses = $courses->count();

        return $courses;
    }

    public function getCourses(
        $offset,
        $limit,
        $order,
        $orderBy,
        array $filters = null
    ) {
        $courseIDs = [];
        if (@$filters['categoryIDs'][0]) { //By Category
            $cIDs = $this->getCourseIDsByCategoryIDs($filters['categoryIDs']);
            if (count($cIDs)) {
                $i = 0;
                foreach ($cIDs as $cID) {
                    $courseIDs[$i] = $cID['course_id'];
                    $i++;
                }
            }
        }

        $courses = ORM::for_table("courses")->where_id_in($courseIDs);

        $whereRaw = [];

        $whereRaw[] = 'hidden = "0"';
        $whereRaw[] = 'is_lightningSkill = "0"';

        // if we are in GBP, then ignore US courses
        $currency = $this->currentCurrency();

        if($currency->code == "GBP") {
            $whereRaw[] = 'usImport = "0"';
        } else {
            $whereRaw[] = 'usImport = "1"';
        }

        $courses = $this->getCoursesSql($filters);

        $courses = $orderBy == 'desc' ? $courses->order_by_desc($order)
            : $courses->order_by_asc($order);

        $courses = $courses->offset($offset)
            ->limit($limit)
            ->find_array();

        return $courses;
    }

    public function getCoursesJson()
    {
        $courses = [];
        $userSavedCourses = [];
        $loadMore = 0;
        $filters = $this->post['filters'];
        $offset = $this->post['offset'];
        $limit = $this->post['limit'];
        $order = $this->post['order'];
        $orderBy = $this->post['orderBy'];
        $currentPage = $this->post['currentPage'];

//        echo "<pre>";
//        print_r($params);
//        die;

        $total = $this->getCoursesCount($filters);

        if ($total >= 1) {
            $loadMore = ($offset + $limit) < $total ? 1 : 0;
            $posts = $this->getCourses($offset, $limit, $order, $orderBy, $filters);
            $i = 0;
            foreach ($posts as $post) {

                $post['price'] = $this->getCoursePrice($post);

                // affiliate pricing
                $excludedCourses = explode(",", $_SESSION["excludedCourses"]);
                if ($_SESSION["affiliateDiscount"] != ""
                    && !in_array($post['id'], $excludedCourses)
                ) {


                    $original = $post['price'];
                    $discounted = $post['price'];
                    $changed = false;

                    if ($_SESSION["affiliateDiscountType"] == "fixed") {
                        $discounted = $discounted
                            - $_SESSION["affiliateDiscount"];
                    } else {
                        $discounted = $discounted * ((100
                                    - $_SESSION["affiliateDiscount"]) / 100);
                    }

                    if($_SESSION["affiliateDiscountMax"] != "" && $_SESSION["affiliateDiscountMin"] != "") {


                        if($post['price'] <= $_SESSION["affiliateDiscountMax"] && $post['price'] >= $_SESSION["affiliateDiscountMin"]) {
                            $post['price'] = $discounted;
                            $changed = true;
                        }

                    } else if ($_SESSION["affiliateDiscountMax"] != "") {

                        if ($post['price']
                            <= $_SESSION["affiliateDiscountMax"]
                        ) {
                            $post['price'] = $discounted;
                            $changed = true;
                        }

                    } else {
                        if ($_SESSION["affiliateDiscountMin"] != "") {

                            if ($post['price']
                                >= $_SESSION["affiliateDiscountMin"]
                            ) {
                                $post['price'] = $discounted;
                                $changed = true;
                            }

                        } else {
                            $post['price'] = $discounted;
                            $changed = true;
                        }
                    }


                    if ($changed == true) {
                        $courses[$i]['price']
                            = '<small class="wasPriceSmall">RRP <s>'
                            .$this->price($original).'</s></small>'
                            .$this->price($post['price']);
                    } else {
                        $courses[$i]['price'] = $this->price($post['price']);
                    }

                } else {
                    $courses[$i]['price'] = $this->price($post['price']);
                }

                $totalStudents = $post['enrollmentCount'];

                // show learning types to users
                $learningTypeHTML = '<img data-toggle="tooltip" alt="Text based modules"
                 title="Text based modules"
                 src="'.SITE_URL.'assets/images/text-based-modules.png">
            <img data-toggle="tooltip" alt="Images" title="Images"
                 src="'.SITE_URL.'assets/images/images-courses.png" style="margin-left:-1px;">';

                if($post['is_video'] == "1") {
                    $learningTypeHTML .= '<img data-toggle="tooltip" alt="Video learning"
                 title="Video learning"
                 src="'.SITE_URL.'assets/images/video-learning.png">';
                }


                if($post['is_audio'] == "1") {
                    $learningTypeHTML .= '<img data-toggle="tooltip" alt="MP3 audio lessons"
                 title="MP3 audio lessons"
                 src="'.SITE_URL.'assets/images/mp3-audio-learning.png">';
                }

                $courses[$i]['id'] = $post['id'];
                $courses[$i]['oldID'] = $post['oldID'];
                $courses[$i]['productID'] = $post['productID'];
                $courses[$i]['title'] = $post['title'];
                $courses[$i]['learningTypes'] = $learningTypeHTML;

                if ($totalStudents == 0) {
                    $courses[$i]['total_students'] = "New Course";
                } else {
                    $courses[$i]['total_students'] = $totalStudents
                        .' students enrolled';
                }

                $courses[$i]['total_modules'] = ORM::for_table("courseModules")
                    ->where("courseID", $post['id'])->count();

                if ($courses[$i]['total_modules'] == 1) {
                    $courses[$i]['module_text'] = "Module";
                } else {
                    $courses[$i]['module_text'] = "Modules";
                }

                if ($post["childCourses"] != "") {
                    $courses[$i]['total_modules']
                        = count(json_decode($post["childCourses"]));
                    $courses[$i]['module_text'] = "Courses";
                }

                $courses[$i]['url'] = SITE_URL.'course/'.$post['slug'];
                $courses[$i]['image_url'] = $this->getCourseImage($post['id'],
                    'large');

                $categories_string = '';
                $categories = $this->getCourseCategories($post['id']);
                if (!empty($categories)) {
                    $categories_string = implode(', ', $categories);
                }
                $courses[$i]['categories'] = $categories_string;
                $courses[$i]['course_type'] = $this->getCourseType($post['id']);

                $rating = '';

                if($post["averageRating"] != "" && $post["averageRating"] > 3.6 && $post["totalRatings"] > 10) {

                    $rating = $this->starRating($post["averageRating"]);

                    $word = 'ratings';
                    if($post["totalRatings"] == "1") {
                        $word = 'rating';
                    }

                    $rating .= '<span class="space"></span> '.$post["averageRating"].' ('.$post["totalRatings"].' '.$word.')';

                }

                $courses[$i]['rating'] = $rating;


                $i++;
            }
            if (CUR_ID_FRONT) {
                $coursesSaved = ORM::for_table('coursesSaved')
                    ->where('userID', CUR_ID_FRONT)
                    ->select('courseID')
                    ->find_array();
                if ($coursesSaved) {
                    foreach ($coursesSaved as $saved) {
                        $userSavedCourses[] = $saved['courseID'];
                    }
                }
            }
        }

        //Main query
        $this->pages->default_ipp = 15;
        $this->pages->items_total = $total;
        $this->pages->mid_range = 6;
        $this->pages->current_page = $currentPage;
        $this->pages->current_url = $_POST['currentUrl'];

        if(@$filters){
            $queryFilters['order'] = $order.'-'.$orderBy;
            foreach ($filters as $key=>$value){
                if($key == 'searchText'){
                    $queryFilters['search'] = $value;
                }elseif($key == 'categoryIDs'){
                    $queryFilters['categoryIDs'] = implode(',', $value);
                }else{
                    $queryFilters[$key] = $value;
                }
            }
            $this->pages->queryParams = $queryFilters;
        }

        $this->pages->paginate();
        $paginationHtml = '';

        if($this->pages->items_total > 0){
            $paginationHtml .= $this->pages->display_pages();
            //$paginationHtml .= $this->pages->display_items_per_page();
            //$paginationHtml .= $this->pages->display_jump_menu();
        }


        $data = [
            'courses'          => $courses,
            'total'            => $total,
            'totalPages'       => ceil($total/$limit),
            'loadMore'         => $loadMore,
            'userSavedCourses' => $userSavedCourses,
            'paginationHtml'   => $paginationHtml
        ];

        echo json_encode(array(
            'status' => 200,
            'data'   => $data
        ));
        exit;
    }

    public function getCoursesSql($filters = null) {
        $courseIDs = [];
        $whereRaw = [];
        $courses = ORM::for_table("courses");

        if (@$filters['categoryIDs'][0]) { //By Category
            $cIDs = $this->getCourseIDsByCategoryIDs($filters['categoryIDs']);
            if (count($cIDs)) {
                $i = 0;
                foreach ($cIDs as $cID) {
                    $courseIDs[$i] = $cID['course_id'];
                    $i++;
                }
            }
        }elseif (@$filters['searchText']){
            $searchCategory = $this->getCourseCategoryByName($filters['searchText']);

            if(@$searchCategory->id){
                $cIDs = $this->getCourseIDsByCategoryIDs([$searchCategory->id]);
                if (count($cIDs)) {
                    $i = 0;
                    foreach ($cIDs as $cID) {
                        $courseIDs[$i] = $cID['course_id'];
                        $i++;
                    }
                }
            }
        }

        $whereRaw[] = 'hidden = "0"';
        $whereRaw[] = 'is_lightningSkill = "0"';

        $currency = $this->currentCurrency();

        if($currency->code == "GBP") {
            $whereRaw[] = 'usImport = "0"';
        } else {
            $whereRaw[] = 'usImport = "1"';
        }

        if (@$filters['searchText']) { //Search Text
            $searchText = $filters['searchText'];
            $whereOrRaw = '(';
            $whereOrRaw .= 'title LIKE "%'.$searchText
                .'%" OR description LIKE "%'.$searchText
                .'%" OR additionalContent LIKE "%'.$searchText
                .'%" OR seoKeywords LIKE "% '.$searchText
                .'" OR seoKeywords LIKE "% '.$searchText.',%"';
            if(count($courseIDs) >= 1){
                $whereOrRaw .= ' OR id IN ('.implode(',', $courseIDs).')';
            }
            $whereOrRaw .= ')';
            $searchTextCourses = ORM::for_table('courses')
                ->select('id')
                ->where_raw($whereOrRaw)->find_array();
            if(count($searchTextCourses) >= 1){
                foreach ($searchTextCourses as $sCourse){
                    $courseIDs[] = $sCourse['id'];
                }
            }
        }


//        if($order == "averageRating") {
//            // exclude those with less than 10
//            $whereRaw[] = 'totalRatings > 10';
//        }

        if (@$filters['duration']) { //Course Duration
            $duration = explode('-', $filters['duration']);
            $whereRaw[] = 'duration BETWEEN '.$duration[0].' AND '.$duration[1];
        }

        if (@$filters['types']) { //Course types
            $filterTypes = explode(',', $filters['types']);
            foreach ($filterTypes as $type) {
                if ($type == 'video') {
                    $types[] = "is_video = 1";
                }
                if ($type == 'audio') {
                    $types[] = "is_audio = 1";
                }
                if ($type == 'text') {
                    $types = [];
                }
            }
            if (@$types) {
                $whereRaw[] = '('.implode(' OR ', $types).')';
            }
        }

        if(count($courseIDs) >= 1){
            $whereRaw[] = 'id IN ('.implode(',', $courseIDs).')';
        }


        if (@$whereRaw) {
            $courses = $courses->where_raw(implode(" AND ", $whereRaw));
        }

//        echo "<pre>";
//        print_r($courses->find_many());
//        echo "</pre>";

        return $courses;
    }

    public function getUserCoursesCount($filter = null, $searchText = null, $onlyCompletedForSub = false)
    {

        $sql
            = "Select Count(coursesAssigned.id) as total from coursesAssigned join courses on courses.id=coursesAssigned.courseID where coursesAssigned.accountID='"
            .CUR_ID_FRONT."' and coursesAssigned.bundleID IS NULL";


        //->join('courses', array('courses.id', '=', 'coursesAssigned.courseID'))
        //->where("coursesAssigned.accountID", CUR_ID_FRONT)
        //->where_null("coursesAssigned.bundleID");

        if (@$searchText) {
            $sql .= " and courses.title LIKE '%".$searchText."%'";
        }

        if ($filter == 'completed') {
            $sql .= " and coursesAssigned.completed='1'";
        } elseif ($filter == 'active') {
            if($onlyCompletedForSub){
                $sql .= " and coursesAssigned.sub='0' and coursesAssigned.completed='0'";
            }else{
                $sql .= " and coursesAssigned.completed='0'";
            }

        }elseif($onlyCompletedForSub){
            $sql .= " and ((coursesAssigned.sub='0'))";
        }


        $total = ORM::for_table("coursesAssigned")
            ->raw_query($sql)
            ->find_one();

        return $total->total;
    }

    public function getUserCourses(
        $offset,
        $limit,
        $order,
        $orderBy,
        $filter = null,
        $searchText = null,
        $onlyCompletedForSub = false
    ) {

        $sql
            = "Select coursesAssigned.* from coursesAssigned join courses on courses.id=coursesAssigned.courseID where coursesAssigned.accountID='"
            .CUR_ID_FRONT."' and coursesAssigned.bundleID IS NULL";

        if (@$searchText) {
            $sql .= " and courses.title LIKE '%".$searchText."%'";
        }


        if ($filter == 'completed') {
            $sql .= " and coursesAssigned.completed='1'";
        } elseif ($filter == 'active') {
            if($onlyCompletedForSub){
                $sql .= " and coursesAssigned.sub='0' and coursesAssigned.completed='0'";
            }else{
                $sql .= " and coursesAssigned.completed='0'";
            }

        }elseif($onlyCompletedForSub){
            $sql .= " and ((coursesAssigned.sub='0'))";
        }

        $sql .= " order by courses.personalTraningOrder asc, coursesAssigned.id desc limit $offset,$limit";


        $coursesAssigned = ORM::for_table("coursesAssigned")
            ->raw_query($sql)
            ->find_array();

        return $coursesAssigned;
    }

    public function getUserCoursesJson()
    {
        $courses = [];
        $loadMore = 0;
        $offset = $this->post['offset'];
        $limit = $this->post['limit'];
        $order = $this->post['order'];
        $orderBy = $this->post['orderBy'];
        $filter = $this->post['filter'];
        $searchText = $this->post['searchText'];

        $onlyCompletedForSub = false;

        if($this->isActiveSubscription(CUR_ID_FRONT) === false){
            $onlyCompletedForSub = true;
        }

        $total = $this->getUserCoursesCount($filter, $searchText, $onlyCompletedForSub);



        if ($total >= 1) {
            $loadMore = ($offset + $limit) < $total ? 1 : 0;
            $items = $this->getUserCourses($offset, $limit, $order, $orderBy,
                $filter, $searchText, $onlyCompletedForSub);
            $i = 0;
            $userCourses = [];
            $trainingCourseLevel2Completed = false;
            foreach ($items as $item) {
                $course = ORM::for_table("courses")->find_one($item['courseID']);

                if (@$course->id) {

                    $item['btnLabel'] = "Access";
                    if ($item['percComplete'] == "100") {
                        $item['btnLabel'] = "Revisit";
                    }
                    $item['percComplete']
                        = $item['percComplete'] = 0 ? 0
                        : number_format($item['percComplete']);
                    $item['courseTitle'] = $course->title;
                    $item['imageUrl'] = $this->getCourseImage($course->id,
                        "large");
                    $item['courseUrl'] = SITE_URL.'start/'.$course->slug;
                    if ($item['activated'] == 1) {
                        $item['courseUrl'] = $course->redirectUrl ??
                            'https://shop.sheilds.org/customer/account/login/';
                    }
                    $item['certificateUrl'] = SITE_URL
                        .'ajax?c=certificate&a=cert-pdf&id='.$item['id'];

                    // check training courses progress
                    $item['trainingCourseDisable'] = false;
                    if(!empty($course->personalTraningOrder)){
                        if($course->personalTraningOrder == 1) {
                            $trainingCourseLevel2Completed = $this->checkCourseComplete($course);
                        }elseif ($trainingCourseLevel2Completed === false && ($course->personalTraningOrder == 2)) {
                            $item['trainingCourseDisable'] = true;
                        }
                    }

                    $item["sellPrinted"] = $course->sellPrinted;
                    $item["sellPrintedPrice"] = $this->price($course->sellPrintedPrice);
                    $item["sellPrintedCheckoutUrl"] = SITE_URL.'ajax?c=cart&a=add-printed-course&id='.$course->id;

                    $userCourses[] = $item;
                }
            }
        }
        $data = [
            'userCourses' => $userCourses,
            'total'       => $total,
            'loadMore'    => $loadMore,
        ];

        echo json_encode(array(
            'status' => 200,
            'data'   => $data
        ));
        exit;
    }

    public function updateColumn()
    {
        $id = $this->post['id'];
        $column = $this->post['column'];
        $value = $this->post['value'];

        $course = ORM::for_table('courses')->find_one($id);
        if (@$course->id) {
            $course->$column = $value;
            $course->save();
            $this->setAlertSuccess("Course details updated");
        } else {
            $this->setAlertSuccess("Something went wrong!");
        }
    }

    public function getCourseType($id)
    { // for Zubaer/marketing

        $course = ORM::for_table("courses")->find_one($id);

        $type = "Full Course";

        if ($course->childCourses != "") {
            $type = "Mega Course";
        } else {
            if ($course->is_video == "1") {
                $type = "Video Course";
            } else {
                if ($course->is_lightningSkill == "1") {
                    $type = "Short Course";
                }
            }
        }

        return $type;

    }

    public function deleteUserDuplicates($id)
    {

        // delete duplicate course enrollments for a given user, possibly caused by import errors
        $enrollments = ORM::for_table("coursesAssigned")
            ->where_null("bundleID")
            ->where("accountID", $id)
            ->group_by("courseID")
            ->find_many();

        foreach ($enrollments as $enrollment) {

            $duplicate = ORM::for_table("coursesAssigned")
                ->where("courseID", $enrollment->courseID)
                ->where("accountID", $id)
                ->where("whenAssigned", $enrollment->whenAssigned)
                ->where_not_equal("id", $enrollment->id)
                ->find_one();

            if ($duplicate->id != "") {
                $duplicate->delete();
            }

        }


    }

    public function checkBundleProgress($id)
    {

        // check if child courses of a bundle are complete so we know whether to mark the bundle as complete or not
        $enrollments = ORM::for_table("coursesAssigned")
            ->where("accountID", $id)
            ->where_null("bundleID")
            ->find_many();

        foreach ($enrollments as $enrollment) {

            $completed = 1;

            // check if children exist
            $children = ORM::for_table("coursesAssigned")
                ->where("accountID", $id)->where("bundleID", $enrollment->id)
                ->find_many();
            $totalBundleCourses = count($children);
            $totalCompletedBundleCourses = 0;

            if(count($children) >= 1) {
                foreach ($children as $child) {
                    if ($child->completed == "0") {
                        $completed = 0; // entire bundle is not complete
                    }else{
                        $totalCompletedBundleCourses++;
                    }
                }
            }


            $courseAssigned = ORM::for_table("coursesAssigned")
                ->find_one($enrollment->id);

            if (count($children) > 0 && $completed == 1) { // if this enrollment IS a bundle and is complete

                // certificate number
                $randomString = $this->generateCertNo();

                // Complete Current Module
                $this->courseModules->saveCourseModuleProgress([
                    'accountID'     => $id,
                    'courseID'      => $courseAssigned->courseID,
                    'moduleID'      => $courseAssigned->currentModule,
                    'completed'     => 1,
                    'whenCompleted' => date('Y-m-d H:i:s')
                ]);

                // save details
                $courseAssigned->certNo = $randomString;
                $courseAssigned->completed = "1";
                $courseAssigned->set_expr("whenCompleted", "NOW()");
                $courseAssigned->percComplete = "100";
            }else if (count($children) > 0){
                $percentage = number_format(($totalCompletedBundleCourses / $totalBundleCourses) * 100, 2);
                $courseAssigned->percComplete = number_format($percentage, 2);
            }
            $courseAssigned->save();
        }

    }

    public function isQualificationCourse($courseID, $courseCategories = null)
    {
        if (empty($courseCategories)) {
            $courseCategories = $this->getCourseCategories($courseID);
        }

        if (in_array('Qualifications', $courseCategories)) {
            return true;
        }
        return false;
    }

    public function showLinkTimerOnModule($module, $assigned)
    {

        // Decide whether to show a link timer or not on modules
        $linkTimer = true;

        $userDisable = ORM::For_table("accounts")->where("id", CUR_ID_FRONT)
            ->where("disableModuleTimer", "1")->count();

        if ($userDisable == 1) { // disable if disabled on user account
            $linkTimer = false;
            //echo "user disabled";
        }

        // only have the timer if the user has not completed this module before
        $currentAssignedModule = ORM::for_table("courseModules")
            ->find_one($assigned->currentModule);

        if ($module->ord
            < $currentAssignedModule->ord
        ) {  // if assigned module is greater than the current one
            $linkTimer = false;
            //echo "assigned module";
        }

        // if timer is disabled on this module
        if ($module->disableModuleTimer == "1") {
            $linkTimer = false;
            //echo "module timer disabled";
        }

        // if this module is a video, then disable for now
        if ($module->new_style_with_video == "1") {
            $linkTimer = false;
            //echo "new style with video";
        }

        return $linkTimer;

    }

    public function getBundleCourses()
    {
        return ORM::for_table('courses')
            ->where_null('childCourses')
            ->order_by_asc('title')
            ->find_many();
    }

    public function selectBundleCourse()
    {
        $course = ORM::for_table('courses')->find_one($this->post['courseID']);
        echo json_encode([
            'course' => [
                'title'     => $course->title,
                'image_url' => $this->getCourseImage($course->id, 'large'),
                'total_students' => $course->enrollmentCount . ' students enrolled',
                'total_modules' => ORM::for_table("courseModules")
                    ->where("courseID", $course->id)->count(),
                'module_text' => 'Modules',
                'url' => SITE_URL.'course/'.$course->slug,
            ]
        ]);
        exit();
    }

    public function downloadContent($courseID)
    {
        $data = [
            'worksheets' => [],
            'moduleContents' => [],
        ];

        // Check user course Assigned
        $coursesAssigned = ORM::for_table("coursesAssigned")
            ->where("accountID", CUR_ID_FRONT)
            ->where("courseID", $courseID)
            ->find_one();
        if(empty($coursesAssigned)){
            return $data;
        }

        $courseModules = ORM::for_table("courseModules")
            ->where("new_style_with_video", "0")
            ->where("courseID", $courseID)
            ->order_by_asc("parentID")
            ->order_by_asc("ord")
            ->find_many();
        if($courseModules){
            foreach ($courseModules as $module) {

                $dom = new DOMDocument;

                @$dom->loadHTML($module->contents);

                $links = $dom->getElementsByTagName('a');

                foreach ($links as $link) {
                    if (strpos($link->nodeValue, "Worksheet") !== false) {
                        $file = substr($link->getAttribute('href'),
                            strrpos($link->getAttribute('href'), '/') + 1);
                        $worksheet = [];
                        $worksheet['title'] = $module->title;
                        $worksheet['url'] = $link->getAttribute('href');
                        $worksheet['file'] = $file;
                        $data['worksheets'][] = $worksheet;
                    }
                }

                $worksheet = ORM::for_table("media")
                    ->where("modelType", "courseModuleController")
                    ->where("modelId", $module->id)
                    ->where("type", "worksheet")
                    ->find_one();

                // For submodules
                if($module->contentType == 'upload'){
                    $worksheet = ORM::for_table("media")
                        ->where("modelType", "courseModuleController")
                        ->where("modelId", $module->id)
                        ->find_one();
                }
                if ($worksheet->id != "") {
                    $count++;
                    $worksheetMedia = [];
                    $worksheetMedia['title'] = $module->title;
                    $worksheetMedia['url'] = $worksheet['url'];
                    $data['worksheets'][] = $worksheetMedia;
                }

                $pdf = [];
                $pdf['id'] = $module->id;
                $pdf['title'] = $module->title;
                $pdf['url'] =  SITE_URL.'ajax?c=course&a=get-module-pdf&id='.$module->id;
                $data['moduleContents'][] = $pdf;

            }
        }
        return $data;
    }

    public function downloadContentZip()
    {
        $data = [
            'error' => true,
            'message' => 'Something went wrong!'
        ];
        $courseID = $_GET['courseID'];
        $downloadContent = $this->downloadContent($courseID);

        if(@$downloadContent['worksheets'] || @$downloadContent['moduleContents']){
            $course = ORM::for_table('courses')->find_one($courseID);
            $directory = TO_PATH_CDN.'courseContent/'.CUR_ID_FRONT;
            $directoryPath = $directory.'/'.$course->slug;
            if (!file_exists($directoryPath)) {
                mkdir($directoryPath, 0777, true);
            }

            if(count($downloadContent['moduleContents']) >= 1){
                foreach ($downloadContent['moduleContents'] as $module){
                    $module['title'] = str_replace('/', '-', $module['title']);
                    $pdfContent = file_get_contents(SITE_URL
                        .'ajax?c=course&a=module-pdf-contents&id='.$module['id']);

                    $mpdf = new Mpdf([
                        'tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf'
                    ]);
                    $mpdf->WriteHTML($pdfContent);
                    //ob_clean();
                    $fileName = $this->createSlug($module['title']).'.pdf';
                    $mpdf->Output($directoryPath.'/'.$fileName, 'F');
                }
            }
            if(count($downloadContent['worksheets']) >= 1){
                $directoryWorksheetPath = $directoryPath.'/worksheets';
                if (!file_exists($directoryWorksheetPath)) {
                    mkdir($directoryWorksheetPath, 0777, true);
                }
                foreach ($downloadContent['worksheets'] as $module){
                    $urlPdf     =   $module['url'];
                    $file_name  =   basename($urlPdf);
                    //save the file by using base name
                    file_put_contents( $directoryWorksheetPath . "/" . $file_name, file_get_contents($urlPdf));
                }
            }

            // Create Zip file
            // Get real path for our folder
            $rootPath = realpath($directoryPath);

            // Initialize archive object
            $zip = new ZipArchive();
            $directoryZipPath = $directory.'/'.$course->slug.'.zip';
            $zip->open($directoryZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            // Create recursive directory iterator
            /** @var SplFileInfo[] $files */
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file)
            {
                // Skip directories (they would be added automatically)
                if (!$file->isDir())
                {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);

                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
                }
            }

            // Zip archive will be created only after closing object
            $zip->close();
            $data = [
                'error' => false,
                'success' => true,
                'message' => 'Ready to download!'
            ];

            $files = glob($directoryPath . '/*');

            // Loop through the file list
            foreach($files as $file) {

                // Check for file
                if(is_file($file)) {

                    // Use unlink function to
                    // delete the file.
                    unlink($file);
                }
            }
            $this->removeFolder($directoryPath);
        }

        echo json_encode($data);
        exit();
    }
    public function getCoursesSelect(): array
    {
        $courses = ORM::for_table('courses')
            ->where_not_equal('hidden', 0)
            ->order_by_asc('title')
            ->find_array();
        return $courses;
    }
    public function getJobCentresSelect(): array
    {
        $jobcentres = ORM::for_table('jobCentres')
            ->where_equal('status', 1)
            ->order_by_asc('id')
            ->find_array();
        return $jobcentres;
    }

    public function learnAccess() {

        $fields = array(
            "firstname",
            "lastname",
            "email",
            "phone",
            "jobCenterID",
            "courseID",
        );

        //$this->validateValues($fields);

        $item = ORM::for_table('courseAccesses')->create();
        $item->firstName = $this->post['firstname'];
        $item->lastname = $this->post['lastname'];
        $item->email = $this->post['email'];
        $item->phone = $this->post['phone'];
        $item->jobCenterID = $this->post['jobCenterID'];
        $item->courseID = $this->post['courseID'];
        $item->courseID = $this->post['courseID'];
        $item->comment = $this->post['comment'];

        
        $item->save();

        // Email to user
        $emailTemplate
            = $this->emailTemplates->getTemplateByTitle('job_centre_request');
        if (@$emailTemplate->id) {
            $variables = [
                '[FIRSTNAME]' => $this->post["firstname"],
                '[LASTNAME]'  => $this->post["lastname"],
                '[EMAIL]'  => $this->post["email"],
                '[PHONE]'  => $this->post["phone"],
                '[JOB_CENTRE]'  => ORM::for_table('jobCentres')->find_one($this->post["jobCenterID"])->name,
                '[COURSE]'  => $this->post["courseID"],
                '[COMMENT]'  => $this->post["comment"],
            ];

            $message = $emailTemplate->description;
            $subject = $emailTemplate->subject;

            foreach ($variables as $k => $v) {
                $message = str_replace($k, $v, $message);
                $subject = str_replace($k, $v, $subject);
            }

            $this->sendEmail($emailTemplate->toEmail, $message, $subject);
        }


        echo $item->firstName . " " . $item->lastname;
        exit();

        // echo "<pre>";
        // print_r($item);
        // die;
    }
    public function getTotalCoursesByCategoryID($categoryID)
    {
        $filters = ['categoryIDs' => [$categoryID]];

        $courses = $this->getCoursesSql($filters);

        return $courses->count();
    }

    public function generateHrefLangTags($course) {

        $html = '';

        $related = ORM::for_table('courseRelations')
            ->where_any_is(array(
                array('courseID' => $course->id),
                array('courseID2' => $course->id)))
            ->find_one();

        if($related->id != "") {
            // relation exists

            // get the other course ID
            $relatedCourseID = $related->courseID2;
            if($relatedCourseID == $course->id) {
                $relatedCourseID = $related->courseID;
            }

            // get the related course data we need
            $relatedCourse = ORM::for_table("courses")
                ->select("slug")
                ->select("usImport")
                ->find_one($relatedCourseID);

            if($relatedCourse->usImport == "1") {

                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=USD" hreflang="en-us" />';
                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=EUR" hreflang="fr" />';
                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=EUR" hreflang="de" />';
                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=EUR" hreflang="nl" />';
                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=EUR" hreflang="es" />';
                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=AUD" hreflang="en-au" />';
                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=CAD" hreflang="ca" />';
                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=NZD" hreflang="nz" />';

            } else {

                // en removed from first line

                $html = '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=GBP" hreflang="en-gb" />';
                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=EUR" hreflang="fr" />';
                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=EUR" hreflang="de" />';
                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=EUR" hreflang="nl" />';
                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=EUR" hreflang="es" />';
                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=AUD" hreflang="en-au" />';
                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=CAD" hreflang="ca" />';
                $html .= '<link rel="alternate" href="'.SITE_URL.'course/'.$relatedCourse->slug.'?currency=NZD" hreflang="nz" />';

            }


        }

        return $html;

    }

    public function getCourseProviderImage(int $courseId, $size = 'full', $type = 'main_image')
    {
        $media = $this->medias->getMedia(courseProvidersController::class, $courseId,
            $type);

        if (@$media['url']) {
            $url = $media['url'];
            if ($size != 'full') {
                $url = str_replace($media['fileName'],
                    $size.'/'.$media['fileName'], $url);
            }
        } else {
            $url = '';
        }

        return $url;
    }

    public function assignSecondaryName()
    {
        $course = ORM::for_table("coursesAssigned")->find_one($this->post['assignedCourseID']);
        if($course) {
            $course->secondaryName = $this->post['name'];
            $course->save();
        }
    }


}
