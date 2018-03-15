function loadSelectedMemberToView(id) {
    var progress = prepareProgressView();

    $("#rightContainer").html(progress);

    $.post(window.location.href + "/edit", {
        "member": id
    }, function (response) {
        $(".details-progress").on("transitionend", function () {
            $("#rightContainer").html(response.template);

            initDropzone();
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

function initDropzone() {
    $("#imageForm").dropzone({
        maxFiles: 1,
        addRemoveLinks: true,
        dictCancelUpload: $("#imageForm").data("cancel-upload"),
        dictRemoveFile: $("#imageForm").data("remove-file"),
        maxfilesexceeded: function (file) {
            this.removeFile(file);

            var alertToShow = prepareAlert(AlertType.warning, $("#imageForm").data("max-file-msg"));

            $("#alertContainer").append(alertToShow);
        },
        removedfile: function (file) {
            var ref;
            if (file.previewElement) {
                if ((ref = file.previewElement) !== null) {
                    ref.parentNode.removeChild(file.previewElement);
                }
            }

            removeTemporaryUploadedFile();
            return this._updateMaxFilesReachedClass();
        }
    });
}

function removeTemporaryUploadedFile() {
    $.get(window.location.href + "/removeTempImage", function (response) {
        switch (response.statusCode) {
            case 200:
                console.log(response.message);
                break;
            case 422:
                var preparedAlert = prepareAlert(AlertType.warning, response.message);
                $("#alertContainer").append(preparedAlert);
                break;
        }
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

        var familyHead = $("#familyHead");

        if (familyHead !== undefined && familyHead.length > 0) {
            var baseNodeChecked = familyHead.is(":checked");

            if (member.base === 0 && baseNodeChecked) {
                member.base = baseNodeChecked ? 1 : 0;
            }
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
                reloadMembers();
                alertToShow = prepareAlert(AlertType.success, response.message);
                break;
            case 204:
                alertToShow = prepareAlert(AlertType.info, response.message);
                break;
            case 422:
                alertToShow = prepareAlert(AlertType.warning, response.message);
                break;
            default:
                break;
        }

        if (alertToShow !== null) {
            $("#alertContainer").append(alertToShow);
        }
    })
}

function removeImage(id) {
    $.post(window.location.href + "/removeImage", {
        "memberId": id
    }, function (response) {
        switch (response.statusCode) {
            case 200: {
                $(".uploaded-file-container").remove();
                $(".dropzone").removeClass("dropzone-hide");
                break;
            }
            default:
                break;
        }
    });
}

/**
 * Remove edit member view from DOM
 */
function cancelEditing() {
    $("#rightContainer").html("");
}

function removeUser(id) {
    var removeUserModal = $('#removeUserModal');


    removeUserModal.find("button.ok").off().on("click", function () {
        $('#removeUserModal').modal("hide");

        console.log("Clicked ok");
        sendRemoveUserRequest(id);
    });

    removeUserModal.find("button.cancel").off().on("click", function () {
        $('#removeUserModal').modal("hide");

        console.log("Clicked cancel");
    });

    removeUserModal.modal("toggle");
}

function sendRemoveUserRequest(id) {
    $.post(window.location.href + "/removeMember", {
        "memberId": id
    }, function (response) {

        var alert = null;

        switch (response.statusCode) {
            case 200:
                alert = prepareAlert(AlertType.success, response.message);
                reloadMembers();
                break;
            case 422:
                alert = prepareAlert(AlertType.warning, response.message);
                break;
            default:
                break;
        }

        if (alert)
            $("#alertContainer").append(alert);
    })
}

function reloadMembers() {
    //TODO: Need to implement reloading list of members
    $.get(window.location.href + "/getMembers", function (response) {
        $("#membersContainer").html(response.content.template);
        $("#rightContainer").html("");
    });
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