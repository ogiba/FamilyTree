<?php
/**
 * User: ogiba
 * Date: 19.08.2017
 * Time: 22:17
 */

namespace API;

use Model\Response;
use Utils\ResponseHeaders;
use Utils\SerializeManager;
use Utils\StatusCode;

abstract class BaseRestController {
    protected $serializeManager;

    public function setupSerializer()
    {
        $this->serializeManager = new SerializeManager();
    }

    /**
     * @param $data
     * @param int $statusCode
     */
    protected function sendJsonResponse($data, $statusCode = StatusCode::OK)
    {
        header(ResponseHeaders::CONTENT_TYPE_JSON);
        header("HTTP/1.1 " . StatusCode::getMessageForCode($statusCode));
        if (is_null($data)) {
            exit;
        }
        echo json_encode($data);
    }

    /**
     * @param Response $response
     */
    protected function sendJsonNewResponse($response)
    {
        header(ResponseHeaders::CONTENT_TYPE_JSON);
        header("HTTP/1.1 " . StatusCode::getMessageForCode($response->getStatusCode()));
        echo json_encode($response);
    }

    abstract function action($name, $action, $params);
}