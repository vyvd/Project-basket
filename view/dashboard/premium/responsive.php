<?php
$this->setControllers(array("course", "sub", "subscription", "moosend"));

$account = ORM::for_table("accounts")->select(["subActive", "subExpiryDate"])->find_one(CUR_ID_FRONT);

if($this->isActiveSubscription(CUR_ID_FRONT) === false) {
    header('Location: '.SITE_URL.'dashboard');
    exit;
}

$subscription = $this->subscription->getCurrentUserSubscription(CUR_ID_FRONT, false);

$plan = $this->subscription->getCurrentUserSubscriptionPlan($subscription->premiumSubPlanID);



$hasTooltips = true;

//$css = array("dashboard.css", "bootstrap-tour.min.css");
$css = array("bootstrap-tour.min.css");
$pageTitle = "Unlimited Learning Dashboard";
include BASE_PATH . 'account.header.php';

// configs
$inTrial = $this->ifUserInTrial();
$allowJobBoard = true;
$allowCareerRadar = true;
$courseLimit = 50;

if($inTrial == true) {

    if($currency->trialJobBoard == "0") {
        $allowJobBoard = false;
    }

    if($currency->trialCareerRadar == "0") {
        $allowCareerRadar = false;
    }

    $courseLimit = $currency->trialActiveCourses;

}


$total = ORM::for_table("coursesAssigned")->where_null("bundleID")->where("accountID", CUR_ID_FRONT)->count();
$active = ORM::for_table("coursesAssigned")->where_null("bundleID")->where("completed", "0")->where("accountID", CUR_ID_FRONT)->count();
$completed = ORM::for_table("coursesAssigned")->where_null("bundleID")->where("completed", "1")->where("accountID", CUR_ID_FRONT)->count();
$messages = ORM::for_table('messagesQueue')->order_by_desc('whenSent')->limit(5)->find_many();

$activeTab = 'dashboard';
if(@$_GET['tab']){
    $activeTab = $_GET['tab'];
}

$account = ORM::for_table("accounts")->select(['currencyID', 'isAdminSub', 'subExpiryDate'])->find_one(CUR_ID_FRONT); // used for subscription localisation

  
$newSignedID = CUR_ID_FRONT;
$ipAddress = $this->getUserIP();

$account = ORM::for_table('accounts')
->where('id', $newSignedID)
->find_one();


// First login of the day
$todayLoginCount = ORM::for_table('accountSignInLogs')
->where('accountID', $newSignedID)
->whereLike('dateTime', date("Y-m-d").'%')
->count();

if($todayLoginCount == 0){
    $log = ORM::for_table("accountSignInLogs")->create();

    $log->accountID = $newSignedID;
    $log->ipAddress = $ipAddress;
    $log->set_expr("dateTime", "NOW()");

    $log->save();

}
  

