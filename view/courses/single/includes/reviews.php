<?php
$totalReviews = 0;
$hasReviews = false;
if(@$course->reviews){
    $totalReviews = 1;
    $hasReviews = true;
    echo str_replace("http:", "https:", $course->reviews);
}
?>


        <?php
        if(@$reviews){
            ?>
<!--            <div class="course-reviews">-->
                <?php
                foreach($reviews as $review) {
                    $imageUrl = null;
                    if(@$review->comments){
                        $hasReviews = true;
                        $media = ORM::for_table('media')
                            ->where('modelType', 'courseReviewController')
                            ->where('modelId', $review->id)
                            ->find_one();
                        if(@$media->url) {
                            $imageUrl = str_replace($media->fileName,
                                'thumb/'.$media->fileName, $media->url);
                        }
                        ?>
                        <div class="single-testim">
                            <?php
                                if(@$imageUrl){
                            ?>
                                    <img src="<?= $imageUrl ?>" class="alignright" style="top: 0px" width="170" height="170" />
                                        <?php
                                }
                            ?>

                            <?= nl2br($review->comments) ?>
                            <em><?= $review->firstname.' '.$review->lastname ?></em>
                        </div>
                        <?php
                    } else if(@$review->testimonial){
                        $hasReviews = true;
                        ?>
                        <div class="single-testim">
                            <img src="<?= $this->testimonial->getTestimonialImage($review->id) ?>" class="alignright" style="top: 0px" width="170" height="170" />
                            <?= nl2br($review->testimonial) ?>
                            <em><?= $review->name ?></em>
                        </div>
                        <?php
                    }
                }
                ?>
<!--            </div>-->

            <?php
        }

        if($hasReviews == false) {
            $testimonials = ORM::for_table("testimonials")->where("location", "p")->order_by_expr("RAND()")->limit(6)->find_many();

            foreach($testimonials as $review) {


                ?>
                <div class="col-12 col-md-12">
                    <div style="max-width: 100%; width: 100%; margin: 0 0px 20px 0" class="review-boxes">
                        <p><?= nl2br($review->testimonial) ?></p>
                        <div class="review-user-details align-items-center">
                            <img src="<?= $this->testimonial->getTestimonialImage($review->id) ?>" class="review-user" />
                            <div>
                                <h3><?= $review->name ?></h3>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }

        }
        ?>