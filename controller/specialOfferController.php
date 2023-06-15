<?php
class specialOfferController extends Controller {

    public function getOffer() {

        $slug = rtrim($_GET["request"], '/');
        return ORM::for_table("offerPages")->where("slug", $slug)->order_by_Desc("id")->find_one();


    }



}