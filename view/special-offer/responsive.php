<?php
$this->setControllers(array("course"));

$css = array("animate.min.css", "offers.css", "journeys.css");

$offer = $this->controller->getOffer();

$courses = json_decode($offer->courses);

$pageTitle = $offer->title;

// get courses in order
$coursesOrder = array();

$query = ORM::for_table("courses")->where_in("id", $courses)->order_by_desc("enrollmentCount")->find_many();

foreach($query as $c) {

    array_push($coursesOrder, $c->id);

}


include BASE_PATH . 'header.php';

$offer->contents = str_replace("%currency%", $currency->short, $offer->contents);

?>
<style>
    #bannerTop {
        display:none !important;
    }
    .Header.fixed {
        top:0px;
    }
    .popularCourseboxes .category-box:hover .Popular-title-bottom, .popularCourseboxes .category-box:hover .Popular-title-top {
        opacity: 1;
    }
    .offerContents ul {
        list-style:none;
        padding:0;
        margin:0;
        margin-top:30px;
        margin-bottom:30px;
    }
    .offerContents ul li {
        margin-bottom: 18px;
        font-size: 1.1rem;
        list-style-position: inside;
        line-height: 1.6rem;
        text-indent: -1.1rem;
    }
    .offerContents ul li::before {
        content: "\f00c";
        font-family: "Font Awesome 5 Pro";
        font-weight: 900;
        position: relative;
        left: -10px;
        top: 1px;
        color: #259cc0;
    }
    #hero .cta {
        background: #b2d489;
    }
    #hero .cta:hover {
        color:#fff;
        background: #809c5f;
    }
    .journeyCard .cta {
        background: #b2d489;
        margin-bottom: 10px;
    }
    .journeyCard .cta:hover {
        background: #819c60;
    }
    .journeyCard .image .saving {
        background: #248cab;
        color:#fff;
    }
    #hero {
        padding:70px 0px;
        margin-bottom:30px;
    }
    #hero:after {
        display:none;
    }
    .journeyCard .image .saving i {
        color:#fff;
    }
    .journeyCard h3 {
        font-size:24px;
    }
    .btn-primary {
        background-color: #259cc0;
        border-color: #259cc0;
        color: #ffffff;
    }
    #hero ul {
        padding: 0;
        text-align: center;
        list-style-position: inside;
    }
    #hero h1 {
        font-size:45px;
    }
    #home .cta {
        background:#259cc0;
    }
    @media(max-width:700px) {
        .Popular-title-bottom {
            display:block;
        }
        #hero h2 {
            font-size:25px;
        }
        #hero {
            padding:30px 0px;
        }
        #hero h3, #hero h4 {
            font-size:20px;
        }
        .offerCourses {
            padding:10px;
        }
        .addOfferForm {
            position: fixed;
            right: 0;
            bottom: 0;
            background: #ffff;
            width: 100%;
            text-align: center;
            -webkit-box-shadow: 0px 5px 32px 0px rgb(0 0 0 / 11%);
            -moz-box-shadow: 0px 5px 32px 0px rgba(0,0,0,0.11);
            box-shadow: 0px 5px 32px 0px rgb(0 0 0 / 11%);
            padding: 9px;
            border-top-left-radius: 0px;
            font-size: 18px;
            z-index: 999;
        }
        .addOfferForm .totals .btn {
            margin-top:-5px;
        }
    }
    #hero, .journeyCard .image .saving, .btn-primary {
        background:<?= $offer->primaryCol ?>
    }
    .btn-primary {
        border:0;
    }
    .btn-primary:hover, #hero .cta, #hero .cta:hover, .journeyCard .cta, .journeyCard .cta:hover {
        background:<?= $offer->secondCol ?>
    }
    #hero .cta:hover, .journeyCard .cta:hover {
        opacity:0.7;
    }
