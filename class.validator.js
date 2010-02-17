
/******************************/
/* version 0.2.3 @ 2010.02.17 */
/******************************/

function Validator()
{
    //-----------------------------------------------------------------------------------------------------------------
    var _elements = Array();
    var _errors = Array();
    var _errorType = "hibás";
    var _patterns = Array();
    var _keyboards = /[\x08\x0D\x23\x24\x25\x26\x27\x28\x2D\x2E]/;
    //-----------------------------------------------------------------------------------------------------------------
    this.validate = validate;
    this.validateForm = validateForm;
    this.mask = mask;
    //-----------------------------------------------------------------------------------------------------------------
    function validate(id, type)
    {
        _elements[_elements.length] = {"id": id, "type": type};
    }
    //-----------------------------------------------------------------------------------------------------------------
    function validateElement(id, type)
    {
        var field = document.getElementById(id).value;
        if(!/^.+$/.test(field)) {
            _errorType = "nem lehet üres";
            return false;
        }
        switch(type)
        {
            case "a":
            case "alpha":
                _errorType = "csak betűket tartalmazhat";
                var alpha = /^[a-záéíóöőúű\s]*$/ig;
                var a = new RegExp(alpha);
                return a.test(field);
            break;
            case "d":
            case "date":
                _errorType = "nem valós dátum";
                var date = /(19|20)[0-9]{2}[-/.](0[1-9]|1[012])[-/.](0[1-9]|[12][0-9]|3[01])/g;
                var d = new RegExp(date);
                return d.test(field);
            break;
            case "m":
            case "email":
                _errorType = "nem valós email cím";
                var email = /^[A-Z0-9._%+-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,4}$/ig;
                var m = new RegExp(email);
                return m.test(field);
            break;
            case "n":
            case "name":
                _errorType = "csak betűket, kötőjelet és pontot tartalmazhat";
                var name = /^[-.a-záéíóöőúüű\s]*$/ig;
                var n = new RegExp(name);
                return n.test(field);
            break;
        }
        return false;
    }
    //-----------------------------------------------------------------------------------------------------------------
    function validateForm()
    {
        var isError = false;
        var labels = document.getElementsByTagName('label');
        for(var i = 0; i < _elements.length; i++) {
            if(!validateElement(_elements[i]["id"], _elements[i]["type"])) {
                isError = true;
                var id =  _elements[i]["id"];
                for(var j = 0; j < labels.length - 1; j++) {
                    if(labels[j].attributes['for'].value == id) {
                        var labelText = labels[j].innerHTML;
                    }
                }
                if(labelText) {
                    _errors[_errors.length] = labelText + " " + _errorType;
                }
                else {
                    var name = document.getElementById(_elements[i]["id"]).attributes['name'];
                    if(name) {
                        _errors[_errors.length] = name.value + " " + _errorType;
                    }
                    else {
                        _errors[_errors.length] = id + " " + _errorType;
                    }
                }
            }
        }
        if(isError) {
            displayErrors();
        }
        else {
            return true;
        }
        return false;
    }
    //-----------------------------------------------------------------------------------------------------------------
    function displayErrors()
    {
        var errorMessage = "Hibák:\n";
        for(var i = 0; i < _errors.length; i++) {
            errorMessage += " - " +  _errors[i] + "\n";
        }
        alert(errorMessage);
    }
    //-----------------------------------------------------------------------------------------------------------------
    function mask(id, mask)
    {
        var pattern;
        attachEvent(id);
        switch(mask)
        {
            case "d":
                pattern = /\d/;
            break;
        }
        _patterns[id] = pattern;
    }
    //-----------------------------------------------------------------------------------------------------------------
    function attachEvent(id)
    {
        var obj = document.getElementById(id);
        if(document.attachEvent) {
            obj.attachEvent("onkeypress", inputMask);
        }
        else if(document.addEventListener) {
            obj.addEventListener("keydown", inputMask, true);
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    function inputMask(e)
    {
        var code, character, target;
        if(!e) {
            var e = window.event;
        }
        if(e.keyCode) {
            code = e.keyCode;
        }
        else if(e.which) {
            code = e.which;
        }
        character = String.fromCharCode(code);
        if(e.target) {
            target = e.target;
        }
        else if(e.srcElement) {
            target = e.srcElement;
        }
        if(target.nodeType == 3) {
            target = target.parentNode;
        }
        if(!_patterns[target.id].test(character) && !_keyboards.test(character)) {
            if(e.preventDefault) {
                e.preventDefault();
            }
            else {
                e.returnValue = false;
            }
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
}