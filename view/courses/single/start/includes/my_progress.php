<?php
$modArray = array();
$count = 1;
foreach($courseModules as $mod) {
    $modArray[$count] = $mod->id;
    $count ++;
}

$key = array_search($currentAssigned->currentModule, $modArray);
?>
<div class="myprogress-status">
    <label>Module <?= $key ?> of <?= count($courseModules); ?></label>
    <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: <?= $item->percComplete ?>%" aria-valuenow="<?= $item->percComplete ?>" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div>
<div id="accordionProgress">
    <?php
    $count = 1;
    foreach($this->controller->courseModules($course) as $module) {
        $subModules = $this->courseModule->getSubmodules($module->id);
        if(count($subModules) >= 1){
            ?>
            <div class="bg-white mb-3 <?=  $currentAssigned->currentModule ?>">
                <a class="myprogress-modules<?php if($count != $key){?> collapsed<?php }?>" data-toggle="collapse" data-target="#module<?= $module->id?>Data" aria-expanded="true" aria-controls="module<?= $module->id?>Data">
                    <div class="sno"><?= $count ?></div>
                    <div class="name"><?= $module->title ?></div>
                    <?php if($module->estTime != "") { ?>
                        <div class="time"><?= $module->estTime ?> minutes</div>
                    <?php } ?>
                    <div class="status <?php if($count < $key) { ?>complete<?php } else { ?>uncomplete<?php } ?>"><i class="fas fa-check"></i></div>
                </a>
                <div id="module<?= $module->id?>Data" class="progress-subModules collapse<?php if($count == $key){?> show<?php }?>" aria-labelledby="module<?= $module->id?>" data-parent="#accordion<?= $module->id?>">
                    <ul>
                        <?php
                        foreach ($subModules as $subModule) {
                            ?>
                            <li class="<?php if(in_array($subModule->id, $completedModules)){?>active<?php }?>">
                                <i class="fas fa-check"></i>
                                <?php if(in_array($subModule->id, $completedModules) || ($currentAssigned->currentSubModule == $subModule->id)){ ?>
                                    <a href="<?= SITE_URL ?>module/<?= $subModule->slug ?>"> <?= $subModule->title ?></a>
                                <?php } else { ?>
                                    <?= $subModule->title ?>
                                <?php }?>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <?php
        }else{
            ?>
            <div class="myprogress-modules" <?php if($count <= $key) { ?>style="cursor:pointer;" onclick="parent.location='<?= SITE_URL ?>module/<?= $module->slug ?>'"<?php } ?>>
                <div class="sno"><?= $count ?></div>
                <div class="name"><?= $module->title ?></div>
                <?php if($module->estTime != "") { ?>
                    <div class="time"><?= $module->estTime ?> minutes</div>
                <?php } ?>
                <div class="status <?php if($count < $key) { ?>complete<?php } else { ?>uncomplete<?php } ?>"><i class="fas fa-check"></i></div>
            </div>
            <?php
        }
        $count ++;
    }
    ?>
</div>