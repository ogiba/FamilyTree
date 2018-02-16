function loadSelectedMemberToView(id) {
    var progress = prepareProgressView();

    $("#rightContainer").html(progress);

    $.post(window.location.href + "/edit", {
        "member": id
    }, function (response) {
        $(".details-progress").on("transitionend", function () {
            $("#rightContainer").html(response.template);

            $("#imageForm").dropzone({
                maxFiles: 1,
                addRemoveLinks: true,
                maxfilesexceeded: function (file) {
                    this.removeFile(file);

                    var alertToShow = prepareAlert(AlertType.warning, "Cannot upload more than one image");

                    $("#alertContainer").append(alertToShow);
                }
            });
        }).css("opacity", "0");
    })
}

function prepareProgressView() {
    var progress = $("<div>", {
        "class": "progress"
    });

    $("<div>", {
        "class": "indeterminate"
    }).appendTo(progress);

    return $("<div>", {
        "class": "details-progress",
        html: progress
    });
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

/**
 * Call method responsible or updating member in DB
 * @param member
 */
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
 * Remove edit member view from DOM
 */
function cancelEditing() {
    $("#rightContainer").html("");
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