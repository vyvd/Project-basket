<?php
class giftController extends Controller {

    public function getCourses() {

        return ORM::for_table("courses")
            ->select("id")
            ->select("title")
            ->where("hidden", "0")
            ->where_not_equal("is_lightningSkill", "1")
            ->order_by_asc("title")
            ->find_many();

    }

    public function courseSelectionPrice() {

        $course = ORM::for_table("courses")->find_one($this->get["id"]);
        if(@$this->get["action"] && ($this->get["action"] == 'json')){

            // take into account affiliate discounting
            $excludedCourses = explode(",", $_SESSION["excludedCourses"]);
            if ($_SESSION["affiliateDiscount"] != ""
                && !in_array($course->id, $excludedCourses)
            ) {


                $original = $course->price;
                $discounted = $course->price;

                if ($_SESSION["affiliateDiscountType"] == "fixed") {
                    $discounted = $discounted
                        - $_SESSION["affiliateDiscount"];
                } else {
                    $discounted = $discounted * ((100
                                - $_SESSION["affiliateDiscount"]) / 100);
                }

                if($_SESSION["affiliateDiscountMax"] != "" && $_SESSION["affiliateDiscountMin"] != "") {


                    if($course->price <= $_SESSION["affiliateDiscountMax"] && $course->price >= $_SESSION["affiliateDiscountMin"]) {
                        $course->price = $discounted;
                    }

                } else if ($_SESSION["affiliateDiscountMax"] != "") {

                    if ($course->price
                        <= $_SESSION["affiliateDiscountMax"]
                    ) {
                        $course->price = $discounted;
                    }

                } else {
                    if ($_SESSION["affiliateDiscountMin"] != "") {

                        if ($course->price
                            >= $_SESSION["affiliateDiscountMin"]
                        ) {
                            $course->price = $discounted;
                        }

                    } else {
                        $course->price = $discounted;
                    }
                }

            }


            $response = [
                    'totalPrice' => $this->price($course->price),
                    'totalPriceAmount' => $course->price,
                    'courseTitle' => $course->title,
            ];
            echo json_encode($response);
            exit();
        }

        ?>
        <script>
            $(".ajaxTotalPrice").html("<?= $this->price($course->price) ?>");
            $( ".giftAmountSelect label" ).removeClass("active");
            $( ".giftPreview .course" ).html('<?= $course->title ?>');
        </script>
        <?php

    }
    public function selectSubscription($return = false) {

        $plan = ORM::for_table("premiumSubscriptionsPlans")
            ->where('months', 12)->find_one();

        $currency = $this->currentCurrency();

        $plan->price = $currency->prem12;

        if($return){
            return $plan;
        }
        if(@$this->get["action"] && ($this->get["action"] == 'json')){
            $response = [
                'totalPrice' => $this->price($plan->price),
                'totalPriceAmount' => $plan->price
            ];
            echo json_encode($response);
            exit();
        }

    }

    public function purchase() {

        $this->validateValues(array("name", "nameRecipient", "message", "firstname", "lastname", "email"));

        $total = $this->post["total"]; // by default we take the user selected value

        if($this->post["courseID"] != "") {
            // then we charge course price
            $course = ORM::for_table("courses")->find_one($this->post["courseID"]);

            $total = $course->price;
        }

        // if we are sending to the users email then we need to make sure we collect that address
        if($this->post["type"] == "deliver") {
            $this->validateValues(array("recipEmail"));
        }

        // create order
        $order = ORM::for_table("orders")->create();

        $order->set(
            array(
                'customerIP' => $this->getUserIP(),
                'firstname' => $this->post["firstname"],
                'lastname' => $this->post["lastname"],
                'email' => $this->post["email"],
                'total' => $total,
                'status' => "completed", // @todo: only until payments are integrated
            )
        );

        if(CUR_ID_FRONT != "") {
            $order->accountID = CUR_ID_FRONT;
        }

        $order->set_expr("whenCreated", "NOW()");
        $order->set_expr("whenUpdated", "NOW()");

        $order->save();

        $orderID = $order->id();

        // generate code
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $code = $randomString;

        // create voucher
        $voucher = ORM::for_table("vouchers")->create();

        $voucher->gifted = "1";
        $voucher->expiry = "2099-01-01";
        $voucher->type = "specific";
        // $voucher->courses = $this->post["courseID"];
        $voucher->valueUpto = $total;
        $voucher->code = $code;
        $voucher->set_expr("whenAdded", "NOW()");
        $voucher->groupID = "5";
        $voucher->giftFrom = $this->post["name"];
        $voucher->giftTo = $this->post["nameRecipient"];
        $voucher->giftMessage = $this->post["message"];
        $voucher->allowCourseSelection = "1";

        $voucher->save();


        // add order item
        $item = ORM::for_table("orderItems")->create();

        $item->set(
            array(
                'orderID' => $orderID,
                'voucherID' => $voucher->id(),
                'course' => '0',
                'price' => $total
            )
        );

        $item->set_expr("whenCreated", "NOW()");

        $item->save();


        // set some session data to use on the completed page
        $_SESSION["giftFrom"] = $this->post["name"];
        $_SESSION["giftTo"] = $this->post["nameRecipient"];
        $_SESSION["giftMessage"] = $this->post["message"];
        $_SESSION["code"] = $code;
        $_SESSION["type"] = $this->post["type"];
        $_SESSION["recipEmail"] = $this->post["recipEmail"];
        $_SESSION["orderID"] = $orderID;
        $_SESSION["email"] = $this->post["email"];

        // redirect to completed page
        $this->redirectJS(SITE_URL.'gift/complete');


    }

