(function($)
{
    $.fn.sharp_ref=function(options)
    {
        var defauts=
        {
            create: false,
            selectOnTab: true,
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

    $('.sharp-ref').each(function()
    {
        createSharpRef($(this));
    });

});

function createSharpRef($el)
{
    var options = {};
    if($el.data("create")) options.create = $el.data("create");

    $el.sharp_ref(options);
}