</style>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <div id="hero">
            <div class="container wider-container">

                <div class="row">
                    <div class="col-12 col-lg-12 text-center">

                        <h2 class="wow fadeInUp"><?= $offer->title ?></h2>
                        <?= $offer->contents ?>

                        <br />

                        <a href="#selectCourses" class="cta wow pulse" data-wow-delay="3s">
                            Claim Offer
                        </a>

                    </div>
                </div>

            </div>
        </div>

        <br />
        <br />

    <?php if(count($courses) == 1) { ?>
        <style>
            .popularCourseboxes .category-box .img {
                height:555px;
            }
            .Popular-title-top {
                font-size:24px;
            }
            #returnCourses {
                max-width: 600px;
                margin: auto;
            }
        </style>
    <?php } ?>

        <!--courses listing-->
        <section class="courses-listing" id="selectCourses">
            <div class="container wider-container offerCourses">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12 popularCourseboxes">
                        <div class="row nsa-offer-courses" id="returnCourses">

                            <?php

                            $position = 0;
                            foreach($coursesOrder as $item) {
                                $position++;
                                $course = ORM::for_table("courses")->find_one($item);

                                $course_cats = $this->course->getCourseCategories($course->id);
                                $course_type = $this->course->getCourseType($course->id);

                                $course->price = $this->getCoursePrice($course);

                                ?>
                                <div data-course-id="<?= $course->id ?>" data-position="<?= $position; ?>" data-course-oldid="<?= $course->oldID ?>" data-oldproductid="<?= $course->productID ?>" data-course-cats="<?= implode(', ', $course_cats) ?>" data-course_type="<?= $course_type; ?>" class="single_offer_course col-12 <?php if(count($courses) == 1) { ?>col-md-12 col-lg-12<?php } else { ?>col-md-6 col-lg-6<?php } ?>">




                                        <div class="journeyCard wow fadeInUp course<?= $course->id ?>">

                                            <div class="row">
                                                <div class="col-12 col-lg-5 col-md-5">
                                                    <div class="image">

                                                        <div class="saving">
                                                            <i class="fas fa-tags"></i>
                                                            RRP <?= $this->price($course->price) ?>
                                                        </div>

                                                        <img src="<?= $this->course->getCourseImage($course->id, "large") ?>" />


                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-7 col-md-7">
                                                    <div class="contents">
                                                        <h3><?= $course->title ?></h3>

                                                        <div class="usps">
                                                            <div class="item">
                                                                <?= ORM::for_table("courseModules")->where("courseID", $course->id)->count() ?> modules
                                                            </div>
                                                        </div>

                                                        <a href="<?= SITE_URL ?>course/<?= $course->slug ?>" class="cta" target="_blank" style="opacity:0.5;">
                                                            More Info
                                                            <i class="fas fa-chevron-right"></i>
                                                        </a>

                                                        <a href="javascript:;" onclick="addToOffer('<?= $course->id ?>');" class="cta btnText<?= $course->id ?>">
                                                            Add To Offer
                                                            <i class="fas fa-check"></i>
                                                        </a>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                </div>
                                <?php

                            }
                            ?>

                            <div class="col-12">
                                <form name="addOffer" class="addOfferForm">

                                    <p>
                                        <strong>Maximum Courses:</strong> <?= $offer->maxCourses ?><br />
                                        <strong>You've Selected:</strong> <span class="offerCount">0</span><br />
                                        <strong>Price:</strong> <span class="offerPrice"><?= $this->price($offer->course1Price) ?></span>
                                    </p>

                                    <input type="hidden" name="courses" id="selectedCourses" value="" />
                                    <input type="hidden" name="coursesCount" id="selectedCount" value="0" />
                                    <input type="hidden" name="offerID" value="<?= $offer->id ?>" />

                                    <div class="totals">
                                        <button type="submit" class="btn btn-primary extra-radius">
                                            <i class="fas fa-shopping-cart" style="margin-right:4px;"></i> Proceed to checkout
                                        </button>
                                    </div>

                                </form>
                                <?php
                                $this->renderFormAjax("cart", "add-offer", "addOffer");
                                ?>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            function addToOffer(courseID) {

                var current = $("#selectedCourses").val();
                var count = parseInt($("#selectedCount").val());
                var course1Price = '<?= $this->price($offer->course1Price) ?>';
                var courseOtherPrice = '<?= $this->price($offer->courseOtherPrice) ?>';

                if ( $( ".course"+courseID ).hasClass( "courseSelected" ) ) {

                    $(".course"+courseID).removeClass("courseSelected");
                    $(".btnText"+courseID).html('Add to Offer');

                    current = current.replace(","+courseID, "");

                    $("#selectedCourses").val(current);

                    count = count-1;

                } else {

                    if(count == <?= $offer->maxCourses ?>) {
                        toastr.options.positionClass = "toast-bottom-left";
                        toastr.options.closeDuration = 1000;
                        toastr.options.timeOut = 5000;
                        toastr.error('You cannot add any more courses to this offer.', 'Oops');
                        return false;
                    }

                    $(".course"+courseID).addClass("courseSelected");
                    $(".btnText"+courseID).html('Remove from Offer <i class="fa fa-times"></i>');

                    $("#selectedCourses").val(current+','+courseID);

                    count = count+1;


                }

                $("#selectedCount").val(count);
                $(".offerCount").html(count);

                if(count == 1 || count == 0) {
                    $(".offerPrice").html(course1Price);
                } else {
                    $(".offerPrice").html(courseOtherPrice);
                }

                checkOfferShow();

            }
            <?php
            if(count($courses) == 1) {
                // if there's only one course in the offer then have it already selected
                ?>
                addToOffer(<?= $courses[0] ?>);
                <?php
            }
            ?>
            function checkOfferShow() {

                var selectedCourses = $(".offerCount").html();

                if(selectedCourses == "0") {

                    $(".addOfferForm").css('display', 'none');

                } else {

                    $(".addOfferForm").css('display', 'block');

                }

            }
            checkOfferShow();
        </script>

        <?php include BASE_PATH . 'learn-confidence.php'; ?>

        <?php include BASE_PATH . 'newsletter.php'; ?>

        <?php include BASE_PATH . 'featured.php'; ?>

        <?php include BASE_PATH . 'success-stories.php'; ?>

    </main>
    <!-- Main Content End -->

