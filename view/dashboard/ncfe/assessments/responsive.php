<?php
$css = array("dashboard.css");
$pageTitle = "Documents";
include BASE_PATH . 'account-ncfe.header.php';
$this->setControllers(array("accountAssignment", "course", "courseModule"));

$assignmentModules = ORM::for_table('accountAssignments')
    ->where('accountID', CUR_ID_FRONT)
    ->where('status', 0)
    ->order_by_asc('moduleID')
    ->find_many();

?>
    <script src="https://js.stripe.com/v3/"></script>
    <style>

    </style>
    <section class="page-title with-nav">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1><?= $pageTitle ?></h1>
                    <?php include ('includes/basket-vue.php') ?>
                    <ul class="nav navbar-nav inner-nav nav-tabs">
                        <li class="nav-item link">
                            <a class="nav-link active show" href="#submit-assessments" data-toggle="tab">Submit for Marking</a>
                        </li>
                    </ul>

                </div>
            </div>
<!--            <a href="javascript:;" class="mainBtn" data-toggle="modal" data-target="#upload">-->
<!--                <i class="fas fa-plus"></i>-->
<!--                Upload-->
<!--            </a>-->


        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div v-if="paymentProcessing" class="loader_wrapper"><i class="fas fa-spin fa-spinner"></i></div>
            <form name="checkout" @submit="validateForm" id="payment-form">
                <div class="row">
                    <div class="col-12 regular-full">
                        <div class="tab-content container" style="padding:0;">
                            <div id="submit-assessments" class="tab-pane active show">
                                <div class="row assignAccordion">
                                    <div  id="accordion" class="col-12">
                                        <?php
                                        foreach ($assignmentModules as $assignment){
                                            $module = $this->courseModule->getModuleByID($assignment->moduleID);
                                            $title = '';
                                            $status = '';
                                            if($module->parentID){
                                                $parentModule = $this->courseModule->getModuleByID($module->parentID);
                                                $title = $parentModule->title.' - ';
                                            }

                                            if($assignment) {
                                                $htmlStatus = "<label class='status payment_pending'>Payment Pending</label>";
                                                $status = $assignment->status;
                                            }else {
                                                $htmlStatus = '<label class="status">Not Sent</label>';
                                            }
                                            $uploadUserFiles = $this->accountAssignment->getUserFilesByAssignmentID($assignment->id);
                                            ?>
                                            <div class="card grey-bg-box">
                                                <div class="card-header" id="module<?= $module->id?>">
                                                    <h5 class="mb-0">
                                                        <a class="faq-title collapsed" data-toggle="collapse" data-target="#module<?= $module->id?>Data" aria-expanded="true" aria-controls="module<?= $module->id?>Data">
                                                            <?= $title.$module->title ?>
                                                        </a>
                                                        <div class="align-items-center tickbox extra-radius float-right">
                                                            <div class="custom-control custom-checkbox">
                                                                <input @change="updateItem($event, '<?= $assignment->id ?>')" type="checkbox" class="custom-control-input addPrinted" v-model="cartItems" value="<?= $assignment->id?>"  id="printed<?= $assignment->id?>">
                                                                <label class="custom-control-label" for="printed<?= $assignment->id?>">&nbsp;</label>
                                                            </div>
                                                        </div>
                                                    </h5>
                                                </div>

                                                <div id="module<?= $module->id?>Data" class="collapse" aria-labelledby="module<?= $module->id?>" data-parent="#accordion<?= $courseID?>">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <?php
                                                                if(count($uploadUserFiles) >= 1) {
                                                                    ?>
                                                                    <div class="userAssignmentSection">
                                                                        <h3>My Submitted Files</h3>
                                                                        <ul class="row">
                                                                            <?php
                                                                            foreach ($uploadUserFiles as $file) {
                                                                                ?>
                                                                                <li class="col-md-6"><a target="_blank" href="<?php echo AWSService::getFromS3('assignments/'.CUR_ID_FRONT.'/'.$module->id.'/'.$file->fileName); ?>"><?= $file->fileName ?? $file->title ?></a></li>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </ul>
                                                                    </div>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="col-md-12">

                                                                <?php if($status != '' && $status == 0) { ?>
                                                                    <div class="row">
                                                                        <div class="col-12 text-center">
                                                                            <button @click="updateItem($event, '<?= $assignment->id ?>', 'add')" type="button" class="btn btn-danger btn-lg add_cart" rel="<?= $module->id ?>">Add To Order</button>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="row white-rounded p-4">
                                    <div class="col-12">
                                        <div class="alert alert-danger" v-if="errors.length">
                                            <b>Please correct the following error(s):</b>
                                            <ul>
                                                <li v-for="error in errors">{{ error }}</li>
                                            </ul>
                                        </div>
                                        <div class="card-payments-method">
                                            <h3 class="mb-4">Select Your Payment Method</h3>
                                            <!--Payment Method-->
                                            <div class="payment-method">
                                                <ul class="nav nav-tabs " id="myTab">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" @click="activePaymentMethod('stripe')" id="visa-tab" data-name="Visa" href="#stripetab">
                                                            <img src="<?= SITE_URL ?>assets/images/visa.png" alt="visa" />
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" @click="activePaymentMethod('paypal')" id="paypal-tab" data-name="PayPal" href="#paypaltab">
                                                            <img src="<?= SITE_URL ?>assets/images/paypal.png" alt="paypal" />
                                                        </a>
                                                    </li>
                                                    <li class="nav-item apple-button">
                                                        <a class="nav-link" @click="validateForm('applepay')" id="apple-tab" data-name="Apple" href="#applepaytab">
                                                            <!-- <a class="nav-link" @click="validateForm('applepay')">  -->
                                                            <img id='apple-tab' src="<?= SITE_URL ?>assets/images/apple.png" alt="apple" />
                                                        </a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content" id="myTabContent">
                                                    <div class="tab-pane fade show active"  id="stripetab" role="tabpanel" aria-labelledby="visa-tab">

                                                        <!-- Used to display form errors -->
                                                        <div v-if="cardErrors != ''" id="card-errors" class="mb-2 alert alert-danger" role="alert">{{cardErrors}}</div>

                                                        <div class="row">
                                                            <div class="form-group col-md-12">
                                                                <div id="card-number-element" class="field stripe_input_fields form-control"></div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="form-group col-md-6">
                                                                <div id="card-expiry-element" class="field stripe_input_fields form-control"></div>
                                                            </div>
                                                            <div class="form-group col-md-6">
                                                                <div id="card-cvc-element" class="field stripe_input_fields form-control"></div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade" id="paypaltab" role="tabpanel" aria-labelledby="paypal-tab">
                                                        <div class="row">
                                                            <div class="col-12 text-center">
                                                                <img @click="validateForm" style="max-width: 100%;" src="<?= SITE_URL?>assets/images/paypal-checkout.png">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade text-center" id="applepaytab" role="tabpanel" aria-labelledby="apple-tab">
                                                        <!--                                            <div id="payment-request-button">-->
                                                        <!---->
                                                        <!--                                            </div>-->
                                                        <div v-if="paymentType=='applepay'" class="">
                                                            <img @click="submitApplePayPayment()" style="width: 200px; max-width: 100%" src="<?= SITE_URL?>assets/images/apple-pay.png">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <div class="custom-control custom-checkbox terms">
                                                    <input type="checkbox" class="custom-control-input" id="terms" v-model="terms" name="terms" value="1">
                                                    <label class="custom-control-label"  for="terms" style="font-size:20px;">
                                                        I accept the <a href="<?= SITE_URL ?>terms-website-use" target="_blank" class="underlined"><span>terms & conditions</span></a>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-12 totaltopay">
                                            <span>Total to Pay <strong class="ajaxTotalPrice">{{totalPrice}}</strong></span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group text-center pay-button">
                                            <input v-if="paymentType=='applepay'" @click="submitApplePayPayment()" type="button" :disabled="paymentProcessing == 1" value="Pay Now" class="btn btn-primary btn-lg extra-radius">
                                            <input v-else type="submit" :disabled="paymentProcessing == 1" value="Pay Now" class="btn btn-primary btn-lg extra-radius">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group col-md-12">
                                            <div class="btm-logo d-flex align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-lock"></i>
                                                    <p><strong>Secure Payments </strong><br/>Your order is safe and secure. We use the latest SSL encryption for all transactions</p>
                                                </div>
                                                <div>
                                                    <img src="<?= SITE_URL ?>assets/images/checkout-trupiolet.png" alt="trust piolet">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <div class="modal fade" id="upload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="height:auto;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel" style="display:block;width:100%;">Upload Document</h4>
                </div>
                <form name="addNewAssignment">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Select Module:</label>
                            <select name="" class="form-control" required>
                                <option value="">Select Module</option>
                                <?php
                                if(count($assignmentModules) >= 1){
                                    foreach ($assignmentModules as $module) {
                                        $title = $module->title;
                                        if($module->parentID) {
                                            $parentModule = $this->courseModule->getModuleByID($module->parentID);
                                            $title = $parentModule->title . ' --> ' . $title;
                                        }
                                        ?>
                                        <option value="<?= $module->id ?>"><?= $title ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Select file:</label>
                            <input type="file" name="file" style="padding:0;" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
                <?php
                $this->renderFormAjax("accountDocument", "upload-document", "addNewAssignment");
                ?>
            </div>
        </div>
    </div>


<?php
include __DIR__ . '/includes/checkout-assessment-vue.php';
include BASE_PATH . 'account.footer.php';?>