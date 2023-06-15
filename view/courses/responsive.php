<?php
// reset affiliate discount - link often used by CS team
if($this->get["reset"] == "true") {

    unset($_SESSION["refCodeInternal"]);
    unset($_SESSION["affiliateDiscount"]);
    unset($_SESSION["affiliateDiscountType"]);
    unset($_SESSION["affiliateDiscountMax"]);
    unset($_SESSION["affiliateDiscountMin"]);
    unset($_SESSION["excludedCourses"]);

    header('Location: '.SITE_URL.'courses');
    exit;
}

/*
 * Search results and single category pages run off this same template.
 */
$css = array("course-category.css");
$currentCategory = $this->controller->currentCategory();

$pageTitle = "All Online Courses";

$isCategoryPage = false;

$subCategories = [];

$breadcrumb = [
    "Courses" => SITE_URL.'courses'
];

$metaDesc = '';

if($currentCategory->title != "") {
    $this->setControllers(array("courseCategory"));
    $pageTitle = @$currentCategory->meta_title ? $currentCategory->meta_title : $currentCategory->title;
    $subCategories = $this->courseCategory->getSubCategories($currentCategory->id);
    $isCategoryPage = true;
    $breadcrumb['Courses'] = SITE_URL.'courses';
    if($currentCategory->parentID == 0){
        $breadcrumb[$currentCategory->title] = '';
    }else{
        $pCategory = $this->controller->getCategoryById($currentCategory->parentID);
        $breadcrumb[$pCategory->title] = SITE_URL.'courses/'.$pCategory->slug;
        $breadcrumb[$currentCategory->title] = '';
    }

    $metaDesc = substr($currentCategory->description, 0, 155).'...';
}


include BASE_PATH . 'header.php';

$categories = ORM::for_table("courseCategories")->where_null('parentID')->where("showOnHome", "1")->order_by_asc("title")->find_many();
?>
<style>
    #refineCourses {
        position: sticky;
        position: -webkit-sticky;
        top: 72px;
    }
</style>

