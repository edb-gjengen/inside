function init_validation() {
    $("#form_migrate").validate({
        rules: {
            username: {
                required: true,
                rangelength: [3, 15],
                lettersonly: true,
                remote: "./migration/user_exists.php",
            },
            password: {
                required: true,
                minlength: 8,
            },
            password_check: {
                required: true,
                minlength: 8,
                equalTo: "#id_password",
            },
        },
        submitHandler: function(form) {
            var form_username = $("#id_username").val();
            var form_password = $("#id_password").val();
            var form_password_check = $("#id_password_check").val();
            var form_uid = $("#id_uid").val();
            $.ajax({
                url: "./migration/submit_user.php",
                type: 'POST',
                data: {
                    username: form_username,
                    password: form_password,
                    password_check: form_password_check,
                },
                dataType: 'json',
                success: function(result) {
                    if(result.result == 'success') {
                        $("#infomodal").html("Brukeren din er klar for fremtiden, <strong>tusen takk!</strong>");
                        $("#infomodal").dialog('option',{
                            buttons: {
                                "Ok": function() { 
                                    $(this).dialog('close');
                                },
                            },
                        });
                    }
                    else {
                        /* Output the errors (will never happend). */
                        var output = '<div style="padding: 0 .7em;" class="ui-state-error ui-corner-all">';
                        for(var error in result.errors) {
                            output += '<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span><strong>'+ error +':</strong> ' + result.errors[error][0] + '</p>';
                        }
                        output += '</div>';
                        $("#infomodal").prepend(output);
                    }
                },
            });
            return false;
        },
    });
}

$( document ).ready( function() {
    $( "#infomodal" ).dialog({
        /* 1. Info about the migration. */
        autoOpen: false,
        height: 450,
        width: 600,
        modal: true,
        buttons: {
            "Ok": function() { 
                /* 2. Load in the user and pass form. */
                $(this).load('./migration/form.php', function() {
                    init_validation();
                    $(this).dialog('option',{
                        buttons: {
                            "Lagre brukerdata": function() { 
                                $("#form_migrate").submit();
                            }
                        }
                    });
                });
            }, 
            "Ikke nå": function() { 
                /* The user chooses to not migrate. */
                $(this).dialog("close"); 
            } 
        }
    });
});
