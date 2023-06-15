<?php

require_once(__DIR__ . '/mediaController.php');
require_once(APP_ROOT_PATH . 'database/import/csv/CourseCategoryCsvImport.php');

class courseCategoryController extends Controller {

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
        $media = $this->medias->getMedia(courseCategoryController::class, $catId, 'main_image');

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
        $items = ORM::for_table("courseCategories")
            ->where_null("parentID")
            ->find_many();

        return $items;
    }

    public function getSubCategories($categoryID){
        $items = ORM::for_table("courseCategories")
            ->where("parentID", $categoryID)
            ->find_many();
        return $items;
    }

    public function getCourseCategoryEdit() {

        return ORM::for_table("courseCategories")->find_one($this->get["id"]);

    }

    public function getCategoryByOldID($oldID)
    {

        return ORM::for_table("courseCategories")
            ->where("oldID", $oldID)
            ->find_one();
    }

    public function getCategoryByTitle($title)
    {
        $category = ORM::for_table("courseCategories")
            ->where("title", $title)
            ->find_one();
        if(empty($category)) {
            $category = ORM::for_table("courseCategories")->create();
            $category->title = $title;
            $category->slug = strtolower(str_replace(" ","-", $category->title));
            $category->save();
        }
        return $category;
    }

    public function importCourseCategories() {
        echo json_encode([
            'success' => false,
            'errors' => 'This endpoint has been disabled'
        ]);
        exit();
        if (!isset($this->post['import_source'])) {
            return;
        }
        $importer = new CourseCategoryCsvImport();
        switch ($this->post['import_source']) {
            case 'file_upload':
                if (!is_array($_FILES['csv_file'])) {
                    return;
                }
                $auth = $importer->importFromCsvFile($_FILES['csv_file']);
                break;
            case 'google_sheet':
                $auth = $importer->importFromGoogleSheets($this->post['google_sheet_url']);
                break;
            default:
                $auth = [
                    'success' => false,
                    'message' => 'Server error importing course categories'
                ];
        }
        if (!$auth['success']) {
            echo json_encode($auth);
            exit();
        }
        echo json_encode([
            'success' => true,
            'errors' => $importer->getErrors()
        ]);
        exit();
    }

}