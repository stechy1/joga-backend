<?php


namespace app\model\manager\lectures;


use app\model\database\Database;
use DateTime;
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
    const COLUMN_START_TIME = "start_time";
    const COLUMN_DURATION = "duration";
    const COLUMN_MAX_PERSONS = "max_persons";
    const COLUMN_PLACE = "place";
    const COLUMN_PUBLISHED = "published";

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
        $monthTimestamp /= 1000;
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
            "SELECT lectures.id as lecture_id, start_time, duration, max_persons, place, published,
                           users.id AS user_id, users.name
                    FROM lectures
                    LEFT JOIN users ON users.id = lectures.id
                    WHERE start_time BETWEEN ? AND ?",
            [$firstDay->getTimestamp(), $lastDay->getTimestamp()]);

    }

    public function insert(int $trainer, int $startTime, int $duration, int $maxPersons, string $place) {
        $this->database->insert(self::TABLE_NAME, [
            LecturesManager::COLUMN_TRAINER => $trainer,
            LecturesManager::COLUMN_START_TIME => $startTime / 1000,
            LecturesManager::COLUMN_DURATION => $duration,
            LecturesManager::COLUMN_MAX_PERSONS => $maxPersons,
            LecturesManager::COLUMN_PLACE => $place
        ]);
    }
}