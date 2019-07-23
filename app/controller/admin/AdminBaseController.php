<?php

namespace app\controller\admin;


use app\controller\BaseApiController;
use app\model\service\request\IRequest;

/**
 * Class AdminBaseController
 * Třída poskytující základni metody pro kontrolery v administraci
 * @package app\controller\admin
 */
abstract class AdminBaseController extends BaseApiController {

    public function onStartup(IRequest $request) {
        $headers = $request->getHeaders();
        if (!$headers['authorization']) {
            echo var_dump($headers);
        }
    }



}