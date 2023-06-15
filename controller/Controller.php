<?php
require_once(__DIR__ . '/couponController.php');
/*
 * All routes are ran through the invoke() function towards the bottom of this main controller.
 * This also contains commonly used functions, such as price() which renders a price in the correct 2 decimal format, or getUserIP() which returns the IP address of a user
 */
class Controller {
    /**
     * @var couponController
     */
    protected $coupons;

    public function __construct()
    {

        $this->post = $_POST;
        $this->get = $_GET;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;
        $this->loadUser();
        $this->coupons = new couponController();

    }
 
    public function setControllers($items) {

        foreach($items as $item) {

            require_once (__DIR__ . '/' . $item.'Controller.php');

            $className = $item.'Controller';
            $this->$item = new $className();

        }

    }

    private function loadUser() {

        if(CUR_ID_FRONT == "") {
            $this->user = array();
        } else {
            $this->user = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);
        }

    }

    public function currentUser() {

        return ORM::for_table("accounts")->find_one(CUR_ID_FRONT);

    }

    private function mediaFixImageOrientation($filename)
    {
        if (function_exists('exif_read_data')) {
            $exif = exif_read_data($filename);
            if($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if($orientation != 1){
                    $img = imagecreatefromjpeg($filename);
                    $deg = 0;
                    switch ($orientation) {
                        case 3:
                            $deg = 180;
                            break;
                        case 6:
                            $deg = 270;
                            break;
                        case 8:
                            $deg = 90;
                            break;
                    }
                    if ($deg) {
                        $img = imagerotate($img, $deg, 0);
                    }
                    // then rewrite the rotated image back to the disk as $filename
                    imagejpeg($img, $filename, 95);
                } // if there is some rotation necessary
            } // if have the exif orientation info
        } // if function exists
    }

    private function mediaGenerateThumbnail($date, $fileName, $img, $size, $quality = 90) {

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

            $imagick = new Imagick(realpath($img));
            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
            $imagick->setImageCompressionQuality($quality);
            $imagick->thumbnailImage($width, $height, false, false);

            if (!file_exists(TO_PATH_CDN.'media/'.$size.'/'.$date)) {
                mkdir(TO_PATH_CDN.'media/'.$size.'/'.$date, 0777, true);
            }

            if (file_put_contents( TO_PATH_CDN.'media/'.$size.'/'.$fileName, $imagick) === false) {
                throw new Exception("Could not put contents.");
            }
            return true;

        }
        else {
            throw new Exception("No valid image provided with {$img}.");
        }

    }

    public function showSubscriptionUpsell() {

        if(CUR_ID_FRONT == "") {
            return true;
        } else {

            $accountUpsell = ORM::for_table("accounts")->select("subActive")->find_one(CUR_ID_FRONT);

            if($accountUpsell->subActive == "0") {
                return true;
            } else {
                return false;
            }

        }

    }

    public function mediaUpload($sizes, $postName = "file") {

        // file details
        $allow = array( 'png','PNG','x-png','gif','jpeg','jpg','JPEG','pjpeg');
        $fileName = $_FILES[$postName]["name"];
        $fileTmpLoc = $_FILES[$postName]["tmp_name"];
        $kaboom = explode(".", $fileName);
        $end = strtolower( end( $kaboom ) );
        $originalFileName = $fileName;

        // create year/month directory
        $date = date('Y-m');

        if (!file_exists(TO_PATH_CDN.'media/'.$date)) {
            mkdir(TO_PATH_CDN.'media/'.$date, 0777, true);
        }

        // make filename unique
        $fileName = $fileName.'-'.md5(time().rand(3333,9999)).'.'.$end;

        // remove any bad characters
        $fileName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $fileName);
        $fileName = mb_ereg_replace("([\.]{2,})", '', $fileName);

        // upload file
        $moveResult = move_uploaded_file($fileTmpLoc, TO_PATH_CDN.'media/'.$date.'/'.$fileName);
        $fileName = $date.'/'.$fileName;

        foreach($sizes as $size) {

            // resize
            $this->mediaGenerateThumbnail($date, $fileName, TO_PATH_CDN.'media/'.$fileName, $size);

        }

        // insert into media table and return the new ID
        return $this->mediaInsert($originalFileName, $fileName, $sizes);

    }

    public function ifMobile() {

        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
            return true;
        } else {
            return false;
        }


    }

    public function mediaUploadExternal($sizes, $url) {

        $pathinfo = pathinfo($url);
        $originalFileName = $pathinfo['filename'];
        $fileName = $pathinfo['filename'];
        $end = $pathinfo['extension'];

        // create year/month directory
        $date = date('Y-m');

        if (!file_exists(TO_PATH_CDN.'media/'.$date)) {
            mkdir(TO_PATH_CDN.'media/'.$date, 0777, true);
        }

        // make filename unique
        $fileName = $fileName.'-'.md5(time().rand(3333,9999)).'.'.$end;

        // remove any bad characters
        $fileName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $fileName);
        $fileName = mb_ereg_replace("([\.]{2,})", '', $fileName);

        // add date
        $fileName = $date.'/'.$fileName;

        // upload file
        file_put_contents(TO_PATH_CDN.'media/'.$fileName, file_get_contents($url));

        foreach($sizes as $size) {

            // resize
            $this->mediaGenerateThumbnail($date, $fileName, TO_PATH_CDN.'media/'.$fileName, $size);

        }

        // insert into media table and return the new ID
        return $this->mediaInsert($originalFileName, $fileName, $sizes);

    }

    private function mediaInsert($title, $fileName, $sizes) {

        $media = ORM::for_table("media")->create();

        $media->title = $title;
        $media->fileName = $fileName;
        $media->sizes = json_encode($sizes);
        $media->set_expr("whenAdded", "NOW()");

        $media->save(); // insert into database

        return $media->id(); // return ID of this media item

    }

    public function getMediaURL($id, $size = "original") {

        $media = ORM::for_table("media")->find_one($id);

        if($media->id == "") {
            return 'https://via.placeholder.com/200x200.png?text=Media%20not%20found';
        }

        if($size == "original") {
            return SITE_URL.'assets/cdn/media/'.$media->fileName;
        } else if($size == "thumb") {
            return SITE_URL.'assets/cdn/media/thumb/'.$media->fileName;
        } else if($size == "medium") {
            return SITE_URL.'assets/cdn/media/medium/'.$media->fileName;
        } else {
            return SITE_URL.'assets/cdn/media/large/'.$media->fileName;
        }

    }

    public function getPage($id) {

        return ORM::for_table("pages")->find_one($id);

    }

    public function checkCartHasDiscount() {

        $order = ORM::for_table("orders")->find_one(ORDER_ID);

        if($order->couponID == "") {
            return false;
        } else {
            return true;
        }

    }

    public function hasUnreadMessages() {

        $unread = ORM::for_table("messages")->where("recipientID", CUR_ID_FRONT)->where("seen", "0")->count();

        if($unread == 0) {
            return false;
        } else {
            return true;
        }

    }

    public function checkCourseSaved($courseID) {

        $existing = ORM::for_table("coursesSaved")->where("userID", CUR_ID_FRONT)->where("courseID", $courseID)->count();

        if($existing == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getCourseCategories() {

        // returns an array of all course categories
        return ORM::for_table("courseCategories")
            ->order_by_asc("title")
            ->find_many();

    }

    public function getAllCourses() {

        return ORM::for_table("courses")
            ->order_by_asc("title")
            ->find_many();

    }
    
    public function getAllCoursesWithoutHidden() {

        return ORM::for_table("courses")
            ->where("hidden", "0")
            ->order_by_asc("title")
            ->find_many();

    }

    public function price($price, $symbol = "£") {

        $currency = $this->currentCurrency();

        $symbol = $currency->short;

        return $symbol.number_format($price, 2);

    }

    public function priceNoFormat($price, $symbol = "£") {

        $currency = $this->currentCurrency();

        $symbol = $currency->short;

        return $symbol.$price;

    }

    public function getUserIP() {

        // attempts to get the IP of the current user
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public function setError($msg) {
        ?>
        <div class="alert alert-danger text-center"><?= $msg ?></div>
        <?php
    }

    public function validateValues($values) {

        // check if all required values have data
        $error = 0;
        ?>
        <script type="text/javascript">
            $(".select2-container").removeClass("form-error");
        </script>
        <?php
        foreach($values as $value) {
            if($this->post[$value] == "") {
                $error = 1;

                ?>
                <script type="text/javascript">
                    $("[name='<?= $value ?>']").addClass("form-error");
                    $(".<?= $value ?> .select2-container").addClass("form-error");
                </script>
                <?php
            }


        }

        if($error == 1) {
            $this->setToastDanger("Please ensure the required data is entered");
            exit;
        }
    }

    public function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        // $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function randomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function checkFileUploadSelected($postName = "uploaded_file", $isMultiple = false) {

        if($isMultiple){
            if(empty($_FILES[$postName]["name"])) {
                return false;
            }
        }else{
            if($_FILES[$postName]["name"] == "") {
                return false;
            }
        }

        return true;

    }

    public function validateUploadImage(
            $postName = "uploaded_file",
            $validations = [
                    'type' => array( 'png','PNG','x-png','gif','jpeg','jpg','JPEG','pjpeg'),
                    'width' => 200,
                    'height' => 200
            ]
    ) {
        $errors = [];
        // Calling getimagesize() function


        $fileName = $_FILES[$postName]["name"];
        $fileTmpLoc = $_FILES[$postName]["tmp_name"];

        $fileType = $_FILES[$postName]["type"];
        $fileSize = $_FILES[$postName]["size"];
        $fileErrorMsg = $_FILES[$postName]["error"];
        $kaboom = explode(".", $fileName);
        $end = strtolower( end( $kaboom ) );


        if (@$validations['type'] && !in_array($end, $validations['type']) ) {
            $errors['type'] = "The file you are uploading is not allowed.";
            return $errors;
        }

        list($width, $height, $type, $attr) = getimagesize($fileTmpLoc);

        if(@$validations['width'] && @$validations['height']){
            if($width < "200" || $height < "200") {
                $errors['dimensions'] = "Image size at least 200x200 pixels.";
            }
        }

        if(empty($errors)){
            return null;
        }

        return $errors;

    }

    public function uploadImage($cdnDir, $postName = "uploaded_file") {

        $allow = array( 'png','PNG','x-png','gif','jpeg','jpg','JPEG','pjpeg');
        $fileName = $_FILES[$postName]["name"];
        $fileTmpLoc = $_FILES[$postName]["tmp_name"];
        $fileType = $_FILES[$postName]["type"];
        $fileSize = $_FILES[$postName]["size"];
        $fileErrorMsg = $_FILES[$postName]["error"];
        $kaboom = explode(".", $fileName);
        $end = strtolower( end( $kaboom ) );

        if($fileSize > 104857600000) {
            echo '<div class="alert alert-danger text-center" role="alert">Your file must be under 10MB.</div>';
            exit;
        } else if (!in_array($end,$allow) ) {
            echo '<div class="alert alert-danger text-center" role="alert">The file you are uploading is now allowed.</div>';
            exit;
        } else if ($fileErrorMsg == 1) {
            echo '<div class="alert alert-danger text-center" role="alert">An unknown error occurred. '.$fileErrorMsg.'</div>';
            exit;
        }

        $date = date('Y-m-d');

        if (!file_exists(TO_PATH_CDN.$cdnDir.'/'.$date)) {
            mkdir(TO_PATH_CDN.$cdnDir.'/'.$date, 0777, true);
        }

        $fileName = $fileName.'-'.md5(time().rand(3333,9999)).'.'.$end;
        $moveResult = move_uploaded_file($fileTmpLoc, TO_PATH_CDN.$cdnDir.'/'.$date.'/'.$fileName);
        if ($moveResult != true) {
            echo '<div class="alert alert-danger text-center" role="alert">We could not complete the upload of your image. Please contact our team.</div>';
            exit;
        }

        return $date.'/'.$fileName;

    }

    public function force404() {

        // mostly used if content, such as a course module, doesnt exist to stop the page from loading nothing

        header("HTTP/1.0 404 Not Found"); // set appropriate header/response code
        include __DIR__ . '/../view/404/responsive.php';
        exit;

    }

    public function getSetting($name) {

        $setting = ORM::for_table("settings")->where("name", $name)->find_one();
        return $setting->value;

    }

    public function getNewsletterCourse() {

        $courseID = $this->getSetting("newsletter_course");

        return ORM::for_table("courses")->find_one($courseID);

    }

    public function renderFormAjax($controller, $function, $formName, $returnTo = "#returnStatus", $reset = false, $loader = false) {

        ?>
        <script type="text/javascript">

            $("form[name='<?= $formName ?>']").submit(function(e) {

                $(".loader_wrapper").css('display', 'block');

                var submitButton = $("form[name='<?= $formName ?>'] button[type='submit']");
                submitButton.prop('disabled', true);

                <?php if($loader == true){?>
                    var buttonText = submitButton.html();
                    submitButton.html('<i class="fa fa-spin fa-spinner"></i>');
                <?php }?>

                var formData = new FormData($(this)[0]);
                e.preventDefault();
                $( "<?= $returnTo ?>" ).empty();
                runFormAjax('<?= $formName ?>', function (auth = null, token = null) {
                    if (auth) {
                        formData.set('auth', auth)
                        formData.set('token', token)
                    }
                    $.ajax({
                        url: "<?= SITE_URL ?>ajax?c=<?= $controller ?>&a=<?= $function ?>",
                        type: "POST",
                        data: formData,
                        async: false,
                        success: function (msg) {

                            $(".loader_wrapper").css('display', 'none');

                            $('<?= $returnTo ?>').append(msg);
                            submitButton.prop('disabled', false);
                            <?php if($reset == true){?>
                                $("form[name='<?= $formName ?>']").trigger('reset');
                            <?php }
                            if($loader == true){?>
                                submitButton.html(buttonText);
                            <?php }
                            if($formName == 'learnAccess'){?>
                                $("#username").text(msg);
                                $("#learnAccess").modal('show');
                                $("form[name='<?= $formName ?>']").trigger('reset');
                            <?php }else if($formName == 'providerLogin'){?>
                                    location.reload();
                            <?php }
                            if($sweetAlert){
                            ?>
                                Swal.fire(
                                    'Success!',
                                    '<?= $sweetAlert ?>',
                                    'success'
                                );
                            <?php
                            }
                            ?>
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                })
            });
        </script>
        <?php

    }
    
    public function renderPriceBasket($item, $symbol = "£") {

        $product = ORM::for_table("products")->find_one($item->productID);

        $vat = 20;
        $vatToPay = ($product->priceRRP / 100) * $vat;

        if($item->framingOptionsID != "") {
            $product->priceRRP = $item->price;
            $vatToPay = 0;
        }

        if(CUR_ID_FRONT == "") {

            // update price in db
            $u = ORM::for_table("orderItems")->find_one($item->id);
            $u->price = number_format(round($product->priceRRP+$vatToPay + 0.01) - 0.01, 2);
            $u->vatRate = $vat;
            $u->save();

            return $symbol.number_format(((round($product->priceRRP+$vatToPay + 0.01) - 0.01))*$item->qty, 2);
        } else {
            if($_SESSION['trade'] == false) {

                // update price in db
                $u = ORM::for_table("orderItems")->find_one($item->id);
                $u->price = number_format(round($product->priceRRP+$vatToPay + 0.01) - 0.01, 2);
                $u->vatRate = $vat;
                $u->save();

                return $symbol.number_format(((round($product->priceRRP+$vatToPay + 0.01) - 0.01))*$item->qty, 2);
            } else {

                // update price in db
                $u = ORM::for_table("orderItems")->find_one($item->id);
                $u->price = number_format(($product->price), 2);
                $u->vatRate = "0";
                $u->save();

                return $symbol.number_format(($product->price*$item->qty), 2);
            }
        }

    }

    public function redirectJS($link) {
        ?>
        <script type="text/javascript">
            window.location.href = "<?= $link ?>";
        </script>
        <?php
    }

    public function renderHtmlEmailButton($btnText, $btnLink) {
        // generates a styled button, suitable for our HTML email template

        return '<center><br /><a href="'.$btnLink.'" style="-webkit-text-size-adjust: none; text-decoration: none; display: inline-block; color: #ffffff; background-color: #17A9CE; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; width: auto; width: auto; border-top: 0px solid #8a3b8f; border-right: 0px solid #8a3b8f; border-bottom: 0px solid #8a3b8f; border-left: 0px solid #8a3b8f; padding-top: 10px; padding-bottom: 10px; font-family: Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif; text-align: center; mso-border-alt: none; word-break: keep-all;" target="_blank"><span style="padding-left:55px;padding-right:55px;font-size:16px;display:inline-block;"><span style="font-size: 16px; line-height: 2; word-break: break-word; mso-line-height-alt: 32px;"><strong>'.$btnText.'</strong></span></span></a></center>';

    }


    public function sendEmail($to, $message, $subject, $from = 'New Skills Academy <noreply@newskillsacademy.co.uk>') {

        if($from == 'New Skills Academy <noreply@newskillsacademy.co.uk>' || ($from == 'New Skills Academy <sales@newskillsacademy.co.uk>')) {
            $mail_contents = file_get_contents(SITE_URL.'assets/cdn/emails/index.html');

            $mail_contents = str_replace("%subject%", $subject, $mail_contents);
            $mail_contents = str_replace("%message%", $message, $mail_contents);
        } else {
            $mail_contents = $message; // plain html
        }


        $array_data = array(
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'html' => $mail_contents,
            'text' => $subject,
            'o:tracking' => 'yes',
            'o:tracking-clicks' => 'yes',
            'o:tracking-opens' => 'yes',
            'o:tag' => "test",
            'h:Reply-To' => $from
        );

        // key-6a1e1b7e69593811eed4fde8fac7372d
        // new private key: key-c2a1090b9f1847f43ab40ea6073b9566

        $session = curl_init('https://api.mailgun.net/v3/mg.newskillsacademy.co.uk/messages');
        curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($session, CURLOPT_USERPWD, 'api:key-6a1e1b7e69593811eed4fde8fac7372d'); // key-6a1e1b7e69593811eed4fde8fac7372d
        curl_setopt($session, CURLOPT_USERPWD, 'api:key-c2a1090b9f1847f43ab40ea6073b9566'); // key-c2a1090b9f1847f43ab40ea6073b9566
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $array_data);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($session);
        curl_close($session);
        $results = json_decode($response, true);

        $log = ORM::for_table("emailLogs")->create();
        
        $log->email = $to;
        $log->contents = $message;
        $log->set_expr("whenSent", "NOW()");
        $log->subject = $subject;

        $log->save();

    }

    public function sendEmailPDFAttachment($to, $message, $subject, $fileLocation, $fileName, $from = 'New Skills Academy <noreply@newskillsacademy.co.uk>') {

        if($from == 'New Skills Academy <noreply@newskillsacademy.co.uk>' || ($from == 'New Skills Academy <sales@newskillsacademy.co.uk>')) {
            $mail_contents = file_get_contents(SITE_URL.'assets/cdn/emails/index.html');

            $mail_contents = str_replace("%subject%", $subject, $mail_contents);
            $mail_contents = str_replace("%message%", $message, $mail_contents);
        } else {
            $mail_contents = $message; // plain html
        }


        $array_data = array(
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'html' => $mail_contents,
            'text' => $subject,
            'o:tracking' => 'yes',
            'o:tracking-clicks' => 'yes',
            'o:tracking-opens' => 'yes',
            'o:tag' => "test",
            'h:Reply-To' => $from,
            'attachment[1]' => curl_file_create($fileLocation, 'application/pdf', $fileName)
        );

        // key-6a1e1b7e69593811eed4fde8fac7372d
        // new private kye: key-c2a1090b9f1847f43ab40ea6073b9566

        $session = curl_init('https://api.mailgun.net/v3/mg.newskillsacademy.co.uk/messages');
        curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($session, CURLOPT_USERPWD, 'api:key-6a1e1b7e69593811eed4fde8fac7372d'); // key-6a1e1b7e69593811eed4fde8fac7372d
        curl_setopt($session, CURLOPT_USERPWD, 'api:key-c2a1090b9f1847f43ab40ea6073b9566'); // key-c2a1090b9f1847f43ab40ea6073b9566
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $array_data);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($session);
        curl_close($session);
        $results = json_decode($response, true);


    }

    public function validatePassword($pwd) {

        /*if (strlen($pwd) < 8) {
            $this->setToastDanger("Password must be at least 8 characters long.");
            exit;
        }

        if(!preg_match("#[0-9]+#",$pwd)) {
            $this->setToastDanger("Your password must contain at least 1 number.");
            exit;
        }

        if(!preg_match("#[A-Z]+#",$pwd)) {
            $this->setToastDanger("Your password must contain at least 1 capital letter.");
            exit;
        }

        if(!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $_POST["password"])) {
            $this->setToastDanger("Your password must contain at least 1 special character.");
            exit;
        }*/

    }

    public function redirectJSDelay($link, $delay) {
        ?>
        <script type="text/javascript">
            setTimeout(function(){window.location.href = "<?= $link ?>";}, <?= $delay ?>);
        </script>
        <?php
    }

    public function currentUserData() {
        return ORM::for_table("accounts")->find_one(CUR_ID_FRONT);
    }

    public function debug($error) {
        echo '<pre>';
        print_r($error);
        echo '</pre>';
    }


    // calculate time elapsed between strnigs
    public function time_elapsed_string($ptime)
    {
        $etime = time() - strtotime($ptime);

        if ($etime < 1)
        {
            return '0 seconds';
        }

        $a = array( 365 * 24 * 60 * 60  =>  'year',
            30 * 24 * 60 * 60  =>  'month',
            24 * 60 * 60  =>  'day',
            60 * 60  =>  'hr',
            60  =>  'min',
            1  =>  'sec'
        );
        $a_plural = array( 'year'   => 'years',
            'month'  => 'months',
            'day'    => 'days',
            'hour'   => 'hrs',
            'minute' => 'mins',
            'second' => 'secs'
        );

        foreach ($a as $secs => $str)
        {
            $d = $etime / $secs;
            if ($d >= 1)
            {
                $r = round($d);
                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
            }
        }
    }

    public function setToastDanger($msg) {
        ?>
        <script type="text/javascript">
            toastr.options.positionClass = "toast-bottom-left";
            toastr.options.closeDuration = 1000;
            toastr.options.timeOut = 5000;
            toastr.error('<?= $msg ?>', 'Oops')
        </script>
        <?php
    }

    public function starRating($rating)
    {
        $rating_round = round($rating * 2) / 2;
        if ($rating_round <= 0.5 && $rating_round > 0) {
            return '<i class="fa fa-star-half-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>';
        }
        if ($rating_round <= 1 && $rating_round > 0.5) {
            return '<i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>';
        }
        if ($rating_round <= 1.5 && $rating_round > 1) {
            return '<i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>';
        }
        if ($rating_round <= 2 && $rating_round > 1.5) {
            return '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>';
        }
        if ($rating_round <= 2.5 && $rating_round > 2) {
            return '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>';
        }
        if ($rating_round <= 3 && $rating_round > 2.5) {
            return '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>';
        }
        if ($rating_round <= 3.5 && $rating_round > 3) {
            return '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><i class="fa fa-star-o"></i>';
        }
        if ($rating_round <= 4 && $rating_round > 3.5) {
            return '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>';
        }
        if ($rating_round <= 4.5 && $rating_round > 4) {
            return '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half"></i>';
        }
        if ($rating_round <= 5 && $rating_round > 4.5) {
            return '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>';
        }

    }


    public function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 ||
            (substr($haystack, -$length) === $needle);
    }

    public function isMobileDevice() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo
|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i"
            , $_SERVER["HTTP_USER_AGENT"]);
    }

    public function setAlertSuccess($msg) {
        ?>
        <div class="alert alert-success text-center"><?= $msg ?></div>
        <?php
    }

    public function setAlertDanger($msg) {
        ?>
        <div class="alert alert-danger text-center"><?= $msg ?></div>
        <?php
    }

    public function setAlertInfo($msg) {
        ?>
        <div class="alert alert-info text-center"><?= $msg ?></div>
        <?php
    }

    public function getIpStackInfo($ip) {

        // set API access key
        $access_key = '0331d53850371ae6347815385d376445';

// Initialize CURL:
        $ch = curl_init('https://api.ipstack.com/'.$ip.'?access_key='.$access_key.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Store the data:
        $json = curl_exec($ch);
        curl_close($ch);

// Decode JSON response:
        $api_result = json_decode($json, true);

        return $api_result;

    }

    public function currentCurrency() {


        if($this->get["currency"] != "") {
            // check for currency via get variable first
            $currency = ORM::for_table("currencies")->where("code", $this->get["currency"])->find_one();

            if($currency->id != "") {

                setcookie("currency", $currency->id, time() + (86400 * 30)*90, "/");

            }

        } else {

            if(CUR_ID_FRONT != "") {
                $account = ORM::for_table("accounts")->select("currencyID")->find_one(CUR_ID_FRONT);

                $currency = ORM::for_table("currencies")->find_one($account->currencyID);

                setcookie("currency", $account->currencyID, time() + (86400 * 30)*90, "/");
            } else {
                if($_COOKIE["currency"] == "") {

                    // currency not set, try and auto detect
                    $ip_addr = $this->getUserIP();
                    $ipInfo = $this->getIpStackInfo($ip_addr);

                    if ( $ipInfo['currency']['code'] != "") {

                        $currency = ORM::for_table("currencies")->where("code", $ipInfo['currency']['code'])->find_one();

                        if($currency->id == "") {

                            if(SITE_TYPE == "uk") {
                                $currency = ORM::for_table("currencies")->find_one(1); // get default gbp one
                            } else {
                                $currency = ORM::for_table("currencies")->find_one(2); // get default usd one
                            }
                        }

                    } else {

                        if(SITE_TYPE == "uk") {
                            $currency = ORM::for_table("currencies")->find_one(1); // get default gbp one
                        } else {
                            $currency = ORM::for_table("currencies")->find_one(2); // get default usd one
                        }

                    }

                    setcookie("currency", $currency->id, time() + (86400 * 30)*90, "/");

                    // as we are redirecting, we do not want to lose any other GET variables, so append these to the direct URL
                    foreach($_GET as $key => $value) {

                        $getString .= '&'.$key.'='.$value;

                    }

                    if($_GET["ref"] != "") {
                        header('Location: '.SITE_URL.REQUEST.'?ref='.$_GET["ref"].$getString);
                        exit;
                    } else {
                        if($getString == "") {
                            header('Location: '.SITE_URL.REQUEST);
                            exit;
                        } else {
                            header('Location: '.SITE_URL.REQUEST.'?cr=true'.$getString);
                            exit;
                        }

                    }

                } else {

                    $currency = ORM::For_table("currencies")->find_one($_COOKIE["currency"]);

                    if($currency->id == "") {

                        if(SITE_TYPE == "uk") {
                            $currency = ORM::for_table("currencies")->find_one(1); // get default gbp one
                        } else {
                            $currency = ORM::for_table("currencies")->find_one(2); // get default usd one
                        }

                    }
                    // set cookie
                    setcookie("currency", $currency->id, time() + (86400 * 30)*90, "/");

                }
            }


        }




        return $currency;

    }

    public function getCoursePrice($course, $currency = "") {

        // gets the course price in the current local currency
        $courseID = $course->id;

        if($courseID == "") {

            $courseID = $course["id"];

        }

        if($currency == "") {
            $currency = $this->currentCurrency();
        } else {
            $currency = ORM::for_table("currencies")->find_one($currency);
        }

        $price = ORM::for_table("coursePricing")
            ->where("courseID", $courseID)
            ->where("currencyID", $currency->id)
            ->find_one();

        if($price->id == "") {

            $price = ORM::for_table("coursePricing")
                ->where("courseID", $courseID)
                ->where("currencyID", "1")
                ->find_one(); // get default gbp one

        }

        return $price->price;

    }

    public function ifUserInTrial($userID = "") {

        // returns true or false based on if a user is in a subscription trial period or not

        if($userID == "") {
            $userID = CUR_ID_FRONT; // if blank, then assume current signed in user
        }

        $subscription = ORM::for_table("subscriptions")
            ->where("accountID", $userID)
            ->where('status', 1)
            ->where('isPremium', 1)
            ->where_not_null('premiumSubPlanID')
            ->find_one();

        if($subscription != "") {

            if($subscription->trialDays == "0" || $subscription->trialDays == "") {
                return false;
            } else {

                $trialEnd = strtotime($subscription->whenAdded.' +'.$subscription->trialDays.' days');
                $trialEnd = date('Y-m-d H:i:s', $trialEnd);

                if($trialEnd > date('Y-m-d H:i:s')) {

                    return true;

                } else {

                    return false;

                }


            }

        } else {
            return false;
        }



    }

    public function courseAvailable($course) {

        // returns true or false if the course is available to the selected currency or not

        if($course->id == "") {

            $course->id = $course["id"];

        }

        $currency = $this->currentCurrency();

        $price = ORM::for_table("coursePricing")
            ->where("courseID", $course->id)
            ->where("currencyID", $currency->id)
            ->find_one();

        if($price->id == "") {

            $price = ORM::for_table("coursePricing")
                ->where("courseID", $course->id)
                ->where("currencyID", "1")
                ->find_one(); // get default gbp one

        }

        if($price->available == "1") {
            return true;
        } else {
            return false;
        }

    }

    public function checkCourseAvailability($course, $currencyID = "1") {

        // checks if course is only available in the uk or other currency
        $price = ORM::for_table("coursePricing")->where("courseID", $course->id)->where("available", "1")->find_many();

        if(count($price) == 1) {

            $price = ORM::for_table("coursePricing")->where("courseID", $course->id)->where("available", "1")->find_one();

            if($price->currencyID == $currencyID) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }

    }

    function ago($ptime)
    {
        $now = new DateTime;
        $ago = new DateTime($ptime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public function generatePrice($price) {
        return number_format($price, 2);
    }

    public function setToastSuccess($msg) {
        ?>
        <script type="text/javascript">
            toastr.options.positionClass = "toast-bottom-left";
            toastr.options.closeDuration = 1000;
            toastr.options.timeOut = 6000;
            toastr.success('<?= $msg ?>', 'Success')
        </script>
        <?php
    }

    public function createSlug($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public function cdnLink($fileName, $location) {

        return SITE_URL.'assets/cdn/'.$location.'/'.$fileName;

    }

    private function checkRoute($array, $key, $val) {

        // class can be accessed via $this->controller in the view
        foreach ($array as $item) {
            if (isset($item[$key]) && $item[$key] == $val) {

                if($item["signInRequired"] == "blume" && CUR_ID == "") {
                    header('Location: '.SITE_URL.'blume/login');
                    exit;
                } else if($item["signInRequired"] == "true" && CUR_ID_FRONT == "") {
                    header('Location: '.SITE_URL.'account/sign-in?return='.urlencode($item["url"]));
                    exit;
                }

                if($item["controller"] != "") {
                    include __DIR__ . '/' . $item["controller"] . '.php';
                    $this->controller = new $item["controller"]();
                }
                return true;
            }
        }

        return false;

    }

    public function userType($type) {

        $account = ORM::for_table("accounts")
            ->select("isIQA")
            ->select("isTutor")
            ->select("isNCFE")
            ->find_one(CUR_ID_FRONT);

        if($type == "IQA") {

            if($account->isIQA == "1") {
                return true;
            } else {
                return false;
            }

        } else if($type == "tutor") {

            if($account->isTutor == "1") {
                return true;
            } else {
                return false;
            }

        } else if($type == "NCFE") {

            if($account->isNCFE == "1") {
                return true;
            } else {
                return false;
            }

        }

    }

    public function filter_content($string)
    {
        $string = str_replace('\r\n',"[br]", $string);

        $string = str_replace('‘',"'", $string);
        $string = str_replace('’',"'", $string);
        $string = str_replace('●',"*", $string);
        $string = str_replace('','*', $string);
        $string = str_replace('○','*', $string);
        //return $string;
        preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $string);

        $string = str_replace('[br]',"\r\n", $string);

        return $string;
    }

    public function getPaypalAccessToken()
    {

        $ch = curl_init();
        $clientId = PAYPAL_CLIENT_ID_NEW;
        $secret = PAYPAL_SECRET_NEW;

        curl_setopt($ch, CURLOPT_URL, PAYPAL_URL."/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6); //NEW ADDITION
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $clientId.":".$secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        $result = curl_exec($ch);

        if (empty($result)) {
            return false;
        }

        $json = json_decode($result);
        return $json->access_token;

    }

    public function createPaypalPlan(array $plan, $accessToken = null): array
    {
        if (empty($accessToken)) {
            $accessToken = $this->getPaypalAccessToken();
        }

        if ($accessToken === false) {
            $reponse = [
                'error'   => true,
                'message' => 'Access token not generated'
            ];
            return $reponse;
        }

        $ch = curl_init();

        $frequency = [
            'frequency'      => [
                "interval_unit"  => $plan['unit'],
                "interval_count" => $plan['count']
            ],
            "tenure_type"    => "REGULAR",
            "sequence"       => 1,
            'total_cycles'   => 0,
            "pricing_scheme" => [
                'fixed_price' => [
                    'value'         => $plan['price'],
                    'currency_code' => $plan['currency']
                ]
            ]
        ];


        $values = array(
            'product_id'          => $plan['product_id'],
            'name'                => $plan['name'],
            'description'         => $plan['description'],
            'status'              => 'ACTIVE',
            'billing_cycles'      => [$frequency],
            'payment_preferences' => [
                "auto_bill_outstanding" => true
            ]
        );


        curl_setopt($ch, CURLOPT_URL, PAYPAL_URL.'/v1/billing/plans');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($values));

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$accessToken;
        $headers[] = 'Paypal-Request-Id: '.$plan['id'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $reponse = [
                'error'   => true,
                'message' => 'Error:'.curl_error($ch),
                'data'    => json_decode($result)
            ];
        } else {
            $reponse = [
                'error'   => false,
                'message' => null,
                'data'    => json_decode($result)
            ];
        }
        curl_close($ch);

        return $reponse;
    }

    public function dd($array){
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }

    public function invoke($page_request){

        // take everything before "?" as GET vars come after it
        $page_request = explode("?",$page_request);
        $page_request = $page_request['0'];

        $request = ltrim($page_request, '/');
        //$request  = str_replace("/", "", $page_request);

        define("REQUEST", $request);

        // redirect old login
        if(REQUEST == "login" || REQUEST == "login/") {
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: '.SITE_URL.'?signIn=true');
            exit;
        }



        // redirect old profile pages
        if(strpos(REQUEST, 'my-account/') !== false) {
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: '.SITE_URL.'?signIn=true');
            exit;
        }

        if(strpos(REQUEST, 'profile/') !== false) {
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: '.SITE_URL.'?signIn=true');
            exit;
        }

        if(strpos(REQUEST, 'lesson/') !== false) {
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: '.SITE_URL.'?signIn=true');
            exit;
        }

        if(REQUEST == "review-your-course" || REQUEST == "review-your-course/") {
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: '.SITE_URL.'courses/review');
            exit;
        }

        if(REQUEST == "become-an-affiliate" || REQUEST == "become-an-affiliate/") {
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: '.SITE_URL.'become-affiliate');
            exit;
        }

        if(REQUEST == "tv" || REQUEST == "tv/") {
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: '.SITE_URL.'courses?ref=303');
            exit;
        }

        if($_GET["ref"] != "" && $_GET["redirect"] == "true") {

            $getString = '';
            foreach($_GET as $key => $value) {
                if($key != "ref" && $key != "redirect" && $key != "request") {
                    $getString .= '&'.$key.'='.$value;
                }
            }

            header('Location: '.SITE_URL.$request.'?ref='.$_GET["ref"].$getString);
            exit;
        }

        // make everything with trailing slash work
        if ($this->endsWith($request,'/') != false && SITE_TYPE == "uk")
        {

            $getString = '';
            foreach($_GET as $key => $value) {
                if($key != "ref" && $key != "request") {
                    $getString .= '&'.$key.'='.$value;
                }
            }


            $request = rtrim($request, '/');
            header("HTTP/1.1 301 Moved Permanently");

            if($_GET["ref"] != "") {
                header('Location: '.SITE_URL.$request.'?ref='.$_GET["ref"].'&redirect=true'.$getString); // add affiliate code if there is one
            } else {
                header('Location: '.SITE_URL.$request.'?'.$getString);
            }

            exit;
        }

        // check for redirects
        $redirect = ORM::for_table("redirects")->where("rFrom", $request)->find_one();

        if($redirect->id != "") {
            header('Location: '.$redirect->rTo);
            exit;
        }


        $allowed_routes = array(
            array(
                'url' => 'ajax/blume',
                'controller' => 'blumeController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'ajax',
                'controller' => 'blumeController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'courses', // this is also used to load the view
                'controller' => 'courseController', // set a controller for this route
                'signInRequired' => "false" // should a user be signed into access this
            ),
            array(
                'url' => 'staff-training',
                'controller' => 'courseController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'support',
                'controller' => 'courseController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'support/help-articles',
                'controller' => 'supportController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'support/resources',
                'controller' => 'resourceController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'checkout',
                'controller' => 'cartController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'create-bundle',
                'controller' => 'courseController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'newsletter/thank-you',
                'controller' => 'cartController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'dashboard',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/billing/balance',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/premium',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/badges',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/badges/download',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'customer-service-portal',
                'controller' => 'accountController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'dashboard/certificates',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/support',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/offers',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'profile',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'gift',
                'controller' => 'giftController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'gift/complete',
                'controller' => 'giftController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'become-an-affiliate/partner-terms-conditions',
                'controller' => 'achieverBoardController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'achievers',
                'controller' => 'achieverBoardController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'achievers/submit',
                'controller' => 'achieverBoardController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'achievers/thanks',
                'controller' => 'achieverBoardController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'blume/achievers',
                'controller' => 'achieverBoardController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/account',
                'controller' => 'achieverBoardController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/account/export-business-emails',
                'controller' => 'accountController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'contact',
                'controller' => 'supportController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'rewards',
                'controller' => 'rewardsController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/courses',
                'controller' => 'courseController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/courses/saved',
                'controller' => 'courseController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/student-card',
                'controller' => 'courseController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'blume/login',
                'controller' => 'blumeController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'blume/login/confirm',
                'controller' => 'blumeController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'blume/dashboard',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/pages',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/reporting',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/reporting/ncfe',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/reporting/leaderboard',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/reporting/board-stats',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/accounts/ncfe',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/reporting/sales',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/courses/reviews/edit',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/orders/gifted',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/orders/addresses',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/orders/addresses',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/orders/subscriptions',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/reporting/subscriptions',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/orders/subs',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/courses/ratings',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/orders/gifted-vouchers',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/orders/gifted-vouchers',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/orders/view',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/orders/gifted',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/orders/subscriptions',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/orders/certificates',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/courses',
                'controller' => 'courseController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/courses/modules',
                'controller' => 'courseModuleController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/messages',
                'controller' => 'messageController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/jobs',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/pages/edit',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/settings/redirects',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/courses/categories',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/courses/providers',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/courses/categories/edit',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/courses/providers/edit',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/courses',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/courses/edit',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/courses/pdf',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/courses/relations',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/support/help-articles',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/support/help-videos',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/portal',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/support/help-articles/edit',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/support/faqs',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/support/faqs/edit',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/support/blog',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/support/blog/categories',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/support/blog/edit',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/support/resources',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/support/resources/edit',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/settings',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/orders',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/coupons',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/reviews',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/vouchers',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/offers',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/marketing/coupons',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/marketing/vouchers',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/marketing/offers',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/orders',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/orders',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/orders/abandoned',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/orders/certificates',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/orders/qualifications',
                'controller' => 'orderController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/orders/printed',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/orders/abandoned',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/orders/qualifications',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/orders/printed',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/courses/enrollments',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/messages',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/courses/modules/reorder',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/courses/modules/edit',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/courses/modules/faqs',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/courses/modules/faqs/edit',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'messages',
                'controller' => 'messageController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'course/complete',
                'controller' => 'courseController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'course/complete/review',
                'controller' => 'courseController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/billing',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/subscribe',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'testimonials',
                'controller' => 'testimonialController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'blume/content/testimonials',
                'controller' => 'testimonialController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/reporting/courses',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/reporting/customers',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/reporting/courses/categories',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/reporting/dashboard',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/reporting/dashboard/csv',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/marketing/why',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/content',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/content/team',
                'controller' => 'aboutTeamController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/content/team/reorder',
                'controller' => 'aboutTeamController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/content/affiliate-faqs',
                'controller' => 'aboutTeamController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/content/affiliate-faqs/edit',
                'controller' => 'aboutTeamController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/content/email-templates',
                'controller' => 'emailTemplateController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/content/email-templates',
                'controller' => 'emailTemplateController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/content/email-templates/edit',
                'controller' => 'emailTemplateController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/accounts',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/admins',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/ncfe/tutors',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/ncfe/messages',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/ncfe/iqa',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/admins/logs',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/accounts/view',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/accounts',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/currencies',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/currencies/catalogue',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/google/auth/redirect',
                'controller' => 'googleAuthController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blog',
                'controller' => 'blogController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'dashboard/courses/notes',
                'controller' => 'courseController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/recommend',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/rewards',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'sitemap/courses',
                'controller' => 'courseController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'sitemap/pages',
                'controller' => 'courseController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'sitemap/categories',
                'controller' => 'courseController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'sitemap/blog',
                'controller' => 'courseController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'sitemap/help-articles',
                'controller' => 'courseController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'become-affiliate',
                'controller' => 'courseController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'about',
                'controller' => 'aboutTeamController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'teens-unite',
                'controller' => 'courseController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'search',
                'controller' => 'courseController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'dashboard/support',
                'controller' => 'supportController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'courses/review',
                'controller' => 'supportController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'blume/courses/reviews',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/courses/ratings',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/offers',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/offers/edit',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/customer-service',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'redeem',
                'controller' => 'redeemController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'redeem/select',
                'controller' => 'redeemController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'fb-catalog',
                'controller' => 'fbCatalogController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'fb-catalog/generate',
                'controller' => 'fbCatalogController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'update-uk-newsletter',
                'controller' => 'updateNewsletterController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'learn',
                'controller' => 'courseController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'blume/support/learning-request',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/support/learning-request',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'subscription',
                'controller' => 'subController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'subscription-sale',
                'controller' => 'subController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'subscription-sale-monthly',
                'controller' => 'subController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'subscription-renew',
                'controller' => 'subController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'subscription-discount',
                'controller' => 'subController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'subscription-old',
                'controller' => 'subController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'premium',
                'controller' => 'subController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'dashboard/iqa',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/iqa/tutors',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/iqa/students',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/iqa/assignments',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/ncfe',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/ncfe/messages',
                'controller' => 'messageController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/ncfe/documents',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/ncfe/courses',
                'controller' => 'courseController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/ncfe/messages',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/ncfe/assessments',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/ncfe/assignments',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/ncfe/thankyou',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/tutor',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/tutor/students',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/tutor/assignments',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'dashboard/tutor/messages',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'fitness',
                'controller' => 'accountController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'account/confirm',
                'controller' => 'accountController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'become-a-personal-trainer',
                'controller' => 'accountController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'blume/content/faqs',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/content/faqs/edit',
                'controller' => 'blumeController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'api/courses',
                'controller' => 'coursesApiController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'api/courses/get-course',
                'controller' => 'coursesApiController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'api/courses/get-course-by-title',
                'controller' => 'coursesApiController',
                'signInRequired' => "false"
            ),
            array(
                'url' => 'blume/jobs',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'dashboard/jobs',
                'controller' => 'jobsController',
                'signInRequired' => "true"
            ),
            array(
                'url' => 'blume/Banner',
                'controller' => 'giftController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/banner',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/jobs/viewClickInfo',
                'controller' => 'jobsController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'blume/datatables/jobs/viewClickInfo',
                'controller' => 'blumeNewController',
                'signInRequired' => "blume"
            ),
            array(
                'url' => 'learning-report',
                'controller' => 'learningReportController',
                'signInRequired' => "blume"
            ),
        );

        // see if we need to load a "static" page first
        $page = ORM::for_table("pages")->where("slug", $request)->find_one();

        if($page->id != "") {

            // page exists, load page template, etc

            include __DIR__ . '/pageController.php';
            $this->controller = new pageController();

            include __DIR__ . '/../view/page/responsive.php';

        } else {

            // check everything else
            $check_route = $this->checkRoute($allowed_routes, "url", REQUEST);

            // if route matches, include view
            if($check_route == true) {
                include __DIR__ . '/../view/'.REQUEST.'/responsive.php';

            } else if ($request === "") {

                // if empty, load home page
                include __DIR__ . '/../view/home/responsive.php';

            } else if (strstr($request, '/', true) == "start") {
                // if single product
                $_GET["request"]  = str_replace("start/", "", $request);

                include __DIR__ . '/courseController.php';
                $this->controller = new courseController();

                include __DIR__ . '/../view/courses/single/start/responsive.php';

            } else if (strpos($request, 'checkout/confirmation') !== false) {
                // so we can put product name in the url for marketing tracking
                $_GET["request"]  = str_replace("checkout/confirmation", "", $request);

                include __DIR__ . '/cartController.php';
                $this->controller = new cartController();

                include __DIR__ . '/../view/checkout/confirmation/responsive.php';

            }
            else if (strpos($request, 'special-offer') !== false) {

                $_GET["request"]  = str_replace("special-offer/", "", $request);

                include __DIR__ . '/specialOfferController.php';
                $this->controller = new specialOfferController();

                include __DIR__ . '/../view/special-offer/responsive.php';

            }
            else if (strpos($request, 'offers') !== false) {

                $_GET["request"]  = str_replace("offers/", "", $request);

                include __DIR__ . '/specialOfferController.php';
                $this->controller = new specialOfferController();

                include __DIR__ . '/../view/special-offer/responsive.php';

            }
            else if (strpos($request, "/additional-reading/") !== false) {

                $parameters = explode('/', $request);
                // if single product
                $_GET["request"]  = $parameters[0];

                include __DIR__ . '/courseController.php';
                $this->controller = new courseController();

                include __DIR__ . '/../view/courses/single/additional-reading/responsive.php';

            }
            else if (strpos($request, "get-our-newsletter") !== false) {

                include __DIR__ . '/../view/newsletter/responsive.php';

            }
            else if (strstr($request, '/', true) == "modules") {

                $_GET["request"]  = str_replace("modules/", "", $request);

                include __DIR__ . '/courseController.php';
                $this->controller = new courseController();

                include __DIR__ . '/../view/courses/single/modules/responsive.php';

            }
            else if (strstr($request, '/', true) == "module") {

                $_GET["request"]  = str_replace("module/", "", $request);

                include __DIR__ . '/courseController.php';
                $this->controller = new courseController();

                include __DIR__ . '/../view/courses/single/module/responsive.php';

            }
            else if (strstr($request, '/', true) == "quiz") {

                $_GET["request"]  = str_replace("quiz/", "", $request);

                include __DIR__ . '/courseController.php';
                $this->controller = new courseController();

                include __DIR__ . '/../view/courses/single/quiz/responsive.php';

            }
            else if (strstr($request, '/', true) == "time") {

                $_GET["request"]  = str_replace("time/", "", $request);

                include __DIR__ . '/courseController.php';
                $this->controller = new courseController();

                include __DIR__ . '/../view/courses/single/time/responsive.php';

            }
            /*else if (strpos($request, 'en/course') !== false) { // no longer needed due to serving multi-domains
                // if single course
                $_GET["request"]  = str_replace("en/course/", "", $request);

                include __DIR__ . '/courseController.php';
                $this->controller = new courseController();

                include __DIR__ . '/../view/courses/single/responsive.php';

            }*/
            else if (strstr($request, '/', true) == "course") {
                // if single course
                $_GET["request"]  = str_replace("course/", "", $request);

                include __DIR__ . '/courseController.php';
                $this->controller = new courseController();

                include __DIR__ . '/../view/courses/single/responsive.php';

            }
            else if (strpos($request, 'support/help-articles') !== false) {
                // if help article
                $_GET["request"]  = str_replace("support/help-articles/", "", $request);

                include __DIR__ . '/supportController.php';
                $this->controller = new supportController();

                include __DIR__ . '/../view/support/help-articles/single/responsive.php';

            }
            else if (strpos($request, 'support/resources') !== false) {
                // if help article
                $_GET["request"]  = str_replace("support/resources/", "", $request);

                include __DIR__ . '/resourceController.php';
                $this->controller = new resourceController();

                include __DIR__ . '/../view/support/resources/single/responsive.php';

            }
            else if (strpos($request, 'blog/category') !== false) {
                $params = explode('/', $request);

                // if help article
                $_GET["request"]  = $params[2];

                include __DIR__ . '/blogController.php';
                $this->controller = new blogController();

                include __DIR__ . '/../view/blog/responsive.php';

            }
            else if (strpos($request, 'stripe/response') !== false) {
                $params = explode('/', $request);

                include __DIR__ . '/stripeController.php';
                $this->controller = new stripeController();

                include __DIR__ . '/../view/checkout/includes/stripe-response.php';

            }
            else if (strpos($request, 'blog') !== false) {
                // if help article
                $_GET["request"]  = str_replace("blog/", "", $request);

                include __DIR__ . '/blogController.php';
                $this->controller = new blogController();

                include __DIR__ . '/../view/blog/single/responsive.php';

            }
            else if (strpos($request, 'certificate') !== false) {
                // if help article
                $_GET["request"]  = str_replace("certificate/", "", $request);

                include __DIR__ . '/certificateController.php';
                $this->controller = new certificateController();

                include __DIR__ . '/../view/certificate/responsive.php';

            }
            else if (strpos($request, 'invoice') !== false) {
                // if help article
                $_GET["request"]  = str_replace("invoice/", "", $request);

                include __DIR__ . '/invoiceController.php';
                $this->controller = new invoiceController();

                include __DIR__ . '/../view/invoice/responsive.php';

            }
            else if (strpos($request, 'courses') !== false) {
                // if help article
                $_GET["request"]  = str_replace("courses/", "", $request);

                include __DIR__ . '/courseController.php';
                $this->controller = new courseController();

                include __DIR__ . '/../view/courses/responsive.php';

            }
            else if (strpos($request, 'refer') !== false) {

                // if refer a friend
                $_GET["request"]  = str_replace("refer/", "", $request);

                $rafCode = $_GET["request"];
                $referrer = ORM::For_table("accounts")->where("rafCode", $rafCode)->find_one();

                if($referrer->id != "" && $referrer->id != CUR_ID_FRONT) { // apply discount if referrer exists and is NOT the current signed in user

                    // see if coupon already exists
                    $existing = ORM::for_table("coupons")
                        ->where("code", $rafCode)
                        ->find_one();

                    if ($existing->id == "") {
                        // then we create the code
                        $item = ORM::for_table("coupons")->create();

                        $item->set(
                            array(
                                'code'       => $rafCode,
                                'type'       => "p",
                                'value'      => "75",
                                'courses'    => "",
                                'totalLimit' => "9999999",
                                'expiry'     => "2099-01-01 11:59:59",
                                'applyTo'    => "basket",
                            )
                        );

                        $item->set_expr("whenUpdated", "NOW()");
                        $item->set_expr("whenAdded", "NOW()");

                        $item->save();

                    }

                    // we use this session to set an automatic discount when something is added to cart
                    $_SESSION["automaticCartDiscountCode"] = $rafCode;

                    // redirect to courses page with message
                    header('Location: '.SITE_URL.'courses?raf=true');

                } else {

                    // redirect to courses page without message
                    header('Location: '.SITE_URL.'courses');

                }

                exit;

            }
            else if (strpos($request, 'stripe/webhook/subscription') !== false) {
                include __DIR__ . '/stripeController.php';
                $this->controller = new stripeController();
                $response = $this->controller->stripeWebhook();
                echo json_encode($response);
                exit();
            }
            else if (strpos($request, 'paypal/webhook/subscription') !== false) {
                include __DIR__ . '/paypalController.php';
                $this->controller = new paypalController();
                $response = $this->controller->paypalWebhook();
                echo json_encode($response);
                exit();
            }
            else if (strpos($request, 'apple-pay') !== false) {
                include __DIR__ . '/../view/apple-pay.php';
            }
            else if (strpos($request, 'dashboard/tutor/student') !== false) {
                $_GET["request"]  = str_replace("dashboard/tutor/student/", "", $request);

                include __DIR__ . '/tutorController.php';
                $this->controller = new tutorController();

                include __DIR__ . '/../view/dashboard/tutor/students/progress.php';

            }
            else
            {
                // check db
                // 404 not found error
                header("HTTP/1.0 404 Not Found"); // set appropriate header/response code
                include __DIR__ . '/../view/404/responsive.php';

            }

        }

        if(SIGNED_IN == true && ADMIN_ACCESSED != true) { // we do not want to update if an admin is on the account

            // set last active, used in admin system to see when users are online, etc
            $account = ORM::for_table("accounts")->find_one(CUR_ID_FRONT);
            $account->set_expr("lastActive", "NOW()");
            $account->save();

        }


    }

    function removeFolder($folderName) {

        if (is_dir($folderName))

            $folderHandle = opendir($folderName);



        if (!$folderHandle)

            return false;



        while($file = readdir($folderHandle)) {

            if ($file != "." && $file != "..") {

                if (!is_dir($folderName."/".$file))

                    unlink($folderName."/".$file);

                else

                    $this->removeFolder($folderName.'/'.$file);

            }

        }



        closedir($folderHandle);

        rmdir($folderName);

        return true;

    }

    public function isActiveSubscription($userID): bool
    {
        $user = ORM::for_table('accounts')->find_one($userID);
        if((@$user->subExpiryDate && $user->subExpiryDate >= date("Y-m-d"))
            || ($user->isAdminSub == 1 && empty($user->subExpiryDate))
            || ($user->subActive == 1)
        ) {
            return true;
        }
        return false;
    }

    
    
}

?>
