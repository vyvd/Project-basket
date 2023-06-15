<?php

require_once(__DIR__ . '/courseController.php');
require_once(__DIR__ . '/courseCategoryController.php');
require_once(__DIR__ . '/courseModuleController.php');
require_once(__DIR__ . '/accountController.php');
require_once(__DIR__ . '/orderController.php');
require_once(__DIR__ . '/blogController.php');
require_once(__DIR__ . '/offerController.php');
require_once(__DIR__ . '/viralQuizController.php');
require_once(__DIR__ . '/voucherController.php');
require_once(__DIR__ . '/rewardsAssignedController.php');
require_once(__DIR__ . '/couponController.php');
require_once(__DIR__ . '/testimonialController.php');
require_once(__DIR__ . '/stripeController.php');
require_once(__DIR__ . '/subscriptionController.php');

class importController extends Controller
{

    /**
     * @var courseController
     */
    protected $courses;

    /**
     * @var courseModuleController
     */
    protected $courseModules;

    /**
     * @var mediaController
     */
    protected $medias;

    /**
     * @var accountController
     */
    protected $accounts;

    /**
     * @var orderController
     */
    protected $orders;

    /**
     * @var blogController
     */
    protected $blogs;

    /**
     * @var offerController
     */
    protected $offers;

    /**
     * @var viralQuizController
     */
    protected $viralQuizzes;

    /**
     * @var rewardsAssignedController
     */
    protected $rewardsAssignes;

    /**
     * @var couponController
     */
    protected $coupons;

    /**
     * @var testimonialController
     */
    protected $testimonials;

    /**
     * @var voucherController
     */
    protected $vouchers;

    /**
     * @var courseCategoryController
     */
    protected $courseCategories;

    /**
     * @var stripeController
     */
    protected $stripes;

    /**
     * @var subscriptionController
     */
    protected $subscriptions;

    public function __construct()
    {
        $this->courses = new courseController();
        $this->courseModules = new courseModuleController();
        $this->medias = new mediaController();
        $this->accounts = new accountController();
        $this->orders = new orderController();
        $this->blogs = new blogController();
        $this->offers = new offerController();
        $this->viralQuizzes = new viralQuizController();
        $this->rewardsAssignes = new rewardsAssignedController();
        $this->coupons = new couponController();
        $this->testimonials = new testimonialController();
        $this->vouchers = new voucherController();
        $this->courseCategories = new courseCategoryController();
        $this->stripes = new stripeController();
        $this->subscriptions = new subscriptionController();


        $this->post = $_POST;
        $this->get = $_GET;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;

        // if admin is not logged in then dont let them do anything
//        if (CUR_ID == "") {
//            echo json_encode(array(
//                'status'  => 402,
//                'message' => 'Invalid User!'
//            ));
//            exit;
//        }
    }

    /*
     * Various functions to import content from old site to the new site.
     * In most cases, we take SQL exports from the live site - add the tables to our current database, query data from there and reformat/reinsert into new tables
     *
     * Connect to live MySQL database to handle most of this, rather than exporting GB+ files, etc
     *
     *
     */

    public function helpArticles()
    {

        $olds = ORM::for_table("ncvpf_posts")
            ->where("post_type", "support_article")->find_many();
        // SELECT * FROM ncvpf_posts WHERE post_type = "support_article"

        foreach ($olds as $old) {

            $new = ORM::for_table("helpArticles")->create();

            $new->title = $old->post_title;
            $new->contents = $old->post_content;
            $new->slug = $old->post_name;
            $new->categoryID = "0";
            $new->whenAdded = $old->post_date;
            $new->whenUpdated = $old->post_modified;

            $new->save();
            // INSERT INTO helpArticles SET title = $old->post_title........

        }

        echo "Import Complete";

    }

    public function importTests()
    {

        // data is serialized. we unserialize by doing the following
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);


        // example of answer_data
        $data
            = 'a:4:{i:0;O:27:"WpProQuiz_Model_AnswerTypes":7:{s:10:"*_answer";s:36:"Dealing with the day to day running.";s:8:"*_html";b:1;s:10:"*_points";i:1;s:11:"*_correct";b:0;s:14:"*_sortString";s:0:"";s:18:"*_sortStringHtml";b:1;s:10:"*_mapper";N;}i:1;O:27:"WpProQuiz_Model_AnswerTypes":7:{s:10:"*_answer";s:28:"Dealing with ordering goods.";s:8:"*_html";b:1;s:10:"*_points";i:1;s:11:"*_correct";b:0;s:14:"*_sortString";s:0:"";s:18:"*_sortStringHtml";b:1;s:10:"*_mapper";N;}i:2;O:27:"WpProQuiz_Model_AnswerTypes":7:{s:10:"*_answer";s:24:"Hiring and firing staff.";s:8:"*_html";b:1;s:10:"*_points";i:1;s:11:"*_correct";b:0;s:14:"*_sortString";s:0:"";s:18:"*_sortStringHtml";b:1;s:10:"*_mapper";N;}i:3;O:27:"WpProQuiz_Model_AnswerTypes":7:{s:10:"*_answer";s:64:"Checking they are working to the standard expected by the hotel.";s:8:"*_html";b:1;s:10:"*_points";i:1;s:11:"*_correct";b:1;s:14:"*_sortString";s:0:"";s:18:"*_sortStringHtml";b:1;s:10:"*_mapper";N;}}';

        $data = preg_replace_callback(
            '!s:(\d+):"(.*?)";!',
            function ($m) {
                return 's:' . strlen($m[2]) . ':"' . $m[2] . '";';
            },
            $data);

        $data = unserialize($data);

        $json = json_encode($data);

        echo $json;

        $data = json_decode($json, true);

