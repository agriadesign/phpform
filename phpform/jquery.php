<?php

require_once("class.autocomplete.php");

$search = $_GET["search"];
$where = $_GET["where"];
$type = $_GET["type"];
$keyword = $_GET["q"];

$ac = new AutoComplete($search);

echo $ac->getText($where, $type, $keyword);

?>
