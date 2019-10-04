<?php


namespace app\model\manager\reservations;


use app\model\database\Database;
use app\model\manager\lectures\LectureException;
use app\model\manager\lectures\LecturesManager;
use DateTime;
use Logger;

/**
 * Class LectureReservationsManager
 * Správce rezervací lekcí
 * @Inject Database
 * @Inject LecturesManager
 * @package app\model\manager\reservations
 */
class LectureReservationsManager {

    const TABLE_NAME = "lecture_reservations";

    const COLUMN_LECTURE = "lecture";
    const COLUMN_CLIENT = "client";
    const COLUMN_CREATED = "created";

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Database
     */
    private $database;
    /**
     * @var LecturesManager
     */
    private $lecturesmanager;

    public function __construct() {
      $this->logger = Logger::getLogger(__CLASS__);
    }

    /**
     * Zarezervuje lekci pro daného klienta
     *
     * @param int $clientId Id klienta, který si chce rezervovat lekci
     * @param int $lectureId Id lekce, do které se klient rezervuje
     * @throws LectureException Pokud lekce není nalezena
     * @throws LectureReservationException Pokud je na rezervaci příliš pozdě
     */
    public function reserve(int $clientId, int $lectureId) {
        $time = new DateTime();
        $lecture = $this->lecturesmanager->byId($lectureId);

        $start = new DateTime();
        $start->setTimestamp($lecture[LecturesManager::COLUMN_TIME_START]);

        $diff = $start->diff($time);

        if ($diff->invert == 0 || $diff->y == 0 && $diff->m == 0 && $diff->days == 0 && $diff->h < 2) {
            throw new LectureReservationException("Na lekci se již nelze rezervovat. Je příliš pozdě!");
        }

        if ($lecture[LecturesManager::VIRTUAL_COLUMN_RESERVED_CLIENTS] == $lecture[LecturesManager::COLUMN_MAX_PERSONS]) {
            throw new LectureReservationException("Na lekci je rezervován maximální počet klientů!");
        }

        $this->database->insert(self::TABLE_NAME, [
            self::COLUMN_LECTURE => $lectureId,
            self::COLUMN_CLIENT => $clientId,
            self::COLUMN_CREATED => $time->getTimestamp()
        ]);
    }

    public function cancel(int $clientId, int $lectureId) {
        $time = new DateTime();
        $lecture = $this->lecturesmanager->byId($lectureId);

        $start = new DateTime();
        $start->setTimestamp($lecture[LecturesManager::COLUMN_TIME_START]);

        $diff = $start->diff($time);

        if ($diff->y == 0 && $diff->m == 0 && $diff->days == 0 && $diff->h < 2) {
            throw new LectureReservationException("Z lekce se již nelze odhlásit. Je příliš pozdě!");
        }

        $this->database->delete(self::TABLE_NAME, "WHERE lecture = ? AND client = ?", [
            $lectureId, $clientId
        ]);
    }
}