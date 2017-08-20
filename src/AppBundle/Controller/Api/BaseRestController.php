<?php
/**
 * User: ogiba
 * Date: 19.08.2017
 * Time: 22:17
 */

namespace AppBundle\Controller\Api;

use AppBundle\Utils\SerializeManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class BaseRestController extends Controller
{
    protected $serializeManager;

    public function setupSerializer() {
        $this->serializeManager = new SerializeManager();
    }
}