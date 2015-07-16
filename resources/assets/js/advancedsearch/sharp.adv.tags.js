/**
 * Define selectize plugin to authorise tag "negation"
 */
Selectize.define('negation', function(options) {
    if (this.settings.mode === 'single') return;

    options = $.extend({
        label     : '+',
        title     : 'Negate',
        className : 'negate',
        append    : true
    }, options);

    var self = this;
    var html = '<a href="javascript:void(0)" class="' + options.className + '" tabindex="-1" title="' + options.title + '">' + options.label + '</a>';

    var append = function(html_container, html_element) {
        var pos = html_container.search(/(<\/[^>]+>\s*)$/);
        return html_container.substring(0, pos) + html_element + html_container.substring(pos);
    };

    this.setup = (function() {
        var original = self.setup;
        return function() {
            // override the item rendering method to add the button to each
            if (options.append) {
                var render_item = self.settings.render.item;
                self.settings.render.item = function(data) {
                    return append(render_item.apply(this, arguments), html);
                };
            }

            original.apply(this, arguments);

            // add event listener
            this.$control.on('click', '.' + options.className, function(e) {
                e.preventDefault();
                if (self.isLocked) return;

                var $item = $(e.currentTarget).parent();

                if($(this).html() == "+")
                {
                    $item.addClass("negative");
                    $(this).html("-");
                }
                else
                {
                    $item.removeClass("negative");
                    $(this).html("+");
                }
            });

        };
    })();

});

(function($)
{
    $.fn.sharp_tags=function(options)
    {
        var defauts=
        {
            create: false,
            selectOnTab: false,
            persist: false,
            maxItems: null,
            sortField: null,
            plugins: ['negation']
        };

        var params = $.extend(defauts, options);

        return this.each(function()
        {
            $(this).selectize(params);
        });
    }

}(jQuery));

$(window).load(function() {

    $('.sharp-advancedsearch-tags').each(function()
    {
        createSharpAdvancedSearchTags($(this));
    });

});

function createSharpAdvancedSearchTags($el)
{
    $el.sharp_tags({});
}