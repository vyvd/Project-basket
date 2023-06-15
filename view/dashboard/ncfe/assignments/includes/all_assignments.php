<div class="row">
    <div class="col-12 col-md-10 notification pt-0">
        <!-- supporting documents come from courses -->
        <?php
        if(count($courses) >= 1) {
            foreach($courses as $courseID => $course) {
        ?>
            <div class="row assignAccordion">
                <h3 class="w-100"><?= $course['title'];?></h3>
                <?php
                    foreach ($course['assignments'] as $module) {
                        $title = '';
                        $status = '';
                        if ($module->parentID) {
                            $parentModule = $this->courseModule->getModuleByID($module->parentID);
                            $title = $parentModule->title.' - ';
                        }
                        $uploadOriginalFiles = $this->courseModule->getModuleAssignments($module->id);
                        if(count($uploadOriginalFiles)) {
                        ?>
                            <div class="allagmt">
                                <h5>
                                    <?= $title.$module->title ?>
<!--                                    -->
                                </h5>
                                <div class="userAssignmentSection mt-2">
                                    <ul>
                                        <?php
                                        foreach ($uploadOriginalFiles as $file) {
                                            ?>
                                            <li><a href="<?= $file->url ?>"><?= $file->title ?>  <i style="color: #d75959; font-size: 22px" class="fad fa-file-pdf" aria-hidden="true"></i></a></li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>

                            </div>
                        <?php
                        }
                        $file = $uploadOriginalFiles[0];
                ?>
                <?php
                    }
                ?>
            </div>
        <?php
            }
        }
        ?>

    </div>
</div>