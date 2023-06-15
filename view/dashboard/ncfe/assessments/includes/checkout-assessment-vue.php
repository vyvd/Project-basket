<!--<script src="--><?//= SITE_URL ?><!--assets/vendor/vue3/dist/vue.global.prod.js"></script>-->
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script src="<?= SITE_URL ?>assets/vendor/axios/dist/axios.min.js"></script>
<script>
    var app = new Vue({
        el: '#mainUser',
        data: {
            errors: [],
            paymentProcessing: 0,
            orderSummaryProcessing: 0,
            cartItems: [],
            accountID: '<?= CUR_ID_FRONT?>',
            totalPrice: null,
            totalPriceAmount: null,
            updateCartItemUrl: '',
            paymentType: 'stripe',
            orderCompleteUrl : '<?= SITE_URL ?>dashboard/ncfe/assessments?message=payment_success',
            stripeAPIToken: '<?= STRIPE_PUBLISHABLE_KEY ?>',
            stripe: '',
            elements: '',
            cardNumber: '',
            cardExpiry: '',
            cardCvc: '',
            cardErrors: '',
            customerID:'',
            stripePaymentUrl: '<?= SITE_URL ?>ajax?c=stripe&a=processPayment&cart=assessment',
            stripePaymentSuccessUrl: '<?= SITE_URL ?>ajax?c=stripe&a=successPayment&cart=assessment',


            stripeCreateSetupIntentUrl: '<?= SITE_URL ?>ajax?c=stripe&a=createSetupIntent',

            stripeClientSecret: null,
            stripePaymentMethod: null,

            paypalPaymentUrl: '<?= SITE_URL ?>ajax?c=paypal&a=processPayment&cart=assessment',

            // Stripe Apple Pay
            stripePay: '',
            payElements: '',
            prButton: '',
            stripeApplePayUrl: '<?= SITE_URL ?>ajax?c=stripe&a=processApplePay&cart=assessment',
            stripeApplePayConfirmUrl: '<?= SITE_URL ?>ajax?c=stripe&a=confirmApplePay&cart=assessment',

            stripeOfflinePaymentUrl : '<?= SITE_URL ?>ajax?c=cart&a=offlinePayment',

        },
        methods: {
            getCartItems: function (e){
                that = this;
                //this.orderSummaryProcessing = 1;
                const url = "<?= SITE_URL ?>ajax?c=cart&a=assessmentCartItems&action=json";
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(response){
                        response = JSON.parse(response);

                        that.totalPrice = response.totalPrice;
                        that.totalPriceAmount = response.totalPriceAmount;
                        that.cartItems = response.items;

                        that.orderSummaryProcessing = 0;

                        console.log('data', response);

                    },
                    error: function(xhr, status, error){
                        console.error(xhr);
                    }
                });
            },
            removeFromCart: function (id, event) {
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
                }else{
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

                if (!this.terms) {
                    that.errors.push('You must accept the terms & conditions before continuing.');
                }

                if (that.errors.length === 0) {
                    if(button === 'applepay'){
                        that.activePaymentMethod('applepay');
                    }else{
                        that.submitPaymentForm();
                    }
                }else{
                    $('html,body').animate({
                        scrollTop: $(".checkout-details").offset().top
                    }, 500);
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
                }else if (this.paymentType == 'ncfeSubscription') {
                    that.submitNcfeSubPayment();
                }
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
                                    window.location.href = '<?= SITE_URL ?>dashboard/ncfe/assessments?error=payment_failed';
                                } else if(result.paymentIntent.status === "succeeded") {
                                    //that.paymentProcessing = 1;
                                    $.ajax({
                                        url: stripeConfirmUrl+'&paymentIntent='+ result.paymentIntent.id,
                                        type: 'post',
                                        data: '',
                                        dataType: 'JSON',
                                    }).done(function (response1) {
                                        window.location.href = '<?= SITE_URL ?>dashboard/ncfe/assessments?message=payment_success';
                                    });
                                }else{
                                    window.location.href = '<?= SITE_URL ?>dashboard/ncfe/assessments?error=payment_failed';
                                }
                            });
                        }else{
                            window.location.href = '<?= SITE_URL ?>dashboard/ncfe/assessments?error=payment_failed';
                        }
                    }
                    else if(response.success === true){
                        $.ajax({
                            url: stripeGiftConfirmUrl+'&paymentIntent='+ response.payment_intent,
                            type: 'post',
                            data: '',
                            dataType: 'JSON',
                        }).done(function (response1) {
                            window.location.href = '<?= SITE_URL ?>dashboard/ncfe/assessments?message=payment_success';
                        });
                    }else{
                        window.location.href = '<?= SITE_URL ?>dashboard/ncfe/assessments?error=payment_failed';
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
                                    window.location.href = '<?= SITE_URL ?>dashboard/ncfe/assessments?error=payment_failed';
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
                                                window.location.href = '<?= SITE_URL ?>dashboard/ncfe/assessments?error=payment_failed';
                                            } else if(result.paymentIntent.status === "succeeded") {
                                                //that.paymentProcessing = 1;
                                                $.ajax({
                                                    url: stripeApplePayConfirmUrl+'&paymentIntent='+ result.paymentIntent.id,
                                                    type: 'post',
                                                    data: '',
                                                    dataType: 'JSON',
                                                }).done(function (response1) {
                                                    window.location.href = '<?= SITE_URL ?>dashboard/ncfe/assessments?message=payment_success';
                                                });
                                            }else{
                                                window.location.href = '<?= SITE_URL ?>dashboard/ncfe/assessments?error=payment_failed';
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
                                            window.location.href = '<?= SITE_URL ?>dashboard/ncfe/assessments?message=payment_success';
                                        });
                                    }
                                }
                            });
                        }else{
                            window.location.href = '<?= SITE_URL ?>dashboard/ncfe/assessments?error=payment_failed';
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
                    }
                    that.errors.push(response.data);
                    $('html,body').animate({
                        scrollTop: $(".checkout-details").offset().top
                    }, 500);
                    that.paymentProcessing = 0;
                    console.log(response);
                });
                return false;
            },
            updateItem: function (event, moduleID, action = null) {
                that = this;
                that.paymentProcessing = 1;
                if (event.target.checked || action === 'add') {
                    //if (event) {
                    that.updateCartItemUrl = '<?= SITE_URL?>ajax?c=cart&a=addAssessmentItem&action=json&id='+moduleID;
                }else{
                    that.updateCartItemUrl = '<?= SITE_URL?>ajax?c=cart&a=removeAssessmentItem&action=json&id='+moduleID;
                }
                $.ajax({
                    url: this.updateCartItemUrl,
                    type: 'get',
                    dataType: 'JSON',
                }).done(function (response) {
                    that.getCartItems();
                    //that.getCartTotals();
                    that.paymentProcessing = 0;
                });
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

</script>
