<?php

$order = ORM::for_table("orders")->find_one($this->get["id"]);

$currency = ORM::for_table("currencies")->find_one($order->currencyID);

$metaTitle = "Order $order->id";
include BASE_PATH . 'blume.header.base.php';
?>

<!-- -------------- Content -------------- -->
<section id="content" class="table-layout animated fadeIn">

    <!-- -------------- /Column Left -------------- -->

    <!-- -------------- Column Center -------------- -->
    <div class="chute chute-center">

        <div id="return_status"></div>


        <div class="row">
            <div class="col-xs-8">
                <form name="updateOrder" id="updateOrder">
                    <h4>Order #<?= $this->get["id"] ?></h4>
                    <div class="panel" id="spy2">
                        <div class="panel-heading">
                            <span class="panel-title">Customer Details</span>
                        </div>
                        <div class="panel-body pn">

                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label>Firstname</label>
                                        <input type="text" class="form-control" name="firstname" value="<?= $order->firstname ?>" />
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label>Lastname</label>
                                        <input type="text" class="form-control" name="lastname" value="<?= $order->lastname ?>" />
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" name="email" value="<?= $order->email ?>" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="panel" id="spy2">
                        <div class="panel-heading">
                            <span class="panel-title">Address & Delivery</span>
                        </div>
                        <div class="panel-body pn">

                            <div class="row">
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <label>Address 1</label>
                                        <input type="text" class="form-control" name="address1" value="<?= $order->address1 ?>" />
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <label>Address 2</label>
                                        <input type="text" class="form-control" name="address2" value="<?= $order->address2 ?>" />
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <label>City</label>
                                        <input type="text" class="form-control" name="city" value="<?= $order->city ?>" />
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <label>Postcode</label>
                                        <input type="text" class="form-control" name="postcode" value="<?= $order->postcode ?>" />
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <label>County</label>
                                        <input type="text" class="form-control" name="county" value="<?= $order->county ?>" />
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <label>Country</label>
                                        <input type="text" class="form-control" name="country" value="<?= $order->country ?>" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="panel" id="spy2">
                        <div class="panel-heading">
                            <span class="panel-title">Admin Notes</span>
                        </div>
                        <div class="panel-body pn">
                            <textarea class="form-control" name="adminNotes" rows="6" placeholder="Record internal customer service notes about this order..."><?= $order->adminNotes ?></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="orderID" value="<?= $this->get["id"] ?>" />
                    <input type="submit" class="btn btn-success btn-block" value="Update" style="margin-top:-20px;margin-bottom:20px;" />
                    <div class="returnStatus"></div>
                </form>

                <div class="panel" id="spy2">
                    <div class="panel-heading">
                        <span class="panel-title">Items</span>

                    </div>
                    <div class="panel-body pn">
                        <div class="table-responsive">
                            <table class="table footable" data-filter="#fooFilter" data-page-navigation=".pagination" data-page-size="50">
                                <thead>
                                <tr>
                                    <th>Item / Course</th>
                                    <th>Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                // For Custom Bundle
                                $customItems = ORM::for_table("orderItems")->where('isCustomBundle', 1)->where("orderID", $order->id)->find_many();
                                if(count($customItems) >= 1){
                                    $cItems = [];
                                    foreach($customItems as $item) {
                                        $course = ORM::for_table("courses")->find_one($item->courseID);
                                        if(@$course->title) {
                                            $cItems[] = $course->title;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            Custom Bundle : <br><?= implode('<br> ', $cItems);?>
                                            <?php
                                            if($item->gift == "1" && $item->giftEmail != "") {
                                                $link = SITE_URL.'?claim=gift&token='.$item->giftToken.'&email='.urlencode($item->giftEmail);
                                                ?>
                                                <p><em><strong>Gifted</strong> to <?= $item->giftEmail ?>. <?php if($item->giftClaimed == "0") { ?><a href="<?= $link ?>" target="_blank">Claim Link</a><?php } ?></em></p>
                                                <?php
                                            }
                                            ?>

                                        </td>
                                        <td>59.00 <?= $currency->code ?></td>
                                    </tr>
                                <?php
                                }


                                foreach(ORM::for_table("orderItems")->where('isCustomBundle', 0)->where("orderID", $order->id)->find_many() as $item) {
                                    if($item->course == "1") {
                                        $course = ORM::for_table("courses")->find_one($item->courseID);
                                        if($course->title == "") {
                                            $course = ORM::for_table("courses")->where("oldID", $item->courseID)->find_one();
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <?= $course->title ?>
                                                <?php
                                                if($item->gift == "1" && $item->giftEmail != "") {
                                                    $link = SITE_URL.'?claim=gift&token='.$item->giftToken.'&email='.urlencode($item->giftEmail);
                                                    ?>
                                                    <p>
                                                        <?php
                                                        if($item->giftSent == "1") {
                                                            ?>
                                                            <label class="label label-success"><i class="fa fa-check"></i> Sent</label>
                                                            <?php
                                                        }
                                                        ?>
                                                        <em><strong>Gifted</strong> to <?= $item->giftEmail ?>. <?php if($item->giftClaimed == "0") { ?><a href="<?= $link ?>" target="_blank">Claim Link</a><?php } ?></em></p>
                                                    <?php

                                                    if($item->giftDate != "") {
                                                        ?>
                                                        <p>
                                                            Scheduled to be sent on <?= date('d/m/Y', strtotime($item->giftDate)) ?>
                                                        </p>
                                                        <?php
                                                    }

                                                }
                                                if($item->printedCourse == "1") {
                                                    ?>
                                                    <p>
                                                        <strong>Printed Materials</strong>
                                                    </p>
                                                    <?php
                                                }
                                                ?>

                                            </td>
                                            <td><?= number_format($item->price, 2) ?> <?= $currency->code ?></td>
                                        </tr>
                                        <?php
                                    }
                                    else if($item->voucherID != "") {
                                        // then its a gifted voucher
                                        $voucher = ORM::for_table("vouchers")->find_one($item->voucherID);
                                        $course = ORM::for_table("courses")->find_one($voucher->courses);

                                        ?>
                                        <tr>
                                            <td>
                                                <strong>Voucher: <?= $voucher->code ?></strong> for <?= $course->title ?>
                                                <br />
                                                <small>To: <?= $voucher->giftTo ?></small><br />
                                                <small>From: <?= $voucher->giftFrom ?></small><br />
                                                <small>Message: <?= $voucher->giftMessage ?></small><br />

                                            </td>
                                            <td><?= $this->price($item->price) ?></td>
                                        </tr>
                                        <?php
                                    }
                                    else if($item->premiumSubPlanID != "") {
                                        ?>
                                        <tr>
                                            <td>Subscription Payment</td>
                                            <td><?= number_format($item->price, 2) ?> <?= $currency->code ?></td>
                                        </tr>
                                        <?php
                                    }
                                    else {
                                        // then its a cert.
                                        $cert = ORM::for_table("coursesAssigned")->find_one($item->certID);
                                        $course = ORM::for_table("courses")->find_one($cert->courseID);

                                        ?>
                                        <tr>
                                            <td><strong>Cert: <?= $cert->certNo ?></strong> <?= $course->title ?></td>
                                            <td><?= number_format($item->price, 2) ?> <?= $currency->code ?></td>
                                        </tr>
                                        <?php
                                    }

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
            </div>
            <div class="col-xs-4">
                <div class="row mb10">
                    <div class="col-12">
                        <button data-type="single" data-url="<?= SITE_URL ?>ajax?c=import&a=orders&order_id=<?= $order->id;?>" class="importJson btn btn-dark pull-right"><i class="fa fa-download"></i> Import Order</button>
                    </div>
                </div>

                <div class="panel" id="spy2">
                    <div class="panel-heading">
                        <span class="panel-title">Other Details</span>
                    </div>
                    <div class="panel-body pn">
                        <form name="otherDetails">
                            <p>
                                <strong>Total Paid (<?= $currency->code ?>):</strong>
                                <input type="text" class="form-control" name="totalPaid" value="<?= number_format($order->total, 2); ?>" style=" display: inline-block;width: 87px;height: auto;padding: 2px 6px;" />
                                <br />
                                <strong>Customer IP:</strong> <?= $order->customerIP ?><br />
                                <strong>Order Date/Time:</strong> <?= date('d/m/Y @ H:i:s', strtotime($order->whenUpdated)); ?><br />

                                <strong>Payment Method:</strong> <?= $order->method_title ?><br />
                                <strong>Payment ID:</strong> <?= $order->transactionID ?><br />
                                <strong>Approx. GBP Value:</strong> <?= $order->totalGBP ?><br />
                                <?php

                                if($order->couponID != "") {
                                    $code = ORM::for_table("coupons")->find_one($order->couponID);
                                    echo "<strong>Discount Code:</strong> ".$code->code;
                                }
                                ?>
                            </p>
                            <?php
                            if($order->utm_source != "") {
                                ?>
                                <p>
                                    <strong>Source:</strong> <?= $order->utm_source ?><br />
                                    <strong>Medium:</strong> <?= $order->utm_medium ?><br />
                                    <strong>Campaign:</strong> <?= $order->utm_campaign ?><br />
                                    <strong>Term:</strong> <?= $order->utm_term ?><br />
                                </p>
                                <?php
                            }
                            ?>
                            <input type="hidden" name="orderID" value="<?= $order->id ?>" />
                        </form>

                        <script type="text/javascript">
                            $("form[name='otherDetails']").submit(function(e) {

                                var formData = new FormData($(this)[0]);

                                e.preventDefault();

                                $( "#return_status" ).empty();


                                $.ajax({
                                    url: "<?= SITE_URL ?>ajax?c=blume&a=update-order-other-details",
                                    type: "POST",
                                    data: formData,
                                    async: false,
                                    success: function (msg) {
                                        $('#return_status').append(msg);
                                    },
                                    cache: false,
                                    contentType: false,
                                    processData: false
                                });

                            });
                        </script>
                    </div>
                </div>

                <div class="panel" id="spy2">
                    <div class="panel-heading">
                        <span class="panel-title">Print / View PDFs</span>
                    </div>
                    <div class="panel-body pn">
                        <a href="<?= SITE_URL ?>ajax?c=invoice&a=invoice-pdf&id=<?= $order->id ?>&adminKey=gre45h-56trh_434rdfng" target="_blank" class="btn btn-system btn-block">Invoice</a>
                    </div>
                </div>

                <div class="panel" id="spy2">
                    <div class="panel-heading">
                        <span class="panel-title">Discount</span>
                    </div>
                    <div class="panel-body pn">
                        <?php
                        if($order->couponID == "") {
                            ?>
                            <p><em>No discount was used on this order.</em></p>
                            <?php
                        } else {
                            $coupon = ORM::For_table("coupons")->find_one($order->couponID);
                            if($coupon->code == "") {
                                $coupon = ORM::For_table("coupons")->where("oldID", $order->couponID)->find_one();
                            }
                            ?>
                            <p>
                                <strong>Code:</strong> <?= $coupon->code ?><br />
                                <?php
                                if($coupon->type == "p") {
                                    $original = $order->total/(1-($coupon->value/100));
                                    $discount = $original-$order->total;
                                    ?>
                                    <strong>Discount:</strong> <?= $coupon->value ?>% off (<s><?= number_format($original, 2) ?></s>) <span style="color:#ff0000">-£<?= number_format($discount, 2) ?></span>
                                    <?php
                                } else {
                                    ?>
                                    <strong>Discount:</strong> <span style="color:#ff0000">-<?= number_format($coupon->value, 2) ?></span>
                                    <?php
                                }
                                ?>
                            </p>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>





<script type="text/javascript">
    function confirm_alert(node) {
        return confirm("Are you sure you want to do this? This action cannot be undone.");
    }
    function markPaid() {
        var x = <?= $this->get["id"] ?>;
        var hr = new XMLHttpRequest();
        var url = "<?= SITE_URL ?>ajax/blume?action=mark-order-paid";
        var kv = "id="+x;

        hr.open("POST", url, true);
        hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        hr.onreadystatechange = function() {
            if(hr.readyState == 4 && hr.status == 200) {
                $(".paidButton").html('<button class="btn btn-danger btn-block" onclick="markUnpaid();">Unmark as Paid</button>');
            }
        }

        hr.send(kv);

    }
    function markProgress() {
        var x = <?= $this->get["id"] ?>;
        var hr = new XMLHttpRequest();
        var url = "<?= SITE_URL ?>ajax?c=blume&a=mark-order-progress";
        var kv = "id="+x;

        hr.open("POST", url, true);
        hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        hr.onreadystatechange = function() {
            if(hr.readyState == 4 && hr.status == 200) {
                $(".progressButton").html('<button type="button" class="btn btn-danger btn-block" onclick="markUnprogress();">Unmark as In Progress</button>');
            }
        }

        hr.send(kv);

    }

    function markUnprogress() {
        var x = <?= $this->get["id"] ?>;
        var hr = new XMLHttpRequest();
        var url = "<?= SITE_URL ?>ajax?c=blume&a=mark-order-unprogress";
        var kv = "id="+x;

        hr.open("POST", url, true);
        hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        hr.onreadystatechange = function() {
            if(hr.readyState == 4 && hr.status == 200) {
                $(".progressButton").html('<button type="button" class="btn btn-success btn-block" onclick="markProgress();">Mark As In Progress</button>');
            }
        }

        hr.send(kv);

    }
    function markUnpaid() {
        var x = <?= $this->get["id"] ?>;
        console.log("running function");
        var hr = new XMLHttpRequest();
        var url = "<?= SITE_URL ?>ajax/blume?action=mark-order-unpaid";
        var kv = "id="+x;

        hr.open("POST", url, true);
        hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        hr.onreadystatechange = function() {
            if(hr.readyState == 4 && hr.status == 200) {
                $(".paidButton").html('<button class="btn btn-danger btn-block" onclick="markPaid();">Mark as Paid</button>');
            }
        }

        hr.send(kv);
    }
    function markDispatched() {
        $("#updateOrder").submit();
        var x = <?= $this->get["id"] ?>;
        var hr = new XMLHttpRequest();
        var url = "<?= SITE_URL ?>ajax/blume?action=mark-order-dispatched";
        var kv = "id="+x;

        hr.open("POST", url, true);
        hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        hr.onreadystatechange = function() {
            if(hr.readyState == 4 && hr.status == 200) {
                $(".dispatchedButton").html('<button class="btn btn-warning btn-block" onclick="markUndispatched();">Unmark As Dispatched</button>');
            }
        }

        hr.send(kv);
    }
    function markUndispatched() {
        var x = <?= $this->get["id"] ?>;
        var hr = new XMLHttpRequest();
        var url = "<?= SITE_URL ?>ajax/blume?action=mark-order-undispatched";
        var kv = "id="+x;

        hr.open("POST", url, true);
        hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        hr.onreadystatechange = function() {
            if(hr.readyState == 4 && hr.status == 200) {
                $(".dispatchedButton").html('<button class="btn btn-warning btn-block" onclick="markDispatched();">Mark As Dispatched</button>');
            }
        }

        hr.send(kv);
    }

    $("form[name='updateOrder']").submit(function(e) {

        var formData = new FormData($(this)[0]);

        e.preventDefault();

        $( ".returnStatus" ).empty();


        $.ajax({
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=update-order",
            type: "POST",
            data: formData,
            async: false,
            success: function (msg) {
                $('.returnStatus').html(msg);
            },
            cache: false,
            contentType: false,
            processData: false
        });

    });
</script>
<style>
    .btn {
        margin-top:5px;
    }
</style>
<?php
$this->controller->recordLog('viewed the following order: '.$this->get["id"]);
?>
<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
