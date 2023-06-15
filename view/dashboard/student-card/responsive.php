<?php
$css = array("dashboard.css");
$pageTitle = "Student Card";
include BASE_PATH . 'account.header.php';
?>
<style>
    /*.carousel-item {*/
    /*    position: relative;*/
    /*    display: none;*/
    /*    float: left;*/
    /*    width: 100%;*/
    /*    margin-right: -100%;*/
    /*}*/
    /*.carousel-item.active {*/
    /*    display: flex;*/
    /*}*/
    .carousel-item > div{
        float: left;
    }

</style>
    <section class="page-title">
        <div class="container">
            <h1>Student Card</h1>
        </div>
    </section>
    <div id="studentCard">
        <div class="loading-card" v-if="loadingCard">
            <div class="container text-center">
                <i class="fa fa-spin fa-spinner" style="font-size:100px;margin-top:100px;color:#248CAB;"></i>
            </div>
        </div>
        <div v-else-if="buyCard">
            <section class="page-content">
                <div class="container">
                    <div class="row">
                        <div class="col-12 regular-full student-card">
                            <div class="row">
                                <div class="col-6">
                                    <div class="card-display">
                                        <img src="<?= SITE_URL ?>assets/images/banner-card.png" alt="Student card" />
                                    </div>
                                </div>
                                <div class="col-6 pl-0">
                                    <div class="card-content">
                                        <h4 class="text-uppercase">All New Skills Academy Customers Are Eligible For an XO Student Discount </h4>
                                        <ul>
                                            <li>Exclusively for students studying online</li>
                                            <li>100's of massive deals and bargains</li>
<!--                                            <li>Premium membership card delivered direct to you</li>-->
                                            <li>12 months of discounts for only £10</li>
                                        </ul>
                                        <div class="col-12 text-center">
                                            <!--<a @click="xoSignUp" href="javascript:void(0)" class="btn btn-light text-uppercase rounded">Join now for Free</a>-->
                                            <a href="https://xostudentdiscounts.co.uk/?ref=NSA" target="_blank" class="btn btn-light text-uppercase rounded">Join Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </div>
        <div v-else>
