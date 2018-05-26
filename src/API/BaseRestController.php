<?php
/**
 * User: ogiba
 * Date: 19.08.2017
 * Time: 22:17
 */

namespace API;

use Database\LoginManager;
use Model\Response;
use Utils\Base\BaseController;
use Utils\ResponseHeaders;
use Utils\SerializeManager;
use Utils\StatusCode;

abstract class BaseRestController extends BaseController {
    protected $serializeManager;
    /**
     * @var LoginManager
     */
    private $loginManager;

    public function setupSerializer()
    {
        $this->serializeManager = new SerializeManager();
        $this->loginManager = new LoginManager();
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

    protected function checkIsAdmin($token)
    {
        return $this->loginManager->checkUserPrivilegesByToken($token);
    }
}