?>
    <script src="https://js.stripe.com/v3/"></script>
    <section class="page-title with-nav">
        <div class="container">
            <h1><?= $pageTitle ?></h1>
            <ul class="nav navbar-nav inner-nav nav-tabs">
                <li class="nav-item link">
                    <a class="nav-link dashboardLink <?php if($activeTab == 'dashboard'){?> active show <?php }?>" href="#dashboard" data-toggle="tab">Dashboard</a>
                </li>
                <li class="nav-item link ">
                    <a class="nav-link addCoursesLink <?php if($activeTab == 'courses'){?> active show <?php }?>" href="#courses" data-toggle="tab" onclick="loadPopular();">Add Courses</a>
                </li>
                <?php if($account->currencyID == "1") { ?>
                <li class="nav-item link">
                    <a class="nav-link xoLink <?php if($activeTab == 'xo'){?> active show <?php }?>" href="#xo" data-toggle="tab">XO Student Card</a>
                </li>
                <?php } ?>
                <li class="nav-item link">
                    <a class="nav-link cvLink <?php if($activeTab == 'cv'){?> active show <?php }?>" href="#cv" data-toggle="tab" onclick="loadRezi();">CV Builder</a>
                </li>
                <?php
                if($allowJobBoard == true) { ?>
                <li class="nav-item link">
                    <a class="nav-link jobLink <?php if($activeTab == 'jobs'){?> active show <?php }?>" href="#jobs" data-toggle="tab" onclick="loadJobs();">Job Board</a>
                </li>
                <?php } ?>
                <?php if($account->currencyID == "1" && $allowCareerRadar == true) { ?>
                <li class="nav-item link">
                    <a class="nav-link careersLink <?php if($activeTab == 'careers'){?> active show <?php }?>" href="#careers" data-toggle="tab" onclick="loadCareerRadar();">Suggested Careers</a>
                </li>
                <?php } ?>
                <li class="nav-item link">
                    <a class="nav-link manageLink <?php if($activeTab == 'subscriptions'){?> active show <?php }?>" href="#subscription" data-toggle="tab">Manage Membership</a>
                </li>
            </ul>
        </div>
    </section>

    <style>
        .circle-outer a {
            color:#000;
        }
        .circle-outer a:hover {
            color:#248CAB;
            text-decoration:none;
        }
    </style>

    <section class="page-content" id="dashboardElement">

        <div class="container">

            <div class="tab-content">

                <div id="dashboard" class="tab-pane <?php if($activeTab == 'dashboard'){?> active show <?php } else{?> fade <?php }?>">

                    <div class="loading-card col-12 text-center" v-if="loadingCard">
                        <i class="fa fa-spin fa-spinner" style="font-size:100px;margin-top:100px;color:#248CAB;"></i>
                    </div>
                    <div class="row" v-else>
                        <div class="col-12 regular-full">
                            <div class="row align-items-center">

                                <div class="col-12 col-md-8 col-lg-8 pl-0">

                                    <div class="white-rounded benefitModule">
                                        <div class="row">
                                            <div class="col-12 col-md-5 col-lg-5 text-center">

                                                <i class="fad fa-medal"></i>

                                                <div class="title">
                                                    Unlimited Plus
                                                </div>

                                            </div>
                                            <div class="col-12 col-md-7 col-lg-7">
                                                <h3>Benefits</h3>

                                                <div class="items">
                                                    <div class="item">
                                                        <i class="far fa-check"></i>
                                                        Unlimited Access to Over 700 Courses
                                                    </div>
                                                    <?php if($account->currencyID == "1") { ?>
                                                    <div class="item">
                                                        <i class="far fa-check"></i>
                                                        XO Student Discount Card
                                                    </div>
                                                    <?php } ?>
                                                    <div class="item">
                                                        <i class="far fa-check"></i>
                                                        Access to Career Webinars
                                                    </div>
                                                    <?php if($account->currencyID == "1" && $allowCareerRadar == true) { ?>
                                                    <div class="item">
                                                        <i class="far fa-check"></i>
                                                        Career Matching
                                                    </div>
                                                    <?php } ?>
                                                    <div class="item">
                                                        <i class="far fa-check"></i>
                                                        CV Builder
                                                    </div>
                                                    <div class="item">
                                                        <i class="far fa-check"></i>
                                                        Prize Draws
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-12 col-md-4 col-lg-4 pr-0">

                                    <div class="white-rounded">
                                        <div class="circle-outer less">
                                            <a href="<?= SITE_URL ?>dashboard/courses">
                                                <h3>My Courses</h3>
                                                <!-- Progress Circle  -->
                                                <div class="progress-circle mx-auto" data-value='100'>
                                                     <span class="progress-left">
                                                        <span class="progress-bar border-primary"></span>
                                                     </span>
                                                    <span class="progress-right">
                                                        <span class="progress-bar border-primary"></span>
                                                     </span>
                                                    <div class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                                                        <div class="h2 font-weight-bold"><?= $total ?></div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>

                                        <div class="circle-outer less">
                                            <a href="<?= SITE_URL ?>dashboard/courses">
                                                <h3>
                                                    Available Courses
                                                    <i class="fad fa-info-circle tooltip-icon" data-toggle="tooltip" data-placement="top" title="You're able to access up to <?= $courseLimit ?> active courses at any time. As soon as you complete a course, it is no longer active - freeing up your available courses."></i>
                                                </h3>
                                                <!-- Progress Circle  -->
                                                <div class="progress-circle mx-auto" data-value='100'>
                                                     <span class="progress-left">
                                                        <span class="progress-bar border-primary"></span>
                                                     </span>
                                                    <span class="progress-right">
                                                        <span class="progress-bar border-primary"></span>
                                                     </span>
                                                    <div class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                                                        <div class="h2 font-weight-bold"><?=$courseLimit-$this->sub->countActiveCourses() ?></div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <?php if($account->currencyID == "1") { ?>
                            <div class="col-12 regular-full">
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-12 col-lg-4 no-padding">
                                        <div class="white-rounded dash-details">
                                            <span style="display:none;">Last 30 Days <i class="fas fa-sort-down"></i></span>
                                            <h1 class="text-center"><?= $this->controller->consecutiveSignIns() ?><sup><i class="fas fa-arrow-up"></i></sup></h1>
                                            <h3 class="text-center">Days Logged In</h3>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 col-lg-4 no-padding">
                                        <div class="white-rounded dash-details">
                                            <span style="display:none;">Last 30 Days <i class="fas fa-sort-down"></i></span>
                                            <h1 class="text-center"><img src="<?= SITE_URL ?>assets/user/images/award.png" alt="award"> <?= $this->user->rewardPoints ?></h1>
                                            <h3 class="text-center">Reward Points</h3>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 col-lg-4 text-center">
                                        <a href="https://www.facebook.com/groups/353686723019972/" target="_blank">
                                            <img src="https://i.gyazo.com/0c9f2ca2048bf378d2ceef366ba34002.png" />
                                        </a>
                                        <!--<img src="<?= SITE_URL ?>assets/images/xo-logo.png" alt="xo">
                            <p>Get access to exclusive student discounts</p>
                            <a class="btn btn-outline-light" href="<?= SITE_URL ?>dashboard/student-card">GET YOUR STUDENT CARD NOW</a>-->
                                    </div>
                                </div>

                            </div>
                        <?php } else {
                            ?>
                            <div class="col-12 regular-full">
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-12 col-lg-6 no-padding">
                                        <div class="white-rounded dash-details">
                                            <span style="display:none;">Last 30 Days <i class="fas fa-sort-down"></i></span>
                                            <h1 class="text-center"><?= $this->controller->consecutiveSignIns() ?><sup><i class="fas fa-arrow-up"></i></sup></h1>
                                            <h3 class="text-center">Days Logged In</h3>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 col-lg-6 no-padding">
                                        <div class="white-rounded dash-details">
                                            <span style="display:none;">Last 30 Days <i class="fas fa-sort-down"></i></span>
                                            <h1 class="text-center"><img src="<?= SITE_URL ?>assets/user/images/award.png" alt="award"> <?= $this->user->rewardPoints ?></h1>
                                            <h3 class="text-center">Reward Points</h3>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <?php
                        } ?>

                        <?php
                        $studyGroupUrl = $this->getSetting("study_group_url");
                        ?>
                        
                        <div class="col-12 regular-full">
                            <a href="<?= $studyGroupUrl ?>" target="_blank">
                                <img src="<?= SITE_URL ?>assets/images/study-group-banner.png" width="100%" />
                            </a>
                        </div>
                            

                        <div class="col-12 regular-full" id="recommendedCourses">
                            <div class="row">
                                <div class="col-12 white-rounded notification" style="padding: 35px 15px 35px 25px;">

                                    <h3 style="margin-bottom:0;">Add these courses to your account...</h3>

                                    <div class="row">
                                        <div class="col-12 regular-full popularCourseboxes">
                                            <div class="row">
                                                <?php
                                                // get a random assigned course
                                                $randomAssigned = ORM::For_table("coursesAssigned")
                                                    ->where("accountID", CUR_ID_FRONT)
                                                    ->order_by_expr("RAND()")
                                                    ->find_one();

                                                $categories = array();

                                                $randomAssignedCats = ORM::for_table("courseCategoryIDs")
                                                    ->where("course_id", $randomAssigned->courseID)
                                                    ->group_by("category_id")
                                                    ->find_many();

                                                foreach($randomAssignedCats as $cat) {

                                                    array_push($categories, $cat->category_id);

                                                }

                                                $items = ORM::for_table("courseCategoryIDs")
                                                    ->where_not_equal("course_id", $randomAssigned->courseID)
                                                    ->where_in("category_id", $categories)
                                                    ->where_not_equal("category_id", "8")
                                                    ->limit(4)
                                                    ->order_by_expr("RAND()")
                                                    ->find_many();

                                                if(count($items) == 0) {
                                                    // dont show recommended courses
                                                    ?>
                                                    <style>
                                                        #recommendedCourses {
                                                            display:none;
                                                        }
                                                    </style>
                                                    <?php
                                                }

                                                foreach($items as $item) {
                                                    $course = ORM::for_table("courses")->find_one($item->course_id);


                                                    $this->sub->renderCourseSmall($course, 'col-lg-3');
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-12 regular-full">
                            <div class="row">

                                <div class="col-12 col-md-6 white-rounded notification">
                                    <div class="leaderboard">
                                        <h3>Student leaderboard</h3>
                                        <div class="dropdown" style="float: right">
                                            <label style="font-weight: bold" class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                This Month
                                            </label>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                <label @click="changeDate('<?= date("Y-m-")?>', 'This Month')" class="dropdown-item" href="#">This Month</label>
                                                <label @click="changeDate('<?= date("Y-m-", strtotime("-1 month"))?>', 'Last Month')" class="dropdown-item" href="#">Last Month</label>
                                                <!--                                        <a @click="changeDate('custom')" class="dropdown-item" href="#">Custom</a>-->
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table">

                                        <tbody>
                                        <tr v-if="loadingLeaderBoard" class="text-center">
                                            <td colspan="4">
                                                <img class="col-md-6" src="<?= SITE_URL ?>assets/images/loading.gif">
                                            </td>
                                        </tr>
                                        <tr v-else-if="leaderAccounts.length" v-for="(account, index) in leaderAccounts" v-bind:class = "(account.userID == currentAccountID)?'you':''">
                                            <th><img src="<?= SITE_URL ?>assets/user/images/award.png" alt="award"> {{index+1}}</th>
                                            <th>{{getLeaderBoardName(account.firstname, account.lastname, account.leaderboardName)}}<span v-if="account.userID == currentAccountID">(you)</span></th>
                                            <th>{{account.total}} pts</th>
                                            <th>
                                                <span v-if="index == 0 && winner == 1" class="btn btn-primary">WINNER</span>
                                            </th>
                                        </tr>
                                        <tr v-else>
                                            <td colspan="4">No data found!</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-12 col-md-6 white-rounded notification">
                                    <div class="leaderboard">
                                        <h3 style="width: 100%">Messages</h3>
                                        <?php
                                        if(count($messages) >=1){
                                            ?>
                                            <table class="table">
                                                <tr>
                                                    <th>Subject</th>
                                                    <th>Date</th>
                                                </tr>
                                                <?php
                                                foreach ($messages as $message){
                                                    ?>
                                                    <tr>
                                                        <td><a href="<?= SITE_URL.'messages'?>"><?= $message->subject;?></a></td>
                                                        <td><?= date('d/m/Y @ H:i', strtotime($message->whenSent)) ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </table>
                                            <?php
                                        }else{
                                            echo "<label>No Message</label>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div id="courses" class="tab-pane <?php if($activeTab == 'courses'){?> active show <?php } else{?> fade <?php }?>">

                    <div class="row">

                        <div class="col-12" id="recommendedCourses">
                            <div class="row">
                                <div class="col-12">

                                    <div class="row">
                                        <div class="col-12 regular-full popularCourseboxes">
                                            <div class="row">
                                                <div class="col-12 col-lg-3" id="categoryData">
                                                    <div class="white-rounded" style="padding:20px;padding-bottom:0;">
                                                        <ul class="custom-control">
                                                            <li class="active" data-category-id="">
                                                                <a href="javascript:;" id="">
                                                                    All Courses
                                                                </a>
                                                            </li>

                                                            <?php
                                                            $categories = ORM::for_table("courseCategories")->where_null('parentID')->where("showOnHome", "1")->order_by_asc("title")->find_many();

                                                            foreach($categories as $category) {
                                                                if($category->id != "19") {
                                                                    ?>
                                                                    <li data-category-id="<?= $category->id ?>">
                                                                        <a href="javascript:;" id="<?= $category->slug?>">
                                                                            <?= $category->title ?>
                                                                        </a>
                                                                    </li>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </ul>
                                                        <div style="clear:both"></div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-9">
                                                    <div class="row">
                                                        <div class="col-12 search-box"><div class="row"><input type="text" id="filterSearch" placeholder="Search courses..." class="search-field"> <a href="javascript:void(0);" class="search-button"><i aria-hidden="true" class="fas fa-search"></i></a></div></div>
                                                    </div>

                                                    <br />

                                                    <div class="white-rounded" style="padding:20px;padding-bottom:0;">
                                                        <div class="row" id="coursesAjax">
                                                            <div class="col-12"><p class="text-center"><i class="fa fa-spin fa-spinner" style="font-size:50px;margin-top:50px;color:#248cab;"></i></p></div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>


                    </div>
    §
                </div>

                <div id="cv" class="tab-pane <?php if($activeTab == 'cv'){?> active show <?php } else{?> fade <?php }?>">

                    <div class="row">

                        <div class="col-12" id="recommendedCourses">
                            <div class="row">
                                <div class="col-12 regular-full">

                                    <div class="white-rounded" style="padding:20px;">


                                        <div id="reziAjax">
                                            <p class="text-center">
                                                <i class="fa fa-spin fa-spinner"></i>
                                            </p>
                                        </div>

                                        <p><small>This tool is powered by Rezi. Your email and name is shared with them to help you build your CV faster.</small></p>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div id="jobs" class="tab-pane <?php if($activeTab == 'jobs'){?> active show <?php } else{?> fade <?php }?>">

                    <div class="row">

                        <div class="col-12" id="recommendedCourses">
                            <div class="row">
                                <div class="col-12 regular-full">

                                    <div class="white-rounded" style="padding:0px;">


                                        <div id="jobsAjax">
                                            <p class="text-center">
                                                <i class="fa fa-spin fa-spinner"></i>
                                            </p>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div id="careers" class="tab-pane <?php if($activeTab == 'careers'){?> active show <?php } else{?> fade <?php }?>">

                    <div class="row">

                        <div class="col-12" id="recommendedCourses">
                            <div class="row">
                                <div class="col-12 regular-full">

                                    <div class="white-rounded" style="padding:20px;">


                                        <ul class="careerRadarNav">
                                            <li data-cr-url="dash" class="active" style="border-top-left-radius: 8px;">
                                                Dashboard
                                            </li>
                                            <li data-cr-url="job">
                                                Find A Job
                                            </li>
                                            <li data-cr-url="occupation" style="border-top-right-radius: 8px;">
                                                Suggested Careers
                                            </li>
                                        </ul>

                                        <div id="careerRadarAjax">
                                            <p class="text-center">
                                                <i class="fa fa-spin fa-spinner"></i>
                                            </p>
                                        </div>

                                        <p><small>This tool is powered by Career Radar. Your email and name is shared with them to provide you with accurate recommendations.</small></p>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div id="xo" class="tab-pane <?php if($activeTab == 'xo'){?> active show <?php } else{?> fade <?php }?>">
                    <div class="container">
                        <div v-if="buyCard">
                            <div class="row">
                                <div class="col-12 regular-full student-card">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="card-display">
                                                <img src="<?= SITE_URL ?>assets/images/banner-card.png" alt="Student card" />
                                            </div>
                                        </div>
                                        <div class="col-6 pl-0">
                                            <div class="card-content">
                                                <h4 class="text-uppercase">All New Skills Academy Customers Get <u>Free Membership</u> at XO Student Discount </h4>
                                                <ul>
                                                    <li>Exclusively for students studying online</li>
                                                    <li>100's of massive deals and bargains</li>
                                                    <!--                                            <li>Premium membership card delivered direct to you</li>-->
                                                    <li>Sign up for FREE</li>
                                                </ul>
                                                <div class="col-12 text-center">
                                                    <a @click="xoSignUp" href="javascript:void(0)" class="btn btn-light text-uppercase rounded">Join now for Free</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div v-else class="row mt-5">
                            <div class="col-12 col-md-12 col-lg-6 no-padding">
                                <div class="student-card-black dash-details">
                                    <div class="st-card-logo d-flex">
                                        <img src="<?= SITE_URL ?>assets/user/images/xo-white.png"
                                             alt="xo" class="xo">
                                        <img style="max-width: 160px" :src="saveStudentData.profile_image"
                                             alt="student"
                                             class="st-profile bordered">
                                    </div>
                                    <div class="st-card-number d-flex justify-content-center">
                                        {{formatString(saveStudentData.membership)}}
                                    </div>
                                    <div class="st-card-footer d-flex">
                                        <h4 class="text-uppercase">
                                            {{saveStudentData.name}}</h4>
                                        <h4 class="text-uppercase">
                                            <span>Expiry</span><br/>
                                            {{formatDate(saveStudentData.expiry_date)}}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-12 col-lg-6 no-padding membership-note white-rounded pt-5">
                                <div class=" dash-details text-center">
                                    <h3 v-if="expiresInDays(saveStudentData.expiry_date) >= 1" class="text-center">
                                        Your membership expires in {{expiresInDays(saveStudentData.expiry_date)}} days
                                    </h3>
                                    <h3 v-else class="text-center"> Your membership has been expired </h3>

                                    <a target="_blank" class="btn btn-primary upgrade"
                                       href="<?= XO_SITE_URL ?>student/my-card"
                                       v-if="expiresInDays(saveStudentData.expiry_date) <= 30">RENEW
                                        NOW</a>
                                    <button class="btn btn-primary upgrade" v-else
                                            disabled>RENEW NOW
                                    </button>

                                    <p v-if="expiresInDays(saveStudentData.expiry_date) <= 30"
                                       class="text-center">You can renew your card
                                        up to 30 days before expiry</p>
                                </div>
                            </div>
                            <div class="col-12 text-center mt-5">
                                <a target="_blank" :href="'<?php echo XO_SITE_URL;?>?token=' + saveStudentData.loginToken"
                                   class="btn btn-primary text-uppercase extra-radius visit-xo">
                                    Visit XO Student Discount to See All Deals
                                </a>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="subscription" class="tab-pane <?php if($activeTab == 'subscriptions'){?> active show <?php } else{?> fade <?php }?>">
                    <div v-if="loadingCancelSubscription" class="loading-card col-12 text-center">
                        <i class="fa fa-spin fa-spinner" style="font-size:100px;margin-top:100px;color:#248CAB;"></i>
                    </div>
                    <div v-else class="row">

                        <div class="col-12" id="recommendedCourses">
                            <div class="row">
                                <div class="col-12 regular-full">

                                    <div class="white-rounded notification" style="padding:20px;">

                                        <div class="row">
                                            <?php if(@$_GET['update_card'] && ($_GET['update_card'] == 'success')){ ?>
                                                <div class="col-12">
                                                    <div class="alert alert-success">
                                                        Your card has been updated successfully!
                                                    </div>
                                                </div>
                                            <?php }?>
                                            <div class="col-lg-6 col-12">
                                                <h4>Your Membership</h4>
                                                <?php
                                                    if($account->isAdminSub == 0) {
                                                ?>
                                                        <div class="row subInfo">
                                                            <div class="col-6">
                                                                <div class="price">
                                                                    £
                                                                    <?= $plan->price;?>
                                                                    <span>/
                                                                <?php
                                                                if($plan->months == 12) {
                                                                    echo "year";
                                                                }elseif ($plan->months == 6) {
                                                                    echo "half year";
                                                                }else{
                                                                    echo "month";
                                                                }
                                                                ?>
                                                            </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <p>
                                                                    <?php
                                                                    $cancelSubscription = true;
                                                                    if($subscription->status == 3 && ($account->subExpiryDate >= date("Y-m-d"))){
                                                                        $cancelSubscription = false;
                                                                    ?>
                                                                        Expire on <?= date('d/m/Y', strtotime($account->subExpiryDate)) ?>
                                                                    <?php } else{?>
                                                                        Renews on <?= date('d/m/Y', strtotime($subscription->nextPaymentDate)) ?>
                                                                    <?php }?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                <?php
                                                    }
                                                ?>

                                                <?php if($cancelSubscription){?>
                                                    <button @click="cancelSubscription()" style="width: 100%" type="button" class="btn btn-danger">Cancel Subscription</button>
                                                <?php }?>
                                                <br />
                                                <hr />
                                                <br />

                                                <p>
                                                    <a href="<?= SITE_URL ?>dashboard/billing">View invoices/past payments</a>
                                                </p>


                                            </div>
                                            <?php if($subscription->paymentMethod == 'stripe') {?>
                                            <div class="col-lg-6 col-12">
                                                <h4>Payment Method</h4>

                                                <p>Your card ending in <strong>XXXX <?= $subscription->last4?></strong> will be billed for this subscription. </p>

                                                <button @click="updateSubscription()" type="button" class="btn btn-primary">Change Payment Method</button>
                                            </div>
                                            <?php }?>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

<!--    <script src="--><?//= SITE_URL ?><!--assets/vendor/vue3/dist/vue.global.prod.js"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<!--    <script src="--><?//= SITE_URL ?><!--assets/vendor/axios/dist/axios.min.js"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.24.0/axios.min.js" integrity="sha512-u9akINsQsAkG9xjc1cnGF4zw5TFDwkxuc9vUp5dltDWYCSmyd0meygbvgXrlc/z7/o4a19Fb5V0OUE58J7dcyw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
            integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
            crossorigin="anonymous"></script>
    <script>
        var app = new Vue({
            el: '#dashboardElement',
            data: {
                loadingLeaderBoard: false,
                loadingCancelSubscription: false,
                monthLeaderBoard: '<?= date("Y-m-")?>',
                currentAccountID : '<?= $this->user->id ?>',
                leaderAccounts: [],
                winner: 0,
                loadingCard: false,
                saveStudentData: [],
                axiosCancelSource: null,
                loginToken: null,
                buyCard: false,
                stripeAPIToken: '<?= STRIPE_PUBLISHABLE_KEY ?>',
            },
            methods: {
                getInitialData: function () {
                    that = this;
                    const url = "<?= SITE_URL ?>ajax?c=import&a=xo_user_data";
                    axios.get(url,
                    ).then(response => {
                            if (response.data.status == 200) {
                                that.saveSessionStorage(response.data.data);
                            } else {
                                that.loadingCard = false;
                                that.buyCard = true;
                            }
                        }
                    );
                },
                xoSignUp: function () {
                    that = this;
                    that.loadingCard = true;
                    const url = "<?= SITE_URL ?>ajax?c=import&a=xo_user_data&register=true&months=<?= $plan->months;?>";
                    axios.get(url,
                    ).then(response => {
                            if (response.data.status == 200) {
                                that.saveSessionStorage(response.data.data);
                                location.reload();
                            } else {
                                that.loadingCard = false;
                                that.buyCard = true;
                            }
                        }
                    );
                },
                getStorageData: function () {
                    that = this;
                    // Get session storage media data
                    that.saveStudentData = JSON.parse(sessionStorage.NSA_studentCard);
                    that.displayData();
                },
                saveSessionStorage: function (storageData) {
                    that = this;
                    that.saveStudentData = {
                        'name': storageData.user.name,
                        'expiry_date': storageData.user.expiry_date,
                        'membership': storageData.user.membership_id,
                        'profile_image': storageData.user.profile_image,
                        'payments': storageData.payments,
                        'newest_deals': storageData.newest_deals,
                        'featured_deals': storageData.featured_deals,
                        'affiliates': storageData.affiliates,
                        'loginToken': storageData.login.token,
                    };


                    sessionStorage.NSA_studentCard = JSON.stringify(this.saveStudentData);
                    sessionStorage.NSA_date = "<?= date("Y-m-d")?>";
                    sessionStorage.NSA_accountID = "<?= CUR_ID_FRONT ?>";


                    this.displayData();
                },
                displayData: function () {
                    that = this;
                    that.loadingCard = false;

                },
                formatDate: function (date) {
                    return moment(date).format("DD/MM/YY");
                },
                formatString: function (string) {
                    //return string;
                     const a = string.toString();
                     return a.substring(0, 4) + " " + a.substring(4, 8) + " " + a.substring(8, 12) + " " + a.substring(12, 16);
                },
                expiresInDays: function (date) {
                    var a = moment("<?= date("Y-m-d")?>");
                    var b = moment(date);
                    return b.diff(a, 'days')
                },
                getLeaderBoardName: function (firstName, lastName, type) {
                    var name = firstName + ' ' + lastName;
                    if(type == 1){
                        name = firstName + ' ' + lastName.charAt(0);
                    }else if(type == 3){
                        name = firstName.charAt(0) + ' ' + lastName;
                    }else if(type == 4){
                        name = 'User Set to Private';
                    }

                    return name;
                },
                getLeaderBoard: function () {
                    that = this;
                    that.loadingLeaderBoard = true;
                    //const url = "<?//= SITE_URL ?>//ajax?c=rewardsAssigned&a=getLeaderBoard&account_id=<?//= $this->user->id;?>//";
                    //axios.get(url, {
                    //        params: {
                    //            month: this.monthLeaderBoard
                    //        }
                    //    }
                    //).then(response => {
                    //        that.loadingLeaderBoard = false;
                    //        console.log(response);
                    //        that.leaderAccounts = response.data.data.leader_board;
                    //        that.winner = response.data.data.winner;
                    //        console.log(response.data.data.leader_board);
                    //        // if (response.data.status == 200) {
                    //        //     location.reload();
                    //        // }
                    //    }
                    //);
                    const url = "<?= SITE_URL ?>ajax?c=rewardsAssigned&a=getLeaderBoard&account_id=<?= $this->user->id;?>&month="+this.monthLeaderBoard;

                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(response){
                            response = JSON.parse(response);
                            that.loadingLeaderBoard = false;
                            that.leaderAccounts = response.data.leader_board;
                            that.winner = response.data.winner;
                        },
                        error: function(xhr, status, error){
                            console.error(xhr);
                        }
                    });
                },
                changeDate: function (month, title) {
                    that = this;
                    that.monthLeaderBoard = month;
                    $(".leaderboard .dropdown label.dropdown-toggle").text(title);
                    that.getLeaderBoard();
                },
                importUserData: function () {
                    that = this;
                    that.loadingCard = true;
                    const url = "<?= SITE_URL ?>ajax?c=import&a=userData&account_id=<?= $this->user->id;?>&user_id=<?= $this->user->oldID;?>";
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(response){

                            response = JSON.parse(response);

                            var status = response.status;
                            if (status == 200) {
                                location.reload();
                            }
                            console.log(response);
                        },
                        error: function(xhr, status, error){
                            console.error(xhr);
                        }
                    });
                    // axios.get(url,
                    // ).then(response => {
                    //         if (response.data.status == 200) {
                    //             location.reload();
                    //         }
                    //     }
                    // );
                },
                cancelSubscription: function () {
                    that = this;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Are you sure?',
                        text: "Do you want to Cancel the Subscription!",
                        //showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#248cab',
                        //denyButtonText: `Don't save`,
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            //$("#"+table+id).remove();
                            this.cancelSubscriptionProcess();
                        }
                    });
                },
                cancelSubscriptionProcess: function () {
                    that = this;
                    that.loadingCancelSubscription = true;
                    const url = "<?= SITE_URL ?>ajax?c=<?= $subscription->paymentMethod ?? null ?>&a=cancelSubscription&sid=<?= $subscription->id;?>";

                    $.ajax({
                        type: "GET",
                        url: url,
                        dataType: 'JSON',
                        success: function(response){
                            location.reload();
                        },
                        error: function(xhr, status, error){
                            console.error(xhr);
                        }
                    });
                },
                updateSubscription: function () {
                    that = this;
                    that.loadingCancelSubscription = true;
                    const url = "<?= SITE_URL ?>ajax?c=stripe&a=updateSubscription&sid=<?= $subscription->id;?>";
                    var stripe = Stripe( this.stripeAPIToken );
                    $.ajax({
                        type: "GET",
                        url: url,
                        dataType: 'JSON',
                        success: function(response){
                            stripe.redirectToCheckout({
                                // Make the id field from the Checkout Session creation API response
                                // available to this file, so you can provide it as argument here
                                // instead of the {{CHECKOUT_SESSION_ID}} placeholder.
                                sessionId: response.session.id
                            }).then(function (result) {
                                // If `redirectToCheckout` fails due to a browser or network
                                // error, display the localized error message to your customer
                                // using `result.error.message`.
                            });
                            return false;
                            //location.reload();
                        },
                        error: function(xhr, status, error){
                            console.error(xhr);
                        }
                    });
                    return false;
                },
            },
            beforeMount: function () {
                that = this;
                <?php
                if(($this->user->oldID != "" || $this->user->oldID != null) && ($this->user->dataImported == 0)){
                ?>

                that.importUserData();
                <?php
                }
                ?>
                that.loadingCard = true;
                if ((sessionStorage.NSA_accountID == "<?= CUR_ID_FRONT ?>") && (sessionStorage.NSA_date == "<?= date("Y-m-d")?>")) {

                    that.getStorageData();
                    //that.getInitialData();
                } else {
                    that.getInitialData();
                }
            },
            mounted: function (){
                that = this;
                that.getLeaderBoard();
                // $('.carousel .carousel-item').each(function () {
                //     var minPerSlide = 1;
                //     var next = $(this).next();
                //     if (!next.length) {
                //         next = $(this).siblings(':first');
                //     }
                //     next.children(':first-child').clone().appendTo($(this));
                //
                //     for (var i = 0; i < minPerSlide; i++) {
                //         next = next.next();
                //         if (!next.length) {
                //             next = $(this).siblings(':first');
                //         }
                //
                //         next.children(':first-child').clone().appendTo($(this));
                //     }
                // });
            },
            updated: function () {
                // $('.carousel .carousel-item').each(function () {
                //     var minPerSlide = 1;
                //     var next = $(this).next();
                //     if (!next.length) {
                //         next = $(this).siblings(':first');
                //     }
                //     next.children(':first-child').clone().appendTo($(this));
                //
                //     for (var i = 0; i < minPerSlide; i++) {
                //         next = next.next();
                //         if (!next.length) {
                //             next = $(this).siblings(':first');
                //         }
                //
                //         next.children(':first-child').clone().appendTo($(this));
                //     }
                // });
            }
        })


    </script>
    <input type="hidden" id="currentCategoryID" />
    <script>
        setInterval(function(){
            $( "#categoryData li" ).click(function() {

                var category = $(this).data("category-id");

                $("#categoryData li").removeClass("active");
                $(this).addClass("active");

                $("#currentCategoryID").val(category);

                $("#coursesAjax").html('<div class="col-12"><p class="text-center"><i class="fa fa-spin fa-spinner" style="font-size:50px;margin-top:50px;color:#248cab;"></i></p></div>');

                $("#coursesAjax").load("<?= SITE_URL ?>ajax?c=sub&a=browse-courses&category="+category);


            });
        }, 2000);

        function loadPopular() {
            $("#coursesAjax").load("<?= SITE_URL ?>ajax?c=sub&a=browse-courses");
        }
    </script>


    <script>
        function loadCareerRadar() {

            $("#careerRadarAjax").load('<?= SITE_URL ?>ajax?c=careerRadar&a=render-iframe');

            $( ".careerRadarNav li" ).click(function() {

                var crURL = $(this).data('cr-url');

                $(".careerRadarNav li").removeClass("active");
                $(this).addClass("active");

                $("#careerRadarAjax").load('<?= SITE_URL ?>ajax?c=careerRadar&a=render-iframe&url='+encodeURIComponent(crURL));


            });

        }

        function loadRezi() {

            $("#reziAjax").load('<?= SITE_URL ?>ajax?c=rezi&a=render-iframe');

        }

        function loadJobs() {

            $("#jobsAjax").load('<?= SITE_URL ?>ajax?c=rezi&a=render-iframe-jobs');

        }
    </script>

