<?php

$css = array("checkout.css", "datepicker.css", "gift.css");
$js = array("datepicker.min.js");

$pageTitle = "Gift";
$hideNewsletterModal = true;
include BASE_PATH . 'header.php';

$courseCount = 700;

if ($currency->code != "GBP") {
    $courseCount = 300;
}
?>
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<div id="gift-page">
    <style>
        html {
            overflow-y: scroll;
        }


    </style>

    <style>
        .checkout-box .stripe_input_fields {
            padding-top: 18px;
        }
    </style>


    <!-- Main Content Start-->
    <main role="main" class="regular page-gift">

        <!--Checkout-->
        <div id="checkOutColor" style="background-color:#0504AA;">
            <section id="checkoutMain">
                <section class="checkout">
                    <div class="container wider-container ">
                        <div class="row">
                        </div>
                    </div>
                </section>
                <div v-if="paymentProcessing" class="loader_wrapper"><i class="fas fa-spin fa-spinner"></i></div>
                <section id="objectFallDown">
                    <div class="container wider-container ">
                        <?php
                        $currentAccount = array();

                        if (SIGNED_IN == true) {
                            $currentAccount = ORM::For_table("accounts")->find_one(CUR_ID_FRONT);
                        }
                        ?>

                        <!--Checkout form-->
                        <form name="checkout" @submit="validateForm" id="payment-form">
                            <div class="row">
                                <div class="col-12 col-md-12 col-lg-2"></div>
                                <div class="col-12 col-md-12 col-lg-8 checkout-box">

                                    <div class="white-box checkout-details">
                                        <h3>Get Started</h3>
                                        <div class="alert alert-danger" v-if="errors.length">
                                            <b>Please correct the following error(s):</b>
                                            <ul>
                                                <li v-for="error in errors">{{ error }}</li>
                                            </ul>
                                        </div>
                                        <div v-bind:class="(giftType==='subscription')?'active':''" class="form-row gift_type">
                                            <div class="form-group col-md-12">
                                                <label>Gift a year subscription to all <?= $courseCount ?> of our courses plus...</label>
                                                <ul class="gift_sub_list">
                                                    <?php
                                                    if ($currency->code == "GBP") {
                                                    ?>
                                                        <li>XO Student Discount membership</li>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <li>Jobs board</li>
                                                    <?php
                                                    }
                                                    ?>
                                                    <li>Career tools</li>
                                                    <li>Access to our community</li>
                                                </ul>
                                                <div v-bind:class="(giftType==='subscription')?'premium_button active':'premium_button'" @click="selectSubscription()">
                                                    12 months full site access only {{totalSubDisplayPrice}}
                                                </div>
                                            </div>
                                        </div>
                                        <div v-bind:class="(giftType==='course')?'active':''" class="form-row gift_type">
                                            <div class="form-group col-md-8">
                                                <label> Pay just {{TotalDisplayPrice}} today & Gift Any Individual Course Worth Up To £100</label>
                                                <div v-bind:class="(giftType==='course')?'premium_button active':'premium_button'" @click="courseSelection()">
                                                    Gift a course for {{TotalDisplayPrice}}
                                                </div>

                                            </div>

                                        </div>

                                        <input type="hidden" name="total" id="totalUser" />

                                        <div class="form-row">
                                            <div class="form-group col-md-12 totaltopay">
                                                <span>Total to Pay <strong class="ajaxTotalPrice">{{totalPrice}}</strong></span>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="white-box">
                                        <h3>Choose A Style</h3>

                                        <div class="styles">
                                            <?php
                                            $count = 1;
                                            while ($count <= 11) {
                                            ?>
                                                <div v-on:click="itemSelection(<?= $count ?>)" class="item <?php if ($count == 1) { ?>selected<?php } ?>" data-style="<?= $count ?>">
                                                    <img src="<?= SITE_URL ?>assets/images/vouchers/<?= $count ?>.png" />
                                                </div>
                                            <?php
                                                $count++;
                                            }
                                            ?>
                                        </div>

                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="name" name="name" placeholder="Your Name" />
                                        </div>

                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="nameRecipient" name="nameRecipient" placeholder="Recipient's Name" />
                                        </div>

                                        <div class="form-group" style="position:relative;">
                                            <textarea maxlength="100" id="message" class="form-control" v-model="message" name="message" rows="3" placeholder="Add a message (up to 100 characters)..." style="height: 100px;border-radius: 10px;"></textarea>
                                            <p id="charactersRemaining"></p>
                                        </div>
                                        <script>
                                            var el;

                                            function countCharacters(e) {
                                                var textEntered, countRemaining, counter;
                                                textEntered = document.getElementById('message').value;
                                                counter = (100 - (textEntered.length));
                                                countRemaining = document.getElementById('charactersRemaining');
                                                countRemaining.textContent = counter;
                                            }
                                            el = document.getElementById('message');
                                            el.addEventListener('keyup', countCharacters, false);
                                        </script>

                                        <h3>Preview</h3>
                                        <div class="giftPreview" style=" background-image:url('<?= SITE_URL ?>assets/images/vouchers/1.png'); backgound-size: cover;">
                                            <p style="text-align: right;max-width:35%;"class="course">{{courseTitle}}</p>
                                            <p class="to">{{nameRecipient}}</p>
                                            <p class="from">{{name}}</p>
                                            <p class="message">{{message}}</p>
                                        </div>

                                    </div>

                                    <div class="white-box">
                                        <h3>Delivery Method</h3>

                                        <div class="d-flex align-items-center tickbox extra-radius" style="font-size: 22px;line-height: 40px;">
                                            <div class="custom-control custom-checkbox">
                                                <input type="radio" class="custom-control-input" id="print" name="type" checked value="print">
                                                <label class="custom-control-label" for="print">
                                                    Print at home
                                                </label>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center tickbox extra-radius" style="font-size: 22px;margin-top: 34px;line-height: 40px;">
                                            <div class="custom-control custom-checkbox">
                                                <input type="radio" class="custom-control-input" id="deliver" name="type" value="deliver">
                                                <label class="custom-control-label" for="deliver">
                                                    Deliver to recipients email
                                                </label>
                                            </div>
                                        </div>

                                        <div v-if="type='deliver'" class="form-group recipEmail">
                                            <input type="email" class="form-control" v-model="recipEmail" name="recipEmail" placeholder="Recipients email address..." />
                                            <br />
                                            <input type="text" class="form-control" v-model="giftDate" name="giftDate" placeholder="Optionally, schedule for..." data-toggle="datepicker">
                                        </div>

                                    </div>

                                    <div class="white-box">
                                        <h3>Your Details</h3>

                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="firstname" name="firstname" placeholder="Firstname..." />
                                        </div>

                                        <div class="form-group">
                                            <input type="text" class="form-control" value="{{lastname}}" v-model="lastname" name="lastname" placeholder="Lastname..." />
                                        </div>

                                        <div class="form-group">
                                            <input type="email" class="form-control" value="{{email}}" v-model="email" name="email" placeholder="Email..." />
                                        </div>


                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox terms">
                                                <input type="checkbox" class="custom-control-input" id="terms" v-model="terms" name="terms" value="1">
                                                <label class="custom-control-label" for="terms">
                                                    I accept the <a href="<?= SITE_URL ?>terms-website-use" target="_blank" class="underlined"><span>terms & conditions</span></a>
                                                </label>
                                            </div>
                                        </div>



                                    </div>



                                    <div class="white-box">
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
                                                    <a class="nav-link" @click="activePaymentMethod('paypal')" id="paypal-tab" data-name="PayPal" href="#paypaltab">
                                                        <img src="<?= SITE_URL ?>assets/images/paypal.png" alt="paypal" />
                                                    </a>
                                                </li>
                                                <li class="nav-item apple-button">
                                                    <a class="nav-link" @click="validateForm('applepay')" id="apple-tab" data-name="Apple" href="#applepaytab">
                                                        <!-- <a class="nav-link" @click="validateForm('applepay')">  -->
                                                        <img id='apple-tab' src="<?= SITE_URL ?>assets/images/apple.png" alt="apple" />
                                                    </a>
                                                </li>
                                            </ul>
                                            <div class="tab-content" id="myTabContent">
                                                <div class="tab-pane fade show active" id="stripetab" role="tabpanel" aria-labelledby="visa-tab">

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
                                                <div class="tab-pane fade" id="paypaltab" role="tabpanel" aria-labelledby="paypal-tab">
                                                    <div class="row">
                                                        <div class="col-12 text-center">
                                                            <img @click="validateForm" style="max-width: 100%;" src="<?= SITE_URL ?>assets/images/paypal-checkout.png">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade text-center" id="applepaytab" role="tabpanel" aria-labelledby="apple-tab">
                                                    <!--                                            <div id="payment-request-button">-->
                                                    <!---->
                                                    <!--                                            </div>-->
                                                    <div v-if="paymentType=='applepay'" class="">
                                                        <img @click="submitApplePayPayment()" style="width: 200px; max-width: 100%" src="<?= SITE_URL ?>assets/images/apple-pay.png">
                                                    </div>

                                                </div>

                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-12 totaltopay">
                                                    <span>Total to Pay <strong class="ajaxTotalPrice">{{totalPrice}}</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="form-group text-center pay-button">
                                        <input type="submit" value="Pay Now" class="btn btn-success btn-lg extra-radius">
                                    </div>

                                    <div class="form-group col-md-12">
                                        <div class="btm-logo d-flex align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-lock"></i>
                                                <p><strong>Secure Payments </strong><br />Your order is safe and secure. We use the latest SSL encryption for all transactions</p>
                                            </div>
                                            <div>
                                                <img src="<?= SITE_URL ?>assets/images/checkout-trupiolet.png" alt="trust piolet">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <input type="hidden" name="gift_type" id="gift_type" value="subscription">
                            <input type="hidden" name="gift_amount" id="gift_amount" value="99.99">
                            <input type="hidden" name="voucher_style" id="voucherStyle" value="1">
                        </form>
                    </div>
                </section>
        </div>
        <?php include BASE_PATH . 'learn-confidence.php'; ?>

        <?php include BASE_PATH . 'newsletter.php'; ?>

       <?php include BASE_PATH . 'featured.php'; ?>

        <?php include BASE_PATH . 'success-stories.php'; ?>

    </main>
    <!-- Main Content End -->
</div>
<?php
include('includes/gift-vue.php');

?>

<?php include BASE_PATH . 'footer.php'; ?>


