/**
 * Created by ogiba on 25.09.2017.
 */
(function() {
	function submit() {
		var loginInfoField = $("#loginInfo");

		if (loginInfoField.hasClass("shown")) {
			loginInfoField.removeClass("shown");
		}

		$(".progress").addClass("shown");

		$.ajax({
			url: "login/loginUser",
			type: "POST",
			data: {
				username: $("#usernameInput").val(),
				password: $("#passwordInput").val()
			},
			success: function(data) {
				window.location.href = "/admin";
			},
			error: function(error) {
				var resp = JSON.parse(error.responseText);

				$(".progress").removeClass("shown");

				var loginInfoField = $("#loginInfo");
				loginInfoField.text(resp.message);
				loginInfoField.addClass("shown");
			}
		});
	}

	/**
	 * @param e
	 */
	function loginOnEnter(e) {
		if (e.keyCode === 13) submit();
	}

	$("#submit").click(submit);
	$("#usernameInput").keydown(loginOnEnter);
	$("#passwordInput").keydown(loginOnEnter);

	$("#usernameInput").on("input", function(e) {
		var re = new RegExp(".{1,}@{1}.{1,}\\.{1}.{2,}");

		setTimeout(function() {
			let insertedValue = $("#usernameInput").val();

			var loginInfoField = $("#loginInfo");

			if (insertedValue.includes("@")) {
				let isProperValue = re.test(insertedValue);
				console.log(isProperValue);

				if (!isProperValue && !loginInfoField.hasClass("shown")) {
					loginInfoField.text("Email not properly formatted");
					loginInfoField.addClass("shown");
				} else if (isProperValue && loginInfoField.hasClass("shown")) {
					loginInfoField.removeClass("shown");
				}
			} else {
				console.log("Not a email");

				if (loginInfoField.hasClass("shown")) {
					loginInfoField.removeClass("shown");
				}
			}
		}, 500);
	});
})();
