<?php

//*************************************************
//* version 0.0.0 @ 2009.07.18                    *
//*************************************************

class Form {
  //---------------------------------------------------------------------------
  public $action;
  public $method;
  private $input_tags = array();
  //---------------------------------------------------------------------------
  function __construct($action, $method) {	
    $this->action = $action;
    $this->method = $method;
  }
  //---------------------------------------------------------------------------
  private function openTags() {
    return "<form action=\"{$this->action}\" method=\"{$this->method}\">\n";		
  }
  //---------------------------------------------------------------------------
  public function fieldset() {
    return "  <fieldset>\n";
  }
  //---------------------------------------------------------------------------
  public function legend($title) {
    return "    <legend>$title</legend>\n";
  }
  //---------------------------------------------------------------------------
  public function input($label, $type, $name) {
    $this->input_tags[] =
      "    <label for=\"{$name}\">{$label}</label>\n".
      "    <input type=\"{$type}\" name=\"{$name}\" id=\"{$name}\">\n";
  }
  //---------------------------------------------------------------------------
  private function closeTags() {
    return "</form>\n";
  }
  //---------------------------------------------------------------------------
  public function renderHTML() {
    print $this->openTags();
    print $this->fieldset();
    print $this->legend("Cím");
    foreach($this->input_tags as $tmp) print $tmp;
    print "  </fieldset>\n";
    print $this->closeTags();
  }
  //---------------------------------------------------------------------------
}

?>