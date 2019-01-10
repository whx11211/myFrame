$.fn.select2.defaults.set("templateResult", function(state){
    if (!state.id || typeof state.element == 'undefined' || typeof state.element.attributes['data-ng-pre-attr'] == 'undefined') {
        return state.text;
    }
    if (/^[0-9]*$/.exec(state.element.attributes['data-ng-pre-attr'].value)) {
        var $state = $(
            '<span style="padding-left:' + state.element.attributes['data-ng-pre-attr'].value + 'em;">' + state.text + '</span>'
        );
    }
    else {
        var $state = state.element.attributes['data-ng-pre-attr'].value + state.text;
    }
    return $state;
});
$.fn.select2.defaults.set("placeholder", '');