<?php

require_once(__DIR__ . '/blogController.php');
require_once(__DIR__ . '/courseController.php');
require_once(__DIR__ . '/mediaController.php');
require_once(__DIR__ . '/cacheController.php');
require_once(__DIR__ . '/courseCategoryController.php');
require_once(__DIR__ . '/courseProvidersController.php');
require_once(__DIR__ . '/courseModuleController.php');
require_once(__DIR__ . '/testimonialController.php');
require_once(__DIR__ . '/rewardsAssignedController.php');
require_once(__DIR__ . '/resourceController.php');
require_once(__DIR__ . '/emailTemplateController.php');
require_once(__DIR__ . '/courseTimeProgressController.php');
require_once(__DIR__ . '/../classes/dompdf/autoload.inc.php');
require_once(__DIR__ . '/quizController.php');


use Dompdf\Dompdf;
use Dompdf\Options;
use Mpdf\Mpdf;

class blumeNewController extends Controller
{

    /**
     * @var blogController
     */
    protected $blogs;

    /**
     * @var courseController
     */
    protected $courses;

    /**
     * @var mediaController
     */
    protected $medias;

    /**
     * @var cacheController
     */
    protected $cache;

    /**
     * @var courseCategoryController
     */
    protected $courseCategory;


    /**
     * @var testimonialController
     */
    protected $testimonialCategory;

    /**
     * @var rewardsAssignedController
     */
    protected $rewardsAssigned;

    /**
     * @var resouceController
     */
    protected $resource;

    /**
     * @var courseModuleController
     */
    protected $courseModules;

    /**
     * @var emailTemplateController
     */
    protected $emailTemplates;

    /**
     * @var courseTimeProgressController
     */
    protected $courseTimeProgress;

    /**
     * @var quizController
     */
    protected $quizzes;

    public function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;
        $this->rewardsAssigned = new rewardsAssignedController();

        // if admin is not logged in then dont let them do anything
        if (CUR_ID == "") {
            header('Location: ' . SITE_URL . 'blume/login');
            exit;
        }

