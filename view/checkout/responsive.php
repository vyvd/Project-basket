<?php
header('X-Frame-Options: SAMEORIGIN');
$this->setControllers(array("course"));

$currency = $this->currentCurrency();

$offer2 = 1;
if(@$_GET['addSub'] && ($_GET['addSub'] == true)){
    if($_COOKIE["allowSubTrial"] == "true" && $currency->trialStatus != "None") {
        $this->controller->addPremiumSubscription(null, $currency->trialDays);
    } else {
        $this->controller->addPremiumSubscription();
    }
    $offer2 = 0;
    header('Location: '.SITE_URL.'checkout');
    exit;
}
if(@$_GET['courseID']){
    $this->controller->addCourse($_GET['courseID']);
    header('Location: '.SITE_URL.'checkout');
    exit;
}

// get trial days if this cart contains a subscription with a free trial
$trial = $this->controller->ifCartContainsSubscriptionTrial();
$isSubscriptionItem = $this->controller->ifCartContainsSubscriptionItem();

$css = array("checkout.css", "datepicker.css");
$js = array("datepicker.min.js");

$pageTitle = "Checkout";

include BASE_PATH . 'header.php';

$newsletterCourse = $this->getNewsletterCourse();

$isAffiliate = @$_SESSION["affiliateDiscount"] ? true : false;

$plan = ORM::for_table('premiumSubscriptionsPlans')->where('months', 12)->find_one();
?>
    <style>
        .checkout-box .stripe_input_fields{
            padding-top: 18px;
        }
        .navbar-toggler, .navbar-expand-lg .navbar-collapse, .learn-with-confidence-sec, .free-cource, .as-featured, .customer-says, #multiAwardsSlider, .validate-students, .ab-preview, .multi-awards {
            display:none !important;
        }
        .navbar-brand {
            display: block;
            margin: auto;
        }
        .useAccountBalance {
            background: #249dbf;
            width: 100%;
            color: #fff;
            display: block;
            border-radius: 8px;
            text-align: center;
            padding: 8px;
        }
        .useAccountBalance:hover {
            background: #1e6e8a;
            text-decoration:none;
            color:#fff;
        }
        .useAccountBalance i {
            margin-right:3px;
        }

        .loader_wrapper label {
            top: 60%;
            font-size: 1.7rem;
            position: absolute;
            color: #ffffff;
            text-align: center;
            max-width: 90%;
            left: 20%;
            right: 20%;
        }
    </style>
    <script src="https://js.stripe.com/v3/"></script>

    <?php if($isSubscriptionItem == 1){?>
        <script src="https://www.paypal.com/sdk/js?client-id=<?= PAYPAL_CLIENT_ID_NEW ?>&vault=true&intent=subscription"></script>
    <?php }else{?>
        <script src="https://www.paypal.com/sdk/js?client-id=<?= PAYPAL_CLIENT_ID_NEW ?>&currency=<?= $currency->code ?>" data-sdk-integration-source="button-factory"></script>
    <?php }?>
    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--Checkout-->
        <section class="checkout">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-md-12">
                        <h1 class="section-title text-center">Checkout</h1>
                    </div>
                </div>
            </div>
        </section>

        <section id="checkoutMain">
            <div class="container wider-container">
                <div v-if="paymentProcessing"  class="loader_wrapper">
                    <i class="fas fa-spin fa-spinner"></i>
                    <label>We’re processing your payment,<br> please don’t refresh or close the window.</label>
                </div>
                <!--Checkout form-->
                <form name="checkout" @submit="validateForm" id="payment-form">
                    <input type="hidden" value="0" id="payment_verify">
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-8 checkout-box order-2 order-md-1">



                            <?php
                            // upsell printed materials
                            foreach($this->controller->currentCartItems() as $item) {
                                //$this->controller->ifContainsPrintedCourse($item->courseID) == false
                                if($item->course == "1" && ($item->printedCourse == '0')) {
                                    $course = ORM::for_table("courses")->find_one($item->courseID);
                                    $checkPrintedItem = ORM::for_table('orderItems')
                                        ->where('printedCourse', '1')
                                        ->where('courseID', $item->courseID)
                                        ->find_one();

                                    if($course->sellPrinted == "111") {
                                        ?>
                                        <div class="white-box">
                                            <div class="d-flex align-items-center tickbox extra-radius">
                                                <div class="custom-control custom-checkbox">
                                                    <input @change="updatePrintedItem($event, '<?= $course->id ?>')" type="checkbox" class="custom-control-input addPrinted<?= $course->id ?>" <?php if(@$checkPrintedItem){?> checked <?php }?> id="printed<?= $course->id ?>" value="1">
                                                    <label class="custom-control-label" for="printed<?= $course->id ?>">
                                                        Tick box to get the printed version of <a><?= $course->title ?></a> delivered to your door
                                                    </label>
                                                </div>
                                                <div class="tickbox-img">
                                                    <img src="<?= $this->course->getCourseImage($course->id, "medium") ?>" alt="nsa" />
                                                    <span>Just &pound;<?= number_format($course->sellPrintedPrice, 2) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }

                                }
                            }
                            ?>



                            <?php
                            $currentAccount = array();
                            $billingPostcode = '';

                            if(SIGNED_IN == true) {
                                $currentAccount = ORM::For_table("accounts")->find_one(CUR_ID_FRONT);
                                // attempt to get billing postcode from past order
                                $pastOrder = ORM::for_table("orders")->where("status", "completed")->where("accountID", CUR_ID_FRONT)->where_not_null("postcode")->find_one();

                                if($pastOrder->id != "") {
                                    $billingPostcode = $pastOrder->postcode;
                                }
                            }
                            ?>
                            <div <?php if(isset($_GET['addSub'])){?> style="display: none" <?php }?> class="upsell-box offer1-box">
