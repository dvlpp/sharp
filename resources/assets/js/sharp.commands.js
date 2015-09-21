$(window).load(function () {

    // ---
    // Show confirm on commands
    // ---
    $("body.sharp-list a.command[data-confirm]").click(function() {
        return confirm($(this).data("confirm"));
    });

    // ---
    // Ajax command call
    // ---
    $("body.sharp-list a.command").click(function (e) {
        e.preventDefault();

        var url = $(this).attr("href");
        var $form = $(".form-command-" + $(this).data("command"));

        if($form.length) {
            var $modal = $form.modal({});
            $modal.find('form').prop("action", url);
            $modal.show();
            return;
        }

        sendCommand(url)
    });

    // ---
    // Ajax command call after filling form
    // ---
    $("body.sharp-list .form-command").submit(function (e) {
        e.preventDefault();

        $(this).parents(".modal").modal('hide');

        sendCommand($(this).attr("action"), $(this).serialize());

        $(this)[0].reset();
    });
});

function sendCommand(url, params) {

    showPageOverlay();

    $.ajax({
        url: url,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
        },
        data: params,
        dataType: 'json',

        success: function(data) {
            hidePageOverlay();
            window["handleCommandReturn_"+data.type](data);
        },
        error: function (jqXhr, json, errorThrown) {
            hidePageOverlay();
        }
    });
}

function handleCommandReturn_ALERT(data) {
    sweetAlert(data.title, data.message, data.level);
}

function handleCommandReturn_RELOAD() {
    window.location.reload();
}

function handleCommandReturn_DOWNLOAD(data) {
    var $dllink = $("#sharp_command_download_link");

    $dllink.prop("href", data.file_path)
        .prop("download", data.file_name);
    $dllink[0].click();
}