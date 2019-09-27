<?php

namespace app\middleware;


use app\model\http\IRequest;
use app\model\http\IResponse;
use Exception;

interface IMiddleware {

    /**
     * Apply rules from request to response
     *
     * @param IRequest $request
     * @param IResponse $response
     * @throws Exception
     */
    function apply(IRequest $request, IResponse $response): void;

}