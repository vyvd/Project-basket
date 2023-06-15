<?php

require_once(__DIR__ . '/mediaController.php');
require_once(__DIR__ . '/quizController.php');
require_once(__DIR__ . '/rewardsAssignedController.php');

class courseModuleController extends Controller
{
    /**
     * @var mediaController
     */
    protected $medias;

    /**
     * @var quizController
     */
    protected $quizzes;

    /**
     * @var rewardsAssignedController
     */
    protected $rewardsAssigned;

    /**
     * @var string
     */
    protected $table;

    public function __construct()
    {
        $this->table = 'courseModules';
        $this->medias = new mediaController();
        $this->quizzes = new quizController();
        $this->rewardsAssigned = new rewardsAssignedController();
    }

    public function saveCourseModule(array $input)
    {
        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table("courseModules")->find_one($input['id']);
        } else { //For Create
            $item = ORM::for_table("courseModules")->create();
        }

        $data = $input;
        if (@$data['feature_image']) {
            $feature_image = $data['feature_image'];
        }

        unset($data['feature_image']);

        if (@$data['quiz']) {
            $quiz = $data['quiz'];
            unset($data['quiz']);
        }

        if (@$data['embed_video']) {
            $embed_video = $data['embed_video'];
            unset($data['embed_video']);
        }

        if (@$data['worksheet_pdf_file']) {
            $worksheet_pdf_file = $data['worksheet_pdf_file'];
            unset($data['worksheet_pdf_file']);
        }

        if (@$data['assignments']) {
            $assignments = $data['assignments'];
            unset($data['assignments']);
        }

        if (@$data['uploads']) {
            $uploads = $data['uploads'];
            unset($data['uploads']);
        }

        // Check if already have slug
        $slugModule = ORM::for_table('courseModules')
            ->where('slug', $data['slug']);
        if(isset($input['id'])) {
            $slugModule = $slugModule->where_not_equal('id', $input['id']);
        }
        $slugModule = $slugModule->count();
        if($slugModule >= 1){
            $data['slug'] = $data['slug'] . '-' . ($slugModule + 1);
        }
        $item->set($data);
        $item->save();


        // Check if feature_image
        if (@$feature_image) {
            $this->medias->saveWPImage(array('full' => $feature_image), array('type' => courseModuleController::class, 'id' => $item->id), 'feature_image', true);
        }

        // Check if Quiz
        if (@$quiz) {
            $quiz['moduleID'] = $item->id;
            $quiz['usImport'] = $item->usImport ?? '0';
            $this->quizzes->saveQuiz($quiz);
        }

        // Check if embed_video
        if (@$embed_video) {

            $this->medias->deleteMediaByModel(courseModuleController::class,
                $item->id, 'embed_video');
            $dataMedia = [];
            $dataMedia['modelType'] = courseModuleController::class;
            $dataMedia['modelId'] = $item->id;
            $dataMedia['url'] = $embed_video;
            $dataMedia['title'] = 'Video';
            $dataMedia['fileName'] = 'Video';
            $dataMedia['type'] = 'embed_video';
            $this->medias->saveMedia($dataMedia);
        }

        // Check if worksheet_pdf_file
        if (@$worksheet_pdf_file) {

            $this->medias->deleteMediaByModel(courseModuleController::class,
                $item->id, 'worksheet');

            $fileUrl = $this->medias->addMediaFromUrl($worksheet_pdf_file);

            if (@$fileUrl) {
                $dataMedia = [];
                $dataMedia['modelType'] = courseModuleController::class;
                $dataMedia['modelId'] = $item->id;
                $dataMedia['url'] = $fileUrl['url'];
                $dataMedia['title'] = $fileUrl['title'];
                $dataMedia['fileName'] = $fileUrl['filename'];
                $dataMedia['type'] = 'worksheet';
                $this->medias->saveMedia($dataMedia);
            }
        }

        // Check if assignments
        if (@$assignments) {

            $this->medias->deleteMediaByModel(courseModuleController::class, $item->id, 'assignment');

            foreach ($assignments as $assignment){
                $fileUrl = $this->medias->addMediaFromUrl($assignment['url']);
                if (@$fileUrl) {
                    $dataMedia = [];
                    $dataMedia['modelType'] = courseModuleController::class;
                    $dataMedia['modelId'] = $item->id;
                    $dataMedia['url'] = $fileUrl['url'];
                    $dataMedia['title'] = $assignment['title'] ?? $fileUrl['title'];
                    $dataMedia['fileName'] = $fileUrl['filename'];
                    $dataMedia['type'] = 'assignment';
                    $this->medias->saveMedia($dataMedia);
                }
            }
        }

