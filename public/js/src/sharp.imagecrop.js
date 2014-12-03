(function($)
{
    $.fn.sharp_fileCrop = function (options)
    {

        var defauts = {
            ratio: ''
        };

        var params = $.extend(defauts, options);

        return this.each(function()
        {
            var id = Math.round(new Date().getTime() + (Math.random() * 100));

            var $glassPane = $(".crop-img-glasspane");
            if(!$glassPane.length)
            {
                $glassPane = $("<div>").addClass("crop-img-glasspane");
                $("body").append($glassPane);
            }

            $(this).parents(".sharp-file").attr("id", "sharp_filetag_"+id);

            var $wrapper = $("<div>").addClass("crop-img-wrapper");
            var $imgCrop = $("<img>");

            var $btnCrop = $('<a>')
                .addClass("btn btn-crop")
                .html('<i class="fa fa-check"></i>')
                .data("filetagid", id);

            $btnCrop.click(function() {
                var selection = ias.getSelection();
                var imgWidth = $imgCrop.width();
                var imgHeight = $imgCrop.height();
                var x1 = selection.x1 / imgWidth;
                var y1 = selection.y1 / imgHeight;
                var x2 = selection.x2 / imgWidth;
                var y2 = selection.y2 / imgHeight;

                var $sharpFileTag = $("#sharp_filetag_"+$(this).data("filetagid"));
                var $thumbnail = $sharpFileTag.find(".sharp-file-thumbnail");
                var $hiddenCropField = $sharpFileTag.find(".sharp-file-crop-values");

                $hiddenCropField.val(x1+","+y1+","+x2+","+y2);

                var $thumbCropMask = $thumbnail.parent().find(".sharp-file-crop-mask");
                if(!$thumbCropMask.length)
                {
                    $thumbCropMask = $("<div>").addClass("sharp-file-crop-mask");
                    $thumbnail.before($thumbCropMask);
                }

                var wThumb = $thumbnail.width();
                var hThumb = $thumbnail.height();

                $thumbCropMask.css({
                    top: y1*hThumb,
                    left: x1*wThumb,
                    right: wThumb-x2*wThumb,
                    bottom: hThumb-y2*hThumb
                });

                $thumbCropMask.show();

                hide();
            });

            var $btnBack = $('<a>')
                .addClass("btn btn-back")
                .html('<i class="fa fa-arrow-left"></i>');
            $btnBack.click(function() {
                hide();
            });

            var $btnCancel = $('<a>')
                .addClass("btn btn-cancel")
                .html('<i class="fa fa-times"></i>')
                .data("filetagid", id);
            $btnCancel.click(function() {
                var $sharpFileTag = $("#sharp_filetag_"+$(this).data("filetagid"));
                var $hiddenCropField = $sharpFileTag.find(".sharp-file-crop-values");
                $hiddenCropField.val('');

                var $thumbCropMask = $sharpFileTag.find(".sharp-file-crop-mask");
                if($thumbCropMask.length) $thumbCropMask.remove();

                hide();
            });


            $wrapper.append($imgCrop);
            $wrapper.append($btnCrop);
            $wrapper.append($btnCancel);
            $wrapper.append($btnBack);

            $("body").append($wrapper);

            $imgCrop.imgAreaSelect({
                handles: true,
                parent: '.crop-img-wrapper',
                aspectRatio: params.ratio
            });

            var ias = $imgCrop.imgAreaSelect({ instance: true });

            var $link = $(this);

            $link.click(function(e) {
                e.preventDefault();

                $glassPane.show();
                $imgCrop.attr("src", $link.attr("href"));
                $wrapper.show();

                ias.update();
            });

            function hide()
            {
                $wrapper.hide();
                $glassPane.hide();
            }

        });

    }

})(jQuery);

$(window).load(function() {

    $('.sharp-file .sharp-file-crop').each(function() {

        createSharpFileCrop($(this));

    });

});

function createSharpFileCrop($el)
{
    var params = {};
    if($el.data("ratio") != undefined) params.ratio= $el.data("ratio");

    $el.sharp_fileCrop(params);
}