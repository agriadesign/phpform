<?php

/******************************/
/* version 0.2.6 @ 2010.03.10 */
/******************************/

require_once("class.autocomplete.php");
$keyword = $_GET["keyword"];
$ac = new autoComplete();
if(ob_get_length()) {
    ob_clean();
}
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
header("Content-Type: text/xml; charset=utf-8");
echo $ac->search("zipcodes", "city", $keyword);

?>
