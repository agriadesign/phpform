
/******************************/
/* version 0.2.9 @ 2010.03.13 */
/******************************/

var autoComplete = new function()
{
    //-----------------------------------------------------------------------------------------------------------------
     var _xhr = null;
     var _response = null;
     var _responseArray = new Array();
    //-----------------------------------------------------------------------------------------------------------------
    this.search = search;
    //-----------------------------------------------------------------------------------------------------------------
    autoComplete = function()
    {
        try {
            _xhr = new XMLHttpRequest();
        }
        catch(e) {
            var xhVersions = new Array("MSXML2.XMLHTTP.6.0",
                                       "MSXML2.XMLHTTP.5.0",
                                       "MSXML2.XMLHTTP.4.0",
                                       "MSXML2.XMLHTTP.3.0",
                                       "MSXML2.XMLHTTP",
                                       "Microsoft.XMLHTTP");
            for(var i = 0; i < xhVersions.length && !_xhr; i++) {
                try {
                    _xhr = new ActiveXObject(XhVersions[i]);
                }
                catch(e) {}
            }
        }
    }();
    //-----------------------------------------------------------------------------------------------------------------
    function responseCallBack()
    {
        if(_xhr.readyState == 4 && _xhr.status == 200) {
            try {
                _response = _xhr.responseText;
                _response = _xhr.responseXML.documentElement;
                _response = _response.getElementsByTagName("result");
                 for(var i = 0; i < _response.length; i++) {
                    _responseArray[i] = _response.item(i).firstChild.data;
                }
                document.getElementById("varosok").innerHTML = generateHTML();
            }
            catch(e) {}
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    function search(search, where, type, keyword)
    {
        if(_xhr) {
            try {
                _xhr.open("GET", "autocomplete.php?search=" +  search +
                                                "&where=" +   where +
                                                "&type=" +    type +
                                                "&keyword=" + encodeURIComponent(keyword), true);
                _xhr.onreadystatechange = responseCallBack;
                _xhr.send(null);
            }
            catch(e) {}
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    function generateHTML()
    {
        var output = "<ul>";
        for(var i = 0; i < _responseArray.length; i++) {
            output +="<li>" + _responseArray[i] + "</li>";
        }
        return (output += "</ul>");
    }
    //-----------------------------------------------------------------------------------------------------------------
}

autoComplete.search("xml", "zipcodes", "name", "pilis");