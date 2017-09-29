<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 19:58
 */

namespace Database;


use Model\About;
use Model\Section;
use Model\SectionInformation;

class InformationManager extends BaseDatabaseManager {
    const ABOUT_ME = 1;

    public function loadSections()
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT * FROM sections");
        $stmt->execute();

        $sectionData = $this->bindResult($stmt);
        $sections = array();

        while ($stmt->fetch()) {
            $sections[] = $this->arrayToObject($sectionData, Section::class);
        }

        return $sections;
    }

    public function loadAboutMe()
    {
        $sectionId = self::ABOUT_ME;
        $connection = $this->createConnection();

        $stmt = $connection->prepare("SELECT * FROM sections WHERE id = ?");
        $stmt->bind_param("i", $sectionId);
        $stmt->execute();

        $sectionData = $this->bindResult($stmt);
        $section = "";

        if ($stmt->fetch()) {
            $section = $sectionData["name"];
        }

        if (empty($section)) {
            return null;
        }

        $stmt->reset();
        $stmt = $connection->prepare("SELECT * FROM informations WHERE section = ?");
        $stmt->bind_param("i", $sectionId);
        $stmt->execute();

        $data = $this->bindResult($stmt);

        if ($stmt->fetch()) {
            $about = $this->arrayToObject($data, SectionInformation::class);
            $about->setSection($section);

            $connection->close();
            return $about;
        } else {
            $connection->close();
            return null;
        }
    }

    /**
     *
     * Load section information by given {@code id}
     *
     * @param $id
     * @return SectionInformation|null
     */
    public function loadSectionById($id)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT i.id, s.name AS section, i.content, i.image, i.dateTime FROM informations i INNER JOIN sections AS s ON i.section = s.id WHERE s.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $data = $this->bindResult($stmt);

        if ($stmt->fetch()) {
            $section = $this->arrayToObject($data, SectionInformation::class);
            return $section;
        } else {
            return null;
        }
    }
}