<!--            <section class="page-title with-nav mb-5">-->
<!--                <div class="container">-->
<!--                    <ul class="nav navbar-nav inner-nav nav-tabs">-->
<!--                        <li class="nav-item link">-->
<!--                            <a class="nav-link active show" href="#myCard"-->
<!--                               data-toggle="tab">My Card</a>-->
<!--                        </li>-->
<!--                        <li class="nav-item link ">-->
<!--                            <a class="nav-link" href="#latestDiscount"-->
<!--                               data-toggle="tab">Latest Discounts</a>-->
<!--                        </li>-->
<!--                        <li class="nav-item link ">-->
<!--                            <a class="nav-link" href="#billing"-->
<!--                               data-toggle="tab">Billing</a>-->
<!--                        </li>-->
<!--                    </ul>-->
<!--                </div>-->
<!--            </section>-->

            <div class="container mt-5">
                <div id="myCard" class="tab-pane subscription-tabs active show">
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-6 no-padding">
                            <div class="student-card-black dash-details">
                                <div class="st-card-logo d-flex">
                                    <img src="<?= SITE_URL ?>assets/user/images/xo-white.png"
                                         alt="xo" class="xo">
                                    <img style="max-width: 160px" :src="saveStudentData.profile_image"
                                         alt="student"
                                         class="st-profile bordered">
                                </div>
                                <div class="st-card-number d-flex justify-content-center">
                                    {{formatString(saveStudentData.membership)}}
                                </div>
                                <div class="st-card-footer d-flex">
                                    <h4 class="text-uppercase">
                                        {{saveStudentData.name}}</h4>
                                    <h4 class="text-uppercase">
                                        <span>Expiry</span><br/>
                                        {{formatDate(saveStudentData.expiry_date)}}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-6 no-padding membership-note white-rounded pt-5">
                            <div class=" dash-details text-center">
                                <h3 v-if="expiresInDays(saveStudentData.expiry_date) >= 1" class="text-center">
                                    Your membership expires in {{expiresInDays(saveStudentData.expiry_date)}} days
                                </h3>
                                <h3 v-else class="text-center"> Your membership has been expired </h3>
                                <a target="_blank" class="btn btn-primary upgrade"
                                   href="<?= XO_SITE_URL ?>student/my-card"
                                   v-if="expiresInDays(saveStudentData.expiry_date) <= 30">RENEW
                                    NOW</a>
                                <button class="btn btn-primary upgrade" v-else
                                        disabled>RENEW NOW
                                </button>

                                <p v-if="expiresInDays(saveStudentData.expiry_date) <= 30"
                                   class="text-center">You can renew your card
                                    up to 30 days before expiry</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!--latestDiscount-->
                <div id="latestDiscount"
                     class="tab-pane subscription-tabs latestDiscount show">
                    <div class="row">

                        <!--Latest Discount Slider start-->
                        <div v-if="saveStudentData.newest_deals.length" class="padded-sec addCourseSliders col-12">
                            <h2 class="latestDiscount-title">New Discount</h2>


                            <div id="latestDiscountSlider" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <div v-for="(item, index) in saveStudentData.newest_deals" class="carousel-item" :class="{ 'active': index === 0 }">
                                        <div class="col-6 col-md-6 col-lg-4">
                                            <div class="category-box text-center">
                                                <img :src="item.full_image" alt="lookfantastic" class="brand-img"/>
                                                <a class="wishlist" href=""><i class="far fa-heart"></i></a>
                                                <div class="disc-cnt">
                                                    <div class="brand-logo d-flex align-items-center">
                                                        <a target="_blank" :href="item.retailer_site_url + '?token=' + saveStudentData.loginToken"><img :src="item.retailer_image"></a>
                                                    </div>
                                                    <span class="brand-text d-flex align-items-center"><a target="_blank" :href="item.deal_site_url + '?token=' + saveStudentData.loginToken"> {{item.title}}</a></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a class="carousel-control-prev"
                                   href="#latestDiscountSlider" role="button"
                                   data-slide="prev">
                                    <i class="fas fa-chevron-left"></i>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next"
                                   href="#latestDiscountSlider" role="button"
                                   data-slide="next">
                                    <i class="fas fa-chevron-right"></i>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                        <!--Latest Discount Slider End-->

                        <!--Featured Discount Slider start-->
                        <div v-if="saveStudentData.featured_deals.length" class="padded-sec addCourseSliders col-12">
                            <h2 class="latestDiscount-title">Featured Discount</h2>


                            <div id="featuredDiscountSlider" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <div v-for="(item, index) in saveStudentData.featured_deals" class="carousel-item" :class="{ 'active': index === 0 }">
                                        <div class="col-6 col-md-6 col-lg-4">
                                            <div class="category-box text-center">
                                                <img :src="item.full_image" alt="lookfantastic" class="brand-img"/>
                                                <a class="wishlist" href=""><i class="far fa-heart"></i></a>
                                                <div class="disc-cnt">
                                                    <div class="brand-logo d-flex align-items-center">
                                                        <a target="_blank" :href="item.retailer_site_url + '?token=' + saveStudentData.loginToken"><img :src="item.retailer_image"></a>
                                                    </div>
                                                    <span class="brand-text d-flex align-items-center"><a target="_blank" :href="item.deal_site_url + '?token=' + saveStudentData.loginToken"> {{item.title}}</a></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a class="carousel-control-prev"
                                   href="#featuredDiscountSlider" role="button"
                                   data-slide="prev">
                                    <i class="fas fa-chevron-left"></i>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next"
                                   href="#featuredDiscountSlider" role="button"
                                   data-slide="next">
                                    <i class="fas fa-chevron-right"></i>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
