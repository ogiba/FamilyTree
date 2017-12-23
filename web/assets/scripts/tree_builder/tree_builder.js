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
    $("#memberFirstNameInput").val(member.firstName).data("value", member.id);
    $("#memberLastNameInput").val(member.lastName);

    var maidenName = member.maidenName;
    $("#memberMaidenNameInput").val(maidenName === null || maidenName === "" ? "-----" : member.maidenName);
    $("#memberBirthDateInput").val(convertDateToProperValue(member.birthDate));
    $("#memberDeathDateInput").val(convertDateToProperValue(member.deathDate));
}

function convertDateToProperValue(dateString) {
    if (dateString === null || dateString === "") {
        return null;
    }

    var date = new Date(dateString);
    return date.toISOString().substring(0, 10)
}

function saveMemberChanges() {
    var firstNameInput = $("#memberFirstNameInput");
    var memberId = firstNameInput.data("value");
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

        if (member.maidenName.trim() !== maidenName.trim()) {
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

        console.log(convertDate(birthDate));

        makeUpdateRequest(member);
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

        switch (response.statusCode) {
            case 200:
                reloadMembers();
                break;
            case 204:
                break;
            default:
                break;
        }
    })
}

function reloadMembers() {
    $("#membersContainer").html("");
    loadMembers();
}
