<?php
class messageController extends Controller {

    public function countUnread() {

        return ORM::for_table("messages")->where("recipientID", CUR_ID_FRONT)->where("seen", "0")->count();

    }

    public function renderInbox($filter = "inbox") {

        $items = ORM::for_table('messagesQueue')->order_by_desc('whenSent')->limit(10)->find_many();

        foreach($items as $item) {
            ?>
            <div class="col-12 regular-full message-outer" id="message<?= $item->id ?>">
                <div class="row align-items-center">
                    <div class="message-box white-rounded">
                        <div class="message-head" id="heading<?= $filter.$item->id ?>" data-toggle="collapse" data-target="#message<?= $filter.$item->id ?>" aria-expanded="false" aria-controls="message<?= $filter.$item->id ?>">
                            <a class="btn btn-primary"><?= $this->ago($item->whenSent) ?></a>
                            <h3><?= $item->subject ?></h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="collapse" aria-labelledby="heading<?= $filter.$item->id ?>" id="message<?= $filter.$item->id ?>" data-parent="#accordion<?=$filter?>">
                            <div class="card card-body">
                                <?php echo $item->message; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php

        }

        if(count($items) == 0) {
            ?>
            <div class="col-12 text-center">
                <p style="margin-top:50px;margin-bottom:50px;">
                    There are currently no messages matching this filter.
                </p>
            </div>
            <?php
        }

    }

    public function sendNcfe() {

        // current users account
        $account = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);

        $fields = array("subject", "message");

        $this->validateValues($fields);

        if($this->post["type"] == "cs") {

            // send directly to cs
            $message = '
<p>An NCFE student has sent in the following enquiry:</p>
        <p>
        <strong>Name:</strong> '.$account->firstname.' '.$account->lastname.'<br />
        <strong>Email:</strong> '.$account->email.'<br />
        <strong>Subject:</strong> '.$this->post["subject"].'<br />
        <strong>Message:</strong> '.$this->post["message"].'<br />
</p>
        
   

' . $this->renderHtmlEmailButton("Reply via Email", 'mailto:'.$account->email);

            $this->sendEmail("support@newskillsacademy.co.uk", $message,
                $this->post["subject"]." from " . $account->firstname.' '.$account->lastname . " via "
                . SITE_NAME, $account->email);

        } else {

            // send to tutor but prompt CS to approve it first
            $message = ORM::for_table("messages")->create();

            $message->csApproved = "0";
            $message->set_expr("whenSent", "NOW()");
            $message->recipientID = $account->tutorID;
            $message->userID = CUR_ID_FRONT;
            $message->subject = $this->post["subject"];
            $message->message = $this->post["message"];

            $message->save();

            // prompt CS team to approve this message before the tutor sees it
            $message = '
<p>An NCFE student has sent in the following enquiry to their tutor, but you need to approve this before the tutor can see it:</p>

        <p>
            <strong>Name:</strong> '.$account->firstname.' '.$account->lastname.'<br />
            <strong>Email:</strong> '.$account->email.'<br />
            <strong>Subject:</strong> '.$this->post["subject"].'<br />
            <strong>Message:</strong> '.$this->post["message"].'<br />
        </p>
        
' . $this->renderHtmlEmailButton("Approve via Admin", SITE_URL.'blume/ncfe/messages');

            $this->sendEmail("support@newskillsacademy.co.uk", $message,
                $this->post["subject"]." from " . $account->firstname.' '.$account->lastname . " via "
                . SITE_NAME, $account->email);

        }

        $this->setToastSuccess("Thank you, we will be in touch shortly.");


    }

    public function sendNcfeTutor() {

        // current tutor account
        $account = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);
        $student = ORM::for_table("accounts")->find_one($this->post["recipientID"]);

        if($account->isTutor != "1") {
            exit;
        }

        $fields = array("subject", "message");

        $this->validateValues($fields);

        $message = ORM::for_table("messages")->create();

        $message->csApproved = "1";
        $message->set_expr("whenSent", "NOW()");
        $message->recipientID = $this->post["recipientID"];
        $message->userID = CUR_ID_FRONT;
        $message->subject = $this->post["subject"];
        $message->message = $this->post["message"];

        $message->save();

        // prompt CS team to approve this message before the tutor sees it
        $message = '
        <p>Hi '.$student->firstname.',</p>

        <p>
            You have just the following message from your tutor:
        </p>
        
        <p>
            <em>"'.$this->post["message"].'"</em>
        </p>
        
        <p>Please reply to this message from within your account.</p>
        
' . $this->renderHtmlEmailButton("My Account", SITE_URL.'dashboard/ncfe/messages');

        $this->sendEmail($student->email, $message,
            $this->post["subject"]." from " . $account->firstname.' '.$account->lastname . " via "
            . SITE_NAME, $account->email);

        $this->setToastSuccess("Your message was successfully sent.");

    }


}