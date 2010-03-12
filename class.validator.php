<?php

/******************************/
/* version 0.2.8 @ 2010.03.12 */
/******************************/

class Validator
{
    //-----------------------------------------------------------------------------------------------------------------
    protected $_formId;
    protected $_checks = array();
    protected $_masks = array();
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
    public function __construct($formId)
    {
        $this->setFormId($formId);
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
    public function render()
    {
        $id = $this->getFormId();
        $v = "val" . $id;
        echo '<script type="text/javascript">' . "\n" . '//<![CDATA[' . "\n";
        echo 'var ' . $v . ' = new Validator();' . "\n";
        foreach($this->_masks as $tmp) {
            echo $v . '.mask("' . $tmp['id'] . '", "' . $tmp['type'] . '");' . "\n";
		}
        echo 'document.forms["form-' . $id . '-0"].onsubmit = function()' . "\n" . '{' . "\n";
        foreach($this->_checks as $tmp) {
            echo "\t" . $v . '.validate("' . $tmp['id'] . '", "' . $tmp['type'] . '");' . "\n";
		}
        echo "\t" . 'return ' . $v . '.validateForm();' . "\n";
        echo '};' . "\n";
        echo '//]]>' . "\n" . '</script>' . "\n";
    }
    //-----------------------------------------------------------------------------------------------------------------
}
?>
