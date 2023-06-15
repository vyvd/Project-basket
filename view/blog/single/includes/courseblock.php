<?php
$courses = explode(",", $article->courses);

?>
<div class="row">
    <?php
    foreach($courses as $courseID) {

        $course = ORM::For_table("courses")->find_one($courseID);

        if($course->id != "") {


            $course_cats = $this->course->getCourseCategories($course->id);

            $course_type = $this->course->getCourseType($course->id);

            $course->price = $this->getCoursePrice($course);

            $was = '';
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
                    $was = $this->price($original);
                    $course->price = $this->price($course->price);
                } else {
                    $course->price = $this->price($course->price);
                }

            } else {
                $course->price = $this->price($course->price);
            }

            ?>
            <div class="col-12 col-md-6">



                <div class="journeyCard">

                    <div class="row">
                        <div class="col-12 col-lg-4 col-md-4">
                            <div class="image">

                                <?php
                                if($was != "") {
                                    ?>
                                    <div class="saving">
                                        <i class="fas fa-tags"></i>
                                        Was <?= $was ?>
                                    </div>
                                    <?php
                                }
                                ?>

                                <img src="<?= $this->course->getCourseImage($course->id, "large") ?>" alt="<?= $course->title ?>">

                                <div class="price">
                                    <span>only</span>
                                    <?php
                                    echo $course->price;
                                    ?>
                                </div>

                            </div>
                        </div>
                        <div class="col-12 col-lg-8 col-md-8">
                            <div class="contents">
                                <h3><?= $course->title ?></h3>

                                <div class="usps">
                                    <?php
                                    if($course->childCourses == "") {
                                        ?>
                                        <div class="item">
                                            <?= ORM::for_table("courseModules")
                                                ->where("courseID", $course->id)->count() ?> modules
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="item">
                                            <?= count(json_decode($course->childCourses)) ?>
                                            courses
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <div class="item">
                                        <?= $course->enrollmentCount ?> students enrolled
                                    </div>
                                </div>

                                <a href="<?= SITE_URL ?>course/<?= $course->slug ?>" class="cta" target="_blank">
                                    More Info
                                    <i class="fas fa-chevron-right"></i>
                                </a>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <?php

        }

    }
    ?>
</div>