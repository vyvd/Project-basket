<?php

//use Controller\paypalController;

require_once(__DIR__ . '/accountController.php');
require_once(__DIR__ . '/courseController.php');
require_once(__DIR__ . '/orderController.php');
require_once(__DIR__ . '/emailTemplateController.php');
require_once(__DIR__ . '/rewardsAssignedController.php');
require_once(__DIR__ . '/tescoController.php');
require_once(__DIR__ . '/moosendController.php');
require_once(__DIR__ . '/facebookBusinessSDKController.php');
//require_once(__DIR__ . '/paypalController.php');

class cartController extends Controller {

    /**
     * @var courseController
     */
    protected $course;


    /**
     * @var orderController
     */
    protected $orders;

    /**
     * @var accountController
     */
    protected $accounts;

    /**
     * @var emailTemplateController
     */
    protected $emailTemplates;

    /**
     * @var rewardsAssignedController
     */
    protected $rewardsAssigned;

    /**
     * @var tescoController
     */
    protected $tesco;

    /**
     * @var moosendController
     */
    protected $moosend;


    public function __construct()
    {
        $this->course = new courseController();
        $this->orders = new orderController();
        $this->accounts = new accountController();
        $this->emailTemplates = new emailTemplateController();
        $this->rewardsAssigned = new rewardsAssignedController();
        $this->tesco = new tescoController();
        $this->moosend = new moosendController();
        //$this->paypal = new paypalController();
        //$this->subscription = new subscriptionController();
        $this->facebook_api = new facebookBusinessSDKController();
        $this->post = $_POST;
        $this->get = $_GET;
    }

    private function addCartItem($orderID, $upsellItem = null, $isCustomBundle = null, $courseID = null) {

        $courseID = $this->post["courseID"] ?? $courseID;

        if(@$upsellItem) {
            // add upsell course item to the basket
            $course = $upsellItem;
            $course->price = $upsellItem->upsellCoursePrice;
        }else{
            // adds a single course item to the basket
            $course = ORM::for_table("courses")->find_one($courseID);

            $course->price = $this->getCoursePrice($course);

            if($course->id == "") {
                $this->setToastDanger("This course does not exist");
                exit;
            }

            // check if course assigned to account
            if(CUR_ID_FRONT != "") {

                $assigned = ORM::for_table("coursesAssigned")
                    ->where("accountID", CUR_ID_FRONT)
                    ->where("courseID", $course->id)
                    ->where("sub", "0")
                    ->count();

                if($assigned != 0) {
                    $this->setToastDanger("You cannot purchase ".$course->title." because it is already assigned to your account.");
                    exit;
                }

            }

            // affiliate pricing
            $excludedCourses = explode(",", $_SESSION["excludedCourses"]);
            if($_SESSION["affiliateDiscount"] != "" && !in_array($course->id, $excludedCourses)) {

                $discounted = $course->price;

                if($_SESSION["affiliateDiscountType"] == "fixed") {
                    $discounted = $discounted-$_SESSION["affiliateDiscount"];
                } else {
                    $discounted = $discounted * ((100-$_SESSION["affiliateDiscount"]) / 100);
                }

                if($_SESSION["affiliateDiscountMax"] != "") {

                    if($course->price <= $_SESSION["affiliateDiscountMax"]) {
                        $course->price = $discounted;
                    }

                } else if($_SESSION["affiliateDiscountMin"] != "") {

                    if($course->price >= $_SESSION["affiliateDiscountMin"]) {
                        $course->price = $discounted;
                    }

                } else {
                    $course->price = $discounted;
                }


            }
        }
        if($course->isNCFE == '0'){
            ORM::for_table("orderItems")->where("orderID", $orderID)->where("isNCFE", '1')->delete_many();
        }else{
            ORM::for_table("orderItems")->where("orderID", $orderID)->where("isNCFE", '0')->delete_many();
        }
        ORM::for_table("orderItems")->where("orderID", $orderID)->where_not_null("premiumSubPlanID")->delete_many();

        // see if this course already exists, if it does then increase qty, if not then add it
        $existing = ORM::for_table("orderItems")
            ->where("orderID", $orderID)
            ->where("courseID", $course->id)
            ->find_one();
        $currentItems = ORM::for_table("orderItems")->where("orderID", $orderID)->count();
        $canAdd = true;
        if(empty($existing->id)) {

            $item = ORM::for_table("orderItems")->create();

            $itemData = array(
                'orderID' => $orderID,
                'courseID' => $course->id,
                'price' => @$course->isNCFE ? (@$course->NCFEPriceMonth ? $course->NCFEPriceMonth : $course->price) : $course->price,
                'isCustomBundle' => $isCustomBundle ?? 0,
                'isNCFE' => $course->isNCFE
            );

            if(@$upsellItem){
                $itemData['isUpsell'] = 1;
                if($currentItems == 0){
                    $canAdd = false;
                }
            }

            $item->set($itemData);

            $item->set_expr("whenCreated", "NOW()");
            if($canAdd){
                $item->save();

                // Add printed item
                // if($course->sellPrinted == '1'){
                //     $this->addPrintedCourseItem($course->id, $orderID, $course->sellPrintedPrice);
                // }
            }

        } else {
            if(@$upsellItem){
                $existing->isUpsell = 1;
                $existing->qty = 1;
                $existing->price = $course->price;
            }else{
                $existing->qty = $existing->qty+1;
            }
            $existing->save();
        }


        $contents = [];
        $content_ids = [];


        $oldProductID = $course->productID;

        $fb_event_course_id = empty($oldProductID) ? $courseID : $oldProductID;

        if( empty($fb_event_course_id) || SITE_TYPE == 'us' ) {
            $fb_event_course_id = $course->id;
        }

        $content = [
            'content_id' => $fb_event_course_id,
            'content_title' => $course->title,
            //'categories' => implode(', ', $categories),
            'quantity' => 1,
            'price' => $course->price
        ];

        $content_ids[] = $fb_event_course_id;
        $contents[] = $content;

        $event_id = 'add_to_cart.'.$orderID.'.'.implode('', $content_ids);

        $addToCartEvent = facebookBusinessSDKController::createAddToCartEvent($contents, $content_ids);

        facebookBusinessSDKController::executeEvents(array($addToCartEvent));



    }

    public function getCouponDetails() {

        $order = ORM::for_table("orders")->find_one(ORDER_ID);

        if($order->couponID != "") {
            $coupon = ORM::for_table("coupons")->find_one($order->couponID);

            if($coupon->code != "" && ORDER_ID != "") {

                if($coupon->balance == "1") {
                    // if this is an account balance coupon, then show a custom message rather than the code
                    ?>
                    Existing Account Balance
                    <?php
                } else {
                    ?>
                    Coupon: <?= $coupon->code ?>
                    <?php
                }


                if($coupon->type == "v") {
                    ?><span class="discount_amount price_value">-<?= $this->price($coupon->value) ?></span><?php
                } else {
                    ?><span class="discount_amount percentage">-<?= $coupon->value ?>%</span><?php
                }
                ?>
                <a href="<?= SITE_URL ?>ajax?c=cart&a=remove-coupon">
                    <i class="fa fa-times" style="color:#ff0000;"></i>
                </a>
                <?php
            }
        }


    }

    public function applyCoupon() {

        $allowCode = true;
        $allowTwo = false;

        $currency = $this->currentCurrency();

        $coupon = ORM::for_table("coupons")
            //->where_raw('FIND_IN_SET(?,currencies)', array($currency->id))
            ->where("code", urldecode($this->get["code"]))
            ->find_one();

        // does the code exist
        if($coupon->code == "") {
            $this->setToastDanger("The code does not exist or is invalid...");
            $allowCode = false;
        }

        // check to see if it matches an affiliate
        $affiliate = ORM::For_table("ap_affiliate_voucher")->where("voucher_code", $coupon->code)->find_one();

        if($affiliate->aff_id != "") {

            // set affiliate session
            $_GET["refManual"] = $affiliate->aff_id;
            include(TO_PATH.'/affiliates/controller/affiliate-tracking-discount-code.php');

        }

        // if it has an expiry, are we past that date
        if($coupon->expiry != "") {
            if($coupon->expiry < date('Y-m-d H:i:s')) {
                // then its expired
                $this->setToastDanger("The code has expired.");
                $allowCode = false;
            }
        }

        // check if all have been used
        if($coupon->totalLimit != "") {
            if($coupon->totalUses >= $coupon->totalLimit) {
                // max uses have already happened
                $this->setToastDanger("The code does not exist or is invalid.");
                $allowCode = false;
            }
        }

        // check only for single user
        if($coupon->forUser != "") {
            if($coupon->forUser != CUR_ID_FRONT) {
                // max uses have already happened
                $this->setToastDanger("The code does not exist or is invalid.");
                $allowCode = false;
            }
        }

        // if all checks are passed, add coupon code to the order
        $order = ORM::for_table("orders")->find_one(ORDER_ID);

        if($order->id == "") {
            // user currently has no order
            $this->setToastDanger("You must add a course to your basket before attempting to add a coupon.");
            $allowCode = false;
        }

        $couponID = $coupon->id;
        $tesco = false;



        if($allowCode == false) {

            // try tesco
            $result = $this->tesco->validateCode(urldecode($this->get["code"]));

            if($result["Status"] == "Active") {

                $allowTwo = true;

                $existing = ORM::for_table("coupons")->where("code", urldecode($this->get["code"]))->find_one();

                if($existing->id != "") {
                    // already used
                    $allowCode = true;
                    $tesco = true;

                    if($this->tesco->redeemCode(urldecode($this->get["code"])) == true) {
                        // redeemed
                        echo "redeemed";
                    }

                } else {

                    // then we create the code
                    $item = ORM::for_table("coupons")->create();

                    $item->set(
                        array(
                            'code'       => urldecode($this->get["code"]),
                            'type'       => "v",
                            'value'      => $result["Value"],
                            'courses'    => "",
                            'totalLimit' => "2",
                            'expiry'     => "2099-01-01 11:59:59",
                            'applyTo'    => "basket",
                            'allowSub'    => "1",
                        )
                    );

                    $item->set_expr("whenUpdated", "NOW()");
                    $item->set_expr("whenAdded", "NOW()");

                    $item->save();

                    $couponID = $item->id();
                    $allowCode = true;
                    $tesco = true;

                    if($this->tesco->redeemCode(urldecode($this->get["code"])) == true) {
                        // redeemed
                        echo "redeemed";
                    }

                }



            } else {
                //$this->setToastDanger("The code does not exist or is invalid.");
                $allowCode = false;
            }


        }

        if($allowCode == true && $tesco == true) {

            // see if order contains a sub and discount it
            if(ORDER_ID != "") {
                $orderItem = ORM::for_table("orderItems")->where("orderID", ORDER_ID)->where_not_null("premiumSubPlanID")->find_one();

            }


        }

        if($allowTwo == true) {
            echo "allowTwo";
        }

        if (strpos($this->get["code"], 'TCC') !== false) {
            $tesco = true;
        }

        if($tesco == false) {
            if($_SESSION["refCodeInternal"] != "" && $allowTwo == false) {
                $this->setToastDanger("The code cannot be used with other offers.");
                $allowCode = false;
            }
        }

        if($allowCode == false) {
            exit;
        }
        $order->couponID = $couponID;

        $order->save();



        // coupon assigned to order, alert user and refresh order value
        $this->redirectJS(SITE_URL.'checkout');

    }

    public function applyCouponInternal($code, $definedOrder = "") {

        $apply = true;

        if($_SESSION["refCodeInternal"] != "") {
            $apply = false;
        }

        $currency = $this->currentCurrency();

        $orderID = ORDER_ID;
        if($definedOrder != "") {
            $orderID = $definedOrder;
        }

        $coupon = ORM::for_table("coupons")
            ->where_raw('FIND_IN_SET(?,currencies)', array($currency->id))
            ->where("code", urldecode($code))
            ->find_one();

        // does the code exist
        if($coupon->code == "") {
            $apply = false;
        }

        // if it has an expiry, are we past that date
        if($coupon->expiry != "") {
            if($coupon->expiry < date('Y-m-d H:i:s')) {
                // then its expired
                $apply = false;
            }
        }

        // check if all have been used
        if($coupon->totalLimit != "") {
            if($coupon->totalUses >= $coupon->totalLimit) {
                // max uses have already happened
                $apply = false;
            }
        }

        // check only for single user
        if($coupon->forUser != "") {
            if($coupon->forUser != CUR_ID_FRONT) {
                // max uses have already happened
                $apply = false;
            }
        }

        // if all checks are passed, add coupon code to the order
        $order = ORM::for_table("orders")->find_one($orderID);

        if($order->id == "") {
            // user currently has no order
            $apply = false;
        }

        if($apply == true) {
            $order->couponID = $coupon->id;

            $order->save();
        }


    }

