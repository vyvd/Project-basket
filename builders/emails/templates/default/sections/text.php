<?php
$styles = '';
if (isset($config['STYLE']) && is_array($config['STYLE'])) {
    $styles = $this->buildStyleString($config['STYLE']);
}
?>
<table style="<?= $styles ?>">
    <tr>
        <td>
            <?= $config['TEXT'] ?>
        </td>
    </tr>
</table>