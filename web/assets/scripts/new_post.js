/**
 * Created by ogiba on 27.09.2017.
 */

function savePost() {
    $.ajax({
        url: "new/save",
        type: 'POST',
        data: {
            "title":  $("#postTitle").val(),
            "content": $("#postContent").val()
        },
        success: function(data){

        },
        error: function (error) {
            var resp = JSON.parse(error.responseText);

            $(".progress").removeClass("shown");

            var loginInfoField = $("#loginInfo");
            loginInfoField.text(resp.message);
            loginInfoField.addClass("shown");
        }
    });
}