    public function saveCertImage() {

        $data = $this->post["data"];

        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        $voucherFileName = $_SESSION["orderID"].'_'.$_SESSION["code"].'.png';
        $voucherFileNamePDF = $_SESSION["orderID"].'_'.$_SESSION["code"].'.pdf';

        // save image
        file_put_contents(TO_PATH_CDN.'generatedVouchers/'.$voucherFileName, $data);

        // save pdf
        $pdfContent = '<img src="'.SITE_URL.'assets/cdn/generatedVouchers/'.$voucherFileName.'" />';

        $content = "<page>".$pdfContent."</page>";

        require_once(__DIR__ . '/../classes/html2pdf-4.4.0/html2pdf.class.php');

        $html2pdf = new HTML2PDF('L','A4', 'en', true, 'UTF-8', array(0, 0, 0, 0));
        $html2pdf->WriteHTML($content);

        $html2pdf->Output(TO_PATH_CDN.'generatedVouchers/pdfs/'.$voucherFileNamePDF, 'F');


        if($_SESSION["type"] == "deliver") { // send to recipient

            $message = '<p>Hi '.$_SESSION["giftTo"].',</p>
        <p>You have been gifted a voucher for New Skills Academy by '.$_SESSION["giftFrom"].'.</p>
        
        <p><em>"'.$this->post["message"].'"</em></p>
        
        <p>
        <img src="'.SITE_URL.'assets/cdn/generatedVouchers/'.$voucherFileName.'" style="width:100%;" />
        </p>
        
        <p>You can claim the voucher by entering your details and following code - <strong>'.$_SESSION["code"].'</strong> - by visiting <a href="'.SITE_URL.'redeem">the following link</a>.</p>
        
        <p>A copy of the voucher is also attached to this email.</p>
       ';

            $message .= $this->renderHtmlEmailButton("Redeem Voucher", SITE_URL.'redeem');

            $this->sendEmailPDFAttachment($_SESSION["recipEmail"], $message, "You've been gifted a voucher from ".$_SESSION["giftFrom"], TO_PATH_CDN.'generatedVouchers/pdfs/'.$voucherFileNamePDF, $voucherFileNamePDF);

        }
        else {

            $message = '<p>Hi '.$_SESSION["giftFrom"].',</p>
        <p>Thank you for purchasing a gifted voucher via New Skills Academy. For reference, your order number is '.$_SESSION["orderID"].'.</p>
        
        <p>
        <img src="'.SITE_URL.'assets/cdn/generatedVouchers/'.$voucherFileName.'" style="width:100%;" />
        </p>
        
        <p>The voucher can be claimed at any time by a person of your choice when they enter the following code - <strong>'.$_SESSION["code"].'</strong> - by visiting <a href="'.SITE_URL.'redeem">the following link</a>.</p>
        
        <p>A copy of the voucher is also attached to this email.</p>
        
       ';

            $message .= $this->renderHtmlEmailButton("Redeem Voucher", SITE_URL.'redeem');

            $this->sendEmailPDFAttachment($_SESSION["email"], $message, "Thank you for purchasing a gift voucher from New Skills Academy", TO_PATH_CDN.'generatedVouchers/pdfs/'.$voucherFileNamePDF, $voucherFileNamePDF);

        }

        ?>
        <a href="<?= SITE_URL ?>assets/cdn/generatedVouchers/<?= $voucherFileName ?>" target="_blank" class="btn btn-primary btn-lg extra-radius">
            Download Voucher (PNG)
        </a>
        <a href="<?= SITE_URL ?>assets/cdn/generatedVouchers/pdfs/<?= $voucherFileNamePDF ?>" target="_blank" class="btn btn-primary btn-lg extra-radius">
            Download Voucher (PDF)
        </a>
        <?php

    }

