/**
 * Created by ogiba on 27.09.2017.
 */

//Bind by twig form php
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

//Bind by twig form php
function updatePost(postValue) {
    $.ajax({
        url: "edit/update",
        type: 'POST',
        data: {
            "id" : postValue,
            "title":  $("#postTitle").val(),
            "content": $("#postContent").val()
        },
        success: function(data){
            window.history.back();
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