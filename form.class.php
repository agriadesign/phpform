<?php

/******************************/
/* version 0.1.0 @ 2009.09.15 */
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
  public function setAction($action)
  {
    $extension = explode(".", strtolower($action));
    if($extension[1] != "php") {
      throw new Exception("<strong>{$action}</strong> is not a valid value for the 'action' attribute");
    }
    $this->_action = strtolower($action);
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function getAction()
  {
    return $this->_action;
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function setMethod($method)
  {
    $method = strtolower($method);
    if($method != "get" && $method != "post") {
      throw new Exception("<strong>{$method}</strong> is not a valid value for the 'method' attribute");
    }
    $this->_method = $method;
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function getMethod()
  {
    return $this->_method;
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function setEnctype($enctype)
  {
    $enctype = strtolower($enctype);
    if($enctype != "text/plain" &&
       $enctype != "multipart/form-data" &&
       $enctype != "application/x-www-form-urlencoded") {
      throw new Exception("<strong>{$enctype}</strong> is not a valid value for the 'enctype' attribute");
    }
    $this->_enctype = $enctype;
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function getEnctype()
  {
    return $this->_enctype;
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function __construct($action, $method = NULL, $enctype = NULL)
  {
    $this->setAction($action);
    if(isset($method)) {
      $this->setMethod($method);
    }
    if(isset($enctype)) {
      $this->setEnctype($enctype);
    }
    self::$_instances++;
    $this->open();
  }
  //-------------------------------------------------------------------------------------------------------------------
  protected function open()
  {
    $this->_form[] = array('tag'    => 'form',
                           'status' => 'open',
                           'action' => $this->getAction());
    $method = $this->getMethod(); 
    if(isset($method)) {
      $this->_form[$this->index()] += array('method' => $method);
    }
    $enctype = $this->getEnctype();
    if(isset($enctype)) {
      $this->_form[$this->index()] += array('enctype' => $enctype);
    }
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function fieldset($id)
  {
    $n = count($this->_fieldsetIds);
    $i = 0;
    while ($i < $n && $this->_fieldsetIds[$i] != $id) {
      $i++;
    }
    if($i < $n) {
      $this->_form[] = array('tag'        => 'fieldset',
                             'status'     => 'close',
                             'fieldsetid' => $id);
      array_splice($this->_fieldsetIds, $i, 1);
    }
    else {
      $this->_form[] = array('tag'        => 'fieldset',
                             'status'     => 'open',
                             'fieldsetid' => $id);
      $this->_fieldsetIds[] = $id;
    }
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function legend($title)
  {
    $this->_form[] = array('tag'     => 'legend',
                           'status'  => 'open',
                           'content' => $title);
    $this->_form[] = array('tag'    => 'legend',
                           'status' => 'close');
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function label($name, $content)
  {
    $index = $this->index();
    if(isset($this->_form[$index]['name']) && $this->_form[$index]['name']== $name) {
      $this->_form[] = array('tag'     => 'label',
                             'status'  => 'open',
                             'for'     => $this->_form[$index]['id'],
                             'content' => $content);
    }
    else {
      $this->_form[] = array('tag'     => 'label',
                             'status'  => 'open',
                             'content' => $content);
    }
    $this->_form[] = array('tag'    => 'label',
                           'status' => 'close');
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function input($type, array $attributeValues)
  {
    $id = $this->id();
    $index = $this->index() - 1;
    if(isset($this->_form[$index]['tag']) &&
       $this->_form[$index]['tag'] == 'label' && $this->_form[$index]['status'] == 'open') {
      $this->_form[$index] += array('for' => $id);
    }
    $this->_form[] = array('tag'    => 'input',
                           'status' => 'empty',
                           'type'   => $type,
                           'id'     => $id);
    switch($type) {
      case "text":
      case "password":
        $attributeNames = array('name',
                                'value',
                                'maxlength',
                                'readonly',
                                'disabled');
      break;
      case "file":
        $attributeNames = array('name',
                                'value',
                                'accept',
                                'disabled');
      break;
      case "checkbox":
      case "radio":
        $attributeNames = array('name',
                                'value',
                                'checked',
                                'disabled');
      break;
      case "button":
      case "submit":
      case "reset":
        $attributeNames = array('name',
                                'value',
                                'disabled');
      break;
      case "hidden":
        $attributeNames = array('name',
                                'value');
      break;
      case "image":
        $attributeNames = array('name',
                                'src',
                                'value',
                                'alt',
                                'disabled');
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
  public function select($name, array $options, array $optGroups = NULL, $size = NULL, $multiple = NULL)
  {
    $this->_form[] = array('tag'    => 'select',
                           'status' => 'open',
                           'name'   => $name);
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
        $this->_form[] = array('tag'    => 'optgroup',
                               'status' => 'open',
                               'label'  => $optGroupNames[$i]);
        for($j = 0; $j < $optGroupLines[$i]; $j++) {
          $this->_form[] = array('tag'     => 'option',
                                 'status'  => 'open',
                                 'content' => $options[$offset]);
        $this->_form[] = array('tag'    => 'option',
                               'status' => 'close');
        $offset++;
        }
        $this->_form[] = array('tag'    => 'optgroup',
                               'status' => 'close');
      }
    }
    else {
      foreach($options as $tmp) {
        $this->_form[] = array('tag'     => 'option',
                               'status'  => 'open',
                               'content' => $tmp);
        $this->_form[] = array('tag'    => 'option',
                               'status' => 'close');
      }
    }
    $this->_form[] = array('tag'    => 'select',
                          'status' => 'close');
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function textarea($cols, $rows, $name, $content = NULL)
  {
    $this->_form[] = array('tag'    => 'textarea',
                           'status' => 'open',
                           'name'   => $name,
                           'cols'   => $cols,
                           'rows'   => $rows);
    if(isset($content)) {
      $this->_form[$this->index()] += array('content' => $content);
    }
    $this->_form[] = array('tag'    => 'textarea',
                           'status' => 'close');
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function button($content, $value, $type = "submit")
  {
    $this->_form[] = array('tag'     => 'button',
                           'status'  => 'open',
                           'type'    => $type,
                           'name'    => $type,
                           'value'   => $value,
                           'content' => $content);
    $this->_form[] = array('tag'    => 'button',
                           'status' => 'close');
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function html($content)
  {
    $this->_form[] = array('html' => $content);
  }
  //-------------------------------------------------------------------------------------------------------------------
  protected function close()
  {
    $n = count($this->_fieldsetIds);
    while($n > 0) {
      $id = array_pop($this->_fieldsetIds);
      $this->_form[] = array('tag'        => 'fieldset',
                             'status'     => 'close',
                             'fieldsetid' => $id);
      $n--;
    }
    $this->_form[] = array('tag'    => 'form',
                           'status' => 'close');
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function render()
  {
    $this->close();
    $tabs = array("",
                 "\t",
                 "\t\t",
                 "\t\t\t",
                 "\t\t\t\t",
                 "\t\t\t\t\t",
                 "\t\t\t\t\t\t",
                 "\t\t\t\t\t\t\t",
                 "\t\t\t\t\t\t\t\t");
    $i = 0;
    $j = 0;
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
              if($tagName == "form" || $tagName == "fieldset" || $tagName == "select" || $tagName == "optgroup") {
                $i = 0 < $i ? $i - 1 : 0;
              }
              else {
                $j = $i;
              }
              echo "{$tabs[$i-$j]}</{$tagName}";
              $j = 0;
            }
            else {
              echo "{$tabs[$i]}<{$tagName}";
              if($tagName == "form" || $tagName == "fieldset" || $tagName == "select" || $tagName == "optgroup") {
                $i = count($tabs) -1 > $i ? $i + 1 : count($tabs) -1;
              }
            }
          break;
          case "content":
            $contentValue = $value;
          break;
          case "html":
            echo "{$tabs[$i]}{$value}\n";
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
  protected function index()
  {
    return count($this->_form) - 1;
  }
  //-------------------------------------------------------------------------------------------------------------------
  protected function id()
  {
    return 'form-' . self::$_instances . '-' . count($this->_form);
  }
  //-------------------------------------------------------------------------------------------------------------------
  public function __toString()
  {
    return "<pre>\n" . print_r($this->_form, true) . "</pre>\n";
  }
  //-------------------------------------------------------------------------------------------------------------------
}
?>