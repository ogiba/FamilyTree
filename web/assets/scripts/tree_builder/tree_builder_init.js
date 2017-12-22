function saveFamily() {
    var name = $("#familyNameInput").val();

    if (!name.trim()) {
        console.log("Family name cannot be empty");
        return;
    }

    $.post(window.location.href + "/save", {
        "familyName": name
    }, function (response) {
        console.log(response);

        switch (response.statusCode) {
            case 200:
                window.location.href += "";
                break;
            case 422:
                break;
            default:
                break;
        }
    });
}