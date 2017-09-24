<?php
/**
 * User: ogiba
 * Date: 19.08.2017
 * Time: 22:17
 */

namespace API;

use Utils\SerializeManager;

abstract class BaseRestController
{
    protected  $serializeManager;

    public function setupSerializer() {
        $this->serializeManager = new SerializeManager();
    }
}