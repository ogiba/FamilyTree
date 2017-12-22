function saveFamily() {
    var name = $("#familyNameInput").val();

    if (!name.trim()) {
        console.log("Fmaily name cannot be empty");
        return;
    }

    $.post(window.location.href + "/save", {
        "familyName": name
    }, function (response) {
        console.log(response);
        alert(response);
    });
}