<?php


namespace app\controller\admin;


use app\model\manager\user\UserManager;
use app\model\service\request\IRequest;
use app\model\util\StatusCodes;
use Exception;
use Logger;

/**
 * Class ApiAdminClientsController
 * @Inject UserManager
 * @package app\controller\admin
 */
class ApiAdminClientsController extends AdminBaseController {

    const KEY_POST_ALL_FROM = 'from';
    const KEY_POST_ALL_COUNT = 'count';
    const KEY_POST_ALL_CLIENTS = 'clients';

    /**
     * @var UserManager
     */
    private $usermanager;

    private $logger;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function defaultPOSTAction(IRequest $request) {
        try {
            $clients = $this->usermanager->all(
                $request->get(self::KEY_POST_ALL_COUNT, 10),
                $request->get(self::KEY_POST_ALL_FROM, -1)
            );
            $this->addData(self::KEY_POST_ALL_CLIENTS, $clients);
            $this->logger->trace($clients);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            $this->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), self::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }


}