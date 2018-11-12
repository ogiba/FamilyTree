window.onpopstate = function(event) {
	const urlParams = new URLSearchParams(window.location.search);
	const lastPage = urlParams.get("page");

	selectPage(lastPage != null ? lastPage : 0, false);
};

function selectPage(pageNumber, updateUrl = true) {
	console.log(pageNumber);

	let pageParam = new QueryParam("page", pageNumber);

	let params = prepareSearchParams(pageParam);

	if (updateUrl) {
		updateUrlWithParams(params);
	}

	$.get(
		window.location.protocol +
			"//" +
			window.location.host +
			window.location.pathname +
			`/getUsers?${params.toString()}`,
		function(response) {
			$("#usersContainer")
				.html("")
				.html(response);
		}
	);
}

function sortList(item, sortingParam) {
	let orderByValue =
		$(item).data("order-by") == undefined || $(item).data("order-by") == "desc"
			? "asc"
			: "desc";

	$(item).data("order-by", orderByValue);

	let sortParam = new QueryParam("sortBy", sortingParam);
	let orderParam = new QueryParam("orderBy", orderByValue);

	let params = prepareSearchParams([sortParam, orderParam]);

	updateUrlWithParams(params);

	$.get(
		window.location.protocol +
			"//" +
			window.location.host +
			window.location.pathname +
			`/getUsers?${params.toString()}`,
		function(response) {
			$("#usersContainer")
				.html("")
				.html(response);
		}
	);
}

function updateUrlWithParams(params) {
	if (history.pushState) {
		// let searchParams = new URLSearchParams(window.location.search);

		// if (Array.isArray(params)) {
		// 	params.array.forEach(element => {
		// 		if (searchParams.has(element.key)) {
		// 			searchParams.set(element.key, element.value);
		// 		} else {
		// 			searchParams.append(element.key, element.value);
		// 		}
		// 	});
		// } else {
		// 	if (searchParams.has(params.key)) {
		// 		searchParams.set(params.key, params.value);
		// 	} else {
		// 		searchParams.append(params.key, params.value);
		// 	}
		// }

		var newurl =
			window.location.protocol +
			"//" +
			window.location.host +
			window.location.pathname +
			`?${params.toString()}`;
		window.history.pushState({ path: newurl }, "", newurl);
	}
}

function prepareSearchParams(params) {
	let searchParams = new URLSearchParams(window.location.search);

	if (Array.isArray(params)) {
		params.forEach(element => {
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

	return searchParams;
}

class QueryParam {
	constructor(key, value) {
		this.key = key;
		this.value = value;
	}
}
