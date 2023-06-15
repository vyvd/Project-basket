<?php
$user = ORM::for_table("blumeUsers")->find_one($this->get["id"]);

$metaTitle = "Admin Log For ".$user->name.' '.$user->surname;
include BASE_PATH . 'blume.header.base.php';
?>
<!-- -------------- Content -------------- -->
<section id="content" class="table-layout animated fadeIn">


    <!-- -------------- /Column Left -------------- -->

    <!-- -------------- Column Center -------------- -->
    <div class="chute chute-center">


        <!-- -------------- Data Filter -------------- -->
        <div class="panel" id="spy2">
            <div class="panel-heading">
                <span class="panel-title"><?= $metaTitle ?></span>
            </div>

            <div class="panel-body pn">
                <div class="table-responsive">
                    <table class="table footable" data-filter="#fooFilter" data-page-navigation=".pagination" data-page-size="50">
                        <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>IP</th>
                            <th>Action Logged</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $items = ORM::for_table("blumeLogs")->where("userID", $user->id)->order_by_desc("id")->limit(1000)->find_many();

                        foreach($items as $item) {

                            ?>
                            <tr id="item<?= $item->id ?>">
                                <td>
                                    <?= $item->dateTime ?>
                                </td>
                                <td>
                                    <?= $item->ip ?>
                                </td>
                                <td>
                                    <?= $item->action ?>
                                </td>
                            </tr>

                            <?php
                        }
                        ?>
                        </tbody>
                        <tfoot class="footer-menu">
                        <tr>
                            <td colspan="7">
                                <nav class="text-right">
                                    <ul class="pagination hide-if-no-paging"></ul>
                                </nav>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>


        <!-- -------------- DEMO Break -------------- -->
        <div class="mv40"></div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>


<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