        // Check if uploads
        if (@$uploads) {

            $this->medias->deleteMediaByModel(courseModuleController::class, $item->id, 'upload');

            foreach ($uploads as $upload){
                $fileUrl = $this->medias->addMediaFromUrl($upload['url']);
                if (@$fileUrl) {
                    $dataMedia = [];
                    $dataMedia['modelType'] = courseModuleController::class;
                    $dataMedia['modelId'] = $item->id;
                    $dataMedia['url'] = $fileUrl['url'];
                    $dataMedia['title'] = $upload['title'] ?? $fileUrl['title'];
                    $dataMedia['fileName'] = $fileUrl['filename'];
                    $dataMedia['type'] = 'upload';
                    $this->medias->saveMedia($dataMedia);
                }
            }
        }

        return $item;
    }

    public function getCourseModuleByOldID($oldID, $courseID = null)
    {
        $courseModule = ORM::for_table('courseModules')
            ->where('oldID', $oldID);
        if($courseID){
            $courseModule = $courseModule->where('courseID', $courseID);
        }
        return $courseModule->find_one();
    }

    public function getModuleByID($id)
    {
        $courseModule = ORM::for_table('courseModules')
            ->find_one($id);
        return $courseModule;
    }

    public function getEmbedVideoById($moduleID)
    {
        $video = $this->medias->getMedia(courseModuleController::class,
            $moduleID, 'embed_video');
        return $video;
    }

    public function getWorksheetById($moduleID)
    {
        $worksheet = $this->medias->getMedia(courseModuleController::class,
            $moduleID, 'worksheet');
        return $worksheet;
    }

    public function getFeatureImageById($moduleID)
    {
        $worksheet = $this->medias->getMedia(courseModuleController::class,
            $moduleID, 'feature_image');
        return $worksheet;
    }

    public function saveCourseModuleProgress(array $input)
    {
        $item = ORM::for_table("courseModuleProgress")
            ->where(array(
                    'accountID' => $input['accountID'],
                    'courseID'  => $input['courseID'],
                    'moduleID'  => $input['moduleID']
                )
            )
            ->find_one();

        if (empty($item)) {
            $item = ORM::for_table("courseModuleProgress")->create();
        }elseif (@$item->whenCompleted) {
            unset($input['whenCompleted']);
        }

        $item->set($input);
        $item->save();
        if(isset($input['whenCompleted'])) {
            $this->checkCompleteModuleRewards($input['accountID']);
        }

        return $item;
    }

    protected function checkCompleteModuleRewards($accountId)
    {
        $rewards = ORM::forTable('rewards')
            ->where('category', 'modules')
            ->orderByAsc('rorder')
            ->find_many();
        $accountCompletedModules
            = ORM::for_table('courseModuleProgress')
            ->where('accountID', $accountId)
            ->where('completed', 1)
            ->count();

        if ($rewards) {
            foreach ($rewards as $reward) {

                $totalCompletedModules = str_replace("modules_", "", $reward->short);

                if ($accountCompletedModules >= $totalCompletedModules) {
                    $this->rewardsAssigned->assignReward($accountId, $reward->short, true, true, $reward->points);
                } else {
                    break;
                }
            }
        }

        $completeModuleLimit = $this->getSetting('reward_module_limit');
        if(($accountCompletedModules >= 1) && ($accountCompletedModules > $completeModuleLimit)){
            $signInRewardPoints = $this->getSetting('reward_module_points_after_limit');
            $this->rewardsAssigned->assignReward($accountId, "Complete ".$accountCompletedModules." modules", false, false, $signInRewardPoints);
        }
    }

    function getModuleAudio(int $moduleID)
    {
        $media = $this->medias->getMedia(courseModuleController::class, $moduleID, 'audio');
        return $media;
    }
    function getModuleVideoTrans(int $moduleID)
    {
        $media = $this->medias->getMedia(courseModuleController::class, $moduleID, 'video_trans');
        return $media;
    }
    function getModuleMp3Audio(int $moduleID)
    {
        $media = $this->medias->getMedia(courseModuleController::class, $moduleID, 'mp3_audio');
        return $media;
    }

    function getLastCourseModule(int $courseID)
    {
        $module = ORM::for_table($this->table)
            ->where('courseID', $courseID)
            ->order_by_desc('ord')
            ->find_one();
        return $module;
    }

    private function get_shortcode_regex( $tagnames = null ) {

        $tagregexp = implode( '|', array_map( 'preg_quote', $tagnames ) );

        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- don't remove regex indentation
        return '\\['                             // Opening bracket.
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]].
            . "($tagregexp)"                     // 2: Shortcode name.
            . '(?![\\w-])'                       // Not followed by word character or hyphen.
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag.
            .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash.
            .     '(?:'
            .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket.
            .         '[^\\]\\/]*'               // Not a closing bracket or forward slash.
            .     ')*?'
            . ')'
            . '(?:'
            .     '(\\/)'                        // 4: Self closing tag...
            .     '\\]'                          // ...and closing bracket.
            . '|'
            .     '\\]'                          // Closing bracket.
            .     '(?:'
            .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags.
            .             '[^\\[]*+'             // Not an opening bracket.
            .             '(?:'
            .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag.
            .                 '[^\\[]*+'         // Not an opening bracket.
            .             ')*+'
            .         ')'
            .         '\\[\\/\\2\\]'             // Closing shortcode tag.
            .     ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]].
        // phpcs:enable
    }

    public function renderContents($module) {

        $contents = $module->contents;

        // extract viral quiz shortcode (if there is one)
        $viralQuizID = '';

        $contents = str_replace("https://www.newskillsacademy.com/wp-content", "https://old.newskillsacademy.com/wp-content", $contents); // to get static images from old site
        $contents = str_replace("http://www.newskillsacademy.com/wp-content", "https://old.newskillsacademy.com/wp-content", $contents); // to get static images from old site
        $contents = str_replace("https://newskillsacademy.com/wp-content", "https://old.newskillsacademy.com/wp-content", $contents); // to get static images from old site

        $contents = preg_replace('#\[viralQuiz[^\]]+\]#', '[DuringModuleQuiz]', $contents);

        $pattern = $this->get_shortcode_regex(array('viralQuiz'));

        if (   preg_match_all( '/'. $pattern .'/s', $contents, $matches ) ) {

            $keys = array();
            $result = array();
            foreach( $matches[0] as $key => $value) {
                // $matches[3] return the shortcode attribute as string
                // replace space with '&' for parse_str() function
                $get = str_replace(" ", "&" , $matches[3][$key] );
                parse_str($get, $output);

                //get all shortcode attribute keys
                $keys = array_unique( array_merge(  $keys, array_keys($output)) );
                $result[] = $output;

            }
            //var_dump($result);
            if( $keys && $result ) {
                // Loop the result array and add the missing shortcode attribute key
                foreach ($result as $key => $value) {
                    // Loop the shortcode attribute key
                    foreach ($keys as $attr_key) {
                        $result[$key][$attr_key] = isset( $result[$key][$attr_key] ) ? $result[$key][$attr_key] : NULL;
                    }
                    //sort the array key
                    ksort( $result[$key]);
                }
            }

            //display the result
            $viralQuizID = $result[0]['id'];

        }

        if($viralQuizID != "") {

            $quizHTML = '';

            $quiz = ORM::for_table("quizzes")
                ->where("moduleID", $module->id)
                ->where("appear", 'd')
                ->find_one();

            if ($quiz->id != "") {

                $quizHTML = '<div id="questionWidget" style=""><p class="text-center"><i class="fa fa-spin fa-spinner"></i></p></div><script type="text/javascript">$("#questionWidget").load("'.SITE_URL.'ajax?c=quiz&a=widget&module='.$module->id.'&id='.$quiz->id.'");</script>';

            }

            // replace viral quiz shortcode with the actual required quiz
            $contents = str_replace('[viralQuiz id='.$viralQuizID.']', $quizHTML, $contents);

        }

        // During Module Quiz
        $duringModuleQuiz = ORM::for_table("quizzes")
            ->where("moduleID", $module->id)
            ->where('appear', 'd')
            ->find_one();
        if (@$duringModuleQuiz->id) {
            $quizHTML = '<div id="questionWidget"><p class="text-center"><i class="fa fa-spin fa-spinner"></i></p></div><script type="text/javascript">$("#questionWidget").load("'.SITE_URL.'ajax?c=quiz&a=widget&module='.$module->id.'&id='.$duringModuleQuiz->id.'");</script>';
            $contents = str_replace('[DuringModuleQuiz]', $quizHTML, $contents);
        }


        // removes remaining shortcodes, such as the [tweet] one
        $contents = preg_replace('#\[Tweet[^\]]+\]#', '', $contents);

        // adds line breaks
        $contents = nl2br($contents);

        // replace some excessive line breaks
        //$contents = str_replace('Take a Quick Recap Test', '', $contents); // temporary
        $contents = str_replace('<ul><br />', '<ul>', $contents);
        $contents = str_replace('<ol><br />', '<ol>', $contents);
        $contents = str_replace('</ul><br />', '</ul>', $contents);
        $contents = str_replace('</ol><br />', '</ol>', $contents);
        $contents = str_replace('</td><br />', '</td>', $contents);
        $contents = str_replace('<td><br />', '<td>', $contents);
        $contents = str_replace('<tr><br />', '<tr>', $contents);
        $contents = str_replace('</tr><br />', '</tr>', $contents);
        $contents = str_replace('<tbody><br />', '<tbody>', $contents);
        $contents = str_replace('</tbody><br />', '</tbody>', $contents);
        $contents = str_replace('<table', '<table class="table"', $contents);
        $contents = str_replace('<div class="summary1"><br />', '<div class="summary1">', $contents);
        $contents = str_replace('<div class="moudle-area"><br />', '<div class="moudle-area">', $contents);
        $contents = str_replace('<div class="dotted-highlight"><br />', '<div class="dotted-highlight">', $contents);
        $contents = str_replace('<div class="diff"><br />', '<div class="diff">', $contents);
        $contents = str_replace('<div class="did-you-know"><br />', '<div class="did-you-know">', $contents);
        $contents = preg_replace("/<a(.*?)>/", "<a$1 target=\"_blank\">", $contents);

        foreach(range('A', 'Z') as $letter) {
            $contents = str_replace('.'.$letter, '.<br /><br />'.$letter, $contents);
            $contents = str_replace('.</strong>'.$letter, '.</strong><br /><br />'.$letter, $contents);
        }

        // show the contents
        echo $contents;

    }

    public function importViralToNormalQuiz() {

        exit;

        /*
         * BACKUP DB before running, just in case
         * Could look to drop viral... tables from DB
         */

        // extract shortcodes
        $pattern = $this->get_shortcode_regex(array('viralQuiz'));

        $modules = ORM::for_table("courseModules")
            ->where_like("usImport", '1')
            ->where_like("contents", "%viralQuiz%")
            //->limit(1)
            ->find_many();

        foreach($modules as $module) {

            ?>
            <hr />
            <?php

            $moduleContents = $module->contents;

            $viralQuizID = '';

            if (   preg_match_all( '/'. $pattern .'/s', $moduleContents, $matches ) ) {

                $keys = array();
                $result = array();
                foreach( $matches[0] as $key => $value) {
                    // $matches[3] return the shortcode attribute as string
                    // replace space with '&' for parse_str() function
                    $get = str_replace(" ", "&" , $matches[3][$key] );
                    parse_str($get, $output);

                    //get all shortcode attribute keys
                    $keys = array_unique( array_merge(  $keys, array_keys($output)) );
                    $result[] = $output;

                }
                //var_dump($result);
                if( $keys && $result ) {
                    // Loop the result array and add the missing shortcode attribute key
                    foreach ($result as $key => $value) {
                        // Loop the shortcode attribute key
                        foreach ($keys as $attr_key) {
                            $result[$key][$attr_key] = isset( $result[$key][$attr_key] ) ? $result[$key][$attr_key] : NULL;
                        }
                        //sort the array key
                        ksort( $result[$key]);
                    }
                }

                //display the result
                $viralQuizID = $result[0]['id'];

            }


            if($viralQuizID != "") {

                // Check already Quiz
                $item = ORM::for_table('quizzes')
                    ->where('moduleID', $module->id)
                    ->where('courseID', $module->courseID)
                    ->where('appear', 'd')
                    ->find_one();

                if(empty($item)){
                    // INSERT into quizzes
                    $item = ORM::for_table("quizzes")->create();
                }

                $item->moduleID = $module->id;
                $item->viralQuizID = $viralQuizID;
                $item->passingPercentage = '80';
                $item->courseID = $module->courseID;
                $item->appear = "d";
                $item->usImport = "1";

                $item->save();

                $insertedQuizID = $item->id();

                //echo str_replace('[viralQuiz id='.$viralQuizID.']', 'JS FOR QUIZ HERE', $moduleContents);

                $viralQuiz = ORM::for_table("viralQuizzes")->find_one($viralQuizID);

                $viralQuestions = ORM::for_table("viralQuestions")->where("quizID", $viralQuizID)->order_by_asc("position")->find_many();

                $orderCount = 0;

                foreach($viralQuestions as $question) {

                    ?>
                    <p><Strong><?= $question->title ?></Strong></p>
                    <?php

                    $answerData = array();

                    $viralAnswers = ORM::For_table("viralAnswers")->where("questionID", $question->id)->find_many();

                    foreach($viralAnswers as $answerRecord) {

                        ?>
                        <p>- <?= $answerRecord->title ?></p>
                        <?php

                        $answer = array();

                        $answer["*_answer"] = $answerRecord->title;
                        $answer["*_correct"] = $answerRecord->isCorrect;

                        array_push($answerData, $answer);


                    }

                    // Check already Question
                    $insertQuestion = ORM::for_table('quizzes')
                        ->where('quizID', $insertedQuizID)
                        ->where('answerType', "single")
                        ->where('ord', $orderCount)
                        ->find_one();

                    if(empty($insertQuestion)){
                        // INSERT into quiz questions
                        $insertQuestion = ORM::for_table("quizQuestions")->create();
                    }

                    $insertQuestion->quizID = $insertedQuizID;
                    $insertQuestion->answerType = "single";
                    $insertQuestion->ord = $orderCount;
                    $insertQuestion->answerData = json_encode($answerData);
                    $insertQuestion->question = $question->title;

                    $insertQuestion->save();

                    $insertedQuestionID = $insertQuestion->id();

                    foreach($viralAnswers as $answerRecord) {


                        // add answer to DB
                        $qqa = ORM::for_table("quizQuestionAnswers")->create();

                        $qqa->questionID = $insertedQuestionID;
                        $qqa->answer = $answerRecord->title;
                        $qqa->isCorrect = $answerRecord->isCorrect;

                        $qqa->save();

                    }


                    $orderCount ++;



                }



            }

        }

    }

    public function getModuleAssignments($moduleID)
    {
        return $this->medias->getMedia('courseModuleController', $moduleID, 'assignment', true);
    }
    public function getModuleUploads($moduleID)
    {
        return $this->medias->getMedia('courseModuleController', $moduleID, 'upload', true);
    }

    public function getSubmodules($moduleID)
    {
        return ORM::for_table("courseModules")
            ->where("parentID", $moduleID)
            ->order_by_asc("ord")
            ->find_many();

    }

    public function getContinueModuleUrl($currentAssigned)
    {
        $moduleId = @$currentAssigned->currentSubModule ? $currentAssigned->currentSubModule : $currentAssigned->currentModule;
        $module = ORM::for_table("courseModules")->find_one($moduleId);
        $url = SITE_URL.'module/'.$module->slug;

        if($module->contentType == 'quiz'){
            //$url = SITE_URL.'quiz/'.$module->slug;
        }

        return $url;
    }

    public function getCompletedModules($courseID, $onlyIDs = false)
    {
        $modules = ORM::for_table('courseModuleProgress')
            ->where('accountID', CUR_ID_FRONT)
            ->where('courseID', $courseID)
            ->where('completed', 1)
            ->find_many();

        if($onlyIDs){
            $moduleIDs = [];
            foreach ($modules as $module) {
                $moduleIDs[] = $module->moduleID;
            }
            return $moduleIDs;
        }

        return $modules;
    }

    public function isModuleAlreadyCompleted($moduleID): bool
    {
        $module = ORM::for_table('courseModuleProgress')
            ->where('accountID', CUR_ID_FRONT)
            ->where('moduleID', $moduleID)
            ->where('completed', 1)
            ->count();
        return $module >= 1;
    }

    public function getPreviousModule($module)
    {
        $prevModule = [];
        if(!empty($module->parentID)){
            $parentModule = ORM::for_table('courseModules')->find_one($module->parentID);
            if ($module->ord >= 2) {
                $prevModule = ORM::for_table('courseModules')
                    ->where('parentID', $module->parentID)
                    ->where('courseID', $module->courseID)
                    ->where('ord', $module->ord - 1)
                    ->find_one();
            }elseif ($parentModule->ord >= 2) {
                $prevParentModule = ORM::for_table('courseModules')
                    ->where('courseID', $module->courseID)
                    ->where('ord', $parentModule->ord - 1)
                    ->whereNull('parentID')
                    ->find_one();
                $prevModule = $prevModule = ORM::for_table('courseModules')
                    ->where('parentID', $prevParentModule->parentID)
                    ->where('courseID', $module->courseID)
                    ->order_by_desc('ord')
                    ->find_one();
                $prevModule = $prevModule ?? $prevParentModule;
            }
        }elseif ($module->ord >= 2) {
            $prevModule = ORM::for_table('courseModules')
                ->where('courseID', $module->courseID)
                ->where('ord', $module->ord - 1)
                ->whereNull('parentID')
                ->find_one();
        }
        return $prevModule;
    }
}