<!-- Main Content Start-->
<main role="main" class="regular">
    <div id="coursesSection">
        <form method="get" name="courses" id="getCoursesForm" >
            <!--course filters-->
            <section class="course-filters">
                <div class="container wider-container">

                    <?php
                    if($this->get["raf"] == "true") {
                        ?>
                        <div class="life-access-banner">
                            <h4 style="font-size:27px;">You were referred by a friend, just add your course(s) to your basket and your 75% discount will be applied!</h4>
                        </div>
                        <br />
                        <?php
                    }
                    ?>

                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-3">
                            <h2 class="section-title text-left">Courses</h2>
                        </div>
                        <div class="col-12 col-md-12 col-lg-6">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control text-left" placeholder="Search" aria-label="Search" aria-describedby="basic-addon2" name="search" value="<?= $_GET['search'] ?>">
                                <div class="input-group-append" style="position: absolute; right: 0; top: 0;">
                                    <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-3">
                            <select class="form-control orderSelection" id="orderSelection" name="order">
                                <option <?php if(isset($_GET['order']) && ($_GET['order'] == 'enrollmentCount-desc') ){?> selected <?php }?> value="enrollmentCount-desc">Popular Courses</option>
                                <option <?php if(isset($_GET['order']) && ($_GET['order'] == 'whenAdded-desc') ){?> selected <?php }?> value="whenAdded-desc">Release date (newest first)</option>
                                <option <?php if(isset($_GET['order']) && ($_GET['order'] == 'title-asc') ){?> selected <?php }?> value="title-asc">Name (A-Z)</option>
                                <option <?php if(isset($_GET['order']) && ($_GET['order'] == 'title-desc') ){?> selected <?php }?> value="title-desc">Name (Z-A)</option>
                                <option <?php if(isset($_GET['order']) && ($_GET['order'] == 'price-asc') ){?> selected <?php }?> value="price-asc">Price (low to high)</option>
                                <option <?php if(isset($_GET['order']) && ($_GET['order'] == 'price-desc') ){?> selected <?php }?> value="price-desc">Price (high to low)</option>
                                <option <?php if(isset($_GET['order']) && ($_GET['order'] == 'averageRating-desc') ){?> selected <?php }?> value="averageRating-desc">Rating (high to low)</option>
                                <option <?php if(isset($_GET['order']) && ($_GET['order'] == 'duration-asc') ){?> selected <?php }?> value="duration-asc">Duration (short to long)</option>
                                <option <?php if(isset($_GET['order']) && ($_GET['order'] == 'duration-desc') ){?> selected <?php }?> value="duration-desc">Duration (long to short)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <!--courses listing-->
            <section class="courses-listing">
                <div class="container wider-container">
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-3 left-filters">
                            <div id="accordion">
                                <div class="card">
                                    <div class="card-header" id="category">
                                        <h5 class="mb-0">
                                            <a class="filter-title" data-toggle="collapse" data-target="#categoryData" aria-expanded="true" aria-controls="categoryData">
                                                Category
                                            </a>
                                        </h5>
                                    </div>

                                    <div id="categoryData" class="collapse show" aria-labelledby="category" data-parent="#accordion">
                                        <div class="card-body">
                                            <ul class="custom-control">
                                                <li class="<?php echo ($currentCategory->id == "") ? 'active' : '';?>">
                                                    <a href="<?= SITE_URL.'courses';?>" id="">
                                                        All Courses
                                                    </a>
                                                </li>

                                                <?php
                                                foreach($categories as $category) {
                                                    $count = $this->controller->getTotalCoursesByCategoryID($category->id);
                                                    ?>
                                                    <li class="<?php echo (@$currentCategory &&  $category->id == $currentCategory->id) ? 'active' : '';?>">
                                                        <a href="<?= SITE_URL.'courses/'.$category->slug;?>" id="<?= $category->slug?>">
                                                            <?= $category->title. " (".$count.")" ?>
                                                        </a>
                                                    </li>
                                                    <!--                                                <div class="custom-control custom-checkbox">-->
                                                    <!--                                                    <input type="checkbox" name="categories[]" onchange="$('#pageno').val('0'); showLoader(); getCourses('hide');" class="catCheck custom-control-input" value="--><!--" id="--><!--" --><!---->
                                                    <!--                                                    <label class="custom-control-label" for="--><!--">--><!--</label>-->
                                                    <!--                                                </div>-->
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header" id="type">
                                        <h5 class="mb-0">
                                            <a class="filter-title collapsed" data-toggle="collapse" data-target="#typeData" aria-expanded="false" aria-controls="typeData">
                                                Type
                                            </a>
                                        </h5>
                                    </div>
                                    <div id="typeData" class="collapse" aria-labelledby="type" data-parent="#accordion">
                                        <div class="card-body">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input filterTypes" value="video" <?php if(@$_GET['types'] && (strpos($_GET['types'], 'video') !== false)){?> checked <?php }?> id="VideoLearning" @change="selectTypes()">
                                                <label class="custom-control-label" for="VideoLearning">Video Learning</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input filterTypes" value="audio" <?php if(@$_GET['types'] && (strpos($_GET['types'], 'audio') !== false)){?> checked <?php }?>  id="AudioLearning" @change="selectTypes()">
                                                <label class="custom-control-label" for="AudioLearning">Audio Learning</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input filterTypes" value="text" <?php if(@$_GET['types'] && (strpos($_GET['types'], 'text') !== false)){?> checked <?php }?> id="TextLearning" @change="selectTypes()">
                                                <label class="custom-control-label" for="TextLearning">Text Learning</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header" id="duration">
                                        <h5 class="mb-0">
                                            <a class="filter-title collapsed" data-toggle="collapse" data-target="#durationData" aria-expanded="false" aria-controls="durationData">
                                                Duration
                                            </a>
                                        </h5>
                                    </div>
                                    <div id="durationData" class="collapse" aria-labelledby="duration" data-parent="#accordion">
                                        <div class="card-body">
                                            <div class="custom-control custom-checkbox">
                                                <input type="radio" name="duration" value="" class="custom-control-input" id="anymins" <?php if(!isset($_GET['duration']) || empty($_GET['duration'])){?> checked <?php }?>  @change="selectDuration($event)">
                                                <label class="custom-control-label" for="anymins">Any</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="radio" name="duration" value="0-0.5" class="custom-control-input"  id="30mins" <?php if(isset($_GET['duration']) && $_GET['duration'] == '0-0.5'){?> checked <?php }?> @change="selectDuration($event)">
                                                <label class="custom-control-label" for="30mins">< 30 mins</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="radio" name="duration" value="0.5-10" class="custom-control-input" id="30minsTo10hrs" <?php if(isset($_GET['duration']) && $_GET['duration'] == '0.5-10'){?> checked <?php }?> @change="selectDuration($event)">
                                                <label class="custom-control-label" for="30minsTo10hrs">30 mins - 10 hours</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="radio" name="duration" value="10-9999" class="custom-control-input" id="10hours" <?php if(isset($_GET['duration']) && $_GET['duration'] == '10-9999'){?> checked <?php }?> @change="selectDuration($event)">
                                                <label class="custom-control-label" for="10hours">10 hours +</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-9 popularCourseboxes">
                            <?php
                            if(@$currentCategory->title){
                                ?>
                                <div class="row">
                                    <div class="col-12">
                                        <h1 class="section-title text-left mb-2"><?php if($currentCategory->title == "Bundle Courses") { ?>Bundles<?php } else { ?><?= $currentCategory->title ?><?php } ?> <?= $currentCategory->parentID == 0 ? 'Courses' : ''?></h1>
                                        <?php
                                        if(@$currentCategory->description || @$subCategories){
                                            ?>
                                            <div class="category-desc">
                                                <?php
                                                if(@$currentCategory->description){
                                                    echo "<div class=\"addReadMore showlesscontent\">". nl2br($currentCategory->description) ."</div>";
                                                    ?>

                                                    <script>
                                                        function AddReadMore() {
                                                            //This limit you can set after how much characters you want to show Read More.
                                                            var carLmt = 280;
                                                            // Text to show when text is collapsed
                                                            var readMoreTxt = " ... Read More";
                                                            // Text to show when text is expanded
                                                            var readLessTxt = " Read Less";


                                                            //Traverse all selectors with this class and manupulate HTML part to show Read More
                                                            $(".addReadMore").each(function() {
                                                                if ($(this).find(".firstSec").length)
                                                                    return;

                                                                var allstr = $(this).text();
                                                                if (allstr.length > carLmt) {
                                                                    var firstSet = allstr.substring(0, carLmt);
                                                                    var secdHalf = allstr.substring(carLmt, allstr.length);
                                                                    var strtoadd = firstSet + "<span class='SecSec'>" + secdHalf + "</span><span class='readMore'  title='Click to Show More'>" + readMoreTxt + "</span><span class='readLess' title='Click to Show Less'>" + readLessTxt + "</span>";
                                                                    $(this).html(strtoadd);
                                                                }

                                                            });
                                                            //Read More and Read Less Click Event binding
                                                            $(document).on("click", ".readMore,.readLess", function() {
                                                                $(this).closest(".addReadMore").toggleClass("showlesscontent showmorecontent");
                                                            });
                                                        }
                                                        $(function() {
                                                            //Calling function after Page Load
                                                            AddReadMore();
                                                        });
                                                    </script>
                                                    <?php
                                                }
                                                ?>
                                                <?php
                                                if(@$subCategories){
                                                    echo "<div class='catSublist'>";
                                                    foreach ($subCategories as $subCategory){
                                                        ?>
                                                        <a href="<?= SITE_URL.'courses/'.$subCategory->slug?>"><?= $subCategory->title?></a>
                                                        <?php
                                                    }
                                                    echo "</div>";
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                        ?>

                                    </div>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="row">
                                    <div class="col-12">
                                        <h1 class="section-title text-left mb-2">All Courses</h1>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                            if($this->showSubscriptionUpsell() == true && ($currentCategory->title != 'Qualifications')) {
                                ?>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="miniUpsell">
                                            <a href="<?= SITE_URL ?>subscription">
                                                Become an Unlimited Learning member & get access to our entire course library for only <?= $this->price($currency->prem12) ?> per year. <u>Find out more</u>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                            <div class="row nsa-category-courses">
                                <div v-if="loadingData" class="text-center col-12">
                                    <i class="fa fa-spin fa-spinner frontEndLoading"></i>
                                </div>
                                <div v-else-if="courses.length" v-for="(course, index) in courses" class="col-12 col-md-6 col-lg-4 single-course-wrapper" :data-course-type="course.type">
                                    <?php include ('includes/course/course-grid-vue.php')?>
                                </div>
                                <div v-else class="text-center col-12">
                                    <h5>We could not find any courses matching your search...</h5>
                                </div>
                            </div>
                            <div class="row mb-4" >
                                <div class="pagingHtml"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
            <input type="hidden" name="types" id="types" value="<?= $_GET['types'] ?? null ?>">
        </form>
    </div>
    <?php include BASE_PATH . 'learn-confidence.php'; ?>

    <?php include BASE_PATH . 'newsletter.php'; ?>

    <?php include BASE_PATH . 'featured.php'; ?>

    <?php include BASE_PATH . 'success-stories.php'; ?>

</main>
<!-- Main Content End -->
<!--    <script src="--><?//= SITE_URL ?><!--assets/vendor/vue3/dist/vue.global.prod.js"></script>-->
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script src="<?= SITE_URL ?>assets/vendor/axios/dist/axios.min.js"></script>
<script>
    var app = new Vue({
        el: '#coursesSection',
        data: {
            loadingData: false,
            loadingMore: false,
            loadMore: 0,
            courses: [],
            userSavedCourses: [],
            filters: {
                'categoryIDs': ['<?php echo @$currentCategory->id ? $currentCategory->id : '';?>'],
            },
            page: 1,
            offset: 0,
            limit: 15,
            order: 'enrollmentCount',
            orderBy: 'desc',
            documentWidth: 0,
            url: location.protocol + '//' + location.host + location.pathname
        },
        methods: {
            getCourses: function(loadMore) {
                that = this;
                that.loadingMore = false;
                that.loadingData = false;
                that.loadMore = 0;

                if(loadMore === true){
                    that.loadMore = 0;
                    that.loadingMore = true;
                }else {
                    that.loadingData = true;
                }
                const url = "<?= SITE_URL ?>ajax?c=course&a=getCoursesJson";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        filters: this.filters,
                        offset: (this.page * this.limit) - this.limit,
                        limit: this.limit,
                        order: this.order,
                        orderBy: this.orderBy,
                        currentPage: this.page,
                        currentUrl: this.url,
                    },
                    success: function(response){

                        response = JSON.parse(response);

                        if(loadMore === true){
                            that.loadingMore = false;
                            response.data.courses.forEach(function (course) {
                                that.courses.push(course);
                            });
                        }else{
                            that.loadingData = false;
                            that.courses = response.data.courses;
                        }
                        that.userSavedCourses = response.data.userSavedCourses;
                        that.loadMore = response.data.loadMore;
                        $(".pagingHtml").html(response.data.paginationHtml);
                        console.log(response);
                    },
                    error: function(xhr, status, error){
                        console.error(xhr);
                    }
                });


            },
            loadMoreCourses: function() {
                that = this;
                that.offset = this.offset + this.limit;
                this.getCourses(true);
            },
            sortCourses: function(event) {
                that = this;
                var order = event.target.value.split("-");
                that.order = order[0];
                that.orderBy = order[1];
                that.getCourses(false);
            },
            searchCourse: function(event) {
                that = this;
                if(event.target.value.length >= 3){
                    that.filters['searchText'] = event.target.value;
                    that.getCourses(false);
                }else if(event.target.value.length === 0) {
                    that.filters['searchText'] = '';
                    that.getCourses(false);
                }
            },
            selectDuration: function(event) {
                that = this;
                that.filters['duration'] = event.target.value;
                $("#getCoursesForm").submit();
                //that.getCourses(false);
            },
            selectTypes: function () {
                that = this;
                that.filters['types'] = [];
                $(".filterTypes").each(function(){
                    if($(this).prop('checked')){
                        that.filters['types'].push( $(this).val() );
                    }
                });
                var types = that.filters['types'].toString();
                $("#types").val(types);
                $("#getCoursesForm").submit();
            },
            addToCart: function(courseID) {
                $.post("<?= SITE_URL ?>ajax?c=cart&a=add-course",
                    {
                        courseID: courseID
                    },
                    function(data, status){
                        // refreshCartTop();
                        // openNav();
                        $("#header-basket").load(SITE_URL+"ajax?c=cart&a=render-cart-header");
                        $("#ajaxItems").load(SITE_URL+"ajax?c=cart&a=render-cart-side");
                        $("#basket").modal("toggle");
                    });
            },
            saveCourse: function(id) {

                $.post(SITE_URL+"ajax?c=course&a=user-save-course",
                    {
                        id: id
                    },
                    function(data, status){

                        $("#returnStatus").append(data);

                    });
            },
            mobileLayout: function(){
                that = this;
                that.documentWidth = $( document ).width();
                if(that.documentWidth <= 580){
                    $("#categoryData").removeClass('show');
                    $("#category.card-header a.filter-title").addClass('collapsed');
                }
            }
        },
        beforeMount: function() {
            that = this;
            <?php
            if(@$_GET['page']){
            ?>
            that.page = parseInt(<?= $_GET['page']; ?>);
            <?php
            }
            if(@$_GET['order']){
            ?>
            var orderFull = '<?= $_GET['order']; ?>';
            var order = orderFull.split("-");
            that.order = order[0];
            that.orderBy = order[1];
            <?php
            }
            if(@$_GET['search']){
            ?>
            that.filters['searchText'] = '<?= $_GET['search'] ?>';
            <?php
            }
            if(@$_GET['duration']){
            ?>
            that.filters['duration'] = '<?= $_GET['duration'] ?>';
            <?php
            }
            if(@$_GET['types']){
            ?>
            that.filters['types'] = '<?= $_GET['types'] ?>';
            <?php
            }
            ?>
        },
        mounted: function(){
            this.getCourses(false);
            this.mobileLayout();
        },
    })
</script>


<?php include BASE_PATH . 'footer.php';?>

<script>

    let brand = 'NSA';

    let list = 'Course Listing Page';

    $('.popularCourseboxes').on('click', '.nsa_add_to_cart_btn', function (e) {

        console.log('add to cart clicked');

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

        let course_type = course.attr('data-course_type');

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


</script>

<?php if(REQUEST == "courses" || $isCategoryPage): ?>

    <script>

        jQuery(document).ready(function ($) {

            let brand = 'NSA';

            let list = $('.title-single h5').text();

            $('#orderSelection').change(function() {
                this.form.submit();
            });

            if(!list) {
                list = 'Course Listing';
            }

            let category_name = $('.popularCourseboxes .section-title').text();


            //$('.popularCourseboxes').on("DOMSubtreeModified", function() {
            $('.popularCourseboxes').on("DOMNodeInserted", function() {

                //console.log('DOMSubtreeModified');
                //console.log('DOMNodeInserted');

                $('.popularCourseboxes .nsa-category-courses .single-course-wrapper').each(function(index, obj){

                    $(obj).attr('data-position', index+1);

                });

            });


            $('body').on('course_list_visible', function (e) {

                //console.log('inside course_list_visible');


                $('.popularCourseboxes .nsa-category-courses .single-course-wrapper').each(function(index, obj){

                    $(obj).attr('data-position', index+1);

                });

                let impressions = [];
                let cat_content_ids = [];

                //$('.response-results.courses-listing1 .wrap_post_course').each(function(index, obj){
                $('.popularCourseboxes .nsa-category-courses .single-course-wrapper').withinviewport({sides:'top bottom', top: -300, bottom: -300}).each(function(index, obj){


                    //console.log($(obj));

                    let add_to_cart_btn = $(obj).find('.nsa_add_to_cart_btn');

                    let course_title = $(obj).find('.nsa_course_title').text();
                    let cat_names = add_to_cart_btn.attr('data-course-cats');
                    let course_type = add_to_cart_btn.attr('data-course_type');
                    //let course_id = add_to_cart_btn.attr('data-course_id');
                    let course_id = add_to_cart_btn.attr('data-oldproductid');

                    if(!course_id || SITE_TYPE == 'us') {
                        course_id = add_to_cart_btn.attr('data-course-id');
                    }

                    /*
                    //console.log( 'course_price', $(obj).find('.course_price').text() );

                    let course_price = $(obj).find('.course_price').text().replace("£", "").replace("$", "");
                    course_price = parseFloat(course_price).toFixed(2);
                    */

                    let price_html = $(obj).find('.course_price').clone();
                    price_html.find('.wasPriceSmall').remove();
                    let course_price = price_html.text().replace("£", "").replace("$", "");

                    course_price = parseFloat(course_price).toFixed(2);


                    //let position = index + 1;
                    let position = $(obj).attr('data-position');

                    cat_content_ids.push(course_id);

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

                //console.log('impressions', impressions);

                //console.log(window.sent_impressions);

                if(impressions.length > 0) {
                    //if(window.sent_impressions.length == 0) {

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


                        fbq(
                            'track',
                            'ViewCategory',
                            {
                                content_type: "product",
                                domain: DOMAIN_NAME,
                                content_category: category_name,
                                event_hour: event_hour,
                                //user_roles: "student",
                                content_name: category_name,
                                content_ids: cat_content_ids,
                                event_day: event_day,
                                event_month: event_month

                            }
                        );


                        window.sent_impressions = window.sent_impressions.concat(impressions_to_sent).unique();
                        //window.sent_impressions = window.sent_impressions.concat(impressions_to_sent);

                        window.last_sent_impressions = impressions_to_sent;
                        //    createCookie('productImpression', string_impressions, 0.001);

                    }

                    //console.log('productImpression event triggered on '+list);

                } else {

                    //console.log('productImpression event already triggered');

                }



            });


            $('.popularCourseboxes .nsa-category-courses').on('click', '.single-course-wrapper .nsa_course_more_info', function (e) {

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

                //console.log('productClick event triggered on '+list);


            });





        });

    </script>

<?php endif; ?>
