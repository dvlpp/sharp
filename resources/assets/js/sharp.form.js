// ---
// Manage tab click with URL hashes
// ---
$(function(){
    var hash = window.location.hash;
    hash && $('ul.entity-tabs a[href="' + hash + '"]').tab('show');

    $('.entity-tabs a').click(function (e) {
        e.preventDefault();

        $(this).tab('show');

        if(history.pushState) {
            history.pushState(null, null, this.hash);
        } else {
            location.hash = this.hash;
        }
    });
});

// ---
// Send the form (ajax) and handle validation errors
// ---
$(function() {

    var $form = $("#sharpform");

    var method = $form.find("input[name=_method]").val();
    if (!method) method = 'POST';

    var $errorMessages = $("#form-validation-error-message");
    var errorMessage = $errorMessages.find("h1").html();
    var errorMessageDetail = $errorMessages.find("h2").html();

    $form.submit(function (e) {
        e.preventDefault();

        showPageOverlay();

        $form.find(".validation-error").remove();
        $form.find(".has-error").removeClass("has-error");

        $.ajax({
            url: $form.prop("action"),
            type: method,
            data: $form.serializeArray(),
            dataType: 'json',
            success: function (data) {
                document.location = data.url;
            },
            error: function (jqXhr, json, errorThrown) {
                hidePageOverlay();

                if (jqXhr.status == 422) {
                    var errors = jqXhr.responseJSON;

                    $.each(errors, function (key, value) {
                        if((pos = key.indexOf(".")) != -1) {
                            // It's a list item
                            key = key.substring(0, pos);
                        }
                        var $field = $form.find(".sf-" + key);
                        $field.addClass("has-error");
                        $field.append('<span class="validation-error">' + value[0] + '</span>');
                    });

                    sweetAlert(errorMessage, errorMessageDetail, "error");
                }
            }
        });
    });
});