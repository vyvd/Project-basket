<?php

$css = array("checkout.css", "datepicker.css", "gift.css");
$js = array("datepicker.min.js");

$pageTitle = "Thank You";

include BASE_PATH . 'header.php';

?>


    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--Checkout-->
        <section class="checkout">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-md-12">
                        <h1 class="section-title text-left">Thank You For Your Purchase</h1>
                    </div>
                </div>
            </div>
        </section>

        <section id="checkoutMain">
            <div class="container wider-container">

                <!--Checkout form-->
                <form name="checkout">
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-8 checkout-box">


                            <div class="white-box">
                                <h3>Your Voucher</h3>

                                <div class="giftPreview" style="background-image:url('<?= SITE_URL ?>assets/images/vouchers/<?= $_SESSION["giftStyle"] ?>.png');">
                                    <p class="to"><?= $_SESSION["giftTo"] ?></p>
                                    <p class="from"><?= $_SESSION["giftFrom"] ?></p>
                                    <p class="message"><?= $_SESSION["giftMessage"] ?></p>
                                    <p class="code"><?= $_SESSION["code"] ?></p>
                                </div>

                                <input type="hidden" name="voucherUrl" id="voucherUrl" value="" />

                                <br />
                                <div id="returnImage"></div>

                                <script src="<?= SITE_URL ?>assets/js/jspdf.debug.js"></script>

                                <div id="previewImage" style="opacity:0;"></div>

                                <script>
                                    var element = $(".giftPreview");
                                    var getCanvas;
                                    $('document').ready(function(){
                                        html2canvas(element, {
                                            onrendered: function (canvas) {
                                                $("#previewImage").append(canvas);
                                                getCanvas = canvas;

                                                var imageData = getCanvas.toDataURL("image/png");
                                                $("#voucherUrl").val(imageData);

                                                $.post("<?= SITE_URL ?>ajax?c=gift&a=save-cert-image",
                                                    {
                                                        data: imageData
                                                    },
                                                    function(data, status){
                                                        $("#returnImage").html(data);
                                                    });

                                            }
                                        });



                                    });
                                </script>


                            </div>





                        </div>

                        <div class="col-12 col-md-12 col-lg-4 checkout-sidebar">


                            <div class="white-box">
                                <div class="col-md-12">
                                    <p><strong>We Accept</strong></p>
                                    <img src="<?= SITE_URL ?>assets/images/accept-cards.png" alt="cards" />
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

<?php
$this->renderFormAjax("cart", "checkout", "checkout");
?>

<?php include BASE_PATH . 'footer.php';?>