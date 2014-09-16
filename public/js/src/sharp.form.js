// ---
// Manage tab click with URL hashes
// ---
$(function(){
    var hash = window.location.hash;
    hash && $('ul.entity-tabs a[href="' + hash + '"]').tab('show');

    $('.entity-tabs a').click(function (e) {
        e.preventDefault();

        $(this).tab('show');

        if(history.pushState)
        {
            history.pushState(null, null, this.hash);
        }
        else
        {
            location.hash = this.hash;
        }
    });
});