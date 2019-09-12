<?php


namespace app\model\manager\lecture_types;


use app\model\database\Database;
use Logger;

/**
 * Class LectureTypesManager
 * @Inject Database
 * @package app\model\manager
 */
class LectureTypesManager {

    const TABLE_NAME = "lecture_type";

    const COLUMN_ID = "id";
    const COLUMN_NAME = "name";
    const COLUMN_DESCRIPTION = "description";
    const COLUMN_PRICE = "price";

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Database
     */
    private $database;

    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function all() {
        return $this->database->queryAll(
            "SELECT id, name, description, price 
                    FROM lecture_type");
    }

    public function byId(int $lectureTypeId) {
        return $this->database->queryOne(
            "SELECT id, name, description, price 
                    FROM lecture_type 
                    WHERE id = ?",
            [$lectureTypeId]);
    }

    public function insert(string $name, string $description, int $price) {
        return $this->database->insert(self::TABLE_NAME,
            [
                LectureTypesManager::COLUMN_NAME => $name,
                LectureTypesManager::COLUMN_DESCRIPTION => $description,
                LectureTypesManager::COLUMN_PRICE => $price
            ]);
    }

    public function update(int $lectureTypeId, string $name, string $description, int $price) {
        return $this->database->update(self::TABLE_NAME,
            [
            LectureTypesManager::COLUMN_NAME => $name,
            LectureTypesManager::COLUMN_DESCRIPTION => $description,
            LectureTypesManager::COLUMN_PRICE => $price
            ],
            " WHERE id = ?",
            [$lectureTypeId]);
    }

    public function delete(int $lectureTypeId) {
        return $this->database->delete(self::TABLE_NAME, "WHERE id = ?", [$lectureTypeId]);
    }
}