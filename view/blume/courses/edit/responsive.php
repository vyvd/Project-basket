<?php

require_once(APP_ROOT_PATH . 'services/AWSService.php');
$this->setControllers(array("courseCategory", "course", "courseModule", "blumePricing", "courseProviders"));

$course = $this->controller->getCourseEdit();

// ensure pricing exists for all currencies
$this->blumePricing->populateInitialPricing($course);

$allCourses = ORM::for_table("courses")->where_not_equal("id", $course->id)->order_by_asc("title")->find_many();
$metaTitle = "Edit Course";

$modules = $this->course->courseModules($course, true);

include BASE_PATH . 'blume.header.base.php';
?>
<link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/select2/css/core.css"
      xmlns="http://www.w3.org/1999/html">
<style>
    .select2.select2-container{
        width: 100% !important;
    }
    .subcategory{
        margin-left: 15px;
    }
</style>
<!-- -------------- Content -------------- -->
<section id="content" class="table-layout animated fadeIn">


    <!-- -------------- /Column Left -------------- -->

    <!-- -------------- Column Center -------------- -->
    <div class="chute chute-center">


        <!-- -------------- Data Filter -------------- -->
        <div class="panel" id="spy2">
            <div class="panel-heading">
                <span class="panel-title">Edit Course - <?= $course->title ?></span>
