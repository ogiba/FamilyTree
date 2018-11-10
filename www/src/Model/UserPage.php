<?php
/**
 * User: ogiba
 * Date: 10.11.2018
 * Time: 22:22
 */

namespace Model;

use JsonSerializable;

class UserPage implements JsonSerializable {

    private $users;
    private $currentPage;
    private $numberOfPages;
    private $totalItems;

    public function jsonSerialize()
    {
        return [
            "users" => $this->users,
            "currentPage" => $this->currentPage,
            "numberOfPages" => $this->numberOfPages,
            "totalItems" => $this->totalItems
        ];
    }

     /**
     * @param mixed $posts
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @param mixed $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @param mixed $numberOfPages
     */
    public function setNumberOfPages($numberOfPages)
    {
        $this->numberOfPages = $numberOfPages;
    }

    /**
     * @param mixed $totalItems
     */
    public function setTotalItems($totalItems)
    {
        $this->totalItems = $totalItems;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return mixed
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @return mixed
     */
    public function getNumberOfPages()
    {
        return $this->numberOfPages;
    }

    /**
     * @return mixed
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }
}