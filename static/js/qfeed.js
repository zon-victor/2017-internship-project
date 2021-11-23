var year = "none", month = "none", deduction = "none", request, years = true, smonth = false;

$(".year").click(function (e) {
    e.preventDefault();
    year = $(this).attr('id');
    $(".adeduction").removeClass("adeduction");
    $(this).addClass("adeduction");
    loadDeductions(deduction, year, month)
});

$(".month").click(function (e) {
    e.preventDefault();
    if (month == $(this).attr('id')) {
        month = 'none';
        $(".amonth").removeClass("amonth");
    } else {
        month = $(this).attr('id');
        $(".amonth").removeClass("amonth");
        $(this).addClass("amonth");
    }
    loadDeductions(deduction, year, month)
});

$(".bmonth").click(function (e) {
    e.preventDefault();
    if (month == $(this).attr('id')) {
        month = 'none';
        $(".abmonth").removeClass("abmonth");
    } else {
        month = $(this).attr('id');
        $(".bmonth").removeClass("abmonth");
        $(this).addClass("abmonth");
    }
    smonth = true;
    loadDeductions(deduction, year, month)
});

$(".deduction").click(function (e) {
    e.preventDefault();
    deduction = $(this).attr('id');
    loadDeductions(deduction, year, month)
    $("#services").children().removeClass('activeCatOpt');
    $(this).addClass('activeCatOpt');
});

function onYearClick(elem, e) {
    e.preventDefault();
    year = $(elem).attr('id');
    $(".adeduction").removeClass("adeduction");
    $(elem).addClass("adeduction");
    loadDeductions(deduction, year, month)
}

function    reloadYears() {
    $.ajax({
        url: "/home/years",
        type: "get"
    }).done(function (response) {
        $("#qhome_content_left").html(response);
        $("#" + year).addClass('adeduction');
    });
}

function    loadDeductions(deduction, year, month) {
    if (!years) {
        reloadYears();
        years = true;
        $("#data").html('');
        $("#months_left").show();
        $("#months_right").show();
    } else if (year !== 'none' && deduction !== 'none') {
        $("#months_left").show();
        $("#months_right").show();
    }
    $("#savingsMenuBar").css({"display": "none"})
    $("#affordabilityMenuBar").css({"display": "none"})
    if (deduction === 'all') {
        getAllDeductions(deduction, year, month);
    }
    else if (deduction !== 'none' && year !== 'none' && month !== 'none') {
        filterByMonth(deduction, year, month);
    } else if (deduction !== 'none' && year !== 'none' && month === 'none') {
        filterByYear(deduction, year, month);
    }
    if (smonth) {
        $("#" + month).addClass('amonth');
    }
}

function getAllDeductions(deduction, year, month) {
    $.ajax({
        url: "/deductions/" + deduction + "/all/" + year + "/" + month,
        type: "get"
    }).done(function (response) {
        $("#data").html(response);
        console.log(response)
    }); 
}

function    filterByMonth(deduction, year, month) {
    $.ajax({
        url: "/deductions/" + deduction + "/monthly/" + year + "/" + month,
        type: "get"
    }).done(function (response) {
        $("#data").html(response);
    });
}

function    filterByYear(deduction, year, month) {
    var c = 0;
    $.ajax({
        url: "/deductions/" + deduction + "/yearly/" + deduction + "/" + year,
        type: "get"
    }).done(function (response) {
        c++;
        $("#data").html(response);
    });

}

function    loadNewAffordabilityTest(elem, event) {
    event.preventDefault();
    $("#newTest").click();
    $("#services").children().removeClass('activeCatOpt');
    $(elem).addClass('activeCatOpt');
}

function    renderAffordability(elem, event) {
    event.preventDefault();
    request = $.ajax({
        url: "/affordability/render",
        type: "get"
    });
    request.done(function (response) {
        $("#months_left").hide();
        $("#months_right").hide();
        $("#savingsMenuBar").css({"display": "none"})
        $("#affordabilityMenuBar").css({"display": "block"})
        $("#affordabilityMenuBar").children().removeClass("activeOption");
        $(elem).addClass("activeOption");
        $("#data").html(response);
        renderSaved();
        $('#adjustTest').hide();
        years = false;
    });
}

