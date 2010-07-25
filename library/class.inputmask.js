function InputMask()
{
    //-----------------------------------------------------------------------------------------------------------------
    var _patterns = Array();
    var _formatMasks = Array();
    //-----------------------------------------------------------------------------------------------------------------
    this.mask = mask;
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
            case "a":
                _patterns[id] = /[a-záéíóöőúű\s\d]/i;
            break;
            case "d":
                _patterns[id] = /\d/;
            break;
            case "l":
                _patterns[id] = /[a-záéíóöőúű\s]/i;
            break;
            case "n":
                _patterns[id] = /[-'.a-záéíóöőúüű\s]/i;
            break;
            case "x":
                _patterns[id] = /./;
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
    function inputMask(e)
    {
        var code, character, target, allowedKey = false;
        if (!e) {
            var e = window.event;
        }
        code = e.charCode || e.keyCode;
        if(e.keyCode != 0 && e.which == 0 && (e.charCode == 0 || typeof(e.charCode) == 'undefined') || code == 8) {
                allowedKey = true;
        }
        character = String.fromCharCode(code);
        target = getTarget(e);
        if(!_patterns[target.id].test(character) && !allowedKey) {
            if(e.preventDefault) {
                e.preventDefault();
            }
            else {
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
