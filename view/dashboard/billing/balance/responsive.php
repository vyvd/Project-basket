<?php
$css = array("dashboard.css");
$pageTitle = "Balance Transactions";
include BASE_PATH . 'account.header.php';

$account = ORM::for_table('accounts')->find_one(CUR_ID_FRONT);
?>
    <script src="https://js.stripe.com/v3/"></script>
    <section class="page-title with-nav">
        <div class="container">
            <h1><?= $pageTitle ?></h1>
        </div>
    </section>


    <section class="page-content">
        <div class="container">

            <?php
            include BASE_PATH . 'subscribe-upsell.php'
            ?>

            <div class="row">
                <div class="col-12 regular-full">
                    <div class="tab-content container white-rounded profile-tabs myProfile">
                        <div id="myReceipts" class="tab-paneactive show">
                            <div class="row">
                                <div class="col-12 col-md-12 white-rounded notification">
                                    <?php
                                    $transactions = ORM::for_table("accountBalanceTransactions")->where("accountID", CUR_ID_FRONT)->order_by_desc("id")->find_many();
                                    ?>
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Debit</th>
                                            <th scope="col">Credit</th>
                                            <th scope="col">Date / Time</th>
                                            <th scope="col">Description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach($transactions as $transaction) {
                                            ?>
                                            <tr>
                                                <th scope="row">#<?= $transaction->id ?></th>
                                                <th>-<?= $this->price($transaction->debit) ?></th>
                                                <th>+<?= $this->price($transaction->credit) ?></th>
                                                <th><?= date('jS M Y @ H:i', strtotime($transaction->dateTime)) ?></th>
                                                <th><?= $transaction->description ?></th>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>




<?php include BASE_PATH . 'account.footer.php';?>