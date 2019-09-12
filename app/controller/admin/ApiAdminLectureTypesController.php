<?php


namespace app\controller\admin;


use app\model\manager\lecture_types\LectureTypesManager;
use app\model\service\request\IRequest;
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

    public function defaultGETAction(IRequest $request) {
        $lectureTypes = $this->lecturetypesmanager->all();
        $this->addData(self::LECTURE_TYPES, $lectureTypes);
    }

    public function idGETAction(IRequest $request) {
        $lectureId = +$request->getParams()[0];

        try {
            $lectureType = $this->lecturetypesmanager->byId($lectureId);
            $this->addData(self::LECTURE_TYPE, $lectureType);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se uskutečnit dotaz pro získání informací o typu lekce s ID: " . $lectureId . "!", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }
    }

    public function defaultPOSTAction(IRequest $request) {
        $name = $request->get(LectureTypesManager::COLUMN_NAME);
        $description = $request->get(LectureTypesManager::COLUMN_DESCRIPTION);
        $price = $request->get(LectureTypesManager::COLUMN_PRICE);

        try {
            $lectureTypeId = $this->lecturetypesmanager->insert($name, $description, $price);
            $lectureType = $this->lecturetypesmanager->byId($lectureTypeId);

            $this->addData(self::LECTURE_TYPE, $lectureType);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se založit nový typ lekce!", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }
    }

    public function updatePOSTAction(IRequest $request) {
        $lectureTypeId = +$request->get(LectureTypesManager::COLUMN_ID);
        $name = $request->get(LectureTypesManager::COLUMN_NAME);
        $description = $request->get(LectureTypesManager::COLUMN_DESCRIPTION);
        $price = $request->get(LectureTypesManager::COLUMN_PRICE);

        try {
            $this->lecturetypesmanager->update($lectureTypeId, $name, $description, $price);
            $lecture = $this->lecturetypesmanager->byId($lectureTypeId);

            $this->addData(self::LECTURE_TYPE, $lecture);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se upravit typ lekce s ID: " . $lectureTypeId . "!", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }
    }

    public function defaultDELETEAction(IRequest $request) {
        $lectureTypeId = +$request->getParams()[0];

        try {
            $lecture = $this->lecturetypesmanager->byId($lectureTypeId);
            $this->lecturetypesmanager->delete($lectureTypeId);

            $this->addData(self::LECTURE_TYPE, $lecture);
        } catch (Exception $ex) {
            $this->logger->error("Nepodařilo se smazat type lekce s ID: " . $lectureTypeId . "!", $ex);
            $this->setCode(StatusCodes::METHOD_FAILURE);
        }
    }


}