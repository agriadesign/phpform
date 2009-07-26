<?php

/******************************/
/* version 0.0.1 @ 2009.07.26 */
/******************************/

class Form {
  //---------------------------------------------------------------------------
  protected $_action = "#";
  protected $_method = "post";
  protected $_enctype = NULL;
  protected $_fieldsetOpen = FALSE;
  protected $_formString = "";
  //---------------------------------------------------------------------------
  public function __construct($action, $method, $enctype = NULL) {		
    $this->_action = $action;
    $this->_method = $method;
    if(!empty($enctype)) $this->_enctype = $enctype;
    $this->openTag();
  }
  //---------------------------------------------------------------------------
  protected function openTag() {
    $this->_formString .=
    '<form action="' . $this->_action . '" method="' . $this->_method;
    if(!empty($this->_enctype))
      $this->_formString .= '" enctype="' . $this->_enctype;
      $this->_formString .= '">' . "\n";
  }
  //---------------------------------------------------------------------------
  public function fieldset() {
    $stringing = "";
    if($this->_fieldsetOpen == TRUE) {
      $stringing .= "\t</fieldset>\n";
      $this->_fieldsetOpen = FALSE;
    }
    $stringing .= "\t<fieldset>\n";
    $this->_fieldsetOpen = TRUE;
    $this->_formString .= $stringing;
  }
  //---------------------------------------------------------------------------
  public function legend($title) {
    $this->_formString .= "\t\t<legend>$title</legend>\n";
  }
  //---------------------------------------------------------------------------
  public function input($type, $name, $label, $value = NULL) {
    $this->_formString .=
    "\t\t" . '<label for="' . $name . '">' . $label . '</label>' . "\n" .
    "\t\t" . '<input type="' . $type . '" name="' . $name .
    '" id="' . $name . '" value="' . $value . '">' . "\n";
  }
  //---------------------------------------------------------------------------
  protected function closeTag() {
    $stringing = "";
    if($this->_fieldsetOpen == TRUE) $stringing .= "\t</fieldset>\n";
    $stringing .= "</form>\n";
    $this->_formString .= $stringing;
  }
  //---------------------------------------------------------------------------
  public function renderHTML() {
    $this->closeTag();
    print $this->_formString;
  }
  //---------------------------------------------------------------------------
}

?>