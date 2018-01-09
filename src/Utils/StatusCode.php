<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 09.01.2018
 * Time: 20:18
 */

namespace Utils;


class StatusCode {
    const OK = 200;
    const NO_CONTENT = 204;
    const BAD_REQUEST = 400;
    const UNATHORIZED = 401;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const UNPROCESSED_ENTITY = 422;
    const INTERNAL_SERVER_ERROR = 500;
    const BAD_GATEWAY = 502;
}