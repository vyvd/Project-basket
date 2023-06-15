<?php
    $this->renderBlockHeader($config);
?>

<table style="display: table;margin: auto">
    <tbody>
    <tr>
        <td>
            <?= $config['LIST'] ?>
        </td>
    </tr>
    </tbody>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;text-align: center">
            <?php
            if (isset($config['CTA']) && is_array($config['CTA'])) {
                ?>

                <a href="<?= $config['CTA']['LINK_HREF'] ?>">
                    <img src="<?= MonthlyLearningReportBuilder::SITE_URL; ?>/assets/images/see-all-courses-btn.png" alt="See all courses" width="140px"
                         height="20px"/>
                </a>
                <?php
            }
            ?>
        </td>
    </tr>
</table>