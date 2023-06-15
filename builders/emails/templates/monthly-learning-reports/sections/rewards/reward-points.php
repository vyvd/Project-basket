<?= $this->renderBlockHeader($config) ?>
<table style="text-align: left; margin: 0 auto;margin-top: 16px">
    <tbody style="display: block">
    <tr style="display: block">
        <td style="display: block">
        <table style="display: table;margin: auto">
            <tbody style="display: table-row-group">
            <tr style="display: table-row">
                <td style="display: table-cell">
                    <img src="<?= $config['IMG_SRC'] ?>" alt="" width="40" height="50"
                         style="width:50px;height:50px;display: table-cell"/>
                </td>
                <td style="display: table-cell;font-size: 24px"><?= $config['POINTS'] ?></td>
            </tr>
            </tbody>
        </table>
        </td>
    </tr>
</table>