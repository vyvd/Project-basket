<?php
$this->setControllers(array("googleAuth"));

$auth = $this->controller->googleAuthRedirectHandler();
?>
<script type="text/javascript">
    <?php
        if ($auth['success']) {
            $message = 'success';
        } else {
            $message = 'error';
        }
    ?>
    window.opener.postMessage('<?= $message ?>', '<?php echo rtrim(SITE_URL, '/'); ?>')
</script>
