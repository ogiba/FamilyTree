/**
 * Created by ogiba on 21.08.2017.
 */
var treeItems = [];

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

    if (ev.target.parentElement.localName === "ul") {
        var draggedItem = document.getElementById(data);

        ev.target.parentElement.childNodes.forEach(function (item, index) {
            console.log(index);
            if (item.id === targetItem.id) {
                if (draggedItem.parentElement.className === "tree-container") {
                    var label = "";
                    draggedItem.childNodes.forEach(function (item, i) {
                        item.childNodes.forEach(function (childrenItem, j) {
                            if (childrenItem.localName === "label") {
                                label = childrenItem.innerText;
                            }
                        });
                    });
                    draggedItem.innerHTML = label;
                }

                parentList.insertBefore(draggedItem, parentList.childNodes[index]);
            }
        });
    } else {
        var droppedItem = document.getElementById(data);
        var parentElement = ev.target.parentElement;
        if (parentElement.classList[0] !== "draggableTest" &&
            parentElement.localName !== "li" &&
            parentElement.id.indexOf("container_") === -1 &&
            parentElement.childNodes[1].childNodes.length < 2) {

            if (droppedItem.parentElement.localName === "ul" && droppedItem.id.startsWith("person")) {
                rebuildPersonItem(droppedItem)
            } else if (droppedItem.parentElement.localName === "ul" && droppedItem.id.startsWith("connection")) {
                rebuildConnectionItem(droppedItem)
            }

            if (parentElement.localName === "td" && parentElement.parentElement.localName == "tr") {
                console.log("col:"+parentElement.cellIndex+" row:"+parentElement.parentElement.rowIndex);

                if (parentElement.parentElement.rowIndex == 0) {
                    addNewRow(true);

                    $('html, body').animate({
                        scrollTop: $(droppedItem).offset().top
                    }, 0);

                    treeItems.forEach(function(item){
                        item.row += 1;
                        console.log(item);
                    });
                } else if (parentElement.parentElement.rowIndex == parentElement.parentElement.parentElement.childElementCount - 1) {
                    addNewRow(false);
                }

                if (parentElement.cellIndex == 0) {
                    addNewColumn(true);

                    $('html, body').animate({
                        scrollLeft: $(droppedItem).offset().left + 500
                    }, 0);

                    treeItems.forEach(function(item){
                        item.column += 1;
                        console.log(item);
                    });
                } else if (parentElement.cellIndex == parentElement.parentElement.childElementCount - 1) {
                    addNewColumn(false);
                }

                var node = new TreeNode(0, parentElement.cellIndex, parentElement.parentElement.rowIndex);
                treeItems.push(node)
                console.log(treeItems);
            }

            ev.target.appendChild(droppedItem);
        }
    }
}

function rebuildPersonItem(elem) {
    var text = elem.childNodes[0];
    elem.innerHTML = "";

    $("<div/>", {
        "class" : "left-dot"
    }).appendTo(elem);

    $("<div/>", {
        "class" : "top-dot"
    }).appendTo(elem);

    $("<div/>", {
        "class" : "bottom-dot"
    }).appendTo(elem);

    $("<div/>", {
        "class" : "right-dot"
    }).appendTo(elem);

    $("<div/>", {
        id: "container_img_" + elem.id,
        "style": "height: 50%; text-align: center;"
    }).appendTo(elem);

    $("<img>", {
    }).appendTo("#container_img_" + elem.id);

    $("<div/>", {
        id: "container_" + elem.id,
        "style": "height: 50%; line-height: 1;"
    }).appendTo(elem);

    $("<label/>", {
        text: text.data,
        "style": "margin-bottom: 0;"
    }).appendTo("#container_" + elem.id);

    $("<div/>", {
        text: "00.00.0000",
        "style": "font-size: 14px; margin-bottom: 0;"
    }).appendTo("#container_" + elem.id);

    $("<div/>", {
        text: "00.00.0000",
        "style": "font-size: 14px; margin-bottom: 0;"
    }).appendTo("#container_" + elem.id);
}

function rebuildConnectionItem(elem) {
    var text = elem.childNodes[0];
    elem.innerHTML = "";

    $("<div/>", {
        "style":"margin-top: 50%; background: black; height: 1px;"
    }).appendTo(elem);
}

function addNewRow(inFirstPlace) {
    var tbody = $(".table").children()[0];
    if (tbody.localName === "tbody") {
        var newRow = $(tbody.children[0].cloneNode(true));

        if (inFirstPlace) {
            newRow.prependTo(tbody);
        } else {
            newRow.appendTo(tbody);
        }
    }
}

function addNewColumn(inFirstPlace) {
    var tbody = $(".table").children()[0];
    if (tbody.localName === "tbody") {
        Array.from(tbody.children).forEach(function(item){
            var clonedCell = $(item.children[0].cloneNode(true));

            if (inFirstPlace) {
                clonedCell.prependTo(item);
            } else {
                clonedCell.appendTo(item);
            }
        });
    }
}

var TreeNode = function(id, column, row) {
    this.id = id;
    this.column = column;
    this.row = row;
}