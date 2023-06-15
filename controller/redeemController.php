<?php
require_once(__DIR__ . '/accountController.php');
require_once(__DIR__ . '/courseController.php');

class redeemController extends Controller {


    /**
     * @var accountController
     */
    protected $accounts;

    /**
     * @var courseController
     */
    protected $course;

    public function __construct()
    {
        $this->accounts = new accountController();
        $this->course = new courseController();

        $this->post = $_POST;
        $this->get = $_GET;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;

    }
    public function redeemVoucherCode($voucher, $account)
    {
        if($voucher->type == 'subscription'){
            $account->subActive = '1';
            $account->subExpiryDate = date('Y-m-d', strtotime("+1 year"));
            $account->save();
        }else if($voucher->type == 'amount'){
            $this->accounts->addBalance($voucher->valueUpto, 'Redeem Gift Voucher', $account->id);
        }
        else {
            $courses = explode(",", $voucher->courses);

            // assign courses
            foreach ($courses as $course) {

                // check if course is a bundle
                $courseData = ORM::for_table("courses")->find_one($course);

                if ($courseData->childCourses != "") {
                    // assign main bundle
                    $assignBundle
                        = $this->accounts->assignCourse($courseData->id,
                        $account->id);
                    $bundleID = $assignBundle->id; // get assigned bundle ID

                    foreach (json_decode($courseData->childCourses) as $child) {

                        // assign inner bundle
                        $assign = $this->accounts->assignCourse($child,
                            $account->id, array('bundleID' => $bundleID));

                    }

                } else {

                    $assign = $this->accounts->assignCourse($courseData->id,
                        $account->id);

                }


            }
        }
        // mark voucher claimed
        $voucher->userID = $account->id;
        $voucher->set_expr("whenAdded", "NOW()");

        $voucher->save();

        // @todo: send email notification here

        return $voucher;
    }
    public function redeemVoucherCodeID($voucher, $accountID)
    {
        if($voucher->type == 'subscription'){
            $account = $this->accounts->getAccountByID($accountID);
            $account->subActive = '1';
            $account->subExpiryDate = date('Y-m-d', strtotime("+1 year"));
            $account->save();
        }else if($voucher->type == 'amount'){
            $this->accounts->addBalance($voucher->valueUpto, 'Redeem Gift Voucher', $accountID);
        }
        else{
            $courses = explode(",", $voucher->courses);
            // assign courses
            foreach($courses as $course) {

                // check if course is a bundle
                $courseData = ORM::for_table("courses")->find_one($course);

                if($courseData->childCourses != "") {
                    // assign main bundle
                    $assignBundle = $this->accounts->assignCourse($courseData->id, $accountID);
                    $bundleID = $assignBundle->id; // get assigned bundle ID

                    foreach(json_decode($courseData->childCourses) as $child) {

                        // assign inner bundle
                        $assign = $this->accounts->assignCourse($child, $accountID, array('bundleID' => $bundleID));

                    }

                }
                else {

                    $assign = $this->accounts->assignCourse($courseData->id, $accountID);

                }


            }
        }




        // mark voucher claimed
        $voucher->userID = $accountID;
        $voucher->set_expr("whenAdded", "NOW()");

        $voucher->save();

        // @todo: send email notification here

        return $voucher;
    }
    public function redeemVoucherCS()
    {
        // check if account exists first
        $account = ORM::for_table("accounts")
            ->where("email", $this->post["email"])->find_one();

        // check voucher is valid
        $voucher = ORM::for_table("vouchers")
            ->where("code", $this->post["code"])
            ->where_null("userID")
            ->find_one();

        $courseID = $this->post["courseID"] ?? null;
        $return = [];
        if(empty($voucher) && @$courseID){
            $voucher = ORM::for_table('vouchers')->create();
            $voucher->code = $this->post["code"];
            $voucher->type = $this->post["code"];
            $voucher->courses = $courseID;
            $voucher->whenAdded = date("Y-m-d H:i:s");
            $voucher->groupID = 0;
            $voucher->save();

        }elseif (empty($voucher)){
            $return['error'] = true;
            $return['showCourses'] = true;
            $return['message'] = "Voucher code doesn't exist. Please select a course to apply this voucher.";
            echo json_encode($return);
            exit();
        }

        if(empty($account)){
            $accountID = $this->accounts->createAccount();
            $account = ORM::for_table('accounts')->find_one($accountID);
        }

        $this->redeemVoucherCode($voucher, $account);

        $return['success'] = true;
        $return['message'] = 'Voucher redeemed. Please email the user their new details.';
        header('Content-Type: application/json');
        echo json_encode($return);
        exit;
    }
    public function redeemVoucher() {

        // check if account exists first
        $existing = ORM::for_table("accounts")
            ->where("email", $this->post["email"])->find_one();

        // check voucher is valid
        $voucher = ORM::for_table("vouchers")
            ->where("code", $this->post["code"])
            ->where_null("userID")
            ->find_one();

        if($voucher->id == "") {
            $this->setToastDanger("Unfortunately the voucher code you have entered is invalid, expired, or already claimed.");
            exit;
        }

        if($voucher->allowCourseSelection == "1") {

            if ($existing->id != "") {

                $newSignedID = $existing->id;
                $_SESSION['id_front'] = $newSignedID;

                $_SESSION['idx_front']
                    = base64_encode("g4p3h9xfn8sq03hs2234$newSignedID");

                //Added by Zubaer
                $_SESSION['nsa_email_front'] = $existing->email;

                $_SESSION['csrftoken']
                    = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 40);

            } else {
                $accountID = $this->accounts->createAccount();
            }

            $_SESSION["allowRedeemSelect"] = "1";
            $_SESSION["voucherID"] = $voucher->id;

            $this->redirectJS(SITE_URL.'redeem/select');
        } else {

        if ($existing->id != "") {
            // exists
            $this->redeemVoucherCode($voucher, $existing);
            if(CUR_ID_FRONT == "") { // if already signed in then redirect to account
                $this->redirectJS(SITE_URL.'redeem?voucher=redeemed');
            } else { // if not signed in, then prompt them to enter their password as they have not done so yet
                $this->redirectJS(SITE_URL.'dashboard/courses?voucher=redeemed');
            }
        }
        else {

            // account does not exist, create new account then redeem

            // create account
            $accountID = $this->accounts->createAccount();
            $this->redeemVoucherCodeID($voucher, $accountID);

            if ($this->post["admin"] == "true") { // claimed via admin, show success message

                $this->setToastSuccess("Voucher redeemed. Please email the user their new details.");

            } else {

                // sign the user into their newly created account
                $newSignedID = $accountID;
                $_SESSION['id_front'] = $newSignedID;

                $_SESSION['idx_front']
                    = base64_encode("g4p3h9xfn8sq03hs2234$newSignedID");

                //Added by Zubaer
                $_SESSION['nsa_email_front'] = $this->post["email"];

                $_SESSION['csrftoken']
                    = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 40);

                // redirect to courses with message
                $this->redirectJS(SITE_URL . 'dashboard/courses?voucher=redeemed');

            }

        }

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
            <div class="category-box subCourse subCourse<?= $course->id ?>">
                <div class="img"
                     style="background-image:url('<?= $this->course->getCourseImage($course->id,
                         "large") ?>');width: 100%;
                             height: 335px;
                             background-size: cover;
                             border-radius: 20px;
                             background-position: center center;"></div>

                <div class="Popular-title-top">
                    <i class="far fa-user"></i> <?= $enrolled ?>
                    students
                    <a class="btn btn-outline-primary btn-lg extra-radius offer_course_link courseInfoIcon" href="<?= SITE_URL ?>course/<?= $course->slug ?>" target="_blank"><i class="fa fa-info"></i></a>
                </div>
                <div class="Popular-title-bottom">
                    <?= $course->title ?>
                    <a href="javascript:;" role="button" class="btn btn-outline-primary btn-lg extra-radius btn-sub-add-course subAddCourse" data-course-id="<?= $course->id ?>">Add Course</a>
                </div>
            </div>
        </div>
        <?php

    }

    public function browseCourses() {


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

                $.get( "<?= SITE_URL ?>ajax?c=redeem&a=add-course&courseID="+courseID, function( data ) {
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
                    $.get( "<?= SITE_URL ?>ajax?c=redeem&a=remove-course&courseID="+courseID, function( data ) {
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

    public function addCourse() {

        if($_SESSION["allowRedeemSelect"] == "1") {

            $voucher = ORM::for_table("vouchers")->find_one($_SESSION["voucherID"]);
            $voucher->userID = CUR_ID_FRONT;
            $voucher->set_expr("whenAdded", "NOW()");

            $voucher->save();

            $this->accounts->assignCourse($this->get["courseID"], CUR_ID_FRONT);

            $this->redirectJS(SITE_URL.'dashboard/courses?voucher=redeemed');

        }


    }

}