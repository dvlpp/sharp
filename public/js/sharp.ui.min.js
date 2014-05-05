$(window).load(function() {

    // ---
    // Manage links on row click in entity list
    // ---
    $("body.sharp-list table .entity-row .entity-data").each(function() {
        if($(this).data("link"))
        {
            $(this).click(function() {
                if($("#entity-list.reorder").length) return;
                window.location = $(this).data("link");
            });
        }
        else
        {
            $(this).parents(".entity-row").addClass("inactive");
        }
    });

    // ---
    // Switch entity list to reorder mode
    // ---
    $("body.sharp-list #sharp-reorder").click(function() {
        $("body").addClass("reorder");
        $("table#entity-list tbody").sortable({
            items: '.entity-row',
            handle: ".reorder-handle",
            axis: "y",
            helper: function(e, tr)
            {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function(index)
                {
                    // Set helper cell sizes to match the original sizes
                    $(this).width($originals.eq(index).outerWidth());
                });
                return $helper;
            }
        });
    });

    // ---
    // Ajax reorder call and switch back to normal mode
    // ---
    $("body.sharp-list #sharp-reorder-ok").click(function(e) {

        // Out of reorder mode.
        e.preventDefault();
        $("body").removeClass("reorder");

        // Ajax call
        var url = $(this).attr("href");
        var tabIds = [];
        $("#entity-list .entity-row").each(function() {
            tabIds.push($(this).data("entity_id"));
        });
        $.post(url, {entities:tabIds}, function(data) {
            if(data.err)
            {

            }
        }, "json");
    });


    // ---
    // Show confirm on delete entity click
    // ---
    $("body.sharp-form #sharpdelete").submit(function() {
        return confirm($(this).find("button").data("confirm"));
    });

    // ---
    // Manage ajax calls for .ajax links
    // ---
    $("body#sharp .ajax").click(function(e) {
        e.preventDefault();
        var link = $(this);
        var url = $(this).attr("href");
        var success = $(this).data("success");
        var failure = $(this).data("failure");
        $.post(url, {}, function(data) {
            if(data.err)
            {

            }
            else
            {
                window[success](link, data);
            }
        }, "json");
    });

});

function activate($source, jsonData)
{
    $source.parents(".state").removeClass("state-inactive").addClass("state-active");
}

function deactivate($source, jsonData)
{
    $source.parents(".state").removeClass("state-active").addClass("state-inactive");
}