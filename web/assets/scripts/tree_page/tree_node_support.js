function showMember(id, isPair) {
    $("#memberModal").modal({
        show: true
    }).one("show.bs.modal", function () {
        $(".modal-progress")
            .css("display", "flex")
            .css("opacity", "100")
    }).one("shown.bs.modal", function () {
        loadMeberDetails(id);
    }).one("hidden.bs.modal", function () {
        $(".modal-body").html("");
    });
}

function loadMeberDetails(id) {
    $.get(window.location.href + "/getDetails?id=" + id, function (response) {
        $(".modal-wrapper").html(response);
        $(".modal-progress").css("opacity", "0").on("transitionend", function () {
            $(".modal-progress").css("display", "none");
        });
    });
}