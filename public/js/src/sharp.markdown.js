(function ($, Editor, undefined) {
    'use strict';
    if (typeof Editor === undefined) {
        throw new Error("Can't find Editor!");
    }

    $.fn.sharp_markdown = function (options)
    {
        var defauts={};

        var params = $.extend(defauts, options);

        return this.each(function (index, element) {
            var editor;
            params.element = element;

            editor = new Editor(params);
            $(element).data('editor', editor);

            editor.render();
        });
    };

}(this.jQuery, this.Editor));

$(window).load(function() {

    $('textarea.sharp-markdown').each(function()
    {
        createSharpMarkdown($(this));
    });

});

var config_to_toolbar = {
    B:['bold', toggleBold],
    I:['italic', toggleItalic],
    Q:['quote', toggleBlockquote],
    U:['unordered-list', toggleUnOrderedList],
    O:['ordered-list', toggleOrderedList],
    L:['link', drawLink],
    G:['image', drawImage],
    P:['preview', togglePreview],
    F:['fullscreen', toggleFullScreen]
};

var defaultToolbar = "";

function createSharpMarkdown($el)
{
    var tb = $el.data("toolbar") ? $el.data("toolbar") : defaultToolbar;
    var toolBar = [];
    for (var i = 0, len = tb.length; i < len; i++)
    {
        var char = tb[i];
        toolBar.push((char == ' ') ? "|" : {name:config_to_toolbar[char][0], action:config_to_toolbar[char][1]});
    }

    $el.sharp_markdown({
        toolbar:toolBar
    });

    if($el.data("height") != undefined)
    {
        // We can only modify height AFTER the creation
        $el.parents(".sharp-field-markdown").find(".CodeMirror").css("height", $el.data("height"));
    }
}