    public function addCourse($courseID = null, $isCustomBundle = 0) {

        $orderID = ORDER_ID ?? ($_SESSION["orderID"] ?? null);

        $currency = $this->currentCurrency();

        if($orderID == "" || $orderID == "0") {


            // create order
            $order = ORM::for_table("orders")->create();

            $order->set(
                array(
                    'customerIP' => $this->getUserIP(),
                    'status' => 'cart'
                )
            );

            $order->set_expr("whenCreated", "NOW()");
            $order->set_expr("whenUpdated", "NOW()");
            $order->currencyID = $currency->id;
            $order->site = SITE_TYPE;

            if($_COOKIE["utm_source"] != "") {
                // if we have utm data then add it to the order
                $order->utm_source = $_COOKIE["utm_source"];
                $order->utm_medium = $_COOKIE["utm_medium"];
                $order->utm_campaign = $_COOKIE["utm_campaign"];
                $order->utm_term = $_COOKIE["utm_term"];
            }

            $order->save();

            $_SESSION["orderID"] = $order->id();

            // add automatic discount (rec. a friend)
            if($_SESSION["automaticCartDiscountCode"] != "") {
                $this->applyCouponInternal($_SESSION["automaticCartDiscountCode"], $_SESSION["orderID"]);
            }
            $orderID = $_SESSION["orderID"];

        } else {
            // add automatic discount (rec. a friend)
            if($_SESSION["automaticCartDiscountCode"] != "") {
                $this->applyCouponInternal($_SESSION["automaticCartDiscountCode"]);
            }
        }
        $this->addCartItem($orderID, null, $isCustomBundle, $courseID);

        // reach tracking code
        $reachCodes = array("370", "369", "368");
        if(in_array($_SESSION["refCodeInternal"], $reachCodes)) {
            ?>
            <!-- Conversion Pixel - Conversion - New Skills Academy - Add to Cart - DO NOT MODIFY -->
            <script src="https://secure.adnxs.com/px?id=1490331&seg=26796648&t=1" type="text/javascript"></script>
            <!-- End of Conversion Pixel -->
            <?php
        }

        // tiktok add to cart event
        if(TIKTOK_KEY_1 != "") {
            $course = ORM::for_table("courses")->select("price")->select("title")->find_one($this->post["courseID"]);

            // affiliate pricing
            $excludedCourses = explode(",", $_SESSION["excludedCourses"]);
            if($_SESSION["affiliateDiscount"] != "" && !in_array($course->id, $excludedCourses)) {

                $discounted = $course->price;

                if($_SESSION["affiliateDiscountType"] == "fixed") {
                    $discounted = $discounted-$_SESSION["affiliateDiscount"];
                } else {
                    $discounted = $discounted * ((100-$_SESSION["affiliateDiscount"]) / 100);
                }

                if($_SESSION["affiliateDiscountMax"] != "") {

                    if($course->price <= $_SESSION["affiliateDiscountMax"]) {
                        $course->price = $discounted;
                    }

                } else if($_SESSION["affiliateDiscountMin"] != "") {

                    if($course->price >= $_SESSION["affiliateDiscountMin"]) {
                        $course->price = $discounted;
                    }

                } else {
                    $course->price = $discounted;
                }


            }
            ?>
                <script>
                    ttq.identify({
                        external_id: '<?= CUR_ID_FRONT ?>',
                        email: '<?= CUR_EMAIL_FRONT ?>', // we will hash with sha-256
                        //phone_number: '+12133734253', // we will hash with sha-256
                    });

                    ttq.track('AddToCart', {
                        content_id: '<?= $this->post["courseID"] ?>',
                        content_type: 'product',
                        content_name: '<?= $course->title ?>',
                        quantity: 1,
                        price: '<?= $course->price ?>',
                        value: '<?= $course->price ?>',
                        currency: '<?= $currency->code ?>',
                    });
                </script>
                <?php
        }


        //echo "complete";

    }

    public function getMyOrder($id) {

        return ORM::for_table("orders")->where("accountID", CUR_ID_FRONT)->find_one($id);

    }

    public function getMyOrderItems($id) {

        return ORM::for_table("orderItems")->where("orderID", $id)->order_by_desc("id")->find_many();

    }

    public function currentCartTotal() {

        $price = $this->currentCartTotalAmount();
        return $this->price($price);
    }

    public function currentCartTotalAmount() {

        $order = ORM::for_table("orders")->find_one(ORDER_ID);

        if($order->id == "") {
            $price = 0.00;
        } else {

            // calculate price
            $price = 0;

            // all items, except certs and custom bundle items
            $items = ORM::for_table("orderItems")
                ->where_null("certID")
                ->where('isCustomBundle', 0)
                ->where("orderID", ORDER_ID)
                ->order_by_asc("id")
                ->find_many();


            $itemCount = 0;

            foreach($items as $item) {

                if($order->couponID != "" && $itemCount == 0) {
                    $coupon = ORM::for_table("coupons")->find_one($order->couponID);

                    if($coupon->applyTo != "basket") {

                        $takeOff = true;

                        if($coupon->valueMin != "") {
                            if($item->price < $coupon->valueMin) {
                                $takeOff = false;
                            }
                        }

                        if($item->premiumSubPlanID != "") {
                            $takeOff = false;
                        }

                        if($takeOff == true) {
                            if($coupon->type == "p") {
                                // percentage off

                                $item->price = $item->price * ((100-$coupon->value) / 100);

                            } else {
                                // value/money off

                                $item->price = $item->price-$coupon->value;

                            }
                        }

                    }

                }

                $price = $price+$item->price;

                $itemCount ++;
            }

            $containsSub = ORM::for_table("orderItems")
                ->where_not_null("premiumSubPlanID")
                ->where("orderID", ORDER_ID)
                ->count();


            // calculate discount (if any)
            if($order->couponID != "") {

                $coupon = ORM::for_table("coupons")->find_one($order->couponID);

                // if contains sub then make sure coupon is only allowSub == 1
                if($containsSub > 0) {




                    if($coupon->id != "") {
                        if ($coupon->allowSub == "1") {



                            if ($coupon->applyTo == "basket") { // discount entire basket

                                if ($coupon->type == "p") {
                                    // percentage off

                                    $price = $price * ((100 - $coupon->value) / 100);

                                } else {
                                    // value/money off
                                    $price = $price - $coupon->value;
                                }

                            }
                        }
                    }

                } else {

                    if($coupon->id != "") {

                        if($coupon->applyTo == "basket") { // discount entire basket
                            if($coupon->type == "p") {
                                // percentage off

                                $price = $price * ((100-$coupon->value) / 100);

                            } else {
                                // value/money off
                                $price = $price-$coupon->value;
                            }
                        }

                    }

                }



            }

            // only certs
            $certs = ORM::for_table("orderItems")->where_not_null("certID")->where("orderID", ORDER_ID)->find_many();

            // cert pricing
            if(count($certs) != 0) {
                // old per currency: $price = $price+$this->getSetting("cert_price_".count($certs));
                // new style cert pricing
                $currency = $this->currentCurrency();
                $certFieldName = "cert".count($certs);

                $price = $price+$currency->$certFieldName;

                // remove pricing at line item level
                foreach($certs as $cert) {

                    $certUpdate = ORM::for_table("orderItems")->find_one($cert->id);

                    // old style $certUpdate->price = number_format($this->getSetting("cert_price_".count($certs))/count($certs), 2); // average price
                    // new style cert pricing
                    $currency = $this->currentCurrency();
                    $certFieldName = "cert".count($certs);

                    $certUpdate->price = number_format($currency->$certFieldName.count($certs)/count($certs), 2);

                    $certUpdate->save();

                }

                if(count($certs) > 10) {

                    $price = $price+40;

                }

            }

            // Custom Bundle Price
            $customItems = ORM::for_table("orderItems")->where("isCustomBundle", 1)->where("orderID", ORDER_ID)->count();
            if($customItems >= 1) {
                $price = $price+59;
            }

            // VAT
            $vatDivisor = 1 + (VAT_RATE / 100);
            $priceBeforeVat = $price / $vatDivisor;
            $vatAmount = number_format($price - $priceBeforeVat, 2);

            // if price is less than 0 then set to 0
            if($price < 0) {
                $price = 0;
            }

            // save to order record
            $order->total = $price;

            // total GBP amount
            $currency = $this->currentCurrency();
            if($currency->code == "GBP") {
                $order->totalGBP = $price;
            } else {

                // need to convert to GBP value
                $order->totalGBP = number_format($price*$currency->convRate, 2);

            }

            $order->vatRate = VAT_RATE;
            $order->vatAmount = $vatAmount;

            $order->save();

        }


        return $price;

    }

    public function currentCartUnitPrice() {

        $order = ORM::for_table("orders")->find_one(ORDER_ID);

        if($order->id == "") {
            return $this->price("0.00");
        } else {

            // calculate price
            $price = 0;

            $items = $this->currentCartItems();
            $itemCount = 0;

            foreach($items as $item) {

                if($order->couponID != "" && $itemCount == 0) {
                    $coupon = ORM::for_table("coupons")->find_one($order->couponID);

                    if($coupon->applyTo != "basket" && ($coupon->valueMin != "" && $item->price > $coupon->valueMin)) {

                        if($coupon->type == "p") {
                            // percentage off

                            //$priceItem = $singleOrderItem->price * ((100-$coupon->value) / 100);

                            $item->price = $item->price * ((100-$coupon->value) / 100);

                        } else {
                            // value/money off
                            //$priceItem = $singleOrderItem->price-$coupon->value;

                            $item->price = $item->price-$coupon->value;

                        }

                    }

                }

                $price = $price+$item->price;

                $itemCount ++;
            }

            // calculate discount (if any)
            if($order->couponID != "") {
                $coupon = ORM::for_table("coupons")->find_one($order->couponID);

                if($coupon->id != "") {

                    if($coupon->applyTo == "basket") { // discount entire basket
                        if($coupon->type == "p") {
                            // percentage off

                            $price = $price * ((100-$coupon->value) / 100);

                        } else {
                            // value/money off
                            $price = $price-$coupon->value;
                        }
                    }

                }

            }

            // VAT
            $vatDivisor = 1 + (VAT_RATE / 100);
            $priceBeforeVat = $price / $vatDivisor;
            $vatAmount = number_format($price - $priceBeforeVat, 2);

            // save to order record
            $order->total = $price;
            $order->vatRate = VAT_RATE;
            $order->vatAmount = $vatAmount;

            $order->save();
            $price = round($price/4, 2);
            return $this->price($price);

        }

    }

    public function currentCartItems() {

        // returns all items in the current cart
        if(@$_GET['action'] && $_GET['action'] == 'json'){
            $contains = false;
            $isPrintedItems = false;
            $isQualificationItems = false;
            $premiumSubscription =  false;
            $ncfeSubscription =  false;
            $offer2Option =  true;
            $premiumMonths = 0;
            $printedItems = [];
            $printedItemsAll = [];
            $printedCoursePrice = '';
            $courseIDs = [];
            $newItems = [];
            $paypalPlanId = '';
            $customItems = ORM::for_table("orderItems")
                ->where('isCustomBundle', 1)
                ->where("orderID", ORDER_ID)
                ->order_by_asc("id")
                ->find_array();
            if(count($customItems) >= 1 ) {
                $item = [];

                foreach ($customItems as $item) {
                    $course = ORM::for_table("courses")->find_one($item['courseID']);
                    $cItems[] = $course->title;
                }
                $item['title'] = 'Custom Bundle: '. implode(', ', $cItems);
                $item['imageUrl'] = null;
                $item['price'] = $this->price(59);
                array_push($newItems, $item);
            }

            $items = ORM::for_table("orderItems")
                ->where('isCustomBundle', 0)
                ->where("orderID", ORDER_ID)
                ->order_by_asc("courseID")
                ->order_by_asc("printedCourse")
                ->order_by_asc("id")
                ->find_array();

            $isUpsell = false;
            if(count($items) >= 1 ){
                foreach($items as $item) {
                    $item['certNo'] = null;
                    $item['onlyPrinted'] = false;
                    if($item['course'] == "1") {
                        $course = ORM::for_table("courses")->find_one($item['courseID']);
                        $courseCategories = $this->course->getCourseCategories($course->id);
                        if(in_array('Qualifications', $courseCategories)){
                            $isQualificationItems = true;
                        }
                        $item['sellPrinted'] = $course->sellPrinted;
                        $item['sellPrintedPrice'] = $this->price($course->sellPrintedPrice);
                        if(@$item['isNCFE']){
                            $ncfeSubscription =  true;
                            $offer2Option =  false;
                        }
                    }else if(@$item['premiumSubPlanID']) {
                        $subscriptionPlan = ORM::for_table("premiumSubscriptionsPlans")->find_one($item['premiumSubPlanID']);
                        $premiumSubscription =  true;
                        $offer2Option =  false;
                        $premiumMonths = $subscriptionPlan->months;

                        if($item['price'] == $subscriptionPlan->price){
                            $paypalPlanId = $this->getPaypalSubscriptionPlan($subscriptionPlan->id)->paypalPlanID;
                        }else{
                            if(empty($currency)){
                                $currency = $this->currentCurrency();
                            }
                            $paypalSubcriptionPlan = ORM::for_table('paypal_subscription_plans')->find_one();
                            $paypalPlan = $this->createPaypalPlan([
                                'id'          => 'Subscription-Plan-'.$premiumMonths."-".$item['price']."-".$currency->code,
                                'product_id'  => $paypalSubcriptionPlan->paypalProductID,
                                'name'        => $premiumMonths." months ".$item['price']." Subscription Plan",
                                'description' => $premiumMonths." months ".$item['price']." Subscription Plan",
                                'price'       => $item['price'],
                                'currency'    => $currency->code,
                                'unit'        => 'MONTH',
                                'count'       => $premiumMonths,
                            ]);
                            if(@$paypalPlan['data']->id){
                                $paypalPlanId = $paypalPlan['data']->id;
                            }
                        }

                    }else{
                        $cert = ORM::for_table("coursesAssigned")->find_one($item['certID']);
                        $course = ORM::for_table("courses")->find_one($cert->courseID);
                        $item['certNo'] = $cert->certNo;
                    }

                    if(@$subscriptionPlan){
                        $item['title'] = $subscriptionPlan->title;
                        $item['price'] = $this->price($item['price']);
                        $item['planID'] = $this->price($item['id']);
                        $item['imageUrl'] = SITE_URL.'assets/images/subscriptionCheckoutThumb.png';
                        $item['productUrl'] = SITE_URL.'subscription';
                    } else {
                        $item['title'] = $course->title;
                        $item['imageUrl'] = $this->course->getCourseImage($course->id, "medium");
                        $item['productUrl'] = SITE_URL.'course/'.$course->slug;
                        $item['price'] = $this->price($item['price']);
                        $item['courseID'] = $course->id;
                        $item['productID'] = $course->productID;
                        $item['courseType'] = $this->course->getCourseType($course->id);
                        $categories = $this->course->getCourseCategories($course->id);
                        $item['categories'] = implode(', ', $categories);

                    }


                    if($item['certID'] != "") { // or printed course materials
                        $contains = true;
                    }
                    if($item['printedCourse'] == 1){
                        array_push($printedItems, $item['courseID']);
                        $printedCoursePrice = $this->price($item['price']);
                        if(!in_array($item['courseID'], $courseIDs)){
                            $item['onlyPrinted'] = true;
                            array_push($printedItemsAll, $item);
                        }
                    }else{
                        if(@$item['courseID']){
                            $courseIDs[] = $item['courseID'];
                        }
                        array_push($newItems, $item);
                    }

                    if($item['isUpsell'] == 1){
                        $isUpsell = true;
                    }
                    if($item['printedCourse'] == '1'){
                        $isPrintedItems = true;
                    }
                }
            }

            $totalPriceAmount = $this->currentCartTotalAmount();

            $unitPrice = $totalPriceAmount >= 1 ? round($totalPriceAmount/4,2) : 0.00;
            if(@$printedItemsAll){
                $newItems = array_merge($newItems, $printedItemsAll);
            }

            echo json_encode(array(
                'status'  => 200,
                'cartContainsCert'  => $contains,
                'items' => $newItems,
                'totalPriceAmount' => $totalPriceAmount,
                'totalPrice' => $this->price($totalPriceAmount),
                'unitPrice' => $this->price($unitPrice),
                'isUpsell' => $isUpsell,
                'premiumSubscription' => $premiumSubscription,
                'ncfeSubscription' => $ncfeSubscription,
                'offer2Option' => $offer2Option,
                'premiumMonths' => $premiumMonths,
                'isPrintedItems' => $isPrintedItems,
                'isQualificationItems' => $isQualificationItems,
                'printedCoursePrice' => $printedCoursePrice,
                'printedItems' => $printedItems,
                'printedItemsAll' => $printedItemsAll,
                'paypalPlanId' => $paypalPlanId
            ));
            exit;
        }
        $items = ORM::for_table("orderItems")->where("orderID", ORDER_ID)->order_by_asc("id")->find_many();

        return $items;
    }

