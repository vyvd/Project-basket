<?php
$containerStyles = [];
if (isset($config['stylesData']['container'])) {
    $containerStyles = $config['stylesData']['container'];
}
$textStyles = [];
if (isset($config['stylesData']['text'])) {
    $textStyles = $config['stylesData']['text'];
}
$bgColor = (isset($containerStyles['background-color']))? $containerStyles['background-color'] : '#ffffff';
?>
<table style="margin: auto">
    <tbody>
    <tr>
        <td>
            <v:roundrect
                    xmlns:v="urn:schemas-microsoft-com:vml"
                    xmlns:w="urn:schemas-microsoft-com:office:word"
                    href="<?= $config['href'] ?>"
                    style="<?= $this->buildStyleString($containerStyles) ?>"
                    arcsize="50%"
                    stroke="false"
                    fillcolor="<?= $bgColor ?>">
                <w:anchorlock>
                <v:textbox inset="0,0,0,0" >
                    <span style="<?= $this->buildStyleString($textStyles) ?>">
                        <center>
                        <a style="<?= $this->buildStyleString($textStyles) . $this->buildStyleString($containerStyles) ?>" href="<?= $config['href'] ?>"><?= $config['text'] ?></a>
                        </center>
                    </span>
                </v:textbox>
                </w:anchorlock>
            </v:roundrect>
        </td>
    </tr>
    </tbody>
</table>
