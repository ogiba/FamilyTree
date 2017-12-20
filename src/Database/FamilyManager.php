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

class FamilyManager extends BaseDatabaseManager
{
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

        return $result;
    }

    /**
     * Returns array of familyMembers for given family ID
     *
     * @param $familyId
     * @return FamilyMember[] array
     */
    public function loadFamilyMembers($familyId)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT * FROM family_members WHERE family = ?");
        $stmt->bind_param("i", $familyId);
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $result = array();

        while ($stmt->fetch()) {
            $familyMember = $this->arrayToObject($data, FamilyMember::class);
            $result[] = $familyMember;
        }

        $baseNode = null;
        foreach ($result as $key => $value) {
            if ($value->parent == null) {
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
                $value->children = $this->recursiveChildrenFilter($value->id, $children);
                $result[] = $value;
            }
        }

        return $result;
    }

    public function loadChildren($familyId, $parentId)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT * FROM family_members WHERE family = ? AND parent = ?");
        $stmt->bind_param("ii", $familyId, $parentId);
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $result = array();

        while ($stmt->fetch()) {
            $familyMember = $this->arrayToObject($data, FamilyMember::class);
            $result[] = $familyMember;
        }

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
        $stmt = $connection->prepare("SELECT * FROM family_members WHERE id = ?");
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
}