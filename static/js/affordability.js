
$("#affordabilityTest").submit(function (e) {
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serialize();

    reset = $.ajax({
        url: "/affordability/test",
        type: "post",
        data: formData
    }).done(function (response) {
        $("#testErrors").html('');
        if (isJSON(response)) {
            var errors = $.parseJSON(response);
            if (typeof errors.amount_error != 'undefined') {
                $("#testErrors").append(errors.amount_error);
            }
            if (typeof errors.net_salary_error != 'undefined') {
                $("#testErrors").append(errors.net_salary_error);
            }
            if (typeof errors.category_error != 'undefined') {
                $("#testErrors").append(errors.category_error);
            }
        } else {
            $("#affordResults").css({"display": "block"});
            $("#affordResultsOutput").prepend(response);
        }
    })
});

function    saveResult(e) {
    e.preventDefault();
    var name = document.getElementById("service_name").value;
    if (name === '') {
        alert('Please provide the name of the service.');
        return;
    }
    var $form = $("#testFeedback");
    
    var $inputs = $form.find("input, select, button");
    
    $inputs.prop("disabled", false);
    
    var serializedData = $form.serialize();
    
    
    $.ajax({
        url: "/affordability/save",
        type: "post",
        data: serializedData
    }).done(function (response) {
        $("#affordResults").css({"display": "none"});
        $("#outcome").prop("disabled", true);
        renderSaved();
    })
    
}

function    adjustAffordability(elem, event) {
    event.preventDefault();
    $("#affordabilityMenuBar").children().removeClass("activeOption");
    $(elem).addClass("activeOption");
    $('.hideme').hide();
}

function    viewTest(elem, event) {
    event.preventDefault();
    var id = elem.id;
    
    $.ajax({
        url: "/affordability/view/"+id,
        type: "get",
    }).done(function (response) {
        var header = $(elem).html();
        $('#adjustTest').show().click();
        $("#affordHistory").css({"display": "block"});
        $("#affordHistoryHeader").html('Adjust ' + header.replace('<br>', '') + ' Affordability Test');
        $("#affordHistoryOutput").html('');
        $("#affordHistoryOutput").html(response);
        $(elem).siblings().css({'color': '#5d8aa8'})
        $(elem).css({'color': '#93AF00'})
        checkPreviousTestResult($(elem).attr('id'));
        checkNextTestResult($(elem).attr('id'));
    })
}

function modifyTestResults(elem, event) {
    event.preventDefault();
    $(elem).hide();
    $("#modify_test").show();

    $("#name").prop("disabled", false);
    $("#value").prop("disabled", false);
    $("#cnet").prop("disabled", false);
    $("#cat").prop("disabled", false);
    $("#outcome").prop("disabled", true);
}


function saveModifiedTest(elem, event) {
    event.preventDefault();
    var $form = $("#testHistory");
      
    var fd = $form.serialize();
    $.ajax({
        url: "/affordability/update/"+$(elem).data("id"),
        type: "post",
        data: fd,
    }).done(function (response) {
        $("#testErrors").html('');
        if (isJSON(response)) {
            var errors = $.parseJSON(response);
            if (typeof errors.amount_error != 'undefined') {
                $("#testErrors").append(errors.amount_error);
            }
            if (typeof errors.net_salary_error != 'undefined') {
                $("#testErrors").append(errors.net_salary_error);
            }
            if (typeof errors.category_error != 'undefined') {
                $("#testErrors").append(errors.category_error);
            }
            if (typeof errors.success != 'undefined') {
                document.getElementById("affordHistoryHeader").value = 'adjust ' + errors.service_name + ' affordability test';
                document.getElementById("name").value = errors.service_name;
                document.getElementById("value").value = errors.amount;
                document.getElementById("cnet").value =  errors.net_salary;
                document.getElementById("cat").value = errors.category;
                document.getElementById("outcome").value = errors.outcome;
                $("#outcome").prop("disabled", true);
            }
        } else {
            $("#affordResults").css({"display": "block"});
            $("#affordResultsOutput").prepend(response);
        }
        renderSaved();
    })
}

function closeTestResults(elem, event) {
    event.preventDefault();
    var close = $(elem).data("close"), restore = $(elem).data("restore");
    $("#" + close).hide();
    $("#" + restore).css({'color': '#5d8aa8'});
    $('#newTest').click();
}

function deleteTestResults(elem, event) {
    event.preventDefault();
    var id = $(elem).data("delete");
    var test = $("#" + id).html();
    if (confirm("You are about to delete "+ test.replace("<br>", '')+" affordability test") == true) {
        $.ajax({
            url: "/affordability/delete/"+ id,
            type: "get",
        }).done(function (response) {
           if (isJSON(response)) {
                var data = $.parseJSON(response);
                if (typeof data.success !== 'undefined') {
                     $("#affordHistory").css({"display": "none"});
                     renderSaved();
                     closeTestResults(elem, event);
                } else if (typeof data.failure !== 'undefined') {
                    $("#affordHistoryOutput").append("<br>FAILED TO DELETE THIS AFFORDABILITY TEST<br>");
                }
            }
        })
    }
}

function viewNextTestResult(elem, event) {
    event.preventDefault();
    var curr_id = $(elem).data("nav"), nxt = $("#" + curr_id).next();
    if (nxt !== 'undefined') {
        nxt.click();
        checkNextTestResult(nxt.attr('id'));
    }
}

function viewPreviousTestResult(elem, event) {
    event.preventDefault();
    var curr_id = $(elem).data("nav"), prev = $("#" + curr_id).prev();
    if (prev !== 'undefined') {
        prev.click();
        checkPreviousTestResult(prev.attr('id'));
    }
}

function checkNextTestResult(id) {
    var test = $("#" + id).next();
    if (typeof test.attr('id') === 'undefined') {
        $("#view_nxt").hide();
    }
}

function checkPreviousTestResult(id) {
    var test = $("#" + id).prev();
    if (typeof test.attr('id') === 'undefined') {
        $("#view_prev").hide();
    }
}