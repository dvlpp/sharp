(function ($)
{
    $.fn.sharp_markdown = function(options)
    {
        var defauts={
            showToolbar:false,
            autofocus:false
        };

        var params = $.extend(defauts, options);

        return this.each(function () {
            var mm = mirrorMark($(this)[0], params);
            mm.render();
        });
    };

})(jQuery);

$(window).load(function() {

    $('textarea.sharp-markdown').each(function()
    {
        createSharpMarkdown($(this));
    });

});

function createSharpMarkdown($el)
{
    var tb = $el.data("toolbar") ? $el.data("toolbar") : false;

    $el.sharp_markdown({
        showToolbar:tb
    });


    if($el.data("height") != undefined)
    {
        // We can only modify height AFTER the creation
        $el.parents(".sharp-field-markdown").find(".CodeMirror").css("height", $el.data("height"));
    }
}