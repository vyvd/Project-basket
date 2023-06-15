<div class="module-infos d-flex align-items-center moduleShare" <?php if($module->worksheet_title != "") { ?>style="margin-top:30px;"<?php } ?>>
    <div class="texts col-12 col-md-12">
        <h5>Share your progress with your friends and family</h5>

        <a href="http://twitter.com/share?text=<?= $socialText ?>" target="_blank">
                        <span class="fa-stack fa-2x">
                          <i class="fas fa-circle fa-stack-2x" style="color:#1C9CEA;"></i>
                          <i class="fab fa-twitter fa-stack-1x fa-inverse"></i>
                        </span>
        </a>

        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= SITE_URL ?>course/<?= $course->slug ?>" target="_blank">
                        <span class="fa-stack fa-2x">
                          <i class="fas fa-circle fa-stack-2x" style="color:#3A5794;"></i>
                          <i class="fab fa-facebook-f fa-stack-1x fa-inverse"></i>
                        </span>
        </a>

        <a href="https://api.whatsapp.com/send/?phone&text=<?= urlencode($socialText) ?>" target="_blank">
                        <span class="fa-stack fa-2x">
                          <i class="fas fa-circle fa-stack-2x" style="color:#24CC63;"></i>
                          <i class="fab fa-whatsapp fa-stack-1x fa-inverse"></i>
                        </span>
        </a>

        <a href="mailto:?subject=Check out my course progress on New Skills Academy&body=<?= $socialText ?>">
                        <span class="fa-stack fa-2x">
                          <i class="fas fa-circle fa-stack-2x"></i>
                          <i class="far fa-envelope fa-stack-1x fa-inverse"></i>
                        </span>
        </a>

    </div>
</div>
