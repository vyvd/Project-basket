<div id="accordionMods">
    <?php
    if($course->childCourses != "") {

        ?>
        <h3>This is a bundle course, and contains the following courses:</h3>
        <?php

        foreach(json_decode($course->childCourses) as $child) {
            $rand = rand(99,99999);
            $childCourse = ORM::for_table("courses")->find_one($child);
            ?>
            <div class="card grey-bg-box">
                <div class="card-header" id="module1<?= $rand ?>">
                    <h5 class="mb-0">
                        <a class="faq-title" data-toggle="collapse" data-target="#module1Data<?= $count ?>" aria-expanded="false" aria-controls="module1Data<?= $rand ?>">
                            <?= $childCourse->title ?>
                        </a>
                    </h5>
                </div>

                <div id="module1Data<?= $rand ?>" class="collapse" aria-labelledby="module1<?= $rand ?>" data-parent="#accordionMods">
                    <div class="card-body">
                        <?= $childCourse->description ?>
                        <?= $childCourse->additionalContent ?>
                    </div>
                </div>
            </div>
            <?php

        }
    }
    ?>

    <?php
    $count = 1;
    foreach($this->controller->courseModules($course) as $module) {
        ?>
        <div class="card grey-bg-box">
            <div class="card-header" id="module1<?= $count ?>">
                <h5 class="mb-0">
                    <a class="faq-title" data-toggle="collapse" data-target="#module1Data<?= $count ?>" aria-expanded="false" aria-controls="module1Data<?= $count ?>">
                        <?= $module->title ?>
                    </a>
                </h5>
            </div>

            <div id="module1Data<?= $count ?>" class="collapse" aria-labelledby="module1<?= $count ?>" data-parent="#accordionMods">
                <div class="card-body">
                    <?= $module->description ?>
                </div>
            </div>
        </div>
        <?php
        $count ++;
    }
    ?>
</div>