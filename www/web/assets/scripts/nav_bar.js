/**
 * Created by ogiba on 21.08.2017.
 */
function navigateToScene(sceneAddress) {
    window.location.href = sceneAddress;
}

// window.addEventListener("DOMContentLoaded", function () {
//     setupScrollListener();
// });
//
// function setupScrollListener() {
//     var navBar =  $('.nav-bar');
//     var distance = navBar.height()/2;
//
//     $(window).scroll(function() {
//         if ( $(this).scrollTop() >= distance ) {
//             navBar.addClass('attach-top');
//         } else {
//             navBar.removeClass("attach-top");
//         }
//     });
// }