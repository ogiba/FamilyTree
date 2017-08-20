/**
 * Created by ogiba on 20.08.2017.
 */

function selectPage(pageNumber) {
    console.log(pageNumber);
    $.get("/page/" + pageNumber, function (data) {
        $("#postsContainer").html("").html(data);
    });
}