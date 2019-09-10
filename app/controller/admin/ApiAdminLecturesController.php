<?php


namespace app\controller\admin;


use app\model\manager\lectures\LecturesManager;
use app\model\manager\user\UserManager;
use app\model\service\request\IRequest;
use app\model\util\StatusCodes;
use DateTime;
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
    const LECTURE_TYPES = "lectureTypes";
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

    public function defaultGETAction(IRequest $request) {
        $date = +$request->getParams()[0];
        $lectures = $this->lecturesmanager->all($date);
        $this->addData('lectures', $lectures);
    }

    public function trainersGETAction(IRequest $request) {
        try {
            $trainers = $this->usermanager->trainers();
            $this->addData(self::TRAINERS, $trainers);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se uskutečnit dotaz pro získání trenérů.", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }
    }

    public function lecture_TypesGETAction(IRequest $request) {
        try {
            $types = $this->lecturesmanager->lectureTypes();
            $this->addData(self::LECTURE_TYPES, $types);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se uskutečnit dotaz pro získání typů lekcí!", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }
    }

    public function idGETAction(IRequest $request) {
        $lectureId = +$request->getParams()[0];

        try {
            $lecture = $this->lecturesmanager->byId($lectureId);
            $this->addData(self::LECTURE, $lecture);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se uskutečnit dotaz pro získání informací o lekci s ID: " . $lectureId . "!", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }
    }

    public function date_time_validityGETAction(IRequest $request) {
        $valid = false;
        $dateTime = +$request->getParams()[0];

        try {
            $valid = $this->lecturesmanager->checkDateTimeValidity($dateTime);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se zvalidovat datum", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }
        $this->addData(self::VALID, $valid);
    }

    public function duration_validityGETAction(IRequest $request) {
        $valid = false;
        $dateTime = +$request->getParams()[0];
        $duration = +$request->getParams()[1];

        try {
            $valid = $this->lecturesmanager->checkDurationValidity($dateTime, $duration);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se zvalidovat datum", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }

        $this->addData(self::VALID, $valid);
    }

    public function defaultPOSTAction(IRequest $request) {
        $trainer = +$request->get(LecturesManager::COLUMN_TRAINER);
        $timeStart = +$request->get(LecturesManager::COLUMN_TIME_START);
        $timeEnd = +$request->get(LecturesManager::COLUMN_TIME_END);
        $maxPersons = +$request->get(LecturesManager::COLUMN_MAX_PERSONS);
        $place = $request->get(LecturesManager::COLUMN_PLACE);
        $lectureType = +$request->get(LecturesManager::COLUMN_TYPE);

        try {
            $lectureId = $this->lecturesmanager->insert($trainer, $timeStart, $timeEnd, $maxPersons, $place, $lectureType);
            $lecture = $this->lecturesmanager->byId($lectureId);

            $this->addData(self::LECTURE, $lecture);
        } catch (Exception $ex) {
            var_dump($ex);
            $this->logger->error("Nepodařilo se založit novou lekci!", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }
    }

    public function updatePOSTAction(IRequest $request) {
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

            $this->addData(self::LECTURE, $lecture);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se upravit lekci s ID: " . $lectureId . "!", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }
    }

    public function defaultDELETEAction(IRequest $request) {
        $lectureId = +$request->getParams()[0];

        try {
            $lecture = $this->lecturesmanager->byId($lectureId);
            $this->lecturesmanager->delete($lectureId);
            $this->addData(self::LECTURE, $lecture);
        } catch (Exception $ex) {
            var_dump($ex);
            $this->logger->error("Nepodařilo se smazat lekci s ID: " . $lectureId . "!", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }
    }


}