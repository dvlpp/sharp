$(window).load(function() {

    $form = $("#sharpform");

    // ---
    // Navigate to an embedded entity form (we have to post all master entity
    // fields for later repopulation)
    // ---
    $form.on('click', '.sharp-embed-update', function(e) {
        e.preventDefault();

        $form.prop("action", $(this).prop("href"));

        $form.submit();
    });


    // ---
    // Delete an embedded instance
    // ---
    $form.on('click', '.sharp-embed-delete', function(e) {
        e.preventDefault();

        fieldName = $(this).data("fieldname");

        $field = $(this).parents(".sharp-field-embed");

        $field.find(".panel-embed").removeClass("updatable").addClass("creatable");
        $field.find(".panel-embed-body").empty();

        $input = $field.find("#"+fieldName);
        $input.val("__DELETE__");

    });

});