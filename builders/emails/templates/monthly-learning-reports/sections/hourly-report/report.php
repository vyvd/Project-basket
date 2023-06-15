<table>
    <tr>
        <td style="height:80px;">
            <table>
                <tr>
                    <td>
                        <a href="<?= $config['HOURS_IMG_HREF'] ?>" style="width:80px;height:80px;display: block;"
                           target="_blank">
                            <img
                                    align="center"
                                    alt="Logo" border="0"
                                    class="center fixedwidth"
                                    src='<?= $config['HOURS_IMG_SRC'] ?>'
                                    style="float:left;text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; width: 80px;
                max-width: 80px; " title="Logo"/>
                        </a>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td style="font-size: 72px;color: #000000;vertical-align: bottom"><?= $config['HOURS'] ?></td>
                                <td style="vertical-align: bottom;line-height:40px;"><?= $config['HOURS_LABEL'] ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td style="vertical-align: middle;width:100%;" align="right">
            <table style="float: right">
                <tr>
                    <td style="text-align: left;max-width:260px;">
                        <?= $config['TEXT'] ?>
                    </td>
                    <?php
                    if ($config['ARROW_IMG_SRC'] !== false && (isset($config['ARROW_IMG_SRC']) && $config['ARROW_IMG_SRC'] !== '')) {
                        ?>
                        <td>
                            <img width="30" src="<?= $config['ARROW_IMG_SRC'] ?>" style="width: 30px;"/>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
            </table>
        </td>
    </tr>
</table>