<script src="https://js.stripe.com/v3/"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script src="<?= SITE_URL ?>assets/vendor/axios/dist/axios.min.js"></script>
<!-- addGiftAmount -->
<script>
    var app = new Vue({
        el: '#checkoutMain',
        data: {
            giftType: 'subscription',
            errors: [],
            courseID: null,
            courseTitle: null,
            name: null,
            nameRecipient: null,
            message: null,
            type: 'print',
            recipEmail: null,
            giftDate: null,
            firstname: '<?= $currentAccount->firstname ?>',
            lastname: '<?= $currentAccount->lastname ?>',
            email: '<?= $currentAccount->email ?>',
            terms: false,
            totalPrice: '£0.00',
            totalPriceAmount: '0.00',
            TotalDisplayPrice: '£0.00',
            totalSubDisplayPrice: '',
            paymentProcessing: 0,
            accountID: '<?= CUR_ID_FRONT ?>',

            paymentType: 'stripe',

            orderCompleteUrl: '<?= SITE_URL ?>gift/complete',

            stripeAPIToken: '<?= STRIPE_PUBLISHABLE_KEY ?>',
            stripeErrorUrl: '<?= SITE_URL ?>ajax?c=stripe&a=cardError',
            stripe: '',
            elements: '',
            cardNumber: '',
            cardExpiry: '',
            cardCvc: '',
            cardErrors: '',
            customerID: '',
            stripePaymentUrl: '<?= SITE_URL ?>ajax?c=stripe&a=processGiftPayment',
            stripePaymentSuccessUrl: '<?= SITE_URL ?>ajax?c=stripe&a=successGiftPayment',

            stripeCreateSetupIntentUrl: '<?= SITE_URL ?>ajax?c=stripe&a=createSetupIntent',

            stripeClientSecret: null,
            stripePaymentMethod: null,

            paypalPaymentUrl: '<?= SITE_URL ?>ajax?c=paypal&a=processGiftPayment',

            // Stripe Apple Pay
            stripePay: '',
            payElements: '',
            prButton: '',
            stripeApplePayUrl: '<?= SITE_URL ?>ajax?c=stripe&a=processGiftApplePay',
            stripeApplePayConfirmUrl: '<?= SITE_URL ?>ajax?c=stripe&a=confirmGiftApplePay',
            customAmount: 0
        },
        
        methods: {
            courseSelection: function(e) {
                that = this;
                that.courseID = $("#courseSelection").val();
                that.giftType = 'course';
                that.paymentProcessing = 1;
                $('.giftAmounts li').removeClass('active');
                const url = "<?= SITE_URL ?>ajax?c=gift&a=course-selection-price&action=json&id=" + that.courseID;
                const url2 = document.URL;
                if (url2 == "<?= SITE_URL ?>gift?promo=new") {
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(response) {
                            response = JSON.parse(response);
                            that.totalPrice = "£25";
                            that.totalPriceAmount = 25;
                            that.TotalDisplayPrice = "£25"
                            that.courseTitle = 'ANY individual course up to the value of £100';
                            that.paymentProcessing = 0;
                        }


                    });
                } else if(url2 == "<?= SITE_URL ?>gift?promo=ulm"){

                            $.ajax({
                                type: "GET",
                                url: url,
                                success: function(response) {
                                    response = JSON.parse(response);
                                    that.totalSubDisplayPrice = "£75";
                                    that.totalPrice = "£100";
                                    that.totalPriceAmount = 100;
                                    that.TotalDisplayPrice = "£100"
                                    that.courseTitle = 'ANY individual course up to the value of £100';
                                    that.paymentProcessing = 0;

                                }


                            });


}else {
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(response) {
                            response = JSON.parse(response);
                            that.totalPrice = "£100";
                            that.totalPriceAmount = 100;
                            that.TotalDisplayPrice = "£100"
                            that.courseTitle = 'ANY individual course up to the value of £100';
                            that.paymentProcessing = 0;

                        }
                    });
                }
            },
            addGiftAmount: function(amount) {
                that = this;
                $('.giftAmounts li').removeClass('active');
                $("option:selected").removeAttr("selected");
                $('.giftAmounts li[rel="' + amount + '"]').addClass('active');
                that.giftType = 'amount';
                if (amount === 'custom') {
                    that.updateGiftAmount();
                } else {
                    that.totalPrice = '£' + amount.toFixed(2);
                    that.totalPriceAmount = amount;
                    that.courseTitle = 'Gift Amount: ' + that.totalPrice;
                }

                return false;
            },
            selectSubscription: function() {
                that = this;
                that.paymentProcessing = 1;
                that.giftType = 'subscription';
                $('.giftAmounts li').removeClass('active');
                $("option:selected").removeAttr("selected");
                const url = "<?= SITE_URL ?>ajax?c=gift&a=selectSubscription&action=json";
                const url2 = document.URL;
                if (url2 == "<?= SITE_URL ?>gift?promo=new") {
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(response) {
                            response = JSON.parse(response);
                            that.TotalDisplayPrice = "£25"

                        }


                    });
                } else {
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(response) {
                            response = JSON.parse(response);
                            that.TotalDisplayPrice = "£100"

                        }
                    });
                }
            
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(response) {
                        response = JSON.parse(response);
                        that.totalPrice = response.totalPrice;
                        that.totalPriceAmount = response.totalPriceAmount;
                        that.totalSubDisplayPrice = response.totalPrice;
                        that.courseTitle = 'A Year’s Unlimited Learning Membership including access to 700+ courses';
                        that.paymentProcessing = 0;
                    }
                });
                if(url2 == "<?= SITE_URL ?>gift?promo=ulm"){

                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(response) {
                            response = JSON.parse(response);
                            that.TotalDisplayPrice = "£20"
                            that.totalSubDisplayPrice = "£75";
                            that.totalPrice = "£75";
                            that.totalPriceAmount = 75;

                        }


                    });


                    }
                

            },
            updateGiftAmount: function() {
                that.totalPriceAmount = 0.00;
                if ($('#custom_amount').val()) {
                    that.totalPriceAmount = parseInt($('#custom_amount').val()).toFixed(2);
                }
                that.totalPrice = '£' + that.totalPriceAmount;
                that.courseTitle = 'Gift Amount: ' + that.totalPrice;
            },
            itemSelection: function(style) {
                that = this;
                $(".styles .item").removeClass("selected");
                $('*[data-style="' + style + '"]').addClass("selected");

                $('.giftPreview').css('background-image', 'url(<?= SITE_URL ?>assets/images/vouchers/' + style + '.png)');
                $('#voucherStyle').val(style)
            },
            validateForm: function(e) {
                var button = '';
                if (e === 'applepay') {
                    button = 'applepay';
                } else {
                    e.preventDefault();
                }
                //alert(button);

                var regularExpression = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{6,16}$/;

                this.errors = [];
                this.cardErrors = false;
                $("#card-errors").html();

                if (!this.giftType) {
                    this.errors.push('Please select gift type.');
                }
                if (!this.name) {
                    this.errors.push('Your Name required.');
                }
                if (!this.nameRecipient) {
                    this.errors.push("Recipient's Name required.");
                }
                if (!this.message) {
                    this.errors.push('Message required.');
                }
                if (!this.firstname) {
                    this.errors.push('Firstname required.');
                }
                if (!this.lastname) {
                    this.errors.push('Lastname required.');
                }
                if (!this.email) {
                    this.errors.push('Email required.');
                }


                if (!this.terms) {
                    this.errors.push('You must accept the terms & conditions before continuing.');
                }

                if (this.errors.length === 0) {
                    if (button === 'applepay') {
                        this.activePaymentMethod('applepay');
                    } else {
                        this.submitPaymentForm();
                    }
                } else {

                    $('html,body').animate({
                        scrollTop: $(".checkout-details").offset().top
                    }, 500);


                }
                return false;

            },
            activePaymentMethod: function(paymentType) {
                that = this;
                if (paymentType != 'offlinePayment') {
                    $(".nav-tabs a[href='#" + paymentType + "tab']").tab('show');
                }
                that.paymentType = paymentType;
                if (paymentType == 'nspay') {
                    that.totalNewPrice = this.unitPrice;
                } else {
                    that.totalNewPrice = this.totalPrice;
                }

            },
            submitPaymentForm: function() {
                that = this;
                $("#gift_type").val(this.giftType);
                $("#gift_amount").val(this.totalPriceAmount);
                if (this.paymentType == 'stripe') {
                    this.submitStripePayment();
                } else if (this.paymentType == 'paypal') {
                    this.submitPaypalPayment();
                } else if (this.paymentType == 'nspay') {
                    this.submitNSPayPayment();
                } else if (this.paymentType == 'applepay') {
                    this.submitApplePayPayment();
                } else if (this.paymentType == 'offlinePayment') {
                    this.submitOfflinePayment();
                }
            },
            submitStripePayment: function() {
                this.paymentProcessing = 1;
                that = this;
                this.stripe.createPaymentMethod(
                    'card',
                    this.cardNumber
                ).then(function(result) {
                    if (result.error) {
                        that.cardErrors = result.error.message;
                        console.log(result.error.message);

                        $('html, body').animate({
                            scrollTop: $(".payment-method").offset().top - 200
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
            stripeTokenHandler: function(paymentMethod) {
                that = this;
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', paymentMethod.id);
                form.appendChild(hiddenInput);

                var stripe = Stripe(this.stripeAPIToken);
                var stripeGiftConfirmUrl = this.stripePaymentSuccessUrl;

                $.ajax({
                    url: this.stripePaymentUrl,
                    type: 'post',
                    data: $("#payment-form").serialize(),
                    dataType: 'JSON',
                }).done(function(response) {
                    if (response.error) {
                        if (response.requires_action === true) {
                            // Let Stripe.js handle the rest of the payment flow.
                            stripe.confirmCardPayment(response.payment_intent_client_secret).then(function(result) {
                                if (result.error) {
                                    $.ajax({
                                        url: that.stripeErrorUrl,
                                        type: 'post',
                                        data: {
                                            error: result.error
                                        },
                                        dataType: 'JSON',
                                    }).done(function(response) {
                                        window.location.href = '<?= SITE_URL ?>gift?error=payment_failed';
                                    });
                                } else if (result.paymentIntent.status === "succeeded") {
                                    //that.paymentProcessing = 1;
                                    $.ajax({
                                        url: stripeGiftConfirmUrl + '&paymentIntent=' + result.paymentIntent.id,
                                        type: 'post',
                                        data: '',
                                        dataType: 'JSON',
                                    }).done(function(response1) {
                                        window.location.href = '<?= SITE_URL ?>gift/complete';
                                    });
                                } else {
                                    window.location.href = '<?= SITE_URL ?>gift?error=payment_failed';
                                }
                            });
                        } else {
                            window.location.href = '<?= SITE_URL ?>gift?error=payment_failed';
                        }
                    } else if (response.success === true) {
                        $.ajax({
                            url: stripeGiftConfirmUrl + '&paymentIntent=' + response.payment_intent,
                            type: 'post',
                            data: '',
                            dataType: 'JSON',
                        }).done(function(response1) {
                            window.location.href = '<?= SITE_URL ?>gift/complete';
                        });
                    } else {
                        window.location.href = '<?= SITE_URL ?>gift?error=payment_failed';
                    }
                    console.log(response);
                    return false;
                });

                return false;
            },
            submitApplePayPayment: function() {
                this.paymentProcessing = 1;
                var stripeApplePayUrl = this.stripeApplePayUrl;
                var stripeApplePayConfirmUrl = this.stripeApplePayConfirmUrl;
                var stripePay = Stripe(this.stripeAPIToken);
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
                    that = this;
                    if (result && (result.applePay === true)) {
                        //prButton.mount('#payment-request-button');
                        paymentRequest.show();
                    } else {
                        $(".apple-button").css('display', 'none');
                        $(".nav-tabs .nav-item").css('width', 'calc(33% - 15px)');
                        document.getElementById('payment-request-button').style.display = 'none';
                    }
                });

                paymentRequest.on('paymentmethod', function(ev) {
                    that = this;
                    $.ajax({
                        url: stripeApplePayUrl,
                        type: 'post',
                        data: $("#payment-form").serialize(),
                        dataType: 'JSON',
                    }).done(function(response) {
                        //return false;
                        if (response.clientSecret) {
                            //that.paymentProcessing = 0;
                            // Create payment method and confirm payment intent.
                            stripePay.confirmCardPayment(
                                response.clientSecret, {
                                    payment_method: ev.paymentMethod.id
                                }, {
                                    handleActions: false
                                }
                            ).then(function(confirmResult) {

                                if (confirmResult.error) {
                                    // Report to the browser that the payment failed, prompting it to
                                    // re-show the payment interface, or show an error message and close
                                    // the payment interface.
                                    ev.complete('fail');
                                    window.location.href = '<?= SITE_URL ?>gift?error=payment_failed';
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
                                            if (result.error) {
                                                window.location.href = '<?= SITE_URL ?>gift?error=payment_failed';
                                            } else if (result.paymentIntent.status === "succeeded") {
                                                //that.paymentProcessing = 1;
                                                $.ajax({
                                                    url: stripeApplePayConfirmUrl + '&paymentIntent=' + result.paymentIntent.id,
                                                    type: 'post',
                                                    data: '',
                                                    dataType: 'JSON',
                                                }).done(function(response1) {
                                                    window.location.href = '<?= SITE_URL ?>gift/complete';
                                                });
                                            } else {
                                                window.location.href = '<?= SITE_URL ?>gift?error=payment_failed';
                                            }
                                        });
                                    } else {
                                        // The payment has succeeded.
                                        $.ajax({
                                            url: stripeApplePayConfirmUrl + '&paymentIntent=' + confirmResult.paymentIntent.id,
                                            type: 'post',
                                            data: '',
                                            dataType: 'JSON',
                                        }).done(function(response1) {
                                            window.location.href = '<?= SITE_URL ?>gift/complete';
                                        });
                                    }
                                }
                            });
                        } else {
                            window.location.href = '<?= SITE_URL ?>gift?error=payment_failed';
                        }
                        //that.paymentProcessing = 0;
                        console.log(response);
                        return false;
                    });
                });
                paymentRequest.on('cancel', function(ev) {
                    window.location.href = '<?= SITE_URL ?>gift';
                });

                return false;
            },
            configurePaypal: function() {

            },
            submitPaypalPayment: function() {
                this.paymentProcessing = 1;
                that = this;

                $.ajax({
                    url: this.paypalPaymentUrl,
                    type: 'post',
                    data: $("#payment-form").serialize(),
                    dataType: 'JSON',
                }).done(function(response) {
                    if (response.success === true) {
                        window.location.href = response.redirect_url;
                    }

                    this.errors.push(response.data);
                    $('html,body').animate({
                        scrollTop: $(".checkout-details").offset().top
                    }, 500);
                    that.paymentProcessing = 0;
                    console.log(response);
                });
                return false;
            },
            submitOfflinePayment: function() {
                this.paymentProcessing = 1;
                that = this;

                $.ajax({
                    url: this.stripeOfflinePaymentUrl,
                    type: 'post',
                    data: $("#payment-form").serialize(),
                    dataType: 'JSON',
                }).done(function(response) {
                    if (response.success === true) {
                        window.location.href = '<?= SITE_URL ?>gift/complete';
                    } else {
                        window.location.href = '<?= SITE_URL ?>gift?error=failed';
                    }
                    console.log(response);

                });

                return false;
            },
            updateUpsellOffer: function(event) {
                that = this;
                this.paymentProcessing = 1;
                if (event.target.checked) {
                    this.updateUpsellOfferUrl = '<?= SITE_URL ?>ajax?c=cart&a=addUpsellToCartJson';
                } else {
                    this.updateUpsellOfferUrl = '<?= SITE_URL ?>ajax?c=cart&a=removeUpsellToCartJson';
                }
                $.ajax({
                    url: this.updateUpsellOfferUrl,
                    type: 'post',
                    dataType: 'JSON',
                }).done(function(response) {

                    //that.getCartTotals();
                    that.paymentProcessing = 0;
                });
            },
            getPostCodeAddresses: function() {
                $(".postCodeMessage").css('display', 'none');
                $(".postCodeMessage").html('');
                var postCode = $("#postcode").val();
                $(".postCodeMessage").css('display', 'block');
                $('.postCodeAddress').css('display', 'none');

                $('#myAddresses').html('<option value="">Please select your address</option>');
                if (postCode == "") {
                    $("#postcode").focus()
                    $(".postCodeMessage").html('<span class="email help-block alert-danger" style="display: block; padding: 5px;">Please Enter Your PostCode</span>');
                } else {
                    $(".postCodeMessage").html('<span class="help-block alert-info" style="display: block; padding: 5px;">Looking up Postcode... </span>');
                    try {
                        axios.get('https://api.ideal-postcodes.co.uk/v1/postcodes/' + postCode, {
                            params: {
                                api_key: 'ak_k1yndbuqM07GSCeo6sT8Zxhz3LZTD'
                            }
                        }).then(response => {
                            console.log(response.data.result);
                            $.each(response.data.result, function(key, value) {
                                $('#myAddresses')
                                    .append($("<option></option>")
                                        .attr("value", value.line_1 + "|||" + value.line_2 + "|||" + value.line_3 + "|||" + value.post_town + "|||" + value.county + "|||" + value.country)
                                        .text(value.line_1 + " " + value.line_2 + " " + value.line_3));
                            });
                            $('.postCodeAddress').css('display', 'block');
                            $('.postCodeMessage').css('display', 'none');
                        }).catch((error) => {
                            $(".postCodeMessage").html('<span class="email help-block alert-warning" style="display: block; padding: 5px;">Your postcode could not be found. Please type in your address</span>');
                        });
                    } catch (error) {
                        console.log(error);
                    }
                }
            },
            selectAddress: function() {
                that = this;
                var address = $("#myAddresses").val();
                if (address != "") {
                    var addr = address.split("|||");
                    that.address1 = addr[0];
                    that.address2 = addr[1];
                    that.address3 = addr[2];
                    that.city = addr[3];
                    that.county = addr[4];
                    that.country = addr[5];
                }
            },

        },
        beforeMount() {

        },
        mounted() {
            if (this.giftType === 'subscription') {
                this.selectSubscription();
            }
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

            this.stripe = Stripe(this.stripeAPIToken);

            this.elements = this.stripe.elements();

            this.cardNumber = this.elements.create('cardNumber', {
                style: style,
                placeholder: 'Card number',
            });
            this.cardNumber.mount('#card-number-element');

            this.cardExpiry = this.elements.create('cardExpiry', {
                style: style,
                placeholder: 'Expiry Date - MM / YY',
            });
            this.cardExpiry.mount('#card-expiry-element');

            this.cardCvc = this.elements.create('cardCvc', {
                style: style,
                placeholder: 'Card CVC',
            });
            this.cardCvc.mount('#card-cvc-element');


            // Stripe Apple Pay
            this.stripePay = Stripe(this.stripeAPIToken);
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
                    if ($isAffiliate == true) {
                    ?>
                        $(".nav-tabs .nav-item").css('width', 'calc(33% - 15px)');
                    <?php } ?>
                } else {
                    $(".apple-button").css('display', 'none');
                    $(".nav-tabs .nav-item").css('width', 'calc(33% - 15px)');
                    <?php
                    if ($isAffiliate == true) {
                    ?>
                        $(".nav-tabs .nav-item").css('width', 'calc(50% - 15px)');
                    <?php } ?>
                    //document.getElementById('payment-request-button').style.display = 'none';
                }
            });

            //console.log("");
            //alert("ss");
        },
    });


    window.addEventListener('message', function(ev) {
        if (ev.data === 'succeeded') {
            window.location.href = '<?= SITE_URL ?>gift/complete';
        } else if (ev.data === 'payment-failed') {
            window.location.href = '<?= SITE_URL ?>gift?error=payment_failed';
        }
    }, false);

    $(document).ready(function() {

        $('input[name=type]').change(function() {
            var value = $('input[name=type]:checked').val();
            if (value == "deliver") {
                $(".recipEmail").css("display", "block");
            } else {
                $(".recipEmail").css("display", "none");
            }
        });

        $('[data-toggle="datepicker"]').datepicker({
            format: 'dd/mm/yyyy'
        });
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