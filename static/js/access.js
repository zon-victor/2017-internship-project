var request;
var reset;

function isJSON(item) {
    item = typeof item !== "string"
            ? JSON.stringify(item)
            : item;

    try {
        item = JSON.parse(item);
    } catch (e) {
        return false;
    }

    if (typeof item === "object" && item !== null) {
        return true;
    }

    return false;
}

$("#register").submit(function (event) {

    event.preventDefault();

    var $form = $(this);

    var $inputs = $form.find("input, select, button");

    var serializedData = $form.serialize();
    $inputs.prop("disabled", true);

    request = $.ajax({
        url: "/access/register",
        type: "post",
        data: serializedData
    });

    request.done(function (response) {
        var data = $.parseJSON(response);
        if (typeof data.reroute != 'undefined' && typeof data.email != 'undefined') {
            $("#wrform").css({
                'box-shadow': 'none',
                'padding-top': '15px',
                'color': '#CD5C5C',
                'text-align': 'left',
                'font-size': '16px',
                'line-height': '24px',
                'border': 'none',
                'text-transform': 'lowercase'
            }).html(data.reroute);
            $("#wmid").css('border', 'none');
            $("#wright").html(data.email);
        }
        if (typeof data.registered != 'undefined') {
            $("#rules").hide();
            $("#reg_errors").hide();
            $("#verify").html(data.registered).slideDown('slow');
        }
        if (typeof data.errors != 'undefined') {
            $("#access_errors").html(data.errors);
            $("#reg_errors").css({'position': 'relative'}).slideDown('slow');
            $("#rules").css({'color': 'rgba(93,138,168, 0.75)'});
            $inputs.prop("disabled", false);
        }

    });
});

//Password rules
$("#password").mousedown(function () {
    $("#rules").slideDown('slow');
    $("#password").off();
});

//Reset Password
$("#forgot_pass").on("click", function (event) {
    event.preventDefault();
    $("#login").hide();
    $("#wlform").css({'height': '166px'});
    $("#reset").show();
});

//Back to login
$("#login_again").on("click", function (event) {
    event.preventDefault();
    $("#reset").hide();
    $("#wlform").css({'height': '242px'});
    $("#login").show();
});

//Forgot password: Send User's email to server
$("#reset").submit(function (e) {
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serialize();

    reset = $.ajax({
        url: "/access/reset",
        type: "post",
        data: formData
    });

    reset.done(function (response) {
        var data = $.parseJSON(response);
        $("#reset").html(data.success);
    })
})

function    sendToAlternativeEmail(e) {
    e.preventDefault();
    var $form = $("#testMail");
    var formData = $form.serialize();

    $.ajax({
        url: "/access/alternative",
        type: "post",
        data: formData
    }).done(function (response) {
        if (isJSON(response)) {
            var data = $.parseJSON(response);
            if (typeof data.redirect !== 'undefined') {
                location.replace(data.redirect);
             } else {
                 return;
                }
        }
    })
}


