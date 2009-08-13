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
$form->label("Felhasználónév");
$form->input("text", "felhasznalonev");
$form->label("Jelszó");
$form->input("password", "jelszo");
$form->input("submit", "submit", "Belépés");
$form->fieldset();
$form->fieldset();
$form->legend("Vélemény");
$form->textarea(50, 10, "velemeny", "Ide írhatja a véleményét...");
$form->button("Elküld", "submit");
$form->fieldset();
$form->fieldset();
$form->legend("Elmúltál már 14?");
$form->label("Igen");
$form->input("radio", "valasz", "igen");
$form->label("Nem");
$form->input("radio", "valasz", "nem", "checked");
$form->fieldset();
$form->render();

// Egyszerű file feltöltő form
$form2 = new Form("index.php", "post", "multipart/form-data");
$form2->fieldset();
$form2->legend("Feltöltés");
$form2->input("file", "file", "File");
$form2->input("submit", "submit", "Feltölt");
$form2->render();

// Ezzel a __toString metódust hívhatjuk meg
// echo $form;

?>
</body>
</html>