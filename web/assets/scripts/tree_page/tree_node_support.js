function showMember(id, isPair) {
    $("#memberModal").modal({
        show:true
    }).one("shown.bs.modal" ,function () {
        loadMeberDetails(id);
    }).one("hidden.bs.modal",function () {
        $(".modal-body").html("");
    });
}

function loadMeberDetails(id) {
    $.get(window.location.href + "/getDetails?id=" + id, function (response) {
        $(".modal-dialog").html(response);
    });
}