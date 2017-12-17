/**
 * Created by ogiba on 21.08.2017.
 */
var treeItems = [];

$(document).ready(function () {
    $("#playground").css("padding-top", "" + $(".nav-bar").height() + "px");

    loadTree();
});

function loadTree() {
    $.get("/tree/load_tree", function (response) {
        var person = JSON.parse(response);
        console.log(person);

        var basePosition = new TablePosition(1, 1);

        recursiveRebuilding(person, basePosition);
    })
}

function recursiveRebuilding(person, basePosition) {
    var parent = $('tr:eq(' + basePosition.row + ') td:eq(' + basePosition.cell + ')');

    generateRequiredColumns(parent, basePosition);
    generateRequiredRows(parent, basePosition);

    $.get("/tree/rebuild?data=" + person.name + "&id=" + 1 + "&position=" + JSON.stringify(basePosition), function (data) {
        var parent = $('tr:eq(' + data.position.row + ') td:eq(' + data.position.cell + ') .tree-container');

        console.log(data);

        $("<div/>", {
            "class": "person-item"
        }).html(data.item).appendTo(parent);
    });

    if (person.children.length > 0) {
        var fixedPosition = 0;
        for (var i = 0; i < person.children.length; i++) {
            var child = person.children[i];
            var position = new TablePosition(basePosition.cell + 2, basePosition.row + i + fixedPosition);
            fixedPosition += child.children.length > 0 ? recursiveChildrenSize(child) : 0;

            recursiveRebuilding(child, position);
            loadConnections(basePosition, position);
        }
    }
}

function recursiveChildrenSize(person) {
    var children = person.children;
    var numberOfChildren = children.length > 0 ? children.length - 1 : 0;

    if (children.length > 0) {
        for (var i = 0; i < children.length; i++) {
            numberOfChildren += recursiveChildrenSize(children[i]);
        }
    }

    return numberOfChildren;
}

function generateRequiredColumns(parent, basePosition) {
    if (basePosition.cell >= parent.parent().children().length - 2) {
        var cellDifference = parent.parent().children().length - basePosition.cell;
        for (var i = 0; i < cellDifference; i++) {
            addNewColumn(false);
        }
    }
}

function generateRequiredRows(parent, basePosition) {
    if (basePosition.row >= parent.parent().parent().children().length - 1) {
        var rowDifference = parent.parent().parent().children().length - basePosition.row;
        for (var i = 0; i < rowDifference; i++) {
            addNewRow(false);
        }
    }
}

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

/**
 * Rebuilds dropped item to show more detailed information about person
 * @param elem selected and dropped person item
 * @param parent droppable container
 */
