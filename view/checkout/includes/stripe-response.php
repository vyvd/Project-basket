<?php
header('X-Frame-Options: SAMEORIGIN');
$this->setControllers(array("stripe"));
$response = $this->stripe->stripeResponse($_GET);
?>
<!doctype html>
<html lang="">
<head>
    <script>

        <?php
        if(@$response['data']->status && $response['data']->status == "succeeded"){
        ?>
            window.top.postMessage('<?= $response['data']->status ?>');
        <?php
        }else{
        ?>
            window.top.postMessage('payment-failed');
        <?php
        }
        ?>

    </script>
    <title>Stripe Response</title>
</head>
<body>
<h2>Redirecting....</h2>
</body>
</html>
