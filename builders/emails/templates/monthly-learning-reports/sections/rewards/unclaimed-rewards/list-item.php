<table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align: left">
    <tr>
        <td style=" font-family: Tahoma, sans-serif">
            <p style="margin: 0;width: 130px"><?= $config['TEXT'] ?></p>
        </td>
        <td>
            <?php
            if (isset($config['CTA']) && is_array($config['CTA'])) {
                ?>

                <a href="<?= $config['CTA']['LINK_HREF'] ?>">
                    <img src="<?= MonthlyLearningReportBuilder::SITE_URL ?>/assets/images/claim-button.png" alt="Claim" width="60px"
                         height="15px"/>
                </a>
                <?php
            }
            ?>
        </td>
    </tr>
</table>