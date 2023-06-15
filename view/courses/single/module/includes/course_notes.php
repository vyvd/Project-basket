<div class="form-row">
    <div class="col-12 col-md-12">

        <div class="row">
            <?php
            $notes = $this->controller->getAllCourseNotes($course);

            if (count($notes) == 0) {
                ?>
                <div class="col-12">
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <p class="text-center">You've not yet made any notes for
                        this course.</p>
                </div>
                <?php
            }

            foreach ($notes as $note) {
                $module = $this->controller->getModuleByID($note->moduleID);
                ?>
                <div class="col-12 col-md-4">
                    <div class="module" style="max-height: 300px;overflow: auto;">
                        <h4><?= $module->title ?></h4>
                        <div id="printable<?= $note->id ?>">
                            <?= $note->notes ?>
                        </div>
                        <div class="module-links">
                            <a href="<?= SITE_URL ?>module/<?= $module->slug ?>"
                               class="visit">Visit Module</a>
                            <a href="javascript:;"
                               onclick="printNotes<?= $note->id ?>();"><i
                                        class="fas fa-print"
                                        aria-hidden="true"></i></a>
                            <a href="<?= SITE_URL ?>module/<?= $module->slug ?>?open=notes"><i
                                        class="fas fa-pen"
                                        aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>

                <script>
                    function printNotes<?= $note->id ?>() {
                        var printContents = document.getElementById("printable<?= $note->id ?>").innerHTML;
                        var originalContents = document.body.innerHTML;

                        document.body.innerHTML = printContents;

                        window.print();

                        document.body.innerHTML = originalContents;
                    }
                </script>
                <?php

            }
            ?>
        </div>

    </div>
</div>