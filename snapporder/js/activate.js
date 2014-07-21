/* global $, document */

function lookup_area(query) {
    var area = $('.area-wrap');
    $.getJSON('/api/zipcode.php', {q: query}, function(data) {
        console.log(data);
        area.html(data.result);
    });
}
$(document).ready(function() {
    var zipcode_field = $('input[name=zipcode]');
    if(zipcode_field.val().length !== 0) {
        lookup_area(zipcode_field.val());
    }

    zipcode_field.on('keyup', function(event) {
        var query = event.target.value;
        lookup_area(query);
    });
});
