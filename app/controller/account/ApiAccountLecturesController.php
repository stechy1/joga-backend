<?php


namespace app\controller\account;


use app\controller\Constants;
use app\middleware\AuthMiddleware;
use app\model\http\BadQueryStringException;
use app\model\http\IRequest;
use app\model\http\IResponse;
use app\model\manager\lectures\LectureException;
use app\model\manager\lectures\LecturesManager;
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

    public function defaultGETAction(IRequest $request, IResponse $response) {
        $calendarViewType = $request->getParam(0);
        if (!is_string($calendarViewType)) {
            throw new BadQueryStringException("View typ není zadaný, nebo nemá správný formát!");
        }
        if ($calendarViewType !== "month" && $calendarViewType !== "week" && $calendarViewType !== "agenda") {
            throw new BadQueryStringException("Neznámý view typ!");
        }
        $timestamp = $request->getParam(1);
        if (!is_string($timestamp)) {
            throw new BadQueryStringException("Timestamp není zadaný, nebo nemá správný formát!");
        }
        $id = null;
        $jwt = $response->getFlowData(AuthMiddleware::JWT_DATA);
        if (isset($jwt)) {
            $id = +$jwt->id;
        }

        $lectures = $this->lecturesmanager->all(+$timestamp, $calendarViewType, false, $id);

        $response->addData(self::LECTURES, $lectures);
    }

    public function assignPUTAction(IRequest $request, IResponse $response) {
        $jwt = $response->getFlowData(AuthMiddleware::JWT_DATA);
        $clientId = +$jwt->id;
        $lectureId = $request->getParam(0);

        try {
            $this->lecturereservationsmanager->reserve($clientId, $lectureId);

        } catch (LectureException $ex) {
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

        } catch (LectureException $ex) {
            $this->logger->error($ex->getMessage());
            $response->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage(), Constants::RESPONSE_MESSAGE_TYPE_ERROR);
        }
    }
}