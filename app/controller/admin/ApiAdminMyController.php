<?php


namespace app\controller\admin;


use app\model\manager\file\FileManipulationException;
use app\model\manager\file\InfoTypeConversionException;
use app\model\manager\my\MyManager;
use app\model\service\request\IRequest;
use app\model\util\StatusCodes;
use Exception;
use Logger;

/**
 * Class ApiAdminMyController
 * @Inject MyManager
 * @package app\controller\admin
 */
class ApiAdminMyController extends AdminBaseController {

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var MyManager
     */
    private $mymanager;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function defaultGETAction(IRequest $request) {
        try {
            $info = $this->mymanager->getInformations();
            $this->addData(MyManager::INFO_TYPE_MY, $info[MyManager::INFO_TYPE_MY]);
            $this->addData(MyManager::INFO_TYPE_STUDIO, $info[MyManager::INFO_TYPE_STUDIO]);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            $this->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), self::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function savePOSTAction(IRequest $request) {
        $what = $request->getParams()[0];
        if ($what == MyManager::INFO_TYPE_MY || $what == MyManager::INFO_TYPE_STUDIO) {
            try {
                $this->mymanager->save($what, $request->get(MyManager::COLUMN_INFO_CONTENT));
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage());
                $this->setCode(StatusCodes::NOT_FOUND);
                $this->setResponseMessage($ex->getMessage(), self::RESPONSE_MESSAGE_TYPE_ERROR);
            }
        } else {
            $this->setCode(StatusCodes::BAD_REQUEST);
            $this->setResponseMessage("Neznámý typ ukládané informace!", self::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function publishPATCHAction(IRequest $request) {
        $what = $request->getParams()[0];
        if ($what == MyManager::INFO_TYPE_MY || $what == MyManager::INFO_TYPE_STUDIO) {
            try {
                $this->mymanager->publish($what);
            } catch (InfoTypeConversionException | FileManipulationException $ex) {
                $this->logger->error($ex->getMessage(), $ex);
                $this->setCode(StatusCodes::NOT_FOUND);
                $this->setResponseMessage($ex->getMessage(), self::RESPONSE_MESSAGE_TYPE_ERROR);
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage(), $ex);
                $this->setCode(StatusCodes::NOT_FOUND);
                $this->setResponseMessage($ex->getMessage(), self::RESPONSE_MESSAGE_TYPE_ERROR);
            }
        } else {
            $this->setCode(StatusCodes::BAD_REQUEST);
            $this->setResponseMessage("Neznámý typ publikované informace!", self::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }
}