<?php


namespace app\controller;


use app\model\http\BadQueryStringException;
use app\model\http\IRequest;
use app\model\http\IResponse;
use app\model\manager\carousel\CarouselManager;
use app\model\manager\email\EmailException;
use app\model\manager\email\EmailManager;
use app\model\manager\lecture_types\LectureTypeException;
use app\model\manager\lecture_types\LectureTypesManager;
use app\model\manager\lectures\LectureException;
use app\model\manager\lectures\LecturesManager;
use app\model\util\StatusCodes;
use Exception;
use Logger;

/**
 * Class ApiGeneralController
 * Kontroler sloužící k servírování dat na hlavní stránce aplikace
 * @Inject LectureTypesManager
 * @Inject LecturesManager
 * @Inject CarouselManager
 * @Inject EmailManager
 * @package app\controller
 */
class ApiGeneralController extends BaseApiController {

    const LECTURE_TYPES = "lectureTypes";
    const LECTURE_TYPE = "lecture_type";
    const CAROUSEL = "carousel";

    const EMAIL_CONTENT = "message";
    const EMAIL_NAME = "name";
    const EMAIL_FROM_EMAIL = "email";
    const EMAIL_RECAPTCHA = "recaptcha";

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
    /**
     * @var EmailManager
     */
    private $emailmanager;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function servicesGETAction(IRequest $request, IResponse $response) {
        $lectureTypes = $this->lecturetypesmanager->all();
        $response->addData(self::LECTURE_TYPES, $lectureTypes);
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

    public function emailPOSTAction(IRequest $request, IResponse $response) {
        $message = $request->get(self::EMAIL_CONTENT);
        $name = $request->get(self::EMAIL_NAME);
        $emailFrom = $request->get(self::EMAIL_FROM_EMAIL);
        $recaptcha = $response->get(self::EMAIL_RECAPTCHA);

        

        try {
            $this->emailmanager->sendEmailFromContactForm($message, $name, $emailFrom);
        } catch (Exception $ex) {
            $response->setCode(StatusCodes::BAD_REQUEST);
            $this->setResponseMessage("E-mail se nepodařilo odeslat!", Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }
}