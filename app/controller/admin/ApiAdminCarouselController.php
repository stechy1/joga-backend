<?php


namespace app\controller\admin;


use app\controller\Constants;
use app\model\http\BadQueryStringException;
use app\model\http\IResponse;
use app\model\manager\carousel\CarouselManager;
use app\model\manager\carousel\ImageNotFoundException;
use app\model\manager\carousel\ImageProcessException;
use app\model\manager\carousel\ImageUploadException;
use app\model\manager\file\FileManipulationException;
use app\model\http\IRequest;
use app\model\util\StatusCodes;
use Logger;

/**
 * Class ApiAdminCarouselController
 * @Inject CarouselManager
 * @package app\controller\admin
 */
class ApiAdminCarouselController extends AdminBaseController {

    const KEY_IMAGES = "images";
    const KEY_IMAGE = "image";

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

    public function defaultGETAction(IRequest $request, IResponse $response) {
        $images = $this->carouselmanager->all();
        $response->addData(self::KEY_IMAGES, $images);
    }

    public function imageGETAction(IRequest $request, IResponse $response) {
        $id = $request->getParam(0);
        if (!is_numeric($id)) {
            throw new BadQueryStringException("ID obrázku není zadané, nebo nemá správný formát!");
        }

        try {
            $image = $this->carouselmanager->byId(+$id);
            $response->addData(self::KEY_IMAGE, $image);
        }
        catch (ImageNotFoundException $ex) {
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
            $response->setCode(StatusCodes::NOT_FOUND);
        }
    }

    public function defaultPOSTAction(IRequest $request, IResponse $response) {
        try {
            $image = $this->carouselmanager->addImage(
                $request->get(CarouselManager::COLUMN_IMAGE_NAME),
                $request->get(CarouselManager::COLUMN_IMAGE_DESCRIPTION, ''),
                $request->getFile(CarouselManager::COLUMN_IMAGE)
            );
            $response->addData(CarouselManager::COLUMN_IMAGE, $image);
            $response->setCode(StatusCodes::CREATED);
        } catch (ImageUploadException $ex) {
            $response->setCode(StatusCodes::CONFLICT);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        } catch (FileManipulationException $ex) {
            $response->setCode(StatusCodes::NOT_ACCEPTABLE);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function updatePOSTAction(IRequest $request, IResponse $response) {
        try {
            $this->carouselmanager->updateImage(
                +$request->get(CarouselManager::COLUMN_IMAGE_ID),
                $request->get(CarouselManager::COLUMN_IMAGE_NAME),
                $request->get(CarouselManager::COLUMN_IMAGE_DESCRIPTION),
                +$request->get(CarouselManager::COLUMN_ENABLED),
                +$request->get(CarouselManager::COLUMN_VIEW_ORDER)
            );
            $image = $this->carouselmanager->byId(+$request->get(CarouselManager::COLUMN_IMAGE_ID));
            $response->addData(self::KEY_IMAGE, $image);
        } catch (ImageProcessException $ex) {
            $response->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        } catch (ImageNotFoundException $ex) {
        }
    }

    public function defaultDELETEAction(IRequest $request, IResponse $response) {
        $id = $request->getParam(0);
        if (!is_numeric($id)) {
            throw new BadQueryStringException("ID obrázku není zadané, nebo nemá správný formát!");
        }

        try {
            $image = $this->carouselmanager->byId(+$request->get(CarouselManager::COLUMN_IMAGE_ID));
            $this->carouselmanager->deleteImage(+$id);
            $response->addData(self::KEY_IMAGE, $image);
        } catch (ImageNotFoundException | ImageProcessException $ex) {
            $this->logger->error($ex->getMessage());
            $response->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

}