<?php include BASE_PATH . 'footer.php';?>

<script>

    jQuery(document).ready(function ($) {

        let brand = 'NSA';

        let list = $('.title-single h5').text();

        if (!list) {
            list = 'Course Listing';
        }

        $('.add_to_offer_btn').on('click', function (e) {


            let $this = $(this);
            let course = $this.closest('.single_offer_course');


            let target = $(e.target);

            //console.log('target', target.hasClass('info-tab'));
            if(target.hasClass('offer_course_link')) {

                return;

            }

            let is_selected = course.find('.course_offer_inner').hasClass('courseSelected');


            //let course_id = course.attr("data-course-id");
            //let course_id = course.attr("data-course-oldid");
            let course_id = course.attr("data-oldproductid");

            if(!course_id || SITE_TYPE == 'us') {
                course_id = course.attr("data-course-id");
            }

            let course_title = course.find('.nsa_course_title').text();
            let categories = course.attr('data-course-cats');
            let content_ids = [course_id];

            //let price_html = course.find('.course_price').clone();
            //price_html.find('.wasPriceSmall').remove();
            //let course_price = price_html.text().replace("£", "").replace("$", "");
            //course_price = parseFloat(course_price).toFixed(2);

            let course_price = course.find('.course_original_price').text();
            course_price = parseFloat(course_price).toFixed(2);

            if(!course_price || isNaN(course_price)) {
                course_price = 0;
            }

            let contents = [{"id":course_id,"quantity":1,"item_price":course_price}];

            let course_type = course.attr('data-course_type');


            console.log('is_selected', is_selected);

            let event_id = 'add_to_cart.'+ CUR_ID_FRONT + '.' +content_ids.join('');


            if(is_selected) {

                gtag('event', 'add to offer', {
                    'event_label': course_title,
                    'event_category': 'offer',
                    'non_interaction': false
                });

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

            } else {

                let products = [];

                let course_object = {
                    'name': course_title,
                    'id': course_id,
                    'price': course_price,
                    'brand': brand,
                    'category': categories,
                    'variant': course_type,
                    'quantity': 1
                };

                products.push(course_object);

                dataLayer.push({
                    'event': 'removeFromCart',
                    'ecommerce': {
                        'remove': {
                            'products': products
                        }
                    }
                });

            }



        });


        $('.nsa-offer-courses').on('click', '.single_offer_course .offer_course_link', function (e) {

            let $this = $(this);
            let course = $this.closest('.single_offer_course');

            console.log('course', course);


            //let course_id = course.attr("data-course-id");
            //let course_id = course.attr("data-course-oldid");
            let course_id = course.attr("data-oldproductid");

            if(!course_id || SITE_TYPE == 'us') {
                course_id = course.attr("data-course-id");
            }

            let course_title = course.find('.nsa_course_title').text();
            let categories = course.attr('data-course-cats');
            let content_ids = [course_id];

            //let price_html = course.find('.course_price').clone();
            //price_html.find('.wasPriceSmall').remove();
            //let course_price = price_html.text().replace("£", "").replace("$", "");
            //course_price = parseFloat(course_price).toFixed(2);

            let course_price = course.find('.course_original_price').text();
            course_price = parseFloat(course_price).toFixed(2);

            if(!course_price || isNaN(course_price)) {
                course_price = 0;
            }

            let contents = [{"id":course_id,"quantity":1,"item_price":course_price}];

            let course_type = course.attr('data-course_type');
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
                            'category': categories,
                            'variant': course_type,
                            'position': position
                        }]
                    }
                }
            });



        });


    });


</script>
