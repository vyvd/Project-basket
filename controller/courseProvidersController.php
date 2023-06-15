<?php
require_once(__DIR__ . '/mediaController.php');

class courseProvidersController extends Controller {

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

    public function getAllCourseProviders()
    {
        return ORM::for_table("courseProviders")
            ->where('status', 1)
            ->find_many();
    }
    public function getProviderImage(int $catId, $size = 'full')
    {
        $media = $this->medias->getMedia(courseProvidersController::class, $catId, 'main_image');

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

    public function getCourseProvidersEdit()
    {
        return ORM::for_table("courseProviders")->find_one($this->get["id"]);
    }
}