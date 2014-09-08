(function($)
{
    $.fn.sharp_file=function(options)
    {
        var defauts=
        {
            image: false,
            progress: false,
            url: null,
            thumbnail: false,
            browseButton: {
                text: 'browse',
                className: 'btn'
            },
            deleteButton: {
                text: '&times; remove',
                className: 'btn'
            },
            file_filter: '',
            file_filter_alert: '',
            file_label_pattern: '<div class="type"><i class="fa fa-file-o"></i><span>%type%</span></div><span class="mime">(%mime%)</span><span class="size">%size%</span>',
            done: function(d) { return true; },
            submit: function(d) { return true; }
        };

        var params = $.extend(defauts, options);

        return this.each(function()
        {
            // Generate DOM

            var $wrapper = $(this);

            // Create input file
            var idInput = '_' + Math.random().toString(36).substr(2, 9);
            var $input = $('<input/>').attr("type", "file").attr("name", "file").attr("id", idInput);
            $input.css({ display: 'block', visibility: 'hidden', width: '0', height: '0' });
            $(this).append($input);

            // Create button (triggers browse)
            var $btn = $('<a/>').addClass(params.browseButton.className).addClass("sharp-file-browse").html(params.browseButton.text);
            $(this).append($btn);

            // Create progress bar
            var $progress = null;
            if(params.progress) {
                $progress = $('<div/>').addClass("sharp-file-progress").css({ width:'0' });
                $(this).prepend($progress);
            }

            // Create OR INIT IF EXISTS thumbnail image
            var $imgThumbnail = null;
            if(params.thumbnail) {
                $imgThumbnail = $(this).find(".sharp-file-thumbnail");
                if($imgThumbnail.length == 0) {
                    $imgThumbnail = $('<img/>').addClass("sharp-file-thumbnail");
                    $(this).prepend($imgThumbnail);
                }
            }

            // Create OR INIT IF EXISTS file label
            var $fileLabel = $(this).find(".sharp-file-label");
            if($fileLabel.length == 0) {
                $fileLabel = $('<div/>').addClass("sharp-file-label");
                $(this).append($fileLabel);
            }

            // Get hidden field containing file name
            var $hiddenFileId = $(this).find(".sharp-file-id");

            // Get hidden field containing file full path (needed for repopulation)
            var $hiddenFilePath = $(this).find(".sharp-file-path");

            // Create delete file link
            var $deleteLink = $('<a/>').addClass(params.deleteButton.className).addClass("sharp-file-delete").html(params.deleteButton.text);
            $(this).append($deleteLink);

            // Init fileupload
            $input.fileupload({
                dataType: 'json',
                url: params.url,

                add: function(e, data) {
                    if(params.file_filter)
                    {
                        var acceptFileTypes = new RegExp("(\\.|\\/)(" + params.file_filter.replace(/,/g, '|') + ")$", "i");
                        if(data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
                            alert(params.file_filter_alert ? params.file_filter_alert : params.file_filter);
                            return false;
                        }
                    }
                    data.submit();
                },

                submit: function(e, data) {
                    $btn.css("visibility", "hidden");
                    return params.submit(data);
                },

                done: function (e, data) {

                    // Hide progress
                    if(params.progress) $progress.css('width', '0%');
                    $btn.css("visibility", "visible");

                    // Call user callback
                    if(params.done(data)) {

                        // Add valuated class
                        $wrapper.addClass("valuated");

                        // Legend / file title
                        var pattern = params.file_label_pattern.replace(/%type%/i, data.originalFiles[0]['name'].split('.').pop());
                        pattern = pattern.replace(/%mime%/i, data.originalFiles[0]['type']);
                        pattern = pattern.replace(/%size%/i, getReadableFileSizeString(data.originalFiles[0]['size']));
                        $fileLabel.html(pattern);

                        // Manage thumbnail
                        if(params.thumbnail && data.result.file.thumbnail)
                        {
                            $imgThumbnail.attr("src", data.result.file.thumbnail).show();
                            $wrapper.addClass("with_thumbnail");
                        }
                        else
                        {
                            $wrapper.removeClass("with_thumbnail");
                        }

                        // Set sharp-file-id
                        $hiddenFileId.val(data.result.file.name);
                        $hiddenFilePath.val(data.result.file.path);
                    }
                },

                progressall: function (e, data) {
                    if(params.progress) {
                        var p = parseInt(data.loaded / data.total * 100, 10);
                        $progress.css('width', p + '%');
                    }
                }
            });

            // Click upload button
            $btn.on("click", function() {
                // We use a "dynamic" way to retrieve $input, otherwise the link is lost in case of 2nd upload
                // (for an unknown reason, maybe a plugin bug)
                $("#"+idInput).click();
            });

            // Click on delete button
            $deleteLink.on("click", function() {

                // Remove valuated class
                $wrapper.removeClass("valuated");

                // Unset sharp-file-id
                $hiddenFileId.val('');
            });

            // Add upload options
            $(this).bind('fileuploadsubmit', function (e, data) {
                var postParams = {};
                if(params.thumbnail) {
                    $.each(params.thumbnail, function( k, v ) {
                        var attrKey = 'thumbnail_'+k;
                        postParams[attrKey] = v;
                    });
                }
                data.formData = postParams;
            });
        });
    };
})(jQuery);

$(window).load(function() {

    $('.sharp-file').each(function()
    {
        createSharpFile($(this));
    });

});

function createSharpFile($el)
{
    var thumbnail = null;
    var dataThumbnail = $el.data("thumbnail");
    if(dataThumbnail)
    {
        var tab = dataThumbnail.split('x');
        if(tab.length == 2)
        {
            thumbnail = {
                width: tab[0],
                height: tab[1]
            };
        }
    }

    var fileFilter = $el.data("file_filter");
    var fileFilterAlert = $el.data("file_filter_alert");

    $el.sharp_file({
        url:thumbnail ? '/admin/uploadWithThumbnail' : '/admin/upload',
        thumbnail:thumbnail,
        progress:true,
        file_filter:fileFilter,
        file_filter_alert: fileFilterAlert,
        done: function(data) {
            if(data.result.err) {
                alert(data.result.err);
                return false;
            }
            return true;
        },
        browseButton:{
            text:'<i class="fa fa-upload"></i> ' + $el.data("browse_text"),
            className:'btn'
        },
        deleteButton:{
            text:'<i class="fa fa-times"></i>',
            className:'btn'
        }
    });
}

function getReadableFileSizeString(fileSizeInBytes)
{
    var i = -1;
    var byteUnits = [' Ko', ' Mo', ' Go', ' To', 'Po', 'Eo', 'Zo', 'Yo'];
    do {
        fileSizeInBytes = fileSizeInBytes / 1024;
        i++;
    } while (fileSizeInBytes > 1024);

    return Math.max(fileSizeInBytes, 0.1).toFixed(1) + byteUnits[i];
};