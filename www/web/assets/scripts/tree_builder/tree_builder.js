$(document).ready(function () {
    loadMembers();
});

function loadMembers() {
    $.get(window.location.href + "/getMembers", function (response) {
        $(".member-progress").on("transitionend", function () {
            $(".member-progress").css("display", "none");
        }).css("opacity", "0");

        if (response.statusCode === 200) {
            var members = new MemberList(response.content);

            $("#membersContainer").append(members.generateList());
        }
    });
}


var MemberList = function (members) {
    this.members = members;
};

MemberList.prototype.generateList = function () {
    var ul = $("<ul>");

    recursiveMemberElements(this.members).appendTo(ul);

    return ul;
};

function recursiveMemberElements(members) {

    var list = $("<ul>");

    if (members === null)
        return $("<li>");

    if (members.children.length > 0) {
        for (var i = 0; i < members.children.length; i++) {
            var child = members.children[i];
            list.append(recursiveMemberElements(child));
        }
    }

    var userLabelSpan = $("<span/>", {
        html: members.firstName + " " + members.lastName,
        click: (function (item) {
            return function (event) {
                event.stopPropagation();
                console.log(item);
                loadSelectedMemberToView(item.id)
            }
        })(members)
    });

    var li = $("<li>", {
        html: userLabelSpan
    }).append(list);
    return li;
}

function loadSelectedMemberToView(memberID) {
    var progress = prepareProgressView();

    $("#rightContainer").html(progress);

    $.post(window.location.href + "/edit", {
        "member": memberID
    }, function (response) {
        $(".details-progress").on("transitionend", function () {
            $("#rightContainer").html(response.content.template);

            initDropzone();
        }).css("opacity", "0");
    })
}

function initDropzone() {
    var dropzoneElement = $("#imageForm.dropzone");

    dropzoneElement.dropzone({
        maxFiles: 1,
        addRemoveLinks: true,
        dictCancelUpload: dropzoneElement.data("cancel-upload"),
        dictRemoveFile: dropzoneElement.data("remove-file"),
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

function convertDateToProperValue(dateString) {
    if (dateString === null || dateString === "") {
        return null;
    }

    var date = new Date(dateString);
    return date.toISOString().substring(0, 10)
}

function saveMemberChanges(memberId) {
    $.get(window.location.href + "/getMembers?id=" + memberId, function (response) {
        if (response.statusCode !== 200) {
            return;
        }

        var member = response.content;

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

        console.log(convertDate(birthDate));

        makeUpdateRequest(member);
    });
}

function cancelEditing() {
    $("#rightContainer").html("");
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

function convertDate(dateString) {
    var date = new Date(dateString);
    return date.getDate() + "." + (date.getMonth() + 1) + "." + date.getFullYear();
}

function makeUpdateRequest(member) {
    $.post(window.location.href + "/update?id=" + member.id, {
        "member": member
    }, function (response) {
        console.log(response);

        var alertToShow = null;
        switch (response.statusCode) {
            case 200:
                alertToShow = prepareAlert(AlertType.success, response.message);
                reloadMembers();
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

function reloadMembers() {
    $("#membersContainer").html("");
    loadMembers();
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
