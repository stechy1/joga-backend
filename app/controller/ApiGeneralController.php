<?php


namespace app\controller;


use app\model\http\BadQueryStringException;
use app\model\http\IRequest;
use app\model\http\IResponse;
use app\model\manager\carousel\CarouselManager;
use app\model\manager\lecture_types\LectureTypeException;
use app\model\manager\lecture_types\LectureTypesManager;
use app\model\manager\lectures\LectureException;
use app\model\manager\lectures\LecturesManager;
use app\model\util\StatusCodes;
use Logger;

/**
 * Class ApiGeneralController
 * Kontroler sloužící k servírování dat na hlavní stránce aplikace
 * @Inject LectureTypesManager
 * @Inject LecturesManager
 * @Inject CarouselManager
 * @package app\controller
 */
class ApiGeneralController extends BaseApiController {

    const LECTURE_TYPES = "lectureTypes";
    const LECTURES = "lectures";
    const LECTURE_TYPE = "lecture_type";
    const CAROUSEL = "carousel";

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var LectureTypesManager
     */
    private $lecturetypesmanager;
    /**
     * @var LecturesManager
     */
    private $lecturesmanager;
    /**
     * @var CarouselManager
     */
    private $carouselmanager;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function servicesGETAction(IRequest $request, IResponse $response) {
        $lectureTypes = $this->lecturetypesmanager->all();
        $response->addData(self::LECTURE_TYPES, $lectureTypes);
    }

    public function lecturesGETAction(IRequest $request, IResponse $response) {
        $timestamp = $request->getParam(0);
        if (!is_string($timestamp)) {
            throw new BadQueryStringException("Timestamp není zadaný, nebo nemá správný formát!");
        }
        $calendarViewType = $request->getParam(1);
        if (!is_string($calendarViewType)) {
            throw new BadQueryStringException("View typ není zadaný, nebo nemá správný formát!");
        }
        if ($calendarViewType !== "month" && $calendarViewType !== "week" && $calendarViewType !== "agenda") {
            throw new BadQueryStringException("Neznámý view typ!");
        }

        $lectures = $this->lecturesmanager->all(+$timestamp, $calendarViewType);
        $response->addData(self::LECTURES, $lectures);
    }

    public function lecture_typeGETAction(IRequest $request, IResponse $response) {
        $lectureTypeId = $request->getParam(0);
        if (!is_numeric($lectureTypeId)) {
            throw new BadQueryStringException("Typ informace není zadaný, nebo nemá správný formát!");
        }

        try {
            $lectureType = $this->lecturetypesmanager->byId(+$lectureTypeId);
            $response->addData(self::LECTURE_TYPE, $lectureType);
        } catch (LectureTypeException $ex) {
            $this->setResponseMessage($ex->getMessage());
            $response->setCode(StatusCodes::NOT_FOUND);
        }
    }

    public function carouselGETAction(IRequest $request, IResponse $response) {
        $images = $this->carouselmanager->all(true);
        $response->addData(self::CAROUSEL, $images);
    }

}