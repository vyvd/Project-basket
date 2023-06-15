<?php
$css = array("dashboard.css");
$pageTitle = "Special Offers";
include BASE_PATH . 'account.header.php';
?>
    <section class="page-title">
        <div class="container">
            <h1><?= $pageTitle ?></h1>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="row">

                <?php
                $offers = $this->controller->mySpecialOffers();

                if(count($offers) == 0) {
                    ?>
                    <div class="col-12 regular-full white-rounded">
                        <div class="rewards-inner">
                            <h3 class="text-center">We currently have no special offers available to you.</h3>
                        </div>
                    </div>
                    <?php
                }

                $count = 1;
                foreach($offers as $offer) {

                    ?>
                    <div class="col-12 regular-full white-rounded">
                        <div class="rewards-inner">
                            <a class="btn btn-primary">Offer <?= $count ?></a>
                            <h3 class="text-center" style="margin-left:-67px;"><?= $offer->title ?></h3>
                        </div>
                        <div class="text-center offer-text">
                            <?= $offer->accountDescription ?>
                            <h3 class="text-primary">
                                <a href="<?= SITE_URL ?>special-offer/<?= $offer->slug ?>">
                                    <span class="underlined">Browse Courses</span>
                                </a>
                            </h3>
                            <a class="btn btn-outline-primary offer-btn" href="<?= SITE_URL ?>special-offer/<?= $offer->slug ?>">Claim Offer</a>
                        </div>
                    </div>
                    <?php

                    $count ++;

                }
                ?>

            </div>
        </div>
    </section>


<?php include BASE_PATH . 'account.footer.php';?>