<!--                                <div v-if="premiumSubscription" class="selectedUpsellOffer">-->
<!--                                    <i class="fas fa-check-circle"></i>-->
<!--                                    <h2>Selected</h2>-->
<!--                                    <label @click="updateUpsellOfferOne(false)">Remove</label>-->
<!--                                </div>-->


                                <div v-if="premiumSubscription || isQualificationItems">

                                </div>
                                <div v-else class="main-heading-box text-left">
<!--                                    <h3>Offer 1</h3>-->
                                    <div class="row">
                                        <div class="col-12 col-md-10">
                                            <h4>
                                                Upgrade to get UNLIMITED ACCESS to ALL COURSES
                                                <br> <strong>for only <?= $this->price($currency->prem12);?><sup>*</sup></strong>
                                            </h4>
                                            <p>Get access to every course on our site for <?= $plan->months ?> months</p>
                                            <p>Why study just one course when you can study all of them?</p>
                                            <p>The course(s) you had in your basket will be included in this price.</p>
                                            <button @click="updateUpsellOfferOne(true)" type="button" class="checkout-offer-button">ADD OFFER TO CART</button>
                                            <p>
                                                <sup>*</sup>Excludes language courses. No more than 50 active courses at any one time.
                                                Membership renews after 12 months. Cancel anytime from your account. Can't be used in conjunction with any other offer.
                                            </p>
                                        </div>
                                        <div class="col-2 pl-0 d-none d-md-block">
                                            <img src="<?= SITE_URL;?>/assets/images/subscription-checkout-offer-buy.png">
                                        </div>
                                    </div>
                                </div>
                            </div>


                           <div class="white-box checkout-details">
                               <!-- Gift Options -->
                               <div class="giftOptionBox">
                                   <div class="row">
                                       <div class="col-md-12">
                                           <div class="lookingGift">Looking to buy this course as a gift? <span @click="toggleGift(true);">Click here</span></div>
                                           <input type="hidden" id="giftOption" name="gift" value="" v-model="gift">
                                       </div>
                                   </div>
                                   <div class="row" id="giftOptions" style="display: none">
                                       <div class="coupon col-md-12">
                                           <p class="pl-2"><strong>Who are you sending this gift to?</strong></p>
                                           <div class="input-group mb-3">
                                               <input type="email" class="form-control" name="giftEmail" v-model="giftEmail" placeholder="Email">
                                           </div>

                                           <p class="pl-2"><strong>Schedule gift (optional)</strong></p>
                                           <div class="input-group mb-3">
                                               <input type="text" class="form-control" name="giftDate" placeholder="Tap to select" data-toggle="datepicker" autocomplete="off">
                                           </div>
                                       </div>
                                       <div class="col-md-12">
                                           <div class="hideGiftOption">Don't buy this course as a gift? <span @click="toggleGift(false);">Click here</span></div>
                                       </div>
                                   </div>
                               </div>
                               <!-- End Gift Options -->
                               <h3>Your Details</h3>
                               <div class="alert alert-danger" v-if="errors.length">
                                   <b>Please correct the following error(s):</b>
                                   <ul>
                                       <li v-for="error in errors">{{ error }}</li>
                                   </ul>
                               </div>

                               <div class="form-row">
                                   <div class="form-group col-md-6">
                                       <input type="text" class="form-control" name="firstname" v-model="firstname" placeholder="First Name *" value="<?= $currentAccount->firstname ?>">
                                   </div>
                                   <div class="form-group col-md-6">
                                       <input type="text" class="form-control" name="lastname" v-model="lastname" placeholder="Last Name *" value="<?= $currentAccount->lastname ?>">
                                   </div>
                               </div>
                               <div class="form-group">
                                   <input type="email" class="form-control" name="email" v-model="email" placeholder="Email Address *" value="<?= $currentAccount->email ?>">
                               </div>
                               <?php
                               if(SIGNED_IN == false) {
                                   ?>
                                   <div class="form-group">
                                       <input type="email" class="form-control" v-model="emailConfirm" name="emailConfirm" placeholder="Confirm Email Address *" value="<?= $this->user->email ?>">
                                   </div>
                                   <div class="form-group">
                                       <input type="password" class="form-control" name="password" v-model="password" placeholder="Enter a password *">
                                   </div>


                               <?php } else { ?>
                                   <p class="text-center">
                                       The course(s) you purchase will automatically be assigned to your account (<?= $this->user->email ?>)
                                   </p>
                               <?php } ?>

                               <div class="form-group" <?php if($this->controller->ifCartContainsSubscription() == false) { ?>style="display:none;"<?php } ?>>
                                   <input type="tel" class="form-control" name="phone" v-model="phone" placeholder="Contact Telephone (optional)" value="<?= $currentAccount->phone ?>">
                               </div>

