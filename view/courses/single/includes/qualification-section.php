<?php
$avgCompletion = $course->avgCompletion;
$suitableFor = $course->suitableFor;
$memberships = $course->memberships;
$hardLevel = $course->hardLevel;
$examType = $course->examType;
$examTime = $course->examTime;
$examTitle = $course->examTitle;

?>
<div class="col-12">
    <div class="single-course-qualification">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12 col-md-3 column brl25">
                        <div class="col-title">Avg. Completion</div>
                        <div class="col-value"><?= $avgCompletion?></div>
                    </div>
                    <div class="col-12 col-md-3 column">
                        <div class="col-title">Suitable for:</div>
                        <div class="col-value">
                            <?php
                            for ($i = 1; $i <= $hardLevel; $i++) {
                                ?>
                                <img class="img-level"
                                     src="<?= SITE_URL ?>assets/images/level-filled.png">
                                <?php
                            }
                            for ($i = 1; $i <= (5 - $hardLevel); $i++) {
                                ?>
                                <img class="img-level"
                                     src="<?= SITE_URL ?>assets/images/level-unfilled.gif">
                                <?php
                            }
                            ?>
                        </div>
                        <p><?= $suitableFor?></p>
                    </div>
                    <div class="col-12 col-md-3 column">
                        <div class="col-title">Exams</div>
                        <div class="col-value">
                            <div class="row">
                                <div class="col-6"><?= $examType?></div>
                                <div class="col-6"><?= $examTime?></div>
                            </div>
                        </div>
                        <p><?= $examTitle?></p>
                    </div>
                    <div class="col-12 col-md-3 column bg-grey">
                        <div class="col-title">Memberships</div>
                        <div class="col-value"><?php echo nl2br($memberships);?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>