<?php
require_once(__DIR__ . '/mediaController.php');
require_once(__DIR__ . '/blumeNewController.php');
require_once(__DIR__ . '/journeyCategoryController.php');

class blumeJourneysController extends Controller
{
    /**
     * @var mediaController
     */
    protected $medias;

    /**
     * @var blumeNewController
     */
    protected $blumeNew;

    public function __construct()
    {

        $this->post = $_POST;
        $this->get = $_GET;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;
        $this->medias = new mediaController();
        $this->blumeNew = new blumeNewController();
        $this->journeyCategory = new journeyCategoryController();
        $this->table = "journeys";

        // if admin is not logged in then dont let them do anything
        if (CUR_ID == "") {
            header('Location: ' . SITE_URL . 'blume/login');
            exit;
        }

    }

    public function addJourney() {

        $item = ORM::For_table($this->table)->create();

        $item->title = $this->post["title"];
        $item->price = $this->post["price"];
        $item->slug = $this->createSlug($this->post["title"]);
        $item->set_expr("whenAdded", "NOW()");

        $item->save();

        $this->blumeNew->recordLog('created a new journey: '.$this->post["title"]);

        $this->redirectJS(SITE_URL.'blume/journeys/edit?id='.$item->id());


    }

    public function editJourney() {

        $item = ORM::For_table($this->table)->find_one($this->post["id"]);

        $fields = array("title", "price", "slug", "hidden", "description", "careers");

        foreach($fields as $field) {

            $item->$field = $this->post[$field];

        }

        // upload featured image
        if ($this->checkFileUploadSelected() == true) {
            $sizes = ['large', 'medium', 'thumb'];
            $model = [
                'type' => 'journeyController',
                'id'   => $this->post["id"]
            ];

            $media = $this->medias->uploadFile($sizes, $model, 'uploaded_file', 'featured_img', true);



        }

        $item->save();

        $this->blumeNew->recordLog('edited a journey: '.$this->post["title"]);

        $this->setAlertSuccess("Journey successfully edited");


    }

    public function editJourneyCourses() {

        $item = ORM::For_table($this->table)->find_one($this->post["id"]);

        $item->courses = implode(",", $this->post["courses"]);

        $item->save();

        $this->setAlertSuccess("Assigned courses successfully updated");

    }

    public function deleteJourney() {

        // check enrollments, if 0 then delete
        $enrollments = ORM::for_table("coursesAssigned")->where("journeyID", $this->get["id"])->count();

        if($enrollments == 0) {

            $item = ORM::for_table($this->table)->find_one($this->get["id"]);
            $item->save();

        }

    }

    public function addJourneyCategory()
    {

        if ($this->post["title"] == "") {
            $this->setAlertSuccess("Please ensure all required data is present.");
            exit;
        }

        $item = ORM::for_table("journeyCategories")->create();

        $item->title = $this->post["title"];
        $item->parentID = @$this->post["parentID"] ? $this->post["parentID"]
            : null;
        $item->icon = $this->post["icon"];
        $item->meta_title = $this->post["meta_title"];
        $item->meta_keywords = $this->post["meta_keywords"];
        $item->meta_description = $this->post["meta_description"];
        $item->slug = $this->createSlug($this->post["title"]);

        $item->save();

        $this->blumeNew->recordLog('added a new journey category: '.$this->post["title"]);

        $this->setAlertSuccess("Journey category successfully created");
        $this->redirectJS(SITE_URL.'blume/journeys/categories');

    }

    public function editJourneyCategory()
    {

        if ($this->post["title"] == "") {
            $this->setAlertDanger("Please ensure all required data is present.");
            exit;
        }

        $item = ORM::for_table("journeyCategories")
            ->find_one($this->post["itemID"]);

        $item->title = $this->post["title"];
        $item->parentID = @$this->post["parentID"] ? $this->post["parentID"]
            : null;
        $item->showOnHome = $this->post["showOnHome"];
        $item->description = $this->post["description"];
        $item->meta_title = $this->post["meta_title"];
        $item->meta_keywords = $this->post["meta_keywords"];
        $item->meta_description = $this->post["meta_description"];
        $item->icon = $this->post["icon"];

        $item->save();

        // Update image
        if ($this->checkFileUploadSelected() == true) {
            $sizes = ['large', 'medium', 'thumb'];
            $model = [
                'type' => journeyCategoryController::class,
                'id'   => $this->post["itemID"]
            ];
            $this->medias->uploadFile($sizes, $model, 'uploaded_file',
                'main_image', true);
        }

        $this->blumeNew->recordLog('edited the following journey category: '.$this->post["title"]);

        $this->setAlertSuccess("Journey category successfully updated");

    }

}