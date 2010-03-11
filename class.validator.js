
/******************************/
/* version 0.2.7 @ 2010.03.11 */
/******************************/

function Validator()
{
    //-----------------------------------------------------------------------------------------------------------------
    var _elements = Array();
    var _errors = Array();
    var _errorType = "hibás";
    var _patterns = Array();
    var _formatMasks = Array();
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
        addEvent(id);
        if(mask.length != 1) {
            _formatMasks[id] = mask;
            var types = mask.split(/\W/);
            for(var i = 0; i < types.length; i++) {
                if(/\w/.test(types[i])) {
                    mask = types[i].substring(0, 1);
                    break;
                }
            }
        }
        switch(mask)
        {
            case "n":
                _patterns[id] = /[-.a-záéíóöőúüű\s]/i;
            break;
            case "d":
                _patterns[id] = /\d/;
            break;
            default:
                _patterns[id] = /\w/;
            break;
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    function addEvent(id)
    {
        var obj = document.getElementById(id);
        if(document.attachEvent) {
            // Internet Explorer, Opera
            obj.attachEvent("onkeypress", inputMask);
        }
        else if(document.addEventListener) {
            // Chrome, Firefox, Safari
            obj.addEventListener("keypress", inputMask, true);
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    function getTarget(e)
    {
        // Chrome, Firefox, Opera, Safari
        if(e.target) {
            return e.target;
        }
        // Internet Explorer
        else if(e.srcElement) {
            return e.srcElement;
        }
        // defeat a Safari bug
        if(target.nodeType == 3) {
            return target.parentNode;
        }
        return null;
    }
    //-----------------------------------------------------------------------------------------------------------------
    function inputMask(e)
    {
        var code, allowedKey = false, character, target;
        if (!e) {
            var e = window.event;
        }
        code = e.charCode || e.keyCode;
        // handle backspace, ins, del, home, end and arrow keys in Firefox and Opera
        if(e.keyCode != 0 && e.which == 0 && (e.charCode == 0 || typeof(e.charCode) == 'undefined') || code == 8) {
                allowedKey = true;
        }
        character = String.fromCharCode(code);
        // Get the target of the event
        target = getTarget(e);
        if(!_patterns[target.id].test(character) && !allowedKey) {
            if(e.preventDefault) {
                // Chrome, Firefox, Opera, Safari
                e.preventDefault();
            }
            else {
                // Internet Explorer
                e.returnValue = false;
            }
        }
        if(typeof _formatMasks[target.id] !== "undefined") {
            var field = document.getElementById(target.id);
            var text = document.getElementById(target.id).value;
            var pattern = _formatMasks[target.id];
            var characters = pattern.split(/\W/);
            var separators = Array();
            var locations = Array();
            var j = 0;
            for(var i = 0; i < characters.length - 1; i++) {
                locations[i] = characters[i].length + j;
                j = locations[i] + 1;
                separators[i] = pattern.substring(locations[i], locations[i] + 1);
            }
            for(var i = 0; i <= locations.length; i++) {
                for(var j = 0; j <= text.length; j++) {
                    if(j == locations[i]) {
                        if(text.substring(j, j + 1) != separators[i] && e.keyCode != 8) {
                            text = text.substring(0, j) + separators[i] + text.substring(j, text.length);
                        }
                    }
                }
            }
            field.value = text;
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
}
