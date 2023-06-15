<?php
if($this->getSetting("status_message") != "") {
    ?>

    <style>
        .statusBar {
            width: 100%;
            background: #ff6f00;
            text-align: center;
            color: #fff;
            padding: 8px;
        }
        .statusBar i {
            color: #000000;
            margin-right: 5px;
        }
    </style>

    <div class="statusBar">
        <i class="fad fa-exclamation-triangle"></i>
        <?= $this->getSetting("status_message") ?>
    </div>

    <?php
}