<!--                               <div v-if="cartContainsCert == false" class="form-group">-->
                               <div class="form-group">
                                   <input type="text" class="form-control" placeholder="Billing <?= $currency->postZipWording ?> *" name="postcode" value="<?= $billingPostcode ?>">
                               </div>

                               <div class="form-row">
                                   <div class="form-group col-md-12">
                                       <div class="custom-control custom-checkbox terms">
                                           <input type="checkbox" class="custom-control-input" id="terms" v-model="terms" name="terms" value="1">
                                           <label class="custom-control-label"  for="terms" style="font-size:20px;">
                                               I accept the <a href="<?= SITE_URL ?>terms-website-use" target="_blank" class="underlined"><span>terms & conditions</span></a>
                                           </label>
                                       </div>
                                   </div>
                               </div>
                               <?php if(SIGNED_IN == false) { ?>
                                   <div class="form-row text-center">
                                       <div class="form-group col-md-6">
                                           <a class="social-login-icons" href="<?= SITE_URL.'ajax?c=account&a=social_login&provider=facebook'?>">
                                               <img src="<?= SITE_URL.'assets/images/fblogin.png' ?>">
                                           </a>
                                       </div>
                                       <div class="form-group col-md-6">
                                           <a class="social-login-icons" href="<?= SITE_URL.'ajax?c=account&a=social_login&provider=google'?>">
                                               <img src="<?= SITE_URL.'assets/images/googlelogin.png' ?>">
                                           </a>
                                       </div>
                                   </div>
                               <?php } ?>

                           </div>

                            <?php
                            //if($this->controller->ifCartContainsCert() == true) {
                            ?>
                                <div class="white-box delivery_box">
                                    <h3>Delivery Address</h3>
                                    <p>So we know where to send your printed items to.</p>

                                    <?php if($currency->code == "GBP") { ?>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <input v-model="postcode" type="text" name="postcode" id="postcode" class="form-control"
                                                       placeholder="Enter Your Postcode">
                                            </div>
                                            <div class="col-md-4">
                                                <button type="button" @click="getPostCodeAddresses" class="btn btn-primary btn-block" style="padding:16px;">Find Address</button>
                                            </div>
                                            <div style="display: none" class="postCodeMessage col-12">

                                            </div>
                                            <div style="display: none" class="postCodeAddress col-12">
                                                <select @change="selectAddress" style="height: auto; padding: 10px 15px;" class="form-control mt-4" id="myAddresses"><option value="">Please select your address</option></select>
                                            </div>
                                        </div>
                                    <?php }else{
                                    ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input v-model="postcode" type="text" name="postcode" id="postcode" class="form-control"
                                                       placeholder="Enter Your Postcode">
                                            </div>
                                        </div>
                                    <?php
                                    } ?>
                                    <div class="row mt-4">
                                        <div class="col-6">
                                            <input type="text" name="address1" v-model="address1" id="address1" class="form-control" placeholder="House Name/No">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="address2" v-model="address2" id="address2" class="form-control" placeholder="Address Line 2">
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-6">
                                            <input type="text" name="address3" v-model="address3" id="address3" class="form-control" placeholder="Address Line 3">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="city" v-model="city" id="city" value="" class="form-control" placeholder="Town/City">
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-6">
                                            <input type="text" name="county" v-model="county" id="county" value="" class="form-control" placeholder="County">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="country" v-model="country" id="country" value="" class="form-control" placeholder="Country">
                                        </div>
                                    </div>

                                    <hr />

                                    <p>
                                        By ordering, I confirm that the details provided above are correct and that I understand certificates can take up to 10 working days to arrive. The name printed onto my certificate is the name specified on my account: <?= $this->user->firstname.' '.$this->user->lastname ?>
                                    </p>

                                </div>
                            <?php //}?>



                            <div v-if="paymentType != 'offlinePayment'" class="white-box">
