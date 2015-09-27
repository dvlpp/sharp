$(window).load(function () {

    // ---
    // Manage links on row click in entity list
    // ---
    $("body.sharp-list table .entity-row .entity-data").each(function () {
        if ($(this).data("link")) {
            $(this).click(function () {
                if ($("#entity-list.reorder").length) return;
                window.location = $(this).data("link");
            });

        } else {
            $(this).parents(".entity-row").addClass("inactive");
        }
    });

    // ---
    // Switch entity list to reorder mode
    // ---
    $("body.sharp-list #sharp-reorder").click(function () {
        $("body").addClass("reorder");

        dragula([document.querySelector("#entity-list tbody")], {
            moves: function (el, source, handle, sibling) {
                return el.classList.contains('entity-row')
                    && handle.classList.contains('reorder-handle');
            },
            mirrorContainer: document.querySelector("#entity-list tbody")
        });
    });

    // ---
    // Ajax reorder call and switch back to normal mode
    // ---
    $("body.sharp-list #sharp-reorder-ok").click(function (e) {

        // Out of reorder mode.
        e.preventDefault();
        $("body").removeClass("reorder");

        // Ajax call
        var url = $(this).attr("href");
        var tabIds = [];
        $("#entity-list .entity-row").each(function () {
            tabIds.push($(this).data("entity_id"));
        });

        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
            },
            dataType: 'json',
            data: {
                entities: tabIds
            },
            error: function (jqXhr, json, errorThrown) {

            }
        });
    });

    // ---
    // Show confirm on delete entity click (with form post)
    // ---
    $("body.sharp-list .sharp-delete").click(function () {
        if (confirm($(this).data("confirmdelete"))) {
            showPageOverlay();
            $("form#" + $(this).data("form")).submit();
        }
    });

    // ---
    // Hide empty command list
    // ---
    $("body.sharp-list .actions .dropdown").each(function () {
        if($(this).find(".dropdown-menu").children().length == 0) {
            $(this).remove();
        }
    });

    // ---
    // Ajax change state call
    // ---
    $("body.sharp-list .change-entity-state").click(function (e) {

        e.preventDefault();
        var $stateLink = $(this);

        showPageOverlay();

        $.ajax({
            url: $stateLink.prop("href"),
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
            },
            dataType: 'json',
            data: {
                instance: $stateLink.data("instance"),
                state: $stateLink.data("state")
            },
            success: function(data) {
                hidePageOverlay();
                $stateLink.parents(".dropdown")
                    .find(".entity-state")
                    .css('color', $stateLink.data("color"))
                    .prop("title", $stateLink.data("title"));
            },
            error: function (jqXhr, json, errorThrown) {
                hidePageOverlay();
                if (jqXhr.status == 500) {
                    sweetAlert(jqXhr.responseJSON.error, jqXhr.responseJSON.message, "error");
                }
            }
        });
    });

});

function activate($source, jsonData) {
    $source.parents(".state").removeClass("state-inactive").addClass("state-active");
}

function deactivate($source, jsonData) {
    $source.parents(".state").removeClass("state-active").addClass("state-inactive");
}

var $pageOverlay = null;
function showPageOverlay() {
    if(!$pageOverlay) {
        $pageOverlay = $("<div>").addClass("sharp-page-overlay hidden");
        $("body").append($pageOverlay);
    }
    $pageOverlay.removeClass("hidden");
}

function hidePageOverlay() {
    if($pageOverlay) {
        $pageOverlay.addClass("hidden");
    }
}