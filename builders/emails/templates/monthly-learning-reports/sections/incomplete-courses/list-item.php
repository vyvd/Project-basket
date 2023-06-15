<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="width: 40%;padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;text-align:left;font-size: 16px">
            <table style="width: 100%;">
                <tr>
                    <td><?= $config['TEXT'] ?></td>
                </tr>
            </table>
        </td>

        <td style="width: 40%;padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;text-align:left;border-radius:5px">
            <table style="width: 100%;height:20px;padding-right: 5px;border:2px solid #248CAB;border-spacing: initial;border-collapse: initial;">
                <tr>
                    <td style="width: <?= $config['PERCENTAGE'] ?>%;background-color: #248CAB;"></td>
                    <td style="font-size:12px;width: <?= $config['PERCENTAGE_REMAIN'] ?>%;background-color: #ffffff;text-align: right"><?= $config['PERCENTAGE'] ?>
                        %
                    </td>
                </tr>
            </table>
        </td>
        <td style="width: 20%;padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;text-align:left;">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <?php
                        if (isset($config['CTA']) && is_array($config['CTA'])) {
                            ?>

                            <a href="<?= $config['CTA']['LINK_HREF'] ?>">
                                <img src="<?= MonthlyLearningReportBuilder::SITE_URL ?>/assets/images/continue-button.png" alt="Continue" width="120px"
                                     height="25px"/>
                            </a>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>