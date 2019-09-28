<?php


namespace app\controller\admin;


use app\controller\Constants;
use app\model\http\BadQueryStringException;
use app\model\http\IResponse;
use app\model\manager\lectures\LecturesManager;
use app\model\manager\user\UserManager;
use app\model\http\IRequest;
use app\model\util\StatusCodes;
use Exception;
use Logger;

/**
 * Class ApiAdminLecturesController
 * @Inject LecturesManager
 * @Inject UserManager
 * @package app\controller\admin
 */
class ApiAdminLecturesController extends AdminBaseController {

    const TRAINERS = "trainers";
    const LECTURE = "lecture";
    const VALID = "valid";

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var LecturesManager
     */
    private $lecturesmanager;
    /**
     * @var UserManager
     */
    private $usermanager;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function defaultGETAction(IRequest $request, IResponse $response) {
        $lectureId = $request->getParam(0);
        if (!is_numeric($lectureId)) {
            throw new BadQueryStringException("ID lekce není zadané, nebo nemá správný formát!");
        }

        $lectures = $this->lecturesmanager->all($lectureId, true);
        $response->addData('lectures', $lectures);
    }

    public function trainersGETAction(IRequest $request, IResponse $response) {
        $trainers = $this->usermanager->trainers();
        $response->addData(self::TRAINERS, $trainers);
    }

    public function idGETAction(IRequest $request, IResponse $response) {
        $lectureId = $request->getParam(0);
        if (!is_numeric($lectureId)) {
            throw new BadQueryStringException("ID lekce není zadané, nebo nemá správný formát!");
        }

        try {
            $lecture = $this->lecturesmanager->byId(+$lectureId);
            $response->addData(self::LECTURE, $lecture);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            $response->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function time_validityGETAction(IRequest $request, IResponse $response) {
        $valid = false;
        $when = +$request->getParam(0);
        $dateTime = +$request->getParam(1);
        $lectureId = $request->hasParams(3) ? +$request->getParam(2) : -1; // isset($request->getParams()[2]) ? +$request->getParams()[2] : -1;

        try {
            switch ($when) {
                case LecturesManager::COLUMN_TIME_START: {
                    $valid = $this->lecturesmanager->checkTimeStartValidity($lectureId, $dateTime);
                    break;
                }
                case LecturesManager::COLUMN_TIME_END: {
                    $valid = $this->lecturesmanager->checkTimeEndValidity($lectureId, $dateTime);
                    break;
                }
                default: {
                    $this->logger->error("Nebylo rozpoznáno, co se má validovat!");
                    $response->setCode(StatusCodes::BAD_REQUEST);
                    return;
                }
            }
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se zvalidovat datum", $ex);
            $response->setCode(StatusCodes::NOT_FOUND);
        }
        $response->addData(self::VALID, $valid);
    }

    public function defaultPOSTAction(IRequest $request, IResponse $response) {
        $trainer = +$request->get(LecturesManager::COLUMN_TRAINER);
        $timeStart = +$request->get(LecturesManager::COLUMN_TIME_START);
        $timeEnd = +$request->get(LecturesManager::COLUMN_TIME_END);
        $maxPersons = +$request->get(LecturesManager::COLUMN_MAX_PERSONS);
        $place = $request->get(LecturesManager::COLUMN_PLACE);
        $lectureType = +$request->get(LecturesManager::COLUMN_TYPE);

        try {
            $lectureId = $this->lecturesmanager->insert($trainer, $timeStart, $timeEnd, $maxPersons, $place, $lectureType);
            $lecture = $this->lecturesmanager->byId($lectureId);

            $response->addData(self::LECTURE, $lecture);
            $response->setCode(StatusCodes::CREATED);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            $this->logger->debug($ex->getTraceAsString());
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
            $response->setCode(StatusCodes::NOT_FOUND);
        }
    }

    public function updatePOSTAction(IRequest $request, IResponse $response) {
        $lectureId = +$request->get(LecturesManager::COLUMN_ID);
        $trainer = +$request->get(LecturesManager::COLUMN_TRAINER);
        $timeStart = +$request->get(LecturesManager::COLUMN_TIME_START);
        $timeEnd = +$request->get(LecturesManager::COLUMN_TIME_END);
        $maxPersons = +$request->get(LecturesManager::COLUMN_MAX_PERSONS);
        $place = $request->get(LecturesManager::COLUMN_PLACE);
        $lectureType = +$request->get(LecturesManager::COLUMN_TYPE);

        try {
            $this->lecturesmanager->update($lectureId, $trainer, $timeStart, $timeEnd, $maxPersons, $place, $lectureType);
            $lecture = $this->lecturesmanager->byId($lectureId);

            $response->addData(self::LECTURE, $lecture);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se upravit lekci s ID: " . $lectureId . "!", $ex);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
            $response->setCode(StatusCodes::NOT_FOUND);
        }
    }

    public function defaultDELETEAction(IRequest $request, IResponse $response) {
        $lectureId = +$request->getParam(0);

        try {
            $lecture = $this->lecturesmanager->byId($lectureId);
            $this->lecturesmanager->delete($lectureId);
            $response->addData(self::LECTURE, $lecture);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se smazat lekci s ID: " . $lectureId . "!", $ex);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
            $response->setCode(StatusCodes::NOT_FOUND);
        }
    }


}