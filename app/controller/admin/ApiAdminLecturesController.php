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

    public function defaultPOSTAction(IRequest $request) {
        $trainer = +$request->get(LecturesManager::COLUMN_TRAINER);
        $startTime = +$request->get(LecturesManager::COLUMN_START_TIME);
        $duration = +$request->get(LecturesManager::COLUMN_DURATION);
        $maxPersons = +$request->get(LecturesManager::COLUMN_MAX_PERSONS);
        $place = $request->get(LecturesManager::COLUMN_PLACE);

        $this->lecturesmanager->insert($trainer, $startTime, $duration, $maxPersons, $place);
    }

}