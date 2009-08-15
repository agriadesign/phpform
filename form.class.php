<?php

/******************************/
/* version 0.0.4 @ 2009.08.15 */
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
    $this->_form[] =
      array('tag' => 'legend', 'status' => 'open', 'content' => $title);
    $this->_form[] =
      array('tag' => 'legend', 'status' => 'close');
  }
  //---------------------------------------------------------------------------
  public function label($content) {
    $this->_form[] =
      array('tag' => 'label', 'status' => 'open', 'content' => $content);
    $this->_form[] =
      array('tag' => 'label', 'status' => 'close');
  }
  //---------------------------------------------------------------------------
  public function input($type, $name, $value = NULL, $checked = NULL) {
    $id = $this->id();
    if($this->_form[$this->index()-1]['tag'] == 'label' &&
       $this->_form[$this->index()-1]['status'] == 'open') {
      $this->_form[$this->index()-1] += array('for' => $id);
    }
    $this->_form[] = array('tag' => 'input', 'status' => 'empty',
                           'type' => $type, 'name' => $name, 'id' => $id);
    if(isset($value)) {
      $this->_form[$this->index()] += array('value' => $value);
    }
    if(isset($checked)) {
      $this->_form[$this->index()] += array('checked' => $checked);
    }
  }
  //---------------------------------------------------------------------------
  public function textarea($cols, $rows, $name, $content = NULL) {
    $this->_form[] = array('tag' => 'textarea', 'status' => 'open',
                           'name' => $name, 'cols' => $cols, 'rows' => $rows);
    if(isset($content)) {
      $this->_form[$this->index()] += array('content' => $content);
    }
    $this->_form[] = array('tag' => 'textarea', 'status' => 'close');
  }
  //---------------------------------------------------------------------------
  public function button($content, $value, $type = "submit") {
    $this->_form[] = array('tag' => 'button', 'status' => 'open',
                           'type' => $type, 'name' => $type,
                           'value' => $value, 'content' => $content,);
    $this->_form[] = array('tag' => 'button', 'status' => 'close');
  }
  //---------------------------------------------------------------------------
  public function html($content) {
    	$this->_form[] = array('html' => $content);
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
    $tagName = "";
    $contentValue = "";
    foreach($this->_form as $tagArray) {
      foreach($tagArray as $key => $value) {
        switch($key) {
          case "tag":
            $tagName = $value;
          break;
          case "status": 
            if($value == "close") {
              echo "</{$tagName}";
            }
            else {
              echo "<{$tagName}";
            }
          break;
          case "content":
            $contentValue = $value;
          break;
          case "html":
            echo "{$value}\n";
          break;
          default:
            echo ' ' . $key . '="' . $value . '"';
          break;
        }
      }
      if($key != "html") {
        if(empty($contentValue)) {
          echo ">\n";
        }
        else {
          echo ">{$contentValue}";
        }
        $contentValue = "";
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