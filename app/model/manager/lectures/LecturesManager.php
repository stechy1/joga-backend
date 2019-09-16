<?php


namespace app\model\manager\lectures;


use app\model\database\Database;
use DateInterval;
use DateTime;
use Exception;
use Logger;

/**
 * Class LecturesManager
 * @Inject Database
 * @package app\model\manager
 */
class LecturesManager {

    const TABLE_NAME = "lectures";

    const COLUMN_ID = "id";
    const COLUMN_TRAINER = "trainer";
    const COLUMN_TYPE = "type";
    const COLUMN_TIME_START = "time_start";
    const COLUMN_TIME_END = "time_end";
    const COLUMN_MAX_PERSONS = "max_persons";
    const COLUMN_PLACE = "place";
    const COLUMN_PUBLISHED = "published";

    const VIRTUAL_COLUMN_LECTURE_NAME = "lecture_name";
    const VIRTUAL_COLUMN_RESERVED_CLIENTS = "reserved_clients";

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

    public function all(int $monthTimestamp) {
        $this->logger->trace($monthTimestamp);

        $firstDay = new DateTime();
        $firstDay->setTimestamp($monthTimestamp);
        $firstDay->setTime(0, 0, 0);
        $firstDay->modify("first day of this month");
        $this->logger->trace($firstDay);

        $lastDay = new DateTime();
        $lastDay->setTimestamp($monthTimestamp);
        $lastDay->setTime(23, 23, 59);
        $lastDay->modify("last day of this month");
        $this->logger->trace($lastDay);

        return $this->database->queryAll(
            "SELECT lectures.id AS lecture_id, time_start, time_end, max_persons, place, published, type,
                           trainers.id AS trainer_id, trainers.name AS trainer_name,
                           lecture_type.name AS lecture_name,
                           COUNT(clients.client) AS reserved_clients
                    FROM lectures
                             LEFT JOIN users trainers ON trainers.id = lectures.trainer
                             LEFT JOIN lecture_type ON lecture_type.id = lectures.type
                             LEFT JOIN lecture_reservations clients ON clients.lecture = lectures.id
                    WHERE time_start BETWEEN ? AND ?
                    GROUP BY lectures.id, time_start, time_end, max_persons, place, published, trainers.id, trainers.name, lecture_type.name",
            [$firstDay->getTimestamp(), $lastDay->getTimestamp()]);
    }

    public function insert(int $trainer, int $timeStart, int $timeEnd, int $maxPersons, string $place, int $type) {
        return $this->database->insert(self::TABLE_NAME, [
            LecturesManager::COLUMN_TRAINER => $trainer,
            LecturesManager::COLUMN_TIME_START => $timeStart,
            LecturesManager::COLUMN_TIME_END => $timeEnd,
            LecturesManager::COLUMN_MAX_PERSONS => $maxPersons,
            LecturesManager::COLUMN_PLACE => $place,
            LecturesManager::COLUMN_TYPE => $type
        ]);
    }

    public function lectureNameByType(int $lectureType) {
        return $this->database->queryOne("SELECT name FROM lecture_type WHERE id = ?", [$lectureType]);
    }

    public function lectureTypes() {
        return $this->database->queryAll("SELECT id, name FROM lecture_type");
    }

    /**
     * @param int $lectureId
     * @throws LectureException Pokud lekce nebyla nalezena
     */
    public function byId(int $lectureId) {
        $fromDb = $this->database->queryOne(
            "SELECT lectures.id AS lecture_id, time_start, time_end, max_persons, place, published, type,
                           trainers.id AS trainer_id, trainers.name AS trainer_name,
                           lecture_type.name AS lecture_name,
                           COUNT(clients.client) AS reserved_clients
                    FROM lectures
                             LEFT JOIN users trainers ON trainers.id = lectures.trainer
                             LEFT JOIN lecture_type ON lecture_type.id = lectures.type
                             LEFT JOIN lecture_reservations clients ON clients.lecture = lectures.id
                    WHERE lectures.id = ?
                    GROUP BY lectures.id, time_start, time_end, max_persons, place, published, type, trainers.id, trainers.name, lecture_type.name",
            [$lectureId]);

        if ($fromDb == null) {
            throw new LectureException("Lekce s Id: ${$lectureId} nebyla nalezena!");
        }
    }

    /**
     * Aktualizuje lekci
     *
     * @param int $lectureId Id lekce
     * @param int $trainer Id trenéra, který lekci vede
     * @param int $timeStart Časová značka představující začátek lekce
     * @param int $timeEnd Časová značka představující začátek lekce
     * @param int $maxPersons Maximální počet osob, který se může lekce zúčastnit
     * @param string $place Místo, kde se lekce koná
     * @param int $lectureType Id typu lekce
     * @throws LectureDataException Pokud se data nepodaří aktualizovat
     */
    public function update(int $lectureId, int $trainer, int $timeStart, int $timeEnd, int $maxPersons, string $place, int $lectureType) {
        $fromDb =  $this->database->update(self::TABLE_NAME,
            [
                LecturesManager::COLUMN_TRAINER => $trainer,
                LecturesManager::COLUMN_TIME_START => $timeStart,
                LecturesManager::COLUMN_TIME_END => $timeEnd,
                LecturesManager::COLUMN_MAX_PERSONS => $maxPersons,
                LecturesManager::COLUMN_PLACE => $place,
                LecturesManager::COLUMN_TYPE => $lectureType
            ], " WHERE id = ?",
            [$lectureId]);

        if ($fromDb == 0) {
            throw new LectureDataException("Lekci se nepodařilo aktualizovat!");
        }
    }

    /**
     * Smaže lekci podle Id
     *
     * @param int $lectureId Id lekce, která se má smazat
     * @throws LectureDataException Pokud se lekci nepodaří smazat
     */
    public function delete(int $lectureId) {
        $this->logger->info("Mažu lekci s id: " . $lectureId);
        $count = $this->database->delete(self::TABLE_NAME, "WHERE id = ?", [$lectureId]);

        if ($count == 0) {
            throw new LectureDataException("Lekci s Id ${lectureId} se nepodařilo smazat!");
        }
    }

    private function checkTimeValidity(int $lectureId, int $timestamp, string $when) {
        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);

        $result = $this->database->queryAll(
            "SELECT id, time_start, time_end FROM lectures WHERE id != ? AND time_start <= ? AND time_end >= ?",
            [$lectureId, $dateTime->getTimestamp(), $dateTime->getTimestamp()]);

        return sizeof($result) == 0;
    }

    public function checkTimeStartValidity(int $lectureId, int $timestamp) {
        return $this->checkTimeValidity($lectureId, $timestamp, LecturesManager::COLUMN_TIME_START);
    }

    public function checkTimeEndValidity(int $lectureId, int $timestamp) {
        return $this->checkTimeValidity($lectureId, $timestamp, LecturesManager::COLUMN_TIME_END);
    }
}