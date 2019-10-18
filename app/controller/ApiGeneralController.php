<?php


namespace app\controller;


use app\middleware\AuthMiddleware;
use app\model\http\BadQueryStringException;
use app\model\http\IRequest;
use app\model\http\IResponse;
use app\model\manager\carousel\CarouselManager;
use app\model\manager\email\EmailManager;
use app\model\manager\lecture_types\LectureTypeException;
use app\model\manager\lecture_types\LectureTypesManager;
use app\model\manager\lectures\LecturesManager;
use app\model\util\StatusCodes;
use Exception;
use Logger;
use ReCaptcha\ReCaptcha;

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

    const LECTURES = "lectures";
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

    public function defaultGETAction(IRequest $request, IResponse $response) {
        $calendarViewType = $request->getParam(0);
        if (!is_string($calendarViewType)) {
            throw new BadQueryStringException("View typ není zadaný, nebo nemá správný formát!");
        }
        if ($calendarViewType !== "month" && $calendarViewType !== "week" && $calendarViewType !== "agenda") {
            throw new BadQueryStringException("Neznámý view typ [" . $calendarViewType . "]!");
        }
        $timestamp = $request->getParam(1);
        if (!is_string($timestamp)) {
            throw new BadQueryStringException("Timestamp není zadaný, nebo nemá správný formát!");
        }
        $id = -1;
        $jwt = $response->getFlowData(AuthMiddleware::JWT_DATA);
        $this->logger->info($jwt);
        if (isset($jwt)) {
            $id = +$jwt->id;
        }

        $lectures = $this->lecturesmanager->all(+$timestamp, $calendarViewType, false, $id);

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

    public function emailPOSTAction(IRequest $request, IResponse $response) {
        $message = $request->get(self::EMAIL_CONTENT);
        $name = $request->get(self::EMAIL_NAME);
        $emailFrom = $request->get(self::EMAIL_FROM_EMAIL);
        $recaptchaToken = $response->get(self::EMAIL_RECAPTCHA);

        $this->logger->trace("Kontroluji captchu...");
        $recaptcha = new ReCaptcha(RECAPTCHA);
        $recaptchaResponse = $recaptcha->verify($recaptchaToken);
        if (!$recaptchaResponse->isSuccess()) {
            $this->setResponseMessage("Google captcha se nepodařilo ověřit!");
            $response->setCode(StatusCodes::BAD_REQUEST);
            return;
        }
        $this->logger->trace("Captcha byla úspěšně ověřena, jdu odeslat e-mail.");
        

        try {
            $this->emailmanager->sendEmailFromContactForm($message, $name, $emailFrom);
        } catch (Exception $ex) {
            $response->setCode(StatusCodes::BAD_REQUEST);
            $this->setResponseMessage("E-mail se nepodařilo odeslat!", Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }
}