<!--                                <div v-if="premiumSubscription">-->
                                <div class="cards-details-box">
                                    <h3>Select Your Payment Method</h3>
                                    <div class="payment-method">
                                        <ul class="nav nav-tabs " id="myTab">
                                            <li class="nav-item">
                                                <a class="nav-link active" @click="activePaymentMethod('premiumSubscription')" id="visa-tab" data-name="Visa" href="#premiumSubscriptiontab">
                                                    <img src="<?= SITE_URL ?>assets/images/visa.png" alt="visa" />
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" @click="validateForm('paypalSubscription')" id="paypal-subscription-tab" data-name="PayPalSubscription" href="#paypalSubscriptiontab">
                                                    <img src="<?= SITE_URL ?>assets/images/paypal.png" alt="paypal" />
                                                </a>
                                            </li>
                                            <?php if($this->controller->ifCartContainsCert() == false && $isAffiliate == false && $this->controller->ifCartContainsSubscription() == false) { ?>
                                                <!--<li v-if="totalPriceAmount >= 80 && isQualificationItems == false" class="nav-item">
                                                    <a class="nav-link" @click="activePaymentMethod('premiumNSPaySubscription')" id="nspay-tab" data-name="NSPAY" href="#premiumNSPaySubscriptiontab">
                                                        <img src="<?= SITE_URL ?>assets/images/ns-pay.png" alt="nspay" />
                                                    </a>
                                                    <span class="instalment"><img src="<?= SITE_URL ?>assets/images/instalments.png" alt="nsa" /></span>
                                                </li>-->
                                            <?php } ?>
                                        </ul>
                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active"  id="premiumSubscriptiontab" role="tabpanel" aria-labelledby="visa-tab">
                                                <!-- Used to display form errors -->
                                                <div v-if="cardPreSubErrors != ''" id="card-errors" class="mb-2 alert alert-danger" role="alert">{{cardPreSubErrors}}</div>

                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <div id="card-number-pre-sub-element" class="field stripe_input_fields form-control"></div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <div id="card-expiry-pre-sub-element" class="field stripe_input_fields form-control"></div>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <div id="card-cvc-pre-sub-element" class="field stripe_input_fields form-control"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="paypalSubscriptiontab" role="tabpanel" aria-labelledby="paypal-subscription-tab">
                                                <div class="row">
                                                    <div class="col-12 text-center">
                                                        <div id="paypal-subscription-button-container"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="premiumNSPaySubscriptiontab" role="tabpanel" aria-labelledby="nspay-tab">
                                                <!-- Used to display form errors -->
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h4 class="pay-instalments">Spread the cost. Pay in 4 interest free instalments</h4>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h3 class="inst-pay text-center">
                                                            <img src="<?= SITE_URL?>assets/images/inst-25.png">
                                                            Pay just {{unitPrice}} now
                                                        </h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h4 class="inst-pay text-center">Then</h4>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 col-md-4">
                                                        <h4 class="inst-pay text-center">
                                                            <img src="<?= SITE_URL?>assets/images/inst-50.png">
                                                            <span>{{unitPrice}} on <?= date("d F Y",strtotime("+1 months"))?></span>
                                                        </h4>
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <h4 class="inst-pay text-center">
                                                            <img src="<?= SITE_URL?>assets/images/inst-75.png">
                                                            <span>{{unitPrice}} on <?= date("d F Y",strtotime("+2 months"))?></span>
                                                        </h4>
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <h4 class="inst-pay text-center">
                                                            <img src="<?= SITE_URL?>assets/images/inst-100.png">
                                                            <span>{{unitPrice}} on <?= date("d F Y",strtotime("+3 months"))?></span>
                                                        </h4>
                                                    </div>
                                                </div>
                                                <!-- Used to display form errors -->
                                                <div v-if="cardPreNsErrors != ''" id="card-errors" class="mb-2 alert alert-danger" role="alert">{{cardPreNsErrors}}</div>

                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <div id="card-number-pre-ns-element" class="field stripe_input_fields form-control"></div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <div id="card-expiry-pre-ns-element" class="field stripe_input_fields form-control"></div>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <div id="card-cvc-pre-ns-element" class="field stripe_input_fields form-control"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
