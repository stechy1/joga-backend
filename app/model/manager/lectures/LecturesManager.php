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

    public function byId(int $lectureId) {
        return $this->database->queryOne(
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
    }

    public function update(int $lectureId, int $trainer, int $timeStart, int $timeEnd, int $maxPersons, $place, int $lectureType) {
        return $this->database->update(self::TABLE_NAME,
            [
                LecturesManager::COLUMN_TRAINER => $trainer,
                LecturesManager::COLUMN_TIME_START => $timeStart,
                LecturesManager::COLUMN_TIME_END => $timeEnd,
                LecturesManager::COLUMN_MAX_PERSONS => $maxPersons,
                LecturesManager::COLUMN_PLACE => $place,
                LecturesManager::COLUMN_TYPE => $lectureType
            ], " WHERE id = ?",
            [$lectureId]);
    }

    public function delete(int $lectureId) {
        $this->logger->info("Mažu lekci s id: " . $lectureId);
        return $this->database->delete(self::TABLE_NAME, "WHERE id = ?", [$lectureId]);
    }

    public function checkDateTimeValidity(int $timestamp) {
        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);

        $this->logger->info($timestamp);

        $this->logger->trace($dateTime);
        return false;
    }

    /**
     *
     *
     * @param int $timestamp
     * @param int $duration
     * @throws Exception
     */
    public function checkDurationValidity(int $timestamp, int $duration) {
        $dateTimeFrom = new DateTime();
        $dateTimeFrom->setTimestamp($timestamp);

        $dateTimeTo = new DateTime();
        $dateTimeTo->setTimestamp($timestamp);
        $dateTimeTo->add(new DateInterval('PT' . $duration . 'M'));


    }
}