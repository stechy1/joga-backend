<?php

namespace app\model\manager\user;

use app\model\database\Database;
use app\model\manager\email\EmailManager;
use app\model\manager\jwt\JWTManager;
use app\model\User;
use app\model\util\StringUtils;
use DateTime;
use Logger;
use PDOException;
use PHPMailer\PHPMailer\Exception;

/**
 * Class UserManager - Správce jednotlivých uživatelů
 * @Inject Database
 * @Inject JWTManager
 * @Inject EmailManager
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
    const COLUMN_ACTIVE = 'active';
    const COLUMN_CHECK_CODE = 'check_code';
    const COLUMN_CHECKED = 'checked';
    const COLUMN_DISABLED = 'disabled';

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
     * @var EmailManager
     */
    private $emailmanager;

    /**
     * UserManager constructor.
     */
    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }

    /**
     * Zkontroluje zadané heslo před zásahem do osobních údajů uživatele
     *
     * @param int $id Id uživatele
     * @param string $password Heslo uživatele
     * @throws UserException Pokud heslo není validní
     */
    private function checkPassword(int $id, string $password) {
        $fromDb = $this->database->queryOne("SELECT password FROM users WHERE id = ?", [$id]);

        $hash = $fromDb['password'];
        if (!password_verify($password, $hash)) {
            throw new UserException('Heslo není validní!');
        }
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
        $query = "SELECT id, email, role, name FROM users";
        if ($from > -1) {
            $query .= " WHERE users.id <= ?";
            $params[] = $from;
        }
        $query .= " ORDER BY users.id DESC LIMIT ?";
        $params[] = $count;
        return $this->database->queryAll($query, $params);
    }

    /**
     * Vrátí informace o jednom uživateli
     *
     * @param int $id Id uživatele, jehož informace chci získat
     * @return array Pole s informacemi o uživateli
     * @throws UserException Pokud uživatel nebyl nalezen
     */
    public function byId(int $id) {
        $fromDb = $this->database->queryOne(
            "SELECT id, email, role, name, 
                            first_login, last_login, 
                            banned, active, checked 
                    FROM users
                    WHERE id = ?",
            [$id]);

        if ($fromDb == null) {
            throw new UserException("Uživatel s id ${id} nebyl nalezen!");
        }

        return $fromDb;
    }

    /**
     * Aktualizuje údaje o uživateli
     *
     * @param int $id Id uživatele
     * @param string $name Nové jméno uživatele
     * @param string $password Uživatelské heslo pro kontrolu přístupu k citlivým údajům
     * @throws UserException Pokud heslo není validní
     * @throws UserDataException Pokud se neaktualizují žádná uživatelská data
     */
    public function update(int $id, string $name, string $password) {
        $this->checkPassword($id, $password);

        $fromDb = $this->database->update(self::TABLE_NAME,
            [self::COLUMN_NAME => $name],
            "WHERE id = ?",
            [$id]);

        if ($fromDb == 0) {
            throw new UserDataException("Data uživatele nebyla aktualizována!");
        }
    }

    /**
     * Deaktivuje uživatelský účet
     *
     * @param int $id Id účtu, který se má deaktivovat
     * @param string $password Uživatelské heslo pro kontrolu přístupu k citlivým údajům
     * @throws UserException Pokud heslo není validní
     * @throws UserDataException Pokud se nepodaří uživatelský účet deaktivovat
     */
    public function deactivate(int $id, string $password) {
        $this->checkPassword($id, $password);
        // TODO ověřit, že uživatel není přihlášený na žádné nadcházející lekci

        $updated = $this->database->update(self::TABLE_NAME,
            [self::COLUMN_ACTIVE => 0],
            "WHERE id = ?",
            [$id]);

        if ($updated == 0) {
            throw new UserDataException("Uživatelský účet se nepodařilo deaktivovat!");
        }
    }

    /**
     * Zruší uživatelský účet
     *
     * @param int $id Id účtu, který se má deaktivovat
     * @param string $password Uživatelské heslo pro kontrolu přístupu k citlivým údajům
     * @throws UserException Pokud heslo není validní
     * @throws UserDataException Pokud se nepodaří uživatelský účet zrušit
     */
    public function disable(int $id, string $password) {
        $this->checkPassword($id, $password);
        // TODO ověřit, že uživatel není přihlášený na žádné nadcházející lekci

        $updated = $this->database->update(self::TABLE_NAME,
            [self::COLUMN_DISABLED => 1],
            "WHERE id = ?",
            [$id]);

        if ($updated == 0) {
            throw new UserDataException("Uživatelský účet se nepodařilo zrušit!");
        }
    }

    /**
     * Aktualizuje uživatelské heslo
     *
     * @param int $id Id uživatele
     * @param string $oldPassword Staré heslo pro kontrolu
     * @param string $newPassword Nové heslo
     * @param string $newPassword2 Nové heslo pro kontrolu
     * @throws UserException Pokud heslo není validní
     * @throws UserDataException Pokud se hedlo nepodaří změnit
     */
    public function updatePassword(int $id, string $oldPassword, string $newPassword, string $newPassword2) {
        $this->checkPassword($id, $oldPassword);

        if ($newPassword !== $newPassword2) {
            throw new UserException("Nová hesla se neshodují!");
        }

        $pass = password_hash($newPassword, PASSWORD_ARGON2I);
        $fromDb = $this->database->update(self::TABLE_NAME,
            [self::COLUMN_PASSWORD => $pass],
            "WHERE id = ?",
            [$id]);

        if ($fromDb == 0) {
            throw new UserDataException("Heslo se nepodařilo změnit!");
        }
    }

    /**
     * Zaregistruje nového uživatele
     *
     * @param string $email E-mail nového uživatele
     * @param string $name Jméno nového uživatele
     * @param string $password Heslo nového uživatele
     * @throws UserException Pokud se registrace nezdaří
     * @throws Exception Pokud se nepodaří odeslat registrační e-mail s ověřovacím kódem
     */
    public function register(string $email, string $name, string $password) {
        $pass = password_hash($password, PASSWORD_ARGON2I);
        $dateTime = new DateTime();
        $randomString = StringUtils::generateRandomToken(20);
        $checkCode = StringUtils::createHash($pass, $randomString);
        $this->logger->debug("Check code: " . $checkCode);
        try {
            $this->database->insert('users',
                [
                    self::COLUMN_EMAIL => $email,
                    self::COLUMN_PASSWORD => $pass,
                    self::COLUMN_NAME => $name,
                    self::COLUMN_FIRST_LOGIN => $dateTime->getTimestamp(),
                    self::COLUMN_LAST_LOGIN => $dateTime->getTimestamp(),
                    self::COLUMN_CHECK_CODE => $checkCode
                ]);
        } catch (PDOException $chyba) {
            $this->logger->error($chyba);
            throw new UserException('Uživatel s touto e-mailovou adresou je již zaregistrovaný!');
        }

        $this->emailmanager->sendRegisterEmail($email, $checkCode);
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
        // Získání údajů
        $fromDb = $this->database->queryOne('
                        SELECT id, password, role, banned, disabled
                        FROM users
                        WHERE email = ?
                ', [$email]);
        if (!$fromDb) throw new UserException('Špatné jméno nebo heslo!');

        // Ověření hesla
        $hash = $fromDb['password'];
        if (!password_verify($password, $hash)) {
            throw new UserException('Špatné jméno nebo heslo!');
        }

        if ($fromDb[self::COLUMN_BANNED] == 1) {
            throw new UserException("Uživatelský účet byl zabanován!");
        }

        if ($fromDb[self::COLUMN_DISABLED] == 1) {
            throw new UserException("Uživatel zrušil účet!");
        }

        $this->database->update(self::TABLE_NAME,
            [self::COLUMN_LAST_LOGIN => $dateTime->getTimestamp()],
            "WHERE id = ?",
            [$fromDb['id']]);

        return $this->jwtmanager->createJWT(new User($fromDb['id'], $email, $fromDb['role']), $remember);

    }

    /**
     * Vrátí všechny uživatele, kteří jsou v roli trenéra
     *
     * @return array|null
     */
    public function trainers() {
        return $this->database->queryAll("SELECT id, name FROM users WHERE role > 1");
    }

    /**
     * Ověří kód účtu
     *
     * @param string $checkCode Kód, který se má ověřit
     * @throws UserDataException Pokud kód neodpovídá žádnému uživateli
     * @throws UserException Pokud se nepodaří aktualizovat informace o aktivačním kódu
     */
    public function checkCode(string $checkCode) {
        $fromDb = $this->database->queryOne("SELECT id FROM users WHERE check_code = ?", [$checkCode]);

        if ($fromDb == null) {
            throw new UserDataException("Aktivační kód neodpovídá žádnému uživateli!");
        }

        $id = $fromDb[self::COLUMN_ID];

        $updatedRows = $this->database->update(self::TABLE_NAME, [self::COLUMN_CHECKED => 1], "WHERE id = ?", [$id]);

        if ($updatedRows == 0) {
            throw new UserException("Kontrolní kód se nepodařilo zpracovat!");
        }
    }


}