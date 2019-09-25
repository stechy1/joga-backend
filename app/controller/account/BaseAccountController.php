<?php


namespace app\controller\account;


use app\controller\BaseApiController;
use app\model\http\IResponse;
use app\model\manager\jwt\JWTManager;
use app\model\http\IRequest;
use app\model\util\StatusCodes;
use Exception;
use Logger;

/**
 * Class AdminBaseController
 * Třída poskytující základni metody pro kontrolery v accountu
 * @package app\controller\admin
 */
abstract class BaseAccountController extends BaseApiController {

    /**
     * @var Logger
     */
    private $logger;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

}