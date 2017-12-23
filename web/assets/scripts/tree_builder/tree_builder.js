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
    $("#memberFirstNameInput").val(member.firstName);
    $("#memberLastNameInput").val(member.lastName);
    $("#memberMaidenNameInput").val(member.maidenName === null ? "-----" : member.maidenName);
    $("#memberBirthDateInput").val(convertDateToProperValue(member.birthDate));
    $("#memberDeathDateInput").val(convertDateToProperValue(member.deathDate));
}

function convertDateToProperValue(dateString) {
    if (dateString === null) {
        return null;
    }

    var date = new Date(dateString);
    return date.toISOString().substring(0, 10)
}
