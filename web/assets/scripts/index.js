/**
 * Created by ogiba on 20.08.2017.
 */

window.addEventListener("DOMContentLoaded", function () {
    setupScrollListener();
});

function setupScrollListener() {
    var navBar =  $('.nav-bar');
    var titleBar = $('.app-title');
    var distance = navBar.offset().top;
    var titleBarDistane = titleBar.offset().top;

    $(window).scroll(function() {
        if ( $(this).scrollTop() >= distance ) {
            navBar.addClass('attach-top');
        } else {
            navBar.removeClass("attach-top");
        }
    });
}

function selectPage(pageNumber) {
    console.log(pageNumber);
    $.get("/?postPage=" + pageNumber, function (data) {
        $("#postsContainer").html("").html(data);
    });
}