        $this->blogs = new blogController();
        $this->courses = new courseController();
        $this->medias = new mediaController();
        $this->quizzes = new quizController();
        $this->resource = new resourceController();
        $this->emailTemplates = new emailTemplateController();
        $this->cache = new cacheController();
        $this->courseTimeProgress = new courseTimeProgressController();
    }

    public function recordLog($action)
    {
        // Record a log of admin and customer service actions

        // attempts to get the IP of the current user
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $item = ORM::for_table("blumeLogs")->create();

        $item->userID = CUR_ID;
        $item->set_expr("dateTime", "NOW()");
        $item->ip = $ip;
        $item->action = $action;

        $item->save();
    }

    public function createRedirect()
    {
        $item = ORM::for_Table("redirects")->create();

        $item->rTo = $this->post["rTo"];
        $item->rFrom = $this->post["rFrom"];

        $item->save();

        $this->recordLog('created a new redirect from ' . $this->post["rFrom"] . ' to ' . $this->post["rTo"]);

        $this->redirectJS(SITE_URL . 'blume/settings/redirects');
    }

    public function deleteRedirect()
    {
        $item = ORM::for_Table("redirects")->where("id", $this->get["id"])
            ->delete_many();

        $this->recordLog('deleted a redirect');

        $this->redirectJS(SITE_URL . 'blume/settings/redirects');
    }

    public function editCourseCategory()
    {
        if ($this->post["title"] == "") {
            $this->setAlertDanger("Please ensure all required data is present.");
            exit;
        }

        $item = ORM::for_table("courseCategories")
            ->find_one($this->post["itemID"]);

        $item->title = $this->post["title"];
        $item->parentID = @$this->post["parentID"] ? $this->post["parentID"]
            : null;
        $item->showOnHome = $this->post["showOnHome"];
        $item->description = $this->post["description"];
        $item->meta_title = $this->post["meta_title"];
        $item->meta_keywords = $this->post["meta_keywords"];
        $item->meta_description = $this->post["meta_description"];

        $item->save();

        // Update image
        if ($this->checkFileUploadSelected() == true) {
            $sizes = ['large', 'medium', 'thumb'];
            $model = [
                'type' => courseCategoryController::class,
                'id' => $this->post["itemID"]
            ];
            $this->medias->uploadFile(
                $sizes,
                $model,
                'uploaded_file',
                'main_image',
                true
            );
        }

        $this->recordLog('edited the following course category: ' . $this->post["title"]);

        $this->setAlertSuccess("Course category successfully updated");
    }

    public function editBlogCategory()
    {
        if ($this->post["title"] == "") {
            $this->setAlertDanger("Please ensure all required data is present.");
            exit;
        }

        $item = ORM::for_table("blogCategories")
            ->find_one($this->post["itemID"]);

        $item->title = $this->post["title"];

        $item->save();

        $this->recordLog('edited the following blog category: ' . $this->post["title"]);

        $this->setAlertSuccess("Blog category successfully updated");
    }

    public function addCourseCategory()
    {
        if ($this->post["title"] == "") {
            $this->setAlertSuccess("Please ensure all required data is present.");
            exit;
        }

        $item = ORM::for_table("courseCategories")->create();

        $item->title = $this->post["title"];
        $item->parentID = @$this->post["parentID"] ? $this->post["parentID"]
            : null;
        $item->icon = $this->post["icon"];
        $item->meta_title = $this->post["meta_title"];
        $item->meta_keywords = $this->post["meta_keywords"];
        $item->meta_description = $this->post["meta_description"];
        $item->slug = $this->createSlug($this->post["title"]);

        $item->save();

        $this->recordLog('added a new course category: ' . $this->post["title"]);

        $this->setAlertSuccess("Course successfully created");
        $this->redirectJS(SITE_URL . 'blume/courses/categories');
    }

    public function addCourseProviders()
    {
//
        if ($this->post["name"] == "") {
            $this->setAlertSuccess("Please ensure all required data is present.");
            exit;
        }

        $item = ORM::for_table("courseProviders")->create();

        $item->name = $this->post["name"];
        $item->slug = $this->createSlug($this->post["name"]);

        $item->save();
//        echo "<pre>";
//        print_r($item);
//        die;
        // Update image
        if ($this->checkFileUploadSelected() == true) {
            $sizes = [];
            $model = [
                'type' => courseProvidersController::class,
                'id' => $item->id
            ];
            $this->medias->uploadFile($sizes, $model, 'uploaded_file', 'main_image', true);
        }

        $this->recordLog('added a new course providers: ' . $this->post["name"]);

        $this->setAlertSuccess("Course Provider successfully created");
        $this->redirectJS(SITE_URL . 'blume/courses/providers');
    }

    public function addJobs()
    {
        $this->validateValues(
            array(
                "jobTitle",
                "jobDescription",
                "location",
                "salary",
                "applicationLink",
                "closingDate",
                "companyName",
                "jobState"
            )
        );

        $item = ORM::for_table("jobs")->create();
        $item->jobTitle = $this->post["jobTitle"];
        $item->jobDescription = $this->post["jobDescription"];
        $item->location = $this->post["location"];
        $item->salary = $this->post["salary"];
        $item->applicationLink = $this->post["applicationLink"];
        $item->closingDate = $this->post["closingDate"];
        $item->companyName = $this->post["companyName"];
        $item->clickAmount = 0;
        $item->jobState = $this->post["jobState"];


        $item->save();
        $this->recordLog('added a new job post: ' . $this->post["jobTitle"]);

        $this->setAlertSuccess("Job post successfully created");
        $this->redirectJS(SITE_URL . 'blume/jobs');
    }

    public function editJob()
    {
        $this->validateValues(
            array(
                "jobTitle",
                "jobDescription",
                "location",
                "salary",
                "applicationLink",
                "closingDate",
                "companyName",
                "jobState"
            )
        );

        $item = ORM::for_table("jobs")->find_one($this->post["itemID"]);


        $item->set(
            array(
                'jobTitle' => $this->post["jobTitle"],
                'jobDescription' => $this->post["jobDescription"],
                'companyName' => $this->post["companyName"],
                'location' => $this->post["location"],
                'salary' => $this->post["salary"],
                'applicationLink' => $this->post["applicationLink"],
                'closingDate' => $this->post["closingDate"],
                'jobState' => $this->post["jobState"],

            )
        );


        $item->save();


        $this->recordLog('edited a job post: ' . $this->post["jobTitle"]);

        $this->setAlertSuccess("Job post successfully updated");

        $this->redirectJS(SITE_URL . 'blume/jobs');
    }


    public function deleteJob()
    {
        $item = ORM::for_table("jobs")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted a job post');
        $this->redirectJS(SITE_URL . 'blume/jobs');
    }

    public function editJobForm()
    {
        ?>
        <script src='<?= SITE_URL ?>assets/js/tinymce/tinymce.min.js'></script>
        <script>
            tinymce.init({
                selector: '.tinymce',
                plugins: 'table link lists hr textcolor emoticons image imagetools media link preview visualchars visualblocks wordcount template code',
                toolbar: 'undo redo paste | styleselect template | bold italic strikethrough underline | link image media | bullist numlist | aligncenter alignleft alignright alignjustify alignnone | blockquote | backcolor forecolor | removeformat visualblocks code',
                height: '150',
                templates: [{
                    title: "Default Starter",
                    description: "",
                    url: "<?= SITE_URL ?>assets/cdn/editorTemplates/moduleDefault.html"
                },
                    {
                        title: "Blue Summary Box",
                        description: "",
                        url: "<?= SITE_URL ?>assets/cdn/editorTemplates/blueSummary.html"
                    },
                    {
                        title: "Grey Background Content",
                        description: "",
                        url: "<?= SITE_URL ?>assets/cdn/editorTemplates/greyBackground.html"
                    },
                    {
                        title: "Did You Know / Tip",
                        description: "",
                        url: "<?= SITE_URL ?>assets/cdn/editorTemplates/didYouKnow.html"
                    },
                    {
                        title: "Paper / Notepad",
                        description: "",
                        url: "<?= SITE_URL ?>assets/cdn/editorTemplates/paper.html"
                    }
                ],
                content_css: "https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css,<?= SITE_URL ?>assets/css/global.css,<?= SITE_URL ?>assets/css/editor.css",
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                // enable title field in the Image dialog
                image_title: true,
                // enable automatic uploads of images represented by blob or data URIs
                automatic_uploads: true,
                // URL of our upload handler (for more details check: https://www.tinymce.com/docs/configure/file-image-upload/#images_upload_url)
                images_upload_url: '<?= SITE_URL ?>ajax?c=blumeNew&a=tiny-mce-uploader',
                // here we add custom filepicker only to Image dialog
                file_picker_types: 'image',
                // and here's our custom image picker
                image_advtab: true,
                file_picker_callback: function (cb, value, meta) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');

                    // Note: In modern browsers input[type="file"] is functional without
                    // even adding it to the DOM, but that might not be the case in some older
                    // or quirky browsers like IE, so you might want to add it to the DOM
                    // just in case, and visually hide it. And do not forget do remove it
                    // once you do not need it anymore.

                    input.onchange = function () {
                        var file = this.files[0];

                        var reader = new FileReader();
                        reader.onload = function () {
                            // Note: Now we need to register the blob in TinyMCEs image blob
                            // registry. In the next release this part hopefully won't be
                            // necessary, as we are looking to handle it internally.
                            var id = 'imageID' + (new Date()).getTime();
                            var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            var base64 = reader.result.split(',')[1];
                            var blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);

                            // call the callback and populate the Title field with the file name
                            cb(blobInfo.blobUri(), {
                                title: file.name
                            });
                        };
                        reader.readAsDataURL(file);
                    };

                    input.click();
                }
            });
        </script>
        <?php

        $item = ORM::for_table("jobs")->find_one($this->get["id"]);

        ?>
        <link rel="stylesheet" type="text/css"
              href="<?= SITE_URL ?>assets/blume/js/plugins/select2/css/core.css">
        <style>
            .select2.select2-container {
                width: 100% !important;
            }
        </style>
        <script src='<?= SITE_URL ?>assets/blume/js/plugins/select2/select2.min.js'></script>

        <div class="form-group">
            <label>Job Title</label>
            <input type="text" name="jobTitle" class="form-control"
                   value="<?= $item->jobTitle ?>"/>
        </div>
        <div class="form-group">
            <label>Job Description</label>
            <textarea name="jobDescription" class="tinymce  form-control"> <?= $item->jobDescription ?></textarea>
        </div>
        <div class="form-group">
            <label>Company Name</label>
            <input type="text" name="companyName" class="form-control"
                   value="<?= $item->companyName ?>"/>
        </div>
        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" class="form-control"
                   value="<?= $item->location ?>"/>
        </div>
        <div class="form-group">
            <label>Salary</label>
            <input type="number" max="9999999999" step="any" name="salary" class="form-control"
                   value="<?= $item->salary ?>"/>
        </div>
        <div class="form-group">
            <label>Application Link</label>
            <input type="url" name="applicationLink" class="form-control"
                   value="<?= $item->applicationLink ?>"/>
        </div>
        <div class="form-group">
            <label>Closing Date</label>
            <input type="date" name="closingDate" class="form-control"
                   value="<?= $item->closingDate ?>"/>
        </div>
        <div class="form-group">
            <label>Job State</label>
            <select name="jobState" class="form-control" value="<?= $item->jobState ?>">
                <option value="">Select a Job State</option>
                <option value="on">On</option>
                <option value="off">Off</option>
            </select>
        </div>

        <input type="hidden" name="itemID" value="<?= $item->id ?>"/>
        <script>
            $('.select2').select2();
        </script>
        <?php
    }


    public function addBanner()
    {
        $this->validateValues(array("bannerColor", "bannerTextColor", "bannerState"));
        $item = ORM::for_table("Banner")->create();
        $item->bannerTextGBP = $this->post["bannerTextGBP"];
        $item->bannerTextUSD = $this->post["bannerTextUSD"];
        $item->bannerTextEUR = $this->post["bannerTextEUR"];
        $item->bannerTextCAD = $this->post["bannerTextCAD"];
        $item->bannerTextAUD = $this->post["bannerTextAUD"];
        $item->bannerTextNZD = $this->post["bannerTextNZD"];
        $item->bannerColor = $this->post["bannerColor"];
        $item->bannerTextColor = $this->post["bannerTextColor"];
        $item->bannerRef = $this->post["bannerRef"];
        $item->BannerTimer = $this->post["bannerTimer"];
        $item->bannerState = $this->post["bannerState"];


        $item->save();
        $this->recordLog('added a new banner post: ' . $this->post["bannerTextGBP"]);

        $this->setAlertSuccess("banner post successfully created");
        $this->redirectJS(SITE_URL . 'blume/Banner');
    }

    public function editBanner()
    {
        $this->validateValues(array("bannerColor", "bannerTextColor", "bannerState"));


        $item = ORM::for_table("Banner")->find_one($this->post["itemID"]);


        $item->set(
            array(
                'bannerTextGBP' => $this->post["bannerTextGBP"],
                'bannerTextUSD' => $this->post["bannerTextUSD"],
                'bannerTextEUR' => $this->post["bannerTextEUR"],
                'bannerTextCAD' => $this->post["bannerTextCAD"],
                'bannerTextAUD' => $this->post["bannerTextAUD"],
                'bannerTextNZD' => $this->post["bannerTextNZD"],
                'bannerColor' => $this->post["bannerColor"],
                'bannerTextColor' => $this->post["bannerTextColor"],
                'bannerRef' => $this->post["bannerRef"],
                'bannerTimer' => $this->post["bannerTimer"],
                'bannerState' => $this->post["bannerState"],
            )
        );


        $item->save();


        $this->recordLog('edited a banner post: ' . $this->post["bannerTextGBP"]);

        $this->setAlertSuccess("banner post successfully updated");

        $this->redirectJS(SITE_URL . 'blume/Banner');
    }

    public function deleteBanner()
    {
        $item = ORM::for_table("Banner")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted a banner post');
        $this->redirectJS(SITE_URL . 'blume/Banner');
    }

    public function editBannerForm()
    {
        ?>
        <script src='<?= SITE_URL ?>assets/js/tinymce/tinymce.min.js'></script>
        <script>
            tinymce.init({
                selector: '.tinymce',
                plugins: 'table link lists hr textcolor emoticons image imagetools media link preview visualchars visualblocks wordcount template code',
                toolbar: 'undo redo paste | styleselect template | bold italic strikethrough underline | link image media | bullist numlist | aligncenter alignleft alignright alignjustify alignnone | blockquote | backcolor forecolor | removeformat visualblocks code',
                height: '150',
                templates: [{
                    title: "Default Starter",
                    description: "",
                    url: "<?= SITE_URL ?>assets/cdn/editorTemplates/moduleDefault.html"
                },
                    {
                        title: "Blue Summary Box",
                        description: "",
                        url: "<?= SITE_URL ?>assets/cdn/editorTemplates/blueSummary.html"
                    },
                    {
                        title: "Grey Background Content",
                        description: "",
                        url: "<?= SITE_URL ?>assets/cdn/editorTemplates/greyBackground.html"
                    },
                    {
                        title: "Did You Know / Tip",
                        description: "",
                        url: "<?= SITE_URL ?>assets/cdn/editorTemplates/didYouKnow.html"
                    },
                    {
                        title: "Paper / Notepad",
                        description: "",
                        url: "<?= SITE_URL ?>assets/cdn/editorTemplates/paper.html"
                    }
                ],
                content_css: "https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css,<?= SITE_URL ?>assets/css/global.css,<?= SITE_URL ?>assets/css/editor.css",
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                // enable title field in the Image dialog
                image_title: true,
                // enable automatic uploads of images represented by blob or data URIs
                automatic_uploads: true,
                // URL of our upload handler (for more details check: https://www.tinymce.com/docs/configure/file-image-upload/#images_upload_url)
                images_upload_url: '<?= SITE_URL ?>ajax?c=blumeNew&a=tiny-mce-uploader',
                // here we add custom filepicker only to Image dialog
                file_picker_types: 'image',
                // and here's our custom image picker
                image_advtab: true,
                file_picker_callback: function (cb, value, meta) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');

                    // Note: In modern browsers input[type="file"] is functional without
                    // even adding it to the DOM, but that might not be the case in some older
                    // or quirky browsers like IE, so you might want to add it to the DOM
                    // just in case, and visually hide it. And do not forget do remove it
                    // once you do not need it anymore.

                    input.onchange = function () {
                        var file = this.files[0];

                        var reader = new FileReader();
                        reader.onload = function () {
                            // Note: Now we need to register the blob in TinyMCEs image blob
                            // registry. In the next release this part hopefully won't be
                            // necessary, as we are looking to handle it internally.
                            var id = 'imageID' + (new Date()).getTime();
                            var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            var base64 = reader.result.split(',')[1];
                            var blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);

                            // call the callback and populate the Title field with the file name
                            cb(blobInfo.blobUri(), {
                                title: file.name
                            });
                        };
                        reader.readAsDataURL(file);
                    };

                    input.click();
                }
            });
        </script>
        <?php

        $item = ORM::for_table("Banner")->find_one($this->get["id"]);

        ?>
        <link rel="stylesheet" type="text/css"
              href="<?= SITE_URL ?>assets/blume/js/plugins/select2/css/core.css">
        <style>
            .select2.select2-container {
                width: 100% !important;
            }
        </style>
        <script src='<?= SITE_URL ?>assets/blume/js/plugins/select2/select2.min.js'></script>

        <div class="form-group">
            <label>Banner Text GBP</label>
            <textarea name="bannerTextGBP" class="tinymce form-control"><?= $item->bannerTextGBP ?></textarea>
        </div>
        <div class="form-group">
            <label>Banner Text USD</label>
            <textarea name="bannerTextUSD" class="tinymce form-control"><?= $item->bannerTextUSD ?></textarea>
        </div>
        <div class="form-group">
            <label>Banner Text EUR</label>
            <textarea name="bannerTextEUR" class="tinymce form-control"><?= $item->bannerTextEUR ?></textarea>
        </div>
        <div class="form-group">
            <label>Banner Text CAD</label>
            <textarea name="bannerTextCAD" class="tinymce form-control"><?= $item->bannerTextCAD ?></textarea>
        </div>
        <div class="form-group">
            <label>Banner Text AUD</label>
            <textarea name="bannerTextAUD" class="tinymce form-control"><?= $item->bannerTextAUD ?></textarea>
        </div>
        <div class="form-group">
            <label>Banner Text NZD</label>
            <textarea name="bannerTextNZD" class="tinymce form-control"><?= $item->bannerTextNZD ?></textarea>
        </div>
        <div class="form-group">
            <label>Banner background Color </label>
            <input type="color" name="bannerColor" class="form-control"
                   value="<?= $item->bannerColor ?>"/>
        </div>
        <div class="form-group">
            <label>Banner Text Color </label>
            <input type="color" name="bannerTextColor" class="form-control"
                   value="<?= $item->bannerTextColor ?>"/>
        </div>
        <div class="form-group">
            <label>Banner Reference</label>
            <input type="text" name="bannerRef" class="form-control"
                   value="<?= $item->bannerRef ?>"/>
        </div>
        <div class="form-group">
            <label>Banner End Date</label>
            <input type="datetime-local" name="bannerTimer" class="form-control"
                   value="<?= $item->bannerTimer ?>"/>
        </div>
        <div class="form-group">
            <label>Banner State</label>
            <select name="bannerState" class="form-control" value="<?= $item->bannerState ?>">
                <option value="">Select a banner State</option>
                <option value="on">On</option>
                <option value="off">Off</option>
            </select>
        </div>


        <input type="hidden" name="itemID" value="<?= $item->id ?>"/>
        <script>
            $('.select2').select2();
        </script>
        <?php
    }

    public function bannerState()
    {
        $item = ORM::for_table("Banner")->find_one($this->get["id"]);


        if ($item->bannerState == "on") {
            $item->set(
                array(
                    'bannerState' => "off",
                )
            );
            $item->save();

            $this->redirectJS(SITE_URL . 'blume/Banner');
        } else {
            if ($item->bannerState == "off") {
                $item->set(
                    array(
                        'bannerState' => "on",
                    )
                );
                $item->save();

                $this->redirectJS(SITE_URL . 'blume/Banner');
            }
        }
    }

    public function jobState()
    {
        $item = ORM::for_table("jobs")->find_one($this->get["id"]);


        if ($item->jobState == "on") {
            $item->set(
                array(
                    'jobState' => "off",
                )
            );
            $item->save();

            $this->redirectJS(SITE_URL . 'blume/jobs');
        } else {
            if ($item->jobState == "off") {
                $item->set(
                    array(
                        'jobState' => "on",
                    )
                );
                $item->save();

                $this->redirectJS(SITE_URL . 'blume/jobs');
            }
        }
    }


    public function deleteCourseProviders()
    {
        $item = ORM::for_table("courseProviders")->find_one($this->get["id"]);
        $item->delete();
    }

    public function editCourseProviders()
    {
        if ($this->post["name"] == "") {
            $this->setAlertSuccess("Please ensure all required data is present.");
            exit;
        }

        $item = ORM::for_table("courseProviders")->find_one($this->post["itemID"]);

        $item->name = $this->post["name"];
        $item->slug = $this->createSlug($this->post["name"]);
        $item->save();

        // Update image
        if ($this->checkFileUploadSelected() == true) {
            $sizes = [];
            $model = [
                'type' => courseProvidersController::class,
                'id' => $this->post["itemID"]
            ];
            $this->medias->uploadFile($sizes, $model, 'uploaded_file', 'main_image', true);
        }

        $this->recordLog('edited the following course providers: ' . $this->post["name"]);

        $this->setAlertSuccess("Course providers successfully updated");
    }

    public function deleteCourseCategory()
    {
        $courses = count(
            ORM::for_table("courses")
                ->where("category", $this->get["id"])
                ->find_many()
        );

        if ($courses
            == 0
        ) { // only allow category deletion if no courses are assigned to category
            $item = ORM::for_table("courseCategories")
                ->where("id", $this->get["id"])->delete_many();
        }

        $this->recordLog('deleted a course category');
    }

    public function userSignInLogs($user, $limit = 50)
    {
        return ORM::for_table("accountSignInLogs")
            ->where("accountID", $user->id)
            ->limit($limit)
            ->order_by_desc("id")
            ->find_many();
    }

    public function createCoupon()
    {
        // check existing
        $existing = ORM::for_table("coupons")
            ->where("code", $this->post["code"])
            ->find_many();

        if (count($existing) > 0) {
            $this->setAlertDanger("A coupon with this code already exists.");
            exit;
        }

        $item = ORM::for_table("coupons")->create();

        $item->set(
            array(
                'code' => $this->post["code"],
                'type' => $this->post["type"],
                'value' => $this->post["value"],
                'currencies' => implode(",", $this->post["currencies"]),
                'courses' => implode(",", $this->post["courses"]),
                'totalLimit' => $this->post["totalLimit"],
                'expiry' => date(
                    'Y-m-d H:i:s',
                    strtotime($this->post["expiry"])
                ),
                'applyTo' => $this->post["applyTo"],
            )
        );

        if ($this->post["valueMax"] != "") {
            $item->valueMax = $this->post["valueMax"];
        }

        if ($this->post["valueMin"] != "") {
            $item->valueMin = $this->post["valueMin"];
        }

        if ($this->post["forUser"] != "") {
            $item->forUser = $this->post["forUser"];
        }

        $item->set_expr("whenUpdated", "NOW()");
        $item->set_expr("whenAdded", "NOW()");

        $item->save();

        $this->recordLog('created a single coupon: ' . $this->post["code"]);

        $this->setAlertSuccess(
            "Your coupon was successfully created: "
            . $this->post["code"]
        );
    }

    public function editCouponForm()
    {
        $item = ORM::for_table("coupons")->find_one($this->get["id"]);

        ?>
        <link rel="stylesheet" type="text/css"
              href="<?= SITE_URL ?>assets/blume/js/plugins/select2/css/core.css">
        <style>
            .select2.select2-container {
                width: 100% !important;
            }
        </style>
        <script src='<?= SITE_URL ?>assets/blume/js/plugins/select2/select2.min.js'></script>

        <div class="form-group">
            <label>Code</label>
            <input type="text" name="code" class="form-control"
                   value="<?= $item->code ?>"/>
        </div>
        <div class="form-group">
            <label>Type</label>
            <select class="form-control" name="type">
                <option value="v">Money (Set Value) Off</option>
                <option value="p"
                        <?php
                        if ($item->type == "p") { ?>selected<?php
                } ?>>
                    Percentage (%) Off
                </option>
            </select>
        </div>
        <div class="form-group">
            <label>Value</label>
            <input type="text" name="value" placeholder="i.e. 10"
                   class="form-control" value="<?= $item->value ?>"/>
        </div>
        <div class="form-group">
            <label>Use Limit</label>
            <input type="number" name="totalLimit" placeholder="0"
                   class="form-control" value="<?= $item->totalLimit ?>"/>
        </div>
        <div class="form-group">
            <label>Expiry</label>
            <input type="text" name="expiry" placeholder="YYYY-MM-DD"
                   class="form-control" value="<?= $item->expiry ?>"/>
        </div>
        <div class="form-group">
            <label>Minimum Course Value (if applicable)</label>
            <input type="number" name="valueMin" value="<?= $item->valueMin ?>"
                   class="form-control"/>
        </div>
        <div class="form-group">
            <label>Maximum Course Value (if applicable)</label>
            <input type="number" name="valueMax" value="<?= $item->valueMax ?>"
                   class="form-control"/>
        </div>
        <div class="form-group">
            <label>Account ID (if applicable)</label>
            <input type="number" name="forUser" placeholder="Paste here..." value="<?= $item->forUser ?>"
                   class="form-control"/>
        </div>
        <div class="form-group">
            <label>Apply To</label>
            <select class="form-control" name="applyTo">
                <option value="single_course">Single Course</option>
                <option value="basket" <?php
                if ($item->applyTo
                == "basket") { ?>selected<?php
                } ?>>Entire Basket
                </option>
            </select>
        </div>
        <div class="form-group">
            <label>Excluded Courses</label>
            <select class="form-control select2" name="excludeCourses[]"
                    multiple="multiple">
                <?php
                $excludeCourses = explode(",", $item->excludeCourses);
                foreach (ORM::for_table("courses")->find_many() as $course) {
                    ?>
                    <option value="<?= $course->id ?>"
                            <?php
                            if (in_array(
                                $course->id,
                                $excludeCourses
                            )) { ?>selected<?php
                    } ?>><?= $course->title ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label>Included Courses</label>
            <select class="form-control select2" name="includeCourses[]"
                    multiple="multiple">
                <?php
                $includeCourses = explode(",", $item->includeCourses);
                foreach (ORM::for_table("courses")->find_many() as $course) {
                    ?>
                    <option value="<?= $course->id ?>"
                            <?php
                            if (in_array(
                                $course->id,
                                $includeCourses
                            )) { ?>selected<?php
                    } ?>><?= $course->title ?></option>
                    <?php
                }
                ?>
            </select>
            <small>If you selected courses to include, this discount will <strong>only</strong> be applied to these
                courses. Excluded courses will be ignored.</small>
        </div>
        <div class="form-group">
            <label>Which currencies should this coupon be available in?</label>
            <div class="row">
                <?php
                $incCurrencies = explode(",", $item->currencies);
                $currencies = ORM::for_table("currencies")->find_many();

                foreach ($currencies as $currency) {
                    $value = '';
                    $existing = ORM::for_table("couponCurrencyPricing")->where("couponID", $item->id)->where(
                        "currencyID",
                        $currency->id
                    )->find_one();

                    if ($existing->value != "") {
                        $value = $existing->value;
                    }
                    ?>
                    <div class="col-xs-6">
                        <label>
                            <input type="checkbox" name="currencies[]" value="<?= $currency->id ?>"
                                   <?php
                                   if (in_array($currency->id, $incCurrencies)) { ?>checked<?php
                            } ?> />
                            <?= $currency->short ?> / <?= $currency->code ?>
                            <input type="text" class="form-control" name="value_<?= $currency->id ?>_<?= $item->id ?>"
                                   placeholder="Value..." value="<?= $value ?>"/>
                        </label>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <input type="hidden" name="itemID" value="<?= $item->id ?>"/>
        <script>
            $('.select2').select2();
        </script>
        <?php
    }

    public function editReviewForm()
    {
        $item = ORM::for_table("courseReviews")->find_one($this->get["id"]);
        $courses = ORM::for_table("courses")->order_by_asc("title")->find_many();
        $media = ORM::for_table('media')
            ->where('modelType', 'courseReviewController')
            ->where('modelId', $item->id)
            ->find_one();
        if (@$media->url) {
            $imageUrl = str_replace($media->fileName, 'thumb/' . $media->fileName, $media->url);
        }
        ?>
        <form name="editReview<?= $item->id ?>">
            <div class="modal-body">
                <?php
                if (@$imageUrl) {
                    ?>
                    <img src="<?= $imageUrl ?>" style="width:150px;margin-bottom:30px;"/>
                    <?php
                }
                ?>


                <div class="form-group">
                    <label>Review Title</label>
                    <input type="text" class="form-control" name="title" value="<?= $item->title ?>"/>
                </div>
                <div class="form-group">
                    <label>Assign Course<br/><small>Users type in the course manually, so it needs to be assigned
                            properly in order for us to show this review in the correct place.</small></label>
                    <select class="form-control" name="courseID">
                        <option value="">Please select...</option>
                        <?php
                        foreach ($courses as $course) {
                            ?>
                            <option value="<?= $course->id ?>"
                                    <?php
                                    if ($course->id == $item->courseID) { ?>selected<?php
                            } ?>><?= $course->title ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="p">Pending</option>
                        <option value="a" <?php
                        if ($item->status == "a") { ?>selected<?php
                        } ?>>Approved
                        </option>
                        <option value="r" <?php
                        if ($item->status == "r") { ?>selected<?php
                        } ?>>Rejected
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Firstname</label>
                    <input type="text" class="form-control" name="firstname" value="<?= $item->firstname ?>"/>
                </div>
                <div class="form-group">
                    <label>Lastname</label>
                    <input type="text" class="form-control" name="lastname" value="<?= $item->lastname ?>"/>
                </div>
                <div class="form-group">
                    <label>Full Review</label>
                    <textarea name="comments" class="form-control" rows="5"><?= $item->comments ?></textarea>
                </div>
                <div class="form-group">
                    <label>Rating</label>
                    <select class="form-control" name="rating">
                        <option><?= $item->rating ?></option>
                        <option>5</option>
                        <option>4</option>
                        <option>3</option>
                        <option>2</option>
                        <option>1</option>
                    </select>
                </div>
                <p><em>This review will only show on the website if it is approved.</em></p>
                <div id="returnStatusAddNew<?= $item->id ?>"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            <input type="hidden" name="itemID" value="<?= $item->id ?>"/>
        </form>
        <script type="text/javascript">
            jQuery("form[name='editReview<?= $item->id ?>']").submit(function (e) {
                e.preventDefault();
                // $('textarea[name="text"]').html($('.summernote').code());
                var formData = new FormData($(this)[0]);

                jQuery.ajax({
                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-course-review",
                    type: "POST",
                    data: formData,
                    async: true,
                    success: function (msg) {
                        jQuery('#returnStatusAddNew<?= $item->id ?>').html(msg);
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            });
        </script>
        <?php
    }

    public function editCoupon()
    {
        $item = ORM::for_table("coupons")->find_one($this->post["itemID"]);

        if ($this->post["expiry"] == "") {
            $this->post["expiry"] = "2099-01-01 00:00:00";
        }

        $item->set(
            array(
                'code' => $this->post["code"],
                'type' => $this->post["type"],
                'value' => $this->post["value"],
                'expiry' => $this->post["expiry"],
                'applyTo' => $this->post["applyTo"],
                'currencies' => implode(",", $this->post["currencies"]),
                'excludeCourses' => implode(",", $this->post["excludeCourses"]),
                'includeCourses' => implode(",", $this->post["includeCourses"])
            )
        );

        if ($this->post["totalLimit"] != "") {
            $item->totalLimit = $this->post["totalLimit"];
        }

        if ($this->post["valueMax"] != "") {
            $item->valueMax = $this->post["valueMax"];
        }

        if ($this->post["valueMin"] != "") {
            $item->valueMin = $this->post["valueMin"];
        }

        $item->set_expr("whenUpdated", "NOW()");

        $item->save();

        // check for currency based pricing
        $currencies = ORM::for_table("currencies")->find_many();

        foreach ($currencies as $currency) {
            $postValue = $this->post["value_" . $currency->id . "_" . $this->post["itemID"]];

            $existing = ORM::for_table("couponCurrencyPricing")->where("couponID", $item->id)->where(
                "currencyID",
                $currency->id
            )->find_one();

            if ($existing->id == "") {
                $existing = ORM::for_table("couponCurrencyPricing")->create();

                $existing->couponID = $item->id;
                $existing->currencyID = $currency->id;
            }

            $existing->value = $postValue;

            $existing->save();
        }

        $this->recordLog('edited a single coupon: ' . $this->post["code"]);

        $this->setAlertSuccess("Coupon successfully updated");
    }

    public function createCourse()
    {
        $data = array(
            'title' => $this->post["title"],
            'slug' => $this->createSlug($this->post["title"]),
            'price' => $this->post["price"],
            'duration' => $this->post["duration"],
            'location' => $this->post["location"],
        );
        $categories = $this->post["categories"];

        $item = ORM::for_table("courses")->create();

        $item->set($data);

        $item->set_expr("whenAdded", "NOW()");

        $item->save();

        // Inserted Categories
        if ($categories) {
            $this->courses->updateCourseCategories($item->id, $categories);
        }

        $this->recordLog('created a course: ' . $this->post["title"]);

        // redirect to edit course so modules, etc can be added
        $this->redirectJS(SITE_URL . 'blume/courses/edit?id=' . $item->id());
    }

    public function copyCourse()
    {
        require_once(__DIR__ . '/../repositories/CoursesRepository.php');
        require_once(__DIR__ . '/../repositories/helpers/ModelDuplicator.php');
        require_once(__DIR__ . '/../repositories/helpers/DbConnectionHandler.php');

        $connections = ModelDuplicator::buildConnections($this->post['copyDest']);

        $duplicateCourseModel = (new CoursesRepository())->courseModelDuplicator($this->post['courseID'], $connections);
        $responseData = [];

        $responseData['success'] = true;
        $responseData['results'] = array_map(function ($modelData) {
            if (isset($modelData['model']) && $modelData['model'] instanceof ORM) {
                $siteUrl = $modelData[DbConnectionHandler::SITE_URL];
                $modelData[DbConnectionHandler::SITE_URL] = $this->genCopyCourseResultUrl(
                    $siteUrl,
                    $modelData['model']->get('id')
                );
            }
            return $this->buildModelDuplicatorResponse($modelData);
        }, $duplicateCourseModel);
        echo json_encode($responseData);
        exit();
    }

    private function buildModelDuplicatorResponse($modelData) {
        if (isset($modelData['model']) && $modelData['model'] instanceof ORM) {
            $modelData['model'] = true;
        } else {
            $modelData['model'] = false;
        }
        $getConfig = DbConnectionHandler::getCourseConnectionByKey($modelData[DbConnectionHandler::CONNECTION]);
        if (isset($getConfig['label'])) {
            $modelData['CONNECTION_LABEL'] = $getConfig['label'];
        }
        return $modelData;

    }
    private function genCopyCourseResultUrl($siteUrl, $courseid)
    {
        return "{$siteUrl}blume/courses/edit?id={$courseid}";
    }

    public function editCourseResources()
    {
        $course = ORM::For_table("courses")->find_one($this->post["courseID"]);

        $course->resources = $this->post["resources"];

        $course->save();

        $this->setAlertSuccess("Course resources successfully updated");
    }

    public function editCourse()
    {
        $postData = $this->post;
        $courseData = $postData;

        $courseData['featured'] = isset($postData['featured'])
            ? $postData['featured'] : 0;
        $courseData['is_cma'] = isset($postData['is_cma']) ? $postData['is_cma']
            : 0;
        $courseData['is_sheilds'] = isset($postData['is_sheilds']) ? $postData['is_sheilds']
            : 0;
        $courseData['is_ctaa'] = isset($postData['is_ctaa']) ? $postData['is_ctaa']
            : 0;
        $courseData['is_rospa'] = isset($postData['is_rospa'])
            ? $postData['is_rospa'] : 0;
        $courseData['is_video'] = isset($postData['is_video'])
            ? $postData['is_video'] : 0;
        $courseData['is_lightningSkill'] = isset($postData['is_lightningSkill'])
            ? $postData['is_lightningSkill'] : 0;
        $courseData['upsellCoursePrice'] = isset($postData['upsellCoursePrice'])
        && @$postData['upsellCoursePrice'] ? $postData['upsellCoursePrice']
            : null;
        $courseData['upsellCourse'] = isset($postData['upsellCourse'])
        && @$postData['upsellCourse'] ? $postData['upsellCourse'] : null;
        $courseData['childCourses'] = isset($postData['childCourses'])
        && @$postData['childCourses']
        && @$postData['bundle'] ? json_encode($postData['childCourses']) : null;

        $courseData['courseProviderID'] = $postData['courseProviderID'] ?? null;

        $courseData['allowSecondaryName'] = isset($postData['allowSecondaryName'])
            ? $postData['allowSecondaryName'] : 0;

        unset($courseData['bundle']);
        unset($courseData['courseApprovals']);


        $course = $this->courses->saveCourse($courseData);

        // Update image
        if ($this->checkFileUploadSelected() == true) {
            $sizes = ['large', 'medium', 'thumb'];
            $model = [
                'type' => courseController::class,
                'id' => $course->id
            ];
            $this->medias->uploadFile(
                $sizes,
                $model,
                'uploaded_file',
                'main_image',
                true
            );
        }

        // Update icon
        if ($this->checkFileUploadSelected('icon_file') == true) {
            $sizes = [];
            $model = [
                'type' => courseController::class,
                'id' => $course->id
            ];
            $this->medias->uploadFile(
                $sizes,
                $model,
                'icon_file',
                'icon_image',
                true
            );
        }


        $this->setAlertSuccess("Course details updated");

        $this->recordLog('edited the following course: ' . $this->post["title"]);

        // clear cache
        $this->cache->generateCourseSingleCache($course->id);
    }

    public function editCoursePdf()
    {
        $postData = $this->post;
        $courseData = $postData;

        // Update image
        if ($this->checkFileUploadSelected() == true) {
            $sizes = [];
            $model = [
                'type' => courseController::class,
                'id' => $courseData['id']
            ];
            $this->medias->uploadFile(
                $sizes,
                $model,
                'uploaded_file',
                'main_pdf',
                true
            );

            $this->recordLog('updated a course PDF');

            $this->setAlertSuccess("Course PDF updated");
            return;
        }

        $this->setAlertDanger("Please upload PDF");
    }

    public function uploadPdf()
    {
        $postName = "file";
        $cdnDir = "resources";

        $allow = array('pdf', 'doc', 'docx', 'ppt', 'pptx', 'xlsx', 'xls');
        $fileName = $_FILES[$postName]["name"];
        $fileTmpLoc = $_FILES[$postName]["tmp_name"];
        $fileType = $_FILES[$postName]["type"];
        $fileSize = $_FILES[$postName]["size"];
        $fileErrorMsg = $_FILES[$postName]["error"];
        $kaboom = explode(".", $fileName);
        $end = strtolower(end($kaboom));

        if ($fileSize > 104857600000) {
            echo '<div class="alert alert-danger text-center" role="alert">Your file must be under 10MB.</div>';
            exit;
        } else {
            if (!in_array($end, $allow)) {
                echo '<div class="alert alert-danger text-center" role="alert">The file you are uploading is not allowed.</div>';
                exit;
            } else {
                if ($fileErrorMsg == 1) {
                    echo '<div class="alert alert-danger text-center" role="alert">An unknown error occurred. ' . $fileErrorMsg . '</div>';
                    exit;
                }
            }
        }

        $date = date('Y-m-d');

        if (!file_exists(TO_PATH_CDN . $cdnDir . '/' . $date)) {
            mkdir(TO_PATH_CDN . $cdnDir . '/' . $date, 0777, true);
        }

        $fileName = $fileName . '-' . md5(time() . rand(3333, 9999)) . '.' . $end;
        $moveResult = move_uploaded_file($fileTmpLoc, TO_PATH_CDN . $cdnDir . '/' . $date . '/' . $fileName);
        if ($moveResult != true) {
            echo '<div class="alert alert-danger text-center" role="alert">We could not complete the upload of your image. Please contact our team.</div>';
            exit;
        }

        ?>
        <p class="text-center">
            Your file link is as follows: <a href="<?= SITE_URL ?>assets/cdn/resources/<?= $date ?>/<?= $fileName ?>"
                                             target="_blank"><?= SITE_URL ?>assets/cdn/resources/<?= $fileName ?></a>
        </p>
        <?php
    }

    public function deleteCoursePdf()
    {
        $postData = $this->post;
        $courseData = $postData;

        $this->medias->deleteMediaByModel(
            'courseController',
            $courseData['id'],
            'main_pdf'
        );

        $this->recordLog('deleted a course PDF');

        $this->setAlertSuccess("Course PDF deleted");
    }

    public function createCourseModule()
    {
        //$defaultContents = file_get_contents(TO_PATH_CDN.'editorTemplates/moduleDefault.html');

        $item = ORM::for_table("courseModules")->create();


        $item->courseID = $this->post["courseID"];
        $item->title = $this->post["title"];
        $item->ord = $this->post["ord"];
        $item->estTime = $this->post["estTime"];
        $item->new_style_with_video = $this->post["new_style_with_video"];
        $item->slug = $this->createSlug($this->post["title"]);
        $item->parentID = @$this->post["parentID"] ? $this->post["parentID"] : null;
        $item->contentType = @$this->post["contentType"] ? $this->post["contentType"] : null;

        // Check existing slug
        $modules = ORM::for_table('courseModules')->where('slug', $item->slug)->count();
        if ($modules >= 1) {
            $item->slug = $item->slug . ('-' . $modules + 1);
        }

        $item->contents = '';//$defaultContents;
        $item->save();

        $this->recordLog('created the following course module: ' . $this->post["title"]);

        $this->redirectJS(
            SITE_URL . 'blume/courses/edit?tab=modules&id='
            . $this->post["courseID"] . '&moduleAdded=true'
        );
    }

    public function copyCourseModule()
    {
        require_once(__DIR__ . '/../repositories/CourseModulesRepository.php');
        require_once(__DIR__ . '/../repositories/helpers/ModelDuplicator.php');
        require_once(__DIR__ . '/../repositories/helpers/DbConnectionHandler.php');

        $connections = ModelDuplicator::buildConnections($this->post['copyDest']);
        $isLocal = ModelDuplicator::connectionsIsLocalOnly($connections);

        $duplicateCourseModel = (new CourseModulesRepository())->modulesModelDuplicator(
            $this->post['courseModuleID'],
            $connections
        );
        $responseData = [
            'success' => false
        ];
//        if ($isLocal && isset($duplicateCourseModel[0])) {
//            $duplicateCourseModel = $duplicateCourseModel[0];
//            if (
//                $duplicateCourseModel['model'] instanceof ORM &&
//                $duplicateCourseModel[DbConnectionHandler::CONNECTION] === DbConnectionHandler::LOCAL_DB_CONNECTION
//            ) {
//                // redirect to edit course so modules, etc can be added
//                $redirectLink = $this->genCopyCourseModuleResultUrl(SITE_URL, $this->post["courseID"]);
//                $responseData['success'] = true;
//                $responseData['redirect'] = $redirectLink;
//            }
//            echo json_encode($responseData);
//            return;
//        }
        $responseData['success'] = true;
        $responseData['results'] = array_map(function ($modelData) {
            if (isset($modelData['model']) && $modelData['model'] instanceof ORM) {
                $siteUrl = $modelData[DbConnectionHandler::SITE_URL];
                $modelData[DbConnectionHandler::SITE_URL] = $this->genCopyCourseModuleResultUrl(
                    $siteUrl,
                    $this->post["courseID"]
                );
            }
            return $this->buildModelDuplicatorResponse($modelData);
        }, $duplicateCourseModel);
        echo json_encode($responseData);
        exit();
    }

    private function genCopyCourseModuleResultUrl($siteUrl, $courseid)
    {
        return "{$siteUrl}blume/courses/edit?tab=modules&id={$courseid}&moduleAdded=true";
    }
    public function moduleQuizExist($module, $appear = null)
    {
        $quiz = ORM::for_table("quizzes")->where("moduleID", $module);
        if ($appear) {
            $quiz = $quiz->where('appear', $appear);
        }
        $quiz = $quiz->count();

        if ($quiz == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function moduleQuizExistTwo($module)
    {
        $quiz = ORM::for_table("quizzes")->where("moduleID", $module)->count();

        if ($quiz != 2) {
            return false;
        } else {
            return true;
        }
    }

    public function getModuleQuiz($module, $appear = null)
    {
        $quiz = ORM::for_table("quizzes")->where("moduleID", $module);
        if ($appear) {
            $quiz = $quiz->where('appear', $appear);
        }
        return $quiz->find_one();
    }

    public function getModuleQuizTwo($module)
    {
        return ORM::for_table("quizzes")->where("moduleID", $module)->offset(1)
            ->find_one();
    }

    public function getModuleQuizQuestions()
    {
        $items = ORM::for_table("quizQuestions")
            ->where("quizID", $this->get["id"])
            ->order_by_asc("ord")
            ->find_many();

        foreach ($items as $item) {
            $answers = ORM::for_table('quizQuestionAnswers')
                ->where('questionID', $item->id)->find_many();
            $imageMedia = $this->getQuestionMainImage($item->id);
            $audioMedia = $this->getQuestionAudio($item->id);
            $mp3CloudMedia = $this->getQuestionMp3Audio($item->id);
            $vimeoUrlMedia = $this->getQuestionVimeoUrl($item->id);
            ?>
            <div class="module" id="singleQuestion<?= $item->id ?>"
                 style="background:#fff;">
                <div class="p20" class="form-group">
                    <form name="updateQuestion<?= $item->id ?>">
                        <input type="text" style="font-weight:bold;"
                               class="form-control" name="question"
                               value="<?= $item->question ?>"/>
                        <br/>
                        <div class="row">
                            <div class="col-xs-4">
                                <label>Answer Type:</label>
                                <select class="form-control" name="answerType">
                                    <option value="single">Single</option>
                                    <option <?php
                                    if ($item->answerType
                                        == 'multiple'
                                    ) { ?> selected <?php
                                    } ?> value="multiple">
                                        Multiple
                                    </option>
                                    <option <?php
                                    if ($item->answerType == 'usertype') { ?> selected <?php
                                    } ?>
                                            value="usertype">
                                        User Type
                                    </option>
                                </select>
                            </div>
                            <div class="col-xs-2">
                                <label>Correct Answers</label>
                                <input class="form-control" name="correctAnswers" type="number"
                                       min="1" max="10"
                                       value="<?= $item->correctAnswers ?>"
                                       placeholder="">
                            </div>
                            <div class="col-xs-2">
                                <label>Order</label>
                                <input class="form-control" name="ord" type="number"
                                       min="1" value="<?= $item->ord ?>"
                                       placeholder="">
                            </div>
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <label>Vimeo URL</label>
                                    <input type="text" class="form-control" name="vimeoURL"
                                           value="<?php
                                           echo $vimeoUrlMedia->url ?? null; ?>"/>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <label>Upload Image</label>
                                    <input type="file" class="form-control" name="uploaded_image" accept="image/*"/>
                                    <?php
                                    if (@$imageMedia->url) {
                                        ?>
                                        <div class="mt10">
                                            <img src="<?= $imageMedia->url ?>" style="max-width: 200px">
                                            <button onclick="deleteModelItem('<?= $imageMedia->id ?>', 'media', true)"
                                                    type="button" class="btn btn-danger btn-small ml10">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <label>Upload Audio</label>
                                    <input type="file" class="form-control" name="uploaded_audio" accept="audio/*"/>
                                </div>
                                <?php
                                if (@$audioMedia->url) {
                                    ?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <audio controls style="max-width: 100%;">
                                                    <source src="<?php
                                                    echo $audioMedia->url; ?>" type="audio/ogg">
                                                    <source src="<?php
                                                    echo $audioMedia->url; ?>" type="audio/mpeg">
                                                </audio>
                                            </div>
                                            <div class="col-sm-1 mt5">
                                                <button onclick="deleteModelItem('<?= $audioMedia->id ?>', 'media', true)"
                                                        type="button" class="btn btn-danger btn-small ml10">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                } ?>
                            </div>
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <label>Mp3 Audio URL</label>
                                    <input type="text" class="form-control" name="mp3_audio" value=""/>
                                </div>
                            </div>
                        </div>

                        <br/>
                        <p><strong>Answers:</strong></p>

                        <div id="answers<?= $item->id ?>">
                            <?php
                            $count = 0;
                            foreach ($answers as $answer) {
                                ?>
                                <div class="module answerRow<?= $item->id . $count ?>">
                                    <div class="row">
                                        <div class="col-xs-9">
                                            <input type="text" class="form-control"
                                                   name="answers[<?= $count ?>][answer]"
                                                   value="<?= $answer->answer ?>"
                                                   style="margin-bottom:5px;"/>
                                        </div>
                                        <div class="col-xs-2">
                                            <label>Correct </label>
                                            <input type="checkbox"
                                                   name="answers[<?= $count ?>][isCorrect]"
                                                   value='1'
                                                   <?php
                                                   if ($answer->isCorrect
                                                   == 1) { ?>checked<?php
                                            } ?> />
                                        </div>
                                        <div class="col-xs-1">
                                            <a href="javascript:;" onclick="deleteAnswer(<?= $item->id . $count ?>);"
                                               class="btn btn-danger btn-small pull-right"
                                               style="padding: 0px 8px;">
                                                <i class="fa fa-trash"></i>
                                        </div>
                                    </div>
                                    <input type="hidden"
                                           name="answers[<?= $count ?>][id]"
                                           value="<?= $answer->id ?>">
                                    </a>
                                </div>
                                <?php
                                $count++;
                            }
                            ?>
                        </div>

                        <p>
                            <button type="submit"
                                    class="btn btn-success btn-small">
                                <input type="hidden" name="answerCount"
                                       id="answerCount<?= $item->id ?>"
                                       value="<?= $count + 1 ?>"/>
                                <i class="fa fa-check"></i>
                                Update
                            </button>
                            <a href="javascript:;"
                               onclick="addAnswer<?= $item->id ?>();"
                               class="btn btn-info btn-small">
                                <i class="fa fa-plus"></i>
                                Add Answer
                            </a>
                        </p>
                        <input type="hidden" name="itemID"
                               value="<?= $item->id ?>"/>
                    </form>
                    <?php
                    $this->renderFormAjax(
                        "blumeNew",
                        "edit-question",
                        "updateQuestion" . $item->id
                    );
                    ?>

                    <script type="text/javascript">
                        function addAnswer<?= $item->id ?>() {

                            var count = $("#answerCount<?= $item->id ?>").val();
                            var newCount = parseInt(count);

                            $("#answers<?= $item->id ?>").append('<div class="module"><div class="row"><div class="col-xs-9"><input type="text" class="form-control" name="answers[' + newCount + '][answer]" value="" style="margin-bottom:5px;" /></div><div class="col-xs-2"><label>Correct </label><input type="checkbox" name="answers[' + newCount + '][isCorrect]" value="1" /> </div><div class="col-xs-1"><a href="javascript:;" class="btn btn-danger btn-small pull-right" style="padding: 0px 8px;"><i class="fa fa-trash"></i></a></div></div></div>');

                            $("#answerCount<?= $item->id ?>").val(newCount + 1);

                        }

                        function deleteAnswer(count) {
                            $(".answerRow" + count).remove();
                        }
                    </script>

                    <hr/>

                    <p>
                        <a href="javascript:;"
                           onclick="deleteQuestion<?= $item->id ?>();"
                           class="btn btn-danger btn-small">
                            <i class="fa fa-trash"></i>
                            Delete Question
                        </a>
                    </p>

                    <script>
                        function deleteQuestion<?= $item->id ?>() {

                            if (window.confirm("Are you sure you want to delete this question?")) {
                                $("#singleQuestion<?= $item->id ?>").slideUp();
                                $("#returnStatus").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-question&id=<?= $item->id ?>");
                            }

                        }
                    </script>

                </div>
            </div>
            <?php
        }

        if (count($items) == 0) {
            echo "<p><em>There are currently no questions in this quiz.</em></p>";
        }
    }

    public function editQuestion()
    {
        $item = ORM::for_table("quizQuestions")
            ->find_one($this->post["itemID"]);

        $item->question = $this->post["question"];
        $item->answerType = $this->post["answerType"];
        $item->correctAnswers = $this->post["correctAnswers"];
        $item->ord = $this->post["ord"];

        $model = [
            'type' => 'quizQuestionController',
            'id' => $item->id
        ];
        // upload image
        if ($this->checkFileUploadSelected("uploaded_image") == true) {
            $this->medias->uploadFile(
                $sizes = null,
                $model,
                'uploaded_image',
                'main_image',
                true,
                false
            );
        }

        // Update audio
        if ($this->checkFileUploadSelected('uploaded_audio') == true) {
            $this->medias->uploadFile(
                $sizes = null,
                $model,
                'uploaded_audio',
                'audio',
                true,
                true
            );
        }
        // Upload MP3 Audio
        if ($this->post["mp3_audio"]) {
            $this->medias->addExternalMediaFromUrl(
                $this->post["mp3_audio"],
                $model['type'],
                $model['id'],
                $item->question,
                'mp3_audio',
                true
            );
        }

        // Upload vimeoURL
        if ($this->post["vimeoURL"]) {
            $this->medias->addExternalMediaFromUrl(
                $this->post["vimeoURL"],
                $model['type'],
                $model['id'],
                $item->question,
                'vimeoURL',
                true
            );
        }


        $answerData = array();
        if (count($this->post["answers"]) >= 1) {
            $existingAnswers = array();

            foreach ($this->post["answers"] as $answer) {
                $answer = json_encode($answer);
                $answer = json_decode($answer);
                if (@$answer->answer) {
                    if (@$answer->id) {
                        $updateAnswer = ORM::for_table('quizQuestionAnswers')->find_one($answer->id);
                    } else {
                        $updateAnswer = ORM::for_table('quizQuestionAnswers')->create();
                        $updateAnswer->questionID = $item->id;
                    }
                    $updateAnswer->answer = $answer->answer;
                    $updateAnswer->isCorrect = @$answer->isCorrect ? 1 : 0;
                    $updateAnswer->save();
                    $existingAnswers[] = $updateAnswer->id;
                }
            }
            ORM::for_table('quizQuestionAnswers')->where('questionID', $item->id)
                ->where_not_in('id', $existingAnswers)->delete_many();
        }

        $item->save();

        $this->recordLog('edited a quiz question: ' . $this->post["question"]);

        $this->setToastSuccess("Question successfully updated");
    }

    public function deleteQuestion()
    {
        $item = ORM::for_table("quizQuestions")->find_one($this->get["id"]);

        ORM::for_table('quizResults')
            ->where('quizID', $item->quizID)
            ->where('completed', 0)
            ->delete_many();

        $item->delete();

        $this->recordLog('deleted a quiz question');
    }
    
    public function deleteQuiz()
    {
        $quiz = ORM::for_table('quizzes')->find_one($this->get["id"]);
        $quiz->delete();
       
        $this->recordLog('deleted a quiz ');

    }

    public function createQuizQuestion()
    {
        $item = ORM::for_table("quizQuestions")->create();

        $item->question = $this->post["question"];
        $item->quizID = $this->post["quizID"];

        $item->save();

        ORM::for_table('quizResults')
            ->where('quizID', $item->quizID)
            ->where('completed', 0)
            ->delete_many();

        $this->recordLog('created a quiz question: ' . $this->post["question"]);

        $this->setToastSuccess("Question successfully created");

        ?>
        <script>
            $("#quizQuestionsAjax<?= $this->post["quizID"] ?>").load("<?= SITE_URL ?>ajax?c=blumeNew&a=get-module-quiz-questions&id=<?= $this->post["quizID"] ?>");
            $("#addQuestion<?= $this->post["quizID"] ?>").modal("toggle");
        </script>
        <?php
    }

    public function editCourseModules()
    {
        $count = 0;
        while ($count <= $this->post["moduleCount"]) {
            $count++;
        }
    }

    public function getCourseEdit()
    {
        return ORM::for_table("courses")->find_one($this->get["id"]);
    }

    public function getHelpArticle()
    {
        return ORM::for_table("helpArticles")->find_one($this->get["id"]);
    }

    public function editHelpArticle()
    {
        $item = ORM::for_table("helpArticles")->find_one($this->post["itemID"]);

        $item->contents = $this->post["contents"];
        $item->title = $this->post["title"];

        $item->save();

        $this->recordLog('edited a help article: ' . $this->post["title"]);

        $this->setAlertSuccess("Help article successfully updated");
    }

    public function abandonedCheckouts()
    {
        return ORM::for_table("orders")
            ->where("status", "c")
            ->order_by_desc("id")
            ->find_many();
    }

    public function deleteHelpArticle()
    {
        $item = ORM::for_table("helpArticles")->find_one($this->get["id"]);
        $item->delete();
    }

    public function getSetting($name)
    {
        return ORM::for_table("settings")->where("name", $name)->find_one();
    }

    public function editSettings()
    {
        $settings = array(
            "Facebook" => "facebook",
            "Twitter" => "twitter",
            "Instagram" => "instagram",
            "YouTube" => "youtube",
            "newsletter_course" => "newsletter_course",
            "Printed Certificate Price" => "printed_cert_price",
            "Certificate Print Email" => "cert_print_email",
            "Staff Training Form Email" => "staff_training_email",
            "Recommend A Friend Discount (%)" => "raf_discount",
            "Optionally show an urgent message on support pages" => "status_message",
            "Study Group URL" => "study_group_url",
            "Is the subscription balance offer currently active?" => "subBalanceOfferActive",
            "Subscription balance offer message, which appears on site" => "subBalanceOfferMessage",
            "How much balance should be added when someone subscribes annually?" => "subBalanceOfferAmount"
        );

        foreach ($settings as $key => $value) {
            //if($this->post[$value] != "") {
            $update = ORM::for_table("settings")->where("name", $value)
                ->find_one();

            $update->value = $this->post[$value];

            $update->save();
            //}


        }

        $this->recordLog('edited the general settings');

        $this->setAlertSuccess("Settings successfully updated");
    }

    public function editPage()
    {
        $item = ORM::for_table("pages")->find_one($this->post["itemID"]);

        $item->contents = $this->post["contents"];
        $item->title = $this->post["title"];
        $item->slug = $this->post["slug"];
        $item->hideTitle = $this->post["hideTitle"];
        $item->hideNewsletterModal = $this->post["hideNewsletterModal"];

        if ($item->seoPage == "1") {
            $item->categories = implode(",", $this->post["categories"]);
            $item->topContent = $this->post["topContent"];
        } else {
            $item->width = $this->post["width"];
            $item->showRedeem = $this->post["showRedeem"];
        }

        $item->save();

        $this->recordLog('edited the following static page: ' . $this->post["title"]);

        $this->setAlertSuccess("Page successfully updated.");
    }

    public function addPage()
    {
        $item = ORM::for_table("pages")->create();

        $item->title = $this->post["title"];
        $item->seoPage = $this->post["seoPage"];
        $item->slug = $this->createSlug($this->post["title"]);
        $item->set_expr("whenAdded", "NOW()");

        $item->save();

        $this->recordLog('added a page: ' . $this->post["title"]);

        $this->redirectJS(SITE_URL . 'blume/pages/edit?id=' . $item->id());
    }

    public function orderDetails()
    {
        $order = ORM::for_table("orders")->find_one($this->get["id"]);

        ?>
        <div class="row">
            <div class="col-xs-6">
                <p>
                    <strong>Order ID:</strong> <?= $order->id ?><br/>
                    <strong>Name:</strong> <?= $order->firstname ?> <?= $order->lastname ?>
                    <br/>
                    <strong>Email:</strong> <?= $order->email ?><br/>
                    <strong>Address 1:</strong> <?= $order->address1 ?><br/>
                    <strong>City:</strong> <?= $order->city ?><br/>
                    <strong>Postcode:</strong> <?= $order->postcode ?><br/>
                    <strong>Country:</strong> <?= $order->country ?><br/>
                </p>
            </div>
            <div class="col-xs-6">
                <a href="#" class="btn btn-system btn-block">
                    <i class="fa fa-file"></i>
                    Print Order PDF
                </a>

                <a href="#" class="btn btn-danger btn-block">
                    <i class="fa fa-exchange"></i>
                    Refund
                </a>
            </div>
            <div class="col-xs-12">
                <h5><strong>Items:</strong></h5>

                <?php
                foreach (
                    ORM::for_table("orderItems")->where("orderID", $order->id)
                        ->find_many() as $item
                ) {
                    $course = ORM::for_table("courses")
                        ->find_one($item->courseID);

                    ?>
                    <p>
                        <strong><?= $course->title ?></strong>
                        (x<?= $item->qty ?>)<br/>
                        £<?= number_format($course->price, 2) ?>
                    </p>
                    <hr style="margin:10px 0px;"/>
                    <?php
                }
                ?>


                <h5><strong>Totals:</strong></h5>
                <p>
                    <?php
                    if ($order->couponID != "") {
                        $coupon = ORM::for_table("coupons")
                            ->find_one($order->couponID);

                        if ($coupon->code != "") {
                            $discount = $coupon->value . '%';
                            if ($coupon->type == "v") {
                                $discount = "£" . number_format(
                                        $coupon->value,
                                        2
                                    );
                            }
                            ?>
                            <strong>Coupon:</strong> -<?= $discount ?><br/>
                            <?php
                        }
                    }
                    ?>
                    <strong>VAT @ <?= $order->vatRate ?>%:</strong>
                    £<?= number_format($order->vatAmount, 2) ?><br/>
                    <strong>Order Total:</strong>
                    £<?= number_format($order->total, 2) ?>
                </p>
            </div>
        </div>


        <?php
    }

    public function messageAllUsers()
    {
        if ($this->post["message"] == "") {
            $this->setAlertDanger("Your message cannot be blank.");
            exit;
        }

        $message = ORM::for_table("messagesQueue")->create();
        $totalUsers = ORM::for_table("accounts")->select("id")->count();

        $message->message = $this->post["message"];
        $message->subject = $this->post["subject"];
        $message->totalUsers = $totalUsers;
        $message->sentUsers = 0;
        $message->set_expr("whenSent", "NOW()");

        $message->save();

        $this->recordLog('sent a message to all users: ' . $this->post["subject"]);

        $this->setAlertSuccess("Messages are on their way.");
    }

    public function createModuleQuiz()
    {
        $module = ORM::for_table("courseModules")
            ->find_one($this->post["moduleID"]);
        $course = ORM::for_table("courses")->find_one($module->courseID);

        $item = ORM::for_table("quizzes")->create();

        $item->courseID = $course->id;
        $item->moduleID = $module->id;
        $item->passingPercentage = $this->post["passingPercentage"];
        $item->maxQuestionValue = $this->post["maxQuestionValue"];
        $item->timeLimit = $this->post["timeLimit"] ?? null;
        $item->appear = $this->post["appear"];

        $item->save();

        $this->recordLog('created a new quiz');

        $this->redirectJS(
            SITE_URL . 'blume/courses/modules/edit?id=' . $module->id
            . '&quizAdded=true'
        );
    }

    public function updateModuleQuiz()
    {
        $item = ORM::for_table("quizzes")->find_one($this->post["quizID"]);
        $oldMaxQuestionValue = $item->maxQuestionValue;
        $item->passingPercentage = $this->post["passingPercentage"];
        $item->maxQuestionValue = $this->post["maxQuestionValue"];
        $item->timeLimit = $this->post["timeLimit"] ?? null;
        $item->appear = $this->post["appear"];

        $item->save();
        if ($oldMaxQuestionValue != $item->maxQuestionValue) { // Delete incomplete quizzes
            ORM::for_table('quizResults')
                ->where('quizID', $item->id)
                ->where('completed', 0)
                ->delete_many();
        }
        $this->recordLog('updated a quiz');

        $this->setToastSuccess("Quiz successfully updated");
    }

    public function orderCourseModules()
    {
        $slide = $_GET['serial'];

        $slides = explode("-", $slide);

        $i = 0;

        foreach ($slides as $s) {
            $i++;

            $update = ORM::for_table("courseModules")->find_one($s);
            $update->ord = $i;
            $update->save();
        }

        $this->recordLog('re-ordered course modules');
    }

    public function orderTeam()
    {
        $slide = $_GET['serial'];

        $slides = explode("-", $slide);

        $i = 0;

        foreach ($slides as $s) {
            $i++;

            $update = ORM::for_table("aboutTeam")->find_one($s);
            $update->ord = $i;
            $update->save();
        }

        $this->recordLog('re-ordered team members');
    }

    public function restoreModule()
    {
        $history = ORM::for_table("courseModuleHistory")->find_one($this->get["id"]);

        $module = ORM::for_table("courseModules")->find_one($history->moduleID);

        if ($module->id != "") {
            $module->title = $history->title;
            $module->contents = $history->contents;
            $module->slug = $history->slug;
            $module->description = $history->description;

            $module->save();
        }

        header('Location: ' . SITE_URL . 'blume/courses/modules/edit?id=' . $history->moduleID . '&restore=true');
    }

    public function editCourseModule()
    {
        $item = ORM::for_table("courseModules")
            ->find_one($this->post["itemID"]);

        // save old version
        $history = ORM::for_table("courseModuleHistory")->create();

        $history->moduleID = $this->post["itemID"];
        $history->title = $item->title;
        $history->slug = $item->slug;
        $history->description = $item->description;
        $history->contents = $item->contents;
        $history->adminID = CUR_ID;
        $history->set_expr("whenSaved", "NOW()");

        $history->save();

        $item->title = $this->post["title"];
        //$item->slug = $this->createSlug($this->post["title"]);
        $item->contents = $this->post["contents"];
        $item->estTime = @$this->post["estTime"] ? $this->post["estTime"]
            : null;
        $item->description = $this->post["description"];
        $item->ord = $this->post["ord"];
        $item->disableModuleTimer = $this->post["disableModuleTimer"];
        $item->new_style_with_video = @$this->post["new_style_with_video"] ? 1 : 0;
        $item->has_optional_section = $this->post["has_optional_section"];
        $item->slug = $this->post["moduleSlug"];
        $item->contentType = $this->post["contentType"];
        $item->worksheet_title = $this->post["worksheet_title"];
        $item->worksheet_text = $this->post["worksheet_text"];
        $item->worksheet_estimate_time = @$this->post["worksheet_estimate_time"]
            ? $this->post["worksheet_estimate_time"] : null;


        $this->medias->deleteMediaByModel(
            courseModuleController::class,
            $item->id,
            'embed_video'
        );
        $dataMedia = [];
        $dataMedia['modelType'] = courseModuleController::class;
        $dataMedia['modelId'] = $item->id;
        $dataMedia['url'] = $this->post["vimeoURL"];
        $dataMedia['title'] = 'Video';
        $dataMedia['fileName'] = 'Video';
        $dataMedia['type'] = 'embed_video';
        $this->medias->saveMedia($dataMedia);

        $model = [
            'type' => courseModuleController::class,
            'id' => $this->post["itemID"]
        ];
        // Update audio
        if ($this->checkFileUploadSelected() == true) {
            $this->medias->uploadFile(
                $sizes = null,
                $model,
                'uploaded_file',
                'audio',
                true,
                true
            );
        }
        // Update video transcription
        if ($this->checkFileUploadSelected("video_trans") == true) {
            $this->medias->uploadFile(
                $sizes = null,
                $model,
                'video_trans',
                'video_trans',
                true,
                false
            );
        }
        // Upload SoundCloud Audio
        if ($this->post["mp3_audio"]) {
            $this->medias->addExternalMediaFromUrl(
                $this->post["mp3_audio"],
                $model['type'],
                $model['id'],
                $item->title,
                'mp3_audio',
                true
            );
        }
        // upload worksheet
        if ($this->checkFileUploadSelected("worksheet_replace") == true) {
            $this->medias->uploadFile(
                $sizes = null,
                $model,
                'worksheet_replace',
                'worksheet',
                true,
                false
            );
        }
        // upload featured image
        if ($this->checkFileUploadSelected("featured_image_replace") == true) {
            $this->medias->uploadFile(
                $sizes = null,
                $model,
                'featured_image_replace',
                'feature_image',
                true,
                false
            );
        }

        // Assignments
        if ($this->checkFileUploadSelected("assignments", true) == true) {
            $this->medias->uploadFile(
                $sizes = null,
                $model,
                'assignments',
                'assignment',
                false,
                false,
                true
            );
        }

        // Uploads
        if ($this->checkFileUploadSelected("uploads", true) == true) {
            $this->medias->uploadFile(
                $sizes = null,
                $model,
                'uploads',
                'upload',
                false,
                false,
                true
            );
        }

        $item->save();

        $this->recordLog('edited a course module: ' . $this->post["title"]);

        $this->setToastSuccess("Module successfully updated");
    }

    public function addTestimonial()
    {
        $fields = array("name", "course", "testimonial", "video", "location");

        $item = ORM::for_table("testimonials")->create();

        $item->set_expr("whenAdded", "NOW()");

        foreach ($fields as $field) {
            $item->$field = $this->post[$field];
        }

        $item->save();

        // Update image
        if ($this->checkFileUploadSelected() == true) {
            $sizes = ['large', 'medium', 'thumb'];
            $model = [
                'type' => testimonialController::class,
                'id' => $item->id()
            ];
            $this->medias->uploadFile(
                $sizes,
                $model,
                'uploaded_file',
                'main_image',
                true
            );
        }

        $this->recordLog('added a testimonial by ' . $this->post["name"]);

        $this->redirectJS(SITE_URL . 'blume/content/testimonials');
    }

    public function editTestimonial()
    {
        $fields = array("name", "course", "testimonial", "video", "location");

        $item = ORM::for_table("testimonials")->find_one($this->post["itemID"]);

        foreach ($fields as $field) {
            $item->$field = $this->post[$field];
        }

        // Update image
        if ($this->checkFileUploadSelected() == true) {
            $sizes = ['large', 'medium', 'thumb'];
            $model = [
                'type' => testimonialController::class,
                'id' => $this->post["itemID"]
            ];
            $this->medias->uploadFile(
                $sizes,
                $model,
                'uploaded_file',
                'main_image',
                true
            );
        }

        $item->save();

        $this->recordLog('edited a testimonial from ' . $this->post["name"]);

        $this->redirectJS(SITE_URL . 'blume/content/testimonials');
    }

    public function deleteTestimonial()
    {
        $item = ORM::for_table("testimonials")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted a testimonial');
    }

    public function addTeam()
    {
        $fields = array("name", "title", "type");

        $item = ORM::for_table("aboutTeam")->create();

        foreach ($fields as $field) {
            $item->$field = $this->post[$field];
        }

        $item->save();

        // Update image
        if ($this->checkFileUploadSelected() == true) {
            $sizes = ['large', 'medium', 'thumb'];
            $model = [
                'type' => aboutTeamController::class,
                'id' => $item->id()
            ];
            $this->medias->uploadFile(
                $sizes,
                $model,
                'uploaded_file',
                'main_image',
                true
            );
        }

        $this->recordLog('added a new team member: ' . $this->post["name"]);

        $this->redirectJS(SITE_URL . 'blume/content/team');
    }

    public function editTeam()
    {
        $fields = array("name", "title", "type");

        $item = ORM::for_table("aboutTeam")->find_one($this->post["itemID"]);

        foreach ($fields as $field) {
            $item->$field = $this->post[$field];
        }

        // Update image
        if ($this->checkFileUploadSelected() == true) {
            $sizes = ['large', 'medium', 'thumb'];
            $model = [
                'type' => aboutTeamController::class,
                'id' => $this->post["itemID"]
            ];
            $this->medias->uploadFile(
                $sizes,
                $model,
                'uploaded_file',
                'main_image',
                true
            );
        }

        $item->save();

        $this->recordLog('edited a team member: ' . $this->post["name"]);

        $this->redirectJS(SITE_URL . 'blume/content/team');
    }

    public function deleteTeam()
    {
        $item = ORM::for_table("aboutTeam")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted a team member');
    }

    public function addWhy()
    {
        $item = ORM::for_table("whyNewSkills")->create();

        $fields = array("title", "content");

        foreach ($fields as $field) {
            $item->$field = $this->post[$field];
        }

        $item->save();


        $this->redirectJS(SITE_URL . 'blume/marketing/why');
    }

    public function editWhy()
    {
        $item = ORM::for_table("whyNewSkills")->find_one($this->post["itemID"]);

        $fields = array("title", "content");

        foreach ($fields as $field) {
            $item->$field = $this->post[$field];
        }

        $item->save();

        $this->redirectJS(SITE_URL . 'blume/marketing/why');
    }

    public function deleteWhy()
    {
        $item = ORM::for_table("whyNewSkills")->find_one($this->get["id"]);
        $item->delete();
    }

    public function createBlog()
    {
        $blog = ORM::for_table("blog")->create();

        $blog->authorID = CUR_ID;
        $blog->title = $this->post["title"];
        $blog->slug = $this->createSlug($this->post["title"]);
        $blog->set_expr("whenAdded", "NOW()");

        $blog->save();

        $this->recordLog('created a blog post: ' . $this->post["title"]);

        $this->redirectJS(SITE_URL . 'blume/support/blog/edit?id=' . $blog->id());
    }

    public function createBlogCategory()
    {
        $blogCategory = ORM::for_table("blogCategories")->create();

        //$blog->authorID = CUR_ID;
        $blogCategory->title = $this->post["title"];
        $blogCategory->slug = $this->createSlug($this->post["title"]);
        //$blog->set_expr("whenAdded", "NOW()");

        $blogCategory->save();
        $this->setAlertSuccess("Blog Category successfully added");
        $this->recordLog('created a blog category: ' . $this->post["title"]);
        $this->redirectJS(SITE_URL . 'blume/support/blog/categories');
    }

    public function editBlog()
    {
        $blog = ORM::for_table("blog")->find_one($this->post["itemID"]);

        $blog->authorID = CUR_ID;
        $blog->title = $this->post["title"];
        $blog->courseID = empty($this->post["courseID"]) ? null
            : $this->post["courseID"];
        $blog->contents = $this->post["contents"];
        $blog->whenAdded = $this->post["whenAdded"];
        $blog->slug = $this->post["slug"];
        $blog->courses = implode(",", $this->post["courses"]);


        if ($this->checkFileUploadSelected() == true) {
            $sizes = ['large', 'medium', 'thumb'];
            $model = [
                'type' => blogController::class,
                'id' => $this->post["itemID"]
            ];
            $this->medias->uploadFile(
                $sizes,
                $model,
                'uploaded_file',
                'main_image',
                true
            );
        }

        $blog->save();
        if (isset($this->post["blogCategories"])) {
            $this->blogs->updateBlogCategories(
                $blog->id,
                $this->post["blogCategories"]
            );
        }

        $this->recordLog('edited a blog post: ' . $this->post["title"]);

        $this->setAlertSuccess("Blog successfully updated");
    }

    public function deleteBlog()
    {
        $item = ORM::for_table("blog")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted a blog post');
    }

    public function deleteBlogCategory()
    {
        $item = ORM::for_table("blogCategories")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted a blog category');
    }

    public function getBlog()
    {
        return ORM::for_table("blog")->find_one($this->get["id"]);
    }

    public function getAccount()
    {
        return ORM::for_table("accounts")->find_one($this->get["id"]);
    }

    public function accessUsersAccount()
    {
        $user = ORM::for_table("accounts")->find_one($this->get["id"]);

        if ($user->id == "") {
            exit;
        }

        $_SESSION["adminAccessed"] = "yes";

        $newSignedID = $user->id;
        $_SESSION['id_front'] = $newSignedID;

        $_SESSION['idx_front']
            = base64_encode("g4p3h9xfn8sq03hs2234$newSignedID");

        //Added by Zubaer
        $_SESSION['nsa_email_front'] = $user->email;

        $_SESSION['csrftoken'] = substr(
            base_convert(
                sha1(uniqid(mt_rand())),
                16,
                36
            ),
            0,
            40
        );

        $this->recordLog('accessed the following users account: ' . $user->id);

        header('Location: ' . SITE_URL . 'dashboard');
    }

    public function exitAdminAccess()
    {
        unset($_SESSION["adminAccessed"]);
        unset($_SESSION['idx_front']);
        //Added by Zubaer
        unset($_SESSION['nsa_email_front']);

        $this->recordLog('exited access from a users account: ' . $this->get["id"]);

        header(
            'Location: ' . SITE_URL . 'blume/accounts/view?id='
            . $this->get["id"]
        );
    }

    public function createAdmin()
    {
        $item = ORM::for_table("blumeUsers")->create();

        $characters
            = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$%#&-/!()[]';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 16; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $password = $randomString;

        $item->name = $this->post["name"];
        $item->surname = $this->post["surname"];
        $item->email = $this->post["email"];
        $item->set_expr("whenCreated", "NOW()");
        $item->role = $this->post["role"];
        $item->password = password_hash($password, PASSWORD_BCRYPT);

        $item->save();

        // send email
        $message = '<p>Hi ' . $this->post["name"] . '</p>
        <p>You have been created as an admin for New Skills Academy. Please sign in using your email address, along with the password and button below.</p>
        
        <p><strong>Password:</strong> ' . $password . '</p>';


        $message .= $this->renderHtmlEmailButton(
            "Sign In",
            SITE_URL . 'blume/login'
        );

        $this->sendEmail(
            $this->post["email"],
            $message,
            "Your admin login for " . SITE_NAME
        );

        $this->recordLog('created a new admin: ' . $this->post["email"]);

        $this->redirectJS(SITE_URL . 'blume/admins');
    }

    public function editAdmin()
    {
        $item = ORM::for_table("blumeUsers")->find_one($this->post["itemID"]);

        $item->name = $this->post["name"];
        $item->surname = $this->post["surname"];
        $item->email = $this->post["email"];
        $item->role = $this->post["role"];

        if ($this->post["password"] != "") {
            // then we need to update the password
            $item->password = password_hash(
                $this->post["password"],
                PASSWORD_BCRYPT
            );
        }

        $item->save();

        $this->recordLog('edited an admin: ' . $this->post["email"]);

        $this->redirectJS(SITE_URL . 'blume/admins');
    }

    public function deleteAdmin()
    {
        $item = ORM::for_table("blumeUsers")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted an admin');
    }

    public function createUserEnrollment()
    {
        // check if already enrolled onto this course first
        $existing = ORM::for_table("coursesAssigned")
            ->where("courseID", $this->post["courseID"])
            ->where("accountID", $this->post["accountID"])
            ->count();

        if ($existing != 0) {
            $this->setAlertDanger("You cannot enroll a user onto a course they are already enrolled in.");
            exit;
        }

        // proceed with enrollment
        //
        //
        //// check if course is a bundle
        $course = ORM::for_table("courses")->find_one($this->post["courseID"]);

        if ($course->childCourses != "") {
            // assign main bundle
            $assign = ORM::for_table("coursesAssigned")->create();

            $assign->set(
                array(
                    'courseID' => $course->id,
                    'accountID' => $this->post["accountID"]
                )
            );

            $assign->set_expr("whenAssigned", "NOW()");
            $assign->sub = $this->post["sub"];

            $assign->save();

            $bundleID = $assign->id(); // get assigned bundle ID

            foreach (json_decode($course->childCourses) as $child) {
                // assign main bundle
                $assign = ORM::for_table("coursesAssigned")->create();

                $assign->set(
                    array(
                        'courseID' => $child,
                        'accountID' => $this->post["accountID"],
                        'bundleID' => $bundleID
                    )
                );

                $assign->set_expr("whenAssigned", "NOW()");

                $assign->save();
            }
        } else {
            $item = ORM::for_table("coursesAssigned")->create();

            $item->courseID = $this->post["courseID"];
            $item->accountID = $this->post["accountID"];
            $item->sub = $this->post["sub"];
            $item->set_expr("whenAssigned", "NOW()");

            $item->save();
        }

        $this->recordLog('enrolled the following user onto a course: ' . $this->post["accountID"]);

        $this->redirectJS(
            SITE_URL . 'blume/accounts/view?id='
            . $this->post["accountID"]
        );
    }

    public function editUserEnrollment()
    {
        $item = ORM::for_table("coursesAssigned")
            ->find_one($this->post["itemID"]);

        $item->currentModule = $this->post["currentModule"];
        $item->completed = $this->post["completed"];
        $item->percComplete = $this->post["percComplete"];

        $this->recordLog('edited an enrollment for the following user: ' . $item->accountID);

        $item->save();

        $this->redirectJS(
            SITE_URL . 'blume/accounts/view?id='
            . $this->post["accountID"]
        );
    }

    public function addUserReward()
    {
        $reward = ORM::for_table('rewards')
            ->where('short', $this->post["rewardID"])->find_one();

        if (@$reward && $reward->category == 'signin') {
            $days = explode("_", $reward->short);
            $signInDays = $days[1];
            for ($i = $signInDays - 1; $i >= 0; $i--) {
                $log = ORM::for_table("accountSignInLogs")->create();
                $log->accountID = $this->post["accountID"];
                $log->ipAddress = null;
                $log->dateTime = date("Y-m-d H:i:s", strtotime('-' . $i . ' days'));
                $log->save();
            }
        }

        $this->rewardsAssigned->assignReward(
            $this->post["accountID"],
            $this->post["rewardID"],
            true,
            false,
            1,
            $this->post["isAdmin"]
        );

        $this->recordLog('added a new reward to the following user: ' . $this->post["accountID"]);

        $this->setToastSuccess("Reward(s) has been assigned");

        // refresh table on frontend
        ?>
        <script>
            $("#userRewards").load("<?= SITE_URL ?>ajax?c=blumeNew&a=load-user-rewards&id=<?= $this->post["accountID"] ?>");
        </script>
        <?php

        exit;
    }

    public function deleteCourseReview()
    {
        $item = ORM::for_table("courseReviews")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted a course review');
    }

    public function addCourseReview()
    {
        $fields = array(
            "firstname",
            "lastname",
            "email",
            "city",
            "courseID",
            "comments",
            "rating"
        );

        $this->validateValues($fields);

        $item = ORM::for_table("courseReviews")->create();
        $course = ORM::for_table("courses")->find_one($this->post["courseID"]);

        $item->set(
            array(
                'firstname' => $this->post["firstname"],
                'lastname' => $this->post["lastname"],
                'email' => $this->post["email"],
                'city' => $this->post["city"],
                'course' => $course->title,
                'courseID' => $this->post["courseID"],
                'comments' => $this->post["comments"],
                'rating' => $this->post["rating"]
            )
        );

        $item->set_expr("whenSubmitted", "NOW()");
        $item->status = "a";


        $item->save();

        $sizes = ['thumb'];
        $model = [
            'type' => 'courseReviewController',
            'id' => $item->id
        ];
        $this->medias->uploadFile($sizes, $model, 'uploaded_file');

        $this->recordLog('added a course review by ' . $this->post["firstname"] . ' ' . $this->post["lastname"]);

        $this->redirectJS(SITE_URL . 'blume/courses/reviews');
    }

    public function editCourseReviewSingle()
    {
        $item = ORM::for_table("courses")->find_one($this->post["id"]);

        if (@$item->id) {
            $item->reviews = $this->post["reviews"];
            $item->save();
        }

        $this->recordLog('edited a course review');

        $this->setAlertSuccess("Course review was successfully updated");
    }

    public function editCourseReview()
    {
        $item = ORM::for_table("courseReviews")
            ->find_one($this->post["itemID"]);

        $item->status = $this->post["status"];
        $item->comments = $this->post["comments"];
        $item->courseID = $this->post["courseID"];
        $item->rating = $this->post["rating"];
        $item->firstname = $this->post["firstname"];
        $item->lastname = $this->post["lastname"];
        $item->title = $this->post["title"];

        $item->save();

        $this->recordLog('edited a course review');

        $this->setAlertSuccess("Course review was successfully updated");
    }

    public function editCourseAdminNotes()
    {
        $item = ORM::for_table("courses")
            ->find_one($this->post["id"]);

        $item->adminNotes = $this->post["adminNotes"];

        $item->save();

        $this->recordLog('edited admin notes for course' . $this->post["id"]);

        $this->setAlertSuccess("Admin updates successfully updated.");
    }

    public function exportCourseReviewsCsv()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header(
            'Content-Disposition: attachment; filename=Course_Reviews_'
            . time() . '.csv'
        );

        $output = fopen('php://output', 'w');


        fputcsv($output, array(
            'FIRSTNAME',
            'LASTNAME',
            'EMAIL',
            'COURSE',
            'STATUS',
            'RATING',
            'REVIEW',
            'CREATED',
            'REVIEWER_IMAGE'
        ));

        $this->recordLog('exported course reviews as a CSV');

        $reviews = ORM::for_table("courseReviews")
            ->where_not_equal("status", "r")->order_by_desc("id")->find_many();


        foreach ($reviews as $review) {
            $courseName = $review->course;

            if ($review->courseID != "") {
                $course = ORM::for_table("courses")
                    ->find_one($review->courseID);
                $courseName = $course->title;
            }

            $status = "Pending";
            if ($review->status == "a") {
                $status = "Approved";
            }

            fputcsv($output, array(
                $review->firstname,
                $review->lastname,
                $review->email,
                $courseName,
                $status,
                $review->rating,
                $review->comments,
                $review->whenSubmitted,
                SITE_URL . 'assets/cdn/reviewImages/' . $review->image
            ));
        }
    }

    public function exportCourseTitles()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header(
            'Content-Disposition: attachment; filename=Course_Titles_' . time()
            . '.csv'
        );

        $output = fopen('php://output', 'w');

        $this->recordLog('exported course titles as a CSV');

        fputcsv(
            $output,
            array(
                'COURSE',
                'HAS_VIDEO',
                'ENROLLMENTS',
                'AVERAGE_RATING',
                'TOTAL_RATINGS',
                'CATEGORIES',
                'PRICE',
                'LINK'
            )
        );


        $courses = ORM::for_table("courses")->order_by_asc("title")
            ->find_many();


        foreach ($courses as $course) {
            $catIDs = ORM::for_table("courseCategoryIDs")->where("course_id", $course->id)->find_many();

            $catList = '';

            foreach ($catIDs as $cat) {
                $category = ORM::for_table("courseCategories")->find_one($cat->category_id);

                $catList = $catList . $category->title . ',';
            }

            fputcsv(
                $output,
                array(
                    $course->title,
                    $course->is_video,
                    $course->enrollmentCount,
                    $course->averageRating,
                    $course->totalRatings,
                    $catList,
                    $course->price,
                    SITE_URL . 'course/' . $course->slug
                )
            );
        }
    }

    public function dashboardSearch()
    {
        $total = 0;

        $this->recordLog('searched for the following from the dashboard: ' . $this->post["search"]);

        $items = ORM::for_table("accounts")
            ->where_like("email", "%" . $this->post["search"] . "%")->find_many();

        $total = count($items);
        if (count($items) > 0) {
            ?>
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Courses</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($items as $item) {
                    ?>
                    <tr>
                        <td><?= $item->id ?></td>
                        <td><?= $item->email ?></td>
                        <td><?= $item->firstname ?></td>
                        <td><?= $item->lastname ?></td>
                        <td><?= ORM::for_table("coursesAssigned")
                                ->where("accountID", $item->id)->count() ?></td>
                        <td><a href="javascript:;"
                               onclick="loadSingleUser(<?= $item->id ?>);"
                               class="label label-info" style="    color: #fff;
    padding: 1px 14px;
    border-radius: 7px;">View</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Courses</th>
                    <th>Actions</th>
                </tr>
                </tfoot>
            </table>
            <?php
        }

        $items = ORM::for_table("orders")
            ->where_like("id", "%" . $this->post["search"] . "%")->find_many();

        $total = $total + count($items);

        if (count($items) > 0) {
            ?>
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Items</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($items as $item) {
                    ?>
                    <tr>
                        <td><?= $item->id ?></td>
                        <td><?= $item->email ?></td>
                        <td><?= $item->firstname ?></td>
                        <td><?= $item->lastname ?></td>
                        <td><?php

                            $item2 = ORM::for_table("orderItems")->where("orderID", $item->id)->find_one();

                            if ($item2->id == "") {
                                echo '0';
                            } else {
                                $text = '';

                                if ($item2->course == "1") {
                                    $course = ORM::for_table("courses")->find_one($item2->courseID);
                                    if ($course->title == "") {
                                        $course = ORM::for_table("courses")->where("oldID", $item2->courseID)->find_one(
                                        );
                                    }
                                    $text = $course->title;
                                } else {
                                    if ($item2->voucherID != "") {
                                        // then its a gifted voucher
                                        $voucher = ORM::for_table("vouchers")->find_one($item2->voucherID);
                                        $course = ORM::for_table("courses")->find_one($voucher->courses);
                                        $text = 'Gift Voucher for ' . $course->title;
                                    } else {
                                        if ($item2->premiumSubPlanID != "") {
                                            $text = 'Subscription';
                                        } else {
                                            // then its a cert.
                                            $cert = ORM::for_table("coursesAssigned")->find_one($item2->certID);

                                            $text = 'Cert: ' . $cert->certNo;
                                        }
                                    }
                                }

                                if (strlen($text) > 30) {
                                    $text = substr($text, 0, 30) . '...';
                                }

                                echo '(' . ORM::for_table("orderItems")->where("orderID", $item->id)->count(
                                    ) . ') ' . $text;
                            }
                            ?></td>
                        <td><a href="javascript:;"
                               onclick="loadSingleUser(<?= $item->accountID ?>);"
                               class="label label-info" style="    color: #fff;
    padding: 1px 14px;
    border-radius: 7px;">View</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Actions</th>
                </tr>
                </tfoot>
            </table>
            <?php
        }

        $items = ORM::for_table("coursesAssigned")
            ->where_like("certNo", "%" . $this->post["search"] . "%")->find_many();

        $total = $total + count($items);

        if (count($items) > 0) {
            ?>
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($items as $cert) {
                    $item = ORM::for_table("accounts")
                        ->find_one($cert->accountID);

                    ?>
                    <tr>
                        <td><?= $item->id ?></td>
                        <td><?= $item->email ?></td>
                        <td><?= $item->firstname ?></td>
                        <td><?= $item->lastname ?></td>
                        <td><a href="javascript:;"
                               onclick="loadSingleUser(<?= $item->id ?>);"
                               class="label label-info" style="    color: #fff;
    padding: 1px 14px;
    border-radius: 7px;">View</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Actions</th>
                </tr>
                </tfoot>
            </table>
            <?php
        }

        if ($total == 0) {
            $this->setAlertDanger("We could not find anything matching your search.");
        }
    }

    public function exportPrintedOrders()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header(
            'Content-Disposition: attachment; filename=Printed_Export_'
            . time() . '.csv'
        );

        $output = fopen('php://output', 'w');


        fputcsv($output, array(
            'NAME',
            'ADDRESS1',
            'ADDRESS2',
            'CITY',
            'POSTCODE',
            'COUNTRY'
        ));


        $query = ORM::for_table("orderItems")->raw_query(
            "SELECT `id`, `accountID`, `whenCreated`, `courseID`, `id`, `id` FROM ( SELECT oi.*, o.accountID FROM orderItems oi JOIN orders o ON o.id = oi.orderID where o.status='completed' and oi.printedCourse='1' ) temp ORDER BY `courseID` ASC"
        )->find_many();

        foreach ($query as $item) {
            $orderItem = ORM::for_table("orderItems")->find_one($item->id);
            $order = ORM::for_table("orders")->find_one($orderItem->orderID);

            if ($orderItem->status == "p") {
                fputcsv($output, array(
                    $order->firstname . ' ' . $order->lastname,
                    $order->address1,
                    $order->address2,
                    $order->city,
                    $order->postcode,
                    $order->country
                ));
            }
        }
    }

    public function createResource()
    {
        $blog = ORM::for_table("resources")->create();

        $blog->title = $this->post["title"];
        $blog->slug = $this->createSlug($this->post["title"]);
        $blog->set_expr("whenAdded", "NOW()");
        $blog->set_expr("whenUpdated", "NOW()");

        $blog->save();

        $this->recordLog('created a new resource: ' . $this->post["title"]);

        $this->redirectJS(
            SITE_URL . 'blume/support/resources/edit?id='
            . $blog->id()
        );
    }

    public function editResource()
    {
        $blog = ORM::for_table("resources")->find_one($this->post["itemID"]);

        $blog->title = $this->post["title"];
        $blog->contents = $this->post["contents"];
        $blog->set_expr("whenUpdated", "NOW()");
        $blog->slug = $this->createSlug($this->post["title"]);

        // Update image
        if ($this->checkFileUploadSelected() == true) {
            $sizes = ['large', 'medium', 'thumb'];
            $model = [
                'type' => resourceController::class,
                'id' => $this->post["itemID"]
            ];
            $this->medias->uploadFile(
                $sizes,
                $model,
                'uploaded_file',
                'main_image',
                true
            );
        }

        $blog->save();

        $this->recordLog('edited a resource: ' . $this->post["title"]);

        $this->setAlertSuccess("Resource successfully updated");
    }

    public function deleteResource()
    {
        $item = ORM::for_table("resources")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted a resource');
    }

    public function getResource()
    {
        return ORM::for_table("resources")->find_one($this->get["id"]);
    }

    public function createUserAccount()
    {
        if ($this->post["password"] != $this->post["passwordConfirm"]) {
            $this->setAlertDanger("The passwords you entered do not match.");
            exit;
        }

        $existing = ORM::for_table("accounts")
            ->where("email", $this->post["email"])->find_one();

        $userID = $existing->id;
        $red = 0; // if this ends up as 2 then its a red error

        if ($existing->id != "") {
            $this->setAlertDanger("An account already exists with this email address.");
            $red++;
        } else {
            // create account
            $item = ORM::for_table("accounts")->create();

            $item->firstname = $this->post["firstname"];
            $item->lastname = $this->post["lastname"];
            $item->email = $this->post["email"];
            $item->password = password_hash(
                $this->post["password"],
                PASSWORD_BCRYPT
            );
            $item->set_expr("whenCreated", "NOW()");
            $item->set_expr("whenUpdated", "NOW()");

            $item->save();

            $userID = $item->id();

            // send email
            $message = '<p>Hi ' . $this->post["firstname"] . ',</p>
        <p>We have created your account on New Skills Academy. We hope you enjoy your course and get the chance to learn much more from our wide range of available courses.</p>
        
        <p>You can sign into your account at any time using your email address - '
                . $this->post["email"] . ' - and the following password - '
                . $this->post["password"] . ' - so you are able to continue with your course(s), view progress, make notes, enroll onto other courses, and much more.</p>
       ';

            $message .= $this->renderHtmlEmailButton(
                "My Courses",
                SITE_URL . 'dashboard/courses'
            );

            $this->sendEmail(
                $this->post["email"],
                $message,
                "Welcome to New Skills Academy"
            );

            $this->recordLog('created a new user account: ' . $userID);
        }

        // see if course exists
        if ($red == 1) {
            $existing = ORM::for_table("coursesAssigned")->where("accountID", $userID)->where(
                "courseID",
                $this->post["courseID"]
            )->count();

            if ($existing != 0) {
                $this->setAlertDanger(
                    "This account already exists AND this course is already assigned to this account."
                );
                exit;
            }
        }

        // assign course
        if ($this->post["courseID"] != "") {
            $courseData = ORM::for_table("courses")
                ->find_one($this->post["courseID"]);

            if ($courseData->childCourses != "") {
                // assign main bundle
                $item = ORM::for_table("coursesAssigned")->create();

                $item->set(
                    array(
                        'courseID' => $this->post["courseID"],
                        'accountID' => $userID
                    )
                );

                $item->set_expr("whenAssigned", "NOW()");

                $item->save();

                $bundleID = $item->id(); // get assigned bundle ID

                foreach (json_decode($courseData->childCourses) as $child) {
                    // assign inner bundle
                    $item = ORM::for_table("coursesAssigned")->create();

                    $item->set(
                        array(
                            'courseID' => $child,
                            'accountID' => $userID,
                            'bundleID' => $bundleID
                        )
                    );

                    $item->set_expr("whenAssigned", "NOW()");

                    $item->save();
                }
            } else {
                $item = ORM::for_table("coursesAssigned")->create();

                $item->set(
                    array(
                        'courseID' => $this->post["courseID"],
                        'accountID' => $userID
                    )
                );

                $item->set_expr("whenAssigned", "NOW()");

                $item->save();
            }
        }

        if ($red == 1) {
            $this->setAlertSuccess("The account already exists, but the course was successfully added to the account");

            // send email
            $message = '<p>Hi there,</p>
        <p>We have added a new course - ' . $courseData->title . ' - to your account.</p>
        
        <p>You can sign into your account using your existing details to access this course at any time.</p>
       ';

            $message .= $this->renderHtmlEmailButton(
                "My Courses",
                SITE_URL . 'dashboard/courses'
            );

            if ($this->post["courseID"] != "") {
                $this->sendEmail(
                    $this->post["email"],
                    $message,
                    "A new course was added to your New Skills Academy account"
                );
            }
        } else {
            $this->setAlertSuccess("The new user was successfully created.");
        }


        ?>
        <script>
            $(".csReset").val('');
        </script>
        <?php
    }

    public function approveAchiever()
    {
        $item = ORM::for_table("achievers")->find_one($this->get["id"]);
        $item->status = "a";
        $item->save();

        $this->recordLog('approved a new item for the achiever board');

        $this->redirectJS(SITE_URL . 'blume/achievers');
    }

    public function deleteAchiever()
    {
        $item = ORM::for_table("achievers")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted an item from the achiever board');
    }

    public function markOrderDispatched()
    {
        // get record from DB
        $item = ORM::for_table("orderItems")->find_one($this->get["id"]);

        $item->status = "d"; // set the new status

        $item->save(); // save to database

    }

    public function markOrderUndispatched()
    {
        // get record from DB
        $item = ORM::for_table("orderItems")->find_one($this->get["id"]);

        $item->status = "p"; // set the new status

        $item->save(); // save to database

    }

    public function exportjobstatscsv()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=job_Stats_' . time() . '.csv');

        $output = fopen('php://output', 'w');

        $this->recordLog('exported Job Stats as a CSV');


        fputcsv(
            $output,
            array(
                'JOB_TITLE',
                'JOB_DESCRIPTION',
                'COMPANY_NAME',
                'LOCATION',
                'SALARY',
                'APPLICATION_LINK',
                'CLOSING_DATE',
                'DATE_POSTED',
                'CLICK_AMOUNT'
            )
        );


        $items = ORM::for_table("jobs")->order_by_desc("id")->find_many();


        foreach ($items as $item) {
            $order = ORM::for_table("jobs")->find_one($item->id);

            fputcsv(
                $output,
                array(
                    $order->jobTitle,
                    $order->jobDescription,
                    $order->companyName,
                    $order->location,
                    $order->salary,
                    $order->applicationLink,
                    $order->closingDate,
                    $order->datePosted,
                    $order->clickAmount
                )
            );
        }
    }

    public function exportjobclickstatscsv()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=job_Stats_' . time() . '.csv');

        $output = fopen('php://output', 'w');

        $this->recordLog('exported Job Stats as a CSV');


        fputcsv($output, array('FIRST_NAME', 'EMAIL', 'JOB_ID', 'WHEN_CLICKED'));


        // ->where("jobID", $_GET['id'])
        $items = ORM::for_table("jobClicks")->find_many();


        foreach ($items as $item) {
            $order = ORM::for_table("jobClicks")->find_one($item->id);
            fputcsv($output, array($order->name, $order->email, $order->jobID, $order->whenClicked));
        }
    }

    public function exportCertificateOrdersCsv()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Certificate_Orders_' . time() . '.csv');

        $output = fopen('php://output', 'w');

        $this->recordLog('exported certificate orders as a CSV');


        fputcsv(
            $output,
            array(
                'FIRSTNAME',
                'LASTNAME',
                'EMAIL',
                'ADDRESS_1',
                'CITY',
                'POSTCODE',
                'COUNTRY',
                'STATUS',
                'DATE_TIME',
                'CERTIFICATE'
            )
        );


        $items = ORM::for_table("orderItems")->where("certPaid", "1")->where_not_equal("certID", "")->order_by_desc(
            "id"
        )->find_many();


        foreach ($items as $item) {
            $order = ORM::for_table("orders")->find_one($item->orderID);

            $status = "Pending";
            if ($item->status == "d") {
                $status = "Dispatched";
            }

            fputcsv(
                $output,
                array(
                    $order->firstname,
                    $order->lastname,
                    $order->email,
                    $order->address1,
                    $order->city,
                    $order->postcode,
                    $order->country,
                    $status,
                    $order->whenUpdated,
                    SITE_URL . 'ajax?c=certificate&a=cert-pdf-admin&id=' . $item->certID . '&adminKey=gre45h-56trh_434rdfng'
                )
            );
        }
    }

    public function importBulkVouchers()
    {
        /*$postName = "file";
        $allow = array('csv');
        $fileName = $_FILES[$postName]["name"];
        $fileTmpLoc = $_FILES[$postName]["tmp_name"];
        $fileType = $_FILES[$postName]["type"];
        $fileSize = $_FILES[$postName]["size"];
        $fileErrorMsg = $_FILES[$postName]["error"];
        $kaboom = explode(".", $fileName);
        $end = strtolower(end($kaboom));

        if ($fileSize > 104857600000) {
            echo '<div class="alert alert-danger text-center" role="alert">Your file must be under 10MB.</div>';
            exit;
        } else {
            if (!in_array($end, $allow)) {
                echo '<div class="alert alert-danger text-center" role="alert">The file you are uploading is now allowed.</div>';
                exit;
            } else {
                if ($fileErrorMsg == 1) {
                    echo '<div class="alert alert-danger text-center" role="alert">An unknown error occurred. '
                        .$fileErrorMsg.'</div>';
                    exit;
                }
            }
        }

        $cdnDir = 'adminImports';

        $date = date('Y-m-d');

        if (!file_exists(TO_PATH_CDN.$cdnDir.'/'.$date)) {
            mkdir(TO_PATH_CDN.$cdnDir.'/'.$date, 0777, true);
        }

        $fileName = $fileName.'-'.md5(time().rand(3333, 9999)).'.'.$end;
        $moveResult = move_uploaded_file($fileTmpLoc,
            TO_PATH_CDN.$cdnDir.'/'.$date.'/'.$fileName);
        if ($moveResult != true) {
            echo '<div class="alert alert-danger text-center" role="alert">We could not complete the upload of your CSV.</div>';
            exit;
        }

        $rows = array_map('str_getcsv',
            file(TO_PATH_CDN.$cdnDir.'/'.$date.'/'.$fileName));
        $header = array_shift($rows);
        $csv = array();
        foreach ($rows as $row) {
            $csv[] = array_combine($header, $row);
        }

        $hasHeader = false;*/

        $csv = explode("\r\n", $this->post["codes"]);


        foreach ($csv as $itemCSV) {
            if ($itemCSV != "") {
                //$hasHeader = true;

                // if code already exists then overwrite
                $item = ORM::for_table("vouchers")
                    ->where("code", $itemCSV)
                    ->find_one();

                if ($item->id == "") {
                    $item = ORM::for_table("vouchers")->create();
                }

                $item->set(
                    array(
                        'code' => $itemCSV,
                        'type' => $this->post["attachCourses"],
                        'groupID' => "0"
                    )
                );

                if ($this->post["attachCourses"] == "specific") {
                    $item->courses = implode(",", $this->post["courses"]);
                }

                if ($this->post["attachCourses"] == "price") {
                    $item->valueUpto = $this->post["valueUpto"];
                }

                $item->set_expr("whenAdded", "NOW()");

                if ($this->post["expiry"] != "") {
                    $item->expiry = date(
                        'Y-m-d H:i:s',
                        strtotime($this->post["expiry"])
                    );
                }

                $item->save();
            }
        }

        // failsafe import for if no headers are present
        /*if ($hasHeader == false) {
            $csvFile = file(TO_PATH_CDN.$cdnDir.'/'.$date.'/'.$fileName);
            $data = [];
            foreach ($csvFile as $line) {
                $data[] = str_getcsv($line);
            }


            foreach ($data as $dItem) {
                // if code already exists then overwrite
                $item = ORM::for_table("vouchers")
                    ->where("code", $dItem[0])
                    ->find_one();

                if ($item->id == "") {
                    $item = ORM::for_table("vouchers")->create();
                }

                $item->set(
                    array(
                        'code'    => $dItem[0],
                        'type'    => $this->post["attachCourses"],
                        'groupID' => "0"
                    )
                );

                if ($this->post["attachCourses"] == "specific") {

                    $item->courses = implode(",", $this->post["courses"]);

                }

                if ($this->post["attachCourses"] == "price") {

                    $item->valueUpto = $this->post["valueUpto"];

                }

                $item->set_expr("whenAdded", "NOW()");

                $item->save();
            }

        }*/

        $this->recordLog('bulk added vouchers');

        $this->setAlertSuccess("Vouchers successfully imported in bulk.");
    }

    public function importBulkCoupons()
    {
        $postName = "file";
        $allow = array('csv');
        $fileName = $_FILES[$postName]["name"];
        $fileTmpLoc = $_FILES[$postName]["tmp_name"];
        $fileType = $_FILES[$postName]["type"];
        $fileSize = $_FILES[$postName]["size"];
        $fileErrorMsg = $_FILES[$postName]["error"];
        $kaboom = explode(".", $fileName);
        $end = strtolower(end($kaboom));

        if ($fileSize > 104857600000) {
            echo '<div class="alert alert-danger text-center" role="alert">Your file must be under 10MB.</div>';
            exit;
        } else {
            if (!in_array($end, $allow)) {
                echo '<div class="alert alert-danger text-center" role="alert">The file you are uploading is now allowed.</div>';
                exit;
            } else {
                if ($fileErrorMsg == 1) {
                    echo '<div class="alert alert-danger text-center" role="alert">An unknown error occurred. '
                        . $fileErrorMsg . '</div>';
                    exit;
                }
            }
        }

        $cdnDir = 'adminImports';

        $date = date('Y-m-d');

        if (!file_exists(TO_PATH_CDN . $cdnDir . '/' . $date)) {
            mkdir(TO_PATH_CDN . $cdnDir . '/' . $date, 0777, true);
        }

        $fileName = $fileName . '-' . md5(time() . rand(3333, 9999)) . '.' . $end;
        $moveResult = move_uploaded_file(
            $fileTmpLoc,
            TO_PATH_CDN . $cdnDir . '/' . $date . '/' . $fileName
        );
        if ($moveResult != true) {
            echo '<div class="alert alert-danger text-center" role="alert">We could not complete the upload of your CSV.</div>';
            exit;
        }

        $rows = array_map(
            'str_getcsv',
            file(TO_PATH_CDN . $cdnDir . '/' . $date . '/' . $fileName)
        );
        $header = array_shift($rows);
        $csv = array();
        foreach ($rows as $row) {
            $csv[] = array_combine($header, $row);
        }

        $hasHeader = false;

        foreach ($csv as $itemCSV) {
            if ($itemCSV["CODE"] != "") {
                $hasHeader = true;

                // if code already exists then overwrite
                $item = ORM::for_table("coupons")
                    ->where("code", $itemCSV["CODE"])
                    ->find_one();

                if ($item->id == "") {
                    $item = ORM::for_table("coupons")->create();
                }

                $item->set(
                    array(
                        'code' => $itemCSV["CODE"],
                        'type' => $this->post["type"],
                        'value' => $this->post["value"],
                        'currencies' => implode(",", $this->post["currencies"]),
                        'courses' => implode(",", $this->post["courses"]),
                        'totalLimit' => $this->post["totalLimit"],
                        'expiry' => date(
                            'Y-m-d H:i:s',
                            strtotime($this->post["expiry"])
                        ),
                        'applyTo' => $this->post["applyTo"],
                    )
                );

                if ($this->post["valueMax"] != "") {
                    $item->valueMax = $this->post["valueMax"];
                }

                if ($this->post["valueMin"] != "") {
                    $item->valueMin = $this->post["valueMin"];
                }

                $item->set_expr("whenUpdated", "NOW()");
                $item->set_expr("whenAdded", "NOW()");


                $item->save();
            }
        }

        // failsafe import for if no headers are present
        if ($hasHeader == false) {
            $csvFile = file(TO_PATH_CDN . $cdnDir . '/' . $date . '/' . $fileName);
            $data = [];
            foreach ($csvFile as $line) {
                $data[] = str_getcsv($line);
            }


            foreach ($data as $dItem) {
                // if code already exists then overwrite
                $item = ORM::for_table("coupons")
                    ->where("code", $dItem[0])
                    ->find_one();

                if ($item->id == "") {
                    $item = ORM::for_table("coupons")->create();
                }

                $item->set(
                    array(
                        'code' => $dItem[0],
                        'type' => $this->post["type"],
                        'value' => $this->post["value"],
                        'courses' => implode(",", $this->post["courses"]),
                        'totalLimit' => $this->post["totalLimit"],
                        'expiry' => date(
                            'Y-m-d H:i:s',
                            strtotime($this->post["expiry"])
                        ),
                        'applyTo' => $this->post["applyTo"],
                    )
                );

                if ($this->post["valueMax"] != "") {
                    $item->valueMax = $this->post["valueMax"];
                }

                if ($this->post["valueMin"] != "") {
                    $item->valueMin = $this->post["valueMin"];
                }

                $item->set_expr("whenUpdated", "NOW()");
                $item->set_expr("whenAdded", "NOW()");


                $item->save();
            }
        }

        $this->recordLog('bulk imported coupons');

        $this->setAlertSuccess("Coupons successfully imported from CSV");
    }

    public function createBulkVouchers()
    {
        // get group Id, used for automatic export after creation
        $previous = ORM::for_table("vouchers")->order_by_desc("id")->find_one();

        $groupID = $previous->groupID;

        if ($previous->groupID == "") {
            $groupID = 0;
        }

        $groupID = rand(10, 9999999);


        // iterate through how many there are to be created and create them
        $count = 0;
        while ($count <= $this->post["qty"]) {
            // generate code
            $characters
                = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $this->post["codeLength"]; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }

            $code = $this->post["prefix"] . $randomString;

            $item = ORM::for_table("vouchers")->create();

            $item->set(
                array(
                    'code' => $code,
                    'type' => $this->post["attachCourses"],
                    'allowCourseSelection' => $this->post["allowCourseSelection"],
                    'groupID' => $groupID
                )
            );

            if ($this->post["attachCourses"] == "specific" && $this->post["allowCourseSelection"] == "0") {
                $item->courses = implode(",", $this->post["courses"]);
            }

            if ($this->post["attachCourses"] == "price") {
                $item->valueUpto = $this->post["valueUpto"];
            }

            $item->set_expr("whenAdded", "NOW()");

            if ($this->post["expiry"] != "") {
                $item->expiry = date(
                    'Y-m-d H:i:s',
                    strtotime($this->post["expiry"])
                );
            }

            $item->save();

            $count++;
        }

        $this->recordLog('bulk created vouchers');

        $this->setAlertSuccess(
            "Voucher codes created successfully. <a href='"
            . SITE_URL . "ajax?c=blumeNew&a=download-bulk-vouchers&group=" . $groupID
            . "' style='color:#fff;font-weight:bold;'>Download as CSV.</a>"
        );
    }

    public function downloadBulkVouchers()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header(
            'Content-Disposition: attachment; filename=Vouchers_' . time()
            . '.csv'
        );

        $output = fopen('php://output', 'w');

        fputcsv($output, array(
            'CODE',
            'EXPIRY',
            'WHEN_ADDED',
            'TYPE',
            'COURSE_IDS',
            'COURSE_NAMES'
        ));

        $this->recordLog('downloaded bulk vouchers as a CSV');

        $items = ORM::for_table("vouchers")
            ->where("groupID", $this->get["group"])->order_by_asc("id")
            ->find_many();


        foreach ($items as $item) {
            $courseNames = array();

            if ($item->courses != "") {
                $courses = ORM::for_table("courses")->select("title")
                    ->where_in("id", explode(",", $item->courses))->find_many();

                foreach ($courses as $course) {
                    array_push($courseNames, $course->title);
                }
            }

            fputcsv($output, array(
                $item->code,
                $item->expiry,
                $item->whenAdded,
                $item->type,
                $item->courses,
                implode(",", $courseNames)
            ));
        }
    }

    public function createOffer()
    {
        // check existing
        $existing = ORM::for_table("offers")->where("code", $this->post["code"])
            ->count();

        if ($existing != 0) {
            $this->setAlertDanger("An offer already exists with this code.");
            exit;
        }

        if ($this->post["dateEnd"] == "") {
            $this->post["dateEnd"] = '2040-01-01';
        }

        // add to database
        $item = ORM::for_table("offers")->create();

        $item->set(
            array(
                'code' => $this->post["code"],
                'price' => $this->post["price"],
                'dateStart' => $this->post["dateStart"] . ' 00:00:00',
                'dateEnd' => $this->post["dateEnd"] . ' 00:00:00',
                'courses' => implode(",", $this->post["courses"])
            )
        );

        $item->set_expr("whenAdded", "NOW()");

        $item->save();

        $this->setAlertSuccess("Offer successfully created");

        $this->recordLog('created a new offer: ' . $this->post["code"]);

        $this->redirectJS(SITE_URL . 'blume/marketing/offers');
    }

    public function deleteOffer()
    {
        $item = ORM::for_table("offers")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted an offer');
    }

    public function downloadCertOrderCsvs()
    {
        $ids = explode(",", $this->get["ids"]);

        // download selected as a csv
        header('Content-Type: text/csv; charset=utf-8');
        header(
            'Content-Disposition: attachment; filename=Certificate_Orders_'
            . time() . '.csv'
        );

        $output = fopen('php://output', 'w');

        $this->recordLog('downloaded a certificate order CSV');


        fputcsv($output, array(
            'FIRSTNAME',
            'LASTNAME',
            'EMAIL',
            'ADDRESS_1',
            'ADDRESS_2',
            'ADDRESS_3',
            'CITY',
            'POSTCODE',
            'COUNTRY',
            'STATUS',
            'DATE_TIME',
            'CERTIFICATE'
        ));


        $items = ORM::for_table("orderItems")->where("certPaid", "1")->where_in("id", $ids)
            ->where_not_equal("certID", "")->order_by_desc("id")->find_many();


        foreach ($items as $item) {
            $order = ORM::for_table("orders")->find_one($item->orderID);

            $status = "Pending";
            if ($item->status == "d") {
                $status = "Dispatched";
            }

            if ($order->status == "completed" && $order->address1 != "") {
                fputcsv($output, array(
                    $order->firstname,
                    $order->lastname,
                    $order->email,
                    $order->address1,
                    $order->address2,
                    $order->address3,
                    $order->city,
                    $order->postcode,
                    $order->country,
                    $status,
                    $order->whenUpdated,
                    SITE_URL . 'ajax?c=certificate&a=cert-pdf-admin&id=' . $item->certID
                    . '&adminKey=gre45h-56trh_434rdfng'
                ));
            }
        }
    }

    public function deleteCourse()
    {
        // if(CUR_ID != "3" && CUR_ID != "1") {

        //     exit;
        // }

        $course = ORM::For_table("courses")->find_one($this->get["id"]);

        $modules = ORM::for_table("courseModules")->where("courseID", $course->id)->delete_many();
        $assigned = ORM::for_table("coursesAssigned")->where("courseID", $course->id)->delete_many();
        $saved = ORM::for_table("coursesSaved")->where("courseID", $course->id)->delete_many();

        $this->recordLog('deleted the following course: ' . $course->title);

        $course->delete();

        $this->redirectJS(SITE_URL . 'blume/courses');
    }

    public function exportCouponsCsv()
    {
        $ids = $this->post["ids"];

        // download selected as a csv
        header('Content-Type: text/csv; charset=utf-8');
        header(
            'Content-Disposition: attachment; filename=Coupons_' . time()
            . '.csv'
        );

        $this->recordLog('exported coupons as a CSV');

        $output = fopen('php://output', 'w');


        fputcsv(
            $output,
            array('CODE', 'TYPE', 'VALUE', 'USES', 'LIMIT', 'EXPIRY')
        );

        if ($ids == "") {
            $items = ORM::for_table("coupons")
                ->where_gt(
                    "whenAdded",
                    date('Y-m-d H:i:s', strtotime($this->post["dateFrom"]))
                )
                ->where_lt(
                    "whenAdded",
                    date('Y-m-d H:i:s', strtotime($this->post["dateTo"]))
                )
                ->order_by_desc("id")
                ->find_many();
        } else {
            $items = ORM::for_table("coupons")->where_in("id", $ids)
                ->order_by_desc("id")->find_many();
        }


        foreach ($items as $item) {
            fputcsv($output, array(
                $item->code,
                $item->type,
                $item->value,
                $item->totalUses,
                $item->totalLimit,
                $item->expiry
            ));
        }
    }

    public function exportCouponsCsvAll()
    {
        // download selected as a csv
        header('Content-Type: text/csv; charset=utf-8');
        header(
            'Content-Disposition: attachment; filename=All_Coupons_' . time()
            . '.csv'
        );

        $this->recordLog('exported coupons as a CSV');

        $output = fopen('php://output', 'w');


        fputcsv(
            $output,
            array('CODE', 'TYPE', 'VALUE', 'USES', 'LIMIT', 'EXPIRY')
        );

        $items = ORM::for_table("coupons")->order_by_desc("totalUses")->limit(30000)->find_many();


        foreach ($items as $item) {
            fputcsv($output, array(
                $item->code,
                $item->type,
                $item->value,
                $item->totalUses,
                $item->totalLimit,
                $item->expiry
            ));
        }
    }

    public function exportVouchersCsv()
    {
        $ids = $this->post["ids"];

        // download selected as a csv
        header('Content-Type: text/csv; charset=utf-8');
        header(
            'Content-Disposition: attachment; filename=Vouchers_' . time()
            . '.csv'
        );

        $output = fopen('php://output', 'w');

        $this->recordLog('exported vouchers as a CSV');


        fputcsv(
            $output,
            array(
                'CODE',
                'TYPE',
                'COURSES',
                'CLAIMED',
                'CLAIM_FIRSTNAME',
                'CLAIM_LASTNAME',
                'CLAIM_EMAIL',
                'CLAIM_DATETIME'
            )
        );

        if ($ids == "") {
            if ($this->post["type"] == "all") {
                $items = ORM::for_table("vouchers")
                    ->where_gt(
                        "whenAdded",
                        date('Y-m-d H:i:s', strtotime($this->post["dateFrom"]))
                    )
                    ->where_lt(
                        "whenAdded",
                        date('Y-m-d H:i:s', strtotime($this->post["dateTo"]))
                    )
                    ->order_by_desc("id")
                    ->find_many();
            } else {
                if ($this->post["type"] == "redeemed") {
                    $items = ORM::for_table("vouchers")
                        ->where_gt(
                            "whenAdded",
                            date(
                                'Y-m-d H:i:s',
                                strtotime($this->post["dateFrom"])
                            )
                        )
                        ->where_lt(
                            "whenAdded",
                            date(
                                'Y-m-d H:i:s',
                                strtotime($this->post["dateTo"])
                            )
                        )
                        ->where_not_equal("userID", "")
                        ->order_by_desc("id")
                        ->find_many();
                } else {
                    if ($this->post["type"] == "unredeemed") {
                        $items = ORM::for_table("vouchers")
                            ->where_gt(
                                "whenAdded",
                                date(
                                    'Y-m-d H:i:s',
                                    strtotime($this->post["dateFrom"])
                                )
                            )
                            ->where_lt(
                                "whenAdded",
                                date(
                                    'Y-m-d H:i:s',
                                    strtotime($this->post["dateTo"])
                                )
                            )
                            ->where("userID", "")
                            ->order_by_desc("id")
                            ->find_many();
                    }
                }
            }
        } else {
            $items = ORM::for_table("vouchers")->where_in("id", $ids)
                ->order_by_desc("id")->find_many();
        }

        // fputcsv($output, array('CODE', 'TYPE', 'COURSES', 'CLAIMED', 'CLAIM_FIRSTNAME', 'CLAIM_LASTNAME', 'CLAIM_EMAIL', 'CLAIM_DATETIME'));

        foreach ($items as $item) {
            $used = "No";
            $claimDate = "";
            $claimFirstname = "";
            $claimLastname = "";
            $claimEmail = "";

            if ($item->whenClaimed != "") {
                $claimDate = $item->whenClaimed;
            }

            if ($item->userID != "") {
                $used = "Yes";

                $user = ORM::for_table("accounts")
                    ->select("firstname")
                    ->select("lastname")
                    ->select("email")
                    ->find_one($item->userID);

                if ($user->firstname != "") {
                    $claimFirstname = $user->firstname;
                    $claimLastname = $user->lastname;
                    $claimEmail = $user->email;
                }
            }

            fputcsv(
                $output,
                array(
                    $item->code,
                    $item->type,
                    $item->courses,
                    $used,
                    $claimFirstname,
                    $claimLastname,
                    $claimEmail,
                    $claimDate
                )
            );
        }
    }

    public function actionCertificateOrders()
    {
        $ids = $this->post["ids"];

        $this->debug($this->post);

        $from = explode("/", $this->post["dateFrom"]);
        $to = explode("/", $this->post["dateTo"]);

        $this->post["dateFrom"] = $from[2] . '-' . $from[1] . '-' . $from[0];
        $this->post["dateTo"] = $to[2] . '-' . $to[1] . '-' . $to[0];

        if ($this->post["dateFrom"] != "") {
            $ids = array();

            $items = ORM::for_table("orderItems")->where("certPaid", "1")->where_not_equal("certID", "")->where_gt(
                "whenCreated",
                $this->post["dateFrom"] . ' 00:00:00'
            )->where_lt("whenCreated", $this->post["dateTo"] . ' 23:59:59')->order_by_asc("id")->find_many();

            if ($this->post["filter1"] == "pending") {
                $items = ORM::for_table("orderItems")->where("status", "p")->where("certPaid", "1")->where_not_equal(
                    "certID",
                    ""
                )->where_gt("whenCreated", $this->post["dateFrom"] . ' 00:00:00')->where_lt(
                    "whenCreated",
                    $this->post["dateTo"] . ' 23:59:59'
                )->order_by_asc("id")->find_many();
            }

            foreach ($items as $item) {
                array_push($ids, $item->id);
            }
        }


        if ($this->post["type"] == "csv") {
            $this->redirectJS(SITE_URL . 'ajax?c=blumeNew&a=download-cert-order-csvs&ids=' . implode(",", $ids));
        } else {
            if ($this->post["type"] == "sent") {
                $this->recordLog('bulk marked certificate orders as sent');

                foreach ($ids as $id) {
                    ?>
                    <script>
                        // update label without reloading page
                        $(".statusLabel<?= $id ?>").html('<label class="label label-success">Dispatched</label>');

                        // send request to server
                        $("#returnStatus").load("<?= SITE_URL ?>ajax?c=blumeNew&a=mark-order-dispatched&id=<?= $id ?>");
                    </script>
                    <?php
                }
            } else {
                if ($this->post["type"] == "unsent") {
                    $this->recordLog('bulk marked certificate orders as unsent');

                    foreach ($ids as $id) {
                        ?>
                        <script>
                            // update label without reloading page
                            $(".statusLabel<?= $id ?>").html('<label class="label label-system">Pending</label>');

                            // send request to server
                            $("#returnStatus").load("<?= SITE_URL ?>ajax?c=blumeNew&a=mark-order-undispatched&id=<?= $id ?>");
                        </script>
                        <?php
                    }
                } else {
                    if ($this->post["type"] == "zip") {
                        $this->recordLog('downloaded ordered certificates as a ZIP');


                        // download selected as a zip

                        $zip = new ZipArchive;
                        $name = time() . '.zip';
                        $tmp_file = TO_PATH_CDN . 'certificates/zips/' . $name;

                        $zip->open($tmp_file, ZipArchive::CREATE);

                        foreach ($ids as $id) {
                            $rand = rand(
                                1,
                                10000
                            ); // we need to add something unique just in case there are two of the same cert

                            $item = ORM::for_table("orderItems")->where("certPaid", "1")->where_not_equal(
                                "certID",
                                ""
                            )->order_by_desc("id")->find_one($id);

                            $assigned = ORM::for_table("coursesAssigned")->find_one($item->certID);

                            $order = ORM::for_table("orders")->find_one($item->orderID);

                            if ($order->status == "completed" && $order->address1 != "" && $assigned->certFile != "") {
                                $zip->addFile(
                                    TO_PATH_CDN . 'certificates/' . $assigned->certFile,
                                    'certificates/' . $rand . '_' . $assigned->certFile
                                );
                            }
                        }


                        $zip->close();

                        $this->redirectJS(SITE_URL . 'assets/cdn/certificates/zips/' . $name);
                    } else {
                        if ($this->post["type"] == "merged") {
                            // download as a merged PDF
                            require_once(__DIR__ . '/../classes/fpdf183/fpdf.php');
                            require_once(__DIR__ . '/../classes/FPDI-master/src/autoload.php');


                            $pdf = new \setasign\Fpdi\Fpdi();

                            $count = 0;

                            foreach ($ids as $id) {
                                $rand = rand(
                                    1,
                                    10000
                                ); // we need to add something unique just in case there are two of the same cert

                                $item = ORM::for_table("orderItems")->where("certPaid", "1")->where_not_equal(
                                    "certID",
                                    ""
                                )->order_by_desc("id")->find_one($id);

                                $assigned = ORM::for_table("coursesAssigned")->find_one($item->certID);

                                $order = ORM::for_table("orders")->find_one($item->orderID);

                                if ($order->status == "completed" && $order->address1 != "") {
                                    if ($assigned->certFile != "") {
                                        
                                        if(file_exists(TO_PATH_CDN . 'certificates/' . $assigned->certFile)) {
                                            
                                            $pageCount = $pdf->setSourceFile(
                                                TO_PATH_CDN . 'certificates/' . $assigned->certFile
                                            );
                                        
                                        } else {
                                            
                                            $pageCount = $pdf->setSourceFile(
                                                str_replace("public/", "", TO_PATH_CDN) . 'certificates/' . $assigned->certFile
                                            );
                                            
                                        }
                                        
                                        for ($i = 0; $i < $pageCount; $i++) {
                                            $tpl = $pdf->importPage($i + 1, '/MediaBox');
                                            $pdf->addPage('L');
                                            $pdf->useTemplate($tpl);
                                        }
                                        $count++;
                                        if ($count == 100) { // limit for memory reasons
                                            break;
                                        }
                                    }
                                }
                            }

                            $fileName = 'merged' . rand(10, 9999) . '.pdf';

// output the pdf as a file (http://www.fpdf.org/en/doc/output.htm)
                            $pdf->Output(TO_PATH_CDN . 'certificates/' . $fileName, 'F');

                            $this->redirectJS(SITE_URL . 'assets/cdn/certificates/' . $fileName);
                        }
                    }
                }
            }
        }
    }

    public function actionPrintedOrders()
    {
        $ids = $this->post["ids"];

        if ($this->post["type"] == "csv") {
            // download selected as a csv
            header('Content-Type: text/csv; charset=utf-8');
            header(
                'Content-Disposition: attachment; filename=Printed_Orders_'
                . time() . '.csv'
            );

            $output = fopen('php://output', 'w');


            fputcsv($output, array(
                'FIRSTNAME',
                'LASTNAME',
                'EMAIL',
                'ADDRESS_1',
                'CITY',
                'POSTCODE',
                'COUNTRY',
                'STATUS',
                'DATE_TIME',
                'COURSE'
            ));


            $items = ORM::for_table("orderItems")->where_in("id", $ids)
                ->order_by_desc("id")->find_many();


            foreach ($items as $item) {
                $order = ORM::for_table("orders")->find_one($item->orderID);

                $status = "Pending";
                if ($item->status == "d") {
                    $status = "Dispatched";
                }

                $course = ORM::for_table("courses")->find_one($item->courseID);

                fputcsv($output, array(
                    $order->firstname,
                    $order->lastname,
                    $order->email,
                    $order->address1,
                    $order->city,
                    $order->postcode,
                    $order->country,
                    $status,
                    $order->whenUpdated,
                    $course->title
                ));
            }
        } else {
            if ($this->post["type"] == "sent") {
                foreach ($ids as $id) {
                    ?>
                    <script>
                        // update label without reloading page
                        $(".statusLabel<?= $id ?>").html('<label class="label label-success">Dispatched</label>');

                        // send request to server
                        $("#returnStatus").load("<?= SITE_URL ?>ajax?c=blumeNew&a=mark-order-dispatched&id=<?= $id ?>");
                    </script>
                    <?php
                }
            } else {
                if ($this->post["type"] == "unsent") {
                    foreach ($ids as $id) {
                        ?>
                        <script>
                            // update label without reloading page
                            $(".statusLabel<?= $id ?>").html('<label class="label label-system">Pending</label>');

                            // send request to server
                            $("#returnStatus").load("<?= SITE_URL ?>ajax?c=blumeNew&a=mark-order-undispatched&id=<?= $id ?>");
                        </script>
                        <?php
                    }
                }
            }
        }
    }

    public function saveCourseCategories()
    {
        // remove existing category records first
        $items = ORM::for_table("courseCategoryIDs")
            ->where("course_id", $this->post["courseID"])
            ->delete_many();

        // add new category records
        foreach ($this->post["categories"] as $category) {
            $item = ORM::for_table("courseCategoryIDs")->create();

            $item->course_id = $this->post["courseID"];
            $item->category_id = $category;

            $item->save();
        }

        $this->recordLog('saved a courses categories');

        $this->setAlertSuccess("Course categories successfully updated");
    }

    public function getSingleOffer()
    {
        return ORM::for_table("offerPages")->find_one($this->get["id"]);
    }

    public function deleteOfferPage()
    {
        $item = ORM::for_table("offerPages")->find_one($this->get["id"]);
        $item->delete();
    }

    public function newOfferPage()
    {
        $item = ORM::For_table("offerPages")->create();

        $item->set(
            array(
                'title' => $this->post["title"],
                'slug' => $this->createSlug($this->post["title"])
            )
        );

        $item->set_expr("whenAdded", "NOW()");

        $item->save();

        $this->recordLog('created a new offer page: ' . $this->post["title"]);

        $this->redirectJS(SITE_URL . 'blume/offers/edit?id=' . $item->id());
    }

    public function editOfferPage()
    {
        $item = ORM::For_table("offerPages")->find_one($this->post["itemID"]);

        $item->set(
            array(
                'title' => $this->post["title"],
                'contents' => $this->post["contents"],
                'course1Price' => $this->post["course1Price"],
                'courseOtherPrice' => $this->post["courseOtherPrice"],
                'showInAccounts' => $this->post["showInAccounts"],
                'accountDescription' => $this->post["contentsAccount"],
                'maxCourses' => $this->post["maxCourses"],
                'primaryCol' => $this->post["primaryCol"],
                'secondCol' => $this->post["secondCol"],
                'slug' => $this->post["slug"],
            )
        );

        $item->save();

        $this->recordLog('edited an offer page: ' . $this->post["title"]);

        $this->setAlertSuccess("Offer page successfully updated");
    }

    public function editOfferCourses()
    {
        $item = ORM::For_table("offerPages")->find_one($this->post["itemID"]);

        $item->set(
            array(
                'courses' => @$this->post["courses"]
                    ? json_encode($this->post["courses"]) : null
            )
        );

        $item->save();

        $this->setAlertSuccess("Offer page successfully updated");
    }

    public function createSupportFaq()
    {
        $item = ORM::for_table("supportFaqs")->create();

        $item->question = $this->post["title"];
        $item->set_expr("whenAdded", "NOW()");

        $item->save();

        $this->recordLog('created a new support FAQ: ' . $this->post["title"]);

        $this->redirectJS(SITE_URL . 'blume/support/faqs/edit?id=' . $item->id());
    }

    public function editSupportFaq()
    {
        $item = ORM::for_table("supportFaqs")->find_one($this->post["itemID"]);

        $item->question = $this->post["question"];
        $item->answer = $this->post["contents"];

        $item->save();

        $this->recordLog('edited a support FAQ: ' . $this->post["title"]);

        $this->setAlertSuccess("FAQ successfully updated");
    }

    public function deleteSupportFaq()
    {
        $item = ORM::for_table("supportFaqs")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted a support FAQ');
    }

    public function createAffiliateFaq()
    {
        $item = ORM::for_table("affiliateFaqs")->create();

        $item->question = $this->post["title"];
        $item->set_expr("whenAdded", "NOW()");

        $item->save();

        $this->recordLog('created a new affiliate FAQ: ' . $this->post["title"]);

        $this->redirectJS(
            SITE_URL . 'blume/content/affiliate-faqs/edit?id='
            . $item->id()
        );
    }

    public function editAffiliateFaq()
    {
        $item = ORM::for_table("affiliateFaqs")
            ->find_one($this->post["itemID"]);

        $item->question = $this->post["question"];
        $item->answer = $this->post["contents"];

        $item->save();

        $this->recordLog('edited an affiliate FAQ: ' . $this->post["title"]);

        $this->setAlertSuccess("FAQ successfully updated");
    }

    public function deleteAffiliateFaq()
    {
        $item = ORM::for_table("affiliateFaqs")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted an affiliate FAQ');
    }

    public function createFaq()
    {
        $item = ORM::for_table("faqs")->create();

        $item->question = $this->post["title"];
        $item->type = $this->post["type"];
        $item->set_expr("whenAdded", "NOW()");

        $item->save();

        $this->recordLog('created a new FAQ: ' . $this->post["title"]);

        $this->redirectJS(
            SITE_URL . 'blume/content/faqs/edit?id='
            . $item->id()
        );
    }

    public function editFaq()
    {
        $item = ORM::for_table("faqs")
            ->find_one($this->post["itemID"]);

        $item->question = $this->post["question"];
        $item->answer = $this->post["contents"];
        $item->type = $this->post["type"];

        $item->save();

        $this->recordLog('edited an FAQ: ' . $this->post["title"]);

        $this->setAlertSuccess("FAQ successfully updated");
    }

    public function deleteFaq()
    {
        $item = ORM::for_table("faqs")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted an FAQ');
    }

    public function createCourseModuleFaq()
    {
        $item = ORM::for_table("courseModuleFaqs")->create();

        $item->question = $this->post["title"];
        $item->set_expr("whenAdded", "NOW()");

        $item->save();

        $this->recordLog('created a course module FAQ: ' . $this->post["title"]);

        $this->redirectJS(
            SITE_URL . 'blume/courses/modules/faqs/edit?id='
            . $item->id()
        );
    }

    public function editCourseModuleFaq()
    {
        $item = ORM::for_table("courseModuleFaqs")
            ->find_one($this->post["itemID"]);

        $item->question = $this->post["question"];
        $item->answer = $this->post["contents"];

        $item->save();

        $this->recordLog('edited a course module FAQ: ' . $this->post["title"]);

        $this->setAlertSuccess("FAQ successfully updated");
    }

    public function deleteCourseModuleFaq()
    {
        $item = ORM::for_table("courseModuleFaqs")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted a course module FAQ');
    }

    public function deleteCourseModule()
    {
        $item = ORM::for_table("courseModules")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted a course module');
    }

    public function addAchiever()
    {
        // submit to admin system for review, and also send email to NSA team
        $this->validateValues(array("firstname", "email", "phone", "city"));

        $item = ORM::for_table("achievers")->create();

        $item->firstname = $this->post["firstname"];
        $item->lastname = $this->post["lastname"];
        $item->email = $this->post["email"];
        $item->phone = $this->post["phone"];
        $item->city = $this->post["city"];
        $item->status = "a";

        $item->set_expr("whenSubmitted", "NOW()");

        $item->save();

        $itemID = $item->id();

        // Update image
        if ($this->checkFileUploadSelected() == true) {
            $sizes = ['large', 'medium', 'thumb'];
            $model = [
                'type' => achieverBoardController::class,
                'id' => $itemID
            ];
            $this->medias->uploadFile(
                $sizes,
                $model,
                'uploaded_file',
                'main_image',
                true
            );
        }

        $this->recordLog('added a new achiever: ' . $this->post["firstname"] . ' ' . $this->post["lastname"]);

        $this->redirectJS(SITE_URL . 'blume/achievers');
    }

    public function editAchiever()
    {
        // submit to admin system for review, and also send email to NSA team
        $this->validateValues(array("firstname", "email", "phone", "city"));

        $item = ORM::for_table("achievers")->find_one($this->post["itemID"]);

        $item->firstname = $this->post["firstname"];
        $item->lastname = $this->post["lastname"];
        $item->email = $this->post["email"];
        $item->phone = $this->post["phone"];
        $item->city = $this->post["city"];

        $item->save();

        // Update image
        if ($this->checkFileUploadSelected() == true) {
            $sizes = ['large', 'medium', 'thumb'];
            $model = [
                'type' => achieverBoardController::class,
                'id' => $this->post["itemID"]
            ];
            $this->medias->uploadFile(
                $sizes,
                $model,
                'uploaded_file',
                'main_image',
                true
            );
        }


        $this->setAlertSuccess("Achiever successfully updated");
    }

    public function coursePdfContents()
    {
        $id = $this->get["id"];

        if (!$id) {
            $this->force404();
            exit;
        }

        $course = ORM::for_table("courses")->find_one($id);

        if ($course->id == "") {
            $this->force404();
            exit;
        }

        ?>
        <div style="background: #ececec9c; width: 100%">
            <div style="padding:10px 0;width:705px;">
                <div>
                    <h1><?= $course->title ?></h1>
                </div>

                <div>
                    <?= $course->description ?>
                </div>

                <br/>

                <?php
                $modules = ORM::for_table("courseModules")
                    ->where("courseID", $course->id)
                    ->order_by_asc("ord")
                    ->find_many();

                foreach ($modules as $key => $module) {
                    $description = preg_replace(
                        "/<img[^>]+\>/i",
                        " ",
                        $module->description
                    );
                    $contents = preg_replace(
                        "/<img[^>]+\>/i",
                        " ",
                        $module->contents
                    );
                    ?>
                    <div class="module" id="module<?= $module->id ?>"
                         style="margin-top: 20px; background: #ffffff; padding: 10px; box-shadow: 3px 2px 5px 0px #cacaca;">
                        <h3><?= $module->title ?></h3>
                        <div class="row"><?= $description ?></div>
                        <div class="row"><?= $contents ?></div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

        <div style="height:1px;width:100%;background:#eeeeee;margin-top:10px;"></div>

        <style>
            body {
                padding: 0;
                margin: 0;
            }
        </style>

        <?php
    }

    public function getCoursePdf()
    {
        $id = $this->get["id"];

        if (!$id) {
            $this->force404();
            exit;
        }

        $pdf = ORM::for_table("media")
            ->where("modelType", "courseController")
            ->where("modelId", $id)
            ->where("type", "main_pdf")
            ->find_one();

        if ($pdf && $pdf->id) {
            $file = explode('assets', $pdf->url);
            $filepath = "assets" . $file[1];
            // Process download
            if (file_exists($filepath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header(
                    'Content-Disposition: attachment; filename="'
                    . basename($filepath) . '"'
                );
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filepath));
                flush(); // Flush system output buffer
                readfile($filepath);
                die();
            }
        }

        $course = ORM::for_table("courses")->find_one($id);

        if ($course->id == "") {
            $this->force404();
            exit;
        }

        $pdfContent = file_get_contents(
            SITE_URL
            . 'ajax?c=course&a=course-pdf-contents&id=' . $id
        );

        /*require_once(__DIR__ . '/../classes/html2pdf-4.4.0/html2pdf.class.php');

        $html2pdf = new HTML2PDF('P','A4','en');
        $html2pdf->WriteHTML($content);
        $html2pdf->Output('New Skills Academy - Course '.$course->id.'.pdf');*/
//        ini_set('display_errors', 1);
//        ini_set('display_startup_errors', 1);
//        error_reporting(E_ALL);

        $mpdf = new Mpdf([
            'tempDir' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mpdf'
        ]);
        $mpdf->WriteHTML($pdfContent);
        $mpdf->Output($course->title . '.pdf', 'D');

        die('done');
    }

    public function courseDownloadContent()
    {
        $courseID = $this->post["id"];

        ?>
        <br/>
        <br/>
        <div class="row">
            <div class="col-xs-6 col-6">
                <h4>Worksheets</h4>
                <?php
                $count = 0;
                foreach (
                    ORM::for_table("courseModules")
                        ->where("courseID", $courseID)->order_by_asc("ord")
                        ->find_many() as $module
                ) {
                    $dom = new DOMDocument;

                    @$dom->loadHTML($module->contents);

                    $links = $dom->getElementsByTagName('a');

                    foreach ($links as $link) {
                        if (strpos($link->nodeValue, "Worksheet") !== false) {
                            $file = substr(
                                $link->getAttribute('href'),
                                strrpos($link->getAttribute('href'), '/') + 1
                            );
                            ?>
                            <p>
                                <a href="<?= $link->getAttribute('href') ?>"
                                   target="_blank" style="    color: grey;
                        font-size: 17px;">
                                    <i class="fa fa-file"
                                       style="margin-right:5px;    color: #a00303;"></i>
                                    <?= $module->title ?> | <?= $file ?>
                                </a>
                            </p>

                            <?php
                        }
                    }

                    $worksheet = ORM::for_table("media")
                        ->where("modelType", "courseModuleController")
                        ->where("modelId", $module->id)
                        ->where("type", "worksheet")
                        ->find_one();

                    if ($worksheet->id != "") {
                        $count++;
                        ?>
                        <p>
                            <a href="<?= $worksheet->url ?>" target="_blank"
                               style="    color: grey;
    font-size: 17px;">
                                <i class="fa fa-file"
                                   style="margin-right:5px;    color: #a00303;"></i>
                                <?= $module->title ?>
                            </a>
                        </p>

                        <?php
                    }
                }

                if ($count == 0) {
                    ?>
                    <p><em>There are no worksheets for this course.</em></p>
                    <?php
                }
                ?>
            </div>
            <div class="col-xs-6 col-6">
                <h4>Module PDF's</h4>
                <?php
                foreach (
                    ORM::for_table("courseModules")
                        ->where("courseID", $courseID)->order_by_asc("ord")
                        ->find_many() as $module
                ) {
                    ?>
                    <p>
                        <a href="<?= SITE_URL ?>ajax?c=course&a=get-module-pdf&id=<?= $module->id ?>"
                           target="_blank" style="    color: grey;
    font-size: 17px;">
                            <i class="fa fa-file"
                               style="margin-right:5px;    color: #a00303;"></i>
                            <?= $module->title ?>
                        </a>
                    </p>

                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }

    public function deleteItem()
    {
        $table = $this->post['table'];
        $id = $this->post['id'];

        $item = ORM::for_table($table)->find_one($id);

        $item->delete();

        $data = [
            'message' => 'Delete Successfully'
        ];

        echo json_encode(array(
            'status' => 200,
            'data' => $data
        ));

        exit;
    }

    public function createCourseBlog()
    {
        $postData = $this->post;
        $blogData = $postData;

        $course = $this->blogs->saveBlog($blogData);

        $this->recordLog('created a new course blog');

        $this->setAlertSuccess("Blog added");
    }

    public function editCourseBlog()
    {
        $postData = $this->post;
        $blogData = $postData;

        $course = $this->blogs->saveBlog($blogData);

        $this->setAlertSuccess("Blog details updated");
    }

    public function completeCourseAssigned()
    {
        // marks assigned course as complete from customer service portal

        $courseAssigned = ORM::for_table("coursesAssigned")
            ->find_one($this->post["id"]);

        // certificate number
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        // save details
        $courseAssigned->certNo = $randomString;
        $courseAssigned->completed = "1";
        if (@$this->post["whenCompleted"]) {
            $courseAssigned->whenCompleted = $this->post["whenCompleted"] . " " . date("H:i:s");
        } else {
            $courseAssigned->set_expr("whenCompleted", "NOW()");
        }
        $courseAssigned->percComplete = "100";

        $this->recordLog('marked a course as complete for the following user: ' . $courseAssigned->accountID);
        if ($courseAssigned->save()) {
            $this->setAlertSuccess("Course has been completed!");
        } else {
            $this->setAlertDanger("Something went wrong!");
        }
    }

    public function resetCourseUser()
    {
        $courseAssigned = ORM::for_table("coursesAssigned")
            ->find_one($this->get["id"]);

        // save details
        $courseAssigned->certNo = "";
        $courseAssigned->completed = "0";
        $courseAssigned->percComplete = "0";
        $courseAssigned->currentModule = "0";

        $this->recordLog('reset a course for the following user: ' . $courseAssigned->accountID);

        $courseAssigned->save();
    }

    public function countModules($courseID)
    {
        return ORM::for_table("courseModules")->where("courseID", $courseID)
            ->count();
    }

    public function moveUserNextModule()
    {
        // moves the user to the next module (and completes the enrollment if its the last module) from customer service portal
        $courseAssigned = ORM::for_table("coursesAssigned")
            ->find_one($this->get["id"]);

        $accountID = $courseAssigned->accountID;

        $module = ORM::For_table("modules")
            ->find_one($courseAssigned->currentModule);
        $course = ORM::For_table("courses")
            ->find_one($courseAssigned->courseID);

        // all modules
        $modules = ORM::for_table("courseModules")
            ->where("courseID", $course->id)
            ->order_by_asc("ord")
            ->find_many();

        $modArray = array();

        $count = 1;
        foreach ($modules as $mod) {
            $modArray[$count] = $mod->id;

            $count++;
        }

        $key = array_search($module->id, $modArray);
        $newKey = $key + 1;


        if ($modArray[$newKey] == "") {
            // no other module to complete, course is complete
            $token = openssl_random_pseudo_bytes(20);
            $token = bin2hex($token);


            $courseAssigned->token = $token;

            $courseAssigned->save();


            $courseAssigned = ORM::for_table("coursesAssigned")
                ->find_one($this->get["id"]);

            // certificate number
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < 10; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }

            // @todo: check existing


            // save details
            $courseAssigned->certNo = $randomString;
            $courseAssigned->completed = "1";
            $courseAssigned->set_expr("whenCompleted", "NOW()");
            $courseAssigned->percComplete = "100";

            $courseAssigned->save();
        } else {
            // update with next module

            // mark this as current module and update percentage progress

            // calculate percentage
            $modules = ORM::for_table("courseModules")
                ->where("courseID", $module->courseID)
                ->order_by_asc("ord")
                ->find_many();

            $modArray = array();

            $count = 1;
            foreach ($modules as $mod) {
                $modArray[$count] = $mod->id;

                $count++;

                // Save Module progress to complete for previous module
                if (($module->ord != 1) && (($module->ord - 1) == $mod->ord)) {
                    $this->courseModules->saveCourseModuleProgress([
                        'accountID' => $accountID,
                        'courseID' => $mod->courseID,
                        'moduleID' => $mod->id,
                        'completed' => 1,
                        'whenCompleted' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            $key = array_search($module->id, $modArray);

            $totalModules = $this->countModules($module->courseID);
            $percentage = number_format(($key / $totalModules) * 100, 2);

            // Save Course Module Progress
            $this->courseModules->saveCourseModuleProgress([
                'accountID' => $accountID,
                'courseID' => $module->courseID,
                'moduleID' => $module->id,
                'completed' => 0,
                'whenStarted' => date('Y-m-d H:i:s')
            ]);

            $currentAssigned = ORM::for_table("coursesAssigned")
                ->where("accountID", $accountID)
                ->where("courseID", $module->courseID)
                ->find_one();

            $currentAssigned->currentModule = $module->id;
            $currentAssigned->currentModuleKey = $key;
            $currentAssigned->set_expr("lastAccessed", "NOW()");
            $currentAssigned->percComplete = $percentage;
            $currentAssigned->save();
        }
    }

    public function loadUserCourses()
    {
        $account = ORM::for_table("accounts")->find_one($this->get["id"]);

        $this->recordLog('viewed the following users account: ' . $account->id);

        ?>
        <table class="table datatableCourses" data-filter="#fooFilter"
               data-page-navigation=".pagination" data-page-size="50">
            <thead>
            <tr>
                <th>Course</th>
                <th>% Complete</th>
                <th>Current Module</th>
                <th>Enrolled</th>
                <th>Learning Time</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $items = ORM::for_table("coursesAssigned")
                ->where("accountID", $account->id)
                ->where_null("bundleID")
                ->order_by_desc("whenAssigned")->find_many();

            foreach ($items as $item) {
                $course = ORM::for_table("courses")->find_one($item->courseID);
                ?>
                <tr id="item<?= $item->id ?>"
                    class="enrollment<?= $item->id ?>">
                    <td>
                        <strong><?= $course->title ?></strong>
                        <?php
                        if ($item->sub == "1") {
                            ?>
                            <i class="fa fa-star" style="color:#e0c011;margin-left:5px;"></i>
                            <?php
                        }
                        ?>
                    </td>
                    <td>
                        <?= $item->percComplete ?>%
                        <?php
                        if ($item->completed == "1") {
                            ?>
                            <a href="javascript:;"
                               class="label label-system completedCourse<?= $item->id ?>"
                               onclick="completeCourse(<?= $item->id ?>,'<?= date(
                                   "Y-m-d",
                                   strtotime($item->whenCompleted)
                               ) ?>');">Completed</a>
                            <a href="<?= SITE_URL ?>ajax?c=certificate&a=cert-pdf&id=<?= $item->id ?>&adminKey=gre45h-56trh_434rdfng"
                               class="label label-info" target="_blank">View
                                Cert.</a>
                            <?php

                            if ($course->isNCFE == "1" && $item->certFile == "") {
                                ?>
                                <a href="javascript:;" class="label label-warning" data-toggle="modal"
                                   data-target="#upload<?= $item->id ?>">
                                    <i class="fa fa-upload"></i>
                                </a>
                                <?php
                            }
                        } else {
                            ?>
                            <a href="javascript:;"
                               class="label label-success completeCourse<?= $item->id ?>"
                               onclick="completeCourse(<?= $item->id ?>);">Complete</a>

                            <a href="<?= SITE_URL ?>ajax?c=certificate&a=cert-pdf&id=<?= $item->id ?>&adminKey=gre45h-56trh_434rdfng"
                               style="display:none;"
                               class="label label-info displayCert<?= $item->id ?>"
                               target="_blank">View Cert.</a>
                            <?php
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $currentModule = ORM::for_table("courseModules")
                            ->select("title")->find_one($item->currentModule);
                        ?>
                        <?= substr($currentModule->title, 0, 12) ?>...
                    </td>
                    <td>
                        <?= date(
                            'd/m/Y @ H:i:s',
                            strtotime($item->whenAssigned)
                        ) ?>
                    </td>
                    <td>
                        <?php
                        $value = $this->courseTimeProgress->calculateCourseTimeAccount(
                            $account->id,
                            '2022-02-01',
                            date('Y-m-d'),
                            $item->id
                        );
                        echo number_format($value / 3600, 2)
                        ?>
                        hrs
                    </td>
                    <td>
                        <label class="label label-system" data-toggle="modal"
                               data-target="#changeModule<?= $item->id ?>"
                               style="margin-left:5px;">
                            <i class="fa fa-chevron-right"></i>
                            Change Module
                        </label>
                        <label class="label label-success"
                               onclick="resetCourse(<?= $item->id ?>);"
                               style="margin-left:5px;">
                            <i class="fa fa-repeat"></i>
                            Reset
                        </label>
                        <label class="label label-danger"
                               style="margin-left:5px;"
                               onclick="deleteEnrollment(<?= $item->id ?>);">
                            <i class="fa fa-trash"></i>
                        </label>
                    </td>
                </tr>

                <div class="modal fade" id="changeModule<?= $item->id ?>"
                     tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close"
                                        data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Change
                                    Module</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Which module do you want to move this
                                        user to?</label>
                                    <select class="form-control"
                                            name="currentModule"
                                            id="currentModule<?= $item->id ?>">
                                        <?php
                                        foreach (
                                            ORM::for_table("courseModules")
                                                ->where("courseID", $course->id)
                                                ->where_null('parentID')
                                                ->order_by_asc("ord")
                                                ->find_many() as $module
                                        ) {
                                            $submodules = ORM::for_table("courseModules")
                                                ->where("courseID", $course->id)
                                                ->where('parentID', $module->id)
                                                ->order_by_asc("ord")
                                                ->find_many();
                                            ?>
                                            <option <?php
                                            if (count($submodules) >= 1) { ?> disabled <?php
                                            } ?>
                                                    value="<?= $module->id ?>"
                                                    <?php
                                                    if ($item->currentModule
                                                    == $module->id) { ?>selected<?php
                                            } ?>><?= str_replace(
                                                    '"',
                                                    '',
                                                    $module->title
                                                ) ?>
                                            </option>
                                            <?php

                                            if (count($submodules) >= 1) {
                                                foreach ($submodules as $submodule) {
                                                    ?>
                                                    <option <?php
                                                            if ($item->currentSubModule
                                                    == $submodule->id) { ?>selected<?php
                                                    } ?>
                                                            value="<?= $submodule->id ?>">
                                                        --<?= str_replace('"', '', $submodule->title); ?>
                                                    </option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Also mark entire course as
                                        complete?</label>
                                    <select class="form-control"
                                            name="completed"
                                            id="completed<?= $item->id ?>">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Do you also want to reset this users quiz progress?</label>
                                    <select class="form-control"
                                            name="resetQuiz"
                                            id="resetQuiz<?= $item->id ?>">
                                        <option value="0">No</option>
                                        <option value="1" selected>Yes</option>
                                    </select>
                                </div>
                                <input type="hidden" name="itemID"
                                       value="<?= $item->id ?>"/>
                                <input type="hidden" name="accountID"
                                       value="<?= $account->id ?>"/>
                                <div id="returnStatusModule<?= $item->id ?>"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default"
                                        data-dismiss="modal">Close
                                </button>
                                <button type="button"
                                        onclick="updateModule<?= $item->id ?>()"
                                        class="btn btn-primary">Update
                                </button>
                            </div>

                            <script>
                                function updateModule<?= $item->id ?>() {

                                    var currentModule = $("#currentModule<?= $item->id ?>").val();
                                    var completed = $("#completed<?= $item->id ?>").val();
                                    var resetQuiz = $("#resetQuiz<?= $item->id ?>").val();

                                    $.post("<?= SITE_URL ?>ajax?c=blumeNew&a=change-user-module",
                                        {
                                            itemID: '<?= $item->id ?>',
                                            accountID: '<?= $account->id ?>',
                                            currentModule: currentModule,
                                            resetQuiz: resetQuiz,
                                            completed: completed
                                        },
                                        function (data, status) {

                                            $("#returnStatusModule<?= $item->id ?>").append(data);

                                        });
                                }
                            </script>

                        </div>
                    </div>
                </div>

                <div class="modal fade" id="upload<?= $item->id ?>"
                     tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close"
                                        data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Upload Certificate</h4>
                            </div>

                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Select File (PDF):</label>
                                    <input type="file" name="uploaded_file" id="file<?= $item->id ?>"/>
                                </div>
                                <input type="hidden" name="itemID" value="<?= $item->id ?>"/>
                                <div id="returnStatusUpload<?= $item->id ?>"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default"
                                        data-dismiss="modal">Close
                                </button>
                                <button type="button" onclick="uploadCert<?= $item->id ?>()"
                                        class="btn btn-primary">Upload
                                </button>
                            </div>
                            <script>
                                function uploadCert<?= $item->id ?>() {

                                    var fd = new FormData();
                                    var files = $('#file<?= $item->id ?>')[0].files;

                                    fd.append('file', files[0]);

                                    $.ajax({
                                        url: '<?= SITE_URL ?>ajax?c=blumeNcfe&a=manual-certificate-upload&id=<?= $item->id ?>',
                                        type: 'post',
                                        data: fd,
                                        contentType: false,
                                        processData: false,
                                        success: function (response) {
                                            $("#returnStatusUpload<?= $item->id ?>").append(response);
                                        },
                                    });
                                }
                            </script>


                        </div>
                    </div>
                </div>
                <?php

                $subItems = ORM::for_table("coursesAssigned")
                    ->where("accountID", $account->id)
                    ->where("bundleID", $item->id)->find_many();

                foreach ($subItems as $sub) {
                    $subCourse = ORM::for_table("courses")->find_one($sub->courseID);

                    ?>
                    <tr id="item<?= $sub->id ?>"
                        class="enrollment<?= $sub->id ?>">
                        <td style="padding-left:30px;">
                            <i class="fa fa-chevron-right" style="margin-right:20px;"></i>
                            <strong><?= $subCourse->title ?></strong>
                        </td>
                        <td>
                            <?= $sub->percComplete ?>%
                            <?php
                            if ($sub->completed == "1") {
                                ?>
                                <a href="javascript:;"
                                   class="label label-system completedCourse<?= $item->id ?>"
                                   onclick="completeCourse(<?= $item->id ?>,'<?= date(
                                       "Y-m-d",
                                       strtotime($sub->whenCompleted)
                                   ) ?>');">Completed</a>
                                <a href="<?= SITE_URL ?>ajax?c=certificate&a=cert-pdf&id=<?= $sub->id ?>&adminKey=gre45h-56trh_434rdfng"
                                   class="label label-info" target="_blank">View
                                    Cert.</a>
                                <?php
                            } else {
                                ?>
                                <a href="javascript:;"
                                   class="label label-success completeCourse<?= $sub->id ?>"
                                   onclick="completeCourse(<?= $sub->id ?>);">Complete</a>

                                <a href="<?= SITE_URL ?>ajax?c=certificate&a=cert-pdf&id=<?= $sub->id ?>&adminKey=gre45h-56trh_434rdfng"
                                   style="display:none;"
                                   class="label label-info displayCert<?= $sub->id ?>"
                                   target="_blank">View Cert.</a>
                                <?php
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $currentModule = ORM::for_table("courseModules")
                                ->select("title")->find_one($sub->currentModule);
                            ?>
                            <?= substr($currentModule->title, 0, 12) ?>...
                        </td>
                        <td>
                            <?= date(
                                'd/m/Y @ H:i:s',
                                strtotime($sub->whenAssigned)
                            ) ?>
                        </td>
                        <td>
                            <?php
                            $value = $this->courseTimeProgress->calculateCourseTimeAccount(
                                $account->id,
                                '2022-02-01',
                                date('Y-m-d'),
                                $sub->id
                            );
                            echo number_format($value / 3600, 2)
                            ?>
                            hrs
                        </td>
                        <td>
                            <label class="label label-system" data-toggle="modal"
                                   data-target="#changeModule<?= $sub->id ?>"
                                   style="margin-left:5px;">
                                <i class="fa fa-chevron-right"></i>
                                Change Module
                            </label>
                            <label class="label label-success"
                                   onclick="resetCourse(<?= $sub->id ?>);"
                                   style="margin-left:5px;">
                                <i class="fa fa-repeat"></i>
                                Reset
                            </label>
                            <label class="label label-danger"
                                   style="margin-left:5px;"
                                   onclick="deleteEnrollment(<?= $sub->id ?>);">
                                <i class="fa fa-trash"></i>
                            </label>
                        </td>
                    </tr>

                    <div class="modal fade" id="changeModule<?= $sub->id ?>"
                         tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close"
                                            data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Change
                                        Module</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Which module do you want to move this
                                            user to?</label>
                                        <select class="form-control"
                                                name="currentModule"
                                                id="currentModule<?= $sub->id ?>">
                                            <?php
                                            foreach (
                                                ORM::for_table("courseModules")
                                                    ->where("courseID", $subCourse->id)
                                                    ->order_by_asc("ord")
                                                    ->find_many() as $module
                                            ) {
                                                ?>
                                                <option value="<?= $module->id ?>"
                                                        <?php
                                                        if ($sub->currentModule
                                                        == $module->id) { ?>selected<?php
                                                } ?>><?= str_replace(
                                                        '"',
                                                        '',
                                                        $module->title
                                                    ) ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Also mark entire course as
                                            complete?</label>
                                        <select class="form-control"
                                                name="completed"
                                                id="completed<?= $sub->id ?>">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="itemID"
                                           value="<?= $sub->id ?>"/>
                                    <input type="hidden" name="accountID"
                                           value="<?= $account->id ?>"/>
                                    <div id="returnStatusModule<?= $sub->id ?>"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default"
                                            data-dismiss="modal">Close
                                    </button>
                                    <button type="button"
                                            onclick="updateModule<?= $sub->id ?>()"
                                            class="btn btn-primary">Update
                                    </button>
                                </div>

                                <script>
                                    function updateModule<?= $sub->id ?>() {

                                        var currentModule = $("#currentModule<?= $sub->id ?>").val();
                                        var completed = $("#completed<?= $sub->id ?>").val();

                                        $.post("<?= SITE_URL ?>ajax?c=blumeNew&a=change-user-module",
                                            {
                                                itemID: '<?= $sub->id ?>',
                                                accountID: '<?= $account->id ?>',
                                                currentModule: currentModule,
                                                completed: completed
                                            },
                                            function (data, status) {

                                                $("#returnStatusModule<?= $sub->id ?>").append(data);

                                            });
                                    }
                                </script>

                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            </tbody>
            <tfoot class="footer-menu">
            <tr>
                <td colspan="7">
                    <nav class="text-right">
                        <ul class="pagination hide-if-no-paging"></ul>
                    </nav>
                </td>
            </tr>
            </tfoot>
        </table>


        <script>
            $('.datatableCourses').dataTable();
        </script>

        <?php
    }

    public function loadUserVouchers()
    {
        $account = ORM::for_table("accounts")->find_one($this->get["id"]);

        ?>
        <table class="table datatableCourses" data-filter="#fooFilter"
               data-page-navigation=".pagination" data-page-size="50">
            <thead>
            <tr>
                <th>Voucher</th>
                <th>Claimed</th>
                <th>Gifted?</th>
                <th>Course(s)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $items = ORM::for_table("vouchers")
                ->where("userID", $account->id)
                ->order_by_desc("whenClaimed")->find_many();

            foreach ($items as $item) {
                $course = ORM::for_table("courses")->find_one($item->courseID);
                ?>
                <tr id="item<?= $item->id ?>"
                    class="voucher<?= $item->id ?>">
                    <td>
                        <strong><?= $item->code ?></strong>
                    </td>
                    <td>
                        <?php
                        if ($item->whenClaimed != "") {
                            echo date('d/m/Y @ H:i:s', strtotime($item->whenClaimed));
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($item->gifted == "1") {
                            ?>
                            <label class="label label-success">Yes</label>
                            <?php
                        } else {
                            ?>
                            <label class="label label-danger">No</label>
                            <?php
                        }
                        ?>
                    </td>
                    <td width="700">
                        <?php
                        $courses = explode(",", $item->courses);

                        foreach ($courses as $course) {
                            $courseData = ORM::for_table("courses")->select("title")->find_one($course);

                            if ($courseData->title != "") {
                                echo $courseData->title . ', ';
                            }
                        }
                        ?>
                    </td>


                </tr>

                <?php
            }
            ?>
            </tbody>
            <tfoot class="footer-menu">
            <tr>
                <td colspan="7">
                    <nav class="text-right">
                        <ul class="pagination hide-if-no-paging"></ul>
                    </nav>
                </td>
            </tr>
            </tfoot>
        </table>

        <script>
            $('.datatableCourses').dataTable();
        </script>

        <?php
    }

    public function deleteUserEnrollment()
    {
        // require 2 parameters, just in case
        $item = ORM::for_table("coursesAssigned")
            ->where("accountID", $this->get["account"])
            ->find_one($this->get["id"]);

        if ($item->id != "") {
            $this->recordLog('deleted an enrolment for the following user: ' . $this->get["account"]);

            $item->delete();
        }
    }

    public function changePassword()
    {
        if (CUR_ID == "") {
            exit;
        }

        $account = ORM::for_table("blumeUsers")->find_one(CUR_ID);

        if ($this->post["password"] != $this->post["passwordConfirm"]) {
            $this->setAlertDanger("Your passwords do not match.");
            exit;
        }

        $password = password_hash($this->post["password"], PASSWORD_BCRYPT);

        $account->password = $password;

        $account->save();

        $this->recordLog('changed their admin password');

        $this->setAlertSuccess("Password successfully saved");
    }

    public function tinyMceUploader()
    {
        /*******************************************************
         * Only these origins will be allowed to upload images *
         ******************************************************/
        $accepted_origins = array("http://localhost", rtrim(SITE_URL, "/"));

        /*********************************************
         * Change this line to set the upload folder *
         *********************************************/
        $imageFolder = TO_PATH_CDN . 'media/';
        /*********************************************/

        if (!file_exists($imageFolder)) {
            mkdir($imageFolder, 0777, true);
        }
        reset($_FILES);
        $temp = current($_FILES);
        if (is_uploaded_file($temp['tmp_name'])) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                // same-origin requests won't set an origin. If the origin is set, it must be valid.
                if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
                    header(
                        'Access-Control-Allow-Origin: '
                        . $_SERVER['HTTP_ORIGIN']
                    );
                } else {
                    header("HTTP/1.1 403 Origin Denied");
                    return;
                }
            }

            /*
              If your script needs to receive cookies, set images_upload_credentials : true in
              the configuration and enable the following two headers.
            */
            // header('Access-Control-Allow-Credentials: true');
            // header('P3P: CP="There is no P3P policy."');

            // Sanitize input
            if (preg_match(
                "/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/",
                $temp['name']
            )
            ) {
                header("HTTP/1.1 400 Invalid file name.");
                return;
            }

            // Verify extension
            if (!in_array(
                strtolower(
                    pathinfo(
                        $temp['name'],
                        PATHINFO_EXTENSION
                    )
                ),
                array("gif", "jpg", "png")
            )
            ) {
                header("HTTP/1.1 400 Invalid extension.");
                return;
            }

            // Accept upload if there was no origin, or if it is an accepted origin
            $filetowrite = $imageFolder . $temp['name'];
            move_uploaded_file($temp['tmp_name'], $filetowrite);

            // Respond to the successful upload with JSON.
            // Use a location key to specify the path to the saved image resource.
            // { location : '/your/uploaded/image/file'}
            $filetowrite = str_replace(
                $imageFolder,
                SITE_URL . 'assets/cdn/media/',
                $filetowrite
            );
            echo json_encode(array('location' => $filetowrite));
        } else {
            // Notify editor that the upload failed
            header("HTTP/1.1 500 Server Error");
        }
    }

    public function editAccount()
    {
        $account = ORM::for_table("accounts")->find_one($this->post["itemID"]);

        $existingEmail = ORM::for_table("accounts")
            ->where("email", $this->post["email"])
            ->where_not_equal("id", $this->post["itemID"])
            ->count();

        if ($existingEmail != 0) {
            $this->setAlertDanger("The selected email address is already in use on another account.");
            exit;
        }

        $account->firstname = $this->post["firstname"];
        $account->lastname = $this->post["lastname"];
        $account->disableModuleTimer = $this->post["disableModuleTimer"];
        $account->twoFactor = $this->post["twoFactor"];
        $account->email = $this->post["email"];
        $account->phone = $this->post["phone"];
        $account->currencyID = $this->post["currencyID"];

        if ($this->post["password"] != "") {
            $this->validatePassword($this->post["password"]);

            $account->password = password_hash(
                $this->post["password"],
                PASSWORD_BCRYPT
            );
        }

        $account->save();

        // update certificates
        if (@$this->post["updateCertificates"]) {
            $certificates = ORM::for_table('coursesAssigned')
                ->where('accountID', $account->id)
                ->where_not_null('certFile')
                ->find_many();
            if (count($certificates) >= 1) {
                foreach ($certificates as $certificate) {
                    $certificate->certFile = null;
                    $certificate->save();
                }
            }
        }

        $this->recordLog('edited the following users account details: ' . $this->post["itemID"]);

        $this->setAlertSuccess("The account was successfully updated");
    }

    public function deleteUserAccount()
    {
        $account = ORM::For_table("accounts")->find_one($this->get["id"]);

        $this->recordLog('deleted a user account');

        // send email
        $user = ORM::for_table("blumeUsers")->find_one(CUR_ID);
        $message = '<p>Hi there</p>
        <p>An account was just deleted by ' . $user->name . ' ' . $user->surname . '.</p>
        
        <p><strong>Email of deleted account:</strong> ' . $account->email . '</p>';


        $this->sendEmail(
            "dan@be-a.co.uk",
            $message,
            "Account deleted by admin from " . SITE_NAME
        );

        // delete assigned courses
        $items = ORM::for_table("coursesAssigned")
            ->where("accountID", $account->id)
            ->delete_many();

        // delete account
        $account->delete();


        header('Location: ' . SITE_URL . 'blume/accounts?deleted=true');
    }

    public function changeUserModule()
    {
        $enrollment = ORM::for_table("coursesAssigned")
            ->find_one($this->post["itemID"]);
        $currentModule = ORM::for_table('courseModules')->find_one($this->post["currentModule"]);
        $isSubModule = @$currentModule->parentID ? true : false;

        // reset quiz if selected
        if ($this->post["resetQuiz"] == "1") {
            $quizzes = ORM::for_table("quizzes")->where("courseID", $enrollment->courseID)->find_many();

            foreach ($quizzes as $quiz) {
                $result = ORM::for_table("quizResults")->where("quizID", $quiz->id)->where(
                    "userID",
                    $enrollment->accountID
                )->delete_many();
            }
        }

        $enrollment->currentModule = @$currentModule->parentID ? $currentModule->parentID : $currentModule->id;
        $enrollment->currentSubModule = @$currentModule->parentID ? $currentModule->id : null;

        $accountID = $enrollment->accountID;

        if ($this->post["completed"] == "1") {
            $enrollment->certNo = $this->courses->generateCertNo();
            $enrollment->completed = "1";
            $enrollment->set_expr("whenCompleted", "NOW()");
            $enrollment->percComplete = "100";
        } else {
            $courseID = $enrollment->courseID;
            $module = ORM::for_table('courseModules')->find_one($enrollment->currentModule);
            $enrollment->currentModuleKey = $module->ord;

            // all previous and current parent modules
            $modules = ORM::for_table("courseModules")
                ->where("courseID", $courseID)
                ->where_null('parentID')
                ->where_lte('ord', $module->ord)
                ->order_by_asc("ord")
                ->find_many();

            foreach ($modules as $singleModule) {
                $subModules = ORM::for_table('courseModules')
                    ->where('parentID', $module->id);
                if ($singleModule->id == $module->id) {
                    $subModules = $subModules->where_lte('ord', $singleModule->ord);
                }
                $subModules = $subModules->order_by_asc('ord')->find_many();

                if (count($subModules) >= 1) {
                    foreach ($subModules as $subModule) {
                        $courseModuleProgress = ORM::for_table('courseModuleProgress')
                            ->where('courseID', $courseID)
                            ->where('accountID', $accountID)
                            ->where('moduleID', $subModule->id)
                            ->find_one();
                        if (empty($courseModuleProgress)) {
                            $courseModuleProgress = ORM::for_table('courseModuleProgress')->create();
                            $courseModuleProgress->accountID = $enrollment->accountID;
                            $courseModuleProgress->courseID = $courseID;
                            $courseModuleProgress->moduleID = $subModule->id;
                            $courseModuleProgress->whenStarted = date("Y-m-d H:i:s");
                            $courseModuleProgress->whenCompleted = date("Y-m-d H:i:s");
                        } elseif (empty($courseModuleProgress->whenCompleted)) {
                            $courseModuleProgress->whenCompleted = date("Y-m-d H:i:s");
                        }
                        $courseModuleProgress->completed = 1;
                        $courseModuleProgress->save();
                    }
                } else {
                    $courseModuleProgress = ORM::for_table('courseModuleProgress')
                        ->where('courseID', $courseID)
                        ->where('accountID', $accountID)
                        ->where('moduleID', $singleModule->id)
                        ->find_one();

                    if (empty($courseModuleProgress)) {
                        $courseModuleProgress = ORM::for_table('courseModuleProgress')->create();
                        $courseModuleProgress->accountID = $accountID;
                        $courseModuleProgress->courseID = $courseID;
                        $courseModuleProgress->moduleID = $singleModule->id;
                        $courseModuleProgress->whenStarted = date("Y-m-d H:i:s");
                        $courseModuleProgress->whenCompleted = date("Y-m-d H:i:s");
                    } elseif (empty($courseModuleProgress->whenCompleted)) {
                        $courseModuleProgress->whenCompleted = date("Y-m-d H:i:s");
                    }
                    $courseModuleProgress->completed = 1;
                    $courseModuleProgress->save();
                }
            }


            // calculate percentage progress
            $totalModules = $this->totalCourseModules($courseID);

            $key = ORM::for_table('courseModuleProgress')
                ->where('courseID', $courseID)
                ->where('accountID', $accountID)
                ->where('completed', 1)
                ->count();
            // update percentage progress
            $percentage = number_format(($key / $totalModules) * 100, 2);

            $enrollment->percComplete = number_format($percentage, 2);
        }


        $enrollment->save();

        if (@$enrollment->bundleID && $enrollment->completed == '1') { // Complete bundle if all courses completed
            $bundleID = $enrollment->bundleID;
            $totalBundleCourses = ORM::for_table('coursesAssigned')
                ->where('bundleID', $bundleID)
                ->where('accountID', $enrollment->accountID)
                ->count();

            $totalCompletedBundleCourses = ORM::for_table('coursesAssigned')
                ->where('bundleID', $bundleID)
                ->where('accountID', $enrollment->accountID)
                ->where('completed', '1')
                ->count();

            $assignBundleCourse = ORM::for_table('coursesAssigned')
                ->find_one($bundleID);

            if ($totalBundleCourses == $totalCompletedBundleCourses) {
                $assignBundleCourse->certNo = $this->courses->generateCertNo();
                $assignBundleCourse->completed = "1";
                $assignBundleCourse->set_expr("whenCompleted", "NOW()");
                $assignBundleCourse->percComplete = "100";
            } else {
                $percentage = number_format(($totalCompletedBundleCourses / $totalBundleCourses) * 100, 2);
                $assignBundleCourse->percComplete = number_format($percentage, 2);
            }
            $assignBundleCourse->save();
        }

        ?>
        <script>
            $('#changeModule<?= $this->post["itemID"] ?>').on('hidden.bs.modal', function (e) {
                // reload enrollments table
                $("#userCourses").load("<?= SITE_URL ?>ajax?c=blumeNew&a=load-user-courses&id=<?= $this->post["accountID"] ?>");
            });
            $('#changeModule<?= $this->post["itemID"] ?>').modal("toggle");
        </script>
        <?php
    }

    public function createHelpVideo()
    {
        $item = ORM::for_table("supportHelpVideos")->create();

        $item->title = $this->post["title"];
        $item->vimeo = $this->post["vimeo"];
        $item->set_expr("whenCreated", "NOW()");

        $item->save();

        $this->recordLog('created a new help video: ' . $this->post["title"]);

        $this->redirectJS(SITE_URL . 'blume/support/help-videos');
    }

    public function editHelpVideo()
    {
        $item = ORM::for_table("supportHelpVideos")
            ->find_one($this->post["itemID"]);

        $item->title = $this->post["title"];
        $item->vimeo = $this->post["vimeo"];

        $item->save();

        $this->recordLog('edited a help video: ' . $this->post["title"]);

        $this->redirectJS(SITE_URL . 'blume/support/help-videos');
    }

    public function deleteHelpVideo()
    {
        $item = ORM::for_table("supportHelpVideos")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted a help video');
    }

    public function signOut()
    {
        $this->recordLog('manually signed out of their account');

        unset($_SESSION["adminAccessed"]);
        unset($_SESSION['idx_front']);
        unset($_SESSION['idx']);
        //Added by Zubaer
        unset($_SESSION['nsa_email_front']);

        unset($_SESSION['id']);
        session_destroy();
        header('Location: ' . SITE_URL . 'blume/login');
    }

    public function editEmailTemplate()
    {
        $postData = $this->post;
        $this->emailTemplates->saveTemplate($postData);
        $this->setAlertSuccess("Email Template updated");
    }

    public function loadUserRewards()
    {
        ?>
        <table class="table footable" data-filter="#fooFilter"
               data-page-navigation=".pagination" data-page-size="50">
            <thead>
            <tr>
                <th>Reward</th>
                <th>Date/Time</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $account = ORM::for_table("accounts")->find_one($this->get["id"]);
            $items = ORM::for_table("rewardsAssigned")
                ->where("userID", $account->id)->order_by_desc("whenAssigned")
                ->find_many();

            foreach ($items as $item) {
                $reward = ORM::for_table("rewards")->find_one($item->rewardID);
                ?>
                <tr id="item<?= $item->id ?>">
                    <td>
                        <?= $reward->name ?>
                    </td>
                    <td>
                        <?= date(
                            'd/m/Y @ H:i:s',
                            strtotime($item->whenAssigned)
                        ) ?>
                    </td>
                </tr>

                <?php
            }
            ?>
            </tbody>
            <tfoot class="footer-menu">
            <tr>
                <td colspan="7">
                    <nav class="text-right">
                        <ul class="pagination hide-if-no-paging"></ul>
                    </nav>
                </td>
            </tr>
            </tfoot>
        </table>
        <?php
    }

    public function deleteMessageQueue()
    {
        if (@$this->get["id"]) {
            $item = ORM::for_table("messagesQueue")->find_one($this->get["id"]);
            $item->delete();
        }
    }

    public function activateQualificationCourse()
    {
        if (@$this->get["id"]) {
            $item = ORM::for_table("coursesAssigned")
                ->find_one($this->get["id"]);
            $item->activated = 1;
            $item->save();
        }
    }

    public function addUserTrophies()
    {
        $this->rewardsAssigned->assignReward(
            $this->post["accountID"],
            'Admin Added ' . date("Y-m-d H:i:s"),
            false,
            false,
            $this->post["no_trophies"]
        );

        $this->setToastSuccess("Trophies has been assigned");


        $this->recordLog('added a trophy/trophies to the following user: ' . $this->post["accountID"]);

        // refresh table on frontend
        ?>
        <script>
            $("#userRewards").load("<?= SITE_URL ?>ajax?c=blumeNew&a=load-user-rewards&id=<?= $this->post["accountID"] ?>");
        </script>
        <?php

        exit;
    }

    public function deleteCoupon()
    {
        $item = ORM::for_table("coupons")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted a coupon');
    }

    public function deleteReview()
    {
        $item = ORM::for_table("courseReviews")->find_one($this->get["id"]);
        $item->delete();

        $this->recordLog('deleted a review');
    }

    function getQuestionMainImage(int $questionID)
    {
        $media = $this->medias->getMedia('quizQuestionController', $questionID, 'main_image');
        return $media;
    }

    function getQuestionAudio(int $questionID)
    {
        $media = $this->medias->getMedia('quizQuestionController', $questionID, 'audio');
        return $media;
    }

    function getQuestionMp3Audio(int $questionID)
    {
        $media = $this->medias->getMedia('quizQuestionController', $questionID, 'mp3_audio');
        return $media;
    }

    function getQuestionVimeoUrl(int $questionID)
    {
        $media = $this->medias->getMedia('quizQuestionController', $questionID, 'vimeoURL');
        return $media;
    }

    public function updateOrder()
    {
        $request = $this->post;
        $item = ORM::for_table("orders")->find_one($request['orderID']);

        $item->firstname = $request["firstname"];
        $item->lastname = $request["lastname"];
        $item->email = $request["email"];
        $item->address1 = $request["address1"];
        $item->address2 = $request["address2"];
        $item->city = $request["city"];
        $item->postcode = $request["postcode"];
        $item->county = $request["county"];
        $item->country = $request["country"];
        $item->adminNotes = $request["adminNotes"];

        $item->save();

        $this->recordLog('updated the following order: ' . $request['orderID']);

        $this->setAlertSuccess("Order successfully updated");
        exit();
    }

    public function deleteBulkUsers()
    {
        $postName = "file";
        $allow = array('csv');
        $fileName = $_FILES[$postName]["name"];
        $fileTmpLoc = $_FILES[$postName]["tmp_name"];
        $fileType = $_FILES[$postName]["type"];
        $fileSize = $_FILES[$postName]["size"];
        $fileErrorMsg = $_FILES[$postName]["error"];
        $kaboom = explode(".", $fileName);
        $end = strtolower(end($kaboom));

        if ($fileSize > 104857600000) {
            echo '<div class="alert alert-danger text-center" role="alert">Your file must be under 10MB.</div>';
            exit;
        } else {
            if (!in_array($end, $allow)) {
                echo '<div class="alert alert-danger text-center" role="alert">The file you are uploading is now allowed.</div>';
                exit;
            } else {
                if ($fileErrorMsg == 1) {
                    echo '<div class="alert alert-danger text-center" role="alert">An unknown error occurred. '
                        . $fileErrorMsg . '</div>';
                    exit;
                }
            }
        }

        $cdnDir = 'adminImports';

        $date = date('Y-m-d');

        if (!file_exists(TO_PATH_CDN . $cdnDir . '/' . $date)) {
            mkdir(TO_PATH_CDN . $cdnDir . '/' . $date, 0777, true);
        }

        $fileName = $fileName . '-' . md5(time() . rand(3333, 9999)) . '.' . $end;
        $moveResult = move_uploaded_file(
            $fileTmpLoc,
            TO_PATH_CDN . $cdnDir . '/' . $date . '/' . $fileName
        );
        if ($moveResult != true) {
            echo '<div class="alert alert-danger text-center" role="alert">We could not complete the upload of your CSV.</div>';
            exit;
        }

        $rows = array_map(
            'str_getcsv',
            file(TO_PATH_CDN . $cdnDir . '/' . $date . '/' . $fileName)
        );
        $header = array_shift($rows);
        $csv = array();
        foreach ($rows as $row) {
            $csv[] = array_combine($header, $row);
        }

        $hasHeader = false;
        $count = 0;

        foreach ($csv as $itemCSV) {
            if ($itemCSV["EMAIL"] != "") {
                $hasHeader = true;

                $account = ORM::for_table("accounts")->where("email", $itemCSV["EMAIL"])->find_one();

                if ($account->email != "") {
                    // go ahead with deletion

                    // delete assigned courses
                    $items = ORM::for_table("coursesAssigned")
                        ->where("accountID", $account->id)
                        ->delete_many();

                    // delete account
                    $account->delete();

                    $count++;
                }
            }
        }

        // delete imported CSV
        unlink(TO_PATH_CDN . $cdnDir . '/' . $date . '/' . $fileName);

        $this->recordLog('bulk deleted ' . $count . ' user accounts');

        $this->setAlertSuccess($count . ' accounts were successfully deleted');
    }

    public function updateSubscription()
    {
        $account = ORM::for_table("accounts")->find_one($this->post["itemID"]);


        $account->subActive = $this->post["subActive"];
        if ($account->subActive == '1') {
            $account->isAdminSub = 1;
            if (@$this->post["subExpiryDate"]) {
                $account->subExpiryDate = $this->post["subExpiryDate"];
            } else {
                if (@$this->post["subExpiryMonths"]) {
                    $account->subExpiryDate = date(
                        "Y-m-d",
                        strtotime("+" . $this->post["subExpiryMonths"] . " months")
                    );
                }
            }
        } else {
            $account->subExpiryDate = NULL;
            $account->isAdminSub = 0;
        }

        $account->save();

        // @todo: update subscriptions table here

        $this->setAlertSuccess("Subscription details were successfully updated");
    }

    public function uploadUserCertificate()
    {
        $courseAssigned = ORM::for_table('coursesAssigned')
            ->where('accountID', $this->post["accountID"])
            ->where('courseID', $this->post["courseID"])
            ->find_one();
        if ($courseAssigned) {
            $certNo = $courseAssigned->certNo;
            if (empty($certNo)) {
                // certificate number
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < 10; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }

                $certNo = $randomString;
                $courseAssigned->certNo = $certNo;
                $courseAssigned->completed = "1";
                $courseAssigned->set_expr("whenCompleted", "NOW()");
                $courseAssigned->percComplete = "100";
            }

            // upload image
            if ($this->checkFileUploadSelected("certificate") == true) {
                $pathInfo = pathinfo($_FILES['certificate']["name"]);
                $fileName = $courseAssigned->accountID . '_' . $courseAssigned->certNo . '.' . $pathInfo['extension'];
                $uploadPath = TO_PATH_CDN . 'certificates/' . $fileName;
                $moveResult = move_uploaded_file($_FILES['certificate']['tmp_name'], $uploadPath);
                $courseAssigned->certFile = $fileName;
                $courseAssigned->save();
            }
        }
        $this->setAlertSuccess("Certificate successfully uploaded");
    }

    public function totalCourseModules($courseID)
    {
        $totalModules = 0;
        $modules = ORM::for_table('courseModules')
            ->where('courseID', $courseID)
            ->where_null('parentID')
            ->order_by_asc('ord')
            ->find_many();

        foreach ($modules as $module) {
            $subModuleCount = ORM::for_table('courseModules')
                ->where('courseID', $courseID)
                ->where('parentID', $module->id)
                ->count();
            $totalModules = $totalModules + ($subModuleCount >= 1 ? $subModuleCount : 1);
        }

        return $totalModules;
    }

    public function actionCourseReviews()
    {
        foreach ($this->post["ids"] as $reviewID) {
            $update = ORM::for_table("courseReviews")->find_one($reviewID);

            $update->status = $this->post["status"];

            $update->save();
        }

        $this->redirectJS(SITE_URL . 'blume/courses/reviews');
    }

    public function getLeaderBoard()
    {
        $accountRank = 0;

        $date = date("Y-m-");
        if ($this->get["show"] == "last") {
            $date = date("Y-m-", strtotime("-1 month"));
        }

        $leaderBoard = ORM::for_table($this->table)
            ->raw_query(
                'SELECT r.userID, SUM(r.points) as total, a.firstname, a.lastname, a.leaderboardName from rewardsAssigned r JOIN accounts a on r.userID=a.id Where r.whenAssigned like "'
                . $date
                . '%"   GROUP BY r.userID ORDER BY total DESC limit 0,10'
            )
            ->find_array();
        $winner = $date == date("Y-m-") ? 0 : 1;

        $data = [
            'leader_board' => $leaderBoard,
            'winner' => $winner,
            'accountRank' => $accountRank,
        ];

        return $data["leader_board"];
    }

    public function addCourseRelation()
    {
        $item = ORM::for_table("courseRelations")->create();

        $item->courseID = $this->post["courseID"];
        $item->courseID2 = $this->post["courseID2"];

        $item->save();

        $this->redirectJS(SITE_URL . 'blume/courses/relations');
    }

    public function deleteCourseRelation()
    {
        $item = ORM::for_table("courseRelations")->find_one($this->get["id"]);
        $item->delete();
    }

    public function exportOrdersCsv()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header(
            'Content-Disposition: attachment; filename=NSA_Orders_'
            . time() . '.csv'
        );

        $output = fopen('php://output', 'w');


        fputcsv($output, array(
            'ID',
            'CUSTOMER',
            'EMAIL',
            'DATE_TIME',
            'ITEMS',
            'METHOD',
            'TOTAL',
            'TOTAL_GBP',
            'CURRENCY',
            'UTM_SOURCE',
            'CODE',
            'GIFT',
            'SITE',
            'IP_ADDRESS',
            'AFFILIATE'
        ));

        $this->recordLog('exported orders as a CSV');

        $date = explode(" - ", $this->post["daterange"]);

        $dateFrom = date('Y-m-d', strtotime($date[0])) . ' 00:00:00';
        $dateTo = date('Y-m-d', strtotime($date[1])) . ' 00:00:00';

        $orders = ORM::for_table("orders")->where_gte("whenCreated", $dateFrom)->where_lte("whenCreated", $dateTo);

        $whereRaw = [];

        $whereRaw[] = 'status = "completed"';

        if ($this->post["currencyID"] != "") {
            $whereRaw[] = 'currencyID = "' . $this->post["currencyID"] . '"';
        }

        if ($this->post["method"] != "") {
            $whereRaw[] = 'method LIKE "%' . $this->post["method"] . '%"';
        }


        if (@$whereRaw) {
            $orders = $orders->where_raw(implode(" AND ", $whereRaw));
        }

        $orders = $orders->order_by_desc("id");

        $orders = $orders->find_many();

        foreach ($orders as $order) {
            $process = true; // used to include/exclude

            // if course selected then check this order contains an item from that course
            if ($this->post["courseID"] != "") {
                $item = ORM::for_table("orderItems")
                    ->where("orderID", $order->id)
                    ->where("courseID", $this->post["courseID"])
                    ->count();

                if ($item == 0) {
                    $process = false;
                }
            }

            if ($this->post["categoryID"] != "") {
                // get all course ID's in this category
                $courseIDs = array();

                $items = ORM::for_table("courseCategoryIDs")->where(
                    "category_id",
                    $this->post["categoryID"]
                )->find_many();

                foreach ($items as $item) {
                    array_push($courseIDs, $item->course_id);
                }

                $item = ORM::for_table("orderItems")
                    ->where("orderID", $order->id)
                    ->where_in("courseID", $courseIDs)
                    ->count();

                if ($item == 0) {
                    $process = false;
                }
            }

            if ($process == true) {
                // get order summary
                $summary = '';
                $item = ORM::for_table("orderItems")->where("orderID", $order->id)->find_one();

                if ($item->id == "") {
                    $summary = '0';
                } else {
                    $text = '';

                    if ($item->course == "1") {
                        $course = ORM::for_table("courses")->find_one($item->courseID);
                        if ($course->title == "") {
                            $course = ORM::for_table("courses")->where("oldID", $item->courseID)->find_one();
                        }
                        $text = $course->title;
                    } else {
                        if ($item->voucherID != "") {
                            // then its a gifted voucher
                            $voucher = ORM::for_table("vouchers")->find_one($item->voucherID);
                            $course = ORM::for_table("courses")->find_one($voucher->courses);
                            $text = 'Gift Voucher for ' . $course->title;
                        } else {
                            if ($item->premiumSubPlanID != "") {
                                $text = 'Subscription';
                            } else {
                                // then its a cert.
                                $cert = ORM::for_table("coursesAssigned")->find_one($item->certID);

                                $text = 'Cert: ' . $cert->certNo;
                            }
                        }
                    }

                    if (strlen($text) > 30) {
                        $text = substr($text, 0, 30) . '...';
                    }

                    $summary = '(' . ORM::for_table("orderItems")->where("orderID", $order->id)->count() . ') ' . $text;
                }

                $currency = ORM::for_table("currencies")->find_one($order->currencyID);

                if ($this->post["hideDetails"] == "1") {
                    $order->firstname = '-';
                    $order->lastname = '-';
                    $order->email = '-';
                }

                $codeValue = '';

                if ($order->couponID != "") {
                    $code = ORM::for_table("coupons")->find_one($order->couponID);
                    $codeValue = $code->code;
                }

                $affID = '';
                $affiliate = ORM::for_table("ap_earnings")->where("product", "NSA Order #: " . $order->id)->find_one();
                if ($affiliate->affiliate_id != "") {
                    $affID = $affiliate->affiliate_id;
                }

                fputcsv($output, array(
                    $order->id,
                    $order->firstname . ' ' . $order->lastname,
                    $order->email,
                    $order->whenCreated,
                    $summary,
                    $order->method,
                    number_format($order->total, 2),
                    number_format($order->totalGBP, 2),
                    $currency->code,
                    $order->utm_source,
                    $codeValue,
                    $order->gifted,
                    $order->site,
                    $order->customerIP,
                    $affID
                ));
            }
        }
    }

    public function mergeAccounts()
    {
        // admin tool used to merge two accounts

        $old = $this->post["oldID"];
        $new = $this->post["newID"];

        $oldAccount = ORM::for_table("accounts")->find_one($old);
        $newAccount = ORM::for_table("accounts")->find_one($old);

        if ($oldAccount->id == "") {
            exit;
        }

        if ($newAccount->id == "") {
            exit;
        }

        // orders
        $results = ORM::for_table('orders')->raw_query(
            'UPDATE orders SET accountID = :new WHERE accountID = :old',
            array('new' => $new, 'old' => $old)
        )->find_many();

        // assigned courses
        $results = ORM::for_table('coursesAssigned')->raw_query(
            'UPDATE coursesAssigned SET accountID = :new WHERE accountID = :old',
            array('new' => $new, 'old' => $old)
        )->find_many();

        // progress
        $results = ORM::for_table('courseModuleProgress')->raw_query(
            'UPDATE courseModuleProgress SET accountID = :new WHERE accountID = :old',
            array('new' => $new, 'old' => $old)
        )->find_many();

        // saved courses
        $results = ORM::for_table('coursesAssigned')->raw_query(
            'UPDATE coursesAssigned SET accountID = :new WHERE accountID = :old',
            array('new' => $new, 'old' => $old)
        )->find_many();

        // notes
        $results = ORM::for_table('courseNotes')->raw_query(
            'UPDATE courseNotes SET userID = :new WHERE userID = :old',
            array('new' => $new, 'old' => $old)
        )->find_many();

        // coupons
        $results = ORM::for_table('courseNotes')->raw_query(
            'UPDATE coupons SET forUser = :new WHERE forUser = :old',
            array('new' => $new, 'old' => $old)
        )->find_many();

        // vouchers
        $results = ORM::for_table('vouchers')->raw_query(
            'UPDATE vouchers SET userID = :new WHERE userID = :old',
            array('new' => $new, 'old' => $old)
        )->find_many();

        // sign ins
        $results = ORM::for_table('accountSignInLogs')->raw_query(
            'UPDATE accountSignInLogs SET accountID = :new WHERE accountID = :old',
            array('new' => $new, 'old' => $old)
        )->find_many();

        // rewards
        $results = ORM::for_table('rewardsAssigned')->raw_query(
            'UPDATE rewardsAssigned SET userID = :new WHERE userID = :old',
            array('new' => $new, 'old' => $old)
        )->find_many();

        $oldAccount->delete(); // delete old account

        $this->setAlertSuccess(
            $old . " is now merged into " . $new . ". The original account is now deleted. This action cannot be undone."
        );
    }

    public function exportQuizCsv()
    {
        $quizappear = $_GET['quizappear'];
        $quizId = $_GET['quizId'];
        $quiz = ORM::for_table('quizzes')->find_one($quizId);
        if ($quiz->id) {
            $module = ORM::for_table('courseModules')->find_one($quiz->moduleID);
            $course = ORM::for_table('courses')->find_one($module->courseID);
            $questions = $this->quizzes->getQuizQuestions($quiz->id);
        } else {
            header('Location: ' . SITE_URL . 'blume/dashboard');
            exit;
        }

        header('Content-Type: text/csv; charset=utf-8');

        if ($quizappear == "d") {
            header(
                'Content-Disposition: attachment; filename=During_Quiz_' . str_replace(" ", "_", $module->title) . time(
                ) . '.csv'
            );
        }
        if ($quizappear == "a") {
            header(
                'Content-Disposition: attachment; filename=After_Quiz_' . str_replace(" ", "_", $module->title) . time(
                ) . '.csv'
            );
        }


        $output = fopen('php://output', 'w');


        fputcsv($output, array(
            'COURSE TITLE',
            'MODULE TITLE',
            'QUIZ QUESTION',
            'QUIZ OPTIONS',
            'QUIZ ANSWERS'
        ));

        $this->recordLog('exported quiz as a CSV');


        foreach ($questions as $question) {
            $answers = $this->quizzes->getAnswersByQuestionID($question->id);
            $answerCount1 = 0;
            $answerCount = 1;
            foreach ($answers as $answer) {
                $answers = $this->quizzes->getAnswersByQuestionID($question->id);
                $answer2 = $this->quizzes->getAnswersByQuestionID($question->id, $onlyCorrect = true);

                if ($answerCount == 1) {
                    fputcsv($output, array(
                        $course->title,
                        $module->title,
                        $question->question,
                        $answer->answer,
                        $answer2[$answerCount1]->answer
                    ));
                } else {
                    fputcsv($output, array(
                        '',
                        '',
                        '',
                        $answer->answer,
                        $answer2[$answerCount1]->answer
                    ));
                }
                $answerCount++;
                $answerCount1++;
            }
            fputcsv($output, array(
                '',
                '',
                '',
                '',
                ''
            ));
        }
    }

    function upcomingRenewals($dateFrom, $planID)
    {
        $subs = ORM::for_table('subscriptions')
            ->where_any_is(array(
                array('status' => '1'),
                array('churnDate' => $dateFrom),
                array('elapsedDate' => $dateFrom),

            ),
                array(
                    'churnDate' => '>=',
                    'elapsedDate' => '>='
                )
            )
            ->where_lt('whenAdded', $dateFrom)
            ->where('premiumSubPlanID', $planID)
            ->count();

        return $subs;
    }

    function cancelledSubs($dateFrom, $dateTo, $planID)
    {
        $subs = ORM::for_table('subscriptions')
            ->where_gte('churnDate', $dateFrom)
            ->where_lt('churnDate', $dateTo)
            ->where('premiumSubPlanID', $planID)
            ->count();

        return $subs;
    }

    function elapsedMonthlySubs($dateFrom, $dateTo)
    {
        $subs = ORM::for_table('subscriptions')
            ->where_gte('elapsedDate', $dateFrom)
            ->where_lt('elapsedDate', $dateTo)
            ->where('premiumSubPlanID', 1)
            ->count();

        return $subs;
    }

    function dynamicChurnRate($dateFrom, $dateTo)
    {
        $monthlyRenewals = $this->upcomingRenewals($dateFrom, 1);
        $monthlyChurn = $this->cancelledSubs($dateFrom, $dateTo, 1);
        $monthly = ($monthlyChurn / $monthlyRenewals) * 100;

        $annualRenewals = $this->upcomingRenewals($dateFrom, 3);
        $annualChurn = $this->cancelledSubs($dateFrom, $dateTo, 3);
        $annual = ($annualChurn / $annualRenewals) * 100;

        return [
            'monthly' => number_format($monthly, 2),
            'annual' => number_format($annual, 2)
        ];
    }

    function getBoardStats()
    {
        $dateFrom = DateTimeImmutable::createFromFormat('d/m/Y', $this->get['dateFrom']);
        $dateFrom = $dateFrom->format('Y-m-d');

        $dateTo = DateTimeImmutable::createFromFormat('d/m/Y', $this->get['dateTo']);
        $dateTo = $dateTo->format('Y-m-d');

        $renewals = $this->upcomingRenewals($dateFrom, 1);
        $cancelled = $this->cancelledSubs($dateFrom, $dateTo, 1);
        $elapsed = $this->elapsedMonthlySubs($dateFrom, $dateTo);

        $churn = (($cancelled + $elapsed) / $renewals) * 100;
        $churn = number_format($churn, 2);

        $dynamicChurn = $this->dynamicChurnRate($dateFrom, $dateTo);

        $data = [
            'renewals' => $renewals,
            'cancelled' => $cancelled,
            'elapsed' => $elapsed,
            'churn' => $churn . '%',
            'monthlyDynamicChurnRate' => $dynamicChurn['monthly'] . '%',
            'annualDynamicChurnRate' => $dynamicChurn['annual'] . '%',
        ];

        echo json_encode($data);
        exit;
    }

    function CSVdata()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=reporting_Stats_' . time() . '.csv');

        $output = fopen('php://output', 'w');

        $this->recordLog('exported reporting Stats as a CSV');


        $dateFrom2 = $this->get['dateFrom2'];
        $dateTo2 = $this->get['dateTo2'];
        $renewals = $this->get['renewals'];
        $cancelled = $this->get['cancelled'];
        $elapsed = $this->get['elapsed'];
        $churn = $this->get['churn'];
        $monthlyDynamicChurnRate = $this->get['monthlyDynamicChurnRate'];
        $annualDynamicChurnRate = $this->get['annualDynamicChurnRate'];

        fputcsv(
            $output,
            array(
                'Monthly Subs Up For Renewal',
                'Cancelled Monthly Subs',
                'Elapsed Monthly Subs',
                'Churn Rate',
                'Dynamic Churn Rate (Monthly)',
                'Dynamic Churn Rate (Annual)',
                'Date From',
                'Date To'
            )
        );


        fputcsv(
            $output,
            array(
                $renewals,
                $cancelled,
                $elapsed,
                $churn,
                $monthlyDynamicChurnRate,
                $annualDynamicChurnRate,
                $dateFrom2,
                $dateTo2
            )
        );
    }

    public function reSendEmail()
    {
        $item = ORM::for_table("emailLogs")->where("id", $this->post["emailID"])->find_one();

        $this->sendEmail($item->email, $item->contents, $item->subject);
    }

}

    




