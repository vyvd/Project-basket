<?php
$css = array("staff-training.css");
$pageTitle = "Redeem A Voucher";

$breadcrumb = array(
    "Redeem A Voucher" => ""
);

include BASE_PATH . 'header.php';
?>
<style>
    .search-box .search-field {
        float: left;
        width: calc(100% - 100px);
        border: 1px solid #f0eded;
        color: #1A1A1A;
        font-size: 22px;
        padding: 18px 0 18px 40px;
        border-top-left-radius:40px;
        border-bottom-left-radius:40px;
    }
    .btn-sub-add-course {
        font-size: 16px;
        display: block;
        margin: auto;
        color: white !important;
        border: 2px solid white;
        padding: 6px 11px;
        margin-top: 11px;
    }
    .courseInfoIcon {
        border-color: #ffffff;
        background-color: #b2d489;
        border-width: 2px;
        font-size: 10px;
        display: inline-block;
        margin: 0;
        color: #fff !important;
        width: 22px;
        padding: 2px 7px;
        text-align: center;
        float: right;
    }
    .courseInfoIcon:hover {
        background-color: #90af70;
    }

    .search-box .search-button {
        float: left;
        width: 100px;
        text-align: center;
        background: #259CC0;
        height: 70px;
        line-height: 70px;
        font-size: 22px;
        color: #ffffff;
        border-top-right-radius:40px;
        border-bottom-right-radius:40px;
    }

    .category-box {
        position: relative;
        width: 100%;
    }

    .popularCourseboxes .category-box {
        margin-bottom: 25px;
    }

    .category-box img {
        width: 100%;
        display: inline-block;
        border-radius: 20px;
    }

    .Popular-title-top {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        text-align: center;
        background: #259cc0;
        padding: 10px 20px;
        opacity: 1;
        transition: .6s ease;
        border-top-right-radius: 20px;
        border-top-left-radius: 20px;
    }

    .Popular-title-bottom {
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        text-align: center;
        color: #ffffff;
        background: rgb(37 156 192 / 67%);
        border-bottom-right-radius: 20px;
        padding: 15px 0 10px 0;
        border-bottom-left-radius: 20px;
        opacity: 1;
        transition: .6s ease;
    }

    .Popular-title-bottom .btn {
        border-radius: 25px;
        width: 100%;
        max-width: 200px;
        border-width: 2px;
    }

    .Popular-title-bottom .btn.two-in-row{
        display: inline-table;
        width: calc(50% - 10px);
        margin-left: 5px;
        max-width: 120px;
    }

    .bg-secondary {
        background-color: #a3cd8c!important;
    }

    .Popular-title-top .progress {
        height: 28px;
        border-radius: 25px;
        margin: 0px 13px;
    }

    .Popular-title-top .progress-bar {
        text-indent: 10px;
    }

    .course-title {
        margin-top: 40px;
        margin-bottom: 20px;
        font-weight: 700;
        font-size: 25px;
    }
    .Popular-title-top {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        text-align: center;
        color: #ffffff;
        background: #259cc0;
        border-top-right-radius: 20px;
        padding: 10px 0;
        font-weight: 300;
        border-top-left-radius: 20px;
        opacity: 1;
        transition: .6s ease;
    }

    .Popular-title-bottom {
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        text-align: center;
        color: #ffffff;
        background: rgb(37 156 192 / 67%);
        border-bottom-right-radius: 20px;
        padding: 10px 0;
        font-weight: 300;
        border-bottom-left-radius: 20px;
        opacity: 1;
        transition: .6s ease;
    }

    .popular-box-overlay {
        opacity: 0;
        transition: .6s ease;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        text-align: center;
        color: #ffffff;
        background: #248cab url(../images/courses-overlay-bg.png) no-repeat;
        background-position: left bottom;
        padding: 15px 0;
        font-weight: 300;
        border-bottom-left-radius: 20px;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        padding: 30px 0;
    }

    .popular-overlay-btn .btn.btn-outline-primary {
        color: #ffffff !important;
        border-color: #ffffff;
        border-width: 2px;
        font-size: 16px;
        margin-bottom: 5px;
    }

    .popular-overlay-btn-btm {
        display: inline-block;
        width: 100%;
        text-align: center;
    }

    .popular-overlay-btn-btm .btn.btn-outline-primary {
        color: #ffffff !important;
        border-color: #ffffff;
        background-color: #b2d489;
        border-width: 2px;
        font-size: 16px;
        margin-bottom: 10px;
        display: inline-table;
        padding-left: 15px;
        padding-right: 15px;
    }

    .btn.extra-radius {
        border-radius: 25px;
    }

</style>
    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--page title-->
        <section class="course-title">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="section-title text-center">Redeem Your Course</h1>
                    </div>
                </div>
            </div>
        </section>

        <!--Page Content-->
        <section class="staff-training">
            <div class="container wider-container">


                <div class="row">
                    <div class="col-12 regular-full popularCourseboxes">
                        <div class="row">
                            <div class="col-12 col-lg-3" id="categoryData">
                                <div class="white-rounded" style="padding:20px;padding-bottom:0;">
                                    <ul class="custom-control">
                                        <li class="active" data-category-id="">
                                            <a href="javascript:;" id="">
                                                All Courses
                                            </a>
                                        </li>

                                        <?php
                                        $categories = ORM::for_table("courseCategories")->where_null('parentID')->where("showOnHome", "1")->order_by_asc("title")->find_many();

                                        foreach($categories as $category) {
                                            if($category->id != "19") {
                                                ?>
                                                <li data-category-id="<?= $category->id ?>">
                                                    <a href="javascript:;" id="<?= $category->slug?>">
                                                        <?= $category->title ?>
                                                    </a>
                                                </li>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                    <div style="clear:both"></div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-9">

                                <div class="white-rounded" style="padding:20px;padding-bottom:0;">
                                    <div class="row" id="coursesAjax">
                                        <div class="col-12"><p class="text-center"><i class="fa fa-spin fa-spinner" style="font-size:50px;margin-top:50px;color:#248cab;"></i></p></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <script>
            setInterval(function(){
                $( "#categoryData li" ).click(function() {

                    var category = $(this).data("category-id");

                    $("#categoryData li").removeClass("active");
                    $(this).addClass("active");

                    $("#currentCategoryID").val(category);

                    $("#coursesAjax").html('<div class="col-12"><p class="text-center"><i class="fa fa-spin fa-spinner" style="font-size:50px;margin-top:50px;color:#248cab;"></i></p></div>');

                    $("#coursesAjax").load("<?= SITE_URL ?>ajax?c=redeem&a=browse-courses&category="+category);


                });
            }, 2000);

            function loadPopular() {
                $("#coursesAjax").load("<?= SITE_URL ?>ajax?c=redeem&a=browse-courses");
            }
        </script>

        <?php include BASE_PATH . 'learn-confidence.php'; ?>

        <?php include BASE_PATH . 'newsletter.php'; ?>

        <?php include BASE_PATH . 'featured.php'; ?>

        <?php include BASE_PATH . 'success-stories.php'; ?>

    </main>
    <!-- Main Content End -->

<?php include BASE_PATH . 'footer.php';?> 