<!--                        <div class="padded-sec addCourseSliders col-12">-->
<!--                            <h2 class="latestDiscount-title">Featured-->
<!--                                Discount</h2>-->
<!--                            <div id="featuredDiscountSlider"-->
<!--                                 class="carousel slide" data-ride="carousel">-->
<!--                                <ol class="carousel-indicators">-->
<!--                                    <li data-target="#featuredDiscountSlider"-->
<!--                                        data-slide-to="0" class="active"></li>-->
<!--                                    <li data-target="#featuredDiscountSlider"-->
<!--                                        data-slide-to="1"></li>-->
<!--                                    <li data-target="#featuredDiscountSlider"-->
<!--                                        data-slide-to="2"></li>-->
<!--                                </ol>-->
<!--                                <div class="carousel-inner">-->
<!--                                    -->
<!--                                    <div class="carousel-item active">-->
<!--                                        <div class="row justify-content-center">-->
<!--                                            <div class="col-6 col-md-6 col-lg-4">-->
<!--                                                <div class="category-box text-center">-->
<!--                                                    <img src="images/lookfantastic.jpg"-->
<!--                                                         alt="lookfantastic"-->
<!--                                                         class="brand-img"/>-->
<!--                                                    <a class="wishlist" href=""><i-->
<!--                                                                class="far fa-heart"></i></a>-->
<!--                                                    <div class="disc-cnt">-->
<!--                                                        <div class="brand-logo d-flex align-items-center">-->
<!--                                                            <img src="images/lookfantastic-logo.png">-->
<!--                                                        </div>-->
<!--                                                        <span class="brand-text d-flex align-items-center">Save up to 40% on this fragnances this Valentine's Day at LOOKFANTASTIC</span>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                            <div class="col-6 col-md-6 col-lg-4">-->
<!--                                                <div class="category-box text-center">-->
<!--                                                    <img src="images/jovonna-london.jpg"-->
<!--                                                         alt="jovonna"-->
<!--                                                         class="brand-img"/>-->
<!--                                                    <a class="wishlist" href=""><i-->
<!--                                                                class="far fa-heart"></i></a>-->
<!--                                                    <a class="xo-ex"-->
<!--                                                       href=""><img-->
<!--                                                                src="images/xo-logo.png"></a>-->
<!--                                                    <div class="disc-cnt">-->
<!--                                                        <div class="brand-logo d-flex align-items-center">-->
<!--                                                            <img src="images/jovonna-london-logo.png">-->
<!--                                                        </div>-->
<!--                                                        <span class="brand-text d-flex align-items-center">20% off everything at Jovonna London</span>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                            <div class="col-6 col-md-6 col-lg-4">-->
<!--                                                <div class="category-box text-center">-->
<!--                                                    <img src="images/foodhub.jpg"-->
<!--                                                         alt="foodhub"-->
<!--                                                         class="brand-img"/>-->
<!--                                                    <a class="wishlist" href=""><i-->
<!--                                                                class="far fa-heart"></i></a>-->
<!--                                                    <a class="xo-ex"-->
<!--                                                       href=""><img-->
<!--                                                                src="images/xo-logo.png"></a>-->
<!--                                                    <div class="disc-cnt">-->
<!--                                                        <div class="brand-logo d-flex align-items-center">-->
<!--                                                            <img src="images/foodhub-logo.png">-->
<!--                                                        </div>-->
<!--                                                        <span class="brand-text d-flex align-items-center">20% off orders at Foothub</span>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!---->
<!--                                        </div>-->
<!--                                    </div>-->
<!---->
<!--                                    -->
<!--                                    <div class="carousel-item">-->
<!--                                        <div class="row justify-content-center">-->
<!--                                            <div class="col-6 col-md-6 col-lg-4">-->
<!--                                                <div class="category-box text-center">-->
<!--                                                    <img src="images/lookfantastic.jpg"-->
<!--                                                         alt="lookfantastic"-->
<!--                                                         class="brand-img"/>-->
<!--                                                    <a class="wishlist" href=""><i-->
<!--                                                                class="far fa-heart"></i></a>-->
<!--                                                    <div class="disc-cnt">-->
<!--                                                        <div class="brand-logo d-flex align-items-center">-->
<!--                                                            <img src="images/lookfantastic-logo.png">-->
<!--                                                        </div>-->
<!--                                                        <span class="brand-text d-flex align-items-center">Save up to 40% on this fragnances this Valentine's Day at LOOKFANTASTIC</span>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                            <div class="col-6 col-md-6 col-lg-4">-->
<!--                                                <div class="category-box text-center">-->
<!--                                                    <img src="images/jovonna-london.jpg"-->
<!--                                                         alt="jovonna"-->
<!--                                                         class="brand-img"/>-->
<!--                                                    <a class="wishlist" href=""><i-->
<!--                                                                class="far fa-heart"></i></a>-->
<!--                                                    <a class="xo-ex"-->
<!--                                                       href=""><img-->
<!--                                                                src="images/xo-logo.png"></a>-->
<!--                                                    <div class="disc-cnt">-->
<!--                                                        <div class="brand-logo d-flex align-items-center">-->
<!--                                                            <img src="images/jovonna-london-logo.png">-->
<!--                                                        </div>-->
<!--                                                        <span class="brand-text d-flex align-items-center">20% off everything at Jovonna London</span>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                            <div class="col-6 col-md-6 col-lg-4">-->
<!--                                                <div class="category-box text-center">-->
<!--                                                    <img src="images/foodhub.jpg"-->
<!--                                                         alt="foodhub"-->
<!--                                                         class="brand-img"/>-->
<!--                                                    <a class="wishlist" href=""><i-->
<!--                                                                class="far fa-heart"></i></a>-->
<!--                                                    <a class="xo-ex"-->
<!--                                                       href=""><img-->
<!--                                                                src="images/xo-logo.png"></a>-->
<!--                                                    <div class="disc-cnt">-->
<!--                                                        <div class="brand-logo d-flex align-items-center">-->
<!--                                                            <img src="images/foodhub-logo.png">-->
<!--                                                        </div>-->
<!--                                                        <span class="brand-text d-flex align-items-center">20% off orders at Foothub</span>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!---->
<!--                                        </div>-->
<!--                                    </div>-->
<!---->
<!--                                    -->
<!--                                    <div class="carousel-item">-->
<!--                                        <div class="row justify-content-center">-->
<!--                                            <div class="col-6 col-md-6 col-lg-4">-->
<!--                                                <div class="category-box text-center">-->
<!--                                                    <img src="images/lookfantastic.jpg"-->
<!--                                                         alt="lookfantastic"-->
<!--                                                         class="brand-img"/>-->
<!--                                                    <a class="wishlist" href=""><i-->
<!--                                                                class="far fa-heart"></i></a>-->
<!--                                                    <div class="disc-cnt">-->
<!--                                                        <div class="brand-logo d-flex align-items-center">-->
<!--                                                            <img src="images/lookfantastic-logo.png">-->
<!--                                                        </div>-->
<!--                                                        <span class="brand-text d-flex align-items-center">Save up to 40% on this fragnances this Valentine's Day at LOOKFANTASTIC</span>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                            <div class="col-6 col-md-6 col-lg-4">-->
<!--                                                <div class="category-box text-center">-->
<!--                                                    <img src="images/jovonna-london.jpg"-->
<!--                                                         alt="jovonna"-->
<!--                                                         class="brand-img"/>-->
<!--                                                    <a class="wishlist" href=""><i-->
<!--                                                                class="far fa-heart"></i></a>-->
<!--                                                    <a class="xo-ex"-->
<!--                                                       href=""><img-->
<!--                                                                src="images/xo-logo.png"></a>-->
<!--                                                    <div class="disc-cnt">-->
<!--                                                        <div class="brand-logo d-flex align-items-center">-->
<!--                                                            <img src="images/jovonna-london-logo.png">-->
<!--                                                        </div>-->
<!--                                                        <span class="brand-text d-flex align-items-center">20% off everything at Jovonna London</span>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                            <div class="col-6 col-md-6 col-lg-4">-->
<!--                                                <div class="category-box text-center">-->
<!--                                                    <img src="images/foodhub.jpg"-->
<!--                                                         alt="foodhub"-->
<!--                                                         class="brand-img"/>-->
<!--                                                    <a class="wishlist" href=""><i-->
<!--                                                                class="far fa-heart"></i></a>-->
<!--                                                    <a class="xo-ex"-->
<!--                                                       href=""><img-->
<!--                                                                src="images/xo-logo.png"></a>-->
<!--                                                    <div class="disc-cnt">-->
<!--                                                        <div class="brand-logo d-flex align-items-center">-->
<!--                                                            <img src="images/foodhub-logo.png">-->
<!--                                                        </div>-->
<!--                                                        <span class="brand-text d-flex align-items-center">20% off orders at Foothub</span>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!---->
<!--                                        </div>-->
<!--                                    </div>-->
<!---->
<!--                                </div>-->
<!--                                <a class="carousel-control-prev"-->
<!--                                   href="#featuredDiscountSlider" role="button"-->
<!--                                   data-slide="prev">-->
<!--                                    <i class="fas fa-chevron-left"></i>-->
<!--                                    <span class="sr-only">Previous</span>-->
<!--                                </a>-->
<!--                                <a class="carousel-control-next"-->
<!--                                   href="#featuredDiscountSlider" role="button"-->
<!--                                   data-slide="next">-->
<!--                                    <i class="fas fa-chevron-right"></i>-->
<!--                                    <span class="sr-only">Next</span>-->
<!--                                </a>-->
<!--                            </div>-->
<!--                        </div>-->
                        <!--Featured DiscountSlider Slider End-->

                        <!--Affiliates  start-->
                        <div v-if="saveStudentData.affiliates.length" class="padded-sec addCourseSliders col-12">
                            <h2 class="latestDiscount-title">Popular Retailers</h2>
                            <div class="row">
                                <div v-for="(item, index) in saveStudentData.affiliates" class="col-6 col-md-2 col-lg-2 mb-3">
                                    <div class="category-box text-center" style="border-radius: 20px;" >
                                        <a target="_blank" :href="item.retailer_site_url + '?token=' + saveStudentData.loginToken"><img style="border-radius: 20px;" :src="item.logo_thumb_image" alt="lookfantastic" class="brand-img"/></a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--Affiliates  End-->

                        <div class="col-12 text-center">
                            <a target="_blank" :href="'<?php echo XO_SITE_URL;?>?token=' + saveStudentData.loginToken"
                               class="btn btn-primary text-uppercase extra-radius visit-xo">
                                Visit XO Student Discount to See All Deals
                            </a>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