function    renderSaved() {
    $.ajax({
        url: "/affordability/saved",
        type: "get"
    }).done(function (response) {
        $("#qhome_content_left").html(response);
        $("#outcome").prop("disabled", true);
    });
}

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

function    renderSavings(elem) {
    request = $.ajax({
        url: "/savings/render",
        type: "get"
    });
    request.done(function (response) {
        $("#months_left").hide();
        $("#months_right").hide();
        $("#data").html(response);
        $("#savingsMenuBar").css({"display": "block"});
        $("#affordabilityMenuBar").css({"display": "none"});
        $("#services").children().removeClass('activeCatOpt');
        $(elem).addClass('activeCatOpt');
        $("#planOne").click();
        renderGoals();
        years = false;
    });
}

function    renderGoals(elem) {
    request = $.ajax({
        url: "/savings/status",
        type: "get"
    });
    request.done(function (response) {
        $("#qhome_content_left").html(response);
    });
}

function    renderSpecificGoals(elem, event) {
    event.preventDefault();
    var id = $(elem).data("target");
    var kind = $(elem).html();
    $("#ggContent").children().removeClass('ggSubContentActive');
    $('.ggH_').children().css({'background-color': 'white', 'color': '#5d8aa8'});
    $("#" + id).addClass('ggSubContentActive');
    $('.ggSubContentActive').children().css({'color': '#5d8aa8', 'font-weight': 'bold'});
    $(elem).css({'background-color': '#5d8aa8', 'color': 'white'});
    $("._ggLoad").html("MANAGE " + kind + " GOALS")
    if (kind == "UNINITIALIZED") {
        $("#manageUninitialized").css({'color': '#93AF00'}).click();
    } else if (kind == "INITIALIZED") {
        $("#manageInitialized").css({'color': '#93AF00'}).click();
    } else if (kind == "REACHED") {
        $("#manageReached").css({'color': '#93AF00'}).click();
    }
}

function    planCalculatorOne(el, e) {
    e.preventDefault();
    var id = el.id;
    var $form = $("#requiredSavings");

    var $inputs = $form.find("input, select, button");

    $inputs.prop("disabled", false);

    var serializedData = $form.serialize();

    $.ajax({
        url: "/savings/target",
        type: "post",
        data: serializedData
    }).done(function (response) {
        $("#savingsMenuBar").children().removeClass("activeOption");
        $("#" + id).addClass("activeOption");
        $("#data").html(response);
    })
}

function    planCalculatorTwo(el, e) {
    e.preventDefault();
    var id = el.id;
    var $form = $("#targetSavings");

    var $inputs = $form.find("input, select, button");

    $inputs.prop("disabled", false);

    var serializedData = $form.serialize();

    $.ajax({
        url: "/savings/term",
        type: "post",
        data: serializedData
    }).done(function (response) {
        $("#savingsMenuBar").children().removeClass("activeOption");
        $("#" + id).addClass("activeOption");
        $("#data").html(response);
    })
}

function loadUnitializedGoals(el, e) {
    e.preventDefault();
    var id = el.id;
    $("#savingsMenuBar").children().removeClass("activeOption");
    $("#" + id).addClass("activeOption");
    manageAllGoals(el, e);
}

function loadItializedGoals(el, e) {
    e.preventDefault();
    var id = el.id;
    $("#savingsMenuBar").children().removeClass("activeOption");
    $("#" + id).addClass("activeOption");
    manageAllGoals(el, e);
}

function loadReachedGoals(el, e) {
    e.preventDefault();
    var id = el.id;
    $("#savingsMenuBar").children().removeClass("activeOption");
    $("#" + id).addClass("activeOption");
    manageAllGoals(el, e);
}

function deleteAllGoals(elem, event) {
    event.preventDefault();
    var status = $(elem).data("status");
    $.ajax({
        url: "/savings/delete/" + status,
        type: "get",
    }).done(function (response) {
        if (response.match(/uninitialized goals not found/) !== null) {
            $('#uninitializedPlan').click();
        } else if (response.match(/initialized goals not found/) !== null) {
            $('#initializedPlan').click();
        } else if (response.match(/reached goals not found/) !== null) {
            $('#reachedPlan').click();
        }
        $("#data").html(response);
    })
}


