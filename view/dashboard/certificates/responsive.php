<?php
$css = array("dashboard.css");
$pageTitle = "My Certificates";
include BASE_PATH . 'account.header.php';
$certificateCoupon = $this->controller->getCertificateCoupon($this->user->id);
$lastOrder = ORM::for_table('orders')->where('accountID', $this->user->id)->where_not_null('address1')->order_by_desc('whenCreated')->find_one();
//echo "<pre>";
//print_r($certificateCoupon);
//die;
?>
    <section class="page-title">
        <div class="container">
            <h1>
                <?= $pageTitle ?>
            </h1>
        </div>
    </section>

    <section class="page-content">
        <div class="container">

            <?php
            include BASE_PATH . 'subscribe-upsell.php'
            ?>

            <div class="row">

                <div class="col-12 regular-full">
                    <div class="row">
                        <div class="col-12 col-md-12 white-rounded notification certificate_lists">
                            <?php
                            if(@$certificateCoupon && $certificateCoupon->totalUses == 0){
                                ?>
                                <div class="alert alert-danger text-center p-4">
                                    <h5 class="m-0">You have earned a free reward certificate. Claim you free Certificate below.</h5>
                                </div>
                                <?php
                            }
                            ?>

                            <img src="<?= SITE_URL ?>assets/images/cert-offer.jpg" style="width:100%;margin-bottom:15px;" />

                            <?php
                            $certs = $this->controller->getMyCertificates();
                            ?>
                            <table class="table" id="desktopCerts">
                                <thead>
                                <tr>
                                    <th scope="col">Course</th>
                                    <th scope="col">Ref</th>
                                    <th scope="col">Date / Time</th>
                                    <th scope="col"></th>
                                    <th scope="col">Certificates</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach($certs as $cert) {

                                    $dispatched = ORM::for_table('orderItems')
                                        ->where('certNumber', $cert->certNo)
                                        ->order_by_desc('whenCreated')
                                        ->find_one();

                                    $course = ORM::for_table("courses")->find_one($cert->courseID);
                                    $order = ORM::For_table("orderItems")->where("certID", $cert->id)->count();

                                    ?>
                                    <tr>
                                        <td scope="row"><?= $course->title ?></td>
                                        <td><?= $cert->certNo ?></td>
                                        <td><?= date('jS M Y @ H:i', strtotime($cert->whenCompleted)) ?></td>
                                        <td><?php if(@$dispatched->id && $dispatched->status == 'd'){?><i style="color: #248cab" title="Dispatched" class="fas fa-truck" data-toggle="tooltip" data-placement="top" title="Tooltip on top"></i><?php }?></td>
                                        <td>
                                            <a class="btn btn-primary" href="<?= SITE_URL ?>certificate/<?= $course->slug ?>-certificate?id=<?= $cert->id ?>" target="_blank" style="width:38%;font-size:13px;">
                                                <i class="far fa-file-pdf" style="margin-right:7px;"></i> <span class="d-none d-md-inline-block">View PDF</span>
                                            </a>
                                            <?php
                                            if($order == 0) {
                                                if(@$certificateCoupon && $certificateCoupon->totalUses == 0){
                                                    ?>
                                                    <a class="btn btn-primary open-freeCertificate" href="javascript:;"  data-toggle="modal" data-target="#freeCertificate" data-id="<?= $cert->id ?>" style="width:60%;font-size:13px;  background-color: #b2d489; border-color: #b2d489;">
                                                        <i class="fa fa-check" style="margin-right:7px;"></i> <span class="d-none d-md-inline-block">GET FOR FREE</span>
                                                    </a>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <a class="btn btn-primary" href="<?= SITE_URL ?>ajax?c=cart&a=add-certificate&id=<?= $cert->id ?>" target="_blank" style="width:60%;font-size:13px;">
                                                        <i class="fa fa-shopping-basket" style="margin-right:7px;"></i> <span class="d-none d-md-inline-block">Order Certificate</span>
                                                    </a>
                                                    <?php
                                                }
                                                ?>
                                                <?php
                                            } else {
                                                if(@$certificateCoupon && $certificateCoupon->totalUses == 0){
                                                    ?>
                                                    <a class="btn btn-primary open-freeCertificate" href="javascript:;"  data-toggle="modal" data-target="#freeCertificate" data-id="<?= $cert->id ?>" style="width:60%;font-size:13px;  background-color: #b2d489; border-color: #b2d489;">
                                                        <i class="fa fa-check" style="margin-right:7px;"></i> <span class="d-none d-md-inline-block">Order Again</span>
                                                    </a>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <a class="btn btn-primary" href="<?= SITE_URL ?>ajax?c=cart&a=add-certificate&id=<?= $cert->id ?>" target="_blank" style="width:60%;font-size:13px;background-color: #a3cd8d;border-color: #a3cd8d;">
                                                        <i class="fa fa-check" style="margin-right:7px;"></i> <span class="d-none d-md-inline-block">Order Again</span>
                                                    </a>
                                                    <?php
                                                }
                                                ?>

                                                <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>


                            <div id="mobileCerts">
                                <?php
                                foreach($certs as $cert) {
                                    $course = ORM::for_table("courses")->find_one($cert->courseID);
                                    $order = ORM::For_table("orderItems")->where("certID", $cert->id)->count();
                                    ?>
                                    <div class="item">
                                        <p><strong><?= $course->title ?></strong></p>
                                        <p><?= $cert->certNo ?></p>
                                        <p class="date">
                                            <em><?= date('jS M Y @ H:i', strtotime($cert->whenCompleted)) ?></em>
                                        </p>
                                        <p>
                                            <a class="btn btn-primary" href="<?= SITE_URL ?>certificate/<?= $course->slug ?>-certificate?id=<?= $cert->id ?>" target="_blank" style="width:38%;font-size:13px;">
                                                <i class="far fa-file-pdf" style="margin-right:7px;"></i> View PDF
                                            </a>
                                            <?php
                                            if($order == 0) {
                                                if(@$certificateCoupon && $certificateCoupon->totalUses == 0){
                                                    ?>
                                                    <a class="btn btn-primary" href="javascript:;"  data-toggle="modal" data-target="#freeCertificate" style="width:60%;font-size:13px;">
                                                        <i class="fa fa-shopping-basket" style="margin-right:7px;"></i> <span class="d-none d-md-inline-block">Get For Free</span></a>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <a class="btn btn-primary" href="<?= SITE_URL ?>ajax?c=cart&a=add-certificate&id=<?= $cert->id ?>" target="_blank" style="width:60%;font-size:13px;">
                                                        <i class="fa fa-shopping-basket" style="margin-right:7px;"></i> <span class="d-none d-md-inline-block">Order Certificate</span></a>
                                                    <?php
                                                }
                                                ?>
                                                <?php
                                            } else {
                                                if(@$certificateCoupon && $certificateCoupon->totalUses == 0){
                                                    ?>
                                                    <a class="btn btn-primary" href="javascript:;"  data-toggle="modal" data-target="#freeCertificate" style="width:60%;font-size:13px;">
                                                        <i class="fa fa-shopping-basket" style="margin-right:7px;"></i> <span class="d-none d-md-inline-block">Order Again</span></a>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <a class="btn btn-primary" href="<?= SITE_URL ?>ajax?c=cart&a=add-certificate&id=<?= $cert->id ?>" target="_blank" style="width:60%;font-size:13px;background-color: #a3cd8d;border-color: #a3cd8d;">
                                                        <i class="fa fa-check" style="margin-right:7px;"></i> <span class="d-none d-md-inline-block">Order Again</span></a>
                                                    <?php
                                                }
                                                ?>

                                                <?php
                                            }
                                            ?>

                                        </p>
                                    </div>
                                    <?php

                                }
                                ?>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <div class="modal fade basket signIn" id="freeCertificate" tabindex="-1" role="dialog" aria-labelledby="basketTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <a class="btn-close" data-dismiss="modal">X</a>
                    <p class="popup-title text-center">Delivery Address</p>
                </div>
                <div class="modal-body coupon">
                    <div class="loader_wrapper" style="display: none;"><i class="fas fa-spin fa-spinner"></i></div>
                    <p>So we know where to send your printed items to.</p>
                    <form name="freeCertificate">
                        <!--                        <div class="row">-->
                        <!--                            <div class="col-md-8 pr-0">-->
                        <!--                                <input type="text" name="postcode" id="postcode" class="form-control" placeholder="Enter Your Postcode">-->
                        <!--                            </div>-->
                        <!--                            <div class="col-md-4 pl-0">-->
                        <!--                                <button type="button" class="btn btn-primary btn-block" style="padding: 12px;">Find Address</button>-->
                        <!--                            </div>-->
                        <!--                            <div class="postCodeMessage col-12" style="display: none;"></div>-->
                        <!--                            <div class="postCodeAddress col-12" style="display: none;">-->
                        <!--                                <select class="form-control mt-4" id="myAddresses" style="height: auto; padding: 10px 15px;">-->
                        <!--                                    <option value="">Please select your address</option>-->
                        <!--                                </select>-->
                        <!--                            </div>-->
                        <!--                        </div>-->

                        <div class="row mt-4">
                            <div class="col-6"><input type="text" name="address1" id="address1" class="form-control" placeholder="House Name/No" required value="<?= $lastOrder->address1 ?? null?>"></div>
                            <div class="col-6"><input type="text" name="address2" id="address2" class="form-control" placeholder="Address Line 2" required value="<?= $lastOrder->address2 ?? null?>"></div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-6"><input type="text" name="address3" id="address3" class="form-control" placeholder="Address Line 3" value="<?= $lastOrder->address3 ?? null?>"></div>
                            <div class="col-6"><input type="text" name="city" id="city" class="form-control" placeholder="Town/City" required value="<?= $lastOrder->city ?? null?>"></div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-6"><input type="text" name="postcode" id="postcode" class="form-control" placeholder="Postcode" required value="<?= $lastOrder->postcode ?? null?>"></div>
                            <div class="col-6"><input type="text" name="county" id="county" class="form-control" placeholder="County" required value="<?= $lastOrder->county ?? null?>"></div>
                            <!--                            <div class="col-6"><input type="text" name="country" id="country" class="form-control" placeholder="Country" required></div>-->
                        </div>
                        <p class="mt-4 mb-4">By ordering, I confirm that the details provided above are correct and that I understand certificates can take up to 10 working days to arrive. The name printed onto my certificate is the name specified on my account: <?= $this->user->firstname. " " . $this->user->lastname?></p>


                        <input type="hidden" name="certificateID" id="certificateID" value="" />


                        <div class="totals">
                            <button type="submit" class="btn btn-primary extra-radius">Submit</button>
                        </div>
                        <div class="mt-4" id="returnStatus"></div>
                    </form>

                    <?php
                    $this->renderFormAjax("cart", "orderFreeCertificate", "freeCertificate", "#returnStatus", false, true);
                    ?>

                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
        $(document).on("click", ".open-freeCertificate", function () {
            var certId = $(this).data('id');
            $("#certificateID").val(certId);
            // As pointed out in comments,
            // it is unnecessary to have to manually call the modal.
            // $('#addBookDialog').modal('show');
        });
    </script>
<?php include BASE_PATH . 'account.footer.php';?>