function rebuildPersonItem(elem, parent) {
    var text = elem.childNodes[0];
    elem.innerHTML = "";
    var droppedPosition = new TablePosition(parent.parentElement.cellIndex, parent.parentElement.parentElement.rowIndex);

    //TODO - Add placeholder while data is loading
    $.get("/tree/rebuild?data=" + text.data + "&id=" + elem.id + "&position=" + JSON.stringify(droppedPosition), function (data) {
        elem.innerHTML = data.item;

        var list = document.querySelector(".table");
        var draggableList = new DraggableList(list, elem);

        draggableList.onDraggingItemMouseEnter = function (item) {
            addClass(item.element, "ghost-over");
        };

        draggableList.onDraggingItemMouseLeave = function (item) {
            removeClass(item.element, "ghost-over");
        };

        draggableList.onItemConnected = function (item, dot) {
            detectConnections(item, dot);
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
 * @param {TablePosition} parentPosition
 * @param {TablePosition} childPosition
 */
function loadConnections(parentPosition, childPosition) {
    var linesToDraw = lookForConnections(parentPosition, childPosition);

    console.log("Number of found postions:" + linesToDraw.length);

    if (linesToDraw.length > 0) {
        var connectionLine = new ConnectionLine(linesToDraw);
        connectionLine.connectLines();
    }
}

/**
 *Detects connection between selected items
 * @param item selected person item form container
 * @param dot determine which of dots of first item was selected to draw connection
 */
function detectConnections(item, dot) {
    var cellIndex = item.element.parentNode.parentNode.cellIndex;
    var rowIndex = item.element.parentNode.parentNode.parentNode.rowIndex;
    var startElemCellIndex = dot.parentNode.parentNode.parentNode.cellIndex;
    var startElemRowIndex = dot.parentNode.parentNode.parentNode.parentNode.rowIndex;

    console.log("Position(" + cellIndex + "," + rowIndex + ")");

    var linesToDraw = lookForConnections(new TablePosition(startElemCellIndex, startElemRowIndex), new TablePosition(cellIndex, rowIndex));

    console.log("Number of found postions:" + linesToDraw.length);

    if (linesToDraw.length > 0) {
        var connectionLine = new ConnectionLine(linesToDraw);
        connectionLine.connectLines();
        addClass(item.element, "selected");
    }
}

/**
 *
 * @param {TablePosition} startPosition
 * @param {TablePosition} endPosition
 */
function lookForConnections(startPosition, endPosition) {
    var linesToDraw = [];
    var justDraw = true;

    var filteredRows = $(document.querySelectorAll("td")).filter(function (cellIndex, cell) {
        var positionFound = new TablePosition(cell.cellIndex, cell.parentElement.rowIndex);
        var connection = null;

        if (!justDraw)
            return false;

        if (positionFound.cell > startPosition.cell
            && positionFound.cell < endPosition.cell
            && positionFound.row === endPosition.row) {

            justDraw = checkConnectionPossibility(cell);

            if (positionFound.cell === startPosition.cell + 1 && positionFound.row < startPosition.row) {
                connection = new Connection(positionFound, ConnectionType.upFinish);
            } else if (positionFound.cell === startPosition.cell + 1 && positionFound.row > startPosition.row) {
                connection = new Connection(positionFound, ConnectionType.downFinish);
            } else {
                connection = new Connection(positionFound, ConnectionType.line);
            }
            linesToDraw.push(connection);
            return true;
        } else if (positionFound.cell < startPosition.cell
            && positionFound.cell > endPosition.cell
            && positionFound.row === endPosition.row) {

            justDraw = checkConnectionPossibility(cell);

            if (positionFound.cell === startPosition.cell - 1 && positionFound.row < startPosition.row) {
                connection = new Connection(positionFound, ConnectionType.down);
            } else if (positionFound.cell === startPosition.cell - 1 && positionFound.row > startPosition.row) {
                connection = new Connection(positionFound, ConnectionType.up);
            } else {
                connection = new Connection(positionFound, ConnectionType.line);
            }
            linesToDraw.push(connection);
            return true;
        } else if (positionFound.cell === startPosition.cell + 1
            && positionFound.cell < endPosition.cell
            && positionFound.row <= startPosition.row
            && positionFound.row >= endPosition.row) {

            justDraw = checkConnectionPossibility(cell);

            if (positionFound.row === startPosition.row) {
                connection = new Connection(positionFound, ConnectionType.up);
            } else {
                connection = new Connection(positionFound, ConnectionType.lineVertical);
            }
            linesToDraw.push(connection);
            return true;
        } else if (positionFound.cell === startPosition.cell - 1
            && positionFound.cell > endPosition.cell
            && positionFound.row <= startPosition.row
            && positionFound.row >= endPosition.row) {

            justDraw = checkConnectionPossibility(cell);

            if (positionFound.row === startPosition.row) {
                connection = new Connection(positionFound, ConnectionType.downFinish);
            } else {
                connection = new Connection(positionFound, ConnectionType.lineVertical);
            }
            linesToDraw.push(connection);
            return true;
        } else if (positionFound.cell === startPosition.cell + 1
            && positionFound.cell < endPosition.cell
            && positionFound.row >= startPosition.row
            && positionFound.row <= endPosition.row) {

            justDraw = checkConnectionPossibility(cell);

            if (positionFound.row === startPosition.row) {
                connection = new Connection(positionFound, ConnectionType.down);
            } else {
                connection = new Connection(positionFound, ConnectionType.lineVertical);
            }
            linesToDraw.push(connection);
            return true;
        } else if (positionFound.cell === startPosition.cell - 1
            && positionFound.cell > endPosition.cell
            && positionFound.row >= startPosition.row
            && positionFound.row <= endPosition.row) {

            justDraw = checkConnectionPossibility(cell);

            if (positionFound.row === startPosition.row) {
                connection = new Connection(positionFound, ConnectionType.upFinish);
            } else {
                connection = new Connection(positionFound, ConnectionType.lineVertical);
            }
            linesToDraw.push(connection);
            return true;
        }

        return false;
    });

    if (!justDraw)
        linesToDraw = [];

    console.log("Filtered children count: " + filteredRows.length);
    return linesToDraw;
}

function checkConnectionPossibility(cell) {
    return $(cell).find(".person-item").length === 0;
}

function rebuildConnectionItem(elem) {
    var text = elem.childNodes[0];
    elem.innerHTML = "";

    $("<div/>", {
        "style": "margin-top: 50%; margin-left: -10px; margin-right: -10px; background: black; height: 1px;"
    }).appendTo(elem);
}

/**
 * Add row at first or last position of the {@code table}
 * @param {Boolean} inFirstPlace - defines wheter the new row should be added (as first or last element)
 */
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

/**
 * Add column at first or last postion of the {@code table}
 * @param {Boolean} inFirstPlace - defines wheter the column should be added (as first or last element)
 */
function addNewColumn(inFirstPlace) {
    var tbody = $(".table").children()[0];
    if (tbody.localName === "tbody") {
        Array.from(tbody.children).forEach(function (item) {
            var clonedCell = $(item.children[0].cloneNode(true));

            if (inFirstPlace) {
                clonedCell.prependTo(item);
            } else {
                clonedCell.appendTo(item);
                clonedCell.appendTo(item);
            }
        });
    }
}

/**
 *
 * @param id
 * @param column
 * @param row
 * @constructor
 */
var TreeNode = function (id, column, row) {
    this.id = id;
    this.column = column;
    this.row = row;
};

/**
 * @param element
 * @param {string} name
 */
function addClass(element, name) {
    var classes = element.className.split(" ");
    var index = classes.indexOf(name);

    if (index >= 0)
        return;

    classes.push(name);
    element.className = classes.join(" ");
}

/**
 * @param element
 * @param name
 */
function removeClass(element, name) {
    var classes = element.className.split(" ");
    var index = classes.indexOf(name);

    if (index >= 0)
        classes.splice(index, 1);

    element.className = classes.join(" ");
}

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
};

/**
 * @param {DraggableElement} element
 * @param {{x: number, y: number}} offset
 * @param {{x: number, y: number}} startPos
 * @param dot
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

/**
 *
 * @param pos
 */
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
 * @param element
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

/**
 *
 * @param dot
 * @param e
 */
DraggableElement.prototype.onMouseDown = function (dot, e) {
    var rect = this.element.getBoundingClientRect();
    var offset = {x: e.clientX - rect.left, y: e.clientY - rect.top};

    this.list.select(this, offset, {x: e.clientX, y: e.clientY}, dot);

    e.preventDefault();
};

/**
 *
 * @param {Connection[]} connectionLines
 * @class
 */
var ConnectionLine = function (connectionLines) {
    this.connectionLines = connectionLines;

    this.onHover = null;
};

ConnectionLine.prototype.connectLines = function () {
    this.connectionLines.forEach(function (connection, index, connectionLines) {
        var cellContainer = $('tr:eq(' + connection.position.row + ') td:eq(' + connection.position.cell + ') .tree-container');

        connection.onHover = (function (lines) {
            return function () {
                console.log("hover_test");
                lines.forEach(function (line) {
                    line.highlight(true);
                })
            }
        })(connectionLines);

        connection.onHoverLeft = (function (lines) {
            return function () {
                console.log("unhover_test");
                lines.forEach(function (line) {
                    line.highlight(false);
                })
            }
        })(connectionLines);
        connection.drawConnection(cellContainer);
    });
};

/**
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
 * @type {{line: number, lineVertical: number, down: number, up: number, downFinish: number, upFinish: number}}
 */
var ConnectionType = {
    line: 0,
    lineVertical: 1,
    down: 2,
    up: 3,
    downFinish: 4,
    upFinish: 5
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
    this.onHover = null;
    this.onHoverLeft = null;
    this.line = [];
};

/**
 * Responsible for drawing connections between selected items
 *
 * @param  parent
 */
Connection.prototype.drawConnection = function (parent) {
    if (!parent.hasClass("no-padding")) {
        parent.addClass("no-padding");
    }

    var drawnLines = [];

    switch (this.type) {
        case ConnectionType.line:
            drawnLines = lineConnectionBehavior(parent);
            break;
        case ConnectionType.lineVertical:
            drawnLines = lineVerticalConnectionBehavior(parent);
            break;
        case ConnectionType.down:
            drawnLines = downConnectionBehavior(parent);
            break;
        case ConnectionType.up:
            drawnLines = upConnectionBehavior(parent);
            break;
        case ConnectionType.downFinish:
            drawnLines = downFinishConnectionBehavior(parent);
            break;
        case ConnectionType.upFinish:
            drawnLines = upFinishConnectionBehavior(parent);
            break;
    }

    if (drawnLines.length > 0) {
        this.line = drawnLines;
        for (var i = 0; i < drawnLines.length; i++) {
            $(drawnLines[i]).hover(this.onHover, this.onHoverLeft);
        }
    }
};

Connection.prototype.highlight = function (shouldHighlight) {
    for (var i = 0; i < this.line.length; i++) {
        var line = this.line[i];
        if (shouldHighlight) {
            $(line).addClass("hovered");
        } else {
            $(line).removeClass("hovered");
        }
    }
};

/**
 * Draws horizontal connection between two selected items
 * @param parent
 */
function lineConnectionBehavior(parent) {
    var line = null;
    var shouldPrepend = false;

    if (parent.find(".connection-type-horizontal.righted").length) {
        var child = parent.find(".connection-type-horizontal.righted");

        var childPostiton = Array.from(parent.children()).indexOf(child[0]);

        if (childPostiton === 0) {
            shouldPrepend = true;
            child.remove();

            line = $("<div/>", {
                "class": "connection-type-horizontal fill"
            });
        } else if (childPostiton === parent.children().length - 1) {
            shouldPrepend = false;
            child.remove();

            line = $("<div/>", {
                "class": "connection-type-horizontal fill"
            });
        } else {
            child.removeClass("righted").addClass("fill");
        }

    } else if (parent.find(".connection-type-horizontal").length
        && !parent.find(".connection-type-horizontal").hasClass("fill")) {
        var child = parent.find(".connection-type-horizontal");
        child.addClass("fill")
    } else if (!parent.find(".connection-type-horizontal").length) {
        line = $("<div/>", {
            "class": "connection-type-horizontal fill centered"
        });
    }

    if (line !== null) {
        if (shouldPrepend) {
            line.addClass("centered");
            parent.prepend(line);
        } else
            parent.append(line);
    }

    return [line];
}

/**
 * Draws vertical lines that are required to properly connect items
 * @param parent
 */
function lineVerticalConnectionBehavior(parent) {
    var verticalLine = null;
    var shouldPrepend = false;

    var foundItem = parent.find(".connection-type-vertical");

    if (!foundItem.length) {
        verticalLine = $("<div/>", {
            "class": "connection-type-vertical fill"
        });
    } else if (foundItem.length < 2) {
        if (foundItem.hasClass("bottom")) {
            shouldPrepend = true;
            verticalLine = $("<div/>", {
                "class": "connection-type-vertical top"
            });
        } else if (foundItem.hasClass("top")) {
            verticalLine = $("<div/>", {
                "class": "connection-type-vertical bottom"
            });
        }
    }

    if (verticalLine !== null) {
        if (shouldPrepend) {
            var horizontalLine = parent.find(".connection-type-horizontal");
            horizontalLine.removeClass("centered");
            parent.prepend(verticalLine);
        } else {
            parent.append(verticalLine);
        }
    }

    return [verticalLine];
}

/**
 * Draws type of connection that point to bottom elements of the tree
 * @param parent
 */
function downConnectionBehavior(parent) {
    var verticalLine = parent.find(".connection-type-vertical");
    var horizontalLine = parent.find(".connection-type-horizontal");
    var linesToReturn = [];

    if (!horizontalLine.length) {
        var line = $("<div/>", {
            "class": "connection-type-horizontal centered"
        });

        if (verticalLine.hasClass("fill")) {
            line.removeClass("centered");
        }

        parent.append(line);
        linesToReturn.push(line);
    } else {
        linesToReturn.push(horizontalLine)
    }

    if (!verticalLine.length) {
        var lowerLine = $("<div/>", {
            "class": "connection-type-vertical bottom"
        });

        parent.append(lowerLine);
        linesToReturn.push(lowerLine);
    } else if (verticalLine.hasClass("top") && !verticalLine.hasClass("bottom")) {
        var upperLine = $("<div/>", {
            "class": "connection-type-vertical bottom"
        });

        parent.append(upperLine);
        linesToReturn.push(upperLine);
    } else if (verticalLine.hasClass("fill")) {
        verticalLine.remove();

        var upperLine = $("<div/>", {
            "class": "connection-type-vertical top"
        });

        var lowerLine = $("<div/>", {
            "class": "connection-type-vertical bottom"
        });

        parent.prepend(upperLine);
        parent.append(lowerLine);
        linesToReturn.push(upperLine, lowerLine);
    }
    return linesToReturn;
}

/**
 * Draws type of connection that point to top elements of the tree
 * @param parent
 */
function upConnectionBehavior(parent) {
    var linesToReturn = [];

    if (!parent.find(".connection-type-horizontal").length) {
        var line = $("<div/>", {
            "class": "connection-type-horizontal"
        });

        parent.append(line);
        linesToReturn.push(line);
    } else if (parent.find(".connection-type-horizontal.centered").length) {
        parent.find(".connection-type-horizontal.centered").removeClass("centered");
    }

    var verticalLine = parent.find(".connection-type-vertical");

    if (!verticalLine.length) {
        var upperLine = $("<div/>", {
            "class": "connection-type-vertical top"
        });

        parent.prepend(upperLine);
        linesToReturn.push(upperLine);
    } else if (verticalLine.hasClass("bottom") && !verticalLine.hasClass("top")) {
        var upperLine = $("<div/>", {
            "class": "connection-type-vertical top"
        });

        parent.prepend(upperLine);
        linesToReturn.push(upperLine);
    } else if (verticalLine.hasClass("fill")) {
        verticalLine.remove();

        var upperLine = $("<div/>", {
            "class": "connection-type-vertical top"
        });

        var lowerLine = $("<div/>", {
            "class": "connection-type-vertical bottom"
        });

        parent.prepend(upperLine);
        parent.append(lowerLine);
        linesToReturn.push(upperLine, lowerLine);
    }
    return linesToReturn;
}

/**
 * Draws type of connection that end drawing vertical lines to bottom elements of the tree
 * @param parent
 */
function downFinishConnectionBehavior(parent) {
    var linesToReturn = [];
    var horizontalItem = parent.find(".connection-type-horizontal");
    if (!horizontalItem.length) {
        var line = $("<div/>", {
            "class": "connection-type-horizontal righted"
        });

        parent.append(line);
        linesToReturn.push(line);
    } else {
        horizontalItem.removeClass("centered");
    }

    var verticalLine = parent.find(".connection-type-vertical");

    if (!verticalLine.length) {
        var lowerLine = $("<div/>", {
            "class": "connection-type-vertical top"
        });

        parent.prepend(lowerLine);
        linesToReturn.push(lowerLine);
    } else if (verticalLine.hasClass("bottom") && !verticalLine.hasClass("top")) {
        var upperLine = $("<div/>", {
            "class": "connection-type-vertical top"
        });

        parent.prepend(upperLine);
        linesToReturn.push(upperLine);
    } else if (verticalLine.hasClass("fill")) {
        verticalLine.remove();

        var upperLine = $("<div/>", {
            "class": "connection-type-vertical top"
        });

        var lowerLine = $("<div/>", {
            "class": "connection-type-vertical bottom"
        });

        parent.prepend(upperLine);
        parent.append(lowerLine);
        linesToReturn.push(upperLine, lowerLine);
    }

    return linesToReturn;
}

/**
 * Draws type of connection that end drawing vertical lines to top elements of the tree
 * @param parent
 */
function upFinishConnectionBehavior(parent) {
    var linesToReturn = [];
    var verticalLine = parent.find(".connection-type-vertical");

    if (!parent.find(".connection-type-horizontal").length) {
        var line = $("<div/>", {
            "class": "connection-type-horizontal centered righted"
        });

        if (verticalLine.hasClass("fill")) {
            line.removeClass("centered");
        }

        parent.append(line);
        linesToReturn.push(line);
    }

    if (!verticalLine.length) {
        var upperLine = $("<div/>", {
            "class": "connection-type-vertical bottom"
        });

        parent.append(upperLine);
        linesToReturn.push(upperLine);
    } else if (verticalLine.hasClass("top") && !verticalLine.hasClass("bottom")) {
        var upperLine = $("<div/>", {
            "class": "connection-type-vertical bottom"
        });

        parent.append(upperLine);
        linesToReturn.push(upperLine);
    } else if (verticalLine.hasClass("fill")) {
        verticalLine.remove();

        var lowerLine = $("<div/>", {
            "class": "connection-type-vertical bottom"
        });

        var upperLine = $("<div/>", {
            "class": "connection-type-vertical top"
        });

        parent.prepend(upperLine);
        parent.append(lowerLine);
        linesToReturn.push(upperLine, lowerLine);
    }

    return linesToReturn;
}