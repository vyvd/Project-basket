<?php  $userCourses = $this->course->userCourses(1000, $account->id);?>
<div class="row">
    <div class="col-xs-12 text-right">
        <a href="javascript:;" data-toggle="modal"
           data-target="#uploadCertificate" class="btn btn-info pull-right"
           style="margin-right:5px;">
            <i class="fa fa-plus"></i> Add Certificate
        </a>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
            <table class="table footable" data-filter="#fooFilter"
                   data-page-navigation=".pagination" data-page-size="50">
                <thead>
                <tr>
                    <th>Course</th>
                    <th>Certificate No.</th>
                    <th>Completed On</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php

                foreach ($userCourses as $item) {
                    if($item->certFile){
                        $course = $this->course->getCourseByID($item->courseID);
                ?>
                        <tr id="course<?= $item->id ?>">
                            <td><?= $course->title ?></td>
                            <td><?= $item->certNo ?></td>
                            <td><?= $item->whenCompleted ?></td>
                            <td>
                                <a href="<?= SITE_URL.'assets/cdn/certificates/'.$item->certFile; ?>" class="label label-system" target="_blank">
                                    View Certificate
                                </a>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>
                </tbody>
                <tfoot class="footer-menu">
                    <tr>
                        <th>Course</th>
                        <th>Certificate No.</th>
                        <th>Completed On</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="uploadCertificate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Upload Certificate</h4>
            </div>
            <form name="uploadCertificate">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Which course do you want to upload a certificate?</label>
                        <select class="form-control" name="courseID" required>
                            <option value="">Select Course</option>
                            <?php
                                foreach($userCourses as $userCourse) {
                                    $course = $this->course->getCourseByID($userCourse->courseID);
                            ?>
                                    <option value="<?= $userCourse->courseID ?>"><?= $course->title ?></option>
                            <?php
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="file" name="certificate" required />
                    </div>
                    <input type="hidden" name="accountID" value="<?= $account->id ?>" />

                    <div id="returnStatusCertificate"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
            <?php
            $this->renderFormAjax("blumeNew", "upload-user-certificate", "uploadCertificate", '#returnStatusCertificate', true, true);
            ?>
        </div>
    </div>
</div>