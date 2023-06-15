<?php
/* contains functionality for managing currencies and pricing */
class blumePricingController extends Controller {

    public function __construct()
    {

        $this->post = $_POST;
        $this->get = $_GET;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;

        // if admin is not logged in then dont let them do anything
        if (CUR_ID == "") {
            header('Location: '.SITE_URL.'blume/login');
            exit;
        }
    }

    public function addCurrency() {

        $item = ORM::for_table("currencies")->create();

        $fields = array("code", "short", "taxPercent", "postZipWording");

        foreach($fields as  $field) {

            $item->$field = $this->post[$field];

        }

        $item->save();

        // add currency to all courses
        $this->populatePricingAllCourses();

        // tell user this is complete
        $this->setAlertSuccess("Currency was successfully added");
        $this->redirectJSDelay(SITE_URL.'blume/currencies', '2000');

    }

    public function editCurrency() {

        $item = ORM::for_table("currencies")->find_one($this->get["id"]);

        $fields = array("code", "short", "taxPercent", "postZipWording", "cert1", "cert2", "cert3", "cert4", "cert5", "cert6", "cert7", "cert8", "cert9", "cert10", "prem1", "prem12", "trialDays", "trialStatus", "convRate", "trialJobBoard", "trialCareerRadar", "trialCerts", "trialActiveCourses");

        foreach($fields as  $field) {

            if (strpos($field, 'cert') !== false) {
                if($this->post[$field] == "") {
                    $item->$field = "0.00";
                } else {
                    $item->$field = $this->post[$field];
                }
            } else {
                $item->$field = $this->post[$field];
            }

        }

        $item->save();


        // tell user this is complete
        $this->setAlertSuccess("Currency was successfully updated");
        $this->redirectJSDelay(SITE_URL.'blume/currencies', '2000');

    }

    public function editCurrencyCatRefs() {


        $delete = ORM::for_table("currenciesDynamicRefCatPricing")->where("currencyID", $this->get["id"])->delete_many();

        $count = 0;


        while($count <= $this->post["refCount"]) {

            $count ++;

            if($this->post["price_$count"] != "") {


                $item = ORM::for_table("currenciesDynamicRefCatPricing")->create();

                $item->price = $this->post["price_$count"];
                $item->ref = $this->post["ref_$count"];
                $item->currencyID = $this->get["id"];

                $item->save();

            }

        }


        // tell user this is complete
        $this->setAlertSuccess("Catalogue ref codes were successfully updated");
        $this->redirectJSDelay(SITE_URL.'blume/currencies', '2000');

    }

    public function bulkEditPricing() {

        $items = ORM::for_table("coursePricing")
            ->where("currencyID", $this->post["currency"])
            ->where("price", $this->post["priceSource"])
            ->find_many();

        foreach($items as $item) {

            $update = ORM::for_table("coursePricing")->find_one($item->id);

            $update->price = $this->post["priceNew"];
            $update->set_expr("whenUpdated", "NOW()");

            $update->save();

        }

        $this->setAlertSuccess("Course pricing successfully updated in bulk.");

    }

    public function populateInitialPricing($course) {

        // check if pricing exists for a course, add if not
        $currencies = ORM::for_table("currencies")->find_many();

        foreach($currencies as $currency) {

            $pricing = ORM::For_table("coursePricing")->where("courseID", $course->id)->where("currencyID", $currency->id)->find_one();

            if($pricing->id == "") {

                $item = ORM::for_table("coursePricing")->create();

                $item->courseID = $course->id;
                $item->currencyID = $currency->id;
                $item->set_expr("whenUpdated", "NOW()");
                $item->price = $course->price;

                $item->save();

            }

        }

    }

    public function populatePricingAllCourses() {

        exit;

        // only to be used to populate initial pricing
        $courses = ORM::for_table("courses")->find_many();

        foreach($courses as $course) {

            $this->populateInitialPricing($course);

        }

    }

    public function editPricingOption() {

        $item = ORM::For_table("coursePricing")->find_one($this->post["id"]);

        $item->price = $this->post["price"];
        $item->available = $this->post["available"];

        $item->save();

        $this->setAlertSuccess("Pricing successfully updated");

    }

    public function deletePricingOption() {

        $item = ORM::for_table("coursePricing")->find_one($this->get["id"]);
        $item->delete();

    }

    public function deleteCurrency() {

        // delete course pricing
        $delete = ORM::for_table("coursePricing")->where("currencyID", $this->get["id"])->delete_many();

        // delete currency
        $item = ORM::for_table("currencies")->find_one($this->get["id"]);
        $item->delete();

    }

}