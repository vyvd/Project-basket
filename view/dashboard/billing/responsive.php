<?php
$css = array("dashboard.css");
$pageTitle = "Billing & Receipts";
include BASE_PATH . 'account.header.php';
$this->setControllers(array("subscription"));
$subscriptions = $this->controller->getMySubscriptions();
$account = ORM::for_table('accounts')->find_one(CUR_ID_FRONT);
$activeTab = $_GET['tab'] ?? 'myReceipts';
?>
    <script src="https://js.stripe.com/v3/"></script>
    <section class="page-title with-nav">
        <div class="container">
            <h1><?= $pageTitle ?></h1>
            <ul class="nav navbar-nav inner-nav nav-tabs">
                <li class="nav-item link">
                    <a class="nav-link <?php if($activeTab == 'myReceipts'){?>active show <?php }?>" href="#myReceipts" data-toggle="tab">My Receipts</a>
                </li>
                <?php if(count($subscriptions) >= 1){?>
                    <li class="nav-item link ">
                        <a class="nav-link <?php if($activeTab == 'myInstalments'){?>active show <?php }?>" href="#myInstalments" data-toggle="tab">NS Pay Instalments</a>
                    </li>
                <?php }?>
                <?php if($account->isNCFE ==  '1'){?>
                    <li class="nav-item link ">
                        <a class="nav-link <?php if($activeTab == 'mySubscriptions'){?>active show <?php }?>" href="#mySubscriptions" data-toggle="tab">Manage My Subscription</a>
                    </li>
                <?php }?>
            </ul>
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
                        <div id="myReceipts" class="tab-pane <?php if($activeTab == 'myReceipts'){?>active show <?php }?>">
                            <div class="row">
                                <div class="col-12 col-md-12 white-rounded notification">
                                    <?php
                                    $orders = $this->controller->getMyOrders();
                                    ?>
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Your Order</th>
                                            <th scope="col">Date / Time</th>
                                            <th scope="col">Total Cost</th>
                                            <th scope="col">Invoice</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach($orders as $order) {
                                            $items = $this->controller->getOrderItems($order->id);
                                            $orderCurrency = ORM::for_table("currencies")->find_one($order->currencyID);
                                            ?>
                                            <tr>
                                                <th scope="row">#<?= $order->id ?></th>
                                                <th><?= count($items) ?> items</th>
                                                <th><?= date('jS M Y @ H:i', strtotime($order->whenUpdated)) ?></th>
                                                <th><?= $orderCurrency->short ?><?= number_format($order->total, 2) ?></th>
                                                <th><a class="btn btn-primary" href="<?= SITE_URL ?>invoice/<?= $order->id ?>?id=<?= $order->id ?>" target="_blank"><i class="far fa-file-pdf" style="margin-right:7px;"></i> View PDF</a></th>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php if(count($subscriptions) >= 1){ ?>
                            <div id="myInstalments" class="tab-pane <?php if($activeTab == 'myInstalments'){?>active show <?php }?>">
                                <div class="row">
                                    <div class="col-12 col-md-12 white-rounded notification">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Invoice No</th>
                                                    <th scope="col">Payment No</th>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">Amount</th>
                                                    <th scope="col">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    foreach ($subscriptions as $subscription){
                                                        $schedules = $this->subscription->getSubscriptionSchedulesByID($subscription->id);
                                                        if (count($schedules) >= 1){
                                                            $i=1;
                                                            foreach ($schedules as $schedule){
                                                                $status = "Not due";
                                                                $class = "";
                                                                if($schedule->isPayed == 1){
                                                                    $status = "Paid";
                                                                    $class = "payment-success";
                                                                }else if(($schedule->isPayed) == 3 || ($schedule->dueDate < date("Y-m-d"))){
                                                                    $status = "Failed";
                                                                    $class = "payment-error";
                                                                }
                                                            ?>
                                                                <tr class="<?= $class ?>">
                                                                    <td><?= $schedule->invoiceNumber?></td>
                                                                    <td>Payment <?= $i?></td>
                                                                    <td><?= date("d/m/Y",strtotime($schedule->dueDate))?></td>
                                                                    <td><?= $this->price($schedule->amount)?></td>
                                                                    <td><?= $status?></td>
                                                                </tr>
                                                                <?php
                                                                $i++;
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if($account->isNCFE ==  '1'){?>
                            <div id="mySubscriptions" class="tab-pane <?php if($activeTab == 'mySubscriptions'){?>active show <?php }?>">
                                <div class="row">
                                    <div class="col-12 col-md-12 white-rounded notification">
                                        <?php include('includes/manage_subscriptions.php');?>
                                    </div>
                                </div>
                            </div>
                        <?php }?>

                    </div>
                </div>
            </div>
        </div>
    </section>




<?php include BASE_PATH . 'account.footer.php';?>