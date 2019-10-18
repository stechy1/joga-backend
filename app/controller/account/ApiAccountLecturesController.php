<?php


namespace app\controller\account;


use app\controller\Constants;
use app\middleware\AuthMiddleware;
use app\model\http\IRequest;
use app\model\http\IResponse;
use app\model\manager\lectures\LectureException;
use app\model\manager\lectures\LecturesManager;
use app\model\manager\reservations\LectureReservationException;
use app\model\manager\reservations\LectureReservationsManager;
use app\model\util\StatusCodes;
use Logger;

/**
 * Class ApiAccountLecturesController
 * @Inject LecturesManager
 * @Inject LectureReservationsManager
 * @package app\controller\account
 */
class ApiAccountLecturesController extends BaseAccountController {

    const LECTURES = "lectures";

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var LecturesManager
     */
    private $lecturesmanager;
    /**
     * @var LectureReservationsManager
     */
    private $lecturereservationsmanager;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function my_lecturesGETAction(IRequest $request, IResponse $response) {
        $jwt = $response->getFlowData(AuthMiddleware::JWT_DATA);
        $clientId = +$jwt->id;

        $lectures = $this->lecturesmanager->clientLectures($clientId);
        $response->addData(self::LECTURES, $lectures);
    }

    public function assignPUTAction(IRequest $request, IResponse $response) {
        $jwt = $response->getFlowData(AuthMiddleware::JWT_DATA);
        $clientId = +$jwt->id;
        $lectureId = $request->getParam(0);

        try {
            $this->lecturereservationsmanager->reserve($clientId, $lectureId);
            $this->setResponseMessage("Přihlášení na lekci proběhlo v pořádku.");
        } catch (LectureException|LectureReservationException $ex) {
            $this->logger->error($ex->getMessage());
            $response->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }

    public function cancelDELETEAction(IRequest $request, IResponse $response) {
        $jwt = $response->getFlowData(AuthMiddleware::JWT_DATA);
        $clientId = +$jwt->id;
        $lectureId = $request->getParam(0);

        try {
            $this->lecturereservationsmanager->cancel($clientId, $lectureId);
            $this->setResponseMessage("Odhlášení z lekce proběhlo v pořádku.");
        } catch (LectureReservationException $ex) {
            $this->logger->error($ex->getMessage());
            $response->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }
}