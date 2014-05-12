$(window).load(function() {

    $("#sharpform .sharp-field[data-conditional_display]").each(function() {
        var $field = $(this);
        var cond = $(this).data("conditional_display");
        var fieldShowOnClicked = cond.charAt(0)!='!';
        var stateFieldName = fieldShowOnClicked ? cond : cond.substring(1);

        var $item = $(this).parents(".sharp-list-item");
        if($item.length)
        {
            // List item case
            $stateField = $item.find(".sharp-field input[name="+stateFieldName+"]");
        }
        else
        {
            // Normal case, conditional field in form-wide
            $stateField = $("#sharpform").find(".sharp-field input[name="+stateFieldName+"]");
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
    });

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

});