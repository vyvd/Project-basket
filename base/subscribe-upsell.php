<?php
$account = ORM::for_table("accounts")->select(["subActive","isNCFE"])->find_one(CUR_ID_FRONT);

$courseCount = 700;

if($currency->code != "GBP") {
    $courseCount = 300;
}

if($account->subActive == "0") {
    if($currency->code == "GBP") {
        ?>
        <div class="subscribeUpsell">
            <a href="<?= SITE_URL ?>dashboard/subscribe">
                <i class="fad fa-medal"></i>
                Get access to all <?= $courseCount ?>+ courses (and MORE) for only <?= $this->price($currency->prem1) ?> per month. Find out more.
            </a>
        </div>
        <?php
    } else {
        ?>
        <div class="subscribeUpsell">
            <a href="<?= SITE_URL ?>dashboard/subscribe">
                <i class="fad fa-medal"></i>
                Get access to all <?= $courseCount ?>+ courses (and MORE) for only <?= $this->price($currency->prem12) ?> per year. Find out more.
            </a>
        </div>
        <?php
    }
}
?>