<?php

require_once(__DIR__ . '/couponController.php');
require_once(__DIR__ . '/emailTemplateController.php');

class rewardsAssignedController extends Controller
{

    /**
     * @var string
     */
    protected $table;

    /**
     * @var couponController
     */
    protected $coupons;

    /**
     * @var emailTemplateController
     */
    protected $emailTemplates;

    public function __construct()
    {
        $this->table = 'rewardsAssigned';
        $this->coupons = new couponController();
        $this->emailTemplates = new emailTemplateController();
    }

    public function saveUserRewards($accountID, array $input)
    {
        if (count($input) >= 1) {
            foreach ($input as $value) {
                $reward = ORM::for_table('rewards')->find_one($value);
                if (@$reward->id) {
                    $isMonthly = true;
                    $emailNotification = false;
                    if($reward->id == 14 || $reward->id == 15){
                        $isMonthly = false;
                    }

                    $this->assignReward($accountID, $reward->short, $isMonthly, $emailNotification);
                }
            }
        }
    }

    public function assignReward($accountID, $rewardName, $isMonthly = true, $emailNotification = true, $rewardPoints = 1, $isAdmin = 0)
    {
        $reward = ORM::for_table("rewards")->where("short", $rewardName)
            ->find_one();

        if ($reward->id != "" || @$rewardName) {
            if($reward->id){
                $checkAlreadyReward = ORM::for_table("rewardsAssigned")
                    ->where("userID", $accountID)
                    ->where("rewardID", $reward->id)
                    ->count();
            }elseif (@$rewardName){
                $checkAlreadyReward = ORM::for_table("rewardsAssigned")
                    ->where("userID", $accountID)
                    ->where("rewardName", $rewardName)
                    ->count();
            }

            if ($checkAlreadyReward == 0) {
                $assign = ORM::for_table("rewardsAssigned")->create();

                $assign->rewardID = $reward->id ?? null;
                $assign->rewardName = $rewardName ?? null;
                $assign->userID = $accountID;
                $assign->points = $rewardPoints;
                $assign->isAdmin = $isAdmin;
                $assign->set_expr("whenAssigned", "NOW()");

                if (strpos($rewardName, 'signed') !== false) {
                    $assign->signIn = "1";
                }

                $assign->save();

                // add reward points
                $account = ORM::for_table("accounts")->find_one($accountID);

                $account->rewardPoints = $account->rewardPoints + $assign->points;

                $account->save();

                // send email notification
                // if(($account->rewardNotification == 1) && ($emailNotification == true)){
                //     $emailTemplate = $this->emailTemplates->getTemplateByTitle('reward_received');
                //     $myRewardsButton = $this->renderHtmlEmailButton("My Rewards", SITE_URL . 'dashboard/rewards');
                //     $message = str_replace("[FIRST_NAME]", $account->firstname, $emailTemplate->description);
                //     $message = str_replace("[LAST_NAME]", $account->lastname, $message);
                //     $message = str_replace("[REWARD_NAME]", $reward->name, $message);
                //     $message = str_replace("[MY_REWARDS_BUTTON]", $myRewardsButton, $message);
                //     $this->sendEmail($account->email, $message, $emailTemplate->subject);
                // }

                // Generate Reward Coupon
                $this->coupons->generateRewardCoupon($account->id, $account->rewardPoints);

            } elseif($isMonthly === true) {

                // Check for monthly reward
                if(@$reward->id){
                    $checkAlreadyReward = ORM::for_table("rewardsAssigned")
                        ->where("userID", $accountID)
                        ->where("rewardID", $reward->id)
                        ->where_like('whenAssigned', date("Y-m") . "%")
                        ->count();
                }else{
                    $checkAlreadyReward = ORM::for_table("rewardsAssigned")
                        ->where("userID", $accountID)
                        ->where("rewardName", $rewardName)
                        ->where_like('whenAssigned', date("Y-m") . "%")
                        ->count();
                }

                if ($checkAlreadyReward == 0) {
                    $assign = ORM::for_table("rewardsAssigned")->create();

                    $assign->rewardID = $reward->id ?? null;
                    $assign->rewardName = $rewardName ?? null;
                    $assign->points = $rewardPoints;
                    $assign->userID = $accountID;
                    $assign->isAdmin = $isAdmin;
                    if (strpos($rewardName, 'signed') !== false) {
                        $assign->signIn = "1";
                    }
                    $assign->set_expr("whenAssigned", "NOW()");

                    $assign->save();

                    // add reward points
                    $account = ORM::for_table("accounts")->find_one($accountID);

                    $account->rewardPoints = $account->rewardPoints + $assign->points;

                    $account->save();
                }
            }

        }

    }

    public function getLeaderBoard()
    {
        $accountRank = 0;

        $date = $_GET['month'];

        $leaderBoard = ORM::for_table($this->table)
            ->raw_query('SELECT r.userID, SUM(r.points) as total, a.firstname, a.lastname, a.leaderboardName from rewardsAssigned r JOIN accounts a on r.userID=a.id Where r.whenAssigned like "'
                . $date
                . '%"   GROUP BY r.userID ORDER BY total DESC limit 0,10')
            ->find_array();
        $winner = $date == date("Y-m-") ? 0 : 1;

        if ($accountRank == 0) {
//            $rank = ORM::for_table($this->table)
//                ->raw_query("select userID, total, rank
//                    from (
//                          select userID, total, @rank := @rank + 1 as rank
//                          from (
//                                select userID, sum(points) total
//                                from   rewardsAssigned
//                                where whenAssigned like '".$date."%'
//                                group by userID
//                                order by sum(total) desc
//                               ) t1, (select @rank := 0) t2
//                         ) t3
//                    where userID = 92;"
//                )
//                ->find_one();
//            echo "<pre>";
//            print_r($rank);
//            die;
        }

        $data = [
            'leader_board' => $leaderBoard,
            'winner'       => $winner,
            'accountRank'  => $accountRank,
        ];

        if ($_GET['a'] == 'getLeaderBoard') {
            echo json_encode(array(
                'status' => 200,
                'data'   => $data
            ));
            exit;
        }
        return $leaderBoard;
    }

    public function getAccountPoints()
    {
        $range = $_GET['pointsRange'];
        if (@$range) {
            if ($range == 'year') {
                $startDate = date("Y-01-01 00:00:01");
                $endDate = date("Y-m-d 23:59:59");
            }else if ($range == 'month') {
                $startDate = date("Y-m-01 00:00:01");
                $endDate = date("Y-m-d 23:59:59");
            }else if ($range == 'week') {
                $startDate = date("Y-m-d 00:00:01", strtotime("-6 days"));
                $endDate = date("Y-m-d 23:59:59");
            }else if ($range == 'today') {
                $startDate = date("Y-m-d 00:00:01");
                $endDate = date("Y-m-d 23:59:59");
            }
        }
        $accountID = $_GET['account_id'];

        if (@$startDate && $endDate) {
            $points = ORM::for_table($this->table)
                ->where('userID', $accountID)
                ->where_gt('whenAssigned', $startDate)
                ->where_lt('whenAssigned', $endDate)
                ->sum('points');
                //->count();
        } else {
            $points = ORM::for_table($this->table)
                ->where('userID', $accountID)
                ->sum('points');
                //->count();
        }

        $data = [
            'totalPoints' => $points
        ];

        if ($_GET['a'] == 'getAccountPoints') {
            echo json_encode(array(
                'status' => 200,
                'data'   => $data
            ));
            exit;
        }
        return $leaderBoard;
    }

}