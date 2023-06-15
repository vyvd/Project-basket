<?php
$course = $this->controller->getCourseComplete();
$courseAssignedDetails = $this->controller->getCourseCompleteAssigned();

$pageTitle = "Congratulations!";
include BASE_PATH . 'account.header.php';
?>

    <div class="container-fluid" id="learn-zone">
        <div class="container containerSmall">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12">
                    <div class="">

                        <div class="quiz-question">

                            <div class="quizPassed">

                                <h2 class="quizResultTitle">Awesome job, <?= $this->user->firstname ?>!</h2>

                                <img src="<?= SITE_URL ?>assets/images/courseSuccess.png" class="quizResultImg" />

                                <p class="quizTextSmall">You did it! Your certificate is now ready to download.</p>


                                <div class="row quizBtnRow">
                                    <div class="col-6">
                                        <a href="<?= SITE_URL ?>certificate/<?= $course->slug ?>-certificate?id=<?= $courseAssignedDetails->id ?>" target="_blank" class="quiz-button dark">Download For Free</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="<?= SITE_URL ?>course/complete/review?token=<?= $courseAssignedDetails->certNo ?>&id=<?= $courseAssignedDetails->id ?>" class="quiz-button">Next</a>
                                    </div>
                                </div>

                                <p class="quizTextSmall" style="margin-top:22px;">
                                    <em>P.S. You just earned a reward point</em>
                                </p>

                            </div>

                        </div>

                        <div class="upsellBox">
                            <p>Get a printed copy of your certificate delivered by post</p>
                            <a href="<?= SITE_URL ?>ajax?c=cart&a=add-certificate&id=<?= $courseAssignedDetails->id ?>" target="_blank">
                                Order Now
                            </a>
                        </div>



                        <div style="display:none;" class="quiz-question">

                            <div class="quizPassed">

                                <p>You have successfully completed the <strong><?= $course->title ?></strong> course!</p>

                                <i class="fa fa-check wow pulse" data-wow-delay="2s"></i>

                                <p>Your Certificate Reference: <?= $courseAssignedDetails->certNo ?></p>

                                <a href="<?= SITE_URL ?>ajax?c=certificate&a=cert-pdf&id=<?= $courseAssignedDetails->id ?>" target="_blank" class="quiz-button">
                                    <i class="fa fa-download" style="margin-right:5px;"></i>
                                    View Certificate
                                </a>

                                <br />
                                <br />
                                <h4>Rate This Course</h4>

                                <form name="rating">
                                    <div class="rating">
                                        <label>
                                            <input type="radio" name="rating" value="5" title="5 stars"> 5
                                        </label>
                                        <label>
                                            <input type="radio" name="rating" value="4" title="4 stars"> 4
                                        </label>
                                        <label>
                                            <input type="radio" name="rating" value="3" title="3 stars"> 3
                                        </label>
                                        <label>
                                            <input type="radio" name="rating" value="2" title="2 stars"> 2
                                        </label>
                                        <label>
                                            <input type="radio" name="rating" value="1" title="1 star"> 1
                                        </label>
                                    </div>
                                    <input type="hidden" name="course" value="<?= $course->id ?>" />
                                </form>
                                <?php
                                $this->renderFormAjax("course", "rate-course", "rating");
                                ?>

                                <p><small>This helps us to recommend better courses to our learners going forward.</small></p>

                                <script>
                                    $('.rating input').change(function () {
                                        var $radio = $(this);
                                        $('.rating .selected').removeClass('selected');
                                        $radio.closest('label').addClass('selected');
                                        $radio.closest('form').submit();
                                    });
                                </script>

                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>
    </div>

<?php include BASE_PATH . 'account.footer.php';?>