<?php

require_once(__DIR__ . '/../services/AWSService.php');

class mediaController extends Controller
{

    /**
     * @var string
     */
    protected $table;

    public function __construct()
    {
        $this->table = 'media';
    }

    public function saveMedia(array $input)
    {
        $data = array(
            'modelType' => $input["modelType"],
            'modelId'   => $input["modelId"],
            'url'       => isset($input["url"]) ? $input["url"] : CDN_URL.'/media/'.date("Y-m").'/'.$input["fileName"],
            'title'     => $input["title"],
            'fileName'  => $input["fileName"],
            'sizes'     => isset($input["sizes"]) ? $input["sizes"] : null,
            'type'     => isset($input["type"]) ? $input["type"] : null
        );

        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table($this->table)->find_one($input['id']);
        } else { //For Create
            $item = ORM::for_table($this->table)->create();
            $data['whenAdded'] = date("Y-m-d H:i:s");
        }

        $item->set($data);
        $item->save();

        return $item;
    }


    public function saveWPImage(array $image, array $model, $type = null, $replaceOld = false, $uploadThumb = false)
    {
        if(@$image['full']){
            $url = $image['full'];
            $pathinfo = pathinfo($url);

            $baseFileName = $pathinfo['basename'];
            $originalFileName = $pathinfo['filename'];
            $fileName = str_replace(" ","-", $pathinfo['filename']);
            $end = $pathinfo['extension'];

            // create year/month directory
            $date = date('Y-m');

            // make filename unique
            $fileName = $fileName . '-' . md5(time() . rand(3333, 9999)) . '.' . $end;

            // remove any bad characters
            $fileName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $fileName);
            $fileName = mb_ereg_replace("([\.]{2,})", '', $fileName);

            $this->addImageFromUrl(TO_PATH_CDN . 'media/'. $date, $fileName, $url);

            if(@$image['thumb']){
                $this->addImageFromUrl(TO_PATH_CDN . 'media/'. $date . '/thumb', $fileName, $image['thumb']);
            }
            if(@$image['medium']){
                $this->addImageFromUrl(TO_PATH_CDN . 'media/'. $date . '/medium', $fileName, $image['medium']);
            }
            if(@$image['large']){
                $this->addImageFromUrl(TO_PATH_CDN . 'media/'. $date . '/large', $fileName, $image['large']);
            }

            // Delete image
            if($replaceOld == true){
                $this->deleteMediaByModel($model['type'], $model['id'], $type);
            }

            $data = [];
            $data['modelType'] = $model['type'];
            $data['modelId'] = $model['id'];
            $data['url'] = TO_URL_CDN.'/media/'.$date.'/'.$fileName;
            $data['title'] = $originalFileName;
            $data['fileName'] = $fileName;
            $data['type'] = $type;
            $media = $this->saveMedia($data);


            if($uploadThumb){
                $sizes = ['thumb', 'medium', 'large'];
                foreach($sizes as $size) {
                    // resize
                    $this->mediaGenerateThumbnail($date, $fileName, TO_PATH_CDN.'media/'.$date.'/'.$fileName, $size);

                }
            }
            return $media;
        }
        return false;

    }

    public function mediaGenerateThumbnail($date, $fileName, $img, $size, $quality = 90) {

        $sizes = getimagesize($img);

        $ratio = $sizes[0]/$sizes[1];

        if($size == "thumb") {

            $width = 150;
            $height = 150;

        } else if($size == "medium") {

            $width = 300;
            $height = 300/$ratio;

        } else {

            $width = 1024;
            $height = 1024/$ratio;

        }

        if (is_file($img)) {
            try {
                $imagick = new Imagick(realpath($img));
                $imagick->setImageFormat('jpeg');
                $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
                $imagick->setImageCompressionQuality($quality);
                $imagick->thumbnailImage($width, $height, false, false);

                if (!file_exists(TO_PATH_CDN.'media/'.$date.'/'.$size)) {
                    mkdir(TO_PATH_CDN.'media/'.$date.'/'.$size, 0777, true);
                }

                if (file_put_contents( TO_PATH_CDN.'media/'.$date.'/'.$size.'/'.$fileName, $imagick) === false) {
                    throw new Exception("Could not put contents.");
                }
                return true;
            }
            catch(Exception $e) {
                return null;
            }

        }
        else {
            throw new Exception("No valid image provided with {$img}.");
        }

    }

    public function uploadFile(array $sizes = null, array $model, $postName = "file", $type = null, $replaceOld = false, $isS3 = false, $isMultiple = false)
    {
        if($isMultiple){
            $medias = [];
            foreach ($_FILES[$postName]['name'] as $key=>$file) {
                // file details
                $pathInfo = pathinfo($_FILES[$postName]["name"][$key]['file']);
                $fileName = $pathInfo['filename'];
                $fileTmpLoc = $_FILES[$postName]["tmp_name"][$key]['file'];
                $end = $pathInfo['extension'];
                $originalFileName = $fileName;
                $title = $_POST[$postName][$key]['title'] ?? $originalFileName;
                $media = $this->uploadFileMedia($title, $originalFileName, $fileTmpLoc, $end, $model, $sizes, $type, $replaceOld, $isS3);
                $medias[] = $media;
            }
            return $medias;
        }else{
            // file details
            $pathInfo = pathinfo($_FILES[$postName]["name"]);
            $fileName = $pathInfo['filename'];
            $fileTmpLoc = $_FILES[$postName]["tmp_name"];
            $end = $pathInfo['extension'];
            $originalFileName = $fileName;
            $title = $originalFileName;
            $media = $this->uploadFileMedia($title, $originalFileName, $fileTmpLoc, $end, $model, $sizes, $type, $replaceOld, $isS3);
            return $media;
        }
    }

    protected function uploadFileMedia($title, $originalFileName, $fileTmpLoc, $end, $model, array $sizes = null, $type = null, $replaceOld = false, $isS3 = false)
    {
        $fileName = $originalFileName;
        // create year/month directory
        $date = date('Y-m');

        if (!file_exists(TO_PATH_CDN.'media/'.$date)) {
            mkdir(TO_PATH_CDN.'media/'.$date, 0777, true);
        }

        // make filename unique
        $fileName = str_replace(" ","-",$fileName.'-'.md5(time().rand(3333,9999)).'.'.$end);

        // remove any bad characters
        $fileName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $fileName);
        $fileName = mb_ereg_replace("([\.]{2,})", '', $fileName);
        $urlCDN = TO_URL_CDN.'/media/'.$date.'/'.$fileName;

        // upload file
        if ($isS3) {
            $moveResult = AWSService::putToS3($fileTmpLoc, $fileName);
            $urlCDN = $moveResult['ObjectURL'];
        } else {
            $moveResult = move_uploaded_file($fileTmpLoc, TO_PATH_CDN.'media/'.$date.'/'.$fileName);
        }
        //$fileName = $date.'/'.$fileName;

        if(@$sizes){
            foreach($sizes as $size) {
                // resize
                $this->mediaGenerateThumbnail($date, $fileName, TO_PATH_CDN.'media/'.$date.'/'.$fileName, $size);

            }
        }

        if($replaceOld === true){
            $this->deleteMediaByModel($model['type'], $model['id'], $type);
        }

        $data = [];
        $data['modelType'] = $model['type'];
        $data['modelId'] = $model['id'];
        $data['url'] = $urlCDN;
        $data['title'] = $title;
        $data['fileName'] = $fileName;
        $data['sizes'] = $sizes ? implode(',', $sizes) : null;
        $data['type'] = $type;

        return $this->saveMedia($data);
    }

    public function addImageFromUrl($directoryPath, $fileName, $url){
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }

        if(file_put_contents($directoryPath . '/' . $fileName, file_get_contents($url))){
            return true;
        }
        return false;
    }


    public function hasMedia(string $model_type, int $model_id){
        $count = ORM::for_table($this->table)->where('modelType', $model_type)->where('modelId', $model_id)->count();
        if(@$count){
            return true;
        }
        return false;
    }

    public function getMedia(string $model_type, int $model_id, $type=null, $multiple = false){
        $media = ORM::for_table($this->table)->where('modelType', $model_type)->where('modelId', $model_id);
        if(@$type){
            $media->where('type', $type);
        }
        if($multiple){
            return $media->order_by_asc('id')->find_many();
        }

        // if site type is US then get content from UK server

        return $media->find_one();
    }

    public function deleteMediaByModel($modelType, $modelId, $type = null, $deleteFile = true, $mediaID = null)
    {
        $item = ORM::for_table($this->table)
            ->where("modelType", $modelType)
            ->where("modelId", $modelId);

        if(@$type){
            $item->where("type", $type);
        }

        $mediaID ? $item->find_one($mediaID) : $item->find_one();

        // delete file if set to true and it exists
        if($deleteFile == true) {

            if(file_exists(TO_PATH_CDN.date('Y-m').'/'.$item->fileName)) {

                unlink(TO_PATH_CDN.date('Y-m').'/'.$item->fileName);

            }

        }

        $item = ORM::for_table($this->table)
            ->where("modelType", $modelType)
            ->where("modelId", $modelId);

        if(@$type){
            $item->where("type", $type);
        }

        $mediaID ? $item->find_one($mediaID)->delete() : $item->delete_many();

    }

    public function deleteMediaByID($mediaID, $deleteFile = true)
    {
        $item = ORM::for_table($this->table)->find_one($mediaID);

        // delete file if set to true and it exists
        if($deleteFile == true) {

            if(file_exists(TO_PATH_CDN.date('Y-m').'/'.$item->fileName)) {

                unlink(TO_PATH_CDN.date('Y-m').'/'.$item->fileName);

            }

        }

        $item = ORM::for_table($this->table)->where("id", $mediaID)->delete_many();
    }

    public function addMediaFromUrl($url){

        $date = date('Y-m');
        $directoryPath = TO_PATH_CDN.'media/'.$date;

        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }

        $pathinfo = pathinfo($url);

        $fileName = str_replace(" ","-", $pathinfo['filename']);
        $end = $pathinfo['extension'];

        // make filename unique
        $oFileName = $fileName;
        $fileName = $fileName . '.' . $end;
        if (file_exists($directoryPath . '/' . $fileName)) {
            $fileName = $oFileName . '_1.' . $end;
        }
        if(file_put_contents($directoryPath . '/' . $fileName, file_get_contents($url))){
            $return = [
                'title' => $pathinfo['filename'],
                'filename' => $fileName,
                'url' => TO_URL_CDN.'/media/'.$date.'/'.$fileName,
            ];
            return $return;
        }
        return false;
    }
    public function addExternalMediaFromUrl($url, $modelType, $modelId, $fileName, $type = null, $replaceOld = false){

        // Delete image
        if($replaceOld == true){
            $this->deleteMediaByModel($modelType, $modelId, $type);
        }

        $data = array(
            'url'       => $url,
            'modelType' => $modelType,
            'modelId'   => $modelId,
            'fileName'  => $fileName,
            'type'     => $type,
            'title'     => $fileName,
            'whenAdded' => date("Y-m-d H:i:s")
        );


        $item = ORM::for_table($this->table)->create();
        $item->set($data);
        $item->save();

       return $item;
    }

    public function unlinkCourseContent()
    {
        $data = [
            'error' => true,
        ];

        echo $courseID = $_GET['courseID'];
        echo $courseSlug = $_GET['courseSlug'];

        // Check user course Assigned
        $coursesAssigned = ORM::for_table("coursesAssigned")
            ->where("accountID", CUR_ID_FRONT)
            ->where("courseID", $courseID)
            ->find_one();
        if(@$coursesAssigned->id){
            $directoryPath = TO_PATH_CDN.'courseContent/'.CUR_ID_FRONT;
            $filePath = $directoryPath.'/'.$courseSlug.'.zip';
            unlink($filePath);
            rmdir($directoryPath);
            $data = [
                'error' => false,
                'success' => true,
            ];
        }

        echo json_encode($data);
        exit();
    }
}
