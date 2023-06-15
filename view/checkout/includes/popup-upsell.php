<div class="modal fade basket upsellDetails" id="upsellDetails" tabindex="-1"
     role="dialog" aria-labelledby="basketTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <a class="btn-close" data-dismiss="modal">X</a>
                <p class="popup-title upsell-title text-center"><?= $upsell->title ?></p>
                <div class="row">
                    <div class="col-8 text-center">
                        <img class="upsell-col" style="max-height: 200px" src="<?= $this->course->getCourseImage($upsell->id, "large");?>">
                    </div>
                    <div class="col-4 ">
                        <img class="upsell-col" style="padding: 20px 5px; background: none" src="<?= SITE_URL.'assets/images/exclusive-ns.png'?>">

                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-9 pr-0">
                        <p class="upsell-col" style="border-right: none">
                            RRP: <?= $this->price($upsell->price) ?><br />
                            Duration: <?= $upsell->duration ?> hours<br />
                            Qualification: <?= $upsell->title ?>
                        </p>
                    </div>
                    <div class="col-3 pl-0 ">
                        <div class="upsell-col" style="background: #259cc0; color: #ffffff; border-left: 0;">
                            <h3>Exclusive <br>Offer</h3>
                        </div>

                    </div>
                </div>
                <?php if($upsell->childCourses != "") {?>
                    <div class="row mt-3">
                        <div class="col-12 ">
                            <div class="upsell-col" style="float:left; padding: 0 20px">
                                <h3 class="pt-3 pb-0 text-left">Includes</h3>
                                <ul>
                                    <?php
                                    foreach(json_decode($upsell->childCourses) as $child) {
                                        $rand = rand(99,99999);
                                        $childCourse = ORM::for_table("courses")->find_one($child);
                                        ?>
                                        <li><?= $childCourse->title ?></li>
                                        <li><?= $childCourse->title ?></li>
                                        <li><?= $childCourse->title ?></li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php }?>
            </div>
        </div>
    </div>
</div>