    public function renderCartHeader() {

        ?>
        <a href="javascript:;" onclick="openNav()"><i class="fal fa-shopping-cart"></i> <?= count($this->currentCartItems()) ?> <span>item<?php if(count($this->currentCartItems()) != 1) { ?>s<?php } ?> - <?= $this->currentCartTotal(); ?></span></a>
        <?php

    }

    public function topCartCount() {

        $items = $this->currentCartItems();
        if(count($items) > 0) {
            ?>
            <span><?= count($items) ?></span>
            <?php
        }

    }

    public function renderCartSide() {

        $items = $this->currentCartItems();

        $currency = $this->currentCurrency();

        // reach tracking code
        $reachCodes = array("370", "369", "368");
        if(in_array($_SESSION["refCodeInternal"], $reachCodes)) {
            ?>
            <!-- Conversion Pixel - Conversion - New Skills Academy - View Basket - DO NOT MODIFY -->
            <script src="https://secure.adnxs.com/px?id=1490332&seg=26796650&t=1" type="text/javascript"></script>
            <!-- End of Conversion Pixel -->
            <?php
        }

        ?>
        <div class="modal-body">
            <a class="btn-close" data-dismiss="modal">X</a>
            <h1 class="text-center">Your Basket</h1>

            <?php
            foreach($items as $item) {
                $isCourseItem = 0;
                $isQualificationItem = 0;
                if(@$item->premiumSubPlanID){
                    $course = ORM::for_table('premiumSubscriptionsPlans')
                        ->find_one($item->premiumSubPlanID);
                    $course->productID = null;
                    $imploded_cats = null;
                    $courseType = null;
                    $course->title = str_replace("£99", $this->price($currency->prem12), $course->title);
                    $course->title = str_replace("£12", $this->price($currency->prem1), $course->title);
                    $title = $course->title;
                }else{
                    $course = ORM::for_table("courses")->find_one($item->courseID);
                    $categories = $this->course->getCourseCategories($course->id);
                    $imploded_cats = implode(', ', $categories);
                    $courseType = $this->course->getCourseType($course->id);
                    $isCourseItem = 1;
                    $title = $course->title;
                    if(in_array('Qualifications', $categories)){
                        $isQualificationItem = 1;
                    }
                }

                if($item->printedCourse != "1") { ?>
                    <div class="cart-items d-flex align-items-center" data-course-id="<?= $course->id ?>" data-oldproductid="<?= $course->productID ?>" data-course-cats="<?= $imploded_cats ?>" data-course_type="<?= $courseType ?>">
                        <div class="product-img">
                            <?php if(@$isCourseItem){ ?>
                                <img src="<?= $this->course->getCourseImage($course->id, "medium") ?>" style="height:75px;object-fit:cover;" />
                            <?php } else{?>
                                <img src="<?= SITE_URL?>assets/images/subscriptionCheckoutThumb.png" alt="Get UNLIMITED ACCESS to ALL COURSES for just £99 per/year">
                            <?php }?>
                        </div>

                        <div class="product-title align-self-md-center">
                            <p class="nsa_course_title">
                                <?= $title ?>
                                <?php
                                if($item->printedCourse == "1") {
                                    ?>
                                    <strong>Printed Materials</strong>
                                    <?php
                                }
                                ?>
                            </p>
                            <p><strong class="course_price"><?= $this->price($item->price) ?></strong></p>
                        </div>
                        <span class="remove-items" onclick="removeFromCartFooter(<?= $item->id ?>);">X</span>
                    </div>
                    <?php
                }
            }

            if(count($items) == 0) {
                ?>
                <p class="text-center"><em>There are currently no items in your cart.</em></p>
                <?php
            }
            ?>
        </div>

        <?php if($isQualificationItem == 0) { ?>
            <div class="sideOfferSection">
                <h3>Upgrade to get UNLIMITED ACCESS to ALL COURSES for only <?= $this->price($currency->prem12)?> per year</h3>
                <div class="btndiv">
                    <a href="<?= SITE_URL?>checkout?addSub=true" >Add Offer to Cart</a>
                </div>
                <p>Excludes language courses. No more than 50 active courses at any one time.
                    Membership renews after 12 months. Cancel anytime from your account. Can't be used in conjunction with any other offer.
                </p>
            </div>

            <div class="modal-body coupon">
                <p>Have a coupon code? Enter it here</p>
                <div class="input-group mb-3" style="display: none">
                    <input type="text" class="form-control" placeholder="Coupon" aria-label="Certificate ID" aria-describedby="basic-addon2" name="code" id="couponCodeFooter">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="applyCouponFooter();">Apply</button>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="modal-body totals text-right" data-total="<?= $this->currentCartTotal(); ?>">
            <p>Subtotal <?= $this->currentCartTotal(); ?></p>
            <div><?php $this->getCouponDetails() ?></div>
            <h2>Total <?= $this->currentCartTotal(); ?></h2>
            <button type="button" class="btn btn-primary extra-radius" onclick='window.location.href = "<?= SITE_URL ?>checkout";'>Proceed to Checkout</button>

            <br />
            <br />

            <p class="text-center">
                <a href="javascript:;" data-dismiss="modal">
                    Continue Browsing
                </a>
            </p>

        </div>

        <script>
            function applyCouponFooter() {

                var coupon = $("#couponCodeFooter").val();
                $( "#returnStatus" ).load( "<?= SITE_URL ?>ajax?c=cart&a=apply-coupon&code="+encodeURIComponent(coupon));

            }

            function removeFromCartFooter(id) {

                $("#returnStatus").load("<?= SITE_URL ?>ajax?c=cart&a=remove-item&id="+id);
                refreshCartSide();
                topCartSide();

            }
            $(".modal-body.coupon p").click(function (event){
                $(".modal-body.coupon .input-group").slideToggle();
                $(".modal-body.coupon .input-group input").focus();
            });
        </script>
        <?php

    }

    public function validateEmail() {
        $existing = ORM::for_table("accounts")->where("email", $this->get["email"])->find_one();
        $response = [
                'error' => false
        ];
        if($existing->id != "") {
            $response = [
                'error' => true,
                'message' => "An account already exists for this email address. Please sign in before proceeding or use a different email address."
            ];
        }
        echo json_encode($response);
        exit;
    }

    public function createAccount(array $input = null) {

        if($input){
            $this->post = $input;
        }

        // check if account exists
        $existing = ORM::for_table("accounts")->where("email", $this->post["email"])->find_one();

        if($existing->id != "") {
            $this->setToastDanger("An account already exists for this email address. Please sign in before proceeding or use a different email address.");
            exit;
        }

        $this->validatePassword($this->post["password"]);

        // actually create account
        $account = ORM::for_table("accounts")->create();

        $account->set(
            array(
                'firstname' => strip_tags($this->post["firstname"]),
                'lastname'  => strip_tags($this->post["lastname"]),
                'email'     => strip_tags($this->post["email"]),
                'password' => password_hash($this->post["password"], PASSWORD_BCRYPT)
            )
        );

        $currency = $this->currentCurrency();

        $account->set_expr("whenCreated", "NOW()");
        $account->set_expr("whenUpdated", "NOW()");
        $account->currencyID = $currency->id;

        $account->save();

        $accountID = $account->id();

        // Assign Default Register Reward
        $this->rewardsAssigned->assignReward($accountID, 'register', false, false);

        // send email confirming new account
        $message = '<p>Hi '.$this->post["firstname"].',</p>
        <p>Thank you for joining New Skills Academy. We hope you enjoy your course and get the chance to learn much more from our wide range of available courses.</p>
        
        <p>You can sign into your account at any time using your email address - '.$this->post["email"].' - and the password you set; so you are able to continue with your course(s), view progress, make notes, enroll onto other courses, and much more.</p>
       ';

        $message .= $this->renderHtmlEmailButton("My Courses", SITE_URL.'dashboard/courses');

        //$this->sendEmail($this->post["email"], $message, "Welcome to New Skills Academy");

        // sign them in
        $_SESSION['id_front'] = $accountID;

        $_SESSION['idx_front'] = base64_encode("g4p3h9xfn8sq03hs2234$accountID");

        //Added by Zubaer
        $_SESSION['nsa_email_front'] = $this->post["email"];

        $encryptedID = base64_encode("g4enm2c0c4y3dn3727553$accountID");
        ini_set("session.cookie_httponly", 1);
        setcookie("idCookieFront", $encryptedID, time()+60*60*24*100, "/");

        $_SESSION['user_new'] = 1;
        $_SESSION['user_password'] = $this->post["password"];

        // return the new account ID
        return $accountID;


    }

    private function assignCourse($course, $user, $moreColumns = null)
    {
        return $this->accounts->assignCourse($course, $user, $moreColumns);
    }

    private function addCouponUsage($couponID) {

        $coupon = ORM::for_table("coupons")->find_one($couponID);

        $coupon->totalUses = $coupon->totalUses+1;

        $coupon->save();
        if(($coupon->isReward == 1) && @$coupon->forUser){
            $rewardsAssigned = ORM::for_table("coupons")
                ->where('rewardID', $coupon->rewardClaimID)
                ->where('userID', $coupon->forUser)
                ->find_one();
            if(@$rewardsAssigned->id){
                $rewardsAssigned->claimed = 1;
                $rewardsAssigned->save();

                // Email to user on Redeem Reward


            }
        }

    }

    public function checkout() {

//        // check terms
//        if($this->post["terms"] != "1") {
//            $this->setToastDanger("You must accept the terms & conditions before continuing.");
//            exit;
//        }

        $items = $this->currentCartItems();
//        if(count($items) == 0) {
//            $this->setToastDanger("Your cart is empty, so there is nothing to checkout.");
//            exit;
//        }

//        if($this->ifCartContainsCert() == true) {
//
//            // check we have an address, etc entered
//            $this->validateValues(array("address1", "city", "country", "postcode"));
//
//        }

        // We need the gift email to be able to send it off
//        if($this->post["gift"] == "1") {
//
//            if($this->post["giftEmail"] == "") {
//                $this->setToastDanger("Please enter the email address of the person you are gifting this to.");
//                exit;
//            }
//
//        }

        if($this->post["xo"] == "1") {
            // @todo: form API link to XO Discounts
        }


        /*$stripe->charges->create([
            'amount' => $order->total*100,
            'currency' => 'gbp',
            'source' => $this->post["token"],
            'description' => '',
        ]);*/


        $this->validateValues(array("firstname", "lastname", "email"));

        $accountID = CUR_ID_FRONT;
        if(SIGNED_IN == false) {

            if ($this->post["email"] != $this->post["emailConfirm"] || !filter_var($this->post["email"], FILTER_VALIDATE_EMAIL)) {
                $this->setToastDanger("The email addresses you entered do not match, or your email address is not valid.");
                exit;
            }

            // create account
            $accountID = $this->createAccount();
        }

        if($this->post["newsletter"] == "1") {

            $newsletterCourse = $this->getNewsletterCourse();

            // assign free course
            $this->assignCourse($newsletterCourse->id, $accountID);


        }

        // update order details
        $order = ORM::for_table("orders")->find_one(ORDER_ID);

        if($this->ifCartContainsCert() == true) {

            $order->set(
                array(
                    'firstname' => $this->post["firstname"],
                    'lastname' => $this->post["lastname"],
                    'email' => $this->post["email"],
                    'method' => $this->post["method"],
                    'status' => 'completed', // p = paid
                    'accountID' => $accountID,
                    'address1' => $this->post["address1"],
                    'city' => $this->post["city"],
                    'postcode' => $this->post["postcode"],
                    'country' => $this->post["country"]
                )
            );

        } else {

            $order->set(
                array(
                    'firstname' => $this->post["firstname"],
                    'lastname' => $this->post["lastname"],
                    'email' => $this->post["email"],
                    'method' => $this->post["method"],
                    'status' => 'completed', // p = paid
                    'accountID' => $accountID
                )
            );

        }


        $order->set_expr("whenUpdated", "NOW()");

        // add usage to coupon if used
        if($order->couponID != "") {
            $this->addCouponUsage($order->couponID);
        }

        // mark as gifted if applicable
        if($this->post["gift"] == "1") {
            $order->gifted = "1";
        }

        // save order
        $order->save();

        // if order is gifted, then iterate through each item and add the data
        // tokens generated are used during the claim process
        foreach($items as $item) {

            // generate random token
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
            $charactersLength = strlen($characters);
            $token = '';
            for ($i = 0; $i < 20; $i++) {
                $token .= $characters[rand(0, $charactersLength - 1)];
            }


            $update = ORM::for_table("orderItems")->find_one($item->id);

            $update->giftEmail = $this->post["giftEmail"];
            $update->giftToken = $token;
            $update->gift = "1";

            if($this->post["giftDate"] != "") {

                $update->giftDate = date('Y-m-d', strtotime($this->post["giftDate"]));

            } else {
                $update->giftSent = "1";

                // now send the gift email
                $course = ORM::for_table("courses")->find_one($item->courseID);
                $link = SITE_URL.'?claim=gift&token='.$token.'&email='.urlencode($this->post["giftEmail"]);

                $message = '<p>Hi there,</p>
        <p>You have been gifted the <strong>'.$course->title.'</strong> course by <strong>'.$this->post["firstname"].' '.$this->post["lastname"].'</strong>.</p>
        
        <p>This course is already paid for, so you can take it at any time via New Skills Academy. All you need to do is click the Claim Gift button below and follow the steps.</p>
       ';

                $message .= $this->renderHtmlEmailButton("Claim Gift", $link);

                $this->sendEmail($this->post["giftEmail"], $message, "You've been gifted a course from ".$this->post["firstname"]." ".$this->post["lastname"]);
            }

            $update->save();

        }


        // send email notification to certificate printer if includes certificates
        foreach($items as $item) {

            if($item->certID != "") {
                // means it's a cert, so email printer person

                $message = '<p>Hi there,</p>
        <p>A certificate order has being placed. This needs to be shipping to the following address.</p>
        
        <p>
        '.$this->post["firstname"].' '.$this->post["lastname"].'<br />
        '.$this->post["address1"].'<br />
        '.$this->post["address2"].'<br />
        '.$this->post["address3"].'<br />
        '.$this->post["city"].'<br />
        '.$this->post["postcode"].'<br />
        '.$this->post["country"].'<br />
</p>
       ';

                $message .= $this->renderHtmlEmailButton("View Certificate PDF", SITE_URL.'ajax?c=certificate&a=cert-pdf&id='.$item->certID.'&adminKey=gre45h-56trh_434rdfng');

                $this->sendEmail($this->getSetting("cert_print_email"), $message, "A new printed certificate order from ".$this->post["firstname"]." ".$this->post["lastname"]);


            }

        }

        // send email notification to customer
        $message = '<p>Hi '.$this->post["firstname"].'</p>
        <p>Thank you for placing an order through New Skills Academy. Please see the below order confirmation.</p>
        
        <p><strong>Order Items:</strong></p><ul style="text-align:left;">';

        foreach($items as $item) {

            $gifted = "";

            if($this->post["gift"] == "1") {
                $gifted = "<em>Gifted</em>";
            }

            if($item->course == "1") {
                $course = ORM::for_table("courses")->find_one($item->courseID);

                if($item->printedCourse == "1") {
                    // show the fact these are printed materials
                    $course->title = $course->title.' <strong>Printed Material</strong>';
                }

                $message .= '<li><strong>'.$course->title.' '.$gifted.'</strong> - '.$this->price($item->price) .' (x'.$item->qty.')</li>';
            } else {
                $cert = ORM::for_table("coursesAssigned")->find_one($item->certID);
                $course = ORM::for_table("courses")->find_one($item->courseID);

                $message .= '<li><strong>'.$course->title.' <em>Cert: '.$cert->certNo.'</em></strong> - '.$this->price($item->price) .' (x'.$item->qty.')</li>';
            }

        }

        //$message .= '<li><strong>Order Total</strong> '.$this->currentCartTotal().'</li>';

        $message .= '</ul>'.$this->renderHtmlEmailButton("My Dashboard", SITE_URL.'dashboard');

        $this->sendEmail($this->post["email"], $message, "Your ".SITE_NAME." order summary: #".ORDER_ID);


        // add courses to account
        if($this->post["gift"] != "1") {
            // only assign if this is not a gifted order
            foreach($items as $item) {

                if($item->course == "1") { // some items might be printable certificates

                    // check if course is a bundle
                    $course = ORM::for_table("courses")->find_one($item->courseID);

                    if($course->childCourses != "") {

                        // assign main bundle
                        $assign = ORM::for_table("coursesAssigned")->create();

                        $assign->set(
                            array(
                                'courseID' => $course->id,
                                'accountID' => $accountID
                            )
                        );

                        $assign->set_expr("whenAssigned", "NOW()");

                        $assign->save();

                        $bundleID = $assign->id(); // get assigned bundle ID

                        foreach(json_decode($course->childCourses) as $child) {

                            // assign main bundle
                            $assignChild = ORM::for_table("coursesAssigned")->create();

                            $assignChild->set(
                                array(
                                    'courseID' => $child,
                                    'accountID' => $accountID,
                                    'bundleID' => $bundleID
                                )
                            );

                            $assignChild->set_expr("whenAssigned", "NOW()");

                            $assignChild->save();



                        }

                    } else {

                        $this->assignCourse($item->courseID, $accountID);

                    }

                }

            }
        }

        $this->redirectJS(SITE_URL.'checkout/confirmation');



    }

    public function validateOrderConfirmed() {

        // check to see if the current order is paid for
        $order = ORM::for_table("orders")->find_one(ORDER_ID);

        if($order->id == "" || $order->status == "c") {
            header('Location: '.SITE_URL.'checkout');
            exit;
        }

    }

    public function destroyOrderSession() {
        unset($_SESSION["orderID"]);
    }

    public function createIntent() {

        $setup_intent = \Stripe\SetupIntent::create([
            'usage' => 'off_session', // The default usage is off_session
        ]);


        ?>
        <!-- placeholder for Elements -->
        <div id="card-element"></div>
        <div style="height:15px;"></div>


        <script type="text/javascript">
            var stripe = Stripe('<?= STRIPE_PUBLIC ?>');

            var elements = stripe.elements();
            var cardElement = elements.create('card');
            cardElement.mount('#card-element');

            var cardholderName = document.getElementById('cardholder-name');
            var cardButton = document.getElementById('card-button');
            var clientSecret = cardButton.dataset.secret;

            cardButton.addEventListener('click', function(ev) {
                stripe.handleCardSetup(
                    clientSecret, cardElement, {
                        payment_method_data: {
                            billing_details: {name: cardholderName.value}
                        }
                    }
                ).then(function(result) {
                    if (result.error) {
                        toastr.options.positionClass = "toast-bottom-left";
                        toastr.options.closeDuration = 1000;
                        toastr.options.timeOut = 5000;
                        toastr.error('Please check all card details are correct and try again.', 'Oops');
                        //$("#returnStatus").load("<?= SITE_URL ?>ajax?c=card&a=failed-card");
                    } else {
                        console.log("success");
                        $("#returnStatus").load("<?= SITE_URL ?>ajax?c=card&a=attach-intent&id=<?= $setup_intent->id ?>");
                    }


                });
            });
        </script>
        <?php
    }

    public function attachIntent() {

        $account = ORM::for_table("savedIntents")->create();
        $user = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);

        $intent = \Stripe\SetupIntent::retrieve($this->get["id"]);

        $customer = \Stripe\Customer::create([
            'payment_method' => $intent->payment_method,
            'email' => $user->email,
            'description' => 'Username: '.$user->email.' - ID: '.$user->id,
        ]);

        $paymentMethod = \Stripe\PaymentMethod::retrieve($intent->payment_method);


        $account->last4 = $paymentMethod->card->last4;
        $account->userID = CUR_ID_FRONT;
        $account->set_expr("whenSaved", "NOW()");
        $account->stripeCustomerID = $customer->id;
        $account->stripeIntentID = $this->get["id"];
        $account->stripePaymentMethodID = $paymentMethod->id;

        $account->save();



    }

