<?php
    $subscription = ORM::for_table('subscriptions')
        ->where('isNCFE', '1')
        ->where('accountID', CUR_ID_FRONT)
        ->order_by_desc('whenAdded')
        ->find_one();
?>
<div v-if="loadingCancelSubscription" class="loading-card col-12 text-center">
    <i class="fa fa-spin fa-spinner"
       style="font-size:100px;margin-top:100px;color:#248CAB;"></i>
</div>
<div v-else class="row">

    <div class="col-12" id="recommendedCourses">
        <div class="row">
            <div class="col-12 regular-full">

                <div class="white-rounded notification" style="padding:20px;">

                    <div class="row">
                        <?php if (@$_GET['update_card'] && ($_GET['update_card'] == 'success')) { ?>
                            <div class="col-12">
                                <div class="alert alert-success">
                                    Your card has been updated successfully!
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-lg-6 col-12">
                            <h4>Your Membership</h4>

                            <div class="row subInfo">
                                <div class="col-6">
                                    <div class="price">
                                        <?= $this->price($subscription->perMonthAmount); ?>
                                        <span>/
                                            <?php
                                            if ($plan->months == 12) {
                                                echo "year";
                                            } elseif ($plan->months == 6) {
                                                echo "half year";
                                            } else {
                                                echo "month";
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <p>
                                        <?= $subscription->nextPaymentDate ? 'Renews on '.date('d/m/Y',
                                            strtotime($subscription->nextPaymentDate)) : null ?>
                                    </p>
                                </div>
                            </div>


                            <button @click="cancelSubscription()"
                                    style="width: 100%; max-width: 100%" type="button"
                                    class="btn btn-danger">Cancel Subscription
                            </button>

                            <br/>
                            <hr/>
                            <br/>


                        </div>
                        <div class="col-lg-6 col-12">
                            <h4>Payment Method</h4>

                            <p>Your card ending in
                                <strong>XXXX <?= $subscription->last4 ?></strong>
                                will be billed for this subscription. </p>

                            <button @click="updateSubscription()" style="width: 100%; max-width: 100%" type="button"
                                    class="btn btn-primary">Change Payment
                                Method
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script src="<?= SITE_URL ?>assets/vendor/axios/dist/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
        integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
        crossorigin="anonymous"></script>
<script>
    var app = new Vue({
        el: '#mySubscriptions',
        data: {
            loadingLeaderBoard: false,
            loadingCancelSubscription: false,
            monthLeaderBoard: '<?= date("Y-m-")?>',
            currentAccountID : '<?= $this->user->id ?>',
            leaderAccounts: [],
            winner: 0,
            loadingCard: false,
            saveStudentData: [],
            axiosCancelSource: null,
            loginToken: null,
            buyCard: false,
            stripeAPIToken: '<?= STRIPE_PUBLISHABLE_KEY ?>',
        },
        methods: {

            cancelSubscription: function () {
                that = this;
                Swal.fire({
                    icon: 'warning',
                    title: 'Are you sure?',
                    text: "Do you want to Cancel the Subscription!",
                    //showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#248cab',
                    //denyButtonText: `Don't save`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        //$("#"+table+id).remove();
                        this.cancelSubscriptionProcess();
                    }
                });
            },
            cancelSubscriptionProcess: function () {
                that = this;
                that.loadingCancelSubscription = true;
                const url = "<?= SITE_URL ?>ajax?c=stripe&a=cancelSubscription&sid=<?= $subscription->id;?>";

                $.ajax({
                    type: "GET",
                    url: url,
                    dataType: 'JSON',
                    success: function(response){
                        location.reload();
                    },
                    error: function(xhr, status, error){
                        console.error(xhr);
                    }
                });
            },
            updateSubscription: function () {
                that = this;
                that.loadingCancelSubscription = true;
                const url = "<?= SITE_URL ?>ajax?c=stripe&a=updateSubscription&ncfe=1&sid=<?= $subscription->id;?>";
                var stripe = Stripe( this.stripeAPIToken );
                $.ajax({
                    type: "GET",
                    url: url,
                    dataType: 'JSON',
                    success: function(response){
                        stripe.redirectToCheckout({
                            // Make the id field from the Checkout Session creation API response
                            // available to this file, so you can provide it as argument here
                            // instead of the {{CHECKOUT_SESSION_ID}} placeholder.
                            sessionId: response.session.id
                        }).then(function (result) {
                            // If `redirectToCheckout` fails due to a browser or network
                            // error, display the localized error message to your customer
                            // using `result.error.message`.
                        });
                        return false;
                        //location.reload();
                    },
                    error: function(xhr, status, error){
                        console.error(xhr);
                    }
                });
                return false;
            },
        },
        beforeMount: function () {
        },
        mounted: function (){
            that = this;
            // $('.carousel .carousel-item').each(function () {
            //     var minPerSlide = 1;
            //     var next = $(this).next();
            //     if (!next.length) {
            //         next = $(this).siblings(':first');
            //     }
            //     next.children(':first-child').clone().appendTo($(this));
            //
            //     for (var i = 0; i < minPerSlide; i++) {
            //         next = next.next();
            //         if (!next.length) {
            //             next = $(this).siblings(':first');
            //         }
            //
            //         next.children(':first-child').clone().appendTo($(this));
            //     }
            // });
        },
        updated: function () {
            // $('.carousel .carousel-item').each(function () {
            //     var minPerSlide = 1;
            //     var next = $(this).next();
            //     if (!next.length) {
            //         next = $(this).siblings(':first');
            //     }
            //     next.children(':first-child').clone().appendTo($(this));
            //
            //     for (var i = 0; i < minPerSlide; i++) {
            //         next = next.next();
            //         if (!next.length) {
            //             next = $(this).siblings(':first');
            //         }
            //
            //         next.children(':first-child').clone().appendTo($(this));
            //     }
            // });
        }
    })


</script>