<?php
require_once(__DIR__ . '/mediaController.php');

class resourceController extends Controller {

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


    public function getResourceImage(int $catId, $size = 'full')
    {
        $media = $this->medias->getMedia(resourceController::class, $catId, 'main_image');

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

}