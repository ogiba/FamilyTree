function loadSelectedMemberToView(member) {
    $.post(window.location.href + "/edit", {
        "member": member.id
    }, function (response) {
        $("#rightContainer").html(response.template);
    })
}

function saveMemberChanges(memberId) {
    $.get(window.location.href + "/getMembers?id=" + memberId, function (member) {
        var firstName = $("#memberFirstNameInput").val();

        if (member.firstName.trim() !== firstName.trim()) {
            member.firstName = firstName;
        }

        var lastName = $("#memberLastNameInput").val();

        if (member.lastName.trim() !== lastName.trim()) {
            member.lastName = lastName;
        }

        var maidenName = $("#memberMaidenNameInput").val();

        if (member.maidenName !== null && member.maidenName.trim() !== maidenName.trim()) {
            member.maidenName = maidenName;
        }

        var birthDate = $("#memberBirthDateInput").val();

        if (member.birthDate !== birthDate) {
            member.birthDate = birthDate;
        }

        var deathDate = $("#memberDeathDateInput").val();

        if (member.deathDate !== deathDate) {
            member.deathDate = deathDate;
        }

        var firstParent = $("#memberFirstParentSelect").find("option:selected").val();

        if (firstParent !== member.firstParent) {
            member.firstParent = firstParent;
        }

        var secondParent = $("#memberSecondParentSelect").find("option:selected").val();

        if (secondParent !== member.secondParent) {
            member.secondParent = secondParent;
        }

        var newPartner = $("#memberPartnerSelect").find("option:selected").val();

        if (member.partner === null || newPartner !== member.partner.id) {
            member.partner = newPartner;
        }

        member.description = $("#memberDescriptionArea").val();

        makeUpdateRequest(member);
    });
}

function makeUpdateRequest(member) {
    $.post(window.location.href + "/update?id=" + member.id, {
        "member": member
    }, function (response) {
        console.log(response);

        var alertToShow = null;
        switch (response.statusCode) {
            case 200:
                alertToShow = prepareAlert(AlertType.success, "Member updated");
                break;
            case 204:
                alertToShow = prepareAlert(AlertType.info, "No changes to save");
                break;
            case 422:
                alertToShow = prepareAlert(AlertType.warning, "Failed updating member");
            default:
                break;
        }

        if (alertToShow !== null) {
            $("#alertContainer").append(alertToShow);
        }
    })
}

/**
 *
 * @param {AlertType|String} type
 * @param {String} message
 * @returns {void|*|jQuery}
 */
function prepareAlert(type, message) {
    var closeSpan = $("<span/>", {
        "aria-hidden": "true",
        html: "&times;"
    });

    var closeBtn = $("<button/>", {
        "type": "button",
        "class": "close",
        "data-dismiss": "alert",
        "aria-label": "Close",
        "click": function () {
            $(".alert").remove();
        }
    }).append(closeSpan);

    return $("<div/>", {
        text: message,
        "class": "alert alert-" + type + " alert-dismissable",
        "role": "alert"
    }).append(closeBtn);
}

var AlertType = {
    primary: "primary",
    secondary: "secondary",
    success: "success",
    warning: "warning",
    danger: "danger",
    info: "info",
};