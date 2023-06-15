<?php
$this->setControllers(array("course", "account"));

$this->account->restrictUserAccessTo("NCFE");

$css = array("dashboard.css");
$pageTitle = "Dashboard";
include BASE_PATH . 'account-ncfe.header.php';

$total = ORM::for_table("coursesAssigned")->where_null("bundleID")->where("accountID", CUR_ID_FRONT)->count();
$active = ORM::for_table("coursesAssigned")->where_null("bundleID")->where("completed", "0")->where("accountID", CUR_ID_FRONT)->count();
$completed = ORM::for_table("coursesAssigned")->where_null("bundleID")->where("completed", "1")->where("accountID", CUR_ID_FRONT)->count();
$messages = ORM::for_table('messagesQueue')->order_by_desc('whenSent')->limit(5)->find_many();
?>
    <section class="page-title">
        <div class="container">
            <h1><?= $pageTitle ?></h1>
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

            <div class="loading-card col-12 text-center" v-if="loadingCard">
                <i class="fa fa-spin fa-spinner" style="font-size:100px;margin-top:100px;color:#248CAB;"></i>
            </div>
            <div class="row" v-else>
                <div class="col-12 white-rounded regular-full">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 col-lg circle-outer">
                            <a href="<?= SITE_URL ?>dashboard/courses">
                                <h3>All Courses</h3>
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
                        <div class="col-12 col-md-6 col-lg circle-outer">
                            <a href="<?= SITE_URL ?>dashboard/courses?filter=active">
                                <h3>Active Courses</h3>
                                <div class="progress-circle mx-auto" data-value='<?= $active*(100/$total) ?>'>
                     <span class="progress-left">
                        <span class="progress-bar border-primary"></span>
                     </span>
                                    <span class="progress-right">
                        <span class="progress-bar border-primary"></span>
                     </span>
                                    <div class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                                        <div class="h2 font-weight-bold"><?= $active ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-12 col-md-6 col-lg circle-outer">
                            <a href="<?= SITE_URL ?>dashboard/courses?filter=completed">
                                <h3>Completed Courses</h3>
                                <div class="progress-circle mx-auto" data-value='<?= $completed*(100/$total) ?>'>
                     <span class="progress-left">
                        <span class="progress-bar border-primary"></span>
                     </span>
                                    <span class="progress-right">
                        <span class="progress-bar border-primary"></span>
                     </span>
                                    <div class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                                        <div class="h2 font-weight-bold"><?= $completed ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 circle-outer grey-bg">
                            <a href="<?= SITE_URL ?>dashboard/certificates">
                                <h3>My Certificates</h3>
                                <div class="progress-circle mx-auto" data-value='100'>
                     <span class="progress-left">
                        <span class="progress-bar border-primary"></span>
                     </span>
                                    <span class="progress-right">
                        <span class="progress-bar border-primary"></span>
                     </span>
                                    <div class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                                        <div class="h2 font-weight-bold"><?= count($this->controller->getMyCertificates()) ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

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
                        <div class="col-12 col-md-12 col-lg-4 xo-bg text-center">
                            <img src="<?= SITE_URL ?>assets/images/xo-logo.png" alt="xo">
                            <p>Get access to exclusive student discounts</p>
                            <a class="btn btn-outline-light" href="<?= SITE_URL ?>dashboard/student-card">GET YOUR STUDENT CARD NOW</a>
                        </div>
                    </div>

                </div>

                <div class="col-12 regular-full" id="recommendedCourses">
                    <div class="row">
                        <div class="col-12 white-rounded notification" style="padding: 35px 15px 35px 25px;">

                            <h3 style="margin-bottom:0;">Other courses you might like...</h3>

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

                                            $enrolled = ORM::for_table("coursesAssigned")
                                                ->where("courseID", $course->id)->count();
                                            $enrolled *= (1 + 35 / 100);
                                            $enrolled = number_format($enrolled + 48);

                                            ?>
                                            <div class="col-12 col-md-6 col-lg-3">
                                                <div class="category-box">
                                                    <div class="img"
                                                         style="background-image:url('<?= $this->course->getCourseImage($course->id,
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
                                                            <a class="btn btn-outline-primary btn-lg extra-radius"
                                                               href="<?= SITE_URL ?>course/<?= $course->slug ?>?add=true" role="button">Add to Cart</a>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
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
    </section>

<!--    <script src="--><?//= SITE_URL ?><!--assets/vendor/vue3/dist/vue.global.prod.js"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script src="<?= SITE_URL ?>assets/vendor/axios/dist/axios.min.js"></script>
    <script>
        var app = new Vue({
            el: '#dashboardElement',
            data: {
                loadingLeaderBoard: false,
                monthLeaderBoard: '<?= date("Y-m-")?>',
                currentAccountID : '<?= $this->user->id ?>',
                leaderAccounts: [],
                winner: 0,
                loadingCard: false
            },
            methods: {
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
                            console.log(response);
                            that.loadingLeaderBoard = false;
                            that.leaderAccounts = response.data.leader_board;
                            that.winner = response.data.winner;

                            console.log(response.data.leader_board);
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
            },
            beforeMount: function () {
                <?php
                if(($this->user->oldID != "" || $this->user->oldID != null) && ($this->user->dataImported == 0)){
                ?>
                that = this;
                that.importUserData();
                <?php
                }
                ?>
            },
            mounted: function (){
                that = this;
                that.getLeaderBoard();
            },
        })


    </script>

<?php include BASE_PATH . 'account.footer.php';?>