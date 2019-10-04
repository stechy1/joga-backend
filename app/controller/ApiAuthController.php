<?php


namespace app\controller;


use app\model\http\BadQueryStringException;
use app\model\http\IResponse;
use app\model\manager\user\UserDataException;
use app\model\manager\user\UserException;
use app\model\manager\user\UserManager;
use app\model\http\IRequest;
use app\model\util\StatusCodes;
use Logger;
use PHPMailer\PHPMailer\Exception;

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

    public function registerPOSTAction(IRequest $request, IResponse $response) {
        $email = $request->get(UserManager::COLUMN_EMAIL);
        $name = $request->get(UserManager::COLUMN_NAME);
        $password = $request->get(UserManager::COLUMN_PASSWORD);
        $password2 = $request->get(UserManager::COLUMN_PASSWORD . '2');

        if ($password != $password2) {
            $response->setCode(StatusCodes::PRECONDITION_FAILED);
            $this->setResponseMessage("Kontrolní heslo se neshoduje se zadaným.", Constants::RESPONSE_MESSAGE_TYPE_ERROR);
            return;
        }

        try {
            $this->usermanager->register($email, $name, $password);
            $this->setResponseMessage("Účet byl úspěšně vytvořen. Nyní se můžete přihlásit.");
        } catch (UserException|Exception $ex) {
            $this->logger->error($ex);
            $response->setCode(StatusCodes::PRECONDITION_FAILED);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function loginPOSTAction(IRequest $request, IResponse $response) {
        $email = $request->get(UserManager::COLUMN_EMAIL);
        $password = $request->get(UserManager::COLUMN_PASSWORD);
        $remember = $request->get(UserManager::FLAG_REMEMBER);

        try {
            $jwt = $this->usermanager->login($email, $password, $remember);
            $response->addData('jwt', $jwt);
        } catch (UserException $ex) {
            $this->logger->error($ex);
            $response->setCode(StatusCodes::UNAUTHORIZED);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function check_codeGETAction(IRequest $request, IResponse $response) {
        $checkCode = $request->getParam(0);
        if (empty($checkCode)) {
            throw new BadQueryStringException("Typ informace není zadaný, nebo nemá správný formát!");
        }

        try {
            $this->usermanager->checkCode($checkCode);
        } catch (UserException | UserDataException $ex) {
            $this->logger->error($ex->getMessage());
            $response->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }
}