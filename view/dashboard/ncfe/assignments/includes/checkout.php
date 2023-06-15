<script src="https://js.stripe.com/v3/"></script>
<div id="submit-assessments">
    <div v-if="paymentProcessing" class="loader_wrapper"><i class="fas fa-spin fa-spinner"></i></div>
    <div class="row white-rounded p-4 mb-4">
        <div class="col-12">
            <h3>Order Summary</h3>
            <div v-if="cartItems.length">
                <div v-for="(item, index) in cartItems" class="assessment-cart-item row">
                    <span class="nsa_course_title col-8">{{item.title}}</span>
                    <span class="nsa_course_price col-2">{{item.price}}</span>
                    <span class="remove-items col-2" @click="removeFromCart(item.id, $event);">Remove</span>
                </div>
                <div class="assessment-cart-total row mt-3">
                    <div class="col-8 text-right pr-5">
                        <h4>Total</h4>
                    </div>
                    <div class="col-2 pl-0">
                        <h4>{{totalPrice}}</h4>
                    </div>
                    <div class="col-2">

                    </div>
                </div>
            </div>
            <div v-else class="text-center col-12">
                <h5>Your cart is empty!</h5>
            </div>
        </div>
    </div>
    <form name="checkout" @submit="validateForm" id="payment-form">
        <div class="row white-rounded p-4">
            <div class="col-12">
                <?php if(@$_GET['payment'] && ($_GET['payment'] == 'failed')) {?>
                    <div class="alert alert-danger">
                        <b>Payment Failed!</b>
                    </div>
                <?php } elseif(@$_GET['payment'] && ($_GET['payment'] == 'success')) {?>
                    <div class="alert alert-success">
                        <b>Payment Successful!</b>
                    </div>
                <?php } ?>
                <div class="card-payments-method">
                    <h3 class="mb-4">Select Your Payment Method</h3>
                    <div class="alert alert-danger" v-if="errors.length">
                        <b>Please correct the following error(s):</b>
                        <ul>
                            <li v-for="error in errors">{{ error }}</li>
                        </ul>
                    </div>
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
                            <div class="tab-pane fade" id="paypaltab" role="tabpanel" aria-labelledby="paypal-tab">
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <img @click="validateForm" style="max-width: 100%;" src="<?= SITE_URL?>assets/images/paypal-checkout.png">
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
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
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
            </div>
            <div class="form-row">
                <div class="form-group col-md-12 totaltopay">
                    <span>Total to Pay <strong class="ajaxTotalPrice">{{totalPrice}}</strong></span>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group text-center pay-button">
                    <input v-if="paymentType=='applepay'" @click="submitApplePayPayment()" type="button" :disabled="paymentProcessing == 1" value="Pay Now" class="btn btn-primary btn-lg extra-radius">
                    <input v-else type="submit" :disabled="paymentProcessing == 1" value="Pay Now" class="btn btn-primary btn-lg extra-radius">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group col-md-12">
                    <div class="btm-logo d-flex align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-lock"></i>
                            <p><strong>Secure Payments </strong><br/>Your order is safe and secure. We use the latest SSL encryption for all transactions</p>
                        </div>
                        <div class="text-center" style="width: 40%">
                            <img src="<?= SITE_URL ?>assets/images/checkout-trupiolet.png" alt="trust piolet">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include __DIR__ . '/checkout-vue.php'; ?>