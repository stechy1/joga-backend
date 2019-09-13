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
        $email = $request->get(UserManager::COLUMN_EMAIL);
        $name = $request->get(UserManager::COLUMN_NAME);
        $password = $request->get(UserManager::COLUMN_PASSWORD);
        $password2 = $request->get(UserManager::COLUMN_PASSWORD . '2');

        if ($password != $password2) {
            $this->setCode(StatusCodes::PRECONDITION_FAILED);
            return;
        }

        try {
            $this->usermanager->register($email, $name, $password);
        } catch (UserException $ex) {
            $this->logger->error($ex);
            $this->setCode(StatusCodes::PRECONDITION_FAILED);
        }
    }

    /**
     * @param IRequest $request
     */
    public function loginPOSTAction(IRequest $request) {
        $email = $request->get(UserManager::COLUMN_EMAIL);
        $password = $request->get(UserManager::COLUMN_PASSWORD);
        $remember = $request->get(UserManager::FLAG_REMEMBER);

        try {
            $jwt = $this->usermanager->login($email, $password, $remember);
            $this->addData('jwt', $jwt);
        } catch (UserException $ex) {
            $this->logger->error($ex);
            $this->setCode(StatusCodes::PRECONDITION_FAILED);
        }
    }
}