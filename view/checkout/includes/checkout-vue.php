<!--<script src="--><?//= SITE_URL ?><!--assets/vendor/vue3/dist/vue.global.prod.js"></script>-->
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script src="<?= SITE_URL ?>assets/vendor/axios/dist/axios.min.js"></script>
<script>

    var app = new Vue({
        el: '#checkoutMain',
        data: {
            errors: [],
            gift: null,
            giftOption : true,
            offer2Option : true,
            premiumSubscription: false,
            ncfeSubscription: false,
            upsellOfferOne : false,
            upsellOfferTwo : false,
            isQualificationItems : false,
            giftEmail: null,
            firstname: '<?= $currentAccount->firstname ?>',
            lastname: '<?= $currentAccount->lastname ?>',
            phone: '<?= $currentAccount->phone ?>',
            email: '<?= $currentAccount->email ?>',
            emailConfirm: '<?= $this->user->email ?>',
            password: null,
            paymentProcessing: 0,
            orderSummaryProcessing: 0,
            address1: null,
            address2: null,
            address3: null,
            city: null,
            county: null,
            country: null,
            postcode: null,
            terms: false,
            newsletter: true,
            cartItems: [],
            cartContainsCert: false,
            accountID: '<?= CUR_ID_FRONT?>',
            premiumMonths: 0,
            totalPrice: null,
            totalPriceAmount: null,
            totalNewPrice: null,
            unitPrice: null,

            paymentType: 'stripe',

            orderCompleteUrl : '<?= SITE_URL ?>checkout/confirmation',

            stripeAPIToken: '<?= STRIPE_PUBLISHABLE_KEY ?>',

            stripe: '',
            elements: '',
            cardNumber: '',
            cardExpiry: '',
            cardCvc: '',
            cardErrors: '',
            customerID:'',
            stripePaymentUrl: '<?= SITE_URL ?>ajax?c=stripe&a=processPayment',
            stripeErrorUrl: '<?= SITE_URL ?>ajax?c=stripe&a=cardError',
            stripePaymentSuccessUrl: '<?= SITE_URL ?>ajax?c=stripe&a=successPayment',

            stripeSub: '',
            subElements: '',
            cardSubNumber: '',
            cardSubExpiry: '',
            cardSubCvc: '',
            cardSubErrors: '',

            // Premium
            stripePreSub: '',
            subPreElements: '',
            cardPreSubPreNumber: '',
            cardPreSubExpiry: '',
            cardPreSubCvc: '',
            cardPreSubErrors: '',

            // Premium NS Pay
            stripePreNs: '',
            subPreNsElements: '',
            cardPreNsNumber: '',
            cardPreNsExpiry: '',
            cardPreNsCvc: '',
            cardPreNsErrors: '',

            stripeSubscriptionUrl: '<?= SITE_URL ?>ajax?c=stripe&a=processSubscription',
            stripePreSubscriptionUrl: '<?= SITE_URL ?>ajax?c=stripe&a=processPreSubscription',
            stripeNcfeSubscriptionUrl: '<?= SITE_URL ?>ajax?c=stripe&a=processNcfeSubscription',

            stripeSubscriptionConfirmUrl: '<?= SITE_URL ?>ajax?c=stripe&a=confirmSubscription',


            stripeCreateSetupIntentUrl: '<?= SITE_URL ?>ajax?c=stripe&a=createSetupIntent',

            stripeClientSecret: null,
            stripePaymentMethod: null,

            paypalPaymentUrl: '<?= SITE_URL ?>ajax?c=paypal&a=processPayment',

            paypalCheckoutUrl: '<?= SITE_URL ?>ajax?c=paypal&a=processCheckout',
            paypalSubscriptionUrl: '<?= SITE_URL ?>ajax?c=paypal&a=processSubscription',

            paypalCompleteSubscriptionUrl: '<?= SITE_URL ?>ajax?c=paypal&a=completeSubscription',
            paypalCompleteCheckoutUrl: '<?= SITE_URL ?>ajax?c=paypal&a=completeCheckout',

            // Stripe Apple Pay
            stripePay: '',
            payElements: '',
            prButton: '',
            stripeApplePayUrl: '<?= SITE_URL ?>ajax?c=stripe&a=processApplePay',
            stripeApplePayConfirmUrl: '<?= SITE_URL ?>ajax?c=stripe&a=confirmApplePay',

            stripeOfflinePaymentUrl : '<?= SITE_URL ?>ajax?c=cart&a=offlinePayment',

            // Upsell Offer
            updateUpsellOfferUrl : '',
            upsellCheckbox: null,

            // Printed Items
            updatePrintedItemUrl : '',
            isPrintedItems : '',
            printedItems : null,
            paypalPlanId : '',

            isAssessment: <?= @$_GET['cart'] &&  $_GET['cart'] == 'assessment' ? 1 : 0 ?>
        },
        methods: {
            getCartItems: function (e){
                that = this;
                that.orderSummaryProcessing = 1;
                const url = "<?= SITE_URL ?>ajax?c=cart&a=currentCartItems&action=json";
                // axios.get(url
                // ).then(response => {
                //
                //         that.totalPrice = response.data.totalPrice;
                //         that.totalNewPrice = response.data.totalPrice;
                //         that.totalPriceAmount = response.data.totalPriceAmount;
                //         that.unitPrice = response.data.unitPrice;
                //
                //         if(this.paymentType === 'nspay'){
                //             that.totalNewPrice = that.unitPrice;
                //         }
                //         that.cartItems = response.data.items;
                //         that.cartContainsCert = response.data.cartContainsCert;
                //
                //         if(response.data.totalPriceAmount === 0){
                //             that.paymentType = 'offlinePayment';
                //         }
                //
                //         that.upsellCheckbox = response.data.isUpsell === true;
                //
                //         this.orderSummaryProcessing = 0;
                //         console.log('data', response.data);
                //
                //         $('body').trigger('cart_items_fetched');
                //
                //
                //         //console.log('upsellCheckbox', that.upsellCheckbox);
                //
                //         if(!that.upsellCheckbox) {
                //
                //             triggerCheckoutEvent(response.data);
                //
                //         } else {
                //
                //             triggeraddToCartAddonEvent();
                //
                //         }
                //
                //     }
                // );
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(response){
                        response = JSON.parse(response);
                        that.totalPrice = response.totalPrice;
                        that.totalNewPrice = response.totalPrice;
                        that.totalPriceAmount = response.totalPriceAmount;
                        that.unitPrice = response.unitPrice;

                        if(this.paymentType === 'nspay'){
                            that.totalNewPrice = that.unitPrice;
                        }
                        that.cartItems = response.items;
                        that.premiumMonths = response.premiumMonths;
                        that.cartContainsCert = response.cartContainsCert;
                        that.premiumSubscription = response.premiumSubscription;
                        that.ncfeSubscription = response.ncfeSubscription;
                        that.offer2Option = response.offer2Option;
                        that.isPrintedItems = response.isPrintedItems;
                        that.isQualificationItems = response.isQualificationItems;
                        that.printedItems = response.printedItems;
                        that.paypalPlanId = response.paypalPlanId;
                        if(that.premiumSubscription === true) {
                            that.paymentType = 'premiumSubscription';
                            //$("#coupon-box").css('display', 'none');
                            $(".cards-details-box").css('display', 'block');
                            $(".card-payments-method").css('display', 'none');
                            //$(".upsell-box").css('display', 'none');
                            $(".giftOptionBox").css('display', 'none');
                            $(".upsell-box.offer2-box").css('display', 'none');
                            //$(".coupon-amount").html("");
                        }else if(that.ncfeSubscription === true) {
                            that.paymentType = 'ncfeSubscription';
                            $("#coupon-box").css('display', 'none');
                            $(".cards-details-box").css('display', 'block');
                            $(".card-payments-method").css('display', 'none');
                            $(".upsell-box").css('display', 'none');
                            $(".giftOptionBox").css('display', 'none');
                            $(".upsell-box.offer2-box").css('display', 'none');
                            $(".coupon-amount").html("");
                        }else {
                            $(".cards-details-box").css('display', 'none');
                            $(".card-payments-method").css('display', 'block');
                            $(".giftOptionBox").css('display', 'block');
                            $(".upsell-box.offer2-box").css('display', 'block');

                            //$(".upsell-box").css('display', 'block');
                        }

                        if(that.isQualificationItems === true) {
                            $("#coupon-box").css('display', 'none');
                            that.offer2Option = false;
                        }

                        if(that.cartContainsCert === false && that.isPrintedItems === false && that.premiumSubscription === false && that.ncfeSubscription === false) {
                            $(".giftOptionBox").css('display', 'block');
                        }else{
                          $(".giftOptionBox").css('display', 'none');
                        }

                        if(that.cartContainsCert === true || that.isPrintedItems === true) {
                          $(".delivery_box").css('display', 'block');
                        }else{
                            $(".delivery_box").css('display', 'none');
                        }

                        if(response.totalPriceAmount === 0){
                            that.paymentType = 'offlinePayment';
                        }else if(that.paymentType == 'offlinePayment') {
                            window.location.reload();
                        }

                        that.upsellOfferTwo = response.isUpsell === true;

                        that.orderSummaryProcessing = 0;

                        //
                        $(".offer1-box .selectedUpsellOffer").css('width', $(".offer1-box").outerWidth() + 'px');
                        $(".offer1-box .selectedUpsellOffer").css('height', $(".offer1-box").outerHeight() + 'px');

                        $(".offer2-box .selectedUpsellOffer").css('width', $(".offer2-box").outerWidth() + 'px');
                        $(".offer2-box .selectedUpsellOffer").css('height', $(".offer2-box").outerHeight() + 'px');

                        console.log('data', response);

                        $('body').trigger('cart_items_fetched');


                        //console.log('upsellCheckbox', that.upsellCheckbox);

                        if(!that.upsellOfferTwo) {

                            jQuery(document).ready(function ($) {

                                triggerCheckoutEvent(response);

                            });

                        } else {

                            triggeraddToCartAddonEvent();

                        }
                    },
                    error: function(xhr, status, error){
                        console.error(xhr);
                    }
                });
            },
            removeFromCart: function (id, event){
                that = this;
                that.orderSummaryProcessing = 1;
                console.log('event target', event.target);
                console.log('jquery event target', $(event.target));

                const url = "<?= SITE_URL ?>ajax?c=cart&a=removeItem&action=json&id="+id;


                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(response){
                        response = JSON.parse(response);
                        that.getCartItems();
                        that.orderSummaryProcessing = 0;

                        //console.log('removeFromCart');

                        let brand = 'NSA';

                        let products = [];

                        //let $this = $(this);
                        let $this = $(event.target);

                        let item = $this.closest('.cart-items');

                        console.log('order-summery item', item);

                        let course_title = $.trim( item.find('.nsa_course_title').text() );

                        console.log(course_title);

                        let cat_names = item.attr('data-course-cats');
                        let course_type = item.attr('data-course_type');
                        //let course_id = item.attr('data-course_id');
                        //let course_id = item.attr('data-product_id');
                        let course_id = item.attr('data-oldproductid');

                        if(!course_id || SITE_TYPE == 'us') {
                            course_id = item.attr('data-course-id');
                        }
                        // Uncheck Printed item if selected
                        $('#printed'+item.attr('data-course-id')).prop('checked', false);
                        //let price_html = item.find('.woocommerce-Price-amount.amount').clone();
                        //price_html.find('span').remove();
                        //let course_price = parseFloat(price_html.text()).toFixed(2);

                        let course_price = item.find('.course_price').text().replace("£", "").replace("$", "");
                        course_price = parseFloat(course_price).toFixed(2);


                        let course_object = {
                            'name': course_title,
                            'id': course_id,
                            'price': course_price,
                            'brand': brand,
                            'category': cat_names,
                            'variant': course_type,
                            'quantity': 1
                        };

                        products.push(course_object);

                        dataLayer.push({
                            'event': 'removeFromCart',
                            'ecommerce': {
                                'remove': {
                                    'products': products
                                }
                            }
                        });
                        console.log('removeFromCart event triggered on the Checkout Page');
                    },
                    error: function(xhr, status, error){
                        console.error(xhr);
                    }
                });
            },
            validateForm: function (e) {
                that = this;
                var button = '';
                if(e === 'applepay'){
                    button = 'applepay';
                }
                else if(e === 'paypalSubscription'){
                    button = 'paypalSubscription';
                }
                else if(e === 'paypalCheckout'){
                    button = 'paypalCheckout';
                }
                else{
                    e.preventDefault();
                }
                //alert(button);

                var regularExpression = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{6,16}$/;

                that.errors = [];
                that.cardErrors = false;
                $("#card-errors").html();
                if (this.cartItems.length === 0) {
                    that.errors.push('Your cart is empty, so there is nothing to checkout.');
                    $('html,body').animate({
                        scrollTop: $(".checkout-details").offset().top
                    }, 500);
                    return false;
                }

                if (!this.firstname) {
                    that.errors.push('Firstname required.');
                }
                if (!this.lastname) {
                    that.errors.push('Lastname required.');
                }
                if (!this.email) {
                    that.errors.push('Email required.');
                }

                if(this.accountID === ''){
                    if (!this.password) {
                        that.errors.push('Password required.');
                    }
                }

                if(this.gift && !this.giftEmail){
                    that.errors.push('Please enter the email address of the person you are gifting this to.');
                }

                if(this.cartContainsCert === true || this.isPrintedItems === true){
                    if (!this.address1) {
                        that.errors.push('House Name/No required.');
                    }
                    if (!this.city) {
                        that.errors.push('City required.');
                    }
                    if (!this.county) {
                        that.errors.push('County required.');
                    }
                    if (!this.postcode) {
                        that.errors.push('Postcode required.');
                    }
                    if (!this.country) {
                        that.errors.push('Country required.');
                    }
                }

                if (!this.terms) {
                    that.errors.push('You must accept the terms & conditions before continuing.');
                }

                if(this.accountID === '' && this.email){

                    const url = "<?= SITE_URL ?>ajax?c=cart&a=validateEmail&response=json&email="+this.email;
                    // axios.get(url
                    // ).then(response => {
                    //         if(response.data.error === true){
                    //             this.errors.push(response.data.message);
                    //         }
                    //
                    //         if (this.errors.length === 0) {
                    //             if(button == 'applepay'){
                    //                 this.activePaymentMethod('applepay');
                    //             }else{
                    //                 this.submitPaymentForm();
                    //             }
                    //         }else{
                    //             $('html,body').animate({
                    //                 scrollTop: $(".checkout-details").offset().top
                    //             }, 500);
                    //
                    //         }
                    //     }
                    // );
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(response){
                            response = JSON.parse(response);
                            console.log(response);
                            if(response.error === true){
                                that.errors.push(response.message);
                            }

                            if (that.errors.length === 0) {
                                if(button == 'applepay'){
                                    that.activePaymentMethod('applepay');
                                }
                                else if(button == 'paypalSubscription'){
                                    that.activePaymentMethod('paypalSubscription');
                                }
                                else if(button == 'paypalCheckout'){
                                    that.activePaymentMethod('paypalCheckout');
                                }
                                else{
                                    that.submitPaymentForm();
                                }
                            }else{
                                $('html,body').animate({
                                    scrollTop: $(".checkout-details").offset().top
                                }, 500);

                            }
                        },
                        error: function(xhr, status, error){
                            console.error(xhr);
                        }
                    });
                }else{

                    if (that.errors.length === 0) {
                        if(button === 'applepay'){
                            that.activePaymentMethod('applepay');
                        }
                        else if(button == 'paypalSubscription'){
                            that.activePaymentMethod('paypalSubscription');
                        }
                        else if(button == 'paypalCheckout'){
                            that.activePaymentMethod('paypalCheckout');
                        }
                        else{
                            that.submitPaymentForm();
                        }
                    }else{
                        $('html,body').animate({
                            scrollTop: $(".checkout-details").offset().top
                        }, 500);
                    }
                }
                return false;

            },
            activePaymentMethod: function (paymentType){
                that = this;

                if(paymentType != 'offlinePayment'){
                    $(".nav-tabs a[href='#" + paymentType + "tab']").tab('show');
                }
                that.paymentType = paymentType;
                if (paymentType == 'nspay') {
                    that.totalNewPrice = this.unitPrice;
                }else {
                    that.totalNewPrice = this.totalPrice;
                }


            },
            submitPaymentForm:function (){
                that = this;
                if (this.paymentType == 'stripe') {
                    that.submitStripePayment();
                }else if (this.paymentType == 'paypal') {
                    that.submitPaypalPayment();
                }else if (this.paymentType == 'nspay') {
                    that.submitNSPayPayment();
                }else if (this.paymentType == 'applepay') {
                    that.submitApplePayPayment();
                }else if (this.paymentType == 'offlinePayment') {
                    that.submitOfflinePayment();
                }else if (this.paymentType == 'premiumSubscription') {
                    that.submitPremiumSubPayment();
                }else if (this.paymentType == 'premiumNSPaySubscription') {
                    that.submitPremiumNSPayPayment();
                }else if (this.paymentType == 'ncfeSubscription') {
                    that.submitNcfeSubPayment();
                }
                // else if (this.paymentType == 'paypalSubscription') {
                //     alert("submit paypal subscription");
                // }
            },
            submitStripePayment: function () {
                that = this;
                that.paymentProcessing = 1;

                that.stripe.createPaymentMethod(
                    'card',
                    this.cardNumber
                ).then(function (result) {
                    if (result.error) {
                        that.cardErrors = result.error.message;
                        console.log(result.error.message);

                        $('html, body').animate({
                            scrollTop: $(".card-payments-method").offset().top - 200
                        }, 500);
                        that.paymentProcessing = 0;
                    } else {
                        //this.paymentProcessing = 0;
                        // Send paymentMethod.id to server
                        that.stripeTokenHandler(result.paymentMethod);
                    }
                });
                return false;
            },
            stripeTokenHandler: function (paymentMethod) {
                that = this;
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', paymentMethod.id);
                form.appendChild(hiddenInput);

                var stripe = Stripe( this.stripeAPIToken );
                var stripeConfirmUrl = this.stripePaymentSuccessUrl;

                $.ajax({
                    url: this.stripePaymentUrl,
                    type: 'post',
                    data: $("#payment-form").serialize(),
                    dataType: 'JSON',
                }).done(function (response) {
                    if(response.error){
                        if(response.requires_action === true){
                            // Let Stripe.js handle the rest of the payment flow.
                            stripe.confirmCardPayment(response.payment_intent_client_secret).then(function(result) {
                                if(result.error) {
                                    $.ajax({
                                        url: that.stripeErrorUrl,
                                        type: 'post',
                                        data: {
                                            error: result.error
                                        },
                                        dataType: 'JSON',
                                    }).done(function (response) {
                                        window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                                    });
                                } else if(result.paymentIntent.status === "succeeded") {
                                    //that.paymentProcessing = 1;
                                    $.ajax({
                                        url: stripeConfirmUrl+'&paymentIntent='+ result.paymentIntent.id,
                                        type: 'post',
                                        data: '',
                                        dataType: 'JSON',
                                    }).done(function (response1) {
                                        window.location.href = '<?= SITE_URL ?>checkout/confirmation';
                                    });
                                }else{
                                    window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                                }
                            });
                        }else{
                            window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                        }
                    }
                    else if(response.success === true){
                        $.ajax({
                            url: stripeGiftConfirmUrl+'&paymentIntent='+ response.payment_intent,
                            type: 'post',
                            data: '',
                            dataType: 'JSON',
                        }).done(function (response1) {
                            window.location.href = '<?= SITE_URL ?>checkout/confirmation';
                        });
                    }else{
                        window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                    }
                    console.log(response);
                    return false;
                });

                return false;
            },

            submitApplePayPayment: function () {
                that = this;
                that.paymentProcessing = 1;
                var stripeApplePayUrl = this.stripeApplePayUrl;
                var stripeApplePayConfirmUrl = this.stripeApplePayConfirmUrl;
                var stripePay = Stripe( this.stripeAPIToken );
                console.log(stripePay);
                console.log(stripeApplePayUrl);
                var paymentRequest = this.stripePay.paymentRequest({
                    country: 'GB',
                    currency: 'gbp',
                    total: {
                        label: 'New Skills Academy Checkout',
                        amount: this.totalPriceAmount * 100,
                    },
                    requestPayerName: true,
                    requestPayerEmail: true,
                });
                paymentRequest.canMakePayment().then(function(result) {
                    //that = this;
                    if (result && (result.applePay === true)) {
                        //prButton.mount('#payment-request-button');
                        paymentRequest.show();
                    } else {
                        $(".apple-button").css('display', 'none');
                        $(".nav-tabs .nav-item").css('width','calc(33% - 15px)');
                        document.getElementById('payment-request-button').style.display = 'none';
                    }
                });

                paymentRequest.on('paymentmethod', function(ev) {
                    //that = this;
                    $.ajax({
                        url: stripeApplePayUrl,
                        type: 'post',
                        data: $("#payment-form").serialize(),
                        dataType: 'JSON',
                    }).done(function (response) {
                        //return false;
                        if(response.clientSecret){
                            //that.paymentProcessing = 0;
                            // Create payment method and confirm payment intent.
                            stripePay.confirmCardPayment(
                                response.clientSecret,
                                {payment_method: ev.paymentMethod.id},
                                {handleActions: false}
                            ).then(function(confirmResult) {

                                if (confirmResult.error) {
                                    // Report to the browser that the payment failed, prompting it to
                                    // re-show the payment interface, or show an error message and close
                                    // the payment interface.
                                    ev.complete('fail');
                                    $.ajax({
                                        url: that.stripeErrorUrl,
                                        type: 'post',
                                        data: {
                                            error: confirmResult.error
                                        },
                                        dataType: 'JSON',
                                    }).done(function (response) {
                                        window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                                    });
                                } else {
                                    // Report to the browser that the confirmation was successful, prompting
                                    // it to close the browser payment method collection interface.
                                    ev.complete('success');
                                    // Check if the PaymentIntent requires any actions and if so let Stripe.js
                                    // handle the flow. If using an API version older than "2019-02-11" instead
                                    // instead check for: `paymentIntent.status === "requires_source_action"`.
                                    if (confirmResult.paymentIntent.status === "requires_action" || confirmResult.paymentIntent.status === "requires_source_action") {
                                        // Let Stripe.js handle the rest of the payment flow.
                                        stripePay.confirmCardPayment(confirmResult.paymentIntent.client_secret).then(function(result) {
                                            if(result.error) {
                                                window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                                            } else if(result.paymentIntent.status === "succeeded") {
                                                //that.paymentProcessing = 1;
                                                $.ajax({
                                                    url: stripeApplePayConfirmUrl+'&paymentIntent='+ result.paymentIntent.id,
                                                    type: 'post',
                                                    data: '',
                                                    dataType: 'JSON',
                                                }).done(function (response1) {
                                                    window.location.href = '<?= SITE_URL ?>checkout/confirmation';
                                                });
                                            }else{
                                                window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                                            }
                                        });
                                    }
                                    else {
                                        // The payment has succeeded.
                                        $.ajax({
                                            url: stripeApplePayConfirmUrl+'&paymentIntent='+ confirmResult.paymentIntent.id,
                                            type: 'post',
                                            data: '',
                                            dataType: 'JSON',
                                        }).done(function (response1) {
                                            window.location.href = '<?= SITE_URL ?>checkout/confirmation';
                                        });
                                    }
                                }
                            });
                        }else{
                            window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                        }
                        //that.paymentProcessing = 0;
                        console.log(response);
                        return false;
                    });
                });
                paymentRequest.on('cancel', function(ev) {
                    window.location.href = '<?= SITE_URL ?>checkout';
                });

                return false;
            },
            submitNSPayPayment: function () {
                that = this;
                that.paymentProcessing = 1;

                that.stripeSub.createPaymentMethod(
                    'card',
                    this.cardSubNumber
                ).then(function (result) {
                    if (result.error) {
                        that.cardSubErrors = result.error.message;
                        console.log(result.error.message);
                        $('html, body').animate({
                            scrollTop: $(".payment-method").offset().top - 200
                        }, 500);
                        that.paymentProcessing = 0;
                    } else {
                        // Send paymentMethod.id to server
                        that.stripeCreateSubscription(result.paymentMethod);
                    }
                });
                return false;
            },
            submitPremiumNSPayPayment: function () {
                that = this;
                that.paymentProcessing = 1;

                that.stripePreNs.createPaymentMethod(
                    'card',
                    this.cardPreNsNumber
                ).then(function (result) {
                    if (result.error) {
                        that.cardPreNsErrors = result.error.message;
                        console.log(result.error.message);
                        $('html, body').animate({
                            scrollTop: $(".payment-method").offset().top - 200
                        }, 500);
                        that.paymentProcessing = 0;
                    } else {
                        // Send paymentMethod.id to server
                        that.stripeCreateSubscription(result.paymentMethod, 'premium');
                    }
                });
                return false;
            },
            stripeCreateSubscription: function (paymentMethod, subscriptionType = 'normal') {
                that = this;

                var stripePayment = that.stripeSub;
                var stripeCardNumber = that.cardSubNumber;

                if(subscriptionType == 'premium'){
                    stripePayment = that.stripePreNs;
                    stripeCardNumber = that.cardPreNsNumber;
                }

                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'paymentMethod');
                hiddenInput.setAttribute('value', paymentMethod.id);
                form.appendChild(hiddenInput);

                var hiddenInput1 = document.createElement('input');
                hiddenInput1.setAttribute('type', 'hidden');
                hiddenInput1.setAttribute('name', 'last4');
                hiddenInput1.setAttribute('value', paymentMethod.card.last4);
                form.appendChild(hiddenInput1);

                var hiddenInput2 = document.createElement('input');
                hiddenInput2.setAttribute('type', 'hidden');
                hiddenInput2.setAttribute('name', 'subscriptionType');
                hiddenInput2.setAttribute('value', subscriptionType);
                form.appendChild(hiddenInput2);

                $.ajax({
                    url: this.stripeSubscriptionUrl,
                    type: 'post',
                    data: $("#payment-form").serialize(),
                    dataType: 'JSON',
                }).done(function (response) {
                    if(response.clientSecret){
                        //that.paymentProcessing = 0;
                        // Create payment method and confirm payment intent.
                        stripePayment.confirmCardPayment(response.clientSecret, {
                            payment_method: {
                                card: stripeCardNumber,
                                billing_details: {
                                    name: that.firstname + ' ' + that.lastname,
                                },
                            }
                        }).then(function( result ){
                            if(result.error) {
                                $.ajax({
                                    url: that.stripeErrorUrl,
                                    type: 'post',
                                    data: {
                                        error: result.error
                                    },
                                    dataType: 'JSON',
                                }).done(function (response) {
                                    window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                                });
                            } else if(result.paymentIntent.status == "succeeded") {
                                //that.paymentProcessing = 1;
                                $.ajax({
                                    url: that.stripeSubscriptionConfirmUrl+'&method=stripe_sub&subscriptionId='+ response.subscriptionId,
                                    type: 'post',
                                    data: '',
                                    dataType: 'JSON',
                                }).done(function (response) {
                                    window.location.href = '<?= SITE_URL ?>checkout/confirmation';
                                });
                            }else{
                                window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                            }
                        });
                    }else{
                        window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                    }
                    //that.paymentProcessing = 0;
                    console.log(response);
                    return false;
                });

                return false;
            },
            submitPremiumSubPayment: function () {
                that = this;
                that.paymentProcessing = 1;

                that.stripePreSub.createPaymentMethod(
                    'card',
                    this.cardPreSubNumber
                ).then(function (result) {
                    if (result.error) {
                        that.cardPreSubErrors = result.error.message;
                        console.log(result.error.message);
                        $('html, body').animate({
                            scrollTop: $(".payment-method").offset().top - 200
                        }, 500);
                        that.paymentProcessing = 0;
                    } else {
                        // Send paymentMethod.id to server
                        console.log(result);
                        that.stripeCreatePremiumSubscription(result.paymentMethod);
                    }
                });
                return false;
            },
            stripeCreatePremiumSubscription: function (paymentMethod) {
                that = this;
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'paymentMethod');
                hiddenInput.setAttribute('value', paymentMethod.id);
                form.appendChild(hiddenInput);

                var hiddenInput1 = document.createElement('input');
                hiddenInput1.setAttribute('type', 'hidden');
                hiddenInput1.setAttribute('name', 'last4');
                hiddenInput1.setAttribute('value', paymentMethod.card.last4);
                form.appendChild(hiddenInput1);

                $.ajax({
                    url: this.stripePreSubscriptionUrl,
                    type: 'post',
                    data: $("#payment-form").serialize(),
                    dataType: 'JSON',
                }).done(function (response) {
                    if(response.clientSecret){
                        //that.paymentProcessing = 0;
                        // Create payment method and confirm payment intent.
                        that.stripePreSub.confirmCardPayment(response.clientSecret, {
                            payment_method: {
                                card: that.cardPreSubNumber,
                                billing_details: {
                                    name: that.firstname + ' ' + that.lastname,
                                },
                            }
                        }).then(function( result ){
                            if(result.error) {
                                $.ajax({
                                    url: that.stripeErrorUrl,
                                    type: 'post',
                                    data: {
                                        error: result.error
                                    },
                                    dataType: 'JSON',
                                }).done(function (response) {
                                    window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                                });
                            } else if(result.paymentIntent.status == "succeeded") {
                                //that.paymentProcessing = 1;
                                $.ajax({
                                    url: that.stripeSubscriptionConfirmUrl+'&method=stripe_pre_sub&subscriptionId='+ response.subscriptionId,
                                    type: 'post',
                                    data: '',
                                    dataType: 'JSON',
                                }).done(function (response) {
                                    window.location.href = '<?= SITE_URL ?>checkout/confirmation';
                                });
                            }else{
                                window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                            }
                        });
                    }
                    else if(response.status === 'trialing'){
                        $.ajax({
                            url: that.stripeSubscriptionConfirmUrl+'&method=stripe_pre_sub&subscriptionId='+ response.subscriptionId,
                            type: 'post',
                            data: '',
                            dataType: 'JSON',
                        }).done(function (response) {
                            window.location.href = '<?= SITE_URL ?>checkout/confirmation';
                        });
                    }else{
                        window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                    }
                    //that.paymentProcessing = 0;
                    console.log(response);
                    return false;
                });

                return false;
            },

            submitNcfeSubPayment: function () {
                that = this;
                that.paymentProcessing = 1;

                that.stripePreSub.createPaymentMethod(
                    'card',
                    this.cardPreSubNumber
                ).then(function (result) {
                    if (result.error) {
                        that.cardPreSubErrors = result.error.message;
                        console.log(result.error.message);
                        $('html, body').animate({
                            scrollTop: $(".payment-method").offset().top - 200
                        }, 500);
                        that.paymentProcessing = 0;
                    } else {
                        // Send paymentMethod.id to server
                        console.log(result);
                        that.stripeCreateNcfeSubscription(result.paymentMethod);
                    }
                });
                return false;
            },
            stripeCreateNcfeSubscription: function (paymentMethod) {
                that = this;
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'paymentMethod');
                hiddenInput.setAttribute('value', paymentMethod.id);
                form.appendChild(hiddenInput);

                var hiddenInput1 = document.createElement('input');
                hiddenInput1.setAttribute('type', 'hidden');
                hiddenInput1.setAttribute('name', 'last4');
                hiddenInput1.setAttribute('value', paymentMethod.card.last4);
                form.appendChild(hiddenInput1);

                $.ajax({
                    url: this.stripeNcfeSubscriptionUrl,
                    type: 'post',
                    data: $("#payment-form").serialize(),
                    dataType: 'JSON',
                }).done(function (response) {
                    if(response.clientSecret){
                        //that.paymentProcessing = 0;
                        // Create payment method and confirm payment intent.
                        that.stripePreSub.confirmCardPayment(response.clientSecret, {
                            payment_method: {
                                card: that.cardPreSubNumber,
                                billing_details: {
                                    name: that.firstname + ' ' + that.lastname,
                                },
                            }
                        }).then(function( result ){

                            if(result.error) {
                                $.ajax({
                                    url: that.stripeErrorUrl,
                                    type: 'post',
                                    data: {
                                        error: result.error
                                    },
                                    dataType: 'JSON',
                                }).done(function (response) {
                                    window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                                });
                            } else if(result.paymentIntent.status == "succeeded") {
                                //that.paymentProcessing = 1;
                                $.ajax({
                                    url: that.stripeSubscriptionConfirmUrl+'&method=stripe_ncfe_sub&subscriptionId='+ response.subscriptionId,
                                    type: 'post',
                                    data: '',
                                    dataType: 'JSON',
                                }).done(function (response) {
                                    window.location.href = '<?= SITE_URL ?>checkout/confirmation';
                                });
                            }else{
                                window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                            }
                        });
                    }else{
                        window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
                    }
                    //that.paymentProcessing = 0;
                    console.log(response);
                    return false;
                });

                return false;
            },
            configurePaypal: function () {

            },
            submitPaypalPayment: function () {
                that = this;
                that.paymentProcessing = 1;
                $.ajax({
                    url: this.paypalPaymentUrl,
                    type: 'post',
                    data: $("#payment-form").serialize(),
                    dataType: 'JSON',
                }).done(function (response) {
                    if(response.success === true){
                        window.location.href = response.redirect_url;
                    }else{
                        that.errors.push(response.data);
                        $('html,body').animate({
                            scrollTop: $(".checkout-details").offset().top
                        }, 500);
                        that.paymentProcessing = 0;
                    }
                    console.log(response);
                });
                return false;
            },
            submitOfflinePayment: function () {
                that = this;
                this.paymentProcessing = 1;


                $.ajax({
                    url: this.stripeOfflinePaymentUrl,
                    type: 'post',
                    data: $("#payment-form").serialize(),
                    dataType: 'JSON',
                }).done(function (response) {
                    if(response.success === true) {
                        window.location.href = '<?= SITE_URL ?>checkout/confirmation';
                    }  else {
                        window.location.href = '<?= SITE_URL ?>checkout?error=failed';
                    }
                    console.log(response);

                });

                return false;
            },
            updateUpsellOfferOne: function (event) {
                that = this;
                that.paymentProcessing = 1;
                //if (event.target.checked) {
                if (event) {
                    that.updateUpsellOfferUrl = '<?= SITE_URL?>ajax?c=cart&a=addPremiumSubscription&action=json';
                }else{
                    that.updateUpsellOfferUrl = '<?= SITE_URL?>ajax?c=cart&a=removePremiumSubscription&action=json';
                }
                $.ajax({
                    url: this.updateUpsellOfferUrl,
                    type: 'post',
                    dataType: 'JSON',
                }).done(function (response) {
                    that.getCartItems();
                    //that.getCartTotals();
                    that.paymentProcessing = 0;
                });
            },
            updateUpsellOffer: function (event) {
                that = this;
                that.paymentProcessing = 1;
                if (event.target.checked) {
                    //if (event) {
                    that.updateUpsellOfferUrl = '<?= SITE_URL?>ajax?c=cart&a=addUpsellToCartJson';
                }else{
                    that.updateUpsellOfferUrl = '<?= SITE_URL?>ajax?c=cart&a=removeUpsellToCartJson';
                }
                $.ajax({
                    url: this.updateUpsellOfferUrl,
                    type: 'post',
                    dataType: 'JSON',
                }).done(function (response) {
                    that.getCartItems();
                    //that.getCartTotals();
                    that.paymentProcessing = 0;
                });
            },
            getPostCodeAddresses: function (){
                $(".postCodeMessage").css('display','none');
                $(".postCodeMessage").html('');
                var postCode = $("#postcode").val();
                $(".postCodeMessage").css('display','block');
                $('.postCodeAddress').css('display', 'none');

                $('#myAddresses').html('<option value="">Please select your address</option>');
                if (postCode == ""){
                    $("#postcode").focus()
                    $(".postCodeMessage").html('<span class="email help-block alert-danger" style="display: block; padding: 5px;">Please Enter Your PostCode</span>');
                }else{
                    $(".postCodeMessage").html('<span class="help-block alert-info" style="display: block; padding: 5px;">Looking up Postcode... </span>');
                    try {
                        // axios.get('https://api.ideal-postcodes.co.uk/v1/postcodes/' + postCode, {
                        //     params: {
                        //         api_key: 'ak_k1yndbuqM07GSCeo6sT8Zxhz3LZTD'
                        //     }
                        // }).then(response => {
                        //     console.log(response.data.result);
                        //     $.each(response.data.result, function(key, value) {
                        //         $('#myAddresses')
                        //             .append($("<option></option>")
                        //                 .attr("value", value.line_1 + "|||" + value.line_2 + "|||" + value.line_3 + "|||" + value.post_town + "|||" + value.county + "|||" + value.country)
                        //                 .text(value.line_1 + " " + value.line_2 + " " + value.line_3));
                        //     });
                        //     $('.postCodeAddress').css('display', 'block');
                        //     $('.postCodeMessage').css('display', 'none');
                        // }).catch((error) => {
                        //     $(".postCodeMessage").html('<span class="email help-block alert-warning" style="display: block; padding: 5px;">Your postcode could not be found. Please type in your address</span>');
                        // });

                        $.ajax({
                            type: "GET",
                            url: 'https://api.ideal-postcodes.co.uk/v1/postcodes/' + postCode + '?api_key=ak_k1yndbuqM07GSCeo6sT8Zxhz3LZTD',
                            success: function(response){
                                //response = JSON.parse(response);
                                $.each(response.result, function(key, value) {
                                    $('#myAddresses')
                                        .append($("<option></option>")
                                            .attr("value", value.line_1 + "|||" + value.line_2 + "|||" + value.line_3 + "|||" + value.post_town + "|||" + value.county + "|||" + value.country)
                                            .text(value.line_1 + " " + value.line_2 + " " + value.line_3));
                                });
                                $('.postCodeAddress').css('display', 'block');
                                $('.postCodeMessage').css('display', 'none');
                            },
                            error: function(xhr, status, error){
                                $(".postCodeMessage").html('<span class="email help-block alert-warning" style="display: block; padding: 5px;">Your postcode could not be found. Please type in your address</span>');
                            }
                        });

                    } catch (error) {
                        console.log(error);
                    }
                }
            },
            updatePrintedItem: function (event = false, courseID) {
                that = this;
                that.paymentProcessing = 1;
                //if (event.target.checked) {
                if(event){
                    //if (event) {
                    that.updatePrintedItemUrl = '<?= SITE_URL?>ajax?c=cart&a=addPrintedCourse&action=json&id='+courseID;
                }else{
                    that.updatePrintedItemUrl = '<?= SITE_URL?>ajax?c=cart&a=removePrintedCourse&action=json&id='+courseID;
                }
                $.ajax({
                    url: this.updatePrintedItemUrl,
                    type: 'get',
                    dataType: 'JSON',
                }).done(function (response) {
                    that.getCartItems();
                    //that.getCartTotals();
                    that.paymentProcessing = 0;
                });
            },
            selectAddress: function (){
                that = this;
                var address = $("#myAddresses").val();
                if(address != ""){
                    var addr = address.split("|||");
                    that.address1 = addr[0];
                    that.address2 = addr[1];
                    that.address3 = addr[2];
                    that.city = addr[3];
                    that.county = addr[4];
                    that.country = addr[5];
                }
            },
            toggleGift: function (enable = true) {
                that = this;
                if (enable === true) {
                    $("#giftOptions").slideDown();
                    that.gift = 1;
                    $(".lookingGift").slideUp();
                } else {
                    $("#giftOptions").slideUp();
                    that.gift = null;
                    $(".lookingGift").slideDown();
                }
            },
            haveCoupon: function () {
                // $(".coupon .couponCode").slideDown();
                // $(".coupon .haveCoupon").slideUp();
                // $(".coupon .couponCode").css('display', 'block');
                // $(".coupon .haveCoupon").css('display', 'none');
                // $("#couponCode").focus();
            },
            submitPaypalSubscription: function (e) {
                that = this;
                that.paymentProcessing = 1;
                $.ajax({
                    url: this.paypalSubscriptionUrl,
                    type: 'post',
                    data: $("#payment-form").serialize(),
                    dataType: 'JSON',
                }).done(function (response) {
                    if(response.success === true){
                        window.location.href = response.redirect_url;
                    }else{
                        that.errors.push(response.data);
                        $('html,body').animate({
                            scrollTop: $(".checkout-details").offset().top
                        }, 500);
                        that.paymentProcessing = 0;
                    }
                    console.log(response);
                });
                return false;
            },
            submitPaypalSubscriptionPayment: function () {
                that = this;
                that.paymentProcessing = 1;
                var form = document.getElementById('payment-form');

                var hiddenInput2 = document.createElement('input');
                hiddenInput2.setAttribute('type', 'hidden');
                hiddenInput2.setAttribute('name', 'subscriptionType');
                hiddenInput2.setAttribute('value', 'premium');
                form.appendChild(hiddenInput2);

                $.ajax({
                    url: this.paypalSubscriptionUrl,
                    type: 'post',
                    data: $("#payment-form").serialize(),
                    dataType: 'JSON',
                }).done(function (response) {
                    if(response.success === true){
                        window.location.href = response.redirect_url;
                    }else{
                        that.errors.push(response.data);
                        $('html,body').animate({
                            scrollTop: $(".checkout-details").offset().top
                        }, 500);
                        that.paymentProcessing = 0;
                    }
                    console.log(response);
                });
                return false;
            },
            setPaypalSubscription: function(resp) {
                that = this;

                window.paypal.Buttons({
                    style: {
                        shape: 'pill',
                        color: 'gold',
                        layout: 'vertical',
                    },
                    // onClick is called when the button is clicked
                    onClick: function(data, actions) {

                        const paypalSubscriptionUrl = that.paypalSubscriptionUrl;
                        const queryParams = '&firstname='+that.firstname+'&lastname='+that.lastname+'&email='+that.email+'&emailConfirm='+that.emailConfirm+'&password='+that.password+'&phone='+that.phone+'&postcode='+that.postcode+'&gift='+that.gift+'&giftEmail='+that.giftEmail+'&newsletter='+that.newsletter;
                        that.paymentProcessing = 1;

                        // You must return a promise from onClick to do async validation
                        return fetch(paypalSubscriptionUrl+queryParams, {
                            method: 'post',
                            headers: {
                                'content-type': 'application/json'
                            }
                        }).then(function(res) {
                            return res.json();
                        }).then(function(data) {

                            // If there is a validation error, reject, otherwise resolve
                            if (data.validationError) {
                                //document.querySelector('#error').classList.remove('hidden');
                                return actions.reject();
                            } else {
                                return actions.resolve();
                            }
                        });
                    },

                    createSubscription: function(data, actions) {
                        return actions.subscription.create({
                            'plan_id': that.paypalPlanId // Creates the subscription
                        });
                    },
                    onApprove: function(data, actions) {
                        that.paymentProcessing = 1;

                        $.ajax({
                            url: that.paypalCompleteSubscriptionUrl+'&paypalSubscriptionID='+data.subscriptionID,
                            type: 'get',
                            dataType: 'JSON',
                        }).done(function (response) {
                            if(response.error === false) {
                                window.location.href = '<?= SITE_URL ?>checkout/confirmation';
                            }  else {
                                window.location.href = '<?= SITE_URL ?>checkout?error=failed';
                            }
                        });

                        //alert('You have successfully created subscription ' + data.subscriptionID); // Optional message given to subscriber
                    }
                }).render('#paypal-subscription-button-container'); // Renders the PayPal button
                    //.render(this.$refs.paypal);
            },

            setPaypalCheckout: function(resp) {
                that = this;

                window.paypal.Buttons({
                    style: {
                        shape: 'pill',
                        color: 'gold',
                        layout: 'vertical',
                    },
                    // onClick is called when the button is clicked
                    onClick: function(data, actions) {

                        const paypalCheckoutUrl = that.paypalCheckoutUrl;
                        let queryParams = '&firstname='+that.firstname+'&lastname='+that.lastname+'&email='+that.email+'&emailConfirm='+that.emailConfirm+'&password='+that.password+'&phone='+that.phone+'&postcode='+that.postcode+'&newsletter='+that.newsletter;

                        if(that.gift){
                            queryParams += '&gift='+ that.gift + '&giftEmail=' + that.giftEmail;
                        }
                        if(that.address1){
                            queryParams += '&address1='+ that.address1;
                        }
                        if(that.address2){
                            queryParams += '&address2='+ that.address2;
                        }
                        if(that.address3){
                            queryParams += '&address3='+ that.address3;
                        }
                        if(that.city){
                            queryParams += '&city='+ that.city;
                        }
                        if(that.county){
                            queryParams += '&county='+ that.county;
                        }
                        if(that.country){
                            queryParams += '&county='+ that.country;
                        }

                        if(that.isAssessment){
                            queryParams += '&cart=assessment';
                        }


                        that.paymentProcessing = 1;

                        // You must return a promise from onClick to do async validation
                        return fetch(paypalCheckoutUrl+queryParams, {
                            method: 'post',
                            headers: {
                                'content-type': 'application/json'
                            }
                        }).then(function(res) {
                            return res.json();
                        }).then(function(data) {

                            // If there is a validation error, reject, otherwise resolve
                            if (data.validationError) {
                                //document.querySelector('#error').classList.remove('hidden');
                                actions.reject();
                                window.location.href = '<?= SITE_URL ?>checkout?error=failed';
                                return true;
                            } else {
                                return actions.resolve();
                            }
                        });
                    },

                    createOrder: function(data, actions) {
                        // Set up the transaction
                        const totalPayment = that.totalPriceAmount;
                        return actions.order.create({
                            purchase_units: [{
                                invoice_id: "<?= ORDER_ID ?>",
                                amount: {
                                    currency_code: "<?= $currency->code ?>",
                                    value: totalPayment,
                                    breakdown: {
                                        item_total: {  /* Required when including the `items` array */
                                            currency_code: "<?= $currency->code ?>",
                                            value: totalPayment
                                        }
                                    }
                                },
                                items: [
                                    {
                                        name: "New Skills Academy Order <?= ORDER_ID ?>",
                                        unit_amount: {
                                            currency_code: "<?= $currency->code ?>",
                                            value: totalPayment
                                        },
                                        quantity: "1"
                                    },
                                ]
                            }]
                        });
                    },
                    onApprove: function(data, actions) {
                        that.paymentProcessing = 1;
                        // console.log(data);
                        // return false;

                        return actions.order.capture().then(function(orderData) {

                            $.ajax({
                                url: that.paypalCompleteCheckoutUrl,
                                type: 'POST',
                                dataType: 'JSON',
                                data: {
                                    order: orderData
                                },
                            }).done(function (response) {
                                if(response.error === false) {
                                    window.location.href = '<?= SITE_URL ?>checkout/confirmation';
                                }  else {
                                    window.location.href = '<?= SITE_URL ?>checkout?error=failed';
                                }
                            });

                            // Successful capture! For demo purposes:
                            //console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                            //var transaction = orderData.purchase_units[0].payments.captures[0];
                            //alert('Transaction '+ transaction.status + ': ' + transaction.id + '\n\nSee console for all available details');

                            // Replace the above to show a success message within this page, e.g.
                            // const element = document.getElementById('paypal-button-container');
                            // element.innerHTML = '';
                            // element.innerHTML = '<h3>Thank you for your payment!</h3>';
                            // Or go to another URL:  actions.redirect('thank_you.html');
                        });



                        //$.ajax({
                        //    url: that.paypalCompleteCheckoutUrl+'&orderID='+ data.orderID +'&payerID='+data.payerID+'&paymentID='+data.paymentID,
                        //    type: 'get',
                        //    dataType: 'JSON',
                        //}).done(function (response) {
                        //    if(response.error === false) {
                        //        window.location.href = '<?//= SITE_URL ?>//checkout/confirmation';
                        //    }  else {
                        //        window.location.href = '<?//= SITE_URL ?>//checkout?error=failed';
                        //    }
                        //});

                        //alert('You have successfully created subscription ' + data.subscriptionID); // Optional message given to subscriber
                    }
                }).render('#paypal-button-container'); // Renders the PayPal button
                //.render(this.$refs.paypal);
            },
        },
        beforeMount: function() {
            <?php
            if(@$_GET['reload']){
            ?>
            that = this;
            that.getCartItems();
            <?php
            }
            ?>
        },
        mounted: function (){
            that = this;
            that.getCartItems();
            this.setPaypalSubscription();
            this.setPaypalCheckout();

            <?php
                if($_GET['error'] == 'payment_failed' && @$_SESSION['failed_payment_message']){
                    $failedPaymentMessage = $_SESSION['failed_payment_message'] ?? 'Something went wrong, please try again!';
            ?>
                that.errors.push("<?= $failedPaymentMessage ?>");
                $('html,body').animate({
                    scrollTop: $(".checkout-details").offset().top
                }, 500);
            <?php
                unset($_SESSION['failed_payment_message']);
                }
            ?>

            if(this.paymentType == 'offlinePayment') {
                that.activePaymentMethod(this.paymentType);
            }else{
                var style = {
                    base: {
                        color: '#1a1a1a',
                        fontFamily: '"Catamaran", sans-serif',
                        fontSmoothing: 'antialiased',
                        padding: '10',
                        fontSize: '16px',
                        '::placeholder': {
                            color: '#1a1a1a'
                        },

                    },
                    invalid: {
                        color: '#fa755a',
                        iconColor: '#fa755a'
                    }
                };

                that.stripe = Stripe( this.stripeAPIToken );

                that.elements = this.stripe.elements();

                that.cardNumber = this.elements.create('cardNumber', {
                    style: style,
                    placeholder: 'Card number',
                });
                that.cardNumber.mount('#card-number-element');

                that.cardExpiry = this.elements.create('cardExpiry', {
                    style: style,
                    placeholder: 'Expiry Date - MM / YY',
                });
                that.cardExpiry.mount('#card-expiry-element');

                that.cardCvc = this.elements.create('cardCvc', {
                    style: style,
                    placeholder: 'Card CVC',
                });
                that.cardCvc.mount('#card-cvc-element');


                that.stripeSub = Stripe( this.stripeAPIToken );

                that.subElements = this.stripeSub.elements();

                that.cardSubNumber = this.subElements.create('cardNumber', {
                    style: style,
                    placeholder: 'Card number',
                });
                that.cardSubNumber.mount('#card-number-sub-element');

                that.cardSubExpiry = this.subElements.create('cardExpiry', {
                    style: style,
                    placeholder: 'Expiry Date - MM / YY',
                });
                that.cardSubExpiry.mount('#card-expiry-sub-element');

                that.cardSubCvc = this.subElements.create('cardCvc', {
                    style: style,
                    placeholder: 'Card CVC',
                });
                that.cardSubCvc.mount('#card-cvc-sub-element');

                // Premium Subscriptions Start

                that.stripePreSub = Stripe( this.stripeAPIToken );

                that.subPreElements = this.stripePreSub.elements();

                that.cardPreSubNumber = this.subPreElements.create('cardNumber', {
                    style: style,
                    placeholder: 'Card number',
                });
                that.cardPreSubNumber.mount('#card-number-pre-sub-element');

                that.cardPreSubExpiry = this.subPreElements.create('cardExpiry', {
                    style: style,
                    placeholder: 'Expiry Date - MM / YY',
                });
                that.cardPreSubExpiry.mount('#card-expiry-pre-sub-element');

                that.cardPreSubCvc = this.subPreElements.create('cardCvc', {
                    style: style,
                    placeholder: 'Card CVC',
                });
                that.cardPreSubCvc.mount('#card-cvc-pre-sub-element');

                // Premium Subscriptions End

                // Premium Subscriptions Pre Start

                that.stripePreNs = Stripe( this.stripeAPIToken );

                that.subPreNsElements = this.stripePreNs.elements();

                that.cardPreNsNumber = this.subPreNsElements.create('cardNumber', {
                    style: style,
                    placeholder: 'Card number',
                });
                that.cardPreNsNumber.mount('#card-number-pre-ns-element');

                that.cardPreNsExpiry = this.subPreNsElements.create('cardExpiry', {
                    style: style,
                    placeholder: 'Expiry Date - MM / YY',
                });
                that.cardPreNsExpiry.mount('#card-expiry-pre-ns-element');

                that.cardPreNsCvc = this.subPreNsElements.create('cardCvc', {
                    style: style,
                    placeholder: 'Card CVC',
                });
                that.cardPreNsCvc.mount('#card-cvc-pre-ns-element');

                // Premium Subscriptions End

                // Stripe Apple Pay
                that.stripePay = Stripe( this.stripeAPIToken );
                var paymentRequest = this.stripePay.paymentRequest({
                    country: 'GB',
                    currency: 'gbp',
                    total: {
                        label: 'New Skills Academy Checkout',
                        amount: this.totalPriceAmount * 100,
                    },
                    requestPayerName: true,
                    requestPayerEmail: true,
                });
                var payElements = this.stripePay.elements();
                var prButton = payElements.create('paymentRequestButton', {
                    paymentRequest: paymentRequest,
                });

                // Check the availability of the Payment Request API first.
                paymentRequest.canMakePayment().then(function(result) {
                    console.log(result);
                    if (result && (result.applePay === true)) {
                        //prButton.mount('#payment-request-button');
                        <?php
                        if($isAffiliate == true){
                        ?>
                        $(".nav-tabs .nav-item").css('width','calc(33% - 15px)');
                        <?php }?>
                    } else {
                        $(".apple-button").css('display', 'none');
                        $(".nav-tabs .nav-item").css('width','calc(33% - 15px)');
                        <?php
                        if($isAffiliate == true){
                        ?>
                        $(".nav-tabs .nav-item").css('width','calc(50% - 15px)');
                        <?php }?>
                        //document.getElementById('payment-request-button').style.display = 'none';
                    }
                });
            }


            //console.log("");
            //alert("ss");
        },
    })

    window.addEventListener('message', function (ev) {
        if (ev.data === 'succeeded') {
            window.location.href = '<?= SITE_URL ?>checkout/confirmation';
        } else if (ev.data === 'payment-failed') {
            window.location.href = '<?= SITE_URL ?>checkout?error=payment_failed';
        }
    }, false);

    function toggleGift(enable = true) {

        // if ($('#gift').is(':checked')) {
        //     $("#giftOptions").slideDown();
        // } else {
        //     $("#giftOptions").slideUp();
        // }
    }

    $(document).ready(function (){
        $('[data-toggle="datepicker"]').datepicker({
            format: 'yyyy-mm-dd'
        });

        var offer2Width = $(".offer2-box").outerWidth();
        var offer2Height = $(".offer2-box").outerHeight();
        $(".offer2-box .selectedUpsellOffer").css('width', offer2Width + 'px');
        $(".offer2-box .selectedUpsellOffer").css('height', offer2Height + 'px');

        <?php
        if(@$_GET['addSub'] && ($_GET['addSub'] == true)) {
        ?>
        $(".chooseSpecialOffer").css('display', 'none');
        <?php
        }
        ?>
    });

</script>
<div id="paymentModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">

            </div>

        </div>

    </div>
</div>
