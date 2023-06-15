<div id="modulesAccordion">
    <?php
    $modArray = array();

    $count = 1;
    foreach($courseModules as $mod) {
        $modArray[$count] = $mod->id;
        $count ++;
    }

    $key = array_search($currentAssigned->currentModule, $modArray);
    ?>

    <?php
    $count = 1;
    if($key == "") {
        $key = "1";
    }
    foreach($courseModules as $module) {
        ?>

        <div class="card">
            <div class="card-header" id="heading<?= $module->id ?>">
                <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#question<?= $module->id ?>" aria-expanded="<?php if($count == $key) { ?>true<?php } else { ?>false<?php } ?>" aria-controls="question<?= $module->id ?>">
                        <?= $module->title ?>
                    </button>
                </h5>
            </div>

            <div id="question<?= $module->id ?>" class="collapse <?php if($count == $key) { ?>show<?php } ?>" aria-labelledby="heading<?= $module->id ?>" data-parent="#modulesAccordion">
                <div class="card-body">
                    <h4 class="underlined">Learning Topics</h4>
                    <br />
                    <?= $module->description ?>
                    <?php
//                    $subModules = $this->courseModule->getSubmodules($module->id);
//                    if(count($subModules) >= 1) {
//                        echo '<div class="courseSubModuleList mb-4" style="height: auto">';
//                        foreach($subModules as $sub) {
//                            echo '<p><i class="fad fa-check"></i>'.$sub->title .'</p>';
//                        }
//                        echo '</div>';
//                    }
                    ?>

                    <?php
                    if($module->estTime != "") {
                        ?>
                        <div class="modules-btn" style="margin-top:10px;">
                            <button type="button" class="btn btn-secondary btn-lg" disabled>Approx Time: <?= $module->estTime ?> minutes</button>
                        </div>
                    <?php } ?>
                    <?php if($count < $key || $item->completed == "1") { ?>
                        <div class="modules-btn" onclick="parent.location='<?= SITE_URL ?>module/<?= $module->slug ?>'">
                            <button type="button" class="btn btn-primary btn-lg">Revisit</button>
                        </div>
                    <?php } else if($count == $key) {
                        ?>
                        <div class="modules-btn" onclick="parent.location='<?= SITE_URL ?>module/<?= $module->slug ?>'">
                            <button type="button" class="btn btn-primary btn-lg">Start</button>
                        </div>
                        <?php
                    } else { ?>
                        Not available until the previous module is completed.
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
        $count ++;
    }
    ?>
</div>