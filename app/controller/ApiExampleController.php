<?php

namespace app\controller;


use Logger;

class ApiExampleController extends BaseController {

private $logger;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }


}