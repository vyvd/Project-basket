<?php
if (!$config) {
    return;
}
$style = [
    'width' => '100%',
    'border-radius' => '5px',
    'text-align' => 'left',
    'padding' => '10px',
];
if (isset($config['IS_CURRENT_ACCOUNT']) && $config['IS_CURRENT_ACCOUNT']) {
    $style['border'] = '2px solid #248CA7';
}
$styleString = $this->buildStyleString($style);
?>
<table class="show_on_desktop table" cellpadding="0" cellspacing="0" border="0" style="<?= $styleString ?>">
    <tr>
        <td >
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="width:40px;text-align: left;">
                        <?= $config['POSITION'] ?>
                    </td>
                    <td style="width:50px;text-align: left;">
                        <img src="<?= $config['TROPHY_IMG'] ?>" alt="" style="width:30px;height:30px;" width="30"
                             height="30"/>
                    </td>
                    <td style="width:170px;text-align: left;">
                        <?= $config['FIRSTNAME'] ?> <?= $config['LASTNAME'] ?>
                    </td>
                    <td style="width:90px;text-align: left;">
                        <?= $config['POINTS'] ?>
                    </td>
                    <td style="width: 90px;text-align: left;">
                        <?php
                        if (isset($config['WINNER_TEXT'])) {
                            ?>
                            <img src="<?= MonthlyLearningReportBuilder::SITE_URL ?>/assets/images/winner-button.png" alt="Winner" width="120px" height="25px" />
                            <?php
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>