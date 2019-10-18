<?php


namespace app\controller;


use app\middleware\AuthMiddleware;
use app\model\http\BadQueryStringException;
use app\model\http\FileEntry;
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


    /**
     * @var int
     */
    private $clientId;
    /**
     * @var string
     */
    private $usersUploads;
    /**
     * @var string
     */
    private $userUploads;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function onStartup(IRequest $request, IResponse $response) {
        parent::onStartup($request, $response);

        $jwt = $response->getFlowData(AuthMiddleware::JWT_DATA);
        if (empty($jwt)) {
            $this->logger->error("Nepřihlášený uživatel se snaží získat přístup k souborovému systému!");
            $response->setCode(StatusCodes::UNAUTHORIZED);
            $this->setResponseMessage("Nemáte oprávnění manipulovat se soubory na serveru!", Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
        $this->clientId = +$jwt->id;

        $this->usersUploads = $this->filemanager->getDirectory(FileManager::FOLDER_USER_UPLOADS);
        $this->userUploads = FileManager::mergePath($this->usersUploads, false, $this->clientId);
    }


    public function defaultGETAction(IRequest $request, IResponse $response) {
        $subfolders = $request->getParam();

        try {
            $userSubfolder = FileManager::mergePath($this->userUploads, false, ...$subfolders);
            $this->filemanager->createDirectory($userSubfolder, true);
            $files = $this->filemanager->getFilesFromDirectory($userSubfolder, $this->usersUploads . DIRECTORY_SEPARATOR);
            $response->addData("files", $files);
        } catch (FileManipulationException $ex) {
            $this->logger->error($ex->getMessage());
            $response->setCode(StatusCodes::BAD_REQUEST);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function defaultPOSTAction(IRequest $request, IResponse $response) {
        $subfolders = $request->getParam();
        /**
         * @var FileEntry[]
         */
        $files = $request->getFiles();

        try {
            $userSubfolder = FileManager::mergePath($this->userUploads, false, ...$subfolders);
            $this->filemanager->createDirectory($userSubfolder, true);

            foreach ($files as $file) {
                if ($this->filemanager->existsFile(FileManager::mergePath($userSubfolder, $file->getName()))) {
                    throw new FileManipulationException("Soubor: {$file->getName()} již existuje!");
                }
                $this->filemanager->moveUploadedFiles($file->getTmpName(), $userSubfolder, $file->getName());
            }
            $files = $this->filemanager->getFilesFromDirectory($userSubfolder, $this->usersUploads . DIRECTORY_SEPARATOR);
            $response->addData("files", $files);
            $this->setResponseMessage("Všechny soubory byly úspěšně nahrány.");
        } catch (FileManipulationException $ex) {
            $this->logger->error($ex->getMessage());
            $response->setCode(StatusCodes::BAD_REQUEST);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function defaultPUTAction(IRequest $request, IResponse $response) {
        $subfolders = $request->getParam();
        $existingSubfolders = array_slice($_POST, 1);
        $newFolderName = $subfolders[sizeof($subfolders) - 1];

        try {
            $userSubfolder = FileManager::mergePath($this->userUploads, false, ...$existingSubfolders);
            $userNewSubfolder = FileManager::mergePath($userSubfolder, false, $newFolderName);

            $this->filemanager->createDirectory($userNewSubfolder, true);
            $files = $this->filemanager->getFilesFromDirectory($userSubfolder, $this->usersUploads . DIRECTORY_SEPARATOR);
            $response->addData("files", $files);
            $this->setResponseMessage("Složka byla úspěšně vytvořena.");
        } catch (FileManipulationException $ex) {
            $this->logger->error($ex->getMessage());
            $response->setCode(StatusCodes::BAD_REQUEST);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function defaultDELETEAction(IRequest $request, IResponse $response) {
        $subfolders = $request->getParam();
        $existingSubfolders = array_slice($_POST, 1);
        $folderForDelete = $subfolders[sizeof($subfolders) - 1];

        try {
            $userSubfolder = FileManager::mergePath($this->userUploads, false, ...$existingSubfolders);
            $userFolderFileForDelete = FileManager::mergePath($userSubfolder, false, $folderForDelete);
            $this->filemanager->recursiveDelete($userFolderFileForDelete);
            $files = $this->filemanager->getFilesFromDirectory($userSubfolder, $this->usersUploads . DIRECTORY_SEPARATOR);
            $response->addData("files", $files);
            $this->setResponseMessage("Soubor/složka byla úspěšně smazána.");
        } catch (FileManipulationException $ex) {
            $this->logger->error($ex->getMessage());
            $response->setCode(StatusCodes::BAD_REQUEST);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }


}