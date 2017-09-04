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
        containment: "document",
        revertDuration: 100,
        appendTo: "#playground",
        helper: "clone",
        connectToSortable: ".person-container ul",
        cursorAt: { left: 5 },
        revert: function(droppableObj)
        {
            if(droppableObj === false)
             {
                $(this).removeClass('dragging');
                return true;
             }
             else
             {
                return false;
             }
        },
        start: function( event, ui ) {
            //Reset
            $( ".draggableTest" ).draggable({
                revert: true
            });

            $draggingParent = $(this).parent();
            $(this).addClass('dragging');
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

    $(".person-container ul").droppable({
      scope: 'demoBox',
      drop: function(event, ui) {
          $( ".draggableTest" ).draggable( "option", "revert", false );

          $(ui.draggable).detach().css({top: 0,left: 0}).appendTo(this);
      }
    });
    //   ,stop: function(event, ui) {
    //       $(ui.item).removeClass('dragging');
    //       $('.dragging').remove();
    //       if($(this).hasClass('new')) {
    //           var clone = $(this).clone();
    //           clone.empty();
    //           $(this).after(clone);
    //           $(this).removeClass('new');
    //           initDroppableSort( clone );
    //       }
    //       cleanUp();
    //     }
    // })
}

function cleanUp() {
      $('.droppable').not('.new').each(function() {
    	if($(this).find('li').length === 0) {
    		$(this).remove();
    	}
	     });
}
