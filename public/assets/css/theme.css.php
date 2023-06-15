<?php
$cssFiles = array(
  "../vendor/bootstrap/bootstrap.min.css",
  "theme.css",
  "media.css",
  "toastr.min.css"
);
$buffer = "";
foreach ($cssFiles as $cssFile) {
  $buffer .= file_get_contents($cssFile);
}
// Remove comments
$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
// Remove space after colons
$buffer = str_replace(': ', ':', $buffer);
// Remove whitespace
$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
// Enable GZip encoding.
ob_start("ob_gzhandler");
// Enable caching
header('Cache-Control: public');
// Expire in one day
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
// Set the correct MIME type, because Apache won't set it for us
header("Content-type: text/css");
// Write everything out
echo '@import url(\'https://fonts.googleapis.com/css2?family=Caveat+Brush&display=swap\');';
echo($buffer);