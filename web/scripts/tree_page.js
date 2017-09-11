/**
 * Created by ogiba on 21.08.2017.
 */

$(document).ready(function () {
    $("#playground").css("padding-top", "" + $(".nav-bar").height() + "px");
});

function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}

function makePlace(ev) {
    ev.preventDefault();
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
    var targetItem = ev.target;
    var parentList = targetItem.parentNode;

    //ev.target.appendChild(document.getElementById(data));

    ev.target.parentElement.childNodes.forEach(function (item, index) {
        console.log(index);
        if (item.id === targetItem.id) {
            parentList.insertBefore(document.getElementById(data), parentList.childNodes[index]);
        }
    });
}
