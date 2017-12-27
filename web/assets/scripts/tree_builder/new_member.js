function saveMember() {
    var firstName = $("#firstName").val();
    var lastName = $("#lastName").val();
    var maidenName = $("#maidenName").val();
    var birthDate = $("#birthDate").val();
    var deathDate = $("#deathDate").val();

    var member = new FamilyMember(firstName, lastName);
    member.setMaidenName(maidenName)
        .setBirthDate(birthDate)
        .setDeathDate(deathDate);

    sendSaveRequest(member)
}

/**
 * Make http request to server to insert new member in to DB
 *
 * @param {FamilyMember} member
 */
function sendSaveRequest(member) {
    $.post(window.location.href + "/add_member", {
        "member": member
    }, function (response) {
        alert(response);
    })
}

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
    this.parent = null;
    this.partner = null;

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

    this.setParent = function (parentId) {
        this.parent = parentId;
        return this;
    }

    this.setPartner = function (partnerId) {
        this.partner = partnerId;
        return this;
    }
};