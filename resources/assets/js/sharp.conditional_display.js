$(window).load(function() {

    $("#sharpform .sharp-field[data-conditional_display]").each(function() {
        manageConditionalDisplay($(this));
    });

});

function manageConditionalDisplay($field) {
    var cond = $field.data("conditional_display");
    var showFieldIfTrue = cond.charAt(0)!='!';
    var stateFieldName = showFieldIfTrue ? cond : cond.substring(1);
    var stateFieldValue = 1;

    if((valPos = stateFieldName.indexOf(':')) != -1) {
        // State field has a specific value (probably a <select> case)
        stateFieldValue = stateFieldName.substring(valPos+1);
        stateFieldName = stateFieldName.substring(0, valPos);

        if((valPos = stateFieldValue.indexOf(',')) != -1) {
            // Multiple values
            stateFieldValue = stateFieldValue.split(',');
        }
    }

    var $item = $field.parents(".sharp-list-item");
    if($item.length) {
        // List item case: check first if it's template
        if($item.hasClass("template")) {
            // Template: skip
            return;
        }

        // We use $= selector to look for input which end of name is [stateFieldName]
        // (with brackets because it's a list)
        $stateField = $item.find(".sharp-field *[name$=\\["+escapeFieldName(stateFieldName)+"\\]]");

    } else {
        // Normal case, conditional field in form-wide
        $stateField = $("#sharpform").find(".sharp-field *[name="+escapeFieldName(stateFieldName)+"]");
    }

    if($stateField.length) {
        if($stateField.is(":checkbox")) {
            $stateField.change(function() {
                checkboxShowHide($(this), $field, showFieldIfTrue);
            });

            checkboxShowHide($stateField, $field, showFieldIfTrue);

        } else if($stateField.is("select")) {
            $stateField.change(function() {
                selectShowHide($(this), stateFieldValue, $field, showFieldIfTrue);
            });

            selectShowHide($stateField, stateFieldValue, $field, showFieldIfTrue);
        }
    }
}

function checkboxShowHide($checkbox, $field, fieldShowOnChecked) {
    showHideField($field,
        ($checkbox.is(":checked") && fieldShowOnChecked)
            || (!$checkbox.is(":checked") && !fieldShowOnChecked));
}

function selectShowHide($select, value, $field, fieldShowIfSelected) {
    var values = null;
    if(!$.isArray(value)) {
        values = [];
        values.push(value);

    } else {
        values = value;
    }

    show = false;
    for(var i=0; i<values.length; i++) {
        value = values[i];

        show = ($select.find('option:selected').val() == value && fieldShowIfSelected)
            || ($select.find('option:selected').val() != value && !fieldShowIfSelected);

        if(show) break;
    }

    showHideField($field, show);
}

function showHideField($field, show) {
    if(show) {
        $field.removeClass("hidden");
    } else {
        $field.addClass("hidden");
    }

    // Check fieldset visibility if there's one.
    var $fieldset = $field.parents(".fieldset");
    if($fieldset.length) {
        if($fieldset.find(".sharp-field:not(.hidden)").length) {
            $fieldset.removeClass("hidden");
        } else {
            $fieldset.addClass("hidden");
        }
    }
}

function escapeFieldName( fieldName ) {
    return fieldName.replace( /(:|\.|\[|\]|~|\\)/g, "\\$1" );
}