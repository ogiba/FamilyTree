function loadSelectedMemberToView(member) {
    $.post(window.location.href + "/edit", {
        "member": member
    }, function (response) {
        $("#rightContainer").html(response.template);
    })
}