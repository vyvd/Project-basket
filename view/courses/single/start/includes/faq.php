<div class="row">
    <h3>Frequently Asked Questions</h3>
    <div id="accordion" class="col-12">
        <div class="card grey-bg-box">
            <div class="card-header" id="module1">
                <h5 class="mb-0">
                    <a class="faq-title" data-toggle="collapse" data-target="#module1Data" aria-expanded="true" aria-controls="module1Data">
                        Who can take the <?= $course->title ?> course?
                    </a>
                </h5>
            </div>

            <div id="module1Data" class="collapse show" aria-labelledby="module1" data-parent="#accordion">
                <div class="card-body">
                    <p>Anyone who has an interest in learning more about this subject matter is encouraged to take the course. There are no entry requirements to take the course.</p>
                </div>
            </div>
        </div>
        <div class="card grey-bg-box">
            <div class="card-header" id="module2">
                <h5 class="mb-0">
                    <a class="faq-title collapsed" data-toggle="collapse" data-target="#module2Data" aria-expanded="false" aria-controls="module2Data">
                        What is the structure of the course?
                    </a>
                </h5>
            </div>
            <div id="module2Data" class="collapse" aria-labelledby="module2" data-parent="#accordion">
                <div class="card-body">
                    <p>The course is broken down into <?= count($this->controller->courseModules($course)) ?> individual modules. Each module takes between 20 and 90 minutes on average to study. Although you are free to spend as much or as little time as you feel necessary on each module, simply log in and out of the course at your convenience.</p>
                </div>
            </div>
        </div>
        <div class="card grey-bg-box">
            <div class="card-header" id="module3">
                <h5 class="mb-0">
                    <a class="faq-title collapsed" data-toggle="collapse" data-target="#module3Data" aria-expanded="false" aria-controls="module3Data">
                        Where / when can I study the course?
                    </a>
                </h5>
            </div>
            <div id="module3Data" class="collapse" aria-labelledby="module3" data-parent="#accordion">
                <div class="card-body">
                    <p> You can study the course any time you like. Simply log in and out of the web based course as often as you require. The course is compatible with all computers, tablet devices and smart phones so you can even study while on the move!</p>
                </div>
            </div>
        </div>
        <div class="card grey-bg-box">
            <div class="card-header" id="module4">
                <h5 class="mb-0">
                    <a class="faq-title collapsed" data-toggle="collapse" data-target="#module4Data" aria-expanded="false" aria-controls="module4Data">
                        Is there a test at the end of the course?
                    </a>
                </h5>
            </div>
            <div id="module4Data" class="collapse" aria-labelledby="module4" data-parent="#accordion">
                <div class="card-body">
                    <p> Once you have completed all <?= count($this->controller->courseModules($course)) ?> modules there is a multiple choice test. The questions will be on a range of topics found within the <?= count($this->controller->courseModules($course)) ?> modules. The test, like the course, is online and can be taken a time and location of your choosing.</p>
                </div>
            </div>
        </div>
        <div class="card grey-bg-box">
            <div class="card-header" id="module5">
                <h5 class="mb-0">
                    <a class="faq-title collapsed" data-toggle="collapse" data-target="#module5Data" aria-expanded="false" aria-controls="module5Data">
                        What is the pass mark for the test?
                    </a>
                </h5>
            </div>
            <div id="module5Data" class="collapse" aria-labelledby="module5" data-parent="#accordion">
                <div class="card-body">
                    <p> The pass mark for the test is 70%.</p>
                </div>
            </div>
        </div>
        <div class="card grey-bg-box">
            <div class="card-header" id="module6">
                <h5 class="mb-0">
                    <a class="faq-title collapsed" data-toggle="collapse" data-target="#module6Data" aria-expanded="false" aria-controls="module6Data">
                        What happens if I fail the test?
                    </a>
                </h5>
            </div>
            <div id="module6Data" class="collapse" aria-labelledby="module6" data-parent="#accordion">
                <div class="card-body">
                    <p>If you don’t pass the test first time you will get further opportunities to take the test again after extra study. There are no limits to the number of times you can take the test. All test retakes are included within the price of the course.</p>
                </div>
            </div>
        </div>
        <div class="card grey-bg-box">
            <div class="card-header" id="module7">
                <h5 class="mb-0">
                    <a class="faq-title collapsed" data-toggle="collapse" data-target="#module7Data" aria-expanded="false" aria-controls="module7Data">
                        When will I receive my certificate?
                    </a>
                </h5>
            </div>
            <div id="module7Data" class="collapse" aria-labelledby="module7" data-parent="#accordion">
                <div class="card-body">
                    <p>Once you have completed your test you can log in to your account and download/print your certificate any time you need it. If you would prefer us to post you a certificate to a UK address, there will be an admin charge of £10 (certificates sent internationally may cost more).</p>
                </div>
            </div>
        </div>
        <div class="card grey-bg-box">
            <div class="card-header" id="module8">
                <h5 class="mb-0">
                    <a class="faq-title collapsed" data-toggle="collapse" data-target="#module8Data" aria-expanded="false" aria-controls="module8Data">
                        How can I pay?
                    </a>
                </h5>
            </div>
            <div id="module8Data" class="collapse" aria-labelledby="module8" data-parent="#accordion">
                <div class="card-body">
                    <p>You can either use your Visa, MasterCard, American Express, Solo cards or PayPal account to pay for the online course. Our site uses the latest SSL encryption to ensure your safety. All payments are handled securely by PayPal.</p>
                </div>
            </div>
        </div>
        <div class="card grey-bg-box">
            <div class="card-header" id="module9">
                <h5 class="mb-0">
                    <a class="faq-title collapsed" data-toggle="collapse" data-target="#module9Data" aria-expanded="false" aria-controls="module9Data">
                        How long after payment can I begin the course?
                    </a>
                </h5>
            </div>
            <div id="module9Data" class="collapse" aria-labelledby="module9" data-parent="#accordion">
                <div class="card-body">
                    <p>You can begin the course immediately after your payment has been received. You will create your login details during the checkout process. We will also send you an email confirming your login details.</p>
                </div>
            </div>
        </div>
        <div class="card grey-bg-box">
            <div class="card-header" id="module10">
                <h5 class="mb-0">
                    <a class="faq-title collapsed" data-toggle="collapse" data-target="#module10Data" aria-expanded="false" aria-controls="module10Data">
                        How long does it take to complete the <?= $course->title ?> course?
                    </a>
                </h5>
            </div>
            <div id="module10Data" class="collapse" aria-labelledby="module10" data-parent="#accordion">
                <div class="card-body">
                    <p>We estimate that the course will take about <?= $course->duration ?> hours to complete in total, plus an additional 30 minutes for the end of course test.</p>
                </div>
            </div>
        </div>
        <div class="card grey-bg-box">
            <div class="card-header" id="module11">
                <h5 class="mb-0">
                    <a class="faq-title collapsed" data-toggle="collapse" data-target="#module11Data" aria-expanded="false" aria-controls="module11Data">
                        How long is my certificate valid for?
                    </a>
                </h5>
            </div>
            <div id="module11Data" class="collapse" aria-labelledby="module11" data-parent="#accordion">
                <div class="card-body">
                    <p>Once you have been awarded your certificate it is valid for life. The certificate does not expire or need renewing.</p>
                </div>
            </div>
        </div>
    </div>
</div>