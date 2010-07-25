function Validator(messageType)
{
    //-----------------------------------------------------------------------------------------------------------------
    var _elements = Array();
    var _errors = Array();
    var _errorType = "hibás";
    //-----------------------------------------------------------------------------------------------------------------
    this.validate = validate;
    this.validateForm = validateForm;
    //-----------------------------------------------------------------------------------------------------------------
    
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
                _errorType = "csak betűket és számokat tartalmazhat";
                var alpha = /^[a-záéíóöőúű\s\d]*$/ig;
                var a = new RegExp(alpha);
                return a.test(field);
            break;
            case "c":
                _errorType = "nem valós dátum";
                var date = /(19|20)[0-9]{2}[-/.](0[1-9]|1[012])[-/.](0[1-9]|[12][0-9]|3[01])/g;
                var c = new RegExp(date);
                return c.test(field);
            break;
            case "d":
                _errorType = "csak számokat tartalmazhat";
                var number = /^[\d]*$/g;
                var d = new RegExp(date);
                return d.test(field);
            break;
            case "l":
                _errorType = "csak betűket tartalmazhat";
                var letter = /^[a-záéíóöőúű\s]*$/ig;
                var l = new RegExp(alpha);
                return l.test(field);
            break;
            case "m":
                _errorType = "nem valós email cím";
                var email = /^[A-Z0-9._%+-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,4}$/ig;
                var m = new RegExp(email);
                return m.test(field);
            break;
            case "n":
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
                    _errors[_errors.length] = {"id": id + "-error", "text": labelText + " " + _errorType};
                }
                else {
                    var name = document.getElementById(_elements[i]["id"]).attributes['name'];
                    if(name) {
                        _errors[_errors.length] = {"id": id + "-error", "text": name.value + " " + _errorType};
                    }
                    else {
                        _errors[_errors.length] = {"id": id + "-error", "text": id + " " + _errorType};
                    }
                }
            }
        }
        var elements = getElementsByClassNames("errorMessage");
        for(var i = 0; i < elements.length; i++) {
            elements[i].innerHTML = "";
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
        switch(messageType)
        {
            case "next":
                for(var i = 0; i < _errors.length; i++) {
                    document.getElementById(_errors[i]["id"]).innerHTML = _errors[i]["text"];
                }
            break;
            case "bottom":
                var errorMessage = "<div><h2>Hibák</h2><ul>";
                for(var i = 0; i < _errors.length; i++) {
                    errorMessage += '<li class="errorMessage">' + _errors[i]["text"] + "</li>";
                }
                errorMessage += "</ul></div>";
                document.getElementById("errors").innerHTML = errorMessage;
            break;
            case "alert":
            default:
                var errorMessage = "Hibák:\n";
                for(var i = 0; i < _errors.length; i++) {
                    errorMessage += " - " + _errors[i]["text"] + "\n";
                }
                alert(errorMessage);
            break;
        }
    }
    //-----------------------------------------------------------------------------------------------------------------
    function getElementsByClassNames(className)
    {
        var elements = new Array();
        var tags = document.getElementsByTagName("div");
        var pattern = new RegExp("(^|\\s)" + className + "(\\s|$)");
        for(var i = 0, j = 0; i < tags.length; i++) {
            if(pattern.test(tags[i].className)) {
                elements[j] = tags[i];
                j++;
            }
        }
        return elements;
    }
    //-----------------------------------------------------------------------------------------------------------------
}