<!--                                <div v-else>-->
                                <div class="card-payments-method">
                                    <h3>Select Your Payment Method</h3>
                                    <!--Payment Method-->
                                    <div class="payment-method">
                                        <ul class="nav nav-tabs " id="myTab">
                                            <li class="nav-item">
                                                <a class="nav-link active" @click="activePaymentMethod('stripe')" id="visa-tab" data-name="Visa" href="#stripetab">
                                                    <img src="<?= SITE_URL ?>assets/images/visa.png" alt="visa" />
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" @click="validateForm('paypalCheckout')" id="paypal-checkout-tab" data-name="PayPalCheckout" href="#paypalCheckouttab">
                                                    <img src="<?= SITE_URL ?>assets/images/paypal.png" alt="paypal" />
                                                </a>
<!--                                                <a class="nav-link" @click="activePaymentMethod('paypal')" id="paypal-tab" data-name="PayPal" href="#paypaltab">-->
<!--                                                    <img src="--><?//= SITE_URL ?><!--assets/images/paypal.png" alt="paypal" />-->
<!--                                                </a>-->
                                            </li>
                                            <li class="nav-item apple-button">
                                                <a class="nav-link" @click="validateForm('applepay')" id="apple-tab" data-name="Apple" href="#applepaytab">
                                                    <!-- <a class="nav-link" @click="validateForm('applepay')">  -->
                                                    <img id='apple-tab' src="<?= SITE_URL ?>assets/images/apple.png" alt="apple" />
                                                </a>
                                            </li>
                                            <?php if($this->controller->ifCartContainsCert() == false && $isAffiliate == false) { ?>
                                               <!-- <li v-if="totalPriceAmount >= 80  && isQualificationItems == false" class="nav-item">
                                                    <a class="nav-link" @click="activePaymentMethod('nspay')" id="nspay-tab" data-name="NSPAY" href="#nspaytab">
                                                        <img src="<?= SITE_URL ?>assets/images/ns-pay.png" alt="nspay" />
                                                    </a>
                                                    <span class="instalment"><img src="<?= SITE_URL ?>assets/images/instalments.png" alt="nsa" /></span>
                                                </li>-->
                                            <?php } ?>
                                        </ul>
                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active"  id="stripetab" role="tabpanel" aria-labelledby="visa-tab">

                                                <!-- Used to display form errors -->
                                                <div v-if="cardErrors != ''" id="card-errors" class="mb-2 alert alert-danger" role="alert">{{cardErrors}}</div>

                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <div id="card-number-element" class="field stripe_input_fields form-control"></div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <div id="card-expiry-element" class="field stripe_input_fields form-control"></div>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <div id="card-cvc-element" class="field stripe_input_fields form-control"></div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="tab-pane fade" id="paypalCheckouttab" role="tabpanel" aria-labelledby="paypal-checkout-tab">
                                                <div class="row">
                                                    <div class="col-12 text-center">
                                                        <div id="paypal-button-container"></div>
