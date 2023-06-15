<?php

require_once(__DIR__ . '/mediaController.php');
require_once(__DIR__ . '/emailTemplateController.php');

class achieverBoardController extends Controller {

    /**
     * @var mediaController
     */
    protected $medias;

    /**
     * @var emailTemplateController
     */
    protected $emailTemplates;

    public function __construct()
    {
        $this->medias = new mediaController();
        $this->emailTemplates = new emailTemplateController();
        $this->post = $_POST;
    }

    public function submit() {

        if($this->post["terms"] != "1") {
            $this->setToastDanger("You must accept the terms & conditions before continuing.");
            exit;
        }

        // submit to admin system for review, and also send email to NSA team
        $this->validateValues(array("firstname", "email", "phone", "city"));

        $item = ORM::for_table("achievers")->create();

        $item->firstname = $this->post["firstname"];
        $item->lastname = $this->post["lastname"];
        $item->email = $this->post["email"];
        $item->phone = $this->post["phone"];
        $item->city = $this->post["city"];

        if(CUR_ID_FRONT != "") {
            $item->accountID = CUR_ID_FRONT;
        }

        $item->set_expr("whenSubmitted", "NOW()");

        $item->save();

        $itemID = $item->id();

        // Update image
        if($this->checkFileUploadSelected() == true) {
            $sizes = ['large','medium','thumb'];
            $model = [
                'type' => achieverBoardController::class,
                'id' => $itemID
            ];
            $this->medias->uploadFile($sizes, $model,'uploaded_file', 'main_image', true);
        }
        // Email to user
        $emailTemplate = $this->emailTemplates->getTemplateByTitle('achiever_board_submission');
        if(@$emailTemplate->id){
            $variables = [
                '[FIRST_NAME]' => $this->post["firstname"],
                '[LAST_NAME]' => $this->post["lastname"],
            ];

            $message = $emailTemplate->description;
            $subject = $emailTemplate->subject;

            foreach ($variables as $k=>$v){
                $message = str_replace($k, $v, $message);
                $subject = str_replace($k, $v, $subject);
            }

            $this->sendEmail($this->post["email"], $message, $subject);
        }


        $this->redirectJS(SITE_URL.'achievers/thanks');

    }

    public function getItemsTotal($excludeIds = null) {
        $total = ORM::for_table("achievers")
            ->where("status", "a");
        if($excludeIds) {
            $total = $total->where_not_in('id', $excludeIds);
        }
        return $total->count();
    }

    public function getItems($limit = 20) {

        $page = $_POST['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        $items = ORM::for_table("achievers")
            ->where("status", "a");

        if(@$_POST['excludeIds']) {
            $excludeIds = explode(',', $_POST['excludeIds']);
            $items = $items->where_not_in('id', $excludeIds);
        }

        $items = $items->order_by_expr("RAND()")
            ->offset($offset)
            ->limit($limit)
            ->find_many();

        if(@$_GET['action'] && $_GET['action'] == 'json'){
            $totalItems = $this->getItemsTotal($excludeIds);
            if($items){
                $reactions = array("clapping", "starstruck", "smiling", "usercap", "cap");
                $itemsHtml = '';
                foreach ($items as $item){
                    $itemsHtml .= "<div class='acheiver-item'><div class='acheiver-box text-center'>";
                    
                    $itemsHtml .= "<img src='".$this->getAchieverImage($item->id, "medium")."' alt='acheivers' />";
                    
                    $itemsHtml .= '<i class="reaction '.$reactions[array_rand($reactions)] .'"></i>';
                    $itemsHtml .= '<p class="acheiver-name">'.$item->firstname.' '.$item->lastname .'</p>';
                    $itemsHtml .= '</div></div>';
                    $excludeIds[] = $item->id;
                }
            }
            $response = [
                'items' => $itemsHtml,
                'loadMore' => $totalItems > ($offset + $limit) ? true : false,
                'excludeIds' => implode(',', $excludeIds)
            ];

            echo json_encode( $response);
            exit;
        }
        return $items;

    }

    public function getAchieverImage(int $achId, $size = 'full')
    {
        $media = $this->medias->getMedia(achieverBoardController::class, $achId);

        if(@$media['url']){
            $url = $media['url'];
            if($size != 'full'){
                $url = str_replace($media['fileName'],$size.'/'.$media['fileName'], $url);
            }
        }else{
            $url = '';
        }

        return $url;
    }

}