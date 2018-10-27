<?php

namespace app\controller;

use app\model\service\request\IRequest;

class ApiFunController extends BaseController {

    public function articleDELETEAction(IRequest $request) {
        $this->addData("info", $_SERVER['REQUEST_METHOD']);
    }

}