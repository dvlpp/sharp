$(window).load(function() {

    $("#sharpform .sharp-field[data-conditional_display]").each(function() {
        manageConditionalDisplay($(this));
    });

});

function manageConditionalDisplay($field)
{
    var cond = $field.data("conditional_display");
    var fieldShowOnClicked = cond.charAt(0)!='!';
    var stateFieldName = fieldShowOnClicked ? cond : cond.substring(1);

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
        $stateField = $item.find(".sharp-field input[name$=\\["+escapeFieldName(stateFieldName)+"\\]]");
    }
    else
    {
        // Normal case, conditional field in form-wide
        $stateField = $("#sharpform").find(".sharp-field input[name="+escapeFieldName(stateFieldName)+"]");
    }

    if($stateField.length)
    {
        if($stateField.is(":checkbox"))
        {
            $stateField.change(function() {
                showHide($(this), $field, fieldShowOnClicked);
            });

            showHide($stateField, $field, fieldShowOnClicked);
        }
    }
}

function showHide($checkbox, $field, fieldShowOnChecked)
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

function escapeFieldName( fieldName )
{
    return fieldName.replace( /(:|\.|\[|\]|~)/g, "\\$1" );
}