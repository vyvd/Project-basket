<?php
$this->setControllers(array("course", "facebookBusinessSDK"));

//$this->controller->validateOrderConfirmed();


$order = $this->controller->getMyOrder(ORDER_ID);
$items = $this->controller->getMyOrderItems(ORDER_ID);

$categories = [];

if(empty(ORDER_ID)) {

    if(isset($_GET['order_id']) && !empty($_GET['order_id'])) {

        $order = $this->controller->getMyOrder($_GET['order_id']);
        $items = $this->controller->getMyOrderItems($_GET['order_id']);

        //var_dump($order->total);
        //var_dump($items);


    }

}


$order_id = ORDER_ID;

if(count($items) == 0 || ORDER_ID == "") {

    if(isset($_GET['order_id']) && !empty($_GET['order_id'])) {

        $order_id = $_GET['order_id'];

    } else {
        $this->force404(); // force 404 if the user doesnt have an order
    }


}



// redirect to friendly marketing url
if($_GET["request"] == "") { // if not already redirects

    foreach($items as $item) {

        if(@$item->courseID) {

            $course = ORM::for_table("courses")->select("slug")->find_one($item->courseID);

            header('Location: '.SITE_URL.'checkout/confirmation/'.$course->slug);
            exit;

        }elseif (@$item->premiumSubPlanID) {

            $plan = ORM::for_table("premiumSubscriptionsPlans")->find_one($item->premiumSubPlanID);

            if($plan->months == "1") {
                header('Location: '.SITE_URL.'checkout/confirmation/monthly-subscription');
                exit;
            } else if($plan->months == "6") {
                header('Location: '.SITE_URL.'checkout/confirmation/6-month-subscription');
                exit;
            } else if($plan->months == "12") {
                header('Location: '.SITE_URL.'checkout/confirmation/yearly-subscription');
                exit;
            }


        }

    }

}


$pageTitle = "Order Complete";
$css = array("checkout.css");
include BASE_PATH . 'header.php';
?>

