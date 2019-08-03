<?php

namespace app\model\manager;

use app\model\database\Database;
use app\model\service\exception\UserException;
use app\model\User;
use Logger;
use PDOException;

/**
 * Class UserManager - Správce jednotlivých uživatelů
 * @Inject Database
 * @Inject JSWManager
 * @package app\model\manager
 */
class UserManager {

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Database
     */
    private $database;
    /**
     * @var JSWManager
     */
    private $jswmanager;

    /**
     * UserManager constructor.
     */
    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }

    /**
     * Zaregistruje nového uživatele
     *
     * @param $email
     * @param $password
     * @throws UserException () Pokud se registrace nezdaří
     */
    public function register($email, $password) {
        $pass = password_hash($password, PASSWORD_ARGON2I);
        $user = array('email' => $email, 'password' => $pass);
        try {
            $this->database->insert('users', $user);
        } catch (PDOException $chyba) {
            $this->logger->error($chyba);
            throw new UserException('Uživatel s touto e-mailovou adresou je již zaregistrovaný.');
        }
    }

    /**
     * Přihlásí uživatele do systému
     *
     * @param $email
     * @param $password
     * @return string JWT řetěžec
     * @throws UserException Pokud se přihlášení nezdaří
     */
    public function login($email, $password): string {
//        Získání údajů
        $fromDb = $this->database->queryOne('
                        SELECT id, password, role
                        FROM users
                        WHERE email = ?
                ', [$email]);
        if (!$fromDb) throw new UserException('Špatné jméno nebo heslo.');

//      Ověření hesla
        $hash = $fromDb['password'];
        if (!password_verify($password, $hash)) {
            throw new UserException('Špatné jméno nebo heslo.');
        }

        return $this->jswmanager->createJSW(new User($fromDb['id'], $email, $fromDb['role'], null));

    }

    /**
     * Vrátí z databáze zadaný počet uživatelů
     *
     * @param int $from Index, od kterého uživatele mám začít vyhledávat
     * @param int $count Počet uživatelů, které vrátím
     * @return array|null Pole uživatelů
     */
    public function all(int $count, int $from = -1) {
        $this->logger->trace('From: ' . $from);
        $this->logger->trace('Count: ' . $count);

        $params = [];
        $query = "SELECT users.id, users.email, users.role FROM users";
        if ($from > -1) {
            $query .= " WHERE users.id <= ?";
            $params[] = $from;
        }
        $query .= " ORDER BY users.id DESC LIMIT ?";
        $params[] = $count;
        return $this->database->queryAll($query, $params);

//        return $this->database->queryAll(
//            "SELECT users.id, users.email, users.role
//                    FROM users" .
//                    $from == -1 ? "" : "WHERE users.id < ?" .
//                    "ORDER BY users.id
//                    DESC LIMIT ?"
//            , [$from, $count]
//        );
    }
}