<?php


namespace app\controller;


use app\middleware\AuthMiddleware;
use app\model\http\IRequest;
use app\model\http\IResponse;
use app\model\manager\file\FileManager;
use app\model\manager\file\FileManipulationException;
use app\model\manager\user\UserException;
use app\model\util\StatusCodes;
use Logger;

/**
 * Class ApiFileBrowserController
 * Kontroler pro přístup k určitým souborům v souborovém systému
 * @Inject FileManager
 * @package app\controller
 */
class ApiFileBrowserController extends BaseApiController {

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var FileManager
     */
    private $filemanager;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function defaultGETAction(IRequest $request, IResponse $response) {
        $jwt = $response->getFlowData(AuthMiddleware::JWT_DATA);
        if (empty($jwt)) {
            $this->logger->error("Nepřihlášený uživatel se snaží získat přístup k souborovému systému!");
            $response->setCode(StatusCodes::UNAUTHORIZED);
            $this->setResponseMessage("Nemáte oprávnění manipulovat se soubory na serveru!", Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
        $clientId = +$jwt->id;

        $subfolder = "";
        if ($request->hasParams(1)) {
            $subfolder = $request->getParam(0);
        }

        try {
            $usersUploads = $this->filemanager->getDirectory(FileManager::FOLDER_USER_UPLOADS);
            $userUploads = FileManager::mergePath($usersUploads, false, $clientId);
            $userSubfolder = FileManager::mergePath($userUploads, false, $subfolder);
            $this->filemanager->createDirectory($userSubfolder, true);
            $files = $this->filemanager->getFilesFromDirectory($userSubfolder, $userUploads);
            $response->addData("files", $files);
        } catch (FileManipulationException $ex) {
            $this->logger->error($ex->getMessage());
            $response->setCode(StatusCodes::BAD_REQUEST);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }


}