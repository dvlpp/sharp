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

        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
            },
            dataType: 'json',
            success: function(data) {
                window["handleCommandReturn_"+data.type](data);
            },
            error: function (jqXhr, json, errorThrown) {

            }
        });
    });

    // ---
    // Manage form creation for commands with form
    // ---
    //$("body.sharp-list .sharp-command.with-form").click(function (e) {
    //    e.preventDefault();
    //
    //    var url = $(this).attr("href");
    //
    //    $.get(url, function(formData) {
    //        var $modal = $(formData);
    //        $("#contenu").append($modal);
    //        $modal.modal({}).show();
    //    });
    //
    //});
});

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