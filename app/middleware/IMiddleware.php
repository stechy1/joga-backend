<?php

namespace app\middleware;


use app\model\http\IRequest;
use app\model\http\IResponse;

interface IMiddleware {

    function apply(IRequest $request, IResponse $response): void;

}