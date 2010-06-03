<?php

require_once("class.autocomplete.php");

$search = $_GET["search"];
$where = $_GET["where"];
$type = $_GET["type"];
$keyword = $_GET["keyword"];

$ac = new AutoComplete($search);
$responseXml = $ac->search($where, $type, $keyword);

if(ob_get_length()) {
    ob_clean();
}
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: text/xml; charset=utf-8");

echo $responseXml;

?>
