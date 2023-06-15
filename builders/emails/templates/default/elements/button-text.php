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
        <td style="text-align: center">
            <v:roundrect
                    xmlns:v="urn:schemas-microsoft-com:vml"
                    xmlns:w="urn:schemas-microsoft-com:office:word"
                    style="<?= $this->buildStyleString($containerStyles) ?>"
                    arcsize="50%"
                    stroke="false"
                    fillcolor="<?= $bgColor ?>">
                <w:anchorlock/>
                <v:textbox inset="0,0,0,0">
                        <center style="display: inline-block"><span style="<?= $this->buildStyleString($textStyles) . $this->buildStyleString($containerStyles) ?>"><?= $config['text'] ?></span></center>
                </v:textbox>
            </v:roundrect>
        </td>
    </tr>
    </tbody>
</table>