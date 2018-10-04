function showRemoveModal() {
    $("#removeModal").modal({backdrop: 'static', keyboard: false})
        .modal("toggle");
}

function proceedRemoving(postId) {
    $.post(window.location.href + "/remove", {
        "post": postId
    }, function (response) {
        var statusCode = response.statusCode;

        switch (statusCode) {
            case 422:
                console.log(response.message);
                break;
            case 200:
                window.history.back();
                $("#removeModal").modal("hide");
                break;
        }
    })
}