<!--                                                        <img @click="validateForm" style="max-width: 100%;" src="--><?//= SITE_URL?><!--assets/images/paypal-checkout.png">-->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade text-center" id="applepaytab" role="tabpanel" aria-labelledby="apple-tab">
                                                <!--                                            <div id="payment-request-button">-->
                                                <!---->
                                                <!--                                            </div>-->
                                                <div v-if="paymentType=='applepay'" class="">
                                                    <img @click="submitApplePayPayment()" style="width: 200px; max-width: 100%" src="<?= SITE_URL?>assets/images/apple-pay.png">
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="nspaytab" role="tabpanel" aria-labelledby="nspay-tab">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h4 class="pay-instalments">Spread the cost. Pay in 4 interest free instalments</h4>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h3 class="inst-pay text-center">
                                                            <img src="<?= SITE_URL?>assets/images/inst-25.png">
                                                            Pay just {{unitPrice}} now
                                                        </h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h4 class="inst-pay text-center">Then</h4>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 col-md-4">
                                                        <h4 class="inst-pay text-center">
                                                            <img src="<?= SITE_URL?>assets/images/inst-50.png">
                                                            <span>{{unitPrice}} on <?= date("d F Y",strtotime("+1 months"))?></span>
                                                        </h4>
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <h4 class="inst-pay text-center">
                                                            <img src="<?= SITE_URL?>assets/images/inst-75.png">
                                                            <span>{{unitPrice}} on <?= date("d F Y",strtotime("+2 months"))?></span>
                                                        </h4>
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <h4 class="inst-pay text-center">
                                                            <img src="<?= SITE_URL?>assets/images/inst-100.png">
                                                            <span>{{unitPrice}} on <?= date("d F Y",strtotime("+3 months"))?></span>
                                                        </h4>
                                                    </div>
                                                </div>
                                                <!-- Used to display form errors -->
                                                <div v-if="cardSubErrors != ''" id="card-sub-errors" class="mb-2 alert alert-danger" role="alert">{{cardSubErrors}}</div>

                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <div id="card-number-sub-element" class="field stripe_input_fields form-control"></div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <div id="card-expiry-sub-element" class="field stripe_input_fields form-control"></div>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <div id="card-cvc-sub-element" class="field stripe_input_fields form-control"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            if($trial != "0") {
                                ?>
                                <p class="text-center" style="font-weight:bold;margin-top:-15px;">
                                    This is so we can ensure there's no disruption to your membership when your trial ends <?= $trial ?> days from now. You can cancel at any time.
                                </p>
                                <?php
                            }
                            ?>


                            <?php
                            $upsell = $this->controller->getBasketUpsell();
                            //$this->dd($upsell);
                            if($upsell != false) {
                            ?>
                                <div v-if="cartItems.length && offer2Option" class="chooseSpecialOffer">
<!--                                    <h2 class="text-center">Choose a Special Offer</h2>-->
<!--                                    <h2 class="text-center">Or</h2>-->
                                    <div class="upsell-box offer2-box" <?php if($offer2==0){?> style="display: none;" <?php } ?>>
<!--                                        <div v-if="upsellOfferTwo" class="selectedUpsellOffer">-->
<!--                                            <i class="fas fa-check-circle"></i>-->
<!--                                            <h2>Selected</h2>-->
<!--                                            <label @click="updateUpsellOffer(false)">Remove</label>-->
<!--                                        </div>-->
                                        <div class="d-flex align-items-center tickbox-addon">
                                            <div class="custom-control custom-checkbox addon_label_container">
                                                <input @change="updateUpsellOffer($event)" v-model="upsellCheckbox" type="checkbox" class="custom-control-input" id="addon">
                                                <label class="custom-control-label addon_label" for="addon" style="padding-top:11px;">
                                                    Tick box to add the <span class="nsa_course_title"><?= $upsell->title ?></span> to my Order for only <strong class="course_price"><?= $this->price($upsell->upsellCoursePrice)?></strong>
                                                </label>
                                                <img src="<?= SITE_URL ?>assets/images/down-arrow-yello.png" alt="arrow" class="down-arrow">
                                                <img data-toggle="modal"  data-target="#upsellDetails" src="<?= SITE_URL ?>assets/images/bundleInfo.png" alt="info" class="info-arrow">
                                            </div>
                                        </div>
                                        <div class="white-box text-center">
                                            <div class="addon-text">
                                                <div class="row">
                                                    <div class="col-12 col-md-9 text-left">
                                                        <p>ONE TIME OFFER: Want to add <?= $upsell->title ?> to your order?</p>
                                                        <p><i><strong>Click YES to add this to your order now for JUST A SINGLE PAYMENT OF <?= $this->price($upsell->upsellCoursePrice)?></strong></i></p>
                                                        <p>This product sells on our website for <?= $this->price($upsell->price) ?> and is only available to you because you are purchasing the <?= $upsell->courseTitle?></p>
                                                        <p>This offer in NOT available at ANY other time or place</p>
                                                    </div>
                                                    <div class="col-12 col-md-3">
                                                        <img src="<?= $this->course->getCourseImage($upsell->id, "medium");?>" />
                                                    </div>
<!--                                                    <div class="col-12 text-center">-->
<!--                                                        <button type="button" @click="updateUpsellOffer(true)" class="checkout-offer-button">ADD OFFER TO CART</button>-->
<!--                                                    </div>-->
                                                </div>
                                            </div>
                                        </div>
                                        <?php include ('includes/popup-upsell.php');?>
                                    </div>
                                </div>
                            <?php } ?>
                            <br />

                            <div class="white-box">
                                <div class="d-flex align-items-center tickbox extra-radius">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" v-model="newsletter" id="newsletter" name="newsletter" checked value="1">
                                        <label class="custom-control-label" for="newsletter">
                                            Tick box to sign up to our newsletter & <a>get a FREE <?= $newsletterCourse->title ?> Course</a>
                                        </label>
                                    </div>
                                    <div class="tickbox-img">
                                        <img src="<?= SITE_URL ?>assets/images/tickbox.png" alt="nsa" />
                                        <span>Value <?= $currency->short ?>299</span>
                                    </div>
                                </div>
                            </div>

                                <div class="form-row">
                                    <?php
                                    if($trial == "0") {
                                        ?>
                                        <div class="form-group col-md-12 totaltopay">
                                            <span>Total to Pay <strong class="ajaxTotalPrice">{{totalNewPrice}}</strong> <span style="font-size:18px;"><?= $currency->code ?></span></span>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="form-group col-md-12 totaltopay">
                                            <span>Total today <strong class="ajaxTotalPrice"><?= $currency->short ?>0.00</strong> <span style="font-size:18px;"><?= $currency->code ?></span></span>
                                            <br /><small style="font-size:10px;">You will be charged {{totalNewPrice}} in <?= $trial ?> days if you do not cancel beforehand.</small>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>

                                <div class="form-group text-center pay-button">
                                    <input v-if="paymentType=='applepay'" @click="submitApplePayPayment()" type="button" :disabled="paymentProcessing == 1" value="Pay Now" class="btn btn-primary btn-lg extra-radius">
                                    <div v-else-if="paymentType=='paypalSubscription'">
                                        <div id="paypal-subscription-button-container"></div>
                                    </div>
                                    <div v-else-if="paymentType=='paypalCheckout'">
                                    </div>
                                    <input v-else type="submit" :disabled="paymentProcessing == 1" value="Pay Now" class="btn btn-primary btn-lg extra-radius">
                                </div>

                                <div class="form-group col-md-12">
                                    <div class="btm-logo d-flex align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-lock"></i>
                                            <p><strong>Secure Payments </strong><br/>Your order is safe and secure. We use the latest SSL encryption for all transactions</p>
                                        </div>
                                        <div>
                                            <img src="<?= SITE_URL ?>assets/images/checkout-trupiolet.png" alt="trustpilot">
                                        </div>
                                    </div>
                                </div>

                        </div>

                        <div class="col-12 col-md-12 col-lg-4 checkout-sidebar order-1 order-md-2">

                            <div class="white-box orderSummaryStick">
                                <div class="order-summery col-md-12">
                                    <?php include('includes/order-summary-vue.php'); ?>
                                </div>
                            </div>



                        </div>

                    </div>
                </form>
            </div>
        </section>

        <?php include BASE_PATH . 'learn-confidence.php'; ?>

        <?php include BASE_PATH . 'newsletter.php'; ?>

        <?php include BASE_PATH . 'featured.php'; ?>

        <?php include BASE_PATH . 'success-stories.php'; ?>

    </main>
    <!-- Main Content End -->
    <script>
        function applyCoupon() {

            var coupon = $("#couponCode").val();
            $( "#returnStatus" ).load( "<?= SITE_URL ?>ajax?c=cart&a=apply-coupon&code="+encodeURIComponent(coupon));

        }

        $(function() {

            let basket = $('.order-summery');
            let basket_items = basket.find('.cart-items').parent();
            let total = basket.find('.checkout_total_amount').text().replace("£", "").replace("$", "");

            let items = [];
            let item_names = [];
            let item_categories = [];
            let item = {};

            basket_items.each(function(index, element) {

                let price = $(element).find('.course_price').clone();
                price.find('.wasPriceSmall').remove();
                let item_price = price.text().replace("£", "").replace("$", "");

                let item_name = $(element).find('.nsa_course_title').text();

                item = {
                    'ProductID': $(element).data('course-id'),
                    'SKU': $(element).data('course-id'),
                    'ProductName': item_name,
                    'Quantity': 1,
                    'ItemPrice': item_price,
                    'RowTotal': item_price,
                    'ProductURL': $(element).data('course-url'),
                    'ImageURL': $(element).find('.product-img img').attr('src'),
                    'ProductCategories': [$(element).data('course-cats')],
                }

                items.push(item);
                item_names.push(item_name);
                item_categories.push($(element).data('course-cats'));
            });

            _learnq.push(["track", "Started Checkout", {
            "$event_id": "<?= CUR_ID_FRONT !== null ? CUR_ID_FRONT.'-' : '' ?>"+Date.now(),
            "$value": total,
            "ItemNames": item_names,
            "CheckoutURL": window.location.href,
            "Categories": item_categories,
            "Items": items
            }]);
        });

    </script>
<?php
include ('includes/checkout-vue.php');
//$this->renderFormAjax("cart", "checkout", "checkout");

// reach tracking code
$reachCodes = array("370", "369", "368");
if(in_array($_SESSION["refCodeInternal"], $reachCodes)) {
    ?>
    <!-- Conversion Pixel - Conversion - New Skills Academy - Proceed to Checkout - DO NOT MODIFY -->
    <script src="https://secure.adnxs.com/px?id=1490333&seg=26796654&t=1" type="text/javascript"></script>
    <!-- End of Conversion Pixel -->
    <?php
}

?>

<?php include BASE_PATH . 'footer.php';?>