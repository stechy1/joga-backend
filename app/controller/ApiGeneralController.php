<?php


namespace app\controller;


use app\model\http\IRequest;
use app\model\http\IResponse;
use app\model\manager\lecture_types\LectureTypesManager;
use Logger;

/**
 * Class ApiGeneralController
 * Kontroler sloužící k servírování dat na hlavní stránce aplikace
 * @Inject LectureTypesManager
 * @package app\controller
 */
class ApiGeneralController extends BaseApiController {

    const LECTURE_TYPES = "lectureTypes";

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var LectureTypesManager
     */
    private $lecturetypesmanager;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function servicesGETAction(IRequest $request, IResponse $response) {
        $lectureTypes = $this->lecturetypesmanager->all();
        $response->addData(self::LECTURE_TYPES, $lectureTypes);
    }


}