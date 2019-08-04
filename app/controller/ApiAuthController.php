<?php


namespace app\controller;


use app\model\manager\user\UserException;
use app\model\manager\user\UserManager;
use app\model\service\request\IRequest;
use app\model\util\StatusCodes;
use Logger;

/**
 * Class ApiAuthController
 * @Inject UserManager
 * @package app\controller
 */
class ApiAuthController extends BaseApiController {

    private $logger;

    /**
     * @var UserManager
     */
    private $usermanager;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function registerPOSTAction(IRequest $request) {
        try {
            $this->usermanager->register($request->get('email'), $request->get('password'));
        } catch (UserException $ex) {
            $this->logger->error($ex);
            $this->setCode(StatusCodes::PRECONDITION_FAILED);
        }
    }

    /**
     * @param IRequest $request
     */
    public function loginPOSTAction(IRequest $request) {
        try {
            $jwt = $this->usermanager->login($request->get('email'), $request->get('password'));
            $this->addData('jwt', $jwt);
        } catch (UserException $ex) {
            $this->logger->error($ex);
            $this->setCode(StatusCodes::PRECONDITION_FAILED);
        }
    }
}