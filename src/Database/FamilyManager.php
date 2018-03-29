<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 16:50
 */

namespace Database;


use Model\ChildParentPair;
use Model\Family;
use Model\FamilyMember;
use Model\MemberImage;

class FamilyManager extends BaseDatabaseManager {
    /**
     * Return families from DB
     *
     * @return Family[] array
     */
    public function loadFamilies()
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT * FROM families");
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $result = array();

        while ($stmt->fetch()) {
            $family = $this->arrayToObject($data, Family::class);
            $result[] = $family;
        }

        $connection->close();
        return $result;
    }

    /**
     * Add new family to DB
     *
     * @param $familyName
     * @return bool
     */
    public function addFamily($familyName)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("INSERT INTO families (familyName) VALUES (?)");
        $stmt->bind_param("s", $familyName);
        $stmt->execute();

        $isSucceed = $stmt->affected_rows > 0;
        $connection->close();

        return $isSucceed;
    }

    /**
     * Returns array of familyMembers for given family ID
     *
     * @param $familyId
     * @return array
     */
    public function loadFamily($familyId)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT fm.id,
                                                    fm.firstName,
                                                    fm.lastName,
                                                    fm.maidenName,
                                                    fm.birthDate,
                                                    fm.deathDate,
                                                    fm.image,
                                                    fm.description,
                                                    fp.partner AS partner,
                                                    tn.baseNode AS base
                                              FROM tree_nodes tn 
                                              INNER JOIN family_members AS fm ON tn.person = fm.id
                                              INNER JOIN family_partners AS fp ON fm.id = fp.base
                                              WHERE tn.family = ? 
                                              ORDER BY fm.firstName ASC");
        $stmt->bind_param("i", $familyId);
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $result = array();

        while ($stmt->fetch()) {
            $familyMember = $this->arrayToObject($data, FamilyMember::class);
            $result[] = $familyMember;
        }

        foreach ($result as $key => $value) {
            $parents = $this->loadParents($stmt, $value->id);
            $this->bindParentsToMember($value, $parents);
        }

        $stmt->close();
        $connection->close();
        return $result;
    }

    /**
     * Experimental method that allows to store parent in separated table
     *
     * @param \mysqli_stmt $stmt
     * @param int $childId
     * @return array|ChildParentPair
     */
    private function loadParents($stmt, $childId)
    {
        $stmt->prepare("SELECT * FROM family_parents WHERE child = ?");
        $stmt->bind_param("i", $childId);
        $stmt->execute();

        $parentsData = $this->bindResult($stmt);
        $parentsResult = array();

        $resultFetched = $stmt->fetch();

        if (is_null($resultFetched)) {
            return $parentsResult;
        }


        while ($resultFetched) {
            $parentsResult[] = $this->arrayToObject($parentsData, ChildParentPair::class);
            $resultFetched = $stmt->fetch();
        }
//        $stmt->close();
        return $parentsResult;
    }

    private function bindMemberData($stmt, $member)
    {
        $parents = $this->loadParents($stmt, $member->id);
        $this->bindParentsToMember($member, $parents);
        $member->image = $this->loadMemberImage($stmt, $member->id);
    }

    /**
     * @param FamilyMember $member
     * @param ChildParentPair[] $parents
     */
    private function bindParentsToMember($member, $parents)
    {
        if (count($parents) > 0 && count($parents) < 2) {
            $member->firstParent = $parents[0]->parent;
            $member->secondParent = null;
        } elseif (count($parents) > 0 && count($parents) == 2) {
            $member->firstParent = $parents[0]->parent;
            $member->secondParent = $parents[1]->parent;
        }
    }

    /**
     * @param \mysqli_stmt $stmt
     * @param $memberId
     *
     * @return array
     */
    private function loadMemberImage($stmt, $memberId)
    {
        $stmt->prepare("SELECT * FROM member_images WHERE memberId = ?");
        $stmt->bind_param("i", $memberId);
        $stmt->execute();

        $data = $this->bindResult($stmt);

        $result = array();

        while ($stmt->fetch()) {
            $memberImage = $this->arrayToObject($data, MemberImage::class);

            $imageSize = filesize($memberImage->image);

            if (is_bool($imageSize) && !$imageSize) {
                $imageSize = 0;
            }

            $memberImage->size = $imageSize;
            $result[] = $memberImage;
        }

        return $result;
    }

    public function loadPossiblePartners($familyId, $memberId)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT fm.id,
                                                    fm.firstName,
                                                    fm.lastName,
                                                    fm.maidenName,
                                                    fm.birthDate,
                                                    fm.deathDate,
                                                    fm.image,
                                                    fm.description
                                              FROM tree_nodes tn 
                                              INNER JOIN family_members AS fm ON tn.person = fm.id
                                              WHERE tn.family = ? AND tn.person != ?
                                              ORDER BY fm.firstName ASC");
        $stmt->bind_param("ii", $familyId, $memberId);
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $result = array();

        while ($stmt->fetch()) {
            $familyMember = $this->arrayToObject($data, FamilyMember::class);
            $result[] = $familyMember;
        }

        $connection->close();
        return $result;
    }

    /**
     * Returns base node with children for given family ID
     *
     * @param $familyId
     * @return FamilyMember array
     */
    public function loadFamilyMembers($familyId)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT fm.id,
                                                    fm.firstName,
                                                    fm.lastName,
                                                    fm.maidenName,
                                                    fm.birthDate,
                                                    fm.deathDate,
                                                    fm.image,
                                                    fm.description,
                                                    fp.partner AS partner,
                                                    tn.baseNode AS base
                                              FROM tree_nodes tn 
                                              INNER JOIN family_members AS fm ON tn.person = fm.id
                                              INNER JOIN family_partners AS fp ON fm.id = fp.base
                                              WHERE tn.family = ?");
        $stmt->bind_param("i", $familyId);
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $result = array();

        while ($stmt->fetch()) {
            $familyMember = $this->arrayToObject($data, FamilyMember::class);
            $result[] = $familyMember;
        }

        return $this->prepareStructure($result, $stmt, $connection);
    }

    public function loadFamilyMembersForMember($familyId, $memberId)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT fm.id,
                                                    fm.firstName,
                                                    fm.lastName,
                                                    fm.maidenName,
                                                    fm.birthDate,
                                                    fm.deathDate,
                                                    fm.image,
                                                    fm.description,
                                                    fp.partner AS partner,
                                                    0 AS base
                                              FROM tree_nodes tn 
                                              INNER JOIN family_members AS fm ON tn.person = fm.id
                                              INNER JOIN family_partners AS fp ON fm.id = fp.base
                                              WHERE tn.family = ?");
        $stmt->bind_param("i", $familyId);
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $result = array();

        while ($stmt->fetch()) {
            $familyMember = $this->arrayToObject($data, FamilyMember::class);

            if ($familyMember->id == $memberId) {
                $familyMember->base = true;
            }

            $result[] = $familyMember;
        }

        return $this->prepareStructure($result, $stmt, $connection);
    }

    /**
     * @param FamilyMember[] $members
     * @param \mysqli_stmt $stmt
     * @param \mysqli $connection
     *
     * @return FamilyMember
     */
    private function prepareStructure($members, $stmt, $connection)
    {
        foreach ($members as $key => $value) {
            $parents = $this->loadParents($stmt, $value->id);
            $this->bindParentsToMember($value, $parents);
            $value->image = $this->loadMemberImage($stmt, $value->id);
        }

        $connection->close();

        $baseNode = null;
        foreach ($members as $key => $value) {
            if ($value->base) {
                $value->partner = $this->findPartner($value->partner, $members);
                $value->children = $this->recursiveChildrenFilter($value->id, $members);
                $baseNode = $value;
                break;
            }
        }

        return $baseNode;
    }


    private function recursiveChildrenFilter($parentId, $children)
    {
        $result = array();

        foreach ($children as $key => $value) {
            if ($value->firstParent == $parentId || $value->secondParent == $parentId) {
                $value->partner = $this->findPartner($value->partner, $children);
                $value->children = $this->recursiveChildrenFilter($value->id, $children);
                $result[] = $value;
            }
        }

        return $result;
    }

    private function findPartner($memberId, $members)
    {
        $partner = null;

        foreach ($members as $key => $value) {
            if ($value->partner == null) {
                continue;
            }

            $newValue = null;
            if ($value->partner instanceof FamilyMember) {
                $newValue = clone $value;
                $newValue->partner = $value->partner->id;
            } else {
                $newValue = $value;
            }

            if ($newValue->id == $memberId) {
                $partner = $newValue;
                break;
            }
        }

        return $partner;
    }

    /**
     * Returns member from DB that match given id
     *
     * @param $id
     * @return FamilyMember mixed|null
     */
    public function getFamilyMemberDetails($id)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT fm.id,
                                                    fm.firstName,
                                                    fm.lastName,
                                                    fm.maidenName,
                                                    fm.birthDate,
                                                    fm.deathDate,
                                                    fm.image,
                                                    fm.description,
                                                    fp.partner AS partner,
                                                    tn.baseNode AS base
                                              FROM family_members fm
                                              INNER JOIN family_partners fp ON fm.id = fp.base
                                              INNER JOIN tree_nodes tn ON fm.id = tn.person
                                              WHERE fm.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $data = $this->bindResult($stmt);

        $familyMember = null;
        if ($stmt->fetch()) {
            $familyMember = $this->arrayToObject($data, FamilyMember::class);
        }

        if (!is_null($familyMember)) {
            $stmt->prepare("SELECT * FROM family_members WHERE id = ?");
            $stmt->bind_param("i", $familyMember->partner);
            $stmt->execute();

            $partnerData = $this->bindResult($stmt);

            if ($stmt->fetch()) {
                $partner = $this->arrayToObject($partnerData, FamilyMember::class);
                $this->bindMemberData($stmt, $partner);
                $familyMember->partner = $partner;
            }

            $this->bindMemberData($stmt, $familyMember);
        }

        $stmt->close();
        $connection->close();
        return $familyMember;
    }

    /**
     * @param $id
     * @param FamilyMember $familyMember
     * @return boolean
     */
    public function updateFamilyMember($id, $familyMember)
    {
        $partnerId = $familyMember->partner == '' ? null : intval($familyMember->partner);
        $firstParentId = $familyMember->firstParent == '' || is_null($familyMember->firstParent) ? null : intval($familyMember->firstParent);
        $secondParentId = $familyMember->secondParent == '' || is_null($familyMember->secondParent) ? null : intval($familyMember->secondParent);

        $connection = $this->createConnection();
        $stmt = $connection->prepare("UPDATE family_members
                                              SET firstName = ?,
                                                  lastName = ?,
                                                  maidenName = ?,
                                                  birthDate = ?,
                                                  deathDate = ?,
                                                  description = ?
                                              WHERE id = ?");
        $stmt->bind_param("ssssssi", $familyMember->firstName,
            $familyMember->lastName, $familyMember->maidenName,
            $familyMember->birthDate, $familyMember->deathDate,
            $familyMember->description, $id);
        $stmt->execute();

        $isSucceed = $stmt->affected_rows > 0;

        $this->updateSelectedParentForMember($stmt, $familyMember->id, $firstParentId, 0);

        $isSucceed = !$isSucceed ? $stmt->affected_rows > 0 : $isSucceed;

        $this->updateSelectedParentForMember($stmt, $familyMember->id, $secondParentId, 1);

        $isSucceed = !$isSucceed ? $stmt->affected_rows > 0 : $isSucceed;

        if ($partnerId != null) {
            $stmt->close();
            $stmt = $connection->prepare("UPDATE family_partners fm, family_partners fm1 
                                                SET fm.partner = ?, fm1.partner = ?
                                                WHERE fm.base = ? AND fm1.base = ?");
            $stmt->bind_param("iiii", $partnerId, $id, $id, $partnerId);
            $stmt->execute();

            $isSucceed = !$isSucceed ? $stmt->affected_rows > 0 : $isSucceed;
        }

        if ($familyMember->base == 1) {
            $isSucceed = $this->updateBaseNode($stmt, $familyMember->id, $familyMember->base);
        }

        $stmt->close();
        $connection->close();

        return $isSucceed;
    }

    /**
     * @param $familyId
     * @param FamilyMember $familyMember
     * @return \stdClass
     */
    public function insertNewMember($familyId, $familyMember)
    {
        $partnerId = $familyMember->partner == '' ? null : intval($familyMember->partner);
        $firstParentId = $familyMember->firstParent == '' || is_null($familyMember->firstParent) ? null : intval($familyMember->firstParent);
        $secondParentId = $familyMember->secondParent == '' || is_null($familyMember->secondParent) ? null : intval($familyMember->secondParent);

        $connection = $this->createConnection();
        $stmt = $connection->prepare("INSERT INTO family_members (firstName, lastName, maidenName, 
                                                                          deathDate, birthDate, description) 
                                            VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $familyMember->firstName, $familyMember->lastName,
            $familyMember->maidenName, $familyMember->deathDate,
            $familyMember->birthDate, $familyMember->description);
        $stmt->execute();

        $addedMember = $stmt->affected_rows > 0;
        $addedMemberId = $stmt->insert_id;

        $isSucceed = false;
        if ($addedMember) {
            $newMemberId = $stmt->insert_id;
            $stmt->close();

            $stmt = $connection->prepare("INSERT INTO tree_nodes (family, person) VALUES (?,?)");
            $stmt->bind_param("ii", $familyId, $newMemberId);
            $stmt->execute();

            $this->addParentForMember($stmt, $newMemberId, $firstParentId);
            $this->addParentForMember($stmt, $newMemberId, $secondParentId);

            $stmt->close();
            $stmt = $connection->prepare("INSERT INTO family_partners(base, partner) VALUES (?,?)");
            $stmt->bind_param("ii", $newMemberId, $partnerId);
            $stmt->execute();

            $isSucceed = $stmt->affected_rows > 0;

            if ($isSucceed && !is_null($partnerId)) {
                $stmt->close();
                $stmt = $connection->prepare("INSERT INTO family_partners(base, partner) 
                                                      VALUES (?,?)
                                                      ON DUPLICATE  KEY UPDATE partner = VALUES(partner)");
                $stmt->bind_param("ii", $partnerId, $newMemberId);
                $stmt->execute();
            }
        }
        $stmt->close();

        $connection->close();

        $insertResult = new \stdClass;
        $insertResult->memberId = $addedMemberId;
        $insertResult->isSucceed = $isSucceed;

        return $insertResult;
    }

    /**
     * Remove user from DB
     *
     * @param integer $memberId
     * @return bool
     */
    public function removeMember($memberId)
    {
        if (!isset($_SESSION["token"])) {
            exit;
        }

        $this->removeMemberPartner($memberId);
        $this->removeMembertParents($memberId);
        $this->removeTreeNodeByPerson($memberId);
        $this->removeMemberImage($memberId);

        $database = $this->createConnection();
        $stmt = $database->prepare("DELETE FROM family_members WHERE id = ?");
        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $stmt->fetch();
        $isSuccess = $stmt->affected_rows > 0;
        $database->close();

        return $isSuccess;
    }

    private function removeMemberPartner($memberId)
    {
        $database = $this->createConnection();
        $stmt = $database->prepare("DELETE FROM family_partners WHERE base = ?");
        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $stmt->fetch();
        $isSuccess = $stmt->affected_rows > 0;
        $database->close();
        return $isSuccess;
    }

    private function removeMembertParents($memberId)
    {
        $database = $this->createConnection();
        $stmt = $database->prepare("DELETE FROM family_parents WHERE child = ?");
        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $stmt->fetch();
        $isSuccess = $stmt->affected_rows > 0;
        $database->close();
        return $isSuccess;
    }

    private function removeTreeNodeByPerson($personId)
    {
        $database = $this->createConnection();
        $stmt = $database->prepare("DELETE FROM tree_nodes WHERE person = ?");
        $stmt->bind_param("i", $personId);
        $stmt->execute();
        $stmt->fetch();
        $isSuccess = $stmt->affected_rows > 0;
        $database->close();
        return $isSuccess;
    }

    /**
     * Add uploaded images to db at given user id
     *
     * @param integer $memberId
     * @param string[] $files
     * @return bool
     */
    public function insertMemberImage($memberId, $files)
    {
        if (!isset($_SESSION["token"])) {
            exit;
        }

        $token = $_SESSION["token"];
        $isSuccees = false;

        foreach ($files as $file) {
            $database = $this->createConnection();
            $stmt = $database->prepare("INSERT INTO member_images (image, memberId) VALUES(?, ?)");
            $stmt->bind_param("si", $file, $memberId);
            $stmt->execute();
            $stmt->fetch();

            $isSuccees = $stmt->affected_rows > 0;

            $database->close();
        }

        return $isSuccees;
    }

    /**
     * Retrieves image from member_images table for given member id
     *
     * @param integer $memberId
     * @return MemberImage|null
     */
    public function retrieveMemberImage($memberId)
    {
        if (!isset($_SESSION["token"])) {
            exit;
        }

        $database = $this->createConnection();
        $stmt = $database->prepare("SELECT * FROM member_images WHERE memberId = ?");
        $stmt->bind_param("i", $memberId);
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $result = null;

        if ($stmt->fetch()) {
            $result = $this->arrayToObject($data, MemberImage::class);
        }

        $database->close();
        return $result;
    }

    /**
     * Allows to remove image from db for given user id.
     *
     * @param integer $memberId
     * @return bool - value that informs wheter record was successfuly removed from DB
     */
    public function removeMemberImage($memberId)
    {
        if (!isset($_SESSION["token"])) {
            exit;
        }

        $database = $this->createConnection();
        $stmt = $database->prepare("DELETE FROM member_images WHERE memberId = ?");
        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $stmt->fetch();
        $isSuccess = $stmt->affected_rows > 0;
        $database->close();
        return $isSuccess;
    }

    /**
     * @param \mysqli_stmt $stmt
     * @param int $id
     * @param int | null $parentId
     */
    private function addParentForMember($stmt, $id, $parentId)
    {
        $stmt->prepare("INSERT INTO family_parents(child, parent) 
                              VALUES (?,?)");
        $stmt->bind_param("ii", $id, $parentId);
        $stmt->execute();
    }

    /**
     * @param \mysqli_stmt $stmt
     * @param int $id
     * @param int | null $newParent
     * @param int $parentNumber
     */
    //TODO: Look for better solution in case of free time
    private function updateSelectedParentForMember($stmt, $id, $newParent, $parentNumber)
    {
        $result = $this->loadParentsForChildOrderedAsc($stmt, $id);

        $parentToUpdate = $result[$parentNumber];
        if (!is_null($parentToUpdate)) {
            $this->updateParentAtGivenId($stmt, $newParent, $parentToUpdate->id);
        }
    }

    /**
     * @param \mysqli_stmt $stmt
     * @param $memberId
     * @param $value
     *
     * @return boolean
     */
    private function updateBaseNode($stmt, $memberId, $value)
    {
        $stmt->prepare("UPDATE tree_nodes tn1, tree_nodes tn2 
                                SET tn1.baseNode = 0, tn2.baseNode = ?
                                WHERE tn2.person = ?");
        $stmt->bind_param("ii", $value, $memberId);
        $stmt->execute();
        $isSucceed = $stmt->affected_rows > 0;

        return $isSucceed;
    }

    /**
     * @param \mysqli_stmt $stmt
     * @param int $childId
     * @return ChildParentPair[]
     */
    private function loadParentsForChildOrderedAsc($stmt, $childId)
    {
        $stmt->prepare("SELECT * FROM family_parents WHERE child = ? ORDER BY id ASC");
        $stmt->bind_param("i", $childId);
        $stmt->execute();

        $data = $this->bindResult($stmt);

        $result = array();
        while ($stmt->fetch()) {
            $result[] = $this->arrayToObject($data, ChildParentPair::class);
        }

        return $result;
    }

    /**
     * @param \mysqli_stmt $stmt
     * @param int $newParent
     * @param int $id
     */
    private function updateParentAtGivenId($stmt, $newParent, $id)
    {
        $stmt->prepare("UPDATE family_parents SET parent = ? WHERE id = ?");
        $stmt->bind_param("ii", $newParent, $id);
        $stmt->execute();
    }
}