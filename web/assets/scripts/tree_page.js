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

            // if (droppedItem.parentElement.localName === "ul" && droppedItem.id.startsWith("person")) {
            //     rebuildPersonItem(droppedItem)
            // } else if (droppedItem.parentElement.localName === "ul" && droppedItem.id.startsWith("connection")) {
            //     rebuildConnectionItem(droppedItem)
            // }

            if (parentElement.localName === "td" && parentElement.parentElement.localName === "tr") {
                console.log("col:" + parentElement.cellIndex + " row:" + parentElement.parentElement.rowIndex);

                if (parentElement.parentElement.rowIndex === 0) {
                    addNewRow(true);

                    $('html, body').animate({
                        scrollTop: $(droppedItem).offset().top
                    }, 0);

                    treeItems.forEach(function (item) {
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

                    treeItems.forEach(function (item) {
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

            if (droppedItem.parentElement.localName === "ul" && droppedItem.id.startsWith("person")) {
                rebuildPersonItem(droppedItem, ev.target)
            } else if (droppedItem.parentElement.localName === "ul" && droppedItem.id.startsWith("connection")) {
                rebuildConnectionItem(droppedItem)
            } else {
                ev.target.appendChild(droppedItem);
            }
        }
    }
}

function rebuildPersonItem(elem, parent) {
    var text = elem.childNodes[0];
    elem.innerHTML = "";

    // $("<div/>", {
    //     "class" : "left-dot"
    // }).appendTo(elem);
    //
    // $("<div/>", {
    //     "class" : "top-dot"
    // }).appendTo(elem);
    //
    // $("<div/>", {
    //     "class" : "bottom-dot"
    // }).appendTo(elem);
    //
    // $("<div/>", {
    //     "class" : "right-dot"
    // }).appendTo(elem);
    //
    // $("<div/>", {
    //     id: "container_img_" + elem.id,
    //     "style": "height: 50%; text-align: center;"
    // }).appendTo(elem);
    //
    // $("<img>", {
    // }).appendTo("#container_img_" + elem.id);
    //
    // $("<div/>", {
    //     id: "container_" + elem.id,
    //     "style": "height: 50%; line-height: 1;"
    // }).appendTo(elem);
    //
    // $("<label/>", {
    //     text: text.data,
    //     "style": "margin-bottom: 0;"
    // }).appendTo("#container_" + elem.id);
    //
    // $("<div/>", {
    //     text: "00.00.0000",
    //     "style": "font-size: 14px; margin-bottom: 0;"
    // }).appendTo("#container_" + elem.id);
    //
    // $("<div/>", {
    //     text: "00.00.0000",
    //     "style": "font-size: 14px; margin-bottom: 0;"
    // }).appendTo("#container_" + elem.id);
    $.get("/tree/rebuild?data=" + text.data + "&id=" + elem.id, function (data) {
        elem.innerHTML = data;

        var list = document.querySelector(".table");
        var draggableList = new DraggableList(list, elem);

        draggableList.onDraggingItemMouseEnter = function (item) {
            addClass(item.element, "ghost-over");
        };

        draggableList.onDraggingItemMouseLeave = function (item) {
            removeClass(item.element, "ghost-over");
        };

        draggableList.onItemConnected = function (item, dot) {
            var cellIndex = item.element.parentNode.parentNode.cellIndex;
            var rowIndex = item.element.parentNode.parentNode.parentNode.rowIndex;
            var startElemCellIndex = dot.parentNode.parentNode.parentNode.cellIndex;
            var startElemRowIndex = dot.parentNode.parentNode.parentNode.parentNode.rowIndex;

            console.log("Position(" + cellIndex + "," + rowIndex + ")");

            var cells = document.querySelectorAll("td");

            var linesToDraw = [];

            cells.forEach(function (_item) {
                switch (dot.className) {
                    // case "left-dot":
                    //     console.log("left-dot");
                    //     break;
                    // case "top-dot":
                    //     console.log("top-dot");
                    //     break;
                    // case "right-dot":
                    //     console.log("right-dot");
                    //     break;
                    // case "bottom-dot":
                    //     console.log("bottom-dot");
                    //     break;
                    // default:
                    //     break;
                }
                // if (item.cellIndex === cellIndex - 1 && item.parentNode.rowIndex === rowIndex) {
                //     console.log("same row(" + item.cellIndex + ", " + item.parentNode.rowIndex + ")");
                // }

                var _foundItemCellIndex = _item.cellIndex;
                var _foundItemRowIndex = _item.parentNode.rowIndex;
                var connection = null;

                if (_foundItemRowIndex === rowIndex
                    && _foundItemRowIndex === startElemRowIndex
                    && _foundItemCellIndex > startElemCellIndex
                    && _foundItemCellIndex < cellIndex) {
                    console.log("same row(" + _item.cellIndex + ", " + _item.parentNode.rowIndex + ")");

                    connection = new Connection(new TablePosition(_item.cellIndex, _item.parentNode.rowIndex),
                        ConnectionType.line);
                } else if (
                    startElemCellIndex + 1 === _foundItemCellIndex
                    && startElemRowIndex === _foundItemRowIndex) {

                    console.log("first cell(" + _foundItemCellIndex + ", " + _foundItemRowIndex + ")");
                    connection = new Connection(new TablePosition(_foundItemCellIndex, _foundItemRowIndex),
                        ConnectionType.down);
                } else if (_foundItemRowIndex === rowIndex
                    && _foundItemRowIndex >= startElemRowIndex
                    && _foundItemCellIndex > startElemCellIndex
                    && _foundItemCellIndex < cellIndex) {

                    if ((_foundItemCellIndex - startElemCellIndex) === 1) {
                        // if (startElemCellIndex === _foundItemCellIndex - 1 && startElemRowIndex === _foundItemRowIndex - 1) {
                        //     console.log("first cell(" + _item.cellIndex + ", " + _item.parentNode.rowIndex - 1 + ")");
                        //     connection = new Connection(new TablePosition(_item.cellIndex, _item.parentNode.rowIndex - 1),
                        //         ConnectionType.down);
                        // }
                        console.log("first cell(" + _item.cellIndex + ", " + _item.parentNode.rowIndex + ")");
                        connection = new Connection(new TablePosition(_item.cellIndex, _item.parentNode.rowIndex),
                            ConnectionType.downFinish);
                    } else {
                        console.log("lower row(" + _item.cellIndex + ", " + _item.parentNode.rowIndex + ")");
                        connection = new Connection(new TablePosition(_item.cellIndex, _item.parentNode.rowIndex),
                            ConnectionType.line);
                    }
                }

                if (connection !== null) {
                    linesToDraw.push(connection);
                }
            });

            console.log("Number of found postions:" + linesToDraw.length);

            if (linesToDraw.length > 0) {
                buildConnectionBetweenItems(linesToDraw);
            }

            addClass(item.element, "selected");
        };

        parent.appendChild(elem);
    });

    // var list = document.querySelector(".table");
    // var draggableList = new DraggableList(list, elem);
    //
    // draggableList.onDraggingItemMouseEnter = function(item)
    // {
    //     addClass(item.element, "ghost-over");
    // };
    //
    // draggableList.onDraggingItemMouseLeave = function(item)
    // {
    //     removeClass(item.element, "ghost-over");
    // };
    //
    // draggableList.onItemConnected = function(item)
    // {
    //     addClass(item.element, "selected");
    // };
}

/**
 *
 * @param {Connection[]} linesToDraw
 */
function buildConnectionBetweenItems(linesToDraw) {
    linesToDraw.forEach(function (connection) {
        var cellContainer = $('tr:eq(' + connection.position.row + ') td:eq(' + connection.position.cell + ') .tree-container');
        //
        // var line = $("<div/>", {
        //     "style": "margin-top: 50%; margin-left: -10px; margin-right: -10px; background: black; height: 1px;"
        // });
        //
        // cellContainer.append(line);
        connection.drawConnection(cellContainer);
    });
}

function rebuildConnectionItem(elem) {
    var text = elem.childNodes[0];
    elem.innerHTML = "";

    $("<div/>", {
        "style": "margin-top: 50%; margin-left: -10px; margin-right: -10px; background: black; height: 1px;"
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
        Array.from(tbody.children).forEach(function (item) {
            var clonedCell = $(item.children[0].cloneNode(true));

            if (inFirstPlace) {
                clonedCell.prependTo(item);
            } else {
                clonedCell.appendTo(item);
            }
        });
    }
}

var TreeNode = function (id, column, row) {
    this.id = id;
    this.column = column;
    this.row = row;
}

/**
 * @param {string} name
 */
function addClass(element, name) {
    var classes = element.className.split(" ");
    var index = classes.indexOf(name);

    if (index >= 0)
        return;

    classes.push(name);
    element.className = classes.join(" ");
};

function removeClass(element, name) {
    var classes = element.className.split(" ");
    var index = classes.indexOf(name);

    if (index >= 0)
        classes.splice(index, 1);

    element.className = classes.join(" ");
};

/**
 *  @class
 */
var DraggableList = function (listElement, parentElement) {
    this.elements = [];
    this.offset = null;
    this.selectedElement = null;
    this.selectDot = null;
    this.draggingOverElement = null;
    this.canvas = null;

    this.onDraggingItemMouseEnter = null;
    this.onDraggingItemMouseLeave = null;
    this.onItemConnected = null;

    var listElements = Array.from(listElement.querySelectorAll(".person-item"));
    listElements.push(parentElement);


    for (var i = 0; i < listElements.length; i++) {
        this.elements.push(new DraggableElement(this, listElements[i]));
    }

    document.addEventListener("mouseup", this.onMouseUp.bind(this));
    document.addEventListener("mousemove", this.onMouseMove.bind(this));
}

/**
 * @param {DraggableElement} element
 * @param {{x: number, y: number}} offset
 * @param {{x: number, y: number}} startPos
 */
DraggableList.prototype.select = function (element, offset, startPos, dot) {
    this.selectedElement = element;
    this.selectedDot = dot;

    this.canvas = document.createElement("canvas");
    this.canvas.width = window.innerWidth;
    this.canvas.height = window.innerHeight;

    this.canvas.style.position = "absolute";
    this.canvas.style.top = "0";
    this.canvas.style.left = "0";

    this.canvasContext = this.canvas.getContext("2d");
    this.offset = offset;
    this.startPos = startPos;

    this.updateCanvasLine(startPos);

    document.body.appendChild(this.canvas);
};

DraggableList.prototype.deselect = function () {
    if (this.draggingOverElement && this.onItemConnected)
        this.onItemConnected(this.draggingOverElement, this.selectedDot);

    document.body.removeChild(this.canvas);

    this.ghostElement = null;
    this.selectedElement = null;
    this.offset = null;
    this.selectDot = null;
    this.canvas = null;
    this.canvasContext = null;
    this.draggingOverElement = null;

    for (var i = 0; i < this.elements.length; i++) {
        var element = this.elements[i];

        if (element.draggingMouseIsOver) {
            element.draggingMouseIsOver = false;

            if (this.onDraggingItemMouseLeave)
                this.onDraggingItemMouseLeave(element);
        }
    }
};

DraggableList.prototype.onMouseUp = function () {
    if (this.canvas) {
        this.deselect();
    }
};

/**
 * @param {MouseEvent} e
 */
DraggableList.prototype.onMouseMove = function (e) {
    if (this.canvas !== null) {
        this.updateCanvasLine({x: e.clientX, y: e.clientY});

        for (var i = 0; i < this.elements.length; i++) {
            var element = this.elements[i];

            if (element === this.selectedElement)
                continue;

            if (this.intersectsWithPos(element, {x: e.clientX, y: e.clientY})) {
                if (!element.draggingMouseIsOver && !this.draggingOverElement) {
                    if (this.onDraggingItemMouseEnter)
                        this.onDraggingItemMouseEnter(element);

                    element.draggingMouseIsOver = true;
                    this.draggingOverElement = element;
                }
            }
            else {
                if (element.draggingMouseIsOver) {
                    if (this.onDraggingItemMouseLeave)
                        this.onDraggingItemMouseLeave(element);

                    element.draggingMouseIsOver = false;
                    this.draggingOverElement = null;
                }
            }
        }
    }
};

/**
 * @param {{x: number, y: number}} startPos
 */
DraggableList.prototype.updateGhostPosition = function (startPos) {
    this.ghostElement.style.left = (startPos.x - this.offset.x) + "px";
    this.ghostElement.style.top = (startPos.y - this.offset.y) + "px";
};

DraggableList.prototype.updateCanvasLine = function (pos) {
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
DraggableList.prototype.intersectsWithGhost = function (element) {
    var elementRect = element.element.getBoundingClientRect();
    var ghostRect = this.ghostElement.getBoundingClientRect();

    return (elementRect.left < ghostRect.right && elementRect.right > ghostRect.left &&
    elementRect.top < ghostRect.bottom && elementRect.bottom > ghostRect.top );
};

/**
 * @param {{ x: number, y: number }} pos
 */
DraggableList.prototype.intersectsWithPos = function (element, pos) {
    var elementRect = element.element.getBoundingClientRect();

    return (pos.x > elementRect.left && pos.x < elementRect.right &&
    pos.y > elementRect.top && pos.y < elementRect.bottom);
};


/**
 * @class
 * @param {DraggableList} list
 * @param {HTMLElement} element
 */
function DraggableElement(list, element) {
    this.list = list;
    this.element = element;
    this.draggingMouseIsOver = false;

    var rightDot = element.querySelector(".right-dot");
    var topDot = element.querySelector(".top-dot");
    var bottomDot = element.querySelector(".bottom-dot");
    var leftDot = element.querySelector(".left-dot");

    rightDot.addEventListener("mousedown", this.onMouseDown.bind(this, rightDot));
    topDot.addEventListener("mousedown", this.onMouseDown.bind(this, topDot));
    bottomDot.addEventListener("mousedown", this.onMouseDown.bind(this, bottomDot));
    leftDot.addEventListener("mousedown", this.onMouseDown.bind(this, leftDot));
}

DraggableElement.prototype.onMouseDown = function (dot, e) {
    var rect = this.element.getBoundingClientRect();
    var offset = {x: e.clientX - rect.left, y: e.clientY - rect.top};

    this.list.select(this, offset, {x: e.clientX, y: e.clientY}, dot);

    e.preventDefault();
};

/**
 *
 * @param {TablePosition} tablePosition
 * @param {ConnectionType|Number} type
 * @constructor
 */
var Connection = function (tablePosition, type) {
    this.position = tablePosition;
    this.type = type;
}

/**
 *
 * @param {Number} cell
 * @param {Number} row
 * @constructor
 */
var TablePosition = function (cell, row) {
    this.cell = cell;
    this.row = row;
};

/**
 * Enum that defines style of drawing connection between elements
 * @type {{line: number, down: number, up: number, cross: number}}
 */
var ConnectionType = {
    line: 0,
    down: 1,
    up: 2,
    downFinish: 3,
    upFinish: 4,
    cross: 5
};

/**
 *
 * @param  parent
 */
Connection.prototype.drawConnection = function (parent) {
    if (!parent.hasClass("no-padding")) {
        parent.addClass("no-padding");
    }

    switch (this.type) {
        case ConnectionType.line:
            var line = $("<div/>", {
                "class": "connection-type-horizontal fill centered"
            });

            parent.append(line);
            break;
        case ConnectionType.down:

            if (!parent.find(".connection-type-horizontal").length) {
                var line = $("<div/>", {
                    "class": "connection-type-horizontal centered"
                });

                parent.append(line);
            }

            var lowerLine = $("<div/>", {
                "class": "connection-type-vertical"
            });

            parent.append(lowerLine);
            break;
        case ConnectionType.up:

            if (!parent.find(".connection-type-horizontal").length) {
                var line = $("<div/>", {
                    "class": "connection-type-horizontal centered"
                });

                parent.append(line);
            }

            var upperLine = $("<div/>", {
                "class": "connection-type-vertical"
            });

            parent.prepend(upperLine);

            break;
        case ConnectionType.downFinish:
            if (!parent.find(".connection-type-horizontal").length) {
                var line = $("<div/>", {
                    "class": "connection-type-horizontal righted"
                });

                parent.append(line);
            }

            var lowerLine = $("<div/>", {
                "class": "connection-type-vertical"
            });

            parent.prepend(lowerLine);
            break;
        case ConnectionType.upFinish:
            if (!parent.find(".connection-type-horizontal").length) {
                var line = $("<div/>", {
                    "class": "connection-type-horizontal centered righted"
                });

                parent.append(line);
            }

            var upperLine = $("<div/>", {
                "class": "connection-type-vertical"
            });

            parent.append(upperLine);
            break;
        case ConnectionType.cross:
            break;
    }
}