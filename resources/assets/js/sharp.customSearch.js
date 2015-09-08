(function ($) {
    $.fn.sharp_customSearch = function (options) {
        var defauts = {
            minchar: 0
        };

        var params = $.extend(defauts, options);
        var token = $("input[name=_token]").val();

        return this.each(function () {

            var $searchField = $(this);
            var remoteUrl = $searchField.data('remote');
            var template = $searchField.data('template');
            var idattr = $searchField.data('idattr');

            var $sharpField = $searchField.parents(".sharp-field-customSearch");
            var $hiddenField = $sharpField.find("input[type=hidden]");
            var $modal = $sharpField.find(".customSearch-modal");
            var $modalBody = $modal.find(".list-group");
            var $modalLoading = $modal.find(".loading");
            var $resultTemplate = $sharpField.find(".panel-template");

            var xhr = null;

            $(this).keypress(function(event) {
                if ( event.which == 13 ) {
                    event.preventDefault();

                    var query = $(this).val().trim();
                    if(query.length < params.minchar) {
                        return;
                    }

                    $modalBody.empty();
                    $modalLoading.removeClass("hidden");
                    $modal.modal();

                    xhr = $.post(remoteUrl, {
                        _token: token,
                        q: query,
                        template: template,
                        idattr: idattr

                    }, function(data) {
                        $modalLoading.addClass("hidden");

                        for(var k=0; k<data.length; k++) {
                            var $link = $("<a>")
                                .data("id", data[k].__id)
                                .data("object", data[k]["object"])
                                .html(data[k].html);

                            $modalBody.append($('<li>').addClass("list-group-item").append($link));
                        }

                    }, "json").fail(function(e) {
                        $modal.modal('hide');
                    });
                }
            });

            // Abort request on modal discard
            $modal.on('hide.bs.modal', function (e) {
                if(xhr) xhr.abort();
            });

            // Item click
            $modal.on('click', '.list-group a', function(e) {
                // Handle hidden field update
                var id = $(this).data("id");
                $hiddenField.val(id);
                var $resultPanel = createCustomSearchResultPanelFromTemplate($resultTemplate, $(this).data("object"));
                $resultTemplate.before($resultPanel);

                // Clear text field
                $searchField.val("");
                $searchField.parent(".search").addClass("hidden");

                $modal.modal('hide');
            });

            $sharpField.on('click', '.close', function(e) {
                $searchField.parent(".search").removeClass("hidden");
                $(this).parents(".panel-valuated").hide().remove();
                $hiddenField.val("");
            });

        });
    }

}(jQuery));

$(window).load(function () {

    $('.sharp-customSearch').each(function () {
        createSharpCustomSearch($(this));
    });

});

function createSharpCustomSearch($el) {
    var options = {};
    if ($el.data("minchar")) options.minchar = $el.data("minchar");

    $el.sharp_customSearch(options);
}

function createCustomSearchResultPanelFromTemplate($template, data) {
    var html = $template[0].outerHTML;

    html = html.replace(/{[^{}]+}/g, function(key){
        return data[key.replace(/[{}]+/g, "")] || "";
    });

    return $(html).removeClass("panel-template").addClass("panel-valuated");
}