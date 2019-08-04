<?php

namespace app\controller\admin;


use Logger;

class ApiAdminDashboardController extends AdminBaseController {

    /**
     * @var Logger
     */
    private $logger;


    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

}