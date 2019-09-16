<?php


namespace app\controller\account;


use app\model\manager\user\UserDataException;
use app\model\manager\user\UserException;
use app\model\manager\user\UserManager;
use app\model\service\request\IRequest;
use app\model\util\StatusCodes;
use Logger;

/**
 * Class ApiAccountPersonalController
 * @Inject UserManager
 * @package app\controller\account
 */
class ApiAccountPersonalController extends BaseAccountController {

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var UserManager
     */
    private $usermanager;

    const PARAM_OLD_PASSWORD = "oldPassword";
    const PARAM_NEW_PASSWORD = "newPassword";
    const PARAM_NEW_PASSWORD2 = "newPassword2";
    const PARAM_PERSONAL_DATA = "personalData";

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function defaultGETAction(IRequest $request) {
        $jwt = $this->flowData[BaseAccountController::JWT_DATA];
        $id = +$jwt->id;

        try {
            $data = $this->usermanager->byId($id);
            $this->addData(self::PARAM_PERSONAL_DATA, $data);
        } catch (UserException $ex) {
            $this->logger->error($ex->getMessage());
            $this->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), self::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function defaultPOSTAction(IRequest $request) {
        $jwt = $this->flowData[BaseAccountController::JWT_DATA];
        $id = +$jwt->id;
        $name = $request->get(UserManager::COLUMN_NAME);
        $password = $request->get(UserManager::COLUMN_PASSWORD);

        try {
            $this->usermanager->update($id, $name, $password);
            $this->setResponseMessage("Údaje byly úspěšně aktualizovány.");
        } catch (UserException | UserDataException $ex) {
            $this->logger->error($ex->getMessage());
            $this->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), self::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function update_passwordPOSTAction(IRequest $request) {
        $jwt = $this->flowData[BaseAccountController::JWT_DATA];
        $id = +$jwt->id;
        $oldPassword = $request->get(self::PARAM_OLD_PASSWORD);
        $newPassword = $request->get(self::PARAM_NEW_PASSWORD);
        $newPassword2 = $request->get(self::PARAM_NEW_PASSWORD2);

        try {
            $this->usermanager->updatePassword($id, $oldPassword, $newPassword, $newPassword2);
            $this->setResponseMessage("Heslo bylo úspěšně aktualizováno.");
        } catch (UserException | UserDataException $ex) {
            $this->logger->error($ex->getMessage());
            $this->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), self::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function deactivate_accountPOSTAction(IRequest $request) {
        $jwt = $this->flowData[BaseAccountController::JWT_DATA];
        $id = +$jwt->id;
        $password = $request->get(UserManager::COLUMN_PASSWORD);

        try {
            $this->usermanager->deactivate($id, $password);
            $this->setResponseMessage("Účet byl úspěšně deaktivovaný.");
        } catch (UserException | UserDataException $ex) {
            $this->logger->error($ex->getMessage());
            $this->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), self::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function disable_accountPOSTAction(IRequest $request) {
        $jwt = $this->flowData[BaseAccountController::JWT_DATA];
        $id = +$jwt->id;
        $password = $request->get(UserManager::COLUMN_PASSWORD);

        try {
            $this->usermanager->disable($id, $password);
            $this->setResponseMessage("Účet byl úspěšně zručený.");
        } catch (UserException | UserDataException $ex) {
            $this->logger->error($ex->getMessage());
            $this->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), self::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }
}