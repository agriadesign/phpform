<?php

/******************************/
/* version 0.2.7 @ 2010.03.11 */
/******************************/

require_once("config.autocomplete.php");

class autoComplete
{
    //-----------------------------------------------------------------------------------------------------------------
    protected $_searchType;
    protected $_mysqli;
    protected $_responseXml;
    //-----------------------------------------------------------------------------------------------------------------
    public function setSearchType($value)
    {
        $value = strtolower($value);
        if($value == "db") {
            $value = 1;
        }
        if($value != 1) {
            throw new Exception("<strong>{$value}</strong> is not a valid value for the 'searchType' attribute");
        }
        $this->_searchType = $value;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function getSearchType()
    {
        return $this->_searchType;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function __construct($searchType = 1)
    {
        $this->setSearchType($searchType);
        if($this->getSearchType() == 1) {
            $this->_mysqli = new mysqli(HOST, USERNAME, PASSWD, DBNAME);
            $this->_mysqli->set_charset("utf8");
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function __destruct()
    {
        if($this->getSearchType() == 1) {
            $this->_mysqli->close();
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function search($where, $type, $keyword)
    {
        if($this->getSearchType() == 1) {
            return $this->dbSearch($where, $type, $keyword);
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    protected function dbSearch($table, $field, $keyword)
    {
        $patterns = array('/\s+/', '/"+/', '/%+/');
        $replace = array("");
        $keyword = preg_replace($patterns, $replace, $keyword);
        if($keyword != "") {
            $query = "SELECT $field FROM $table WHERE $field LIKE '" . $keyword  . "%'";
        }
        else {
            $query = "SELECT $field FROM $table WHERE $field=''";
        }
        $result = $this->_mysqli->query($query);
        $this->_responseXml = '<?xml version="1.0" encoding="UTF-8"?>';
        $this->_responseXml .= '<response>';
        if($result->num_rows) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
               $this->_responseXml .= '<result>' . $row["$field"] . '</result>';
            }
        }
        $result->close();
        $this->_responseXml .= '</response>';
        return $this->_responseXml;
    }
    //-----------------------------------------------------------------------------------------------------------------
}
?>
