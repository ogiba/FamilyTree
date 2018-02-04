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
            navBar.addClass('fixed');
        } else {
            navBar.removeClass("fixed");
        }
    });
}