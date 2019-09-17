<?php


namespace app\controller\account;


use app\controller\BaseApiController;
use app\model\manager\jwt\JWTManager;
use app\model\service\request\IRequest;
use app\model\util\StatusCodes;
use Exception;
use Logger;

/**
 * Class AdminBaseController
 * Třída poskytující základni metody pro kontrolery v accountu
 * @Inject JWTManager
 * @package app\controller\admin
 */
abstract class BaseAccountController extends BaseApiController {

    protected const JWT_DATA = "jwt_data";

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var JWTManager
     */
    private $jwtmanager;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function onStartup(IRequest $request) {
        $headers = $request->getHeaders();
        $this->logger->trace($headers);
        if (!isset($headers['authorization']) && !isset($headers['Authorization'])) {
            return;
        }

        $jwt = isset($headers['authorization'])
            ? $headers['authorization']
            : $headers['Authorization'];

        try {
            $jwtData = $this->jwtmanager->decodeJWT($jwt);
            $this->logger->trace($jwtData);
            $this->flowData[self::JWT_DATA] = $jwtData;
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se naparsovat JWT!", $ex);
            $this->setCode(StatusCodes::UNAUTHORIZED);
            $this->setResponseMessage("Přihlašovací token není validní!", self::RESPONSE_MESSAGE_TYPE_ERROR);
            return;
        }
    }

    public function onExit() {
        $this->flowData = [];
    }


}