        $this->debug($data);
        foreach ($data as $item) {
            echo $item["*_answer"];
        }

    }

    public function user_accounts($user_id = null, $usImport = 0)
    {

        exit;

        if(isset($_GET['importFrom']) && ($_GET['importFrom'] == 'us')){
            $usImport = 1;
        }

        $importUrl = IMPORT_BASE_URL;
        if($usImport == 0){ // For UK
            $importUrl = IMPORT_BASE_UK_URL;
        }

        if ($_GET['account_id']) {
            $a = $this->accounts->getAccountByID($_GET['account_id']);
            if (@$a) {
                // get data from API
                $data = file_get_contents($importUrl. "get_users.php?user_id=" . $a->oldID."&email=" . $a->email);
                // JSON to PHP array
                $data = json_decode($data);
                $this->save_account($data->data, $usImport);
            }
        }else if (@$user_id) {
            // get data from API
            $data = file_get_contents($importUrl. "get_users.php?user_id=" . $user_id);
            // JSON to PHP array
            $data = json_decode($data);
            return $this->save_account($data->data, $usImport);
        }else if (@$_GET['user_old_id']) {
            // get data from API
            $data = file_get_contents($importUrl. "get_users.php?user_id=" . $_GET['user_old_id']);
            // JSON to PHP array
            $data = json_decode($data);
            return $this->save_account($data->data, $usImport);
        } else {
            /*
             * Before importing, create a file in /assets/cdn/importCounts called userAccounts.txt - and put a 0 inside
             *
             * This file will be used to increment the offset every time we request the API
             *
             * Run this URL on a cron job every minute. In this case, the following URL would be requested every 2 mins: https://www.whitelabelcreatives.com/cli/g/newskills/dev/ajax?c=import&a=user-accounts
             */

            $path = TO_PATH_CDN . 'importCounts/usa_accounts.txt';
            if($usImport == 0){
                $path = TO_PATH_CDN . 'importCounts/uk_accounts.txt';
            }

            if (file_exists($path) === false) {
                file_put_contents($path, 0);
            }
            // get the current offset from the file
            $offsetStart = file_get_contents($path);

            // get data from API
            $data = file_get_contents($importUrl. "get_users.php?number=100&offset=". $offsetStart);

            // JSON to PHP array
            $data = json_decode($data);

            // iterate through data
            foreach ($data->data->users as $item) {
                $this->save_account($item, $usImport);
                // write updated offset to file
                $offsetStart += 1;
                file_put_contents($path, $offsetStart);

            }
        }
        echo json_encode(array(
            'status'  => 200,
            'message' => 'Import Successfully!'
        ));
        exit;
    }

    protected function save_account($item, $usImport = 1)
    {
        exit;
        if(empty($item->id)){
            return null;
        }
        $data = [];
        $data['oldID'] = $item->id;
        $data['firstname'] = $item->first_name;
        $data['lastname'] = $item->last_name;
        $data['email'] = $item->email;
        $data['password'] = $item->password;
        $data['whenCreated'] = $item->when_created;
        $data['whenUpdated'] = $item->when_updated;
        $data['lastActive'] = $item->last_active;
        $data['usImport'] = $usImport ? '1' : '0';

        $currentAccount = ORM::for_table('accounts')
            ->where('oldId', $item->id)
            ->where('usImport', $usImport)
            ->find_one();
        if(empty($currentAccount)){
            $currentAccount = ORM::for_table('accounts')
                ->where('email', $item->email)
                ->find_one();
        }

        if (@$currentAccount->id) {
            $data['id'] = $currentAccount->id;
        }else{
            $data['forcePasswordReset'] = '1';
        }

        if (@$item->profile_image) {
            $data['wpImage']['full'] = $item->profile_image;
        }

        return $this->accounts->saveAccount($data);
    }

    public function mediaImportExample()
    {

        // Currently only in place for images

        // Import from an external URL
        $mediaID = $this->mediaUploadExternal(
            array("thumb", "medium", "large"),
            "https://newskillsacademy.co.uk/wp-content/uploads/2016/02/makeup-artist-diploma-course-331x166.jpg"
        );

        // OR

        // Upload a file
        $mediaID = $this->mediaUpload(
            array("thumb", "medium", "large"),
            "file" // POST name of field, defaults to "file" though so we do not necessarily need this here
        );

        // Original Size
        echo '<br />';
        echo $this->getMediaURL($mediaID);

        // Thumnail
        echo '<br />';
        echo $this->getMediaURL($mediaID, "thumb");

        // Medium
        echo '<br />';
        echo $this->getMediaURL($mediaID, "medium");

        // Large
        echo '<br />';
        echo $this->getMediaURL($mediaID, "large");

    }

    public function courseCategories()
    {

        /*
         * Run this URL on a cron job every minute. In this case, the following URL would be requested every 2 mins: ajax?c=import&a=course-accounts
         */

        // get data from API
        $data = file_get_contents(IMPORT_BASE_URL
            . "get_course_categories.php");

        // JSON to PHP array
        $data = json_decode($data);

        // iterate through data
        foreach ($data->data as $item) {

            $currentCategory = ORM::for_table('courseCategories')
                ->where('title' , $item->name)
                ->find_one();

            if (!isset($currentCategory->id)) {
                $currentCategory = ORM::for_table("courseCategories")->create();
            }

            $currentCategory->oldId = $item->id;
            $currentCategory->title = $item->name;
            $currentCategory->slug = $item->slug;
            $currentCategory->save();

        }

        echo "Import Successfully!";

    }


    public function courses($courseId = null, $courseOldId = null, $usImport = 1)
    {
        if(isset($_GET['importFrom']) && ($_GET['importFrom'] == 'us')){
            $usImport = 1;
        }

        $importUrl = IMPORT_BASE_URL;
        if($usImport == 0){ // For UK
            $importUrl = IMPORT_BASE_UK_URL;
        }

        $courseId = $_GET['course_id'] ?? $courseId;
        $courseOldId = $_GET['course_old_id'] ?? $courseOldId;

        if (@$courseId) {
            $c = $this->courses->getCourseByID($courseId);
            if (@$c->oldID) {
                // get data from API
                $data = file_get_contents($importUrl. "get_courses.php?course_id=" . $c->oldID);
                // JSON to PHP array
                $data = json_decode($data);
                $this->save_course($data->data, $usImport);
            }
        }else if (@$courseOldId) {
            $data = file_get_contents($importUrl. "get_courses.php?course_id=" . $courseOldId);
            // JSON to PHP array
            $data = json_decode($data);

            return $this->save_course($data->data, $usImport);
        } else {
            /*
             * Before importing, create a file in /assets/cdn/importCounts called courses.txt - and put a 0 inside
             *
             * This file will be used to increment the offset every time we request the API
             *
             * Run this URL on a cron job every minute. In this case, the following URL would be requested every 2 mins: ajax?c=import&a=courses
             */

            $path = TO_PATH_CDN . 'importCounts/usa_courses.txt';

            if($usImport == 0) { // For UK
                $path = TO_PATH_CDN . 'importCounts/uk_courses.txt';
            }

            if (file_exists($path) === false) {
                file_put_contents($path, 0);
            }

            // get the current offset from the file
            $offsetStart = file_get_contents($path);

            // get data from API
            $data = file_get_contents($importUrl. "get_courses.php?number=10&offset=" . $offsetStart);

            // JSON to PHP array
            $data = json_decode($data);

            // iterate through data
            if (@$data->data->courses) {
                foreach ($data->data->courses as $item) {
                    $this->save_course($item, $usImport);
                    // write updated offset to file
                    $offsetStart += 1;
                    file_put_contents($path, $offsetStart);
                }
            }
        }

        echo "Import Successfully Courses!";

    }

    public function nsfa_courses()
    {
        if (@$_GET['course_id']) {
            $c = $this->courses->getCourseByID($_GET['course_id']);
            if (@$c->oldID) {
                // get data from API
                $url = NSFA_API_URL . "getCourses/".$c->oldID;
                $postRequest = [];
                $cURLConnection = curl_init($url);

                curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $postRequest);
                curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                $apiResponse = curl_exec($cURLConnection);
                curl_close($cURLConnection);

                // JSON to PHP array
                $data = json_decode($apiResponse);

                $this->save_course($data->courses);
            }
        }else if (@$_GET['course_old_id']) {
            $data = file_get_contents(IMPORT_BASE_URL
                . "get_courses.php?course_id=" . $_GET['course_old_id']);
            // JSON to PHP array
            $data = json_decode($data);
            $this->save_course($data->data);
        } else {
            /*
             * Before importing, create a file in /assets/cdn/importCounts called courses.txt - and put a 0 inside
             *
             * This file will be used to increment the offset every time we request the API
             *
             * Run this URL on a cron job every minute. In this case, the following URL would be requested every 2 mins: ajax?c=import&a=courses
             */

            $path = TO_PATH_CDN . 'importCounts/nsfa_courses.txt';
            if (file_exists($path) === false) {
                file_put_contents(TO_PATH_CDN . 'importCounts/nsfa_courses.txt', 0);
            }

            // get the current offset from the file
            $offsetStart = file_get_contents($path);

            $url = NSFA_API_URL . "getCourses";

            $postRequest = [];

            if(@$offsetStart){
                $postRequest['offsetStart'] = $offsetStart;
            }

            $cURLConnection = curl_init($url);

            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $postRequest);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

            $apiResponse = curl_exec($cURLConnection);
            curl_close($cURLConnection);

            // JSON to PHP array
            $data = json_decode($apiResponse);

            // iterate through data
            if (@$data->courses) {
                foreach ($data->courses as $item) {

                    $this->save_course($item);
                    // write updated offset to file
                    $offsetStart += 1;
                    file_put_contents(TO_PATH_CDN . 'importCounts/nsfa_courses.txt', $offsetStart);

                }
            }
        }

        echo "Import Successfully!";

    }

    protected function save_course($item, $usImport = 1)
    {

        $data = [];
        $data['oldID'] = $item->id;
        $data['title'] = $this->filter_content($item->title);
        $data['slug'] = $item->slug;
        $data['price'] = $item->price->number ?? 0.00;
        $data['duration'] = $item->duration ? $item->duration * 60 : 0;
        $data['location'] = $item->learning_type->name ?? 'Online';
//
        $data['whenAdded'] = $item->created_at;
        $data['whenUpdated'] = $item->updated_at;
        $data['usImport'] = $usImport ? '1' : '0';
        $data['hidden'] = '1';
//        $data['categories'] = null;
//
        if (@$item->image) {
            $data['wpImage']['full'] = $item->image->full ?? $item->image;
            if (@$item->image->thumb) {
                $data['wpImage']['thumb'] = $item->image->thumb;
            }
            if (@$item->image->medium) {
                $data['wpImage']['medium'] = $item->image->medium;
            }
            if (@$item->image->large) {
                $data['wpImage']['large'] = $item->image->large;
            }
        }
        
//        if (@$item->category->id) {
//            $data['categories'][] = $this->courseCategories->getCategoryByTitle($item->category->name)->id;
//        }

        if (@$item->categories) {
            foreach ($item->categories as $category) {
                $data['categories'][]
                    = $this->courses->getCategoryByOldId($category)->id;
            }
        }
//
        // layout
        if (@$item->layout && $item->layout == 'new_style_with_video') {
            $data['layout'] = 1;
        }
        if (@$item->expire_after_year && $item->expire_after_year == 'yes') {
            $data['expire_after_year'] = 1;
        }
        if (@$item->have_tests && $item->have_tests == 'yes') {
            $data['have_tests'] = 1;
        }
        if (@$item->show_oldname && $item->show_oldname == 'yes') {
            $data['show_oldname'] = 1;
        }
        if (@$item->cma) {
            $data['is_cma'] = 1;
        }
        if (@$item->lightning_skill) {
            $data['is_lightningSkill'] = 1;
        }

        if (@$item->course_certificate_color) {
            $data['certificate_color'] = $item->course_certificate_color;
        }

        if (@$item->course_cpd_code) {
            $data['cpd_code'] = $item->course_cpd_code;
        }

        if (@$item->qualification) {
            $data['qualification'] = $item->qualification;
        }
//        if (@$item->upsellCourse) {
//            $offerCourse = $this->courses->getCourseByOldID($item->upsellCourse, true);
//            if (@$offerCourse->id) {
//                $data['upsellCourse'] = $offerCourse->id;
//            }else{
//                $newCourseData = file_get_contents(IMPORT_BASE_URL
//                    . "get_courses.php?course_id=" . $item->upsellCourse);
//                // JSON to PHP array
//                $newCourseData = json_decode($newCourseData);
//                $this->save_course($newCourseData->data);
//            }
//        }
        if (@$item->upsellCoursePrice) {
            $data['upsellCoursePrice'] = $item->upsellCoursePrice;
        }

        if (@$item->product_id) {
            $data['productID'] = $item->product_id;
        }

//        if (@$item->mega_courses) {
//            foreach ($item->mega_courses as $mc) {
//                $mcid = $this->courses->getCourseByOldID($mc);
//                if (@$mcid->id) {
//                    $data['mega_courses'][] = $mcid->id;
//                } else {
//                    $newCourseData = file_get_contents(IMPORT_BASE_URL
//                        . "get_courses.php?course_id=" . $mc);
//                    // JSON to PHP array
//                    $newCourseData = json_decode($newCourseData);
//                    $this->save_course($newCourseData->data);
//                }
//            }
//        }

        if (@$item->meta->_course_special_offer[0]) {
            $data['reviews'] = $this->filter_content($item->meta->_course_special_offer[0]);
        }

        if (@$item->meta->_course_final_quiz[0] && ($item->meta->_course_final_quiz[0] == 'Yes')) {
            $data['finalQuizOnly'] = '1';
        }

        if (@$item->meta->_course_amazon_products[0]) {
            $amazonProducts = str_replace(array("\n", "\r"), ',', $item->meta->_course_amazon_products[0]);
            $data['amazonProducts'] = str_replace(',,',',', $amazonProducts);
        }

        $currentCourse = ORM::for_table('courses')
            ->where('oldID', $item->id)
            ->where('usImport', $data['usImport'])
            ->find_one();

        if (@$currentCourse->id) {
            $data['id'] = $currentCourse->id;
        }else{
            $data['description'] = $this->filter_content($item->description);
            $data['additionalContent'] = $this->filter_content($item->course_additional_content);
        }


        $c = $this->courses->saveCourse($data);

//        if(@$item->course_modules){
//            foreach ($item->course_modules as $module) {
//                $m = $this->save_nsfa_module($module, $c->id);
//                if(@$module->lessons){
//                    foreach ($module->lessons as  $lesson) {
//                        $lesson->name = $lesson->title;
//                        $lesson->slug = @$lesson->slug ? $lesson->slug : $this->createSlug($lesson->title);
//                        $lesson->sort_order = $lesson->position;
//                        $lesson->contents = preg_replace('/\>\s+\</m', '><', $lesson->full_text);
//                        $lesson->contentType = $lesson->content_type;
//
//                        if($lesson->content_type == 'quiz') {
//                            // for quiz
//                            $lesson->quiz->id = $lesson->id;
//                            $lesson->quiz->passing_percentage = $lesson->quiz_pass_percentage;
//                            $lesson->quiz->timeLimit = $lesson->quiz_time;
//                            $lesson->quiz->show_max_question = @$lesson->questions_display ? 1 : 0;
//                            $lesson->quiz->show_max_question_value = $lesson->questions_display;
//                            if(@$lesson->course_questions){
//                                $quizQuestions = [];
//                                $ij = 1;
//                                foreach ($lesson->course_questions as $question) {
//                                    $quizQuestion['id'] = $question->id;
//                                    $quizQuestion['title'] = $question->question;
//                                    $quizQuestion['sort'] = $ij;
//                                    $quizQuestion['answerType'] = @$question->multiple ? 'multiple' : 'single';
//                                    if($question->quiz_options){
//                                        $jk = 0;
//                                        foreach ($question->quiz_options as $option) {
//                                            $quizOption = [];
//                                            $quizOption['answer'] = $option->name;
//                                            $quizOption['isCorrect'] = $option->answer;
//                                            $quizQuestion['answers'][$jk] = $quizOption;
//                                            $jk++;
//                                        }
//                                    }
//                                    $lesson->quiz->questions[] = $quizQuestion;
//                                    $ij++;
//                                }
//                            }
//
//                        }
//                        elseif($lesson->content_type == 'assignment') {
//                            if(@$lesson->media_files){
//                                $i = 0;
//                                foreach ($lesson->media_files as $media) {
//                                    if(empty($media->model_id)){
//                                        $lesson->assignments[$i]['title'] = $media->name;
//                                        $lesson->assignments[$i]['fileName'] = $media->file_name;
//                                        $lesson->assignments[$i]['url'] = $media->url;
//                                        $i++;
//                                    }
//                                }
//                            }
//                        }
//                        elseif($lesson->content_type == 'upload') {
//                            if(@$lesson->media_files){
//                                $i = 0;
//                                foreach ($lesson->media_files as $media) {
//                                    $lesson->uploads[$i]['title'] = $media->name;
//                                    $lesson->uploads[$i]['fileName'] = $media->file_name;
//                                    $lesson->uploads[$i]['url'] = $media->url;
//                                    $i++;
//                                }
//                            }
//                        }
//                        elseif($lesson->content_type == 'video' && $lesson->vimeo) {
//                            $lesson->embed_video = 'https://vimeo.com/' . $lesson->vimeo;
//                        }
//
//
//                        $subModule = $this->save_nsfa_module($lesson, $c->id, $m->id);
//                    }
//                }
//            }
//        }

        $this->course_modules($c->id, $usImport);
        $this->course_blogs($c->id, $usImport);
        return $c;
    }

    public function course_modules($courseID = null, $usImport = 1)
    {
        $courseID = isset($_GET['course_id']) ? $_GET['course_id'] : $courseID;
        $course = $this->courses->getCourseByID($courseID);



        if (@$course['oldID']) {
            $importUrl = IMPORT_BASE_URL;

            if($usImport == 0) {
                $importUrl = IMPORT_BASE_UK_URL;
            }


            // get data from API
            $data = file_get_contents($importUrl
                . "get_course_lessons.php?course_id=" . $course['oldID']);

            // JSON to PHP array
            $data = json_decode($data);
            if (@$data->data->lessons) {
                foreach ($data->data->lessons as $item) {
                    $this->save_lesson($item, $courseID, $usImport);
                }
            }
            echo "Import Successfully!";
        } else {
            echo "Course Not Found!";
        }


    }
    public function save_nsfa_module($item, $courseID, $parentID = null)
    {
        $data = [];
        $data['oldID'] = $item->id;
        $data['isNCFE'] = '1';
        $data['courseID'] = $courseID;
        if($parentID) {
            $data['parentID'] = $parentID;
        }
        $data['title'] = $this->filter_content($item->name);
        $data['slug'] = $this->createSlug($data['title']);
        $data['ord'] = $item->sort_order;
        $data['contentType'] = $item->contentType ?? null;

        //$data['feature_image'] = $item->featured_image ?? null;
        $data['description'] = $item->info ? $this->filter_content($item->info) : null;
        $data['estTime'] = is_numeric($item->time) ? $item->time : null;
        $data['contents'] = $item->contents ? $this->filter_content($this->replaceImagePath($item->contents, 'newskillsfitnessacademy.co.uk')) : null;
        //$data['contents'] = null;

//        if (@$item->list_1 || @$item->list_2 || @$item->list_3 || @$item->list_4
//        ) {
//            $data['description'] = '<ul>';
//            if (@$item->list_1) {
//                $data['description'] .= '<li>' . $item->list_1 . '</li>';
//            }
//            if (@$item->list_2) {
//                $data['description'] .= '<li>' . $item->list_2 . '</li>';
//            }
//            if (@$item->list_3) {
//                $data['description'] .= '<li>' . $item->list_3 . '</li>';
//            }
//            if (@$item->list_4) {
//                $data['description'] .= '<li>' . $item->list_4 . '</li>';
//            }
//            $data['description'] .= '</ul>';
//        }
//
//        $data['new_style_with_video'] = isset($item->new_style_with_video) ? $item->new_style_with_video : 0;
//        $data['video_source'] = isset($item->video_source) ? $item->video_source : null;
        if (@$item->embed_video) {
            $data['embed_video'] = $item->embed_video;
        }
//        $data['has_optional_section'] = isset($item->has_optional_section) ? $item->has_optional_section : 0;
//        $data['worksheet_title'] = isset($item->worksheet_title) ? $item->worksheet_title : null;
//        $data['worksheet_text'] = isset($item->worksheet_text) ? $item->worksheet_text : null;
//        $data['worksheet_estimate_time'] = isset($item->worksheet_estimate_time) ? $item->worksheet_estimate_time : null;
//        if (@$item->worksheet_pdf_file) {
//            $data['worksheet_pdf_file'] = $item->worksheet_pdf_file;
//        }
        $currentCourseModule = ORM::for_table('courseModules')
            ->where('oldID', $item->id)
            ->where('courseID', $courseID)
            ->where('isNCFE', '1')
            ->find_one();

        if (@$currentCourseModule->id) {
            $data['id'] = $currentCourseModule->id;
        }


        // for quiz
        if (@$item->quiz->id) {
            $data['quiz'] = [
                'oldID'              => $item->quiz->id,
                'courseID'           => $courseID,
                'isNCFE'             => '1',
                'passingPercentage'  => $item->quiz->passing_percentage,
                'maxQuestion'        => @$item->quiz->show_max_question ? 1 : 0,
                'maxQuestionValue'   => $item->quiz->show_max_question_value ?? null,
                'maxQuestionPercent' => @$item->quiz->show_max_question_percent ? 1 : 0,
                'appear'             => 'm',
                'timeLimit'          => $item->quiz->timeLimit ?? 0,
            ];

            $currentQuiz = ORM::for_table('quizzes')
                ->where('OldId', $item->quiz->id)
                ->where('isNCFE', '1')
                ->find_one();

            if (@$currentQuiz->id) {
                $data['quiz']['id'] = $currentQuiz->id;
            }

            if (@$item->quiz->questions) {
                $data['quiz']['questions'] = [];
                $i = 0;
                foreach ($item->quiz->questions as $question) {
                    $data['quiz']['questions'][$i] = [
                        'oldID'      => $question['id'],
                        'question'   => $question['title'],
                        'ord'        => $question['sort'],
                        'answerType' => $question['answerType'] ?? 'single',
                        'answerData' => null,
                        'answers' => $question['answers']
                    ];
                    $i++;
                }
            }
        }

        if(@$item->assignments) {
            $data['assignments'] = $item->assignments;
        }

        if(@$item->uploads) {
            $data['uploads'] = $item->uploads;
        }

        return $this->courseModules->saveCourseModule($data);
    }
    public function save_lesson($item, $courseID, $usImport = 1)
    {

        $data = [];
        $data['oldID'] = $item->id;
        $data['courseID'] = $courseID;
        $data['title'] = $this->filter_content($item->title);
        $data['slug'] = $item->slug;
        $data['ord'] = $item->order;
        $data['feature_image'] = $item->featured_image ?? null;
        $data['description'] = null;
        $data['estTime'] = is_numeric($item->duration) ? $item->duration : null;
        $data['usImport'] = $usImport ? '1' : '0';
        $data['contents'] = $this->filter_content($item->contents);
        //$data['contents'] = null;

        if (@$item->list_1 || @$item->list_2 || @$item->list_3 || @$item->list_4
        ) {
            $data['description'] = '<ul>';
            if (@$item->list_1) {
                $data['description'] .= '<li>' . $item->list_1 . '</li>';
            }
            if (@$item->list_2) {
                $data['description'] .= '<li>' . $item->list_2 . '</li>';
            }
            if (@$item->list_3) {
                $data['description'] .= '<li>' . $item->list_3 . '</li>';
            }
            if (@$item->list_4) {
                $data['description'] .= '<li>' . $item->list_4 . '</li>';
            }
            $data['description'] .= '</ul>';
        }

        $data['new_style_with_video'] = isset($item->new_style_with_video) ? $item->new_style_with_video : 0;
        $data['video_source'] = isset($item->video_source) ? $item->video_source : null;
        if (@$item->embed_video) {
            $data['embed_video'] = $item->embed_video;
        }
        $data['has_optional_section'] = isset($item->has_optional_section) ? $item->has_optional_section : 0;
        $data['worksheet_title'] = isset($item->worksheet_title) ? $item->worksheet_title : null;
        $data['worksheet_text'] = isset($item->worksheet_text) ? $item->worksheet_text : null;
        $data['worksheet_estimate_time'] = isset($item->worksheet_estimate_time) ? $item->worksheet_estimate_time : null;
        if (@$item->worksheet_pdf_file) {
            $data['worksheet_pdf_file'] = $item->worksheet_pdf_file;
        }
        $currentCourseModule = ORM::for_table('courseModules')
            ->where('oldID', $item->id)
            ->where('courseID', $courseID)
            ->find_one();

        if (@$currentCourseModule->id) {
            $data['id'] = $currentCourseModule->id;
        }


        // for quiz
        if (@$item->quiz->id) {
            $data['quiz'] = [
                'oldID'              => $item->quiz->id,
                'courseID'           => $courseID,
                'passingPercentage'  => $item->quiz->passing_percentage,
                'maxQuestion'        => @$item->quiz->show_max_question ? 1 : 0,
                'maxQuestionValue'   => $item->quiz->show_max_question_value ?? null,
                'maxQuestionPercent' => @$item->quiz->show_max_question_percent ? 1 : 0,
                'appear'             => 'a',
                'usImport'           => $data['usImport'],
            ];

            $currentQuiz = ORM::for_table('quizzes')
                ->where('oldID', $item->quiz->id)
                ->where('usImport', $data['usImport'])
                ->find_one();

            if (@$currentQuiz->id) {
                $data['quiz']['id'] = $currentQuiz->id;
            }

            if (@$item->quiz->questions) {
                $data['quiz']['questions'] = [];
                $i = 0;
                foreach ($item->quiz->questions as $question) {
                    $data['quiz']['questions'][$i] = [
                        'oldID'      => $question->id,
                        'question'   => $question->title,
                        'ord'        => $question->sort,
                        'answerType' => 'single',
                        'answerData' => $question->answers,
                    ];

//                    if (@$question->answers) {
//                        $answerData = array();
//                        foreach ($question->answers as $ans) {
//                            $answer = array();
//                            $answer["*_answer"] = $ans->title;
//                            $answer["*_correct"] = $ans->correct === true ? 1 : null;
//                            array_push($answerData, $answer);
//                        }
//                        $data['quiz']['questions'][$i]['answerData'] = $question->answers;
//                    }

                    $i++;
                }
            }
        }

        return $this->courseModules->saveCourseModule($data);
    }
    public function course_blogs($courseID = null)
    {
        $courseID = isset($_GET['course_id']) ? $_GET['course_id'] : $courseID;
        $course = $this->courses->getCourseByID($courseID);

        if (@$course['oldID']) {
            // get data from API
            $data = file_get_contents(IMPORT_BASE_URL
                . "get_course_blogs.php?course_id=" . $course['oldID']);

            // JSON to PHP array
            $data = json_decode($data);

            if (@$data->data->posts) {
                foreach ($data->data->posts as $item) {
                    $this->save_blog($item);
                }
            }
            echo "Import Successfully!";
        } else {
            echo "Course Not Found!";
        }


    }

    public function orders()
    {
        if ($_GET['order_id']) {
            $o = $this->orders->getOrderByID($_GET['order_id']);

            if (@$o->oldID) {
                // get data from API
                $data = file_get_contents(IMPORT_BASE_URL
                    . "get_orders.php?order_id=" . $o->oldID);
                // JSON to PHP array
                $data = json_decode($data);


                $this->save_order($data->data, 1);
            }
        } else {
            /*
             * Before importing, create a file in /assets/cdn/importCounts called orders.txt - and put a 0 inside
             *
             * This file will be used to increment the offset every time we request the API
             *
             * Run this URL on a cron job every minute. In this case, the following URL would be requested every 2 mins: https://www.whitelabelcreatives.com/cli/g/newskills/dev/ajax?c=import&a=user-accounts
             */

            // get the current offset from the file
            $path = TO_PATH_CDN . 'importCounts/us_orders.txt';
            if (file_exists($path) === false) {
                file_put_contents($path, 0);
            }
            $offset = file_get_contents($path);

            // get data from API
            $data = file_get_contents(IMPORT_BASE_URL . "get_orders.php?number=500&offset="
                . $offset);

            // JSON to PHP array
            $data = json_decode($data);

            // iterate through data
            foreach ($data->data->orders as $item) {
                $order = $this->save_order($item, 1);

                // write updated offset to file
                $offset += 1;
                file_put_contents($path, $offset);
            }
        }
        echo "Orders Imported";
        exit();
    }

    protected function save_order($item, $usImport = 1)
    {
        $data = [];
        $data['oldID'] = $item->id;
        $data['total'] = $item->total;
        $data['email'] = $item->billing->email;
        $data['firstname'] = $item->billing->first_name;
        $data['lastname'] = $item->billing->last_name;
        $data['customerIP'] = $item->customer_ip;
        $data['whenCreated'] = $item->when_created;
        $data['whenUpdated'] = $item->when_updated;
        $data['status'] = $item->status;
        $data['accountID'] = null;
        $data['method'] = $item->method;
        $data['method_title'] = $item->method_title;
        $data['usImport'] = $usImport ? '1' : '0';
        $currentUser = $this->accounts->getAccountByOldID($item->user_id, $data['usImport']);

        if(empty($currentUser)){
            $currentUser = $this->accounts->getAccountByEmail($item->customer_email);
        }

        if (@$currentUser->id) {
            $data['accountID'] = $currentUser->id;
        }elseif($item->user_id >= 1) {
            $currentUser = $this->user_accounts($item->user_id, $usImport);
            if(@$currentUser->id){
                $data['accountID'] = $currentUser->id;
            }
        }

        $currentOrder = $this->orders->getOrderByOldID($item->id, $data['usImport']);

        if(empty($currentOrder) && $data['accountID']){
            $currentOrder = ORM::for_table('orders')
                ->where('oldID', $item->id)
                ->where('accountID', $data['accountID'])
                ->find_one();
        }

        if (@$currentOrder->id) {
            $data['id'] = $currentOrder->id;

//            if($usImport == 0){
//                ORM::for_table('orderItems')
//                    ->where('orderID', $data['id'])
//                    ->where('usImport', '0')
//                    ->delete_many();
//            }
        }


        // Items
        if (@$item->items) {
            $i = 0;
            foreach ($item->items as $orderItem) {
                // Check existing course
                $currentCourse = ORM::for_table('courses')
                    ->where('oldID', $orderItem->course_id)
                    ->where('usImport', $data['usImport'])
                    ->find_one();

                if(@$currentCourse->id){
                    $data['items'][$i]['courseID'] = $currentCourse->id;
                }else{
                    $currentCourse = $this->courses(null, $orderItem->course_id);
                    if(@$currentCourse->id){
                        $data['items'][$i]['courseID'] = $currentCourse->id;
                    }
                }

                $data['items'][$i]['oldID'] = $orderItem->id;

                $data['items'][$i]['price'] = $orderItem->total;
                $data['items'][$i]['qty'] = $orderItem->quantity;
                $data['items'][$i]['usImport'] = $data['usImport'];

                $currentOrderItem = $this->orders->getOrderItemByOldID($orderItem->id, $data['usImport']);

                if (@$currentOrderItem->id) {
                    $data['items'][$i]['id'] = $currentOrderItem->id;
                }
                $i++;
            }
        }

        return $this->orders->saveOrder($data);
    }

    // User's Orders and Enrollments
    public function user_enrollments()
    {
        $usImport = 0;
        if(isset($_GET['importFrom']) && $_GET['importFrom'] == 'us'){
            $usImport = 1;
        }

        if ($_GET['account_id']) {
            $a = $this->accounts->getAccountByID($_GET['account_id']);

            if (@$a->id) {
                $this->save_user_enrollments($a, null, $usImport);
                if($a->rewardImported == 0){
                    $this->userRewards($a->oldID, $a->id);
                }
                echo json_encode(array(
                    'status'  => 200,
                    'message' => 'Import Successfully!'
                ));
                exit;
            }
        } else {

            $path = TO_PATH_CDN . 'importCounts/usUserEnrollments.txt';

            if (file_exists($path) === false) {
                file_put_contents($path, 0);
            }

            $offset = file_get_contents($path);
            $data['usImport'] = @$usImport ? '1' : '0';

            $accounts = ORM::for_table('accounts')
                ->where('dataImported', 0)
                ->where('usImport', $data['usImport'])
                ->where_not_null('oldID')
                ->order_by_desc('oldID')
                ->limit(50)
                ->find_many();

            if (count($accounts) >= 1) {
                foreach ($accounts as $account) {

                    $this->save_user_enrollments($account, null, $usImport);
                    if($account->rewardImported == 0){
                        $this->userRewards($account->oldID, $account->id);
                        $account->rewardImported = 1;
                    }
                    // write updated offset to file
                    $offset += 1;
                    file_put_contents($path, $offset);
                    $account->dataImported = 1;
                    $account->save();
                }
            }
            echo json_encode(array(
                'status'  => 200,
                'message' => 'Import Successfully!'
            ));
            exit;
        }

        echo json_encode(array(
            'status'  => 404,
            'message' => 'Something went wrong!'
        ));
        exit;
    }

    protected function save_user_enrollments($account, $course = null, $usImport = 1)
    {
        $importUrl = IMPORT_BASE_URL;

        if($usImport == 0){
            $importUrl = IMPORT_BASE_UK_URL;
        }

        $userId = $account->oldID;
        $getOrdersUrl = $importUrl."get_orders.php?user_id=". $userId."&email=". $account->email;


        // Start Get Orders
        // get data from API
        $data = file_get_contents($getOrdersUrl);

        // JSON to PHP array
        $data = json_decode($data);

        // iterate through data
        foreach ($data->data->orders as $item) {
            $this->save_order($item, $usImport);
        }
        // End Get Orders


        // Start Get Enrollments
        // get data from API
        $getUserCoursesUrl = $importUrl. "get_user_courses.php?user_id=" . $userId."&email=". $account->email;

        if(@$course->id){
            $data = file_get_contents($getUserCoursesUrl. "&course_id=".$course->oldID);

            // JSON to PHP array
            $data = json_decode($data);
            if(@$data->data->user_id){
                $this->save_enrollment($data->data, $account, $usImport);
            }
        }else{
            $data = file_get_contents($getUserCoursesUrl);

            // JSON to PHP array
            $data = json_decode($data);

            // iterate through data
            foreach ($data->data->user_courses as $item) {
                $this->save_enrollment($item, $account, $usImport);
            }
            $account->dataImported = 1;
            $account->save();
        }
        // End Get Enrollments

    }

    public function save_enrollment($item, $account, $usImport = 1){
        $data['usImport'] = @$usImport ? '1' : '0';
        $course = $this->courses->getCourseByOldID($item->course_id, $data['usImport']);

        if (@$course->id) {
            $data = [
                'accountID'     => $account->id,
                'courseID'      => $course->id,
                'currentModule' => null,
                'whenAssigned'  => $item->when_enrolled,
                'completed'     => (isset($item->cert_no) && @$item->cert_no) ? '1' : '0',
                'certNo'        => isset($item->cert_no) ? $item->cert_no : null,
                'whenCompleted' => $item->when_completed,
                'percComplete'  => @$item->percentage_complete ? $item->percentage_complete : 0,
            ];

            // check already assigned course
            $assigned = ORM::for_table('coursesAssigned')
                ->where("accountID", $account->id)
                ->where("courseID", $course->id)
                ->find_one();


            if (@$assigned->id) {
                $data['id'] = $assigned->id;
            }

            // check current lesson
            if (@$item->current_lesson_id) {
                $currentModule = $this->courseModules->getCourseModuleByOldID($item->current_lesson_id, $course->id);
                if (empty($assigned) || (@$assigned && $assigned->currentModuleKey <= $currentModule->ord)){
                    $data['currentModule'] = $currentModule->id;
                    $data['currentModuleKey'] = $currentModule->ord;
                }
            }

            if($data['completed'] == 1){
                $lastCourseModule =  $this->courseModules->getLastCourseModule($course->id);
                if(@$lastCourseModule->id){
                    $data['currentModule'] = $lastCourseModule->id;
                    $data['currentModuleKey'] = $lastCourseModule->ord;
                }
            }

            // Import Course Progress
            if(@$item->lesson_progress){
                foreach ($item->lesson_progress as $lessonProgress) {
                    $courseModule = ORM::for_table('courseModules')
                        ->where('courseID', $course->id)
                        ->where('oldID', $lessonProgress->lesson_id)
                        ->find_one();
//                    echo "<pre>";
//                    print_r($courseModule);
//                    die;
                    $courseProgress = [
                        'accountID' => $account->id,
                        'courseID' => $course->id,
                        'moduleID' => $courseModule->id,
                        'completed' => $lessonProgress->module_skip == 0 ? 1 : 0,
                        'whenStarted' => @$lessonProgress->date_created ? $lessonProgress->date_created : date("Y-m-d H:i:s")
                    ];
                    if($lessonProgress->module_skip == 0){
                        $courseProgress['completed'] = 1;
                        $courseProgress['whenCompleted'] = $courseProgress['whenStarted'];
                    }
                    $item = ORM::for_table("courseModuleProgress")
                        ->where(array(
                                'accountID' => $account->id,
                                'courseID'  => $course->id,
                                'moduleID'  => $courseModule->id
                            )
                        )
                        ->find_one();
                    if(empty($item)){
                        $this->courseModules->saveCourseModuleProgress($courseProgress);
                    }
                }
            }

            return $this->courses->saveCourseAssigned($data);
        }
    }

    // User's Course Notes
    public function user_notes()
    {
        if ($_GET['account_id']) {
            $a = $this->accounts->getAccountByID($_GET['account_id']);
            if (@$a->oldID) {
                $this->save_user_notes($a);
                echo json_encode(array(
                    'status'  => 200,
                    'message' => 'Import Successfully!'
                ));
                exit;
            }
        } else {

            $path = TO_PATH_CDN . 'importCounts/usa_userNotes.txt';

            if (file_exists($path) === false) {
                file_put_contents($path, 0);
            }

            $offset = file_get_contents($path);

            $accounts = ORM::for_table('accounts')
                ->where('usImport', '1')
                ->where_not_null('oldID')
                ->order_by_asc('oldID')
                ->limit(100)
                ->offset($offset)
                ->find_many();

            if (count($accounts) >= 1) {
                foreach ($accounts as $account) {

                    $this->save_user_notes($account);

                    // write updated offset to file
                    $offset += 1;
                    file_put_contents($path, $offset);
                }
            }
            echo json_encode(array(
                'status'  => 200,
                'message' => 'Import Successfully!'
            ));
            exit;
        }

        echo json_encode(array(
            'status'  => 404,
            'message' => 'Something went wrong!'
        ));
        exit;
    }

    protected function save_user_notes($account)
    {
        $userId = $account->oldID;

        // Start Get Notes
        // get data from API
        $data = file_get_contents(IMPORT_BASE_URL
            . "get_user_notes.php?user_id=" . $userId);

        // JSON to PHP array
        $data = json_decode($data);


        // iterate through data
        foreach ($data->data->posts as $item) {

            $courseModule
                = $this->courseModules->getCourseModuleByOldID($item->lesson_id);

            if (@$courseModule->id) {
                $data = [
                    'oldID'       => $item->id,
                    'courseID'    => $courseModule->courseID,
                    'moduleID'    => $courseModule->id,
                    'userID'      => $account->id,
                    'notes'       => $item->description,
                    'whenAdded'   => $item->when_created,
                    'whenUpdated' => $item->when_updated,
                ];

                // check already assigned course
                $notes = ORM::for_table('courseNotes')
                    ->where("oldID", $item->id)
                    ->find_one();
                if (@$notes->id) {
                    $data['id'] = $notes->id;
                }


               // $this->accounts->saveCourseNote($data);
            }
        }
        // End Get Notes

    }

    public function xo_user_data()
    {
        $account = ORM::forTable('accounts')->findOne(CUR_ID_FRONT);
        $firstname = $account->firstname;
        $lastname = $account->lastname;
        $email = $account->email;

        $url = XO_API_URL . "nsa/user";

        $postRequest = array(
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email
        );

        if(@$_GET['register']){
            $postRequest['register'] = true;
        }

        if(@$_GET['months']){
            $postRequest['expiry_date'] = date("Y-m-d", strtotime("+". $_GET['months'] ." months"));
        }

        $cURLConnection = curl_init($url);

        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $postRequest);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

        $apiResponse = curl_exec($cURLConnection);
        curl_close($cURLConnection);

        // $apiResponse - available data from the API request
        // $jsonArrayResponse = json_decode($apiResponse);
        echo $apiResponse;
    }

    public function blog_categories()
    {
        /*
         * Run this URL on a cron job every minute. In this case, the following URL would be requested every 2 mins: ajax?c=import&a=course-accounts
         */

        // get data from API
        $data = file_get_contents(IMPORT_BASE_URL
            . "get_post_categories.php?taxonomy=blog_category");

        // JSON to PHP array
        $data = json_decode($data);

        // iterate through data
        foreach ($data->data as $item) {

            $currentCategory = ORM::for_table('blogCategories')
                ->where_any_is(array(
                    array('oldID' => $item->id),
                    array('title' => $item->name)
                ))
                ->find_one();

            if (!isset($currentCategory->id)) {
                $currentCategory = ORM::for_table("blogCategories")->create();
            }

            $currentCategory->oldId = $item->id;
            $currentCategory->title = $item->name;
            $currentCategory->slug = $item->slug;
            $currentCategory->parent = $item->parent;
            $currentCategory->description = $item->description;

            $currentCategory->save();

        }

        echo "Import Successfully!";

    }

    public function blogs()
    {

        if (@$_GET['blog_id']) {
            $c = $this->blogs->getBlogByID($_GET['blog_id']);
            if (@$c->oldID) {
                // get data from API
                $data = file_get_contents(IMPORT_BASE_URL
                    . "get_blog_posts.php?post_type=blog&taxonomy=blog_category&post_id="
                    . $c->oldID);
                // JSON to PHP array
                $data = json_decode($data);
                $this->save_blog($data->data);
            }
        } else {
            /*
             * Before importing, create a file in /assets/cdn/importCounts called courses.txt - and put a 0 inside
             *
             * This file will be used to increment the offset every time we request the API
             *
             * Run this URL on a cron job every minute. In this case, the following URL would be requested every 2 mins: ajax?c=import&a=courses
             */

            $path = TO_PATH_CDN . 'importCounts/blogs.txt';
            if (file_exists($path) === false) {
                file_put_contents(TO_PATH_CDN . 'importCounts/blogs.txt', 0);
            }

            // get the current offset from the file
            $offsetStart = file_get_contents($path);

            // get data from API
            $data = file_get_contents(IMPORT_BASE_URL
                . "get_posts.php?post_type=blog&taxonomy=blog_category&offset="
                . $offsetStart);

            // JSON to PHP array
            $data = json_decode($data);
            // iterate through data
            if (@$data->data->posts) {
                foreach ($data->data->posts as $item) {
                    $this->save_blog($item);
                    // write updated offset to file
                    $offsetStart += 1;
                    file_put_contents(TO_PATH_CDN . 'importCounts/blogs.txt',
                        $offsetStart);
                }
            }
        }

        echo "Import Successfully!";

    }

    protected function save_blog($item)
    {
        $courseID = null;
        if(@$item->meta->_course[0]){
            $currentCourse = ORM::for_table('courses')
                ->where('OldId', $item->meta->_course[0])
                ->find_one();

            if (@$currentCourse->id) {
                $courseID = $currentCourse->id;
            }
        }
        $data = [];
        $data['oldID'] = $item->id;
        $data['title'] = $item->title;
        $data['slug'] = $item->slug;
        $data['contents'] = $this->filter_content($item->description);
        $data['whenAdded'] = $item->when_created;
        $data['whenUpdated'] = $item->when_updated;
        $data['courseID'] =  $courseID;

        $data['categories'] = null;

        if (@$item->featured_image) {
            $data['wpImage']['full'] = $item->featured_image;
        }


        if (@$item->category_ids) {
            foreach ($item->category_ids as $category) {
                $data['categories'][]
                    = $this->blogs->getCategoryByOldId($category)->id;
            }
        }


        $currentBlog = ORM::for_table('blog')
            ->where('OldId', $item->id)
            ->find_one();

        if (@$currentBlog->id) {
            $data['id'] = $currentBlog->id;
        }

        $c = $this->blogs->saveBlog($data);

        return $c;
    }

    // Offers
    public function offers()
    {
        if ($_GET['offer_id']) {
            $a = $this->offers->getOfferByID($_GET['offer_id']);
            if (@$a->oldID) {
                $this->save_offer($a);
                echo json_encode(array(
                    'status'  => 200,
                    'message' => 'Import Successfully!'
                ));
                exit;
            }
        } else {

            $path = TO_PATH_CDN . 'importCounts/offers.txt';

            if (file_exists($path) === false) {
                file_put_contents(TO_PATH_CDN . 'importCounts/offers.txt', 0);
            }

            $offsetStart = file_get_contents($path);

            // get data from API
            $data = file_get_contents(IMPORT_BASE_URL
                . "get_posts.php?post_type=january_offer&offset="
                . $offsetStart);

            // JSON to PHP array
            $data = json_decode($data);

            if (@$data->data->posts) {
                foreach ($data->data->posts as $item) {

                    $this->save_offer($item);

                    // write updated offset to file
                    $offsetStart += 1;
                    file_put_contents(TO_PATH_CDN . 'importCounts/offers.txt',
                        $offsetStart);
                }
            }
            echo json_encode(array(
                'status'  => 200,
                'message' => 'Import Successfully!'
            ));
            exit;
        }

        echo json_encode(array(
            'status'  => 404,
            'message' => 'Something went wrong!'
        ));
        exit;
    }
    // New Offers
    public function new_offers()
    {
        if ($_GET['offer_id']) {
            $a = $this->offers->getOfferByID($_GET['offer_id']);
            if (@$a->oldID) {
                $this->save_offer($a);
                echo json_encode(array(
                    'status'  => 200,
                    'message' => 'Import Successfully!'
                ));
                exit;
            }
        } else {

            $path = TO_PATH_CDN . 'importCounts/new_offers.txt';

            if (file_exists($path) === false) {
                file_put_contents(TO_PATH_CDN . 'importCounts/new_offers.txt', 0);
            }

            $offsetStart = file_get_contents($path);

            // get data from API
            $data = file_get_contents(IMPORT_BASE_URL
                . "get_posts.php?post_type=new_offer&offset="
                . $offsetStart);

            // JSON to PHP array
            $data = json_decode($data);
            if (@$data->data->posts) {
                foreach ($data->data->posts as $item) {

                    $this->save_offer($item);

                    // write updated offset to file
                    $offsetStart += 1;
                    file_put_contents(TO_PATH_CDN . 'importCounts/new_offers.txt',
                        $offsetStart);
                }
            }
            echo json_encode(array(
                'status'  => 200,
                'message' => 'Import Successfully!'
            ));
            exit;
        }

        echo json_encode(array(
            'status'  => 404,
            'message' => 'Something went wrong!'
        ));
        exit;
    }

    protected function save_offer($item, $usImport = 1)
    {

        $data = [
            'oldID'            => $item->id,
            'title'            => $item->title,
            'slug'             => $item->slug,
            'whenAdded'        => $item->when_created,
            //'whenUpdated'      => $item->when_updated,
            'contents'         => $item->description,
            'course1Price'     => @$item->meta->_jo_price_single[0]
                ? $item->meta->_jo_price_single[0] : null,
            'courseOtherPrice' => @$item->meta->_jo_price_multi[0]
                ? $item->meta->_jo_price_multi[0] : null,
            'maxCourses'       => @$item->meta->_jo_max_nr_courses[0]
                ? $item->meta->_jo_max_nr_courses[0] : null,
            'courses'          => @$item->meta->_jo_courses[0]
                ? json_decode($item->meta->_jo_courses[0]) : null,
            'usImport' => $usImport == 1 ? '1' : '0'
        ];

        if(@$item->meta->_no_price_single[0]){
            $data['course1Price'] = $item->meta->_no_price_single[0];
        }
        if(@$item->meta->_no_price_multi[0]){
            $data['courseOtherPrice'] = $item->meta->_no_price_multi[0];
        }
        if(@$item->meta->_no_courses[0]){
            $data['courses'] = $item->meta->_no_courses;
        }
        if(@$item->meta->_no_max_nr_courses[0]){
            $data['maxCourses'] = $item->meta->_no_max_nr_courses[0];
        }

        $newCourses = [];
        if (@$data['courses']) {
            foreach ($data['courses'] as $c) {
                $oldCourse = $this->courses->getCourseByOldID($c, $usImport);
                if (@$oldCourse->id) {
                    $newCourses[] = $oldCourse->id;
                }
            }
        }


        $data['courses'] = $newCourses;
        $currentOffer = $this->offers->getOfferByOldID($item->id, $usImport);


        if (@$currentOffer->id) {
            $data['id'] = $currentOffer->id;
        }

        return $this->offers->saveOffer($data);
    }

    // Viral Quizes
    public function viral_quizes()
    {
        $usImport = 0;
        if(isset($_GET['importFrom']) && $_GET['importFrom'] == 'us'){
            $usImport = 1;
        }

        $importUrl = IMPORT_BASE_URL;
        if($usImport == 0){ // For UK
            $importUrl = IMPORT_BASE_UK_URL;
        }

        if ($_GET['viral_quiz_id']) {
            // get data from API
            $data = file_get_contents($importUrl. "get_viral_quizes.php?quiz_id=" . $_GET['viral_quiz_id']);
            // JSON to PHP array
            $data = json_decode($data);
            $this->save_viral_quiz($data->data);
            echo json_encode(array(
                'status'  => 200,
                'message' => 'Import Successfully!'
            ));
            exit;
        } else {

            $path = TO_PATH_CDN . 'importCounts/viralQuizzes-'.$usImport.'.txt';

            if (file_exists($path) === false) {
                file_put_contents($path, 0);
            }

            $offsetStart = file_get_contents($path);

            // get data from API
            $data = file_get_contents($importUrl. "get_viral_quizes.php?offset=" . $offsetStart);

            // JSON to PHP array
            $data = json_decode($data);

            if (@$data->data->quizes) {
                foreach ($data->data->quizes as $item) {

                    $quiz = $this->save_viral_quiz($item);

                    // write updated offset to file
                    $offsetStart += 1;
                    file_put_contents($path, $offsetStart);
                }
            }
            echo json_encode(array(
                'status'  => 200,
                'message' => 'Import Successfully!'
            ));
            exit;
        }

        echo json_encode(array(
            'status'  => 404,
            'message' => 'Something went wrong!'
        ));
        exit;
    }

    protected function save_viral_quiz($item)
    {

        $data = [
            'id'              => $item->ID,
            'title'           => $item->name,
            'type'            => $item->type == 'WPVQGameTrueFalse' ? 1 : 2,
            'randomQuestions' => $item->randomQuestions,
            'whenAdded'       => date("Y-m-d H:i:s", $item->dateCreation),
            'whenUpdated'     => date("Y-m-d H:i:s", $item->dateUpdate),
        ];

        if (@$item->questions) {
            $i = 0;
            foreach ($item->questions as $question) {

                $data['questions'][$i]['id'] = $question->ID;
                $data['questions'][$i]['title'] = $question->label;
                $data['questions'][$i]['quizID'] = $question->quizId;
                $data['questions'][$i]['position'] = $question->position;
                $data['questions'][$i]['description'] = $question->content;
                $data['questions'][$i]['imageUrl'] = @$question->picture
                    ? $question->picture : null;

                if (@$question->answers) {
                    $j = 0;
                    foreach ($question->answers as $answer) {
                        $data['questions'][$i]['answers'][$j]['id']
                            = $answer->ID;
                        $data['questions'][$i]['answers'][$j]['title']
                            = $answer->label;
                        $data['questions'][$i]['answers'][$j]['questionID']
                            = $answer->questionId;
                        $data['questions'][$i]['answers'][$j]['isCorrect']
                            = $answer->weight;
                        $data['questions'][$i]['answers'][$j]['description']
                            = $answer->content;
                        $data['questions'][$i]['answers'][$j]['imageUrl']
                            = @$answer->picture ? $answer->picture : null;
                        $j++;
                    }
                }

                $i++;
            }
        }

        return $this->viralQuizzes->saveQuiz($data);
    }

    public function userRewards($userID = null, $accountID = null)
    {
        if(@$userID && @$accountID){
            $_GET['user_id'] = $userID;
            $_GET['account_id'] = $accountID;
        }

        if (@$_GET['user_id']) {
            $account_id = $_GET['account_id'];
            $data = file_get_contents(IMPORT_BASE_URL . "get_user_rewards.php?user_id=" . $_GET['user_id']);
            // JSON to PHP array
            $data = json_decode($data);
            if (@$data->data) {
                $response = $data->data;

                if (@$response->coupons) {
                    foreach ($response->coupons as $coupon) {
                        $this->save_coupon($coupon);
                    }
                }

                $userRewardsIds = [];

                if (@$response->fetch_login_awards) {
                    $loginAwards = $response->fetch_login_awards;

                    foreach ($loginAwards as $key => $value) {
                        if ($value == '1') {
                            if($key == 28){
                                $key = 21;
                            }
                            $short = 'signed_' . $key;
                            $currentRewards = ORM::for_table('rewards')
                                ->where('short', $short)
                                ->find_one();
                            if (@$currentRewards->id) {
                                $userRewardsIds[] = $currentRewards->id;
                            }
                        }

                    }
                }
                if (@$response->completed_modules_awards) {
                    $moduleAwards = $response->completed_modules_awards;
                    foreach ($moduleAwards as $key => $value) {
                        if ($value == '1') {
                            $module = 'modules_' . $key;
                            if($key == 30){ // For 30
                                $currentRewards = ORM::for_table('rewards')
                                    ->where('short', 'modules_25')
                                    ->find_one();
                                if (@$currentRewards->id) {
                                    $userRewardsIds[] = $currentRewards->id;
                                }
                            }else if($key == 100){ // For 30
                                $currentRewards = ORM::for_table('rewards')
                                    ->where('short', 'modules_75')
                                    ->find_one();
                                if (@$currentRewards->id) {
                                    $userRewardsIds[] = $currentRewards->id;
                                }
                            }
                            $currentRewards = ORM::for_table('rewards')
                                ->where('short', $module)
                                ->find_one();

                            if (@$currentRewards->id) {
                                $userRewardsIds[] = $currentRewards->id;
                            }
                        }

                    }
                }
                if (@$response->completed_courses_awards) {
                    $moduleAwards = $response->completed_courses_awards;
                    foreach ($moduleAwards as $key => $value) {
                        if ($value == '1') {
                            if($key == 3){ // For 30
                                $currentRewards = ORM::for_table('rewards')
                                    ->where('short', 'courses_2')
                                    ->find_one();
                                if (@$currentRewards->id) {
                                    $userRewardsIds[] = $currentRewards->id;
                                }
                            }

                            $course = 'courses_' . $key;
                            $currentRewards = ORM::for_table('rewards')
                                ->where('short', $course)
                                ->find_one();

                            if (@$currentRewards->id) {
                                $userRewardsIds[] = $currentRewards->id;
                            }
                        }
                    }
                }
                //if (@$response->facebook_award) {
                    $userRewardsIds[] = 14; // Default register reward
                //}
                if (@$response->newsletter_award) {
                    $userRewardsIds[] = 15;
                }

                $this->rewardsAssignes->saveUserRewards($account_id, $userRewardsIds);

                $account = ORM::for_table('accounts')->find_one($account_id);

                $data = array(
                    'rewardPoints'   => count($userRewardsIds),
                    'rewardImported' => 1,
                );
                $account->set($data);
                $account->save();

                if(@$response->has_claimed_certificate_award){
                    // Clam Certificate Coupon
                    $this->coupons->generateRewardCoupon($account->id, 12, 1);
                }



                $this->coupons->checkUserRewardCoupons($account->id, $account->rewardPoints);

                if(@$userID && @$accountID){
                    return true;
                }

                echo json_encode(array(
                    'status'  => 200,
                    'message' => 'Import Successfully!'
                ));
                exit;


            }

        }
    }
    public function userData()
    {
        if (@$_GET['user_id']) {
            $account = $this->accounts->getAccountByID($_GET['account_id']);
            $usImport = $account->usImport == '1' ? 1 : 0;
            $this->save_user_enrollments($account, null, $usImport);
            $this->userRewards($_GET['user_id'], $_GET['account_id']);
            echo json_encode(array(
                'status'  => 200,
                'message' => 'Import Successfully!'
            ));
            exit;
        }
    }

    // Coupons
    public function coupons()
    {

        if ($_GET['coupon_id']) {
            // get data from API
            $data = file_get_contents(IMPORT_BASE_URL
                . "get_coupons.php?coupon_id=" . $_GET['coupon_id']);
            // JSON to PHP array
            $data = json_decode($data);
            $this->save_coupon($data->data);
            echo json_encode(array(
                'status'  => 200,
                'message' => 'Import Successfully!'
            ));
            exit;
        } else {

            $path = TO_PATH_CDN . 'importCounts/coupons.txt';

            if (file_exists($path) === false) {
                file_put_contents(TO_PATH_CDN . 'importCounts/coupons.txt', 0);
            }

            $offsetStart = file_get_contents($path);

            // get data from API
            $data = file_get_contents(IMPORT_BASE_URL
                . "get_coupons.php?number=5000&offset=" . $offsetStart);

            // JSON to PHP array
            $data = json_decode($data);

            if (@$data->data->coupons) {
                foreach ($data->data->coupons as $item) {

                    $quiz = $this->save_coupon($item);

                    // write updated offset to file
                    $offsetStart += 1;
                    file_put_contents(TO_PATH_CDN . 'importCounts/coupons.txt',
                        $offsetStart);
                }
            }
            echo json_encode(array(
                'status'  => 200,
                'message' => 'Import Successfully!'
            ));
            exit;
        }

        echo json_encode(array(
            'status'  => 404,
            'message' => 'Something went wrong!'
        ));
        exit;
    }

    protected function save_coupon($item, $usImport = 1)
    {
        $usedBy = [];

        $data = [
            'oldID'       => $item->id,
            'code'        => $item->code,
            'type'        => $item->discount_type == 'percent' ? 'p' : 'v',
            'value'       => $item->discount_value,
            'valueMin'    => @$item->meta->minimum_amount[0] ? $item->meta->minimum_amount[0] : null,
            'valueMax'    => @$item->meta->maximum_amount[0] ? $item->meta->maximum_amount[0] : null,
            'totalUses'   => @$item->used_by ? count($item->used_by) : 0,
            'totalLimit'  => @$item->usage_limit ? $item->usage_limit : null,
            'expiry'      => @$item->expiry_date  ? date("Y-m-d 23:59:59",strtotime($item->expiry_date)) : null,
            'isReward'    => isset($item->meta->is_award_coupon[0])
            && ($item->meta->is_award_coupon[0] == true) ? 1 : 0,
            'whenAdded'   => isset($item->when_created) ? $item->when_created
                : date("Y-m-d H:i:s"),
            'whenUpdated' => isset($item->when_updated) ? $item->when_updated
                : date("Y-m-d H:i:s"),
            'usImport' => $usImport == 1 ? '1' : '0'
        ];

        if(@$data['isReward'] && ($data['type'] == 'p') && ($data['value'] == 100)){
            $data['type'] = 'v';
            $data['value'] = 100;
        }

        if (@$item->meta->customer_email[0]) {
            $email = unserialize($item->meta->customer_email[0]);
            $account = $this->accounts->getAccountByEmail($email[0], $data['usImport']);
            if ($account->id) {
                $data['forUser'] = $account->id;
            }
        }

        //
        if (@$item->courses) {
            $data['courses'] = '';
            $excludeCourses = [];
            foreach ($item->courses as $courseId) {
                $c = $this->courses->getCourseByOldID($courseId, $data['usImport'] );
                if(@$c->id){
                    $excludeCourses[] = $c->id;
                }
            }
            $data['courses'] = implode(',', $excludeCourses);
        }

        if (@$item->excludeCourses) {
            $data['excludeCourses'] = '';
            $excludeCourses = [];
            foreach ($item->excludeCourses as $courseId){
                $c = $this->courses->getCourseByOldID($courseId,
                    $usImport == '1');
                if(@$c->id){
                    $excludeCourses[] = $c->id;
                }
            }
            $data['excludeCourses'] = implode(',', $excludeCourses);
        }

        $currentCoupon = ORM::for_table('coupons')
            ->where('oldID', $item->id)
            ->where('usImport', $usImport)
            ->find_one();

        if (@$currentCoupon->id) {
            $data['id'] = $currentCoupon->id;
        }

        $excludeCoupons = ['9890', '123279'];
        //$excludeCoupons = [];

        if (@$item->used_by && (!in_array($data['oldID'], $excludeCoupons))) {
            $accounts = $this->accounts->getIdsByOlds($item->used_by, $usImport);
            if (count($accounts) >= 1) {
                foreach ($accounts as $a) {
                    array_push($usedBy, $a['id']);
                }
            }
        }

        // Check claim reward id
        $claimedRewards = ORM::for_table('rewardsClaims')
            ->where('type', $data['type'])
            ->where('value', $data['value'])
            ->find_one();
        if(@$claimedRewards->id){
            $data['rewardClaimID'] = $claimedRewards->id;
        }

        return $this->coupons->saveCoupon($data, $usedBy);
    }

    // Testimonials
    public function testimonials()
    {

        if ($_GET['testimonial_id']) {
            // get data from API
            $data = file_get_contents(IMPORT_BASE_URL
                . "get_posts.php?post_id=" . $_GET['testimonial_id']);
            // JSON to PHP array
            $data = json_decode($data);
            $this->save_testimonial($data->data);
            echo json_encode(array(
                'status'  => 200,
                'message' => 'Import Successfully!'
            ));
            exit;
        } else {

            $path = TO_PATH_CDN . 'importCounts/testimonials.txt';

            if (file_exists($path) === false) {
                file_put_contents(TO_PATH_CDN . 'importCounts/testimonials.txt', 0);
            }

            $offsetStart = file_get_contents($path);

            // get data from API
            $data = file_get_contents(IMPORT_BASE_URL
                . "get_posts.php?post_type=ttshowcase&number=500&offset=" . $offsetStart);

            // JSON to PHP array
            $data = json_decode($data);
            if (@$data->data->posts) {

                foreach ($data->data->posts as $item) {
                    $testimonial = $this->save_testimonial($item);

                    // write updated offset to file
                    $offsetStart += 1;
                    file_put_contents(TO_PATH_CDN . 'importCounts/coupons.txt',
                        $offsetStart);
                }
            }
            echo json_encode(array(
                'status'  => 200,
                'message' => 'Import Successfully!'
            ));
            exit;
        }

        echo json_encode(array(
            'status'  => 404,
            'message' => 'Something went wrong!'
        ));
        exit;
    }

    protected function save_testimonial($item)
    {

        $data = [
            'oldID'       => $item->id,
            'name'        => $item->title,
            'slug'        => $item->slug,
            'location'    => 'p',
            'testimonial' => $item->meta->_aditional_info_short_testimonial[0] ?? null,
            'whenAdded'   => isset($item->when_created) ? $item->when_created : date("Y-m-d H:i:s"),
            //'whenUpdated' => isset($item->when_updated) ? $item->when_updated : date("Y-m-d H:i:s"),
        ];

        if (@$item->featured_image) {
            $data['wpImage']['full'] = $item->featured_image;
        }

        $currentBlog = ORM::for_table('testimonials')
            ->where('OldId', $item->id)
            ->find_one();

        if (@$currentBlog->id) {
            $data['id'] = $currentBlog->id;
        }

        return $this->testimonials->saveTestimonial($data);
    }

    //Synchronous User Logs
    public function syncUserLogs()
    {
        $url = SYNC_BASE_URL . 'get_logs.php?limit=30'; // Add limit=-1 for all records
        if(@$_GET['accountID']){
            $account = $this->accounts->getAccountByID($_GET['accountID']);
            $url .= '&user_id='. $account->oldID;
        }

        if(@$_GET['type']){
            $url .= '&type='. $_GET['type'];
        }

        // get data from API
        $data = file_get_contents($url);

        // JSON to PHP array
        $data = json_decode($data);
        if (@$data->data) {
            $logIds = [];
            $newUserIds = [];
            foreach ($data->data as $log){
                if($log->user_id == 0){
                    $logIds[] = $log->id;
                }else{
                    $account = $this->accounts->getAccountByOldID($log->user_id);

                    if(empty($account)){
                        $account = $this->user_accounts($log->user_id);
                        $this->save_user_enrollments($account);
                    }

                    if($log->type == 'login'){
                        $logData = [
                            'accountID' => $account->id,
                            'ipAddress' => $log->ipAddress,
                            'dateTime' => $log->created_at,
                        ];
                        $this->accounts->signInLog($logData);
                        $logIds[] = $log->id;
                    } else if($log->type == 'completeModule' || $log->type == 'completeCourse'){
                        $course = $this->courses->getCourseByOldID($log->course_id);
                        if(empty($course)){
                            // get data from API
                            $data = file_get_contents(IMPORT_BASE_URL. "get_courses.php?course_id=" . $log->course_id);
                            // JSON to PHP array
                            $data = json_decode($data);
                            $course = $this->save_course($data->data);
                        }
                        $accountID = $account->id;
                        $courseID = $course->id;
                        if($this->courses->checkUserCourseAccess($courseID, $accountID) == false){
                            $this->save_user_enrollments($account, $course);
                        }

                        if($log->type == 'completeModule'){
                            $module = $this->courseModules->getCourseModuleByOldID($log->lesson_id);
                            if(empty($module)){
                                // get data from API
                                $data = file_get_contents(IMPORT_BASE_URL. "get_course_lessons.php?lesson_id=" . $log->lesson_id);
                                // JSON to PHP array
                                $data = json_decode($data);
                                if(@$data->data->id){
                                    $module = $this->save_lesson($data->data, $course->id);
                                }
                            }
                            $moduleID = $module->id;
                            // Save Progress
                            $this->courses->getSingleModule($moduleID, $accountID);
                            $logIds[] = $log->id;
                        }
                        else if($log->type == 'completeCourse'){
                            $this->courses->completeCourse($courseID, $accountID);
                            $logIds[] = $log->id;
                        }
                    }
                }
            }


        }

        // Delete user logs
        if(count($logIds) >= 1 && (SITE_ENVIRONMENT == 'production')){
            $postRequest = array(
                'ids' => $logIds
            );
            $fields_string = http_build_query($postRequest);

            $cURLConnection = curl_init(SYNC_BASE_URL.'delete_logs.php');
            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

            $apiResponse = curl_exec($cURLConnection);
            curl_close($cURLConnection);
        }

            // $apiResponse - available data from the API request
            //$jsonArrayResponse - json_decode($apiResponse);

        echo json_encode(array(
            'status'  => 200,
            'message' => 'Synchronous Successfully!'
        ));
        exit;

    }

    public function updateOrders()
    {
        $orders = ORM::for_table('orders')
            ->where('dataImported', 0)
            ->where_not_null('oldID')
            ->order_by_desc('oldID')
            ->limit(250)
            ->find_many();

        if (count($orders) >= 1) {
            foreach ($orders as $order) {
                if (@$order->oldID) {
                    // get data from API
                    $data = file_get_contents(IMPORT_BASE_URL
                        . "get_orders.php?order_id=" . $order->oldID);
                    // JSON to PHP array
                    $data = json_decode($data);
                    $this->save_order($data->data, '1');
                }
            }
        }
        echo json_encode(array(
            'status'  => 200,
            'message' => 'Import Successfully!'
        ));
    }

    // Vouchers
    public function vouchers()
    {
        if ($_GET['voucher_code_id']) {
            // get data from API
            $data = file_get_contents(IMPORT_BASE_URL
                . "get_voucher_codes.php?voucher_code_id=" . $_GET['voucher_code_id']);
            // JSON to PHP array
            $data = json_decode($data);
            $this->save_voucher_code($data->data, '1');
            echo json_encode(array(
                'status'  => 200,
                'message' => 'Import Successfully!'
            ));
            exit;
        } else {

            $path = TO_PATH_CDN . 'importCounts/vouchers.txt';

            if (file_exists($path) === false) {
                file_put_contents(TO_PATH_CDN . 'importCounts/vouchers.txt',
                    0);
            }

            $offsetStart = file_get_contents($path);

            // get data from API
            $data = file_get_contents(IMPORT_BASE_URL
                . "get_voucher_codes.php?offset=" . $offsetStart);

            // JSON to PHP array
            $data = json_decode($data);

            if (@$data->data->codes) {
                foreach ($data->data->codes as $item) {

                    $code = $this->save_voucher_code($item, '1');

                    // write updated offset to file
                    $offsetStart += 1;
                    file_put_contents(TO_PATH_CDN. 'importCounts/vouchers.txt', $offsetStart);
                }
            }
            echo json_encode(array(
                'status'  => 200,
                'message' => 'Import Successfully!'
            ));
            exit;
        }

        echo json_encode(array(
            'status'  => 404,
            'message' => 'Something went wrong!'
        ));
        exit;
    }

    protected function save_voucher_code($item, $usImport = '0')
    {
        if(@$item->course_id){
            $course = $this->courses->getCourseByOldID($item->course_id, $usImport == '1');
        }
        if(@$item->user_id){
            $account = $this->accounts->getAccountByOldID($item->user_id, $usImport);
            if(empty($account)){
                //$account = $this->user_accounts($item->user_id);
            }
        }

        $data = [
            'oldID'           => $item->id,
            'code'            => $item->voucher_code,
            'expiry'          => $item->expiry_date ?? null,
            'type'            => @$item->course_id ? 'specific' : 'all',
            'courses'         => $course->id ?? null,
            'valueUpto'       => $item->course_id == 'price_conditional_upto' ? 100 : null,
            'whenAdded'       => @$item->date_redeemed && ( $item->date_redeemed != '0000-00-00 00:00:00') ? $item->date_redeemed : date("Y-m-d h:i:s"),
            'whenClaimed'     => @$item->date_redeemed && ( $item->date_redeemed != '0000-00-00 00:00:00') ? $item->date_redeemed : null,
            'userID'          => $account->id ?? null,
            'groupID'         => 0,
            'usImport'        => $usImport
        ];

        // Check if exist
        $voucher = ORM::for_table('vouchers')
            ->where('oldID', $item->id)
            ->where('usImport', $usImport)
            ->find_one();
        if(@$voucher->id){
            $data['id'] = $voucher->id;
        }

        return $this->vouchers->saveVoucher($data);
    }

    public function deleteDulplicateQuizQuestion()
    {
        //echo $sql = "SELECT id, quizID, question, ord, COUNT(question) FROM quizQuestions GROUP BY  question HAVING COUNT(question) > 1;";
        $sql = "select quizID, question, count(*) from quizQuestions where group by quizID, question having count(*) > 1";
        $dquestions = ORM::for_table('quizQuestions')->raw_query($sql)->find_array();

        foreach ($dquestions as $dquestion){
            $questions = ORM::for_table('quizQuestions')->where('quizID', $dquestion['quizID'])->find_many();
            $cquestions = [];
            foreach($questions as $question){
                $qa = $question->question.$question->answerData;
                if(in_array($qa, $cquestions)){
                    echo "Delete:". $question->id."<br>";
                    $question->delete();
                }else{
                    $cquestions[] = $qa;
                }
            }
        }
        echo "completed";
        exit();
    }

    public function updateQuiz()
    {
        $quizzes = ORM::for_table('quizzes')
            ->where_not_null('oldID')
            ->where('appear', 'a')
            ->where('timeLimit', 0)
            ->find_many();

        foreach ($quizzes as $quiz){

            // get data from API
            $data = file_get_contents(IMPORT_BASE_URL . "get_quiz.php?quiz_id=" . $quiz->oldID);

            // JSON to PHP array
            $data = json_decode($data);
            if(@$data->data->quiz->id){
                $quiz->timeLimit = @$data->data->quiz->time_limit ? ceil($data->data->quiz->time_limit/60) : 0;
                $quiz->save();
            }
        }
        echo "completed";
        exit();
    }

    public function updateUserTypeQuestions()
    {
        $sql = 'SELECT questionID, COUNT(*) as total FROM `quizQuestionAnswers` GROUP BY questionID HAVING total=1 ORDER BY questionID';
        $questions = ORM::for_table('quizQuestionAnswers')->raw_query($sql)->find_many();
        if(count($questions) >= 1){
            foreach ($questions as $questionID) {
                $question = ORM::for_table('quizQuestions')->find_one($questionID->questionID);
                $quiz = ORM::for_table('quizzes')->find_one($question->quizID);

                if(@$quiz->oldID){
                    // get data from API
                    $data = file_get_contents(IMPORT_BASE_URL . "get_quiz_questions.php?quiz_id=" . $quiz->oldID . "&question_id=".$question->oldID);
                    // JSON to PHP array
                    $data = json_decode($data);
                    $questionData = $data->data->quiz->questions;
                    if(@$questionData && $questionData->type == 'free_answer'){
                        $answers = explode("\n", $questionData->answers[0]->title);
                        if(count($answers) >= 2){
                            // Delete existing Answers
                            ORM::for_table('quizQuestionAnswers')->where('questionID', $question->id)->delete_many();

                            foreach ($answers as $answer){
                                $item = ORM::for_table('quizQuestionAnswers')->create();
                                $item->questionID = $question->id;
                                $item->answer = $answer;
                                $item->isCorrect = 1;
                                $item->save();
                            }

                            $question->answerType = 'usertype';
                            $question->save();
                        }
                    }
                }
            }
        }
        echo "Completed";
        exit();
    }

    public  function updateModuleSlug()
    {
        $modules = ORM::for_table('courseModules')
            ->raw_query('SELECT `slug`, COUNT(*) FROM courseModules GROUP BY slug HAVING COUNT(*) > 1')
            ->find_many();
        if(count($modules) >= 1) {
            foreach ($modules as $module) {
                $duplicateModules = ORM::for_table('courseModules')
                    ->where('slug', $module->slug)
                    ->find_many();
                $i = 0;
                foreach ($duplicateModules as $duplicateModule) {
                    if($i >= 1){
                        $duplicateModule->slug = $duplicateModule->slug . '-' . $i;
                        $duplicateModule->save();
                    }
                    $i++;
                }
            }
        }
        echo "Completed";
        die;
    }

    public function reportOne() {

        exit;
        // all users in the past year with 3 or more courses
        $date = date('Y-m-d H:i:s', strtotime('-1 year'));

        $accounts = ORM::for_table("accounts")->select("id")->where_gt("whenCreated", $date)->find_many();

        $count = 0;

        foreach($accounts as $account) {

            $courses = ORM::for_table("coursesAssigned")->where_null("bundleID")->where("sub", "0")->where_not_equal("courseID", "342")->where("accountID", $account->id)->count();

            if($courses >= 6) {
                $count ++;
            }

        }

        echo $count;

    }

    public function updateNsfaImages()
    {
        $modules = ORM::for_table('courseModules')
            ->where_like('contents', '%newskillsfitnessacademy.co.uk%')
            ->find_many();
        if(count($modules) >= 1){
            foreach ($modules as $module){
                $module->contents = $this->replaceImagePath($module->contents, 'newskillsfitnessacademy.co.uk');
                $module->save();
            }
        }
    }

    protected function replaceImagePath($htmlString, $fromUrl)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($htmlString);
        $tags = $doc->getElementsByTagName('img');

        foreach ($tags as $tag) {
            $oldSrc = $tag->getAttribute('src');
            if (strpos($oldSrc, $fromUrl) !== false) {
                $newScrURL = $this->medias->addMediaFromUrl($oldSrc);
                $tag->setAttribute('src', $newScrURL['url']);
            }
        }

        return $doc->saveHTML();
    }

    public function orders_old()
    {
        $limit = $_GET['limit'] ?? 50;
        $path = TO_PATH_CDN . 'importCounts/orders_old.txt';

        if (file_exists($path) === false) {
            file_put_contents($path, 0);
        }

        $offset = file_get_contents($path);


        $orders = ORM::for_table('orders')
            ->raw_query("SELECT `oldID`, COUNT(*) FROM orders GROUP BY `oldID` HAVING COUNT(*) > 1 order by oldID limit $offset,$limit")
            ->find_many();

        // iterate through data
        foreach ($orders as $order) {
            if(@$order->oldID){
                // get data from API
                $data = file_get_contents("https://old.newskillsacademy.co.uk/wp-content/themes/academy/export-data/get_orders.php?order_id=" . $order->oldID);

                // JSON to PHP array
                $data = json_decode($data);
                if(@$data->data->id){
                    $this->save_order($data->data);
                }
            }

            // write updated offset to file
            $offset += 1;
            file_put_contents($path, $offset);

        }

        echo "Completed";
        exit();
    }

    public function duplicate_users()
    {
        $path = TO_PATH_CDN . 'importCounts/duplicate_users.txt';

        if (file_exists($path) === false) {
            file_put_contents($path, 0);
        }

        $offset = file_get_contents($path);


        $accounts = ORM::for_table('accounts')
            ->raw_query("SELECT `email`, COUNT(*) AS total FROM accounts GROUP BY `email` HAVING COUNT(*) > 1 order by email desc limit 5")
            ->find_many();

        if(count($accounts) >= 1){
            foreach ($accounts as $account){
                if(@$account['email'] && $account['total'] == 2){
                    $usAccount = ORM::for_table('accounts')
                        ->where('email', $account['email'])
                        ->where('usImport', '1')
                        ->order_by_desc('id')
                        ->find_one();

                    if(empty($usAccount)) {
                        $usAccount = ORM::for_table('accounts')
                            ->where('email', $account['email'])
                            ->order_by_desc('id')
                            ->find_one();
                    }

                    $ukAccount = ORM::for_table('accounts')
                        ->where('email', $account['email'])
                        ->whereNotEqual('id', $usAccount->id)
                        ->find_one();



                    // Import User Course if not imported
                    if(@$usAccount->oldID  && ($usAccount->dataImported == 0) && ($usAccount->usImport == '1')  ){
                        $this->save_user_enrollments($usAccount);
                        if($usAccount->rewardImported == 0){
                            $this->userRewards($usAccount->oldID, $usAccount->id);
                        }
                    }

                    if($ukAccount && $usAccount) {
                        // Update Tables
                        $accountIDs = [
                            'accountAdminNotes',
                            'accountAssignments',
                            'accountBalanceTransactions',
                            'accountDocuments',
                            'accountSignInLogs',
                            'achievers',
                            'courseAssignments',
                            'courseModuleProgress',
                            'coursesAssigned',
                            'journeysAssigned',
                            'leaderboard',
                            'orders',
                            'subscriptions'
                        ];
                        $userIDs = [
                            'couponUserIDs',
                            'courseNotes',
                            'courseRatings',
                            'courseReviews',
                            'coursesSaved',
                            'messages',
                            'quizResults',
                            'rewardsAssigned',
                            'savedIntents'
                        ];
                        foreach ($accountIDs as $table){
                            ORM::for_table($table)
                                ->raw_query("Update $table set accountID='".$ukAccount->id."',accountIDOld='".$usAccount->id."' where accountID='".$usAccount->id."'")
                                ->find_many();
                        }
                        foreach ($userIDs as $table){
                            ORM::for_table($table)
                                ->raw_query("Update $table set userID='".$ukAccount->id."', userIDOld='".$usAccount->id."' where userID='".$usAccount->id."'")
                                ->find_many();
                        }
                        ORM::for_table('coupons')
                            ->raw_query("Update coupons set forUser='".$ukAccount->id."', forUserOld='".$usAccount->id."' where forUser='".$usAccount->id."'")
                            ->find_many();

                        ORM::for_table('media')
                            ->raw_query("Update media set modelId='".$ukAccount->id."',modelIDOld='".$usAccount->id."' where modelType='accountController' and modelId='".$usAccount->id."'")
                            ->find_many();
                        ORM::for_table('messages')
                            ->raw_query("Update messages set recipientID='".$ukAccount->id."', recipientIDOld='".$usAccount->id."' where recipientID='".$usAccount->id."'")
                            ->find_many();

                        $ukAccount->usImport = '0';
                        $ukAccount->is_duplicate = 1;
                        $ukAccount->save();

                        $usAccount->email = $usAccount->email."-merge-new";
                        $usAccount->save();
                    }

                }

                $offset += 1;
                file_put_contents($path, $offset);

            }
        }
        echo "Done";
        exit();
    }

    public function updateCourseUpsell()
    {
        $courses = ORM::for_table('courses')
            ->raw_query("SELECT *  FROM `courses` WHERE `upsellCourse` IS NULL AND `upsellCoursePrice` IS NOT NULL AND `usImport` = '1'  
ORDER BY `courses`.`oldID` ASC")
            ->find_many();

        if(count($courses) >= 1){
            foreach ($courses as $course){
                if(@$course->oldID){
                    // get data from API
                    $data = file_get_contents(IMPORT_BASE_URL
                        . "get_courses.php?course_id=" . $course->oldID);
                    // JSON to PHP array
                    $data = json_decode($data);
                    if(@$data->data->upsellCourse){

                        $upsellCourse = ORM::for_table('courses')
                            ->where('oldID', $data->data->upsellCourse)
                            ->where('usImport', '1')
                            ->find_one();

                        if($upsellCourse->id){
                            $course->upsellCourse = $upsellCourse->id;
                            $course->save();
                        }
                    }

                }

            }
        }
        echo "Done";
        exit();
    }

    public function checkSubscriptions() {

        $subscriptions = ORM::for_table('subscriptions')
            ->raw_query("SELECT subscriptionID FROM `subscriptions` WHERE `isPremium` = 1 AND `status` = 1 AND `totalPaid` = 0 ORDER BY `subscriptions`.`id` DESC")
            ->find_many();
        foreach ($subscriptions as $subscription){

            $stripeSubscription = $this->stripes->retrieveSubscription($subscription['subscriptionID']);
            if($stripeSubscription){
                if($stripeSubscription->status == 'active'){
                    $invoice = $this->stripes->retrieveInvoice($stripeSubscription->latest_invoice);
                    $this->subscriptions->activateSubscriptionByStripeInvoice($invoice);
                }
            }else{
                echo "Not found: ". $subscription['subscriptionID'] . "<br>";
            }
        }
        echo "Done";
        exit();
    }

    public function importReviewImages()
    {
        $reviews = ORM::for_table('courseReviews')
            ->where_not_null('image')
            ->order_by_asc('id')
            ->find_many();
        foreach ($reviews as $review){
            $imageUrl = SITE_URL . 'assets/cdn/reviewImages/' .$review->image;
            // Open file
            $handle = @fopen($imageUrl, 'r');

            // Check if file exists
            if(!$handle){

            }else{
                //echo $imageUrl;
                $date = date("Y-m", strtotime($review->whenSubmitted));
                $directoryPath = TO_PATH_CDN . 'media/'. $date;
                $pathinfo = pathinfo($imageUrl);
                $originalFileName = $pathinfo['filename'];
                $fileName = str_replace(" ", "_",$pathinfo['basename']);

                $this->medias->addImageFromUrl($directoryPath, $fileName, $imageUrl);
                $this->medias->mediaGenerateThumbnail($date, $fileName, $directoryPath.'/'.$fileName, 'thumb');

                $data = [];
                $data['modelType'] = 'courseReviewController';
                $data['modelId'] = $review->id;
                $data['url'] = TO_URL_CDN.'/media/'.$date.'/'.$fileName;
                $data['title'] = $originalFileName;
                $data['fileName'] = $fileName;
                $data['sizes'] = 'thumb';
                $this->medias->saveMedia($data);
            }

        }
        echo "Done";
        die;
    }

}