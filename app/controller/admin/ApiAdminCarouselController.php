<?php


namespace app\controller\admin;


use app\model\manager\carousel\CarouselManager;
use app\model\manager\carousel\ImageNotFoundException;
use app\model\manager\carousel\ImageProcessException;
use app\model\manager\carousel\ImageUploadException;
use app\model\manager\file\FileManipulationException;
use app\model\service\request\IRequest;
use app\model\util\StatusCodes;
use Logger;

/**
 * Class ApiAdminCarouselController
 * @Inject CarouselManager
 * @package app\controller\admin
 */
class ApiAdminCarouselController extends AdminBaseController {

    const KEY_GET_ALL_IMAGES = "images";

    const KEY_POST_UPLOAD_NAME = "name";
    const KEY_POST_UPLOAD_DESCRIPTION = "description";
    const KEY_POST_UPLOAD_IMAGE = "image";

    const KEY_ERROR = "error";

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var CarouselManager
     */
    private $carouselmanager;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function defaultGETAction(IRequest $request) {
        $images = $this->carouselmanager->all();
        $this->addData(self::KEY_GET_ALL_IMAGES, $images);
    }

    public function defaultPOSTAction(IRequest $request) {
        try {
            $image = $this->carouselmanager->addImage(
                $request->get(self::KEY_POST_UPLOAD_NAME),
                $request->get(self::KEY_POST_UPLOAD_DESCRIPTION, ''),
                $request->getFile(self::KEY_POST_UPLOAD_IMAGE)
            );
            $this->addData(self::KEY_POST_UPLOAD_IMAGE, $image);
            $this->setCode(StatusCodes::CREATED);
        } catch (ImageUploadException $ex) {
            $this->addData(self::KEY_ERROR, $ex->getMessage());
            $this->setCode(StatusCodes::CONFLICT);
        } catch (FileManipulationException $ex) {
            $this->addData(self::KEY_ERROR, $ex->getMessage());
            $this->setCode(StatusCodes::NOT_ACCEPTABLE);
        }
    }

    public function defaultDELETEAction(IRequest $request) {
        $id = $request->getParams()[0];
        try {
            $this->carouselmanager->deleteImage($id);
            $this->setCode(StatusCodes::NO_CONTENT);
        } catch (ImageNotFoundException $e) {
            $this->setCode(StatusCodes::NOT_FOUND);
        } catch (ImageProcessException $ex) {
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }
    }

}