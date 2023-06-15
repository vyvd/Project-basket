<?php
require_once(__DIR__ . '/mediaController.php');

class journeyController extends Controller
{
    /**
     * @var mediaController
     */
    protected $medias;

    public function __construct()
    {

        $this->post = $_POST;
        $this->get = $_GET;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;
        $this->medias = new mediaController();
        $this->table = "journeys";

    }

    public function getSingle() {

        $journey = ORM::for_table($this->table)->where("slug", $_GET["request"])->find_one();

        if($journey->hidden == "0" || $journey->title == "") {
            $this->force404();
            exit;
        } else {
            return $journey;
        }

    }

    public function getJourneyImage(int $journeyId, $size = 'full')
    {
        $media = $this->medias->getMedia(journeyController::class, $journeyId,
            'featured_img');

        if (@$media['url']) {
            $url = $media['url'];
            if ($size != 'full') {
                $url = str_replace($media['fileName'],
                    $size.'/'.$media['fileName'], $url);
            }
        } else {
            $url = '';
        }

        return $url;
    }

    public function calculateSavings($journey) {

        $courseIDs = explode(",", $journey->courses);

        // get total cost of all courses in this journey
        $totalCost = 0;

        foreach($courseIDs as $courseID) {

            $course = ORM::for_table("courses")->select("price")->find_one($courseID);

            if($course->price != "") {

                $totalCost = $totalCost+$course->price;

            }


        }

        // minus journey cost from total cost to get the saving
        $saving = $totalCost-$journey->price;

        return $saving;

    }

    public function calculateStudyDuration($journey) {

        $courseIDs = explode(",", $journey->courses);

        // get total duration of all courses in this journey
        $totalDuration = 0;

        foreach($courseIDs as $courseID) {

            $course = ORM::for_table("courses")->select("duration")->find_one($courseID);

            if($course->duration != "") {

                $totalDuration = $totalDuration+$course->duration;

            }

        }

        return $totalDuration;

    }

}