<script src="<?= SITE_URL ?>assets/js/bootstrap-tour.min.js"></script>

<?php

$tour = ORM::for_table("accounts")->where("id", CUR_ID_FRONT)->find_one();
?>
<script>
    var tour2 = '<?php echo $tour->hasDoneTour; ?>';
    if (tour2 == 0) {

        <?php
        $item = ORM::for_table("accounts")->where("id", CUR_ID_FRONT)->find_one();

        $item->hasDoneTour = "1";

        $item->save();
        ?>
        $(document).ready(function() {

            var tour = new Tour({
                framework: 'bootstrap4', // or "bootstrap4" depending on your version of bootstrap
                steps: [{
                        element: ".dashboardLink",
                        title: "Welcome to Premium",
                        content: "We are now going to walk you through the perks of your premium membership..."
                    },
                    {
                        element: ".addCoursesLink",
                        title: "Add Courses",
                        content: "This is where you can add any of the courses you’re eligible for during your membership."
                    },
                    {
                        element: ".xoLink",
                        title: "XO Student Discounts",
                        content: "You’re entitled to a free XO Student Discount card to receive exclusive discounts from UK-based retailers."
                    },
                    {
                        element: ".cvLink",
                        title: "Suggested Careers",
                        content: "Impress your next employer by building a robust CV with our CV builder tool."
                    },
                    {
                        element: ".jobLink",
                        title: "Rewards",
                        content: "Browse thousands of jobs that best match your skills."
                    },
                    {
                        element: ".careersLink",
                        title: "Rewards",
                        content: "Fill out some simple questions and we will suggest the best careers to match you."
                    },
                    {
                        element: ".manageLink",
                        title: "Rewards",
                        content: "Cancel your membership at any given time and manage your payment details."
                    }
                ]
            });
            localStorage.removeItem('tour_current_step');
            localStorage.removeItem('tour_end');

            tour.start();


        });
    }
