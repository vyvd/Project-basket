<?php

//use facebookBusinessSDKController;

$css = array("single-course.css");
$course = $this->controller->getSingleCourse();
$isQualificationCourse = false;

// if course is hidden in admin or not available in currency currency then 404 it
if($course->hidden == "1" || $course->id == "") { // $this->courseAvailable($course) == false
    $this->force404();
    exit;
}

$currency = $this->currentCurrency();

// if course is only available in UK, then make sure we serve via /en slug  // no longer needed due to serving multi-domains
/*if($this->checkCourseAvailability($course) == true && strpos(REQUEST, 'en/') === false && $currency->code == "GBP") {
    header("HTTP/1.1 301 Moved Permanently");
    header('Location: '.SITE_URL.'en/course/'.$course->slug);
    exit;
}*/

$pageTitle = $course->title;
if($course->seoTitle != "") {
    $pageTitle = $course->seoTitle;
}

$categories = $this->controller->getCourseCategories($course->id);
$allCourseCategories = $this->controller->getCategoriesByIds(array_keys($categories));
$breadcrumb = array(
    "Courses" => SITE_URL.'courses',
);
foreach ($allCourseCategories as $cat){
    if($cat->parentID == 0){
        $breadcrumb[$cat->title] = SITE_URL.'courses/'.$cat->slug;
        break;
    }
}
$breadcrumb[$course->title] = "";

$this->setControllers(array("course", "testimonial", "facebookBusinessSDK"));

$courseType = $this->controller->getCourseType($course->id);

// Single Course Tabs Contents
$courseContents['description'] = 'Course Description';
if($course->is_cudoo != "1" && !in_array('Qualifications', $categories)) {
    $courseContents['modules'] = 'Modules';
    $courseContents['certificate'] = 'Example certificate';
}
if(!in_array('Qualifications', $categories)){
    $courseContents['faq'] = 'FAQ’s';

    $courseContents['reviews'] = 'Reviews';
}else{
    $isQualificationCourse = true;
}

$reviews = $this->controller->getReviews($course->id);

$course->price = $this->getCoursePrice($course);

$coursePriceOriginal = $course->price;

$originalPrice = $this->price($course->price);
$course_final_price = $course->price;
$wasPrice = $this->price($course->price);

// affiliate pricing
$excludedCourses = explode(",", $_SESSION["excludedCourses"]);
if($_SESSION["affiliateDiscount"] != "" && !in_array($course->id, $excludedCourses)) {

    $original = $course->price;
    $discounted = $course->price;

    if($_SESSION["affiliateDiscountType"] == "fixed") {
        $discounted = $discounted-$_SESSION["affiliateDiscount"];
    } else {
        $discounted = $discounted * ((100-$_SESSION["affiliateDiscount"]) / 100);
    }

    if($_SESSION["affiliateDiscountMax"] != "" && $_SESSION["affiliateDiscountMin"] != "") {


        if($course->price <= $_SESSION["affiliateDiscountMax"] && $course->price >= $_SESSION["affiliateDiscountMin"]) {
            $originalPrice = $this->price($discounted);
            $course_final_price = $discounted;
        }

    } else if($_SESSION["affiliateDiscountMax"] != "") {

        if($course->price <= $_SESSION["affiliateDiscountMax"]) {
            $originalPrice = $this->price($discounted);
            $course_final_price = $discounted;
        }

    } else if($_SESSION["affiliateDiscountMin"] != "") {

        if($course->price >= $_SESSION["affiliateDiscountMin"]) {
            $originalPrice = $this->price($discounted);
            $course_final_price = $discounted;
        }

    } else {
        $originalPrice = $this->price($discounted);
        $course_final_price = $discounted;
    }



}

$metaDesc = 'Study our online '.$course->title.' course anywhere, anytime, with lifetime access to all '.ORM::for_table("courseModules")->where("courseID", $course->id)->count().' modules.';

if($course->seoDescription != "") {
    $metaDesc = $course->seoDescription;
}

if($course->seoKeywords != "") {
    $metaTags = $course->seoKeywords;
}

