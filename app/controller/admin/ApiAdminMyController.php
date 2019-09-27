<?php


namespace app\controller\admin;


use app\model\http\IResponse;
use app\model\manager\file\FileManipulationException;
use app\model\manager\file\InfoTypeConversionException;
use app\model\manager\my\MyManager;
use app\model\http\IRequest;
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

    public function defaultGETAction(IRequest $request, IResponse $response) {
        try {
            $info = $this->mymanager->getInformations();
            $response->addData(MyManager::INFO_TYPE_MY, $info[MyManager::INFO_TYPE_MY]);
            $response->addData(MyManager::INFO_TYPE_STUDIO, $info[MyManager::INFO_TYPE_STUDIO]);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            $response->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function savePOSTAction(IRequest $request, IResponse $response) {
        $what = $request->getParams()[0];
        if ($what == MyManager::INFO_TYPE_MY || $what == MyManager::INFO_TYPE_STUDIO) {
            try {
                $this->mymanager->save($what, $request->get(MyManager::COLUMN_INFO_CONTENT));
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage());
                $response->setCode(StatusCodes::NOT_FOUND);
                $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
            }
        } else {
            $response->setCode(StatusCodes::BAD_REQUEST);
            $this->setResponseMessage("Neznámý typ ukládané informace!", Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function publishPATCHAction(IRequest $request, IResponse $response) {
        $what = $request->getParams()[0];
        if ($what == MyManager::INFO_TYPE_MY || $what == MyManager::INFO_TYPE_STUDIO) {
            try {
                $this->mymanager->publish($what);
            } catch (InfoTypeConversionException | FileManipulationException $ex) {
                $this->logger->error($ex->getMessage(), $ex);
                $response->setCode(StatusCodes::NOT_FOUND);
                $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage(), $ex);
                $response->setCode(StatusCodes::NOT_FOUND);
                $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
            }
        } else {
            $response->setCode(StatusCodes::BAD_REQUEST);
            $this->setResponseMessage("Neznámý typ publikované informace!", Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }
}