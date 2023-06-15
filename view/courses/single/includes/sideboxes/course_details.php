<div class="course-details">
    <span><p>Duration: <br/><strong><?= $course->duration ?> hours</strong></p></span>
</div>
<div class="course-details">
    <span><p>Access: <br/><strong>Lifetime Access</strong></p></span></div>
<?php

$course->courseApprovals = "";

if ($course->cpd_code != "") {
    $course->courseApprovals .= "CPD, ";
}

if ($course->is_cma == "1") {
    $course->courseApprovals .= "CMA, ";
}

if ($course->is_rospa == "1") {
    $course->courseApprovals .= "ROSPA, ";
}

if ($course->courseApprovals != "") {
    ?>
    <div class="course-details">
        <span><p>Approved by: <br/><strong><?= rtrim($course->courseApprovals,
                        ', '); ?></strong></p></span></div>
    <?php
}
if (in_array('Qualifications', $categories)) {
    ?>
    <div class="course-details">
        <span><p>NFQ Level:<br/><strong><?= $course->nfqLevel ?></strong></p></span>
    </div>
    <?php
}
?>
<div class="course-details">

    <div class="course-icons">
        <p><span>Learning Type:</span></p><br>
        <img data-toggle="tooltip" alt="Text based modules"
             title="Text based modules"
             src="<?= SITE_URL ?>assets/images/text-based-modules.png">&nbsp;
        <img data-toggle="tooltip" alt="Images" title="Images"
             src="<?= SITE_URL ?>assets/images/images-courses.png">&nbsp;
        <?php
        if ($course->is_video == 1) {
            ?>
            <img data-toggle="tooltip" alt="Video learning"
                 title="Video learning"
                 src="<?= SITE_URL ?>assets/images/video-learning.png">&nbsp;
            <?php
        }
        ?>
        <?php
        if ($course->is_audio == 1) {
            ?>
            <img data-toggle="tooltip" alt="MP3 audio lessons"
                 title="MP3 audio lessons"
                 src="<?= SITE_URL ?>assets/images/mp3-audio-learning.png">&nbsp;
            <?php
        }
        ?>
    </div>
</div>
<div class="course-details">


    <?php
    if ($originalPrice != $wasPrice) {
        ?>
        <span><p>Cost: <br/><strong><?= $originalPrice ?></strong></p></span>
        <a href="" class="btn btn-secondary btn-sm extra-radius financeBtn">was
            <s><?= $wasPrice ?></s></a>
        <?php
    } else {
        ?>
        <span><p>Cost: <br/><strong><?= $originalPrice ?></strong></p></span>
        <!--<a href="" class="btn btn-secondary btn-sm extra-radius financeBtn">0%
            Finance</a>-->
        <?php
    }

    ?>
</div>
<div class="course-details text-center">
    <a href="javascript:;"
       class="btn btn-primary btn-lg extra-radius start-course-button nsa_add_to_cart_btn"
       data-course-id="<?= $course->id ?>">BUY NOW</a>
</div>