<?php

/******************************/
/* version 0.2.6 @ 2010.03.10 */
/******************************/

require_once("config.autocomplete.php");

class autoComplete
{
    //-----------------------------------------------------------------------------------------------------------------
    protected $_mysqli;
    protected $_response;
    //-----------------------------------------------------------------------------------------------------------------
    function __construct()
    {
        $this->_mysqli = new mysqli(HOST, USERNAME, PASSWD, DBNAME);
        $this->_mysqli->set_charset("utf8");
    }
    //-----------------------------------------------------------------------------------------------------------------
    function __destruct()
    {
        $this->_mysqli->close();
    }
    //-----------------------------------------------------------------------------------------------------------------
    public function search($table, $field, $keyword)
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
        $this->_response .= '<?xml version="1.0" encoding="utf-8"?>';
        $this->_response .= '<response>';
        if($result->num_rows) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->_response .= '<result>' . $row['city'] . '</result>';
            }
        }
        $result->close();
        $this->_response .= '</response>';
        return $this->_response;
    }
    //-----------------------------------------------------------------------------------------------------------------
}
?>
