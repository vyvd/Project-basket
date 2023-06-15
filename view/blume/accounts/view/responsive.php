<?php
$this->setControllers(array('course', "subscription"));
$account = $this->controller->getAccount();

// get current subscription details, if there is one
$subItem = $this->subscription->getCurrentUserSubscription($account->id);
$premiumSubscriptionsPlans = ORM::for_table('premiumSubscriptionsPlans')
    ->order_by_asc('months')->find_many();

$metaTitle = $account->firstname.' '.$account->lastname;
include BASE_PATH . 'blume.header.base.php';
if($this->get["iframe"] == "true") {
    ?>
    <style>
        header {
            display:none;
        }
        #sidebar_left {
            display:none;
        }
        #content_wrapper {
            margin: 0 !important;
            margin-top: -63px !important;
        }

    </style>
    <?php
}
?>
<link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/select2/css/core.css"
      xmlns="http://www.w3.org/1999/html">
<style>
    .select2.select2-container{
        width: 100% !important;
    }
    .subcategory{
        margin-left: 15px;
    }
    #ui-datepicker-div{
        z-index: 99999 !important;
    }
</style>
<!-- -------------- Content -------------- -->
<section id="content" class="table-layout animated fadeIn">


    <!-- -------------- /Column Left -------------- -->

    <!-- -------------- Column Center -------------- -->
    <div class="chute chute-center">


        <!-- -------------- Data Filter -------------- -->
        <div class="panel" id="spy2">
            <div class="panel-heading">

                <span class="panel-title" style="font-size:16px;">Account - <?= $account->firstname.' '.$account->lastname ?> <?php if($this->isActiveSubscription($account->id)) { ?><i class="fa fa-star" style="color:#e0c011;margin-left:5px;"></i><?php } ?>
                <span class="label label-system" style="font-size: 10px;
    position: Relative;
    top: -2px;
    left: 5px;">Spend: £<?= number_format($account->totalSpend, 2) ?></span>
                </span>

                <button data-type="multiple" data-url="<?= SITE_URL?>ajax?c=import&a=user_notes&account_id=<?= $account->id ?>" class="importJson btn btn-primary pull-right ml10"><i class="fa fa-download"></i> Notes</button>


                <button data-type="multiple" data-url="<?= SITE_URL?>ajax?c=import&a=user_enrollments&importFrom=us&account_id=<?= $account->id ?>" class="importJson btn btn-dark pull-right ml10"><i class="fa fa-download"></i> COM Enrollments</button>
                <button data-type="multiple" data-url="<?= SITE_URL?>ajax?c=import&a=user_enrollments&importFrom=uk&account_id=<?= $account->id ?>" class="importJson btn btn-dark pull-right ml10"><i class="fa fa-download"></i> UK Enrollments</button>


                <a href="javascript:;" data-toggle="modal" data-target="#addReward" class="btn btn-warning pull-right">
                    <i class="fa fa-plus"></i> Reward
                </a>

                <a href="javascript:;" data-toggle="modal" data-target="#addTrophies" class="btn btn-system pull-right" style="margin-right:5px;"><i class="fa fa-plus"></i> Trophies</a>

                <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=access-users-account&id=<?= $account->id ?>" target="_blank" class="btn btn-danger pull-right" style="margin-right:5px;"><i class="fa fa-chevron-right"></i> Access</a>

                <a href="javascript:;" data-toggle="modal" data-target="#enroll" class="btn btn-info pull-right" style="margin-right:5px;"><i class="fa fa-plus"></i> Course</a>
            </div>

            <br />

            <div class="tab-block">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab1" data-toggle="tab">Details</a>
                    </li>
                    <li>
                        <a href="#tab2" data-toggle="tab">Purchases</a>
                    </li>
                    <li>
                        <a href="#tab3" data-toggle="tab">Courses</a>
                    </li>
                    <li>
                        <a href="#tab4" data-toggle="tab">Sign In Log</a>
                    </li>
                    <li>
                        <a href="#tab5" data-toggle="tab">Rewards</a>
                    </li>
                    <li>
                        <a href="#tab6" data-toggle="tab">Redeemed Vouchers</a>
                    </li>
                    <li>
                        <a href="#tab7" data-toggle="tab">Subscription</a>
                    </li>
                    <li>
                        <a href="#tab8" data-toggle="tab">Email Logs</a>
                    </li>
                    <li>
                        <a href="#certificatesTab" data-toggle="tab">Certificates</a>
                    </li>
                </ul>
                <div class="tab-content p30">
                    <div id="tab1" class="tab-pane active">


                        <div class="row">
                            <div class="col-xs-12">

                                <div class="row">
                                    <div class="col-xs-3">
                                        <div class="panel panel-tile">
                                            <div class="panel-body">
                                                <div class="row pv10">
                                                    <div class="col-xs-12 pl5 text-center">
                                                        <h6 class="text-muted">Courses</h6>

                                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;"><?= ORM::for_table("coursesAssigned")->where("accountID", $account->id)->order_by_desc("whenAssigned")->count() ?></h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-3">
                                        <div class="panel panel-tile">
                                            <div class="panel-body">
                                                <div class="row pv10">
                                                    <div class="col-xs-12 pl5 text-center">
                                                        <h6 class="text-muted">Purchases</h6>

                                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;"><?= ORM::for_table("orders")->where("accountID", $account->id)->order_by_desc("whenUpdated")->count() ?></h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-3">
                                        <div class="panel panel-tile">
                                            <div class="panel-body">
                                                <div class="row pv10">
                                                    <div class="col-xs-12 pl5 text-center">
                                                        <h6 class="text-muted">Rewards</h6>

                                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;"><?= ORM::for_table("rewardsAssigned")->where("userID", $account->id)->count() ?></h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-3">
                                        <div class="panel panel-tile">
                                            <div class="panel-body">
                                                <div class="row pv10">
                                                    <div class="col-xs-12 pl5 text-center">
                                                        <h6 class="text-muted">Total Sign Ins</h6>

                                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;"><?= ORM::for_table("accountSignInLogs")->where("accountID", $account->id)->count() ?></h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <form name="updateGeneral" autocomplete="off">
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" class="form-control" name="firstname" value="<?= $account->firstname ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" class="form-control" name="lastname" value="<?= $account->lastname ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" class="form-control" name="email" value="<?= $account->email ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="tel" class="form-control" name="phone" value="<?= $account->phone ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Currency</label>
                                                <select class="form-control" name="currencyID">
                                                    <?php
                                                    $currencyCode = "GBP";
                                                    foreach(ORM::for_table("currencies")->find_many() as $currency) {
                                                        if($account->currencyID == $currency->id) {
                                                            $currencyCode = $currency->code;
                                                        }
                                                        ?>
                                                        <option value="<?= $currency->id ?>" <?php if($currency->id == $account->currencyID) { ?>selected<?php } ?>><?= $currency->code ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>
                                                    Balance (<?= $currencyCode ?>)
                                                    <a href="javascript:;" data-toggle="modal" data-target="#addBalance">Add</a>
                                                    |
                                                    <a href="javascript:;" data-toggle="modal" data-target="#removeBalance">Remove</a>
                                                </label>
                                                <input type="text" class="form-control" disabled name="" value="<?= $account->balance ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>New Password <small>(only enter to reset the users password)</small></label>
                                                <input type="text" class="form-control" name="password" />
                                            </div>
                                        </div>
                                        <div class="col-xs-1">
                                            <img src="<?= SITE_URL ?>assets/cdn/profileImg/<?= $account->profileImg ?>" style="width:70px;height:70px;border-radius:50%;" />
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Replace Profile Image</label>
                                                <input type="file" class="form-control" name="uploaded_file" />
                                            </div>
                                        </div>
                                        <div class="col-xs-3" style="min-height:88px;">
                                            <div class="form-group">
                                                <label class="form-check-label">Also update name on certificates?</label>&nbsp;
                                                <br />
                                                <input type="checkbox" name="updateCertificates" id="updateCertificates" value='1' class="form-check-input"> Yes
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Disable timer on modules?</label>&nbsp;
                                                <select class="form-control" name="disableModuleTimer">
                                                    <option value="0">No</option>
                                                    <option value="1" <?php if($account->disableModuleTimer == "1") { ?>selected<?php } ?>>Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Two Factor Authentication</label>&nbsp;
                                                <select class="form-control" name="twoFactor">
                                                    <option value="0">No</option>
                                                    <option value="1" <?php if($account->twoFactor == "1") { ?>selected<?php } ?>>Yes</option>
                                                </select>
                                            </div>
                                        </div>


                                    </div>

                                    <p>
                                        <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=delete-user-account&id=<?= $account->id ?>" style="color:#ff0000;" onclick="return confirm('Are you sure you want to delete this account? This is permanent and cannot be undone!')">Delete this account...</a>
                                    </p>
                                    <br />
                                    <br />
                                    <input type="submit" class="btn btn-success" value="Update" />
                                    <input type="hidden" name="itemID" value="<?= $account->id ?>" />
                                </form>
                                <br />
                                <div id="returnGeneral"></div>
                                <?php
                                $this->renderFormAjax("blumeNew", "edit-account", "updateGeneral", "#returnGeneral", false);
                                ?>

                                <hr />

                                <?php
                                $notes = ORM::for_table("accountAdminNotes")->where("accountID", $account->id)->find_one();
                                ?>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <form name="updateAdminNotes" autocomplete="off">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Admin Notes <small>(these notes cannot be seen by students)</small></label>
                                                        <textarea name="adminNotes" class="tinymce"><?= $notes->notes ?></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <input type="submit" class="btn btn-success" value="Update" />
                                                </div>
                                            </div>
                                            <input type="hidden" name="id" value="<?= $account->id ?>" />
                                        </form>
                                        <br />
                                        <div id="returnAdminNotes"></div>
                                        <script type="text/javascript">
                                            jQuery("form[name='updateAdminNotes']").submit(function(e) {
                                                tinyMCE.triggerSave();
                                                e.preventDefault();
                                                var formData = new FormData($(this)[0]);

                                                jQuery.ajax({
                                                    url: "<?= SITE_URL ?>ajax?c=blumeAccount&a=edit-admin-notes",
                                                    type: "POST",
                                                    data: formData,
                                                    async: true,
                                                    success: function (msg) {
                                                        jQuery('#returnAdminNotes').html(msg);
                                                    },
                                                    cache: false,
                                                    contentType: false,
                                                    processData: false
                                                });
                                            });
                                        </script>


                                    </div>
                                </div>


                            </div>
                        </div>


                    </div>
                    <div id="tab2" class="tab-pane">

                        <div class="table-responsive">
                            <table class="table footable" data-filter="#fooFilter" data-page-navigation=".pagination" data-page-size="50">
                                <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Status</th>
                                    <th>Date/Time</th>
                                    <th>Total</th>
                                    <th>Items</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $items = ORM::for_table("orders")->where("accountID", $account->id)->order_by_desc("whenUpdated")->find_many();

                                foreach($items as $item) {
                                    $category = ORM::for_table("categories")->find_one($item->category);
                                    ?>
                                    <tr id="item<?= $item->id ?>">
                                        <td>
                                            <?= $item->id ?>
                                        </td>
                                        <td>
                                            <?php
                                            if($item->status != "completed") {
                                                ?>
                                                <label class="label label-danger">Incomplete</label>
                                                <?php
                                            } else {
                                                ?>
                                                <label class="label label-success">Complete</label>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y @ H:i:s', strtotime($item->whenUpdated)) ?>
                                        </td>
                                        <td>
                                            £<?= number_format($item->total, 2) ?>
                                        </td>
                                        <td>
                                            <?php

                                            $item2 = ORM::for_table("orderItems")->where("orderID", $item->id)->find_one();

                                            if($item2->id == "") {
                                                echo '0';
                                            } else {

                                                $text = '';

                                                if($item2->course == "1") {
                                                    $course = ORM::for_table("courses")->find_one($item2->courseID);
                                                    if($course->title == "") {
                                                        $course = ORM::for_table("courses")->where("oldID", $item2->courseID)->find_one();
                                                    }
                                                    $text = $course->title;
                                                }
                                                else if($item2->voucherID != "") {
                                                    // then its a gifted voucher
                                                    $voucher = ORM::for_table("vouchers")->find_one($item2->voucherID);
                                                    $course = ORM::for_table("courses")->find_one($voucher->courses);
                                                    $text = 'Gift Voucher for '.$course->title;
                                                }
                                                else if($item2->premiumSubPlanID != "") {
                                                    $text = 'Subscription';
                                                }
                                                else {
                                                    // then its a cert.
                                                    $cert = ORM::for_table("coursesAssigned")->find_one($item2->certID);

                                                    $text = 'Cert: '.$cert->certNo;

                                                }

                                                if (strlen($text) > 30) {
                                                    $text = substr($text, 0, 30) . '...';
                                                }

                                                echo '('.ORM::for_table("orderItems")->where("orderID", $item->id)->count().') '.$text;

                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?= SITE_URL ?>blume/orders/view?id=<?= $item->id ?>" class="label label-system" target="_blank">
                                                View
                                            </a>
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
                    <div id="tab3" class="tab-pane">

                        <div class="table-responsive" id="userCourses">
                            <i class="fa fa-spin fa-spinner"></i>
                        </div>

                        <script>
                            $("#userCourses").load("<?= SITE_URL ?>ajax?c=blumeNew&a=load-user-courses&id=<?= $account->id ?>");
                        </script>

                    </div>
                    <div id="tab4" class="tab-pane">

                        <div class="table-responsive">
                            <table class="table footable" data-filter="#fooFilter" data-page-navigation=".pagination" data-page-size="50">
                                <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>IP Address</th>
                                    <th>Success</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $items = ORM::for_table("accountSignInLogs")->where("accountID", $account->id)->order_by_desc("dateTime")->find_many();

                                foreach($items as $item) {
                                    ?>
                                    <tr id="item<?= $item->id ?>">
                                        <td>
                                            <?= date('d/m/Y @ H:i:s', strtotime($item->dateTime)) ?>
                                        </td>
                                        <td>
                                            <?= $item->ipAddress ?>
                                        </td>
                                          <td>
                                            <?= $item->success ?>
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
                    <div id="tab5" class="tab-pane">

                        <div class="table-responsive" id="userRewards">
                            <i class="fa fa-spin fa-spinner"></i>
                        </div>

                        <script>
                            $("#userRewards").load("<?= SITE_URL ?>ajax?c=blumeNew&a=load-user-rewards&id=<?= $account->id ?>");
                        </script>



                    </div>
                    <div id="tab6" class="tab-pane">

                        <div class="table-responsive" id="userVouchers">
                            <i class="fa fa-spin fa-spinner"></i>
                        </div>

                        <script>
                            $("#userVouchers").load("<?= SITE_URL ?>ajax?c=blumeNew&a=load-user-vouchers&id=<?= $account->id ?>");
                        </script>

                    </div>
                    <div id="tab7" class="tab-pane">


                        <div class="row">
                            <div class="col-xs-12">

                                <form name="updateSubscription" autocomplete="off">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <select class="form-control" name="subActive">
                                                    <option value="0"><?= $account->firstname ?> does NOT currently have an active subscription</option>
                                                    <option value="1" <?php if($account->subActive == "1") { ?>selected<?php } ?>><?= $account->firstname ?> does currently have an active subscription</option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if($subItem->id != "") { ?>
                                            <div class="col-xs-4">
                                                <div class="form-group">
                                                    <label>Renewal Amount</label>
                                                    <input type="text" class="form-control" name="pricePerMonthAmount" value="<?= $subItem->perMonthAmount ?>" />
                                                </div>
                                            </div>
                                            <div class="col-xs-4">
                                                <div class="form-group">
                                                    <label>Next Payment Date</label>
                                                    <input type="text" class="form-control" disabled name="" value="<?= date('d/m/Y', strtotime($subItem->nextPaymentDate)) ?>" />
                                                </div>
                                            </div>
                                            <div class="col-xs-4">
                                                <div class="form-group">
                                                    <label>Auto Renew Subscription?</label>
                                                    <select class="form-control" name="autoRenew">
                                                        <option value="1">Yes</option>
                                                        <option value="0">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php }else{
                                            if($account->subActive == 0) {
                                        ?>
                                            <div class="col-xs-4">
                                                <div class="form-group">
                                                    <label>Expiration Period</label>
                                                    <select name="subExpiryMonths" class="form-control">
                                                        <option value="">Select Months</option>
                                                        <?php
                                                            foreach ($premiumSubscriptionsPlans as $plan) {
                                                        ?>
                                                                <option value="<?= $plan->months ?>"><?= $plan->months ?> Months</option>
                                                        <?php
                                                            }
                                                        ?>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-1">OR</div>
                                        <?php }?>
                                            <div class="col-xs-4">
                                                <div class="form-group">
                                                    <label>Expiration Date</label>
                                                    <input id="expiryDate" type="text" class="form-control" name="subExpiryDate" value="<?= $account->subExpiryDate ?>" />
                                                </div>
                                            </div>
                                        <?php
                                        } ?>


                                    </div>


                                    <br />
                                    <br />
                                    <input type="submit" class="btn btn-success" value="Update" />
                                    <input type="hidden" name="itemID" value="<?= $account->id ?>" />
                                </form>
                                <br />
                                <div id="returnSubscription"></div>
                                <?php
                                $this->renderFormAjax("blumeNew", "update-subscription", "updateSubscription", "#returnSubscription");
                                ?>


                            </div>
                        </div>


                    </div>
                    
                    <div id="tab8" class="tab-pane">

                        <div class="table-responsive">
                            <table class="table footable" data-filter="#fooFilter" data-page-navigation=".pagination" data-page-size="50">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Contents</th>
                                    <th>Time Sent</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                $items = ORM::for_table("emailLogs")->where("email", $account->email )->order_by_desc("whenSent")->find_many();
                                

                            
                                foreach($items as $item) {

                                    ?>
                                    <tr>
                                        
                                        <td>
                                            <?= $item->id ?>
                                        </td>

                                        <td>
                                            <?= $item->email ?>
                                        </td>
                                        <td>
                                            <?= $item->subject ?>
                                        </td>
                                        <td>
                                            <?= $item->contents ?>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y @ H:i:s', strtotime($item->whenSent)) ?>
                                        </td>
                                        <td>
                                            <a onclick="reSendEmail(<?= $item->id?>)" href="" style="text-align: center;" class="btn btn-warning">Resend Email</a>
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
                     
                     
                    <div id="certificatesTab" class="tab-pane">
                        <?php include ('includes/certificates.php');?>
                    </div>
                </div>
            </div>
        </div>


        <!-- -------------- DEMO Break -------------- -->
        <div class="mv40"></div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>
<div id="deleteProduct"></div>
<script type="text/javascript">

    $("#expiryDate").datepicker({
        numberOfMonths: 1,

        dateFormat: "yy-mm-dd",
        prevText: '<i class="fa fa-chevron-left"></i>',
        nextText: '<i class="fa fa-chevron-right"></i>',
        showButtonPanel: false,
        beforeShow: function(input, inst) {
            var newclass = 'allcp-form';
            var themeClass = $(this).parents('.allcp-form').attr('class');
            var smartpikr = inst.dpDiv.parent();
            if (!smartpikr.hasClass(themeClass)) {
                inst.dpDiv.wrap('<div class="' + themeClass + '"></div>');
            }
        }

    });
    $("#expiryDate").datepicker({
        numberOfMonths: 1,
        dateFormat: "yy-mm-dd",
        prevText: '<i class="fa fa-chevron-left"></i>',
        nextText: '<i class="fa fa-chevron-right"></i>',
        showButtonPanel: false,
        beforeShow: function(input, inst) {
            var newclass = 'allcp-form';
            var themeClass = $(this).parents('.allcp-form').attr('class');
            var smartpikr = inst.dpDiv.parent();
            if (!smartpikr.hasClass(themeClass)) {
                inst.dpDiv.wrap('<div class="' + themeClass + '"></div>');
            }
        }

    });
    
    function reSendEmail(emailID) {
        $.ajax({
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=reSendEmail",
            type: "POST",
            data: {
                'emailID': emailID,
            },
            success: function(response) {
                console.log(response);
            },

        });
    }

    function deleteItem(id) {
        if (window.confirm("Are you sure you want to delete this item?")) {
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-course&id="+id);
            $("#item"+id).fadeOut();
        }
    }
    function deleteEnrollment(id) {
        if (window.confirm("Are you sure you want to delete this enrollment? This cannot be undone and the user will no longer have access to this course.")) {
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-user-enrollment&account=<?= $account->id ?>&id="+id);
            $(".enrollment"+id).fadeOut();
        }
    }
    function completeCourse(id, complete_date = null) {
        $("#courseAssignedId").val(id);
        if(complete_date){
            $("#whenCompletedCourse").val(complete_date);
        }else{
            $("#whenCompletedCourse").val('<?= date("Y-m-d") ?>');
        }
        $('#completeCourseModal').modal("show");
        return false;

        $( "#deleteProduct" ).load( "<?= SITE_URL ?>ajax?c=blumeNew&a=complete-course-assigned&id="+id, function() {

            $("#userCourses").load("<?= SITE_URL ?>ajax?c=blumeNew&a=load-user-courses&id=<?= $account->id ?>");

        });

    }

    function resetCourse(id) {

        $( "#deleteProduct" ).load( "<?= SITE_URL ?>ajax?c=blumeNew&a=reset-course-user&id="+id, function() {

            $("#userCourses").load("<?= SITE_URL ?>ajax?c=blumeNew&a=load-user-courses&id=<?= $account->id ?>");

        });

    }
</script>

<div class="modal fade" id="completeCourseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Complete Course</h4>
            </div>
            <form name="completeCourseForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Completion Date</label>
                        <input autocomplete="off" type="text" class="form-control datepicker" id="whenCompletedCourse" name="whenCompleted" value="<?= date("Y-m-d") ?>" placeholder="" />
                    </div>
                    <div id="returnStatusCompleteCourse"></div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="courseAssignedId" name="id" value="">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
            <?php
            $this->renderFormAjax("blumeNew", "complete-course-assigned", "completeCourseForm", "#returnStatusCompleteCourse");
            ?>
        </div>
    </div>
</div>
<div class="modal fade" id="addBalance" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Balance</h4>
            </div>
            <form name="addBalance">
                <div class="modal-body">
                    <div class="form-group">
                        <label>How much balance do you want to add to <?= $account->firstname ?>'s account?</label>
                        <input type="text" class="form-control" name="amount" value="10.00" placeholder="0.00" />
                    </div>
                    <div class="form-group">
                        <label>Description <small>(<?= $account->firstname ?> will be able to see this)</small></label>
                        <input type="text" class="form-control" name="description" placeholder="Just a sentence will do..." />
                    </div>
                    <input type="hidden" name="accountID" value="<?= $account->id ?>" />
                    <p><em><?= $account->firstname ?> will be notified about this balance change via email.</em></p>
                    <div id="returnStatusAddBalance"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Balance</button>
                </div>
            </form>
            <?php
            $this->renderFormAjax("blumeAccount", "add-user-balance", "addBalance", "#returnStatusAddBalance");
            ?>
        </div>
    </div>
</div>

<div class="modal fade" id="removeBalance" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Remove Balance</h4>
            </div>
            <form name="removeBalance">
                <div class="modal-body">
                    <div class="form-group">
                        <label>How much balance do you want to remove from <?= $account->firstname ?>'s account?</label>
                        <input type="text" class="form-control" name="amount" value="10.00" placeholder="0.00" />
                    </div>
                    <div class="form-group">
                        <label>Description <small>(<?= $account->firstname ?> will be able to see this)</small></label>
                        <input type="text" class="form-control" name="description" placeholder="Just a sentence will do..." />
                    </div>
                    <input type="hidden" name="accountID" value="<?= $account->id ?>" />
                    <p><em><?= $account->firstname ?> will be notified about this balance change via email.</em></p>
                    <div id="returnStatusRemoveBalance"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Remove Balance</button>
                </div>
            </form>
            <?php
            $this->renderFormAjax("blumeAccount", "remove-user-balance", "removeBalance", "#returnStatusRemoveBalance");
            ?>
        </div>
    </div>
</div>

<div class="modal fade" id="enroll" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Create Enrollment</h4>
            </div>
            <form name="enroll">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Which course do you want to enroll <?= $account->firstname ?> onto?</label>
                        <select class="form-control select2" name="courseID">
                            <?php
                            foreach($this->getAllCoursesWithoutHidden() as $course) {
                                if($course->isNCFE == "0") {
                                    ?>
                                    <option value="<?= $course->id ?>"><?= $course->title ?> <?php if($course->usImport == "1") { ?>(US)<?php } ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Which course type should this be classed as?</label>
                        <select class="form-control" name="sub">
                            <option value="0">Purchased</option>
                            <option value="1">Subscription</option>
                        </select>
                    </div>
                    <input type="hidden" name="accountID" value="<?= $account->id ?>" />
                    <p><em><?= $account->firstname ?> will be enrolled automatically onto the start of this course.</em></p>
                    <div id="returnStatusAddNew"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Enroll</button>
                </div>
            </form>
            <?php
            $this->renderFormAjax("blumeNew", "create-user-enrollment", "enroll");
            ?>
        </div>
    </div>
</div>

<div class="modal fade" id="addReward" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Reward</h4>
            </div>
            <form name="addReward">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Which reward do you want to assign to <?= $account->firstname ?>?</label>
                        <select class="form-control" name="rewardID">
                            <?php
                            foreach(ORM::for_table("rewards")->find_many() as $reward) {
                                ?>
                                <option value="<?= $reward->short ?>"><?= $reward->name ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <input type="hidden" name="accountID" value="<?= $account->id ?>" />
                    <input type="hidden" name="isAdmin" value="1" />
                    <p><em><?= $account->firstname ?> will also have a point (or points) added to their account.</em></p>
                    <div id="returnStatusAddNew"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
            <?php
            $this->renderFormAjax("blumeNew", "add-user-reward", "addReward");
            ?>
        </div>
    </div>
</div>
<div class="modal fade" id="addTrophies" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Reward</h4>
            </div>
            <form name="addTrophies">
                <div class="modal-body">
                    <div class="form-group">
                        <label>How many more Trophies assign to <?= $account->firstname ?>?</label>
                        <input type="number" name="no_trophies" class="form-control" min="1" max="20" >
                    </div>
                    <input type="hidden" name="accountID" value="<?= $account->id ?>" />
                    <div id="returnStatusAddNew"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
            <?php
            $this->renderFormAjax("blumeNew", "add-user-trophies", "addTrophies");
            ?>
        </div>
    </div>
</div>


<script src='<?= SITE_URL ?>assets/blume/js/plugins/select2/select2.min.js'></script>
<script type="text/javascript">
    $('.select2').select2();
    $( function() {
        $(".datepicker").datepicker({
            numberOfMonths: 1,
            changeYear: true,
            dateFormat: "yy-mm-dd",
            prevText: '<i class="fa fa-chevron-left"></i>',
            nextText: '<i class="fa fa-chevron-right"></i>',
            showButtonPanel: false,
            beforeShow: function(input, inst) {
                var newclass = 'allcp-form';
                var themeClass = $(this).parents('.allcp-form').attr('class');
                var smartpikr = inst.dpDiv.parent();
                if (!smartpikr.hasClass(themeClass)) {
                    inst.dpDiv.wrap('<div class="' + themeClass + '"></div>');
                }
            }

        });
    } );
</script>
<script src='<?= SITE_URL ?>assets/js/tinymce/tinymce.min.js'></script>
<script>

    tinymce.init({
        selector: '.tinymce',
        plugins: 'table link lists hr textcolor emoticons image imagetools media link preview visualchars visualblocks wordcount template code',
        toolbar: 'undo redo paste | styleselect template | bold italic strikethrough underline | link image media | bullist numlist | aligncenter alignleft alignright alignjustify alignnone | blockquote | backcolor forecolor | removeformat visualblocks code',
        height: '200',
        templates: [
            {
                title: "Default Starter",
                description: "",
                url: "<?= SITE_URL ?>assets/cdn/editorTemplates/moduleDefault.html"
            },
            {
                title: "Blue Summary Box",
                description: "",
                url: "<?= SITE_URL ?>assets/cdn/editorTemplates/blueSummary.html"
            },
            {
                title: "Grey Background Content",
                description: "",
                url: "<?= SITE_URL ?>assets/cdn/editorTemplates/greyBackground.html"
            },
            {
                title: "Did You Know / Tip",
                description: "",
                url: "<?= SITE_URL ?>assets/cdn/editorTemplates/didYouKnow.html"
            },
            {
                title: "Paper / Notepad",
                description: "",
                url: "<?= SITE_URL ?>assets/cdn/editorTemplates/paper.html"
            }
        ],
        content_css : "https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css,<?= SITE_URL ?>assets/css/global.css,<?= SITE_URL ?>assets/css/editor.css",
        relative_urls : false,
        remove_script_host : false,
        convert_urls : true,
        // enable title field in the Image dialog
        image_title: true,
        // enable automatic uploads of images represented by blob or data URIs
        automatic_uploads: true,
        // URL of our upload handler (for more details check: https://www.tinymce.com/docs/configure/file-image-upload/#images_upload_url)
        images_upload_url: '<?= SITE_URL ?>ajax?c=blumeNew&a=tiny-mce-uploader',
        // here we add custom filepicker only to Image dialog
        file_picker_types: 'image',
        // and here's our custom image picker
        image_advtab: true,
        file_picker_callback: function(cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            // Note: In modern browsers input[type="file"] is functional without
            // even adding it to the DOM, but that might not be the case in some older
            // or quirky browsers like IE, so you might want to add it to the DOM
            // just in case, and visually hide it. And do not forget do remove it
            // once you do not need it anymore.

            input.onchange = function() {
                var file = this.files[0];

                var reader = new FileReader();
                reader.onload = function () {
                    // Note: Now we need to register the blob in TinyMCEs image blob
                    // registry. In the next release this part hopefully won't be
                    // necessary, as we are looking to handle it internally.
                    var id = 'imageID' + (new Date()).getTime();
                    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);

                    // call the callback and populate the Title field with the file name
                    cb(blobInfo.blobUri(), { title: file.name });
                };
                reader.readAsDataURL(file);
            };

            input.click();
        }
    });

    // Active Tabs
    $(document).ready(function(){
        var tab = 'tab1';
        <?php
        if(@$_GET['tab'] && $_GET['tab'] == 'modules'){
        ?>
        tab = 'tabModules'
        <?php
        }
        ?>


        activaTab(tab);
    });

    function activaTab(tab){
        $('.nav-tabs a[href="#' + tab + '"]').tab('show');
    }
</script>


<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