<!-- Main Content Start-->
<main role="main" class="regular">

    <!--Checkout-->
    <section class="checkout">
        <div class="container wider-container">
            <div class="row">
                <div class="col-12 col-md-12">
                    <h1 class="section-title text-left">Order Completed</h1>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container wider-container">
            <div class="row">
                <div class="col-12 col-md-12 white-box checkout-thnkyou-box">


                    <?php

                    $grandTotal = $order->total;
                    $isQualificationCourse = false;
                    $subscriptionPrice = 0;

                    $coupon_used = 'no';

                    //var_dump($order);
                    //var_dump($order->couponID);

                    $couponController = new couponController();

                    if(!empty($order->couponID)) {
                        $coupon = $couponController->getCouponByID($order->couponID);
                        $coupon_name = $coupon->code;

                        //var_dump($this->controller->getCouponDetails());

                        if (!empty($coupon_name)) {
                            $coupon_used = 'yes';
                        }
                    }

                    $payment_method = 'Card';
                    if($order->method == 'paypal') {

                        $payment_method = 'Paypal';

                    } else if($order->method == 'offline') {

                        $payment_method = 'Offline';

                    }

                    $order_date = $order->whenCreated;

                    foreach($items as $item) {
                        if(@$item->courseID) {
                            $courseID = $item->courseID;
                            $categories = $this->course->getCourseCategories($courseID);
                            if (in_array('Qualifications', $categories)) {
                                $qualificationCourse = ORM::for_table("courses")->find_one($courseID);
                                $isQualificationCourse = true;
                            }
                        }
                    }

                    if($isQualificationCourse === true) {
                        ?>
                        <p class="bold">Thanks for your order of <?= $qualificationCourse->title ?>.</p>
                        <p class="bold">Your order will be processed and an account will be created by one of our student support assistants. We will email you once your course is active. Please allow up to 48 hours. If you haven’t heard from us after this time please contact us.</p>
                        <?php
                    }else{
                        ?>
                        <p class="bold">Thank you <?= $order->firstname ?>. Your order has been received.</p>
                        <?php
                    }
                    ?>

                    <div class="order-total-wrapper mb-4 order-details-outer row">
                        <div class="order-total-inner col-12 col-md-12 col-lg-12">

                            <div class="order-details">
                                <p class="coupon_codes hidden" style="display: none;"><?= $coupon_name ?? '' ?></p>
                                <p class="underlined"><strong><span>Order Details</span></strong></p>
                                <p>Order: <span class="nsa_order_number"><?= $order_id; ?></span></p>
                                <p>Email: <?= $order->email ?></p>
                                <p>Date: <?= $order_date ?></p>
                                <p>Total: <span class="order_total_amount"><?= $this->price($grandTotal); ?></span></p>
                                <p>Payment method: <?= $payment_method; ?></p>

                            </div>
                            <div class="order-invoice">
                                <a class="btn view-invoice" href="<?= SITE_URL ?>invoice/<?= $order->id ?>?id=<?= $order->id ?>" target="_blank">View Invoice</a>
                            </div>

                        </div>
                    </div>


                    <?php

                    //$grandTotal = 0;
                    $num_items = count($items);



                    //var_dump($items);


                    $courseID = '';

                    $item_ids = [];

                    $purchase_event_contents = [];
                    $purchase_event_content_ids = [];

                    foreach($items as $item) {

                        $isPrintMaterial = $item->printedCourse;

                        //$grandTotal += $item->price;
                        $isSubscriptions = false;
                        if(@$item->courseID) {
                            $courseID = $item->courseID;
                            $course = ORM::for_table("courses")->find_one($item->courseID);
                            $categories = $this->course->getCourseCategories($courseID);
                            if (in_array('Qualifications', $categories)) {
                                continue;
                            }

                            $courseID = $item->courseID;

                            $course = ORM::for_table("courses")->find_one($item->courseID);


                            $categories = $this->course->getCourseCategories($courseID);



                            $course_type = $this->course->getCourseType($courseID);

                            $course_id = $course->id;
                            $oldProductID = $course->productID;

                            $item_id = empty($oldProductID) ? $course_id : $oldProductID;
                            $item->title = $course->title;

                        }elseif (@$item->premiumSubPlanID) {

                            $plan = ORM::for_table("premiumSubscriptionsPlans")->find_one($item->premiumSubPlanID);
                            $isSubscriptions = true;

                            $subscriptionPrice = $item->price;

                            $item->title = "Unlimited Learning Subscription";//$plan->title;
                            $item_id = $plan->id;
                        }


                        $coupon_used = 'no';

                        //var_dump($order);
                        //var_dump($order->couponID);

                        $couponController = new couponController();

                        if(!empty($order->couponID)) {

                            $coupon = $couponController->getCouponByID($order->couponID);
                            $coupon_name = $coupon->code;

                            //var_dump($this->controller->getCouponDetails());

                            if (!empty($coupon_name)) {
                                $coupon_used = 'yes';
                            }
                            
                            if ($coupon_used = 'yes') {
                                if ($coupon->applyTo == 'basket') {
                                    if ($coupon->type == 'p') {

                                        $course_price = $item->price * ((100 - $coupon->value) / 100);
    
                                    } else {

                                        $course_price = $item->price - $coupon->value;
                                    }
    
                                } else if(!empty($coupon->includeCourses)) {
                                    if(in_array($course_id, explode(',', $coupon->includeCourses))) {

                                        if ($coupon->type == 'p') {

                                            $course_price = $item->price * ((100 - $coupon->value) / 100);
        
                                        } else {
    
                                            $course_price = $item->price - $coupon->value;
                                        }

                                    }
                                }
                            }
                        }
                


                        $course_id = $course->id;
                        $course_old_id = $course->oldID;
                        $oldProductID = $course->productID;


                        $item_id = empty($oldProductID) ? $course_id : $oldProductID;

                        $item_ids[] = $item_id;


                        if($item->course != "1" && ($isSubscriptions == false)) {
                            $cert = ORM::for_table("coursesAssigned")->find_one($item->certID);

                            $course = ORM::for_table("courses")->find_one($cert->courseID);
                        }

                        $premiumSubPlanID = $item->premiumSubPlanID;



                        $purchase_content = [
                            'content_id' => $item_id,
                            'content_title' => $item->title,
                            'quantity' => 1,
                            'price' => $course_price ?? $item->price,
                            'categories' => implode(', ', $categories)
                        ];

                        if(SITE_TYPE == 'us') {
                            $purchase_content['content_id'] = $course_id;
                        }

                        if(SITE_TYPE == 'us') {
                            $purchase_event_content_ids[] = $course_id;
                        } else {
                            $purchase_event_content_ids[] = $item_id;
                        }

                        $purchase_event_contents[] = $purchase_content;

                        ?>
                        <div class="order-details-outer mt-4 thankyou-order-item row">
                            <div class="col-12 col-md-4 col-lg-3 order-img">
                                <?php if($course->id){ ?><img src="<?= $this->course->getCourseImage($course->id, "medium") ?>" alt="order"><?php } ?>
                            </div>

                            <div class="col-12 col-md-8 col-lg-9 order-details">
                                <p class="nsa_course_title"><?= $item->title ?></p>
                                <p class="coupon_codes hidden" style="display: none;"><?= $coupon_name  ?? '' ?></p>
                                <p class="old_product_id hidden" style="display: none;"><?= $oldProductID ?></p>
                                <p class="course_id hidden" style="display: none;"><?= $course_id ?></p>
                                <p class="course_old_id hidden" style="display: none;"><?= $course_old_id ?></p>
                                <p class="item_id hidden" style="display: none;"><?= $item_id ?></p>
                                <p class="premium_subscription_id hidden" style="display: none;"><?= $premiumSubPlanID ?></p>
                                <p class="course_cats hidden" style="display: none;"><?= implode(', ', $categories) ?></p>
                                <p class="course_type hidden" style="display: none;"><?= $course_type ?></p>
                                <p class="is_print_material hidden" style="display: none;"><?= $isPrintMaterial ?></p>
                                <!--<p class="underlined"><strong><span>Details</span></strong></p>-->
                                <!--<p>Order: <span class="nsa_order_number">--><?//= //$item->orderID ?><!--</span></p>-->
                                <!--<p>Email: --><?//= //$order->email ?><!--</p>-->
                                <!--<p>Date: --><?//= //date('d/m/Y') ?><!--</p>-->
                                <p>Price: <span class="course_price"><?= $this->price($item->price) ?></span></p>
                                <p>Price: <span class="course_discounted_price"><?= isset($course_price) ? $this->price($course_price): $this->price($item->price) ?></span></p>
                                <!--<p>Payment method: Card</p>-->
                                <!--<a class="btn view-invoice" href="--><?//= SITE_URL ?><!--invoice/--><?//= $order->id ?><!--?id=--><?//= $order->id ?><!--" target="_blank">View Invoice</a>-->
                                <?php
                                if(@$item->premiumSubPlanMonths) {
                                    ?>
                                    <a class="btn btn-primary" href="<?= SITE_URL ?>dashboard">Start Now</a>
                                    <?php
                                } else if($item->gift == "0" && $item->certID == "") {
                                    ?>
                                    <a class="btn btn-primary" href="<?= SITE_URL ?>start/<?= $course->slug ?>">Start Now</a>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>


                </div>

            </div>
        </div>
    </section>

</main>
<!-- Main Content End -->

<?php
$this->controller->renderPostSaleDiscounts();
?>

<?php
$cookieName = "Source"; // Name of the cookie to use for last click referrer de-duplication
 
if (isset($_COOKIE[$cookieName])) {
$channel = $_COOKIE[$cookieName];
} else {
$channel = "aw"; // No paid channel assisted
}

// AWIN Tracking
$total = number_format($order->total, 2);

$url = "https://www.awin1.com/sread.php?tt=ss&tv=2&merchant=31125";
$url .= "&amount=" . $total;
$url .= "&ch=" . $channel ?? 'aw';
$url .= "&cr=" . $currency->code;
$url .= "&ref=" . $order->id;
$url .= "&parts=DEFAULT:" . $total;
$url .= "&testmode=0";
$url .= isset($coupon) ? '&vc='.$coupon->code : '&vc=';

if(isset($_COOKIE['awc'])) {
    $url .= "&cks=" . $_COOKIE['awc']; // affiliate click
}

$c = curl_init();
curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c, CURLOPT_URL, $url);
curl_exec($c);
curl_close($c);
?>

<script>
    // AWIN Tracking - Conversion tag

    var sCookieName = "Source"; // Name of the cookie to use for last click referrer de-duplication

    var _getCookie = function (sCookieName) {
        sCookieName += "=";
        var aCookies = document.cookie.split(";");
        for (var i = 0; i < aCookies.length; i++) {
            while (aCookies[i].charAt(0) == " ") aCookies[i] = aCookies[i].substring(1);
            if (aCookies[i].indexOf(sCookieName) != -1) {
                return aCookies[i].substring(sCookieName.length, aCookies[i].length);
            }
        }
    };

    if (_getCookie(sCookieName)) {
        var sChannel = _getCookie(sCookieName);
    } else {
        var sChannel = "aw"; // No paid channel assisted
    }

    let awin_contents = [];

    $('.thankyou-order-item').each(function(index){

        let is_print_material = $(this).find('.is_print_material').text();

        //Do not include printed materials
        if(is_print_material == '1') {
            return; //equivalnet to continue in JS
        }

        let course_price = $(this).find('.course_discounted_price').text().replace("£", "").replace("$", "");
        course_price = parseFloat(course_price).toFixed(2);

        let course_id = $(this).find('.item_id').text();
        let course_title = $(this).find('.nsa_course_title').text();
        let categories = $(this).find('.course_cats').text();

        if(!course_id) {
            let premium_subscription_id = $(this).find('.premium_subscription_id').text();
            if(premium_subscription_id) {
                course_id = premium_subscription_id;
                subscription_total = course_price;
            }
        }

        let awin_content = {
            'id': course_id,
            'quantity': 1,
            'price': course_price,
            'name': course_title,
            'category': categories,
        };

        if(course_id) {
            awin_contents.push(awin_content);
        }
    });

    let order_details = $('.order-details');

    let coupon_name = order_details.find('.coupon_codes').first().text();

    let order_id = order_details.find('.nsa_order_number').text();

    let order_total = order_details.find('.order_total_amount').text().replace("£", "").replace("$", "");
    order_total = parseFloat(order_total).toFixed(2);

    var awPixel = new Image(0, 0);
    awPixel.src = "https://www.awin1.com/sread.img?tt=ns&tv=2&merchant=31125"+"&amount="+parseFloat(order_total).toFixed(2)+ "&ch=<?= $channel ?? 'aw' ?>" + "&cr=<?= strtoupper($currency->code) ?>&parts=DEFAULT:" + parseFloat(order_total).toFixed(2) + "&ref=" + order_id + "&testmode=0&vc=" + coupon_name;

    if (typeof AWIN != "undefined" && typeof AWIN.Tracking != "undefined") {
        AWIN.Tracking.Sale = {};
        AWIN.Tracking.Sale.amount = parseFloat(order_total).toFixed(2);
        AWIN.Tracking.Sale.channel = $channel ?? 'aw';
        AWIN.Tracking.Sale.orderRef = order_id;
        AWIN.Tracking.Sale.parts = "DEFAULT" + parseFloat(order_total).toFixed(2);
        AWIN.Tracking.Sale.currency = "<?= strtoupper($currency->code) ?>";
        AWIN.Tracking.Sale.voucher = coupon_name;
        AWIN.Tracking.Sale.test = "0";

        var transactionProducts = awin_contents;
        AWIN.Tracking.Sale.plt = '';
        for (i = 0; i < transactionProducts.length; i++) {
            AWIN.Tracking.Sale.plt += "AW:P|31125|"+order_id+"|" +
                transactionProducts[i]['id'] + "|" +
                transactionProducts[i]['name'] + "|" +
                transactionProducts[i]['price'] + "|" +
                transactionProducts[i]['quantity'] + "|" +
                transactionProducts[i]['id'] + "|" +
                "DEFAULT|" +
            transactionProducts[i]['category'] + "\n";
        }

        var basketForm = document.createElement('form');
        basketForm.setAttribute('style', 'display:none;');
        basketForm.setAttribute('name', 'basket_form');
        var basketTextArea = document.createElement('textarea');
        basketTextArea.setAttribute('wrap', 'physical');
        basketTextArea.setAttribute('id', 'aw_basket');
        basketTextArea.value = AWIN.Tracking.Sale.plt;
        basketForm.appendChild(basketTextArea);
        document.getElementsByTagName('body')[0].appendChild(basketForm);


        AWIN.Tracking.run();

    } else {
        //<![CDATA[
        /*** Do not change ***/
        var AWIN = {};
        AWIN.Tracking = {};
        AWIN.Tracking.Sale = {};
        AWIN.Tracking.Sale.amount = parseFloat(order_total).toFixed(2);
        AWIN.Tracking.Sale.channel = '<?= $channel ?? 'aw' ?>';
        AWIN.Tracking.Sale.orderRef = order_id;
        AWIN.Tracking.Sale.parts = "DEFAULT:" + parseFloat(order_total).toFixed(2);
        AWIN.Tracking.Sale.currency = "<?= strtoupper($currency->code) ?>";
        AWIN.Tracking.Sale.voucher = coupon_name;
        AWIN.Tracking.Sale.test = "0";

        var transactionProducts = awin_contents;
        AWIN.Tracking.Sale.plt = '';
        for (i = 0; i < transactionProducts.length; i++) {
            AWIN.Tracking.Sale.plt += "AW:P|31125|"+order_id+"|" +
                transactionProducts[i]['id'] + "|" +
                transactionProducts[i]['name'] + "|" +
                transactionProducts[i]['price'] + "|" +
                transactionProducts[i]['quantity'] + "|" +
                transactionProducts[i]['id'] + "|" +
                "DEFAULT|" +
            transactionProducts[i]['category'] + "\n";
        }

        var basketForm = document.createElement('form');
        basketForm.setAttribute('style', 'display:none;');
        basketForm.setAttribute('name', 'basket_form');
        var basketTextArea = document.createElement('textarea');
        basketTextArea.setAttribute('wrap', 'physical');
        basketTextArea.setAttribute('id', 'aw_basket');
        basketTextArea.value = AWIN.Tracking.Sale.plt;
        basketForm.appendChild(basketTextArea);
        document.getElementsByTagName('body')[0].appendChild(basketForm);

        //]]>
    }
</script>

<?php

// reach tracking code
$reachCodes = array("370", "369", "368");
if(in_array($_SESSION["refCodeInternal"], $reachCodes)) {
    ?>
    <!-- Conversion Pixel - Conversion - New Skills Academy - Pay Now - DO NOT MODIFY -->
    <script src="https://secure.adnxs.com/px?id=1490334&seg=26796655&t=1" type="text/javascript"></script>
    <!-- End of Conversion Pixel -->
    <?php
}

// record onto affiliate system
$sale_amount = number_format($order->total, 2);
$product = 'NSA Order #: '.$order->id;
include(TO_PATH.'/affiliates/controller/record-sale.php');

$this->controller->destroyOrderSession();
?>
<?php include BASE_PATH . 'footer.php';?>

<?php if( floatval($grandTotal) > 0 || floatval($subscriptionPrice) > 0 ): ?>

    <?php if( !empty($items) ): ?>

        <?php

        $event_id = 'purchase.'.$order_id;

        $purchaseTotal = $grandTotal;

        if(floatval($grandTotal) <= 0 && floatval($subscriptionPrice) > 0) {

            $purchaseTotal = $subscriptionPrice;
            $purchase_event_content_ids = [$premiumSubPlanID];
            if(!empty($purchase_event_contents)) {
                $purchase_event_contents[0]['content_id'] = $premiumSubPlanID;
            }

        }

        /*
        var_dump($purchase_event_contents);
        var_dump($purchaseTotal);
        var_dump($purchase_event_content_ids);
        var_dump($event_id);
        */

        $purchase_event = facebookBusinessSDKController::createPurchaseEvent($purchase_event_contents, $purchaseTotal, $purchase_event_content_ids, $event_id);
        facebookBusinessSDKController::executeEvents(array($purchase_event));

        ?>


        <script>
            //let course_title = "<?php //echo $item->title; ?>//";
            //let categories = "<?php //echo implode(', ', $categories); ?>//";
            //let course_id = "<?php //echo $item_id; ?>//";
            //let content_ids = [course_id];
            //let content_ids = [<?php //echo '"'.implode('","', $item_ids).'"' ?>//];
            //let course_price = "<?php //echo $item->price; ?>//";

            let content_ids = [];
            let contents = [];
            let course_titles = [];
            let course_cats = [];
            let tiktok_contents = [];

            let num_items = 0;
            let subscription_total = 0;

            $('.thankyou-order-item').each(function(index){

                let is_print_material = $(this).find('.is_print_material').text();

                //Do not include printed materials
                if(is_print_material == '1') {

                    return; //equivalnet to continue in JS

                }

                num_items += 1;

                let course_price = $(this).find('.course_discounted_price').text().replace("£", "").replace("$", "");
                course_price = parseFloat(course_price).toFixed(2);

                //console.log(course_price);

                let course_id = $(this).find('.item_id').text();
                let course_title = $(this).find('.nsa_course_title').text();
                let categories = $(this).find('.course_cats').text();

                if(!course_id) {
                    let premium_subscription_id = $(this).find('.premium_subscription_id').text();
                    if(premium_subscription_id) {
                        course_id = premium_subscription_id;
                        subscription_total = course_price;
                    }
                }


                let content = {
                    'id': course_id,
                    'quantity': 1,
                    'item_price': course_price,
                    'course_title': course_title
                };

                //console.log(content);

                let tiktok_content = {
                    'content_id': course_id,
                    'content_type': 'product',
                    'content_name': course_title,
                    'quantity': 1,
                    'price': course_price,
                };

                if(course_id) {
                    content_ids.push(course_id);
                    contents.push(content);
                    course_cats.push(categories);
                    tiktok_contents.push(tiktok_content);
                }

                course_titles.push(course_title);


            });

            let course_title = course_titles.join(', ');
            let all_course_cats = course_cats.join(', ');

            //let contents = [{"id":course_id,"quantity":1,"item_price":course_price}];
            //let coupon_name = "<?php //echo $coupon_name; ?>//";
            //let order_total = "<?php //echo $grandTotal; ?>//";
            //let num_items = "<?php //echo $num_items; ?>//";
            //let coupon_used = "<?php //echo $coupon_used; ?>//";


            // let order_details = $('.order-details');

            //let coupon_name = order_details.find('.coupon_codes').text();

            //let order_id = order_details.find('.nsa_order_number').text();

            //let order_total = "<?php //echo $grandTotal; ?>//";

            // let order_total = order_details.find('.order_total_amount').text().replace("£", "").replace("$", "");
            //order_total = parseFloat(order_total).toFixed(2);

            if(order_total <= 0) {

                order_total = subscription_total;

            }

            //let num_items = "<?php //echo $num_items; ?>//";
            //let coupon_used = "<?php //echo $coupon_used; ?>//";

            let coupon_used = !coupon_name ? 'no': 'yes';

            let event_id = 'purchase.'+order_id;

            fbq(
                'track',
                'Purchase',
                {
                    content_type: "product",
                    domain: DOMAIN_NAME,
                    coupon_name: coupon_name,
                    total: order_total,
                    //transactions_count: 497,
                    num_items: num_items,
                    //predicted_ltv: 573.06,
                    //event_hour: event_hour,
                    //user_roles: "student",
                    //tax: 0,
                    category_name: all_course_cats,
                    //average_order: 1.15,
                    currency: "<?= strtoupper($currency->code) ?>",
                    //value: course_price,
                    value: order_total,
                    content_name: course_title,
                    content_ids: content_ids,
                    coupon_used: coupon_used,
                    //event_day: event_day,
                    //product_price: course_price,
                    product_price: order_total,
                    contents: contents,
                    //event_month: event_month,
                    //shipping_cost: 0

                }, {
                    eventID: event_id
                }
            );

            //ttq.track('CompletePayment');

            ttq.track('CompletePayment', {
                contents: tiktok_contents,
                value: order_total,
                currency: '<?= strtoupper($currency->code) ?>',
            });


            snaptr('track', 'PURCHASE', {
                'currency': "<?= strtoupper($currency->code) ?>",
                'price': order_total,
                'transaction_id': order_id,
                'item_category': all_course_cats,
                'item_ids': content_ids,
                'number_items': num_items
            });

        </script>

    <?php else: ?>

        <script>

            let content_ids = [];
            let contents = [];
            let course_titles = [];
            let course_cats = [];
            let tiktok_contents = [];

            let num_items = 0;

            $('.thankyou-order-item').each(function(index){

                num_items += 1;

                let course_price = $(this).find('.course_discounted_price').text().replace("£", "").replace("$", "");
                course_price = parseFloat(course_price).toFixed(2);

                //console.log(course_price);

                let course_id = $(this).find('.item_id').text();
                let course_title = $(this).find('.nsa_course_title').text();
                let categories = $(this).find('.course_cats').text();

                let content = {
                    'id': course_id,
                    'quantity': 1,
                    'item_price': course_price,
                    'course_title': course_title
                };

                //console.log(content);

                let tiktok_content = {
                    'content_id': course_id,
                    'content_type': 'product',
                    'content_name': course_title,
                    'quantity': 1,
                    'price': course_price,
                };

                if(course_id) {
                    content_ids.push(course_id);
                    contents.push(content);
                    course_cats.push(categories);
                    tiktok_contents.push(tiktok_content);
                }

                course_titles.push(course_title);

            });

            let course_title = course_titles.join(', ');
            let all_course_cats = course_cats.join(', ');


            //let order_details = $('.order-details');

            // let coupon_name = order_details.find('.coupon_codes').text();

            //let order_id = order_details.find('.nsa_order_number').text();

            //let order_total = "<?php //echo $grandTotal; ?>//";

            let order_total = order_details.find('.order_total_amount').text().replace("£", "").replace("$", "");
            order_total = parseFloat(order_total).toFixed(2);

            //let num_items = "<?php //echo $num_items; ?>//";
            //let coupon_used = "<?php //echo $coupon_used; ?>//";

            let coupon_used = !coupon_name ? 'no': 'yes';

            let event_id = 'purchase.'+order_id;

            fbq(
                'track',
                'Purchase',
                {
                    //content_type: "product",
                    <?php if(SITE_TYPE == "uk") { ?>domain: "newskillsacademy.co.uk",<?php } else { ?>domain: "newskillsacademy.com",<?php } ?>
                    //coupon_name: coupon_name,
                    total: order_total,
                    //transactions_count: 497,
                    //num_items: num_items,
                    //predicted_ltv: 573.06,
                    //event_hour: event_hour,
                    //user_roles: "student",
                    //tax: 0,
                    //category_name: all_course_cats,
                    //average_order: 1.15,
                    currency: "<?= strtoupper($currency->code) ?>",
                    //value: course_price,
                    value: order_total,
                    content_name: course_title,
                    //content_ids: content_ids,
                    //coupon_used: coupon_used,
                    //event_day: event_day,
                    //product_price: course_price,
                    product_price: order_total,
                    //contents: contents,
                    //event_month: event_month,
                    //shipping_cost: 0

                }, {
                    eventID: event_id
                }
            );




            //ttq.track('CompletePayment');

            ttq.track('CompletePayment', {
                contents: tiktok_contents,
                value: order_total,
                currency: '<?= strtoupper($currency->code) ?>P',
            });

            snaptr('track', 'PURCHASE', {
                'currency': "<?= strtoupper($currency->code) ?>",
                'price': order_total,
                'transaction_id': order_id,
                'item_category': all_course_cats,
                'item_ids': content_ids,
                'number_items': num_items
            });
        </script>


    <?php endif; ?>

<?php endif; ?>
