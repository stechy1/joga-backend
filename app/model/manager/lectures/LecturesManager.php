<?php


namespace app\model\manager\lectures;


use app\model\database\Database;
use app\model\http\BadQueryStringException;
use DateInterval;
use DateTime;
use Exception;
use InvalidArgumentException;
use Logger;
use PDOException;

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

    public function all(int $timestamp, string $calendarViewType, bool $showHidden = false, int $clientId = -1) {
        $this->logger->trace($timestamp);
        $this->logger->trace("Getting all lectures for: {$calendarViewType}");

        $startTime = new DateTime();
        $startTime->setTimestamp($timestamp);
        $startTime->setTime(0, 0, 0);

        $endTime = new DateTime();
        $endTime->setTimestamp($timestamp);
        $endTime->setTime(23, 23, 59);

        switch ($calendarViewType) {
            case "month":
                $startTime->modify("first day of this month");
                $endTime->modify("last day of this month");
                break;
            case "week":
                $startTime->modify("first day of this week");
                $endTime->modify("last day of this week");
                break;
            case "agenda":

                break;
            default:
                throw new InvalidArgumentException("Neznámý view typ!");
        }

        return $this->database->queryAll(
            "SELECT lectures.id AS lecture_id, time_start, time_end, max_persons, place, published, type,
                           trainers.id AS trainer_id, trainers.name AS trainer_name,
                           lecture_type.name AS lecture_name,
                           COUNT(clients.client) AS reserved_clients,
                           COUNT(reserved.client) AS assigned
                    FROM lectures
                             LEFT JOIN users trainers ON trainers.id = lectures.trainer
                             LEFT JOIN lecture_type ON lecture_type.id = lectures.type
                             LEFT JOIN lecture_reservations clients ON clients.lecture = lectures.id
                             LEFT JOIN lecture_reservations reserved ON clients.client = ?
                    WHERE time_start BETWEEN ? AND ? 
                    " . (!$showHidden ? "AND published = 1" : "") . "
                    GROUP BY lectures.id, time_start, time_end, max_persons, place, published, trainers.id, trainers.name, lecture_type.name",
            [$clientId, $startTime->getTimestamp(), $endTime->getTimestamp()]);
    }

    public function clientLectures(int $clientId) {
        return $this->database->queryAll(
            "SELECT lectures.id AS lecture_id, time_start, time_end, max_persons, place, published, type,
                           trainers.id AS trainer_id, trainers.name AS trainer_name,
                           lecture_type.name AS lecture_name,
                           COUNT(clients.client) AS reserved_clients,
                           COUNT(reserved.client) AS assigned
                    FROM lecture_reservations
                             LEFT JOIN lectures ON lectures.id = lecture
                             LEFT JOIN users trainers ON trainers.id = lectures.trainer
                             LEFT JOIN lecture_type ON lecture_type.id = lectures.type
                             LEFT JOIN lecture_reservations clients ON clients.lecture = lectures.id
                             LEFT JOIN lecture_reservations reserved ON clients.client = ?
                    WHERE lecture_reservations.client = ?
                    GROUP BY lectures.id, time_start, time_end, max_persons, place, published, trainers.id, trainers.name, lecture_type.name",
            [$clientId, $clientId]);
    }

    public function insert(int $trainer, int $timeStart, int $timeEnd, int $maxPersons, string $place, int $type) {
        $today = new DateTime();
        if ($timeStart < $today->getTimestamp()) {
            throw new BadQueryStringException("Nelze založit lekci v minulosti!");
        }
        if ($timeEnd < $timeStart) {
            throw new BadQueryStringException("Konec lekce nemůže být dříve, než začátek!");
        }
        if ($timeStart > $timeEnd) {
            throw new BadQueryStringException("Začátek lekce nemůže být později, než konec!");
        }

        try {
            return $this->database->insert(self::TABLE_NAME,
                [
                    LecturesManager::COLUMN_TRAINER => $trainer,
                    LecturesManager::COLUMN_TIME_START => $timeStart,
                    LecturesManager::COLUMN_TIME_END => $timeEnd,
                    LecturesManager::COLUMN_MAX_PERSONS => $maxPersons,
                    LecturesManager::COLUMN_PLACE => $place,
                    LecturesManager::COLUMN_TYPE => $type
                ]);
        } catch (PDOException $ex) {
            $this->logger->fatal($ex->getMessage());
            $this->logger->fatal($ex->getCode());
            $this->logger->debug($ex->getTraceAsString());
            throw new LectureDataException("Nepodařilo se založit novou lekci!");
        }
    }

    public function lectureNameByType(int $lectureType) {
        return $this->database->queryOne("SELECT name FROM lecture_type WHERE id = ?", [$lectureType]);
    }

    public function lectureTypes() {
        return $this->database->queryAll("SELECT id, name FROM lecture_type");
    }

    /**
     * Vrátí lekci podle Id
     *
     * @param int $lectureId
     * @return array
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
            throw new LectureException("Lekce s Id: " . $lectureId . " nebyla nalezena!");
        }

        return $fromDb;
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

    public function publish(int $lectureId) {
        $fromDb = $this->database->update(self::TABLE_NAME, [LecturesManager::COLUMN_PUBLISHED => 1], "WHERE id = ?", [$lectureId]);

        if ($fromDb == 0) {
            throw new LectureDataException("Lekci se nepodařilo publikovat!");
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