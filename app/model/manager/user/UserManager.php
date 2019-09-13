<?php

namespace app\model\manager\user;

use app\model\database\Database;
use app\model\manager\jwt\JWTManager;
use app\model\User;
use app\model\util\StringUtils;
use DateTime;
use Logger;
use PDOException;

/**
 * Class UserManager - Správce jednotlivých uživatelů
 * @Inject Database
 * @Inject JWTManager
 * @package app\model\manager
 */
class UserManager {

    const TABLE_NAME = 'users';

    const COLUMN_ID = 'id';
    const COLUMN_EMAIL = 'email';
    const COLUMN_PASSWORD = 'password';
    const COLUMN_ROLE = 'role';
    const COLUMN_NAME = 'name';
    const COLUMN_FIRST_LOGIN = 'first_login';
    const COLUMN_LAST_LOGIN = 'last_login';
    const COLUMN_BANNED = 'banned';
    const COLUMN_ACTIVATED = 'activated';
    const COLUMN_ACTIVATION_CODE = 'activation_code';

    const FLAG_REMEMBER = 'remember';

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Database
     */
    private $database;
    /**
     * @var JWTManager
     */
    private $jwtmanager;

    /**
     * UserManager constructor.
     */
    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }

    /**
     * Zaregistruje nového uživatele
     *
     * @param string $email E-mail nového uživatele
     * @param string $name Jméno nového uživatele
     * @param string $password Heslo nového uživatele
     * @throws UserException () Pokud se registrace nezdaří
     */
    public function register(string $email, string $name, string $password) {
        $pass = password_hash($password, PASSWORD_ARGON2I);
        $dateTime = new DateTime();
        $randomString = StringUtils::generateRandomToken(20);
        $activationCode = StringUtils::createHash($pass, $randomString);
        try {
            $this->database->insert('users',
                [
                    self::COLUMN_EMAIL => $email,
                    self::COLUMN_PASSWORD => $pass,
                    self::COLUMN_NAME => $name,
                    self::COLUMN_FIRST_LOGIN => $dateTime->getTimestamp(),
                    self::COLUMN_LAST_LOGIN => $dateTime->getTimestamp(),
                    self::COLUMN_ACTIVATION_CODE => $activationCode
                ]);
        } catch (PDOException $chyba) {
            $this->logger->error($chyba);
            throw new UserException('Uživatel s touto e-mailovou adresou je již zaregistrovaný.');
        }
    }

    /**
     * Přihlásí uživatele do systému
     *
     * @param string $email E-mail uživatele
     * @param string $password Heslo uživatele
     * @param bool $remember True, pokud si chce uživatel podržet přihlášení déle, jinak False
     * @return string JWT řetěžec
     * @throws UserException Pokud se přihlášení nezdaří
     */
    public function login(string $email, string $password, bool $remember): string {
        $dateTime = new DateTime();
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

        $this->database->update(self::TABLE_NAME,
            [self::COLUMN_LAST_LOGIN => $dateTime->getTimestamp()],
            "WHERE id = ?",
            [$fromDb['id']]);

        return $this->jwtmanager->createJWT(new User($fromDb['id'], $email, $fromDb['role']), $remember);

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

    public function trainers() {
        return $this->database->queryAll("SELECT id, name FROM users WHERE role > 1");
    }
}