    public function getBasketUpsell() {

        $items = $this->currentCartItems();
        $upsellID = "";

        foreach($items as $item) {

            $course = ORM::for_table("courses")->find_one($item->courseID);

            if($course->upsellCourse != "") {
                $upsellID = $course->upsellCourse;
                $upsellCoursePrice = $course->upsellCoursePrice;
                $courseTitle = $course->title;
                break;
            }

        }

        if($upsellID == "") {
            $upsellCourse = ORM::for_table("courses")
                ->where('isDefaultUpsellCourse', 1)->find_one();
            if(@$upsellCourse->id){
                $upsellCourse->upsellCoursePrice = $upsellCourse->defaultUpsellCoursePrice;
                $upsellCourse->courseTitle = $course->title;
            }
        } else {
            $upsellCourse = ORM::for_table("courses")->find_one($upsellID);
            $upsellCourse->upsellCoursePrice = $upsellCoursePrice;
            $upsellCourse->courseTitle = $courseTitle;
        }

        //var_dump($upsellCourse);

        $upsellCourse->categories = '';

        $categories = $this->course->getCourseCategories($upsellCourse->id);
        if(!empty($categories)) {
            $upsellCourse->categories = implode(', ', $categories);
        }
        $upsellCourse->courseType = $this->course->getCourseType($upsellCourse->id);

        //var_dump($upsellCourse);

        return $upsellCourse;

    }
    public function addUpsellToCartJson(){
        $upsellCourse = $this->getBasketUpsell();
        if(@$upsellCourse->id){
            $this->addCartItem(ORDER_ID, $upsellCourse);
            $response = [
                'success' => true
            ];
        }else{
            $response = [
                'error' => true
            ];
        }
        echo json_encode($response);
        exit;
    }
    public function removeUpsellToCartJson(){
        $item = ORM::for_table("orderItems")
            ->where("orderID", ORDER_ID)
            ->where("isUpsell", 1)
            ->find_one();
        if(@$item->id){
            $item->delete();
        }
        $response = [
            'success' => true
        ];
        echo json_encode($response);
        exit;
    }

    private function addCertSingle($id, $order, $price = 0) {

        $item = ORM::for_table("orderItems")
            ->where('orderID', $order)
            ->where('certID', $id)
            ->find_one();
        if(empty($item)){
            $item = ORM::for_table("orderItems")->create();

            if($_COOKIE["utm_source"] != "") {
                // if we have utm data then add it to the order
                $order->utm_source = $_COOKIE["utm_source"];
                $order->utm_medium = $_COOKIE["utm_medium"];
                $order->utm_campaign = $_COOKIE["utm_campaign"];
                $order->utm_term = $_COOKIE["utm_term"];
            }

        }

        $currency = $this->currentCurrency();
        $certFieldName = "cert_1";

        $cert = ORM::for_table("coursesAssigned")->find_one($id);

        $item->set(
            array(
                'orderID' => $order,
                'certID' => $id,
                'course' => '0',
                'certNumber' => $cert->certNo,
                'price' => $price == 0 ? 0 : $currency->$certFieldName
            )
        );

        $item->set_expr("whenCreated", "NOW()");

        $item->save();

    }

    private function addPrintedCourseItem($id, $order, $price) {

        // Delete previous items
        $item = ORM::for_table("orderItems")
            ->where('orderID', $order)
            ->where('courseID', $id)
            ->where('printedCourse', '1')
            ->find_one();

        if(empty($item)){
            $item = ORM::for_table("orderItems")->create();
            $item->set(
                array(
                    'orderID' => $order,
                    'courseID' => $id,
                    'course' => '1',
                    'printedCourse' => '1',
                    'price' => $price
                )
            );
            $item->set_expr("whenCreated", "NOW()");
            $item->save();
        }
    }

    public function addCertificate() {

        $item = ORM::for_table("coursesAssigned")
            ->where("accountID", CUR_ID_FRONT)
            ->where("completed", "1")
            ->find_one($this->get["id"]);

        if($item->id == "") {
            header('Location: '.SITE_URL.'dashboard');
            exit;
        }

        if(ORDER_ID == "") {

            // create order
            $order = ORM::for_table("orders")->create();

            $order->set(
                array(
                    'customerIP' => $this->getUserIP(),
                )
            );

            $order->set_expr("whenCreated", "NOW()");
            $order->set_expr("whenUpdated", "NOW()");
            $order->site = SITE_TYPE;

            if($_COOKIE["utm_source"] != "") {
                // if we have utm data then add it to the order
                $order->utm_source = $_COOKIE["utm_source"];
                $order->utm_medium = $_COOKIE["utm_medium"];
                $order->utm_campaign = $_COOKIE["utm_campaign"];
                $order->utm_term = $_COOKIE["utm_term"];
            }

            $order->save();

            $_SESSION["orderID"] = $order->id();

            $this->addCertSingle($item->id, $_SESSION["orderID"]);

        } else {

            $this->addCertSingle($item->id, ORDER_ID);

        }

        header('Location: '.SITE_URL.'checkout?reload=1');

    }

    public function addPrintedCourse() {

        $course = ORM::For_table("courses")->find_one($this->get["id"]);

        if(ORDER_ID == "") {

            // create order
            $order = ORM::for_table("orders")->create();

            $order->set(
                array(
                    'customerIP' => $this->getUserIP(),
                )
            );

            $order->set_expr("whenCreated", "NOW()");
            $order->set_expr("whenUpdated", "NOW()");
            $order->site = SITE_TYPE;

            $order->save();

            $_SESSION["orderID"] = $order->id();

            $this->addPrintedCourseItem($course->id, $_SESSION["orderID"], $course->sellPrintedPrice);

        } else {

            $this->addPrintedCourseItem($course->id, ORDER_ID, $course->sellPrintedPrice);

        }


        if(isset($this->get["action"]) && ($this->get["action"] == 'json')){
            echo json_encode([
                    'success' => true
            ]);
            exit;
        }

        header('Location: '.SITE_URL.'checkout');

    }

    public function removePrintedCourse() {
        $courseID = $this->get["id"];
        $item = ORM::for_table("orderItems")
            ->where("orderID", ORDER_ID)
            ->where('courseID', $courseID)
            ->where('printedCourse', '1')
            ->find_one();
        if($item){
            $item->delete();
        }

        echo json_encode([
            'success' => true
        ]);
        exit;
    }

    public function ifContainsPrintedCourse($id) {

        $existing = ORM::for_table("orderItems")
            ->where("orderID", ORDER_ID)
            ->where("courseID", $id)
            ->where("printedCourse", "1")
            ->count();

        if($existing == 0) {
            return false;
        } else {
            return true;
        }

    }

    public function ifCartContainsCert() {

        $items = $this->currentCartItems();
        $contains = false;

        foreach($items as $item) {

            if($item->certID != "" || $item->printedCourse == "1") { // or printed course materials
                $contains = true;
            }

        }

        return $contains;

    }

    public function ifCartContainsSubscription() {

        // function to check if cart contains subscription

        $items = $this->currentCartItems();
        $contains = false;

        foreach($items as $item) {

            if($item->premiumSubPlanID != "") { // contains sub
                $contains = true;
            }

        }

        return $contains;

    }

