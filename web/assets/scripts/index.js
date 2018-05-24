/**
 * Created by ogiba on 20.08.2017.
 */

window.addEventListener("DOMContentLoaded", function () {
    setupScrollListener();
    setupAlertListener();
});

function setupScrollListener() {
    var navBar = $('#nav-bar');
    var titleBar = $('.app-title');
    var distance = navBar.offset().top;
    var titleBarDistane = titleBar.offset().top;

    $(window).scroll(function () {
        if ($(this).scrollTop() >= distance) {
            navBar.addClass("fixed-top");
        } else {
            navBar.removeClass("fixed-top");
        }
    });
}

function setupAlertListener() {
    $("#cookieInfo").on("close.bs.alert", function () {
        updateCookieInfo();
    });
}

function selectPage(pageNumber) {
    console.log(pageNumber);
    $.get("/?postPage=" + pageNumber, function (data) {
        $("#postsContainer").html("").html(data);
    });
}

function updateCookieInfo() {
    setCookies("cookieInfo", "displayed");
}


function getCookie(key) {
    let cookies = document.cookie;

    if (!cookies.includes(key)) {
        return null;
    }

    let values = cookies.split(";");
    let cookieValue = values.find(t => t.trim().startsWith(key));
    return cookieValue.replace(key, "");
}

function setCookies(key, value) {
    document.cookie = `${key}=${value}`;

    return document.cookie.includes(key);
}
