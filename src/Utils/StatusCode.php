<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 09.01.2018
 * Time: 20:18
 */

namespace Utils;


class StatusCode {
    // [Successful 2xx]
    const OK = 200;
    const NO_CONTENT = 204;

    // [Client Error 4xx]
    const BAD_REQUEST = 400;
    const UNATHORIZED = 401;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const UNPROCESSED_ENTITY = 422;

    // [Server Error 5xx]
    const INTERNAL_SERVER_ERROR = 500;
    const BAD_GATEWAY = 502;

    private static $messages = array(
        // [Informational 1xx]
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        // [Successful 2xx]
        200 => '200 OK',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        // [Redirection 3xx]
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        306 => '306 (Unused)',
        307 => '307 Temporary Redirect',
        // [Client Error 4xx]
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        407 => '407 Proxy Authentication Required',
        408 => '408 Request Timeout',
        409 => '409 Conflict',
        410 => '410 Gone',
        411 => '411 Length Required',
        412 => '412 Precondition Failed',
        413 => '413 Request Entity Too Large',
        414 => '414 Request-URI Too Long',
        415 => '415 Unsupported Media Type',
        416 => '416 Requested Range Not Satisfiable',
        417 => '417 Expectation Failed',
        // [Server Error 5xx]
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Timeout',
        505 => '505 HTTP Version Not Supported');


    public static function httpHeaderForStatus($code)
    {
        return 'HTTP/1.1 ' . self::$messages[$code];
    }

    public static function getMessageForCode($code)
    {
        return self::$messages[$code];
    }

    public static function isError($code)
    {
        return is_numeric($code) && $code >= self::BAD_REQUEST;
    }
}