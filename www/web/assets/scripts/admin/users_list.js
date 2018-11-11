window.onpopstate = function(event) {
	const urlParams = new URLSearchParams(window.location.search);
	const lastPage = urlParams.get("page");

	selectPage(lastPage != null ? lastPage : 0, false);
};

function selectPage(pageNumber, updateUrl = true) {
	console.log(pageNumber);

	if (updateUrl) {
		updateUrlWithPage(pageNumber);
	}

	$.get(
		window.location.protocol +
			"//" +
			window.location.host +
			window.location.pathname +
			`/getUsers?page=${pageNumber}`,
		function(response) {
			$("#usersContainer")
				.html("")
				.html(response);
		}
	);
}

function updateUrlWithPage(pageNumber) {
	if (history.pushState) {
		var newurl =
			window.location.protocol +
			"//" +
			window.location.host +
			window.location.pathname +
			`?page=${pageNumber}`;
		window.history.pushState({ path: newurl }, "", newurl);
	}
}
