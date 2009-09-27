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
//---------------------------------------------------------------------------------------------------------------------
require_once("form.class.php");

$options = array("Bács-Kiskun megye",
                 "Baranya megye",
                 "Békés megye",
                 "Borsod-Abaúj-Zemplén megye",
                 "Csongrád megye",
                 "Fejér megye",
                 "Győr-Moson-Sopron megye",
                 "Hajdú-Bihar megye",
                 "Heves megye",
                 "Jász-Nagykun-Szolnok megye",
                 "Komárom-Esztergom megye",
                 "Nógrád megye",
                 "Pest megye",
                 "Somogy megye",
                 "Szabolcs-Szatmár-Bereg megye",
                 "Tolna megye",
                 "Vas megye",
                 "Veszprém megye",
                 "Zala megye");
                 
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
$form = new Form("index.php", "post");

$form->fieldset("Nagy keret");
$form->legend("Regisztrációs űrlap");

$form->fieldset(1);
$form->legend("Személyes adatok");
$form->label("nev", "Név");
$form->input("text", array("nev"));
$form->label("email", "E-mail cím");
$form->input("text", array("email"));
$form->label("jelszo", "Jelszó");
$form->input("password", array("jelszo"));
$form->html("<p>Neme</p>");
$form->input("radio", array("nem", "férfi"));
$form->label("nem", "Férfi");
$form->input("radio", array("nem", "nő", "checked"));
$form->label("nem", "Nő");
$form->input("hidden", array("regid", "12345"));
$form->fieldset(1);

$form->fieldset(2);
$form->legend("Lakcím adatok");
$form->label("megye[]", "Megye");
$form->select("megye[]", $options);
$form->label("varos", "Város");
$form->input("text", array("varos"));
$form->label("utca", "Utca, házszám");
$form->input("text", array("utca"));
$form->fieldset(2);

$form->fieldset(3);
$form->legend("Érdeklődési területek");
$form->input("checkbox", array("erdeklodes1", "informatika", "checked"));
$form->label("erdeklodes1", "Informatika");
$form->input("checkbox", array("erdeklodes2", "utazás"));
$form->label("erdeklodes2", "Utazás");
$form->input("checkbox", array("erdeklodes3", "művészet"));
$form->label("erdeklodes3", "Művészet");
$form->input("checkbox", array("erdeklodes4", "sport"));
$form->label("erdeklodes4", "Sport");
$form->input("checkbox", array("erdeklodes5", "divat"));
$form->label("erdeklodes5", "Divat");
$form->fieldset(3);

$form->fieldset(4);
$form->legend("Üzenet");
$form->html('<p>Ha szeretne nekümk üzenetet küldeni, akkor itt megteheti:</p>');
$form->textarea(50, 10, "velemeny", "Ide írhatja az üzenetét...");
$form->fieldset(4);

$form->fieldset(5);
$form->legend("Menü");
$form->html('<p>Kérjük, hogy az ebédhez válasszon levest, főételt és desszertet:</p>');
$form->select("menu[]", $options2, $optgroups, 5, "multiple");
$form->fieldset(5);

$form->input("submit", array("submit", "Regisztrál"));
$form->input("reset", array("reset", "Töröl"));

$form->fieldset("Nagy keret");

$form->render();
//---------------------------------------------------------------------------------------------------------------------
$form2 = new Form("index.php", "post", "multipart/form-data");
$form2->fieldset("Kép");
$form2->legend("Képfeltöltés");
$form2->html('<p>Ha szeretne magáról képet feltölteni, itt megteheti:</p>');
$form2->input("file", array("file", "File"));
$form2->button("Feltölt", "submit");

$form2->render();
//---------------------------------------------------------------------------------------------------------------------
// echo $form;
// echo $form2;
//---------------------------------------------------------------------------------------------------------------------

?>
</body>
</html>