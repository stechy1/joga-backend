<?php


namespace app\controller\admin;


use app\model\manager\UserManager;
use app\model\service\request\IRequest;
use app\model\util\StatusCodes;

/**
 * Class ApiAdminClientsController
 * @Inject UserManager
 * @package app\controller\admin
 */
class ApiAdminClientsController extends AdminBaseController {

    const KEY_GET_ALL_FROM = 'from';
    const KEY_GET_ALL_COUNT = 'count';
    const KEY_GET_ALL_CLIENTS = 'clients';

    /**
     * @var UserManager
     */
    private $usermanager;

    public function defaultGETAction(IRequest $request) {
        try {
            $clients = $this->usermanager->all(
                $request->get(self::KEY_GET_ALL_FROM, 0),
                $request->get(self::KEY_GET_ALL_COUNT, 10)
            );
            $this->addData(self::KEY_GET_ALL_CLIENTS, $clients);
        } catch (\Exception $ex) {
            $this->setCode(StatusCodes::NOT_FOUND);
            var_dump($ex);
        }
    }


}