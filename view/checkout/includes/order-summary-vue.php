<p><strong>Order Summary</strong></p>
<h2 v-if="orderSummaryProcessing" class="text-center"><i
            class="fas fa-spin fa-spinner"></i></h2>
<div v-else-if="cartItems.length" v-for="(item, index) in cartItems"
     :data-course-id="item.courseID" :data-oldproductid="item.productID" :data-course-url="item.productUrl"
     :data-course_type="item.courseType" :data-course-cats="item.categories" :data-subscription_id="item.premiumSubPlanID">

    <div class="cart-items d-flex align-items-center">
        <span class="remove-items"
              @click="removeFromCart(item.id, $event);"><i class='fas fa-times-circle'></i></span>
        <div v-if="item.imageUrl" class="product-img"><img :src="item.imageUrl"
                                                           :alt="item.title"></div>
        <div class="product-title align-self-md-center">
            <div v-if="premiumMonths >= 1">
                <p>
                <h6>Unlimited Learning Membership</h6>
                <strong>{{item.price}} for {{premiumMonths}} month<span
                            v-if="premiumMonths == 12">s</span> unlimited course
                    access</strong>
                </p>
            </div>
            <div v-else>
                <p>
                    <strong v-if="item.certNo">Cert: {{item.certNo}}</strong>
                    <span class="nsa_course_title">{{item.title}}</span>
                    <strong v-if="item.printedCourse == 1">Printed
                        Materials</strong>
                </p>
                <p><strong class="course_price">{{item.price}}</strong></p>

            </div>
        </div>
    </div>

    <!-- <div v-if="item.sellPrinted == 1 && item.onlyPrinted == false" class="printedCartItem">
        <h5 v-if="printedItems.includes(item.courseID)"><span @click="updatePrintedItem(false,item.courseID)"><i class="fa fa-times" aria-hidden="true"></i></span> Printed course materials added {{item.sellPrintedPrice}} </h5>
        <h5 v-else> <span @click="updatePrintedItem(true,item.courseID)"><i class="fa fa-plus" aria-hidden="true"></i></span> Add printed course materials only {{item.sellPrintedPrice}} inc. delivery </h5>
    </div>


        <h5 v-if="printedItems.includes(item.courseID)"><span @click="updatePrintedItem(false,item.courseID)"></span>  Due to shortages over the Christmas period, please wait up to 2-3 weeks for your printed materials to be delivered </h5> -->

</div>
<div v-else class="text-center col-12">
    <h5>Your cart is empty!</h5>
</div>
<div v-if="premiumMonths >= 1">
    <ul class="premium_list">
        <li>Unlimited course access <span v-if="premiumMonths == 12">for 12 months</span>
        </li>
        <li v-if="premiumMonths == 1"><strong>Cancel anytime</strong></li>
        <?php
        if($currency->code == "GBP") {
        ?>
        <li>XO Student Discount membership</li>
        <?php } ?>
        <li>Career personality matching</li>
        <li>CV builder</li>
        <li>Jobs board</li>
        <li>Tutor support</li>
    </ul>
    <p class="text-center" style="line-height:14px;">
        <small style="font-size:10px;">
            Excludes language courses. No more than 50 active courses at any one
            time. Membership renews after 12 months. Cancel anytime from your account.
        </small>
    </p>
</div>

<div class="totals text-right">
    <span class="coupon-amount"><?php $this->controller->getCouponDetails() ?></span>
</div>
<div id="coupon-box" class="coupon col-md-12 p-0">
    <p style="display: none;" class="haveCoupon" @click="haveCoupon">Have a coupon code? Enter it here</p>

    <div class="input-group mb-3 couponCode" >
        <input type="text" class="form-control" placeholder="Have a coupon code? Enter it here"
               aria-label="Certificate ID" aria-describedby="basic-addon2"
               name="code" id="couponCode">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button"
                    onclick="applyCoupon();">Apply
            </button>
        </div>
    </div>

</div>

<?php
if(CUR_ID_FRONT != "" && ORDER_ID != "") {

    $showABDiscount = true;

    $order = ORM::for_table("orders")->find_one(ORDER_ID);

    if($order->couponID != "") {

        $coupon = ORM::for_table("coupons")->where("balance", "1")->where("id", $order->couponID)->count();

        if($coupon != 0) {
            $showABDiscount = false;
        }

    }


    $account = ORM::for_table("accounts")->select("balance")->find_one(CUR_ID_FRONT);

    if($account->balance != "0.00" && $showABDiscount == true) {

        ?>
        <a href="<?= SITE_URL ?>ajax?c=cart&a=apply-balance-discount" class="useAccountBalance">
            <i class="fas fa-check"></i>
            Use <?= $this->price($account->balance) ?> of balance
        </a>
        <?php

    }


}
?>

<div class="totals text-right">
    <?php
    if($trial == "0") {
        ?>
        <h3>Total to pay  <span class="checkout_total_amount">{{totalNewPrice}}</span> <span style="font-size:18px;"><?= $currency->code ?></span></h3>
        <?php
    } else {
        ?>
        <h3>Total today  <span class="checkout_total_amount"><?= $currency->short ?>0.00</span> <span style="font-size:18px;"><?= $currency->code ?></span></h3>
        <p>
            <small style="font-size:10px;">You will be charged <span>{{totalNewPrice}}</span> in <?= $trial ?> days if you do not cancel beforehand.</small>
        </p>
        <?php
    }
    ?>
</div>

<div class="d-none d-md-block">
    <div class="col-md-12 text-center">
        <p class="text-center"><strong>We Accept</strong></p>
        <img src="<?= SITE_URL ?>assets/images/accept-cards.png" alt="cards" />
        <img src="<?= SITE_URL ?>assets/images/ssl-checkout.png" class="sslImg" alt="ssl secure" />
    </div>
</div>