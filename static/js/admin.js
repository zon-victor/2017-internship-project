
function loadContent(el, ev) {
    ev.preventDefault();
    $(el).parent().siblings().children().removeClass('activeMenu_');
    $(el).addClass('activeMenu_');
    $("#admin_content").children().hide();
    var target = $(el).data('target'), path = $(el).data('path');
    $.ajax({
        url: path,
        type: "get",
    }).done(function (response) {
        $("#" + target).html(response).show();
    })
}

function getAccounts(el, ev) {
    ev.preventDefault();
    $(el).siblings().removeClass('activeAccBtn_');
    $(el).addClass('activeAccBtn_');
    var display = $(el).data('display'), path = $(el).data('path');
    $.ajax({
        url: path,
        type: "get",
    }).done(function (response) {
        $("." + display).html(response);
    })
}

function toggleUserAccess(el, ev) {
    ev.preventDefault();
    var email = $(el).data('email'), access = $(el).data('access'), target = $(el).data('status'), fd = new FormData();

    fd.append('email', email);
    fd.append('access', access);
    $.ajax({
        url: "/home/access/user",
        type: "post",
        data: fd,
        processData: false,
        contentType: false,
    }).done(function (response) {
        if (response.match(/success/) !== null) {
            var str = $("#" + target).html(), status;
            if (access == 'no') {
                $(el).data('access', 'yes').html('Enable Account');
                status = str.replace('Enabled', 'Disabled');
            } else {
                $(el).data('access', 'no').html('Disable Account');
                status = str.replace('Disabled', 'Enabled');
            }
            $("#" + target).html(status);
        }
    })
}

function toggleInstitutionAccess(el, ev) {
    ev.preventDefault();
    var username = $(el).data('username'), access = $(el).data('access'), target = $(el).data('status'), fd = new FormData();

    fd.append('username', username);
    fd.append('access', access);
    $.ajax({
        url: "/home/access/institution",
        type: "post",
        data: fd,
        processData: false,
        contentType: false,
    }).done(function (response) {
        if (response.match(/success/) !== null) {
            var str = $("#" + target).html(), status;
            if (access == 'no') {
                $(el).data('access', 'yes').html('Enable Account');
                status = str.replace('Enabled', 'Disabled');
            } else {
                $(el).data('access', 'no').html('Disable Account');
                status = str.replace('Disabled', 'Enabled');
            }
            $("#" + target).html(status);
        }
    })
}

function deleteError(el, ev) {
    ev.preventDefault();
    var error_id = $(el).data('id'), fd = new FormData();

    fd.append('error_id', error_id);
    fd.append('multi', 'no');
    $.ajax({
        url: "/home/errors/delete",
        type: "post",
        data: fd,
        processData: false,
        contentType: false,
    }).done(function (response) {
        $("#errors").html(response);
    })
}

function deleteErrors(el, ev) {
    ev.preventDefault();
    var fd = new FormData();

    fd.append('multi', 'yes');
    $.ajax({
        url: "/home/errors/delete",
        type: "post",
        data: fd,
        processData: false,
        contentType: false,
    }).done(function (response) {
        $("#errors").html(response);
    })
}

function changeExceptionStatus(el, ev) {
    ev.preventDefault();
    var fd = new FormData(),
            line = $(el).data('line'),
            filename = $(el).data('file'),
            status = $(el).data('change'),
            target = $(el).data('target'),
            button = $(el).data('button');

    fd.append('line', line);
    fd.append('filename', filename);
    fd.append('status', status);
    $.ajax({
        url: "/home/exceptions/update",
        type: 'post',
        data: fd,
        processData: false,
        contentType: false
    }).done(function (response) {
        if (response.match(/success/) !== null) {
            if (status === 'solved') {
                $('.' + target).html('solved');
                $('.' + button).html('Set As Unsolved');
                $('.' + button).data('change', 'unsolved');
            } else if (status === 'unsolved') {
                $('.' + target).html('unsolved');
                $('.' + button).html('Set As Solved');
                $('.' + button).data('change', 'solved');
            }
        }
    })
}

function deleteException(el, ev) {
    ev.preventDefault();
    var fd = new FormData(),
            line = $(el).data('line'),
            filename = $(el).data('file');

    fd.append('line', line);
    fd.append('filename', filename);
    fd.append('multi', 'no');
    $.ajax({
        url: "/home/exceptions/delete",
        type: "post",
        data: fd,
        processData: false,
        contentType: false,
    }).done(function (response) {
        $("#exceptions").html(response);
    })
}

function deleteAllExceptions(el, ev) {
    ev.preventDefault();
    var fd = new FormData();

    fd.append('multi', 'yes');
    $.ajax({
        url: "/home/exceptions/delete",
        type: "post",
        data: fd,
        processData: false,
        contentType: false,
    }).done(function (response) {
        $("#exceptions").html(response);
    })
}

function showHelpMessages(el, ev) {
    ev.preventDefault();
    var email = $(el).data('email'),
            key = $(el).data('key');
    if (email === '' || key === '') {
        alert('All fields are required');
        return;
    }
    var fd = new FormData()

    fd.append('email', email);
    fd.append('key', key);

    $.ajax({
        url: "/home/help/user",
        type: "post",
        data: fd,
        processData: false,
        contentType: false,
    }).done(function (response) {
        $('#data').html(response);
    })
}

function sendHelpSolution(el, ev) {
    ev.preventDefault();
    var name = $(el).data('name'),
            email = $(el).data('email'),
            key = $(el).data('key'),
            solution = document.getElementById('solution').value;
    
    if (name === '' || email === '' || solution === '') {
        alert('All fields are required');
        return;
    }
    
    var fd = new FormData();

    fd.append('name', name);
    fd.append('email', email);
    fd.append('key', key);
    fd.append('solution', solution);

    $.ajax({
        url: "/home/help/solution",
        type: "post",
        data: fd,
        processData: false,
        contentType: false,
    }).done(function (response) {
                var data = $.parseJSON(response);
        if (typeof data.success !== 'undefined') {
            $("#q_and_a").append(data.success);
        }
    })
}