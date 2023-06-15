<?php

class supportController extends Controller {



    public function getHelpArticle() {

        return ORM::for_table("helpArticles")->where("slug", $_GET["request"])->find_one();

    }

    public function getResource() {

        return ORM::for_table("resources")->where("slug", $_GET["request"])->find_one();

    }

    public function latestHelpArticles($limit = 99) {

        return ORM::for_table("helpArticles")->order_by_desc("whenAdded")->limit($limit)->find_many();

    }

    public function latestResources($limit = 99) {

        return ORM::for_table("resources")->order_by_desc("whenAdded")->limit($limit)->find_many();

    }

    public function relatedHelpArticles($article) {

        return ORM::for_table("helpArticles")->order_by_expr("RAND()")->find_many();

    }
    public function getOrderByID($id)
    {
        return ORM::for_table('courseAccesses')->find_one($id);
    }

    public function contact() {
        
        $this->validateValues(array("firstname", "email", "phone", "subject", "message"));
        
        // reCAPTCHA validation
        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {

            // Google secret API
            $secretAPIkey = '6LcDY_olAAAAADBxM-GyyuQ-h6FAewtH4vRtilFY';

            // reCAPTCHA response verification
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secretAPIkey . '&response=' . $_POST['g-recaptcha-response']);

            // Decode JSON data
            $response = json_decode($verifyResponse);
            if ($response->success) {

            } else {
                $this->setToastDanger("Robot verification failed, please try again!");
                exit;
            }
        } else {
            $this->setToastDanger("Make sure you have checked the recaptcha!");
            exit;
        }

        $message = '
        <p>
        <strong>Name:</strong> '.$this->post["firstname"].' '.$this->post["lastname"].'<br />
        <strong>Email:</strong> '.$this->post["email"].'<br />
        <strong>Phone:</strong> '.$this->post["phone"].'<br />
        <strong>Country:</strong> '.$this->post["site"].'<br />
        <strong>Subject:</strong> '.$this->post["subject"].'<br />
        <strong>Message:</strong> '.$this->post["message"].'<br />
</p>
        
  
' . $this->renderHtmlEmailButton("Reply via Email", 'mailto:'.$this->post["email"]);

        $emailTo = 'support@newskillsacademy.co.uk';

        if(SITE_TYPE != "uk") {
            $emailTo = 'support@newskillsacademy.com';
        }
        $this->sendEmail($emailTo, $message,
            $this->post["subject"]." from " . $this->post["firstname"].' '.$this->post["lastname"] . " via "
            . SITE_NAME, $this->post["email"]);

        // Email to user
        $emailTemplate = ORM::for_table('email_templates')
            ->where('template','support_enquiry')
            ->find_one();
        $variables = [
            '[FIRST_NAME]' => $this->post["firstname"],
            '[LAST_NAME]' => $this->post["lastname"],
        ];
        $message = $emailTemplate->description;
        $subject = $emailTemplate->subject;
        foreach ($variables as $k=>$v){
            $message = str_replace($k, $v, $message);
            $subject = str_replace($k, $v, $subject);
        }

        $this->sendEmail($this->post["email"], $message, $subject);

        $this->setToastSuccess("Thank you, we will be in touch shortly.");

        // reset form to original state
        ?>
        <script>
            contact.reset();
        </script>
        <?php

    }

    public function newTicket() {

        $this->validateValues(array("firstname", "email"));

        $message = '
        <p>
        <strong>Name:</strong> '.$this->post["firstname"].' '.$this->post["lastname"].'<br />
        <strong>Email:</strong> '.$this->post["email"].'<br />
        <strong>Phone:</strong> '.$this->post["phone"].'<br />
        <strong>Message:</strong> '.$this->post["message"].'<br />
</p>
        
   

' . $this->renderHtmlEmailButton("Reply via Email", 'mailto:'.$this->post["email"]);

        $emailTo = 'support@newskillsacademy.co.uk';

        if(SITE_TYPE != "uk") {
            $emailTo = 'support@newskillsacademy.com';
        }

        $this->sendEmail($emailTo, $message, "Support enquiry from " . $this->post["firstname"].' '.$this->post["lastname"] . " via ". SITE_NAME, $this->post["email"]);

        $this->setToastSuccess("Thank you, we will be in touch shortly.");

    }

    public function courseFeedback() {

        $this->validateValues(array("firstname", "email"));

        $message = '
        <p>
        <strong>Name:</strong> '.$this->post["firstname"].' '.$this->post["lastname"].'<br />
        <strong>Email:</strong> '.$this->post["email"].'<br />
        <strong>Course:</strong> '.$this->post["course"].'<br />
        <strong>Feedback Message:</strong> '.$this->post["message"].'<br />
</p>
        
   

' . $this->renderHtmlEmailButton("Reply via Email", 'mailto:'.$this->post["email"]);

        $this->sendEmail("support@newskillsacademy.co.uk", $message, "Support enquiry from " . $this->post["firstname"].' '.$this->post["lastname"] . " via ". SITE_NAME, $this->post["email"]);

        $this->setToastSuccess("Thank you, we will be in touch shortly.");

    }
   

}