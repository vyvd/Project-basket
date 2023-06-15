<?php
$this->setControllers(array("course", "courseCategory"));
$css = array("home-page.css");
$pageTitle = "Online Courses & Qualifications";
$metaDesc = 'Join over 800,000 students who have taken one of our 700+ online courses. Study at your own pace and gain a CPD certified qualification.';
//include BASE_PATH . 'cache.top.php'; // we want to cache this page
include BASE_PATH . 'header.php';

$categories = $this->getCourseCategories();
?>

    <!-- Main Content -->
    <main role="main" class="HomePage">

        <!-- Course Categories Slider-->
        <div class="container wider-container" style="margin-top:-90px">

            <div class="home-slider padded-sec">
                <h1 class="section-title">Online Courses</h1>
                <p class="section-sub-title">Course Categories</p>
                <div id="courseCategorySlider" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">

                        <div class="carousel-item active">
                            <div class="row justify-content-center">
                                <div class="col-12 col-md-12">
                                    <div class="category-box-sub">
                                        <?php
                                            foreach(ORM::for_table("courseCategories")->where("showOnHome", "1")->where_null('parentID')->order_by_asc("title")->find_many() as $category) {
                                                ?>
                                                <div class="category-box flip-card">
                                                    <a href="<?= SITE_URL ?>courses/<?= $category->slug ?>">
                                                        <div class="flip-card-inner">
                                                            <div class="flip-card-front">
                                                                <img src="<?= $this->courseCategory->getCategoryImage($category->id, "medium") ?>" alt="<?= $category->title ?>" />
                                                                <div class="category-title"><?= $category->title ?></div>
                                                            </div>
                                                            <div class="flip-card-back">
                                                                  <span class="hover">
                                                                <?= ORM::for_table("courseCategoryIDs")->where("category_id", $category->id)->count() ?> courses
                                                            </span>
                                                            </div>
                                                        </div>

                                                    </a>
                                                </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="all-course-btn"><button type="button" class="btn btn-outline-primary btn-lg extra-radius" onclick="parent.location='<?= SITE_URL ?>courses'">View All Courses</button></div>

            </div>

        </div>
        <!-- Course Categories Slider End-->

        <?php
        include BASE_PATH . 'why-new-skills.php';
        ?>
        <?php include BASE_PATH . 'newsletter.php'; ?>
        <section class="container wider-container">
            <div class="padded-sec">
                <div class="life-access-banner">
                    <h4>All New Skills Academy Courses Come With Lifetime Access!</h4>
                </div>
            </div>
        </section>

        <!-- Popular Course Slider-->
        <section class="bottom-bordered-section">
            <div class="container wider-container">

                <div class="home-slider padded-sec">
                    <h2 class="section-title">Popular Courses</h2>
                    <div id="popularCoursesSlider" class="popularCoursesSlider popularCourseboxes owl-carousel">
                        <?php

                        if($currency->code == "GBP") {
                            $courses = ORM::for_table("courses")->where("hidden", "0")->where("featured", "1")->find_many();
                        } else {
                            $courses = ORM::for_table("courses")->where("hidden", "0")->where("featured", "1")->where("usImport", "1")->find_many();
                        }

                        foreach ($courses as $key => $course){

                            $position = $key + 1;

                            $course_cats = $this->course->getCourseCategories($course->id);

                            //var_dump($course_cats);

                            $course_type = $this->course->getCourseType($course->id);

                            $course->price = $this->getCoursePrice($course);

                            ?>
                            <div class="item single_course single-course-wrapper" data-position="<?php echo $position; ?>">
                                <div class="category-box">
                                    <div class="img" style="background-image:url('<?= $this->course->getCourseImage($course->id, "large") ?>');"></div>
                                    <div class="Popular-title-top"><i class="far fa-user"></i> <?= $course->enrollmentCount ?> students enrolled</div>
                                    <div class="Popular-title-bottom"><span class="nsa_course_title"><?= $course->title ?></span>

                                        <h3 class="course_price"><?php
                                            // affiliate pricing
                                            $excludedCourses = explode(",", $_SESSION["excludedCourses"]);

                                            if($_SESSION["affiliateDiscount"] != "" && !in_array($course->id, $excludedCourses)) {

                                                $original = $course->price;
                                                $discounted = $course->price;
                                                $changed = false;

                                                if($_SESSION["affiliateDiscountType"] == "fixed") {
                                                    $discounted = $discounted-$_SESSION["affiliateDiscount"];
                                                } else {
                                                    $discounted = $discounted * ((100-$_SESSION["affiliateDiscount"]) / 100);
                                                }

                                                if($_SESSION["affiliateDiscountMax"] != "") {

                                                    if($course->price <= $_SESSION["affiliateDiscountMax"]) {
                                                        $course->price = $discounted;
                                                        $changed = true;
                                                    }

                                                } else if($_SESSION["affiliateDiscountMin"] != "") {

                                                    if($course->price >= $_SESSION["affiliateDiscountMin"]) {
                                                        $course->price = $discounted;
                                                        $changed = true;
                                                    }

                                                } else {
                                                    $course->price = $discounted;
                                                    $changed = true;
                                                }


                                                if($changed == true) {
                                                    $course->price = '<small class="wasPriceSmall">RRP <s>'.$this->price($original).'</s></small>'.$this->price($course->price);
                                                } else {
                                                    $course->price = $this->price($course->price);
                                                }

                                            } else {
                                                $course->price = $this->price($course->price);
                                            }
                                            echo $course->price;
                                            ?></h3>

                                    </div>
                                    <div class="popular-box-overlay">
                                        <p><strong><?= $course->title ?></strong></p>
                                        <div class="popular-overlay-btn">
                                            <?php
                                            if($course->childCourses == "") {
                                                ?>
                                                <a href="<?= SITE_URL ?>course/<?= $course->slug ?>"
                                                        class="btn btn-outline-primary btn-lg extra-radius"><?= ORM::for_table("courseModules")
                                                        ->where("courseID", $course->id)->count() ?>
                                                    Modules
                                                </a>
                                                <?php
                                            } else {
                                                ?>
                                                <a href="<?= SITE_URL ?>course/<?= $course->slug ?>"
                                                        class="btn btn-outline-primary btn-lg extra-radius"><?= count(json_decode($course->childCourses)) ?>
                                                    Courses
                                                </a>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="popular-overlay-btn"><a href="<?= SITE_URL ?>course/<?= $course->slug ?>" class="btn btn-outline-primary btn-lg extra-radius">0% Finance</a></div>
                                        <h3 class="course_price"><?= $course->price ?></h3>
                                        <div class="popular-overlay-btn-btm">
                                            <a class="btn btn-outline-primary btn-lg extra-radius nsa_course_more_info" href="<?= SITE_URL ?>course/<?= $course->slug ?>" role="button">More Info</a>
                                            <a class="btn btn-outline-primary btn-lg extra-radius start-course-button nsa_add_to_cart_btn"
                                               data-course-id="<?= $course->id ?>" data-course-oldid="<?= $course->oldID ?>" data-oldproductid="<?= $course->productID ?>" data-course_type="<?= $course_type ?>" data-course-cats="<?= implode(', ', $course_cats) ?>"
                                               href="javascript:;" role="button">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>

            </div>
        </section>

        <?php
        $courseCount = 700;

        if($currency->code != "GBP") {
            $courseCount = 300;
        }
        ?>
        <section class="container wider-container">
            <div class="premiumBanner" onclick="parent.location='<?= SITE_URL ?>subscription'">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <img src="<?= SITE_URL ?>assets/images/course-trophy.png" alt="course trophy">
                        <label class="premium_membership">Premium Membership</label>
                    </div>
                    <div class="col-md-9">
                        <h3 class="premium_title">Get Access To Our Entire Course Library</h3>
                        <div class="premium_price">
                            <span>Only</span>
                            <label><sup><?= $currency->short ?></sup><?= $currency->prem12 ?></label>
                            <span>Per Year</span>
                        </div>
                        <ul class="premium_list">
                            <li><i class="fa fa-check-circle" aria-hidden="true"></i> Study <?= $courseCount ?>+ courses</li>
                            <li><i class="fa fa-check-circle" aria-hidden="true"></i> Unlimited access to study <small>(max 50 active courses at any one time)</small></li>
                            <li><i class="fa fa-check-circle" aria-hidden="true"></i> Career matching service</li>
                            <?php if($currency->code == "GBP") { ?><li><i class="fa fa-check-circle" aria-hidden="true"></i> Free XO Student Discounts membership</li><?php } ?>
                        </ul>

                        <a href="<?= SITE_URL ?>subscription" class="premium_start_button">Start Now</a>
                    </div>
                </div>
            </div>
        </section>

        <?php include BASE_PATH . 'learn-confidence.php'; ?>
        <?php include BASE_PATH . 'stats.php'; ?>
        <?php include BASE_PATH . 'featured.php'; ?>

        <!-- Popular Course Slider End-->
        <?php include BASE_PATH . 'success-stories.php'; ?>

    </main>
    <!-- Main Content End-->
    <script>
        $(document).ready(function (){
            let owl = $('.popularCoursesSlider.owl-carousel').owlCarousel({
                loop:false,
                autoplay: true,
                margin:10,
                nav:true,
                responsive:{
                    0:{
                        items:1
                    },
                    600:{
                        items:4
                    },
                    1000:{
                        items:4
                    }
                }
            });

            owl.on('changed.owl.carousel', function(e) {

                $('body').trigger('popular_courses_visible');
                //console.log('trigger popular_courses_visible event on owl carousel moved');

            });

        });
    </script>

<?php
// forgot password modal
if($this->get["pass"] == "true") {
    ?>
    <div class="modal fade basket signIn" id="reset" tabindex="-1" role="dialog" aria-labelledby="basketTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <a class="btn-close" data-dismiss="modal">X</a>
                    <h1 class="text-center">Reset Password</h1>

                </div>
                <div class="modal-body coupon">
                    <form name="resetPassword">
                        <div class="form-group">
                            <label>Your New Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="passwordConfirm" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Your Email Address</label>
                            <input type="email" name="email" placeholder="So we can confirm your account..." class="form-control">
                        </div>

                        <input type="hidden" name="id" value="<?= $this->get["id"] ?>" />
                        <input type="hidden" name="pwToken" value="<?= $this->get["token"] ?>" />


                        <div class="totals">
                            <button type="submit" class="btn btn-primary extra-radius">Reset Password</button>
                        </div>
                    </form>

                    <?php
                    $this->renderFormAjax("account", "reset-password", "resetPassword");
                    ?>

                </div>
            </div>
        </div>
    </div>

    <script>
        $( document ).ready(function() {
            $("#reset").modal("toggle");
        });
    </script>
    <?php
}
?>


<?php include BASE_PATH . 'footer.php';?>

<script>

    let brand = 'NSA';

    let list = 'Home Page';

    $('.popularCourseboxes').on('click', '.nsa_add_to_cart_btn', function (e) {

        //console.log('add to cart clicked');

        let $this = $(this);
        let course = $this.closest('.single_course');

        //let course_id = $this.attr("data-course-id");
        //let course_id = $this.attr("data-course-oldid");
        let course_id = $this.attr("data-oldproductid");

        if(!course_id || SITE_TYPE == 'us') {
            course_id = $this.attr("data-course-id");
        }

        let course_title = course.find('.nsa_course_title').text();
        let categories = course.attr('data-course-cats');
        let content_ids = [course_id];

        let price_html = course.find('.course_price').clone();
        price_html.find('.wasPriceSmall').remove();
        let course_price = price_html.text().replace("£", "").replace("$", "");

        course_price = parseFloat(course_price).toFixed(2);

        let contents = [{"id":course_id,"quantity":1,"item_price":course_price}];

        let course_type = course.attr('course-type');

        let event_id = 'add_to_cart.'+ CUR_ID_FRONT + '.' +content_ids.join('');


        fbq(
            'track',
            'AddToCart',
            {
                content_type: "product",
                domain: DOMAIN_NAME,
                event_hour: event_hour,
                //user_roles: "student",
                category_name: categories,
                currency: CURRENCY,
                value: course_price,
                content_name: course_title,
                content_ids: content_ids,
                event_day: event_day,
                product_price: course_price,
                contents: contents,
                event_month: event_month

            }
        );


        dataLayer.push({
            'event': 'addToCart',
            'ecommerce': {
                'currencyCode': 'USD',
                'add': {
                    'products': [{
                        'name': course_title,
                        'id': course_id,
                        'price': course_price,
                        'brand': brand,
                        'category': categories,
                        'variant': course_type,
                        'quantity': 1
                    }]

                }
            }

        });

        ttq.track('AddToCart',{
            content_id: course_id,
            content_name: course_title,
            quantity: 1,
            price: course_price,
            value: course_price,
            currency: CURRENCY,
        });

        snaptr('track', 'ADD_CART', {
            'currency': "GBP",
            'price': course_price,
            'item_category': categories,
            'item_ids': course_id,
            'number_items': 1
        });


    });


</script>

<script>

    jQuery(document).ready(function ($) {

        let brand = 'NSA';
        let list = 'Homepage';

        $('body').on('popular_courses_visible', function (e) {

            //console.log(window.sent_impressions);

            let impressions = [];

            //$('.popular_courses_listing .single_popular_course').each(function(index, obj){
            $('.popularCourseboxes .owl-item').withinviewport({sides:'top bottom', top: -300, bottom: -300}).each(function(index, obj){


                let add_to_cart_btn = $(obj).find('.nsa_add_to_cart_btn');

                let course_title = $(obj).find('.nsa_course_title').text();
                let cat_names = add_to_cart_btn.attr('data-course-cats');
                let course_type = $(obj).attr('data-course_type');
                //let course_id = $(obj).attr('data-course_id');
                //let course_id = $(obj).attr('data-product_id');
                let course_id = add_to_cart_btn.attr('data-oldproductid');

                if(!course_id || SITE_TYPE == 'us') {
                    course_id = add_to_cart_btn.attr('data-course-id');
                }

                //let price_html = $(obj).find('.woocommerce-Price-amount.amount').clone();
                //price_html.find('span').remove();
                //let course_price = parseFloat(price_html.text()).toFixed(2);

                /*
                let course_price = $(obj).find('.course_price').text().replace("£", "").replace("$", "");
                course_price = parseFloat(course_price).toFixed(2);
                */

                let price_html = $(obj).find('.course_price').clone();
                price_html.find('.wasPriceSmall').remove();
                let course_price = price_html.text().replace("£", "").replace("$", "");

                course_price = parseFloat(course_price).toFixed(2);

                //let position = index + 1;
                let position = $(obj).find('.item').attr('data-position');


                let course_object = {
                    'name': course_title,
                    'id': course_id,
                    'price': course_price,
                    'brand': brand,
                    'category': cat_names,
                    'variant': course_type,
                    'list': list,
                    'position': position
                };

                impressions.push(course_object);


            });



            //if(window.sent_impressions.length == 0) {
            if(impressions.length > 0) {

                //let impressions_from_cookie = getCookie('productImpression', true);
                //let string_impressions = JSON.stringify(impressions);

                //console.log(impressions_from_cookie);
                //console.log(string_impressions);

                let impressions_to_sent = impressions.filter( function( el ) {

                    //console.log(el);
                    //console.log(window.sent_impressions.indexOf( el ));

                    let result = window.sent_impressions.filter(obj => {
                        return obj.name === el.name;
                    });

                    //return window.sent_impressions.indexOf( el ) < 0;

                    //console.log(result);

                    return result.length == 0;

                });

                if( impressions_to_sent.length > 0 && JSON.stringify(impressions_to_sent) != JSON.stringify(window.last_sent_impressions) ) {
                    //if(impressions_from_cookie != string_impressions) {

                    //console.log('impressions', impressions);
                    //console.log('sent_impressions', window.sent_impressions);
                    //console.log('last_sent_impressiosn', window.last_sent_impressions);
                    //console.log('impressions_to_sent', impressions_to_sent);

                    dataLayer.push({
                        'event': 'productImpression',
                        'ecommerce': {
                            'currencyCode': 'USD',
                            'impressions': impressions_to_sent
                        }
                    });

                    window.sent_impressions = window.sent_impressions.concat(impressions_to_sent).unique();

                    window.last_sent_impressions = impressions_to_sent;

                    //    createCookie('productImpression', string_impressions, 0.001);

                }

                //console.log('productImpression event triggered on Homepage');

            } else {

                //console.log('productImpression event already triggered');

            }

        });


        $('.popularCourseboxes').on('click', '.owl-item .nsa_course_more_info', function (e) {

            let $this = $(this);

            let course = $this.closest('.single-course-wrapper');


            let add_to_cart_btn = course.find('.nsa_add_to_cart_btn');

            let course_title = course.find('.nsa_course_title').text();
            let cat_names = add_to_cart_btn.attr('data-course-cats');
            let course_type = add_to_cart_btn.attr('data-course_type');
            //let course_id = add_to_cart_btn.attr('data-course_id');
            let course_id = add_to_cart_btn.attr('data-oldproductid');

            if(!course_id || SITE_TYPE == 'us') {
                course_id = add_to_cart_btn.attr('data-course-id');
            }

            //console.log( 'course_price', course.find('.course_price').text() );

            let course_price = course.find('.course_price').text().replace("£", "").replace("$", "");
            course_price = parseFloat(course_price).toFixed(2);

            //let position = index + 1;
            let position = course.attr('data-position');

            dataLayer.push({
                'event': 'productClick',
                'ecommerce': {
                    'click': {
                        'actionField': {'list': list},
                        'products': [{
                            'name': course_title,
                            'id': course_id,
                            'price': course_price,
                            'brand': brand,
                            'category': cat_names,
                            'variant': course_type,
                            'position': position
                        }]
                    }
                }
            });


            //console.log('productClick event triggered on Homepage');


        });


        $('#courseCategorySlider .category-box').on('click', function (e) {

            let category = $(this);
            let cat_name = category.find('.catname-term').text();

            gtag('event', 'click', {
                'event_label': cat_name,
                'event_category': 'course categories',
                'non_interaction': false
            });

        });



    });

</script>
<?php
//include BASE_PATH . 'cache.bottom.php'; // we want to cache this page