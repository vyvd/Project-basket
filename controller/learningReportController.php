<?php
require_once(__DIR__ . '/courseTimeProgressController.php');
require_once(__DIR__ . '/../builders/emails/MonthlyLearningReportBuilder.php');

class learningReportController extends Controller
{

    /**
     * @var courseTimeProgressController
     */
    protected $courseTimeProgress;
    protected $monthlyLearningReportBuilder;

    public function __construct()
    {

        $this->post = $_POST;
        $this->get = $_GET;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;
        $this->courseTimeProgress = new courseTimeProgressController();
        $this->monthlyLearningReportBuilder = new MonthlyLearningReportBuilder();

    }

    public function generateReport($accountID, $month) {

        echo $this->courseTimeProgress->calculateCourseTimeAccount($accountID, $month.'-01 00:00:00', $month.'-31 23:59:59');

    }

    public function generateReportAjax() {

        $this->generateReport($this->get["account"], $this->get["month"]);

    }

    public function renderMonthlyLearningReport() {
        if (CUR_ID == "") {
            header('Location: ' . SITE_URL . 'blume/login');
            exit;
        }

        $account = ORM::for_table("accounts")->find_one(CUR_ID);
        if (!$account instanceof ORM) {
            header('Location: ' . SITE_URL . 'blume/login');
            exit;
        }
        $this->monthlyLearningReportBuilder->setAccount($account);
        return $this->monthlyLearningReportBuilder->renderReportHtml();
    }

}
