
/******************************/
/* version 0.2.0 @ 2010.01.08 */
/******************************/

function Validator()
{
    //-----------------------------------------------------------------------------------------------------------------
    var _elements = Array();
    var _errors = Array();
    var _errorType = "hibás";
    //-----------------------------------------------------------------------------------------------------------------
    this.validate = validate;
    this.validateForm = validateForm;
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
                return /^[a-záéíóöőúű\s]*$/ig.test(field);
            break;
            case "d":
            case "date":
                _errorType = "nem valós dátum";
                return /(19|20)[0-9]{2}[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])/g.test(field);
            break;
            case "m":
            case "email":
                _errorType = "nem valós email cím";
                return /^[a-z0-9._%+-]+@(?:[a-z0-9-]+\.)+[a-z]{2,4}/ig.test(field);
            break;
            case "n":
            case "name":
                _errorType = "csak betűket, kötőjelet és pontot tartalmazhat";
                return /^[-.a-záéíóöőúüű\s]*$/ig.test(field);
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
}