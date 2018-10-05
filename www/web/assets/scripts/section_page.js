function updateSection(id) {
    $.ajax({
        url: "/admin/sections/section/view/" + id + "/save",
        type: "POST",
        data: {
            content: $("#sectionContent").val()
        },
        success: function (data) {
            window.history.back();
        },
        error: function (error) {
        }
    });
}