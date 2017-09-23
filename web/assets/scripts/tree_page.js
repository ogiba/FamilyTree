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

            if (parentElement.localName === "td" && parentElement.parentElement.localName === "tr") {
                console.log("col:"+parentElement.cellIndex+" row:"+parentElement.parentElement.rowIndex);

                if (parentElement.parentElement.rowIndex === 0) {
                    addNewRow(true);

                    $('html, body').animate({
                        scrollTop: $(droppedItem).offset().top
                    }, 0);

                    treeItems.forEach(function(item){
                        item.row += 1;
                        console.log(item);
                    });
                } else if (parentElement.parentElement.rowIndex === parentElement.parentElement.parentElement.childElementCount - 1) {
                    addNewRow(false);
                }

                if (parentElement.cellIndex === 0) {
                    addNewColumn(true);

                    $('html, body').animate({
                        scrollLeft: $(droppedItem).offset().left + 500
                    }, 0);

                    treeItems.forEach(function(item){
                        item.column += 1;
                        console.log(item);
                    });
                } else if (parentElement.cellIndex === parentElement.parentElement.childElementCount - 1) {
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

    var list = document.querySelector(".table");
    var draggableList = new DraggableList(list, elem);
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

var DraggableList = function(listElement, parentElem)
{
    this.elements = [];
    this.ghostElement = null;
    this.offset = null;
    this.selectedElement = null;
    this.canvas = null;

    var items = Array.from(listElement.querySelectorAll(".person-item"));
    items.push(parentElem);
    console.log(items);

    var allItems = [];
    var rightDots = document.querySelectorAll(".right-dot");
    var topDots = document.querySelectorAll(".top-dot");
    var bottomDots = document.querySelectorAll(".bottom-dot");
    var leftDots = document.querySelectorAll(".left-dot");
    Array.prototype.push.apply(allItems, rightDots);
    Array.prototype.push.apply(allItems, topDots);
    Array.prototype.push.apply(allItems, bottomDots);
    Array.prototype.push.apply(allItems, leftDots);
    var listElements = allItems;


    for(var i = 0; i < listElements.length; i++)
    {
        this.elements.push(new DraggableElement(this, listElements[i], parentElem));
    }

    document.addEventListener("mouseup", this.onMouseUp.bind(this));
    document.addEventListener("mousemove", this.onMouseMove.bind(this));
}

/**
 * @param {DraggableElement} element
 * @param {{x: number, y: number}} offset
 * @param {{x: number, y: number}} startPos
 */
DraggableList.prototype.select = function(element, offset, startPos)
{
    //element.addClass("selected");

    this.selectedElement = element;
    //this.ghostElement = element.element.cloneNode();
    //this.ghostElement.className = "element ghost";
    this.canvas = document.createElement("canvas");
    this.canvas.width = window.innerWidth;
    this.canvas.height = window.innerHeight;

    this.canvas.style.position = "absolute";
    this.canvas.style.top = "0";
    this.canvas.style.left = "0";

    this.canvasContext = this.canvas.getContext("2d");
    this.offset = offset;
    this.startPos = startPos;

    //this.updateGhostPosition(startPos);
    this.updateCanvasLine(startPos);

    //document.body.appendChild(this.ghostElement);
    document.body.appendChild(this.canvas);

};

DraggableList.prototype.onMouseUp = function()
{
    if(this.canvas)
    {
        this.selectedElement.removeClass("selected");

        //document.body.removeChild(this.ghostElement);
        document.body.removeChild(this.canvas);
        this.ghostElement = null;
        this.selectedElement = null;
        this.offset = null;
        this.canvas = null;
        this.canvasContext = null;

        for(var i = 0; i < this.elements.length; i++)
        {
            var element = this.elements[i];
            element.removeClass("ghost-over");
        }
    }
};

/**
 * @param {MouseEvent} e
 */
DraggableList.prototype.onMouseMove = function(e)
{
    if(this.canvas !== null)
    {
        //this.updateGhostPosition(e);
        this.updateCanvasLine({ x: e.clientX,  y: e.clientY });
        var isOver = false;

        for(var i = 0; i < this.elements.length; i++)
        {
            var element = this.elements[i];

            if(element === this.selectedElement)
                continue;

            if(this.intersectsWithPos(element, { x: e.clientX,  y: e.clientY }) && !isOver)
            {
                element.addClass("ghost-over");
                isOver = true;
            }
            else
            {
                element.removeClass("ghost-over");
            }
        }
    }
};

/**
 * @param {{x: number, y: number}} startPos
 */
DraggableList.prototype.updateGhostPosition = function(startPos)
{
    this.ghostElement.style.left = (startPos.x - this.offset.x) + "px";
    this.ghostElement.style.top = (startPos.y - this.offset.y) + "px";
};

DraggableList.prototype.updateCanvasLine = function(pos)
{
    this.canvasContext.clearRect(0, 0, window.innerWidth, window.innerHeight);
    this.canvasContext.strokeStyle = "black";
    this.canvasContext.beginPath();
    this.canvasContext.moveTo(this.startPos.x, this.startPos.y);
    this.canvasContext.lineTo(pos.x, pos.y);
    this.canvasContext.stroke();
};

/**
 * @param {DraggableElement} element
 */
DraggableList.prototype.intersectsWithGhost = function(element)
{
    var elementRect = element.element.getBoundingClientRect();
    var ghostRect = this.ghostElement.getBoundingClientRect();

    return (elementRect.left < ghostRect.right && elementRect.right > ghostRect.left &&
    elementRect.top < ghostRect.bottom && elementRect.bottom > ghostRect.top );
};

/**
 * @param {{ x: number, y: number }} pos
 */
DraggableList.prototype.intersectsWithPos = function(element, pos)
{
    var elementRect = element.parent.getBoundingClientRect();

    return (pos.x > elementRect.left  && pos.x < elementRect.right &&
    pos.y > elementRect.top && pos.y < elementRect.bottom);
};


/**
 * @param {DraggableList} list
 * @param {HTMLElement} element
 */
function DraggableElement(list, element, parent)
{
    this.list = list;
    this.element = element;
    this.parent = parent;

    this.element.addEventListener("mousedown", this.onMouseDown.bind(this));
}

/**
 * @param {string} name
 */
DraggableElement.prototype.addClass = function(name)
{
    var classes = this.parent.className.split(" ");
    var index = classes.indexOf(name);

    if(index >= 0)
        return;

    classes.push(name);
    this.parent.className = classes.join(" ");
};

DraggableElement.prototype.removeClass = function(name)
{
    var classes = this.parent.className.split(" ");
    var index = classes.indexOf(name);

    if(index >= 0)
        classes.splice(index, 1);

    this.parent.className = classes.join(" ");
};

DraggableElement.prototype.onMouseDown = function(e)
{
    var rect = this.element.getBoundingClientRect();
    var offset = { x: e.clientX - rect.left, y: e.clientY - rect.top };

    this.list.select(this, offset, { x: e.clientX, y: e.clientY });

    e.preventDefault();
};
