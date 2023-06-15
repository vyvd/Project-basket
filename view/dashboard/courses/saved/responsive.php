<?php
$pageTitle = "Saved Courses";
include BASE_PATH . 'account.header.php';

?>

<section class="page-title">
    <div class="container">
        <h1><?= $pageTitle ?></h1>
    </div>
</section>

<style>
    .pagination {
        list-style-type: none;
        padding: 10px 0;
        display: inline-flex;
        justify-content: space-between;
        box-sizing: border-box;
        
    }

    .pagination li {
        box-sizing: border-box;
        padding-right: 10px;

    }

    .pagination li a {
        box-sizing: border-box;
        background-color: #e2e6e6;
        padding: 12px;
        text-decoration: none;
        font-size: 14px;
        font-weight: bold;
        color: #616872;
        border-radius: 4px;
        
    }

    .pagination li a:hover {
        background-color: #d4dada;
        
    }

    .pagination .next a,
    .pagination .prev a {
        text-transform: uppercase;
        font-size: 12px;
        
    }

    .pagination .currentpage a {
        background-color: #518acb;
        color: #fff;
    }

    .pagination .currentpage a:hover {
        background-color: #518acb;
    }
</style>
<section class="page-content">
    <div class="container">
        <div class="row">



            <div id="popularCourseboxes" class="col-12 regular-full popularCourseboxes">
                <div class="row">
                    <?php

                    // $items = ORM::for_table("coursesSaved")->where("userID", CUR_ID_FRONT)->order_by_desc("id")->find_many();



                    $total_pages = ORM::for_table("coursesSaved")->where("userID", CUR_ID_FRONT)->count();
                    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
                    $num_results_on_page  = 15;
                    if (isset($_GET["page"])) {
                        $page  = $_GET["page"];
                    } else {
                        $page = 1;
                    };
                    $start_from = ($page - 1) * $num_results_on_page;

                    $result =  ORM::for_table("coursesSaved")->where("userID", CUR_ID_FRONT)->offset($start_from)->limit($num_results_on_page)->order_by_desc("id")->find_many();



                    if (count($result) == 0) {
                    ?>
                        <div class="col-12 text-center noSavedCourses">
                            You've not yet saved any courses. Save a course at any time by clicking the <i class="far fa-heart"></i> icon when browsing.
                        </div>

                    <?php
                    }

                    foreach ($result as $item) {
                        $course = ORM::for_table("courses")->find_one($item->courseID);

                    ?>
                        <div class="col-12 col-md-6 col-lg-4" id="courseRemove<?= $item->id ?>">
                            <div id="course-box" class="category-box">
                                <div class="img" style="background-image:url('<?= $this->controller->getCourseImage(
                                                                                    $course->id,
                                                                                    "large"
                                                                                ) ?>');"></div>

                                <div class="Popular-title-top"><i class="far fa-user"></i> <?= $course->enrollmentCount; ?>
                                    students enrolled
                                </div>
                                <div class="Popular-title-bottom"><?= $course->title ?>
                                    <h3><?= $this->price($course->price) ?></h3>
                                </div>
                                <div class="popular-box-overlay">
                                    <p><strong><?= $course->title ?></strong></p>
                                    <div class="popular-overlay-btn">
                                        <button type="button" class="btn btn-outline-primary btn-lg extra-radius"><?= ORM::for_table("courseModules")
                                                                                                                        ->where("courseID", $course->id)->count() ?>
                                            Modules
                                        </button>
                                    </div>
                                    <div class="popular-overlay-btn">
                                        <button type="button" class="btn btn-outline-primary btn-lg extra-radius">
                                            0% Finance
                                        </button>
                                    </div>
                                    <h3><?= $this->price($course->price) ?></h3>
                                    <div class="popular-overlay-btn-btm">
                                        <a class="btn btn-outline-primary btn-lg extra-radius nsa_course_more_info" href="<?= SITE_URL ?>course/<?= $course->slug ?>" role="button">More Info</a>
                                        <a class="btn btn-outline-primary btn-lg extra-radius" href="<?= SITE_URL ?>course/<?= $course->slug ?>?add=true" role="button">Add to Cart</a>

                                        <a class="saveHeart saveCourse<?= $course->id ?> <?php if ($this->checkCourseSaved($course->id) == true) { ?>active<?php } ?>" href="javascript:;" role="button" onclick="saveCourse(<?= $course->id ?>); $('#courseRemove<?= $item->id ?>').remove();">
                                            <i class="far fa-heart"></i>
                                        </a>


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
    </div>
    <div class="col-12 text-center">
        <ul class="pagination">

            <?php if ($page > 3) : ?>
                <li class="start"><a href="?page=1">1</a></li>
                <li class="dots">...</li>
            <?php endif; ?>

            <?php if ($page - 2 > 0) : ?><li class="page"><a href="?page=<?php echo $page - 2 ?>"><?php echo $page - 2 ?></a></li><?php endif; ?>
            <?php if ($page - 1 > 0) : ?><li class="page"><a href="?page=<?php echo $page - 1 ?>"><?php echo $page - 1 ?></a></li><?php endif; ?>

            <?php if( $total_pages == 0){ 

            }else{?>
                <li class="currentpage"><a href="?page=<?php echo $page ?>"><?php echo $page ?></a></li><?php
            }?>

            <?php if ($page + 1 < ceil($total_pages / $num_results_on_page) + 1) : ?><li class="page"><a href="?page=<?php echo $page + 1 ?>"><?php echo $page + 1 ?></a></li><?php endif; ?>
            <?php if ($page + 2 < ceil($total_pages / $num_results_on_page) + 1) : ?><li class="page"><a href="?page=<?php echo $page + 2 ?>"><?php echo $page + 2 ?></a></li><?php endif; ?>

            <?php if ($page < ceil($total_pages / $num_results_on_page) - 2) : ?>
                <li class="dots">...</li>
                <li class="end"><a href="?page=<?php echo ceil($total_pages / $num_results_on_page) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
            <?php endif; ?>

            <?php if ($page < ceil($total_pages / $num_results_on_page)) : ?>
            <?php endif; ?>
        </ul>

    </div>




</section>

<?php include BASE_PATH . 'account.footer.php';?>