<!--                <a href="--><?php //= SITE_URL ?><!--ajax?c=blumeNew&a=copy-course&id=--><?php //= $course->id ?><!--" class="btn btn-info pull-right" style="margin-left:5px;">-->
                <a href="#" data-toggle="modal" data-target="#copyCourse" class="btn btn-success pull-right" style="margin-left:5px;">
                    <i class="fa fa-copy"></i>
                    Copy Course
                </a>
                <a href="<?= SITE_URL ?>course/<?= $course->slug ?>" target="_blank" class="btn btn-info pull-right" style="margin-left:5px;">
                    <i class="fa fa-search"></i>
                    View Course
                </a>
                <a href="#" data-toggle="modal" data-target="#addModule" class="btn btn-success pull-right" style="margin-left:5px;">
                    <i class="fa fa-plus"></i>
                    New Module
                </a>
                <a href="<?= SITE_URL ?>blume/courses/modules/reorder?id=<?= $course->id ?>" class="btn btn-system pull-right" style="margin-left:5px;">
                    <i class="fa fa-sort"></i>
                    Re-order Modules
                </a>
                <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=get-course-pdf&id=<?= $course->id ?>" class="btn btn-system pull-right" target="_blank">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            </div>

            <br />

            <div class="tab-block">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab1" data-toggle="tab">General</a>
                    </li>
                    <li>
                        <a href="#tab6" data-toggle="tab">Categories</a>
                    </li>
                    <li>
                        <a href="#tabModules" data-toggle="tab">Modules</a>
                    </li>
                    <li>
                        <a href="#tab3" data-toggle="tab">Enrollments</a>
                    </li>
                    <li>
                        <a href="#tab4" data-toggle="tab">Resources</a>
                    </li>
                    <li>
                        <a href="#tabReading" data-toggle="tab">Recommended Reading</a>
                    </li>
                    <li>
                        <a href="#tabPDF" data-toggle="tab">PDF upload</a>
                    </li>
                    <li>
                        <a href="#userReviews" data-toggle="tab">User Reviews</a>
                    </li>
                    <li>
                        <a href="#pricing" data-toggle="tab">Pricing</a>
                    </li>
                    <li>
                        <a href="#adminNotes" data-toggle="tab">
                            <i class="fa fa-edit"></i>
                            Admin Notes
                        </a>
                    </li>
                    <?php
                    if($course->isNCFE == "1") {
                        ?>
                        <li>
                            <a href="#supportingDocs" data-toggle="tab">Supporting Docs.</a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <div class="tab-content p30">
                    <div id="tab1" class="tab-pane active">
                        <div class="row mb20">
                            <div class="col-12">
                                <?php if($course->isNCFE == '1'){?>
                                    <button data-type="single" data-url="<?= SITE_URL ?>ajax?c=import&a=nsfa_courses&course_id=<?= $course->id;?>" class="importJson btn btn-dark pull-right"><i class="fa fa-download"></i> Import Course</button>
                                <?php } else{?>
                                    <button data-type="single" data-url="<?= SITE_URL ?>ajax?c=import&a=courses&course_id=<?= $course->id;?>" class="importJson btn btn-dark pull-right"><i class="fa fa-download"></i> Import Course</button>
                                <?php }?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <form name="updateGeneral" autocomplete="off">
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <img src="<?= $this->course->getCourseImage($course->id, "medium") ?>" style="width:100%;" />
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Replace Image</label>
                                                <input type="file" class="form-control" name="uploaded_file" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Replace Icon</label>
                                                <div class="flex" style="display: flex">
                                                    <input type="file" class="form-control" name="icon_file" />
                                                    <?php
                                                    $iconUrl = $this->course->getCourseImage($course->id, "full", 'icon_image');
                                                    if(@$iconUrl){
                                                        ?>
                                                        <img width="50" style="margin-left: 5px" class="ml-5" src="<?= $iconUrl ?>">
                                                        <?php
                                                    }
                                                    ?>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" class="form-control" name="title" value="<?= $course->title ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>SEO Title</label>
                                                <input type="text" class="form-control" name="seoTitle" value="<?= $course->seoTitle ?>" placeholder="This will override the title in META data..." />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>SEO Keywords <small>(Separated by comma)</small></label>
                                                <input type="text" class="form-control" name="seoKeywords" value="<?= $course->seoKeywords ?>" placeholder="This will override the Keywords in META data..." />
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            <div class="form-group">
                                                <label>SEO Description</label>
                                                <input type="text" class="form-control" name="seoDescription" value="<?= $course->seoDescription ?>" placeholder="This will override auto generated META description..." />
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            <div class="form-group">
                                                <label>Slug/URL <small>newskillsacademy.com/course/{slug}</small></label>
                                                <input type="text" class="form-control" name="slug" value="<?= $course->slug ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Default Price (GBP)</label>
                                                <input type="text" class="form-control" name="price" value="<?= $course->price ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Duration</label>
                                                <input type="text" class="form-control" name="duration" value="<?= $course->duration ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Location</label>
                                                <input type="text" class="form-control" name="location" value="<?= $course->location ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Approvals</label>
                                                <input type="text" class="form-control" name="courseApprovals" value="<?= $course->courseApprovals ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Feature on homepage?</label>
                                                <select class="form-control" name="featured">
                                                    <option value="0">No</option>
                                                    <option value="1" <?php if($course->featured == "1") { ?>selected<?php } ?>>Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Is NCFE Course?</label>
                                                <select class="form-control" name="isNCFE">
                                                    <option value="0">No</option>
                                                    <option value="1" <?php if($course->isNCFE == "1") { ?>selected<?php } ?>>Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php
                                        if($course->isNCFE == "1") {
                                            ?>
                                            <div class="col-xs-4">
                                                <div class="form-group">
                                                    <label>Monthly Price (NCFE)</label>
                                                    <input type="text" class="form-control" name="NCFEPriceMonth" value="<?= $course->NCFEPriceMonth ?>" placeholder="0.00" />
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Hidden?</label>
                                                <select class="form-control" name="hidden">
                                                    <option value="0">No</option>
                                                    <option value="1" <?php if($course->hidden == "1") { ?>selected<?php } ?>>Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Qualification</label>
                                                <input type="text" class="form-control" name="qualification" value="<?= $course->qualification ?>" />
                                            </div>
                                        </div>
                                        <?php
                                            if(in_array('Qualifications', $this->course->getCourseCategories($course->id))){
                                                $courseProviders = $this->courseProviders->getAllCourseProviders();
                                        ?>
                                            <div class="col-xs-4">
                                                <div class="form-group">
                                                    <label>Select Provider</label>
                                                    <select name="courseProviderID" class="form-control">
                                                        <option value="">Select Provider</option>
                                                        <?php
                                                            foreach ($courseProviders as $courseProvider) {
                                                        ?>
                                                            <option value="<?= $courseProvider->id ?>" <?php if($course->courseProviderID == $courseProvider->id){?>selected <?php }?>><?= $courseProvider->name ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php }?>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <label>Upsell Offer Price</label>
                                                <input type="text" class="form-control" name="upsellCoursePrice" value="<?= $course->upsellCoursePrice ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <label>Allow Secondary Name</label>
                                            <div class="form-group">
                                                <input class="form-check-input" type="radio" name="allowSecondaryName" id="allowSecondaryName" value='0' <?php if($course->allowSecondaryName == 0){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="allowSecondaryNameNo">No</label>

                                                <input class="form-check-input ml10" type="radio" name="allowSecondaryName" id="allowSecondaryName" value='1' <?php if($course->allowSecondaryName == 1){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="allowSecondaryName">Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label>Which course should be upsold with this one during customer checkout?</label>
                                                <select name="upsellCourse" class="form-control">
                                                    <option value="">Not selected...</option>
                                                    <?php
                                                    foreach($allCourses as $item) {
                                                        ?>
                                                        <option value="<?= $item->id ?>" <?php if($course->upsellCourse == $item->id) { ?>selected<?php } ?>><?= $item->title ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-md-3">
                                            <label>Course Layout</label>
                                            <div class="form-group">
                                                <input class="form-check-input" type="radio" name="layout" id="layoutDefault" value='0' <?php if($course->layout == 0){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="layoutDefault">Default</label>

                                                <input class="form-check-input ml10" type="radio" name="layout" id="layoutVideo" value='1' <?php if($course->layout == 1){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="layoutVideo">With Video</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-md-3">
                                            <label>Expire after 1 year of completion</label>
                                            <div class="form-group">
                                                <input class="form-check-input" type="radio" name="expire_after_year" id="expire_after_year_no" value='0' <?php if($course->expire_after_year == 0){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="expire_after_year_no">No</label>

                                                <input class="form-check-input ml10" type="radio" name="expire_after_year" id="expire_after_year_yes" value='1' <?php if($course->expire_after_year == 1){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="expire_after_year_yes">Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-md-3">
                                            <label>Have Tests?</label>
                                            <div class="form-group">
                                                <input class="form-check-input" type="radio" name="have_tests" id="have_tests_no" value='0' <?php if($course->have_tests == 0){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="expire_after_year_no">No</label>

                                                <input class="form-check-input ml10" type="radio" name="have_tests" id="have_tests_yes" value='1' <?php if($course->have_tests == 1){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="have_tests_yes">Yes</label>
                                            </div>
                                        </div>

                                        <div class="col-xs-4 col-md-3">
                                            <label>Show old name to the old users?</label>
                                            <div class="form-group">
                                                <input class="form-check-input" type="radio" name="show_oldname" id="show_oldname_no" value='0' <?php if($course->show_oldname == 0){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="show_oldname_no">No</label>

                                                <input class="form-check-input ml10" type="radio" name="show_oldname" id="show_oldname_yes" value='1' <?php if($course->show_oldname == 1){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="have_tests_yes">Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-8 col-md-6">
                                            <div class="form-group">
                                                <label class="form-check-label" for="is_cma" style="margin-left:10px;">CMA </label>
                                                <input type="checkbox" name="is_cma" id="is_cma" value='1' class="form-check-input" <?php if($course->is_cma == 1){ ?> checked <?php }?>>

                                                <label class="form-check-label" for="is_ctaa" style="margin-left:10px;">CTAA </label>
                                                <input type="checkbox" name="is_ctaa" id="is_ctaa" value='1' class="form-check-input" <?php if($course->is_ctaa == 1){ ?> checked <?php }?>>

                                                <label class="form-check-label" for="is_sheilds" style="margin-left:10px;">Sheilds </label>
                                                <input type="checkbox" name="is_sheilds" id="is_sheilds" value='1' class="form-check-input" <?php if($course->is_sheilds == 1){ ?> checked <?php }?>>
                                                
                                                <label class="form-check-label" for="is_rospa" style="margin-left:10px;">RoSPA </label>
                                                <input type="checkbox" name="is_rospa" id="is_rospa" value='1' class="form-check-input" <?php if($course->is_rospa == 1){ ?> checked <?php }?>>

                                                <label class="form-check-label ml20" for="is_video" style="margin-left:10px;">Video </label>
                                                <input type="checkbox" name="is_video" id="is_video" value='1' class="form-check-input" <?php if($course->is_video == 1){ ?> checked <?php }?>>

                                                <label class="form-check-label ml20" for="is_audio" style="margin-left:10px;">Audio </label>
                                                <input type="checkbox" name="is_audio" id="is_audio" value='1' class="form-check-input" <?php if($course->is_audio == 1){ ?> checked <?php }?>>

                                                <label class="form-check-label ml20" for="is_lightningSkill" style="margin-left:10px;">LightningSkill </label>
                                                <input type="checkbox" id="is_lightningSkill" name="is_lightningSkill" value='1' class="form-check-input" <?php if($course->is_lightningSkill == 1){ ?> checked <?php }?>>
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-md-3">
                                            <div class="form-group">
                                                <label>Certificate Color</label>
                                                <input type="text" class="form-control" name="certificate_color" value="<?= $course->certificate_color ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-md-3">
                                            <div class="form-group">
                                                <label>CPD Code</label>
                                                <input type="text" class="form-control" name="cpd_code" value="<?= $course->cpd_code ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-md-3">
                                            <label>Cudoo/Language Course</label>
                                            <div class="form-group">
                                                <input class="form-check-input" type="radio" name="is_cudoo" id="cudoo_no" value='0' <?php if($course->is_cudoo == "0"){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="cudoo_no">No</label>

                                                <input class="form-check-input ml10" type="radio" name="is_cudoo" id="cudoo_yes" value='1' <?php if($course->is_cudoo == "1"){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="cudoo_yes">Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-md-3">
                                            <div class="form-group">
                                                <label>Cudoo/Language Course ID's</label>
                                                <input type="text" class="form-control" name="cudooCourseIDs" placeholder="Comma separated..." value="<?= $course->cudooCourseIDs ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-md-3">
                                            <label>Sell printed version?</label>
                                            <div class="form-group">
                                                <input class="form-check-input" type="radio" name="sellPrinted" id="printed_no" value='0' <?php if($course->sellPrinted == "0"){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="prined_no">No</label>

                                                <input class="form-check-input ml10" type="radio" name="sellPrinted" id="printed_yes" value='1' <?php if($course->sellPrinted == "1"){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="printed_yes">Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-md-3">
                                            <div class="form-group">
                                                <label>Printed Price</label>
                                                <input type="text" class="form-control" name="sellPrintedPrice" placeholder="0.00" value="<?= $course->sellPrintedPrice ?>" />
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-md-3">
                                            <label>Is Bundle?</label>
                                            <div class="form-group">
                                                <input class="form-check-input" type="radio" name="bundle" id="bundle_no" value='0' <?php if(empty($course->childCourses)){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="bundle_no">No</label>

                                                <input class="form-check-input ml10" type="radio" name="bundle" id="bundle_yes" value='1' <?php if(@$course->childCourses){ ?> checked <?php }?>>
                                                <label class="form-check-label" for="bundle_yes">Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 bg-light">
                                            <div class="row">
                                                <div class="col-md-9" style="padding: 0px">
                                                    <div class="row">
                                                        <div class="col-xs-4 col-md-3">
                                                            <label>NFQ Level</label>
                                                            <input type="text" class="form-control" name="nfqLevel" placeholder="" value="<?= $course->nfqLevel ?>" />
                                                        </div>
                                                        <div class="col-xs-4 col-md-3">
                                                            <label>Avg. Completion</label>
                                                            <input type="text" class="form-control" name="avgCompletion" placeholder="" value="<?= $course->avgCompletion ?>" />
                                                        </div>
                                                        <div class="col-xs-4 col-md-3">
                                                            <label>Suitable For</label>
                                                            <input type="text" class="form-control" name="suitableFor" placeholder="" value="<?= $course->suitableFor ?>" />
                                                        </div>
                                                        <div class="col-xs-4 col-md-3">
                                                            <label>Redirect Url</label>
                                                            <input type="text" class="form-control" name="redirectUrl" placeholder="" value="<?= $course->redirectUrl ?>" />
                                                        </div>
                                                        <div class="col-xs-4 col-md-3 mt10">
                                                            <label>Hard Level</label>
                                                            <div class="form-group">
                                                                <input class="form-check-input" type="radio" name="hardLevel" id="hardLevel_1" value='1' <?php if($course->hardLevel == 1){ ?> checked <?php }?>>
                                                                <label class="form-check-label" for="hardLevel_1">1</label>
                                                                &nbsp;
                                                                <input class="form-check-input" type="radio" name="hardLevel" id="hardLevel_2" value='2' <?php if($course->hardLevel == 2){ ?> checked <?php }?>>
                                                                <label class="form-check-label" for="hardLevel_2">2</label>
                                                                &nbsp;
                                                                <input class="form-check-input" type="radio" name="hardLevel" id="hardLevel_3" value='3' <?php if($course->hardLevel == 3){ ?> checked <?php }?>>
                                                                <label class="form-check-label" for="hardLevel_3">3</label>
                                                                &nbsp;
                                                                <input class="form-check-input" type="radio" name="hardLevel" id="hardLevel_4" value='4' <?php if($course->hardLevel == 4){ ?> checked <?php }?>>
                                                                <label class="form-check-label" for="hardLevel_4">4</label>
                                                                &nbsp;
                                                                <input class="form-check-input" type="radio" name="hardLevel" id="hardLevel_5" value='5' <?php if($course->hardLevel == 5){ ?> checked <?php }?>>
                                                                <label class="form-check-label" for="hardLevel_5">5</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-4 col-md-3 mt10">
                                                            <label>Exam Type</label>
                                                            <input type="text" class="form-control" name="examType" placeholder="" value="<?= $course->examType ?>" />
                                                        </div>
                                                        <div class="col-xs-4 col-md-3 mt10">
                                                            <label>Exam Time</label>
                                                            <input type="text" class="form-control" name="examTime" placeholder="" value="<?= $course->examTime ?>" />
                                                        </div>
                                                        <div class="col-xs-4 col-md-3 mt10">
                                                            <label>Exam Title</label>
                                                            <input type="text" class="form-control" name="examTitle" placeholder="" value="<?= $course->examTitle ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Memberships</label>
                                                    <textarea class="form-control" rows="5" name="memberships"><?php echo ($course->memberships); ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-12 child-courses-section" style="display: <?= @$course->childCourses ? 'block' : 'none' ?>">
                                            <div class="form-group">
                                                <label>Select Bundle Courses</label>
                                                <select name="childCourses[]" multiple class="form-control select2">
                                                    <?php
                                                    foreach($allCourses as $item) {
                                                        ?>
                                                        <option <?php if(in_array($item->id, json_decode($course->childCourses))) {?> selected="selected" <?php }?> value="<?= $item->id ?>" <?php if($course->upsellCourse == $item->id) { ?>selected<?php } ?>><?= $item->title ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea name="description" class="tinymce"><?= $course->description ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <label>Additional Content</label>
                                                <textarea name="additionalContent" class="tinymce"><?= $course->additionalContent ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <input type="submit" class="btn btn-success" value="Update" />
                                        </div>

                                    </div>
                                    <input type="hidden" name="id" value="<?= $course->id ?>" />
                                </form>
                                <br />
                                <div id="returnGeneral"></div>
                                <script type="text/javascript">
                                    jQuery("form[name='updateGeneral']").submit(function(e) {
                                        tinyMCE.triggerSave();
                                        e.preventDefault();
                                        var formData = new FormData($(this)[0]);

                                        jQuery.ajax({
                                            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-course",
                                            type: "POST",
                                            data: formData,
                                            async: true,
                                            success: function (msg) {
                                                jQuery('#returnGeneral').html(msg);
                                            },
                                            cache: false,
                                            contentType: false,
                                            processData: false
                                        });
                                    });
                                </script>


                            </div>
                        </div>


                    </div>
                    <div id="tabModules" class="tab-pane">
                        <?php include ('includes/modules.php');?>
                    </div>
                    <div id="tab3" class="tab-pane">

                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Enrolled</th>
                                    <th>Completed</th>
                                    <th>Certificate</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Enrolled</th>
                                    <th>Completed</th>
                                    <th>Certificate</th>
                                </tr>
                                </tfoot>
                            </table>

                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $('.datatable').DataTable( {
                                        "processing": true,
                                        "serverSide": true,
                                        "ajax": {
                                            "type" : "GET",
                                            "url" : "<?= SITE_URL ?>blume/datatables/courses/enrollments?courseID=<?= $course->id ?>",
                                            "dataSrc": function ( json ) {

                                                return json.data;

                                            }
                                        },
                                        "drawCallback": function( settings ) {

                                        }
                                    } );
                                } );


                            </script>
                        </div>

                    </div>
                    <div id="tab4" class="tab-pane">

                        <?php
                        $count = 0;
                        foreach(ORM::for_table("courseModules")->where("courseID", $course->id)->order_by_asc("ord")->find_many() as $module) {

                            $worksheet = ORM::for_table("media")
                                ->where("modelType", "courseModuleController")
                                ->where("modelId", $module->id)
                                ->where("type", "worksheet")
                                ->find_one();

                            if($worksheet->id != "") {
                                $count ++;
                                ?>
                                <p>
                                    <a href="<?= $worksheet->url ?>" target="_blank" style="    color: grey;
    font-size: 17px;">
                                        <i class="fa fa-file" style="margin-right:5px;    color: #a00303;"></i>
                                        <?= $module->title ?>
                                    </a>
                                </p>
                                <?php

                            }

                        }

                        ?>

                        <form name="courseResources">
                            <textarea name="resources" class="tinymce"><?= $course->resources ?></textarea>
                            <button type="submit" class="btn btn-success">
                                Update
                            </button>
                            <div id="returnCourseResources"></div>
                            <input type="hidden" name="courseID" value="<?= $course->id ?>" />
                        </form>
                        <script>
                            jQuery("form[name='courseResources']").submit(function(e) {
                                tinyMCE.triggerSave();
                                e.preventDefault();
                                var formData = new FormData($(this)[0]);

                                jQuery.ajax({
                                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-course-resources",
                                    type: "POST",
                                    data: formData,
                                    async: true,
                                    success: function (msg) {
                                        jQuery('#returnCourseResources').html(msg);
                                    },
                                    cache: false,
                                    contentType: false,
                                    processData: false
                                });
                            });
                        </script>


                    </div>
                    <div id="tabReading" class="tab-pane">
                        <?php include('includes/blogs.php');?>
                    </div>
                    <div id="pricing" class="tab-pane">
                        <?php include('includes/pricing.php');?>
                    </div>
                    <div id="tab6" class="tab-pane">

                        <h4>Select which category, or categories, this course is part of:</h4>

                        <form name="editCategories">
                            <div class="row">
                                <?php
                                foreach($this->courseCategory->getParentCategories() as $category) {
                                        $subCategories = $this->courseCategory->getSubCategories($category->id);
                                ?>
                                    <div class="col-xs-6">
                                        <label>
                                            <input type="checkbox" name="categories[]" <?php if($this->course->checkCourseCategory($course->id, $category->id) == true) { ?>checked<?php } ?> value="<?= $category->id ?>" />
                                            <?= $category->title ?>
                                        </label>
                                        <?php
                                        if(count($subCategories) >= 1){
                                            ?>
                                            <div class="row " >
                                        <?php
                                            foreach($subCategories as $subCategory ){
                                        ?>
                                                <div class="col-xs-4">
                                                    <label class="subcategory">
                                                        <input type="checkbox" name="categories[]" <?php if($this->course->checkCourseCategory($course->id, $subCategory->id) == true) { ?>checked<?php } ?> value="<?= $subCategory->id ?>" />
                                                        <?= $subCategory->title ?>
                                                    </label>
                                                </div>
                                        <?php
                                            }
                                            ?>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                    <?php

                                }
                                ?>
                            </div>

                            <input type="hidden" name="courseID" value="<?= $course->id ?>" />

                            <input type="submit" class="btn btn-success" value="Update" />

                            <div id="returnStatusCats"></div>

                        </form>
                        <script type="text/javascript">
                            jQuery("form[name='editCategories']").submit(function(e) {
                                e.preventDefault();
                                // $('textarea[name="text"]').html($('.summernote').code());
                                var formData = new FormData($(this)[0]);

                                jQuery.ajax({
                                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=save-course-categories",
                                    type: "POST",
                                    data: formData,
                                    async: true,
                                    success: function (msg) {
                                        jQuery('#returnStatusCats').html(msg);
                                    },
                                    cache: false,
                                    contentType: false,
                                    processData: false
                                });
                            });
                        </script>


                    </div>
                    <div id="tabPDF" class="tab-pane">

                        <div class="row">
                            <div class="col-xs-12">
                                <form name="updatePDF" autocomplete="off">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label>Upload PDF</label>
                                                <input type="file" class="form-control" name="uploaded_file" accept="application/pdf"/>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <input type="submit" class="btn btn-success" value="Update" />
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" value="<?= $course->id ?>" />
                                </form>
                                <form name="deletePDF" autocomplete="off" style="margin-top: 10px">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="submit" class="btn btn-danger" value="Delete" />
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" value="<?= $course->id ?>" />
                                </form>
                                <br />
                                <div id="returnPDF"></div>
                                <script type="text/javascript">
                                    jQuery("form[name='updatePDF']").submit(function(e) {
                                        tinyMCE.triggerSave();
                                        e.preventDefault();
                                        var formData = new FormData($(this)[0]);

                                        jQuery.ajax({
                                            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-course-pdf",
                                            type: "POST",
                                            data: formData,
                                            async: true,
                                            success: function (msg) {
                                                jQuery('#returnPDF').html(msg);
                                            },
                                            cache: false,
                                            contentType: false,
                                            processData: false
                                        });
                                    });

                                    jQuery("form[name='deletePDF']").submit(function(e) {
                                        tinyMCE.triggerSave();
                                        e.preventDefault();
                                        var formData = new FormData($(this)[0]);

                                        jQuery.ajax({
                                            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=delete-course-pdf",
                                            type: "POST",
                                            data: formData,
                                            async: true,
                                            success: function (msg) {
                                                jQuery('#returnPDF').html(msg);
                                            },
                                            cache: false,
                                            contentType: false,
                                            processData: false
                                        });
                                    });
                                </script>


                            </div>
                        </div>

                    </div>
                    <div id="userReviews" class="tab-pane">

                        <div class="row">
                            <div class="col-xs-12">
                                <form name="updateReviews" autocomplete="off">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label>User Reviews</label>
                                                <textarea name="reviews" class="tinymce"><?= $course->reviews ?></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <input type="submit" class="btn btn-success" value="Update" />
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" value="<?= $course->id ?>" />
                                </form>
                                <br />
                                <div id="returnReview"></div>
                                <script type="text/javascript">
                                    jQuery("form[name='updateReviews']").submit(function(e) {
                                        tinyMCE.triggerSave();
                                        e.preventDefault();
                                        var formData = new FormData($(this)[0]);

                                        jQuery.ajax({
                                            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=editCourseReviewSingle",
                                            type: "POST",
                                            data: formData,
                                            async: true,
                                            success: function (msg) {
                                                jQuery('#returnReview').html(msg);
                                            },
                                            cache: false,
                                            contentType: false,
                                            processData: false
                                        });
                                    });
                                </script>


                            </div>
                        </div>

                    </div>
                    <div id="adminNotes" class="tab-pane">

                        <div class="row">
                            <div class="col-xs-12">
                                <form name="updateAdminNotes" autocomplete="off">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label>Admin Notes <small>(these notes are not public and are only visible to admins)</small></label>
                                                <textarea name="adminNotes" class="tinymce"><?= $course->adminNotes ?></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <input type="submit" class="btn btn-success" value="Update" />
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" value="<?= $course->id ?>" />
                                </form>
                                <br />
                                <div id="returnAdminNotes"></div>
                                <script type="text/javascript">
                                    jQuery("form[name='updateAdminNotes']").submit(function(e) {
                                        tinyMCE.triggerSave();
                                        e.preventDefault();
                                        var formData = new FormData($(this)[0]);

                                        jQuery.ajax({
                                            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-course-admin-notes",
                                            type: "POST",
                                            data: formData,
                                            async: true,
                                            success: function (msg) {
                                                jQuery('#returnAdminNotes').html(msg);
                                            },
                                            cache: false,
                                            contentType: false,
                                            processData: false
                                        });
                                    });
                                </script>


                            </div>
                        </div>

                    </div>
                    <?php
                    if($course->isNCFE == "1") {
                        ?>
                        <div id="supportingDocs" class="tab-pane">
                            <?php
                            include __DIR__ . '/includes/supporting-docs.php';
                            ?>
                        </div>
                        <?php
                    }
                    ?>

                </div>
            </div>
        </div>


        <!-- -------------- DEMO Break -------------- -->
        <div class="mv40"></div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>
<div id="deleteProduct"></div>
<script type="text/javascript">
    // $('document').ready(function (){
    //     alert('dd');
    // });
    function deleteItem(id) {
        if (window.confirm("Are you sure you want to delete this item?")) {
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-course&id="+id);
            $("#item"+id).fadeOut();
        }
    }
    function deleteModule(id) {
        if (window.confirm("Are you sure you want to delete this module?")) {
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNew&a=delete-course-module&id="+id);
            $("#module"+id).fadeOut();
        }
    }
</script>
<div class="modal fade" id="addModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Course Module</h4>
            </div>
            <form name="addNewItem">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label>Order</label>
                        <input type="number" name="ord" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label>Estimated Time <small>(in-minutes)</small></label>
                        <input type="number" name="estTime" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label>Layouts</label>
                        <select class="form-control" name="new_style_with_video">
                            <option value="0">Default</option>
                            <option value="1">New Style With Video</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Parent Module</label>
                        <select class="form-control" name="parentID">
                            <option value="">Select parent module</option>
                            <?php
                                if(count($modules) >= 1) {
                                    foreach ($modules as $module) {
                            ?>
                                        <option value="<?= $module->id ?>"><?= $module->title ?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Content Type</label>
                        <select name="contentType" class="form-control content_type">
                            <option value="">Select Content Type</option>
                            <option value="video">Video</option>
                            <option value="quiz">Test</option>
                            <option value="upload">Upload</option>
                            <option value="text">Rich text</option>
                            <option value="assessment">Assessment</option>
                            <option value="assignment">Assignment</option>
                        </select>
                    </div>

                    <input type="hidden" name="courseID" value="<?= $course->id ?>" />
                    <p><em>Slug/URL is generated automatically.</em></p>
                    <div id="returnStatusAddNew"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src='<?= SITE_URL ?>assets/blume/js/plugins/select2/select2.min.js'></script>
<script type="text/javascript">

    $('.select2').select2();

    $('input[type=radio][name=bundle]').change(function() {
        if (this.value == 1) {
            $(".child-courses-section").css('display','block');
        }else{
            $(".child-courses-section").css('display','none');
        }
    });

    jQuery("form[name='addNewItem']").submit(function(e) {
        e.preventDefault();
        // $('textarea[name="text"]').html($('.summernote').code());
        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=create-course-module",
            type: "POST",
            data: formData,
            async: true,
            success: function (msg) {
                jQuery('#returnStatusAddNew').html(msg);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
</script>
<?php include_once(__DIR__ . '/includes/js/copy-course-js.php'); ?>
<script type="text/javascript">
    copyCourseFormHandler('<?= $course->id ?>');
</script>
<script src='<?= SITE_URL ?>assets/js/tinymce/tinymce.min.js'></script>
<script>
        var wWidth = $(window).width();
        var wHeight = $(window).height();
    tinymce.init({
        selector: '.tinymce',
        plugins: 'table link lists hr textcolor emoticons image imagetools media link preview visualchars visualblocks wordcount template code',
        toolbar: 'undo redo paste | styleselect template | bold italic strikethrough underline | link image media | bullist numlist | aligncenter alignleft alignright alignjustify alignnone | blockquote | backcolor forecolor | removeformat visualblocks code',
        height: '800',
        code_dialog_height: wHeight * 0.7,
        code_dialog_width: wWidth * 0.7,
        templates: [
            {
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
        content_css : "https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css,<?= SITE_URL ?>assets/css/global.css,<?= SITE_URL ?>assets/css/editor.css",
        relative_urls : false,
        remove_script_host : false,
        convert_urls : true,
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
        file_picker_callback: function(cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            // Note: In modern browsers input[type="file"] is functional without
            // even adding it to the DOM, but that might not be the case in some older
            // or quirky browsers like IE, so you might want to add it to the DOM
            // just in case, and visually hide it. And do not forget do remove it
            // once you do not need it anymore.

            input.onchange = function() {
                var file = this.files[0];

                var reader = new FileReader();
                reader.onload = function () {
                    // Note: Now we need to register the blob in TinyMCEs image blob
                    // registry. In the next release this part hopefully won't be
                    // necessary, as we are looking to handle it internally.
                    var id = 'imageID' + (new Date()).getTime();
                    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);

                    // call the callback and populate the Title field with the file name
                    cb(blobInfo.blobUri(), { title: file.name });
                };
                reader.readAsDataURL(file);
            };

            input.click();
        }
    });

    // Active Tabs
    $(document).ready(function(){
        var tab = 'tab1';
        <?php
            if(@$_GET['tab'] && $_GET['tab'] == 'modules'){
        ?>
                tab = 'tabModules'
        <?php
            }
        ?>


        activaTab(tab);
    });

    function activaTab(tab){
        $('.nav-tabs a[href="#' + tab + '"]').tab('show');
    }
</script>
<?php
if($this->get["moduleAdded"] == "true") {
    $this->setToastSuccess("Module successfully created");
}
if($this->get["quizAdded"] == "true") {
    $this->setToastSuccess("Quiz successfully created");
}
?>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
