var xmlHttpRequestObject = new function()
{
    //-----------------------------------------------------------------------------------------------------------------
    this.init = init;
    //-----------------------------------------------------------------------------------------------------------------
    function init()
    {
        try {
            xhr = new XMLHttpRequest();
        }
        catch(e) {
            var xhVersions = new Array("MSXML2.XMLHTTP.6.0",
                                       "MSXML2.XMLHTTP.5.0",
                                       "MSXML2.XMLHTTP.4.0",
                                       "MSXML2.XMLHTTP.3.0",
                                       "MSXML2.XMLHTTP",
                                       "Microsoft.XMLHTTP");
            for(var i = 0; i < xhVersions.length && !xhr; i++) {
                try {
                    xhr = new ActiveXObject(XhVersions[i]);
                }
                catch(e) {}
            }
        }
        if (xhr) {
            return xhr;
        }
        else {
            return null;
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
}

function AutoComplete(xhr, search, where, type, id, instance)
{
    //-----------------------------------------------------------------------------------------------------------------
    var _response = null;
    var _resultArray = new Array();
    var _keyword = document.getElementById(id);
    var _cacheObject = new Object();
    var _timer = setTimeout(checkForChanges, 500);
    var _resultId = id + "-ac";
    var _listId = id + "-list";
    var _requestedKeyword = "";
    var _typedKeyword = "";
    var _autocompletedKeyword = "";
    var _suggestions = 0;
    var _keyPressed = false;
    var _hasResult = false;
    var _position = -1;
    var _minPosition = 0;
    var _maxPosition = 9;
    var createDiv = function()
    {
        var newNode = document.createElement("div");
        newNode.id = _resultId;
        newNode.setAttribute("class", "autocomplete");
        document.getElementById(id).parentNode.insertBefore(newNode, document.getElementById(id).nextSibling);
        var newList = document.createElement("div");
        newList.id = _listId;
        document.getElementById(_resultId).appendChild(newList);
    }();
    var _scroll = document.getElementById(_resultId);
    var _suggest = document.getElementById(_listId);
    _keyword.setAttribute("autocomplete", "off");
    _keyword.onkeyup = handleKeyUp;
    document.body.onclick = hideSuggestions;
    document.body.onkeypress = disableEnter;
    //-----------------------------------------------------------------------------------------------------------------
    this.handleOnMouseOver = handleOnMouseOver;
    this.handleOnMouseOut = handleOnMouseOut;
    this.handleOnClick = handleOnClick;
    //-----------------------------------------------------------------------------------------------------------------
    function getSuggestions(keyword) 
    {
        if(keyword != "" && !_keyPressed) {
            var isInCache = checkCache(keyword);
            if(isInCache == true) {
                _requestedKeyword = keyword;
                _typedKeyword = keyword;
                displayResults(keyword, _cacheObject[keyword]);
            }
            else {
                if(xhr) {
                    try {
                        if (xhr.readyState == 4 || xhr.readyState == 0) {
                            _requestedKeyword = keyword;
                            _typedKeyword = keyword;
                            xhr.open("GET", "javascript/autocomplete.php?search=" +  search +
                                                                         "&where=" +   where +
                                                                         "&type=" +    type +
                                                                         "&keyword=" + keyword, true);
                            xhr.onreadystatechange = responseCallBack;
                            xhr.send(null);
                        }
                        else {
                            _typedKeyword = keyword;
                            clearTimeout(_timer);
                            _timer = setTimeout(function() { getSuggestions(_typedKeyword); }, 500);
                        }
                    }
                    catch(e) {}
                }
            }
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    function responseCallBack()
    {
        if(xhr.readyState == 4 && xhr.status == 200) {
            try {
                updateSuggestions();
            }
            catch(e) {}
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    function updateSuggestions()
    {
        _response = xhr.responseText;
        if(_response.indexOf("ERRNO") >= 0 || _response.indexOf("error:") >= 0 || _response.length == 0) {
            throw(_response.length == 0 ? "Empty response" : _response);
        }
        _response = xhr.responseXML.documentElement;
        var resultArray = new Array();
        if(_response.childNodes.length) {
            _response = _response.getElementsByTagName("result");
            for(var i = 0; i < _response.length; i++) {
                resultArray[i] = _response.item(i).firstChild.data;
            }
        }
        if(_requestedKeyword == _typedKeyword) {
            displayResults(_requestedKeyword, resultArray);
        }
        else {
            addToCache(_requestedKeyword, resultArray);
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    function displayResults(keyword, resultArray)
    {
        var output = '<ul>';
        if(!_cacheObject[keyword] && keyword) {
            addToCache(keyword, resultArray);
        }
        if(resultArray.length == 0) {
            output += '<li>Nincs találat: <strong>' + keyword + '</strong></li>';
            _hasResult = false;
            _suggestions = 0;
        }
        else {
            _position = -1;
            _keyPressed = false;
            _hasResult = true;
            _suggestions = _cacheObject[keyword].length;
            for(var i = 0; i < _cacheObject[keyword].length; i++) {
                var listItem = _cacheObject[keyword][i];
                output += '<li id="item' + i + '" onclick="' + instance + '.handleOnClick(this);"' +
                          'onmouseover="' + instance + '.handleOnMouseOver(this);" ' +
                          'onmouseout="' + instance + '.handleOnMouseOut(this);">';
                if(listItem.length <= 50) {
                    output += '<strong>' + listItem.substring(0, _requestedKeyword.length) + '</strong>';
                    output += listItem.substring(_requestedKeyword.length, listItem.length) + '</li>';
                }
                else {
                    if(_requestedKeyword.length < 50) {
                        output += '<strong>' + listItem.substring(0, _requestedKeyword.length) + '</strong>';
                        output += listItem.substring(_requestedKeyword.length, 50) + '</li>';
                    }
                    else {
                        output += '<strong>' + listItem.substring(0, 50) + '</strong></li>';
                    }
                }
            }
        }
        output += '</ul>';
        _scroll.scrollTop = 0;
        _suggest.innerHTML = output;
        _scroll.style.visibility = 'visible';
        if(resultArray.length > 0) {
            autocompleteKeyword();
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    function checkForChanges()
    {
        var keyword = _keyword.value;
        if(keyword == "") {
            hideSuggestions();
            _typedKeyword = "";
            _requestedKeyword = "";
        }
        if((_typedKeyword != keyword) && (_autocompletedKeyword != keyword) && (!_keyPressed)) {
            getSuggestions(keyword);
        }
        _timer = setTimeout(checkForChanges, 500);
    }
    //-----------------------------------------------------------------------------------------------------------------
    function addToCache(keyword, values)
    {
        _cacheObject[keyword] = new Array();
        for(var i = 0; i < values.length; i++) {
            _cacheObject[keyword][i] = values[i];
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    function checkCache(keyword)
    {
        if(_cacheObject[keyword]) {
            return true;
        }
        for(var i = keyword.length - 2; i >= 0; i--) {
            var currentKeyword = keyword.substring(0, i + 1);
            if(_cacheObject[currentKeyword]) {
                var cacheResults = _cacheObject[currentKeyword];
                var keywordResults = new Array();
                var keywordResultsSize = 0;
                for(var j = 0; j < cacheResults.length; j++) {
                    if(cacheResults[j].indexOf(keyword) == 0) {
                        keywordResults[keywordResultsSize++] = cacheResults[j];
                    }
                }
                addToCache(keyword, keywordResults);
                return true;
            }
        }
        return false;
    }
    //-----------------------------------------------------------------------------------------------------------------
    function getTarget(e)
    {
        if(e.target) {
            return e.target;
        }
        else if(e.srcElement) {
            return e.srcElement;
        }
        if(target.nodeType == 3) {
            return target.parentNode;
        }
        return null;
    }
    //-----------------------------------------------------------------------------------------------------------------
    function handleKeyUp(e)
    {
        if (!e) {
            var e = window.event;
        }
        var target = getTarget(e);
        var code = (e.charCode) ? e.charCode : ((e.keyCode) ? e.keyCode : ((e.which) ? e.which : 0));
        if(e.type == "keyup") {
            _keyPressed =false;
            if((code < 13 && code != 8) || (code >=14 && code < 32) ||
              (code >= 33 && code <= 46 && code != 38 && code != 40) || (code >= 112 && code <= 123)) {
            }
            else if(code == 13) {
                    hideSuggestions();
            }
            else if(code == 40) {
                var newItem = document.getElementById("item" + (++_position));
                var oldItem = document.getElementById("item" + (--_position));
                if(_position >= 0 && _position < _suggestions - 1) {
                    oldItem.className = "";
                }
                if(_position < _suggestions - 1)  {
                    newItem.className = "highlight";
                    updateKeywordValue(newItem);
                    _position++;
                }
                e.cancelBubble = true;
                e.returnValue = false;
                _keyPressed = true;
                if(_position > _maxPosition) {
                    _scroll.scrollTop += 14;
                    _maxPosition += 1;
                    _minPosition += 1;
                }
            }
            else if(code == 38) {
                newItem = document.getElementById("item" + (--_position));
                oldItem = document.getElementById("item" + (++_position));
                if(_position >= 0 && _position <= _suggestions - 1) {
                    oldItem.className = "";
                }
                if(_position > 0) {
                    newItem.className = "highlight";
                    updateKeywordValue(newItem);
                    _position--;
                    if(_position < _minPosition) {
                        _scroll.scrollTop -= 14;
                        _maxPosition -= 1;
                        _minPosition -= 1;
                    }
                }
                else if(_position == 0) {
                    _position--;
                }
                e.cancelBubble = true;
                e.returnValue = false;
                _keyPressed = true;
            }
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    function handleOnMouseOver(listItem)
    {
        deselectAll();
        listItem.className = "highlight";
        _position = listItem.id.substring(4, listItem.id.length);
    }
    //-----------------------------------------------------------------------------------------------------------------
    function handleOnMouseOut(listItem)
    {
        listItem.className = "";
        _position = -1;
    }
    //-----------------------------------------------------------------------------------------------------------------
    function handleOnClick(listItem)
    {
        hideSuggestions();
        updateKeywordValue(listItem);
        _typedKeyword = _keyword.value;
    }
    //-----------------------------------------------------------------------------------------------------------------
    function autocompleteKeyword()
    {
          _position = 0;
          deselectAll();
          var current = document.getElementById("item0");
          current.className = "highlight";
          updateKeywordValue(current);
          selectRange(_keyword, _requestedKeyword.length, _keyword.value.length);
          _autocompletedKeyword = _keyword.value;
    }
    //-----------------------------------------------------------------------------------------------------------------
    function updateKeywordValue(listItem)
    {
        var current = listItem.innerHTML;
        current = current.replace(/<[//]{0,1}(strong)[^><]*>/ig,"");
        _keyword.value = current;
    }
    //-----------------------------------------------------------------------------------------------------------------
    function selectRange(text, start, length)
    {
        if(text.createTextRange) {
            var range = text.createTextRange();
            range.moveStart("character", start);
            range.moveEnd("character", length - text.value.length);
            range.select();
        }
        else {
            if(text.setSelectionRange) {
                text.setSelectionRange(start, length);
            }
        }
        text.focus();
    }
    //-----------------------------------------------------------------------------------------------------------------
    function deselectAll()
    {
        for(var i = 0; i < _suggestions; i++) {
            document.getElementById("item" + i).className = "";
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    function hideSuggestions()
    {
        _scroll.style.visibility = "hidden";
    }
    //-----------------------------------------------------------------------------------------------------------------
    function disableEnter(e)
    {
        var key;
        if(window.event) {
            key = window.event.keyCode;
        }
        else {
            key = e.which;
        }
        return (key != 13);
    }
    //-----------------------------------------------------------------------------------------------------------------
}
