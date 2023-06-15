<?php
$pageTitle = "Messages";
include BASE_PATH . 'account.header.php';
?>
    <section class="page-title" style="padding-bottom:0;">
        <div class="container">
            <h1><?= $pageTitle ?></h1>
        </div>
    </section>

    <form name="selectedMessages" id="selectedMessages">
        <section class="page-content messages">
            <div class="container">

                <?php
                include BASE_PATH . 'subscribe-upsell.php'
                ?>

                <div class="row">
                    <div class="col-12">
                        <div class="tab-content container">
                            <div id="all" class="tab-pane active show">
                                <div class="row">
                                    <div class="col-12" id="accordioninbox">
                                        <?php $this->controller->renderInbox(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </form>
<?php include BASE_PATH . 'account.footer.php';?>