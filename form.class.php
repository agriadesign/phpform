<?php

/******************************/
/* version 0.0.2 @ 2009.08.06 */
/******************************/

class Form {
  //---------------------------------------------------------------------------
  protected $_action;
  protected $_method = NULL;
  protected $_enctype = NULL;
  protected $_fieldsetOpen = FALSE;
  protected $_form = array();
  protected static $_instances = 0;
  //---------------------------------------------------------------------------
  public function __construct($action, $method = NULL, $enctype = NULL) {
    $this->_action = $action;
    if(!empty($method)) {
      $this->_method = $method;
    }
    if(!empty($enctype)) {
      $this->_enctype = $enctype;
    }
    self::$_instances++;
    $this->open();
  }
  //---------------------------------------------------------------------------
  protected function open() {
    $this->_form[] = array('tag' => 'form', 'action' => $this->_action);
    if(!empty($this->_method)) {
      $this->_form[$this->index()] += array('method' => $this->_method);
    }
    if(!empty($this->_enctype)) {
      $this->_form[$this->index()] += array('enctype' => $this->_enctype);
    }
  }
  //---------------------------------------------------------------------------
  public function fieldset() {
    if($this->_fieldsetOpen == FALSE) {
      $this->_form[] = array('tag' => 'fieldset', 'status' => 'open');
      $this->_fieldsetOpen = TRUE;
      return;
    }
    $this->_form[] = array('tag' => 'fieldset', 'status' => 'close');
    $this->_fieldsetOpen = FALSE;
  }
  //---------------------------------------------------------------------------
  public function legend($title) {
    $this->_form[] = array('tag' => 'legend', 'content' => $title);
  }
  //---------------------------------------------------------------------------
  public function label($content) {
    $this->_form[] = array('tag' => 'label', 'content' => $content);
  }
  //---------------------------------------------------------------------------
  public function input($type, $name, $value = NULL) {
    $id = $this->id();
    if($this->_form[$this->index()]['tag'] == 'label') {
      $this->_form[$this->index()] += array('for' => $id);
    }
    $this->_form[] = 
      array('tag' => 'input', 'type' => $type, 'name' => $name, 'id' => $id);
    if(!empty($value)) {
      $this->_form[$this->index()] += array('value' => $value);
    }
  }
  //---------------------------------------------------------------------------
  protected function close() {
    if($this->_fieldsetOpen == TRUE) {
      $this->_form[] = array('tag' => 'fieldset', 'status' => 'close');
    }
  }
  //---------------------------------------------------------------------------
  public function render() {
    $this->close();
    foreach($this->_form as $tags) {
      switch($tags['tag']) {
        //----- input -----//
        case 'input': echo"\t\t";
        //----- form -----//
        case 'form':
          foreach($tags as $key => $value) {
            if ($key == 'tag') {
              echo'<' . $value;
            }
            else {
              echo' ' . $key . '="' . $value . '"';
            }
          }
          echo ">\n";
        break;
        //----- fieldset -----//
        case 'fieldset':
          if($tags['status'] == 'open') {
            echo "\t<fieldset>\n";
          }
          if($tags['status'] == 'close') {
            echo "\t</fieldset>\n";
          }
        break;
        //----- legend -----//
        case 'legend':
          echo "\t\t<legend>" . $tags['content'] . "</legend>\n";
        break;
        //----- label -----//
        case 'label':
          echo "\t\t" . '<label for="' . $tags['for'] . '">' .
               $tags['content'] . '</label>'. "\n";
        break;
      }
    }
    echo"</form>\n";
  }
  //---------------------------------------------------------------------------
  protected function index() {
    return count($this->_form) - 1;
  }
  //---------------------------------------------------------------------------
  protected function id() {
    $digit = '';
    if(count($this->_form) < 10) $digit = '0';
    return 'form-' . self::$_instances . $digit . count($this->_form);
  }
  //---------------------------------------------------------------------------
  public function __toString() {
    return "<pre>\n" . print_r($this->_form, true) . "</pre>\n";
  }
  //---------------------------------------------------------------------------
}
?>