<?= $course->description ?>
<?= $course->additionalContent ?>
<?php if(!in_array('Qualifications', $categories)){?>
    <h4>This course includes</h4>
    <div class="features-box align-items-center">
    <span>
      <em class="icon">
          <i class="fas fa-headset"></i>
      </em>
      <p>24/7 Student Support</p>
    </span>
        <span>
            <em class="icon">
          <i class="fas fa-file-certificate"></i>
      </em>
      <p>End of course certification</p>
    </span>
        <span>
      <em class="icon">
          <i class="fas fa-infinity"></i>
      </em>
      <p>Lifetime access to your course</p>
    </span>
        <span>
      <em class="icon">
          <i class="fas fa-phone-laptop"></i>
      </em>
      <p>Compatible with modern devices</p>
    </span>
    </div>

    <?php
    $studyGroupUrl = $this->getSetting("study_group_url");

    if($studyGroupUrl != "") {
        ?>
        <div class="studyGroup">
            PLUS - Access to the exclusive New Skills Academy Study Group!
        </div>
        <?php
    }
    ?>

<?php }?>