
function sendHelpQuery(ev) {
    ev.preventDefault();
    var name = document.getElementById('name').value,
            email = document.getElementById('email').value,
            query = document.getElementById('query').value;
    if (name === '' || email === '' || query === '') {
        alert('All fields are required');
        return;
    }
    var fd = new FormData()

    fd.append('name', name);
    fd.append('email', email);
    fd.append('query', query);

    $.ajax({
        url: "/access/help/query",
        type: "post",
        data: fd,
        processData: false,
        contentType: false,
    }).done(function (response) {
        var data = $.parseJSON(response);
        if (typeof data.success !== 'undefined') {
            $("#show_hlogin").click();
            $("#help_chat").append(data.success);
        }
    })
}

function showThis(el, ev) {
    ev.preventDefault();
    var hide = $(el).data('hide'), show = $(el).data('show');
    $("#" + show).css({'display': 'block'});
    $("#" + hide).css({'display': 'none'});
}

function loginForHelp(ev) {
    ev.preventDefault();
    var email = document.getElementById('guestmail').value,
            key = document.getElementById('key').value;
    if (email === '' || key === '') {
        alert('All fields are required');
        return;
    }
    var fd = new FormData()

    fd.append('email', email);
    fd.append('key', key);

    $.ajax({
        url: "/access/help/login",
        type: "post",
        data: fd,
        processData: false,
        contentType: false,
    }).done(function (response) {
        var data = $.parseJSON(response);
        if (typeof data.success !== 'undefined') {
            window.location = data.success;
        }
    })
}
