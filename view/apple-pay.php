<html>
<head>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe('pk_test_UyaB3ILxqsIJ0eZoJSyC37ij', {
            //apiVersion: "2020-08-27",
        });
        var paymentRequest = stripe.paymentRequest({
            country: 'GB',
            currency: 'gbp',
            total: {
                label: 'Demo total',
                amount: 1099,
            },
            requestPayerName: true,
            requestPayerEmail: true,
        });
        var elements = stripe.elements();
        var prButton = elements.create('paymentRequestButton', {
            paymentRequest: paymentRequest,
        });
        console.log(paymentRequest);

        // Check the availability of the Payment Request API first.
        paymentRequest.canMakePayment().then(function(result) {
            if (result) {
                prButton.mount('#payment-request-button');
            } else {
                document.getElementById('payment-request-button').style.display = 'none';
            }
        });
    </script>
</head>
<body>
    <div id="payment-request-button">
        <!-- A Stripe Element will be inserted here. -->
    </div>
</body>
</html>