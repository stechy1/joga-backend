<?php

namespace app\controller\admin;


use app\controller\BaseApiController;
use app\controller\Constants;
use app\middleware\AuthMiddleware;
use app\model\http\IResponse;
use app\model\manager\jwt\JWTManager;
use app\model\http\IRequest;
use app\model\util\StatusCodes;
use Exception;
use Logger;

/**
 * Class AdminBaseController
 * Třída poskytující základni metody pro kontrolery v administraci
 * @package app\controller\admin
 */
abstract class AdminBaseController extends BaseApiController {

    /**
     * @var Logger
     */
    private $logger;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function onStartup(IRequest $request, IResponse $response) {
        $jwt = $response->getFlowData(AuthMiddleware::JWT_DATA);
        if ($jwt == null) {
            $this->setResponseMessage("Pro přístup do aktministrace, musíte být přihlášený!", Constants::RESPONSE_MESSAGE_TYPE_WARNING);
            $response->setCode(StatusCodes::UNAUTHORIZED);
            $this->valid = false;
        }
    }


}