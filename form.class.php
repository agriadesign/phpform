<?php

/******************************/
/* version 0.1.4 @ 2009.10.28 */
/******************************/

class Form
{
    //-----------------------------------------------------------------------------------------------------------------
    protected $_action;
    protected $_method;
    protected $_enctype;
    protected $_fieldsetId = array();
    protected $_form = array();
    protected static $_instanceCounter = 0;
    //-----------------------------------------------------------------------------------------------------------------
    public function setAction($value)
    {
        $extension = explode(".", strtolower($value));
        if($extension[1] != "php") {
            throw new Exception("<strong>{$value}</strong> is not a valid value for the 'action' attribute");
        }
        $this->_action = $value;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function getAction()
    {
        return $this->_action;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function setMethod($value)
    {
        $value = strtolower($value);
        if($value != "get" && $value != "post") {
            throw new Exception("<strong>{$value}</strong> is not a valid value for the 'method' attribute");
        }
        $this->_method = $value;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function getMethod()
    {
        return $this->_method;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function setEnctype($value)
    {
        $value = strtolower($value);
        if($value != "text/plain" &&
           $value != "multipart/form-data" &&
           $value != "application/x-www-form-urlencoded") {
            throw new Exception("<strong>{$value}</strong> is not a valid value for the 'enctype' attribute");
        }
        $this->_enctype = $value;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function getEnctype()
    {
        return $this->_enctype;
    }
    //-----------------------------------------------------------------------------------------------------------------
    protected function setInstanceCounter($value)
    {
        self::$_instanceCounter = $value;
    }
    //-----------------------------------------------------------------------------------------------------------------
    protected function getInstanceCounter()
    {
        return self::$_instanceCounter;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function __construct($action, $method = NULL, $enctype = NULL)
    {
        $this->setAction($action);
        if(isset($method)) {
            $this->setMethod($method);
        }
        if(isset($enctype)) {
            $this->setEnctype($enctype);
        }
        $this->setInstanceCounter($this->getInstanceCounter() + 1);
        $this->open();
    }
    //-----------------------------------------------------------------------------------------------------------------
    protected function open()
    {
        $this->_form[] = array('tag'    => 'form',
                               'status' => 'open',
                               'id'     => $this->id(),
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
    //-----------------------------------------------------------------------------------------------------------------
    public function fieldset($id)
    {
        if(in_array($id, $this->_fieldsetId)) {
            $this->_form[] = array('tag'        => 'fieldset',
                                   'status'     => 'close',
                                   'fieldsetid' => $id);
            $index = array_search($id, $this->_fieldsetId);
            array_splice($this->_fieldsetId, $index, 1);
        }
        else {
            $this->_form[] = array('tag'        => 'fieldset',
                                   'status'     => 'open',
                                   'fieldsetid' => $id);
            $this->_fieldsetId[] = $id;
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function legend($title)
    {
        $this->_form[] = array('tag'     => 'legend',
                               'status'  => 'open',
                               'content' => $title);
        $this->_form[] = array('tag'    => 'legend',
                               'status' => 'close');
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function label($name, $content)
    {
        $this->_form[] = array('tag'     => 'label',
                               'status'  => 'open',
                               'for'     => $name,
                               'content' => $content);
        $this->_form[] = array('tag'    => 'label',
                               'status' => 'close');
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function input($type, array $attributeValues)
    {
        $this->_form[] = array('tag'    => 'input',
                               'status' => 'empty',
                               'type'   => $type,
                               'id'     => $this->id());
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
        $names = count($attributeNames);
        $values = count($attributeValues);
        if($values > $names) {
            $attributeValues = array_slice($attributeValues, 0, $names);
        }
        if($values < $names) {
            $attributeNames = array_slice($attributeNames, 0, $values);
        }
        if(!empty($attributeNames)) {
            $attributes = array_combine($attributeNames, $attributeValues);
            foreach($attributes as $key => $value) {
                if(!empty($value)) $this->_form[$this->index()] += array($key => $value);
            }
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function select($name, array $options, $selected = NULL, array $optGroups = NULL,
                           $size = NULL, $multiple = NULL, array $multipleSelected = NULL)
    {
        $this->_form[] = array('tag'    => 'select',
                               'status' => 'open',
                               'id'     => $this->id(),
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
                                       'id'     => $this->id(),
                                       'label'  => $optGroupNames[$i]);
                for($j = 0; $j < $optGroupLines[$i]; $j++) {
                    $this->_form[] = array('tag'     => 'option',
                                           'status'  => 'open',
                                           'content' => $options[$offset]);
                    if(isset($multiple) && isset($multipleSelected) &&
                             in_array($options[$offset], $multipleSelected)) {
                       $this->_form[$this->index()] += array('selected' => 'selected');
                    }
                    elseif(isset($selected) && $selected  == $options[$offset]) {
                        $this->_form[$this->index()] += array('selected' => 'selected');
                    }
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
                if(isset($multiple) && isset($multipleSelected) && in_array($tmp, $multipleSelected)) {
                    $this->_form[$this->index()] += array('selected' => 'selected');
                }
                elseif(isset($selected) && $selected  == $tmp) {
                      $this->_form[$this->index()] += array('selected' => 'selected');
                }
                $this->_form[] = array('tag'    => 'option',
                                       'status' => 'close');
            }
        }
        $this->_form[] = array('tag'    => 'select',
                               'status' => 'close');
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function textarea($cols, $rows, $name, $content = NULL)
    {
        $this->_form[] = array('tag'    => 'textarea',
                               'status' => 'open',
                               'id'     => $this->id(),
                               'name'   => $name,
                               'cols'   => $cols,
                               'rows'   => $rows);
        if(isset($content)) {
            $this->_form[$this->index()] += array('content' => $content);
        }
        $this->_form[] = array('tag'    => 'textarea',
                               'status' => 'close');
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function button($content, $value, $type = "submit")
    {
        $this->_form[] = array('tag'     => 'button',
                               'status'  => 'open',
                               'id'      => $this->id(),
                               'type'    => $type,
                               'name'    => $type,
                               'value'   => $value,
                               'content' => $content);
        $this->_form[] = array('tag'    => 'button',
                               'status' => 'close');
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function html($content)
    {
        $this->_form[] = array('html' => $content);
    }
    //-----------------------------------------------------------------------------------------------------------------
    protected function close()
    {
        $n = count($this->_fieldsetId);
        while($n > 0) {
            $id = array_pop($this->_fieldsetId);
            $this->_form[] = array('tag'        => 'fieldset',
                                   'status'     => 'close',
                                   'fieldsetid' => $id);
            $n--;
        }
        $this->_form[] = array('tag'    => 'form',
                               'status' => 'close');
        $this->replaceForValues();
    }
    //-----------------------------------------------------------------------------------------------------------------
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
                        if($tagName == "form"   || $tagName == "fieldset" ||
                           $tagName == "select" || $tagName == "optgroup") {
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
                        if($tagName == "form"   || $tagName == "fieldset" ||
                           $tagName == "select" || $tagName == "optgroup") {
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
    //-----------------------------------------------------------------------------------------------------------------
    protected function index()
    {
        return count($this->_form) - 1;
    }
    //-----------------------------------------------------------------------------------------------------------------
    protected function id()
    {
        return 'form-' . $this->getInstanceCounter() . '-' . count($this->_form);
    }
    //-----------------------------------------------------------------------------------------------------------------
    protected function replaceForValues()
    {
        $count = count($this->_form);
        for($i = 0; $i < $count; $i++) {
            if(isset($this->_form[$i]['for'])) {
                if(isset($this->_form[$i - 1]['name']) &&
                   isset($this->_form[$i - 1]['id']) &&
                   $this->_form[$i - 1]['name'] == $this->_form[$i]['for']) {
                    $this->_form[$i]['for'] = $this->_form[$i - 1]['id'];
                }
                if(isset($this->_form[$i + 2]['name']) &&
                   isset($this->_form[$i + 2]['id']) &&
                   $this->_form[$i + 2]['name'] == $this->_form[$i]['for']) {
                    $this->_form[$i]['for'] = $this->_form[$i + 2]['id'];
                }
            } 
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function __toString()
    {
        return "<pre>\n" . print_r($this->_form, TRUE) . "</pre>\n";
    }
    //-----------------------------------------------------------------------------------------------------------------
}
?>