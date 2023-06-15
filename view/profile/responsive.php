<?php
$pageTitle = "My Profile";
include BASE_PATH . 'account.header.php';
?>

    <section class="page-title with-nav">
        <div class="container">
            <h1>My Profile</h1>
            <ul class="nav navbar-nav inner-nav nav-tabs">
                <li class="nav-item link">
                    <a class="nav-link active show" href="#myDetails" data-toggle="tab">My Details</a>
                </li>
                <li class="nav-item link ">
                    <a class="nav-link" href="#profileImage" data-toggle="tab">Profile Image</a>
                </li>
                <li class="nav-item link">
                    <a class="nav-link" href="#changePass" data-toggle="tab">Change Password</a>
                </li>
                <li class="nav-item link">
                    <a class="nav-link" href="#userPreferences" data-toggle="tab">Preferences & 2FA</a>
                </li>
            </ul>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="row">



                <div class="col-12 regular-full">
                    <div class="tab-content container white-rounded profile-tabs myProfile">
                        <div id="myDetails" class="tab-pane active show">
                            <h3>My Details</h3>
                            <form name="editProfile">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="firstName">First name</label>
                                        <input type="text" id="firstName" class="form-control" name="firstname" value="<?= $this->user->firstname ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="lastName">Last name</label>
                                        <input type="text" id="lastName" class="form-control" name="lastname" value="<?= $this->user->lastname ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="email">Email Address</label>
                                        <input type="email" id="email" class="form-control"  name="email" value="<?= $this->user->email ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="leaderboard">How would you like your name to appear on the leaderboard</label>
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" value="1" <?php if($this->user->leaderboardName == 1){?> checked <?php }?> name="leaderboardName">First name and First letter of Last Name
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" value="2" <?php if($this->user->leaderboardName == 2){?> checked <?php }?> name="leaderboardName">Full Name
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" value="3" <?php if($this->user->leaderboardName == 3){?> checked <?php }?> name="leaderboardName">First Letter of First Name and Full Last Name
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" value="4" <?php if($this->user->leaderboardName == 4){?> checked <?php }?> name="leaderboardName">Don't show my name
                                            </label>
                                        </div>
                                    </div>
                                    <!--<div class="form-group col-md-6">
                                        <label for="contact">Contact Number</label>
                                        <input type="text" id="contact" class="form-control">
                                    </div>-->
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                            <?php
                            $this->renderFormAjax("account", "edit-profile", "editProfile");
                            ?>

                        </div>
                        <div id="profileImage" class="tab-pane fade">

                            <form name="editImage">
                                <div class="form-row">
                                    <label>Upload a Profile Image</label>
                                    <div class="form-group col-12">
                                        <div class="exisiting-img">
                                            <img src="<?= SITE_URL ?>assets/cdn/profileImg/<?= $this->user->profileImg ?>" alt="profile" style="max-width:150px;" id="currentImg" />
                                        </div>

                                    </div>
                                    <div class="custom-file col-12 col-md-6">
                                        <input type="file" class="custom-file-input" id="customFile" name="uploaded_file">
                                        <label class="custom-file-label" for="customFile">Choose file</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                            <?php
                            $this->renderFormAjax("account", "edit-image", "editImage");
                            ?>
                        </div>
                        <div id="changePass" class="tab-pane fade">
                            <form name="editPassword">
                                <div class="form-row">
                                    <label>Update Password</label>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="passwordCurrent">Current Password</label>
                                        <input type="password" id="passwordCurrent" class="form-control" name="passwordCurrent">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="password">New Password</label>
                                        <input type="password" id="password" class="form-control" name="password">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="confirmPassword">Confirm New Password</label>
                                        <input type="password" id="confirmPassword" class="form-control" name="passwordConfirm">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                            <?php
                            $this->renderFormAjax("account", "edit-password", "editPassword");
                            ?>
                        </div>
                        <div id="userPreferences" class="tab-pane fade">
                            <form name="updatePreferences">
                                <div class="form-row">

                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="rewardNotification">Receive reward notifications...</label>&nbsp;&nbsp;
                                        <label class="nsa-switch">
                                            <input id="rewardNotification" name="rewardNotification" type="checkbox" <?php if($this->user->rewardNotification == 1){?>checked<?php }?>>
                                            <span  class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="twoFactor">Enable 2FA (Two Factor Authentication) (we will email you a code when you sign in from a new device)...</label>&nbsp;&nbsp;
                                        <label class="nsa-switch">
                                            <input id="twoFactor" value="1" name="twoFactor" type="checkbox" <?php if($this->user->twoFactor == 1){?>checked<?php }?>>
                                            <span  class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                            <?php
                                $this->renderFormAjax("account", "updatePreferences", "updatePreferences");
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>



<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#currentImg').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#customFile").change(function() {
        readURL(this);
    });
</script>

<?php include BASE_PATH . 'account.footer.php';?>