<?php


namespace app\controller;


use app\model\util\StatusCodes;
use Logger;

class BaseApiController extends BaseController {

    /**
     * @var Logger
     */
    private $logger;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    protected function sendResponce() {
        $this->setCode(StatusCodes::OK);
        echo json_encode($this->data);
    }
}