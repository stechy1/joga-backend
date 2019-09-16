<?php


namespace app\model\manager\lecture_types;


use app\model\database\Database;
use app\model\manager\lectures\LectureDataException;
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

    /**
     * Najde lekci podle Id
     *
     * @param int $lectureTypeId Id lekce, která se má najít
     * @return array
     * @throws LectureTypeException Pokud se nepodaří najít lekci se zadaným Id
     */
    public function byId(int $lectureTypeId) {
        $fromDb = $this->database->queryOne(
            "SELECT id, name, description, price 
                    FROM lecture_type 
                    WHERE id = ?",
            [$lectureTypeId]);

        if ($fromDb == null) {
            throw new LectureTypeException("Typ lekce s Id ${lectureTypeId} nebyl nalezen!");
        }

        return $fromDb;
    }

    /**
     * Vloží nový typ lekce do databáze
     *
     * @param string $name
     * @param string $description
     * @param int $price
     * @return int Id nově založeného typu lekce
     */
    public function insert(string $name, string $description, int $price) {
        return $this->database->insert(self::TABLE_NAME,
            [
                LectureTypesManager::COLUMN_NAME => $name,
                LectureTypesManager::COLUMN_DESCRIPTION => $description,
                LectureTypesManager::COLUMN_PRICE => $price
            ]);
    }

    /**
     * Aktualizuje údaje o typu lekce
     *
     * @param int $lectureTypeId Id typu lekce
     * @param string $name Název typu lekce
     * @param string $description Popis typu lekce
     * @param int $price Cena lekce
     * @throws LectureTypeDataException Pokud se údaje o lekci nepodaří aktualizovat
     */
    public function update(int $lectureTypeId, string $name, string $description, int $price) {
        $updated = $this->database->update(self::TABLE_NAME,
            [
            LectureTypesManager::COLUMN_NAME => $name,
            LectureTypesManager::COLUMN_DESCRIPTION => $description,
            LectureTypesManager::COLUMN_PRICE => $price
            ],
            " WHERE id = ?",
            [$lectureTypeId]);

        if ($updated == 0) {
            throw new LectureTypeDataException("Údaje o lekci se nepodařilo aktualizovat!");
        }
    }

    /**
     * Smaže vybraný typ lekce
     *
     * @param int $lectureTypeId Id lekce, která se má smazat
     * @throws LectureDataException Pokud se lekci nepodaří smazat
     */
    public function delete(int $lectureTypeId) {
        // TODO lekci pouze označím za smazanou, ale fyzicky v DB zůstane
        $deleted = $this->database->delete(self::TABLE_NAME, "WHERE id = ?", [$lectureTypeId]);

        if ($deleted == 0) {
            throw new LectureDataException("Lekci se nepodařilo smazat!");
        }
    }
}