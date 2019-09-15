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
            $this->logger->error("Uživatel se zadaným ID {$id} nebyl nalezen!", $ex);
            $this->setCode(StatusCodes::NOT_FOUND);
        }
    }

    public function defaultPOSTAction(IRequest $request) {
        $jwt = $this->flowData[BaseAccountController::JWT_DATA];
        $id = +$jwt->id;
        $name = $request->get(UserManager::COLUMN_NAME);
        $password = $request->get(UserManager::COLUMN_PASSWORD);

        try {
            $this->usermanager->update($id, $name, $password);
        } catch (UserDataException $ex) {
            $this->logger->error("Nepodařilo se aktualizovat uživatelská data!", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        } catch (UserException $ex) {
            $this->logger->error("Uživatel pro aktualizaci dat nebyl nalezen!", $ex);
            $this->setCode(StatusCodes::NOT_FOUND);
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
        } catch (UserDataException $ex) {
            $this->logger->error("Nepodařilo se aktualizovat uživatelské heslo!", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        } catch (UserException $ex) {
            $this->logger->error("Uživatel pro aktualizaci hesla nebyl nalezen!", $ex);
            $this->setCode(StatusCodes::NOT_FOUND);
        }
    }

    public function deactivate_accountPOSTAction(IRequest $request) {
        $jwt = $this->flowData[BaseAccountController::JWT_DATA];
        $id = +$jwt->id;
        $password = $request->get(UserManager::COLUMN_PASSWORD);

        try {
            $this->usermanager->deactivate($id, $password);
        } catch (UserException $ex) {
            $this->logger->error("Nepodařilo se deaktivovat uživatelský účet!", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }
    }

    public function disable_accountPOSTAction(IRequest $request) {
        $jwt = $this->flowData[BaseAccountController::JWT_DATA];
        $id = +$jwt->id;
        $password = $request->get(UserManager::COLUMN_PASSWORD);

        try {
            $this->usermanager->disable($id, $password);
        } catch (UserException $ex) {
            $this->logger->error("Nepodařilo se zrušit uživatelský účet!", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }
    }
}