    public function removeItem() {

        $order = ORM::for_table("orders")->find_one(ORDER_ID);

        $item = ORM::for_table("orderItems")
            ->where("orderID", ORDER_ID)
            ->find_one($this->get["id"]);

        if(@$item && $item->isCustomBundle == 1){
            ORM::for_table("orderItems")
                ->where("orderID", ORDER_ID)
                ->delete_many();
        }else{
            //Delete upsell from cart
            ORM::for_table("orderItems")
                ->where("orderID", ORDER_ID)
                ->where("isUpsell", 1)
                ->whereNotEqual('id', $this->get["id"])
                ->delete_many();

            //Delete printed version of the course from cart
            ORM::for_table("orderItems")
                ->where("orderID", ORDER_ID)
                ->where("courseID", $item->courseID)
                ->where("printedCourse", '1')
                ->whereNotEqual('id', $this->get["id"])
                ->delete_many();
        }


        if($order->offerID != "") {
            // delete all
            $item = ORM::for_table("orderItems")->where("orderID", ORDER_ID)->delete_many();

        } else {
            $item = ORM::for_table("orderItems")->where("orderID", ORDER_ID)->find_one($this->get["id"]);
            $item->delete();
        }

        if(@$_GET['action'] && $_GET['action'] == 'json') {
            $response = [
                'success' => true
            ];
            echo json_encode($response);
            exit();
        }

    }

    public function removeCoupon() {
        $item = ORM::for_table("orders")->find_one(ORDER_ID);

        if($item->id != "") {

            // check coupon to see if its an account balance one
            // if it is, then add balance to users account
            if(CUR_ID_FRONT != "") {

                $coupon = ORM::for_table("coupons")->find_one($item->couponID);

                if($coupon->balance == "1") {

                    $this->accounts->addBalance($coupon->value, "Balance discount removed from order #".ORDER_ID, CUR_ID_FRONT);

                }

            }

            $item->couponID = null;
            $item->save();

        }

        header('Location: '.SITE_URL.'checkout');

    }

    public function addOffer() {

        // collate courses into array
        $courses = explode(",", $this->post["courses"]);

        // remove empty values
        $courses = array_filter($courses);

        // get offer
        $offer = ORM::for_table("offerPages")->find_one($this->post["offerID"]);

        // create order
        $order = ORM::for_table("orders")->create();

        $order->set(
            array(
                'customerIP' => $this->getUserIP(),
                'offerID' => $offer->id,
            )
        );

        $order->set_expr("whenCreated", "NOW()");
        $order->set_expr("whenUpdated", "NOW()");
        $order->site = SITE_TYPE;

        if($_COOKIE["utm_source"] != "") {
            // if we have utm data then add it to the order
            $order->utm_source = $_COOKIE["utm_source"];
            $order->utm_medium = $_COOKIE["utm_medium"];
            $order->utm_campaign = $_COOKIE["utm_campaign"];
            $order->utm_term = $_COOKIE["utm_term"];
        }

        $order->save();

        $_SESSION["orderID"] = $order->id();


        $count = 0;
        foreach($courses as $course) {

            $price = 0;

            if($count == 0) {

                $price = $offer->course1Price;

                if(count($courses) > 1) {
                    $price = $offer->courseOtherPrice;
                }

            }

            $item = ORM::for_table("orderItems")->create();

            $item->set(
                array(
                    'orderID' => $_SESSION["orderID"],
                    'courseID' => $course,
                    'price' => $price
                )
            );

            $item->set_expr("whenCreated", "NOW()");

            $item->save();

            $count ++;

        }

        ?>
        <script>
            refreshCartTop();
            openNav();
            topCartSide();
        </script>
        <?php

    }

    public function addSubscriberCheckout($firstname, $email, $course) {

        $api_key = '81802e21-83f2-4ed2-b604-a31a463fb046';
        $list_id = '38794bb4-cbd8-4de0-bd6a-a30efa2689cf';

        $name = $firstname;
        $user_email = $email;

        $user = ORM::For_table("accounts")->where("email", $email)->find_one();

        $custom_fields = array();

        if($user->id != "") {


            if($course->id != "") {

                $custom_fields = array(
                    'first_course_id' => $course->id,
                    'first_course_name' => $course->title,
                    'user_id' => $user->id,
                    'user_first_name' => $user->firstname,
                    'user_last_name' => $user->lastname
                );

            }

        }

        $custom_fields_format_array = [];

        foreach ($custom_fields as $field_name => $field_value) {

            $custom_fields_format_array[] = $field_name.'='.$field_value;

        }

        //Add interests/course details to a user
        $data = array(
            'Name' => $name,
            'Email' => $user_email,
            "CustomFields" => $custom_fields_format_array
        );

        $ch = curl_init( "https://api.moosend.com/v3/subscribers/{$list_id}/subscribe.json?apikey={$api_key}" );

        $payload = json_encode( $data );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);

