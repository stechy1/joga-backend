<?php


namespace app\controller\admin;


use app\model\http\BadQueryStringException;
use app\model\http\IResponse;
use app\model\manager\lecture_types\LectureTypesManager;
use app\model\http\IRequest;
use app\model\util\StatusCodes;
use Exception;
use Logger;

/**
 * Class ApiAdminLectureTypeController
 * @Inject LectureTypesManager
 * @package app\controller\admin
 */
class ApiAdminLectureTypesController extends AdminBaseController {

    const LECTURE_TYPES = "lectureTypes";
    const LECTURE_TYPE = "lectureType";

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var LectureTypesManager
     */
    private $lecturetypesmanager;

    public function defaultGETAction(IRequest $request, IResponse $response) {
        $lectureTypes = $this->lecturetypesmanager->all();
        $response->addData(self::LECTURE_TYPES, $lectureTypes);
    }

    public function idGETAction(IRequest $request, IResponse $response) {
        $lectureTypeId = $request->getParam(0);
        if (!is_numeric($lectureTypeId)) {
            throw new BadQueryStringException("ID typu lekce není zadané, nebo nemá správný formát!");
        }

        try {
            $lectureType = $this->lecturetypesmanager->byId(+$lectureTypeId);
            $response->addData(self::LECTURE_TYPE, $lectureType);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            $response->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage());
        }
    }

    public function defaultPOSTAction(IRequest $request, IResponse $response) {
        $name = $request->get(LectureTypesManager::COLUMN_NAME);
        $description = $request->get(LectureTypesManager::COLUMN_DESCRIPTION);
        $price = $request->get(LectureTypesManager::COLUMN_PRICE);

        try {
            $lectureTypeId = $this->lecturetypesmanager->insert($name, $description, $price);
            $lectureType = $this->lecturetypesmanager->byId($lectureTypeId);

            $response->addData(self::LECTURE_TYPE, $lectureType);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se založit nový typ lekce!", $ex);
            $response->setCode(StatusCodes::NOT_FOUND);
            $this->setResponseMessage($ex->getMessage());
        }
    }

    public function updatePOSTAction(IRequest $request, IResponse $response) {
        $lectureTypeId = +$request->get(LectureTypesManager::COLUMN_ID);
        $name = $request->get(LectureTypesManager::COLUMN_NAME);
        $description = $request->get(LectureTypesManager::COLUMN_DESCRIPTION);
        $price = $request->get(LectureTypesManager::COLUMN_PRICE);

        try {
            $this->lecturetypesmanager->update($lectureTypeId, $name, $description, $price);
            $lecture = $this->lecturetypesmanager->byId($lectureTypeId);

            $response->addData(self::LECTURE_TYPE, $lecture);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se upravit typ lekce s ID: " . $lectureTypeId . "!", $ex);
            $response->setCode(StatusCodes::NOT_FOUND);
        }
    }

    public function defaultDELETEAction(IRequest $request, IResponse $response) {
        $lectureTypeId = $request->getParam(0);
        if (!is_numeric($lectureTypeId)) {
            throw new BadQueryStringException("ID typu lekce není zadané, nebo nemá správný formát!");
        }

        try {
            $lecture = $this->lecturetypesmanager->byId(+$lectureTypeId);
            $this->lecturetypesmanager->delete(+$lectureTypeId);

            $response->addData(self::LECTURE_TYPE, $lecture);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se smazat type lekce s ID: " . $lectureTypeId . "!", $ex);
            $response->setCode(StatusCodes::NOT_FOUND);
        }
    }


}