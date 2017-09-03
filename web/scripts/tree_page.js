/**
 * Created by ogiba on 21.08.2017.
 */

$(document).ready(function () {
    $("#playground").css("padding-top", "" + $(".nav-bar").height() + "px");

    setupDraggable()
});

function setupDraggable() {


    // $("#draggableTest").draggable({
    //     scroll: true,
    //     containment: '#treeContainer',
    //     drag: function () {
    //         var offset = $(this).offset();
    //         var xPos = offset.left;
    //         var yPos = offset.top;
    //         console.log("x: " + xPos);
    //         console.log("y: " + yPos);
    //     }
    // });

    $( ".draggableTest" ).draggable({
        scope: 'demoBox',
        revertDuration: 100,
        start: function( event, ui ) {
            //Reset
            $( ".draggableTest" ).draggable( "option", "revert", true );
        }
    });

    $( ".tree-container" ).droppable({
        scope: 'demoBox',
        drop: function( event, ui ) {
            var box = $(ui.draggable).html();
            $( ".draggableTest" ).draggable( "option", "revert", false );

            //Realign item
            $(ui.draggable).detach().css({top: 0,left: 0}).appendTo(this);
        }
    })

}
