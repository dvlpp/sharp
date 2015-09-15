(function($) {
    $.fn.sharp_file = function(options) {

        var defauts = {
            maxFilesize: 6, // MB
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
            },
            maxFiles: 1,
            file_filter: '',
            file_filter_alert: '',
            previewTemplate: document.querySelector('#dz-template').innerHTML,
            dictInvalidFileType: '',
            dictFileTooBig: 'Fichier non pris en compte : trop lourd ({{maxFilesize}} Mo maximum).',
            dictResponseError: 'Fichier non pris en compte : erreur technique.',
            dictMaxFilesExceeded: 'Fichier non pris en compte : vous ne pouvez envoyer qu’1 fichier.',
            dictCancelUploadConfirmation: 'Êtes-vous sûr(e) de vouloir annuler cet envoi ?'
        };

        var params = $.extend(defauts, options);

        return this.each(function() {

            // Create DOM
            var $addBtn = $("<a>")
                    .addClass("btn btn-default btn-block dz-message add-btn")
                    .html(params.browseText)
                    .prepend('<i class="fa fa-upload"></i> ');

            var $errorLabel = $("<span>").addClass("hidden validation-error");
            var $dz = $("<div>").addClass("dropzone-upload")
                .append($addBtn)
                .append($errorLabel);

            $(this).append($dz);

            var $hiddenField = $(this).find('.sharp-file-id');

            var dropzone = null;

            $dz.dropzone($.extend(params, {
                init: function () {
                    dropzone = this;

                    this.on("addedfile", function () {
                        $addBtn.addClass("hidden");
                        $errorLabel.html("").addClass("hidden");
                    });

                    this.on("removedfile", function () {
                        $addBtn.removeClass("hidden");
                        $hiddenField.val("");
                    });

                    this.on("success", function (file, response) {
                        $hiddenField.val(response.file.name);
                    });

                    this.on("error", function (file, errorMessage, xhr) {
                        $errorLabel.html(errorMessage).removeClass("hidden");
                        $addBtn.removeClass("hidden");
                        this.removeFile(file);
                    });
                }

            }));

            if(params.populatedFile) {
                dropzone.emit("addedfile", params.populatedFile);
                if(params.thumb) {
                    dropzone.emit("thumbnail", params.populatedFile, params.thumb);
                }
                dropzone.emit("complete", params.populatedFile);

                $(this).find(".dz-dl").prop("href", params.dl_url).removeClass("hidden");
            }
        });
    };
})(jQuery);

$(window).load(function() {

    $('.sharp-file').each(function() {
        createSharpFile($(this));
    });

});

function createSharpFile($el) {
    var params = {};

    if($el.data("thumbnail")) {
        var tab = $el.data("thumbnail").split('x');
        if(tab.length == 2) {
            params.thumbnailWidth = tab[0]!=0 ? tab[0] : null;
            params.thumbnailHeight = tab[1]!=0 ? tab[1] : null;
        }
    }

    params.url = ($el.data("thumbnail") ? '/admin/uploadWithThumbnail' : '/admin/upload');

    if($el.data("file_filter")) {
        params.acceptedFiles = $el.data("file_filter");
        if($el.data("file_filter_alert")) {
            params.dictInvalidFileType = $el.data("file_filter_alert");
        }
    }

    if($el.data("max_file_size")) {
        params.maxFilesize = $el.data("max_file_size");
    }

    params.browseText = $el.data("browse_text");

    if($el.data("name")) {
        // File is valuated
        params.populatedFile = {
            name: $el.data("name"),
            size: $el.data("size")
        };

        if($el.data("thumbnail")) {
            params.thumb = $el.data("thumbnail")
        }
    }

    if($el.data("dl_link")) {
        // There's a DL link
        params.dl_url = $el.data("dl_link");
    }

    $el.sharp_file(params);
}
