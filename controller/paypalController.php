<?php

use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Plan;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\Webhook;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Common\PayPalModel;
use PayPal\Rest\ApiContext;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;

require_once(__DIR__ . '/cartController.php');
require_once(__DIR__ . '/orderController.php');
require_once(__DIR__ . '/giftController.php');
require_once(__DIR__ . '/subscriptionController.php');

class paypalController extends Controller
{

    /**
     * @var cartController
     */
    protected $cart;

    /**
     * @var orderController
     */
    protected $orders;

    /**
     * @var string
     */
    protected $returnUrl;

    /**
     * @var string
     */
    protected $cancelUrl;

    /**
     * @var string
     */
    protected $returnGiftUrl;

    /**
     * @var string
     */
    protected $cancelGiftUrl;

    /**
     * @var ApiContext
     */
    protected $_api_context;

    /**
     * @var ApiContext
     */
    protected $_api_context_new;

    /**
     * @var giftController
     */
    protected $gifts;

    protected $currency;
    /**
     * @var subscriptionController
     */
    private $subscriptions;

    public function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;
        $this->returnUrl = SITE_URL.'ajax?c=paypal&a=getPaymentStatus';
        $this->cancelUrl = SITE_URL.'checkout?error=payment_cancel';
        $this->returnGiftUrl = SITE_URL.'ajax?c=paypal&a=getGiftPaymentStatus';
        $this->cancelGiftUrl = SITE_URL.'gift?error=payment_cancel';

        /** PayPal api context **/
        $this->_api_context
            = new ApiContext(new OAuthTokenCredential(PAYPAL_CLIENT_ID,
            PAYPAL_SECRET));
        $this->_api_context->setConfig(array(
            'mode'                   => PAYMENT_MODE,
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled'         => true,
            'log.FileName'           => '/logs/paypal.log',
            'log.LogLevel'           => 'ERROR'
        ));

        $this->_api_context_new
            = new ApiContext(new OAuthTokenCredential(PAYPAL_CLIENT_ID_NEW,
            PAYPAL_SECRET_NEW));
        $this->_api_context_new->setConfig(array(
            'mode'                   => PAYMENT_MODE,
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled'         => true,
            'log.FileName'           => '/logs/paypal.log',
            'log.LogLevel'           => 'ERROR'
        ));

        $this->cart = new cartController();
        $this->orders = new orderController();
        $this->gifts = new giftController();
        $this->subscriptions = new subscriptionController();

