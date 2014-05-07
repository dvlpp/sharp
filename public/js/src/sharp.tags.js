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
            render: {
                option_create: function(data, escape) {
                    return '<div class="create">Ajouter <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                }
            }
        };

        var params = $.extend(defauts, options);

        return this.each(function()
        {
            $(this).selectize(params);
        });
    }

}(jQuery));

$(window).load(function() {

    $('.sharp-tags').each(function()
    {
        createSharpTags($(this));
    });

});

function createSharpTags($el)
{
    var options = {};
    if($el.data("create")) options.create = $el.data("create");

    $el.sharp_tags(options);

    // Deal with to_add data: some options to add and select
    if($el.data("to_add"))
    {
        var tab = $el.data("to_add").split(',');
        for (k=0; k<tab.length; k++) {
            $el[0].selectize.addOption({value:tab[k],text:tab[k]});
            $el[0].selectize.addItem(tab[k]);
        }
    }
}