// generate hreflang tags
$headerHTML = $this->controller->generateHrefLangTags($course);


//include BASE_PATH . 'cache.top.php'; // we want to cache this page
include BASE_PATH . 'header.php';

?>

<div id="courseHeader">
    <div class="container wider-container">
        <div class="row">
            <div class="col-6 title">
                <?= $course->title ?>
            </div>
            <div class="col-4 price">
                <?= $originalPrice ?>
            </div>
            <div class="col-2 button">
                <a href="javascript:;" class="btn btn-primary btn-lg extra-radius start-course-button nsa_add_to_cart_btn" data-course-id="<?= $course->id ?>" >BUY NOW</a>
            </div>
        </div>
    </div>
</div>

<script>
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();

        if (scroll >= 580) {
            $("#courseHeader").css("display", "block");
        } else {
            $("#courseHeader").css("display", "none");
        }
    });
</script>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--course title-->
        <section class="course-title">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="section-title text-left"><?= $course->title ?></h1>
                    </div>
                </div>
            </div>
        </section>

        <!--courses listing-->
        <section class="courses-listing">
            <div class="container wider-container">
                <div class="row">
                    <!--Side Bar-->
                    <div class="col-12 col-md-12 col-lg-3 left-sidebar">
                        <div class="sideboxes d-none d-md-block">
                            <?php include('includes/sideboxes/course_details.php');?>
                        </div>

                        <?php
                            if(in_array('Qualifications', $categories) && (@$course->courseProviderID)){
                        ?>
                            <div class="sideboxes d-none d-md-block text-center" style="padding:15px;">
                                <?php include('includes/sideboxes/accredited.php');?>
                            </div>
                        <?php }?>

                        <div class="sideboxes d-none d-md-block" style="padding:15px;">
                            <?php include('includes/sideboxes/trustpilot.php');?>
                        </div>

                        <?php
                        if($currency->code == "GBP") {
                            ?>
                            <div class="sideboxes xo-box text-center d-none d-md-block">
                                <?php include('includes/sideboxes/xo-box.php');?>
                            </div>
                            <?php
                        }
                        ?>

                        <div class="sideboxes youtube d-none d-md-block">
                            <?php include('includes/sideboxes/youtube.php');?>
                        </div>
                    </div>

                    <div class="col-12 col-md-12 col-lg-9 single-course">
                        <div class="row align-items-center">
                            <?php
                            if($this->showSubscriptionUpsell() == true && (!in_array('Qualifications', $categories))) {

                                $courseCount = 700;

                                if($currency->code != "GBP") {
                                    $courseCount = 300;
                                }
                                ?>
                                    <div class="col-12" style="padding:0;">
                                    <?php
                                if( $course->cudooCourseIDs == "") {
                                    ?>
                                        <div class="miniUpsell">

                                            <a href="<?= SITE_URL ?>subscription">
                                                Get this course and <?= $courseCount ?>+ others for only <?= $this->price($currency->prem12) ?> per year. <u>Find out more</u>
                                            </a>
                                        </div>
                                        <?php
                                }
                                ?>
                                    </div>

                                <?php
                            }
                            ?>
                            <!--Course Banner with buy options-->
                            <div class="single-course-banner">

                                <?php
                                if($isQualificationCourse === false && $course->isNCFE == "0" && $course->cudooCourseIDs == "") {
                                    ?>
                                    <div class="includedPremium">
                                        Included as part of the unlimited membership
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <?php
                                }
                                ?>

                                <a class="saveHeart saveCourse<?= $course->id ?> <?php if($this->checkCourseSaved($course->id) == true) { ?>active<?php } ?>"
                                   href="javascript:;" role="button" onclick="saveCourse(<?= $course->id ?>);">
                                    <i class="far fa-heart"></i>
                                </a>

                                <img src="<?= $this->course->getCourseImage($course->id) ?>" alt="<?= $course->title ?>" class="banner" />
                                <div class="buy-options">
                                    <div class="left-logos">
                                        <?php
                                        if($course->cpd_code != "" && ($isQualificationCourse == false)) {
                                            ?>
                                            <img src="<?= SITE_URL ?>assets/images/cpd.png" alt="cpd"/>
                                            <?php
                                        }
                                        if($course->is_cma == "1" && ($isQualificationCourse == false)) {
                                            ?>
                                            <img src="<?= SITE_URL ?>assets/images/cma.jpg" style="border-radius:5px;" alt="cpd"/>
                                            <?php
                                        }
                                        if($course->is_ctaa == "1" && ($isQualificationCourse == false)) {
                                            ?>
                                            <img src="<?= SITE_URL ?>assets/images/ctaa.png" style="border-radius:5px;" alt="ctaa"/>
                                            <?php
                                        }
                                        if($course->is_sheilds == "1" && ($isQualificationCourse == false)) {
                                            ?>
                                            <img src="<?= SITE_URL ?>assets/images/shields.png" alt="sheilds" />
                                            <?php
                                        }
                                        if($course->is_rospa == "1" && ($isQualificationCourse == false)) {
                                            ?>
                                            <img src="<?= SITE_URL ?>assets/images/rospa.png" alt="rospa" />
                                            <?php
                                        }

                                        ?>
                                    </div>
                                    <div class="price buy-course">
                                        <?php
                                        if($originalPrice != $wasPrice) {
                                            ?>
                                            <span class="courseWasPrice"><small>RRP</small> <s><?= $wasPrice ?></s></span>
                                            <?php
                                        }

                                        ?>
                                        <span class="courseOrigiinalPrice"><?= $originalPrice ?></span>
                                        <a href="javascript:;" class="btn btn-primary nsa_add_to_cart_btn" data-course-id="<?= $course->id ?>" >BUY NOW</a>
                                    </div>
                                    <?php
                                    if($this->get["add"] == "true") {
                                        ?>
                                        <script>
                                            $( document ).ready(function() {
                                                $(".buy-course a.btn").trigger("click");
                                            });
                                        </script>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <?php
                            if($course->childCourses != "") {

                                ?>
                                <div class="multiCourses">
                                    <h4>Includes the following courses:</h4>
                                    <?php
                                    foreach(json_decode($course->childCourses) as $child) {
                                        $rand = rand(99,99999);
                                        $childCourse = ORM::for_table("courses")->find_one($child);
                                        ?>
                                        <a href="<?= SITE_URL ?>course/<?= $childCourse->slug ?>" class="item">
                                            <?= $childCourse->title ?>
                                        </a>
                                        <?php

                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            if(in_array('Qualifications', $categories)){
                                include ('includes/qualification-section.php');
                            }
                            ?>
                            <!--Course Content -->
                            <div class="single-course-content">
                                <div class="d-none d-md-block">
                                    <ul class="nav nav-tabs ">
                                        <?php
                                        $i = 1;
                                        foreach ($courseContents as $key => $title){
                                            ?>
                                            <li class="nav-item">
                                                <a class="nav-link <?php if($i == 1){?> active <?php }?>" data-toggle="tab" href="#c<?= $key?>"><?= $title ?></a>
                                            </li>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content mb-3 ">

                                        <style>
                                            .single-course-content .tab-content li {
                                                margin-bottom: 18px;
                                                font-size: 1.1rem;
                                                list-style-position: inside;
                                                line-height: 1.6rem;
                                                text-indent: -1.1rem;
                                            }
                                        </style>

                                        <?php
                                        $i = 1;
                                        foreach ($courseContents as $key => $title){
                                            ?>
                                            <div id="c<?= $key?>" class="container tab-pane <?php if($i == 1) { echo 'active'; } else { echo 'fade';} ?>">
                                                <?php include ('includes/'.$key.'.php');?>
                                            </div>
                                            <?php
                                            $i++;
                                        }
                                        ?>

                                    </div>
                                </div>
                                <div class="d-md-none">
                                    <div class="sideboxes">
                                        <?php include('includes/sideboxes/course_details.php');?>
                                    </div>
                                    <?php if(in_array('Qualifications', $categories)){?>
                                        <div class="sideboxes text-center" style="padding:15px;">
                                            <?php include('includes/sideboxes/accredited.php');?>
                                        </div>
                                    <?php }?>
                                    <div id="accordionContents" class="mb-3 left-filters col-lg-3">
                                        <?php
                                        $i = 1;
                                        foreach ($courseContents as $key => $title){
                                        ?>
                                            <div class="card">
                                                <div class="card-header" id="heading<?= $key ?>">
                                                    <h5 class="mb-0">
                                                        <a class="filter-title collapsed" data-toggle="collapse" data-target="#collapse<?= $key ?>" aria-expanded="true" aria-controls="collapse<?= $key ?>">
                                                            <?= $title ?>
                                                        </a>
                                                    </h5>
                                                </div>

                                                <div id="collapse<?= $key ?>" class="collapse" aria-labelledby="heading<?= $key ?>" data-parent="#accordionContents">
                                                    <div class="card-body">
                                                        <?php include ('includes/'.$key.'.php');?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                            $i++;
                                        }
                                        ?>
                                    </div>

                                    <div class="sideboxes" style="padding:15px;">
                                        <?php include('includes/sideboxes/trustpilot.php');?>
                                    </div>


                                    <?php
                                    if($currency->code == "GBP") {
                                    ?>
                                    <div class="sideboxes xo-box text-center">
                                        <?php include('includes/sideboxes/xo-box.php');?>
                                    </div>
                                        <?php
                                    }
                                    ?>

                                    <div class="sideboxes youtube">
                                        <?php include('includes/sideboxes/youtube.php');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Related Course Slider-->
                        <div class="related-courses p-3 p-md-0">
                            <div class="">
                                <h3 class="section-title">Related Courses</h3>
                                <div id="relatedCoursesSlider" class="relatedCoursesSlider popularCourseboxes owl-carousel">

                                        <?php
                                        $related = $this->controller->relatedCourses(array_keys($categories), $course->id, 0);
                                        $cIds = [];
                                        foreach($related as $rel) {

                                            if($currency->code == "GBP") {
                                                $relCourse = ORM::for_table("courses")->where("usImport", "0")->find_one($rel->course_id);
                                            } else {
                                                $relCourse = ORM::for_table("courses")->where("usImport", "1")->find_one($rel->course_id);
                                            }

                                            if(in_array($relCourse->id, $cIds)){
                                                continue;
                                            }
                                            $cIds[] = $relCourse->id;
                                            $course_cats = '';
                                            //$course_cats = $this->getCourseCategories($relCourse->id);

                                            $enrolled = $relCourse->enrollmentCount;

                                            if($relCourse->id != "") {
                                                $relCourse->price = $this->getCoursePrice($relCourse);
                                                ?>
                                                <div class="item">
                                                    <div class="category-box single_course">
                                                        <div class="img" style="background-image:url('<?= $this->course->getCourseImage($relCourse->id, "large") ?>');"></div>

                                                        <div class="Popular-title-top"><i class="far fa-user"></i> <?= $enrolled ?> students enrolled</div>
                                                        <div class="Popular-title-bottom"><span class="nsa_course_title"><?= $relCourse->title ?></span>
                                                            <h3 class="course_price"><?php
                                                                // affiliate pricing
                                                                $excludedCourses = explode(",", $_SESSION["excludedCourses"]);
                                                                if($_SESSION["affiliateDiscount"] != "" && !in_array($relCourse->id, $excludedCourses)) {

                                                                    $original = $relCourse->price;
                                                                    $discounted = $relCourse->price;
                                                                    $changed = false;

                                                                    if($_SESSION["affiliateDiscountType"] == "fixed") {
                                                                        $discounted = $discounted-$_SESSION["affiliateDiscount"];
                                                                    } else {
                                                                        $discounted = $discounted * ((100-$_SESSION["affiliateDiscount"]) / 100);
                                                                    }

                                                                    if($_SESSION["affiliateDiscountMax"] != "" && $_SESSION["affiliateDiscountMin"] != "") {


                                                                        if($relCourse->price <= $_SESSION["affiliateDiscountMax"] && $relCourse->price >= $_SESSION["affiliateDiscountMin"]) {
                                                                            $originalPrice = $this->price($discounted);
                                                                            $relCourse->price = $discounted;
                                                                        }

                                                                    } else if($_SESSION["affiliateDiscountMax"] != "") {

                                                                        if($relCourse->price < $_SESSION["affiliateDiscountMax"]) {
                                                                            $relCourse->price = $discounted;
                                                                            $changed = true;
                                                                        }

                                                                    } else if($_SESSION["affiliateDiscountMin"] != "") {

                                                                        if($relCourse->price > $_SESSION["affiliateDiscountMin"]) {
                                                                            $relCourse->price = $discounted;
                                                                            $changed = true;
                                                                        }

                                                                    } else {
                                                                        $relCourse->price = $discounted;
                                                                        $changed = true;
                                                                    }


                                                                    if($changed == true) {
                                                                        $relCourse->price = '<small class="wasPriceSmall">RRP <s>'.$this->price($original).'</s></small>'.$this->price($relCourse->price);
                                                                    } else {
                                                                        $relCourse->price = $this->price($relCourse->price);
                                                                    }

                                                                } else {
                                                                    $relCourse->price = $this->price($relCourse->price);
                                                                }
                                                                echo $relCourse->price;
                                                                ?></h3>
                                                        </div>
                                                        <div class="popular-box-overlay">
                                                            <p><strong><?= $relCourse->title ?></strong></p>
                                                            <div class="popular-overlay-btn"><button type="button" class="btn btn-outline-primary btn-lg extra-radius"><?= ORM::for_table("courseModules")->where("courseID", $relCourse->id)->count() ?> Modules</button></div>
                                                            <!--<div class="popular-overlay-btn"><button type="button" class="btn btn-outline-primary btn-lg extra-radius">0% Finance</button></div>-->
                                                            <h3 class="course_price"><?= $relCourse->price ?></h3>
                                                            <div class="popular-overlay-btn-btm">
                                                                <a class="btn btn-outline-primary btn-lg extra-radius nsa_course_more_info" href="<?= SITE_URL ?>course/<?= $relCourse->slug ?>" role="button">More Info</a>
                                                                <a class="btn btn-outline-primary btn-lg extra-radius start-course-button nsa_add_to_cart_btn_rel" data-course-id="<?= $relCourse->id ?>" data-course-oldid="<?= $relCourse->oldID ?>" data-oldproductid="<?= $relCourse->productID ?>" data-course-cats="<?= implode(', ', $course_cats) ?>" data-course_type="" href="javascript:;" role="button">Add to Cart</a>
                                                                <a class="saveHeart saveCourse<?= $relCourse->id ?> <?php if($this->checkCourseSaved($relCourse->id) == true) { ?>active<?php } ?>"
                                                                   href="javascript:;" role="button" onclick="saveCourse(<?= $relCourse->id ?>);">
                                                                    <i class="far fa-heart"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }

                                        }
                                        ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php include BASE_PATH . 'learn-confidence.php'; ?>

        <?php include BASE_PATH . 'newsletter.php'; ?>

        <?php include BASE_PATH . 'featured.php'; ?>

        <?php include BASE_PATH . 'success-stories.php'; ?>

    </main>

    <script>
        $(document).ready(function (){
            $('.relatedCoursesSlider.owl-carousel').owlCarousel({
                loop:true,
                autoplay: true,
                margin:10,
                nav:true,
                responsive:{
                    0:{
                        items:1
                    },
                    600:{
                        items:2
                    },
                    1000:{
                        items:3
                    }
                }
            })
        });

    $(window).on('load', function() {

         // klaviyo tracking
         if(typeof  _learnq === 'undefined' || _learnq === null) {
            let _learnq = [];
        }
        
        var item = {
            "ProductName": '<?= $course->title ?>',
            "ProductID": '<?= $course->id ?>',
            "SKU": '<?= $course->id ?>',
            "Categories": '<?= implode(', ', $categories) ?>',
            "ImageURL": '<?= $this->course->getCourseImage($course->id) ?>',
            "URL": window.location.href,
            "Brand": 'New Skills Academy',
            "Price": <?= $course_final_price ?>,
            "CompareAtPrice": <?= $coursePriceOriginal ?>
        };
        
        // viewed item
        _learnq.push(["track", "Viewed Product", item]);

        // recently viewed items
        _learnq.push(["trackViewedItem", {
            "Title": '<?= $course->title ?>',
            "ItemId": '<?= $course->id ?>',
            "Categories": '<?= implode(', ', $categories) ?>',
            "ImageUrl": '<?= $this->course->getCourseImage($course->id) ?>',
            "Url": window.location.href,
            "Metadata": {
            "Brand": 'New Skills Academy',
            "Price": <?= $course_final_price ?>,
            "CompareAtPrice": <?= $coursePriceOriginal ?>
            }
        }]);
    });

    </script>

<?php
// reach tracking code
$reachCodes = array("370", "369", "368");
if(in_array($_SESSION["refCodeInternal"], $reachCodes)) {
    ?>
    <!-- Conversion Pixel - Conversion - New Skills Academy - Course Info Click - DO NOT MODIFY -->
    <script src="https://secure.adnxs.com/px?id=1490330&seg=26796647&t=1" type="text/javascript"></script>
    <!-- End of Conversion Pixel -->
    <?php
}
?>
<?php include BASE_PATH . 'footer.php';?>

<?php

$course_id = $course->id;
$oldProductID = $course->productID;

$item_id = empty($oldProductID) ? $course_id : $oldProductID;

if(SITE_TYPE == 'us') {
    $item_id = $course_id;
}


$contents = [];
$content_ids = [];

$content = [
    'content_id' => $item_id,
    'content_title' => $course->title,
    'categories' => implode(', ', $categories),
    'quantity' => 1,
    'price' => $course_final_price
];

$content_ids[] = $item_id;
$contents[] = $content;

//var_dump('contents', $contents);

$event_id = 'view_content.'.CUR_ID_FRONT.'.'.implode('', $content_ids);

$viewContentEvent = facebookBusinessSDKController::createViewContentEvent($contents, $content_ids);

//var_dump($viewContentEvent);

facebookBusinessSDKController::executeEvents(array($viewContentEvent));


?>

<script>


    let brand = 'NSA';

    let list = 'Product Detail Page';

    let course_title = "<?php echo $course->title; ?>";
    let categories = "<?php echo implode(', ', $categories); ?>";
    let course_id = "<?php echo $item_id; ?>";
    let content_ids = [course_id];


    let course_price = $('.courseOrigiinalPrice').text().replace("£", "").replace("$", "");


    let contents = [{"id":course_id,"quantity":1,"item_price":course_price}];
    let course_type = '<?= $courseType ?>';

    let event_id = 'view_content.'+CUR_ID_FRONT+'.'+content_ids.join('');

    fbq(
        'track',
        'ViewContent',
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
            event_month: event_month,
            is_category_page: "no"
        }
    );


    ttq.track('ViewContent',{
        content_type: "product",
        content_id: course_id,
        content_name: course_title,
        quantity: 1,
        price: course_price,
        value: course_price,
        currency: CURRENCY,
    });

    snaptr('track', 'VIEW_CONTENT', {
        'currency': "GBP",
        'price': course_price,
        'item_category': categories,
        'item_ids': course_id,
        'number_items': 1
    });


    dataLayer.push({
        'event': 'productDetailView',
        'ecommerce': {
            'detail': {
                'products': [{
                    'name': course_title,
                    'id': course_id,
                    'price': course_price,
                    'brand': brand,
                    'category': categories,
                    'variant': course_type,
                }]
            }
        }
    });

    //console.log('productDetailView event triggered on Product Detail Page');


    let add_to_cart_btn = $('.nsa_add_to_cart_btn');

    add_to_cart_btn.on('click', function (e) {

        let course_title = "<?php echo $course->title; ?>";
        let categories = "<?php echo implode(', ', $categories); ?>";
        let course_id = "<?php echo $item_id; ?>";
        let content_ids = [course_id];


        let course_price = $('.courseOrigiinalPrice').text().replace("£", "").replace("$", "");

        let contents = [{"id":course_id,"quantity":1,"item_price":course_price}];
        let course_type = '<?= $courseType ?>';

        let course_url = window.location.href;

        let course_image = $('.banner').attr('src');

        let event_id = 'add_to_cart.'+ CUR_ID_FRONT + '.' +content_ids.join('');

        let basket = $('#basket');

        let basket_items = basket.find('.cart-items');

        let items = [];
        let item_names = [];
        let item = {};

        basket_items.each(function(index, element) {
            let price = element.find('.course_price').clone();
            price.find('.wasPriceSmall').remove();
            let item_price = price.text().replace("£", "").replace("$", "");

            let item_name = element.find('.nsa_course_title').text();

            item = {
                'ProductID': element.data('course-id'),
                'SKU': element.data('course-id'),
                'ProductName': item_name,
                'Quantity': 1,
                'ItemPrice': item_price,
                'RowTotal': item_price,
                'ProductURL': '', // url isn't currently available in the basket modal
                'ImageURL': element.find('.product-img img').attr('src'),
                'ProductCategories': [element.data('course-cats')],
            }

            array_push(items, item);
            array_push(item_names, item_name);
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

        if(typeof  _learnq === 'undefined' || _learnq === null) {
            let _learnq = [];
        }
        _learnq.push(["track", "Added to Cart", {
                "$value": basket.find('.totals').data('total'),
                "AddedItemProductName": course_title,
                "AddedItemProductID": course_id,
                "AddedItemSKU": course_id,
                "AddedItemCategories":[categories],
                "AddedItemImageURL": course_image,
                "AddedItemURL": course_url,
                "AddedItemPrice": course_price,
                "AddedItemQuantity": 1 ,
                "ItemNames": item_names,
                "CheckoutURL": window.location.protocol + '//' + window.location.host + '/checkout',
                "Items": items
        }]);

    });


    $('.popularCourseboxes').on('click', '.nsa_add_to_cart_btn_rel', function (e) {

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

        let course_type = '<?= $courseType ?>';

        let course_url = course.find('.nsa_course_more_info').attr('href');;

        let course_image = course.find('.img').css('background-image');

        let event_id = 'add_to_cart.'+ CUR_ID_FRONT + '.' +content_ids.join('');

        let basket = $('#basket');

        let basket_items = basket.find('.cart-items');

        let items = [];
        let item_names = [];
        let item = {};

        basket_items.each(function(index, element) {
            let price = element.find('.course_price').clone();
            price.find('.wasPriceSmall').remove();
            let item_price = price.text().replace("£", "").replace("$", "");

            let item_name = element.find('.nsa_course_title').text();

            item = {
                'ProductID': element.data('course-id'),
                'SKU': element.data('course-id'),
                'ProductName': item_name,
                'Quantity': 1,
                'ItemPrice': item_price,
                'RowTotal': item_price,
                'ProductURL': '', // url isn't currently available in the basket modal
                'ImageURL': element.find('.product-img img').attr('src'),
                'ProductCategories': [element.data('course-cats')],
            }

            array_push(items, item);
            array_push(item_names, item_name);
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

        if(typeof  _learnq === 'undefined' || _learnq === null) {
            let _learnq = [];
        }
        _learnq.push(["track", "Added to Cart", {
                "$value": basket.find('.totals').data('total'),
                "AddedItemProductName": course_title,
                "AddedItemProductID": course_id,
                "AddedItemSKU": course_id,
                "AddedItemCategories":[categories],
                "AddedItemImageURL": course_image,
                "AddedItemURL": course_url,
                "AddedItemPrice": course_price,
                "AddedItemQuantity": 1 ,
                "ItemNames": item_names,
                "CheckoutURL": window.location.protocol + '//' + window.location.host + '/checkout',
                "Items": items
            }]);


        //console.log('addToCart event triggered on Product Detail Page');


    });





</script>
<?php
//include BASE_PATH . 'cache.bottom.php'; // we want to cache this page