        // get current currency
        $currency = $this->currentCurrency();
        $this->currency = strtoupper($currency->code);

    }

    protected function processPaypalPayment(
        $order,
        $returnPaypalUrl,
        $cancelPaypalUrl
    ) {
        $orderItems = $this->orders->getOrderItemsByOrderID($order->id);
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $currencyData = $this->currentCurrency(); // get currency code

        $currency = $currencyData->code;

        foreach ($orderItems as $item) {
            $item_1 = new Item();
            $item_1->setName($item->id)->setCurrency($currency)->setQuantity(1)
                ->setPrice($item->price);
            $items[] = $item_1;
        }

        $item_list = new ItemList();
        $item_list->setItems($items);

        $amount = new Amount();
        $amount->setCurrency($currency)->setTotal($order->total);

        $transaction = new Transaction();
        //$transaction->setAmount($amount)->setItemList($item_list)->setDescription('New Skills Academy UK Order '.$order->id)->setInvoiceNumber($order->invoiceNo);
        $transaction->setAmount($amount)
            ->setDescription('New Skills Academy UK Order '.$order->id)
            ->setInvoiceNumber($order->invoiceNo);

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl($returnPaypalUrl)
            ->setCancelUrl($cancelPaypalUrl);

        $payment = new Payment();
        $payment->setIntent('Sale')->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));


        try {
            $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            return [
                'error'   => true,
                'message' => $ex->getCode(),
                'data'    => $ex->getData()
            ];
        }

        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        $_SESSION['paypal_payment_id'] = $payment->getId();

        $order->transactionID = $_SESSION['paypal_payment_id'];
        $order->save();

        if (isset($redirect_url)) {
            return [
                'success'      => true,
                'redirect_url' => $redirect_url
            ];
        }

        return [
            'error'   => true,
            'message' => 'Last Something went wrong, Please try again!'
        ];
    }

    public function processPayment()
    {
        $request = $this->post;
        $accountID = CUR_ID_FRONT;

        if (empty($accountID)) {
            $accountID = $this->cart->createAccount();
        }

        $_SESSION['user_username'] = $this->post["email"] ??
            ORM::for_table('accounts')->find_one($accountID)->email;


        if (isset($this->get['cart']) && ($this->get['cart'] == 'assessment')) {
            $order = $this->cart->updateAssessmentOrder($accountID);
            $this->returnUrl = SITE_URL
                .'ajax?c=paypal&a=getPaymentStatus&cart=assessment';
            $this->cancelUrl = SITE_URL
                .'dashboard/ncfe/assessments?error=payment_failed';
        } else {
            $order = $this->cart->updateCheckoutOrder($request, ORDER_ID,
                $accountID);
        }

        $orderItems = $this->orders->getOrderItemsByOrderID($order->id);
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $currencyData = $this->currentCurrency(); // get currency code

        $currency = $currencyData->code;

        foreach ($orderItems as $item) {
            $item_1 = new Item();
            $item_1->setName($item->id)->setCurrency($currency)->setQuantity(1)
                ->setPrice($item->price);
            $items[] = $item_1;
        }

        $item_list = new ItemList();
        $item_list->setItems($items);

        $amount = new Amount();
        $amount->setCurrency($currency)->setTotal($order->total);

        $transaction = new Transaction();
        //$transaction->setAmount($amount)->setItemList($item_list)->setDescription('New Skills Academy UK Order '.$order->id)->setInvoiceNumber($order->invoiceNo);
        $transaction->setAmount($amount)
            ->setDescription('New Skills Academy UK Order '.$order->id)
            ->setInvoiceNumber($order->invoiceNo);

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl($this->returnUrl)
            ->setCancelUrl($this->cancelUrl);

        $payment = new Payment();
        $payment->setIntent('Sale')->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));


        try {
            $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            echo json_encode([
                'error'   => true,
                'message' => $ex->getCode(),
                'data'    => $ex->getData()
            ]);
            exit;
        }

        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        $_SESSION['paypal_payment_id'] = $payment->getId();

        $order->transactionID = $_SESSION['paypal_payment_id'];
        $order->save();

        if (isset($redirect_url)) {
            echo json_encode([
                'success'      => true,
                'redirect_url' => $redirect_url
            ]);
            exit;
        }

        echo json_encode([
            'error'   => true,
            'message' => 'Last Something went wrong, Please try again!'
        ]);
        exit;
    }

    public function processGiftPayment()
    {
        $request = $this->post;
        $order = $this->gifts->processPayment($request);

        $processPaypalResponse = $this->processPaypalPayment($order,
            $this->returnGiftUrl, $this->cancelGiftUrl);
        echo json_encode($processPaypalResponse);
        exit();

    }

    public function getGiftPaymentStatus()
    {
        /** Get the payment ID before session clear **/
        $payment_id = $_SESSION['paypal_payment_id'];
        /** clear the session payment ID **/
        unset($_SESSION['paypal_payment_id']);

        if (empty($this->get['PayerID']) || empty($this->get['token'])) {
            header('Location: '.SITE_URL.'gift?error=payment_failed');
            exit;
        }
        $payment = Payment::get($payment_id, $this->_api_context);

        $execution = new PaymentExecution();

        $execution->setPayerId($this->get['PayerID']);
        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);

        if ($result->getState() == 'approved') {
            $order = ORM::for_table('orders')
                ->where('transactionID', $payment_id)->find_one();
            if (@$order->id) {
                $this->gifts->completePayment($order, 'paypal', 'paypal',
                    $payment_id);
            }
            header('Location: '.SITE_URL.'gift/complete');
            exit;
        } else {
            header('Location: '.SITE_URL.'gift?error=payment_failed');
            exit;
        }
    }

    public function getPaymentStatus()
    {
        $successUrl = SITE_URL.'checkout/confirmation';
        $failedUrl = SITE_URL.'checkout?error=payment_failed';

        if ($this->get['cart'] && ($this->get['cart'] == 'assessment')) {
            $successUrl = SITE_URL
                .'dashboard/ncfe/assessments?tab=checkout&payment=success';
            $failedUrl = SITE_URL
                .'dashboard/ncfe/assessments?tab=checkout&payment=failed';
        }


        /** Get the payment ID before session clear **/
        $payment_id = $_SESSION['paypal_payment_id'];

        /** clear the session payment ID **/
        //unset($_SESSION['paypal_payment_id']);

        if (empty($this->get['PayerID']) || empty($this->get['token'])) {
            header('Location: '.$failedUrl);
            exit;
        }
        $payment = Payment::get($payment_id, $this->_api_context);

        $execution = new PaymentExecution();

        $execution->setPayerId($this->get['PayerID']);
        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);

        if ($result->getState() == 'approved') {
            $order = ORM::for_table('orders')
                ->where('transactionID', $payment_id)->find_one();
            if (@$order->id) {

                if ($this->get['cart']
                    && ($this->get['cart'] == 'assessment')
                ) {
                    $this->cart->completeAssessmentOrder($order, 'paypal',
                        'paypal');
                } else {
                    $this->cart->completePaymentOrder($order, 'paypal',
                        'paypal');
                }

            }
            header('Location: '.$successUrl);
            exit;
        } else {
            header('Location: '.$failedUrl);
            exit;
        }
    }

    public function paypalWebhook(): array
    {
        echo 'Webhook worked!';
        $payload = @file_get_contents('php://input');
        $event = json_decode($payload, true);

        //$this->sendEmail('er.hpreetsingh@gmail.com', $event['event_type'] . ': ' . json_encode($event['resource']), 'Paypal Webhook');

        // Handle the event
        switch ($event['event_type']) {
            case 'BILLING.SUBSCRIPTION.ACTIVATED':
            case 'BILLING.SUBSCRIPTION.RENEWED':
                $resource  = $event['resource'];
                if(@$resource['id']) {
                    $this->subscriptions->activateSubscriptionByPaypalSubscription($resource);
                }
                echo 'Received event type ' . $event->type;
                break;

            case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
            case 'BILLING.SUBSCRIPTION.EXPIRED':
                $resource  = $event['resource'];
                $subscription = $this->subscriptions->getBySubscriptionID($resource['id']);
                if(@$subscription) {

                    // Send email for Failed Subscription Payment
                    if($resource->status == 'ACTIVE' && ($subscription->status == 1)){
                        $this->subscriptions->sendSubscriptionFailedNotification($subscription->id);
                    }

                    $subscription->status = $subscription->status == 3 ? 3 : 2;
                    $subscription->set_expr("churnDate", "NOW()");

                    if($subscription->status == "2") {
                        $subscription->set_expr("elapsedDate", "NOW()");
                    }

                    $subscription->save();



                    $otherActiveSubscriptions = ORM::forTable('subscriptions')
                        ->where('accountID', $subscription->accountID)
                        ->where('status', 1)
                        ->count();

                    if($otherActiveSubscriptions == 0){
                        // update Account
                        $account = ORM::forTable('accounts')->findOne($subscription->accountID);
                        if($account) {
                            $account->subActive = $subscription->status == 3 ? 3 : 2;
                            $account->save();
                        }
                    }
                }
                echo 'Received event type ' . $event->type;
                break;

            case 'PAYMENT.CAPTURE.COMPLETED':
                $resource  = $event['resource'];
                $order = ORM::for_table('orders')
                    ->whereNotEqual('status', 'completed')
                    ->find_one($resource['invoice_id']);

                if(@$order->id){
                    if ($order->isAssessment == 1) {
                        $this->cart->completeAssessmentOrder($order, 'paypal', 'paypal', $resource['id']);
                    } else {
                        $this->cart->completePaymentOrder($order, 'paypal', 'paypal', $resource['id']);
                    }
                }

                echo 'Received event type ' . $event->type;
                break;
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        http_response_code(200);
        exit();

    }

    public function getAccessToken()
    {

        $ch = curl_init();
        $clientId = PAYPAL_CLIENT_ID_NEW;
        $secret = PAYPAL_SECRET_NEW;

        curl_setopt($ch, CURLOPT_URL, PAYPAL_URL."/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6); //NEW ADDITION
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $clientId.":".$secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        $result = curl_exec($ch);

        if (empty($result)) {
            return false;
        }

        $json = json_decode($result);
        return $json->access_token;

    }

    public function createProduct(array $product, $accessToken = null): array
    {
        if (empty($accessToken)) {
            $accessToken = $this->getAccessToken();
        }

        if ($accessToken === false) {
            $reponse = [
                'error'   => true,
                'message' => 'Access token not generated'
            ];
            return $reponse;
        }

        $ch = curl_init();

        $values = array(
            'name'        => $product['name'],
            'description' => $product['description'],
            'type'        => 'DIGITAL',
            'category'    => 'EDUCATIONAL_AND_TEXTBOOKS',
        );

        curl_setopt($ch, CURLOPT_URL, PAYPAL_URL.'/v1/catalogs/products');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($values));

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$accessToken;
        $headers[] = 'Paypal-Request-Id: '.$product['id'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $reponse = [
                'error'   => true,
                'message' => 'Error:'.curl_error($ch),
                'data'    => json_decode($result)
            ];
        } else {
            $reponse = [
                'error'   => false,
                'message' => null,
                'data'    => json_decode($result)
            ];
        }
        curl_close($ch);

        return $reponse;
    }

    public function createPlan(array $plan, $accessToken = null): array
    {
        if (empty($accessToken)) {
            $accessToken = $this->getAccessToken();
        }

        if ($accessToken === false) {
            $reponse = [
                'error'   => true,
                'message' => 'Access token not generated'
            ];
            return $reponse;
        }

        $ch = curl_init();

        $frequency = [
            'frequency'      => [
                "interval_unit"  => $plan['unit'],
                "interval_count" => $plan['count']
            ],
            "tenure_type"    => "REGULAR",
            "sequence"       => 1,
            'total_cycles'   => 0,
            "pricing_scheme" => [
                'fixed_price' => [
                    'value'         => $plan['price'],
                    'currency_code' => $plan['currency']
                ]
            ]
        ];


        $values = array(
            'product_id'          => $plan['product_id'],
            'name'                => $plan['name'],
            'description'         => $plan['description'],
            'status'              => 'ACTIVE',
            'billing_cycles'      => [$frequency],
            'payment_preferences' => [
                "auto_bill_outstanding" => true
            ]
        );


        curl_setopt($ch, CURLOPT_URL, PAYPAL_URL.'/v1/billing/plans');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($values));

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$accessToken;
        $headers[] = 'Paypal-Request-Id: '.$plan['id'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $reponse = [
                'error'   => true,
                'message' => 'Error:'.curl_error($ch),
                'data'    => json_decode($result)
            ];
        } else {
            $reponse = [
                'error'   => false,
                'message' => null,
                'data'    => json_decode($result)
            ];
        }
        curl_close($ch);

        return $reponse;
    }

    public function getSubscriptionDetails(string $subscriptionId, $accessToken = null): array
    {
        if (empty($accessToken)) {
            $accessToken = $this->getAccessToken();
        }

        if ($accessToken === false) {
            $reponse = [
                'error'   => true,
                'message' => 'Access token not generated'
            ];
            return $reponse;
        }

        $ch = curl_init();


        curl_setopt($ch, CURLOPT_URL, PAYPAL_URL.'/v1/billing/subscriptions/'.$subscriptionId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($values));

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$accessToken;
        //$headers[] = 'Paypal-Request-Id: '.$plan['id'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);


        if (curl_errno($ch)) {
            $reponse = [
                'error'   => true,
                'message' => 'Error:'.curl_error($ch),
                'data'    => json_decode($result)
            ];
        } else {
            $reponse = [
                'error'   => false,
                'message' => null,
                'data'    => json_decode($result)
            ];
        }
        curl_close($ch);

        return $reponse;
    }

    public function processSubscription($isNCFE = false, $isGift = false)
    {
        $request = $this->get;

        $accountID = CUR_ID_FRONT;
        $_SESSION['user_username'] = $request["email"];
        $subscriptionType = $request["subscriptionType"] ?? ($isGift ? 'gift' : null);


        if(empty($accountID)){
            $accountID = $this->cart->createAccount($request);
        }

        $order = $this->cart->updateCheckoutOrder($request, ORDER_ID, $accountID );

        $orderItem = ORM::for_table('orderItems')
            ->where('orderID',$order->id)
            ->whereNotNull('premiumSubPlanID')
            ->find_one();

        $subscriptionData = [
            'isPremium' => 1,
            'isNCFE' => $isNCFE == true ? '1' : '0',
            'accountID' => $accountID,
            'orderID' => $order->id,
            'premiumSubPlanID' => $orderItem->premiumSubPlanID ?? null,
            'paymentMethod' => 'paypal',
            'last4' => $request['last4'] ?? null,
            'status' => 0,
            'whenAdded' => date("Y-m-d H:i:s"),
            'whenUpdated' => date("Y-m-d H:i:s"),
        ];

        $this->subscriptions->saveSubscription($subscriptionData);

        $response = [
            'validationError' => false
        ];
        echo json_encode($response);
        exit();

    }
    public function completeSubscription()
    {
        $response = [
            'error' => true
        ];
        $request = $this->get;
//        $paypalSubscription = $this->getSubscriptionDetails($request['paypalSubscriptionID']);
//        if ($paypalSubscription['data']->status == 'ACTIVE'){
//            $subscription = $this->subscriptions->activateSubscriptionByPaypalSubscription($paypalSubscription['data'], ORDER_ID);
//
//        }
        $order = ORM::for_table('orders')->find_one(ORDER_ID);
        $subscription = ORM::for_table('subscriptions')
            ->where('orderID', ORDER_ID)
            ->order_by_desc('id')
            ->find_one();

        $subscription->subscriptionID = $request['paypalSubscriptionID'];

        if($subscription->save()){
            $account = ORM::for_table('accounts')->find_one($subscription->accountID);
            $account->subActive = '1';
            $account->save();
            $this->cart->completePaymentOrder($order, 'paypal_subscription', 'paypal_subscription');
            $response = [
                'error' => false
            ];
        }

        echo json_encode($response);
        exit();
    }

    public function check_subscriptions()
    {
        $subscriptions = ORM::for_table('subscriptions')
            ->where('paymentMethod', 'paypal')
            ->where('status', 1)
            ->whereNotNull('subscriptionID')
            ->where('nextPaymentDate', date('Y-m-d', strtotime('-1 day')))
            ->find_many();

        foreach ($subscriptions as $subscription) {
            $paypalSubscription = $this->getSubscriptionDetails($subscription->subscriptionID);
            $paypalSubscriptionData = $paypalSubscription['data'] ?? null;

            if($paypalSubscriptionData && $paypalSubscriptionData->status != 'ACTIVE') {
                $subscription->status = 2;

                if($subscription->status == "2") {
                    $subscription->set_expr("elapsedDate", "NOW()");
                }

                // Check user other active subscriptions
                $otherSubscriptions = ORM::for_table('subscriptions')
                    ->where('accountID', $subscription->accountID)
                    ->where('status', 1)
                    ->where_gte('nextPaymentDate', date('Y-m-d'))
                    ->count();
                if ($otherSubscriptions == 0){
                    if($subscription->isPremium == 1){
                        $account = ORM::for_table('accounts')->find_one($subscription->accountID);
                        $account->subActive = ($account->subActive == '3') ? '3' : '2';
                        $account->save();
                    }elseif ($subscription->isNCFE == '1') {
                        $userCourses = ORM::forTable('coursesAssigned')
                            ->where('subscriptionID', $subscription->subscriptionID)
                            ->where('accountID', $subscription->accountID)
                            ->find_many();
                        if(count($userCourses) >= 1){
                            foreach ($userCourses as $userCourse) {
                                $userCourse->status = 2;
                                $userCourse->save();
                            }
                        }
                    }
                }
            }else {
                $subscription->nextPaymentDate = date("Y-m-d", strtotime($paypalSubscriptionData->billing_info->next_billing_time));
                $subscription->status = 1;
                $subscription->totalPaid += 1;

                $account = ORM::for_table('accounts')->find_one($subscription->accountID);
                $account->subExpiryDate = $account->subExpiryDate <= $subscription->nextPaymentDate ? $subscription->nextPaymentDate : $account->subExpiryDate;
                $account->subActive = '1';
                $account->save();
            }

            $subscription->save();
        }

        echo "Done";
        exit();
    }

    public function cancelPaypalSubscription(string $subscriptionId, $accessToken = null): array
    {
        if (empty($accessToken)) {
            $accessToken = $this->getAccessToken();
        }

        if ($accessToken === false) {
            $reponse = [
                'error'   => true,
                'message' => 'Access token not generated'
            ];
            return $reponse;
        }
        $values = [
            'reason' => 'Cancel Subscription'
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, PAYPAL_URL.'/v1/billing/subscriptions/'.$subscriptionId.'/cancel');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($values));

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$accessToken;
        //$headers[] = 'Paypal-Request-Id: '.$plan['id'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);


        if (curl_errno($ch)) {
            $reponse = [
                'error'   => true,
                'message' => 'Error:'.curl_error($ch),
                'data'    => json_decode($result)
            ];
        } else {
            $reponse = [
                'error'   => false,
                'message' => null
            ];
        }
        curl_close($ch);

        return $reponse;
    }

    public function cancelSubscription()
    {
        $response = [
            'error' => true
        ];
        $request = $this->get;
        $subscription = ORM::forTable('subscriptions')
            ->where('accountID', CUR_ID_FRONT)
            ->where('id', $request['sid'])
            ->findOne();

        $cancelSubscription = $this->cancelPaypalSubscription($subscription->subscriptionID);

        if ($cancelSubscription['error'] == false){
            $subscription->status = 3;
            $subscription->set_expr("churnDate", "NOW()");

            $subscription->save();

            $otherActiveSubscriptions = ORM::forTable('subscriptions')
                ->where('accountID', CUR_ID_FRONT)
                ->where('status', 1)
                ->count();

            if($otherActiveSubscriptions == 0){
                // update Account
                $account = ORM::forTable('accounts')->findOne($subscription->accountID);
                if($account) {
                    $account->subActive = '3';
                    $account->save();
                }
            }

            $response = [
                'error' => false,
                'success' => true
            ];

        }

        echo json_encode($response);
        exit();
    }

    public function processCheckout($isNCFE = false, $isGift = false)
    {
        $request = $this->get;
        $accountID = CUR_ID_FRONT;

        if($request['gift'] == 'null'){
            unset($request['gift']);
            unset($request['giftEmail']);
        }

        if (empty($accountID)) {
            $accountID = $this->cart->createAccount($request);
        }

        $_SESSION['user_username'] = $request["email"] ?? ORM::for_table('accounts')->find_one($accountID)->email;


        if (isset($request['cart']) && ($request['cart'] == 'assessment')) {
            $order = $this->cart->updateAssessmentOrder($accountID, ORDER_ID, $request);
            $this->returnUrl = SITE_URL
                .'ajax?c=paypal&a=getPaymentStatus&cart=assessment';
            $this->cancelUrl = SITE_URL
                .'dashboard/ncfe/assessments?error=payment_failed';
        } else {
            $order = $this->cart->updateCheckoutOrder($request, ORDER_ID, $accountID);
        }


        $response = [
            'validationError' => false,
            'ORDER_ID' => ORDER_ID,
            'accountID' => $accountID,
            'order' => $order,
            'request' => $request
        ];

        echo json_encode($response);
        exit();

    }

    public function completeCheckout()
    {
        $request = $this->post;

        $response = [
            'error' => true
        ];

        $order = ORM::for_table('orders')->find_one(ORDER_ID);

        if (@$order->id) {
            $transaction = $request['order']['purchase_units'][0]['payments']['captures'][0];
            if ($this->get['cart'] && ($this->get['cart'] == 'assessment')) {
                $this->cart->completeAssessmentOrder($order, 'paypal', 'paypal', $transaction['id']);
            } else {
                $this->cart->completePaymentOrder($order, 'paypal', 'paypal', $transaction['id']);
            }

            $response = [
                'error' => false
            ];
        }

        echo json_encode($response);
        exit();
    }
}