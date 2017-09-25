/**
 * Created by ogiba on 25.09.2017.
 */
$('#submit').click(function () {
    $.ajax({
        url: "login/loginUser",
        type: 'POST',
        data: {
            "username":  $("#usernameInput").val(),
            "password": $("#passwordInput").val()
        },
        success: function(data){
            alert(data);
        }
    });
});