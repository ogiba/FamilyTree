function saveMember() {
    var firstName = $("#firstName").val();
    var lastName = $("#lastName").val();

    var member = new FamilyMember(firstName, lastName);

    alert(member.firstName)
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