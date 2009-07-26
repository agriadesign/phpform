<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="style.css" type="text/css">
<title>&lt;?php form&gt;</title>
</head>
<body>
<?php

require_once("form.class.php");

// Egyszerű form néhány input mezővel és két fieldsettel
$form = new Form("index.php", "post");
$form->fieldset();
$form->legend("Login");
$form->input("text", "felhasznalonev", "Felhasználónév");
$form->input("password", "jelszo", "Jelszó");
$form->input("submit", "submit", "", "Belépés");
$form->fieldset();
$form->legend("Kérdőív");
$form->input("text", "velemeny", "Vélemény");
$form->input("submit", "submit", "", "Elküld");
$form->renderHTML();

// Egyszerű file feltöltő form
$form2 = new Form("index.php", "post", "multipart/form-data");
$form2->fieldset();
$form2->legend("Feltöltés");
$form2->input("file", "file", "File");
$form2->input("submit", "submit", "", "Feltölt");
$form2->renderHTML();

?>
</body>
</html>