<!--    <script src="https://unpkg.com/vue@3.0.7/dist/vue.global.prod.js"></script>-->
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"-->
<!--            integrity="sha512-bZS47S7sPOxkjU/4Bt0zrhEtWx0y0CRkhEp8IckzK+ltifIIE9EMIMTuT/mEzoIMewUINruDBIR/jJnbguonqQ=="-->
<!--            crossorigin="anonymous"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script src="<?= SITE_URL ?>assets/vendor/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
            integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
            crossorigin="anonymous"></script>
    <script>
        $(document).ready(function (){
            // $('#latestDiscountSlider').carousel({
            //     interval: 2000
            // })

        });
        var app = new Vue({
            el: '#studentCard',
            data: {
                loadingCard: true,
                saveStudentData: [],
                axiosCancelSource: null,
                loginToken: null,
                buyCard: false
            },
            methods: {
                getInitialData: function () {
                    that = this;
                    const url = "<?= SITE_URL ?>ajax?c=import&a=xo_user_data";
                    axios.get(url,
                    ).then(response => {
                            if (response.data.status == 200) {
                                that.saveSessionStorage(response.data.data);
                            } else {
                                that.loadingCard = false;
                                that.buyCard = true;
                            }
                        }
                    );
                },
                xoSignUp: function () {
                    that = this;
                    that.loadingCard = true;
                    const url = "<?= SITE_URL ?>ajax?c=import&a=xo_user_data&register=true";
                    axios.get(url,
                    ).then(response => {
                        console.log(response);
                            if (response.data.status == 200) {
                                that.saveSessionStorage(response.data.data);
                                location.reload();
                            } else {
                                that.loadingCard = false;
                                that.buyCard = true;
                            }
                        }
                    );
                },
                getStorageData: function () {
                    that = this;
                    // Get session storage media data
                    that.saveStudentData = JSON.parse(sessionStorage.NSA_studentCard);
                    that.displayData();
                },
                saveSessionStorage: function (storageData) {
                    that = this;
                    that.saveStudentData = {
                        'name': storageData.user.name,
                        'expiry_date': storageData.user.expiry_date,
                        'membership': storageData.user.membership_id,
                        'profile_image': storageData.user.profile_image,
                        'payments': storageData.payments,
                        'newest_deals': storageData.newest_deals,
                        'featured_deals': storageData.featured_deals,
                        'affiliates': storageData.affiliates,
                        'loginToken': storageData.login.token,
                    };


                    sessionStorage.NSA_studentCard = JSON.stringify(this.saveStudentData);
                    sessionStorage.NSA_date = "<?= date("Y-m-d")?>";
                    sessionStorage.NSA_accountID = "<?= CUR_ID_FRONT ?>";


                    this.displayData();
                },
                displayData: function () {
                    that = this;
                    that.loadingCard = false;

                },
                formatDate: function (date) {
                    return moment(date).format("DD/MM/YY");
                },
                formatString: function (string) {
                    const a = string.toString();
                    return a.substring(0, 4) + " " + a.substring(4, 8) + " " + a.substring(8, 12) + " " + a.substring(12, 16);
                },
                expiresInDays: function (date) {
                    var a = moment("<?= date("Y-m-d")?>");
                    var b = moment(date);
                    return b.diff(a, 'days')
                }
            },
            beforeMount: function () {
                that = this;
                that.loadingCard = true;
                if ((sessionStorage.NSA_accountID == "<?= CUR_ID_FRONT ?>") && (sessionStorage.NSA_date == "<?= date("Y-m-d")?>")) {
                    that.getStorageData();
                } else {
                    that.getInitialData();
                }
            },
            mounted: function (){
                $('.carousel .carousel-item').each(function () {
                    var minPerSlide = 1;
                    var next = $(this).next();
                    if (!next.length) {
                        next = $(this).siblings(':first');
                    }
                    next.children(':first-child').clone().appendTo($(this));

                    for (var i = 0; i < minPerSlide; i++) {
                        next = next.next();
                        if (!next.length) {
                            next = $(this).siblings(':first');
                        }

                        next.children(':first-child').clone().appendTo($(this));
                    }
                });
            },

            updated: function () {
                $('.carousel .carousel-item').each(function () {
                    var minPerSlide = 1;
                    var next = $(this).next();
                    if (!next.length) {
                        next = $(this).siblings(':first');
                    }
                    next.children(':first-child').clone().appendTo($(this));

                    for (var i = 0; i < minPerSlide; i++) {
                        next = next.next();
                        if (!next.length) {
                            next = $(this).siblings(':first');
                        }

                        next.children(':first-child').clone().appendTo($(this));
                    }
                });
            }
        })

    </script>


<?php include BASE_PATH . 'account.footer.php'; ?>