    public function processPayment($request){
        $total = $request['gift_amount'];
        $giftType = $request["gift_type"];


        if($request["gift_type"] == 'course' && $request["courseID"] != "") {
            // then we charge course price
            $course = ORM::for_table("courses")->find_one($request["courseID"]);
            $total = $course->price;
        }else if($request["gift_type"] == 'subscription'){
            $plan = $this->selectSubscription(true);
            $total = $plan->price;
        }

        // if we are sending to the users email then we need to make sure we collect that address
//        if($this->post["type"] == "deliver") {
//            $this->validateValues(array("recipEmail"));
//        }

        // create order
        $order = ORM::for_table("orders")->create();

        $order->set(
            array(
                'customerIP' => $this->getUserIP(),
                'firstname' => $request["firstname"],
                'lastname' => $request["lastname"],
                'email' => $request["email"],
                'total' => $total,
                'status' => "processing", // @todo: only until payments are integrated
            )
        );

        if(CUR_ID_FRONT != "") {
            $order->accountID = CUR_ID_FRONT;
        }

        $order->set_expr("whenCreated", "NOW()");
        $order->set_expr("whenUpdated", "NOW()");

        $order->save();

        $orderID = $order->id();

        // generate code
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $code = $randomString;

        // create voucher
        $voucher = ORM::for_table("vouchers")->create();

        $voucher->gifted = "1";
        $voucher->expiry = "2099-01-01";
        $voucher->type = $giftType;
        $voucher->courses = $request["courseID"] ?? null;
        $voucher->valueUpto = $total;
        $voucher->code = $code;
        $voucher->style = $request['voucher_style'] ?? 1;
        $voucher->set_expr("whenAdded", "NOW()");
        $voucher->groupID = "0";
        $voucher->giftFrom = $request["name"];
        $voucher->giftTo = $request["nameRecipient"];
        $voucher->giftMessage = $request["message"];
        $voucher->recipType = $request["type"];
        $voucher->recipEmail = $request["recipEmail"];
        $voucher->allowCourseSelection = "1";

        $voucher->save();


        // add order item
        $item = ORM::for_table("orderItems")->create();

        $item->set(
            array(
                'orderID' => $orderID,
                'voucherID' => $voucher->id(),
                'course' => '0',
                'price' => $total
            )
        );

        $item->set_expr("whenCreated", "NOW()");

        $item->save();

        if($order->invoiceNo == "" || $order->invoiceNo == null){
            $invoice = "NSA".time().$orderID;
            $order->invoiceNo = $invoice;
            $order->save();
        }

        return $order;
    }

    public function completePayment($order, $method, $method_title, $transactionID){
        $order->method = $method;
        $order->method_title = $method_title;
        $order->transactionID = $transactionID;
        $order->status = 'completed';
        $order->whenUpdated = date("Y-m-d H:i:s");
        $order->save();

        $orderItem = ORM::for_table('orderItems')->where('orderID', $order->id)->find_one();
        $voucher = ORM::for_table('vouchers')->find_one($orderItem->voucherID);

        // set some session data to use on the completed page
        $_SESSION["giftFrom"] = $voucher->giftFrom;
        $_SESSION["giftTo"] = $voucher->giftTo;
        $_SESSION["giftMessage"] = $voucher->giftMessage;
        $_SESSION["code"] = $voucher->code;
        $_SESSION["type"] = $voucher->recipType;
        $_SESSION["recipEmail"] = $voucher->recipEmail;
        $_SESSION["orderID"] = $order->id;
        $_SESSION["email"] = $order->email;
        $_SESSION["giftStyle"] = $voucher->style;

        $response = [
            'success' => true
        ];
        return $response;
        echo json_encode($response);
        exit();
    }
}