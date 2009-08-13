<?php

/******************************/
/* version 0.0.3 @ 2009.08.13 */
/******************************/

class Form {
  //---------------------------------------------------------------------------
  protected $_action;
  protected $_method;
  protected $_enctype;
  protected $_fieldsetOpen = FALSE;
  protected $_form = array();
  protected static $_instances = 0;
  //---------------------------------------------------------------------------
  public function __construct($action, $method = NULL, $enctype = NULL) {
    $this->_action = $action;
    if(isset($method)) {
      $this->_method = $method;
    }
    if(isset($enctype)) {
      $this->_enctype = $enctype;
    }
    self::$_instances++;
    $this->open();
  }
  //---------------------------------------------------------------------------
  protected function open() {
    $this->_form[] =
      array('tag' => 'form', 'status' => 'open', 'action' => $this->_action);
    if(isset($this->_method)) {
      $this->_form[$this->index()] += array('method' => $this->_method);
    }
    if(isset($this->_enctype)) {
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
    else {
      $this->_form[] = array('tag' => 'fieldset', 'status' => 'close');
      $this->_fieldsetOpen = FALSE;
    }
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
  public function input($type, $name, $value = NULL, $checked = NULL) {
    $id = $this->id();
    if($this->_form[$this->index()]['tag'] == 'label') {
      $this->_form[$this->index()] += array('for' => $id);
    }
    $this->_form[] = 
      array('tag' => 'input', 'type' => $type, 'name' => $name, 'id' => $id);
    if(isset($value)) {
      $this->_form[$this->index()] += array('value' => $value);
    }
    if(isset($checked)) {
      $this->_form[$this->index()] += array('checked' => $checked);
    }
  }
  //---------------------------------------------------------------------------
  public function textarea($cols, $rows, $name, $content = NULL) {
    $this->_form[] = array('tag' => 'textarea', 'name' => $name,
                           'cols' => $cols, 'rows' => $rows);
    if(isset($content)) {
      $this->_form[$this->index()] += array('content' => $content);
    }
  }
  //---------------------------------------------------------------------------
  public function button($content, $value, $type = "submit") {
    $this->_form[] =
      array('tag' => 'button', 'type' => $type, 'name' => $type,
            'value' => $value, 'content' => $content,);
  }
  //---------------------------------------------------------------------------
  public function html($content) {
    	$this->_form[] = array('tag' => 'html', 'content' => $content);
  }
  //---------------------------------------------------------------------------
  protected function close() {
    if($this->_fieldsetOpen == TRUE) {
      $this->_form[] = array('tag' => 'fieldset', 'status' => 'close');
    }
    $this->_form[] = array('tag' => 'form', 'status' => 'close');
  }
  //---------------------------------------------------------------------------
  public function render() {
    $this->close();
    foreach($this->_form as $tags) {
      switch($tags['tag']) {
        //----- input -----//
        case 'input':
          echo"\t\t";
          foreach($tags as $key => $value) {
            if ($key == 'tag') {
              echo'<' . $value;
            }
            else if($key != 'status') {
              echo' ' . $key . '="' . $value . '"';
            }
          }
          echo ">\n";
        break;
        //----- form -----//
        case 'form':
          foreach($tags as $key => $value) {
            if ($key == 'status' && $value == 'open') {
              echo"<form";
            }
            if($key != 'tag' && $key != 'status') {
              echo' ' . $key . '="' . $value . '"';
            }
          }
          if($key == 'status' && $value == 'close') echo "</form";
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
        //----- textarea -----//
        case 'textarea':
          echo "\t\t". '<textarea cols="' . $tags['cols'] . '" rows="' .
               $tags['rows'] . '" name="' . $tags['name'] . '">' .
               $tags['content'] . "</textarea>\n";
        break;
        //----- button -----//
        case 'button':
          echo "\t\t" . '<button type="' . $tags['type'] . '" name="' .
          $tags['name'] . '" value="' . $tags['value'] . '">' .
          $tags['content'] . "</button>\n";
        break;
        //----- html -----//
        case 'html':
          echo "\t" . $tags['content'] . "\n";
        break;
      }
    }
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