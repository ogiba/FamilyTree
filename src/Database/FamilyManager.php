<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 16:50
 */

namespace Database;


use Model\Family;
use Model\FamilyMember;

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
        $stmt = $connection->prepare("SELECT * FROM tree_nodes tn 
                                              INNER JOIN family_members AS fm ON tn.person = fm.id
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

        $stmt->close();
        $connection->close();
        return $result;
    }

    public function loadPossiblePartners($familyId, $memberId, $parentId)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT * FROM tree_nodes tn 
                                              INNER JOIN family_members AS fm ON tn.person = fm.id
                                              WHERE tn.family = ? AND tn.person != ? AND tn.person != ?
                                              ORDER BY fm.firstName ASC");
        $stmt->bind_param("iii", $familyId, $memberId, $parentId);
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
     * @return FamilyMember[] array
     */
    public function loadFamilyMembers($familyId)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT * FROM tree_nodes tn 
                                              INNER JOIN family_members AS fm ON tn.person = fm.id
                                              WHERE tn.family = ?");
        $stmt->bind_param("i", $familyId);
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $result = array();

        while ($stmt->fetch()) {
            $familyMember = $this->arrayToObject($data, FamilyMember::class);
            $result[] = $familyMember;
        }

        $connection->close();

        $baseNode = null;
        foreach ($result as $key => $value) {
            if ($value->parent == null) {
                $value->partner = $this->findPartner($value->partner, $result);
                $value->children = $this->recursiveChildrenFilter($value->id, $result);
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
            if ($value->parent == $parentId) {
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
            if ($value->partner == null || $value->partner instanceof FamilyMember) {
                continue;
            }

            if ($value->id == $memberId) {
                $partner = $value;
                break;
            }
        }

        return $partner;
    }

    /**
     * Loads children for given family
     * @param $familyId
     * @param $parentId
     * @return array
     */
    public function loadChildren($familyId, $parentId)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT * FROM family_members WHERE parent = ?");
        $stmt->bind_param("ii", $familyId, $parentId);
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
                                                    fm.parent,
                                                    fp.partner AS partner
                                              FROM family_members fm
                                              INNER JOIN family_partners fp ON fm.id = fp.base
                                              WHERE fm.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $data = $this->bindResult($stmt);

        $familyMember = null;
        if ($stmt->fetch()) {
            $familyMember = $this->arrayToObject($data, FamilyMember::class);
        }

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

        $connection = $this->createConnection();
        $stmt = $connection->prepare("UPDATE family_members
                                              SET firstName = ?,
                                                  lastName = ?,
                                                  maidenName = ?,
                                                  birthDate = ?,
                                                  deathDate = ?,
                                                  parent = ?,
                                                  partner = ?
                                              WHERE id = ?");
        $stmt->bind_param("sssssiii", $familyMember->firstName,
            $familyMember->lastName, $familyMember->maidenName,
            $familyMember->birthDate, $familyMember->deathDate,
            intval($familyMember->parent), $partnerId,
            $id);
        $stmt->execute();

        $isSucceed = $stmt->affected_rows > 0;

        if ($isSucceed && $partnerId != null) {
            $stmt->close();
            $stmt = $connection->prepare("UPDATE family_members 
                                                SET partner = ?
                                                WHERE id = ?");
            $stmt->bind_param("ii", $id, $partnerId);
            $stmt->execute();
        }

        $connection->close();

        return $isSucceed;
    }

    /**
     * @param $familyId
     * @param FamilyMember $familyMember
     * @return boolean
     */
    public function insertNewMember($familyId, $familyMember)
    {
        $partnerId = $familyMember->partner == '' ? null : intval($familyMember->partner);
        $parentId = $familyMember->parent == '' ? null : intval($familyMember->parent);

        $connection = $this->createConnection();
        $stmt = $connection->prepare("INSERT INTO family_members (firstName, lastName, maidenName, 
                                                                          deathDate, birthDate, parent, partner) 
                                            VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssii", $familyMember->firstName, $familyMember->lastName,
            $familyMember->maidenName, $familyMember->deathDate, $familyMember->birthDate,
            $parentId, $partnerId);
        $stmt->execute();

        $addedMember = $stmt->affected_rows > 0;

        $isSucceed = false;
        if ($addedMember) {
            $newMemberId = $stmt->insert_id;
            $stmt->close();

            $stmt = $connection->prepare("INSERT INTO tree_nodes (family, person) VALUES (?,?)");
            $stmt->bind_param("ii", $familyId, $newMemberId);
            $stmt->execute();

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
        return $isSucceed;
    }
}