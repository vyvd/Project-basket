<?php
require_once(__DIR__ . '/mediaController.php');

class journeyCategoryController extends Controller {

    /**
     * @var mediaController
     */
    protected $medias;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->medias = new mediaController();
    }


    public function getCategoryImage(int $catId, $size = 'full')
    {
        $media = $this->medias->getMedia(journeyCategoryController::class, $catId, 'main_image');

        if (@$media['url']) {
            $url = $media['url'];
            if ($size != 'full') {
                $url = str_replace($media['fileName'],
                    $size . '/' . $media['fileName'], $url);
            }
        } else {
            $url = '';
        }

        return $url;
    }

    public function getParentCategories(){
        $items = ORM::for_table("journeyCategories")
            ->where_null("parentID")
            ->find_many();

        return $items;
    }

    public function getSubCategories($categoryID){
        $items = ORM::for_table("journeyCategories")
            ->where("parentID", $categoryID)
            ->find_many();
        return $items;
    }

    public function getJourneyCategoryEdit() {

        return ORM::for_table("journeyCategories")->find_one($this->get["id"]);

    }

    public function getCategoryByOldID($oldID)
    {

        return ORM::for_table("journeyCategories")
            ->where("oldID", $oldID)
            ->find_one();
    }

    public function getCategoryByTitle($title)
    {
        $category = ORM::for_table("journeyCategories")
            ->where("title", $title)
            ->find_one();
        if(empty($category)) {
            $category = ORM::for_table("journeyCategories")->create();
            $category->title = $title;
            $category->slug = strtolower(str_replace(" ","-", $category->title));
            $category->save();
        }
        return $category;
    }
}