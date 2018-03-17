<?php
/**
 * User: ogiba
 * Date: 19.08.2017
 * Time: 22:17
 */

namespace API;

use Utils\ResponseHeaders;
use Utils\SerializeManager;
use Utils\StatusCode;

abstract class BaseRestController {
    protected $serializeManager;

    public function setupSerializer()
    {
        $this->serializeManager = new SerializeManager();
    }

    protected function sendJsonResponse($data)
    {
        header(ResponseHeaders::CONTENT_TYPE_JSON);
        echo json_encode($data);
    }

    protected function sendJsonNewResponse($response)
    {
        header(ResponseHeaders::CONTENT_TYPE_JSON);
        header("HTTP/1.1 " . StatusCode::getMessageForCode($response->getStatusCode()));
        echo json_encode($response);
    }
}