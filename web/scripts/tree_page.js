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
    if (ev.target.parentElement.className === "tree-container") {
        var label = "";
        ev.target.childNodes.forEach(function (item, i) {
            item.childNodes.forEach(function (childrenItem, j) {
                if (childrenItem.localName === "label") {
                    label = childrenItem.innerText;
                }
            });
        });
        ev.target.innerHTML = label;
    }
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

    if (ev.target.parentElement.localName === "ul") {
        ev.target.parentElement.childNodes.forEach(function (item, index) {
            console.log(index);
            if (item.id === targetItem.id) {
                parentList.insertBefore(document.getElementById(data), parentList.childNodes[index]);
            }
        });
    } else {
        var droppedItem = document.getElementById(data)
        rebuildItem(droppedItem)
        if (ev.target.parentElement.className !== "tree-container") {
            ev.target.appendChild(droppedItem);
        }
    }
}

function rebuildItem(elem) {
    var text = elem.childNodes[0];
    elem.innerHTML = "";

    $("<div/>", {
        id: "container_img_" + elem.id,
        "style": "height: 50%; text-align: center;"
    }).appendTo(elem);

    $("<img>", {
    }).appendTo("#container_img_" + elem.id);

    $("<div/>", {
        id: "container_" + elem.id,
        "style": "height: 50%;"
    }).appendTo(elem);

    $("<label/>", {
        text: text.data
    }).appendTo("#container_" + elem.id);


}
