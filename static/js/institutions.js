
function loadContent(el, ev) {
    ev.preventDefault();
    $(el).parent().siblings().children().removeClass('activeInstMenu_');
    $(el).addClass('activeInstMenu_');
    var path = $(el).data('path'), target = $(el).data('target');
    $.ajax({
        url: path,
        type: "get",
    }).done(function (response) {
        $("#" + target).html(response).show();
    })
}

function getDeductions(el, ev) {
    ev.preventDefault();
    var path = $(el).data('path');

    $(el).siblings().removeClass('activeAccBtn_');
    $(el).addClass('activeAccBtn_');

    $.ajax({
        url: path,
        type: "get"
    }).done(function (response) {
        $('#data').html(response);
    })
}

(function getActiveYear() {
    $.ajax({
        url: '/institution/deductions/year',
        type: "get"
    }).done(function (response) {
        $('#' + response).siblings().removeClass('activeAccBtn_');
        $('#' + response).addClass('activeAccBtn_');
    })
})()

function manageDeductions(el, ev) {
    ev.preventDefault();
    var path = $(el).data('path');

    $(el).siblings().removeClass('activeAccBtn_');
    $(el).addClass('activeAccBtn_');

    $.ajax({
        url: path,
        type: "get"
    }).done(function (response) {
        $('#data').html(response);
    })
}

function uploadDeductionsFile(el, e) {
    var showUploaded = document.getElementById('uploaded');
    var file = el.files[0];
    var textType = /text.*/;

    if (file.type.match(textType)) {
        var reader = new FileReader();

        reader.onload = function (e) {
            showUploaded.innerText = csvToJSON(reader.result);
        }

        reader.readAsText(file);
    } else {
        showUploaded.innerText = "File not supported!"
    }
}

function csvToJSON(csv) {

    var lines = csv.split("\n");

    var result = [];

    var headers = lines[0].split(",");

    for (var i = 1; i < lines.length; i++) {

        var obj = {};
        var currentline = lines[i].split(",");

        for (var j = 0; j < headers.length; j++) {
            var header = headers[j], modified;
            if (header.match(/action/) !== null) {
                modified = 'action';
            } else if (header.match(/salary/) !== null) {
                modified = 'salary_month';
            } else if (header.match(/employee/) !== null) {
                modified = 'employee_no';
            } else if (header.match(/employer/) !== null) {
                modified = 'employer';
            }  else if (header.match(/reason/) !== null) {
                modified = 'reason';
            }  else if (header.match(/amount/) !== null) {
                modified = 'amount';
            }
            obj[modified] = currentline[j];
        }

        result.push(obj);

    }
    
    return JSON.stringify(result); //JSON
}