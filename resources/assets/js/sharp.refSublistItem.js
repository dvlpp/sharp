(function ($) {
    $.fn.sharp_refSublistItem = function (options) {
        var defauts = {};
        var params = $.extend(defauts, options);

        function refreshSubList($baseRefField, $refSublistField, $datastore) {
            var baseRefValue = $baseRefField.find("option:selected").val();

            $refSublistField.disable();
            $refSublistField.clearOptions();

            $datastore.find("optgroup").each(function () {
                if ($(this).attr("label") == baseRefValue) {
                    $refSublistField.enable();

                    $(this).find("option").each(function () {
                        $refSublistField.addOption({
                            value: $(this).val(),
                            text: $(this).text()
                        });
                        $refSublistField.addItem($(this).val());
                    });

                    return false;
                }
            });
        }

        return this.each(function () {
            $(this).selectize(params);

            var $refSublistField = $(this)[0].selectize;

            var $item = $(this).parents(".sharp-list-item");
            var $baseRefField = null;
            var $datastore = $(this).prev("select");

            if($item.length) {
                // List item case: check first if it's template
                if($item.hasClass("template")) {
                    // Template: skip
                    return;
                }

                // We use $= selector to look for input which end of name is [stateFieldName]
                // (with brackets because it's a list)
                $baseRefField = $item.find(".sharp-field *[name$=\\["+escapeFieldName(params.linked_ref_field)+"\\]]");

            } else {
                $baseRefField = $(":input[name=" + escapeFieldId(params.linked_ref_field) + "]");
                $datastore = $(this).prev("select[name=values_" + escapeFieldId($(this).prop("name")) + "]");
            }

            $baseRefField.on('change', function () {
                refreshSubList($baseRefField, $refSublistField, $datastore);
            });

            refreshSubList($baseRefField, $refSublistField, $datastore);

            // Restore initial value
            if (params.initial_value) {
                $refSublistField.setValue(params.initial_value);
            }

        });
    }

}(jQuery));

$(window).load(function () {

    $('.sharp-refSublistItem').each(function () {
        createSharpRefSublistItem($(this));
    });

});

function createSharpRefSublistItem($el) {
    var options = {
        linked_ref_field: $el.data("linked_ref_field"),
        initial_value: $el.data("initial_value")
    };

    $el.sharp_refSublistItem(options);
}

function escapeFieldId(id) {
    return id.replace(/(:|\.|\[|\]|\~|,)/g, "\\$1");
}