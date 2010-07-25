<?php

class JavaScript
{
    //-----------------------------------------------------------------------------------------------------------------
    protected $_formId;
    protected $_checks = array();
    protected $_masks = array();
    protected $_autoCompletes = array();
    protected $_messageType;
    protected $_testMode;
    //-----------------------------------------------------------------------------------------------------------------
    public function setFormId($value)
    {
        $this->_formId = $value;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function getFormId()
    {
        return $this->_formId;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function setMessageType($value)
    {
        if($value == 1) {
            $value = "next";
        }
        if($value == 2) {
            $value = "bottom";
        }
        $value = strtolower($value);
        if($value != "alert" &&
           $value != "next" &&
           $value != "bottom") {
            throw new Exception("<strong>{$value}</strong> is not a valid value for the 'messageType' attribute");
        }
            $this->_messageType = $value;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function getMessageType()
    {
        return $this->_messageType;
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
    public function __construct($formId, $messageType = NULL)
    {
        $this->setFormId($formId);
        if(empty($messageType)) {
            $this->setMessageType("alert");
        }
        else {
            $this->setMessageType($messageType);
        }
        $this->setTestMode(FALSE);
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function mask($id, $type)
    {
        $this->_masks[] = array('id'   => $id,
                                 'type' => $type);
    }    
    //-----------------------------------------------------------------------------------------------------------------
    public function validate($id, $type)
    {
        $this->_checks[] = array('id'   => $id,
                                 'type' => $type);
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function autoComplete($search, $where, $type, $id, $instance)
    {
        $this->_autoCompletes[] = array('search'    => $search,
                                        'where'     => $where,
                                        'type'      => $type,
                                        'id'        => $id,
                                        'instance'  => $instance);
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
        $output = "";
        $emptyChecks = empty($this->_checks);
        $emptyMasks = empty($this->_masks);
        $emptyAutoCompletes = empty($this->_autoCompletes);
        if(!$emptyChecks || !$emptyMasks || !$emptyAutoCompletes) {
            $id = $this->getFormId();
            $output .= $lt . 'script type="text/javascript"' . $gt . "\n" . '//' . $lt . '![CDATA[' . "\n";
            if(!$emptyMasks) {
                $m = "im" . $id;
                $output .= 'var ' . $m . ' = new InputMask();' . "\n";
                foreach($this->_masks as $tmp) {
                    $output .= $m . '.mask("' . $tmp['id'] . '", "' . $tmp['type'] . '");' . "\n";
                }
            }
            if(!$emptyChecks) {
                $v = "val" . $id;
                $output .= 'document.forms["form-' . $id . '-0"].onsubmit = function()' . "\n" . '{' . "\n";
                $output .= "\t" . 'var ' . $v . ' = new Validator("' . $this->getMessageType() . '");' . "\n";
                foreach($this->_checks as $tmp) {
                    $output .= "\t" . $v . '.validate("' . $tmp['id'] . '", "' . $tmp['type'] . '");' . "\n";
                }
                $output .= "\t" . 'return ' . $v . '.validateForm();' . "\n";
                $output .= '};' . "\n";
            }
            if(!$emptyAutoCompletes) {
                $output .= '$().ready(function() {' . "\n";
                foreach($this->_autoCompletes as $tmp) {
                    $output .= "\t" . '$("#' . $tmp['id'] . '").autocomplete("../phpform/jquery.php?search=' .
                    $tmp['search'] . '&where=' . $tmp['where'] . '&type=' . $tmp['type'] . '");' . "\n";
                }
                /*$output .= 'var xhr = xmlHttpRequestObject.init();' . "\n";
                foreach($this->_autoCompletes as $tmp) {
                    $ac = "ac" . $id . $tmp['instance'];
                    $output .= 'var ' . $ac . ' = new AutoComplete(xhr, "' . $tmp['search'] . '", "' .
                                                                             $tmp['where'] . '", "' .
                                                                             $tmp['type'] . '", "' .
                                                                             $tmp['id'] . '", "' .
                                                                             $ac . '");' . "\n";
                }*/
            $output .= '});' . "\n";
            }
            $output .= '//]]' . $gt . "\n" . $lt . '/script' . $gt . "\n";
            echo $output;
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function __toString()
    {
        return print_r(array_merge($this->_checks, $this->_masks, $this->_autoCompletes), TRUE);
    }
    //-----------------------------------------------------------------------------------------------------------------
}

?>
