<?php
if (!$config) {
    return;
}
?>
<?= $this->renderBlockHeader($config) ?>

<?= $config['LIST'] ?>
<p><?= $config['FOOTER_TEXT'] ?></p>