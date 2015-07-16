(function($)
{
    $.fn.sharp_date=function(options)
    {
        var defauts=
        {
            timepicker: false,
            datepicker:true,
            scrollInput: false,
            step:30,
            lang:'fr',
            format:'d/m/Y',
            dayOfWeekStart:1,
            mask:true

        };

        var params = $.extend(defauts, options);

        return this.each(function()
        {
            var $hiddenTSField = $(this).prev(".sharp-date-timestamp");

            params.onChangeDateTime = function(dp,$input) {
                $hiddenTSField.val(dp);
            };

            $(this).datetimepicker(params);
        });
    }

}(jQuery));

$(window).load(function() {

    $('.sharp-date').each(function()
    {
        createSharpDate($(this));
    });

});

function createSharpDate($el)
{
    var options = {};
    if($el.data("lang")) options.lang= $el.data("lang");
    if($el.data("has_date")) options.datepicker= $el.data("has_date");
    if($el.data("has_time")) options.timepicker = $el.data("has_time");
    if($el.data("step_time")) options.step = $el.data("step_time");
    if($el.data("max_date")) options.maxDate = $el.data("max_date");
    if($el.data("min_date")) options.minDate = $el.data("min_date");
    if($el.data("max_time")) options.maxTime = $el.data("max_time");
    if($el.data("min_time")) options.minTime = $el.data("min_time");
    if($el.data("start_date")) options.startDate = $el.data("start_date");
    if($el.data("format")) options.format = $el.data("format");
    if($el.data("start_on_sunday")) options.dayOfWeekStart = 0;

    $el.sharp_date(options);
}