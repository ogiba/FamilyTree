function saveMember() {
    var firstName = $("#firstName").val();
    var lastName = $("#lastName").val();
    var maidenName = $("#maidenName").val();
    var birthDate = $("#birthDate").val();
    var deathDate = $("#deathDate").val();
    var firstParent = $("#firstParent").find("option:selected").val();
    var secondParent = $("#secondParent").find("option:selected").val();
    var partner = $("#partner").find("option:selected").val();
    var description = $("#descriptionArea").val();

    if (!checkBasicInfo(firstName, lastName)) {
        return;
    }

    var member = new FamilyMember(firstName, lastName);
    member.setMaidenName(maidenName)
        .setBirthDate(birthDate)
        .setDeathDate(deathDate)
        .setFirstParent(firstParent)
        .setSecondParent(secondParent)
        .setPartner(partner)
        .setDescription(description);

    sendSaveRequest(member)
}

function checkBasicInfo(firstName, lastName) {
    var alertToShow = null;
    if (firstName === "" || lastName === "") {
        alertToShow = prepareAlert(AlertType.warning, "Fields cannot be empty")
    }

    if (alertToShow !== null) {
        $("#alertContainer").append(alertToShow);
        return false;
    }

    return true;
}

/**
 * Make http request to server to insert new member in to DB
 *
 * @param {FamilyMember} member
 */
function sendSaveRequest(member) {
    $.post(window.location.href + "/save", {
        "member": member
    }, function (response) {

        var alertToShow = null;
        switch (response.statusCode) {
            case 200:
                window.location.href = "/admin/tree_builder";
                break;
            case 422:
                alertToShow = prepareAlert(AlertType.warning, response.message);
                break;
            case 500:
                alertToShow = prepareAlert(AlertType.danger, response.message);
                break;
            default:
                break;
        }

        if (alertToShow !== null) {
            $("#alertContainer").append(alertToShow);
        }
    })
}

/**
 *
 * @param {AlertType|String} type
 * @param {String} message
 * @returns {void|*|jQuery}
 */
function prepareAlert(type, message) {
    var closeSpan = $("<span/>", {
        "aria-hidden": "true",
        html: "&times;"
    });

    var closeBtn = $("<button/>", {
        "type": "button",
        "class": "close",
        "data-dismiss": "alert",
        "aria-label": "Close",
        "click": function () {
            $(".alert").remove();
        }
    }).append(closeSpan);

    return $("<div/>", {
        text: message,
        "class": "alert alert-" + type + " alert-dismissable",
        "role": "alert"
    }).append(closeBtn);
}

var AlertType = {
    primary: "primary",
    secondary: "secondary",
    success: "success",
    warning: "warning",
    danger: "danger",
    info: "info",
};

/**
 * JS representation of family member object
 *
 * @param {String} firstName
 * @param {String} lastName
 * @constructor
 */
var FamilyMember = function (firstName, lastName) {
    this.firstName = firstName;
    this.lastName = lastName;
    this.maidenName = null;
    this.birthDate = null;
    this.deathDate = null;
    this.firstParent = null;
    this.secondParent = null;
    this.partner = null;
    this.description = null;

    this.setMaidenName = function (name) {
        this.maidenName = name;
        return this;
    };

    this.setBirthDate = function (birthDate) {
        this.birthDate = birthDate;
        return this;
    };

    this.setDeathDate = function (deathDate) {
        this.deathDate = deathDate;
        return this;
    };

    this.setFirstParent = function (parentId) {
        this.firstParent = parentId;
        return this;
    };

    this.setSecondParent = function (parentId) {
        this.secondParent = parentId;
        return this;
    };

    this.setPartner = function (partnerId) {
        this.partner = partnerId;
        return this;
    };

    this.setDescription = function (content) {
        this.description = content;
        return this;
    }
};