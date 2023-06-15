<?php
// This should really be named installmentController()

require_once(__DIR__ . '/emailTemplateController.php');

class subscriptionController extends Controller {

    protected $table;

    /**
     * @var emailTemplateController
     */
    protected $emailTemplates;

    public function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;
        $this->emailTemplates = new emailTemplateController();

        $this->table = 'subscriptions';

    }

    public function saveSubscription(array $input)
    {
        $item = ORM::forTable($this->table)->where('orderID', $input['orderID'])->find_one();
        if(empty($item)){
            $item = ORM::for_table($this->table)->create();
        }
        $data = $input;
        $item->set($data);
        $item->save();
        return $item;
    }

    public function getBySubscriptionID($id)
    {
        $item = ORM::for_table($this->table)->where('subscriptionID', $id)->find_one();
        return $item;
    }
    public function getSubscriptionByOrderID($orderID)
    {
        $item = ORM::for_table($this->table)->where('orderID', $orderID)->find_one();
        return $item;
    }

    public function createStripeSchedules($subscriptionID, $amount, $invoiceNumber, $number = 4)
    {
        $invoice = explode('-', $invoiceNumber);
        for ($i=0; $i<$number; $i++){

            $item = ORM::for_table('subscription_schedules')->create();
            $data = [
                'subscriptionID' => $subscriptionID,
                'invoiceNumber' => $invoice[0].('-000'.($i + 1)),
                'amount' => $amount,
                'dueDate' => date('Y-m-d', strtotime("+".$i." months")),
                'whenAdded' => date("Y-m-d H:i:s"),
                'whenUpdated' => date("Y-m-d H:i:s"),
            ];

            $item->set($data);
            $item->save();
        }
    }

    public function getSubscriptionSchedulesByID($subscriptionID)
    {
        return ORM::for_table('subscription_schedules')
            ->where('subscriptionID', $subscriptionID)
            ->order_by_asc('invoiceNumber')
            ->find_many();
    }

    public function getCurrentUserSubscription($userID, $activeStatus = true) {

        $subscription = ORM::for_table("subscriptions")
            ->where("accountID", $userID);

        if($activeStatus) {
            $subscription = $subscription->where('status', 1);
        }

        return $subscription->where('isPremium', 1)
            ->whereNotNull('premiumSubPlanID')
            ->find_one();
    }

    public function getCurrentUserSubscriptionPlan($planID) {
        return ORM::for_table("premiumSubscriptionsPlans")
            ->find_one($planID);
    }

    public function activateSubscriptionByStripeInvoice($invoice) {
        $subscription = $this->getBySubscriptionID($invoice->subscription);
        if($subscription->id){
            $subscription->status = 1;
            $subscription->totalPaid += 1;
            $account = ORM::for_table('accounts')->find_one($subscription->accountID);
            if(@$subscription->premiumSubPlanID){
                $plan = ORM::forTable('premiumSubscriptionsPlans')->findOne($subscription->premiumSubPlanID);
                $subscription->nextPaymentDate = date("Y-m-d",strtotime("+".$plan->months." Months"));
                $account->subExpiryDate = $subscription->nextPaymentDate;
                $account->subActive = '1';
                $account->save();

            }elseif(@$subscription->isNCFE){
                $subscription->nextPaymentDate = date("Y-m-d",strtotime("+1 Month"));
                $account->isNCFE = '1';
                $account->save();
            }else{
                $subscription->nextPaymentDate = date("Y-m-d",strtotime("+1 Month"));
            }
            $subscription->whenUpdated = date("Y-m-d H:i:s", $invoice->created);
            $subscription->save();

            if($subscription->isPremium == 0 || ($subscription->isPremium == 1 && $subscription->isNSPay == 1)) {
                $scheduleCounts = ORM::for_table('subscription_schedules')
                    ->where('subscriptionID', $subscription->id)
                    ->count();
                if($scheduleCounts == 0){
                    // Total Subscription Schedule
                    $this->createStripeSchedules($subscription->id, $subscription->perMonthAmount, $invoice->number);
                }

                $schedule = ORM::for_table('subscription_schedules')
                    ->where('subscriptionID', $subscription->id)
                    ->where('invoiceNumber', $invoice->number)
                    ->find_one();
                if(empty($schedule)){
                    $schedule = ORM::for_table('subscription_schedules')
                        ->where('subscriptionID', $subscription->id)
                        ->order_by_asc('dueDate')
                        ->find_one();
                }
                if(@$schedule->id){
                    $schedule->invoiceID = $invoice->id;
                    $schedule->invoiceNumber = $invoice->number;
                    $schedule->isPayed = 1;
                    $schedule->whenUpdated = date("Y-m-d H:i:s", $invoice->created);
                    $schedule->save();
                }
            }else{


                // Add XO Account
                $url = XO_API_URL . "signup-nsa";

                $postRequest = array(
                    'name' => $account->firstname . ' ' . $account->lastname,
                    'email' => $account->email,
                    'expiry_date' => $subscription->nextPaymentDate
                );

                $cURLConnection = curl_init($url);

                curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $postRequest);
                curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                $apiResponse = curl_exec($cURLConnection);
                curl_close($cURLConnection);
            }
        }
    }

    public function activateSubscriptionByPaypalSubscription($paypalSubscription, $orderID = null) {

        $subscription = $this->getBySubscriptionID($paypalSubscription['id']);

        if($orderID){
            $subscription = $this->getSubscriptionByOrderID($orderID);
        }

        if(@$subscription->id){
            $id = $paypalSubscription['id'];
            $planID = $paypalSubscription['plan_id'];
            $payerID = $paypalSubscription['subscriber']['payer_id'] ?? null;
            $updateTime = $paypalSubscription['update_time'] ?? null;
            if($updateTime){
                $updateTime = date("Y-m-d", strtotime($updateTime));
            }
            $nextPaymentDate = $paypalSubscription['billing_info']['next_billing_time'] ?? null;
            if($nextPaymentDate){
                $nextPaymentDate = date("Y-m-d", strtotime($nextPaymentDate));
            }

            $subscription->subscriptionID = $id;

            if(@$payerID){
                $subscription->customerID = $payerID;
            }

            $subscription->priceID = $planID;
            $subscription->status = 1;
            $subscription->totalPaid += 1;

            $account = ORM::for_table('accounts')->find_one($subscription->accountID);

            if(@$subscription->premiumSubPlanID){
                $plan = ORM::forTable('premiumSubscriptionsPlans')->findOne($subscription->premiumSubPlanID);
                $subscription->nextPaymentDate = $nextPaymentDate;
                $account->subExpiryDate = $subscription->nextPaymentDate;
                $account->subActive = '1';
                $account->save();

            }elseif(@$subscription->isNCFE){
                $subscription->nextPaymentDate = date("Y-m-d",strtotime("+1 Month"));
                $account->isNCFE = '1';
                $account->save();
            }else{
                $subscription->nextPaymentDate = date("Y-m-d",strtotime("+1 Month"));
            }
            $subscription->whenUpdated = $updateTime;
            $subscription->save();

            // Add XO Account
            $url = XO_API_URL . "signup-nsa";

            $postRequest = array(
                'name' => $account->firstname . ' ' . $account->lastname,
                'email' => $account->email,
                'expiry_date' => $subscription->nextPaymentDate
            );

            $cURLConnection = curl_init($url);

            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $postRequest);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

            $apiResponse = curl_exec($cURLConnection);
            curl_close($cURLConnection);

            return $subscription;
        }

        return false;

    }

    public function deactivateSubscriptionByPaypalSubscription($paypalSubscription, $orderID = null) {

        $subscription = $this->getBySubscriptionID($paypalSubscription->id);

        if($orderID){
            $subscription = $this->getSubscriptionByOrderID($orderID);
        }

        if(@$subscription->id){
            $subscription->subscriptionID = $paypalSubscription->id;

            if(isset($paypalSubscription->subscriber->payer_id)){
                $subscription->customerID = $paypalSubscription->subscriber->payer_id;
            }

            $subscription->priceID = $paypalSubscription->plan_id;
            $subscription->status = 1;
            $subscription->totalPaid += 1;

            $account = ORM::for_table('accounts')->find_one($subscription->accountID);

            if(@$subscription->premiumSubPlanID){
                $plan = ORM::forTable('premiumSubscriptionsPlans')->findOne($subscription->premiumSubPlanID);
                $subscription->nextPaymentDate = date("Y-m-d", strtotime($paypalSubscription->billing_info->next_billing_time));
                $account->subExpiryDate = $subscription->nextPaymentDate;
                $account->subActive = '1';
                $account->save();

            }elseif(@$subscription->isNCFE){
                $subscription->nextPaymentDate = date("Y-m-d",strtotime("+1 Month"));
                $account->isNCFE = '1';
                $account->save();
            }else{
                $subscription->nextPaymentDate = date("Y-m-d",strtotime("+1 Month"));
            }
            $subscription->whenUpdated = date("Y-m-d H:i:s", strtotime($paypalSubscription->update_time));
            $subscription->save();

            // Add XO Account
            $url = XO_API_URL . "signup-nsa";

            $postRequest = array(
                'name' => $account->firstname . ' ' . $account->lastname,
                'email' => $account->email,
                'expiry_date' => $subscription->nextPaymentDate
            );

            $cURLConnection = curl_init($url);

            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $postRequest);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

            $apiResponse = curl_exec($cURLConnection);
            curl_close($cURLConnection);

            return $subscription;
        }

        return false;

    }

    public function sendSubscriptionFailedNotification($subscriptionID) {

        $subscription = $this->getBySubscriptionID($subscriptionID);
        $account = ORM::for_table('accounts')->find_one($subscription->accountID);

        $emailTemplate = $this->emailTemplates->getTemplateByTitle('premium_subscription_failed');

        $redirectLink = SITE_URL.'subscription';

        if(@$emailTemplate->id){
            $variables = [
                '[FIRST_NAME]' => $account->firstname,
                '[LAST_NAME]' => $account->lastname,
                '[REDIRECT_LINK]' => $redirectLink,
            ];

            $message = $emailTemplate->description;
            $subject = $emailTemplate->subject;

            foreach ($variables as $k=>$v){
                $message = str_replace($k, $v, $message);
                $subject = str_replace($k, $v, $subject);
            }

            $this->sendEmail($account->email, $message, $subject);
        }
    }

}