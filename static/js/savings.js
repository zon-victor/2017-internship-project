
function    amountBasedCalculator(e) {
    e.preventDefault();
    var $form = $("#amountCalculation");

    var $inputs = $form.find("input, select, button");

    $inputs.prop("disabled", false);

    var serializedData = $form.serialize();

    $.ajax({
        url: "/savings/calculate/target",
        type: "post",
        data: serializedData
    }).done(function (response) {
        if (isJSON(response)) {
            var errors = $.parseJSON(response);
            if (typeof errors.goal_error != 'undefined') {
                $("#savingsCalcContent").append(errors.goal_error);
            }
            if (typeof errors.interest_error != 'undefined') {
                $("#savingsCalcContent").append(errors.interest_error);
            }
            if (typeof errors.years_error != 'undefined') {
                $("#savingsCalcContent").append(errors.years_error);
            }
            if (typeof errors.months_error != 'undefined') {
                $("#savingsCalcContent").append(errors.months_error);
            }
            if (typeof errors.target_error != 'undefined') {
                $("#savingsCalcContent").append(errors.target_error);
            }
        } else {
            $("#savingsCalcContent").html(response);
        }
    })
}

function    periodBasedCalculator(e) {
    e.preventDefault();
    var $form = $("#periodicCalculation");

    var $inputs = $form.find("input, select, button");

    $inputs.prop("disabled", false);

    var serializedData = $form.serialize();

    $.ajax({
        url: "/savings/calculate/term",
        type: "post",
        data: serializedData
    }).done(function (response) {
        if (isJSON(response)) {
            var errors = $.parseJSON(response);
            if (typeof errors.goal_error != 'undefined') {
                $("#termCalcContent").append(errors.goal_error);
            }
            if (typeof errors.interest_error != 'undefined') {
                $("#termCalcContent").append(errors.interest_error);
            }
            if (typeof errors.years_error != 'undefined') {
                $("#termCalcContent").append(errors.years_error);
            }
            if (typeof errors.months_error != 'undefined') {
                $("#termCalcContent").append(errors.months_error);
            }
            if (typeof errors.amount_error != 'undefined') {
                $("#termCalcContent").append(errors.amount_error);
            }
        } else {
            $("#termCalcContent").html(response);
        }
    })
}