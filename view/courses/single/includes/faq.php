<?php
$id = rand(1,99999);
?>
<div id="accordion<?= $id ?>">
    <div class="card grey-bg-box">
        <div class="card-header" id="module1<?= $id ?>">
            <h5 class="mb-0">
                <a class="faq-title" data-toggle="collapse" data-target="#module1Data<?= $id ?>" aria-expanded="true" aria-controls="module1Data<?= $id ?>">
                    <span>Who can take the <?= $course->title ?> course?</span>
                </a>
            </h5>
        </div>

        <div id="module1Data<?= $id ?>" class="collapse show" aria-labelledby="module1<?= $id ?>" data-parent="#accordion<?= $id ?>">
            <div class="card-body">
                <p>Anyone who has an interest in learning more about this subject matter is encouraged to take the course. There are no entry requirements to take the course.</p>
            </div>
        </div>
    </div>
    <div class="card grey-bg-box">
        <div class="card-header" id="module2<?= $id ?>">
            <h5 class="mb-0">
                <a class="faq-title collapsed" data-toggle="collapse" data-target="#module2Data<?= $id ?>" aria-expanded="false" aria-controls="module2Data<?= $id ?>">
                    <span>What is the structure of the course?</span>
                </a>
            </h5>
        </div>
        <div id="module2Data<?= $id ?>" class="collapse" aria-labelledby="module2<?= $id ?>" data-parent="#accordion<?= $id ?>">
            <div class="card-body">
                <p>The course is broken down into <?= count($this->controller->courseModules($course)) ?> individual modules. Each module takes between 20 and 90 minutes on average to study. Although you are free to spend as much or as little time as you feel necessary on each module, simply log in and out of the course at your convenience.</p>
            </div>
        </div>
    </div>
    <div class="card grey-bg-box">
        <div class="card-header" id="module3<?= $id ?>">
            <h5 class="mb-0">
                <a class="faq-title collapsed" data-toggle="collapse" data-target="#module3Data<?= $id ?>" aria-expanded="false" aria-controls="module3Data<?= $id ?>">
                    <span>Where / when can I study the course?</span>
                </a>
            </h5>
        </div>
        <div id="module3Data<?= $id ?>" class="collapse" aria-labelledby="module3<?= $id ?>" data-parent="#accordion<?= $id ?>">
            <div class="card-body">
                <p> You can study the course any time you like. Simply log in and out of the web based course as often as you require. The course is compatible with all computers, tablet devices and smart phones so you can even study while on the move!</p>
            </div>
        </div>
    </div>
    <div class="card grey-bg-box">
        <div class="card-header" id="module4<?= $id ?>">
            <h5 class="mb-0">
                <a class="faq-title collapsed" data-toggle="collapse" data-target="#module4Data<?= $id ?>" aria-expanded="false" aria-controls="module4Data<?= $id ?>">
                    <span> Is there a test at the end of the course?</span>
                </a>
            </h5>
        </div>
        <div id="module4Data<?= $id ?>" class="collapse" aria-labelledby="module4<?= $id ?>" data-parent="#accordion<?= $id ?>">
            <div class="card-body">
                <p> Once you have completed all <?= count($this->controller->courseModules($course)) ?> modules there is a multiple choice test. The questions will be on a range of topics found within the <?= count($this->controller->courseModules($course)) ?> modules. The test, like the course, is online and can be taken a time and location of your choosing.</p>
            </div>
        </div>
    </div>
    <div class="card grey-bg-box">
        <div class="card-header" id="module5<?= $id ?>">
            <h5 class="mb-0">
                <a class="faq-title collapsed" data-toggle="collapse" data-target="#module5Data<?= $id ?>" aria-expanded="false" aria-controls="module5Data<?= $id ?>">
                    <span>What is the pass mark for the test?</span>
                </a>
            </h5>
        </div>
        <div id="module5Data<?= $id ?>" class="collapse" aria-labelledby="module5<?= $id ?>" data-parent="#accordion<?= $id ?>">
            <div class="card-body">
                <p> The pass mark for the test is 70%.</p>
            </div>
        </div>
    </div>
    <div class="card grey-bg-box">
        <div class="card-header" id="module6<?= $id ?>">
            <h5 class="mb-0">
                <a class="faq-title collapsed" data-toggle="collapse" data-target="#module6Data<?= $id ?>" aria-expanded="false" aria-controls="module6Data<?= $id ?>">
                    <span>What happens if I fail the test?</span>
                </a>
            </h5>
        </div>
        <div id="module6Data<?= $id ?>" class="collapse" aria-labelledby="module6<?= $id ?>" data-parent="#accordion<?= $id ?>">
            <div class="card-body">
                <p>If you don’t pass the test first time you will get further opportunities to take the test again after extra study. There are no limits to the number of times you can take the test. All test retakes are included within the price of the course.</p>
            </div>
        </div>
    </div>
    <div class="card grey-bg-box">
        <div class="card-header" id="module7<?= $id ?>">
            <h5 class="mb-0">
                <a class="faq-title collapsed" data-toggle="collapse" data-target="#module7Data<?= $id ?>" aria-expanded="false" aria-controls="module7Data<?= $id ?>">
                    <span>When will I receive my certificate?</span>
                </a>
            </h5>
        </div>
        <div id="module7Data<?= $id ?>" class="collapse" aria-labelledby="module7<?= $id ?>" data-parent="#accordion<?= $id ?>">
            <div class="card-body">
                <p>Once you have completed your test you can log in to your account and download/print your certificate any time you need it. If you would prefer us to post you a certificate to a UK address, there will be an admin charge of £10 (certificates sent internationally may cost more).</p>
            </div>
        </div>
    </div>
    <div class="card grey-bg-box">
        <div class="card-header" id="module8">
            <h5 class="mb-0">
                <a class="faq-title collapsed" data-toggle="collapse" data-target="#module8Data<?= $id ?>" aria-expanded="false" aria-controls="module8Data<?= $id ?>">
                    <span>How can I pay?</span>
                </a>
            </h5>
        </div>
        <div id="module8Data<?= $id ?>" class="collapse" aria-labelledby="module8<?= $id ?>" data-parent="#accordion<?= $id ?>">
            <div class="card-body">
                <p>You can either use your Visa, MasterCard, American Express, Solo cards or PayPal account to pay for the online course. Our site uses the latest SSL encryption to ensure your safety. All payments are handled securely by PayPal.</p>
            </div>
        </div>
    </div>
    <div class="card grey-bg-box">
        <div class="card-header" id="module9<?= $id ?>">
            <h5 class="mb-0">
                <a class="faq-title collapsed" data-toggle="collapse" data-target="#module9Data<?= $id ?>" aria-expanded="false" aria-controls="module9Data<?= $id ?>">
                    <span>How long after payment can I begin the course?</span>
                </a>
            </h5>
        </div>
        <div id="module9Data<?= $id ?>" class="collapse" aria-labelledby="module9<?= $id ?>" data-parent="#accordion<?= $id ?>">
            <div class="card-body">
                <p>You can begin the course immediately after your payment has been received. You will create your login details during the checkout process. We will also send you an email confirming your login details.</p>
            </div>
        </div>
    </div>
    <div class="card grey-bg-box">
        <div class="card-header" id="module10<?= $id ?>">
            <h5 class="mb-0">
                <a class="faq-title collapsed" data-toggle="collapse" data-target="#module10Data<?= $id ?>" aria-expanded="false" aria-controls="module10Data<?= $id ?>">
                    <span>How long does it take to complete the <?= $course->title ?> course?</span>
                </a>
            </h5>
        </div>
        <div id="module10Data<?= $id ?>" class="collapse" aria-labelledby="module10<?= $id ?>" data-parent="#accordion<?= $id ?>">
            <div class="card-body">
                <p>We estimate that the course will take about <?= $course->duration ?> hours to complete in total, plus an additional 30 minutes for the end of course test.</p>
            </div>
        </div>
    </div>
    <div class="card grey-bg-box">
        <div class="card-header" id="module11<?= $id ?>">
            <h5 class="mb-0">
                <a class="faq-title collapsed" data-toggle="collapse" data-target="#module11Data<?= $id ?>" aria-expanded="false" aria-controls="module11Data<?= $id ?>">
                    <span>How long is my certificate valid for?</span>
                </a>
            </h5>
        </div>
        <div id="module11Data<?= $id ?>" class="collapse" aria-labelledby="module11<?= $id ?>" data-parent="#accordion<?= $id ?>">
            <div class="card-body">
                <p>Once you have been awarded your certificate it is valid for life. The certificate does not expire or need renewing.</p>
            </div>
        </div>
    </div>
</div>