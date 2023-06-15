<?php


class jobsController extends Controller {

    public function jobClicks()
    {

        $orderItems = ORM::for_table("accounts")->where("id", $this->post["accountID"])->find_one();
        $item = ORM::for_table("jobClicks")->create();
        $item->accountID = $this->post["accountID"];
        $item->email = $orderItems->email;
        $item->name = $orderItems->firstname;
        $item->jobID = $this->post["jobID"];
        $item->set_expr("whenClicked", "NOW()");
        $item->save();

    }

    public function clickAmount()
    {
        $item = ORM::for_table("jobs")->where("id", $this->post["jobID"])->find_one();
        $item->set(array(
            'clickAmount' => $this->post["clickAmount"],
        ));

        $item->save();

    }

}

?>