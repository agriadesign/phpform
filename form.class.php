<?php

/******************************/
/* version 0.0.8 @ 2009.09.10 */
/******************************/

class Form {
  //-------------------------------------------------------------------------------------------------------------------
  protected $_action;
  protected $_method;
  protected $_enctype;
  protected $_fieldsetIds = array();
  protected $_form = array();
  protected static $_instances = 0;
  //-------------------------------------------------------------------------------------------------------------------
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
  //-------------------------------------------------------------------------------------------------------------------
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
  //-------------------------------------------------------------------------------------------------------------------
  public function fieldset($id) {
    $n = count($this->_fieldsetIds);
    $i = 0;
    while ($i < $n && $this->_fieldsetIds[$i] != $id) {
      $i++;
    }
    if($i < $n) {
      $this->_form[] =
        array('tag' => 'fieldset', 'status' => 'close', 'fieldsetid' => $id);
      array_splice($this->_fieldsetIds, $i, 1);
    }
    else {
      $this->_form[] =
        array('tag' => 'fieldset', 'status' => 'open', 'fieldsetid' => $id);
      $this->_fieldsetIds[] = $id;
    }
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function legend($title) {
    $this->_form[] =
      array('tag' => 'legend', 'status' => 'open', 'content' => $title);
    $this->_form[] =
      array('tag' => 'legend', 'status' => 'close');
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function label($content) {
    $this->_form[] =
      array('tag' => 'label', 'status' => 'open', 'content' => $content);
    $this->_form[] =
      array('tag' => 'label', 'status' => 'close');
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function input($type, array $attributeValues) {
    $id = $this->id();
    if($this->_form[$this->index()-1]['tag'] == 'label' &&
       $this->_form[$this->index()-1]['status'] == 'open') {
      $this->_form[$this->index()-1] += array('for' => $id);
    }
    $this->_form[] = array('tag' => 'input', 'status' => 'empty',
                           'type' => $type, 'id' => $id);
    switch($type) {
      case "text":
      case "password":
        $attributeNames = array('name', 'value', 'maxlength', 'readonly', 'disabled');
      break;
      case "file":
        $attributeNames = array('name', 'value', 'accept', 'disabled');
      break;
      case "checkbox":
      case "radio":
        $attributeNames = array('name', 'value', 'checked', 'disabled');
      break;
      case "button":
      case "submit":
      case "reset":
        $attributeNames = array('name', 'value', 'disabled');
      break;
      case "hidden":
        $attributeNames = array('name', 'value');
      break;
      case "image":
        $attributeNames = array('name', 'src', 'value', 'alt', 'disabled');
      break;
      default:
        throw new Exception("<strong>{$type}</strong> is not a valid value for the 'type' attribute");
      break;
    }
    $attributeNames = array_slice($attributeNames, 0, count($attributeValues));
    if(!empty($attributeNames)) {
      $attributes = array_combine($attributeNames, $attributeValues);
      foreach($attributes as $key => $value) {
        if(!empty($value)) $this->_form[$this->index()] += array($key => $value);
      }
    }
  }    
  //-------------------------------------------------------------------------------------------------------------------
  public function select($name, array $options, array $optGroups = NULL, $size = NULL, $multiple = NULL) {
    $this->_form[] =
      array('tag' => 'select', 'status' => 'open', 'name' => $name);
    if(isset($size)) {
      $this->_form[$this->index()] += array('size' => $size);
    }
    if(isset($multiple)) {
      $this->_form[$this->index()] += array('multiple' => $multiple);
    }
    if(isset($optGroups)) {
      foreach($optGroups as $key => $value) {
        $optGroupNames[] = $key;
        $optGroupLines[] = $value;
      }
      $n = count($optGroupNames);
      $offset = 0;
      for($i = 0; $i < $n; $i++) {
        $this->_form[] =
          array('tag' => 'optgroup', 'status' => 'open', 'label' => $optGroupNames[$i]);
        for($j = 0; $j < $optGroupLines[$i]; $j++) {
          $this->_form[] =
            array('tag' => 'option', 'status' => 'open', 'content' => $options[$offset]);
        $this->_form[] = array('tag' => 'option', 'status' => 'close');
        $offset++;
        }
        $this->_form[] = array('tag' => 'optgroup', 'status' => 'close');
      }
    }
    else {
      foreach($options as $tmp) {
        $this->_form[] =
          array('tag' => 'option', 'status' => 'open', 'content' => $tmp);
        $this->_form[] = array('tag' => 'option', 'status' => 'close');
      }
		}
   $this->_form[] = array('tag' => 'select', 'status' => 'close');
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function textarea($cols, $rows, $name, $content = NULL) {
    $this->_form[] = array('tag' => 'textarea', 'status' => 'open',
                           'name' => $name, 'cols' => $cols, 'rows' => $rows);
    if(isset($content)) {
      $this->_form[$this->index()] += array('content' => $content);
    }
    $this->_form[] = array('tag' => 'textarea', 'status' => 'close');
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function button($content, $value, $type = "submit") {
    $this->_form[] = array('tag' => 'button', 'status' => 'open',
                           'type' => $type, 'name' => $type,
                           'value' => $value, 'content' => $content,);
    $this->_form[] = array('tag' => 'button', 'status' => 'close');
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function html($content) {
    	$this->_form[] = array('html' => $content);
  }
  //-------------------------------------------------------------------------------------------------------------------
  protected function close() {
    $n = count($this->_fieldsetIds);
    while($n > 0) {
      $id = array_pop($this->_fieldsetIds);
      $this->_form[] =
        array('tag' => 'fieldset', 'status' => 'close', 'fieldsetid' => $id);
      $n--;
    }
    $this->_form[] = array('tag' => 'form', 'status' => 'close');
  }
  //-------------------------------------------------------------------------------------------------------------------
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
          case "fieldsetid":
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
  //-------------------------------------------------------------------------------------------------------------------
  protected function index() {
    return count($this->_form) - 1;
  }
  //-------------------------------------------------------------------------------------------------------------------
  protected function id() {
    $digit = '';
    if(count($this->_form) < 10) $digit = '0';
    return 'form-' . self::$_instances . $digit . count($this->_form);
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function __toString() {
    return "<pre>\n" . print_r($this->_form, true) . "</pre>\n";
  }
  //-------------------------------------------------------------------------------------------------------------------
}
?>