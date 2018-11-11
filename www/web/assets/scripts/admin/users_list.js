window.onpopstate = function(event) {
	const urlParams = new URLSearchParams(window.location.search);
	const lastPage = urlParams.get("page");

	selectPage(lastPage != null ? lastPage : 0, false);
};

function selectPage(pageNumber, updateUrl = true) {
	console.log(pageNumber);

	if (updateUrl) {
		let pageParam = new QueryParam("page", pageNumber);

		updateUrlWithParams(pageParam);
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

function sortList(sortingParam) {
	let sortParam = new QueryParam("sortBy", sortingParam);

	updateUrlWithParams(sortParam);
}

function updateUrlWithParams(params) {
	if (history.pushState) {
		let searchParams = new URLSearchParams(window.location.search);

		if (Array.isArray(params)) {
			params.array.forEach(element => {
				if (searchParams.has(element.key)) {
					searchParams.set(element.key, element.value);
				} else {
					searchParams.append(element.key, element.value);
				}
			});
		} else {
			if (searchParams.has(params.key)) {
				searchParams.set(params.key, params.value);
			} else {
				searchParams.append(params.key, params.value);
			}
		}

		var newurl =
			window.location.protocol +
			"//" +
			window.location.host +
			window.location.pathname +
			`?${searchParams.toString()}`;
		window.history.pushState({ path: newurl }, "", newurl);
	}
}

class QueryParam {
	constructor(key, value) {
		this.key = key;
		this.value = value;
	}
}
