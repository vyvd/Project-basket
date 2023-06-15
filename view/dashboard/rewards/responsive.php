<?php
$css = array("dashboard.css");
$pageTitle = "My Rewards";
include BASE_PATH . 'account.header.php';
$this->setControllers(array("rewardClaims","facebook"));
?>
    <section class="page-title">
        <div class="container">
            <h1><?= $pageTitle ?></h1>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="row" id="userRewards">
                <div class="loading-card col-12 text-center" v-if="loadingCard">
                    <i class="fa fa-spin fa-spinner" style="font-size:100px;margin-top:100px;color:#248CAB;"></i>
                </div>
                <div class="col-12 regular-full" v-else>
                    <div class="row pl-3 pr-3">
                        <div class="col-12 col-md-2">
                            <div class="regular-full pointsEarned text-center pointPicker" >
                                <div class="dropdown">
                                    <label class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        This Month
                                    </label>
                                    <div class="dropdown-menu " aria-labelledby="dropdownMenuButton">
                                        <label @click="changeDate('today', 'Today')" class="dropdown-item" href="#">Today</label>
                                        <label @click="changeDate('week', 'This Week')" class="dropdown-item" href="#">This Week</label>
                                        <label @click="changeDate('month', 'This Month')" class="dropdown-item" href="#">This Month</label>
                                        <label @click="changeDate('year', 'This Year')" class="dropdown-item" href="#">This Year</label>
                                        <label @click="changeDate('all', 'All Time')" class="dropdown-item" href="#">All Time</label>
                                        <!--                                        <a @click="changeDate('custom')" class="dropdown-item" href="#">Custom</a>-->
                                    </div>
                                </div>
                                <div class="points mr-0">
                                    <p>Points Earned</p>
                                    <h2 v-if="pointsLoading"><i class="fas fa-spinner fa-spin"></i></h2>
                                    <h2 v-else>{{pointsRewards}}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-10">
                            <div class=" regular-full pointsEarned align-items-center">

                                <div class="trophy">
                                    <?php
                                    for($i=1; $i<=15; $i++){
                                        ?>
                                        <img src="<?= SITE_URL ?>assets/user/images/trophy.png" alt="trophy" class="<?= $i<=$this->user->rewardPoints ? '' : 'not-earned' ?>" />
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 regular-full white-rounded achievements">
                        <h3>Your Achievements</h3>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <?php
                                // sign in rewards
                                $items = ORM::for_table("rewards")->where("category", "signin")->order_by_asc("rorder")->find_many();
                                $signInRewardPoints = $this->getSetting('reward_signin_points_after_limit');
                                $signLimitReward = ORM::for_table('rewardsAssigned')->where_like('rewardName','signed 22 days')->where('userID', $this->user->id)->find_one();
                                ?>
                                <ul>
                                    <?php
                                    $count = 1;
                                    foreach($items as $item) {
                                        ?>
                                        <li <?php if($this->controller->hasReward($item->id) == false) { ?>style="opacity:0.5"<?php } ?>>
                                            <a href="javascript:;"><?= $item->name ?></a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    <li <?php if(empty($signLimitReward)) { ?>style="opacity:0.5"<?php } ?>><a href="javascript:;"><?= $signInRewardPoints?> rewards for every consecutive day after</a></li>
                                </ul>
                            </div>
                            <div class="col-12 col-md-4">
                                <?php
                                // modules rewards
                                $items = ORM::for_table("rewards")->where("category", "modules")->order_by_asc("rorder")->find_many();
                                $moduleRewardPoints = $this->getSetting('reward_module_points_after_limit');

                                ?>
                                <ul>
                                    <?php
                                    $count = 1;
                                    foreach($items as $item) {
                                        $moduleLimit = explode('_', $item->short);
                                        ?>
                                        <li <?php if($this->controller->hasReward($item->id) == false) { ?>style="opacity:0.5"<?php } ?>>
                                            <a href="javascript:;"><?= $item->name ?></a>
                                        </li>
                                        <?php
                                    }
                                    $moduleLimitReward = ORM::for_table('rewardsAssigned')->where_like('rewardName','Complete '.($moduleLimit[1] + 1).' modules')->where('userID', $this->user->id)->find_one();
                                    ?>
                                    <li <?php if(empty($moduleLimitReward)) { ?>style="opacity:0.5"<?php } ?>><a href="javascript:;"><?= $moduleRewardPoints?> rewards for every module after</a></li>
                                </ul>
                            </div>
                            <div class="col-12 col-md-4">
                                <?php
                                // courses rewards
                                $items = ORM::for_table("rewards")->where("category", "courses")->order_by_asc("rorder")->find_many();
                                $courseRewardPoints = $this->getSetting('reward_course_points_after_limit');
                                ?>
                                <ul>
                                    <?php

                                    foreach($items as $item) {
                                        $courseLimit = explode('_', $item->short);
                                        ?>
                                        <li <?php if($this->controller->hasReward($item->id) == false) { ?>style="opacity:0.5"<?php } ?>>
                                            <a href="javascript:;"><?= $item->name ?></a>
                                        </li>
                                        <?php
                                        $courseLimitReward = ORM::for_table('rewardsAssigned')->where_like('rewardName','Complete '.($courseLimit[1] + 1).' courses')->where('userID', $this->user->id)->find_one();
                                    }
                                    ?>
                                    <li <?php if(empty($courseLimitReward)) { ?>style="opacity:0.5"<?php } ?>><a href="javascript:;"><?= $courseRewardPoints?> rewards for every course completion after</a></li>
                                    <?php
                                    if($this->controller->hasReward(14) == false) {
                                        $fb = $this->facebook->likeFacebookPage($this->user->id);
                                        if($fb['facebookLoginUrl']){
                                            $fbLoginUrl = $fb['facebookLoginUrl'];
                                        }
                                        ?>

                                    <?php } else{ ?>
                                        <li class="mt-2"><a href="javascript:;">Register an Account</a></li>
                                    <?php }?>
                                    <?php if($this->controller->hasReward(15) == false) { ?>
                                        <li class="light-trophy">
                                            <a style="opacity:0.5" href="<?= SITE_URL;?>get-our-newsletter">Subscribe to our newsletter</a>
                                        </li>
                                    <?php }else{?>
                                        <li><a href="javascript:;">Subscribe to our newsletter</a></li>
                                    <?php }?>
                                </ul>
                            </div>
                            <div class="col-12 text-center">
                                <br />
                                <p>Only additional rewards points collected after 12/06/21 will apply to your total</p>
                                <p><strong>Important:</strong> Rewards can only be claimed against full price courses. In some cases, you may have automatic discounts applied. Please <a href="<?= SITE_URL ?>courses?reset=true">click here to disable automatic discounting</a>.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 regular-full claimed-rewards">
                        <div class="row">
                            <div class="col-12 col-md-8 white-rounded">
                                <h3>Claimed Rewards</h3>
                                <?php
                                $userClaimedRewards = $this->rewardClaims->getUserClaimedRewards($this->user->id);
                                $claimedRewards = $this->rewardClaims->getAll();

                                if(@$claimedRewards){
                                    foreach ($claimedRewards as $claimed){
                                        $totalPoints = $claimed->points;
                                        $code = $userClaimedRewards[$claimed->id]['code'] ?? false;
                                        $totalUses = $userClaimedRewards[$claimed->id]['totalUses'] ?? null;
                                        $userPoints = $this->user->rewardPoints;
                                        $needMoreTrophy = $totalPoints - $userPoints;
                                        if($code){
                                            $claimed->title = str_replace("£", $currency->short, $claimed->title);
                                            ?>
                                            <div class="rewards-inner <?= $code ? 'active' : ''?>">
                                                <a class="btn btn-primary"><img src="<?= SITE_URL ?>assets/user/images/trophy-white.png" alt="trophy" />X<?= $totalPoints?></a>
                                                <p>
                                                    <?= $claimed->title ?>
                                                    <?php
                                                    if($claimed->type != 'c'){
                                                        ?>
                                                        courses coupon code: <?= $code ?>
                                                        <?php
                                                    }
                                                    ?>

                                                    -
                                                    <?php
                                                    if($totalUses == 1){
                                                        echo '<span class="claimed">Claimed</span>';
                                                    }else{
                                                        if($claimed->type == 'c'){
                                                            echo '<a style="padding: 0" href="'.SITE_URL.'dashboard/certificates"><span class="unclaimed">Claim</span></a>';
                                                        }else{
                                                            echo '<span class="unclaimed">Unclaimed</span>';
                                                        }
                                                    }
                                                    ?>

                                                </p>
                                            </div>
                                            <?php
                                        }else{
                                            ?>
                                            <div class="rewards-inner">
                                                <a class="btn btn-primary"><img src="<?= SITE_URL ?>assets/user/images/trophy-white.png" alt="trophy" />X<?= $totalPoints?></a>
                                                <p>
                                                    <?= $claimed->title .' - '. $needMoreTrophy . ' more trophies needed' ?>

                                                </p>
                                            </div>
                                            <?php
                                        }
                                    }
                                }else{
                                    ?>
                                    <p><em>There is nothing to claim yet.</em></p>
                                    <?php
                                }
                                ?>

                                <p style="padding: 20px;padding-bottom: 0;font-size: 13px;">% off codes are <strong>only</strong> valid on courses.</p>


                            </div>
                            <div class="col-12 col-md-4 no-padding">
                                <div class="white-rounded trophy find-bg">
                                    <p>Find out how to earn trophies</p>
                                    <a href="<?= SITE_URL ?>support/help-articles/how-to-claim-a-reward" class="find-btn"><img src="<?= SITE_URL ?>assets/user/images/find-btn.png" alt="Find out" /></a>
                                </div>
                                <div class="white-rounded redeem find-bg">
                                    <p>Find out how to redeem your rewards</p>
                                    <a href="<?= SITE_URL ?>support/help-articles/redeem-reward-coupon" class="find-btn"><img src="<?= SITE_URL ?>assets/user/images/find-btn.png" alt="Find out" /></a>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12 col-md-8 white-rounded notification">
                                <div class="leaderboard">
                                    <h3 class="mt-0">Student leaderboard</h3>
                                    <div class="dropdown" style="float: right">
                                        <label style="font-weight: bold" class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            This Month
                                        </label>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                            <label @click="changeMonthDate('<?= date("Y-m-")?>', 'This Month')" class="dropdown-item" href="#">This Month</label>
                                            <label @click="changeMonthDate('<?= date("Y-m-", strtotime("-1 month"))?>', 'Last Month')" class="dropdown-item" href="#">Last Month</label>
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
                        </div>
                        <p style="margin-top:30px;"><em>Please note that language courses do not count towards your rewards totals.</em></p>
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
            el: '#userRewards',
            data: {
                loadingCard: false,
                pointsRewards : 0,
                pointsLoading: false,
                pointsRange: 'month',
                loadingLeaderBoard: false,
                monthLeaderBoard: '<?= date("Y-m-")?>',
                currentAccountID : '<?= $this->user->id ?>',
                leaderAccounts: [],
                winner: 0
            },
            methods: {
                getRewardsPoints: function() {
                    that = this;
                    this.pointsLoading = true;
                    const url = "<?= SITE_URL ?>ajax?c=rewardsAssigned&a=getAccountPoints&account_id=<?= $this->user->id;?>&pointsRange="+this.pointsRange;
                    // axios.get(url, {
                    //         params: {
                    //             pointsRange: this.pointsRange
                    //         }
                    //     }
                    // ).then(response => {
                    //         this.pointsLoading = false;
                    //         if (response.data.status == 200) {
                    //             that.pointsRewards = response.data.data.totalPoints
                    //         }
                    //     }
                    // );
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(response){
                            response = JSON.parse(response);
                            that.pointsLoading = false;
                            if (response.status == 200) {
                                that.pointsRewards = response.data.totalPoints;
                            }
                            console.log(response);
                        },
                        error: function(xhr, status, error){
                            console.error(xhr);
                        }
                    });
                },
                importUserRewards: function() {
                    this.loadingCard = true;
                    const url = "<?= SITE_URL ?>ajax?c=import&a=userRewards&account_id=<?= $this->user->id;?>&user_id=<?= $this->user->oldID;?>";
                    // axios.get(url,
                    // ).then(response => {
                    //         if (response.data.status == 200) {
                    //             location.reload();
                    //         }
                    //     }
                    // );
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(response){
                            response = JSON.parse(response);
                            if (response.status == 200) {
                                location.reload();
                            }
                        },
                        error: function(xhr, status, error){
                            console.error(xhr);
                        }
                    });
                },
                changeDate: function(range, title) {
                    that = this;
                    that.pointsRange = range;
                    $(".pointPicker .dropdown label.dropdown-toggle").text(title);
                    this.getRewardsPoints();
                },
                getLeaderBoardName: function(firstName, lastName, type) {
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
                getLeaderBoard: function() {
                    this.loadingLeaderBoard = true;
                    that = this;
                    const url = "<?= SITE_URL ?>ajax?c=rewardsAssigned&a=getLeaderBoard&account_id=<?= $this->user->id;?>&month="+this.monthLeaderBoard;
                    // axios.get(url, {
                    //         params: {
                    //             month: this.monthLeaderBoard
                    //         }
                    //     }
                    // ).then(response => {
                    //         that.loadingLeaderBoard = false;
                    //         console.log(response);
                    //         that.leaderAccounts = response.data.data.leader_board;
                    //         that.winner = response.data.data.winner;
                    //         console.log(response.data.data.leader_board);
                    //         // if (response.data.status == 200) {
                    //         //     location.reload();
                    //         // }
                    //     }
                    // );
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(response){
                            response = JSON.parse(response);
                            that.loadingLeaderBoard = false;
                            console.log(response);
                            that.leaderAccounts = response.data.leader_board;
                            that.winner = response.data.winner;
                            console.log(response.data.leader_board);
                        },
                        error: function(xhr, status, error){
                            console.error(xhr);
                        }
                    });
                },
                changeMonthDate: function(month, title) {
                    that = this;
                    that.monthLeaderBoard = month;
                    $(".leaderboard .dropdown label.dropdown-toggle").text(title);
                    this.getLeaderBoard();
                }
            },
            beforeMount: function() {
                <?php
                if(($this->user->oldID != "" || $this->user->oldID != "") && ($this->user->rewardImported == 0)){
                ?>
                this.importUserRewards();
                <?php
                }
                ?>

            },
            mounted: function (){
                this.getRewardsPoints();
                this.getLeaderBoard();
            },
        })
    </script>

<?php include BASE_PATH . 'account.footer.php';?>