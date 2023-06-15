<?php
unset($_SESSION['idx']);
unset($_SESSION['idx_front']);
//Added by Zubaer
unset($_SESSION['nsa_email_front']);

header('Location: '.SITE_URL.'blume/login');