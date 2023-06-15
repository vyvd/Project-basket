<?php
require_once(__DIR__ . '/cartController.php');
require_once(__DIR__ . '/subscriptionController.php');
require_once(__DIR__ . '/giftController.php');

class stripeController extends Controller {

    /**
     * @var cartController
     */
    protected $cart;

    /**
     * @var subscriptionController
     */
    protected $subscriptions;

    /**
     * @var giftController
     */
    protected $gifts;

    protected $stripeSecret;

    protected $currency;

    public function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;

        $this->cart = new cartController();
        $this->subscriptions = new subscriptionController();
        $this->gifts = new giftController();

        $this->stripeSecret = STRIPE_SECRET_KEY;
        // get current currency
        $currency = $this->currentCurrency();
        $this->currency = strtolower($currency->code);
    }

    public function updatePaymentIntent ($paymentIntentId, $request, $amount, $customerId, $description, $order)
    {
        $intent = \Stripe\PaymentIntent::update($paymentIntentId,[
            'payment_method'         => $request['stripeToken'],
            'amount'                 => $amount * 100,
            'customer'               => $customerId,
            'setup_future_usage'     => 'off_session',
            'description'            => $description,
            'confirm'                => true,
            'metadata'               => ['invoice' => $order->invoiceNo],
            //'return_url'             => SITE_URL.'ajax?c=stripe&a=stripeResponse',
            'return_url'             => SITE_URL.'stripe/response',
            'payment_method_options' => [
                'card' => [
                    'request_three_d_secure' => 'any'
                ]
            ],
            'receipt_email'          => $request['email'],
        ]);
        return $intent;
    }

    public function processPayment()
    {
        $request = $this->post;

        $accountID = CUR_ID_FRONT;

        if(empty($accountID)){
            $accountID = $this->cart->createAccount();
        }
        $_SESSION['user_username'] = $this->post["email"] ?? ORM::for_table('accounts')->find_one($accountID)->email;

        if(isset($this->get['cart']) && ($this->get['cart'] == 'assessment')) {
            $order = $this->cart->updateAssessmentOrder($accountID );
        }else{
            $order = $this->cart->updateCheckoutOrder($request, ORDER_ID, $accountID );
        }

        //Charging Customer
        $status = $this->createStripeCharge($request, $order);

        if (isset($status['success']) && $status['success'] == true) {

            echo json_encode([
                'success' => true,
                'payment_intent' => $status['data']->id,
                'data' => $status
            ]);
            exit;
        }
        elseif (
            isset($status['error']) &&
            ($status['error'] == true) &&
            (($status['data']->status == 'requires_source_action') || ($status['data']->next_action->type == 'redirect_to_url'))
        ) {
            $order->transactionID = $status['data']->id;
            $order->save();
            echo json_encode([
                'error' => true,
                'requires_action' => true,
                'payment_intent_client_secret' => $status['data']->client_secret,
                'data' => $status
            ]);
            exit();
        }
        else {
            $order->status = 2;
            $order->save();
            echo json_encode(['error' => true, 'data' => $status]);
            exit();
        }
    }
    public function processGiftPayment()
    {
        $request = $this->post;
        $order = $this->gifts->processPayment($request);
//        if($request['gift_type'] == 'subscription'){
//            $subscription = $this->processSubscription(false, true);
//        }
//        echo "<pre>";
//        print_r($subscription);
//        print_r($request);
//        print_r($order);
//        die;
        //Charging Customer
        $status = $this->createStripeCharge($request, $order);

        if (isset($status['success']) && $status['success'] == true) {

            echo json_encode([
                'success' => true,
                'payment_intent' => $status['data']->id,
                'data' => $status
            ]);
            exit;
        }
        elseif (isset($status['error']) && ($status['error'] == true) && ($status['data']->status == 'requires_source_action')) {
            $order->transactionID = $status['data']->id;
            $order->save();
            echo json_encode([
                'error' => true,
                'requires_action' => true,
                'payment_intent_client_secret' => $status['data']->client_secret,
                'data' => $status
            ]);
            exit();
        }
        else {
            $order->status = 2;
            $order->save();
            echo json_encode(['error' => true, 'data' => $status]);
            exit();
        }
    }
    private function createStripeCharge($request, $order)
    {
        $status = "";
        \Stripe\Stripe::setApiKey($this->stripeSecret);
        $amount = $order->total;
        $currency = $this->currency;
        $description = 'New Skills Academy Order '.$order->id;

        // Create the Customer
        $customer = \Stripe\Customer::create([
            'email' => $request['email'],
        ]);

        $intent = \Stripe\PaymentIntent::create([
            'payment_method'         => $request['stripeToken'],
            'amount'                 => $amount * 100,
            'description'            => $description,
            'currency'               => strtolower($currency),
            'confirm'                => true,
            'customer'               => $customer->id,
            'metadata'               => ['invoice' => $order->invoiceNo],
            //'return_url'             => SITE_URL.'ajax?c=stripe&a=stripeResponse',
            'return_url'             => SITE_URL.'stripe/response',
            'payment_method_options' => [
                'card' => [
                    'request_three_d_secure' => 'any'
                ]
            ],
            'receipt_email'          => $request['email'],
        ]);
        $response = $this->stripeGenerateResponse($intent, $request, $order);
        return $response;

        echo json_encode($response);
        exit;
    }
    private function createStripeFutureCharge($request, $order, $unitPrice, $customerId)
    {
        $status = "";
        \Stripe\Stripe::setApiKey($this->stripeSecret);
        $amount = $unitPrice;
        $currency = $this->currency;
        $description = 'New Skills Academy Order '.$order->id;


        $intent = \Stripe\PaymentIntent::create([
            'payment_method'         => $request['stripeToken'],
            'amount'                 => $amount * 100,
            'customer'               => $customerId,
            'setup_future_usage'     => 'off_session',
            'description'            => $description,
            'currency'               => strtolower($currency),
            'confirm'                => true,
            'metadata'               => ['invoice' => $order->invoiceNo],
            //'return_url'             => SITE_URL.'ajax?c=stripe&a=stripeResponse',
            'return_url'             => SITE_URL.'stripe/response',
            'payment_method_options' => [
                'card' => [
                    'request_three_d_secure' => 'any'
                ]
            ],
            'receipt_email'          => $request['email'],
        ]);
        $response = $this->stripeGenerateResponse($intent, $request, $order);

        echo json_encode($response);
        exit;
    }

    public function stripeGenerateResponse($intent, $request = null, $order = null)
    {

        if ($intent->status == 'requires_action' && $intent->next_action->type == 'redirect_to_url') {
            # Tell the client to handle the action
            $response = [
                'error'                        => true,
                'requires_action'              => true,
                'payment_intent_client_secret' => $intent->client_secret,
                'data'                         => $intent
            ];
        } else {
            if ($intent->status == 'succeeded') {

                $order = ORM::for_table('orders')->where('invoiceNo', $intent->metadata->invoice)->find_one();
                if($order->id){
                    $this->cart->completePaymentOrder($order, 'stripe_cc', 'stripe_cc', $intent->id, null, $intent->receipt_email ?? null);
                }

                $response = [
                    'success' => true,
                    'data'    => $intent
                ];

            } else {
                $error_message = "";
                if(@$intent->last_payment_error->message){
                    $error_message = $intent->last_payment_error->message;
                }
                $response = [
                    'error' => true,
                    'data'  => $intent
                ];
            }
        }
        return $response;
    }

    public function stripeResponse($response)
    {
        \Stripe\Stripe::setApiKey($this->stripeSecret);

        if(@$response['setup_intent']){
            $intent = \Stripe\SetupIntent::retrieve(
                $response['setup_intent']
            );
            return $this->stripeSubscriptionResponse($intent);
        }

        $intent = \Stripe\PaymentIntent::retrieve(
            $response['payment_intent']
        );

        return $this->stripeGenerateResponse($intent);
    }

    protected function createSetupIntent()
    {
        \Stripe\Stripe::setApiKey($this->stripeSecret);
        $intent = \Stripe\SetupIntent::create([
            'payment_method_types' => ['card'],
            'payment_method_options' => [
                'card' => [
                    'request_three_d_secure' => 'any'
                ]
            ],
            //'return_url' => SITE_URL.'stripe/response'
        ]);
        $response = [
            'success' => true,
            'data'    => $intent
        ];
        echo json_encode($response);
        exit();
    }

    public function stripeUpdateSubscription($subscriptionId, $paymentMethodId)
    {
        // Create the Subscription
        $subscription = \Stripe\Subscription::update($subscriptionId, [
            'default_payment_method' => $paymentMethodId
        ]);

        return $subscription;
    }

    public function stripeSubscriptionResponse($intent)
    {

        if ($intent->status == 'requires_action' && $intent->next_action->type == 'redirect_to_url') {
            # Tell the client to handle the action
            $response = [
                'error'                        => true,
                'requires_action'              => true,
                'payment_intent_client_secret' => $intent->client_secret,
                'data'                         => $intent
            ];
        } else {
            if ($intent->status == 'succeeded') {
                $order = ORM::for_table('orders')->where('invoiceNo', $intent->metadata->invoice)->find_one();
                $subscription = $this->stripeUpdateSubscription($order->subscriptionID, $intent->payment_method);
                echo "<pre>";
                print_r($subscription);
                die;
                //
//                if($order->id){
//                    $this->cart->completePaymentOrder($order, 'stripe_cc', 'stripe_cc', $intent->id);
//                }

                $response = [
                    'success' => true,
                    'data'    => $intent
                ];

            } else {
                $error_message = "";
                if(@$intent->last_payment_error->message){
                    $error_message = $intent->last_payment_error->message;
                }
                $response = [
                    'error' => true,
                    'data'  => $intent
                ];
            }
        }
        return $response;
    }

    public function retrieveSubscription($subscriptionId){
        \Stripe\Stripe::setApiKey($this->stripeSecret);
        try {
            $subscription = \Stripe\Subscription::retrieve($subscriptionId);
        } catch(\Stripe\Exception\InvalidRequestException $exception)
        {
            return false;
        }

        return $subscription;
    }

    public function confirmSubscription()
    {
        $subscriptionId = $this->get['subscriptionId'];
        $method = $this->get['method'];
        $subscription = $this->subscriptions->getBySubscriptionID($subscriptionId);
        $subscription->status = 1;
        $subscription->save();

        $order = ORM::for_table('orders')->find_one($subscription->orderID);
        $this->cart->completePaymentOrder($order, $method, $method, $subscriptionId, $subscription->perMonthAmount);
        $response = [
            'success' => true
        ];

        echo json_encode($response);
        exit();
    }

    public function stripeWebhook(): array
    {
        $payload = @file_get_contents('php://input');
        $event = null;

        try {
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                if(@$invoice->subscription) {
                    $this->subscriptions->activateSubscriptionByStripeInvoice($invoice);
                }
                echo 'Received event type ' . $event->type;
                break;

            case 'invoice.payment_failed':
                $invoice = $event->data->object;
                if(@$invoice->subscription) {
                    $subscription = $this->subscriptions->getBySubscriptionID($invoice->subscription);
                    if($subscription->id){
                        $oldSubscriptionStatus = $subscription->status;

                        $subscription->status = $subscription->status == 3 ? 3 : 2;

                        if($subscription->status == "2") {
                            $subscription->set_expr("elapsedDate", "NOW()");
                        }

                        $subscription->save();

                        // Check user other active subscriptions
                        $otherSubscriptions = ORM::for_table('subscriptions')
                            ->where('accountID', $subscription->accountID)
                            ->where('status', 1)
                            ->where_gte('nextPaymentDate', date('Y-m-d'))
                            ->count();

                        if ($otherSubscriptions == 0){
                            if($subscription->isPremium == 1){
                                // Send email for Failed Subscription Payment
                                if($oldSubscriptionStatus == 1){
                                    $this->subscriptions->sendSubscriptionFailedNotification($subscription->id);
                                }

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

                    }
                }
                echo 'Received event type ' . $event->type;
                break;

            case 'checkout.session.completed':
                $invoice = $event->data->object;
                if(@$invoice->setup_intent) {
                    $setup_intent = $invoice->setup_intent;
                    \Stripe\Stripe::setApiKey($this->stripeSecret);

                    $intent = \Stripe\SetupIntent::retrieve($setup_intent);

                    \Stripe\Customer::update(
                        $intent->customer,
                        [
                            'invoice_settings' => ['default_payment_method' => $intent->payment_method],
                        ]
                    );

                    \Stripe\Subscription::update(
                        $intent->metadata->subscription_id,
                        [
                            'default_payment_method' => $intent->payment_method,
                        ]
                    );

                    $paymentMethod = \Stripe\PaymentMethod::retrieve($intent->payment_method);

                    $subscription = $this->subscriptions->getBySubscriptionID($intent->metadata->subscription_id);
                    if($subscription->id){
                        $subscription->customerID = $intent->customer;
                        $subscription->last4 = $paymentMethod->card->last4;
                        $subscription->save();
                    }
                }
                echo 'Received event type ' . $event->type;
                break;
            case 'payment_intent.succeeded':
                $intent = $event->data->object;
                $order = ORM::for_table('orders')
                    ->where('transactionID', $intent->id)
                    ->whereNotEqual('status', 'completed')
                    ->find_one();

                if(@$order->id) {
                    $this->cart->completePaymentOrder($order, 'stripe_cc', 'stripe_cc', $intent->id, null, $intent->receipt_email ?? null);
                }
                echo 'Received event type ' . $event->type;
                break;
            case 'payment_method.attached':
                $paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
                // Then define and call a method to handle the successful attachment of a PaymentMethod.
                // handlePaymentMethodAttached($paymentMethod);
                break;
            // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        http_response_code(200);
        exit();
    }
    public function processApplePay()
    {
        $request = $this->post;
        $accountID = CUR_ID_FRONT;

        if(empty($accountID)){
            $accountID = $this->cart->createAccount();
        }

        $_SESSION['user_username'] = $this->post["email"] ?? ORM::for_table('accounts')->find_one($accountID)->email;

        if(isset($this->get['cart']) && ($this->get['cart'] == 'assessment')) {
            $order = $this->cart->updateAssessmentOrder($accountID );
        }else{
            $order = $this->cart->updateCheckoutOrder($request, ORDER_ID, $accountID );
        }

        \Stripe\Stripe::setApiKey($this->stripeSecret);
        $amount = $order->total;
        $currency = $this->currency;
        $description = 'New Skills Academy Order '.$order->id;

        $intent = \Stripe\PaymentIntent::create([
            'amount'                 => $amount * 100,
            'description'            => $description,
            'currency'               => strtolower($currency),
            'metadata'               => ['invoice' => $order->invoiceNo],
            //'return_url'             => SITE_URL.'ajax?c=stripe&a=stripeResponse',
            //'return_url'             => SITE_URL.'stripe/response',
            'payment_method_options' => [
                'card' => [
                    'request_three_d_secure' => 'any'
                ]
            ],
            'receipt_email'          => $request['email'],
        ]);
        $order->transactionID = $intent->id;
        $order->save();
        echo json_encode([
            'success' => true,
            'clientSecret' => $intent->client_secret
        ]);
        exit();

    }
    public function confirmApplePay()
    {
        \Stripe\Stripe::setApiKey($this->stripeSecret);

        $paymentIntentID = $this->get['paymentIntent'];

        $intent = \Stripe\PaymentIntent::retrieve(
            $paymentIntentID
        );

        if ($intent->status == 'succeeded') {

            $order = ORM::for_table('orders')->where('transactionID', $intent->id)->find_one();
            if($order->id){
                if($this->get['cart'] && ($this->get['cart'] == 'assessment')){
                    $this->cart->completeAssessmentOrder($order, 'stripe_pay', 'stripe_pay', $intent->id, null, $intent->receipt_email ?? null);
                }else{
                    $this->cart->completePaymentOrder($order, 'stripe_pay', 'stripe_pay', $intent->id, null, $intent->receipt_email ?? null);
                }
            }

            $response = [
                'success' => true,
                'data'    => $intent
            ];

        } else {
            $error_message = "";
            if(@$intent->last_payment_error->message){
                $error_message = $intent->last_payment_error->message;
            }
            $response = [
                'error' => true,
                'data'  => $intent
            ];
        }

        echo json_encode($response);
        exit();
    }
    public function successPayment()
    {
        \Stripe\Stripe::setApiKey($this->stripeSecret);

        $paymentIntentID = $this->get['paymentIntent'];

        $intent = \Stripe\PaymentIntent::retrieve(
            $paymentIntentID
        );

        if ($intent->status == 'succeeded') {

            $order = ORM::for_table('orders')->where('transactionID', $intent->id)->find_one();
            if($order->id){
                if($this->get['cart'] && ($this->get['cart'] == 'assessment')){
                    $this->cart->completeAssessmentOrder($order, 'stripe_cc', 'stripe_cc', $intent->id, null, $intent->receipt_email ?? null);
                }else{
                    $this->cart->completePaymentOrder($order, 'stripe_cc', 'stripe_cc', $intent->id, null, $intent->receipt_email ?? null);
                }
            }

            $response = [
                'success' => true,
                'data'    => $intent
            ];

        } else {
            $error_message = "";
            if(@$intent->last_payment_error->message){
                $error_message = $intent->last_payment_error->message;
            }
            $response = [
                'error' => true,
                'data'  => $intent
            ];
        }

        echo json_encode($response);
        exit();
    }
    public function successGiftPayment()
    {
        \Stripe\Stripe::setApiKey($this->stripeSecret);

        $paymentIntentID = $this->get['paymentIntent'];

        $intent = \Stripe\PaymentIntent::retrieve(
            $paymentIntentID
        );

        if ($intent->status == 'succeeded') {

            $order = ORM::for_table('orders')->where('transactionID', $intent->id)->find_one();
            if($order->id){
                $this->gifts->completePayment($order, 'stripe_cc', 'stripe_cc', $intent->id);
            }

            $response = [
                'success' => true,
                'data'    => $intent
            ];

        } else {
            $error_message = "";
            if(@$intent->last_payment_error->message){
                $error_message = $intent->last_payment_error->message;
            }
            $response = [
                'error' => true,
                'data'  => $intent
            ];
        }

        echo json_encode($response);
        exit();
    }

    public function processGiftApplePay()
    {
        $request = $this->post;
        $order = $this->gifts->processPayment($request);

        \Stripe\Stripe::setApiKey($this->stripeSecret);
        $amount = $order->total;
        $currency = $this->currency;
        $description = 'New Skills Academy Order '.$order->id;

        $intent = \Stripe\PaymentIntent::create([
            'amount'                 => $amount * 100,
            'description'            => $description,
            'currency'               => strtolower($currency),
            'metadata'               => ['invoice' => $order->invoiceNo],
            //'return_url'             => SITE_URL.'ajax?c=stripe&a=stripeResponse',
            //'return_url'             => SITE_URL.'stripe/response',
            'payment_method_options' => [
                'card' => [
                    'request_three_d_secure' => 'any'
                ]
            ],
            'receipt_email'          => $request['email'],
        ]);
        $order->transactionID = $intent->id;
        $order->save();
        echo json_encode([
            'success' => true,
            'clientSecret' => $intent->client_secret
        ]);
        exit();

    }
    public function confirmGiftApplePay()
    {
        \Stripe\Stripe::setApiKey($this->stripeSecret);

        $paymentIntentID = $this->get['paymentIntent'];

        $intent = \Stripe\PaymentIntent::retrieve(
            $paymentIntentID
        );

        if ($intent->status == 'succeeded') {

            $order = ORM::for_table('orders')->where('transactionID', $intent->id)->find_one();
            if($order->id){
                $this->gifts->completePayment($order, 'stripe_pay', 'stripe_pay', $intent->id);
            }

            $response = [
                'success' => true,
                'data'    => $intent
            ];

        } else {
            $error_message = "";
            if(@$intent->last_payment_error->message){
                $error_message = $intent->last_payment_error->message;
            }
            $response = [
                'error' => true,
                'data'  => $intent
            ];
        }

        echo json_encode($response);
        exit();
    }


    // Process Subscription
    public function processSubscription($isNCFE = false, $isGift = false)
    {
        $request = $this->post;
        $accountID = CUR_ID_FRONT;
        $_SESSION['user_username'] = $this->post["email"];
        $subscriptionType = $this->post["subscriptionType"] ?? ($isGift ? 'gift' : null);
        $last4 = $this->post["last4"] ?? null;

        if(empty($accountID)){
            $accountID = $this->cart->createAccount();
        }

        $order = $this->cart->updateCheckoutOrder($request, ORDER_ID, $accountID );
        if($isNCFE === false) {
            $unitPrice = round($order->total / 4, 2);
        }else{
            $unitPrice = round($order->total, 2);
        }
        \Stripe\Stripe::setApiKey($this->stripeSecret);

        if($subscriptionType == 'premium'){
            $orderItem = ORM::for_table('orderItems')->where('orderID', ORDER_ID)->whereNotNull('premiumSubPlanID')->find_one();
            $planItem = ORM::for_table('premiumSubscriptionsPlans')->find_one($orderItem->premiumSubPlanID);
            // Create the Product
            $product = \Stripe\Product::create([
                'name' => 'New Skills Academy Order '.$order->id,
                'description' => 'New Skills Academy Order '.$order->id." Premium Subscriptions",
            ]);

        }else if($subscriptionType == 'gift'){
            $orderItem = ORM::for_table('orderItems')->where('orderID', ORDER_ID)->whereNotNull('voucherID')->find_one();
            $planItem = ORM::for_table('premiumSubscriptionsPlans')
                ->where('months', 12)->find_one();
            // Create the Product
            $product = \Stripe\Product::create([
                'name' => 'New Skills Academy UK Order '.$order->id,
                'description' => 'New Skills Academy UK Order '.$order->id." Premium Subscriptions Gift",
            ]);

        }else{
            // Create the Product
            $product = \Stripe\Product::create([
                'name' => 'New Skills Academy Order '.$order->id,
                'description' => 'New Skills Academy Order '.$order->id." subscriptions",
            ]);
        }


        // Create the Price
        $price = \Stripe\Price::create([
            'product' => $product->id,
            'unit_amount' => $unitPrice * 100,
            'currency' => $this->currency,
            "recurring[interval]" => 'month'
        ]);

        // Create the Customer
        $customer = \Stripe\Customer::create([
            'email' => $request['email'],
        ]);


        $paymentMethod = \Stripe\PaymentMethod::retrieve($request['paymentMethod'] ?? $request['stripeToken']);

        $paymentMethod->attach([
            'customer' => $customer->id,
        ]);

        $customer = \Stripe\Customer::update($customer->id,[
            'invoice_settings' => [
                'default_payment_method' => $paymentMethod->id,
            ],
        ]);

        $subscription = $this->stripeCreateSubscription($customer->id, $price->id, $isNCFE, $isGift);

        $subscriptionData = [
            'isPremium' => ($subscriptionType == 'premium' || $subscriptionType == 'gift') ? 1 : 0,
            'isNCFE' => $isNCFE == true ? '1' : '0',
            'isNSPay' => $subscriptionType == 'premium' ? 1 : 0,
            'accountID' => $accountID,
            'orderID' => $order->id,
            'premiumSubPlanID' => $planItem->id ?? null,
            'subscriptionID' => $subscription->id,
            'customerID' => $customer->id,
            'priceID' => $price->id,
            'productID' => $product->id,
            'perMonthAmount' => $unitPrice,
            'last4' => $request['last4'] ?? null,
            'status' => 0,
            'whenAdded' => date("Y-m-d H:i:s"),
            'whenUpdated' => date("Y-m-d H:i:s"),
        ];

        $s = $this->subscriptions->saveSubscription($subscriptionData);

        if($isGift){
            return $subscription;
        }

//        // Total Subscription Schedule
//        $this->subscriptions->createStripeSchedules($s->id, $unitPrice);

        echo json_encode([
            'success' => true,
            'subscriptionId' => $subscription->id,
            'clientSecret' => $subscription->latest_invoice->payment_intent->client_secret
        ]);
        exit();

    }
    public function stripeCreateSubscription($customerId , $priceId, $isNCFE = false, $isGift = false)
    {
        // Create the Subscription
        $createSubscription = [
            'customer' => $customerId,
            'items' => [
                [
                    'price' => $priceId,
                ],
            ],
            'payment_behavior' => 'default_incomplete',
            'expand' => ['latest_invoice.payment_intent'],
            'off_session' => TRUE
        ];
        if($isGift) {
            $createSubscription['trial_period_days'] = 365;
        }
        if($isNCFE == false && $isGift == false) {
            $createSubscription['cancel_at'] =  strtotime("+3 months 10 Days");
        }
        $subscription = \Stripe\Subscription::create($createSubscription);

        return $subscription;
    }
    // End Process Subscription

    // Process Premium Subscription
    public function processPreSubscription()
    {
        $request = $this->post;
        $accountID = CUR_ID_FRONT;
        $_SESSION['user_username'] = $this->post["email"];
        if(empty($accountID)){
            $accountID = $this->cart->createAccount();
        }

        $order = $this->cart->updateCheckoutOrder($request, ORDER_ID, $accountID );
        $unitPrice = $order->total;
        $orderItem = ORM::for_table('orderItems')->where('orderID', ORDER_ID)->whereNotNull('premiumSubPlanID')->find_one();
        $planItem = ORM::for_table('premiumSubscriptionsPlans')->find_one($orderItem->premiumSubPlanID);
        \Stripe\Stripe::setApiKey($this->stripeSecret);


        // Create the Product
        $product = \Stripe\Product::create([
            'name' => 'New Skills Academy Order '.$order->id,
            'description' => 'New Skills Academy Order '.$order->id." Premium Subscriptions",
        ]);



        // Create the Price
        $price = \Stripe\Price::create([
            'product' => $product->id,
            'unit_amount' => $unitPrice * 100,
            'currency' => $this->currency,
            "recurring" => [
                'interval' => 'month',
                'interval_count' => $planItem->months,
            ]
        ]);



        // Create the Customer
        $customer = \Stripe\Customer::create([
            'email' => $request['email'],
        ]);

        $paymentMethod = \Stripe\PaymentMethod::retrieve($request['paymentMethod']);

        try {
            $paymentMethod->attach([
                'customer' => $customer->id,
            ]);
        } catch (Exception $exception) {
            $message = $exception->getMessage() ?? 'Payment Failed, Please try again';
            $_SESSION['failed_payment_message'] = $message;
            echo json_encode([
                'error' => true,
                'message' => $message
            ]);
            exit();
        }

        $customer = \Stripe\Customer::update($customer->id,[
            'invoice_settings' => [
                'default_payment_method' => $paymentMethod->id,
            ],
        ]);

        $subscription = $this->stripeCreatePreSubscription($customer->id, $price->id, $orderItem->trialDays);

        $subscriptionData = [
            'isPremium' => 1,
            'accountID' => $accountID,
            'orderID' => $order->id,
            'premiumSubPlanID' => $orderItem->premiumSubPlanID,
            'subscriptionID' => $subscription->id,
            'customerID' => $customer->id,
            'priceID' => $price->id,
            'productID' => $product->id,
            'perMonthAmount' => $unitPrice,
            'trialDays' => $orderItem->trialDays,
            'last4' => $request['last4'] ?? null,
            'status' => 0,
            'whenAdded' => date("Y-m-d H:i:s"),
            'whenUpdated' => date("Y-m-d H:i:s"),
        ];
        $s = $this->subscriptions->saveSubscription($subscriptionData);

//        // Total Subscription Schedule
//        $this->subscriptions->createStripeSchedules($s->id, $unitPrice);

        echo json_encode([
            'success' => true,
            'status' => $subscription->status,
            'subscriptionId' => $subscription->id,
            'clientSecret' => $subscription->latest_invoice->payment_intent->client_secret
        ]);
        exit();

    }
    public function stripeCreatePreSubscription($customerId , $priceId, $trialDays = 0)
    {
        // Create the Subscription
        $subscription = \Stripe\Subscription::create([
            'customer' => $customerId,
            'items' => [
                [
                    'price' => $priceId,
                ],
            ],
            'payment_behavior' => 'default_incomplete',
            'expand' => ['latest_invoice.payment_intent'],
            'trial_period_days' => $trialDays,
            'off_session' => TRUE
        ]);

        return $subscription;
    }
    // End Process Premium Subscription

    // Process NCFE Subscription
    public function processNcfeSubscription()
    {
        $this->processSubscription(true);
    }
    public function stripeCreateNcfeSubscription($customerId , $priceId)
    {
        // Create the Subscription
        $subscription = \Stripe\Subscription::create([
            'customer' => $customerId,
            'items' => [
                [
                    'price' => $priceId,
                ],
            ],
            'payment_behavior' => 'default_incomplete',
            'expand' => ['latest_invoice.payment_intent'],
            'off_session' => TRUE
        ]);

        return $subscription;
    }
    // End Process NCFE Subscription

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
        if($subscription) {
            \Stripe\Stripe::setApiKey($this->stripeSecret);

            try {
                \Stripe\Subscription::update(
                    $subscription->subscriptionID,
                    [
                        'cancel_at_period_end' => true,
                    ]
                );
            }
            catch (Stripe\Exception\ApiErrorException $error) {
                // Since it's a decline, Stripe_CardError will be caught
                $errorStatus = $error->getHttpStatus();
            }


            $subscription->status = 3;
            $subscription->set_expr("churnDate", "NOW()");

            $subscription->save();

            // update Account
            $account = ORM::forTable('accounts')->findOne($subscription->accountID);
            if($account) {
                $account->subActive = '3';
                $account->save();
            }
            $response = [
                'error' => false,
                'success' => true
            ];
        }
        echo json_encode($response);
        exit();
    }
    public function updateSubscription()
    {
        $successUrl = SITE_URL.'dashboard/premium?tab=subscription&update_card=success';
        $cancelUrl = SITE_URL.'dashboard/premium?tab=subscription&update_card=cancel';
        $response = [
            'error' => true
        ];
        $request = $this->get;

        if($request['ncfe'] == 1) {
            $successUrl = SITE_URL.'dashboard/billing?tab=mySubscriptions&update_card=success';
            $cancelUrl = SITE_URL.'dashboard/billing?tab=mySubscriptions&update_card=cancel';
        }

        $subscription = ORM::forTable('subscriptions')
            ->where('accountID', CUR_ID_FRONT)
            ->where('id', $request['sid'])
            ->findOne();
        if($subscription) {
            \Stripe\Stripe::setApiKey($this->stripeSecret);

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'setup',
                'customer' => $subscription->customerID,
                'setup_intent_data' => [
                    'metadata' => [
                        'customer_id' => $subscription->customerID,
                        'subscription_id' => $subscription->subscriptionID,
                    ],
                ],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);

            $response = [
                'error' => false,
                'session' => $session,
                'success' => true,
            ];
        }
        echo json_encode($response);
        exit();
    }

    public function cardError()
    {
        $_SESSION['failed_payment_message'] = $_POST['error']['message'] ?? 'Something went wrong, Please try again!';
        $response = [
            'success' => true,
        ];
        echo json_encode($response);
        exit();
    }

    public function retrieveInvoice($invoiceId){
        \Stripe\Stripe::setApiKey($this->stripeSecret);
        try {
            $invoice = \Stripe\Invoice::retrieve($invoiceId);
        } catch(\Stripe\Exception\InvalidRequestException $exception)
        {
            return false;
        }

        return $invoice;
    }
}