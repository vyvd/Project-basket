<?php

require_once(__DIR__ . '/apiController.php');
//require_once('courseModuleController.php');
//require_once('mediaController.php');
//require_once('quizController.php');
//require_once('rewardsAssignedController.php');
//require_once('moosendController.php');
//require_once('emailTemplateController.php');
//require_once ('classes/Pagination/paginator.class.php');

//use Classes\Pagination\Paginator;
//use Dompdf\Dompdf;
//use Dompdf\Options;
//use Mpdf\Mpdf;

class coursesApiController extends apiController
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

        // call apiController's constructor
        parent::__construct();

        $this->get = $_GET;
        $this->post = $_POST;
        //$this->medias = new mediaController();
        //$this->quizzes = new quizController();
        //$this->courseModules = new courseModuleController();
        //$this->rewardsAssigned = new rewardsAssignedController();
        //$this->moosend = new moosendController();
        //$this->emailTemplates = new emailTemplateController();
        //$this->pages = new Classes\Pagination\Paginator();

    }


    //APIs

    //Get all courses
    public function index() {

    }

    //Get Single course by id
    public function getCourse($course_id){

        //echo $course_id;
        //die();

        $course = [];

        $course_query = ORM::for_table("courses")->find_one($course_id);

        if(!empty($course_query)) {
            $course = $course_query->as_array();
        }


        echo json_encode(array(
            'status' => 200,
            'data'   => $course
        ));

        die();

    }

    //Get Single course by course_title
    public function getCourseByTitle($course_title){

        //var_dump($course_title);


        $course = ORM::for_table("courses")->where('title', $course_title)->find_one()->as_array();


        echo json_encode(array(
            'status' => 200,
            'data'   => $course
        ));
        exit;

    }

    //Save/Store course
    public function store(){

    }

    //Update course
    public function update() {


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

            $custom_fields = array(
                'course_id'             => $module->courseID,
                'course_name'           => $course->title,
                'course_category'       => $imploded_cat_names,
                'course_progress'       => number_format($percentage),
                'user_first_name'       => $user->firstname,
                'user_last_name'        => $user->lastname,
                'user_id'               => $user->id,
                'quiz_score_percentage' => '100',
                'last_study_date'       => date('Y-m-d H:i:s')
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

    public function getNotes($moduleID)
    {

        return ORM::for_table("courseNotes")
            ->where("moduleID", $moduleID)
            ->where("userID", CUR_ID_FRONT)
            ->find_one();

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

    }

    public function getCoursesSearch()
    {

        // remove common words
        $this->get["search"] = str_replace("course", "", $this->get["search"]);
        $this->get["search"] = str_replace("courses", "", $this->get["search"]);

        $currency = $this->currentCurrency();

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
                .'%" OR additionalContent LIKE "%'.$searchText.'%")';
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
                            = '<small class="wasPriceSmall">was <s>'
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
                .'%" OR additionalContent LIKE "%'.$searchText.'%")';
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

        if (@$whereRaw) {
            $courses = $courses->where_raw(implode(" AND ", $whereRaw));
        }

        return $courses;
    }

    public function getUserCoursesCount($filter = null, $searchText = null)
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
            //$total = $total->where("coursesAssigned.completed", "1");
        } elseif ($filter == 'active') {
            $sql .= " and coursesAssigned.completed='0'";
            //$total = $total->where("coursesAssigned.completed", "0");
        }

        $account = ORM::for_table("accounts")->select("subActive")->find_one(CUR_ID_FRONT);

        if($account->subActive == "0" || $account->subActive == "2") {
            // if user doesnt have a subscription then do not show courses added during subscription
            $sql .= " and coursesAssigned.sub='0'";
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
        $searchText = null
    ) {

        $sql
            = "Select coursesAssigned.* from coursesAssigned join courses on courses.id=coursesAssigned.courseID where coursesAssigned.accountID='"
            .CUR_ID_FRONT."' and coursesAssigned.bundleID IS NULL";

        if (@$searchText) {
            $sql .= " and courses.title LIKE '%".$searchText."%'";
        }

        if ($filter == 'completed') {
            $sql .= " and coursesAssigned.completed='1'";
            //$total = $total->where("coursesAssigned.completed", "1");
        } elseif ($filter == 'active') {
            $sql .= " and coursesAssigned.completed='0'";
            //$total = $total->where("coursesAssigned.completed", "0");
        }

        $account = ORM::for_table("accounts")->select("subActive")->find_one(CUR_ID_FRONT);

        if($account->subActive == "0" || $account->subActive == "2") {
            // if user doesnt have a subscription then do not show courses added during subscription
            $sql .= " and coursesAssigned.sub='0'";
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

        $total = $this->getUserCoursesCount($filter, $searchText);


        if ($total >= 1) {
            $loadMore = ($offset + $limit) < $total ? 1 : 0;
            $items = $this->getUserCourses($offset, $limit, $order, $orderBy,
                $filter, $searchText);
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

            foreach ($children as $child) {

                if ($child->completed == "0") {
                    $completed = 0; // entire bundle is not complete
                }

            }

            if (count($children) > 0
                && $completed == 1
            ) { // if this enrollment IS a bundle and is complete
                // update bundle as complete

                $courseAssigned = ORM::for_table("coursesAssigned")
                    ->find_one($enrollment->id);

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

                $courseAssigned->save();

            }

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


    public function getTotalCoursesByCategoryID($categoryID)
    {
        $filters = ['categoryIDs' => [$categoryID]];

        $courses = $this->getCoursesSql($filters);

        return $courses->count();
    }


}