function deleteGoal(elem, event) {
    event.preventDefault();
    var id = elem.id;
    var status = $(elem).data("status");
    $.ajax({
        url: "/savings/remove/" + id + "/" + status,
        type: "get",
    }).done(function (response) {
        if (isJSON(response)) {
            var data = $.parseJSON(response);
            if (typeof data.none !== 'undefined') {
                $('#' + status + 'Plan').click();
                $("#data").html(data.none);
            }
        } else if (response.match(/successful/) !== null) {
            $("a[data-goal='" + id + "']").hide();
            $(".goal_details").html('\
                <div style="position: relative; color: #708090; width: 100%; height: 100%; line-height: 100%; text-align: center; font-size: 16px; font-weight: bold">\n\
                    SELECT A GOAL FROM LISTED GOALS\n\
                </div>');
        } else if (response.match(/failed/) !== null) {
            alert('Failed to delete goal');
        }
    })
}

function manageAllGoals(elem, event) {
    event.preventDefault();
    var status = $(elem).data("status");
    var clicked = $(elem).data("clicked");
    $.ajax({
        url: "/savings/" + status,
        type: "get",
    }).done(function (response) {
        if (isJSON(response)) {
            var data = $.parseJSON(response);
            if (typeof data.none !== 'undefined') {
                if (clicked == 'clicked') {
                    $('#' + status + 'Plan').click();
                }
                $("#data").html(data.none);
            }
        } else {
            if (clicked == 'clicked') {
                $('#' + status + 'Plan').click();
            }
            $("#data").html(response);
        }
    })
}

function loadGoalDetails(elem, event) {
    event.preventDefault();
    var id = elem.id;
    var status = $(elem).data("status");
    $.ajax({
        url: "/savings/goal/" + id + "/" + status,
        type: "get",
    }).done(function (response) {
        if (isJSON(response)) {
            var data = $.parseJSON(response);
            if (typeof data.none !== 'undefined') {
                $(".goal_details").html(data.none);
            }
        } else {
            $(".goal_details").html(response);
        }
    })
}

function startAllGoals(elem, event) {
    event.preventDefault();
    var id = elem.id, period = $(elem).data("period");
    var fd = new FormData();
    fd.append('id', id);
    fd.append('period', period);
    $.ajax({
        url: "/savings/start_all",
        type: "post",
        data: fd,
        processData: false,
        contentType: false,
    }).done(function (response) {
        if (isJSON(response)) {
            var data = $.parseJSON(response);
            if (typeof data.none !== 'undefined') {
                console.log(data);
            }
        } else {
            console.log(response);
        }
    })

}

function stopAllGoals(elem, event) {
    event.preventDefault();
    var status = $(elem).data("status");
    console.log(status);
}

function initializeGoal(elem, event) {
    event.preventDefault();
    var id = elem.id, period = $(elem).data("period");

    var fd = new FormData();
    fd.append('id', id);
    fd.append('period', period);

    $.ajax({
        url: "/savings/initialize",
        type: "post",
        data: fd,
        processData: false,
        contentType: false,
    }).done(function (response) {
        if (isJSON(response)) {
            var data = $.parseJSON(response);
            if (typeof data.none !== 'undefined') {

            }
        } else if (response === 'update successful') {
            $("#initializedPlan").click();
        }
    })
}

$("#modify_test").on("click", function (event) {
    event.preventDefault();
    modifyTestResults()
});

function modifyTestResults() {
    $("#modify_test").html("Retest");
    console.log($("#modify_test"))
    $("#modify_test").unbind("click");

    var $form = $("#testHistory");

    var $inputs = $form.find("input, select");

    $inputs.prop("disabled", false);
    $("#cat").prop("disabled", true);
    $("#outcome").prop("disabled", true);
}

function sendFeedback(ev) {
    ev.preventDefault();
    var message = document.getElementById('myFeedback').value;
    if (message === '') {
        alert('Feedback cannot be empty');
        return;
    }
    var fd = new FormData()

    fd.append('feedback', message);
    $.ajax({
        url: "/access/feedback/user",
        type: "post",
        data: fd,
        processData: false,
        contentType: false,
    }).done(function (response) {
        var data = $.parseJSON(response);
        if (typeof data.success !== 'undefined') {
            $("#data").append(data.success);
        }
    })
}