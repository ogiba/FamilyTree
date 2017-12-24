$(document).ready(function () {
    loadMembers();
});

function loadMembers() {
    $.get(window.location.href + "/getMembers", function (response) {
        var members = new MemberList(response);

        $("#membersContainer").append(members.generateList());
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

    if (members.children.length > 0) {
        for (var i = 0; i < members.children.length; i++) {
            var child = members.children[i];
            list.append(recursiveMemberElements(child));
        }
    }

    var li = $("<li>", {
        html: members.firstName + " " + members.lastName,
        click: (function (item) {
            return function (event) {
                event.stopPropagation();
                console.log(item);
                loadSelectedMemberToView(item)
            }
        })(members)
    }).append(list);

    return li;
}

function loadSelectedMemberToView(member) {
    // $("#memberFirstNameInput").val(member.firstName).data("value", member.id);
    // $("#memberLastNameInput").val(member.lastName);
    //
    // var maidenName = member.maidenName;
    // $("#memberMaidenNameInput").val(maidenName === null || maidenName === "" ? "-----" : member.maidenName);
    // $("#memberBirthDateInput").val(convertDateToProperValue(member.birthDate));
    // $("#memberDeathDateInput").val(convertDateToProperValue(member.deathDate));
    $.post(window.location.href + "/edit", {
        "member": member
    }, function (response) {
        $("#rightContainer").html(response.template);
    })
}

function convertDateToProperValue(dateString) {
    if (dateString === null || dateString === "") {
        return null;
    }

    var date = new Date(dateString);
    return date.toISOString().substring(0, 10)
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

        var newParent = $("#memberParentSelect").find("option:selected").val();

        if (newParent !== member.parent) {
            member.parent = newParent;
        }

        var newPartner = $("#memberPartnerSelect").find("option:selected").val();

        if (member.partner === null || newPartner !== member.partner.id) {
            member.partner = newPartner;
        }

        console.log(convertDate(birthDate));

        makeUpdateRequest(member);
    });
}

function cancelEditing() {
    $("#rightContainer").html("");
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

        switch (response.statusCode) {
            case 200:
                $("#alertContainer").append(prepareAlert(AlertType.success, "Member updated"));
                reloadMembers();
                break;
            case 204:
                $("#alertContainer").append(prepareAlert(AlertType.info, "No changes to save"));
                break;
            case 422:
                $("#alertContainer").append(prepareAlert(AlertType.warning, "Failed updating member"));
            default:
                break;
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
