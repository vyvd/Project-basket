<style>
    .module {
        background: #f5f5f5;
        padding: 20px;
        margin-bottom: 15px;
        position: relative;
        border-radius: 15px;
    }

    .deleteModule {
        position: absolute;
        right: 6px;
        top: -11px;
        background: #ff4343;
        padding: 8px 10px;
        color: #fff;
        border-radius: 50%;
        cursor: pointer;
    }
</style>
<div class="row mb20">
    <div class="col-12">
        <!--        <button data-type="single" data-url="--><!--ajax?c=import&a=course_modules&course_id=-->
        <!--" class="importJson btn btn-dark pull-right"><i class="fa fa-download"></i> Import Modules</button>-->
        <table width="100%" class="table moduleDatatable">
            <thead>
            <tr>
                <th width="10%">Order</th>
                <th width="50%">Module Name</th>
                <th width="20%">Estimated Time</th>
                <th width="20%">Actions</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>Order</th>
                <th>Module Name</th>
                <th>Estimated Time</th>
                <th>Actions</th>
            </tr>
            </tfoot>
        </table>

        <script type="text/javascript">
            $(document).ready(function () {
                $('.moduleDatatable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "pageLength": 25,
                    "ajax": {
                        "type": "GET",
                        "url": "<?= SITE_URL?>blume/datatables/courses/modules?courseID=<?= $course->id?>",
                        "dataSrc": function (json) {
                            return json.data;
                        }
                    },
                    "drawCallback": function (settings) {

                    }
                });

            });

            function deleteModuleItem(x) {
                var result = confirm("Are you sure you want to delete this? This action can not be undone");

                if (result == true) {

                    jQuery.ajax({
                        url: "<?= SITE_URL ?>ajax?c=blumeNew&a=deleteCourseModule&id=" + x,
                        type: "GET",
                        data: '',
                        async: true,
                        success: function (msg) {
                            location.reload();
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                }
            }

        </script>

        <div class="modal fade" id="copyCourseModuleModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Copy Course Module Results</h4>
                    </div>
                        <div class="copyCourseModuleResults" style="display: none">
                            <h5>Results</h5>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript">


            function showCopyCourseToast(msg, status) {
                toastr.options.positionClass = "toast-bottom-left";
                toastr.options.closeDuration = 1000;
                toastr.options.timeOut = 5000;
                if (status === 'success') {
                    toastr.success(msg, '')
                    return ''
                }
                toastr.error(msg || 'Error', 'Oops')
            }

            function parseCopyCourseResult(msg) {
                var msgToObj = JSON.parse(msg)
                if (!typeof msgToObj === 'object') {
                    return false;
                }
                return msgToObj;
            }

            jQuery(document).on('click', ".copyCourseModule", function (e) {
                e.preventDefault();
                let courseModuleId = jQuery(this).attr('data-module-id');
                let courseId = jQuery(this).attr('data-course-id');

                var formData = new FormData();
                formData.set('copyDest', 'current-website');
                formData.set('courseModuleID', courseModuleId);
                formData.set('courseID', courseId);
                jQuery.ajax({
                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=copy-course-module",
                    type: "POST",
                    data: formData,
                    async: true,
                    success: function (msg) {
                        let errorMsg = 'There was an error copying the course module';
                        let successMsg = 'Copying course module';
                        let parseResult = parseCopyCourseResult(msg);
                        if (!parseResult || !parseResult.hasOwnProperty('success')) {
                            showCopyCourseToast(errorMsg, 'error');
                            return;
                        }
                        if (!parseResult.success) {
                            if (parseResult.hasOwnProperty('errors')) {
                                if (Array.isArray(parseResult.errors) && parseResult.errors.length) {
                                    console.error(parseResult.errors);
                                }
                            }
                            showCopyCourseToast(errorMsg, 'error');
                            return;
                        }
                        if (parseResult.hasOwnProperty('redirect') && parseResult.redirect !== '') {
                            window.location = parseResult.redirect;
                            return;
                        }
                        let resultsModal =  jQuery('#copyCourseModuleModal').modal();
                        let resultsBox = jQuery('#copyCourseModuleModal').find('.copyCourseModuleResults')
                        if (parseResult.hasOwnProperty('results')) {
                            if (Array.isArray(parseResult.results) && parseResult.results.length) {
                                var resultsList = buildCopyCourseResultList(parseResult.results)
                                jQuery(resultsModal).modal('show')
                                jQuery(resultsBox).append(resultsList).fadeIn();
                            }
                        }

                        showCopyCourseToast(successMsg, 'success');
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            });
        </script>
    </div>
</div>