/* global $, document */

function lookup_area(query) {
    var area = $('.area-wrap');
    $.getJSON('/api/zipcode.php', {q: query}, function(data) {
        area.html(data.result);
    });
}
function validate_username(query) {
    var check = $('.username-check');
    var error_msg = $(".activation > .error");
    var valid_chars_pattern = new RegExp('^[a-z][a-z0-9]*');
    if(query.length == 0) {
        error_msg.html('');
        check.removeClass("failed");
        check.removeClass("check");
        error_msg.addClass("hidden");
        return;
    }
    if( !valid_chars_pattern.test(query) ) {
        error_msg.html('Brukernavnet kan kun inneholde små bokstaver og tall (tall kan ikke være første tegn)');
        check.removeClass("failed");
        check.removeClass("check");
        error_msg.removeClass("hidden");
        return;
    }
    $.getJSON('/api/username.php', {q: query}, function(data) {
        console.log(data);
        if(data.result) {
            error_msg.html('Brukernavnet er desverre opptatt, prøv en annen variant');
            check.addClass("failed");
            check.removeClass("check");
            error_msg.removeClass("hidden");
        } else {
            check.addClass("check");
            check.removeClass("failed");
            error_msg.addClass("hidden");
            error_msg.html('');
        }
    });
}
$(document).ready(function() {
    if( $(".activation").length > 0 ) {
        var zipcode_field = $('input[name=zipcode]');
        var username_field= $('input[name=username]');

        if(zipcode_field.val().length !== 0) {
            lookup_area(zipcode_field.val());
        }

        zipcode_field.on('keyup keypress', function(event) {
            var query = event.target.value;
            lookup_area(query);
        });

        username_field.on('keyup keypress', function(event) {
            var query = event.target.value;
            validate_username(query);
        });
    }

});

