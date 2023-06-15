<?php
$this->setControllers(array("blog"));
$course = $this->controller->getSingleCourse();
$item = $this->controller->getCourseAssigned($course);

$_GET['request'] = $parameters[2];
$blog = $this->blog->getBlogArticle();

if($item->id == "" || $blog->id == "") {
    $this->force404();
}

if($item->currentModule == "" || $item->currentModule == "0") {
    $item->currentModule = "1";
}

if($this->get["start"] == "true") {
    // actually start the course
    $this->controller->startCourse($course);
}

$pageTitle = $blog->title;
include BASE_PATH . 'account.header.php';


?>
<section class="page-title with-nav">
    <div class="container">

        <?php if($course->childCourses == "") { ?>
            <?php
            if($this->controller->checkCourseStarted($course) == false) {
                ?>
                <a href="<?= SITE_URL ?>start/<?= $course->slug ?>?start=true" class="mainBtn">Back To Course</a>
            <?php } else {
                $continueModule = ORM::for_table("courseModules")->find_one($this->controller->checkCourseStarted($course));
                ?>
                <a href="<?= SITE_URL ?>module/<?= $continueModule->slug ?>" class="mainBtn">Continue Course</a>
            <?php } ?>
        <?php } else {
            ?>
            <a href="<?= SITE_URL ?>dashboard/courses" class="mainBtn">All Courses</a>
            <?php
        } ?>

        <h1 class="pb-4" ><?= $blog->title ?></h1>



    </div>
</section>

<div class="tab-content container">

    <style>
        .category-box h1, .category-box h2, .category-box h3, .category-box h4, .category-box h5, .category-box h6 {
            font-family: Caveat Brush;
        }
        .popularCourseboxes .category-box.blog-content img {
            margin-bottom:30px;
        }
    </style>

    <div class="popularCourseboxes" style="max-width:1000px;margin:auto;margin-top:55px;">
        <div class="row">
            <div class="col-12">
                <div class="category-box bg-white p-5 blog-content">
                    <?php echo nl2br($blog->contents);?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include BASE_PATH . 'account.footer.php';?>
