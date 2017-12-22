function saveFamily() {
    var location = window.location.href;
    $.post(location += "/save", {}, function (response) {
        alert(response);
    });
}