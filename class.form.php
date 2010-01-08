<?php

/******************************/
/* version 0.2.0 @ 2010.01.08 */
/******************************/

class Form
{
    //-----------------------------------------------------------------------------------------------------------------
    protected $_action;
    protected $_method;
    protected $_enctype;
    protected $_XHTML;
    protected $_validation;
    protected $_validator;
    protected $_testMode;
    protected $_form = array();
    protected $_fieldsetId = array();
    protected static $_instanceCounter = 0;
    //-----------------------------------------------------------------------------------------------------------------
    public function setAction($value)
    {
        if(!empty($value)) {
            $extension = explode(".", $value, 10);
            if(array_pop($extension) != "php") {
                throw new Exception("<strong>{$value}</strong> is not a valid value for the 'action' attribute");
            }
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
        if($value == 1) {
            $value = "post";
        }
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
        if($value == 1) {
            $value = "multipart/form-data";
        }
        if($value == 2) {
            $value = "text/plain";
        }
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
    public function setXHTML($value)
    {
        if(strtolower($value) == "xhtml" || $value == 1) {
            $value = TRUE;
        }
        if(strtolower($value) == "html") {
            $value = FALSE;
        }
        if (!is_bool($value)) {
            throw new Exception("<strong>{$value}</strong> is not a valid value for the 'XHTML' attribute");
        }
        $this->_XHTML =  $value;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function getXHTML()
    {
        return $this->_XHTML;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function setValidation($value)
    {
    if (!is_bool($value)) {
            throw new Exception("<strong>{$value}</strong> is not a valid value for the 'validation' attribute");
        }
        $this->_validation =  $value;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function getValidation()
    {
        return $this->_validation;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function setTestMode($value)
    {
        if($value == 0) {
            $value = FALSE;
        }
        if($value == 1) {
            $value = TRUE;
        }
        if (!is_bool($value)) {
            throw new Exception("<strong>{$value}</strong> is not a valid value for the 'testMode' attribute");
        }
        $this->_testMode =  $value;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function getTestMode()
    {
        return $this->_testMode;
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
    public function __construct($action = NULL, $method = NULL, $enctype = NULL, $XHTML = NULL, $validation = NULL)
    {
        if(empty($action)) {
            $this->setAction("");
        }
        else {
            $this->setAction($action);
        }
        if(empty($method)) {
            $this->setMethod("get");
        }
        else {
            $this->setMethod($method);
        }
        if(empty($enctype)) {
            $this->setEnctype("application/x-www-form-urlencoded");
        }
        else {
            $this->setEnctype($enctype);
        }
        if(empty($XHTML)) {
            $this->setXHTML(FALSE);
        }
        else {
            $this->setXHTML($XHTML);
        }
        $this->setInstanceCounter($this->getInstanceCounter() + 1);
        if(empty($validation)) {
            $this->setValidation(FALSE);
        }
        else {
            $this->setValidation($validation);
        }
        $this->_validator = new Validator($this->getInstanceCounter());
        $this->setTestMode(FALSE);
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
        if($method != "get") {
            $this->_form[$this->index()] += array('method' => $method);
        }
        $enctype = $this->getEnctype();
        if($enctype != "application/x-www-form-urlencoded") {
            $this->_form[$this->index()] += array('enctype' => $enctype);
        }
        if($this->getValidation()) {
            $this->_form[$this->index()] +=
                array('onsubmit' => 'return validator' . $this->getInstanceCounter() . '();');
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function fieldset($id = NULL)
    {
        if(!isset($id)) {
            $id = array_pop($this->_fieldsetId);
            $this->_form[] = array('tag'        => 'fieldset',
                                   'status'     => 'close',
                                   'fieldsetid' => $id);
        }
        else {
            $this->_form[] = array('tag'        => 'fieldset',
                                   'status'     => 'open',
                                   'fieldsetid' => $id);
            $this->_fieldsetId[] = $id;
            if(!$this->getXHTML()) {
                $this->legend($id);
            }
            else {
                if(!empty($id) && is_string($id)) {
                    $this->legend($id);
                }
            }
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    protected function legend($title)
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
                if(!empty($value)) {
                    $this->_form[$this->index()] += array($key => $value);
                }
            }
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function select($name, array $options, $selected = NULL,
                           array $optGroups = NULL, $size = NULL, $multiple = NULL)
    {
        if(!isset($multiple) && is_array($selected)) {
            $selected = $selected[0];
        }
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
                    if(isset($selected)) {
                        $this->selectedMarker($options[$offset], $selected, $multiple);
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
                if(isset($selected)) {
                    $this->selectedMarker($tmp, $selected, $multiple);
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
        $this->_form[] = array('tag'    => 'form',
                               'status' => 'close');
        $this->replaceForValues();
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function render()
    {
        if($this->getTestMode()) {
            $lt = "&lt;";
            $gt = "&gt;";
        }
        else {
            $lt = "<";
            $gt = ">";
        }
        $this->close();
        $tabs = array("",
                      "\t",
                      "\t\t",
                      "\t\t\t",
                      "\t\t\t\t",
                      "\t\t\t\t\t",
                      "\t\t\t\t\t\t",
                      "\t\t\t\t\t\t\t",
                      "\t\t\t\t\t\t\t\t",
                      "\t\t\t\t\t\t\t\t\t",
                      "\t\t\t\t\t\t\t\t\t\t");
        $i = 0;
        $j = 0;
        $k = 0;
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
                        echo "{$tabs[$i-$j]}{$lt}/{$tagName}";
                        $j = 0;
                    }
                    else {
                        echo "{$tabs[$i]}{$lt}{$tagName}";
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
                if($contentValue == "") {
                    if($tagArray['status'] == "empty") {
                        echo " /{$gt}\n";
                    }
                    else {
                        if($this->_form[$k]['tag'] == $this->_form[$k + 1]['tag'] &&
                           $this->_form[$k]['status'] == "open" && $this->_form[$k + 1]['status'] == "close") {
                            echo "{$gt}";
                        }
                        else {
                            echo "{$gt}\n";
                        }
                    }
                }
                else {
                    echo "{$gt}{$contentValue}";
                }
                $contentValue = "";
            }
            $k++;
        }
        if($this->getValidation()) {
            $this->_validator->render();
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function validate($type)
    {
        $id = 'form-' . $this->getInstanceCounter() . '-' . $this->index();
        $this->_validator->validate($id, $type);
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
    protected function selectedMarker($option, $selected, $multiple = NULL)
    {
        if(isset($multiple) && is_array($selected)) {
            if(in_array($option, $selected)) {
                $this->_form[$this->index()] += array('selected' => 'selected');
            }
        }
        else {
            if($selected == $option) {
                $this->_form[$this->index()] += array('selected' => 'selected');
            }
        }    
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function __toString()
    {
        return print_r($this->_form, TRUE);
    }
    //-----------------------------------------------------------------------------------------------------------------
}
?>