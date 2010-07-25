<?php

require_once("config.autocomplete.php");

class AutoComplete
{
    //-----------------------------------------------------------------------------------------------------------------
    protected $_searchType;
    protected $_mysqli;
    protected $_response = array();
    protected $_responseXml;
    protected $_array = array();
    //-----------------------------------------------------------------------------------------------------------------
    public function setSearchType($value)
    {
        $value = strtolower($value);
        if($value != "xml" && $value != "db") {
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
    public function getArray($where, $type)
    {
        if($this->getSearchType() == "xml") {
            $this->readXml($where, $type);
        }
        if($this->getSearchType() == "db") {
            $this->readDb($where, $type);
        }
        return $this->_array;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function __construct($searchType = NULL)
    {
        if(empty($searchType)) {
            $this->setSearchType("xml");
        }
        else {
            $this->setSearchType($searchType);
        }
        if($this->getSearchType() == "db") {
            $this->_mysqli = @new mysqli(HOST, USERNAME, PASSWD, DBNAME);
            if ($this->_mysqli->connect_error) {
                throw new Exception("Error: " . $this->_mysqli->connect_error);
            }
            $this->_mysqli->set_charset("utf8");
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function __destruct()
    {
        if($this->getSearchType() == "db") {
            $this->_mysqli->close();
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function search($where, $type, $keyword)
    {
        if($this->getSearchType() == "xml") {
            $this->xmlSearch($where, $type, $keyword);
        }
        if($this->getSearchType() == "db") {
            $this->dbSearch($where, $type, $keyword);
        }
        return $this->createResponseXml();
    }
    //-----------------------------------------------------------------------------------------------------------------
    protected function dbSearch($table, $field, $keyword)
    {
        $keyword = $this->keywordFilter($keyword);
        if($keyword != "") {
            $keyword = $this->_mysqli->real_escape_string($keyword);
            $query = "SELECT {$field} FROM {$table} WHERE LOWER({$field}) REGEXP '^{$keyword}' ORDER BY {$field}";
            $result = $this->_mysqli->query($query);
            if($result->num_rows) {
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $this->_response[] = $row["{$field}"];
                }
            }
            $result->close();
        }
        else {
            $this->_response = NULL;
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    protected function xmlSearch($filename, $node, $keyword)
    {
        $this->readXml($filename, $node);
        $keyword = $this->keywordFilter($keyword);
        if($keyword != "") {
            foreach ($this->_array as $tmp) {
                if(mb_stripos($tmp, $keyword, NULL, "utf-8") !== FALSE &&
                   mb_stripos($tmp, $keyword, NULL, "utf-8") == 0) {
                    $this->_response[] = $tmp;
                }
            }
            sort($this->_response);
        }
        else {
            $this->_response = NULL;
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    protected function readDb($table, $field)
    {
        $query = "SELECT {$field} FROM {$table} ORDER BY {$field}";
        $result = $this->_mysqli->query($query);
        if($result->num_rows) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->_array[] = $row["{$field}"];
            }
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    protected function readXml($filename, $node)
    {
        if (!file_exists($filename)) {
            throw new Exception("Wrong file and/or path name, or non-exsiting file");
        }
        $xml = simplexml_load_file($filename);
        if(!@$xml->xpath("//{$node}")) {
            throw new Exception("Wrong XML node name, or non-exsiting node");
        }
        $result = $xml->xpath("//{$node}");
        foreach($result as $tmp) {
            $this->_array[] = (string)$tmp[0];
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    protected function keywordFilter($keyword)
    {
        $keyword = stripslashes($keyword);
        $patterns = array('/\s+/', '/"+/', "/'+/", '/\\\+/', '/\/+/');
        $replace = array("");
        return preg_replace($patterns, "", $keyword);
    }
    //-----------------------------------------------------------------------------------------------------------------
    protected function createResponseXml()
    {
        $this->_responseXml = '<?xml version="1.0" encoding="UTF-8"?>';
        $this->_responseXml .= '<response>';
        if(!empty($this->_response)) {
            foreach($this->_response as $tmp) {
                $this->_responseXml .= '<result>' . $tmp . '</result>';
            }
        }
        $this->_responseXml .= '</response>';
        return $this->_responseXml;
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function getText($where, $type, $keyword)
    {
        if($this->getSearchType() == "xml") {
            $this->xmlSearch($where, $type, $keyword);
        }
        if($this->getSearchType() == "db") {
            $this->dbSearch($where, $type, $keyword);
        }
        foreach ($this->_response as $tmp) {
            $output .= $tmp . "\n";
        }
        return $output;
    }
    //-----------------------------------------------------------------------------------------------------------------
}

?>