</script>


<?php include BASE_PATH . 'account.footer.php';

$this->moosend->syncProfile();
    
               // checking if the user has signed in today
            if($todayLoginCount == 1){
    
                // Check if user login yesterday
                $yesterdayLoginCount = ORM::for_table('accountSignInLogs')
                    ->where('accountID', $newSignedID)
                    ->whereLike('dateTime', date("Y-m-d", strtotime('-1 day')).'%')
                    ->count();
                if($yesterdayLoginCount == 0){
    
                    $account->loginDays = 1;
                }else{
                    if($account->loginDays == null) {
    
                        $loginDays = 0;
                        for($lDays = 0; $lDays <= 2000; $lDays++){
                            $lLoginCount = ORM::for_table('accountSignInLogs')
                                ->where('accountID', $newSignedID)
                                ->whereLike('dateTime', date("Y-m-d", strtotime('-'.$lDays.' days')).'%')
                                ->count();
                            if($lLoginCount >= 1){
                                $loginDays++;
                            }else{
                                break;
                            }
                        }
                        $account->loginDays = $loginDays;
                    }else{
                    
                        $account->loginDays = $account->loginDays + 1;
                    }
                }
            
                $account->save();
            
                // check sign in reward
                $this->checkSignInReward($newSignedID, $account->loginDays);
                
            }
?>