        return $result;

    }

    public function addSubscriptionMailingList($firstname, $email) {

        $api_key = '81802e21-83f2-4ed2-b604-a31a463fb046';
        $list_id = 'bd1251aa-6cd2-4fd1-9e5d-68fd97beb09a';

        $name = $firstname;
        $user_email = $email;

        $custom_fields_format_array = [];
        $custom_fields = array();

        // get account and check subscription plan
        $account = ORM::For_table("accounts")->where("email", $email)->find_one();

        $accountSub = ORM::for_table("subscriptions")
            ->where("accountID", $account->id)
            ->where('status', 1)
            ->where('isPremium', 1)
            ->whereNotNull('premiumSubPlanID')
            ->find_one();

        $accountSubPlan = ORM::for_table("premiumSubscriptionsPlans")
            ->find_one($accountSub->premiumSubPlanID);

        if($accountSubPlan->months == "") {
            $accountSubPlan->months = "12";
        }

        $custom_fields = array(
            'sub_type' => $accountSubPlan->months
        );


        foreach ($custom_fields as $field_name => $field_value) {

            $custom_fields_format_array[] = $field_name.'='.$field_value;

        }

        $data = array(
            'Name' => $name,
            'Email' => $user_email,
            "CustomFields" => $custom_fields_format_array
        );

        $ch = curl_init( "https://api.moosend.com/v3/subscribers/{$list_id}/subscribe.json?apikey={$api_key}" );

        $payload = json_encode( $data );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);

        return $result;

    }

    public function giftedOrder($order, $items = null){

        if($items == null){
            $items = $this->orders->getOrderItemsByOrderID($order->id);
        }

        // if order is gifted, then iterate through each item and add the data
        // tokens generated are used during the claim process
        foreach($items as $item) {

            // generate random token
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
            $charactersLength = strlen($characters);
            $token = '';
            for ($i = 0; $i < 20; $i++) {
                $token .= $characters[rand(0, $charactersLength - 1)];
            }


            $update = $item;

            $update->giftEmail = $order->giftEmail;
            $update->giftToken = $token;
            $update->gift = "1";

            if($order->giftDate != "") {

                $update->giftDate = date('Y-m-d', strtotime($order->giftDate));

            } else {

                // only send the gift if its not scheduled
                $update->giftSent = "1";

                // now send the gift email
                $course = ORM::for_table("courses")->find_one($item->courseID);
                $link = SITE_URL.'?claim=gift&token='.$token.'&email='.urlencode($order->giftEmail);

                $message = '<p>Hi there,</p>
        <p>You have been gifted the <strong>'.$course->title.'</strong> course by <strong>'.$order->firstname.' '.$order->lastname.'</strong>.</p>
        
        <p>This course is already paid for, so you can take it at any time via New Skills Academy. All you need to do is click the Claim Gift button below and follow the steps.</p>
       ';

                $message .= $this->renderHtmlEmailButton("Claim Gift", $link);

                $this->sendEmail($order->giftEmail, $message, "You've been gifted a course from ".$order->firstname." ".$order->lastname);

            }


            $update->save();

        }
    }
    public function completePaymentOrder($order, $method, $methodTitle, $transactionID = null, $unitPrice = null, $customerEmail = null)
    {
        // we need to requery from the DB otherwise it doesnt update
        $order = ORM::for_table("orders")->find_one($order->id);

        if($order->status == 'completed'){ // already completed
            return $order;
        }

        $thisOrderAccountID = $order->accountID;

        $assignCourseData = [];

        $subscription = ORM::forTable('subscriptions')
            ->where('orderID', $order->id)
            ->where('accountID', $order->accountID)
            ->where_not_null('subscriptionID')
            ->order_by_desc('whenAdded')
            ->find_one();

        if($subscription->id){
            $assignCourseData['subscriptionID'] = $subscription->subscriptionID;
            if($subscription->isNCFE == '1') {
                $assignCourseData['isNCFE'] = '1';
            }
        }

        $order->status = 'completed';
        $order->method = $method;
        $order->method_title = $methodTitle;

        if($method == "offline" && $order->total != 0 ){
            $this->moosend->placedOrderEvent($order);
            return $order;
        }

        // update customer total spend
        if($order->accountID != "") {
            $account = ORM::for_table("accounts")->find_one($order->accountID);

            $totalSpend = $account->totalSpend+$order->total;

            $account->totalSpend = $totalSpend;

            $account->save();

        }

        if(@$transactionID){
            $order->transactionID = $transactionID;
        }

        $orderDetails = $order->otherDetails;
        if(@$orderDetails){
            $details = json_decode($orderDetails);
            if(@$details->user_new){
                if($subscription->isNCFE == '1') {
                    $emailTemplate = $this->emailTemplates->getTemplateByTitle('order_successful_ncfe_new');
                }elseif(@$order->couponID && ($order->total == 0)){
                    $emailTemplate = $this->emailTemplates->getTemplateByTitle('order_coupon_successful_new');
                }else{
                    $emailTemplate = $this->emailTemplates->getTemplateByTitle('order_successful_new');
                }
            }else{
                if($subscription->isNCFE == '1') {
                    $emailTemplate = $this->emailTemplates->getTemplateByTitle('order_successful_ncfe');
                }elseif(@$order->couponID && ($order->total == 0)){
                    $emailTemplate = $this->emailTemplates->getTemplateByTitle('order_coupon_successful');
                }else{
                    $emailTemplate = $this->emailTemplates->getTemplateByTitle('order_successful');
                }
            }
            $order->otherDetails = null;
        }

        $order->set_expr("whenUpdated", "NOW()");

        $order->save();

        if(empty($order->accountID) && @$customerEmail) {
            $account = ORM::for_table('accounts')->where('email', $customerEmail)->find_one();
            if(@$account->id){
                $order->accountID = $account->id;
                if(empty($order->email)){
                    $order->email = $account->email;
                }
            }
            $order->save();
        }

        $orderItems = $this->orders->getOrderItemsByOrderID($order->id);

        if($order->couponID != "") {
            $this->addCouponUsage($order->couponID);
        }

        // If order is Gifted
        if($order->gifted == 1) {
            $this->giftedOrder($order, $orderItems);
        }

        if($order->newsletter == "1") {
            $newsletterCourse = $this->getNewsletterCourse();
            // assign free course
            $this->assignCourse($newsletterCourse->id, $order->accountID, $assignCourseData);
            $account = ORM::for_table('accounts')->find_one( $order->accountID);
            $this->moosend->addSubscriber($account->firstname, $account->email);
        }

        // send email notification to customer
        $userName = $details->user_name ?? '';
        $password = $details->user_password ?? '';
        $courseNames = [];
        $certNos = [];

        $isQualificationCourse = false;

        foreach($orderItems as $item) {
            $courseName = "";
            $isCourse = 0;
            if($item->course == "1") {
                $course = ORM::for_table("courses")->find_one($item->courseID);

                // if course is ncfe then set ncfe flag on their account

                if($course->isNCFE == "1") {
                    $account = ORM::for_table("accounts")->find_one($order->accountID);
                    $account->isNCFE = "1";
                    $account->save();
                }

                $courseName .= $course->title;
                if($item->printedCourse == "1") {
                    // show the fact these are printed materials
                    $courseName .= ' <strong>Printed Material</strong>';
                }
                if($order->gifted == "1") {
                    $courseName .= " <em>Gifted</em>";
                }
                $isCourse = 1;

                $courseCategories = $this->course->getCourseCategories($course->id);
                if (in_array('Qualifications', $courseCategories)) {
                    $isQualificationCourse = true;
                }
            }elseif (@$item->premiumSubPlanID) {
                $plan = ORM::for_table('premiumSubscriptionsPlans')->find_one($item->premiumSubPlanID);
                $courseName .= '<strong>'.$plan->title.' </strong>';
                $isCourse = 1;

                // add promo balance to their account if setting is turned on from subBalanceOfferAmount and sub is annual
                if($this->getSetting("subBalanceOfferActive") == "Yes" && $item->premiumSubPlanID == "3") {

                    $this->accounts->addBalance($this->getSetting("subBalanceOfferAmount"), "Subscription promotion", $thisOrderAccountID);

                }

                if($_SESSION["freeCredit"] == "true" && $item->premiumSubPlanID == "3") {

                    $this->accounts->addBalance("50", "Subscription promotion", $thisOrderAccountID);

                }

            } else {
                $cert = ORM::for_table("coursesAssigned")->find_one($item->certID);
                $course = ORM::for_table("courses")->find_one($item->courseID);
                $courseName .= '<strong>'.$course->title.' <em>Cert: '.$cert->certNo.'</em></strong>';
                $certNos[] = $cert->certNo;
            }
            $courseNames[] = $courseName;

        }

        $fromEmail = 'New Skills Academy <sales@newskillsacademy.co.uk>';
        if($isCourse == 0){
            $emailTemplate = $this->emailTemplates->getTemplateByTitle('certificate_order');
        }

        if($isQualificationCourse == true){
            $emailTemplate = $this->emailTemplates->getTemplateByTitle('qualification_order');
        }

        $variables = [
                '[FIRST_NAME]' => $order->firstname,
                '[LAST_NAME]' => $order->lastname,
                '[COURSE_NAME]' => implode(', ', $courseNames),
                '[USER_NAME]' => $_SESSION['user_username'],
                '[PASSWORD]' => $password
        ];
        $message = $emailTemplate->description;
        $subject = $emailTemplate->subject;
        foreach ($variables as $k=>$v){
            $message = str_replace($k, $v, $message);
            $subject = str_replace($k, $v, $subject);
        }

        $this->sendEmail($order->email, $message, $subject, $fromEmail);

        if($isQualificationCourse == true){
            $this->sendEmail("support@newskillsacademy.co.uk", $message, $subject, $fromEmail);
        }else{
            $this->sendEmail("sales@newskillsacademy.co.uk", $message, $subject, $fromEmail);
        }


        // Email Certificate Confirmation
        if($isCourse == 0){
            $emailTemplate = $this->emailTemplates->getTemplateByTitle('certificate_order_cs_team');
            $variables = [
                '[FIRST_NAME]' => $order->firstname,
                '[LAST_NAME]' => $order->lastname,
                '[CERT_NO]' => implode(', ',$certNos),
                '[ADDRESS]' => $order->address1.'<br />'.$order->address2.'<br />'.$order->address3.'<br />'.$order->city.'<br />'.$order->postcode.'<br />'.$order->country.'<br />',
            ];
            $message = $emailTemplate->description;
            $subject = $emailTemplate->subject;
            foreach ($variables as $k=>$v){
                $message = str_replace($k, $v, $message);
                $subject = str_replace($k, $v, $subject);
            }
            $toEmails = explode(',', $emailTemplate->toEmail);

            $this->sendEmail("sales@newskillsacademy.co.uk", $message, $subject, $fromEmail);
        }


        if($method == 'stripe_sub'){
            $assignCourseData['subscriptionID'] = $transactionID;

            // Email to user Instalment Plan
            $emailTemplate = $this->emailTemplates->getTemplateByTitle('instalments_schedules');

            $variables = [
                '[FIRST_NAME]' => $order->firstname,
                '[LAST_NAME]' => $order->lastname,
                '[COURSE_NAME]' => implode(', ', $courseNames),
                '[DATE_1]' => date("d/m/Y"),
                '[DATE_2]' => date('d/m/Y', strtotime("+1 months")),
                '[DATE_3]' => date('d/m/Y', strtotime("+2 months")),
                '[DATE_4]' => date('d/m/Y', strtotime("+3 months")),
                '[AMOUNT_1]' => $this->price($unitPrice),
                '[AMOUNT_2]' => $this->price($unitPrice),
                '[AMOUNT_3]' => $this->price($unitPrice),
                '[AMOUNT_4]' => $this->price($unitPrice),
                '[TOTAL_AMOUNT]' => $this->price($order->total),
            ];

            $message = $emailTemplate->description;
            $subject = $emailTemplate->subject;
            foreach ($variables as $k=>$v){
                $message = str_replace($k, $v, $message);
                $subject = str_replace($k, $v, $subject);
            }

            $this->sendEmail($order->email, $message, $subject, 'New Skills Academy <sales@newskillsacademy.co.uk>');
            unset($_SESSION['user_username']);

        }

        // subscribe user to moosend if they are a subscription customer
        if($method == 'stripe_pre_sub'){

            // subscribe user to moosend
            $this->addSubscriptionMailingList($order->firstname, $order->email);

        }

        // newsletter course ID
        $nCourse = array();
        // add courses to account
        if($order->gifted != "1") {
            // only assign if this is not a gifted order
            foreach($orderItems as $item) {

                if($item->course == "1") { // some items might be printable certificates

                    // check if course is a bundle
                    $course = ORM::for_table("courses")->find_one($item->courseID);

                    $nCourse = $course;

                    if($course->childCourses != "") {

                        // assign main bundle
                        $assign = $this->assignCourse($course->id, $order->accountID, $assignCourseData);

                        $bundleID = $assign->id(); // get assigned bundle ID

                        foreach(json_decode($course->childCourses) as $child) {
                            // assign main bundle
                            $assignCourseData['bundleID'] = $bundleID;
                            $this->assignCourse($child, $order->accountID, $assignCourseData);
                        }

                    } else {

                        $this->assignCourse($item->courseID, $order->accountID, $assignCourseData);

                        // Check if Personal Training Course Level 2 or Level 3
                        if(!empty($course) && ($course->personalTraningOrder == 1 || $course->personalTraningOrder == 2)) {
                            $newOrder = $course->personalTraningOrder == 2 ? 1 : 2;
                            $otherCourse = ORM::for_table('courses')->where('personalTraningOrder', $newOrder)->find_one();
                            $this->assignCourse($otherCourse->id, $order->accountID, $assignCourseData);
                        }
                    }

                }

            }
        }

        if($order->newsletter == "1") {
            // subscribe to moosend
            $this->addSubscriberCheckout($order->firstname, $order->email, $nCourse);

        }

        // send email notification to certificate printer if includes certificates
        foreach($orderItems as $item) {

            if($item->course != "1" && is_null($item->premiumSubPlanID)) {
                // means it's a cert, so email printer person

                $message = '<p>Hi there,</p>
        <p>A certificate order has being placed. This needs to be shipping to the following address.</p>
        
        <p>
                '.$order->firstname.' '.$order->lastname.'<br />
                '.$order->address1.'<br />
                '.$order->address2.'<br />
                '.$order->address3.'<br />
                '.$order->city.'<br />
                '.$order->postcode.'<br />
                '.$order->country.'<br />
        </p>
       ';

                $message .= $this->renderHtmlEmailButton("View Certificate PDF", SITE_URL.'ajax?c=certificate&a=cert-pdf&id='.$item->certID.'&adminKey=gre45h-56trh_434rdfng');
                mail("robbie@be-a.co.uk","Free Certificate",$message);
                // $this->sendEmail($this->getSetting("cert_print_email"), $message, "A new printed certificate order from ".$order->firstname." ".$order->lastname, $fromEmail);



            }

        }

        $this->moosend->placedOrderEvent($order);
        
        return $order;

    }

    public function updateCheckoutOrder($request, $orderID, $accountID = null)
    {

        $order = ORM::for_table("orders")->find_one($orderID);

        if($order->invoiceNo == "" || $order->invoiceNo == null){
            $invoice = "NSA".time().$order->id;
            $order->invoiceNo = $invoice;
        }

        if(@$accountID){
            $account = ORM::for_table('accounts')->find_one($accountID);

            // update with phone, if we have it
            if($request["phone"] != "") {
                $account->phone = $request["phone"];

                $account->save();

                // requery account
                $account = ORM::for_table('accounts')->find_one($accountID);
            }

            $_SESSION['user_username'] = $account->email;
        }

        $order->accountID = $accountID;
        $order->firstname = $request['firstname'];
        $order->lastname = $request['lastname'];
        $order->email = $request['email'];
        if(isset($request['postcode'])){
            $order->postcode = $request['postcode'];
        }
        $order->status = 'processing';


        if(@$request['gift']){
            $order->gifted = 1;
        }
        if(@$request['giftEmail']){
            $order->giftEmail = $request['giftEmail'];
        }
        if(@$request['giftDate']){
            $order->giftDate = date("Y-m-d",strtotime($request['giftDate'])) ;
        }
        if(@$request['newsletter']){
            $order->newsletter = 1;
        }

        if(@$request['address1']){
            $order->address1 = $request['address1'];
        }
        if(@$request['address2']){
            $order->address2 = $request['address2'];
        }
        if(@$request['address3']){
            $order->address3 = $request['address3'];
        }
        if(@$request['city']){
            $order->city = $request['city'];
        }
        if(@$request['county']){
            $order->county = $request['county'];
        }
        if(@$request['country']){
            $order->country = $request['country'];
        }
        if(@$request['postcode']){
            $order->postcode = $request['postcode'];
        }

        if(@$_SESSION['user_username']){
            $order->otherDetails = json_encode([
                    'user_new' => $_SESSION['user_new'] ?? 0,
                    'user_username' => $_SESSION['user_username'] ?? '',
                    'user_password' => $_SESSION['user_password'] ?? '',
            ]);
            unset($_SESSION["user_new"]);
            //unset($_SESSION["user_username"]);
            unset($_SESSION["user_password"]);
        }

        $order->set_expr("whenUpdated", "NOW()");

        $order->save();

        return $order;
    }

    public function addressFinder() {

        $this->get["number"] = urldecode($this->get["building"]);
        $this->get["postcode"] = str_replace(" ", "", urldecode($this->get["postcode"]));

        $json = file_get_contents('https://api.getaddress.io/find/'.$this->get["postcode"].'?expand=true&api-key='.GETADDRESSIO_KEY);

        $data = json_decode($json);


            ?>
            <div class="form-group">
                <select class="form-control" id="selectAddress">
                    <option value="">Select address...</option>
                    <?php
                    foreach($data->addresses as $address) {

                        ?>
                        <option><?= $address->line_1 ?>, <?= $address->town_or_city ?>, <?= $data->postcode ?>, <?= $address->country ?></option>
                        <?php

                    }
                    ?>
                </select>
            </div>
            <?php




        if($data->addresses[0]->thoroughfare == "") {
            ?>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <input type="text" class="form-control" name="address1" v-model="address1" placeholder="Address Line 1">
                </div>
                <div class="form-group col-md-6">
                    <input type="text" class="form-control" name="address2" v-model="address2" placeholder="Address Line 2">
                </div>
                <div class="form-group col-md-6">
                    <input type="text" class="form-control" name="address3" v-model="address3" placeholder="Address Line 3">
                </div>
                <div class="form-group col-md-6">
                    <input type="text" class="form-control" name="city" v-model="city" placeholder="Town / City">
                </div>
                <div class="form-group col-md-6">
                    <input type="text" class="form-control" name="postcode" v-model="postcode" placeholder="Post / Zip Code">
                </div>
                <div class="form-group col-md-6">
                    <input type="text" class="form-control" name="country" v-model="country" placeholder="Country" value="United Kingdom">
                </div>
            </div>
            <?php
        }

    }

    public function offlinePayment()
    {
        $request = $this->post;

        $accountID = CUR_ID_FRONT;

        if(empty($accountID)){
            $accountID = $this->createAccount();
        }

        $order = $this->updateCheckoutOrder($request, ORDER_ID, $accountID );
        $order = $this->completePaymentOrder($order, 'offline', 'offline',null, null, $this->post["email"] ?? null);

        $orderItem = ORM::for_table('orderItems')
            ->where('orderID', ORDER_ID)
            ->whereNotNull('premiumSubPlanID')
            ->find_one();

        if(@$orderItem->id){
            $nextPaymentDate = date("Y-m-d", strtotime("+". $orderItem->premiumSubPlanMonths." months"));

            // Add Subscription
            $subscription = ORM::for_table('subscriptions')->create();
            $subscription->isPremium = 1;
            $subscription->accountID = $accountID;
            $subscription->orderID = ORDER_ID;
            $subscription->premiumSubPlanID = $orderItem->premiumSubPlanID;
            $subscription->paymentMethod = 'offline';
            $subscription->nextPaymentDate = $nextPaymentDate;
            $subscription->status = 1;
            $subscription->save();


            $account = ORM::for_table('accounts')->find_one($accountID);
            $account->subActive = '1';
            $account->subExpiryDate = $nextPaymentDate;
            $account->save();
        }

        if($order->status == 'completed'){
            $response = [
                'success' => true
            ];
        }else{
            $response = [
                'error' => true
            ];
        }

        echo json_encode($response);
        exit();
    }

    public function orderFreeCertificate()
    {
        $request = $this->post;

        if(CUR_ID_FRONT == "") {
            $this->setAlertDanger("Something wrong!");
            exit;
        }
        if($request['address1'] == "") {
            $this->setAlertDanger("Please add Address!");
            exit;
        }

        $certificateCoupon = $this->accounts->getCertificateCoupon(CUR_ID_FRONT);
        if(empty($certificateCoupon) || (@$certificateCoupon && $certificateCoupon->totalUses == 1)) {
            $this->setAlertDanger("Coupon already has been redeemed!");
            exit;
        }
        $account = ORM::for_table('accounts')->find_one(CUR_ID_FRONT);

        // create order
        $order = ORM::for_table("orders")->create();

        $order->set(
            array(
                'email' => $account->email,
                'firstname' => $account->firstname,
                'lastname' => $account->lastname,
                'customerIP' => $this->getUserIP(),
                'accountID' => $account->id,
                'couponID' => $certificateCoupon->id,
                'address1' => $request['address1'],
                'address2' => $request['address2'],
                'address3' => $request['address3'],
                'city' => $request['city'],
                'postcode' => $request['postcode'],
                'county' => $request['county'],
                'total' => 0,
                'status' => 'processing'
            )
        );

        $order->set_expr("whenCreated", "NOW()");
        $order->set_expr("whenUpdated", "NOW()");
        $order->site = SITE_TYPE;

        $order->save();
        if($order->invoiceNo == "" || $order->invoiceNo == null){
            $invoice = "NSA".time().$order->id;
            $order->invoiceNo = $invoice;
        }
        $order->save();

        $this->addCertSingle($request['certificateID'], $order->id);
        $this->completePaymentOrder($order, 'offline', 'offline');

        // send email to customer services
        $cert = ORM::for_table("coursesAssigned")->find_one($request['certificateID']);

        $fromEmail = 'New Skills Academy <sales@newskillsacademy.co.uk>';
        $message = '
        <p>Hi there</p>
        <p>New certificate order received with the following details.<br /><br />

Certificate No: '.$cert->certNo.'<br /><br />

Address: '.$request['address1'].', '.$request['address2'].','.$request['address3'].', '.$request['city'].', '.$request['postcode'].', '.$request['county'].'<br /><br />

New Skills Academy Certification Team</p>
        ';

        $this->sendEmail("sales@newskillsacademy.co.uk", $message, "New reward certificate order", $fromEmail);

        $this->setAlertSuccess("Order has been successfully placed!");
        //        echo "<pre>";
        //        print_r($order);
        //        echo "</pre>";
        exit;
    }

    public function cartBundleCheckout()
    {
        $this->validateValues(array("cartBundle1", "cartBundle2", "cartBundle3"));

        if(($this->post['cartBundle1'] == $this->post['cartBundle2']) || ($this->post['cartBundle2'] == $this->post['cartBundle3']) || ($this->post['cartBundle1'] == $this->post['cartBundle3'])){
            $this->setToastDanger("Please select different courses");
            exit;
        }
        if(@$_SESSION["orderID"]){
            ORM::for_table('orderItems')->where('orderID',$_SESSION["orderID"])->delete_many();
        }

        // Delete the any affiliate session
        if(@$_SESSION["affiliateDiscount"]){
            unset($_SESSION["affiliateDiscount"]);
        }
        if(@$_SESSION["automaticCartDiscountCode"]){
            unset($_SESSION["automaticCartDiscountCode"]);
        }

        $this->addCourse($this->post['cartBundle1'], 1);
        $this->addCourse($this->post['cartBundle2'], 1);
        $this->addCourse($this->post['cartBundle3'], 1);

        $order = ORM::for_table('orders')->find_one($_SESSION["orderID"]);
        $order->isCustomBundle = 1;
        $order->save();
        $this->renderCartSide();
        echo '<script> window.location.href="'.SITE_URL.'checkout"</script>';
        exit();
    }

    protected function getCurrentOrderID()
    {
        $orderID = ORDER_ID ?? ($_SESSION["orderID"] ?? null);

        if($orderID == "" || $orderID == "0") {


            // create order
            $order = ORM::for_table("orders")->create();

            $order->set(
                array(
                    'customerIP' => $this->getUserIP(),
                    'status'     => 'cart'
                )
            );

            $order->set_expr("whenCreated", "NOW()");
            $order->set_expr("whenUpdated", "NOW()");
            $order->site = SITE_TYPE;

            if($_COOKIE["utm_source"] != "") {
                // if we have utm data then add it to the order
                $order->utm_source = $_COOKIE["utm_source"];
                $order->utm_medium = $_COOKIE["utm_medium"];
                $order->utm_campaign = $_COOKIE["utm_campaign"];
                $order->utm_term = $_COOKIE["utm_term"];
            }

            $order->save();

            $orderID = $order->id();
            $_SESSION["orderID"] = $orderID;



            // add automatic discount (rec. a friend)
            if ($_SESSION["automaticCartDiscountCode"] != "") {
                $this->applyCouponInternal($_SESSION["automaticCartDiscountCode"]);
            }

        }

        return $orderID;

    }

    public function addPremiumSubscription($planID = null, $trialDays = 0)
    {

        // gets the current currency
        $currency = $this->currentCurrency();

        $planID = $_GET['plan'] ?? $planID;
        $trialDays = $_GET['trial_days'] ?? $trialDays;

        $orderID = $this->getCurrentOrderID();
        $order = ORM::forTable('orders')->find_one($orderID);
        $order->couponID = null;
        $order->transactionID = null;
        $order->subscriptionID = null;
        $order->save();
        // Delete all Existing order items
        $tempItems = ORM::for_table('orderItems')
            ->where('orderID', $orderID)
            ->find_array();
        if(@$tempItems) {
            unset($_SESSION['temp_items']);
            $tItems = array();
            foreach ($tempItems as $cartItem) {
                if(empty($cartItem['premiumSubPlanID'])){
                    $tItems[] = $cartItem;
                }
                $dItem = ORM::forTable('orderItems')->find_one($cartItem['id']);
                $dItem->delete();
            }
            $_SESSION['temp_items'] = $tItems;
        }
        if(@$planID){
            $premiumSubscriptionPlan = ORM::for_table('premiumSubscriptionsPlans')->find_one($planID);
        }else{
            $premiumSubscriptionPlan = ORM::for_table('premiumSubscriptionsPlans')
                ->where('months', 12)->find_one();
        }

        $price = $currency->prem1;

        if($premiumSubscriptionPlan->months != "1") {
            $price = $currency->prem12;
        }

        $item = ORM::for_table('orderItems')->create();
        $item->orderID = $orderID;
        $item->premiumSubPlanID = $premiumSubscriptionPlan->id;
        $item->premiumSubPlanMonths = $premiumSubscriptionPlan->months;
        $item->price = $price;
        $item->whenCreated = date("Y-m-d H:i:s");
        $item->course = '0';
        $item->trialDays = $trialDays;
        $item->save();
        if($_GET['action'] && $_GET['action'] == 'json') {
            echo json_encode([
                    'item' => $item,
            ]);
            exit();
        }
        return $item;
    }
    public function removePremiumSubscription()
    {
        $orderID = $this->getCurrentOrderID();
        // Delete all Existing order items
        ORM::for_table('orderItems')
            ->where('orderID', $orderID)
            ->delete_many();
        if(@$_SESSION['temp_items']){
            $tItems = $_SESSION['temp_items'];
            foreach ($tItems as $newItem) {
                $item = ORM::for_table('orderItems')->create();
                unset($newItem['id']);
                $item->set($newItem);
                $item->save();
            }
            unset($_SESSION['temp_items']);
        }
        if($_GET['action'] && $_GET['action'] == 'json') {
            echo json_encode([
                'message' => true,
            ]);
            exit();
        }
    }

    public function getAssessmentCart()
    {
        return $_SESSION['assessmentCart'];
    }

    public function updateAssessmentCart()
    {
        $_SESSION['assessmentCart']['items'] = array_filter($_SESSION['assessmentCart']['items']);
        $_SESSION['assessmentCart']['total'] = count($_SESSION['assessmentCart']['items']) * $this->getAssessmentModulePrice();
        return $this->getAssessmentCart();
    }

    public function addAssessmentItem()
    {
        $assignmentId = $this->get['id'];
        $_SESSION['assessmentCart']['items'][] = $assignmentId;
        $_SESSION['assessmentCart']['items'] = array_values(array_unique($_SESSION['assessmentCart']['items']));
        $cart = $this->updateAssessmentCart();
        echo json_encode([
            'cart' => $cart,
            'message' => true,
            'status' => 200,
        ]);
        exit();
    }
    public function removeAssessmentItem()
    {
        $items = $_SESSION['assessmentCart']['items'];
        $assignmentId = $this->get['id'];
        if (($key = array_search($assignmentId, $items)) !== false) {
            unset($items[$key]);
        }
        $_SESSION['assessmentCart']['items'] = array_values(array_unique($items));
        $cart = $this->updateAssessmentCart();
        echo json_encode([
            'cart' => $cart,
            'message' => true,
            'status' => 200,
        ]);
        exit();
    }

    public function assessmentCartItems()
    {
        $cart = $this->getAssessmentCart();
        $total = count($cart['items']) * $this->getAssessmentModulePrice();
        $cartItems = [];
        if(count($cart['items']) >= 1){
            foreach ($cart['items'] as $item) {
                $assignment = ORM::for_table('accountAssignments')->find_one($item);
                if(@$assignment->id) {
                    $module = ORM::for_table('courseModules')->find_one($assignment->moduleID);
                    if($module->parentID){
                        $module = ORM::for_table('courseModules')->find_one($module->parentID);
                    }
                    $cartItems[] = [
                        'id' => $item,
                        'title' => $module->title,
                        'price' => $this->price($this->getAssessmentModulePrice())
                    ];

                }
            }

        }
        echo json_encode(array(
            'status'  => 200,
            'items' => array_values($cart['items']),
            'cartItems' => $cartItems,
            'totalItems' => count($cart['items']),
            'totalPriceAmount' => $total,
            'totalPrice' => $this->price($total),
        ));
        exit;
    }

    public function getAssessmentModulePrice()
    {
        return $this->getSetting('assessment_fee');
    }

    public function updateAssessmentOrder($accountID, $orderID = null, $request = null)
    {
        $cart = $this->getAssessmentCart();
        if(empty($orderID)){
            $orderID = $this->getCurrentOrderID();
        }
        $order = ORM::for_table("orders")->find_one($orderID);

        // Delete Items
        ORM::for_table('orderItems')->where('orderID', $orderID)->delete_many();

        // Add Items

        if(count($cart['items']) >= 1) {
            foreach ($cart['items'] as $cartItem){
                $item = ORM::for_table('orderItems')->create();
                $itemData = array(
                    'orderID' => $orderID,
                    'assignmentID' => $cartItem,
                    'course' => '0',
                    'price' => $this->getAssessmentModulePrice(),
                );
                $item->set($itemData);

                $item->set_expr("whenCreated", "NOW()");
                $item->save();
            }
        }

        if($order->invoiceNo == "" || $order->invoiceNo == null){
            $invoice = "NSA".time().$order->id;
            $order->invoiceNo = $invoice;
        }

        if(@$accountID){
            $account = ORM::for_table('accounts')->find_one($accountID);
            $_SESSION['user_username'] = $account->email;
        }


        $order->isAssessment = 1;
        $order->accountID = $accountID;
        $order->firstname = $account->firstname;
        $order->lastname = $account->lastname;
        $order->email = $account->email;
        $order->status = 'processing';

        // update with phone, if we have it
        if($request["phone"] != "") {
            $account->phone = $request["phone"];

            $account->save();

            // requery account
            $account = ORM::for_table('accounts')->find_one($accountID);
        }

        if(@$request['gift']){
            $order->gifted = 1;
        }
        if(@$request['giftEmail']){
            $order->giftEmail = $request['giftEmail'];
        }
        if(@$request['giftDate']){
            $order->giftDate = date("Y-m-d",strtotime($request['giftDate'])) ;
        }
        if(@$request['newsletter']){
            $order->newsletter = 1;
        }

        if(@$request['address1']){
            $order->address1 = $request['address1'];
        }
        if(@$request['address2']){
            $order->address2 = $request['address2'];
        }
        if(@$request['address3']){
            $order->address3 = $request['address3'];
        }
        if(@$request['city']){
            $order->city = $request['city'];
        }
        if(@$request['county']){
            $order->county = $request['county'];
        }
        if(@$request['country']){
            $order->country = $request['country'];
        }
        if(@$request['postcode']){
            $order->postcode = $request['postcode'];
        }



        if(@$_SESSION['user_username']){
            $order->otherDetails = json_encode([
                'user_new' => $_SESSION['user_new'] ?? 0,
                'user_username' => $_SESSION['user_username'] ?? '',
                'user_password' => $_SESSION['user_password'] ?? '',
            ]);
            unset($_SESSION["user_new"]);
            //unset($_SESSION["user_username"]);
            unset($_SESSION["user_password"]);
        }

        $order->set_expr("whenUpdated", "NOW()");

        $price = $cart['total'];
        // VAT
        $vatDivisor = 1 + (VAT_RATE / 100);
        $priceBeforeVat = $price / $vatDivisor;
        $vatAmount = number_format($price - $priceBeforeVat, 2);

        // if price is less than 0 then set to 0
        if($price < 0) {
            $price = 0;
        }

        // save to order record
        $order->total = $price;
        $order->vatRate = VAT_RATE;
        $order->vatAmount = $vatAmount;


        $order->save();

        return $order;
    }
    public function completeAssessmentOrder($order, $method, $methodTitle, $transactionID = null, $unitPrice = null, $customerEmail = null)
    {

        // we need to requery from the DB otherwise it doesnt update
        $order = ORM::for_table("orders")->find_one($order->id);

        $assignCourseData = [];


        $order->status = 'completed';
        $order->method = $method;
        $order->method_title = $methodTitle;

        if($method == "offline" && $order->total != 0 ){
            return $order;
        }

        // update customer total spend
        if($order->accountID != "") {
            $account = ORM::for_table("accounts")->find_one($order->accountID);

            $totalSpend = $account->totalSpend + $order->total;

            $account->totalSpend = $totalSpend;

            $account->save();

        }

        if(@$transactionID){
            $order->transactionID = $transactionID;
        }

        $order->otherDetails = null;
        $order->set_expr("whenUpdated", "NOW()");

        $order->save();

        $orderItems = $this->orders->getOrderItemsByOrderID($order->id);


        foreach($orderItems as $item) {
            if($item->assignmentID) {
                $assignment = ORM::for_table('accountAssignments')->find_one($item->assignmentID);
                if($assignment->id){
                    $assignment->status = 1;
                    $assignment->save();
                }
            }
        }

        // Send email notification to customer
        $emailTemplate = $this->emailTemplates->getTemplateByTitle('order_assessment_successful');
        $fromEmail = 'New Skills Academy <sales@newskillsacademy.co.uk>';

        $variables = [
            '[FIRST_NAME]' => $order->firstname,
            '[LAST_NAME]' => $order->lastname,
        ];
        $message = $emailTemplate->description;
        $subject = $emailTemplate->subject;
        foreach ($variables as $k=>$v){
            $message = str_replace($k, $v, $message);
            $subject = str_replace($k, $v, $subject);
        }

        $this->sendEmail($order->email, $message, $subject, $fromEmail);

        // Send email notification to Teacher/Tutor
        $emailTemplate = $this->emailTemplates->getTemplateByTitle('new_assigment_assigned_tutor');
        $fromEmail = 'New Skills Academy <sales@newskillsacademy.co.uk>';
        $account = ORM::for_table('accounts')->find_one($assignment->accountID);
        $tutor = ORM::for_table('accounts')->find_one($account->tutorID);
        if(@$tutor->id && @$account->id) {
            $course = ORM::for_table('course')->find_one($assignment->courseID);
            $module = ORM::for_table('courseModules')->find_one($assignment->moduleID);
            $variables = [
                '[TUTOR_FIRST_NAME]' => $tutor->firstname,
                '[TUTOR_LAST_NAME]' => $tutor->lastname,
                '[STUDENT_FIRST_NAME]' => $account->firstname,
                '[STUDENT_LAST_NAME]' => $account->lastname,
                '[COURSE_NAME]' => $course->title,
                '[MODULE_NAME]' => $module->title
            ];
            $message = $emailTemplate->description;
            $subject = $emailTemplate->subject;
            foreach ($variables as $k=>$v){
                $message = str_replace($k, $v, $message);
                $subject = str_replace($k, $v, $subject);
            }

            $this->sendEmail($tutor->email, $message, $subject, $fromEmail);
        }

        unset($_SESSION['assessmentCart']);
        unset($_SESSION["orderID"]);

        return $order;

    }

    public function assignmentBasket() {
        $cart = $this->getAssessmentCart();
    ?>
        <div class="text-center">
            <h4>Your Basket</h4>
            <div class="cart-items align-items-center">
                <?php if(@$cart['items']) { ?>
                    <p><?= count($cart['items']); ?> X Module Assessment</p>
                    <h5>Total to Pay - <?= $this->price($cart['total']); ?></h5>
                <?php } else { ?>
                    <p>Your cart is empty!</p>
                <?php } ?>
            </div>
        </div>
    <?php
    }

    public function applyBalanceDiscount() {


        if(CUR_ID_FRONT != "" && ORDER_ID != "") {

            $currentCart = $this->currentCartTotalAmount();

            $account = ORM::for_table("accounts")->select("balance")->find_one(CUR_ID_FRONT);

            $totalDiscount = $account->balance;

            // create coupon and apply to order
            $code = "AB".rand(1000,9999999).CUR_ID_FRONT;

            $coupon = ORM::for_table("coupons")->create();

            $coupon->forUser = CUR_ID_FRONT;
            $coupon->code = $code;
            $coupon->type = "v";
            $coupon->value = $totalDiscount;
            $coupon->totalLimit = "1";
            $coupon->set_expr("whenAdded", "NOW()");
            $coupon->set_expr("whenUpdated", "NOW()");
            $coupon->balance = "1";

            $coupon->save();


            $couponID = $coupon->id();

            // update order with new coupon
            $order = ORM::for_table("orders")->find_one(ORDER_ID);

            $order->couponID = $couponID;

            $order->save();

            // take account balance from user
            $this->accounts->removeBalance($totalDiscount, "Discounted from order #".ORDER_ID, CUR_ID_FRONT);


            $this->redirectJS(SITE_URL.'checkout');

        }

    }

    public function addRenewalDiscountedSubscription() {

        // adds a discounted subscription to the basket, used for marketing purposes
        $plan = ORM::for_table("premiumSubscriptionsPlans")->find_one(3);

        $newCharge = 75;

        if($this->get["discount"] == "true") {
            $newCharge = 50;
        }

        // get currency so we know which subscription price to charge
        $currency = $this->currentCurrency();

        if($currency->code == "USD") {
            $newCharge = 99;
        }

        // create new order
        $order = ORM::for_table("orders")->create();

        $order->set(
            array(
                'customerIP' => $this->getUserIP(),
                'status' => 'cart'
            )
        );

        $order->set_expr("whenCreated", "NOW()");
        $order->set_expr("whenUpdated", "NOW()");
        $order->site = SITE_TYPE;

        // add some additional tracking details
        $order->utm_source = "nsa";
        $order->utm_medium = SITE_TYPE;
        $order->utm_campaign = "";
        $order->utm_term = "renewal-upsell";

        $order->save();

        // set new order session
        $orderID = $order->id();
        $_SESSION["orderID"] = $orderID;

        // add subscription to this basket
        $item = ORM::for_table('orderItems')->create();
        $item->orderID = $orderID;
        $item->premiumSubPlanID = $plan->id;
        $item->premiumSubPlanMonths = $plan->months;
        $item->price = $newCharge;
        $item->whenCreated = date("Y-m-d H:i:s");
        $item->course = '0';
        $item->save();

        header('Location: '.SITE_URL.'checkout');

    }
    
    public function addMonthlyDiscountedSubscription() {

        // adds a discounted subscription to the basket, used for marketing purposes
        $plan = ORM::for_table("premiumSubscriptionsPlans")->find_one(4);

        $newCharge = 8.25;

        // create new order
        $order = ORM::for_table("orders")->create();

        $order->set(
            array(
                'customerIP' => $this->getUserIP(),
                'status' => 'cart'
            )
        );

        $order->set_expr("whenCreated", "NOW()");
        $order->set_expr("whenUpdated", "NOW()");
        $order->site = SITE_TYPE;

        // add some additional tracking details
        $order->utm_source = "nsa";
        $order->utm_medium = SITE_TYPE;
        $order->utm_campaign = "";
        $order->utm_term = "renewal-upsell";

        $order->save();

        // set new order session
        $orderID = $order->id();
        $_SESSION["orderID"] = $orderID;

        // add subscription to this basket
        $item = ORM::for_table('orderItems')->create();
        $item->orderID = $orderID;
        $item->premiumSubPlanID = $plan->id;
        $item->premiumSubPlanMonths = $plan->months;
        $item->price = $newCharge;
        $item->whenCreated = date("Y-m-d H:i:s");
        $item->course = '0';
        $item->save();

        header('Location: '.SITE_URL.'checkout');

    }

    public function addPostSaleUpsellSubscription() {

        // actually add upsell subscription to the cart, but first do some checks
        $order = ORM::for_table("orders")->where("accountID", CUR_ID_FRONT)->find_one($this->get["order"]);

        if($order->id != "") {

            $plan = ORM::for_table("premiumSubscriptionsPlans")->find_one(3);

            $newCharge = $plan->price-$order->total;

            // create new order
            $order = ORM::for_table("orders")->create();

            $order->set(
                array(
                    'customerIP' => $this->getUserIP(),
                    'status' => 'cart'
                )
            );

            $order->set_expr("whenCreated", "NOW()");
            $order->set_expr("whenUpdated", "NOW()");
            $order->site = SITE_TYPE;

            // add some additional tracking details
            $order->utm_source = "nsa";
            $order->utm_medium = SITE_TYPE;
            $order->utm_campaign = "";
            $order->utm_term = "thank-you-upsell";

            $order->save();

            // set new order session
            $orderID = $order->id();
            $_SESSION["orderID"] = $orderID;

            // add subscription to this basket
            $item = ORM::for_table('orderItems')->create();
            $item->orderID = $orderID;
            $item->premiumSubPlanID = $plan->id;
            $item->premiumSubPlanMonths = $plan->months;
            $item->price = $newCharge;
            $item->whenCreated = date("Y-m-d H:i:s");
            $item->course = '0';
            $item->save();


        }

        // redirect user back to checkout
        header('Location: '.SITE_URL.'checkout');
        exit;


    }

    public function addPostSaleUpsellBundle() {

        // actually add upsell bundle to the cart, but first do some checks
        $order = ORM::for_table("orders")->where("accountID", CUR_ID_FRONT)->find_one($this->get["order"]);

        if($order->id != "") {

            $assigned = ORM::for_table("coursesAssigned")->where("courseID", "25")->where("accountID", CUR_ID_FRONT)->count();

            if($assigned == 0) {

                // create new order
                $order = ORM::for_table("orders")->create();

                $order->set(
                    array(
                        'customerIP' => $this->getUserIP(),
                        'status' => 'cart'
                    )
                );

                $order->set_expr("whenCreated", "NOW()");
                $order->set_expr("whenUpdated", "NOW()");
                $order->site = SITE_TYPE;

                // add some additional tracking details
                $order->utm_source = "nsa";
                $order->utm_medium = "uk";
                $order->utm_campaign = "";
                $order->utm_term = "thank-you-upsell";

                $order->save();

                // set new order session
                $orderID = $order->id();
                $_SESSION["orderID"] = $orderID;

                // add item to this basket
                $item = ORM::for_table('orderItems')->create();
                $item->orderID = $orderID;
                $item->courseID = '25';
                $item->price = '15.00';
                $item->whenCreated = date("Y-m-d H:i:s");
                $item->course = '1';
                $item->save();

            } else {

                // create new order
                $order = ORM::for_table("orders")->create();

                $order->set(
                    array(
                        'customerIP' => $this->getUserIP(),
                        'status' => 'cart'
                    )
                );

                $order->set_expr("whenCreated", "NOW()");
                $order->set_expr("whenUpdated", "NOW()");
                $order->site = SITE_TYPE;

                // add some additional tracking details
                $order->utm_source = "nsa";
                $order->utm_medium = "uk";
                $order->utm_campaign = "";
                $order->utm_term = "thank-you-upsell";

                $order->save();

                // set new order session
                $orderID = $order->id();
                $_SESSION["orderID"] = $orderID;

                // add item to this basket
                $item = ORM::for_table('orderItems')->create();
                $item->orderID = $orderID;
                $item->courseID = '540';
                $item->price = '20.00';
                $item->whenCreated = date("Y-m-d H:i:s");
                $item->course = '1';
                $item->save();

            }

        }

        // redirect user back to checkout
        header('Location: '.SITE_URL.'checkout');
        exit;

    }

    public function renderPostSaleDiscounts() {

        // this function shows various discounts to the user after they have made a purchase

        // get currency so we know which subscription price to charge
        $currency = $this->currentCurrency();

        $containsSubscription = $this->ifCartContainsSubscription();
        $containsCert = $this->ifCartContainsCert();

        $orderTotal = $this->currentCartTotalAmount();
        $order = ORM::for_table("orders")->find_one(ORDER_ID);

        if($containsSubscription == false && $containsCert == false) {

            // then upsell annual subscription with the price being the subscription price minus what the customer spent
            $plan = ORM::for_table("premiumSubscriptionsPlans")->find_one(3);


            // this only happens if the amount they spent is not more than the subscription amount
            if($orderTotal < ($plan->price-5)) { // so we're not charging pennies

                // display offer
                $difference = $currency->prem12-$orderTotal;

                $dailyPenceCost = number_format($difference/365*100);

                ?>
                <div class="modal fade basket signIn" id="upsell" tabindex="-1" role="dialog" aria-labelledby="basketTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-body">


                                <div id="carouselUpsell" class="carousel slide" data-ride="false">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">

                                            <p><strong>Hey <?= $order->firstname ?>, an order confirmation was sent to <?= $order->email ?></strong></p>

                                            <p><strong>Before your begin your study we have some special offers for you...</strong></p>

                                            <p>Get 12 months access to all <?php if($currency->code == "GBP") { ?>700<?php } else { ?>300<?php } ?>+ of our CPD courses for only <?= $this->price($difference) ?> more. You will also get <?php if($currency->code == "GBP") { ?>an XO Student discounts membership,<?php } ?> access to CV builder tools, career personality matching, job board, study groups and more!</p>

                                            <p>That's unlimited access to our CPD course library for just <strong><?= $dailyPenceCost ?><?php if($currency->code == "GBP") { ?>p<?php } else { ?>c<?php } ?> per day</strong>.</p>

                                            <a href="<?= SITE_URL ?>ajax?c=cart&a=add-post-sale-upsell-subscription&order=<?= ORDER_ID ?>" class="upsellBtn">
                                                <i class="fas fa-check"></i>
                                                Yes Please
                                            </a>

                                            <a href="javascript:;" class="upsellBtn no" data-target="#carouselUpsell" data-slide="prev">
                                                No Thanks
                                            </a>

                                        </div>
                                        <div class="carousel-item">
                                            <?php
                                            // check if user has excel, if not then show that upsell. If they do, when show another
                                            $assigned = ORM::for_table("coursesAssigned")->where("courseID", "25")->where("accountID", CUR_ID_FRONT)->count();

                                            if($assigned == 0) {

                                                ?>
                                                <p><strong>How about the Microsoft Excel Bundle for only <?= $this->price("15") ?>?</strong></p>

                                                <p>Saving you an amazing <?= $this->price("165") ?> with this multi course bundle offer!</p>

                                                <a href="<?= SITE_URL ?>ajax?c=cart&a=add-post-sale-upsell-bundle&order=<?= ORDER_ID ?>" class="upsellBtn">
                                                    <i class="fas fa-check"></i>
                                                    Yes Please
                                                </a>

                                                <a href="javascript:;" class="upsellBtn no" data-dismiss="modal">
                                                    No Thanks
                                                </a>
                                                <?php

                                            } else {

                                                ?>
                                                <p><strong>How about the Ultimate Admin Diploma for only <?= $this->price("20") ?>?</strong></p>

                                                <p>Saving you an amazing <?= $this->price("220") ?> with this multi course bundle offer!</p>

                                                <a href="<?= SITE_URL ?>ajax?c=cart&a=add-post-sale-upsell-bundle&order=<?= ORDER_ID ?>" class="upsellBtn">
                                                    <i class="fas fa-check"></i>
                                                    Yes Please
                                                </a>

                                                <a href="javascript:;" class="upsellBtn no" data-dismiss="modal">
                                                    No Thanks
                                                </a>
                                                <?php

                                            }

                                            ?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    $( document ).ready(function() {
                        $("#upsell").modal("toggle");
                    });
                </script>
                <?php

            }



        }



    }

    public function ifCartContainsSubscriptionTrial() {

        if($this->ifCartContainsSubscription() == true) {

            $item = ORM::for_table("orderItems")
                ->where("orderID", ORDER_ID)
                ->where_not_equal("trialDays", "0")
                ->where_not_null("premiumSubPlanID")
                ->find_one();

            if($item->id == "") {

                return "0";

            } else {

                return $item->trialDays;

            }
        } else {
            return "0";
        }

    }

    public function ifCartContainsSubscriptionItem() {

        return ORM::for_table("orderItems")
            ->where("orderID", ORDER_ID)
            ->where_not_null("premiumSubPlanID")
            ->count();

    }

    public function getPaypalSubscriptionPlan($premiumSubscriptionsPlanID = 3, $currency = null)
    {
        if(empty($currency)){
            $currency = $this->currentCurrency();
        }

        $paypalSubcriptionPlan = ORM::for_table('paypal_subscription_plans')
            ->where('premiumSubscriptionsPlanID', $premiumSubscriptionsPlanID)
            ->where('currencyID', $currency->id)
            ->find_one();

        if(empty($paypalSubcriptionPlan)) {
            $paypalSubcriptionPlan = ORM::for_table('paypal_subscription_plans')
                ->find_one();

            $premiumSubscriptionsPlan = ORM::for_table('premiumSubscriptionsPlans')
                ->find_one($premiumSubscriptionsPlanID);

            $paypalPlan = $this->createPaypalPlan([
                'id'          => 'Subscription-Plan-'.$premiumSubscriptionsPlan->months."-".$currency->code,
                'product_id'  => $paypalSubcriptionPlan->paypalProductID,
                'name'        => $premiumSubscriptionsPlan->months." months Subscription Plan",
                'description' => $premiumSubscriptionsPlan->months." months Subscription Plan",
                'price'       => $premiumSubscriptionsPlan->price,
                'currency'    => $currency->code,
                'unit'        => 'MONTH',
                'count'       => $premiumSubscriptionsPlan->months,
            ]);

            if(@$paypalPlan['data']->id){
                $paypalSubcriptionPlan = ORM::for_table('paypal_subscription_plans')->create();
                $paypalSubcriptionPlan->premiumSubscriptionsPlanID = $premiumSubscriptionsPlanID;
                $paypalSubcriptionPlan->currencyID = $currency->id;
                $paypalSubcriptionPlan->paypalPlanID = $paypalPlan['data']->id;
                $paypalSubcriptionPlan->paypalProductID = $paypalPlan['data']->product_id;
                $paypalSubcriptionPlan->save();
            }
        }
        return $paypalSubcriptionPlan;
    }

}