$(window).load(function() {

    $("#sharpform .sharp-field[data-conditional_display]").each(function() {
        manageConditionalDisplay($(this));
    });

});

function manageConditionalDisplay($field)
{
    var cond = $field.data("conditional_display");
    var showFieldIfTrue = cond.charAt(0)!='!';
    var stateFieldName = showFieldIfTrue ? cond : cond.substring(1);
    var stateFieldValue = 1;

    if((valPos = stateFieldName.indexOf(':')) != -1)
    {
        // State field has a specific value (probably a <select> case)
        stateFieldValue = stateFieldName.substring(valPos+1);
        stateFieldName = stateFieldName.substring(0, valPos);
    }

    var $item = $field.parents(".sharp-list-item");
    if($item.length)
    {
        // List item case: check first if it's template
        if($item.hasClass("template"))
        {
            // Template: skip
            return;
        }
        // We use $= selector to look for input which end of name is [stateFieldName]
        // (with brackets because it's a list)
        $stateField = $item.find(".sharp-field *[name$=\\["+escapeFieldName(stateFieldName)+"\\]]");
    }
    else
    {
        // Normal case, conditional field in form-wide
        $stateField = $("#sharpform").find(".sharp-field *[name="+escapeFieldName(stateFieldName)+"]");
    }

    if($stateField.length)
    {
        if($stateField.is(":checkbox"))
        {
            $stateField.change(function() {
                checkboxShowHide($(this), $field, showFieldIfTrue);
            });

            checkboxShowHide($stateField, $field, showFieldIfTrue);
        }

        else if($stateField.is("select"))
        {
            $stateField.change(function() {
                selectShowHide($(this), stateFieldValue, $field, showFieldIfTrue);
            });

            selectShowHide($stateField, stateFieldValue, $field, showFieldIfTrue);
        }
    }
}

function checkboxShowHide($checkbox, $field, fieldShowOnChecked)
{
    if(($checkbox.is(":checked") && fieldShowOnChecked)
        || (!$checkbox.is(":checked") && !fieldShowOnChecked))
    {
        $field.show();
    }
    else
    {
        $field.hide();
    }
}

function selectShowHide($select, value, $field, fieldShowIfSelected)
{
    if(($select.find('option:selected').val() == value && fieldShowIfSelected)
        || ($select.find('option:selected').val() != value && !fieldShowIfSelected))
    {
        $field.show();
    }
    else
    {
        $field.hide();
    }
}

function escapeFieldName( fieldName )
{
    return fieldName.replace( /(:|\.|\[|\]|~|\\)/g, "\\$1" );
}