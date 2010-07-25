<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>&lt;?php form&gt;</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<?php

require_once("../phpform/class.form.php");

//---------------------------------------------------------------------------------------------------------------------
$dynamicField = new autoComplete("xml");
$options = $dynamicField->getArray("data/counties.xml", "name");
$options2 = array("Húsleves",
                  "Gulyásleves",
                  "Gyümölcsleves",
                  "Rántott hús",
                  "Sült hal",
                  "Rakott káposzta",
                  "Somlói",
                  "Gesztenyepüré",
                  "Palacsinta");
                  
$optgroups = array("Leves" => 3, "Főétel" => 3, "Desszert" => 3);
//---------------------------------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------------------------------
$form = new Form("index.php", "post", 0, "xhtml", true, "next");

$form->fieldset("Regisztrációs űrlap");

$form->fieldset("Személyes adatok");
$form->label("nev", "Név");
$form->input("text", array("nev"));
$form->mask("n");
$form->validate("n");
$form->label("email", "E-mail cím");
$form->input("text", array("email"));
$form->validate("m");
$form->label("jelszo1", "Jelszó");
$form->input("password", array("jelszo1"));
$form->label("jelszo2", "Jelszó még egyszer");
$form->input("password", array("jelszo2"));
$form->label("szulido", "Születési idő");
$form->input("text", array("szulido"));
$form->mask("dddd-dd-dd");
$form->validate("c");
$form->html('<p class="labelText">Neme</p>');
$form->input("radio", array("nem", "férfi"));
$form->label("nem", "Férfi");
$form->input("radio", array("nem", "nő", "checked"));
$form->label("nem", "Nő");
$form->input("hidden", array("regid", "12345"));
$form->fieldset();

$form->fieldset("Lakcím adatok");
$form->label("megye", "Megye");
$form->select("megye", $options, "Heves megye");
$form->label("varos", "Város");
$form->input("text", array("varos"));
$form->autoComplete("xml", "../data/zipcodes.xml", "name");
$form->label("iranyitoszam", "Irányítószám");
$form->input("text", array("iranyitoszam"));
$form->label("utca", "Utca, házszám");
$form->input("text", array("utca"));
$form->fieldset();

$form->fieldset("Érdeklődési területek");
$form->input("checkbox", array("erdeklodes01", "informatika", "checked"));
$form->label("erdeklodes01", "Informatika");
$form->input("checkbox", array("erdeklodes02", "utazás"));
$form->label("erdeklodes02", "Utazás");
$form->input("checkbox", array("erdeklodes03", "művészet"));
$form->label("erdeklodes03", "Művészet");
$form->input("checkbox", array("erdeklodes04", "sport"));
$form->label("erdeklodes04", "Sport");
$form->input("checkbox", array("erdeklodes05", "divat"));
$form->label("erdeklodes05", "Divat");
$form->fieldset();

$form->fieldset("Üzenet");
$form->html('<p style="font-style: italic">Ha szeretne nekümk üzenetet küldeni, akkor itt megteheti:</p>');
$form->textarea("velemeny", 50, 10, "Ide írhat...");
$form->fieldset();

$form->fieldset("Menü");
$form->html('<p>Kérjük, hogy az ebédhez válasszon levest, főételt és desszertet:</p>');
$form->select("menu", $options2, array("Húsleves", "Rántott hús", "Somlói"), $optgroups, 5, "multiple");
$form->fieldset();

$form->input("submit", array("submit", "Regisztrál"));
$form->input("reset", array("reset", "Töröl"));

$form->fieldset();

$form->setTestMode(TRUE);
echo"<pre>";
$form->render();
echo"</pre>";
//---------------------------------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------------------------------
$form2 = new Form("index.php", "post", "multipart/form-data", 1);
$form2->fieldset("Képfeltöltés");
$form2->html('<p>Ha szeretne magáról képet feltölteni, itt megteheti:</p>');
$form2->input("file", array("file", "File"));
$form2->button("Feltölt", "submit");
$form2->fieldset();

$form2->setTestMode(TRUE);
echo"<pre>";
$form2->render();
echo"</pre>";
//---------------------------------------------------------------------------------------------------------------------

?>

</body>
</html>