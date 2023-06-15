<table width="100%" height="100px" bgcolor="#248CAB"
       style="width: 100%;background-color: #248CAB;height: 100px;font-size:22px;color:#ffffff;font-weight: 600;border-spacing: 20px 0" valign="middle">
    <tr>
        <td id="header_logo">
            <a href="<?= MonthlyLearningReportBuilder::SITE_URL ?>" style="outline:none" tabindex="-1" target="_blank">
                <img alt="Logo" border="0"
                     src="<?= MonthlyLearningReportBuilder::SITE_URL ?>/assets/images/logo-white.png"
                     style="width: 130px;" title="Logo" width="130" />
            </a>
        </td>
        <td style="text-align: right">
            Learner Report <?= date('F Y', time()) ?>
        </td>
    </tr>
</table>