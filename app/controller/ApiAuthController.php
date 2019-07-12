<?php


namespace app\controller;


use app\model\manager\UserManager;
use app\model\service\exception\UserException;
use app\model\service\request\IRequest;
use Logger;

/**
 * Class ApiAuthController
 * @Inject UserManager
 * @package app\controller
 */
class ApiAuthController extends BaseController {

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
            $this->addData('result', 'success');
        } catch (UserException $ex) {
            $this->logger->error($ex);
            $this->addData('result', 'fail');
        }
    }

    /**
     * @param IRequest $request
     */
    public function loginPOSTAction(IRequest $request) {
        try {
            $jwt = $this->usermanager->login($request->get('email'), $request->get('password'));
            $this->addData('result', 'success');
            $this->addData('jwt', $jwt);
        } catch (UserException $ex) {
            $this->logger->error($ex);
            $this->addData('result', 'fail');
        }
    }
}